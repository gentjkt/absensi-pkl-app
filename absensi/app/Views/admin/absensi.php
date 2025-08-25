<h2>📊 Data Absensi Siswa</h2>

<div class="stats-overview">
    <div class="stat-card">
        <div class="stat-number"><?= $totalAbsensi ?></div>
        <div class="stat-label">Total Absensi</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-number"><?= $totalHariIni ?></div>
        <div class="stat-label">Hari Ini</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-number"><?= $totalMingguIni ?></div>
        <div class="stat-label">Minggu Ini</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-number"><?= ceil($totalAbsensi / max(1, $totalHariIni)) ?></div>
        <div class="stat-label">Rata-rata/Hari</div>
    </div>
</div>

<div class="action-buttons" style="margin: 20px 0;">
    <a href="?r=admin/dashboard" class="btn btn-secondary">
        ↩️ Kembali ke Dashboard
    </a>
    
    <a href="?r=admin/report" class="btn btn-info">
        📈 Laporan & Analisis
    </a>
    
    <button onclick="exportToCSV()" class="btn btn-success">
        📥 Export CSV
    </button>
    
    <button onclick="exportToPDF()" class="btn btn-danger">
        📄 Export PDF
    </button>
    
    <button onclick="printReport()" class="btn btn-warning">
        🖨️ Print
    </button>
</div>

<div class="card">
    <h3>🔍 Filter & Pencarian</h3>
    
            <div class="form-group">
                <label for="searchSiswa">Cari Siswa:</label>
                <input type="text" id="searchSiswa" placeholder="Ketik nama atau NIS siswa..." class="form-control" value="<?= htmlspecialchars($filters['search'] ?? ($_GET['search'] ?? '')) ?>">
            </div>
            
            <div class="form-group">
                <label for="filterStatus">Filter Status:</label>
                <select id="filterStatus" class="form-control">
                    <?php $statusVal = $filters['status'] ?? ($_GET['status'] ?? ''); ?>
                    <option value="" <?= $statusVal === '' ? 'selected' : '' ?>>Semua Status</option>
                    <option value="dalam" <?= $statusVal === 'dalam' ? 'selected' : '' ?>>Dalam Radius</option>
                    <option value="luar" <?= $statusVal === 'luar' ? 'selected' : '' ?>>Luar Radius</option>
                </select>
            </div>
  
            <div class="form-group">
               <label for="filterDate">Filter Tanggal:</label>
               <input type="date" id="filterDate" class="form-control" value="<?= htmlspecialchars($filters['date'] ?? ($_GET['date'] ?? '')) ?>">
            </div>
  
            <button type="button" class="btn btn-primary" onclick="filterData()">
               🔍 Terapkan Filter
            </button>
            <button type="button" class="btn btn-secondary" onclick="resetFilter()">
               🔄 Reset Filter
            </button>
              

</div>

<div class="card">
    <h3>📋 Daftar Absensi</h3>
    
    <?php if(empty($absensi)): ?>
        <div class="alert alert-info">
            <strong>ℹ️ Info:</strong> Tidak ada data absensi yang ditemukan.
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table" id="absensiTable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>NIS</th>
                        <th>Nama Siswa</th>
                        <th>Kelas</th>
                        <th>Tempat PKL</th>
                        <th>Waktu Absen</th>
                        <th>Lokasi</th>
                        <th>Jarak</th>
                        <th>Selfie</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php 
                $no = ($currentPage - 1) * $limit + 1;
                foreach($absensi as $abs): 
                ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td>
                            <strong><?= htmlspecialchars($abs['nis']) ?></strong>
                        </td>
                        <td>
                            <strong><?= htmlspecialchars($abs['siswa']) ?></strong>
                        </td>
                        <td><?= htmlspecialchars($abs['kelas']) ?></td>
                        <td>
                            <?php if($abs['tempat_pkl']): ?>
                                <span class="badge badge-info">
                                    <?= htmlspecialchars($abs['tempat_pkl']) ?>
                                </span>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="time-info">
                                <span class="time"><?= date('H:i', strtotime($abs['waktu'])) ?></span>
                                <span class="date"><?= date('d/m/Y', strtotime($abs['waktu'])) ?></span>
                            </div>
                        </td>
                        <td>
                            <div class="location-info">
                                <span class="coordinate">
                                    <?= number_format($abs['lat'], 6) ?>, <?= number_format($abs['lng'], 6) ?>
                                </span>
                            </div>
                        </td>
                        <td>
                            <span class="distance-info">
                                <?= number_format($abs['jarak_m'], 1) ?> m
                            </span>
                        </td>
                        <td>
                            <?php if(!empty($abs['selfie_path'])): ?>
                                <a href="<?= htmlspecialchars($abs['selfie_path']) ?>" 
                                   target="_blank" class="btn btn-sm btn-outline-primary">
                                    📸 Lihat
                                </a>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php 
                                $jarak = (float)($abs['jarak_m'] ?? 0);
                                if ($jarak <= 150) {
                                    echo '<span class="badge badge-success">✅ Dalam Radius</span>';
                                } else {
                                    echo '<span class="badge badge-warning">⚠️ Luar Radius</span>';
                                }
                            ?>
                        </td>
                        <td>
                            <div class="action-buttons-small">
                                <a href="?r=admin/absensi/detail&id=<?= $abs['id'] ?>" 
                                   class="btn btn-sm btn-info" title="Detail">
                                    👁️ Detail
                                </a>
                                
                                <button onclick="showMap(<?= $abs['lat'] ?>, <?= $abs['lng'] ?>, '<?= htmlspecialchars($abs['siswa']) ?>')" 
                                        class="btn btn-sm btn-outline-secondary" title="Lihat Peta">
                                    🗺️ Peta
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <?php if($totalPages > 1): ?>
            <div class="pagination-wrapper">
                <div class="pagination-info">
                    Menampilkan <?= (($currentPage - 1) * $limit) + 1 ?> - 
                    <?= min($currentPage * $limit, $totalAbsensi) ?> dari <?= $totalAbsensi ?> data
                </div>
                
                <nav class="pagination">
                    <?php if($currentPage > 1): ?>
                        <a href="?r=admin/absensi&page=<?= $currentPage - 1 ?><?= !empty($_GET['search']) ? '&search=' . urlencode($_GET['search']) : '' ?><?= !empty($_GET['date']) ? '&date=' . urlencode($_GET['date']) : '' ?><?= !empty($_GET['status']) ? '&status=' . urlencode($_GET['status']) : '' ?>" 
                           class="page-link">← Sebelumnya</a>
                    <?php endif; ?>
                    
                    <?php for($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++): ?>
                        <a href="?r=admin/absensi&page=<?= $i ?><?= !empty($_GET['search']) ? '&search=' . urlencode($_GET['search']) : '' ?><?= !empty($_GET['date']) ? '&date=' . urlencode($_GET['date']) : '' ?><?= !empty($_GET['status']) ? '&status=' . urlencode($_GET['status']) : '' ?>" 
                           class="page-link <?= $i == $currentPage ? 'active' : '' ?>"><?= $i ?></a>
                    <?php endfor; ?>
                    
                    <?php if($currentPage < $totalPages): ?>
                        <a href="?r=admin/absensi&page=<?= $currentPage + 1 ?><?= !empty($_GET['search']) ? '&search=' . urlencode($_GET['search']) : '' ?><?= !empty($_GET['date']) ? '&date=' . urlencode($_GET['date']) : '' ?><?= !empty($_GET['status']) ? '&status=' . urlencode($_GET['status']) : '' ?>" 
                           class="page-link">Selanjutnya →</a>
                    <?php endif; ?>
                </nav>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<!-- Modal Peta -->
<div id="mapModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>🗺️ Lokasi Siswa</h3>
            <span class="close" onclick="closeMap()">&times;</span>
        </div>
        <div class="modal-body">
            <div id="map" style="height: 400px; width: 100%;"></div>
        </div>
    </div>
</div>

<script>
function filterData() {
	// Ambil nilai filter
	const search = document.getElementById('searchSiswa').value.trim();
	const status = document.getElementById('filterStatus').value;
	const date = document.getElementById('filterDate').value;
	
	// Bangun URL dengan query string
	const params = new URLSearchParams();
	params.set('r', 'admin/absensi');
	if (search) params.set('search', search);
	if (status) params.set('status', status);
	if (date) params.set('date', date);
	// Reset halaman ke 1 saat menerapkan filter baru
	params.set('page', '1');
	
	window.location.href = `?${params.toString()}`;
}

function resetFilter() {
	document.getElementById('searchSiswa').value = '';
	document.getElementById('filterStatus').value = '';
	document.getElementById('filterDate').value = '';
	
	// Redirect tanpa parameter filter
	window.location.href = '?r=admin/absensi';
}

function exportToCSV() {
    const table = document.getElementById('absensiTable');
    const rows = table.querySelectorAll('tbody tr');
    
    let csv = 'NIS,Nama Siswa,Kelas,Tempat PKL,Waktu Absen,Lokasi,Jarak,Status\n';
    
    rows.forEach(row => {
        const cells = row.querySelectorAll('td');
        const rowData = [];
        
        // NIS
        rowData.push(cells[1].textContent.trim());
        
        // Nama Siswa
        rowData.push(cells[2].textContent.trim());
        
        // Kelas
        rowData.push(cells[3].textContent.trim());
        
        // Tempat PKL
        rowData.push(cells[4].textContent.trim());
        
        // Waktu Absen
        rowData.push(cells[5].textContent.trim());
        
        // Lokasi
        rowData.push(cells[6].textContent.trim());
        
        // Jarak
        rowData.push(cells[7].textContent.trim());
        
        // Status
        rowData.push(cells[9].textContent.trim());
        
        csv += rowData.map(cell => `"${cell}"`).join(',') + '\n';
    });
    
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    link.setAttribute('href', url);
    link.setAttribute('download', `absensi_${new Date().toISOString().split('T')[0]}.csv`);
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function exportToPDF() {
    // Load jsPDF library
    if (typeof jsPDF === 'undefined') {
        const script = document.createElement('script');
        script.src = 'https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js';
        script.onload = () => generatePDF();
        document.head.appendChild(script);
    } else {
        generatePDF();
    }
}

function generatePDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();
    
    // Header
    doc.setFontSize(20);
    doc.text('LAPORAN ABSENSI SISWA', 105, 20, { align: 'center' });
    
    doc.setFontSize(12);
    doc.text(`Tanggal: ${new Date().toLocaleDateString('id-ID')}`, 20, 35);
    doc.text(`Total Data: ${document.querySelectorAll('#absensiTable tbody tr').length}`, 20, 45);
    
    // Table headers
    const headers = ['No', 'NIS', 'Nama', 'Kelas', 'Tempat PKL', 'Waktu', 'Status'];
    const startY = 60;
    let currentY = startY;
    
    // Draw table headers
    doc.setFontSize(10);
    doc.setFillColor(240, 240, 240);
    let x = 20;
    const colWidths = [15, 25, 40, 25, 35, 30, 20];
    
    headers.forEach((header, index) => {
        doc.rect(x, currentY - 5, colWidths[index], 8, 'F');
        doc.text(header, x + 2, currentY);
        x += colWidths[index];
    });
    
    currentY += 10;
    
    // Table data
    const table = document.getElementById('absensiTable');
    const rows = table.querySelectorAll('tbody tr');
    
    rows.forEach((row, rowIndex) => {
        if (currentY > 270) {
            doc.addPage();
            currentY = 20;
        }
        
        const cells = row.querySelectorAll('td');
        x = 20;
        
        // No
        doc.text((rowIndex + 1).toString(), x + 5, currentY);
        x += colWidths[0];
        
        // NIS
        doc.text(cells[1].textContent.trim(), x + 2, currentY);
        x += colWidths[1];
        
        // Nama (truncate if too long)
        const nama = cells[2].textContent.trim();
        doc.text(nama.length > 15 ? nama.substring(0, 15) + '...' : nama, x + 2, currentY);
        x += colWidths[2];
        
        // Kelas
        doc.text(cells[3].textContent.trim(), x + 2, currentY);
        x += colWidths[3];
        
        // Tempat PKL (truncate if too long)
        const tempat = cells[4].textContent.trim();
        doc.text(tempat.length > 12 ? tempat.substring(0, 12) + '...' : tempat, x + 2, currentY);
        x += colWidths[4];
        
        // Waktu
        doc.text(cells[5].textContent.trim(), x + 2, currentY);
        x += colWidths[5];
        
        // Status
        doc.text(cells[9].textContent.trim(), x + 2, currentY);
        
        currentY += 8;
    });
    
    // Footer
    doc.setFontSize(10);
    doc.text(`Dicetak pada: ${new Date().toLocaleString('id-ID')}`, 20, 280);
    
    // Save PDF
    doc.save(`laporan_absensi_${new Date().toISOString().split('T')[0]}.pdf`);
}

function printReport() {
    // Create a new window for printing
    const printWindow = window.open('', '_blank');
    const table = document.getElementById('absensiTable');
    
    // Get table data
    const rows = table.querySelectorAll('tbody tr');
    let printContent = `
        <!DOCTYPE html>
        <html>
        <head>
            <title>Laporan Absensi Siswa</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                .header { text-align: center; margin-bottom: 30px; }
                .header h1 { color: #2c3e50; margin-bottom: 10px; }
                .header p { color: #7f8c8d; margin: 5px 0; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #f8f9fa; font-weight: bold; }
                .footer { margin-top: 30px; text-align: right; color: #7f8c8d; }
                @media print {
                    .no-print { display: none; }
                }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>LAPORAN ABSENSI SISWA</h1>
                <p>Tanggal: ${new Date().toLocaleDateString('id-ID')}</p>
                <p>Total Data: ${rows.length}</p>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>NIS</th>
                        <th>Nama Siswa</th>
                        <th>Kelas</th>
                        <th>Tempat PKL</th>
                        <th>Waktu Absen</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
    `;
    
    rows.forEach((row, index) => {
        const cells = row.querySelectorAll('td');
        printContent += `
            <tr>
                <td>${index + 1}</td>
                <td>${cells[1].textContent.trim()}</td>
                <td>${cells[2].textContent.trim()}</td>
                <td>${cells[3].textContent.trim()}</td>
                <td>${cells[4].textContent.trim()}</td>
                <td>${cells[5].textContent.trim()}</td>
                <td>${cells[9].textContent.trim()}</td>
            </tr>
        `;
    });
    
    printContent += `
                </tbody>
            </table>
            
            <div class="footer">
                <p>Dicetak pada: ${new Date().toLocaleString('id-ID')}</p>
            </div>
        </body>
        </html>
    `;
    
    printWindow.document.write(printContent);
    printWindow.document.close();
    
    // Wait for content to load then print
    printWindow.onload = function() {
        printWindow.print();
        printWindow.close();
    };
}

function showMap(lat, lng, namaSiswa) {
    const modal = document.getElementById('mapModal');
    modal.style.display = 'block';
    
    // Load Leaflet CSS dan JS jika belum ada
    if (!document.querySelector('#leaflet-css')) {
        const leafletCSS = document.createElement('link');
        leafletCSS.id = 'leaflet-css';
        leafletCSS.rel = 'stylesheet';
        leafletCSS.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
        document.head.appendChild(leafletCSS);
    }
    
    if (!document.querySelector('#leaflet-js')) {
        const leafletJS = document.createElement('script');
        leafletJS.id = 'leaflet-js';
        leafletJS.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
        leafletJS.onload = () => initMap(lat, lng, namaSiswa);
        document.head.appendChild(leafletJS);
    } else {
        initMap(lat, lng, namaSiswa);
    }
}

function initMap(lat, lng, namaSiswa) {
    const map = L.map('map').setView([lat, lng], 15);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);
    
    const marker = L.marker([lat, lng]).addTo(map);
    marker.bindPopup(`<b>${namaSiswa}</b><br>Lokasi: ${lat}, ${lng}`).openPopup();
}

function closeMap() {
    const modal = document.getElementById('mapModal');
    modal.style.display = 'none';
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('mapModal');
    if (event.target === modal) {
        modal.style.display = 'none';
    }
}
</script>

<style>
.stats-overview {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 20px;
}

.filter-form {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    border: 1px solid #dee2e6;
}

.form-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    align-items: end;
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

.time-info .time {
    display: block;
    font-weight: bold;
    color: #007bff;
}

.time-info .date {
    font-size: 0.8rem;
    color: #6c757d;
}

.location-info .coordinate {
    font-family: monospace;
    font-size: 0.9rem;
}

.distance-info {
    font-weight: bold;
    color: #28a745;
}

.action-buttons-small {
    display: flex;
    gap: 5px;
}

.pagination-wrapper {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 20px;
    padding: 20px 0;
    border-top: 1px solid #dee2e6;
}

.pagination-info {
    color: #6c757d;
    font-size: 0.9rem;
}

.pagination {
    display: flex;
    gap: 5px;
}

.page-link {
    padding: 8px 12px;
    text-decoration: none;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    color: #007bff;
    transition: all 0.3s ease;
}

.page-link:hover {
    background-color: #e9ecef;
}

.page-link.active {
    background-color: #007bff;
    color: white;
    border-color: #007bff;
}

.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
}

.modal-content {
    background-color: white;
    margin: 5% auto;
    padding: 0;
    border-radius: 8px;
    width: 80%;
    max-width: 800px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.3);
}

.modal-header {
    padding: 20px;
    border-bottom: 1px solid #dee2e6;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-header h3 {
    margin: 0;
}

.close {
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
    color: #aaa;
}

.close:hover {
    color: #000;
}

.modal-body {
    padding: 20px;
}

@media print {
    .action-buttons, .filter-form, .pagination-wrapper {
        display: none;
    }
    
    .card {
        border: none;
        box-shadow: none;
    }
}

@media (max-width: 768px) {
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .pagination-wrapper {
        flex-direction: column;
        gap: 15px;
    }
    
    .modal-content {
        width: 95%;
        margin: 10% auto;
    }
}
</style>
