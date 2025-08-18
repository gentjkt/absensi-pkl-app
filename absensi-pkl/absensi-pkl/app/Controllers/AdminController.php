<?php
namespace App\Controllers;
use App\Helpers\Auth;
use App\Models\{Database, Siswa, TempatPKL, User, AuditLog, Absensi, Pembimbing};
use Exception;
use DateTime;
use DateTimeZone;

// Set timezone ke WIB
date_default_timezone_set('Asia/Jakarta');

class AdminController extends Controller {
    public function dashboard(): void {
        Auth::requireRole('admin');
        $db = new Database(require __DIR__.'/../../config/db.php');
        $totalSiswa = (new Siswa($db))->count();
        $totalTempat = (new TempatPKL($db))->count();
        $totalAbsensi = (new Absensi($db))->count();
        
        // Ambil absensi terbaru untuk dashboard
        $absensi = new Absensi($db);
        $absensiTerbaru = $absensi->getLatestAbsensi(10); // 10 absensi terbaru
        
        $this->view('admin/dashboard', [
            'totalSiswa' => $totalSiswa,
            'totalTempat' => $totalTempat,
            'totalAbsensi' => $totalAbsensi,
            'absensiTerbaru' => $absensiTerbaru
        ]);
    }
    
    public function absensi(): void {
        Auth::requireRole('admin');
        $db = new Database(require __DIR__.'/../../config/db.php');
        
        // Ambil parameter filter
        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = 20;
        $offset = ($page - 1) * $limit;
        
        $absensi = new Absensi($db);
        $totalAbsensi = $absensi->count();
        $totalPages = ceil($totalAbsensi / $limit);
        
        // Ambil data absensi dengan pagination
        $listAbsensi = $absensi->getAbsensiWithPagination($offset, $limit);
        
        // Statistik absensi hari ini (WIB)
        $today = (new DateTime('now', new DateTimeZone('Asia/Jakarta')))->format('Y-m-d');
        $absensiHariIni = $absensi->getAbsensiByDate($today);
        $totalHariIni = count($absensiHariIni);
        
        // Statistik absensi minggu ini (WIB)
        $weekStart = (new DateTime('monday this week', new DateTimeZone('Asia/Jakarta')))->format('Y-m-d');
        $weekEnd = (new DateTime('sunday this week', new DateTimeZone('Asia/Jakarta')))->format('Y-m-d');
        $absensiMingguIni = $absensi->getAbsensiByDateRange($weekStart, $weekEnd);
        $totalMingguIni = count($absensiMingguIni);
        
        $this->view('admin/absensi', [
            'absensi' => $listAbsensi,
            'totalAbsensi' => $totalAbsensi,
            'totalHariIni' => $totalHariIni,
            'totalMingguIni' => $totalMingguIni,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'limit' => $limit
        ]);
    }
    
    public function absensiRealtime(): void {
        Auth::requireRole('admin');
        header('Content-Type: application/json');
        
        $db = new Database(require __DIR__.'/../../config/db.php');
        $absensi = new Absensi($db);
        
        // Ambil absensi terbaru (5 menit terakhir) - WIB
        $recentTime = (new DateTime('-5 minutes', new DateTimeZone('Asia/Jakarta')))->format('Y-m-d H:i:s');
        $absensiRealtime = $absensi->getAbsensiAfterTime($recentTime);
        
        // Hitung statistik realtime (WIB)
        $totalHariIni = $absensi->getAbsensiByDate((new DateTime('now', new DateTimeZone('Asia/Jakarta')))->format('Y-m-d'));
        $totalMingguIni = $absensi->getAbsensiByDateRange(
            (new DateTime('monday this week', new DateTimeZone('Asia/Jakarta')))->format('Y-m-d'),
            (new DateTime('sunday this week', new DateTimeZone('Asia/Jakarta')))->format('Y-m-d')
        );
        
        echo json_encode([
            'success' => true,
            'data' => [
                'recent_absensi' => $absensiRealtime,
                'total_hari_ini' => count($totalHariIni),
                'total_minggu_ini' => count($totalMingguIni),
                'last_update' => (new DateTime('now', new DateTimeZone('Asia/Jakarta')))->format('Y-m-d H:i:s'),
                'online_users' => $this->getOnlineUsers()
            ]
        ]);
    }
    
    private function getOnlineUsers(): int {
        // Hitung user yang aktif dalam 15 menit terakhir (WIB)
        $db = new Database(require __DIR__.'/../../config/db.php');
        $recentTime = (new DateTime('-15 minutes', new DateTimeZone('Asia/Jakarta')))->format('Y-m-d H:i:s');
        
        $stmt = $db->prepare("
            SELECT COUNT(DISTINCT user_id) as online_count 
            FROM audit_log 
            WHERE waktu > ? AND action IN ('login', 'absen')
        ");
        $stmt->execute([$recentTime]);
        $result = $stmt->fetch();
        
        return (int)($result['online_count'] ?? 0);
    }
    
    public function siswa(): void {
        Auth::requireRole('admin');
        $db = new Database(require __DIR__.'/../../config/db.php');
        $siswa = new Siswa($db);
        
        // Ambil parameter pencarian
        $searchNIS = trim($_GET['search_nis'] ?? '');
        $searchNama = trim($_GET['search_nama'] ?? '');
        $searchKelas = trim($_GET['search_kelas'] ?? '');
        $searchPembimbing = trim($_GET['search_pembimbing'] ?? '');
        $searchTempat = trim($_GET['search_tempat'] ?? '');
        
        // Jika ada parameter pencarian, gunakan method pencarian
        if (!empty($searchNIS) || !empty($searchNama) || !empty($searchKelas) || 
            !empty($searchPembimbing) || !empty($searchTempat)) {
            $listSiswa = $siswa->searchSiswa([
                'nis' => $searchNIS,
                'nama' => $searchNama,
                'kelas' => $searchKelas,
                'pembimbing' => $searchPembimbing,
                'tempat_pkl' => $searchTempat
            ]);
        } else {
            // Jika tidak ada pencarian, tampilkan semua data
            $listSiswa = $siswa->listAll();
        }
        
        // Ambil data untuk dropdown pembimbing dan tempat PKL
        $pembimbing = (new User($db))->listByRole('pembimbing');
        $tempatPKL = (new TempatPKL($db))->listAll();
        
        $this->view('admin/siswa', [
            'siswa' => $listSiswa,
            'pembimbing' => $pembimbing,
            'tempatPKL' => $tempatPKL
        ]);
    }
    
    public function siswaAdd(): void {
        Auth::requireRole('admin');
        $db = new Database(require __DIR__.'/../../config/db.php');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->siswaAddProcess($db);
            return;
        }
        
        $pembimbing = (new User($db))->listByRole('pembimbing');
        $tempatPKL = (new TempatPKL($db))->listAll();
        
        $this->view('admin/siswa_add', [
            'pembimbing' => $pembimbing,
            'tempatPKL' => $tempatPKL
        ]);
    }
    
    private function siswaAddProcess($db): void {
        $nis = trim($_POST['nis'] ?? '');
        $nama = trim($_POST['nama'] ?? '');
        $kelas = trim($_POST['kelas'] ?? '');
        $pembimbing_id = (int)($_POST['pembimbing_id'] ?? 0);
        $tempat_pkl_id = (int)($_POST['tempat_pkl_id'] ?? 0);
        $password = trim($_POST['password'] ?? '123456');
        
        // Validasi input
        if (empty($nis) || empty($nama) || empty($kelas)) {
            $this->view('admin/siswa_add', [
                'error' => 'NIS, Nama, dan Kelas harus diisi!',
                'oldData' => $_POST,
                'pembimbing' => (new User($db))->listByRole('pembimbing'),
                'tempatPKL' => (new TempatPKL($db))->listAll()
            ]);
            return;
        }
        
        $siswa = new Siswa($db);
        $user = new User($db);
        
        // Cek NIS sudah ada atau belum
        if ($siswa->findByNIS($nis)) {
            $this->view('admin/siswa_add', [
                'error' => 'NIS sudah terdaftar!',
                'oldData' => $_POST,
                'pembimbing' => (new User($db))->listByRole('pembimbing'),
                'tempatPKL' => (new TempatPKL($db))->listAll()
            ]);
            return;
        }
        
        // Cek username sudah ada atau belum
        if ($user->findByUsername($nis)) {
            $this->view('admin/siswa_add', [
                'error' => 'Username (NIS) sudah terdaftar!',
                'oldData' => $_POST,
                'pembimbing' => (new User($db))->listByRole('pembimbing'),
                'tempatPKL' => (new TempatPKL($db))->listAll()
            ]);
            return;
        }
        
        try {
            $db->beginTransaction();
            
            // Buat user account
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $user_id = $user->create([
                'username' => $nis,
                'password_hash' => $password_hash,
                'role' => 'siswa',
                'name' => $nama
            ]);
            
            if (!$user_id) {
                throw new Exception('Gagal membuat user account');
            }
            
            // Buat data siswa
            $success = $siswa->create([
                'nis' => $nis,
                'nama' => $nama,
                'kelas' => $kelas,
                'pembimbing_id' => $pembimbing_id,
                'tempat_pkl_id' => $tempat_pkl_id,
                'user_id' => $user_id
            ]);
            
            if (!$success) {
                throw new Exception('Gagal membuat data siswa');
            }
            
            // Log audit
            (new AuditLog($db))->add($_SESSION['user']['id'], 'tambah_siswa', "NIS: $nis, Nama: $nama");
            
            $db->commit();
            
            // Redirect ke halaman siswa dengan pesan sukses
            header('Location: ?r=admin/siswa&success=1');
            exit;
            
        } catch (Exception $e) {
            $db->rollBack();
            $this->view('admin/siswa_add', [
                'error' => 'Gagal menambah siswa: ' . $e->getMessage(),
                'oldData' => $_POST,
                'pembimbing' => (new User($db))->listByRole('pembimbing'),
                'tempatPKL' => (new TempatPKL($db))->listAll()
            ]);
        }
    }
    
    public function siswaEdit(): void {
        Auth::requireRole('admin');
        $db = new Database(require __DIR__.'/../../config/db.php');
        $siswa_id = (int)($_GET['id'] ?? 0);
        
        if (!$siswa_id) {
            header('Location: ?r=admin/siswa&error=ID siswa tidak valid');
            exit;
        }
        
        $siswa = new Siswa($db);
        $dataSiswa = $siswa->findById($siswa_id);
        
        if (!$dataSiswa) {
            header('Location: ?r=admin/siswa&error=Siswa tidak ditemukan');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->siswaEditProcess($db, $siswa_id, $dataSiswa);
            return;
        }
        
        $pembimbing = (new User($db))->listByRole('pembimbing');
        $tempatPKL = (new TempatPKL($db))->listAll();
        
        $this->view('admin/siswa_edit', [
            'siswa' => $dataSiswa,
            'pembimbing' => $pembimbing,
            'tempatPKL' => $tempatPKL
        ]);
    }
    
    private function siswaEditProcess($db, $siswa_id, $dataSiswa): void {
        // Debug: log struktur data siswa
        error_log("Debug siswaEditProcess - dataSiswa: " . json_encode($dataSiswa));
        error_log("Debug siswaEditProcess - POST data: " . json_encode($_POST));
        
        $nama = trim($_POST['nama'] ?? '');
        $kelas = trim($_POST['kelas'] ?? '');
        $pembimbing_id = (int)($_POST['pembimbing_id'] ?? 0);
        $tempat_pkl_id = (int)($_POST['tempat_pkl_id'] ?? 0);
        $password = trim($_POST['password'] ?? '');
        
        // Validasi input
        if (empty($nama) || empty($kelas)) {
            $this->view('admin/siswa_edit', [
                'error' => 'Nama dan Kelas harus diisi!',
                'siswa' => $dataSiswa,
                'oldData' => $_POST,
                'pembimbing' => (new User($db))->listByRole('pembimbing'),
                'tempatPKL' => (new TempatPKL($db))->listAll()
            ]);
            return;
        }
        
        $siswa = new Siswa($db);
        $user = new User($db);
        
        try {
            $db->beginTransaction();
            
            // Update data siswa
            $success = $siswa->update($siswa_id, [
                'nama' => $nama,
                'kelas' => $kelas,
                'pembimbing_id' => $pembimbing_id,
                'tempat_pkl_id' => $tempat_pkl_id
            ]);
            
            if (!$success) {
                throw new Exception('Gagal mengupdate data siswa');
            }
            
            // Update password jika diisi
            if (!empty($password)) {
                // Validasi user_id
                if (empty($dataSiswa['user_id'])) {
                    throw new Exception('User ID tidak ditemukan untuk siswa ini');
                }
                
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                $userSuccess = $user->updatePassword((int)$dataSiswa['user_id'], $password_hash);
                
                if (!$userSuccess) {
                    throw new Exception('Gagal mengupdate password');
                }
            }
            
            // Log audit
            (new AuditLog($db))->add($_SESSION['user']['id'], 'edit_siswa', "ID: $siswa_id, Nama: $nama");
            
            $db->commit();
            
            // Redirect ke halaman siswa dengan pesan sukses
            header('Location: ?r=admin/siswa&success=2');
            exit;
            
        } catch (Exception $e) {
            $db->rollBack();
            $this->view('admin/siswa_edit', [
                'error' => 'Gagal mengupdate siswa: ' . $e->getMessage(),
                'siswa' => $dataSiswa,
                'oldData' => $_POST,
                'pembimbing' => (new User($db))->listByRole('pembimbing'),
                'tempatPKL' => (new TempatPKL($db))->listAll()
            ]);
        }
    }
    
    public function siswaDelete(): void {
        Auth::requireRole('admin');
        $db = new Database(require __DIR__.'/../../config/db.php');
        $siswa_id = (int)($_GET['id'] ?? 0);
        
        // Debug: log ID siswa yang akan dihapus
        error_log("Debug siswaDelete - siswa_id: " . $siswa_id);
        
        if (!$siswa_id) {
            header('Location: ?r=admin/siswa&error=ID siswa tidak valid');
            exit;
        }
        
        $siswa = new Siswa($db);
        $dataSiswa = $siswa->findById($siswa_id);
        
        // Debug: log struktur data siswa
        error_log("Debug siswaDelete - dataSiswa: " . json_encode($dataSiswa));
        
        if (!$dataSiswa) {
            header('Location: ?r=admin/siswa&error=Siswa tidak ditemukan');
            exit;
        }
        
        try {
            $db->beginTransaction();
            
            // 1. Hapus data absensi terlebih dahulu (foreign key constraint)
            $absensi = new Absensi($db);
            $absensiSuccess = $absensi->deleteBySiswaId($siswa_id);
            
            if (!$absensiSuccess) {
                throw new Exception('Gagal menghapus data absensi siswa');
            }
            
            // 2. Hapus data siswa
            $success = $siswa->delete($siswa_id);
            
            if (!$success) {
                throw new Exception('Gagal menghapus data siswa');
            }
            
            // 3. Hapus user account
            if (!empty($dataSiswa['user_id'])) {
                $user = new User($db);
                $userSuccess = $user->delete((int)$dataSiswa['user_id']);
                
                if (!$userSuccess) {
                    throw new Exception('Gagal menghapus user account');
                }
            }
            
            // Log audit
            (new AuditLog($db))->add($_SESSION['user']['id'], 'hapus_siswa', "ID: $siswa_id, Nama: " . $dataSiswa['nama']);
            
            $db->commit();
            
            // Redirect ke halaman siswa dengan pesan sukses
            header('Location: ?r=admin/siswa&success=3');
            exit;
            
        } catch (Exception $e) {
            $db->rollBack();
            header('Location: ?r=admin/siswa&error=Gagal menghapus siswa: ' . $e->getMessage());
            exit;
        }
    }
    
    public function import(): void {
        Auth::requireRole('admin');
        $db = new Database(require __DIR__.'/../../config/db.php');
        $pembimbing = (new User($db))->listByRole('pembimbing');
        $tempatPKL = (new TempatPKL($db))->listAll();
        $this->view('admin/import', ['pembimbing' => $pembimbing, 'tempatPKL' => $tempatPKL]);
    }
    
    public function importProcess(): void {
        Auth::requireRole('admin');
        $db = new Database(require __DIR__.'/../../config/db.php');
        
        if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
            header('Location: ?r=admin/import&error=File CSV tidak valid');
            exit;
        }
        
        $pembimbing_id = (int)($_POST['pembimbing_id'] ?? 0);
        $tempat_pkl_id = (int)($_POST['tempat_pkl_id'] ?? 0);
        $default_password = trim($_POST['default_password'] ?? '123456');
        
        if (empty($default_password)) {
            header('Location: ?r=admin/import&error=Password default harus diisi');
            exit;
        }
        
        $siswa = new Siswa($db);
        $user = new User($db);
        
        $handle = fopen($_FILES['csv_file']['tmp_name'], 'r');
        if (!$handle) {
            header('Location: ?r=admin/import&error=Gagal membaca file CSV');
            exit;
        }
        
        $results = [];
        $row = 1;
        $success = 0;
        $errors = 0;
        
        // Skip header row
        fgetcsv($handle);
        $row++;
        
        while (($data = fgetcsv($handle)) !== false) {
            $nis = trim($data[0] ?? '');
            $nama = trim($data[1] ?? '');
            $kelas = trim($data[2] ?? '');
            
            if (empty($nis) || empty($nama) || empty($kelas)) {
                $results[] = [
                    'row' => $row,
                    'nis' => $nis,
                    'nama' => $nama,
                    'kelas' => $kelas,
                    'status' => 'error',
                    'message' => 'Data tidak lengkap'
                ];
                $errors++;
                $row++;
                continue;
            }
            
            // Check existing NIS
            if ($siswa->findByNIS($nis)) {
                $results[] = [
                    'row' => $row,
                    'nis' => $nis,
                    'nama' => $nama,
                    'kelas' => $kelas,
                    'status' => 'error',
                    'message' => 'NIS sudah terdaftar'
                ];
                $errors++;
                $row++;
                continue;
            }
            
            // Check existing username
            if ($user->findByUsername($nis)) {
                $results[] = [
                    'row' => $row,
                    'nis' => $nis,
                    'nama' => $nama,
                    'kelas' => $kelas,
                    'status' => 'error',
                    'message' => 'Username (NIS) sudah terdaftar'
                ];
                $errors++;
                $row++;
                continue;
            }
            
            try {
                $db->beginTransaction();
                
                // Create user account
                $password_hash = password_hash($default_password, PASSWORD_DEFAULT);
                $user_id = $user->create([
                    'username' => $nis,
                    'password_hash' => $password_hash,
                    'role' => 'siswa',
                    'name' => $nama
                ]);
                
                if (!$user_id) {
                    throw new Exception('Gagal membuat user account');
                }
                
                // Create student data
                $success_siswa = $siswa->create([
                    'nis' => $nis,
                    'nama' => $nama,
                    'kelas' => $kelas,
                    'pembimbing_id' => $pembimbing_id,
                    'tempat_pkl_id' => $tempat_pkl_id,
                    'user_id' => $user_id
                ]);
                
                if (!$success_siswa) {
                    throw new Exception('Gagal membuat data siswa');
                }
                
                $db->commit();
                
                $results[] = [
                    'row' => $row,
                    'nis' => $nis,
                    'nama' => $nama,
                    'kelas' => $kelas,
                    'status' => 'success',
                    'message' => 'Berhasil diimport',
                    'login_info' => "Username: $nis, Password: $default_password"
                ];
                $success++;
                
            } catch (Exception $e) {
                $db->rollBack();
                if (isset($user_id)) {
                    $user->delete($user_id);
                }
                
                $results[] = [
                    'row' => $row,
                    'nis' => $nis,
                    'nama' => $nama,
                    'kelas' => $kelas,
                    'status' => 'error',
                    'message' => $e->getMessage()
                ];
                $errors++;
            }
            
            $row++;
        }
        
        fclose($handle);
        
        // Log audit
        (new AuditLog($db))->add($_SESSION['user']['id'], 'import_siswa', "Total: " . count($results) . ", Success: $success, Errors: $errors");
        
        $this->view('admin/import_result', [
            'results' => $results,
            'total_rows' => count($results),
            'success_count' => $success,
            'error_count' => $errors,
            'default_password' => $default_password
        ]);
    }
    
    public function tempat(): void {
        Auth::requireRole('admin');
        $db = new Database(require __DIR__.'/../../config/db.php');
        $tempat = new TempatPKL($db);
        $listTempat = $tempat->listAll();
        $this->view('admin/tempat', ['tempat' => $listTempat]);
    }
    
    public function tempatAdd(): void {
        Auth::requireRole('admin');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $db = new Database(require __DIR__.'/../../config/db.php');
            
            // Validasi input
            $nama = trim($_POST['nama'] ?? '');
            $lat = (float)($_POST['lat'] ?? 0);
            $lng = (float)($_POST['lng'] ?? 0);
            $radius_m = (int)($_POST['radius_m'] ?? 150);
            
            if (empty($nama)) {
                $this->view('admin/tempat', [
                    'tempat' => (new TempatPKL($db))->listAll(),
                    'error' => 'Nama tempat PKL harus diisi'
                ]);
                return;
            }
            
            if ($lat == 0 || $lng == 0) {
                $this->view('admin/tempat', [
                    'tempat' => (new TempatPKL($db))->listAll(),
                    'error' => 'Latitude dan Longitude harus diisi dengan benar'
                ]);
                return;
            }
            
            if ($radius_m < 50 || $radius_m > 1000) {
                $this->view('admin/tempat', [
                    'tempat' => (new TempatPKL($db))->listAll(),
                    'error' => 'Radius harus antara 50-1000 meter'
                ]);
                return;
            }
            
            try {
                $tempat = new TempatPKL($db);
                $success = $tempat->create([
                    'nama' => $nama,
                    'lat' => $lat,
                    'lng' => $lng,
                    'radius_m' => $radius_m
                ]);
                
                if ($success) {
                    // Log audit
                    (new AuditLog($db))->add($_SESSION['user']['id'], 'tambah_tempat_pkl', "Nama: $nama, Lat: $lat, Lng: $lng, Radius: $radius_m");
                    
                    header('Location: ?r=admin/tempat&success=1');
                    exit;
                } else {
                    throw new Exception('Gagal menyimpan data tempat PKL');
                }
                
            } catch (Exception $e) {
                $this->view('admin/tempat', [
                    'tempat' => (new TempatPKL($db))->listAll(),
                    'error' => 'Error: ' . $e->getMessage()
                ]);
                return;
            }
        }
        
        // Jika bukan POST, redirect ke halaman tempat
        header('Location: ?r=admin/tempat');
        exit;
    }
    
    public function tempatEdit(): void {
        Auth::requireRole('admin');
        $db = new Database(require __DIR__.'/../../config/db.php');
        
        $id = (int)($_GET['id'] ?? 0);
        if (!$id) {
            header('Location: ?r=admin/tempat&error=ID tidak valid');
            exit;
        }
        
        $tempat = new TempatPKL($db);
        $dataTempat = $tempat->findById($id);
        
        if (!$dataTempat) {
            header('Location: ?r=admin/tempat&error=Tempat PKL tidak ditemukan');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validasi input
            $nama = trim($_POST['nama'] ?? '');
            $lat = (float)($_POST['lat'] ?? 0);
            $lng = (float)($_POST['lng'] ?? 0);
            $radius_m = (int)($_POST['radius_m'] ?? 150);
            
            if (empty($nama)) {
                $this->view('admin/tempat_edit', [
                    'tempat' => $dataTempat,
                    'error' => 'Nama tempat PKL harus diisi'
                ]);
                return;
            }
            
            if ($lat == 0 || $lng == 0) {
                $this->view('admin/tempat_edit', [
                    'tempat' => $dataTempat,
                    'error' => 'Latitude dan Longitude harus diisi dengan benar'
                ]);
                return;
            }
            
            if ($radius_m < 50 || $radius_m > 1000) {
                $this->view('admin/tempat_edit', [
                    'tempat' => $dataTempat,
                    'error' => 'Radius harus antara 50-1000 meter'
                ]);
                return;
            }
            
            try {
                $success = $tempat->update($id, [
                    'nama' => $nama,
                    'lat' => $lat,
                    'lng' => $lng,
                    'radius_m' => $radius_m
                ]);
                
                if ($success) {
                    // Log audit
                    (new AuditLog($db))->add($_SESSION['user']['id'], 'edit_tempat_pkl', "ID: $id, Nama: $nama, Lat: $lat, Lng: $lng, Radius: $radius_m");
                    
                    header('Location: ?r=admin/tempat&success=2');
                    exit;
                } else {
                    throw new Exception('Gagal mengupdate data tempat PKL');
                }
                
            } catch (Exception $e) {
                $this->view('admin/tempat_edit', [
                    'tempat' => $dataTempat,
                    'error' => 'Error: ' . $e->getMessage()
                ]);
                return;
            }
        }
        
        $this->view('admin/tempat_edit', ['tempat' => $dataTempat]);
    }
    
    public function tempatDelete(): void {
        Auth::requireRole('admin');
        $db = new Database(require __DIR__.'/../../config/db.php');
        
        $id = (int)($_GET['id'] ?? 0);
        if (!$id) {
            header('Location: ?r=admin/tempat&error=ID tidak valid');
            exit;
        }
        
        try {
            $tempat = new TempatPKL($db);
            $dataTempat = $tempat->findById($id);
            
            if (!$dataTempat) {
                header('Location: ?r=admin/tempat&error=Tempat PKL tidak ditemukan');
                exit;
            }
            
            // Cek apakah ada siswa yang menggunakan tempat ini
            $siswa = new Siswa($db);
            $siswaUsingTempat = $siswa->findByTempatPKL($id);
            
            if (!empty($siswaUsingTempat)) {
                header('Location: ?r=admin/tempat&error=Tidak dapat menghapus tempat PKL yang masih digunakan oleh siswa');
                exit;
            }
            
            $success = $tempat->delete($id);
            
            if ($success) {
                // Log audit
                (new AuditLog($db))->add($_SESSION['user']['id'], 'hapus_tempat_pkl', "ID: $id, Nama: {$dataTempat['nama']}");
                
                header('Location: ?r=admin/tempat&success=3');
                exit;
            } else {
                throw new Exception('Gagal menghapus data tempat PKL');
            }
            
        } catch (Exception $e) {
            header('Location: ?r=admin/tempat&error=Error: ' . $e->getMessage());
            exit;
        }
    }
    
    public function pembimbing(): void {
        Auth::requireRole('admin');
        $db = new Database(require __DIR__.'/../../config/db.php');
        $pembimbing = new Pembimbing($db);
        $listPembimbing = $pembimbing->listAll();
        $this->view('admin/pembimbing', ['pembimbing' => $listPembimbing]);
    }
    
    public function pembimbingAdd(): void {
        Auth::requireRole('admin');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $db = new Database(require __DIR__.'/../../config/db.php');
            
            // Validasi input
            $nama = trim($_POST['nama'] ?? '');
            $nip = trim($_POST['nip'] ?? '');
            $username = trim($_POST['username'] ?? '');
            $password = trim($_POST['password'] ?? '');
            $confirmPassword = trim($_POST['confirm_password'] ?? '');
            
            if (empty($nama)) {
                $this->view('admin/pembimbing_add', [
                    'error' => 'Nama pembimbing harus diisi'
                ]);
                return;
            }
            
            if (empty($nip)) {
                $this->view('admin/pembimbing_add', [
                    'error' => 'NIP pembimbing harus diisi'
                ]);
                return;
            }
            
            if (empty($username)) {
                // Generate username otomatis dari NIP
                $username = 'pemb' . str_replace(' ', '', $nip);
            }
            
            if (empty($password)) {
                $this->view('admin/pembimbing_add', [
                    'error' => 'Password harus diisi'
                ]);
                return;
            }
            
            if ($password !== $confirmPassword) {
                $this->view('admin/pembimbing_add', [
                    'error' => 'Konfirmasi password tidak cocok'
                ]);
                return;
            }
            
            if (strlen($password) < 6) {
                $this->view('admin/pembimbing_add', [
                    'error' => 'Password minimal 6 karakter'
                ]);
                return;
            }
            
            try {
                $db->beginTransaction();
                
                $pembimbing = new Pembimbing($db);
                $user = new User($db);
                
                // Cek apakah NIP sudah ada
                $existingPembimbing = $pembimbing->findByNIP($nip);
                if ($existingPembimbing) {
                    $this->view('admin/pembimbing_add', [
                        'error' => 'NIP pembimbing sudah terdaftar'
                    ]);
                    return;
                }
                
                // Cek apakah username sudah ada
                $existingUser = $user->findByUsername($username);
                if ($existingUser) {
                    $this->view('admin/pembimbing_add', [
                        'error' => 'Username sudah digunakan'
                    ]);
                    return;
                }
                
                // Buat user terlebih dahulu
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $userData = [
                    'username' => $username,
                    'password_hash' => $hashedPassword,
                    'role' => 'pembimbing',
                    'name' => $nama
                ];
                
                $userSuccess = $user->create($userData);
                if (!$userSuccess) {
                    throw new Exception('Gagal membuat user');
                }
                
                $userId = $db->lastInsertId();
                
                // Buat pembimbing
                $pembimbingSuccess = $pembimbing->create([
                    'nama' => $nama,
                    'nip' => $nip,
                    'user_id' => $userId
                ]);
                
                if (!$pembimbingSuccess) {
                    throw new Exception('Gagal membuat data pembimbing');
                }
                
                $db->commit();
                
                // Log audit
                (new AuditLog($db))->add($_SESSION['user']['id'], 'tambah_pembimbing', "Nama: $nama, NIP: $nip, Username: $username");
                
                header('Location: ?r=admin/pembimbing&success=1');
                exit;
                
            } catch (Exception $e) {
                $db->rollBack();
                $this->view('admin/pembimbing_add', [
                    'error' => 'Error: ' . $e->getMessage()
                ]);
                return;
            }
        }
        
        $this->view('admin/pembimbing_add');
    }
    
    public function pembimbingEdit(): void {
        Auth::requireRole('admin');
        $db = new Database(require __DIR__.'/../../config/db.php');
        
        $id = (int)($_GET['id'] ?? 0);
        if (!$id) {
            header('Location: ?r=admin/pembimbing&error=ID tidak valid');
            exit;
        }
        
        $pembimbing = new Pembimbing($db);
        $dataPembimbing = $pembimbing->findById($id);
        
        if (!$dataPembimbing) {
            header('Location: ?r=admin/pembimbing&error=Pembimbing tidak ditemukan');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validasi input
            $nama = trim($_POST['nama'] ?? '');
            $nip = trim($_POST['nip'] ?? '');
            $username = trim($_POST['username'] ?? '');
            $password = trim($_POST['password'] ?? '');
            $confirmPassword = trim($_POST['confirm_password'] ?? '');
            
            if (empty($nama)) {
                $this->view('admin/pembimbing_edit', [
                    'pembimbing' => $dataPembimbing,
                    'error' => 'Nama pembimbing harus diisi'
                ]);
                return;
            }
            
            if (empty($nip)) {
                $this->view('admin/pembimbing_edit', [
                    'pembimbing' => $dataPembimbing,
                    'error' => 'NIP pembimbing harus diisi'
                ]);
                return;
            }
            
            if (empty($username)) {
                $this->view('admin/pembimbing_edit', [
                    'pembimbing' => $dataPembimbing,
                    'error' => 'Username harus diisi'
                ]);
                return;
            }
            
            if (!empty($password) && $password !== $confirmPassword) {
                $this->view('admin/pembimbing_edit', [
                    'pembimbing' => $dataPembimbing,
                    'error' => 'Konfirmasi password tidak cocok'
                ]);
                return;
            }
            
            if (!empty($password) && strlen($password) < 6) {
                $this->view('admin/pembimbing_edit', [
                    'pembimbing' => $dataPembimbing,
                    'error' => 'Password minimal 6 karakter'
                ]);
                return;
            }
            
            try {
                $db->beginTransaction();
                
                // Cek apakah NIP sudah ada (kecuali untuk pembimbing yang sedang diedit)
                $existingPembimbing = $pembimbing->findByNIP($nip);
                if ($existingPembimbing && $existingPembimbing['id'] != $id) {
                    $this->view('admin/pembimbing_edit', [
                        'pembimbing' => $dataPembimbing,
                        'error' => 'NIP pembimbing sudah terdaftar'
                    ]);
                    return;
                }
                
                // Update pembimbing
                $pembimbingSuccess = $pembimbing->update($id, [
                    'nama' => $nama,
                    'nip' => $nip
                ]);
                
                if (!$pembimbingSuccess) {
                    throw new Exception('Gagal mengupdate data pembimbing');
                }
                
                // Update user jika ada user_id
                if (!empty($dataPembimbing['user_id'])) {
                    $user = new User($db);
                    
                    // Cek apakah username sudah ada (kecuali untuk user yang sedang diedit)
                    $existingUser = $user->findByUsername($username);
                    if ($existingUser && $existingUser['id'] != $dataPembimbing['user_id']) {
                        throw new Exception('Username sudah digunakan');
                    }
                    
                    // Update username
                    $userUpdateData = [
                        'username' => $username,
                        'name' => $nama
                    ];
                    
                    // Update password jika diisi
                    if (!empty($password)) {
                        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                        $userUpdateData['password_hash'] = $hashedPassword;
                    }
                    
                    $userSuccess = $user->update($dataPembimbing['user_id'], $userUpdateData);
                    if (!$userSuccess) {
                        throw new Exception('Gagal mengupdate data user');
                    }
                }
                
                $db->commit();
                
                // Log audit
                $auditMessage = "ID: $id, Nama: $nama, NIP: $nip, Username: $username";
                if (!empty($password)) {
                    $auditMessage .= ", Password: diubah";
                }
                (new AuditLog($db))->add($_SESSION['user']['id'], 'edit_pembimbing', $auditMessage);
                
                header('Location: ?r=admin/pembimbing&success=2');
                exit;
                
            } catch (Exception $e) {
                $db->rollBack();
                $this->view('admin/pembimbing_edit', [
                    'pembimbing' => $dataPembimbing,
                    'error' => 'Error: ' . $e->getMessage()
                ]);
                return;
            }
        }
        
        $this->view('admin/pembimbing_edit', ['pembimbing' => $dataPembimbing]);
    }
    
    public function pembimbingDetail(): void {
        Auth::requireRole('admin');
        
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            header('Location: ?r=admin/pembimbing&error=ID tidak valid');
            exit;
        }
        
        $db = new Database(require __DIR__.'/../../config/db.php');
        $pembimbing = new Pembimbing($db);
        
        try {
            // Ambil data pembimbing
            $dataPembimbing = $pembimbing->findById($id);
            if (!$dataPembimbing) {
                header('Location: ?r=admin/pembimbing&error=Pembimbing tidak ditemukan');
                exit;
            }
            
            // Ambil data siswa yang dibimbing
            $siswa = new Siswa($db);
            $siswaDibimbing = $siswa->findByPembimbingId($id);
            
            // Ambil statistik absensi
            $absensi = new Absensi($db);
            $totalAbsensi = $absensi->countByPembimbing($id);
            
            // Ambil absensi terbaru
            $absensiTerbaru = $absensi->getLatestByPembimbing($id, 10);
            
            $this->view('admin/pembimbing_detail', [
                'pembimbing' => $dataPembimbing,
                'siswaDibimbing' => $siswaDibimbing,
                'totalAbsensi' => $totalAbsensi,
                'absensiTerbaru' => $absensiTerbaru
            ]);
            
        } catch (Exception $e) {
            error_log("Error in AdminController::pembimbingDetail(): " . $e->getMessage());
            header('Location: ?r=admin/pembimbing&error=Terjadi kesalahan sistem');
            exit;
        }
    }
    
    public function pembimbingDelete(): void {
        Auth::requireRole('admin');
        $db = new Database(require __DIR__.'/../../config/db.php');
        
        $id = (int)($_GET['id'] ?? 0);
        if (!$id) {
            header('Location: ?r=admin/pembimbing&error=ID tidak valid');
            exit;
        }
        
        try {
            $pembimbing = new Pembimbing($db);
            $dataPembimbing = $pembimbing->findById($id);
            
            if (!$dataPembimbing) {
                header('Location: ?r=admin/pembimbing&error=Pembimbing tidak ditemukan');
                exit;
            }
            
            // Cek apakah ada siswa yang dibimbing oleh pembimbing ini
            $siswaByPembimbing = $pembimbing->getSiswaByPembimbing($id);
            
            if (!empty($siswaByPembimbing)) {
                header('Location: ?r=admin/pembimbing&error=Tidak dapat menghapus pembimbing yang masih membimbing siswa');
                exit;
        }
        
        $success = $pembimbing->delete($id);
        
        if ($success) {
            // Log audit
            (new AuditLog($db))->add($_SESSION['user']['id'], 'hapus_pembimbing', "ID: $id, Nama: {$dataPembimbing['nama']}");
            
            header('Location: ?r=admin/pembimbing&success=3');
            exit;
        } else {
            throw new Exception('Gagal menghapus data pembimbing');
        }
        
    } catch (Exception $e) {
        header('Location: ?r=admin/pembimbing&error=Error: ' . $e->getMessage());
        exit;
    }
}
    
    public function report(): void {
        Auth::requireRole('admin');
        $db = new Database(require __DIR__.'/../../config/db.php');
        
        // Ambil filter dari query string
        $start = $_GET['start'] ?? (new DateTime('-30 days', new DateTimeZone('Asia/Jakarta')))->format('Y-m-d');
        $end = $_GET['end'] ?? (new DateTime('now', new DateTimeZone('Asia/Jakarta')))->format('Y-m-d');
        $kelas = $_GET['kelas'] ?? '';
        $tempat = (int)($_GET['tempat'] ?? 0);
        
        // Ambil data absensi berdasarkan filter
        $absensi = new Absensi($db);
        $rows = $absensi->getAbsensiForReport($start, $end, $kelas, $tempat);
        
        $this->view('admin/report', [
            'rows' => $rows,
            'filters' => [
                'start' => $start,
                'end' => $end,
                'kelas' => $kelas,
                'tempat' => $tempat
            ]
        ]);
    }
    
    public function reportCsv(): void {
        Auth::requireRole('admin');
        $db = new Database(require __DIR__.'/../../config/db.php');
        
        // Ambil filter dari query string
        $start = $_GET['start'] ?? (new DateTime('-30 days', new DateTimeZone('Asia/Jakarta')))->format('Y-m-d');
        $end = $_GET['end'] ?? (new DateTime('now', new DateTimeZone('Asia/Jakarta')))->format('Y-m-d');
        $kelas = $_GET['kelas'] ?? '';
        $tempat = (int)($_GET['tempat'] ?? 0);
        
        // Ambil data absensi berdasarkan filter
        $absensi = new Absensi($db);
        $rows = $absensi->getAbsensiForReport($start, $end, $kelas, $tempat);
        
        // Set header untuk download file Excel
        $filename = "laporan_absensi_{$start}_sampai_{$end}.xls";
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        // Output content Excel
        echo '<table border="1">';
        
        // Header tabel
        echo '<tr>';
        echo '<th style="background-color: #f0f0f0; font-weight: bold;">No</th>';
        echo '<th style="background-color: #f0f0f0; font-weight: bold;">Nama Siswa</th>';
        echo '<th style="background-color: #f0f0f0; font-weight: bold;">NIS</th>';
        echo '<th style="background-color: #f0f0f0; font-weight: bold;">Kelas</th>';
        echo '<th style="background-color: #f0f0f0; font-weight: bold;">Tempat PKL</th>';
        echo '<th style="background-color: #f0f0f0; font-weight: bold;">Tanggal</th>';
        echo '<th style="background-color: #f0f0f0; font-weight: bold;">Waktu</th>';
        echo '<th style="background-color: #f0f0f0; font-weight: bold;">Jenis Absen</th>';
        echo '<th style="background-color: #f0f0f0; font-weight: bold;">Latitude</th>';
        echo '<th style="background-color: #f0f0f0; font-weight: bold;">Longitude</th>';
        echo '<th style="background-color: #f0f0f0; font-weight: bold;">Jarak (m)</th>';
        echo '<th style="background-color: #f0f0f0; font-weight: bold;">Status Radius</th>';
        echo '<th style="background-color: #f0f0f0; font-weight: bold;">Selfie</th>';
        echo '</tr>';
        
        // Data rows
        $no = 1;
        foreach ($rows as $row) {
            echo '<tr>';
            echo '<td>' . $no++ . '</td>';
            echo '<td>' . htmlspecialchars($row['siswa']) . '</td>';
            echo '<td>' . htmlspecialchars($row['nis'] ?? 'N/A') . '</td>';
            echo '<td>' . htmlspecialchars($row['kelas']) . '</td>';
            echo '<td>' . htmlspecialchars($row['tempat']) . '</td>';
            echo '<td>' . date('d/m/Y', strtotime($row['waktu'])) . '</td>';
            echo '<td>' . date('H:i:s', strtotime($row['waktu'])) . '</td>';
            echo '<td>' . ($row['jenis_absen'] === 'datang' ? 'Datang' : 'Pulang') . '</td>';
            echo '<td>' . number_format($row['lat'], 6) . '</td>';
            echo '<td>' . number_format($row['lng'], 6) . '</td>';
            echo '<td>' . number_format($row['jarak_m'], 1) . '</td>';
            
            // Status radius
            $jarak = (float)($row['jarak_m'] ?? 0);
            $statusRadius = ($jarak <= 150) ? 'Dalam Radius' : 'Luar Radius';
            echo '<td>' . $statusRadius . '</td>';
            
            // Selfie
            $selfie = !empty($row['selfie_path']) ? 'Ada' : 'Tidak Ada';
            echo '<td>' . $selfie . '</td>';
            echo '</tr>';
        }
        
        echo '</table>';
        
        // Log audit
        try {
            (new AuditLog($db))->add($_SESSION['user']['id'], 'export_xls', "Export laporan absensi periode $start sampai $end");
        } catch (Exception $e) {
            error_log("Error logging export: " . $e->getMessage());
        }
        
        exit;
    }
    
    public function reportPrint(): void {
        Auth::requireRole('admin');
        $db = new Database(require __DIR__.'/../../config/db.php');
        
        // Ambil filter dari query string
        $start = $_GET['start'] ?? (new DateTime('-30 days', new DateTimeZone('Asia/Jakarta')))->format('Y-m-d');
        $end = $_GET['end'] ?? (new DateTime('now', new DateTimeZone('Asia/Jakarta')))->format('Y-m-d');
        $kelas = $_GET['kelas'] ?? '';
        $tempat = (int)($_GET['tempat'] ?? 0);
        
        // Ambil data absensi berdasarkan filter
        $absensi = new Absensi($db);
        $rows = $absensi->getAbsensiForReport($start, $end, $kelas, $tempat);
        
        // Set header untuk PDF
        header('Content-Type: text/html; charset=utf-8');
        
        // Log audit
        try {
            (new AuditLog($db))->add($_SESSION['user']['id'], 'print_report', "Print laporan absensi periode $start sampai $end");
        } catch (Exception $e) {
            error_log("Error logging print: " . $e->getMessage());
        }
        
        $this->view('admin/report_print', [
            'rows' => $rows,
            'filters' => [
                'start' => $start,
                'end' => $end,
                'kelas' => $kelas,
                'tempat' => $tempat
            ]
        ]);
    }
    
    public function audit(): void {
        Auth::requireRole('admin');
        $db = new Database(require __DIR__.'/../../config/db.php');
        $audit = new AuditLog($db);
        $logs = $audit->listAll();
        $this->view('admin/audit', ['logs' => $logs]);
    }
}
