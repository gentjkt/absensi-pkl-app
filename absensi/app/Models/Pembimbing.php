<?php
namespace App\Models;

class Pembimbing extends BaseModel {
    protected function getTableName(): string {
        return 'pembimbing';
    }
    
    public function listAll(): array {
        $st = $this->db->prepare("
            SELECT 
                p.*, 
                COALESCE(siswa_count.total_siswa, 0) as total_siswa,
                u.username,
                u.role as user_role,
                u.name as user_name
            FROM pembimbing p
            LEFT JOIN (
                SELECT pembimbing_id, COUNT(*) as total_siswa
                FROM siswa 
                GROUP BY pembimbing_id
            ) siswa_count ON p.id = siswa_count.pembimbing_id
            LEFT JOIN users u ON p.user_id = u.id
            ORDER BY p.nama ASC
        ");
        $st->execute();
        return $st->fetchAll();
    }
    
    public function findById(int $id): ?array {
        $st = $this->db->prepare("
            SELECT p.*, u.username, u.role
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
            
            // Timestamp sudah tersedia dari pembimbing table (p.created_at, p.updated_at)
            // Tidak perlu fallback karena pembimbing table sudah memiliki kolom tersebut
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
        $fields = [];
        $values = [];
        
        // Handle field yang dapat diupdate
        if (isset($data['nama'])) {
            $fields[] = "nama = ?";
            $values[] = $data['nama'];
        }
        
        if (isset($data['nip'])) {
            $fields[] = "nip = ?";
            $values[] = $data['nip'];
        }
        
        if (isset($data['user_id'])) {
            $fields[] = "user_id = ?";
            $values[] = $data['user_id'];
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $values[] = $id;
        $sql = "UPDATE pembimbing SET " . implode(', ', $fields) . " WHERE id = ?";
        $st = $this->db->prepare($sql);
        return $st->execute($values);
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
    
    /**
     * Mendapatkan statistik pembimbing yang lebih detail
     */
    public function getPembimbingStats(): array {
        $st = $this->db->prepare("
            SELECT 
                p.id,
                p.nama,
                p.nip,
                COALESCE(siswa_count.total_siswa, 0) as total_siswa,
                CASE 
                    WHEN COALESCE(siswa_count.total_siswa, 0) > 0 THEN 'aktif'
                    ELSE 'tidak_aktif'
                END as status,
                CASE 
                    WHEN u.id IS NOT NULL THEN 'ada'
                    ELSE 'belum'
                END as status_user,
                u.username,
                p.created_at,
                p.updated_at
            FROM pembimbing p
            LEFT JOIN (
                SELECT pembimbing_id, COUNT(*) as total_siswa
                FROM siswa 
                GROUP BY pembimbing_id
            ) siswa_count ON p.id = siswa_count.pembimbing_id
            LEFT JOIN users u ON p.user_id = u.id
            ORDER BY p.nama ASC
        ");
        $st->execute();
        return $st->fetchAll();
    }
    
    /**
     * Verifikasi perhitungan total siswa untuk debugging
     */
    public function verifySiswaCount(): array {
        $st = $this->db->prepare("
            SELECT 
                p.id,
                p.nama,
                p.nip,
                COUNT(s.id) as actual_count,
                GROUP_CONCAT(s.nama SEPARATOR ', ') as nama_siswa
            FROM pembimbing p
            LEFT JOIN siswa s ON p.id = s.pembimbing_id
            GROUP BY p.id, p.nama, p.nip
            ORDER BY p.nama ASC
        ");
        $st->execute();
        return $st->fetchAll();
    }
}
