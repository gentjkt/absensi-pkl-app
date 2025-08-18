<?php
namespace App\Models;
use PDO;

abstract class BaseModel {
    protected PDO $db;
    
    public function __construct(Database $database) { 
        $this->db = $database->pdo(); 
    }
    
    // Method untuk menghitung total record
    public function count(): int {
        $table = $this->getTableName();
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM {$table}");
        $result = $stmt->fetch();
        return (int)($result['total'] ?? 0);
    }
    
    // Method untuk mendapatkan nama tabel (harus diimplementasikan di child class)
    abstract protected function getTableName(): string;
}