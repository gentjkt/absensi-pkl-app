<?php
namespace App\Controllers;
use App\Helpers\{Auth, Response};
use App\Models\{Database, Absensi};
class ReportController extends Controller {
    private function fetch(array $f): array {
        $db = new Database(require __DIR__.'/../../config/db.php');
        // Build query with filters (date range, class, tempat)
        $sql = "SELECT a.*, s.nama as siswa, s.kelas, t.nama as tempat FROM absensi a
                JOIN siswa s ON a.siswa_id=s.id
                LEFT JOIN tempat_pkl t ON s.tempat_pkl_id=t.id WHERE 1=1";
        $params = [];
        if (!empty($f['start'])) { $sql .= " AND a.waktu >= ?"; $params[] = $f['start'].' 00:00:00'; }
        if (!empty($f['end']))   { $sql .= " AND a.waktu <= ?"; $params[] = $f['end'].' 23:59:59'; }
        if (!empty($f['kelas'])) { $sql .= " AND s.kelas = ?"; $params[] = $f['kelas']; }
        if (!empty($f['tempat'])){ $sql .= " AND t.id = ?"; $params[] = (int)$f['tempat']; }
        $sql .= " ORDER BY a.waktu DESC";
        $st = $db->pdo()->prepare($sql); $st->execute($params);
        return $st->fetchAll();
    }
    public function index(): void {
        Auth::requireRole('admin','pembimbing');
        $filters = [
            'start'=>$_GET['start']??'',
            'end'=>$_GET['end']??'',
            'kelas'=>$_GET['kelas']??'',
            'tempat'=>$_GET['tempat']??'',
        ];
        $rows = $this->fetch($filters);
        $this->view('admin/report', ['rows'=>$rows,'filters'=>$filters]);
    }
    public function csv(): void {
        Auth::requireRole('admin','pembimbing');
        $filters = [
            'start'=>$_GET['start']??'',
            'end'=>$_GET['end']??'',
            'kelas'=>$_GET['kelas']??'',
            'tempat'=>$_GET['tempat']??'',
        ];
        $rows = $this->fetch($filters);
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="report_absensi.csv"');
        $out=fopen('php://output','w');
        fputcsv($out,['Siswa','Kelas','Tempat','Waktu','Lat','Lng','Jarak(m)','Selfie']);
        foreach($rows as $r){ fputcsv($out,[$r['siswa'],$r['kelas'],$r['tempat'],$r['waktu'],$r['lat'],$r['lng'],$r['jarak_m'],$r['selfie_path']]); }
        fclose($out); exit;
    }
    public function print(): void {
        Auth::requireRole('admin','pembimbing');
        $filters = [
            'start'=>$_GET['start']??'',
            'end'=>$_GET['end']??'',
            'kelas'=>$_GET['kelas']??'',
            'tempat'=>$_GET['tempat']??'',
        ];
        $rows = $this->fetch($filters);
        // render a minimal view without layout for browser's "Save as PDF"
        include __DIR__ . '/../Views/admin/report_print.php';
    }
}