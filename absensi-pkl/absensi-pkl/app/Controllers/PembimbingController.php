<?php
namespace App\Controllers;
use App\Helpers\Auth;
use App\Models\{Database, Absensi, Siswa};

class PembimbingController extends Controller {
    public function dashboard(): void {
        Auth::requireRole('pembimbing');
        $db = new Database(require __DIR__.'/../../config/db.php');
        $uid = (int)$_SESSION['user']['id'];
        $abs = (new Absensi($db))->listByPembimbing($uid);
        $this->view('pembimbing/dashboard', ['absensi' => $abs]);
    }
    
    public function report(): void {
        Auth::requireRole('pembimbing');
        $db = new Database(require __DIR__.'/../../config/db.php');
        $uid = (int)$_SESSION['user']['id'];
        
        // Ambil bulan dan tahun dari parameter atau gunakan bulan sekarang
        $month = (int)($_GET['month'] ?? date('n'));
        $year = (int)($_GET['year'] ?? date('Y'));
        
        // Ambil data siswa yang dibimbing
        $siswa = new Siswa($db);
        $listSiswa = $siswa->listByPembimbing($uid);
        
        // Ambil data absensi untuk bulan tertentu
        $absensi = new Absensi($db);
        $dataAbsensi = $absensi->getMonthlyReport($uid, $month, $year);
        
        // Hitung statistik per siswa
        $statistikSiswa = [];
        foreach ($listSiswa as $s) {
            $siswaId = $s['id'];
            $statistikSiswa[$siswaId] = [
                'siswa' => $s,
                'hadir' => 0,
                'ijin' => 0,
                'sakit' => 0,
                'alpa' => 0,
                'total_hari' => 0
            ];
        }
        
        // Proses data absensi
        foreach ($dataAbsensi as $abs) {
            $siswaId = $abs['siswa_id'];
            if (isset($statistikSiswa[$siswaId])) {
                $statistikSiswa[$siswaId]['hadir']++;
            }
        }
        
        // Hitung hari kerja dalam bulan (exclude weekend)
        $totalHariKerja = $this->getWorkingDays($month, $year);
        
        // Update total hari dan hitung alpa
        foreach ($statistikSiswa as $siswaId => &$stat) {
            $stat['total_hari'] = $totalHariKerja;
            $stat['alpa'] = $totalHariKerja - $stat['hadir'] - $stat['ijin'] - $stat['sakit'];
            if ($stat['alpa'] < 0) $stat['alpa'] = 0;
        }
        
        // Ambil daftar bulan untuk dropdown
        $bulanList = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        
        // Ambil daftar tahun (5 tahun ke belakang dan 1 tahun ke depan)
        $tahunList = [];
        $currentYear = date('Y');
        for ($i = $currentYear - 5; $i <= $currentYear + 1; $i++) {
            $tahunList[] = $i;
        }
        
        $this->view('pembimbing/report', [
            'statistikSiswa' => $statistikSiswa,
            'bulan' => $month,
            'tahun' => $year,
            'bulanList' => $bulanList,
            'tahunList' => $tahunList,
            'totalHariKerja' => $totalHariKerja
        ]);
    }
    
    private function getWorkingDays(int $month, int $year): int {
        $totalDays = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $workingDays = 0;
        
        for ($day = 1; $day <= $totalDays; $day++) {
            $date = date('N', mktime(0, 0, 0, $month, $day, $year));
            // 6 = Saturday, 7 = Sunday
            if ($date < 6) {
                $workingDays++;
            }
        }
        
        return $workingDays;
    }
}