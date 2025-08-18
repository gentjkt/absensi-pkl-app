<h2>Dashboard Siswa</h2>

<?php if(!empty($_SESSION['user']) && $_SESSION['user']['role'] === 'siswa'): ?>
<div class="action-buttons" style="margin-bottom: 20px;">
    <a href="?r=student/dashboard" class="btn btn-info">
        🏠 Home
    </a>
</div>
<?php endif; ?>

<?php if(!$siswa): ?>
  <p>Data siswa belum dikaitkan ke akun ini.</p>
<?php else: ?>
  <div class="grid">
    <div class="card">
      <h3>Data PKL</h3>
      <p><b>Nama:</b> <?= \App\Helpers\Response::e($siswa['nama']) ?></p>
      <p><b>Kelas:</b> <?= \App\Helpers\Response::e($siswa['kelas']) ?></p>
      <p><b>Tempat PKL:</b> <?= \App\Helpers\Response::e($tempat['nama'] ?? '-') ?></p>
      <p><b>Koordinat:</b> <?= \App\Helpers\Response::e(($tempat['lat']??'').', '.($tempat['lng']??'')) ?></p>
         <h3>📱 Absensi</h3>
      <div class="absensi-buttons">
          <?php if (!$sudahDatang): ?>
              <button onclick="showAbsenForm('datang')" class="btn btn-primary btn-large">
                  🚀 Absen Datang
              </button>
          <?php else: ?>
              <button class="btn btn-success btn-large" disabled>
                  ✅ Sudah Absen Datang
              </button>
          <?php endif; ?>
          
          <?php if ($sudahDatang && !$sudahPulang): ?>
              <button onclick="showAbsenForm('pulang')" class="btn btn-warning btn-large">
                  🏠 Absen Pulang
              </button>
          <?php elseif ($sudahPulang): ?>
              <button class="btn btn-success btn-large" disabled>
                  ✅ Sudah Absen Pulang
              </button>
          <?php else: ?>
              <button class="btn btn-secondary btn-large" disabled>
                  ⏳ Harus Absen Datang Dulu
              </button>
          <?php endif; ?>
      </div>

    </div>
    
    <div class="card">
      <h3>Ambil Selfie & Absen</h3>
      
      <!-- Info Selfie Wajib -->
      <div class="selfie-warning" style="background: #fff3cd; border: 1px solid #ffc107; border-radius: 8px; padding: 15px; margin-bottom: 20px;">
        <div style="display: flex; align-items: center; margin-bottom: 10px;">
          <span style="font-size: 1.5rem; margin-right: 10px;">📸</span>
          <strong style="color: #856404;">Selfie Wajib untuk Absensi!</strong>
        </div>
        <p style="color: #856404; margin: 0; font-size: 0.9rem;">
          Foto selfie diperlukan untuk verifikasi kehadiran. Pastikan foto jelas dan terang sebelum mengirim absensi.
        </p>
      </div>
      
      <!-- Camera Section -->
      <div class="camera-section">
        <div class="camera-container">
          <video id="video" autoplay playsinline muted style="width: 100%; max-width: 400px; height: auto; border: 2px solid #ddd; border-radius: 8px;"></video>
          <canvas id="canvas" style="display: none;"></canvas>
          <div class="camera-controls">
            <button type="button" id="snapBtn" class="btn btn-primary">📸 Ambil Selfie</button>
          </div>
        </div>
        
        <!-- Preview Section -->
        <div class="preview-section" style="margin-top: 15px;">
          <h4>Preview Foto:</h4>
          <img id="preview" alt="Preview Selfie" style="display: none; max-width: 100%; height: auto; border: 2px solid #28a745; border-radius: 8px;" />
        </div>
        
        <!-- Form Absen -->
        <form id="absenForm" method="post" action="?r=student/absen" style="margin-top: 20px;">
          <?= \App\Helpers\CSRF::field() ?>
          <input type="hidden" name="lat" id="lat">
          <input type="hidden" name="lng" id="lng">
          <input type="hidden" name="selfie" id="selfie">
          <input type="hidden" name="jenis_absen" id="jenisAbsen" value="">
          <input type="hidden" name="device_time" id="deviceTime">
          <input type="hidden" name="timezone_offset" id="timezoneOffset">
          
          <div class="form-group">
            <label>Jenis Absensi:</label>
            <div id="jenisAbsenDisplay" style="padding: 8px; background: #e3f2fd; border-radius: 4px; margin: 5px 0; font-weight: bold;"></div>
          </div>
          
          <div class="form-group">
            <label>Status Lokasi:</label>
            <div id="locationStatus" style="padding: 8px; background: #f8f9fa; border-radius: 4px; margin: 5px 0;">
              <span id="locationText">Mendapatkan lokasi...</span>
            </div>
          </div>
          
          <div class="form-group">
            <label>Waktu Perangkat:</label>
            <div id="deviceTimeDisplay" style="padding: 8px; background: #e8f5e8; border-radius: 4px; margin: 5px 0; font-family: monospace; font-weight: bold;">
              <span id="deviceTimeText">Memuat waktu...</span>
            </div>
          </div>
          
          <button type="submit" id="submitAbsen" class="btn btn-success btn-block" style="margin-top: 10px;" disabled>
            ⏳ Pilih Jenis Absensi Dulu
          </button>
        </form>
        
        <div class="alert alert-info" style="margin-top: 15px;">
          <strong>💡 Tips:</strong> Pastikan mengizinkan akses lokasi & kamera di browser Anda.
        </div>
      </div>
      
      <!-- Map Section -->
      <div class="map-section" style="margin-top: 20px;">
        <h4>Peta Lokasi PKL:</h4>
        <div id="map" style="height: 300px; border: 2px solid #ddd; border-radius: 8px;"></div>
      </div>
    </div>
  </div>
  
  <div class="card">
    <h3>Riwayat Absensi</h3>
    <?php if(empty($absensi)): ?>
      <p class="text-muted">Belum ada riwayat absensi.</p>
    <?php else: ?>
      <table class="table">
        <thead>
          <tr>
            <th>Tanggal</th>
            <th>Waktu</th>
            <th>Jenis</th>
            <th>Lokasi</th>
            <th>Jarak (m)</th>
            <th>Selfie</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach($absensi as $a): ?>
          <tr>
            <td><?= date('d/m/Y', strtotime($a['waktu'])) ?></td>
            <td><?= date('H:i', strtotime($a['waktu'])) ?></td>
            <td>
              <?php if($a['jenis_absen'] === 'datang'): ?>
                <span class="badge badge-primary">🚀 Datang</span>
              <?php elseif($a['jenis_absen'] === 'pulang'): ?>
                <span class="badge badge-warning">🏠 Pulang</span>
              <?php else: ?>
                <span class="badge badge-secondary">❓ Tidak Diketahui</span>
              <?php endif; ?>
            </td>
            <td>
              <small><?= number_format($a['lat'], 6) ?>, <?= number_format($a['lng'], 6) ?></small>
            </td>
            <td><?= number_format($a['jarak_m'], 1) ?>m</td>
            <td>
              <?php if(!empty($a['selfie_path'])): ?>
                <a target="_blank" href="<?= \App\Helpers\Response::e($a['selfie_path']) ?>" class="btn btn-sm btn-outline-primary">
                  👁️ Lihat
                </a>
              <?php else: ?>
                <span class="text-muted">-</span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
  
  <!-- Pesan Sukses/Error -->
  <?php if(isset($_GET['success'])): ?>
      <?php 
      $message = '';
      $type = 'success';
      switch($_GET['success']) {
          case 'absen_datang': $message = 'Absen datang berhasil!'; break;
          case 'absen_pulang': $message = 'Absen pulang berhasil!'; break;
          case 'absen': $message = 'Absensi berhasil!'; break;
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
  
  <!-- Status Absensi Hari Ini -->
  <div class="card">
      <h3>📅 Status Absensi Hari Ini</h3>
      <div class="absensi-status">
          <?php 
          $today = date('Y-m-d');
          $absensi = new \App\Models\Absensi($db);
          $absensiHariIni = $absensi->getAbsensiHariIni($siswa['id']);
          
          $sudahDatang = false;
          $sudahPulang = false;
          $waktuDatang = '';
          $waktuPulang = '';
          
          foreach ($absensiHariIni as $absen) {
              if ($absen['jenis_absen'] === 'datang') {
                  $sudahDatang = true;
                  $waktuDatang = $absen['waktu'];
              } elseif ($absen['jenis_absen'] === 'pulang') {
                  $sudahPulang = true;
                  $waktuPulang = $absen['waktu'];
              }
          }
          ?>
          
          <div class="status-grid">
              <div class="status-item <?= $sudahDatang ? 'completed' : 'pending' ?>">
                  <div class="status-icon">
                      <?= $sudahDatang ? '✅' : '⏰' ?>
                  </div>
                  <div class="status-text">
                      <strong>Absen Datang</strong>
                      <?php if ($sudahDatang): ?>
                          <div class="status-time"><?= date('H:i', strtotime($waktuDatang)) ?></div>
                      <?php else: ?>
                          <div class="status-desc">Belum absen</div>
                      <?php endif; ?>
                  </div>
              </div>
              
              <div class="status-item <?= $sudahPulang ? 'completed' : 'pending' ?>">
                  <div class="status-icon">
                      <?= $sudahPulang ? '✅' : '⏰' ?>
                  </div>
                  <div class="status-text">
                      <strong>Absen Pulang</strong>
                      <?php if ($sudahPulang): ?>
                          <div class="status-time"><?= date('H:i', strtotime($waktuPulang)) ?></div>
                      <?php else: ?>
                          <div class="status-desc">Belum absen</div>
                      <?php endif; ?>
                  </div>
              </div>
          </div>
      </div>
  </div>
  
  <!-- Tombol Absensi -->
  
<?php endif; ?>

<!-- Include Leaflet CSS and JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
  let map, marker, currentPosition;
  
  // Fungsi untuk menampilkan form absen
  function showAbsenForm(jenis) {
    document.getElementById('jenisAbsen').value = jenis;
    document.getElementById('jenisAbsenDisplay').innerHTML = jenis === 'datang' ? '🚀 Absen Datang' : '🏠 Absen Pulang';
    document.getElementById('absenForm').style.display = 'block';
    
    // Set waktu perangkat dan timezone offset
    setDeviceTime();
    
    // Aktifkan tombol submit dan ubah teks
    const submitBtn = document.getElementById('submitAbsen');
    submitBtn.disabled = false;
    submitBtn.innerHTML = jenis === 'datang' ? '✅ Kirim Absen Datang' : '✅ Kirim Absen Pulang';
    submitBtn.className = 'btn btn-success btn-block';
    
    // Scroll ke form
    document.getElementById('absenForm').scrollIntoView({ behavior: 'smooth' });
    
    // Mulai tracking lokasi
    startLocationTracking();
    
    // Cek status tombol submit setelah setup
    setTimeout(checkSubmitButtonStatus, 100);
  }
  
  // Fungsi untuk menyembunyikan form absen
  function hideAbsenForm() {
    document.getElementById('absenForm').style.display = 'none';
    
    // Reset tombol submit
    const submitBtn = document.getElementById('submitAbsen');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '⏳ Pilih Jenis Absensi Dulu';
    submitBtn.className = 'btn btn-secondary btn-block';
  }
  
  // Fungsi untuk memulai tracking lokasi
  function startLocationTracking() {
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(
        function(position) {
          currentPosition = position;
          const lat = position.coords.latitude;
          const lng = position.coords.longitude;
          
          document.getElementById('lat').value = lat;
          document.getElementById('lng').value = lng;
          document.getElementById('locationText').innerHTML = `✅ Lokasi berhasil didapatkan: ${lat.toFixed(6)}, ${lng.toFixed(6)}`;
          document.getElementById('locationStatus').style.background = '#d4edda';
          
          // Cek apakah bisa mengaktifkan tombol submit
          checkSubmitButtonStatus();
          
          // Update map
          if (map) {
            map.setView([lat, lng], 16);
            if (marker) {
              marker.setLatLng([lat, lng]);
            } else {
              marker = L.marker([lat, lng]).addTo(map);
            }
          }
        },
        function(error) {
          document.getElementById('locationText').innerHTML = `❌ Error: ${error.message}`;
          document.getElementById('locationStatus').style.background = '#f8d7da';
        },
        {
          enableHighAccuracy: true,
          timeout: 10000,
          maximumAge: 60000
        }
      );
    } else {
      document.getElementById('locationText').innerHTML = '❌ Geolocation tidak didukung di browser ini';
      document.getElementById('locationStatus').style.background = '#f8d7da';
    }
  }
  
  // Fungsi untuk set waktu perangkat
  function setDeviceTime() {
    const now = new Date();
    const deviceTime = now.toISOString();
    const timezoneOffset = now.getTimezoneOffset();
    
    document.getElementById('deviceTime').value = deviceTime;
    document.getElementById('timezoneOffset').value = timezoneOffset;
    
    // Update waktu setiap detik
    updateDeviceTime();
  }
  
  // Fungsi untuk update waktu secara real-time
  function updateDeviceTime() {
    const now = new Date();
    const deviceTime = now.toISOString();
    const timezoneOffset = now.getTimezoneOffset();
    
    document.getElementById('deviceTime').value = deviceTime;
    document.getElementById('timezoneOffset').value = timezoneOffset;
    
    // Update tampilan waktu
    const deviceTimeText = document.getElementById('deviceTimeText');
    if (deviceTimeText) {
      const localTime = now.toLocaleString('id-ID', {
        timeZone: 'Asia/Jakarta',
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: false
      });
      
      const utcTime = now.toUTCString();
      deviceTimeText.innerHTML = `${localTime} (WIB)<br><small style="color: #666;">UTC: ${utcTime}</small>`;
    }
    
    // Update setiap detik
    setTimeout(updateDeviceTime, 1000);
  }
  
  // Fungsi untuk mengecek status tombol submit
  function checkSubmitButtonStatus() {
    const submitBtn = document.getElementById('submitAbsen');
    const jenisAbsen = document.getElementById('jenisAbsen').value;
    const lat = document.getElementById('lat').value;
    const lng = document.getElementById('lng').value;
    const selfie = document.getElementById('selfie').value;
    
    // Debug: log status data
    console.log('Status data:', { jenisAbsen, lat, lng, selfie });
    
    // Tombol aktif jika jenis absen sudah dipilih
    if (jenisAbsen) {
      submitBtn.disabled = false;
      submitBtn.className = 'btn btn-success btn-block';
      
      // Update teks tombol sesuai jenis absen
      if (jenisAbsen === 'datang') {
        submitBtn.innerHTML = '✅ Kirim Absen Datang';
      } else if (jenisAbsen === 'pulang') {
        submitBtn.innerHTML = '✅ Kirim Absen Pulang';
      }
      
      // Tambahkan warning jika data belum lengkap
      if (!lat || !lng) {
        submitBtn.innerHTML += ' (⏳ Menunggu Lokasi)';
      }
      if (!selfie) {
        submitBtn.innerHTML += ' (📸 Belum Selfie)';
      }
    } else {
      submitBtn.disabled = true;
      submitBtn.innerHTML = '⏳ Pilih Jenis Absensi Dulu';
      submitBtn.className = 'btn btn-secondary btn-block';
    }
  }
  
  // Initialize map when page loads
  document.addEventListener('DOMContentLoaded', function() {
    try {
      map = L.map('map');
      
      <?php if(!empty($tempat)): ?>
        const tempat = [<?= (float)$tempat['lat'] ?>, <?= (float)$tempat['lng'] ?>];
        map.setView(tempat, 17);
        
        // Add tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
          attribution: '© OpenStreetMap contributors'
        }).addTo(map);
        
        // Add PKL location marker
        L.marker(tempat, {icon: L.divIcon({
          className: 'custom-div-icon',
          html: "<div style='background-color: #007bff; width: 20px; height: 20px; display: block; left: -10px; top: -10px; position: relative; border-radius: 20px; border: 2px solid #fff;'></div>",
          iconSize: [20, 20],
          iconAnchor: [10, 10]
        })}).addTo(map).bindPopup('<strong>Tempat PKL</strong><br><?= \App\Helpers\Response::e($tempat['nama'] ?? '') ?>');
        
        // Add radius circle
        L.circle(tempat, {
          radius: <?= (int)($tempat['radius_m'] ?? 150) ?>,
          color: '#007bff',
          fillColor: '#007bff',
          fillOpacity: 0.1
        }).addTo(map).bindPopup('Radius: <?= (int)($tempat['radius_m'] ?? 150) ?> meter');
      <?php endif; ?>
      
      // Get user location
      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
          function(position) {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;
            
            // Update form fields
            document.getElementById('lat').value = lat;
            document.getElementById('lng').value = lng;
            
            // Update location status
            document.getElementById('locationText').innerHTML = `✅ Lokasi berhasil: ${lat.toFixed(6)}, ${lng.toFixed(6)}`;
            document.getElementById('locationStatus').style.background = '#d4edda';
            document.getElementById('locationStatus').style.color = '#155724';
            
            // Add user position marker to map
            if (map) {
              L.marker([lat, lng], {icon: L.divIcon({
                className: 'custom-div-icon',
                html: "<div style='background-color: #28a745; width: 20px; height: 20px; display: block; left: -10px; top: -10px; position: relative; border-radius: 20px; border: 2px solid #fff;'></div>",
                iconSize: [20, 20],
                iconAnchor: [10, 10]
              })}).addTo(map).bindPopup('<strong>Posisi Anda</strong><br>Lat: ' + lat.toFixed(6) + '<br>Lng: ' + lng.toFixed(6));
              
              // Fit map to show both markers
              const bounds = L.latLngBounds([tempat, [lat, lng]]);
              map.fitBounds(bounds, {padding: [20, 20]});
            }
          },
          function(error) {
            console.error('Geolocation error:', error);
            let errorMessage = 'Gagal mendapatkan lokasi: ';
            
            switch(error.code) {
              case error.PERMISSION_DENIED:
                errorMessage += 'Akses lokasi ditolak.';
                break;
              case error.POSITION_UNAVAILABLE:
                errorMessage += 'Informasi lokasi tidak tersedia.';
                break;
              case error.TIMEOUT:
                errorMessage += 'Timeout mendapatkan lokasi.';
                break;
              default:
                errorMessage += 'Error tidak diketahui.';
            }
            
            document.getElementById('locationText').innerHTML = `❌ ${errorMessage}`;
            document.getElementById('locationStatus').style.background = '#f8d7da';
            document.getElementById('locationStatus').style.color = '#721c24';
          },
          {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 300000
          }
        );
      } else {
        document.getElementById('locationText').innerHTML = '❌ Geolocation tidak didukung browser ini';
        document.getElementById('locationStatus').style.background = '#f8d7da';
        document.getElementById('locationStatus').style.color = '#721c24';
      }
      
    } catch (e) {
      console.error('Map initialization error:', e);
      document.getElementById('map').innerHTML = '<div style="padding: 20px; text-align: center; color: #721c24; background-color: #f8d7da; border: 1px solid #f5c6cb; border-radius: 4px;">Error loading map: ' + e.message + '</div>';
    }
  });
  
  // Event listener untuk selfie dan input lainnya
  document.addEventListener('DOMContentLoaded', function() {
    // Monitor perubahan pada input selfie
    const selfieInput = document.getElementById('selfie');
    if (selfieInput) {
      // Monitor perubahan pada input selfie
      const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
          if (mutation.type === 'attributes' && mutation.attributeName === 'value') {
            checkSubmitButtonStatus();
          }
        });
      });
      
      observer.observe(selfieInput, {
        attributes: true,
        attributeFilter: ['value']
      });
    }
    
    // Monitor perubahan pada input lat dan lng
    const latInput = document.getElementById('lat');
    const lngInput = document.getElementById('lng');
    
    if (latInput && lngInput) {
      [latInput, lngInput].forEach(input => {
        input.addEventListener('input', checkSubmitButtonStatus);
        input.addEventListener('change', checkSubmitButtonStatus);
      });
    }
    
    // Monitor perubahan pada jenis absen
    const jenisAbsenInput = document.getElementById('jenisAbsen');
    if (jenisAbsenInput) {
      jenisAbsenInput.addEventListener('change', checkSubmitButtonStatus);
    }
  });
</script>

<style>
    .card {
      background: white;
      border-radius: 8px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
      padding: 20px;
      margin-bottom: 20px;
    }
    
    .status-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 20px;
      margin-top: 15px;
    }
    
    .status-item {
      display: flex;
      align-items: center;
      padding: 15px;
      border-radius: 8px;
      border: 2px solid #e9ecef;
      transition: all 0.3s ease;
    }
    
    .status-item.completed {
      border-color: #28a745;
      background-color: #f8fff9;
    }
    
    .status-item.pending {
      border-color: #ffc107;
      background-color: #fffdf7;
    }
    
    .status-icon {
      font-size: 2rem;
      margin-right: 15px;
    }
    
    .status-text strong {
      display: block;
      margin-bottom: 5px;
      color: #333;
    }
    
    .status-time {
      color: #28a745;
      font-weight: bold;
      font-size: 1.1rem;
    }
    
    .status-desc {
      color: #6c757d;
      font-style: italic;
    }
    
    .absensi-buttons {
      display: flex;
      gap: 15px;
      margin-top: 15px;
      flex-wrap: wrap;
    }
    
    .btn-large {
      padding: 15px 30px;
      font-size: 1.1rem;
      min-width: 200px;
    }
    
    .badge {
      padding: 6px 12px;
      border-radius: 20px;
      font-size: 0.85rem;
      font-weight: bold;
    }
    
    .badge-primary {
      background-color: #007bff;
      color: white;
    }
    
    .badge-warning {
      background-color: #ffc107;
      color: #212529;
    }
    
    .badge-secondary {
      background-color: #6c757d;
      color: white;
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
    
    @media (max-width: 768px) {
      .status-grid {
        grid-template-columns: 1fr;
      }
      
      .absensi-buttons {
        flex-direction: column;
      }
      
      .btn-large {
        min-width: auto;
      }
    }
  </style>