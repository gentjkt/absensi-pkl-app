<?php
namespace App\Models;
class AuditLog extends BaseModel {
    protected function getTableName(): string {
        return 'audit_log';
    }
    
    public function add(?int $userId, string $action, string $detail=''): void {
        $st = $this->db->prepare('INSERT INTO audit_log(user_id, action, detail) VALUES (?,?,?)');
        $st->execute([$userId, $action, $detail]);
    }
    public function latest(int $limit=200): array {
        $st = $this->db->prepare('SELECT a.*, u.username, u.name FROM audit_log a LEFT JOIN users u ON u.id=a.user_id ORDER BY a.id DESC LIMIT ?');
        $st->bindValue(1, $limit, \PDO::PARAM_INT);
        $st->execute();
        return $st->fetchAll();
    }
}