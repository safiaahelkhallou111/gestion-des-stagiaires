<?php
require_once __DIR__ . '/../config/database.php';

class User {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function findByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => strtolower(trim($email))]);
        return $stmt->fetch();
    }

    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function create($role, $nom, $prenom, $email, $password, $telephone = null) {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $this->db->prepare("
            INSERT INTO users (role, nom, prenom, email, password, telephone)
            VALUES (:role, :nom, :prenom, :email, :password, :telephone)
        ");
        $stmt->execute([
            ':role'      => $role,
            ':nom'       => strtoupper(trim($nom)),
            ':prenom'    => ucfirst(trim($prenom)),
            ':email'     => strtolower(trim($email)),
            ':password'  => $hash,
            ':telephone' => $telephone
        ]);
        return $this->db->lastInsertId();
    }

    /**
     * Mise à jour du profil utilisateur (nom, prenom, email, telephone)
     */
    public function updateProfil(int $id, array $data): bool {
        $stmt = $this->db->prepare("
            UPDATE users
            SET nom = :nom, prenom = :prenom, email = :email, telephone = :telephone
            WHERE id = :id
        ");
        return $stmt->execute([
            ':nom'       => strtoupper(trim($data['nom'] ?? '')),
            ':prenom'    => ucfirst(trim($data['prenom'] ?? '')),
            ':email'     => strtolower(trim($data['email'] ?? '')),
            ':telephone' => trim($data['telephone'] ?? ''),
            ':id'        => $id
        ]);
    }

    /**
     * Mise à jour de la photo de profil (avatar)
     */
    public function updateAvatar(int $id, string $avatarFilename): bool {
        $stmt = $this->db->prepare("UPDATE users SET avatar = :avatar WHERE id = :id");
        return $stmt->execute([':avatar' => $avatarFilename, ':id' => $id]);
    }

    public function updatePassword($id, $newPassword) {
        $hash = password_hash($newPassword, PASSWORD_BCRYPT);
        $stmt = $this->db->prepare("UPDATE users SET password = :password, reset_token = NULL, reset_expires = NULL WHERE id = :id");
        return $stmt->execute([':password' => $hash, ':id' => $id]);
    }

    public function setResetToken($email, $token, $expires) {
        $stmt = $this->db->prepare("UPDATE users SET reset_token = :token, reset_expires = :expires WHERE email = :email");
        return $stmt->execute([':token' => $token, ':expires' => $expires, ':email' => $email]);
    }

    public function findByResetToken($token) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE reset_token = :token AND reset_expires > :now LIMIT 1");
        $stmt->execute([':token' => $token, ':now' => date('Y-m-d H:i:s')]);
        return $stmt->fetch();
    }
}
