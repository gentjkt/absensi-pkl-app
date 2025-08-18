<?php
namespace App\Models;

class Siswa extends BaseModel {
    protected function getTableName(): string {
        return 'siswa';
    }
    
    public function findByUserId(int $userId): ?array {
        $st = $this->db->prepare("
            SELECT s.*, u.name as pembimbing, t.nama as tempat_pkl 
            FROM siswa s 
            LEFT JOIN users u ON s.pembimbing_id = u.id 
            LEFT JOIN tempat_pkl t ON s.tempat_pkl_id = t.id 
            WHERE s.user_id = ? 
            LIMIT 1
        ");
        $st->execute([$userId]);
        $result = $st->fetch();
        return $result ?: null;
    }
    
    public function findByNIS(string $nis): ?array {
        $st = $this->db->prepare("
            SELECT s.*, u.name as pembimbing, t.nama as tempat_pkl 
            FROM siswa s 
            LEFT JOIN users u ON s.pembimbing_id = u.id 
            LEFT JOIN tempat_pkl t ON s.tempat_pkl_id = t.id 
            WHERE s.nis = ? 
            LIMIT 1
        ");
        $st->execute([$nis]);
        $result = $st->fetch();
        return $result ?: null;
    }
    
    public function findByPembimbingId(int $pembimbingId): array {
        $st = $this->db->prepare("
            SELECT s.*, t.nama as tempat_pkl 
            FROM siswa s 
            LEFT JOIN tempat_pkl t ON s.tempat_pkl_id = t.id 
            WHERE s.pembimbing_id = ? 
            ORDER BY s.nama ASC
        ");
        $st->execute([$pembimbingId]);
        return $st->fetchAll();
    }
    
    public function findById(int $id): ?array {
        $st = $this->db->prepare("
            SELECT s.*, s.user_id, u.name as pembimbing, t.nama as tempat_pkl 
            FROM siswa s 
            LEFT JOIN users u ON s.pembimbing_id = u.id 
            LEFT JOIN tempat_pkl t ON s.tempat_pkl_id = t.id 
            WHERE s.id = ? 
            LIMIT 1
        ");
        $st->execute([$id]);
        $result = $st->fetch();
        return $result ?: null;
    }
    
    public function listByPembimbing(int $pembimbingId): array {
        $st = $this->db->prepare("
            SELECT s.*, t.nama as tempat_pkl, t.lat, t.lng
            FROM siswa s 
            LEFT JOIN tempat_pkl t ON s.tempat_pkl_id = t.id
            WHERE s.pembimbing_id = ?
            ORDER BY s.nama ASC
        ");
        $st->execute([$pembimbingId]);
        return $st->fetchAll();
    }
    
    public function findByTempatPKL(int $tempatPKLId): array {
        $st = $this->db->prepare("
            SELECT * FROM siswa 
            WHERE tempat_pkl_id = ?
            ORDER BY nama ASC
        ");
        $st->execute([$tempatPKLId]);
        return $st->fetchAll();
    }
    
    public function listAll(): array {
        $st = $this->db->query("
            SELECT s.*, u.name as pembimbing, t.nama as tempat_pkl, t.lat, t.lng 
            FROM siswa s 
            LEFT JOIN users u ON s.pembimbing_id = u.id 
            LEFT JOIN tempat_pkl t ON s.tempat_pkl_id = t.id 
            ORDER BY s.nama
        ");
        return $st->fetchAll();
    }
    
    public function create(array $data): bool {
        $st = $this->db->prepare("
            INSERT INTO siswa (nis, nama, kelas, pembimbing_id, tempat_pkl_id, user_id) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        return $st->execute([
            $data['nis'],
            $data['nama'],
            $data['kelas'],
            $data['pembimbing_id'],
            $data['tempat_pkl_id'],
            $data['user_id'] ?? null
        ]);
    }
    
    public function update(int $id, array $data): bool {
        $st = $this->db->prepare("
            UPDATE siswa 
            SET nama = ?, kelas = ?, pembimbing_id = ?, tempat_pkl_id = ?
            WHERE id = ?
        ");
        return $st->execute([
            $data['nama'],
            $data['kelas'],
            $data['pembimbing_id'],
            $data['tempat_pkl_id'],
            $id
        ]);
    }
    
    public function delete(int $id): bool {
        $st = $this->db->prepare("DELETE FROM siswa WHERE id = ?");
        return $st->execute([$id]);
    }
    
    public function count(): int {
        $st = $this->db->query("SELECT COUNT(*) FROM siswa");
        return (int)$st->fetchColumn();
    }
    
    public function searchSiswa(array $filters): array {
        $sql = "
            SELECT s.*, u.name as pembimbing, t.nama as tempat_pkl, t.lat, t.lng 
            FROM siswa s 
            LEFT JOIN users u ON s.pembimbing_id = u.id 
            LEFT JOIN tempat_pkl t ON s.tempat_pkl_id = t.id 
            WHERE 1=1
        ";
        
        $params = [];
        
        // Filter berdasarkan NIS
        if (!empty($filters['nis'])) {
            $sql .= " AND s.nis LIKE ?";
            $params[] = '%' . $filters['nis'] . '%';
        }
        
        // Filter berdasarkan nama
        if (!empty($filters['nama'])) {
            $sql .= " AND s.nama LIKE ?";
            $params[] = '%' . $filters['nama'] . '%';
        }
        
        // Filter berdasarkan kelas
        if (!empty($filters['kelas'])) {
            $sql .= " AND s.kelas LIKE ?";
            $params[] = '%' . $filters['kelas'] . '%';
        }
        
        // Filter berdasarkan pembimbing
        if (!empty($filters['pembimbing'])) {
            $sql .= " AND u.name LIKE ?";
            $params[] = '%' . $filters['pembimbing'] . '%';
        }
        
        // Filter berdasarkan tempat PKL
        if (!empty($filters['tempat_pkl'])) {
            $sql .= " AND t.nama LIKE ?";
            $params[] = '%' . $filters['tempat_pkl'] . '%';
        }
        
        $sql .= " ORDER BY s.nama ASC";
        
        $st = $this->db->prepare($sql);
        $st->execute($params);
        return $st->fetchAll();
    }
}