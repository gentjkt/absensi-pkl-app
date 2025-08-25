<h2>Import Data Siswa</h2>

<div class="card">
  <div class="card">
    <h3>📤 Upload File CSV</h3>
    
    <?php if(!empty($error)): ?>
      <div class="alert alert-danger">
        <strong>❌ Error:</strong> <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>
    
    <form method="post" action="?r=admin/importProcess" enctype="multipart/form-data">
      <?= \App\Helpers\CSRF::field() ?>

      <div class="form-group">
        <label for="csv_file">📁 File CSV <span class="text-danger">*</span></label>
        <input type="file" name="csv_file" id="csv_file" accept=".csv" required class="form-control">
        <small class="text-muted">Format: username, password, NIS, Nama, kelas, pembimbing, tempat PKL (tanpa header)</small>
      </div>
      
      <button type="submit" class="btn btn-primary">
        🚀 Upload & Import
      </button>
      
      <a href="?r=admin/siswa" class="btn btn-secondary">
        ↩️ Kembali ke Data Siswa
      </a>
    </form>
  </div>
  
  <div class="card">
    <h3>📋 Format File CSV</h3>
    
    <div class="info">
      <p><strong>Struktur file CSV:</strong></p>
      <p>username, password, NIS, Nama, kelas, pembimbing, tempat PKL</p>
      
      <p><strong>Contoh isi file:</strong></p>
      <pre style="background: #f8f9fa; padding: 15px; border-radius: 5px; font-family: monospace;">
john_doe,123456,12345,John Doe,XII IPA 1,John Smith,PT ABC
jane_smith,123456,12346,Jane Smith,XII IPA 2,John Smith,PT ABC
bob_johnson,123456,12347,Bob Johnson,XII IPS 1,Jane Doe,PT XYZ</pre>
      
      <p><strong>Catatan:</strong></p>
      <ul>
        <li>File harus berformat CSV</li>
        <li>Gunakan koma (,) sebagai pemisah</li>
        <li>Jangan gunakan header/baris judul</li>
        <li>Setiap siswa dapat memiliki pembimbing dan tempat PKL yang berbeda</li>
        <li>Username dan password dapat berbeda untuk setiap siswa</li>
        <li>NIS harus unik (tidak boleh duplikat)</li>
        <li>Username harus unik (tidak boleh duplikat)</li>
      </ul>
    </div>
  </div>
  
  <div class="card">
    <h3>💡 Tips Import</h3>
    
    <div class="info">
      <h4>✅ Yang Harus Diperhatikan:</h4>
      <ul>
        <li><strong>Format CSV:</strong> Pastikan format sesuai: username, password, NIS, Nama, kelas, pembimbing, tempat PKL</li>
        <li><strong>Data Individual:</strong> Setiap siswa dapat memiliki pembimbing dan tempat PKL yang berbeda</li>
        <li><strong>Username & Password:</strong> Dapat berbeda untuk setiap siswa sesuai data CSV</li>
        <li><strong>Validasi:</strong> Sistem akan memvalidasi setiap baris data</li>
        <li><strong>Unik:</strong> NIS dan username harus unik untuk setiap siswa</li>
      </ul>
     
    </div>
  </div>
</div>

<div class="card">
  <h3>📊 Template Download</h3>
  
  <p>Download template CSV untuk memudahkan pembuatan file import:</p>
  
  <a href="data:text/csv;charset=utf-8,username,password,NIS,Nama,kelas,pembimbing,tempat_pkl%0Ajohn_doe,123456,12345,John Doe,XII IPA 1,John Smith,PT ABC%0Ajane_smith,123456,12346,Jane Smith,XII IPA 2,John Smith,PT ABC%0Abob_johnson,123456,12347,Bob Johnson,XII IPS 1,Jane Doe,PT XYZ" 
     download="template_siswa.csv" 
     class="btn btn-success">
    📥 Download Template CSV
  </a>
  
  <p class="text-muted">
    <small>
      Template ini berisi contoh format data yang benar dengan 7 kolom: username, password, NIS, Nama, kelas, pembimbing, tempat PKL. 
      Anda bisa mengedit file ini sesuai kebutuhan dan mengupload kembali.
    </small>
  </p>
</div>