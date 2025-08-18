<?php
namespace App\Controllers;
use App\Helpers\{Auth, CSRF, Response};
use App\Models\{Database, Siswa, TempatPKL, Absensi, AuditLog};
use App\Helpers\Geo;
use DateTime;
use DateTimeZone;
use Exception;

// Set timezone ke WIB
date_default_timezone_set('Asia/Jakarta');
class StudentController extends Controller {
    public function dashboard(): void {
        Auth::requireRole('siswa');
        $db = new Database(require __DIR__.'/../../config/db.php');
        $siswa = (new Siswa($db))->findByUserId((int)$_SESSION['user']['id']);
        $tempat = $siswa ? (new TempatPKL($db))->byId((int)$siswa['tempat_pkl_id']) : null;
        $abs = (new Absensi($db))->listBySiswa((int)$siswa['id']);
        
        // Cek status absensi hari ini (WIB)
        $today = (new DateTime('now', new DateTimeZone('Asia/Jakarta')))->format('Y-m-d');
        $absensi = new Absensi($db);
        $sudahDatang = false;
        $sudahPulang = false;
        
        if ($siswa) {
            $sudahDatang = $absensi->sudahAbsenDatang((int)$siswa['id'], $today);
            $sudahPulang = $absensi->sudahAbsenPulang((int)$siswa['id'], $today);
        }
        
        $this->view('student/dashboard', [
            'siswa' => $siswa,
            'tempat' => $tempat,
            'absensi' => $abs,
            'db' => $db,
            'sudahDatang' => $sudahDatang,
            'sudahPulang' => $sudahPulang
        ]);
    }
    public function absen(): void {
        Auth::requireRole('siswa');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo 'Method not allowed';
            return;
        }
        
        // CSRF check
        if (!CSRF::verify($_POST['csrf'] ?? '')) {
            http_response_code(403);
            echo 'CSRF token invalid';
            return;
        }
        
        $db = new Database(require __DIR__.'/../../config/db.php');
        
        try {
            $siswa = new Siswa($db);
            $dataSiswa = $siswa->findByUserId($_SESSION['user']['id']);
            
            if (!$dataSiswa) {
                http_response_code(404);
                echo 'Data siswa tidak ditemukan';
                return;
            }
            
            $tempat = new TempatPKL($db);
            $dataTempat = $tempat->byId($dataSiswa['tempat_pkl_id']);
            
            if (!$dataTempat) {
                http_response_code(400);
                echo 'Tempat PKL tidak ditemukan';
                return;
            }
            
            $lat = (float)($_POST['lat'] ?? 0);
            $lng = (float)($_POST['lng'] ?? 0);
            $data = $_POST['selfie'] ?? '';
            $jenisAbsen = $_POST['jenis_absen'] ?? 'datang';
            
            if ($lat == 0 || $lng == 0) {
                http_response_code(400);
                echo 'Lokasi tidak valid';
                return;
            }
            
            // Validasi wajib selfie
            if (empty($data)) {
                http_response_code(400);
                echo '<!DOCTYPE html>
                <html lang="id">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>Selfie Wajib</title>
                    <style>
                        body { 
                            font-family: Arial, sans-serif; 
                            margin: 0; 
                            padding: 20px; 
                            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
                            min-height: 100vh;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                        }
                        .error-container {
                            background: white;
                            border-radius: 20px;
                            padding: 40px;
                            text-align: center;
                            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
                            max-width: 500px;
                            width: 100%;
                        }
                        .error-icon {
                            font-size: 4rem;
                            margin-bottom: 20px;
                        }
                        .error-title {
                            color: #e74c3c;
                            font-size: 1.8rem;
                            margin-bottom: 15px;
                            font-weight: bold;
                        }
                        .error-message {
                            color: #555;
                            font-size: 1.1rem;
                            line-height: 1.6;
                            margin-bottom: 30px;
                        }
                        .selfie-info {
                            background: #fff3cd;
                            border-radius: 15px;
                            padding: 20px;
                            margin: 20px 0;
                            border-left: 5px solid #ffc107;
                        }
                        .selfie-item {
                            display: flex;
                            align-items: center;
                            margin: 10px 0;
                            padding: 8px 0;
                        }
                        .selfie-icon {
                            font-size: 1.5rem;
                            margin-right: 15px;
                        }
                        .selfie-text {
                            color: #856404;
                            font-weight: 500;
                        }
                        .btn-home {
                            background: linear-gradient(45deg, #ff6b6b, #ee5a24);
                            color: white;
                            border: none;
                            padding: 15px 30px;
                            border-radius: 25px;
                            font-size: 1.1rem;
                            cursor: pointer;
                            text-decoration: none;
                            display: inline-block;
                            margin-top: 20px;
                            transition: all 0.3s ease;
                        }
                        .btn-home:hover {
                            transform: translateY(-2px);
                            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
                        }
                    </style>
                </head>
                <body>
                    <div class="error-container">
                        <div class="error-icon">📸</div>
                        <div class="error-title">Selfie Wajib!</div>
                        <div class="error-message">
                            Untuk melakukan absensi, Anda wajib mengambil foto selfie terlebih dahulu.
                        </div>
                        
                        <div class="selfie-info">
                            <div class="selfie-item">
                                <span class="selfie-icon">⚠️</span>
                                <span class="selfie-text">Selfie diperlukan untuk verifikasi kehadiran</span>
                            </div>
                            <div class="selfie-item">
                                <span class="selfie-icon">📱</span>
                                <span class="selfie-text">Pastikan kamera depan berfungsi dengan baik</span>
                            </div>
                            <div class="selfie-item">
                                <span class="selfie-icon">💡</span>
                                <span class="selfie-text">Ambil foto yang jelas dan terang</span>
                            </div>
                        </div>
                        
                        <a href="?r=student/dashboard" class="btn-home">🏠 Kembali ke Dashboard</a>
                    </div>
                </body>
                </html>';
                return;
            }
            
            // Hitung jarak
            $jarak = Geo::distance($lat, $lng, $dataTempat['lat'], $dataTempat['lng']);
            
            if ($jarak > $dataTempat['radius_m']) {
                http_response_code(400);
                
                // Tampilan error yang lebih bagus dengan HTML
                echo '<!DOCTYPE html>
                <html lang="id">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>Lokasi Di Luar Radius</title>
                    <style>
                        body { 
                            font-family: Arial, sans-serif; 
                            margin: 0; 
                            padding: 20px; 
                            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                            min-height: 100vh;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                        }
                        .error-container {
                            background: white;
                            border-radius: 20px;
                            padding: 40px;
                            text-align: center;
                            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
                            max-width: 500px;
                            width: 100%;
                        }
                        .error-icon {
                            font-size: 4rem;
                            margin-bottom: 20px;
                        }
                        .error-title {
                            color: #e74c3c;
                            font-size: 1.8rem;
                            margin-bottom: 15px;
                            font-weight: bold;
                        }
                        .error-message {
                            color: #555;
                            font-size: 1.1rem;
                            line-height: 1.6;
                            margin-bottom: 30px;
                        }
                        .location-info {
                            background: #f8f9fa;
                            border-radius: 15px;
                            padding: 20px;
                            margin: 20px 0;
                            border-left: 5px solid #e74c3c;
                        }
                        .location-item {
                            display: flex;
                            justify-content: space-between;
                            margin: 10px 0;
                            padding: 8px 0;
                            border-bottom: 1px solid #eee;
                        }
                        .location-item:last-child {
                            border-bottom: none;
                        }
                        .location-label {
                            font-weight: bold;
                            color: #333;
                        }
                        .location-value {
                            color: #e74c3c;
                            font-weight: bold;
                        }
                        .btn-home {
                            background: linear-gradient(45deg, #667eea, #764ba2);
                            color: white;
                            border: none;
                            padding: 15px 30px;
                            border-radius: 25px;
                            font-size: 1.1rem;
                            cursor: pointer;
                            text-decoration: none;
                            display: inline-block;
                            margin-top: 20px;
                            transition: all 0.3s ease;
                            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
                        }
                        .btn-home:hover {
                            transform: translateY(-2px);
                            box-shadow: 0 8px 25px rgba(0,0,0,0.3);
                        }
                        .info-note {
                            background: #e3f2fd;
                            border-radius: 10px;
                            padding: 15px;
                            margin: 20px 0;
                            border-left: 5px solid #2196f3;
                        }
                        .info-note strong {
                            color: #1976d2;
                        }
                    </style>
                </head>
                <body>
                    <div class="error-container">
                        <div class="error-icon">🚫</div>
                        <div class="error-title">Lokasi Di Luar Radius</div>
                        <div class="error-message">
                            Maaf, Anda berada di luar radius yang diizinkan untuk melakukan absensi.
                        </div>
                        
                        <div class="location-info">
                            <div class="location-item">
                                <span class="location-label">Jarak Anda:</span>
                                <span class="location-value">' . round($jarak, 2) . ' meter</span>
                            </div>
                            <div class="location-item">
                                <span class="location-label">Radius Diizinkan:</span>
                                <span class="location-value">' . $dataTempat['radius_m'] . ' meter</span>
                            </div>
                            <div class="location-item">
                                <span class="location-label">Tempat PKL:</span>
                                <span class="location-value">' . htmlspecialchars($dataTempat['nama'] ?? 'Tidak Diketahui') . '</span>
                            </div>
                        </div>
                        
                        <div class="info-note">
                            <strong>💡 Informasi:</strong> Data absensi Anda sudah masuk ke server dan tersimpan dengan baik. 
                            Silakan pindah ke lokasi yang sesuai dengan radius yang diizinkan untuk melakukan absensi.
                        </div>
                        
                        <a href="?r=student/dashboard" class="btn-home">
                            🏠 Kembali ke Dashboard
                        </a>
                    </div>
                </body>
                </html>';
                return;
            }
            
            $selfie_path = null;
            if (!empty($data) && preg_match('#^data:image/(png|jpeg);base64,#', $data, $m)) {
                $bin = base64_decode(preg_replace('#^data:image/\w+;base64,#', '', $data), true);
                if ($bin !== false && strlen($bin) <= (int)(require __DIR__.'/../../config/app.php')['max_file_size']) {
                    $ext = $m[1] === 'png' ? 'png' : 'jpg';
                    $fname = 'selfie_' . (new DateTime('now', new DateTimeZone('Asia/Jakarta')))->format('Ymd_His') . '_' . $dataSiswa['id'] . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                    $uploadPath = (require __DIR__.'/../../config/app.php')['upload_path'];
                    $path = $uploadPath . $fname;
                    
                    if (!is_dir($uploadPath)) {
                        mkdir($uploadPath, 0755, true);
                    }
                    
                    if (file_put_contents($path, $bin)) {
                        $selfie_path = 'uploads/' . $fname;
                    }
                }
            }
            
                    // Cek apakah sudah absen dengan jenis yang sama hari ini (WIB)
        $today = (new DateTime('now', new DateTimeZone('Asia/Jakarta')))->format('Y-m-d');
            $absensi = new Absensi($db);
            
            if ($jenisAbsen === 'datang' && $absensi->sudahAbsenDatang($dataSiswa['id'], $today)) {
                http_response_code(400);
                echo 'Anda sudah melakukan absen datang hari ini';
                return;
            }
            
            if ($jenisAbsen === 'pulang' && $absensi->sudahAbsenPulang($dataSiswa['id'], $today)) {
                http_response_code(400);
                echo 'Anda sudah melakukan absen pulang hari ini';
                return;
            }
            
            // Ambil waktu dari perangkat user
            $deviceTime = $_POST['device_time'] ?? null;
            $timezoneOffset = $_POST['timezone_offset'] ?? 0;
            
            // Validasi dan konversi waktu perangkat dengan timezone WIB
            $waktuAbsen = null;
            if ($deviceTime) {
                try {
                    // Parse waktu dari perangkat (ISO 8601 format)
                    $waktuPerangkat = new DateTime($deviceTime);
                    
                    // Set timezone ke WIB
                    $waktuPerangkat->setTimezone(new DateTimeZone('Asia/Jakarta'));
                    
                    // Validasi: waktu tidak boleh lebih dari 5 menit dari waktu server WIB
                    $waktuServer = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
                    $selisih = abs($waktuPerangkat->getTimestamp() - $waktuServer->getTimestamp());
                    
                    if ($selisih <= 300) { // 5 menit = 300 detik
                        $waktuAbsen = $waktuPerangkat->format('Y-m-d H:i:s');
                        error_log("Info: Using device time (WIB): " . $waktuAbsen);
                    } else {
                        // Jika selisih terlalu besar, gunakan waktu server WIB
                        $waktuAbsen = $waktuServer->format('Y-m-d H:i:s');
                        error_log("Warning: Device time differs too much from server time (WIB). Using server time. Device: $deviceTime, Server WIB: " . $waktuAbsen);
                    }
                } catch (Exception $e) {
                    // Jika parsing gagal, gunakan waktu server WIB
                    $waktuAbsen = (new DateTime('now', new DateTimeZone('Asia/Jakarta')))->format('Y-m-d H:i:s');
                    error_log("Error parsing device time: " . $e->getMessage() . ". Using server time (WIB): " . $waktuAbsen);
                }
            } else {
                // Jika tidak ada waktu perangkat, gunakan waktu server WIB
                $waktuAbsen = (new DateTime('now', new DateTimeZone('Asia/Jakarta')))->format('Y-m-d H:i:s');
                error_log("Info: No device time provided, using server time (WIB): " . $waktuAbsen);
            }
            
            // Buat absensi
            $success = $absensi->create([
                'siswa_id' => $dataSiswa['id'],
                'waktu' => $waktuAbsen,
                'lat' => $lat,
                'lng' => $lng,
                'jarak_m' => $jarak,
                'selfie_path' => $selfie_path,
                'jenis_absen' => $jenisAbsen
            ]);
            
            if ($success) {
                // Log audit
                $auditMessage = "Absen " . ($jenisAbsen === 'datang' ? 'datang' : 'pulang') . " - Lat: $lat, Lng: $lng, Jarak: " . round($jarak, 2) . "m";
                (new AuditLog($db))->add($_SESSION['user']['id'], 'absen', $auditMessage);
                
                $successMessage = $jenisAbsen === 'datang' ? 'absen_datang' : 'absen_pulang';
                header('Location: ?r=student/dashboard&success=' . $successMessage);
                exit;
            } else {
                throw new Exception('Gagal menyimpan data absensi');
            }
            
        } catch (Exception $e) {
            error_log("Error in StudentController::absen(): " . $e->getMessage());
            http_response_code(500);
            echo 'Terjadi kesalahan sistem: ' . $e->getMessage();
        }
    }
}