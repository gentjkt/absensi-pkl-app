<?php
namespace App\Controllers;
use App\Helpers\{Auth, CSRF};
use App\Models\{Database, Siswa, TempatPKL, User, AuditLog, Pembimbing};

class ImportController extends Controller {
    public function siswa(): void {
        Auth::requireRole('admin');
        if($_SERVER['REQUEST_METHOD']!=='POST'){ $this->view('admin/import'); return; }
        if(!CSRF::verify($_POST['csrf_token']??'')) { http_response_code(400); echo 'Invalid CSRF'; return; }
        if(!isset($_FILES['csv_file']) || $_FILES['csv_file']['error']!==UPLOAD_ERR_OK){ echo 'Upload gagal'; return; }
        
        $tmp = $_FILES['csv_file']['tmp_name'];
        $db = new Database(require __DIR__.'/../../config/db.php');
        $mS = new Siswa($db); 
        $mT = new TempatPKL($db); 
        $mU = new User($db); 
        $mP = new Pembimbing($db);
        $log = new AuditLog($db);
        
        $fh = fopen($tmp,'r'); 
        $count = 0; 
        $skipped = 0;
        
        while(($row = fgetcsv($fh)) !== false){
            if(count($row) < 7) { 
                $skipped++; 
                continue; 
            }
            
            // Format baru: username, password, NIS, Nama, kelas, pembimbing, tempat PKL
            [$username, $password, $nis, $nama, $kelas, $namaPembimbing, $namaTempat] = $row;
            
            // Validasi data tidak boleh kosong
            if(empty($username) || empty($password) || empty($nis) || empty($nama) || empty($kelas) || empty($namaPembimbing) || empty($namaTempat)) {
                $skipped++; 
                continue; 
            }
            
            // Cek username sudah ada atau belum
            $st = $db->pdo()->prepare("SELECT id FROM users WHERE username=? LIMIT 1");
            $st->execute([$username]); 
            if($st->fetch()){ 
                $skipped++; 
                continue; 
            }
            
            // Cek NIS sudah ada atau belum
            $st = $db->pdo()->prepare("SELECT id FROM siswa WHERE nis=? LIMIT 1"); 
            $st->execute([$nis]);
            if($st->fetch()){ 
                $skipped++; 
                continue; 
            }
            
            // Cari atau buat pembimbing berdasarkan nama
            $st = $db->pdo()->prepare("SELECT id FROM pembimbing WHERE nama=? LIMIT 1");
            $st->execute([$namaPembimbing]); 
            $pb = $st->fetch();
            if(!$pb){ 
                // Buat pembimbing baru jika tidak ada dengan NIP unik
                $uniqueNIP = 'IMP' . date('Ymd') . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
                $pembimbingId = $mP->create([
                    'nama' => $namaPembimbing,
                    'nip' => $uniqueNIP
                ]);
            } else {
                $pembimbingId = (int)$pb['id'];
            }
            
            // Cari atau buat tempat PKL berdasarkan nama
            $st = $db->pdo()->prepare("SELECT id FROM tempat_pkl WHERE nama=? LIMIT 1");
            $st->execute([$namaTempat]); 
            $tp = $st->fetch();
            if(!$tp){ 
                // Buat tempat PKL baru jika tidak ada
                $tempatId = $mT->create([
                    'nama' => $namaTempat,
                    'lat' => 0.0,
                    'lng' => 0.0,
                    'radius_m' => 150
                ]);
            } else {
                $tempatId = (int)$tp['id'];
            }
            
            // Buat user untuk siswa
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $userId = $mU->create([
                'username' => $username,
                'password_hash' => $hashedPassword,
                'role' => 'siswa',
                'name' => $nama
            ]);
            
            // Buat data siswa
            $mS->create([
                'user_id' => $userId,
                'nis' => $nis,
                'nama' => $nama,
                'kelas' => $kelas,
                'pembimbing_id' => $pembimbingId,
                'tempat_pkl_id' => $tempatId
            ]);
            
            $count++;
        }
        fclose($fh);
        $log->add($_SESSION['user']['id']??null, 'import_csv_siswa', "imported=$count skipped=$skipped");
        $this->view('admin/import_result',[
            'results' => [], // Empty results since this controller doesn't provide detailed results
            'total_rows' => $count + $skipped,
            'success_count' => $count,
            'error_count' => $skipped
        ]);
    }
    
    public function form(): void {
        Auth::requireRole('admin');
        $this->view('admin/import');
    }
}