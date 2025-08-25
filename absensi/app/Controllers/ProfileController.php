<?php
namespace App\Controllers;
use App\Helpers\Auth;
use App\Models\{Database, AppSettings, AuditLog};
use App\Helpers\CSRF;

class ProfileController extends Controller {
    
    /**
     * Tampilkan halaman profile administrator
     */
    public function index(): void {
        Auth::requireRole('admin');
        $db = new Database(require __DIR__.'/../../config/db.php');
        $appSettings = new AppSettings($db);
        
        // Ambil semua pengaturan
        $settings = $appSettings->getAllSettings();
        
        // Kelompokkan pengaturan berdasarkan kategori
        $schoolSettings = [];
        $appSettings = [];
        $systemSettings = [];
        
        foreach ($settings as $setting) {
            switch ($setting['setting_key']) {
                case 'school_name':
                case 'school_address':
                case 'school_phone':
                case 'school_email':
                case 'school_website':
                    $schoolSettings[] = $setting;
                    break;
                case 'app_title':
                case 'gps_radius':
                case 'max_upload_size':
                case 'session_timeout':
                case 'login_captcha_enabled':
                    $systemSettings[] = $setting;
                    break;
                default:
                    $appSettings[] = $setting;
            }
        }
        
        $this->view('admin/profile', [
            'schoolSettings' => $schoolSettings,
            'systemSettings' => $systemSettings,
            'appSettings' => $appSettings,
            'message' => $_SESSION['message'] ?? null,
            'error' => $_SESSION['error'] ?? null
        ]);
        
        // Hapus pesan setelah ditampilkan
        unset($_SESSION['message'], $_SESSION['error']);
    }
    
    /**
     * Update pengaturan aplikasi
     */
    public function update(): void {
        Auth::requireRole('admin');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?r=profile');
            exit;
        }
        
        $db = new Database(require __DIR__.'/../../config/db.php');
        $appSettings = new AppSettings($db);
        $auditLog = new AuditLog($db);
        
        try {
            // Validasi CSRF token
            if (!isset($_POST['csrf_token']) || !CSRF::verify($_POST['csrf_token'])) {
                throw new \Exception('Token CSRF tidak valid');
            }
            
            // Ambil data dari form
            $settings = [];
            foreach ($_POST as $key => $value) {
                if (strpos($key, 'setting_') === 0) {
                    $settingKey = str_replace('setting_', '', $key);
                    $settings[$settingKey] = trim($value);
                }
            }
            
            // Validasi pengaturan
            $allSettings = $appSettings->getAllSettings();
            $settingMap = [];
            foreach ($allSettings as $setting) {
                $settingMap[$setting['setting_key']] = $setting;
            }
            
            foreach ($settings as $key => $value) {
                if (isset($settingMap[$key])) {
                    $setting = $settingMap[$key];
                    if (!$appSettings->validateSetting($key, $value, $setting['setting_type'])) {
                        throw new \Exception("Pengaturan '{$setting['description']}' tidak valid");
                    }
                }
            }
            
            // Update pengaturan
            if ($appSettings->updateMultipleSettings($settings)) {
                // Log audit
                $auditLog->add(
                    $_SESSION['user']['id'],
                    'profile_update',
                    'Mengupdate pengaturan aplikasi: ' . json_encode($settings)
                );
                
                $_SESSION['message'] = 'Pengaturan berhasil diperbarui!';
            } else {
                throw new \Exception('Gagal memperbarui pengaturan');
            }
            
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Error: ' . $e->getMessage();
        }
        
        header('Location: ?r=profile');
        exit;
    }
    
    /**
     * Reset pengaturan ke default
     */
    public function reset(): void {
        Auth::requireRole('admin');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?r=profile');
            exit;
        }
        
        $db = new Database(require __DIR__.'/../../config/db.php');
        $appSettings = new AppSettings($db);
        $auditLog = new AuditLog($db);
        
        try {
            // Validasi CSRF token
            if (!isset($_POST['csrf_token']) || !CSRF::verify($_POST['csrf_token'])) {
                throw new \Exception('Token CSRF tidak valid');
            }
            
            // Hapus semua pengaturan dan buat ulang
            $allSettings = $appSettings->getAllSettings();
            foreach ($allSettings as $setting) {
                $appSettings->deleteSetting($setting['setting_key']);
            }
            
            // Buat ulang dengan pengaturan default
            $newAppSettings = new AppSettings($db);
            
            // Log audit
            $auditLog->add(
                $_SESSION['user']['id'],
                'profile_reset',
                'Reset pengaturan aplikasi ke default: Reset semua pengaturan'
            );
            
            $_SESSION['message'] = 'Pengaturan berhasil direset ke default!';
            
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Error: ' . $e->getMessage();
        }
        
        header('Location: ?r=profile');
        exit;
    }
    
    /**
     * Backup database ke file SQL
     */
    public function backup(): void {
        Auth::requireRole('admin');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?r=profile');
            exit;
        }
        
        try {
            // Validasi CSRF token
            if (!isset($_POST['csrf_token']) || !CSRF::verify($_POST['csrf_token'])) {
                throw new \Exception('Token CSRF tidak valid');
            }
            
            $db = new Database(require __DIR__.'/../../config/db.php');
            $auditLog = new AuditLog($db);
            
            // Generate nama file backup
            $timestamp = date('Y-m-d_H-i-s');
            $filename = "backup_absensi_pkl_{$timestamp}.sql";
            
            // Set header untuk download
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Cache-Control: no-cache, must-revalidate');
            header('Expires: 0');
            
            // Ambil konfigurasi database
            $dbConfig = require __DIR__.'/../../config/db.php';
            $host = $dbConfig['host'];
            $dbname = $dbConfig['name'];
            $username = $dbConfig['user'];
            $password = $dbConfig['pass'];
            
            // Buat koneksi PDO untuk backup
            $pdo = new \PDO("mysql:host=$host;dbname=$dbname", $username, $password);
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            
            // Output header backup
            echo "-- Backup Database Absensi PKL\n";
            echo "-- Tanggal: " . date('d/m/Y H:i:s') . " WIB\n";
            echo "-- Database: $dbname\n";
            echo "-- Host: $host\n";
            echo "-- Generated by: " . ($_SESSION['user']['name'] ?? 'Administrator') . "\n";
            echo "-- ==========================================\n\n";
            
            // Set charset
            echo "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n";
            echo "START TRANSACTION;\n";
            echo "SET time_zone = \"+00:00\";\n\n";
            
            // Dapatkan daftar tabel
            $tables = $pdo->query("SHOW TABLES")->fetchAll(\PDO::FETCH_COLUMN);
            
            foreach ($tables as $table) {
                // Skip tabel yang tidak perlu di-backup
                if (in_array($table, ['migrations', 'failed_jobs'])) {
                    continue;
                }
                
                echo "-- Struktur tabel `$table`\n";
                echo "DROP TABLE IF EXISTS `$table`;\n";
                
                // Dapatkan CREATE TABLE statement
                $createTable = $pdo->query("SHOW CREATE TABLE `$table`")->fetch();
                echo $createTable[1] . ";\n\n";
                
                // Dapatkan data dari tabel
                $rows = $pdo->query("SELECT * FROM `$table`")->fetchAll(\PDO::FETCH_ASSOC);
                
                if (!empty($rows)) {
                    echo "-- Data untuk tabel `$table`\n";
                    
                    foreach ($rows as $row) {
                        $columns = array_keys($row);
                        $values = array_values($row);
                        
                        // Escape dan quote values
                        $escapedValues = array_map(function($value) use ($pdo) {
                            if ($value === null) {
                                return 'NULL';
                            }
                            return $pdo->quote($value);
                        }, $values);
                        
                        echo "INSERT INTO `$table` (`" . implode('`, `', $columns) . "`) VALUES (" . implode(', ', $escapedValues) . ");\n";
                    }
                    echo "\n";
                }
            }
            
            echo "COMMIT;\n";
            
            // Log audit
            $auditLog->add(
                $_SESSION['user']['id'],
                'database_backup',
                "Backup database berhasil: $filename"
            );
            
            exit;
            
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Error backup database: ' . $e->getMessage();
            header('Location: ?r=profile');
            exit;
        }
    }
    
    /**
     * Restore database dari file SQL
     */
    public function restore(): void {
        Auth::requireRole('admin');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?r=profile');
            exit;
        }
        
        $pdo = null;
        
        try {
            // Validasi CSRF token
            if (!isset($_POST['csrf_token']) || !CSRF::verify($_POST['csrf_token'])) {
                throw new \Exception('Token CSRF tidak valid');
            }
            
            // Validasi file upload
            if (!isset($_FILES['sql_file']) || $_FILES['sql_file']['error'] !== UPLOAD_ERR_OK) {
                throw new \Exception('File SQL tidak ditemukan atau error');
            }
            
            $file = $_FILES['sql_file'];
            
            // Debug info untuk troubleshooting
            error_log("File upload info: " . print_r($file, true));
            
            // Validasi tipe file
            $allowedTypes = ['text/plain', 'application/sql', 'application/octet-stream'];
            $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            
            if (!in_array($fileExtension, ['sql']) && !in_array($file['type'], $allowedTypes)) {
                throw new \Exception('File harus berformat .sql');
            }
            
            // Validasi ukuran file (max 10MB)
            if ($file['size'] > 10 * 1024 * 1024) {
                throw new \Exception('Ukuran file maksimal 10MB');
            }
            
            $db = new Database(require __DIR__.'/../../config/db.php');
            $auditLog = new AuditLog($db);
            $pdo = $db->pdo();

            // Bersihkan database agar tidak konflik jika file SQL tidak mengandung DROP TABLE
            try {
                $pdo->exec('SET FOREIGN_KEY_CHECKS=0');
                $tables = $pdo->query('SHOW TABLES')->fetchAll(\PDO::FETCH_COLUMN);
                foreach ($tables as $table) {
                    $pdo->exec("DROP TABLE IF EXISTS `{$table}`");
                }
                $pdo->exec('SET FOREIGN_KEY_CHECKS=1');
            } catch (\Throwable $t) {
                // Abaikan kesalahan saat pembersihan, lanjutkan restore
            }
            
            // Baca isi file SQL
            $sqlContent = file_get_contents($file['tmp_name']);
            if (empty($sqlContent)) {
                throw new \Exception('File SQL kosong atau tidak dapat dibaca');
            }
            
            // Validasi konten SQL (basic validation)
            if (strpos($sqlContent, '-- Backup Database Absensi PKL') === false) {
                throw new \Exception('File tidak valid atau bukan backup dari sistem ini');
            }
            
            // Nonaktifkan FK checks selama restore
            $pdo->exec('SET FOREIGN_KEY_CHECKS=0');

            // Eksekusi SQL tanpa transaksi untuk menghindari masalah
            try {
                // Split SQL statements
                $statements = array_filter(array_map('trim', explode(';', $sqlContent)));
                
                foreach ($statements as $statement) {
                    if (!empty($statement) && !preg_match('/^(--|\/\*|\n|\r)/', $statement)) {
                        // Perbaiki constraint FK salah yang mengarah ke users untuk pembimbing_id
                        $fixedStatement = preg_replace(
                            '/ADD CONSTRAINT\s+`?siswa_ibfk_2`?\s+FOREIGN KEY\s*\(`?pembimbing_id`?\)\s*REFERENCES\s*`?users`?\s*\(`?id`?\)/i',
                            'ADD CONSTRAINT `siswa_ibfk_2` FOREIGN KEY (`pembimbing_id`) REFERENCES `pembimbing`(`id`)',
                            $statement
                        );

                        $pdo->exec($fixedStatement);
                    }
                }

                // Verifikasi tabel inti, fallback otomatis jika perlu
                try {
                    $this->verifyCoreTables($pdo);
                } catch (\Exception $ve) {
                    // Coba fallback: import schema bawaan untuk memastikan tabel inti ada
                    $schemaPath = __DIR__ . '/../../db_absensi_pkl.sql';
                    if (file_exists($schemaPath)) {
                        $schemaSql = file_get_contents($schemaPath);
                        if ($schemaSql !== false && trim($schemaSql) !== '') {
                            $buffer = '';
                            $lines = preg_split('/\r\n|\r|\n/', $schemaSql);
                            foreach ($lines as $line) {
                                $trimLine = trim($line);
                                if ($trimLine === '' || str_starts_with($trimLine, '--')) { continue; }
                                if (preg_match('/^\/\*!/', $trimLine)) { continue; }
                                $buffer .= $line . "\n";
                                if (substr(rtrim($buffer), -1) === ';') {
                                    $stmt = trim($buffer);
                                    $buffer = '';
                                    if ($stmt !== '') {
                                        $fixedStmt = preg_replace(
                                            '/ADD CONSTRAINT\s+`?siswa_ibfk_2`?\s+FOREIGN KEY\s*\(`?pembimbing_id`?\)\s*REFERENCES\s*`?users`?\s*\(`?id`?\)/i',
                                            'ADD CONSTRAINT `siswa_ibfk_2` FOREIGN KEY (`pembimbing_id`) REFERENCES `pembimbing`(`id`)',
                                            $stmt
                                        );
                                        $pdo->exec($fixedStmt);
                                    }
                                }
                            }
                            // Verifikasi kembali
                            $this->verifyCoreTables($pdo);
                        }
                    }
                }

                // Log audit
                $auditLog->add(
                    $_SESSION['user']['id'],
                    'database_restore',
                    "Restore database berhasil dari file: " . $file['name']
                );
                
                $_SESSION['message'] = 'Database berhasil di-restore dari file ' . $file['name'] . '!';
                
            } catch (\Exception $e) {
                throw new \Exception('Error saat eksekusi SQL: ' . $e->getMessage());
            }
            
            // Aktifkan kembali FK checks
            $pdo->exec('SET FOREIGN_KEY_CHECKS=1');
            
        } catch (\Exception $e) {
            // Pastikan FK checks diaktifkan kembali jika ada error
            if ($pdo) {
                try { 
                    $pdo->exec('SET FOREIGN_KEY_CHECKS=1'); 
                } catch (\Throwable $t) {}
            }
            $_SESSION['error'] = 'Error restore database: ' . $e->getMessage();
        }
        
        header('Location: ?r=profile');
        exit;
    }

    /**
     * Reset ulang database ke kondisi awal berdasarkan file schema bawaan
     * Catatan: TINDAKAN INI MENGHAPUS SELURUH DATA YANG ADA.
     */
    public function resetDatabase(): void {
        Auth::requireRole('admin');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?r=profile');
            exit;
        }

        try {
            // Validasi CSRF token
            if (!isset($_POST['csrf_token']) || !CSRF::verify($_POST['csrf_token'])) {
                throw new \Exception('Token CSRF tidak valid');
            }

            $db = new Database(require __DIR__.'/../../config/db.php');
            $auditLog = new AuditLog($db);
            $pdo = $db->pdo();

            // 1) Matikan foreign key checks dan drop semua tabel
            $pdo->exec('SET FOREIGN_KEY_CHECKS=0');
            $tables = $pdo->query('SHOW TABLES')->fetchAll(\PDO::FETCH_COLUMN);
            foreach ($tables as $table) {
                // Drop semua tabel aplikasi
                $pdo->exec("DROP TABLE IF EXISTS `{$table}`");
            }
            $pdo->exec('SET FOREIGN_KEY_CHECKS=1');

            // 2) Import ulang schema dari file bawaan
            $schemaPath = __DIR__ . '/../../db_absensi_pkl.sql';
            if (!file_exists($schemaPath)) {
                throw new \Exception('File schema db_absensi_pkl.sql tidak ditemukan');
            }

            $sqlContent = file_get_contents($schemaPath);
            if ($sqlContent === false || trim($sqlContent) === '') {
                throw new \Exception('Gagal membaca isi file schema');
            }

            // Eksekusi per-statement (sederhana): pisah berdasarkan ;
            // Abaikan baris komentar dan directive khusus phpMyAdmin (/*! ... */)
            $buffer = '';
            $lines = preg_split('/\r\n|\r|\n/', $sqlContent);
            foreach ($lines as $line) {
                $trimLine = trim($line);
                if ($trimLine === '' || str_starts_with($trimLine, '--')) {
                    continue;
                }
                if (preg_match('/^\/\*!/',$trimLine)) {
                    // Lewati directive seperti /*!40101 SET ... */;
                    continue;
                }
                $buffer .= $line . "\n";
                if (substr(rtrim($buffer), -1) === ';') {
                    $stmt = trim($buffer);
                    $buffer = '';
                    if ($stmt !== '') {
                        // Perbaiki constraint FK salah yang mengarah ke users untuk pembimbing_id
                        $fixedStatement = preg_replace(
                            '/ADD CONSTRAINT\s+`?siswa_ibfk_2`?\s+FOREIGN KEY\s*\(`?pembimbing_id`?\)\s*REFERENCES\s*`?users`?\s*\(`?id`?\)/i',
                            'ADD CONSTRAINT `siswa_ibfk_2` FOREIGN KEY (`pembimbing_id`) REFERENCES `pembimbing`(`id`)',
                            $stmt
                        );
                        $pdo->exec($fixedStatement);
                    }
                }
            }

            // Verifikasi tabel inti tersedia
            $this->verifyCoreTables($pdo);

            // Aktifkan kembali FK checks
            $pdo->exec('SET FOREIGN_KEY_CHECKS=1');

            // Log audit
            $auditLog->add(
                $_SESSION['user']['id'] ?? null,
                'database_reset',
                'Reset database ke kondisi awal menggunakan db_absensi_pkl.sql'
            );

            $_SESSION['message'] = 'Database berhasil direset ke kondisi awal!';

        } catch (\Exception $e) {
            try { $pdo?->exec('SET FOREIGN_KEY_CHECKS=1'); } catch (\Throwable $t) {}
            $_SESSION['error'] = 'Error reset database: ' . $e->getMessage();
        }

        header('Location: ?r=profile');
        exit;
    }
    /**
     * Pastikan tabel inti ada setelah restore/reset
     */
    private function verifyCoreTables(\PDO $pdo): void {
        $required = ['users','pembimbing','tempat_pkl','siswa','absensi','audit_log'];
        $existing = $pdo->query('SHOW TABLES')->fetchAll(\PDO::FETCH_COLUMN);
        $missing = array_values(array_diff($required, $existing));
        if (!empty($missing)) {
            throw new \Exception('Tabel inti hilang setelah restore/reset: ' . implode(', ', $missing));
        }
    }
}
