<h2>Dashboard Admin</h2>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-number"><?= $totalSiswa ?></div>
        <div class="stat-label">Total Siswa</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-number"><?= $totalTempat ?></div>
        <div class="stat-label">Total Tempat PKL</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-number"><?= $totalAbsensi ?></div>
        <div class="stat-label">Total Absensi</div>
    </div>
    
    <div class="stat-card realtime-card" id="onlineUsers">
        <div class="stat-number">-</div>
        <div class="stat-label">User Online</div>
        <div class="realtime-indicator">🔄</div>
    </div>
</div>

<div class="action-buttons" style="margin: 20px 0;">
    <a href="?r=admin/absensi" class="btn btn-primary">
        📊 Lihat Semua Absensi
    </a>
    
    <a href="?r=admin/siswa" class="btn btn-success">
        👥 Kelola Siswa
    </a>
    
    <a href="?r=admin/pembimbing" class="btn btn-success">
        👨‍🏫 Kelola Pembimbing
    </a>
    
    <a href="?r=profile" class="btn btn-info">
        ⚙️ Profile Administrator
    </a>
    
    <?php if(!empty($_SESSION['user']) && $_SESSION['user']['role'] === 'admin'): ?>
        <a href="?r=admin/tempat" class="btn btn-secondary">
            🏢 Tempat PKL
        </a>
    <?php endif; ?>
</div>

<div class="dashboard-grid">
    <!-- Absensi Terbaru -->
    <div class="card">
        <div class="card-header">
            <h3>📱 Absensi Terbaru (Realtime)</h3>
            <div class="realtime-controls">
                <span class="last-update" id="lastUpdate">Update: -</span>
                <button class="btn btn-sm btn-outline-primary" onclick="toggleRealtime()" id="toggleBtn">
                    🔴 Stop Realtime
                </button>
            </div>
        </div>
        
        <div class="realtime-content">
            <?php if(empty($absensiTerbaru)): ?>
                <div class="alert alert-info">
                    <strong>ℹ️ Info:</strong> Belum ada data absensi. 
                    Siswa akan muncul di sini saat melakukan absensi.
                </div>
            <?php else: ?>
                <div class="absensi-list" id="absensiList">
                    <?php foreach($absensiTerbaru as $abs): ?>
                        <div class="absensi-item" data-id="<?= $abs['id'] ?>">
                            <div class="absensi-header">
                                <div class="siswa-info">
                                    <strong><?= htmlspecialchars($abs['siswa']) ?></strong>
                                    <span class="nis">(<?= htmlspecialchars($abs['nis']) ?>)</span>
                                    <span class="kelas"><?= htmlspecialchars($abs['kelas']) ?></span>
                                </div>
                                <div class="waktu-info">
                                    <span class="time"><?= date('H:i', strtotime($abs['waktu'])) ?></span>
                                    <span class="date"><?= date('d/m/Y', strtotime($abs['waktu'])) ?></span>
                                </div>
                            </div>
                            
                            <div class="absensi-details">
                                <div class="location-info">
                                    <span class="coordinate">
                                        📍 <?= number_format($abs['lat'], 6) ?>, <?= number_format($abs['lng'], 6) ?>
                                    </span>
                                    <span class="distance">
                                        📏 <?= number_format($abs['jarak_m'], 1) ?>m
                                    </span>
                                </div>
                                
                                <div class="tempat-info">
                                    <span class="tempat">
                                        🏢 <?= htmlspecialchars($abs['tempat_pkl'] ?? 'Tidak ada') ?>
                                    </span>
                                </div>
                                
                                <?php if(!empty($abs['selfie_path'])): ?>
                                    <div class="selfie-info">
                                        <a href="<?= htmlspecialchars($abs['selfie_path']) ?>" 
                                           target="_blank" class="btn btn-sm btn-outline-primary">
                                            📸 Lihat Selfie
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="status-badge">
                                <?php 
                                    $jarak = (float)($abs['jarak_m'] ?? 0);
                                    if ($jarak <= 150) {
                                        echo '<span class="badge badge-success">✅ Dalam Radius</span>';
                                    } else {
                                        echo '<span class="badge badge-warning">⚠️ Luar Radius</span>';
                                    }
                                ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Statistik Cepat -->
    <div class="card">
        <h3>📈 Statistik Cepat</h3>
        
        <div class="quick-stats">
            <div class="stat-item">
                <div class="stat-label">Hari Ini</div>
                <div class="stat-value" id="todayCount">-</div>
            </div>
            
            <div class="stat-item">
                <div class="stat-label">Minggu Ini</div>
                <div class="stat-value" id="weekCount">-</div>
            </div>
            
            <div class="stat-item">
                <div class="stat-label">Bulan Ini</div>
                <div class="stat-value" id="monthCount">-</div>
            </div>
        </div>
        
        <div class="chart-placeholder">
            <div class="chart-info">
                <h4>📊 Grafik Absensi</h4>
                <p>Grafik akan ditampilkan di sini</p>
                <a href="?r=admin/absensi" class="btn btn-sm btn-primary">
                    Lihat Detail
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Realtime Script -->
<script>
let realtimeInterval;
let isRealtimeActive = true;

function startRealtime() {
    isRealtimeActive = true;
    document.getElementById('toggleBtn').innerHTML = '🔴 Stop Realtime';
    document.getElementById('toggleBtn').className = 'btn btn-sm btn-outline-danger';
    
    // Update setiap 10 detik
    realtimeInterval = setInterval(updateRealtimeData, 10000);
    
    // Update pertama kali
    updateRealtimeData();
}

function stopRealtime() {
    isRealtimeActive = false;
    document.getElementById('toggleBtn').innerHTML = '🟢 Start Realtime';
    document.getElementById('toggleBtn').className = 'btn btn-sm btn-outline-success';
    
    if (realtimeInterval) {
        clearInterval(realtimeInterval);
    }
}

function toggleRealtime() {
    if (isRealtimeActive) {
        stopRealtime();
    } else {
        startRealtime();
    }
}

function updateRealtimeData() {
    fetch('?r=admin/absensiRealtime')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update statistik
                document.getElementById('onlineUsers').querySelector('.stat-number').textContent = data.data.online_users;
                document.getElementById('todayCount').textContent = data.data.total_hari_ini;
                document.getElementById('weekCount').textContent = data.data.total_minggu_ini;
                document.getElementById('lastUpdate').textContent = 'Update: ' + data.data.last_update;
                
                // Update absensi terbaru jika ada
                if (data.data.recent_absensi.length > 0) {
                    updateAbsensiList(data.data.recent_absensi);
                }
            }
        })
        .catch(error => {
            console.error('Error updating realtime data:', error);
        });
}

function updateAbsensiList(absensiData) {
    const absensiList = document.getElementById('absensiList');
    if (!absensiList) return;
    
    // Update atau tambah absensi baru
    absensiData.forEach(abs => {
        const existingItem = document.querySelector(`[data-id="${abs.id}"]`);
        if (!existingItem) {
            // Tambah absensi baru di atas
            const newItem = createAbsensiItem(abs);
            absensiList.insertBefore(newItem, absensiList.firstChild);
            
            // Hapus item lama jika lebih dari 10
            const items = absensiList.querySelectorAll('.absensi-item');
            if (items.length > 10) {
                items[items.length - 1].remove();
            }
            
            // Tambah animasi
            newItem.style.animation = 'slideInDown 0.5s ease-out';
        }
    });
}

function createAbsensiItem(abs) {
    const div = document.createElement('div');
    div.className = 'absensi-item';
    div.setAttribute('data-id', abs.id);
    
    const jarak = parseFloat(abs.jarak_m || 0);
    const statusBadge = jarak <= 150 ? 
        '<span class="badge badge-success">✅ Dalam Radius</span>' : 
        '<span class="badge badge-warning">⚠️ Luar Radius</span>';
    
    div.innerHTML = `
        <div class="absensi-header">
            <div class="siswa-info">
                <strong>${abs.siswa}</strong>
                <span class="nis">(${abs.nis})</span>
                <span class="kelas">${abs.kelas}</span>
            </div>
            <div class="waktu-info">
                <span class="time">${new Date(abs.waktu).toLocaleTimeString('id-ID', {hour: '2-digit', minute: '2-digit'})}</span>
                <span class="date">${new Date(abs.waktu).toLocaleDateString('id-ID')}</span>
            </div>
        </div>
        
        <div class="absensi-details">
            <div class="location-info">
                <span class="coordinate">
                    📍 ${parseFloat(abs.lat).toFixed(6)}, ${parseFloat(abs.lng).toFixed(6)}
                </span>
                <span class="distance">
                    📏 ${parseFloat(abs.jarak_m).toFixed(1)}m
                </span>
            </div>
            
            <div class="tempat-info">
                <span class="tempat">
                    🏢 ${abs.tempat_pkl || 'Tidak ada'}
                </span>
            </div>
        </div>
        
        <div class="status-badge">
            ${statusBadge}
        </div>
    `;
    
    return div;
}

// Start realtime saat halaman dimuat
document.addEventListener('DOMContentLoaded', function() {
    startRealtime();
    
    // Update statistik bulan ini
    const currentMonth = new Date().getMonth() + 1;
    const currentYear = new Date().getFullYear();
    document.getElementById('monthCount').textContent = '<?= count(array_filter($absensiTerbaru, function($a) { return date("n", strtotime($a["waktu"])) == date("n"); })) ?>';
});
</script>

<style>
.dashboard-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 20px;
    margin-top: 20px;
}

.realtime-controls {
    display: flex;
    align-items: center;
    gap: 15px;
}

.last-update {
    font-size: 0.9rem;
    color: #6c757d;
}

.realtime-indicator {
    font-size: 1.2rem;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.5; }
    100% { opacity: 1; }
}

.absensi-list {
    max-height: 500px;
    overflow-y: auto;
}

.absensi-item {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 10px;
    transition: all 0.3s ease;
}

.absensi-item:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transform: translateY(-2px);
}

.absensi-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.siswa-info {
    display: flex;
    align-items: center;
    gap: 10px;
}

.nis {
    color: #6c757d;
    font-size: 0.9rem;
}

.kelas {
    background: #e9ecef;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 0.8rem;
}

.waktu-info {
    text-align: right;
}

.time {
    display: block;
    font-size: 1.1rem;
    font-weight: bold;
    color: #007bff;
}

.date {
    font-size: 0.8rem;
    color: #6c757d;
}

.absensi-details {
    margin-bottom: 10px;
}

.location-info, .tempat-info {
    display: flex;
    gap: 15px;
    margin-bottom: 5px;
    font-size: 0.9rem;
}

.coordinate, .distance, .tempat {
    color: #495057;
}

.status-badge {
    text-align: right;
}

.quick-stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 15px;
    margin-bottom: 20px;
}

.stat-item {
    text-align: center;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
}

.stat-label {
    font-size: 0.9rem;
    color: #6c757d;
    margin-bottom: 5px;
}

.stat-value {
    font-size: 1.5rem;
    font-weight: bold;
    color: #007bff;
}

.chart-placeholder {
    background: #f8f9fa;
    border: 2px dashed #dee2e6;
    border-radius: 8px;
    padding: 40px;
    text-align: center;
}

.chart-info h4 {
    margin-bottom: 10px;
    color: #6c757d;
}

.chart-info p {
    color: #6c757d;
    margin-bottom: 15px;
}

@keyframes slideInDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@media (max-width: 768px) {
    .dashboard-grid {
        grid-template-columns: 1fr;
    }
    
    .quick-stats {
        grid-template-columns: 1fr;
    }
    
    .absensi-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }
    
    .waktu-info {
        text-align: left;
    }
}
</style>