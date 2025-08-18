<h2>✏️ Edit Tempat PKL</h2>

<div class="action-buttons" style="margin-bottom: 20px;">
    <a href="?r=admin/tempat" class="btn btn-secondary">
        ↩️ Kembali ke Daftar Tempat PKL
    </a>
</div>

<div class="card">
    <h3>✏️ Edit Data Tempat PKL</h3>
    
    <?php if(isset($error)): ?>
        <div class="alert alert-danger">
            <strong>❌ Error:</strong> <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>
    
    <form method="post" action="?r=admin/tempatEdit&id=<?= $tempat['id'] ?>">
        <?= \App\Helpers\CSRF::field() ?>
        
        <div class="form-group">
            <label for="nama">Nama Tempat PKL:</label>
            <input type="text" id="nama" name="nama" required 
                   value="<?= htmlspecialchars($tempat['nama']) ?>"
                   placeholder="Contoh: PT. Maju Bersama" class="form-control">
        </div>
        
        <div class="form-group">
            <label for="lat">Latitude:</label>
            <input type="number" id="lat" name="lat" step="any" required 
                   value="<?= htmlspecialchars($tempat['lat']) ?>"
                   placeholder="Contoh: -6.2088" class="form-control">
            <small class="form-text">Gunakan format desimal (contoh: -6.2088)</small>
        </div>
        
        <div class="form-group">
            <label for="lng">Longitude:</label>
            <input type="number" id="lng" name="lng" step="any" required 
                   value="<?= htmlspecialchars($tempat['lng']) ?>"
                   placeholder="Contoh: 106.8456" class="form-control">
            <small class="form-text">Gunakan format desimal (contoh: 106.8456)</small>
        </div>
        
        <div class="form-group">
            <label for="radius_m">Radius (meter):</label>
            <input type="number" id="radius_m" name="radius_m" value="<?= (int)$tempat['radius_m'] ?>" required 
                   min="50" max="1000" class="form-control">
            <small class="form-text">Jarak maksimal siswa dari lokasi (50-1000 meter)</small>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">💾 Update Data</button>
            <a href="?r=admin/tempat" class="btn btn-secondary">❌ Batal</a>
        </div>
    </form>
</div>

<div class="card">
    <h3>🗺️ Preview Lokasi</h3>
    <div id="map" style="height: 400px; width: 100%; border-radius: 8px;"></div>
    <small class="form-text">Peta akan menampilkan lokasi dan radius tempat PKL</small>
</div>

<script>
// Load Leaflet CSS dan JS
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
    leafletJS.onload = initMap;
    document.head.appendChild(leafletJS);
} else {
    initMap();
}

function initMap() {
    const lat = <?= (float)$tempat['lat'] ?>;
    const lng = <?= (float)$tempat['lng'] ?>;
    const radius = <?= (int)$tempat['radius_m'] ?>;
    const nama = '<?= htmlspecialchars($tempat['nama']) ?>';
    
    const map = L.map('map').setView([lat, lng], 15);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetmap contributors'
    }).addTo(map);
    
    // Marker untuk tempat PKL
    const marker = L.marker([lat, lng]).addTo(map);
    marker.bindPopup(`<b>${nama}</b><br>Lokasi: ${lat}, ${lng}`).openPopup();
    
    // Circle untuk radius
    const circle = L.circle([lat, lng], {
        color: 'red',
        fillColor: '#f03',
        fillOpacity: 0.2,
        radius: radius
    }).addTo(map);
    
    // Popup untuk radius
    circle.bindPopup(`Radius: ${radius} meter`);
    
    // Update map when form inputs change
    document.getElementById('lat').addEventListener('change', updateMap);
    document.getElementById('lng').addEventListener('change', updateMap);
    document.getElementById('radius_m').addEventListener('change', updateMap);
    
    function updateMap() {
        const newLat = parseFloat(document.getElementById('lat').value);
        const newLng = parseFloat(document.getElementById('lng').value);
        const newRadius = parseInt(document.getElementById('radius_m').value);
        
        if (newLat && newLng) {
            map.setView([newLat, newLng], 15);
            marker.setLatLng([newLat, newLng]);
            circle.setLatLng([newLat, newLng]);
            circle.setRadius(newRadius);
        }
    }
}
</script>

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

.form-control {
    width: 100%;
    padding: 12px;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 14px;
    transition: border-color 0.3s ease;
}

.form-control:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 0 2px rgba(0,123,255,0.25);
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

@media (max-width: 768px) {
    .form-actions {
        flex-direction: column;
    }
    
    .btn {
        width: 100%;
        text-align: center;
    }
}
</style>
