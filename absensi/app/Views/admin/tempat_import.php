<h2>📥 Import Tempat PKL</h2>

<div class="action-buttons" style="margin-bottom: 20px;">
    <a href="?r=admin/tempat" class="btn btn-secondary">
        ↩️ Kembali ke Daftar Tempat PKL
    </a>
</div>

<div class="card">
    <!-- Form Import CSV -->
    <div class="card">
        <h3>📥 Import Data Tempat PKL dari File CSV</h3>
        
        <?php if(isset($error)): ?>
            <div class="alert alert-danger">
                <strong>❌ Error:</strong> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        
        <form method="post" action="?r=admin/tempatImport" enctype="multipart/form-data">
            <?= \App\Helpers\CSRF::field() ?>
            
            <div class="form-group">
                <label for="csv_file">File CSV:</label>
                <input type="file" id="csv_file" name="csv_file" accept=".csv" required class="form-control">
                <small class="form-text">Pilih file CSV yang berisi data tempat PKL</small>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">📥 Upload & Import</button>
                <button type="button" onclick="downloadTemplate()" class="btn btn-outline-info">📋 Download Template</button>
            </div>
        </form>
    </div>
    
    <!-- Panduan Format CSV -->
    <div class="card">
        <h3>📋 Panduan Format CSV</h3>
        
        <div class="alert alert-info">
            <strong>ℹ️ Format yang Diperlukan:</strong><br>
            File CSV harus memiliki header dan minimal 4 kolom dengan urutan sebagai berikut:
        </div>
        
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kolom</th>
                        <th>Deskripsi</th>
                        <th>Contoh</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td><strong>Nama Tempat</strong></td>
                        <td>Nama lengkap tempat PKL</td>
                        <td>PT. Maju Bersama</td>
                        <td><span class="badge badge-success">Wajib</span></td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td><strong>Pemilik</strong></td>
                        <td>Nama pemilik tempat PKL</td>
                        <td>John Doe</td>
                        <td><span class="badge badge-warning">Opsional</span></td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td><strong>Alamat</strong></td>
                        <td>Alamat lengkap tempat PKL</td>
                        <td>Jl. Sudirman No. 123, Jakarta Pusat</td>
                        <td><span class="badge badge-warning">Opsional</span></td>
                    </tr>
                    <tr>
                        <td>4</td>
                        <td><strong>Koordinat</strong></td>
                        <td>Latitude,Longitude (pisahkan dengan koma atau titik koma)</td>
                        <td>-6.2088,106.8456</td>
                        <td><span class="badge badge-success">Wajib</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div class="alert alert-warning">
            <strong>⚠️ Catatan Penting:</strong>
            <ul>
                <li>Baris pertama adalah header (akan diabaikan)</li>
                <li>Gunakan koma (,) atau titik koma (;) untuk memisahkan koordinat</li>
                <li>Format koordinat: latitude,longitude (contoh: -6.2088,106.8456)</li>
                <li>Latitude: -90 sampai 90, Longitude: -180 sampai 180</li>
                <li>Radius default: 150 meter</li>
                <li>Ukuran file maksimal: 5MB</li>
            </ul>
        </div>
    </div>
    
    <!-- Contoh Data CSV -->
    <div class="card">
        <h3>📝 Contoh Data CSV</h3>
        
        <div class="code-block">
            <pre><code>Nama Tempat,Pemilik,Alamat,Koordinat
PT. Maju Bersama,John Doe,Jl. Sudirman No. 123 Jakarta Pusat,-6.2088,106.8456
CV. Sukses Mandiri,Jane Smith,Jl. Thamrin No. 45 Jakarta Selatan,-6.1865,106.8223
UD. Makmur Jaya,Ahmad Rizki,Jl. Gatot Subroto No. 67 Jakarta Barat,-6.1751,106.8272</code></pre>
        </div>
        
        <div class="alert alert-success">
            <strong>✅ Tips:</strong>
            <ul>
                <li>Gunakan aplikasi spreadsheet seperti Excel atau Google Sheets</li>
                <li>Simpan sebagai format CSV (Comma Separated Values)</li>
                <li>Pastikan encoding UTF-8 untuk karakter khusus</li>
                <li>Test dengan data kecil terlebih dahulu</li>
            </ul>
        </div>
    </div>
</div>

<script>
function downloadTemplate() {
    // Buat template CSV
    const csvContent = "Nama Tempat,Pemilik,Alamat,Koordinat\nPT. Maju Bersama,John Doe,Jl. Sudirman No. 123 Jakarta Pusat,-6.2088,106.8456\nCV. Sukses Mandiri,Jane Smith,Jl. Thamrin No. 45 Jakarta Selatan,-6.1865,106.8223";
    
    // Buat blob dan download
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    
    if (link.download !== undefined) {
        const url = URL.createObjectURL(blob);
        link.setAttribute('href', url);
        link.setAttribute('download', 'template_tempat_pkl.csv');
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
}
</script>

<style>
.code-block {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 4px;
    padding: 15px;
    margin: 15px 0;
}

.code-block pre {
    margin: 0;
    font-family: 'Courier New', monospace;
    font-size: 14px;
    line-height: 1.4;
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

.badge-warning {
    background-color: #ffc107;
    color: #212529;
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

.form-control {
    width: 100%;
    padding: 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
}

.form-control:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 0 2px rgba(0,123,255,0.25);
}

.form-text {
    font-size: 12px;
    color: #6c757d;
    margin-top: 4px;
}

.form-actions {
    margin-top: 20px;
    display: flex;
    gap: 10px;
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

.btn-outline-info {
    background-color: transparent;
    color: #17a2b8;
    border: 1px solid #17a2b8;
}

.btn-outline-info:hover {
    background-color: #17a2b8;
    color: white;
}

.alert {
    padding: 15px;
    margin-bottom: 20px;
    border: 1px solid transparent;
    border-radius: 4px;
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

.alert-warning {
    color: #856404;
    background-color: #fff3cd;
    border-color: #ffeaa7;
}

.alert-success {
    color: #155724;
    background-color: #d4edda;
    border-color: #c3e6cb;
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
</style>
