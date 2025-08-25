<?php
namespace App\Models;
use PDOException;

class User extends BaseModel {
    protected function getTableName(): string {
        return 'users';
    }
    
    public function findByUsername(string $username): ?array {
        $st = $this->db->prepare("SELECT * FROM users WHERE username = ?");
        $st->execute([$username]);
        $result = $st->fetch();
        return $result ?: null;
    }
    
    public function findById(int $id): ?array {
        $st = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $st->execute([$id]);
        $result = $st->fetch();
        return $result ?: null;
    }
    
    public function findByUsernameAndPassword(string $username, string $password): ?array {
        $st = $this->db->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
        $st->execute([$username]);
        $user = $st->fetch();
        
        if ($user && password_verify($password, $user['password_hash'])) {
            return $user;
        }
        
        return null;
    }
    
    public function listByRole(string $role): array {
        $st = $this->db->prepare("SELECT * FROM users WHERE role = ? ORDER BY name");
        $st->execute([$role]);
        return $st->fetchAll();
    }
    
    public function countByRole(string $role): int {
        $st = $this->db->prepare("SELECT COUNT(*) FROM users WHERE role = ?");
        $st->execute([$role]);
        return (int)$st->fetchColumn();
    }
    
    public function create(array $data): ?int {
        $st = $this->db->prepare("
            INSERT INTO users (username, password_hash, role, name) 
            VALUES (?, ?, ?, ?)
        ");
        
        if ($st->execute([
            $data['username'],
            $data['password_hash'],
            $data['role'],
            $data['name']
        ])) {
            return (int)$this->db->lastInsertId();
        }
        
        return null;
    }
    
    public function updatePassword(int $id, string $passwordHash): bool {
        try {
            $st = $this->db->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
            $result = $st->execute([$passwordHash, $id]);
            
            if (!$result) {
                error_log("Failed to execute password update query for user ID: " . $id);
                return false;
            }
            
            // Cek apakah ada row yang terupdate
            if ($st->rowCount() === 0) {
                error_log("No rows updated for password change. User ID: " . $id);
                return false;
            }
            
            return true;
        } catch (PDOException $e) {
            error_log("Database error updating password: " . $e->getMessage());
            return false;
        }
    }
    
    public function delete(int $id): bool {
        $st = $this->db->prepare("DELETE FROM users WHERE id = ?");
        return $st->execute([$id]);
    }
    
    public function update(int $id, array $data): bool {
        $fields = [];
        $values = [];
        
        foreach ($data as $key => $value) {
            if ($key === 'password_hash') {
                $fields[] = "password_hash = ?";
                $values[] = $value;
            } elseif ($key === 'username') {
                $fields[] = "username = ?";
                $values[] = $value;
            } elseif ($key === 'role') {
                $fields[] = "role = ?";
                $values[] = $value;
            } elseif ($key === 'name') {
                $fields[] = "name = ?";
                $values[] = $value;
            }
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $values[] = $id;
        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = ?";
        $st = $this->db->prepare($sql);
        return $st->execute($values);
    }
    
    public function count(): int {
        $st = $this->db->query("SELECT COUNT(*) FROM users");
        return (int)$st->fetchColumn();
    }
}