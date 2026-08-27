<?php
require_once __DIR__ . '/../config/database.php';

class Encadrant {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function findByUserId($userId) {
        $stmt = $this->db->prepare("
            SELECT e.*, u.nom, u.prenom, u.email, u.telephone, u.avatar 
            FROM encadrants e
            JOIN users u ON e.user_id = u.id
            WHERE e.user_id = :user_id LIMIT 1
        ");
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetch();
    }

    public function findById($id) {
        $stmt = $this->db->prepare("
            SELECT e.*, u.nom, u.prenom, u.email, u.telephone, u.avatar 
            FROM encadrants e
            JOIN users u ON e.user_id = u.id
            WHERE e.id = :id LIMIT 1
        ");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function create($userId, $departement = 'Digitalisation OCP', $fonction = 'Encadrant Digital') {
        $stmt = $this->db->prepare("
            INSERT INTO encadrants (user_id, departement, fonction)
            VALUES (:user_id, :departement, :fonction)
        ");
        $stmt->execute([
            ':user_id' => $userId,
            ':departement' => trim($departement),
            ':fonction' => trim($fonction)
        ]);
        return $this->db->lastInsertId();
    }

    public function getDashboardStats($encadrantId) {
        // Nb stagiaires encadrés
        $stmt1 = $this->db->prepare("SELECT COUNT(*) as total FROM stages WHERE encadrant_id = :enc_id AND statut = 'en_cours'");
        $stmt1->execute([':enc_id' => $encadrantId]);
        $en_cours = $stmt1->fetch()['total'];

        $stmt2 = $this->db->prepare("SELECT COUNT(*) as total FROM stages WHERE encadrant_id = :enc_id AND statut = 'termine'");
        $stmt2->execute([':enc_id' => $encadrantId]);
        $termines = $stmt2->fetch()['total'];

        // Sujets actifs
        $stmt3 = $this->db->prepare("SELECT COUNT(*) as total FROM sujets_stage WHERE encadrant_id = :enc_id");
        $stmt3->execute([':enc_id' => $encadrantId]);
        $nb_sujets = $stmt3->fetch()['total'];

        // Évaluations en attente (stages terminés ou proches sans évaluation)
        $stmt4 = $this->db->prepare("
            SELECT COUNT(*) as total 
            FROM stages st
            LEFT JOIN evaluations ev ON st.id = ev.stage_id
            WHERE st.encadrant_id = :enc_id AND ev.id IS NULL
        ");
        $stmt4->execute([':enc_id' => $encadrantId]);
        $evaluations_a_completer = $stmt4->fetch()['total'];

        return [
            'total_stagiaires' => $en_cours + $termines,
            'en_cours' => $en_cours,
            'termines' => $termines,
            'nb_sujets' => $nb_sujets,
            'evaluations_a_completer' => $evaluations_a_completer
        ];
    }

    public function getStagiaires($encadrantId, $search = null, $typeStage = null, $statut = null) {
        $sql = "
            SELECT st.id as stage_id, st.date_debut, st.date_fin, st.statut as stage_statut,
                   s.id as stagiaire_id, s.cin, s.universite, s.filiere, s.type_stage, s.user_id as stagiaire_user_id,
                   u.nom, u.prenom, u.email, u.telephone, u.avatar,
                   su.titre as sujet_titre,
                   (SELECT COALESCE(AVG(progression), 0) FROM taches WHERE stage_id = st.id) as progression_moyenne,
                   ev.note_globale
            FROM stages st
            JOIN stagiaires s ON st.stagiaire_id = s.id
            JOIN users u ON s.user_id = u.id
            JOIN sujets_stage su ON st.sujet_id = su.id
            LEFT JOIN evaluations ev ON st.id = ev.stage_id
            WHERE st.encadrant_id = :encadrant_id
        ";
        $params = [':encadrant_id' => $encadrantId];

        if (!empty($search)) {
            $sql .= " AND (u.nom LIKE :search OR u.prenom LIKE :search OR s.cin LIKE :search OR su.titre LIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }
        if (!empty($typeStage)) {
            $sql .= " AND s.type_stage = :type_stage";
            $params[':type_stage'] = $typeStage;
        }
        if (!empty($statut)) {
            $sql .= " AND st.statut = :statut";
            $params[':statut'] = $statut;
        }

        $sql .= " ORDER BY st.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Mise à jour des infos encadrant (departement, fonction)
     */
    public function update(int $id, array $data): bool {
        $stmt = $this->db->prepare("
            UPDATE encadrants SET departement = :departement, fonction = :fonction WHERE id = :id
        ");
        return $stmt->execute([
            ':departement' => trim($data['departement'] ?? ''),
            ':fonction'    => trim($data['fonction'] ?? ''),
            ':id'          => $id
        ]);
    }
}
