<h2>📊 Hasil Import Data Siswa</h2>

<div class="card">
  <h3>📈 Ringkasan Import</h3>
  
  <div class="stats-grid">
    <div class="stat-card">
      <div class="stat-number"><?= $total_rows ?></div>
      <div class="stat-label">Total Baris</div>
    </div>
    
    <div class="stat-card success">
      <div class="stat-number"><?= $success_count ?></div>
      <div class="stat-label">Berhasil</div>
    </div>
    
    <div class="stat-card <?= $error_count > 0 ? 'danger' : 'success' ?>">
      <div class="stat-number"><?= $error_count ?></div>
      <div class="stat-label">Gagal</div>
    </div>
    
    <div class="stat-card">
      <div class="stat-number"><?= $total_rows > 0 ? round(($success_count / $total_rows) * 100, 1) : 0 ?>%</div>
      <div class="stat-label">Success Rate</div>
    </div>
  </div>
  
  <?php if($success_count > 0): ?>
    <div class="alert alert-info" style="margin-top: 20px;">
      <h4>🔑 Informasi Login Siswa</h4>
      <p><strong>Password Default:</strong> <code><?= htmlspecialchars($default_password) ?></code></p>
      <p><strong>Username:</strong> NIS siswa (contoh: 12345)</p>
      <p><strong>Role:</strong> Siswa</p>
      <p class="text-muted">
        <small>
          Siswa dapat login dengan NIS sebagai username dan password default. 
          Setelah login pertama kali, siswa disarankan untuk mengubah password.
        </small>
      </p>
    </div>
  <?php endif; ?>
</div>

<div class="card">
  <h3>📋 Detail Hasil Import</h3>
  
  <?php if(empty($results)): ?>
    <div class="alert alert-info">
      <strong>ℹ️ Info:</strong> Tidak ada data yang diproses.
    </div>
  <?php else: ?>
    <div class="table-responsive">
      <table class="table">
        <thead>
          <tr>
            <th>Baris</th>
            <th>Status</th>
            <th>Pesan</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($results as $result): ?>
            <tr class="<?= $result['status'] === 'success' ? 'success' : 'error' ?>">
              <td>
                <strong>Baris <?= $result['row'] ?></strong>
              </td>
              <td>
                <?php if($result['status'] === 'success'): ?>
                  <span class="badge badge-success">✅ Berhasil</span>
                <?php else: ?>
                  <span class="badge badge-danger">❌ Gagal</span>
                <?php endif; ?>
              </td>
              <td>
                <?= htmlspecialchars($result['message']) ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<div class="card">
  <h3>🔄 Aksi Selanjutnya</h3>
  
  <div class="action-buttons">
    <a href="?r=admin/import" class="btn btn-primary">
      📤 Import Lagi
    </a>
    
    <a href="?r=admin/siswa" class="btn btn-success">
      👥 Lihat Data Siswa
    </a>
    
    <a href="?r=admin/dashboard" class="btn btn-secondary">
      🏠 Dashboard
    </a>
  </div>
  
  <?php if($error_count > 0): ?>
    <div class="alert alert-warning" style="margin-top: 20px;">
      <h4>⚠️ Ada <?= $error_count ?> baris yang gagal diimport</h4>
      <p><strong>Solusi:</strong></p>
      <ul>
        <li>Periksa format data di baris yang gagal</li>
        <li>Pastikan NIS tidak duplikat dengan data yang sudah ada</li>
        <li>Periksa apakah ada karakter khusus yang tidak valid</li>
        <li>Download template CSV dan ikuti format yang benar</li>
        <li>Pastikan NIS tidak sama dengan username user lain</li>
      </ul>
    </div>
  <?php endif; ?>
  
  <?php if($success_count > 0): ?>
    <div class="alert alert-success" style="margin-top: 20px;">
      <h4>🎉 <?= $success_count ?> siswa berhasil diimport!</h4>
      <p>Data siswa dan akun user sudah tersedia di sistem dan siap digunakan untuk:</p>
      <ul>
        <li>🔐 Login siswa dengan NIS dan password default</li>
        <li>📱 Absensi dengan GPS dan selfie</li>
        <li>📊 Monitoring oleh pembimbing</li>
        <li>📈 Laporan dan statistik</li>
        <li>🔍 Pencarian dan filter data</li>
      </ul>
      
      <div class="info" style="margin-top: 15px;">
        <h5>📋 Daftar Akun Siswa yang Berhasil Dibuat:</h5>
        <p><strong>Format Login:</strong> <code>Username: NIS, Password: <?= htmlspecialchars($default_password) ?></code></p>
        <p><strong>Contoh:</strong> Jika ada siswa dengan NIS 12345, maka:</p>
        <ul>
          <li>Username: <code>12345</code></li>
          <li>Password: <code><?= htmlspecialchars($default_password) ?></code></li>
          <li>Role: Siswa</li>
        </ul>
      </div>
    </div>
  <?php endif; ?>
</div>

<style>
.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
  gap: 20px;
  margin: 20px 0;
}

.stat-card {
  background: white;
  padding: 20px;
  border-radius: 10px;
  text-align: center;
  border: 2px solid #e9ecef;
  transition: all 0.3s ease;
}

.stat-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.stat-card.success {
  border-color: #28a745;
  background-color: #f8fff9;
}

.stat-card.danger {
  border-color: #dc3545;
  background-color: #fff8f8;
}

.stat-card.warning {
  border-color: #ffc107;
  background-color: #fffbf0;
}

.stat-number {
  font-size: 2.5rem;
  font-weight: bold;
  color: #2c3e50;
  margin-bottom: 10px;
}

.stat-label {
  color: #6c757d;
  font-size: 0.9rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.badge {
  padding: 5px 10px;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: bold;
}

.badge-success {
  background-color: #28a745;
  color: white;
}

.badge-danger {
  background-color: #dc3545;
  color: white;
}

.badge-warning {
  background-color: #ffc107;
  color: #212529;
}

.badge-info {
  background-color: #17a2b8;
  color: white;
}

.action-buttons {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 15px;
  margin: 20px 0;
}

.table-responsive {
  overflow-x: auto;
}

tr.success {
  background-color: #f8fff9;
}

tr.error {
  background-color: #fff8f8;
}

code {
  background-color: #f8f9fa;
  padding: 2px 6px;
  border-radius: 4px;
  font-family: 'Courier New', monospace;
  color: #e83e8c;
}
</style>