<?php
require_once __DIR__ . '/../config/database.php';

class SujetStage {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getByEncadrant($encadrantId) {
        $stmt = $this->db->prepare("
            SELECT su.*, 
                   (SELECT COUNT(*) FROM stages WHERE sujet_id = su.id) as nb_utilisations
            FROM sujets_stage su
            WHERE su.encadrant_id = :encadrant_id
            ORDER BY su.created_at DESC
        ");
        $stmt->execute([':encadrant_id' => $encadrantId]);
        return $stmt->fetchAll();
    }

    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM sujets_stage WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function create($encadrantId, $titre, $description, $domaine = 'Informatique / Digital') {
        $stmt = $this->db->prepare("
            INSERT INTO sujets_stage (encadrant_id, titre, description, domaine)
            VALUES (:encadrant_id, :titre, :description, :domaine)
        ");
        $stmt->execute([
            ':encadrant_id' => $encadrantId,
            ':titre' => trim($titre),
            ':description' => trim($description),
            ':domaine' => trim($domaine)
        ]);
        return $this->db->lastInsertId();
    }

    public function update($id, $titre, $description, $domaine) {
        $stmt = $this->db->prepare("
            UPDATE sujets_stage 
            SET titre = :titre, description = :description, domaine = :domaine
            WHERE id = :id
        ");
        return $stmt->execute([
            ':titre' => trim($titre),
            ':description' => trim($description),
            ':domaine' => trim($domaine),
            ':id' => $id
        ]);
    }

    public function delete($id, $encadrantId) {
        $stmt = $this->db->prepare("DELETE FROM sujets_stage WHERE id = :id AND encadrant_id = :encadrant_id");
        return $stmt->execute([':id' => $id, ':encadrant_id' => $encadrantId]);
    }
}
