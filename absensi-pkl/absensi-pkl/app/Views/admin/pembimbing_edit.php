<h2>✏️ Edit Data Pembimbing</h2>

<div class="action-buttons" style="margin-bottom: 20px;">
    <a href="?r=admin/pembimbing" class="btn btn-secondary">
        ↩️ Kembali ke Daftar Pembimbing
    </a>
</div>

<div class="card">
    <h3>✏️ Form Edit Pembimbing</h3>
    
    <?php if(isset($error)): ?>
        <div class="alert alert-danger">
            <strong>❌ Error:</strong> <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>
    
    <form method="post" action="?r=admin/pembimbingEdit&id=<?= $pembimbing['id'] ?>">
        <?= \App\Helpers\CSRF::field() ?>
        
        <div class="form-group">
            <label for="nama">Nama Pembimbing:</label>
            <input type="text" id="nama" name="nama" required 
                   value="<?= htmlspecialchars($pembimbing['nama']) ?>"
                   placeholder="Contoh: Dr. Ahmad Suryanto, M.Pd" class="form-control">
            <small class="form-text">Masukkan nama lengkap pembimbing</small>
        </div>
        
        <div class="form-group">
            <label for="nip">NIP:</label>
            <input type="text" id="nip" name="nip" required 
                   value="<?= htmlspecialchars($pembimbing['nip']) ?>"
                   placeholder="Contoh: 198501012010012001" class="form-control" maxlength="18">
            <small class="form-text">Masukkan NIP pembimbing (18 digit)</small>
        </div>
        
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required 
                   value="<?= htmlspecialchars($pembimbing['username'] ?? '') ?>"
                   placeholder="Contoh: pemb198501012010012001" class="form-control">
            <small class="form-text">Username untuk login</small>
        </div>
        
        <div class="form-group">
            <label for="password">Password Baru:</label>
            <input type="password" id="password" name="password" 
                   placeholder="Kosongkan jika tidak ingin mengubah password" class="form-control" minlength="6">
            <small class="form-text">Password minimal 6 karakter (kosongkan jika tidak ingin mengubah)</small>
        </div>
        
        <div class="form-group">
            <label for="confirm_password">Konfirmasi Password Baru:</label>
            <input type="password" id="confirm_password" name="confirm_password" 
                   placeholder="Ulangi password baru" class="form-control" minlength="6">
            <small class="form-text">Ulangi password baru untuk konfirmasi</small>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">💾 Update Data</button>
            <a href="?r=admin/pembimbing" class="btn btn-secondary">❌ Batal</a>
        </div>
    </form>
</div>

<script>
// Validasi password confirmation
document.getElementById('confirm_password').addEventListener('input', function() {
    const password = document.getElementById('password').value;
    const confirmPassword = this.value;
    
    if (password !== '' && password !== confirmPassword) {
        this.setCustomValidity('Password tidak cocok');
    } else {
        this.setCustomValidity('');
    }
});

document.getElementById('password').addEventListener('input', function() {
    const confirmPassword = document.getElementById('confirm_password');
    if (confirmPassword.value) {
        if (this.value !== confirmPassword.value) {
            confirmPassword.setCustomValidity('Password tidak cocok');
        } else {
            confirmPassword.setCustomValidity('');
        }
    }
});
</script>

<div class="card">
    <h3>ℹ️ Informasi</h3>
    <div class="info-content">
        <p><strong>Nama Pembimbing:</strong> Masukkan nama lengkap pembimbing sesuai dengan gelar akademik yang dimiliki.</p>
        <p><strong>NIP:</strong> Nomor Induk Pegawai adalah nomor unik yang diberikan kepada setiap pegawai negeri sipil.</p>
        <p><strong>Catatan:</strong> Pembimbing yang sudah memiliki siswa bimbingan tidak dapat dihapus.</p>
    </div>
</div>

<style>
.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: bold;
    color: #333;
}

.form-control {
    width: 100%;
    padding: 12px;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 14px;
    transition: border-color 0.3s ease;
}

.form-control:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 0 2px rgba(0,123,255,0.25);
}

.form-text {
    font-size: 0.85rem;
    color: #6c757d;
    margin-top: 5px;
    display: block;
}

.form-actions {
    display: flex;
    gap: 15px;
    margin-top: 25px;
    padding-top: 20px;
    border-top: 1px solid #dee2e6;
}

.btn {
    padding: 10px 20px;
    border: none;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 500;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-primary {
    background-color: #007bff;
    color: white;
}

.btn-primary:hover {
    background-color: #0056b3;
}

.btn-secondary {
    background-color: #6c757d;
    color: white;
}

.btn-secondary:hover {
    background-color: #545b62;
}

.alert {
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 6px;
    border: 1px solid transparent;
}

.alert-danger {
    color: #721c24;
    background-color: #f8d7da;
    border-color: #f5c6cb;
}

.info-content {
    line-height: 1.6;
}

.info-content p {
    margin-bottom: 10px;
}

.info-content strong {
    color: #007bff;
}

@media (max-width: 768px) {
    .form-actions {
        flex-direction: column;
    }
    
    .btn {
        width: 100%;
        text-align: center;
    }
}
</style>
