<h2>📊 Laporan Absensi</h2>

<div class="card">
  <h3>🔍 Filter Laporan</h3>
  <form method="get" action="?r=admin/report">
    <div class="grid">
      <label>Mulai <input type="date" name="start" value="<?= htmlspecialchars($filters['start'] ?? '') ?>"></label>
      <label>Selesai <input type="date" name="end" value="<?= htmlspecialchars($filters['end'] ?? '') ?>"></label>
      <label>Kelas <input name="kelas" placeholder="mis. XII TKJ 1" value="<?= htmlspecialchars($filters['kelas'] ?? '') ?>"></label>
      <label>Tempat PKL (ID) <input type="number" name="tempat" value="<?= htmlspecialchars($filters['tempat'] ?? '') ?>"></label>
    </div>
    <p>
      <button type="submit" class="btn btn-primary">🔍 Terapkan Filter</button>
      <a class="btn btn-success" href="?r=admin/reportCsv&start=<?=urlencode($filters['start'] ?? '')?>&end=<?=urlencode($filters['end'] ?? '')?>&kelas=<?=urlencode($filters['kelas'] ?? '')?>&tempat=<?=urlencode($filters['tempat'] ?? '')?>">📊 Export Excel (XLS)</a>
      <a class="btn btn-info" target="_blank" href="?r=admin/reportPrint&start=<?=urlencode($filters['start'] ?? '')?>&end=<?=urlencode($filters['end'] ?? '')?>&kelas=<?=urlencode($filters['kelas'] ?? '')?>&tempat=<?=urlencode($filters['tempat'] ?? '')?>">🖨️ Cetak PDF</a>
    </p>
  </form>
</div>

<div class="card">
  <h3>📋 Data Absensi</h3>
  
  <?php if(empty($rows)): ?>
    <div class="alert alert-info">
      <strong>ℹ️ Info:</strong> Tidak ada data absensi untuk periode yang dipilih.
    </div>
  <?php else: ?>
    <div class="table-responsive">
      <table class="table">
        <thead>
          <tr>
            <th>No</th>
            <th>Siswa</th>
            <th>Kelas</th>
            <th>Tempat PKL</th>
            <th>Waktu</th>
            <th>Jenis</th>
            <th>Lokasi</th>
            <th>Jarak (m)</th>
          </tr>
        </thead>
        <tbody>
          <?php 
          $no = 1;
          foreach($rows as $r): 
          ?>
            <tr>
              <td><?= $no++ ?></td>
              <td><strong><?= htmlspecialchars($r['siswa']) ?></strong></td>
              <td><?= htmlspecialchars($r['kelas']) ?></td>
              <td><?= htmlspecialchars($r['tempat']) ?></td>
              <td>
                <small>
                  <?= date('d/m/Y', strtotime($r['waktu'])) ?><br>
                  <?= date('H:i', strtotime($r['waktu'])) ?>
                </small>
              </td>
              <td>
                <?php if($r['jenis_absen'] === 'datang'): ?>
                  <span class="badge badge-primary">🚀 Datang</span>
                <?php elseif($r['jenis_absen'] === 'pulang'): ?>
                  <span class="badge badge-warning">🏠 Pulang</span>
                <?php else: ?>
                  <span class="badge badge-secondary">❓ Tidak Diketahui</span>
                <?php endif; ?>
              </td>
              <td>
                <small>
                  <?= number_format($r['lat'], 6) ?>, <?= number_format($r['lng'], 6) ?>
                </small>
              </td>
              <td><?= number_format($r['jarak_m'], 1) ?>m</td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    
    <div class="summary">
      <p><strong>Total Data:</strong> <?= count($rows) ?> absensi</p>
    </div>
  <?php endif; ?>
</div>

<style>
.grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 15px;
  margin-bottom: 20px;
}

.grid label {
  display: flex;
  flex-direction: column;
  gap: 5px;
}

.grid input {
  padding: 8px;
  border: 1px solid #ddd;
  border-radius: 4px;
}

.btn {
  display: inline-block;
  padding: 10px 20px;
  margin: 5px;
  text-decoration: none;
  border-radius: 4px;
  border: none;
  cursor: pointer;
  font-size: 14px;
}

.btn-primary {
  background-color: #007bff;
  color: white;
}

.btn-success {
  background-color: #28a745;
  color: white;
}

.btn-info {
  background-color: #17a2b8;
  color: white;
}

.table-responsive {
  overflow-x: auto;
}

.table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 15px;
}

.table th,
.table td {
  padding: 10px;
  text-align: left;
  border-bottom: 1px solid #ddd;
}

.table th {
  background-color: #f8f9fa;
  font-weight: bold;
}

.badge {
  padding: 4px 8px;
  border-radius: 12px;
  font-size: 0.8rem;
  font-weight: bold;
}

.badge-primary {
  background-color: #007bff;
  color: white;
}

.badge-warning {
  background-color: #ffc107;
  color: #212529;
}

.badge-secondary {
  background-color: #6c757d;
  color: white;
}

.alert {
  padding: 15px;
  margin: 15px 0;
  border-radius: 6px;
  border: 1px solid transparent;
}

.alert-info {
  color: #0c5460;
  background-color: #d1ecf1;
  border-color: #bee5eb;
}

.summary {
  margin-top: 20px;
  padding: 15px;
  background-color: #f8f9fa;
  border-radius: 6px;
}

.summary p {
  margin: 0;
  font-weight: bold;
  color: #495057;
}
</style>