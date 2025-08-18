<h2>📊 Laporan Absensi Bulanan</h2>

<div class="card">
  <h3>🔍 Filter Laporan</h3>
  
  <form method="get" action="?r=pembimbing/report" class="filter-form">
    <div class="form-row">
      <div class="form-group">
        <label for="month">Bulan:</label>
        <select name="month" id="month" class="form-control">
          <?php foreach($bulanList as $key => $nama): ?>
            <option value="<?= $key ?>" <?= $key == $bulan ? 'selected' : '' ?>>
              <?= $nama ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      
      <div class="form-group">
        <label for="year">Tahun:</label>
        <select name="year" id="year" class="form-control">
          <?php foreach($tahunList as $tahunOption): ?>
            <option value="<?= $tahunOption ?>" <?= $tahunOption == $tahun ? 'selected' : '' ?>>
              <?= $tahunOption ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      
      <div class="form-group">
        <label>&nbsp;</label>
        <button type="submit" class="btn btn-primary">
          🔍 Tampilkan Laporan
        </button>
      </div>
    </div>
  </form>
  
  <div class="info" style="margin-top: 15px;">
    <p><strong>Periode:</strong> <?= $bulanList[$bulan] ?> <?= $tahun ?></p>
    <p><strong>Total Hari Kerja:</strong> <?= $totalHariKerja ?> hari (Senin-Jumat)</p>
    <p><strong>Keterangan:</strong> Hadir = Absen dengan GPS, Ijin/Sakit = Input manual, Alpa = Tidak hadir</p>
  </div>
</div>

<div class="card">
  <h3>📈 Statistik Absensi Siswa</h3>
  
  <?php if(empty($statistikSiswa)): ?>
    <div class="alert alert-info">
      <strong>ℹ️ Info:</strong> Tidak ada siswa yang dibimbing untuk periode ini.
    </div>
  <?php else: ?>
    <div class="table-responsive">
      <table class="table">
        <thead>
          <tr>
            <th>No</th>
            <th>NIS</th>
            <th>Nama Siswa</th>
            <th>Kelas</th>
            <th>Tempat PKL</th>
            <th>Hadir</th>
            <th>Ijin</th>
            <th>Sakit</th>
            <th>Alpa</th>
            <th>Total Hari</th>
            <th>Persentase</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
        <?php 
        $no = 1;
        $totalHadir = 0;
        $totalIjin = 0;
        $totalSakit = 0;
        $totalAlpa = 0;
        
        foreach($statistikSiswa as $stat): 
          $hadir = $stat['hadir'];
          $ijin = $stat['ijin'];
          $sakit = $stat['sakit'];
          $alpa = $stat['alpa'];
          $totalHari = $stat['total_hari'];
          
          $totalHadir += $hadir;
          $totalIjin += $ijin;
          $totalSakit += $sakit;
          $totalAlpa += $alpa;
          
          $persentase = $totalHari > 0 ? round((($hadir + $ijin + $sakit) / $totalHari) * 100, 1) : 0;
        ?>
          <tr>
            <td><?= $no++ ?></td>
            <td><strong><?= htmlspecialchars($stat['siswa']['nis']) ?></strong></td>
            <td><strong><?= htmlspecialchars($stat['siswa']['nama']) ?></strong></td>
            <td><?= htmlspecialchars($stat['siswa']['kelas']) ?></td>
            <td><?= htmlspecialchars($stat['siswa']['tempat_pkl'] ?? '-') ?></td>
            <td>
              <span class="badge badge-success"><?= $hadir ?></span>
            </td>
            <td>
              <span class="badge badge-info"><?= $ijin ?></span>
            </td>
            <td>
              <span class="badge badge-warning"><?= $sakit ?></span>
            </td>
            <td>
              <span class="badge badge-danger"><?= $alpa ?></span>
            </td>
            <td><strong><?= $totalHari ?></strong></td>
            <td>
              <span class="persentase <?= $persentase >= 80 ? 'success' : ($persentase >= 60 ? 'warning' : 'danger') ?>">
                <?= $persentase ?>%
              </span>
            </td>
            <td>
              <?php if($persentase >= 80): ?>
                <span class="badge badge-success">✅ Baik</span>
              <?php elseif($persentase >= 60): ?>
                <span class="badge badge-warning">⚠️ Cukup</span>
              <?php else: ?>
                <span class="badge badge-danger">❌ Kurang</span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
        <tfoot>
          <tr class="table-footer">
            <td colspan="5"><strong>TOTAL</strong></td>
            <td><strong><?= $totalHadir ?></strong></td>
            <td><strong><?= $totalIjin ?></strong></td>
            <td><strong><?= $totalSakit ?></strong></td>
            <td><strong><?= $totalAlpa ?></strong></td>
            <td><strong><?= $totalHariKerja * count($statistikSiswa) ?></strong></td>
            <td colspan="2"></td>
          </tr>
        </tfoot>
      </table>
    </div>
    
    <!-- Summary Cards -->
    <div class="stats-grid" style="margin-top: 20px;">
      <div class="stat-card success">
        <div class="stat-number"><?= $totalHadir ?></div>
        <div class="stat-label">Total Hadir</div>
      </div>
      
      <div class="stat-card info">
        <div class="stat-number"><?= $totalIjin ?></div>
        <div class="stat-label">Total Ijin</div>
      </div>
      
      <div class="stat-card warning">
        <div class="stat-number"><?= $totalSakit ?></div>
        <div class="stat-label">Total Sakit</div>
      </div>
      
      <div class="stat-card danger">
        <div class="stat-number"><?= $totalAlpa ?></div>
        <div class="stat-label">Total Alpa</div>
      </div>
    </div>
  <?php endif; ?>
</div>

<div class="card">
  <h3>📋 Export & Print</h3>
  
  <div class="action-buttons">
    <a href="?r=pembimbing/report&month=<?= $bulan ?>&year=<?= $tahun ?>&export=pdf" class="btn btn-primary">
      📄 Export PDF
    </a>
    
    <a href="?r=pembimbing/report&month=<?= $bulan ?>&year=<?= $tahun ?>&export=excel" class="btn btn-success">
      📊 Export Excel
    </a>
    
    <button onclick="window.print()" class="btn btn-secondary">
      🖨️ Print Laporan
    </button>
    
    <a href="?r=pembimbing/dashboard" class="btn btn-outline-primary">
      ↩️ Kembali ke Dashboard
    </a>
  </div>
</div>

<style>
.filter-form {
  background: #f8f9fa;
  padding: 20px;
  border-radius: 8px;
  border: 1px solid #dee2e6;
}

.form-row {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 15px;
  align-items: end;
}

.persentase {
  font-weight: bold;
  padding: 4px 8px;
  border-radius: 4px;
}

.persentase.success {
  background-color: #d4edda;
  color: #155724;
}

.persentase.warning {
  background-color: #fff3cd;
  color: #856404;
}

.persentase.danger {
  background-color: #f8d7da;
  color: #721c24;
}

.table-footer {
  background-color: #f8f9fa;
  font-weight: bold;
}

.table-footer td {
  border-top: 2px solid #dee2e6;
}

.badge {
  padding: 5px 10px;
  border-radius: 20px;
  font-size: 0.9rem;
  font-weight: bold;
}

.badge-success {
  background-color: #28a745;
  color: white;
}

.badge-info {
  background-color: #17a2b8;
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

@media print {
  .filter-form, .action-buttons, .card:last-child {
    display: none;
  }
  
  .card {
    border: none;
    box-shadow: none;
  }
  
  table {
    font-size: 12px;
  }
}
</style>
