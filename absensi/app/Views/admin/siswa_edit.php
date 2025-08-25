<h2>✏️ Edit Data Siswa</h2>

<?php if(isset($error)): ?>
    <div class="alert alert-danger">
        <strong>❌ Error:</strong> <?= htmlspecialchars($error) ?>
    </div>
<?php endif; ?>

<div class="card">
    <h3>📝 Form Edit Siswa</h3>
    
    <form method="post" action="?r=admin/siswaEdit&id=<?= $siswa['id'] ?>" class="form">
        <div class="form-row">
            <div class="form-group">
                <label for="nis">NIS</label>
                <input type="text" id="nis" class="form-control" 
                       value="<?= htmlspecialchars($siswa['nis']) ?>" 
                       readonly disabled>
                <small class="form-text">NIS tidak dapat diubah</small>
            </div>
            
            <div class="form-group">
                <label for="nama">Nama Lengkap <span class="required">*</span></label>
                <input type="text" id="nama" name="nama" class="form-control" 
                       value="<?= htmlspecialchars($oldData['nama'] ?? $siswa['nama']) ?>" 
                       placeholder="Masukkan nama lengkap siswa" required>
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="kelas">Kelas <span class="required">*</span></label>
                <input type="text" id="kelas" name="kelas" class="form-control" 
                       value="<?= htmlspecialchars($oldData['kelas'] ?? $siswa['kelas']) ?>" 
                       placeholder="Contoh: XII RPL 1" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password Baru</label>
                <input type="text" id="password" name="password" class="form-control" 
                       value="<?= htmlspecialchars($oldData['password'] ?? '') ?>" 
                       placeholder="Kosongkan jika tidak ingin mengubah password">
                <small class="form-text">Kosongkan jika tidak ingin mengubah password</small>
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="pembimbing_id">Pembimbing</label>
                <select id="pembimbing_id" name="pembimbing_id" class="form-control">
                    <option value="">-- Pilih Pembimbing --</option>
                    <?php foreach($pembimbing as $p): ?>
                        <option value="<?= $p['id'] ?>" 
                                <?= ($oldData['pembimbing_id'] ?? $siswa['pembimbing_id']) == $p['id'] ? 'selected' : '' ?>>
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
                                <?= ($oldData['tempat_pkl_id'] ?? $siswa['tempat_pkl_id']) == $t['id'] ? 'selected' : '' ?>>
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
                💾 Update Data
            </button>
            
            <a href="?r=admin/siswa" class="btn btn-secondary">
                ❌ Batal
            </a>
        </div>
    </form>
</div>

<div class="card">
    <h3>📋 Informasi Siswa</h3>
    
    <div class="info-grid">
        <div class="info-item">
            <strong>🆔 ID:</strong> <?= $siswa['id'] ?>
        </div>
        
        <div class="info-item">
            <strong>📚 NIS:</strong> <?= htmlspecialchars($siswa['nis']) ?>
        </div>
        
        <div class="info-item">
            <strong>👤 Nama:</strong> <?= htmlspecialchars($siswa['nama']) ?>
        </div>
        
        <div class="info-item">
            <strong>🏫 Kelas:</strong> <?= htmlspecialchars($siswa['kelas']) ?>
        </div>
        
        <div class="info-item">
            <strong>👨‍🏫 Pembimbing:</strong> 
            <?= htmlspecialchars($siswa['pembimbing'] ?? 'Belum ditentukan') ?>
        </div>
        
        <div class="info-item">
            <strong>📍 Tempat PKL:</strong> 
            <?= htmlspecialchars($siswa['tempat_pkl'] ?? 'Belum ditentukan') ?>
        </div>
        
        <div class="info-item">
            <strong>🔑 User ID:</strong> <?= $siswa['user_id'] ?? 'Tidak ada' ?>
        </div>
    </div>
</div>

<div class="card">
    <h3>💡 Informasi Edit</h3>
    
    <div class="info-list">
        <div class="info-item">
            <strong>🔒 Password:</strong> 
            <ul>
                <li>Jika field password dikosongkan, password lama akan tetap digunakan</li>
                <li>Jika diisi, password akan diubah ke password baru</li>
                <li>Password minimal 6 karakter</li>
            </ul>
        </div>
        
        <div class="info-item">
            <strong>⚠️ Catatan:</strong>
            <ul>
                <li>NIS tidak dapat diubah untuk menjaga konsistensi data</li>
                <li>Perubahan akan langsung tersimpan ke database</li>
                <li>Semua perubahan akan dicatat dalam audit log</li>
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

.form-control[readonly] {
    background-color: #f8f9fa;
    color: #6c757d;
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

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 15px;
}

.info-item {
    background: #f8f9fa;
    padding: 12px;
    border-radius: 6px;
    border-left: 3px solid #28a745;
}

.info-list {
    display: grid;
    gap: 20px;
}

.info-list .info-item {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    border-left: 4px solid #007bff;
}

.info-list ul {
    margin: 10px 0 0 20px;
    padding: 0;
}

.info-list li {
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
    
    .info-grid {
        grid-template-columns: 1fr;
    }
}
</style>
