<?php
require_once __DIR__ . '/../config/database.php';

class Message {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Vérifie côté serveur que l'utilisateur participe bien au stage (RG9)
     */
    public function verifyAccess(int $stageId, int $userId): ?array {
        $stmt = $this->db->prepare("
            SELECT st.*, s.user_id as stagiaire_user_id, e.user_id as encadrant_user_id
            FROM stages st
            JOIN stagiaires s ON st.stagiaire_id = s.id
            JOIN encadrants e ON st.encadrant_id = e.id
            WHERE st.id = :stage_id
            LIMIT 1
        ");
        $stmt->execute([':stage_id' => $stageId]);
        $stage = $stmt->fetch();

        if (!$stage) return null;

        if ($userId != $stage['stagiaire_user_id'] && $userId != $stage['encadrant_user_id']) {
            return null;
        }

        return $stage;
    }

    /**
     * Récupère la conversation avec pagination / limite
     */
    public function getConversation(int $stageId, int $limit = 100, int $offset = 0): array {
        $stmt = $this->db->prepare("
            SELECT m.*, u_exp.nom as exp_nom, u_exp.prenom as exp_prenom, u_exp.avatar as exp_avatar, u_exp.role as exp_role
            FROM messages m
            JOIN users u_exp ON m.expediteur_id = u_exp.id
            WHERE m.stage_id = :stage_id
            ORDER BY m.created_at ASC, m.id ASC
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindValue(':stage_id', $stageId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Envoyez un message avec contrôle d'accès strict (RG9)
     * Note: CURRENT_TIMESTAMP est compatible MySQL et SQLite.
     */
    public function envoyer(int $stageId, int $expediteurId, int $destinataireId, string $message): int {
        $stage = $this->verifyAccess($stageId, $expediteurId);
        if (!$stage) {
            throw new Exception("Règle RG9 : Vous n'êtes pas autorisé à envoyer de message dans ce canal de stage.");
        }

        // Vérification du destinataire (doit être l'autre membre du stage)
        $expectedDest = ($expediteurId == $stage['stagiaire_user_id']) ? $stage['encadrant_user_id'] : $stage['stagiaire_user_id'];
        if ($destinataireId != $expectedDest) {
            throw new Exception("Règle RG9 : Le destinataire doit être l'interlocuteur officiel du stage.");
        }

        $now = date('Y-m-d H:i:s');
        $stmt = $this->db->prepare("
            INSERT INTO messages (stage_id, expediteur_id, destinataire_id, message, lu, created_at)
            VALUES (:stage_id, :exp_id, :dest_id, :msg, 0, :created_at)
        ");
        $stmt->execute([
            ':stage_id'   => $stageId,
            ':exp_id'     => $expediteurId,
            ':dest_id'    => $destinataireId,
            ':msg'        => trim($message),
            ':created_at' => $now
        ]);

        return (int)$this->db->lastInsertId();
    }

    /**
     * Marque tous les messages reçus dans un stage comme lus
     */
    public function marquerCommeLus(int $stageId, int $destinataireId): bool {
        $stmt = $this->db->prepare("
            UPDATE messages
            SET lu = 1
            WHERE stage_id = :stage_id AND destinataire_id = :dest_id AND lu = 0
        ");
        return $stmt->execute([':stage_id' => $stageId, ':dest_id' => $destinataireId]);
    }

    /**
     * Nombre total de messages non lus pour un utilisateur (pour le badge global)
     */
    public function getNonLusCount(int $userId): int {
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM messages WHERE destinataire_id = :user_id AND lu = 0");
        $stmt->execute([':user_id' => $userId]);
        return (int)($stmt->fetch()['total'] ?? 0);
    }

    /**
     * Nombre de messages non lus par stage (pour la liste de conversations de l'encadrant)
     */
    public function getUnreadCountPerStage(int $userId): array {
        $stmt = $this->db->prepare("
            SELECT stage_id, COUNT(*) as unread_count
            FROM messages
            WHERE destinataire_id = :user_id AND lu = 0
            GROUP BY stage_id
        ");
        $stmt->execute([':user_id' => $userId]);
        $rows = $stmt->fetchAll();
        $result = [];
        foreach ($rows as $row) {
            $result[$row['stage_id']] = (int)$row['unread_count'];
        }
        return $result;
    }
}
