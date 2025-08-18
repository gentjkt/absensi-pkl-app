<?php
namespace App\Models;

class Pembimbing extends BaseModel {
    protected function getTableName(): string {
        return 'pembimbing';
    }
    
    public function listAll(): array {
        $st = $this->db->prepare("
            SELECT p.*, COUNT(s.id) as total_siswa, u.username
            FROM pembimbing p
            LEFT JOIN siswa s ON p.id = s.pembimbing_id
            LEFT JOIN users u ON p.user_id = u.id
            GROUP BY p.id
            ORDER BY p.nama ASC
        ");
        $st->execute();
        return $st->fetchAll();
    }
    
    public function findById(int $id): ?array {
        $st = $this->db->prepare("
            SELECT p.*, u.username, u.role, u.created_at as user_created_at, u.updated_at as user_updated_at
            FROM pembimbing p
            LEFT JOIN users u ON p.user_id = u.id
            WHERE p.id = ?
        ");
        $st->execute([$id]);
        $result = $st->fetch();
        
        if ($result) {
            // Tambahkan field yang tidak ada dengan nilai default
            $result['email'] = null;
            $result['no_telp'] = null;
            $result['instansi'] = null;
            
            // Gunakan timestamp dari users table jika ada, fallback ke pembimbing table
            if (!empty($result['user_created_at'])) {
                $result['created_at'] = $result['user_created_at'];
            }
            if (!empty($result['user_updated_at'])) {
                $result['updated_at'] = $result['user_updated_at'];
            }
        }
        
        return $result ?: null;
    }
    
    public function findByNIP(string $nip): ?array {
        $st = $this->db->prepare("SELECT * FROM pembimbing WHERE nip = ?");
        $st->execute([$nip]);
        $result = $st->fetch();
        return $result ?: null;
    }
    
    public function create(array $data): bool {
        $st = $this->db->prepare("
            INSERT INTO pembimbing (nama, nip) 
            VALUES (?, ?)
        ");
        return $st->execute([
            $data['nama'],
            $data['nip']
        ]);
    }
    
    public function update(int $id, array $data): bool {
        $st = $this->db->prepare("
            UPDATE pembimbing 
            SET nama = ?, nip = ? 
            WHERE id = ?
        ");
        return $st->execute([
            $data['nama'],
            $data['nip'],
            $id
        ]);
    }
    
    public function delete(int $id): bool {
        $st = $this->db->prepare("DELETE FROM pembimbing WHERE id = ?");
        return $st->execute([$id]);
    }
    
    public function count(): int {
        $st = $this->db->query("SELECT COUNT(*) FROM pembimbing");
        return (int)$st->fetchColumn();
    }
    
    public function getSiswaByPembimbing(int $pembimbingId): array {
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
}
