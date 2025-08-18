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
        <form method="post" action="?r=admin/pembimbingAdd">
            <?= \App\Helpers\CSRF::field() ?>
            
            <div class="form-group">
                <label for="nama">Nama Pembimbing:</label>
                <input type="text" id="nama" name="nama" required 
                       placeholder="Contoh: Dr. Ahmad Suryanto, M.Pd" class="form-control">
                <small class="form-text">Masukkan nama lengkap pembimbing</small>
            </div>
            
            <div class="form-group">
                <label for="nip">NIP:</label>
                <input type="text" id="nip" name="nip" required 
                       placeholder="Contoh: 198501012010012001" class="form-control">
                <small class="form-text">Masukkan NIP pembimbing (18 digit)</small>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">💾 Simpan</button>
                <button type="button" onclick="hideAddForm()" class="btn btn-secondary">❌ Batal</button>
            </div>
        </form>
    </div>
    
    <!-- Tabel Pembimbing -->
    <div class="card">
        <h3>📋 Daftar Pembimbing</h3>
        
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
                            </td>
                            <td>
                                <span class="nip-badge"><?= htmlspecialchars($p['nip']) ?></span>
                            </td>
                            <td>
                                <span class="username-badge"><?= htmlspecialchars($p['username'] ?? 'Belum ada') ?></span>
                            </td>
                            <td>
                                <span class="siswa-count"><?= $p['total_siswa'] ?? 0 ?> siswa</span>
                            </td>
                            <td>
                                <?php if(($p['total_siswa'] ?? 0) > 0): ?>
                                    <span class="badge badge-success">🟢 Aktif</span>
                                <?php else: ?>
                                    <span class="badge badge-secondary">⚪ Tidak Aktif</span>
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
        <?php endif; ?>
    </div>
</div>

<script>
function showAddForm() {
    document.getElementById('addForm').style.display = 'block';
}

function hideAddForm() {
    document.getElementById('addForm').style.display = 'none';
}

function deletePembimbing(id, nama) {
    if (confirm(`Yakin ingin menghapus pembimbing "${nama}"? Tindakan ini tidak dapat dibatalkan!`)) {
        window.location.href = `?r=admin/pembimbingDelete&id=${id}`;
    }
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

.form-text {
    font-size: 0.8rem;
    color: #6c757d;
    margin-top: 5px;
}

.form-actions {
    display: flex;
    gap: 10px;
    margin-top: 20px;
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

@media (max-width: 768px) {
    .form-actions {
        flex-direction: column;
    }
    
    .action-buttons-small {
        flex-direction: column;
    }
}
</style>
