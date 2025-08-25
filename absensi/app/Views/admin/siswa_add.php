<h2>➕ Tambah Siswa Baru</h2>

<?php if(isset($error)): ?>
    <div class="alert alert-danger">
        <strong>❌ Error:</strong> <?= htmlspecialchars($error) ?>
    </div>
<?php endif; ?>

<div class="card">
    <h3>📝 Form Data Siswa</h3>
    
    <form method="post" action="?r=admin/siswaAdd" class="form">
        <div class="form-row">
            <div class="form-group">
                <label for="nis">NIS <span class="required">*</span></label>
                <input type="text" id="nis" name="nis" class="form-control" 
                       value="<?= htmlspecialchars($oldData['nis'] ?? '') ?>" 
                       placeholder="Masukkan NIS siswa" required>
                <small class="form-text">NIS akan digunakan sebagai username login</small>
            </div>
            
            <div class="form-group">
                <label for="nama">Nama Lengkap <span class="required">*</span></label>
                <input type="text" id="nama" name="nama" class="form-control" 
                       value="<?= htmlspecialchars($oldData['nama'] ?? '') ?>" 
                       placeholder="Masukkan nama lengkap siswa" required>
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="kelas">Kelas <span class="required">*</span></label>
                <input type="text" id="kelas" name="kelas" class="form-control" 
                       value="<?= htmlspecialchars($oldData['kelas'] ?? '') ?>" 
                       placeholder="Contoh: XII RPL 1" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password <span class="required">*</span></label>
                <input type="text" id="password" name="password" class="form-control" 
                       value="<?= htmlspecialchars($oldData['password'] ?? '123456') ?>" 
                       placeholder="Password untuk login" required>
                <small class="form-text">Password default: 123456</small>
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="pembimbing_id">Pembimbing</label>
                <select id="pembimbing_id" name="pembimbing_id" class="form-control">
                    <option value="">-- Pilih Pembimbing --</option>
                    <?php foreach($pembimbing as $p): ?>
                        <option value="<?= $p['id'] ?>" 
                                <?= ($oldData['pembimbing_id'] ?? '') == $p['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($p['nama']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <small class="form-text">Pembimbing yang akan membimbing siswa ini</small>
            </div>
            
            <div class="form-group">
                <label for="tempat_pkl_id">Tempat PKL</label>
                <select id="tempat_pkl_id" name="tempat_pkl_id" class="form-control">
                    <option value="">-- Pilih Tempat PKL --</option>
                    <?php foreach($tempatPKL as $t): ?>
                        <option value="<?= $t['id'] ?>" 
                                <?= ($oldData['tempat_pkl_id'] ?? '') == $t['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($t['nama']) ?> 
                            (<?= htmlspecialchars($t['alamat'] ?? '-') ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <small class="form-text">Lokasi tempat PKL siswa</small>
            </div>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                💾 Simpan Siswa
            </button>
            
            <a href="?r=admin/siswa" class="btn btn-secondary">
                ❌ Batal
            </a>
        </div>
    </form>
</div>

<div class="card">
    <h3>💡 Informasi</h3>
    
    <div class="info-list">
        <div class="info-item">
            <strong>🔑 User Account:</strong> Sistem akan otomatis membuat user account dengan:
            <ul>
                <li>Username: NIS siswa</li>
                <li>Password: Sesuai yang diinput (default: 123456)</li>
                <li>Role: siswa</li>
            </ul>
        </div>
        
        <div class="info-item">
            <strong>📱 Login:</strong> Siswa dapat login menggunakan:
            <ul>
                <li>Username: NIS mereka</li>
                <li>Password: Password yang diinput</li>
            </ul>
        </div>
        
        <div class="info-item">
            <strong>⚠️ Catatan:</strong>
            <ul>
                <li>NIS harus unik dan tidak boleh duplikat</li>
                <li>Password minimal 6 karakter</li>
                <li>Pembimbing dan Tempat PKL bersifat opsional</li>
            </ul>
        </div>
    </div>
</div>

<style>
.form-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
    margin-bottom: 20px;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
    color: #333;
}

.form-control {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
}

.form-control:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 0 2px rgba(0,123,255,0.25);
}

.required {
    color: #dc3545;
}

.form-text {
    font-size: 12px;
    color: #6c757d;
    margin-top: 5px;
}

.form-actions {
    margin-top: 30px;
    padding-top: 20px;
    border-top: 1px solid #eee;
    text-align: center;
}

.form-actions .btn {
    margin: 0 10px;
}

.info-list {
    display: grid;
    gap: 20px;
}

.info-item {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    border-left: 4px solid #007bff;
}

.info-item ul {
    margin: 10px 0 0 20px;
    padding: 0;
}

.info-item li {
    margin-bottom: 5px;
}

@media (max-width: 768px) {
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .form-actions .btn {
        display: block;
        width: 100%;
        margin: 10px 0;
    }
}
</style>
