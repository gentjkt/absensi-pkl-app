<?php
namespace App\Models;
use PDO, PDOException;

class Database {
    private PDO $pdo;
    
    public function __construct(array $cfg) {
        $dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s',$cfg['host'],$cfg['name'],$cfg['charset']??'utf8mb4');
        $opt = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        try { $this->pdo = new PDO($dsn,$cfg['user'],$cfg['pass'],$opt); }
        catch (PDOException $e) { die('DB connection failed: '.$e->getMessage()); }
    }
    
    public function pdo(): PDO { 
        return $this->pdo; 
    }
    
    public function beginTransaction(): bool {
        return $this->pdo->beginTransaction();
    }
    
    public function commit(): bool {
        return $this->pdo->commit();
    }
    
    public function rollBack(): bool {
        return $this->pdo->rollBack();
    }
    
    public function inTransaction(): bool {
        return $this->pdo->inTransaction();
    }
    
    public function prepare(string $query) {
        return $this->pdo->prepare($query);
    }
    
    public function query(string $query) {
        return $this->pdo->query($query);
    }
    
    public function lastInsertId(string $name = null): string {
        return $this->pdo->lastInsertId($name);
    }
}