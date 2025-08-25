<h2>👨‍🏫 Detail Pembimbing</h2>

<div class="action-buttons" style="margin-bottom: 20px;">
    <a href="?r=admin/pembimbing" class="btn btn-secondary">
        ← Kembali ke Daftar Pembimbing
    </a>
    
    <a href="?r=admin/pembimbingEdit&id=<?= $pembimbing['id'] ?>" class="btn btn-primary">
        ✏️ Edit Pembimbing
    </a>
</div>

<div class="grid">
    <!-- Informasi Pembimbing -->
    <div class="card">
        <h3>📋 Informasi Pembimbing</h3>
        
        <div class="info-grid">
            <div class="info-item">
                <label>🆔 ID:</label>
                <span class="info-value"><?= $pembimbing['id'] ?></span>
            </div>
            
            <div class="info-item">
                <label>👤 Nama Lengkap:</label>
                <span class="info-value"><?= htmlspecialchars($pembimbing['nama']) ?></span>
            </div>
            
            <div class="info-item">
                <label>📧 Email:</label>
                <span class="info-value">
                    <?php if (!empty($pembimbing['email'])): ?>
                        <a href="mailto:<?= htmlspecialchars($pembimbing['email']) ?>">
                            <?= htmlspecialchars($pembimbing['email']) ?>
                        </a>
                    <?php else: ?>
                        <span class="text-muted">Tidak ada email</span>
                    <?php endif; ?>
                </span>
            </div>
            
            <div class="info-item">
                <label>📱 No. Telepon:</label>
                <span class="info-value">
                    <?php if (!empty($pembimbing['no_telp'])): ?>
                        <a href="tel:<?= htmlspecialchars($pembimbing['no_telp']) ?>">
                            <?= htmlspecialchars($pembimbing['no_telp']) ?>
                        </a>
                    <?php else: ?>
                        <span class="text-muted">Tidak ada nomor telepon</span>
                    <?php endif; ?>
                </span>
            </div>
            
            <div class="info-item">
                <label>🏢 Instansi:</label>
                <span class="info-value">
                    <?php if (!empty($pembimbing['instansi'])): ?>
                        <?= htmlspecialchars($pembimbing['instansi']) ?>
                    <?php else: ?>
                        <span class="text-muted">Tidak ada instansi</span>
                    <?php endif; ?>
                </span>
            </div>
            
            <div class="info-item">
                <label>👤 Username:</label>
                <span class="username-badge"><?= htmlspecialchars($pembimbing['username']) ?></span>
            </div>
            
            <div class="info-item">
                <label>🔑 Role:</label>
                <span class="role-badge"><?= htmlspecialchars($pembimbing['role'] ?? 'Tidak ada') ?></span>
            </div>
            
            <div class="info-item">
                <label>📅 Tanggal Dibuat:</label>
                <span class="info-value">
                    <?php if (!empty($pembimbing['created_at'])): ?>
                        <?= date('d/m/Y H:i', strtotime($pembimbing['created_at'])) ?> WIB
                    <?php else: ?>
                        <span class="text-muted">Tidak ada data</span>
                    <?php endif; ?>
                </span>
            </div>
            
            <div class="info-item">
                <label>🔄 Terakhir Diupdate:</label>
                <span class="info-value">
                    <?php if (!empty($pembimbing['updated_at'])): ?>
                        <?= date('d/m/Y H:i', strtotime($pembimbing['updated_at'])) ?> WIB
                    <?php else: ?>
                        <span class="text-muted">Tidak ada data</span>
                    <?php endif; ?>
                </span>
            </div>
        </div>
    </div>
    
    <!-- Statistik -->
    <div class="card">
        <h3>📊 Statistik Pembimbing</h3>
        
        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-number"><?= count($siswaDibimbing) ?></div>
                <div class="stat-label">👥 Total Siswa Dibimbing</div>
            </div>
            
            <div class="stat-item">
                <div class="stat-number"><?= $totalAbsensi ?></div>
                <div class="stat-label">📝 Total Absensi</div>
            </div>
        </div>
    </div>
    
    <!-- Daftar Siswa yang Dibimbing -->
    <div class="card">
        <h3>👥 Siswa yang Dibimbing</h3>
        
        <?php if (empty($siswaDibimbing)): ?>
            <div class="empty-state">
                <p>📭 Pembimbing ini belum membimbing siswa apapun</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>🆔 NIS</th>
                            <th>👤 Nama</th>
                            <th>📚 Kelas</th>
                            <th>🏢 Tempat PKL</th>
                            <th>📅 Info</th>
                            <th>🔧 Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($siswaDibimbing as $siswa): ?>
                            <tr>
                                <td>
                                    <span class="nis-badge"><?= htmlspecialchars($siswa['nis']) ?></span>
                                </td>
                                <td><?= htmlspecialchars($siswa['nama']) ?></td>
                                <td><?= htmlspecialchars($siswa['kelas']) ?></td>
                                <td>
                                    <?php if (!empty($siswa['tempat_pkl'])): ?>
                                        <?= htmlspecialchars($siswa['tempat_pkl']) ?>
                                    <?php else: ?>
                                        <span class="text-muted">Belum ditentukan</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="text-muted">Tidak tersedia</span>
                                </td>
                                <td>
                                    <a href="?r=admin/siswaEdit&id=<?= $siswa['id'] ?>" 
                                       class="btn btn-sm btn-primary" title="Edit Siswa">
                                        ✏️ Edit
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Absensi Terbaru -->
    <div class="card">
        <h3>📝 Absensi Terbaru</h3>
        
        <?php if (empty($absensiTerbaru)): ?>
            <div class="empty-state">
                <p>📭 Belum ada data absensi</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>👤 Siswa</th>
                            <th>📅 Tanggal</th>
                            <th>🕐 Waktu</th>
                            <th>📍 Lokasi</th>
                            <th>📸 Selfie</th>
                            <th>✅ Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($absensiTerbaru as $abs): ?>
                            <tr>
                                <td><?= htmlspecialchars($abs['nama_siswa']) ?></td>
                                <td><?= date('d/m/Y', strtotime($abs['waktu'])) ?></td>
                                <td><?= date('H:i', strtotime($abs['waktu'])) ?> WIB</td>
                                <td>
                                    <div class="location-info">
                                        <div>📍 <?= number_format($abs['lat'], 6) ?>, <?= number_format($abs['lng'], 6) ?></div>
                                        <div>📏 <?= number_format($abs['jarak_m'], 1) ?>m</div>
                                    </div>
                                </td>
                                <td>
                                    <?php if (!empty($abs['selfie_path'])): ?>
                                        <a href="<?= htmlspecialchars($abs['selfie_path']) ?>" 
                                           target="_blank" class="btn btn-sm btn-outline-primary">
                                            📸 Lihat
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">Tidak ada</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php 
                                    $jarak = (float)($abs['jarak_m'] ?? 0);
                                    if ($jarak <= 150) {
                                        echo '<span class="status-badge status-success">✅ Dalam Radius</span>';
                                    } else {
                                        echo '<span class="status-badge status-warning">⚠️ Luar Radius</span>';
                                    }
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 20px;
    margin-top: 20px;
}

.card {
    background: white;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 15px;
}

.info-item {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.info-item label {
    font-weight: bold;
    color: #666;
    font-size: 0.9rem;
}

.info-value {
    color: #333;
    font-size: 1rem;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 20px;
    margin-top: 15px;
}

.stat-item {
    text-align: center;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 8px;
    border: 1px solid #e9ecef;
}

.stat-number {
    font-size: 2rem;
    font-weight: bold;
    color: #007bff;
    margin-bottom: 5px;
}

.stat-label {
    color: #666;
    font-size: 0.9rem;
}

.table-responsive {
    overflow-x: auto;
}

.table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
}

.table th,
.table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #e9ecef;
}

.table th {
    background: #f8f9fa;
    font-weight: bold;
    color: #495057;
}

.nis-badge {
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

.role-badge {
    background: #6c757d;
    color: white;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: bold;
}

.status-indicators {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.status-badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: bold;
    text-align: center;
}

.status-success {
    background: #d4edda;
    color: #155724;
}

.status-warning {
    background: #fff3cd;
    color: #856404;
}

.location-info {
    font-size: 0.8rem;
    line-height: 1.4;
}

.empty-state {
    text-align: center;
    padding: 40px;
    color: #666;
}

.empty-state p {
    margin: 0;
    font-size: 1.1rem;
}

.action-buttons {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
}

.btn {
    display: inline-block;
    padding: 8px 16px;
    text-decoration: none;
    border-radius: 4px;
    font-size: 0.9rem;
    font-weight: 500;
    text-align: center;
    cursor: pointer;
    border: none;
    transition: all 0.3s ease;
}

.btn-primary {
    background: #007bff;
    color: white;
}

.btn-primary:hover {
    background: #0056b3;
}

.btn-secondary {
    background: #6c757d;
    color: white;
}

.btn-secondary:hover {
    background: #545b62;
}

.btn-sm {
    padding: 4px 8px;
    font-size: 0.8rem;
}

.btn-outline-primary {
    background: transparent;
    color: #007bff;
    border: 1px solid #007bff;
}

.btn-outline-primary:hover {
    background: #007bff;
    color: white;
}

.text-muted {
    color: #6c757d;
}

@media (max-width: 768px) {
    .info-grid {
        grid-template-columns: 1fr;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .action-buttons {
        flex-direction: column;
    }
}
</style>
