<h2>👥 Kelola Data Siswa</h2>

<?php if(isset($_GET['success'])): ?>
    <?php 
    $successMessages = [
        1 => '✅ Siswa berhasil ditambahkan!',
        2 => '✅ Data siswa berhasil diupdate!',
        3 => '✅ Siswa berhasil dihapus!'
    ];
    $message = $successMessages[$_GET['success']] ?? '✅ Operasi berhasil!';
    ?>
    <div class="alert alert-success">
        <strong><?= $message ?></strong>
    </div>
<?php endif; ?>

<?php if(isset($_GET['error'])): ?>
    <div class="alert alert-danger">
        <strong>❌ Error:</strong> <?= htmlspecialchars($_GET['error']) ?>
    </div>
<?php endif; ?>

<div class="action-buttons" style="margin-bottom: 20px;">
    <a href="?r=admin/siswaAdd" class="btn btn-primary">
        ➕ Tambah Siswa Baru
    </a>
    
    <a href="?r=admin/import" class="btn btn-success">
        📥 Import Siswa (CSV)
    </a>
    
    <a href="?r=admin/report" class="btn btn-info">
        📊 Export Absensi (CSV)
    </a>
</div>

<div class="card">
    <h3>🔍 Filter & Pencarian</h3>
    <form method="get" action="">
        <input type="hidden" name="r" value="admin/siswa">
        <div class="filter-grid">
            <div class="filter-group">
                <label for="search_nama">Nama Siswa</label>
                <input type="text" id="search_nama" name="search_nama" class="form-control" placeholder="Ketik nama siswa" value="<?= htmlspecialchars($_GET['search_nama'] ?? '') ?>">
            </div>
            <div class="filter-group">
                <label for="search_kelas">Kelas</label>
                <input type="text" id="search_kelas" name="search_kelas" class="form-control" placeholder="mis. XII TKJ 1" value="<?= htmlspecialchars($_GET['search_kelas'] ?? '') ?>">
            </div>
            <div class="filter-group">
                <label for="pembimbing_id">Pembimbing</label>
                <select id="pembimbing_id" name="pembimbing_id" class="form-control">
                    <option value="">-- Semua Pembimbing --</option>
                    <?php foreach (($pembimbing ?? []) as $p): ?>
                        <option value="<?= (int)$p['id'] ?>" <?= ((string)($filters['pembimbing_id'] ?? ($_GET['pembimbing_id'] ?? '')) === (string)$p['id']) ? 'selected' : '' ?>><?= htmlspecialchars($p['nama']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="filter-group">
                <label for="tempat_pkl_id">Tempat PKL</label>
                <select id="tempat_pkl_id" name="tempat_pkl_id" class="form-control">
                    <option value="">-- Semua Tempat --</option>
                    <?php foreach (($tempatPKL ?? []) as $t): ?>
                        <option value="<?= (int)$t['id'] ?>" <?= ((string)($filters['tempat_pkl_id'] ?? ($_GET['tempat_pkl_id'] ?? '')) === (string)$t['id']) ? 'selected' : '' ?>><?= htmlspecialchars($t['nama']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="filter-actions">
            <button type="submit" class="btn btn-primary">🔍 Terapkan Filter</button>
            <a href="?r=admin/siswa" class="btn btn-secondary">🔄 Reset</a>
        </div>
    </form>
</div>


<div class="card">
    <h3>📋 Daftar Siswa</h3>
 
    <?php if(empty($siswa)): ?>
        <div class="alert alert-info">
            <strong>ℹ️ Info:</strong> Belum ada data siswa. 
            <a href="?r=admin/siswaAdd">Klik di sini</a> untuk menambah siswa pertama.
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>NIS</th>
                        <th>Nama Siswa</th>
                        <th>Kelas</th>
                        <th>Pembimbing</th>
                        <th>Tempat PKL</th>
                        <th>Koordinat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php 
                $no = 1;
                foreach($siswa as $s): 
                ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td>
                            <strong><?= htmlspecialchars($s['nis']) ?></strong>
                        </td>
                        <td>
                            <strong><?= htmlspecialchars($s['nama']) ?></strong>
                        </td>
                        <td><?= htmlspecialchars($s['kelas']) ?></td>
                        <td>
                            <?php if($s['pembimbing']): ?>
                                <span class="badge badge-info">
                                    <?= htmlspecialchars($s['pembimbing']) ?>
                                </span>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($s['tempat_pkl']): ?>
                                <span class="badge badge-success">
                                    <?= htmlspecialchars($s['tempat_pkl']) ?>
                                </span>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($s['lat'] && $s['lng']): ?>
                                <span class="coordinate-info">
                                    <?= number_format($s['lat'], 6) ?>, <?= number_format($s['lng'], 6) ?>
                                </span>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="action-buttons-small">
                                <a href="?r=admin/siswaEdit&id=<?= $s['id'] ?>" 
                                   class="btn btn-sm btn-warning" title="Edit">
                                    ✏️ Edit
                                </a>
                                
                                <a href="?r=admin/siswaDelete&id=<?= $s['id'] ?>" 
                                   class="btn btn-sm btn-danger" 
                                   onclick="return confirm('Yakin ingin menghapus siswa <?= htmlspecialchars($s['nama']) ?>? Tindakan ini tidak dapat dibatalkan!')"
                                   title="Hapus">
                                    🗑️ Hapus
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <div class="table-info">
            <p><strong>Total Siswa:</strong> <?= count($siswa) ?> orang</p>
        </div>
    <?php endif; ?>
</div>

<div class="card">
    <h3>💡 Informasi</h3>
    
    <div class="info-grid">
        <div class="info-item">
            <strong>➕ Tambah Siswa:</strong>
            <ul>
                <li>Klik tombol "Tambah Siswa Baru" untuk menambah siswa satu per satu</li>
                <li>Sistem akan otomatis membuat user account dengan username = NIS</li>
                <li>Password dapat diatur sesuai kebutuhan</li>
            </ul>
        </div>
        
        <div class="info-item">
            <strong>📥 Import CSV:</strong>
            <ul>
                <li>Gunakan fitur import untuk menambah banyak siswa sekaligus</li>
                <li>Format CSV: NIS, Nama, Kelas</li>
                <li>Semua siswa akan menggunakan password yang sama</li>
            </ul>
        </div>
        
        <div class="info-item">
            <strong>✏️ Edit & Hapus:</strong>
            <ul>
                <li>Gunakan tombol edit untuk mengubah data siswa</li>
                <li>Password dapat diubah melalui form edit</li>
                <li>Hapus siswa akan menghapus user account juga</li>
            </ul>
        </div>
    </div>
</div>

<style>
/* Search Form Styling */
.search-section {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 10px;
    border: 1px solid #e9ecef;
}

.search-form {
    width: 100%;
}

.search-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    margin-bottom: 20px;
}

.search-group {
    display: flex;
    flex-direction: column;
}

.search-group label {
    font-weight: 600;
    margin-bottom: 5px;
    color: #495057;
    font-size: 0.9rem;
}

.search-group input,
.search-group select {
    padding: 8px 12px;
    border: 1px solid #ced4da;
    border-radius: 6px;
    font-size: 0.9rem;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

.search-group input:focus,
.search-group select:focus {
    border-color: #80bdff;
    outline: 0;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.search-actions {
    display: flex;
    align-items: center;
    gap: 15px;
    flex-wrap: wrap;
}

.search-info {
    margin-left: auto;
    font-size: 0.9rem;
    color: #6c757d;
    font-weight: 500;
}

.action-buttons {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.action-buttons-small {
    display: flex;
    gap: 5px;
}

.badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: bold;
}

.badge-info {
    background-color: #17a2b8;
    color: white;
}

.badge-success {
    background-color: #28a745;
    color: white;
}

.coordinate-info {
    font-family: monospace;
    font-size: 0.9rem;
    color: #6c757d;
}

.table-info {
    margin-top: 20px;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 6px;
    text-align: center;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
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

/* ===== FILTER STYLES ===== */
.filter-section {
    background: white;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin-bottom: 20px;
}

.filter-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 2px solid #e9ecef;
}

.filter-header h4 {
    margin: 0;
    color: #2c3e50;
    font-size: 18px;
}

.filter-content {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
}

.filter-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 20px;
}

.filter-group {
    display: flex;
    flex-direction: column;
}

.filter-group label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 8px;
    font-size: 14px;
}

.filter-actions {
    display: flex;
    gap: 15px;
    align-items: center;
    flex-wrap: wrap;
}

.filter-info {
    margin-top: 15px;
}

.filter-summary {
    background: #e3f2fd;
    color: #1976d2;
    padding: 12px 16px;
    border-radius: 6px;
    font-weight: 500;
    text-align: center;
}

.alert-link {
    color: #1976d2;
    text-decoration: none;
    font-weight: 600;
}

.alert-link:hover {
    text-decoration: underline;
}

.btn-outline {
    background: transparent;
    color: #6c757d;
    border: 1px solid #6c757d;
}

.btn-outline:hover {
    background: #6c757d;
    color: white;
}

@media (max-width: 768px) {
    .filter-grid {
        grid-template-columns: 1fr;
        gap: 15px;
    }
    
    .filter-actions {
        flex-direction: column;
        align-items: stretch;
    }
    
    .filter-actions .btn {
        width: 100%;
    }
    
    .filter-header {
        flex-direction: column;
        gap: 15px;
        align-items: stretch;
    }
    
    .search-row {
        grid-template-columns: 1fr;
        gap: 10px;
    }
    
    .search-actions {
        flex-direction: column;
        align-items: stretch;
    }
    
    .search-info {
        margin-left: 0;
        text-align: center;
        margin-top: 10px;
    }
    
    .action-buttons {
        flex-direction: column;
    }
    
    .action-buttons .btn {
        width: 100%;
    }
    
    .action-buttons-small {
        flex-direction: column;
    }
    
    .info-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<!-- JavaScript untuk Filter -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggleFilters = document.getElementById('toggleFilters');
    const filterContent = document.getElementById('filterContent');
    const toggleText = document.getElementById('toggleText');
    const clearFilters = document.getElementById('clearFilters');

    // Pasang event listener hanya jika elemen tersedia
    if (toggleFilters && filterContent && toggleText) {
        toggleFilters.addEventListener('click', function() {
            if (filterContent.style.display === 'none') {
                filterContent.style.display = 'block';
                toggleText.textContent = '📋 Sembunyikan Filter';
            } else {
                filterContent.style.display = 'none';
                toggleText.textContent = '📋 Tampilkan Filter';
            }
        });
    }

    if (clearFilters && filterContent) {
        clearFilters.addEventListener('click', function() {
            const inputs = filterContent.querySelectorAll('input, select');
            inputs.forEach(input => {
                if (input.type === 'text') {
                    input.value = '';
                } else if (input.tagName === 'SELECT') {
                    input.selectedIndex = 0;
                }
            });
        });
    }

    // Auto-show filters jika ada yang aktif (hanya jika container ada)
    if (filterContent && toggleText) {
        const urlParams = new URLSearchParams(window.location.search);
        const hasActiveFilters = urlParams.has('search_nama') || urlParams.has('search_kelas') || 
                               urlParams.has('search_pembimbing') || urlParams.has('search_tempat');
        if (hasActiveFilters) {
            filterContent.style.display = 'block';
            toggleText.textContent = '📋 Sembunyikan Filter';
        }
    }
});
</script>