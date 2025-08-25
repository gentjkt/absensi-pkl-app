<h2>👨‍🏫 Kelola Pembimbing</h2>

<div class="action-buttons" style="margin-bottom: 20px;">
    <a href="?r=admin/dashboard" class="btn btn-secondary">
        ↩️ Kembali ke Dashboard
    </a>
    
    <button onclick="showAddForm()" class="btn btn-primary">
        ➕ Tambah Pembimbing
    </button>
</div>

<!-- Pesan Sukses/Error -->
<?php if(isset($_GET['success'])): ?>
    <?php 
    $message = '';
    $type = 'success';
    switch($_GET['success']) {
        case '1': $message = 'Pembimbing berhasil ditambahkan!'; break;
        case '2': $message = 'Data pembimbing berhasil diupdate!'; break;
        case '3': $message = 'Pembimbing berhasil dihapus!'; break;
    }
    ?>
    <div class="alert alert-<?= $type ?>">
        <strong>✅ Sukses:</strong> <?= htmlspecialchars($message) ?>
    </div>
<?php endif; ?>

<?php if(isset($_GET['error'])): ?>
    <div class="alert alert-danger">
        <strong>❌ Error:</strong> <?= htmlspecialchars($_GET['error']) ?>
    </div>
<?php endif; ?>

<div class="grid">
    <!-- Form Tambah Pembimbing -->
    <div class="card" id="addForm" style="display: none;">
        <h3>➕ Tambah Pembimbing Baru</h3>
        <form method="post" action="?r=admin/pembimbingAdd" id="formTambahPembimbing">
            <?= \App\Helpers\CSRF::field() ?>
            
            <div class="form-group">
                <label for="nama">Nama Pembimbing: <span class="text-danger">*</span></label>
                <input type="text" id="nama" name="nama" required 
                       placeholder="Contoh: Dr. Ahmad Suryanto, M.Pd" class="form-control">
                <small class="form-text">Masukkan nama lengkap pembimbing sesuai gelar akademik</small>
            </div>
            
            <div class="form-group">
                <label for="nip">NIP: <span class="text-danger">*</span></label>
                <input type="text" id="nip" name="nip" required 
                       placeholder="Contoh: 198501012010012001" class="form-control" maxlength="18" pattern="[0-9]{18}">
                <small class="form-text">Masukkan NIP pembimbing (18 digit angka)</small>
            </div>
            
            <div class="form-group">
                <label for="username">Username: <span class="text-danger">*</span></label>
                <input type="text" id="username" name="username" required 
                       placeholder="Contoh: pemb198501012010012001" class="form-control">
                <small class="form-text">Username untuk login (akan dibuat otomatis jika dikosongkan)</small>
            </div>
            
            <div class="form-group">
                <label for="password">Password: <span class="text-danger">*</span></label>
                <input type="password" id="password" name="password" required 
                       placeholder="Minimal 6 karakter" class="form-control" minlength="6">
                <small class="form-text">Password minimal 6 karakter</small>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Konfirmasi Password: <span class="text-danger">*</span></label>
                <input type="password" id="confirm_password" name="confirm_password" required 
                       placeholder="Ulangi password" class="form-control" minlength="6">
                <small class="form-text">Ulangi password untuk konfirmasi</small>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">💾 Simpan Pembimbing</button>
                <button type="button" onclick="hideAddForm()" class="btn btn-secondary">❌ Batal</button>
            </div>
        </form>
    </div>
    
    <!-- Tabel Pembimbing -->
    <div class="card">
        <h3>📋 Daftar Pembimbing</h3>
        
        <!-- Filter dan Pencarian -->

        <?php if(empty($pembimbing)): ?>
            <div class="alert alert-info">
                <strong>ℹ️ Info:</strong> Belum ada data pembimbing. 
                Silakan tambah pembimbing baru.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Pembimbing</th>
                            <th>NIP</th>
                            <th>Username</th>
                            <th>Total Siswa</th>
                            <th>Status</th>
                            <th>Tanggal Dibuat</th>
                            <th>Terakhir Update</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php 
                    $no = 1;
                    foreach($pembimbing as $p): 
                    ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td>
                                <strong><?= htmlspecialchars($p['nama']) ?></strong>
                                <?php if (!empty($p['user_id'])): ?>
                                    <br><small class="text-muted">ID User: <?= $p['user_id'] ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="nip-badge"><?= htmlspecialchars($p['nip']) ?></span>
                            </td>
                            <td>
                                <?php if (!empty($p['username'])): ?>
                                    <span class="username-badge"><?= htmlspecialchars($p['username']) ?></span>
                                <?php else: ?>
                                    <span class="badge badge-warning">Belum ada user</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="siswa-count"><?= $p['total_siswa'] ?? 0 ?> siswa</span>
                                <?php if (($p['total_siswa'] ?? 0) > 0): ?>
                                    <br><small class="text-muted">Aktif membimbing</small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if(($p['total_siswa'] ?? 0) > 0): ?>
                                    <span class="badge badge-success">🟢 Aktif</span>
                                <?php else: ?>
                                    <span class="badge badge-secondary">⚪ Tidak Aktif</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($p['created_at'])): ?>
                                    <span class="date-info">
                                        <?= date('d/m/Y', strtotime($p['created_at'])) ?>
                                        <br><small class="text-muted"><?= date('H:i', strtotime($p['created_at'])) ?> WIB</small>
                                    </span>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($p['updated_at'])): ?>
                                    <span class="date-info">
                                        <?= date('d/m/Y', strtotime($p['updated_at'])) ?>
                                        <br><small class="text-muted"><?= date('H:i', strtotime($p['updated_at'])) ?> WIB</small>
                                    </span>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="action-buttons-small">
                                    <a href="?r=admin/pembimbingEdit&id=<?= $p['id'] ?>" 
                                       class="btn btn-sm btn-warning" title="Edit">
                                        ✏️ Edit
                                    </a>
                                    
                                    <?php if(($p['total_siswa'] ?? 0) == 0): ?>
                                        <button onclick="deletePembimbing(<?= $p['id'] ?>, '<?= htmlspecialchars($p['nama']) ?>')" 
                                                class="btn btn-sm btn-danger" title="Hapus">
                                            🗑️ Hapus
                                        </button>
                                    <?php else: ?>
                                        <button class="btn btn-sm btn-secondary" disabled title="Tidak dapat dihapus karena masih membimbing siswa">
                                            🔒 Terkunci
                                        </button>
                                    <?php endif; ?>
                                    
                                    <a href="?r=admin/pembimbingDetail&id=<?= $p['id'] ?>" 
                                       class="btn btn-sm btn-info" title="Detail">
                                        👁️ Detail
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Statistik Pembimbing -->
            <div class="stats-section" style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #dee2e6;">
                <h4>📊 Statistik Pembimbing</h4>
                <div class="stats-grid">
                    <div class="stat-item">
                        <div class="stat-number"><?= count($pembimbing) ?></div>
                        <div class="stat-label">Total Pembimbing</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number"><?= count(array_filter($pembimbing, function($p) { return ($p['total_siswa'] ?? 0) > 0; })) ?></div>
                        <div class="stat-label">Pembimbing Aktif</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number"><?= count(array_filter($pembimbing, function($p) { return !empty($p['username']); })) ?></div>
                        <div class="stat-label">Sudah Ada User</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number"><?= array_sum(array_column($pembimbing, 'total_siswa')) ?></div>
                        <div class="stat-label">Total Siswa Dibimbing</div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function showAddForm() {
    document.getElementById('addForm').style.display = 'block';
    // Reset form ketika dibuka
    document.getElementById('formTambahPembimbing').reset();
    // Reset validasi
    clearValidationErrors();
}

function hideAddForm() {
    document.getElementById('addForm').style.display = 'none';
    // Reset form ketika ditutup
    document.getElementById('formTambahPembimbing').reset();
    clearValidationErrors();
}

function deletePembimbing(id, nama) {
    if (confirm(`Yakin ingin menghapus pembimbing "${nama}"? Tindakan ini tidak dapat dibatalkan!`)) {
        window.location.href = `?r=admin/pembimbingDelete&id=${id}`;
    }
}

// Validasi form
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('formTambahPembimbing');
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('confirm_password');
    const nip = document.getElementById('nip');
    const username = document.getElementById('username');
    
    // Validasi password confirmation
    function validatePassword() {
        if (password.value !== confirmPassword.value) {
            confirmPassword.setCustomValidity('Password tidak cocok');
            showError('confirm_password', 'Password tidak cocok');
        } else {
            confirmPassword.setCustomValidity('');
            clearError('confirm_password');
        }
    }
    
    password.addEventListener('input', validatePassword);
    confirmPassword.addEventListener('input', validatePassword);
    
    // Auto-generate username dari NIP
    nip.addEventListener('input', function() {
        const nipValue = this.value.replace(/\s/g, '');
        if (nipValue.length >= 8 && username.value === '') {
            username.value = 'pemb' + nipValue;
        }
    });
    
    // Validasi NIP (hanya angka)
    nip.addEventListener('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
        if (this.value.length > 18) {
            this.value = this.value.slice(0, 18);
        }
    });
    
    // Validasi form sebelum submit
    form.addEventListener('submit', function(e) {
        clearValidationErrors();
        
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
    const form = document.getElementById('formTambahPembimbing');
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

<style>
.grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 20px;
    margin-top: 20px;
}

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

.nip-badge {
    background: #007bff;
    color: white;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: bold;
    font-family: monospace;
}

.username-badge {
    background: #28a745;
    color: white;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: bold;
    font-family: monospace;
}

.siswa-count {
    color: #28a745;
    font-weight: bold;
}

.action-buttons-small {
    display: flex;
    gap: 5px;
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

.btn-warning {
    background-color: #ffc107;
    color: #212529;
}

.btn-warning:hover {
    background-color: #e0a800;
}

.btn-danger {
    background-color: #dc3545;
    color: white;
}

.btn-danger:hover {
    background-color: #c82333;
}

.btn-info {
    background-color: #17a2b8;
    color: white;
}

.btn-info:hover {
    background-color: #138496;
}

.btn-sm {
    padding: 5px 10px;
    font-size: 12px;
}

.alert {
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 6px;
    border: 1px solid transparent;
}

.alert-success {
    color: #155724;
    background-color: #d4edda;
    border-color: #c3e6cb;
}

.alert-danger {
    color: #721c24;
    background-color: #f8d7da;
    border-color: #f5c6cb;
}

.alert-info {
    color: #0c5460;
    background-color: #d1ecf1;
    border-color: #bee5eb;
}

.badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: bold;
}

.badge-success {
    background-color: #28a745;
    color: white;
}

.badge-secondary {
    background-color: #6c757d;
    color: white;
}

.badge-warning {
    background-color: #ffc107;
    color: #212529;
}

.date-info {
    font-size: 0.85rem;
    color: #555;
}

.stats-section {
    background-color: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    margin-top: 20px;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 15px;
}

.stat-item {
    text-align: center;
    padding: 15px;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    background-color: #fff;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.stat-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.stat-number {
    font-size: 2rem;
    font-weight: bold;
    color: #007bff;
    margin-bottom: 5px;
}

.stat-label {
    font-size: 0.9rem;
    color: #666;
    font-weight: 500;
}

.text-muted {
    color: #6c757d !important;
}

/* Responsive table */
.table-responsive {
    overflow-x: auto;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.table {
    width: 100%;
    border-collapse: collapse;
    background-color: #fff;
}

.table th {
    background-color: #f8f9fa;
    padding: 12px 8px;
    text-align: left;
    font-weight: 600;
    color: #495057;
    border-bottom: 2px solid #dee2e6;
}

.table td {
    padding: 12px 8px;
    border-bottom: 1px solid #dee2e6;
    vertical-align: top;
}

.table tbody tr:hover {
    background-color: #f8f9fa;
}

/* Badge styling improvements */
.nip-badge, .username-badge {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: bold;
    font-family: monospace;
    white-space: nowrap;
}

.nip-badge {
    background: #007bff;
    color: white;
}

.username-badge {
    background: #28a745;
    color: white;
}

.siswa-count {
    color: #28a745;
    font-weight: bold;
}

/* Action buttons improvements */
.action-buttons-small {
    display: flex;
    gap: 5px;
    flex-wrap: wrap;
}

.action-buttons-small .btn {
    margin: 2px 0;
}

/* Animasi untuk form */
#addForm {
    animation: slideDown 0.3s ease;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Responsive design untuk form */
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

/* Responsive improvements */
@media (max-width: 1200px) {
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .table-responsive {
        font-size: 0.9rem;
    }
    
    .table th, .table td {
        padding: 8px 6px;
    }
    
    .action-buttons-small {
        flex-direction: column;
    }
    
    .action-buttons-small .btn {
        width: 100%;
        text-align: center;
    }
}

/* Filter section styling */
.filter-section {
    background-color: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 20px;
    border: 1px solid #e9ecef;
}

.filter-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    margin-bottom: 15px;
}

.filter-item label {
    display: block;
    margin-bottom: 5px;
    font-weight: 600;
    color: #495057;
    font-size: 0.9rem;
}

.filter-item .form-control {
    padding: 8px 12px;
    font-size: 0.9rem;
}

.filter-actions {
    display: flex;
    gap: 10px;
    align-items: center;
}

.filter-actions .btn {
    padding: 8px 16px;
    font-size: 0.9rem;
}

/* Responsive filter */
@media (max-width: 768px) {
    .filter-grid {
        grid-template-columns: 1fr;
    }
    
    .filter-actions {
        flex-direction: column;
        align-items: stretch;
    }
    
    .filter-actions .btn {
        width: 100%;
    }
}
</style>
