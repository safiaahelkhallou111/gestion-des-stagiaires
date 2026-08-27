<?php
require_once __DIR__ . '/../config/database.php';

class Document {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getByStage(int $stageId): array {
        $stmt = $this->db->prepare("SELECT * FROM documents WHERE stage_id = :stage_id ORDER BY created_at DESC");
        $stmt->execute([':stage_id' => $stageId]);
        return $stmt->fetchAll();
    }

    public function getByStagiaire(int $stagiaireId): array {
        $stmt = $this->db->prepare("SELECT * FROM documents WHERE stagiaire_id = :stagiaire_id ORDER BY created_at DESC");
        $stmt->execute([':stagiaire_id' => $stagiaireId]);
        return $stmt->fetchAll();
    }

    /**
     * Récupère le rapport final le plus récent d'un stagiaire
     */
    public function getRapportFinal(int $stagiaireId): ?array {
        $stmt = $this->db->prepare("
            SELECT * FROM documents
            WHERE stagiaire_id = :stagiaire_id AND type_document = 'rapport_final'
            ORDER BY created_at DESC LIMIT 1
        ");
        $stmt->execute([':stagiaire_id' => $stagiaireId]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Récupère tous les documents sauf le rapport final
     */
    public function getDocumentsCourants(int $stagiaireId): array {
        $stmt = $this->db->prepare("
            SELECT * FROM documents
            WHERE stagiaire_id = :stagiaire_id AND type_document != 'rapport_final'
            ORDER BY created_at DESC
        ");
        $stmt->execute([':stagiaire_id' => $stagiaireId]);
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM documents WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function create(int $stagiaireId, int $stageId, string $titre, string $typeDocument, string $cheminFichier, int $tailleOctets = 0, int $permanent = 0): int {
        // Note: the `permanent` column may not exist on older databases — run the
        // migration SQL to add it. Passing $permanent=1 will mark the document
        // as permanent (protected from deletion and preserved if related
        // stagiaire/stage rows are removed).
        $stmt = $this->db->prepare(
            "INSERT INTO documents (stagiaire_id, stage_id, titre, type_document, chemin_fichier, taille_octets, permanent)\n"
            . "VALUES (:stagiaire_id, :stage_id, :titre, :type_document, :chemin_fichier, :taille_octets, :permanent)"
        );
        $stmt->execute([
            ':stagiaire_id'  => $stagiaireId,
            ':stage_id'      => $stageId,
            ':titre'         => trim($titre),
            ':type_document' => $typeDocument,
            ':chemin_fichier'=> $cheminFichier,
            ':taille_octets' => $tailleOctets,
            ':permanent'     => $permanent
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function delete(int $id, int $stagiaireId): bool {
        $doc = $this->findById($id);
        if ($doc && $doc['stagiaire_id'] == $stagiaireId) {
            // Do not allow deletion of permanent documents
            if (!empty($doc['permanent'])) {
                return false;
            }
            $fullPath = UPLOAD_PATH . '/documents/' . $doc['chemin_fichier'];
            if (file_exists($fullPath)) {
                @unlink($fullPath);
            }
            $stmt = $this->db->prepare("DELETE FROM documents WHERE id = :id");
            return $stmt->execute([':id' => $id]);
        }
        return false;
    }

    /**
     * Formate une taille en octets en chaîne lisible (Ko, Mo)
     */
    public static function formatTaille(int $octets): string {
        if ($octets >= 1048576) {
            return number_format($octets / 1048576, 2) . ' Mo';
        } elseif ($octets >= 1024) {
            return number_format($octets / 1024, 1) . ' Ko';
        }
        return $octets . ' o';
    }
}
