<?php
require_once __DIR__ . '/../config/database.php';

class Stage {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM stages WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function create($stagiaireId, $encadrantId, $sujetId, $dateDebut, $dateFin) {
        // Verification RG2: Un stagiaire n'a qu'un seul stage actif à la fois
        $stmtCheck = $this->db->prepare("SELECT id FROM stages WHERE stagiaire_id = :stagiaire_id AND statut = 'en_cours' LIMIT 1");
        $stmtCheck->execute([':stagiaire_id' => $stagiaireId]);
        if ($stmtCheck->fetch()) {
            throw new Exception("Le stagiaire a déjà un stage actif en cours.");
        }

        $stmt = $this->db->prepare("
            INSERT INTO stages (stagiaire_id, encadrant_id, sujet_id, date_debut, date_fin, statut)
            VALUES (:stagiaire_id, :encadrant_id, :sujet_id, :date_debut, :date_fin, 'en_cours')
        ");
        $stmt->execute([
            ':stagiaire_id' => $stagiaireId,
            ':encadrant_id' => $encadrantId,
            ':sujet_id' => $sujetId,
            ':date_debut' => $dateDebut,
            ':date_fin' => $dateFin
        ]);
        return $this->db->lastInsertId();
    }

    public function updateStatut($stageId, $newStatut) {
        $current = $this->findById($stageId);
        if (!$current) {
            throw new Exception("Stage non trouvé.");
        }

        // Verification RG6: Statut du stage: en_cours -> termine (pas de retour en arrière)
        if ($current['statut'] === 'termine') {
            throw new Exception("Règle RG6 : Ce stage a déjà été validé et marqué comme terminé. Action irréversible.");
        }

        $now = date('Y-m-d H:i:s');
        $stmt = $this->db->prepare("UPDATE stages SET statut = :statut, date_validation = :now WHERE id = :id");
        return $stmt->execute([':statut' => $newStatut, ':now' => $now, ':id' => $stageId]);
    }

    public function getDetailsComplets($stageId) {
        $stmt = $this->db->prepare("
            SELECT st.*, 
                   s.cin, s.universite, s.filiere, s.niveau_etudes, s.type_stage, s.user_id as stagiaire_user_id,
                   u_st.nom as stagiaire_nom, u_st.prenom as stagiaire_prenom, u_st.email as stagiaire_email, u_st.telephone as stagiaire_telephone, u_st.avatar as stagiaire_avatar,
                   u_enc.nom as encadrant_nom, u_enc.prenom as encadrant_prenom, u_enc.email as encadrant_email, u_enc.avatar as encadrant_avatar, u_enc.id as encadrant_user_id,
                   enc.departement as encadrant_departement, enc.fonction as encadrant_fonction,
                   su.titre as sujet_titre, su.description as sujet_description, su.domaine as sujet_domaine
            FROM stages st
            JOIN stagiaires s ON st.stagiaire_id = s.id
            JOIN users u_st ON s.user_id = u_st.id
            JOIN encadrants enc ON st.encadrant_id = enc.id
            JOIN users u_enc ON enc.user_id = u_enc.id
            JOIN sujets_stage su ON st.sujet_id = su.id
            WHERE st.id = :stage_id
            LIMIT 1
        ");
        $stmt->execute([':stage_id' => $stageId]);
        return $stmt->fetch();
    }
}
