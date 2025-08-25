<h2>🏠 Dashboard Pembimbing</h2>

<?php if(!empty($_SESSION['user']) && $_SESSION['user']['role'] === 'pembimbing'): ?>
<div class="action-buttons" style="margin-bottom: 20px;">
    <a href="?r=pembimbing/dashboard" class="btn btn-info">
        🏠 Home
    </a>
    <a href="?r=pembimbing/report" class="btn btn-primary">
        📊 Laporan Absensi Bulanan
    </a>
    <a href="?r=pembimbing/bimbingan" class="btn btn-primary">
        👥 Daftar Siswa
    </a>
</div>
<?php endif; ?>

<!-- Statistik Siswa Bimbingan -->
<div class="card">
    <h3>👥 Statistik Siswa Bimbingan</h3>
    <div class="stats-grid" style="margin-bottom: 20px;">
        <div class="stat-card">
            <div class="stat-number"><?= $totalSiswa ?></div>
            <div class="stat-label">Total Siswa</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-number"><?= $siswaAktif ?></div>
            <div class="stat-label">Siswa Aktif</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-number"><?= $siswaBelumAbsen ?></div>
            <div class="stat-label">Belum Absen</div>
        </div>
    </div>
</div>


<!-- Daftar Siswa Bimbingan -->
<div class="card">
    <h3>👥 Daftar Lengkap Siswa Bimbingan</h3>
    
    <?php if(empty($siswaBimbingan)): ?>
        <div class="alert alert-info">
            <strong>ℹ️ Info:</strong> Anda belum memiliki siswa bimbingan.
        </div>
    <?php else: ?>
        <!-- Filter dan Pencarian -->
        <div class="filter-section" style="margin-bottom: 20px; padding: 15px; background-color: #f8f9fa; border-radius: 8px;">
            <h4>🔍 Filter & Pencarian</h4>
            <div class="filter-row" style="display: flex; gap: 15px; flex-wrap: wrap; align-items: end;">
                <div class="form-group" style="flex: 1; min-width: 200px;">
                    <label for="searchSiswa">Cari Siswa:</label>
                    <input type="text" id="searchSiswa" placeholder="Ketik nama atau NIS..." class="form-control">
                </div>
                
                <div class="form-group" style="flex: 1; min-width: 150px;">
                    <label for="filterStatus">Status Hari Ini:</label>
                    <select id="filterStatus" class="form-control">
                        <option value="">Semua Status</option>
                        <option value="sudah">Sudah Absen</option>
                        <option value="belum">Belum Absen</option>
                    </select>
                </div>
                
                <div class="form-group" style="flex: 1; min-width: 150px;">
                    <label for="filterKelas">Filter Kelas:</label>
                    <select id="filterKelas" class="form-control">
                        <option value="">Semua Kelas</option>
                        <?php 
                            $kelasUnik = array_unique(array_column($siswaBimbingan, 'kelas'));
                            foreach($kelasUnik as $kelas): 
                                if (!empty($kelas)):
                        ?>
                            <option value="<?= htmlspecialchars($kelas) ?>"><?= htmlspecialchars($kelas) ?></option>
                        <?php 
                                endif;
                            endforeach; 
                        ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <button type="button" class="btn btn-primary" onclick="filterSiswa()">
                        🔍 Terapkan Filter
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="resetFilterSiswa()">
                        🔄 Reset
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Tabel Siswa Bimbingan -->
        <div class="table-responsive">
            <table class="table" id="tabelSiswa">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>NIS</th>
                        <th>Nama Siswa</th>
                        <th>Kelas</th>
                        <th>Tempat PKL</th>
                        <th>Status Hari Ini</th>
                        <th>Total Absen</th>
                        <th>Terakhir Absen</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach($siswaBimbingan as $index => $s): ?>
                    <?php 
                        // Cek status absensi hari ini
                        $hariIni = date('Y-m-d');
                        $sudahAbsenHariIni = false;
                        $waktuAbsenHariIni = null;
                        $totalAbsen = 0;
                        $terakhirAbsen = null;
                        
                        // Hitung total absensi dan cek status hari ini
                        foreach($absensi as $a) {
                            if ($a['siswa_id'] == $s['id']) {
                                $totalAbsen++;
                                if (date('Y-m-d', strtotime($a['waktu'])) === $hariIni) {
                                    $sudahAbsenHariIni = true;
                                    $waktuAbsenHariIni = $a['waktu'];
                                }
                                if (!$terakhirAbsen || strtotime($a['waktu']) > strtotime($terakhirAbsen)) {
                                    $terakhirAbsen = $a['waktu'];
                                }
                            }
                        }
                        
                        $statusColor = $sudahAbsenHariIni ? 'green' : 'red';
                        $statusText = $sudahAbsenHariIni ? '✅ Sudah Absen' : '⏰ Belum Absen';
                        $waktuAbsen = $waktuAbsenHariIni ? date('H:i', strtotime($waktuAbsenHariIni)) : '-';
                    ?>
                    <tr data-siswa="<?= htmlspecialchars(strtolower($s['nama'] . ' ' . $s['nis'])) ?>" 
                        data-kelas="<?= htmlspecialchars($s['kelas']) ?>"
                        data-status="<?= $sudahAbsenHariIni ? 'sudah' : 'belum' ?>">
                        <td><?= $index + 1 ?></td>
                        <td>
                            <strong><?= htmlspecialchars($s['nis'] ?? '-') ?></strong>
                        </td>
                        <td>
                            <strong><?= htmlspecialchars($s['nama']) ?></strong>
                        </td>
                        <td><?= htmlspecialchars($s['kelas'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($s['tempat_pkl'] ?? '-') ?></td>
                        <td>
                            <span style="color: <?= $statusColor ?>; font-weight: bold;">
                                <?= $statusText ?>
                            </span>
                            <?php if ($waktuAbsenHariIni): ?>
                                <br><small>(<?= $waktuAbsen ?>)</small>
                            <?php endif; ?>
                        </td>
                        <td style="text-align: center;">
                            <span class="badge badge-<?= $totalAbsen > 0 ? 'success' : 'warning' ?>">
                                <?= $totalAbsen ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($terakhirAbsen): ?>
                                <span class="time-info">
                                    <?= htmlspecialchars(date('d/m/Y H:i', strtotime($terakhirAbsen))) ?>
                                </span>
                            <?php else: ?>
                                <span class="text-muted">Belum pernah absen</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="?r=pembimbing/bimbingan" class="btn btn-sm btn-info">
                                👁️ Detail
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<!-- Absensi Terbaru -->
<div class="card">
    <h3>📊 Absensi Terbaru</h3>
  
    <?php if(empty($absensi)): ?>
        <div class="alert alert-info">
            <strong>ℹ️ Info:</strong> Belum ada data absensi dari siswa yang Anda bimbing.
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>NIS</th>
                        <th>Nama Siswa</th>
                        <th>Kelas</th>
                        <th>Tempat PKL</th>
                        <th>Waktu Absen</th>
                        <th>Lokasi</th>
                        <th>Jarak (m)</th>
                        <th>Selfie</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach($absensi as $a): ?>
                    <tr>
                        <td>
                            <strong><?= \App\Helpers\Response::e($a['nis'] ?? '-') ?></strong>
                        </td>
                        <td>
                            <strong><?= \App\Helpers\Response::e($a['siswa']) ?></strong>
                        </td>
                        <td><?= \App\Helpers\Response::e($a['kelas'] ?? '-') ?></td>
                        <td><?= \App\Helpers\Response::e($a['tempat_pkl'] ?? '-') ?></td>
                        <td>
                            <span class="time-info">
                                <?= \App\Helpers\Response::e(date('d/m/Y H:i', strtotime($a['waktu']))) ?>
                            </span>
                        </td>
                        <td>
                            <span class="location-info">
                                <?= \App\Helpers\Response::e(number_format($a['lat'], 6)) ?>, 
                                <?= \App\Helpers\Response::e(number_format($a['lng'], 6)) ?>
                            </span>
                        </td>
                        <td>
                            <span class="distance-info">
                                <?= \App\Helpers\Response::e(number_format($a['jarak_m'], 1)) ?> m
                            </span>
                        </td>
                        <td>
                            <?php if(!empty($a['selfie_path'])): ?>
                                <a target="_blank" href="<?= \App\Helpers\Response::e($a['selfie_path']) ?>" class="btn btn-sm btn-outline-primary">
                                    👁️ Lihat
                                </a>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php 
                                $jarak = (float)($a['jarak_m'] ?? 0);
                                if ($jarak <= 150) {
                                    echo '<span class="badge badge-success">✅ Dalam Radius</span>';
                                } else {
                                    echo '<span class="badge badge-warning">⚠️ Luar Radius</span>';
                                }
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<script>
// Filter untuk daftar siswa bimbingan
function filterSiswa() {
    const searchTerm = document.getElementById('searchSiswa').value.toLowerCase();
    const statusFilter = document.getElementById('filterStatus').value;
    const kelasFilter = document.getElementById('filterKelas').value;
    
    const rows = document.querySelectorAll('tbody tr');
    
    rows.forEach(row => {
        const nama = row.cells[2].textContent.toLowerCase();
        const nis = row.cells[1].textContent.toLowerCase();
        const kelas = row.cells[3].textContent;
        const status = row.cells[6].textContent.toLowerCase();
        
        let showRow = true;
        
        // Search filter
        if (searchTerm && !nama.includes(searchTerm) && !nis.includes(searchTerm)) {
            showRow = false;
        }
        
        // Status filter
        if (statusFilter === 'sudah' && !status.includes('sudah absen')) {
            showRow = false;
        }
        if (statusFilter === 'belum' && !status.includes('belum absen')) {
            showRow = false;
        }
        
        // Kelas filter
        if (kelasFilter && kelas !== kelasFilter) {
            showRow = false;
        }
        
        row.style.display = showRow ? '' : 'none';
    });
}

function resetFilterSiswa() {
    document.getElementById('searchSiswa').value = '';
    document.getElementById('filterStatus').value = '';
    document.getElementById('filterKelas').value = '';
    
    const rows = document.querySelectorAll('tbody tr');
    rows.forEach(row => row.style.display = '');
}

// Filter untuk absensi (fungsi lama)
function filterData() {
    const searchTerm = document.getElementById('searchSiswa').value.toLowerCase();
    const statusFilter = document.getElementById('filterStatus').value;
    const dateFilter = document.getElementById('filterDate').value;
    
    const rows = document.querySelectorAll('tbody tr');
    
    rows.forEach(row => {
        const nama = row.cells[1].textContent.toLowerCase();
        const nis = row.cells[0].textContent.toLowerCase();
        const waktu = row.cells[4].textContent;
        const status = row.cells[8].textContent.toLowerCase();
        
        let showRow = true;
        
        // Search filter
        if (searchTerm && !nama.includes(searchTerm) && !nis.includes(searchTerm)) {
            showRow = false;
        }
        
        // Status filter
        if (statusFilter === 'dalam' && !status.includes('dalam radius')) {
            showRow = false;
        }
        if (statusFilter === 'luar' && !status.includes('luar radius')) {
            showRow = false;
        }
        
        // Date filter
        if (dateFilter && !waktu.includes(dateFilter.split('-').reverse().join('/'))) {
            showRow = false;
        }
        
        row.style.display = showRow ? '' : 'none';
    });
}

function resetFilter() {
    document.getElementById('searchSiswa').value = '';
    document.getElementById('filterStatus').value = '';
    document.getElementById('filterDate').value = '';
    
    const rows = document.querySelectorAll('tbody tr');
    rows.forEach(row => row.style.display = '');
}
</script>