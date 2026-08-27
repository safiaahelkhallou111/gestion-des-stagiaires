<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

try {
    $db = Database::getConnection();
    $stmt = $db->query("SELECT id, stagiaire_id, stage_id, titre, type_document, chemin_fichier, taille_octets, created_at FROM documents ORDER BY created_at DESC LIMIT 20");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "DB_RESULT:\n";
    echo json_encode($rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . PHP_EOL;
} catch (Exception $e) {
    echo "DB_ERROR: " . $e->getMessage() . PHP_EOL;
}

$files = [];
$dir = UPLOAD_PATH . '/documents/';
if (is_dir($dir)) {
    $items = array_values(array_diff(scandir($dir), ['..', '.']));
    foreach ($items as $f) {
        $path = $dir . $f;
        if (is_file($path)) {
            $files[] = ['name' => $f, 'size' => filesize($path), 'mtime' => date('c', filemtime($path))];
        }
    }
}

echo "FILES:\n";
echo json_encode($files, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . PHP_EOL;
