<?php
require_once __DIR__ . '/../config/database.php';

class Evaluation {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getByStage($stageId) {
        $stmt = $this->db->prepare("
            SELECT e.*, u.nom as encadrant_nom, u.prenom as encadrant_prenom 
            FROM evaluations e
            JOIN encadrants enc ON e.encadrant_id = enc.id
            JOIN users u ON enc.user_id = u.id
            WHERE e.stage_id = :stage_id LIMIT 1
        ");
        $stmt->execute([':stage_id' => $stageId]);
        return $stmt->fetch();
    }

    public function createOrUpdate($stageId, $encadrantId, $noteTechnique, $noteComportement, $noteAssiduite, $commentaire) {
        // Validation des notes de 1 à 10
        $nTech = max(1, min(10, (int)$noteTechnique));
        $nComp = max(1, min(10, (int)$noteComportement));
        $nAssid = max(1, min(10, (int)$noteAssiduite));
        $noteGlobale = round(($nTech + $nComp + $nAssid) / 3, 2);

        // Verification RG8: L'évaluation appartient au stage précis et est créée par l'encadrant
        $existing = $this->getByStage($stageId);
        if ($existing) {
            $stmt = $this->db->prepare("
                UPDATE evaluations 
                SET note_technique = :nTech, note_comportement = :nComp, note_assiduite = :nAssid, 
                    note_globale = :nGlob, commentaire = :comment, encadrant_id = :enc_id
                WHERE stage_id = :stage_id
            ");
            return $stmt->execute([
                ':nTech' => $nTech,
                ':nComp' => $nComp,
                ':nAssid' => $nAssid,
                ':nGlob' => $noteGlobale,
                ':comment' => trim($commentaire),
                ':enc_id' => $encadrantId,
                ':stage_id' => $stageId
            ]);
        }

        $stmt = $this->db->prepare("
            INSERT INTO evaluations (stage_id, encadrant_id, note_technique, note_comportement, note_assiduite, note_globale, commentaire)
            VALUES (:stage_id, :enc_id, :nTech, :nComp, :nAssid, :nGlob, :comment)
        ");
        return $stmt->execute([
            ':stage_id' => $stageId,
            ':enc_id' => $encadrantId,
            ':nTech' => $nTech,
            ':nComp' => $nComp,
            ':nAssid' => $nAssid,
            ':nGlob' => $noteGlobale,
            ':comment' => trim($commentaire)
        ]);
    }
}
