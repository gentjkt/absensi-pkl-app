<?php
namespace App\Controllers;
use App\Helpers\Auth;
use App\Models\{Database, Absensi, Siswa};

class PembimbingController extends Controller {
    public function dashboard(): void {
        Auth::requireRole('pembimbing');
        $db = new Database(require __DIR__.'/../../config/db.php');
        $uid = (int)$_SESSION['user']['id'];
        
        // Cari pembimbing_id berdasarkan user_id
        $stmt = $db->prepare("SELECT id FROM pembimbing WHERE user_id = ?");
        $stmt->execute([$uid]);
        $pembimbing = $stmt->fetch();
        
        if (!$pembimbing) {
            // Jika tidak ada pembimbing, tampilkan pesan error
            $this->view('pembimbing/dashboard', [
                'absensi' => [],
                'siswaBimbingan' => [],
                'totalSiswa' => 0,
                'siswaAktif' => 0,
                'siswaBelumAbsen' => 0,
                'error' => 'Pembimbing tidak ditemukan untuk user ini.'
            ]);
            return;
        }
        
        $pembimbingId = $pembimbing['id'];
        
        // Ambil data absensi
        $abs = (new Absensi($db))->listByPembimbing($pembimbingId);
        
        // Ambil data siswa yang dibimbing
        $siswa = new Siswa($db);
        $siswaBimbingan = $siswa->listByPembimbing($pembimbingId);
        
        // Hitung statistik siswa
        $totalSiswa = count($siswaBimbingan);
        $siswaAktif = count(array_unique(array_column($abs, 'siswa_id')));
        $siswaBelumAbsen = $totalSiswa - $siswaAktif;
        
        $this->view('pembimbing/dashboard', [
            'absensi' => $abs,
            'siswaBimbingan' => $siswaBimbingan,
            'totalSiswa' => $totalSiswa,
            'siswaAktif' => $siswaAktif,
            'siswaBelumAbsen' => $siswaBelumAbsen
        ]);
    }
    
    public function bimbingan(): void {
        Auth::requireRole('pembimbing');
        $db = new Database(require __DIR__.'/../../config/db.php');
        $uid = (int)$_SESSION['user']['id'];
        
        // Cari pembimbing_id berdasarkan user_id
        $stmt = $db->prepare("SELECT id FROM pembimbing WHERE user_id = ?");
        $stmt->execute([$uid]);
        $pembimbing = $stmt->fetch();
        
        if (!$pembimbing) {
            // Jika tidak ada pembimbing, tampilkan pesan error
            $this->view('pembimbing/bimbingan', [
                'siswaBimbingan' => [],
                'statistikSiswa' => [],
                'totalSiswa' => 0,
                'siswaAktif' => 0,
                'siswaBelumAbsen' => 0,
                'totalAbsensi' => 0,
                'rataRataAbsensi' => 0,
                'error' => 'Pembimbing tidak ditemukan untuk user ini.'
            ]);
            return;
        }
        
        $pembimbingId = $pembimbing['id'];
        
        // Ambil data siswa yang dibimbing
        $siswa = new Siswa($db);
        $siswaBimbingan = $siswa->listByPembimbing($pembimbingId);
        
        // Ambil data absensi untuk statistik
        $absensi = new Absensi($db);
        $absensiData = $absensi->listByPembimbing($pembimbingId);
        
        // Hitung statistik per siswa
        $statistikSiswa = [];
        foreach ($siswaBimbingan as $s) {
            $siswaId = $s['id'];
            $absensiSiswa = array_filter($absensiData, function($a) use ($siswaId) {
                return $a['siswa_id'] == $siswaId;
            });
            
            // Hitung statistik absensi
            $totalAbsen = count($absensiSiswa);
            $dalamRadius = count(array_filter($absensiSiswa, function($a) {
                return (float)($a['jarak_m'] ?? 0) <= 150;
            }));
            $luarRadius = $totalAbsen - $dalamRadius;
            
            // Hitung rata-rata jarak
            $totalJarak = array_sum(array_column($absensiSiswa, 'jarak_m'));
            $rataRataJarak = $totalAbsen > 0 ? $totalJarak / $totalAbsen : 0;
            
            // Cek absen hari ini
            $hariIni = date('Y-m-d');
            $sudahAbsenHariIni = false;
            $waktuAbsenHariIni = null;
            
            foreach ($absensiSiswa as $a) {
                if (date('Y-m-d', strtotime($a['waktu'])) === $hariIni) {
                    $sudahAbsenHariIni = true;
                    $waktuAbsenHariIni = $a['waktu'];
                    break;
                }
            }
            
            // Cek absen terakhir
            $terakhirAbsen = null;
            if (!empty($absensiSiswa)) {
                $terakhirAbsen = max(array_column($absensiSiswa, 'waktu'));
            }
            
            $statistikSiswa[$siswaId] = [
                'siswa' => $s,
                'total_absen' => $totalAbsen,
                'dalam_radius' => $dalamRadius,
                'luar_radius' => $luarRadius,
                'rata_rata_jarak' => $rataRataJarak,
                'sudah_absen_hari_ini' => $sudahAbsenHariIni,
                'waktu_absen_hari_ini' => $waktuAbsenHariIni,
                'terakhir_absen' => $terakhirAbsen,
                'persentase_tepat_waktu' => $totalAbsen > 0 ? ($dalamRadius / $totalAbsen) * 100 : 0
            ];
        }
        
        // Hitung statistik keseluruhan
        $totalSiswa = count($siswaBimbingan);
        $siswaAktif = count(array_filter($statistikSiswa, function($s) {
            return $s['total_absen'] > 0;
        }));
        $siswaBelumAbsen = $totalSiswa - $siswaAktif;
        $totalAbsensi = array_sum(array_column($statistikSiswa, 'total_absen'));
        $rataRataAbsensi = $totalSiswa > 0 ? $totalAbsensi / $totalSiswa : 0;
        
        $this->view('pembimbing/bimbingan', [
            'siswaBimbingan' => $siswaBimbingan,
            'statistikSiswa' => $statistikSiswa,
            'totalSiswa' => $totalSiswa,
            'siswaAktif' => $siswaAktif,
            'siswaBelumAbsen' => $siswaBelumAbsen,
            'totalAbsensi' => $totalAbsensi,
            'rataRataAbsensi' => $rataRataAbsensi
        ]);
    }
    
    public function report(): void {
        Auth::requireRole('pembimbing');
        $db = new Database(require __DIR__.'/../../config/db.php');
        $uid = (int)$_SESSION['user']['id'];
        
        // Ambil nama pembimbing dari session
        $namaPembimbing = $_SESSION['user']['name'] ?? 'Pembimbing';
        
        // Ambil bulan dan tahun dari parameter atau gunakan bulan sekarang
        $month = (int)($_GET['month'] ?? date('n'));
        $year = (int)($_GET['year'] ?? date('Y'));
        
        // Ambil filter tanggal range
        $startDate = $_GET['start_date'] ?? '';
        $endDate = $_GET['end_date'] ?? '';
        
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
        
        // Cari pembimbing_id berdasarkan user_id
        $stmt = $db->prepare("SELECT id FROM pembimbing WHERE user_id = ?");
        $stmt->execute([$uid]);
        $pembimbing = $stmt->fetch();
        
        if (!$pembimbing) {
            // Jika tidak ada pembimbing, tampilkan pesan error
            $this->view('pembimbing/report', [
                'namaPembimbing' => $namaPembimbing,
                'statistikSiswa' => [],
                'bulan' => $month,
                'year' => $year,
                'bulanList' => $bulanList,
                'tahunList' => $tahunList,
                'totalHariKerja' => 0,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'totalHariFilter' => 0,
                'error' => 'Pembimbing tidak ditemukan untuk user ini.'
            ]);
            return;
        }
        
        $pembimbingId = $pembimbing['id'];
        
        // Ambil data siswa yang dibimbing
        $siswa = new Siswa($db);
        $listSiswa = $siswa->listByPembimbing($pembimbingId);
        
        // Ambil data absensi berdasarkan filter
        $absensi = new Absensi($db);
        
        if (!empty($startDate) && !empty($endDate)) {
            // Gunakan filter range tanggal
            $dataAbsensi = $absensi->getDateRangeReport($pembimbingId, $startDate, $endDate);
            $totalHariFilter = $this->getWorkingDaysInRange($startDate, $endDate);
        } else {
            // Gunakan filter bulan dan tahun
            $dataAbsensi = $absensi->getMonthlyReport($pembimbingId, $month, $year);
            $totalHariFilter = $this->getWorkingDays($month, $year);
        }
        
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
        
        // Update total hari dan hitung alpa
        foreach ($statistikSiswa as $siswaId => &$stat) {
            $stat['total_hari'] = $totalHariFilter;
            $stat['alpa'] = $totalHariFilter - $stat['hadir'] - $stat['ijin'] - $stat['sakit'];
            if ($stat['alpa'] < 0) $stat['alpa'] = 0;
        }
        
        $this->view('pembimbing/report', [
            'namaPembimbing' => $namaPembimbing,
            'statistikSiswa' => $statistikSiswa,
            'bulan' => $month,
            'year' => $year,
            'bulanList' => $bulanList,
            'tahunList' => $tahunList,
            'totalHariKerja' => $totalHariFilter,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'totalHariFilter' => $totalHariFilter
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
    
    private function getWorkingDaysInRange(string $startDate, string $endDate): int {
        $start = new DateTime($startDate);
        $end = new DateTime($endDate);
        $interval = new DateInterval('P1D');
        $period = new DatePeriod($start, $interval, $end->modify('+1 day'));
        
        $workingDays = 0;
        foreach ($period as $date) {
            $dayOfWeek = $date->format('N'); // 1 = Monday, 7 = Sunday
            if ($dayOfWeek < 6) { // Monday to Friday
                $workingDays++;
            }
        }
        
        return $workingDays;
    }
}