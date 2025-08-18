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
    <h3>📋 Daftar Siswa</h3>
    
    <!-- Form Pencarian -->
    <div class="search-section" style="margin-bottom: 20px;">
        <form method="GET" action="?r=admin/siswa" class="search-form">
            <div class="search-row">
                <div class="search-group">
                    <label for="search_nis">🔍 NIS:</label>
                    <input type="text" id="search_nis" name="search_nis" 
                           value="<?= htmlspecialchars($_GET['search_nis'] ?? '') ?>" 
                           placeholder="Cari berdasarkan NIS..." class="form-control">
                </div>
                
                <div class="search-group">
                    <label for="search_nama">👤 Nama:</label>
                    <input type="text" id="search_nama" name="search_nama" 
                           value="<?= htmlspecialchars($_GET['search_nama'] ?? '') ?>" 
                           placeholder="Cari berdasarkan nama..." class="form-control">
                </div>
                
                <div class="search-group">
                    <label for="search_kelas">📚 Kelas:</label>
                    <input type="text" id="search_kelas" name="search_kelas" 
                           value="<?= htmlspecialchars($_GET['search_kelas'] ?? '') ?>" 
                           placeholder="Cari berdasarkan kelas..." class="form-control">
                </div>
                
                <div class="search-group">
                    <label for="search_pembimbing">👨‍🏫 Pembimbing:</label>
                    <select id="search_pembimbing" name="search_pembimbing" class="form-control">
                        <option value="">Semua Pembimbing</option>
                        <?php 
                        if(isset($pembimbing) && is_array($pembimbing)):
                            foreach($pembimbing as $p): 
                        ?>
                            <option value="<?= htmlspecialchars($p['name']) ?>" 
                                    <?= (isset($_GET['search_pembimbing']) && $_GET['search_pembimbing'] === $p['name']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($p['name']) ?>
                            </option>
                        <?php 
                            endforeach;
                        endif;
                        ?>
                    </select>
                </div>
                
                <div class="search-group">
                    <label for="search_tempat">🏢 Tempat PKL:</label>
                    <select id="search_tempat" name="search_tempat" class="form-control">
                        <option value="">Semua Tempat PKL</option>
                        <?php 
                        if(isset($tempatPKL) && is_array($tempatPKL)):
                            foreach($tempatPKL as $t): 
                        ?>
                            <option value="<?= htmlspecialchars($t['nama']) ?>" 
                                    <?= (isset($_GET['search_tempat']) && $_GET['search_tempat'] === $t['nama']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($t['nama']) ?>
                            </option>
                        <?php 
                            endforeach;
                        endif;
                        ?>
                    </select>
                </div>
            </div>
            
            <div class="search-actions">
                <button type="submit" class="btn btn-primary">
                    🔍 Cari
                </button>
                
                <a href="?r=admin/siswa" class="btn btn-secondary">
                    🔄 Reset
                </a>
                
                <span class="search-info">
                    <?php 
                    $totalResults = count($siswa);
                    $searchActive = !empty($_GET['search_nis']) || !empty($_GET['search_nama']) || 
                                   !empty($_GET['search_kelas']) || !empty($_GET['search_pembimbing']) || 
                                   !empty($_GET['search_tempat']);
                    ?>
                    <?php if($searchActive): ?>
                        📊 Menampilkan <?= $totalResults ?> hasil pencarian
                    <?php else: ?>
                        📊 Total: <?= $totalResults ?> siswa
                    <?php endif; ?>
                </span>
            </div>
        </form>
    </div>
    
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

@media (max-width: 768px) {
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