<?php
namespace App\Models;

use PDO;

class User extends BaseModel {
    public function findByUsername(string $username) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
        $stmt->execute([$username]);
        return $stmt->fetch();
    }
    
    public function updatePasswordHash(int $userId, string $hash) {
        $stmt = $this->db->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
        return $stmt->execute([$hash, $userId]);
    }

    public function getUserDetails(int $userId) {
        $stmt = $this->db->prepare("SELECT u.id, u.username, u.email, u.signature, u.role_id, r.name as role_name 
                                    FROM users u 
                                    LEFT JOIN roles r ON u.role_id = r.id 
                                    WHERE u.id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetch();
    }

    public function updateSignature(int $userId, string $signatureData) {
        $stmt = $this->db->prepare("UPDATE users SET signature = ? WHERE id = ?");
        return $stmt->execute([$signatureData, $userId]);
    }
}
