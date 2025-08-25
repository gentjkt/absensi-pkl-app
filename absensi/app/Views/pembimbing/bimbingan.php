<h2>👥 Menu Bimbingan</h2>

<div class="action-buttons" style="margin-bottom: 20px;">
    <a href="?r=pembimbing/dashboard" class="btn btn-info">
        🏠 Dashboard
    </a>
    <a href="?r=pembimbing/report" class="btn btn-primary">
        📊 Laporan Absensi Bulanan
    </a>
</div>

<!-- Statistik Keseluruhan -->
<div class="card">
    <h3>📊 Statistik Keseluruhan Bimbingan</h3>
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
            <div class="stat-label">Belum Pernah Absen</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-number"><?= $totalAbsensi ?></div>
            <div class="stat-label">Total Absensi</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-number"><?= number_format($rataRataAbsensi, 1) ?></div>
            <div class="stat-label">Rata-rata Absensi/Siswa</div>
        </div>
    </div>
</div>

<!-- Daftar Siswa Bimbingan -->
<div class="card">
    <h3>📚 Daftar Lengkap Siswa Bimbingan</h3>
    
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
                
                <div class="form-group" style="flex: 1; min-width: 150px;">
                    <label for="filterAktivitas">Aktivitas:</label>
                    <select id="filterAktivitas" class="form-control">
                        <option value="">Semua</option>
                        <option value="aktif">Aktif (Sudah Absen)</option>
                        <option value="tidak_aktif">Tidak Aktif (Belum Absen)</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <button type="button" class="btn btn-primary" onclick="filterSiswa()">
                        🔍 Terapkan Filter
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="resetFilter()">
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
                        <th>Dalam Radius</th>
                        <th>Luar Radius</th>
                        <th>Rata-rata Jarak</th>
                        <th>Terakhir Absen</th>
                        <th>Persentase Tepat Waktu</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach($siswaBimbingan as $index => $s): ?>
                    <?php 
                        $stat = $statistikSiswa[$s['id']] ?? null;
                        $statusColor = $stat && $stat['sudah_absen_hari_ini'] ? 'green' : 'red';
                        $statusText = $stat && $stat['sudah_absen_hari_ini'] ? '✅ Sudah Absen' : '⏰ Belum Absen';
                        $waktuAbsen = $stat && $stat['waktu_absen_hari_ini'] ? date('H:i', strtotime($stat['waktu_absen_hari_ini'])) : '-';
                    ?>
                    <tr data-siswa="<?= htmlspecialchars(strtolower($s['nama'] . ' ' . $s['nis'])) ?>" 
                        data-kelas="<?= htmlspecialchars($s['kelas']) ?>"
                        data-status="<?= $stat && $stat['sudah_absen_hari_ini'] ? 'sudah' : 'belum' ?>"
                        data-aktivitas="<?= $stat && $stat['total_absen'] > 0 ? 'aktif' : 'tidak_aktif' ?>">
                        <td><?= $index + 1 ?></td>
                        <td>
                            <strong><?= \App\Helpers\Response::e($s['nis'] ?? '-') ?></strong>
                        </td>
                        <td>
                            <strong><?= \App\Helpers\Response::e($s['nama']) ?></strong>
                        </td>
                        <td><?= \App\Helpers\Response::e($s['kelas'] ?? '-') ?></td>
                        <td><?= \App\Helpers\Response::e($s['tempat_pkl'] ?? '-') ?></td>
                        <td>
                            <span style="color: <?= $statusColor ?>; font-weight: bold;">
                                <?= $statusText ?>
                            </span>
                            <?php if ($stat && $stat['waktu_absen_hari_ini']): ?>
                                <br><small>(<?= $waktuAbsen ?>)</small>
                            <?php endif; ?>
                        </td>
                        <td style="text-align: center;">
                            <span class="badge badge-<?= $stat && $stat['total_absen'] > 0 ? 'success' : 'warning' ?>">
                                <?= $stat ? $stat['total_absen'] : 0 ?>
                            </span>
                        </td>
                        <td style="text-align: center;">
                            <span class="badge badge-success">
                                <?= $stat ? $stat['dalam_radius'] : 0 ?>
                            </span>
                        </td>
                        <td style="text-align: center;">
                            <span class="badge badge-warning">
                                <?= $stat ? $stat['luar_radius'] : 0 ?>
                            </span>
                        </td>
                        <td style="text-align: center;">
                            <?php if ($stat && $stat['total_absen'] > 0): ?>
                                <span class="badge badge-info">
                                    <?= number_format($stat['rata_rata_jarak'], 1) ?> m
                                </span>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($stat && $stat['terakhir_absen']): ?>
                                <span class="time-info">
                                    <?= \App\Helpers\Response::e(date('d/m/Y H:i', strtotime($stat['terakhir_absen']))) ?>
                                </span>
                            <?php else: ?>
                                <span class="text-muted">Belum pernah absen</span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align: center;">
                            <?php if ($stat && $stat['total_absen'] > 0): ?>
                                <?php 
                                    $persentase = $stat['persentase_tepat_waktu'];
                                    $badgeClass = $persentase >= 80 ? 'success' : ($persentase >= 60 ? 'warning' : 'danger');
                                ?>
                                <span class="badge badge-<?= $badgeClass ?>">
                                    <?= number_format($persentase, 1) ?>%
                                </span>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="?r=pembimbing/report&siswa_id=<?= $s['id'] ?>" class="btn btn-sm btn-outline-primary" title="Detail Laporan">
                                    📊 Detail
                                </a>
                                <a href="?r=pembimbing/report&siswa_id=<?= $s['id'] ?>&month=<?= date('n') ?>&year=<?= date('Y') ?>" class="btn btn-sm btn-outline-info" title="Laporan Bulan Ini">
                                    📅 Bulan Ini
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Informasi Tambahan -->
        <div class="alert alert-info" style="margin-top: 20px;">
            <h4>💡 Informasi Menu Bimbingan</h4>
            <ul>
                <li><strong>Status Hari Ini:</strong> Indikator apakah siswa sudah absen hari ini</li>
                <li><strong>Total Absen:</strong> Jumlah total absensi yang pernah dilakukan</li>
                <li><strong>Dalam Radius:</strong> Absensi yang dilakukan dalam radius 150m dari tempat PKL</li>
                <li><strong>Luar Radius:</strong> Absensi yang dilakukan di luar radius 150m</li>
                <li><strong>Rata-rata Jarak:</strong> Rata-rata jarak absensi dari tempat PKL</li>
                <li><strong>Persentase Tepat Waktu:</strong> Persentase absensi yang dilakukan dalam radius</li>
                <li><strong>Aksi:</strong> Link ke detail laporan dan laporan bulan ini</li>
            </ul>
        </div>
    <?php endif; ?>
</div>

<!-- Grafik Statistik -->


<script>
// Filter dan pencarian siswa
function filterSiswa() {
    const searchTerm = document.getElementById('searchSiswa').value.toLowerCase();
    const statusFilter = document.getElementById('filterStatus').value;
    const kelasFilter = document.getElementById('filterKelas').value;
    const aktivitasFilter = document.getElementById('filterAktivitas').value;
    
    const rows = document.querySelectorAll('#tabelSiswa tbody tr');
    
    rows.forEach(row => {
        const siswaData = row.getAttribute('data-siswa');
        const kelas = row.getAttribute('data-kelas');
        const status = row.getAttribute('data-status');
        const aktivitas = row.getAttribute('data-aktivitas');
        
        let showRow = true;
        
        // Search filter
        if (searchTerm && !siswaData.includes(searchTerm)) {
            showRow = false;
        }
        
        // Status filter
        if (statusFilter && status !== statusFilter) {
            showRow = false;
        }
        
        // Kelas filter
        if (kelasFilter && kelas !== kelasFilter) {
            showRow = false;
        }
        
        // Aktivitas filter
        if (aktivitasFilter && aktivitas !== aktivitasFilter) {
            showRow = false;
        }
        
        row.style.display = showRow ? '' : 'none';
    });
    
    // Update nomor urut
    updateRowNumbers();
}

function resetFilter() {
    document.getElementById('searchSiswa').value = '';
    document.getElementById('filterStatus').value = '';
    document.getElementById('filterKelas').value = '';
    document.getElementById('filterAktivitas').value = '';
    
    const rows = document.querySelectorAll('#tabelSiswa tbody tr');
    rows.forEach(row => row.style.display = '');
    
    // Update nomor urut
    updateRowNumbers();
}

function updateRowNumbers() {
    const visibleRows = document.querySelectorAll('#tabelSiswa tbody tr:not([style*="display: none"])');
    visibleRows.forEach((row, index) => {
        row.cells[0].textContent = index + 1;
    });
}

// Auto-filter saat input berubah
document.getElementById('searchSiswa').addEventListener('input', filterSiswa);
document.getElementById('filterStatus').addEventListener('change', filterSiswa);
document.getElementById('filterKelas').addEventListener('change', filterSiswa);
document.getElementById('filterAktivitas').addEventListener('change', filterSiswa);
</script>

<style>
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 20px;
}

.stat-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 20px;
    border-radius: 10px;
    text-align: center;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.stat-number {
    font-size: 2em;
    font-weight: bold;
    margin-bottom: 5px;
}

.stat-label {
    font-size: 0.9em;
    opacity: 0.9;
}

.filter-section {
    background-color: #f8f9fa;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
}

.filter-row {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
    align-items: end;
}

.form-group {
    flex: 1;
    min-width: 200px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
    color: #333;
}

.form-control {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
}

.btn {
    padding: 8px 16px;
    border: none;
    border-radius: 4px;
    font-size: 14px;
    cursor: pointer;
    text-decoration: none;
    display: inline-block;
}

.btn-primary {
    background-color: #007bff;
    color: white;
}

.btn-secondary {
    background-color: #6c757d;
    color: white;
}

.btn-outline-primary {
    background-color: transparent;
    color: #007bff;
    border: 1px solid #007bff;
}

.btn-outline-info {
    background-color: transparent;
    color: #17a2b8;
    border: 1px solid #17a2b8;
}

.badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.8em;
    font-weight: bold;
}

.badge-success {
    background-color: #28a745;
    color: white;
}

.badge-warning {
    background-color: #ffc107;
    color: #212529;
}

.badge-danger {
    background-color: #dc3545;
    color: white;
}

.badge-info {
    background-color: #17a2b8;
    color: white;
}

.chart-container {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
}

.chart-item {
    flex: 1;
    min-width: 300px;
}

.chart-bar {
    margin-bottom: 15px;
    position: relative;
}

.chart-label {
    font-weight: bold;
    margin-bottom: 5px;
}

.chart-bar-fill {
    height: 20px;
    border-radius: 10px;
    transition: width 0.3s ease;
}

.chart-value {
    position: absolute;
    right: 0;
    top: 0;
    font-weight: bold;
}

.chart-circle {
    text-align: center;
    padding: 20px;
}

.chart-number {
    font-size: 2em;
    font-weight: bold;
    color: #007bff;
}

.table-responsive {
    overflow-x: auto;
}

.table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
}

.table th,
.table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #dee2e6;
}

.table th {
    background-color: #f8f9fa;
    font-weight: bold;
    color: #333;
}

.table tbody tr:hover {
    background-color: #f8f9fa;
}

.btn-group {
    display: flex;
    gap: 5px;
}

.btn-sm {
    padding: 4px 8px;
    font-size: 12px;
}

@media (max-width: 768px) {
    .filter-row {
        flex-direction: column;
    }
    
    .form-group {
        min-width: 100%;
    }
    
    .stats-grid {
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    }
    
    .chart-container {
        flex-direction: column;
    }
    
    .chart-item {
        min-width: 100%;
    }
}
</style>
