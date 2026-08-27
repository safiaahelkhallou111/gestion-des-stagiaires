<?php
require_once __DIR__ . '/../config/database.php';

class Tache {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getByStage($stageId) {
        $stmt = $this->db->prepare("SELECT * FROM taches WHERE stage_id = :stage_id ORDER BY date_limite ASC");
        $stmt->execute([':stage_id' => $stageId]);
        return $stmt->fetchAll();
    }

    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM taches WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function create($stageId, $titre, $description, $dateLimite) {
        $stmt = $this->db->prepare("
            INSERT INTO taches (stage_id, titre, description, date_limite, statut, progression)
            VALUES (:stage_id, :titre, :description, :date_limite, 'a_faire', 0)
        ");
        $stmt->execute([
            ':stage_id' => $stageId,
            ':titre' => trim($titre),
            ':description' => trim($description),
            ':date_limite' => $dateLimite
        ]);
        return $this->db->lastInsertId();
    }

    public function updateProgression($id, $progression, $statut = null) {
        $progression = max(0, min(100, (int)$progression));
        if ($statut === null) {
            if ($progression == 0) $statut = 'a_faire';
            else if ($progression == 100) $statut = 'termine';
            else $statut = 'en_cours';
        }

        $stmt = $this->db->prepare("UPDATE taches SET progression = :progression, statut = :statut WHERE id = :id");
        return $stmt->execute([
            ':progression' => $progression,
            ':statut' => $statut,
            ':id' => $id
        ]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM taches WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}
