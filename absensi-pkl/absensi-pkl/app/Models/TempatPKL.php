<?php
namespace App\Models;

class TempatPKL extends BaseModel {
    protected function getTableName(): string {
        return 'tempat_pkl';
    }
    
    public function byId(int $id): ?array {
        $st = $this->db->prepare("SELECT * FROM tempat_pkl WHERE id = ?");
        $st->execute([$id]);
        $result = $st->fetch();
        return $result ?: null;
    }
    
    public function findById(int $id): ?array {
        return $this->byId($id);
    }
    
    public function create(array $data): bool {
        $st = $this->db->prepare("
            INSERT INTO tempat_pkl (nama, lat, lng, radius_m) 
            VALUES (?, ?, ?, ?)
        ");
        return $st->execute([
            $data['nama'],
            $data['lat'],
            $data['lng'],
            $data['radius_m']
        ]);
    }
    
    public function update(int $id, array $data): bool {
        $st = $this->db->prepare("
            UPDATE tempat_pkl 
            SET nama = ?, lat = ?, lng = ?, radius_m = ? 
            WHERE id = ?
        ");
        return $st->execute([
            $data['nama'],
            $data['lat'],
            $data['lng'],
            $data['radius_m'],
            $id
        ]);
    }
    
    public function delete(int $id): bool {
        $st = $this->db->prepare("DELETE FROM tempat_pkl WHERE id = ?");
        return $st->execute([$id]);
    }
    
    public function listAll(): array {
        $st = $this->db->prepare("
            SELECT t.*, COUNT(s.id) as total_siswa
            FROM tempat_pkl t
            LEFT JOIN siswa s ON t.id = s.tempat_pkl_id
            GROUP BY t.id
            ORDER BY t.nama ASC
        ");
        $st->execute();
        return $st->fetchAll();
    }
    
    public function count(): int {
        $st = $this->db->query("SELECT COUNT(*) FROM tempat_pkl");
        return (int)$st->fetchColumn();
    }
}