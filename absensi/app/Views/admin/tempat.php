<h2>🏢 Kelola Tempat PKL</h2>

<div class="action-buttons" style="margin-bottom: 20px;">
    <a href="?r=admin/dashboard" class="btn btn-secondary">
        ↩️ Kembali ke Dashboard
    </a>
    
    <button onclick="showAddForm()" class="btn btn-primary">
        ➕ Tambah Tempat PKL
    </button>
    
    <a href="?r=admin/tempatImport" class="btn btn-success">
        📥 Import Tempat PKL
    </a>
</div>

<!-- Pesan Sukses/Error -->
<?php if(isset($_GET['success'])): ?>
    <?php 
    $message = '';
    $type = 'success';
    switch($_GET['success']) {
        case '1': $message = 'Tempat PKL berhasil ditambahkan!'; break;
        case '2': $message = 'Data tempat PKL berhasil diupdate!'; break;
        case '3': $message = 'Tempat PKL berhasil dihapus!'; break;
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
    <!-- Form Tambah Tempat PKL -->
    <div class="card" id="addForm" style="display: none;">
        <h3>➕ Tambah Tempat PKL Baru</h3>
        <form method="post" action="?r=admin/tempatAdd">
            <?= \App\Helpers\CSRF::field() ?>
            
            <div class="form-group">
                <label for="nama">Nama Tempat PKL:</label>
                <input type="text" id="nama" name="nama" required 
                       placeholder="Contoh: PT. Maju Bersama" class="form-control">
            </div>
            
            <div class="form-group">
                <label for="pemilik">Pemilik:</label>
                <input type="text" id="pemilik" name="pemilik" 
                       placeholder="Contoh: John Doe" class="form-control">
                <small class="form-text">Nama pemilik tempat PKL (opsional)</small>
            </div>
            
            <div class="form-group">
                <label for="alamat">Alamat:</label>
                <textarea id="alamat" name="alamat" rows="3" 
                          placeholder="Contoh: Jl. Sudirman No. 123, Jakarta Pusat" class="form-control"></textarea>
                <small class="form-text">Alamat lengkap tempat PKL (opsional)</small>
            </div>
            
            <div class="form-group">
                <label for="lat">Latitude:</label>
                <input type="number" id="lat" name="lat" step="any" required 
                       placeholder="Contoh: -6.2088" class="form-control">
                <small class="form-text">Gunakan format desimal (contoh: -6.2088)</small>
            </div>
            
            <div class="form-group">
                <label for="lng">Longitude:</label>
                <input type="number" id="lng" name="lng" step="any" required 
                       placeholder="Contoh: 106.8456" class="form-control">
                <small class="form-text">Gunakan format desimal (contoh: 106.8456)</small>
            </div>
            
            <div class="form-group">
                <label for="radius_m">Radius (meter):</label>
                <input type="number" id="radius_m" name="radius_m" value="150" required 
                       min="50" max="1000" class="form-control">
                <small class="form-text">Jarak maksimal siswa dari lokasi (50-1000 meter)</small>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">💾 Simpan</button>
                <button type="button" onclick="hideAddForm()" class="btn btn-secondary">❌ Batal</button>
            </div>
        </form>
    </div>
    
    <!-- Tabel Tempat PKL -->
    <div class="card">
        <h3>📋 Daftar Tempat PKL</h3>
        
        <?php if(empty($tempat)): ?>
            <div class="alert alert-info">
                <strong>ℹ️ Info:</strong> Belum ada data tempat PKL. 
                Silakan tambah tempat PKL baru.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Tempat</th>
                            <th>Pemilik</th>
                            <th>Alamat</th>
                            <th>Koordinat</th>
                            <th>Radius</th>
                            <th>Total Siswa</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php 
                    $no = 1;
                    foreach($tempat as $t): 
                    ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td>
                                <strong><?= \App\Helpers\Response::e($t['nama']) ?></strong>
                            </td>
                            <td>
                                <span class="owner-info"><?= \App\Helpers\Response::e($t['pemilik'] ?? '-') ?></span>
                            </td>
                            <td>
                                <span class="address-info"><?= \App\Helpers\Response::e($t['alamat'] ?? '-') ?></span>
                            </td>
                            <td>
                                <small class="coordinate">
                                    Lat: <?= \App\Helpers\Response::e((string)$t['lat']) ?><br>
                                    Lng: <?= \App\Helpers\Response::e((string)$t['lng']) ?>
                                </small>
                            </td>
                            <td>
                                <span class="radius-badge"><?= (int)$t['radius_m'] ?> m</span>
                            </td>
                            <td>
                                <span class="siswa-count"><?= $t['total_siswa'] ?? 0 ?> siswa</span>
                            </td>
                            <td>
                                <div class="action-buttons-small">
                                    <button onclick="showMap(<?= $t['lat'] ?>, <?= $t['lng'] ?>, '<?= htmlspecialchars($t['nama']) ?>', <?= $t['radius_m'] ?>)" 
                                            class="btn btn-sm btn-outline-info" title="Lihat Peta">
                                        🗺️ Peta
                                    </button>
                                    
                                    <a href="?r=admin/tempatEdit&id=<?= $t['id'] ?>" 
                                       class="btn btn-sm btn-warning" title="Edit">
                                        ✏️ Edit
                                    </a>
                                    
                                    <button onclick="deleteTempat(<?= $t['id'] ?>, '<?= htmlspecialchars($t['nama']) ?>')" 
                                            class="btn btn-sm btn-danger" title="Hapus">
                                        🗑️ Hapus
                                    </button>
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

<!-- Modal Peta -->
<div id="mapModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>🗺️ Lokasi Tempat PKL</h3>
            <span class="close" onclick="closeMap()">&times;</span>
        </div>
        <div class="modal-body">
            <div id="map" style="height: 400px; width: 100%;"></div>
        </div>
    </div>
</div>

<script>
function showAddForm() {
    document.getElementById('addForm').style.display = 'block';
}

function hideAddForm() {
    document.getElementById('addForm').style.display = 'none';
}

function showMap(lat, lng, namaTempat, radius) {
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
        leafletJS.onload = () => initMap(lat, lng, namaTempat, radius);
        document.head.appendChild(leafletJS);
    } else {
        initMap(lat, lng, namaTempat, radius);
    }
}

function initMap(lat, lng, namaTempat, radius) {
    const map = L.map('map').setView([lat, lng], 15);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);
    
    // Marker untuk tempat PKL
    const marker = L.marker([lat, lng]).addTo(map);
    marker.bindPopup(`<b>${namaTempat}</b><br>Lokasi: ${lat}, ${lng}`).openPopup();
    
    // Circle untuk radius
    const circle = L.circle([lat, lng], {
        color: 'red',
        fillColor: '#f03',
        fillOpacity: 0.2,
        radius: radius
    }).addTo(map);
    
    // Popup untuk radius
    circle.bindPopup(`Radius: ${radius} meter`);
}

function closeMap() {
    const modal = document.getElementById('mapModal');
    modal.style.display = 'none';
}

function deleteTempat(id, nama) {
    if (confirm(`Yakin ingin menghapus tempat PKL "${nama}"? Tindakan ini tidak dapat dibatalkan!`)) {
        window.location.href = `?r=admin/tempatDelete&id=${id}`;
    }
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

.coordinate {
    font-family: monospace;
    font-size: 0.9rem;
    color: #495057;
}

.radius-badge {
    background: #28a745;
    color: white;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: bold;
}

.siswa-count {
    color: #007bff;
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

@media (max-width: 768px) {
    .form-actions {
        flex-direction: column;
    }
    
    .modal-content {
        width: 95%;
        margin: 10% auto;
    }
}
</style>