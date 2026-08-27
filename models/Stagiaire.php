<?php
require_once __DIR__ . '/../config/database.php';

class Stagiaire {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function findByUserId($userId) {
        $stmt = $this->db->prepare("
            SELECT s.*, u.nom, u.prenom, u.email, u.telephone, u.avatar, u.created_at as user_created_at
            FROM stagiaires s
            JOIN users u ON s.user_id = u.id
            WHERE s.user_id = :user_id LIMIT 1
        ");
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetch();
    }

    public function findById($id) {
        $stmt = $this->db->prepare("
            SELECT s.*, u.nom, u.prenom, u.email, u.telephone, u.avatar
            FROM stagiaires s
            JOIN users u ON s.user_id = u.id
            WHERE s.id = :id LIMIT 1
        ");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function findByCin($cin) {
        $stmt = $this->db->prepare("SELECT * FROM stagiaires WHERE cin = :cin LIMIT 1");
        $stmt->execute([':cin' => strtoupper(trim($cin))]);
        return $stmt->fetch();
    }

    public function create($userId, $cin, $universite, $filiere, $niveauEtudes = 'Bac +5', $typeStage = 'PFE') {
        $stmt = $this->db->prepare("
            INSERT INTO stagiaires (user_id, cin, universite, filiere, niveau_etudes, type_stage)
            VALUES (:user_id, :cin, :universite, :filiere, :niveau_etudes, :type_stage)
        ");
        $stmt->execute([
            ':user_id'       => $userId,
            ':cin'           => strtoupper(trim($cin)),
            ':universite'    => trim($universite),
            ':filiere'       => trim($filiere),
            ':niveau_etudes' => trim($niveauEtudes),
            ':type_stage'    => $typeStage
        ]);
        return $this->db->lastInsertId();
    }

    /**
     * Mise à jour des informations spécifiques du stagiaire
     */
    public function update(int $id, array $data): bool {
        $stmt = $this->db->prepare("
            UPDATE stagiaires
            SET cin = :cin, universite = :universite, filiere = :filiere,
                niveau_etudes = :niveau_etudes, type_stage = :type_stage
            WHERE id = :id
        ");
        return $stmt->execute([
            ':cin'           => strtoupper(trim($data['cin'] ?? '')),
            ':universite'    => trim($data['universite'] ?? ''),
            ':filiere'       => trim($data['filiere'] ?? ''),
            ':niveau_etudes' => trim($data['niveau_etudes'] ?? 'Bac +5'),
            ':type_stage'    => $data['type_stage'] ?? 'PFE',
            ':id'            => $id
        ]);
    }

    public function getStageActif($stagiaireId) {
        $stmt = $this->db->prepare("
            SELECT st.*,
                   su.titre as sujet_titre, su.description as sujet_description, su.domaine as sujet_domaine,
                   u_enc.nom as encadrant_nom, u_enc.prenom as encadrant_prenom,
                   u_enc.email as encadrant_email, u_enc.telephone as encadrant_telephone,
                   u_enc.id as encadrant_user_id,
                   e.departement as encadrant_departement, e.fonction as encadrant_fonction
            FROM stages st
            JOIN sujets_stage su ON st.sujet_id = su.id
            JOIN encadrants e ON st.encadrant_id = e.id
            JOIN users u_enc ON e.user_id = u_enc.id
            WHERE st.stagiaire_id = :stagiaire_id AND st.statut = 'en_cours'
            LIMIT 1
        ");
        $stmt->execute([':stagiaire_id' => $stagiaireId]);
        return $stmt->fetch();
    }
}
