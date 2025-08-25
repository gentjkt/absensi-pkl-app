<h2>📊 Hasil Import Tempat PKL</h2>

<div class="action-buttons" style="margin-bottom: 20px;">
    <a href="?r=admin/tempat" class="btn btn-secondary">
        ↩️ Kembali ke Daftar Tempat PKL
    </a>
    
    <a href="?r=admin/tempatImport" class="btn btn-primary">
        📥 Import Lagi
    </a>
</div>

<!-- Ringkasan Hasil -->
<div class="card">
    <h3>📊 Ringkasan Import</h3>
    
    <div class="stats-grid">
        <div class="stat-item">
            <div class="stat-number"><?= $total_rows ?></div>
            <div class="stat-label">Total Data</div>
        </div>
        
        <div class="stat-item success">
            <div class="stat-number"><?= $success_count ?></div>
            <div class="stat-label">Berhasil</div>
        </div>
        
        <div class="stat-item error">
            <div class="stat-number"><?= $error_count ?></div>
            <div class="stat-label">Gagal</div>
        </div>
        
        <div class="stat-item">
            <div class="stat-number"><?= $total_rows > 0 ? round(($success_count / $total_rows) * 100, 1) : 0 ?>%</div>
            <div class="stat-label">Success Rate</div>
        </div>
    </div>
    
    <?php if($success_count > 0): ?>
        <div class="alert alert-success">
            <strong>✅ Import Berhasil!</strong> 
            <?= $success_count ?> data tempat PKL berhasil diimport ke database.
        </div>
    <?php endif; ?>
    
    <?php if($error_count > 0): ?>
        <div class="alert alert-warning">
            <strong>⚠️ Ada <?= $error_count ?> data yang gagal diimport.</strong> 
            Silakan periksa detail error di bawah dan perbaiki file CSV Anda.
        </div>
    <?php endif; ?>
</div>

<!-- Detail Hasil Import -->
<div class="card">
    <h3>📋 Detail Hasil Import</h3>
    
    <?php if(empty($results)): ?>
        <div class="alert alert-info">
            <strong>ℹ️ Info:</strong> Tidak ada data yang diproses.
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Baris</th>
                        <th>Status</th>
                        <th>Data</th>
                        <th>Pesan</th>
                    </tr>
                </thead>
                <tbody>
                <?php 
                $no = 1;
                foreach($results as $result): 
                ?>
                    <tr class="<?= $result['status'] === 'success' ? 'success-row' : 'error-row' ?>">
                        <td><?= $no++ ?></td>
                        <td>
                            <span class="row-number">Baris <?= $result['row'] ?></span>
                        </td>
                        <td>
                            <?php if($result['status'] === 'success'): ?>
                                <span class="badge badge-success">✅ Berhasil</span>
                            <?php else: ?>
                                <span class="badge badge-danger">❌ Gagal</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($result['status'] === 'success' && isset($result['data'])): ?>
                                <div class="data-preview">
                                    <strong><?= htmlspecialchars($result['data']['nama']) ?></strong><br>
                                    <small>
                                        Pemilik: <?= htmlspecialchars($result['data']['pemilik'] ?: '-') ?><br>
                                        Alamat: <?= htmlspecialchars($result['data']['alamat'] ?: '-') ?><br>
                                        Koordinat: <?= htmlspecialchars($result['data']['lat']) ?>, <?= htmlspecialchars($result['data']['lng']) ?>
                                    </small>
                                </div>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="message <?= $result['status'] === 'success' ? 'success-message' : 'error-message' ?>">
                                <?= htmlspecialchars($result['message']) ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<!-- Rekomendasi -->
<div class="card">
    <h3>💡 Rekomendasi</h3>
    
    <?php if($error_count > 0): ?>
        <div class="alert alert-warning">
            <strong>🔧 Untuk memperbaiki error:</strong>
            <ul>
                <li>Periksa format CSV sesuai panduan</li>
                <li>Pastikan koordinat valid (latitude: -90 sampai 90, longitude: -180 sampai 180)</li>
                <li>Hapus baris kosong atau data yang tidak lengkap</li>
                <li>Gunakan template yang disediakan</li>
            </ul>
        </div>
    <?php endif; ?>
    
    <div class="action-buttons">
        <a href="?r=admin/tempat" class="btn btn-primary">
            👁️ Lihat Data Tempat PKL
        </a>
        
        <a href="?r=admin/tempatImport" class="btn btn-outline-info">
            📥 Import Lagi
        </a>
        
        <button onclick="downloadErrorReport()" class="btn btn-outline-warning" <?= $error_count > 0 ? '' : 'disabled' ?>>
            📋 Download Laporan Error
        </button>
    </div>
</div>

<script>
function downloadErrorReport() {
    // Buat laporan error dalam format CSV
    const errorData = <?= json_encode(array_filter($results, function($r) { return $r['status'] === 'error'; })) ?>;
    
    if (errorData.length === 0) {
        alert('Tidak ada error untuk didownload');
        return;
    }
    
    let csvContent = 'Baris,Status,Pesan\n';
    
    errorData.forEach(function(item) {
        csvContent += `${item.row},Error,"${item.message}"\n`;
    });
    
    // Buat blob dan download
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    
    if (link.download !== undefined) {
        const url = URL.createObjectURL(blob);
        link.setAttribute('href', url);
        link.setAttribute('download', 'error_report_tempat_pkl.csv');
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
}
</script>

<style>
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 20px;
    margin: 20px 0;
}

.stat-item {
    text-align: center;
    padding: 20px;
    border-radius: 8px;
    background: #f8f9fa;
    border: 1px solid #e9ecef;
}

.stat-item.success {
    background: #d4edda;
    border-color: #c3e6cb;
    color: #155724;
}

.stat-item.error {
    background: #f8d7da;
    border-color: #f5c6cb;
    color: #721c24;
}

.stat-number {
    font-size: 2.5em;
    font-weight: bold;
    margin-bottom: 5px;
}

.stat-label {
    font-size: 0.9em;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.success-row {
    background-color: #f8fff9;
}

.error-row {
    background-color: #fff8f8;
}

.row-number {
    font-weight: bold;
    color: #495057;
}

.data-preview {
    line-height: 1.4;
}

.data-preview strong {
    color: #28a745;
}

.data-preview small {
    color: #6c757d;
}

.message {
    font-size: 0.9em;
}

.success-message {
    color: #28a745;
}

.error-message {
    color: #dc3545;
}

.badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: bold;
}

.badge-success {
    background-color: #28a745;
    color: white;
}

.badge-danger {
    background-color: #dc3545;
    color: white;
}

.alert {
    padding: 15px;
    margin-bottom: 20px;
    border: 1px solid transparent;
    border-radius: 4px;
}

.alert-success {
    color: #155724;
    background-color: #d4edda;
    border-color: #c3e6cb;
}

.alert-warning {
    color: #856404;
    background-color: #fff3cd;
    border-color: #ffeaa7;
}

.alert-info {
    color: #0c5460;
    background-color: #d1ecf1;
    border-color: #bee5eb;
}

.table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
}

.table th,
.table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #dee2e6;
}

.table th {
    background-color: #f8f9fa;
    font-weight: bold;
    color: #495057;
}

.table tbody tr:hover {
    background-color: #f8f9fa;
}

.action-buttons {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.btn {
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    text-decoration: none;
    display: inline-block;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.btn-primary {
    background-color: #007bff;
    color: white;
}

.btn-primary:hover:not(:disabled) {
    background-color: #0056b3;
}

.btn-secondary {
    background-color: #6c757d;
    color: white;
}

.btn-secondary:hover:not(:disabled) {
    background-color: #545b62;
}

.btn-outline-info {
    background-color: transparent;
    color: #17a2b8;
    border: 1px solid #17a2b8;
}

.btn-outline-info:hover:not(:disabled) {
    background-color: #17a2b8;
    color: white;
}

.btn-outline-warning {
    background-color: transparent;
    color: #ffc107;
    border: 1px solid #ffc107;
}

.btn-outline-warning:hover:not(:disabled) {
    background-color: #ffc107;
    color: #212529;
}

.text-muted {
    color: #6c757d;
}

@media (max-width: 768px) {
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .action-buttons {
        flex-direction: column;
    }
    
    .table-responsive {
        overflow-x: auto;
    }
}
</style>
