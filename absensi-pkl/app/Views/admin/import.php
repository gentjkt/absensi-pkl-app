<h2>Import Data Siswa</h2>

<div class="grid">
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
        <label for="pembimbing_id">👨‍🏫 Pembimbing <span class="text-danger">*</span></label>
        <select name="pembimbing_id" id="pembimbing_id" required class="form-control">
          <option value="">- Pilih Pembimbing -</option>
          <?php foreach($pembimbing as $p): ?>
            <option value="<?= (int)$p['id'] ?>"><?= htmlspecialchars($p['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      
      <div class="form-group">
        <label for="tempat_pkl_id">🏢 Tempat PKL <span class="text-danger">*</span></label>
        <select name="tempat_pkl_id" id="tempat_pkl_id" required class="form-control">
          <option value="">- Pilih Tempat PKL -</option>
          <?php foreach($tempatPKL as $t): ?>
            <option value="<?= (int)$t['id'] ?>"><?= htmlspecialchars($t['nama']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      
      <div class="form-group">
        <label for="default_password">🔒 Password Default <span class="text-danger">*</span></label>
        <input 
          type="text" 
          name="default_password" 
          id="default_password" 
          value="123456" 
          required 
          class="form-control"
          placeholder="Password default untuk semua siswa"
        >
        <small class="text-muted">
          Password ini akan digunakan untuk semua siswa yang diimport. 
          Siswa dapat mengubah password setelah login pertama kali.
        </small>
      </div>
      
      <div class="form-group">
        <label for="csv_file">📁 File CSV <span class="text-danger">*</span></label>
        <input type="file" name="csv_file" id="csv_file" accept=".csv" required class="form-control">
        <small class="text-muted">Format: NIS, Nama, Kelas (tanpa header)</small>
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
    
      
      <p><strong>Contoh isi file:</strong></p>
      <pre style="background: #f8f9fa; padding: 15px; border-radius: 5px; font-family: monospace;">
12345,John Doe,XII IPA 1
12346,Jane Smith,XII IPA 2
12347,Bob Johnson,XII IPS 1</pre>
      
      <p><strong>Catatan:</strong></p>
      <ul>
        <li>File harus berformat CSV</li>
        <li>Gunakan koma (,) sebagai pemisah</li>
        <li>Jangan gunakan header/baris judul</li>
        <li>Semua siswa akan diassign ke pembimbing dan tempat PKL yang dipilih</li>
        <li>NIS akan menjadi username untuk login</li>
        <li>Password default akan sama untuk semua siswa</li>
        <li>NIS harus unik (tidak boleh duplikat)</li>
      </ul>
    </div>
  </div>
  
  <div class="card">
    <h3>💡 Tips Import</h3>
    
    <div class="info">
      <h4>✅ Yang Harus Diperhatikan:</h4>
      <ul>
        <li><strong>Pembimbing:</strong> Pilih pembimbing yang akan mengawasi siswa</li>
        <li><strong>Tempat PKL:</strong> Pilih lokasi tempat siswa akan PKL</li>
        <li><strong>Password Default:</strong> Password yang akan digunakan semua siswa</li>
        <li><strong>Format Data:</strong> Pastikan format CSV sesuai standar</li>
        <li><strong>Validasi:</strong> Sistem akan memvalidasi setiap baris data</li>
      </ul>
     
    </div>
  </div>
</div>

<div class="card">
  <h3>📊 Template Download</h3>
  
  <p>Download template CSV untuk memudahkan pembuatan file import:</p>
  
  <a href="data:text/csv;charset=utf-8,NIS,Nama,Kelas%0A12345,John Doe,XII IPA 1%0A12346,Jane Smith,XII IPA 2%0A12347,Bob Johnson,XII IPS 1" 
     download="template_siswa.csv" 
     class="btn btn-success">
    📥 Download Template CSV
  </a>
  
  <p class="text-muted">
    <small>
      Template ini berisi contoh format data yang benar. 
      Anda bisa mengedit file ini sesuai kebutuhan dan mengupload kembali.
    </small>
  </p>
</div>