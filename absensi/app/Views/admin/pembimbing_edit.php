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
            <label for="nama">Nama Pembimbing: <span class="text-danger">*</span></label>
            <input type="text" id="nama" name="nama" required 
                   value="<?= htmlspecialchars($pembimbing['nama']) ?>"
                   placeholder="Contoh: Dr. Ahmad Suryanto, M.Pd" class="form-control">
            <small class="form-text">Masukkan nama lengkap pembimbing sesuai gelar akademik</small>
        </div>
        
        <div class="form-group">
            <label for="nip">NIP: <span class="text-danger">*</span></label>
            <input type="text" id="nip" name="nip" required 
                   value="<?= htmlspecialchars($pembimbing['nip']) ?>"
                   placeholder="Contoh: 198501012010012001" class="form-control" maxlength="18" pattern="[0-9]{18}">
            <small class="form-text">Masukkan NIP pembimbing (18 digit angka)</small>
        </div>
        
        <div class="form-group">
            <label for="username">Username: <span class="text-danger">*</span></label>
            <input type="text" id="username" name="username" required 
                   value="<?= htmlspecialchars($pembimbing['username'] ?? '') ?>"
                   placeholder="Contoh: pemb198501012010012001" class="form-control">
            <small class="form-text">
                <?php if (empty($pembimbing['username'])): ?>
                    <span style="color: #dc3545;">⚠️ Pembimbing ini belum memiliki user account. Username akan dibuat otomatis jika dikosongkan.</span>
                <?php else: ?>
                    Username untuk login. Dapat diubah sesuai kebutuhan.
                <?php endif; ?>
            </small>
        </div>
        
        <div class="form-group">
            <label for="password">Password Baru:</label>
            <input type="password" id="password" name="password" 
                   placeholder="<?= empty($pembimbing['username']) ? 'Password untuk user account baru' : 'Kosongkan jika tidak ingin mengubah password' ?>" 
                   class="form-control" minlength="6">
            <small class="form-text">
                <?php if (empty($pembimbing['username'])): ?>
                    <span style="color: #dc3545;">⚠️ Password wajib diisi untuk membuat user account baru!</span>
                <?php else: ?>
                    Password minimal 6 karakter (kosongkan jika tidak ingin mengubah)
                <?php endif; ?>
            </small>
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
// Validasi form edit pembimbing
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('confirm_password');
    const nip = document.getElementById('nip');
    const username = document.getElementById('username');
    const hasExistingUser = <?= !empty($pembimbing['username']) ? 'true' : 'false' ?>;
    
    // Auto-generate username jika kosong dan NIP diisi
    function autoGenerateUsername() {
        if (!username.value.trim() && nip.value.trim()) {
            username.value = 'pemb' + nip.value.trim();
        }
    }
    
    nip.addEventListener('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
        if (this.value.length > 18) {
            this.value = this.value.slice(0, 18);
        }
        autoGenerateUsername();
    });
    
    // Validasi password confirmation
    function validatePassword() {
        if (password.value !== '' && password.value !== confirmPassword.value) {
            confirmPassword.setCustomValidity('Password tidak cocok');
            showError('confirm_password', 'Password tidak cocok');
        } else {
            confirmPassword.setCustomValidity('');
            clearError('confirm_password');
        }
    }
    
    // Validasi password requirement untuk user baru
    function validatePasswordRequirement() {
        if (!hasExistingUser && password.value.trim() === '') {
            password.setCustomValidity('Password wajib diisi untuk membuat user account baru');
            showError('password', 'Password wajib diisi untuk membuat user account baru');
        } else {
            password.setCustomValidity('');
            clearError('password');
        }
    }
    
    password.addEventListener('input', function() {
        validatePassword();
        validatePasswordRequirement();
    });
    
    confirmPassword.addEventListener('input', validatePassword);
    
    // Validasi username
    username.addEventListener('input', function() {
        if (this.value.trim() === '') {
            this.setCustomValidity('Username harus diisi');
            showError('username', 'Username harus diisi');
        } else if (this.value.length < 3) {
            this.setCustomValidity('Username minimal 3 karakter');
            showError('username', 'Username minimal 3 karakter');
        } else {
            this.setCustomValidity('');
            clearError('username');
        }
    });
    
    // Validasi form sebelum submit
    form.addEventListener('submit', function(e) {
        clearValidationErrors();
        
        // Validasi khusus untuk user baru
        if (!hasExistingUser && password.value.trim() === '') {
            e.preventDefault();
            showError('password', 'Password wajib diisi untuk membuat user account baru');
            return;
        }
        
        if (!form.checkValidity()) {
            e.preventDefault();
            showFormErrors();
        }
    });
});

// Fungsi untuk menampilkan error
function showError(fieldId, message) {
    const field = document.getElementById(fieldId);
    const errorDiv = document.createElement('div');
    errorDiv.className = 'error-message';
    errorDiv.textContent = message;
    errorDiv.style.color = '#dc3545';
    errorDiv.style.fontSize = '0.8rem';
    errorDiv.style.marginTop = '5px';
    
    // Hapus error message yang sudah ada
    const existingError = field.parentNode.querySelector('.error-message');
    if (existingError) {
        existingError.remove();
    }
    
    field.parentNode.appendChild(errorDiv);
    field.style.borderColor = '#dc3545';
}

// Fungsi untuk menghapus error
function clearError(fieldId) {
    const field = document.getElementById(fieldId);
    const errorDiv = field.parentNode.querySelector('.error-message');
    if (errorDiv) {
        errorDiv.remove();
    }
    field.style.borderColor = '#ddd';
}

// Fungsi untuk menghapus semua error
function clearValidationErrors() {
    const errorMessages = document.querySelectorAll('.error-message');
    errorMessages.forEach(error => error.remove());
    
    const fields = document.querySelectorAll('.form-control');
    fields.forEach(field => {
        field.style.borderColor = '#ddd';
    });
}

// Fungsi untuk menampilkan semua error form
function showFormErrors() {
    const form = document.querySelector('form');
    const invalidFields = form.querySelectorAll(':invalid');
    
    invalidFields.forEach(field => {
        if (field.validity.valueMissing) {
            showError(field.id, 'Field ini harus diisi');
        } else if (field.validity.patternMismatch) {
            showError(field.id, 'Format tidak valid');
        } else if (field.validity.tooShort) {
            showError(field.id, `Minimal ${field.minLength} karakter`);
        }
    });
}
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

.form-group label .text-danger {
    color: #dc3545;
}

.form-control {
    width: 100%;
    padding: 12px;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 14px;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
}

.form-control:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 0 2px rgba(0,123,255,0.25);
}

.form-control:invalid {
    border-color: #dc3545;
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

/* Responsive design */
@media (max-width: 768px) {
    .form-actions {
        flex-direction: column;
    }
    
    .btn {
        width: 100%;
        text-align: center;
    }
    
    .form-control {
        padding: 10px;
    }
}
</style>
