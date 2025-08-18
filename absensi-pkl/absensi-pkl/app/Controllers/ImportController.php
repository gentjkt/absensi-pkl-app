<?php
namespace App\Controllers;
use App\Helpers\{Auth, CSRF};
use App\Models\{Database, Siswa, TempatPKL, User, AuditLog};
class ImportController extends Controller {
    public function siswa(): void {
        Auth::requireRole('admin');
        if($_SERVER['REQUEST_METHOD']!=='POST'){ $this->view('admin/import'); return; }
        if(!CSRF::verify($_POST['csrf']??'')) { http_response_code(400); echo 'Invalid CSRF'; return; }
        if(!isset($_FILES['csv']) || $_FILES['csv']['error']!==UPLOAD_ERR_OK){ echo 'Upload gagal'; return; }
        $tmp = $_FILES['csv']['tmp_name'];
        $db=new Database(require __DIR__.'/../../config/db.php');
        $mS=new Siswa($db); $mT=new TempatPKL($db); $mU=new User($db); $log=new AuditLog($db);
        $fh=fopen($tmp,'r'); $count=0; $skipped=0;
        while(($row=fgetcsv($fh))!==false){
            if(count($row)<7) { $skipped++; continue; }
            [$nis,$nama,$kelas,$namaTempat,$lat,$lng,$pembUsername] = $row;
            // ensure pembimbing exists
            $st=$db->pdo()->prepare("SELECT id FROM users WHERE username=? AND role='pembimbing' LIMIT 1");
            $st->execute([$pembUsername]); $pb=$st->fetch();
            if(!$pb){ $skipped++; continue; }
            // ensure tempat exists or create
            $st=$db->pdo()->prepare("SELECT id FROM tempat_pkl WHERE nama=? LIMIT 1");
            $st->execute([$namaTempat]); $tp=$st->fetch();
            $tempatId = $tp ? (int)$tp['id'] : (function() use($mT,$namaTempat,$lat,$lng){ return $mT->create(['nama'=>$namaTempat,'lat'=>(float)$lat,'lng'=>(float)$lng,'radius_m'=>150]); })();
            // avoid duplicate NIS
            $st=$db->pdo()->prepare("SELECT id FROM siswa WHERE nis=? LIMIT 1"); $st->execute([$nis]);
            if($st->fetch()){ $skipped++; continue; }
            $mS->create(['nis'=>$nis,'nama'=>$nama,'kelas'=>$kelas,'pembimbing_id'=>(int)$pb['id'],'tempat_pkl_id'=>$tempatId]);
            $count++;
        }
        fclose($fh);
        $log->add($_SESSION['user']['id']??null, 'import_csv_siswa', "imported=$count skipped=$skipped");
        $this->view('admin/import_result',['imported'=>$count,'skipped'=>$skipped]);
    }
    public function form(): void {
        Auth::requireRole('admin');
        $this->view('admin/import');
    }
}