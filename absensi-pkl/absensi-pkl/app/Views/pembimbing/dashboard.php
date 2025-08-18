<h2>Dashboard Pembimbing</h2>

<?php if(!empty($_SESSION['user']) && $_SESSION['user']['role'] === 'pembimbing'): ?>
<div class="action-buttons" style="margin-bottom: 20px;">
    <a href="?r=pembimbing/dashboard" class="btn btn-info">
        🏠 Home
    </a>
</div>
<?php endif; ?>

<div class="action-buttons" style="margin-bottom: 20px;">
  <a href="?r=pembimbing/report" class="btn btn-primary">
    📊 Laporan Absensi Bulanan
  </a>
  <a href="?r=pembimbing/report&month=<?= date('n') ?>&year=<?= date('Y') ?>" class="btn btn-success">
    📈 Laporan Bulan Ini
  </a>
</div>

<div class="card">
  <h3>📊 Ringkasan Absensi Siswa</h3>
  
  <?php if(empty($absensi)): ?>
    <div class="alert alert-info">
      <strong>ℹ️ Info:</strong> Belum ada data absensi dari siswa yang Anda bimbing.
    </div>
  <?php else: ?>
    <div class="stats-grid" style="margin-bottom: 20px;">
      <div class="stat-card">
        <div class="stat-number"><?= count($absensi) ?></div>
        <div class="stat-label">Total Absensi</div>
      </div>
      
      <div class="stat-card">
        <div class="stat-number"><?= count(array_unique(array_column($absensi, 'siswa_id'))) ?></div>
        <div class="stat-label">Siswa Aktif</div>
      </div>
      
      <div class="stat-card">
        <div class="stat-number"><?= count(array_filter($absensi, fn($a) => !empty($a['selfie_path']))) ?></div>
        <div class="stat-label">Dengan Selfie</div>
      </div>
    </div>
    
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
    
    <div class="alert alert-info" style="margin-top: 20px;">
      <h4>💡 Informasi Dashboard</h4>
      <ul>
        <li><strong>Total Absensi:</strong> Jumlah semua absensi dari siswa yang Anda bimbing</li>
        <li><strong>Siswa Aktif:</strong> Jumlah siswa yang sudah melakukan absensi</li>
        <li><strong>Dengan Selfie:</strong> Jumlah absensi yang dilengkapi foto selfie</li>
        <li><strong>Status:</strong> Indikator apakah siswa absen dalam radius lokasi PKL (≤150m)</li>
      </ul>
    </div>
  <?php endif; ?>
</div>

<div class="card">
  <h3>🔍 Filter & Pencarian</h3>
  
  <div class="form-group">
    <label for="searchSiswa">Cari Siswa:</label>
    <input type="text" id="searchSiswa" placeholder="Ketik nama atau NIS siswa..." class="form-control">
  </div>
  
  <div class="form-group">
    <label for="filterStatus">Filter Status:</label>
    <select id="filterStatus" class="form-control">
      <option value="">Semua Status</option>
      <option value="dalam">Dalam Radius</option>
      <option value="luar">Luar Radius</option>
    </select>
  </div>
  
  <div class="form-group">
    <label for="filterDate">Filter Tanggal:</label>
    <input type="date" id="filterDate" class="form-control">
  </div>
  
  <button type="button" class="btn btn-primary" onclick="filterData()">
    🔍 Terapkan Filter
  </button>
  
  <button type="button" class="btn btn-secondary" onclick="resetFilter()">
    🔄 Reset Filter
  </button>
</div>

<script>
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