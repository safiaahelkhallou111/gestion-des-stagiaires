<?php
require_once __DIR__ . '/../config/database.php';

class Notification {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getByUser(int $userId, int $limit = 50): array {
        $stmt = $this->db->prepare("
            SELECT * FROM notifications
            WHERE user_id = :user_id
            ORDER BY lu ASC, created_at DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function creer(int $userId, string $titre, string $message, ?string $lien = null): bool {
        $stmt = $this->db->prepare("
            INSERT INTO notifications (user_id, titre, message, lien, lu)
            VALUES (:user_id, :titre, :message, :lien, 0)
        ");
        return $stmt->execute([
            ':user_id' => $userId,
            ':titre'   => trim($titre),
            ':message' => trim($message),
            ':lien'    => $lien
        ]);
    }

    public function marquerCommeLue(int $id, int $userId): bool {
        $stmt = $this->db->prepare("UPDATE notifications SET lu = 1 WHERE id = :id AND user_id = :user_id");
        return $stmt->execute([':id' => $id, ':user_id' => $userId]);
    }

    /**
     * Marquer toutes les notifications d'un utilisateur comme lues
     */
    public function markAllRead(int $userId): bool {
        $stmt = $this->db->prepare("UPDATE notifications SET lu = 1 WHERE user_id = :user_id AND lu = 0");
        return $stmt->execute([':user_id' => $userId]);
    }

    public function getNonLuesCount(int $userId): int {
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM notifications WHERE user_id = :user_id AND lu = 0");
        $stmt->execute([':user_id' => $userId]);
        return (int)($stmt->fetch()['total'] ?? 0);
    }
}
