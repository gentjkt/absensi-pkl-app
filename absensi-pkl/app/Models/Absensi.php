<?php
namespace App\Models;

class Absensi extends BaseModel {
    protected function getTableName(): string {
        return 'absensi';
    }
    
    public function listBySiswa(int $siswaId): array {
        $st = $this->db->prepare("
            SELECT a.*, s.nama as siswa 
            FROM absensi a 
            JOIN siswa s ON a.siswa_id = s.id 
            WHERE a.siswa_id = ?
            ORDER BY a.waktu DESC
        ");
        $st->execute([$siswaId]);
        return $st->fetchAll();
    }
    
    public function listByPembimbing(int $pembimbingId): array {
        $st = $this->db->prepare("
            SELECT a.*, s.nama as siswa, s.kelas, s.nis, t.nama as tempat_pkl
            FROM absensi a 
            JOIN siswa s ON a.siswa_id = s.id 
            LEFT JOIN tempat_pkl t ON s.tempat_pkl_id = t.id
            WHERE s.pembimbing_id = ?
            ORDER BY a.waktu DESC
        ");
        $st->execute([$pembimbingId]);
        return $st->fetchAll();
    }
    
    public function getMonthlyReport(int $pembimbingId, int $month, int $year): array {
        $st = $this->db->prepare("
            SELECT a.*, s.nama as siswa, s.kelas, s.nis, s.pembimbing_id
            FROM absensi a 
            JOIN siswa s ON a.siswa_id = s.id 
            WHERE s.pembimbing_id = ? 
            AND MONTH(a.waktu) = ? 
            AND YEAR(a.waktu) = ?
            ORDER BY a.waktu ASC
        ");
        $st->execute([$pembimbingId, $month, $year]);
        return $st->fetchAll();
    }
    
    public function getLatestAbsensi(int $limit = 10): array {
        $st = $this->db->prepare("
            SELECT a.*, s.nama as siswa, s.nis, s.kelas, t.nama as tempat_pkl
            FROM absensi a 
            JOIN siswa s ON a.siswa_id = s.id 
            LEFT JOIN tempat_pkl t ON s.tempat_pkl_id = t.id
            ORDER BY a.waktu DESC 
            LIMIT ?
        ");
        $st->execute([$limit]);
        return $st->fetchAll();
    }
    
    public function getAbsensiWithPagination(int $offset, int $limit): array {
        $st = $this->db->prepare("
            SELECT a.*, s.nama as siswa, s.nis, s.kelas, t.nama as tempat_pkl
            FROM absensi a 
            JOIN siswa s ON a.siswa_id = s.id 
            LEFT JOIN tempat_pkl t ON s.tempat_pkl_id = t.id
            ORDER BY a.waktu DESC 
            LIMIT ? OFFSET ?
        ");
        $st->execute([$limit, $offset]);
        return $st->fetchAll();
    }
    
    public function getAbsensiByDate(string $date): array {
        $st = $this->db->prepare("
            SELECT a.*, s.nama as siswa, s.nis, s.kelas, t.nama as tempat_pkl
            FROM absensi a 
            JOIN siswa s ON a.siswa_id = s.id 
            LEFT JOIN tempat_pkl t ON s.tempat_pkl_id = t.id
            WHERE DATE(a.waktu) = ?
            ORDER BY a.waktu DESC
        ");
        $st->execute([$date]);
        return $st->fetchAll();
    }
    
    public function getAbsensiByDateRange(string $startDate, string $endDate): array {
        $st = $this->db->prepare("
            SELECT a.*, s.nama as siswa, s.nis, s.kelas, t.nama as tempat_pkl
            FROM absensi a 
            JOIN siswa s ON a.siswa_id = s.id 
            LEFT JOIN tempat_pkl t ON s.tempat_pkl_id = t.id
            WHERE DATE(a.waktu) BETWEEN ? AND ?
            ORDER BY a.waktu DESC
        ");
        $st->execute([$startDate, $endDate]);
        return $st->fetchAll();
    }
    
    public function getAbsensiAfterTime(string $time): array {
        $st = $this->db->prepare("
            SELECT a.*, s.nama as siswa, s.nis, s.kelas, t.nama as tempat_pkl
            FROM absensi a 
            JOIN siswa s ON a.siswa_id = s.id 
            LEFT JOIN tempat_pkl t ON s.tempat_pkl_id = t.id
            WHERE a.waktu > ?
            ORDER BY a.waktu DESC
        ");
        $st->execute([$time]);
        return $st->fetchAll();
    }
    
    public function countByPembimbing(int $pembimbingId): int {
        $st = $this->db->prepare("
            SELECT COUNT(*) as total
            FROM absensi a 
            JOIN siswa s ON a.siswa_id = s.id 
            WHERE s.pembimbing_id = ?
        ");
        $st->execute([$pembimbingId]);
        $result = $st->fetch();
        return (int)($result['total'] ?? 0);
    }
    
    public function getLatestByPembimbing(int $pembimbingId, int $limit = 10): array {
        $st = $this->db->prepare("
            SELECT a.*, s.nama as nama_siswa, s.nis, s.kelas, t.nama as tempat_pkl
            FROM absensi a 
            JOIN siswa s ON a.siswa_id = s.id 
            LEFT JOIN tempat_pkl t ON s.tempat_pkl_id = t.id
            WHERE s.pembimbing_id = ?
            ORDER BY a.waktu DESC 
            LIMIT ?
        ");
        $st->execute([$pembimbingId, $limit]);
        return $st->fetchAll();
    }
    
    public function getAbsensiBySiswaAndDate(int $siswaId, string $date): array {
        $st = $this->db->prepare("
            SELECT * FROM absensi 
            WHERE siswa_id = ? AND DATE(waktu) = ? 
            ORDER BY waktu ASC
        ");
        $st->execute([$siswaId, $date]);
        return $st->fetchAll();
    }
    
    public function getAbsensiForReport(string $start, string $end, string $kelas = '', int $tempat = 0): array {
        $sql = "
            SELECT 
                s.nama as siswa,
                s.nis,
                s.kelas,
                t.nama as tempat,
                a.waktu,
                a.lat,
                a.lng,
                a.jarak_m,
                a.jenis_absen,
                a.selfie_path
            FROM absensi a
            JOIN siswa s ON a.siswa_id = s.id
            JOIN tempat_pkl t ON s.tempat_pkl_id = t.id
            WHERE DATE(a.waktu) BETWEEN ? AND ?
        ";
        
        $params = [$start, $end];
        
        if (!empty($kelas)) {
            $sql .= " AND s.kelas LIKE ?";
            $params[] = "%$kelas%";
        }
        
        if ($tempat > 0) {
            $sql .= " AND s.tempat_pkl_id = ?";
            $params[] = $tempat;
        }
        
        $sql .= " ORDER BY a.waktu DESC";
        
        $st = $this->db->prepare($sql);
        $st->execute($params);
        return $st->fetchAll();
    }
    
    public function getAbsensiHariIni(int $siswaId): array {
        $today = date('Y-m-d');
        return $this->getAbsensiBySiswaAndDate($siswaId, $today);
    }
    
    public function sudahAbsenDatang(int $siswaId, string $date): bool {
        $st = $this->db->prepare("
            SELECT COUNT(*) FROM absensi 
            WHERE siswa_id = ? AND DATE(waktu) = ? AND jenis_absen = 'datang'
        ");
        $st->execute([$siswaId, $date]);
        return (int)$st->fetchColumn() > 0;
    }
    
    public function sudahAbsenPulang(int $siswaId, string $date): bool {
        $st = $this->db->prepare("
            SELECT COUNT(*) FROM absensi 
            WHERE siswa_id = ? AND DATE(waktu) = ? AND jenis_absen = 'pulang'
        ");
        $st->execute([$siswaId, $date]);
        return (int)$st->fetchColumn() > 0;
    }
    
    public function listAll(): array {
        return $this->db->query("
            SELECT a.*, s.nama as siswa 
            FROM absensi a 
            JOIN siswa s ON a.siswa_id = s.id 
            ORDER BY a.waktu DESC
        ")->fetchAll();
    }
    
    public function create(array $data): bool {
        $st = $this->db->prepare("
            INSERT INTO absensi (siswa_id, waktu, lat, lng, jarak_m, selfie_path, jenis_absen) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        return $st->execute([
            $data['siswa_id'],
            $data['waktu'],
            $data['lat'],
            $data['lng'],
            $data['jarak_m'],
            $data['selfie_path'] ?? null,
            $data['jenis_absen'] ?? 'datang'
        ]);
    }
    
    public function deleteBySiswaId(int $siswaId): bool {
        $st = $this->db->prepare("DELETE FROM absensi WHERE siswa_id = ?");
        return $st->execute([$siswaId]);
    }
}