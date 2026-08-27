<?php
require_once __DIR__ . '/../config/database.php';

$pdo = Database::getConnection();
$driver = Database::getDriver();

try {
    if ($driver === 'mysql') {
        echo "Detected MySQL driver. Running MySQL migration...\n";

        // Add permanent column if not exists
        try {
            $pdo->exec("ALTER TABLE documents ADD COLUMN permanent TINYINT(1) NOT NULL DEFAULT 0");
            echo "Added column 'permanent'.\n";
        } catch (Throwable $e) {
            echo "Could not add 'permanent' column (maybe exists): " . $e->getMessage() . "\n";
        }

        // Make stagiaire_id and stage_id nullable
        try {
            $pdo->exec("ALTER TABLE documents MODIFY stagiaire_id INT NULL");
            $pdo->exec("ALTER TABLE documents MODIFY stage_id INT NULL");
            echo "Modified stagiaire_id and stage_id to allow NULLs.\n";
        } catch (Throwable $e) {
            echo "Could not modify stagiaire_id/stage_id: " . $e->getMessage() . "\n";
        }

        // Drop existing foreign keys referencing stagiaires/stages on documents
        try {
            $stmt = $pdo->query("SELECT constraint_name FROM information_schema.key_column_usage WHERE table_schema = DATABASE() AND table_name = 'documents' AND referenced_table_name IN ('stagiaires','stages')");
            $constraints = $stmt->fetchAll(PDO::FETCH_COLUMN);
            foreach ($constraints as $cname) {
                if ($cname) {
                    try {
                        $pdo->exec("ALTER TABLE documents DROP FOREIGN KEY `" . $cname . "`");
                        echo "Dropped foreign key: $cname\n";
                    } catch (Throwable $e) {
                        echo "Failed to drop foreign key $cname: " . $e->getMessage() . "\n";
                    }
                }
            }
        } catch (Throwable $e) {
            echo "Error reading existing constraints: " . $e->getMessage() . "\n";
        }

        // Add new foreign keys with ON DELETE SET NULL
        try {
            $pdo->exec("ALTER TABLE documents ADD CONSTRAINT fk_documents_stagiaire FOREIGN KEY (stagiaire_id) REFERENCES stagiaires(id) ON DELETE SET NULL");
            $pdo->exec("ALTER TABLE documents ADD CONSTRAINT fk_documents_stage FOREIGN KEY (stage_id) REFERENCES stages(id) ON DELETE SET NULL");
            echo "Added new foreign keys with ON DELETE SET NULL.\n";
        } catch (Throwable $e) {
            echo "Could not add new foreign keys: " . $e->getMessage() . "\n";
        }

        echo "MySQL migration completed.\n";

    } else {
        echo "Detected SQLite driver. Running SQLite-safe migration (limited).\n";

        // Add permanent column if not exists (SQLite supports this)
        try {
            $pdo->exec("ALTER TABLE documents ADD COLUMN permanent INTEGER NOT NULL DEFAULT 0");
            echo "Added column 'permanent' (SQLite).\n";
        } catch (Throwable $e) {
            echo "Could not add 'permanent' column (maybe exists): " . $e->getMessage() . "\n";
        }
        echo "Attempting to rebuild the 'documents' table to set ON DELETE SET NULL on FKs...\n";
        try {
            $pdo->exec('PRAGMA foreign_keys = OFF');

            $pdo->beginTransaction();

            // Create new table with desired schema
            $pdo->exec(
                "CREATE TABLE IF NOT EXISTS documents_new (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    stagiaire_id INTEGER DEFAULT NULL,
                    stage_id INTEGER DEFAULT NULL,
                    titre TEXT NOT NULL,
                    type_document TEXT NOT NULL,
                    chemin_fichier TEXT NOT NULL,
                    taille_octets INTEGER NOT NULL DEFAULT 0,
                    permanent INTEGER NOT NULL DEFAULT 0,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (stagiaire_id) REFERENCES stagiaires(id) ON DELETE SET NULL,
                    FOREIGN KEY (stage_id) REFERENCES stages(id) ON DELETE SET NULL
                )"
            );

            // Copy existing data, mapping permanent default
            $pdo->exec(
                "INSERT INTO documents_new (id, stagiaire_id, stage_id, titre, type_document, chemin_fichier, taille_octets, permanent, created_at)
                 SELECT id, stagiaire_id, stage_id, titre, type_document, chemin_fichier, taille_octets, COALESCE(permanent, 0), created_at FROM documents"
            );

            // Drop old table and rename
            $pdo->exec("DROP TABLE documents");
            $pdo->exec("ALTER TABLE documents_new RENAME TO documents");

            $pdo->commit();
            $pdo->exec('PRAGMA foreign_keys = ON');
            echo "SQLite documents table rebuilt successfully with ON DELETE SET NULL.\n";
        } catch (Throwable $e) {
            try { if ($pdo->inTransaction()) $pdo->rollBack(); } catch (Throwable $e2) {}
            echo "SQLite rebuild failed: " . $e->getMessage() . "\n";
            echo "You can fallback to reinitializing the DB using database/init_db.php on a fresh file.\n";
        }
    }
} catch (Throwable $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}

return 0;
