<?php
require_once __DIR__ . '/../config/database.php';

class Pointage {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function pointer($stageId, $stagiaireId, $datePointage, $statut = 'present', $remarque = null) {
        // RG7 : Verification d'existence préalable
        $existing = $this->getByStagiaireAndDate($stagiaireId, $datePointage);
        if ($existing) {
            // Mise à jour si déjà existant pour ce jour
            $stmt = $this->db->prepare("
                UPDATE pointages 
                SET statut = :statut, remarque = :remarque 
                WHERE stagiaire_id = :stagiaire_id AND date_pointage = :date_pointage
            ");
            return $stmt->execute([
                ':statut' => $statut,
                ':remarque' => $remarque,
                ':stagiaire_id' => $stagiaireId,
                ':date_pointage' => $datePointage
            ]);
        }

        $stmt = $this->db->prepare("
            INSERT INTO pointages (stage_id, stagiaire_id, date_pointage, statut, remarque)
            VALUES (:stage_id, :stagiaire_id, :date_pointage, :statut, :remarque)
        ");
        return $stmt->execute([
            ':stage_id' => $stageId,
            ':stagiaire_id' => $stagiaireId,
            ':date_pointage' => $datePointage,
            ':statut' => $statut,
            ':remarque' => $remarque
        ]);
    }

    public function getByStagiaireAndDate($stagiaireId, $datePointage) {
        $stmt = $this->db->prepare("SELECT * FROM pointages WHERE stagiaire_id = :stagiaire_id AND date_pointage = :date_pointage LIMIT 1");
        $stmt->execute([':stagiaire_id' => $stagiaireId, ':date_pointage' => $datePointage]);
        return $stmt->fetch();
    }

    public function getByStage($stageId) {
        $stmt = $this->db->prepare("SELECT * FROM pointages WHERE stage_id = :stage_id ORDER BY date_pointage DESC");
        $stmt->execute([':stage_id' => $stageId]);
        return $stmt->fetchAll();
    }

    public function getStatistiquesAssiduite($stageId) {
        $stmt = $this->db->prepare("
            SELECT 
                COUNT(*) as total_jours,
                SUM(CASE WHEN statut = 'present' THEN 1 ELSE 0 END) as nb_presents,
                SUM(CASE WHEN statut = 'absent' THEN 1 ELSE 0 END) as nb_absents,
                SUM(CASE WHEN statut = 'retard' THEN 1 ELSE 0 END) as nb_retards
            FROM pointages 
            WHERE stage_id = :stage_id
        ");
        $stmt->execute([':stage_id' => $stageId]);
        $res = $stmt->fetch();

        $total = $res['total_jours'] > 0 ? $res['total_jours'] : 1;
        $res['taux_presence'] = round(($res['nb_presents'] / $total) * 100, 1);
        return $res;
    }
}
