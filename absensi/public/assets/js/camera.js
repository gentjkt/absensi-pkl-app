(function(){
  const video = document.getElementById('video');
  const canvas = document.getElementById('canvas');
  const preview = document.getElementById('preview');
  const snapBtn = document.getElementById('snapBtn');
  
  if (!video || !canvas || !snapBtn) {
    console.error('Camera elements not found');
    return;
  }

  // Polyfill untuk navigator.mediaDevices (browser lama)
  if (navigator.mediaDevices === undefined) {
    navigator.mediaDevices = {};
  }

  // Polyfill untuk getUserMedia (browser lama)
  if (navigator.mediaDevices.getUserMedia === undefined) {
    navigator.mediaDevices.getUserMedia = function(constraints) {
      const getUserMedia = navigator.webkitGetUserMedia || navigator.mozGetUserMedia || navigator.msGetUserMedia;
      
      if (!getUserMedia) {
        return Promise.reject(new Error('getUserMedia tidak didukung browser ini'));
      }
      
      return new Promise(function(resolve, reject) {
        getUserMedia.call(navigator, constraints, resolve, reject);
      });
    }
  }

  // Debug info
  console.log('Camera initialization started');
  console.log('Available devices:', navigator.mediaDevices ? 'Yes' : 'No');
  console.log('getUserMedia support:', navigator.mediaDevices.getUserMedia ? 'Yes' : 'No');
  
  async function initCam(){
    try {
      console.log('Requesting camera access...');
      
      // Coba berbagai konfigurasi kamera
      const constraints = {
        video: {
          facingMode: 'user',
          width: { ideal: 640 },
          height: { ideal: 480 }
        },
        audio: false
      };
      
      const stream = await navigator.mediaDevices.getUserMedia(constraints);
      console.log('Camera access granted:', stream);
      
      video.srcObject = stream;
      video.onloadedmetadata = () => {
        console.log('Video metadata loaded');
        video.play();
      };
      
      // Tambahkan event listener untuk error
      video.onerror = (e) => {
        console.error('Video error:', e);
        alert('Error pada video stream: ' + e.message);
      };
      
    } catch(e) {
      console.error('Camera access failed:', e);
      
      // Error handling yang lebih informatif
      let errorMessage = 'Gagal mengakses kamera: ';
      
      if (e.name === 'NotAllowedError') {
        errorMessage += 'Akses kamera ditolak. Pastikan Anda mengizinkan akses kamera di browser.';
      } else if (e.name === 'NotFoundError') {
        errorMessage += 'Kamera tidak ditemukan. Pastikan device memiliki kamera dan tidak digunakan aplikasi lain.';
      } else if (e.name === 'NotReadableError') {
        errorMessage += 'Kamera sedang digunakan aplikasi lain. Tutup aplikasi lain yang menggunakan kamera.';
      } else if (e.name === 'OverconstrainedError') {
        errorMessage += 'Kamera tidak mendukung resolusi yang diminta.';
      } else if (e.message && e.message.includes('getUserMedia tidak didukung')) {
        errorMessage += 'Browser ini tidak mendukung akses kamera. Gunakan browser modern seperti Chrome, Firefox, atau Safari.';
      } else {
        errorMessage += e.message || 'Unknown error';
      }
      
      alert(errorMessage);
      
      // Fallback: tampilkan pesan error yang lebih user-friendly
      const cameraContainer = video.parentElement;
      if (cameraContainer) {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'camera-error';
        errorDiv.innerHTML = `
          <div style="padding: 20px; text-align: center; color: #721c24; background-color: #f8d7da; border: 1px solid #f5c6cb; border-radius: 4px;">
            <h4>⚠️ Kamera Tidak Dapat Diakses</h4>
            <p>${errorMessage}</p>
            <p><strong>Solusi:</strong></p>
            <ul style="text-align: left; display: inline-block;">
              <li>Refresh halaman dan izinkan akses kamera</li>
              <li>Pastikan tidak ada aplikasi lain yang menggunakan kamera</li>
              <li>Cek pengaturan privacy browser</li>
              <li>Gunakan browser yang mendukung WebRTC (Chrome, Firefox, Safari)</li>
              <li>Pastikan menggunakan HTTPS (untuk production)</li>
            </ul>
            <button onclick="location.reload()" style="margin-top: 10px; padding: 8px 16px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer;">
              🔄 Refresh Halaman
            </button>
          </div>
        `;
        cameraContainer.appendChild(errorDiv);
        video.style.display = 'none';
      }
    }
  }

  // Inisialisasi kamera
  initCam();
  
  // Event listener untuk tombol ambil foto
  snapBtn.addEventListener('click', () => {
    if (!video.srcObject) {
      alert('Kamera belum siap. Tunggu sebentar atau refresh halaman.');
      return;
    }
    
    try {
      const w = video.videoWidth || 640;
      const h = video.videoHeight || 480;
      
      canvas.width = w;
      canvas.height = h;
      
      const ctx = canvas.getContext('2d');
      ctx.drawImage(video, 0, 0, w, h);
      
      const dataUrl = canvas.toDataURL('image/jpeg', 0.85);
      document.getElementById('selfie').value = dataUrl;
      preview.src = dataUrl;
      
      // Tampilkan preview
      preview.style.display = 'block';
      preview.style.maxWidth = '100%';
      preview.style.height = 'auto';
      
      console.log('Photo captured successfully');
      
    } catch (e) {
      console.error('Photo capture failed:', e);
      alert('Gagal mengambil foto: ' + e.message);
    }
  });
})();