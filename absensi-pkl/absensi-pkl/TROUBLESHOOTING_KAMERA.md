# 🔧 Troubleshooting Kamera - Sistem Absensi PKL

## ❌ Error: "cannot read properties of undefined (reading 'getUserMedia')"

### Penyebab Error:
Error ini terjadi ketika browser tidak mendukung `navigator.mediaDevices.getUserMedia` atau menggunakan browser lama.

### Solusi yang Sudah Diterapkan:

#### 1. ✅ **Polyfill untuk Browser Lama**
```javascript
// Polyfill untuk navigator.mediaDevices
if (navigator.mediaDevices === undefined) {
    navigator.mediaDevices = {};
}

// Polyfill untuk getUserMedia
if (navigator.mediaDevices.getUserMedia === undefined) {
    navigator.mediaDevices.getUserMedia = function(constraints) {
        const getUserMedia = navigator.webkitGetUserMedia || 
                           navigator.mozGetUserMedia || 
                           navigator.msGetUserMedia;
        
        if (!getUserMedia) {
            return Promise.reject(new Error('getUserMedia tidak didukung browser ini'));
        }
        
        return new Promise(function(resolve, reject) {
            getUserMedia.call(navigator, constraints, resolve, reject);
        });
    }
}
```

#### 2. ✅ **Error Handling yang Lebih Baik**
- Pesan error yang spesifik dan informatif
- Fallback UI ketika kamera tidak bisa diakses
- Debug logging untuk troubleshooting

#### 3. ✅ **File Test Terpisah**
- `test_camera.html` untuk testing kamera secara independen
- Debug info lengkap untuk diagnosis

## 🚀 **Cara Test Kamera:**

### **Option 1: Test via Aplikasi Utama**
1. Buka `http://localhost/absensi-pkl/`
2. Login sebagai siswa: `siswa1` / `123456`
3. Akses student dashboard
4. Izinkan akses kamera ketika browser meminta

### **Option 2: Test via File Terpisah**
1. Buka `test_camera.html` di browser
2. Klik "🚀 Mulai Kamera"
3. Izinkan akses kamera
4. Test ambil foto dengan "📸 Ambil Foto"

## 🔍 **Diagnosis Masalah:**

### **1. Cek Browser Support**
```javascript
// Buka Console Browser (F12) dan jalankan:
console.log('MediaDevices:', !!navigator.mediaDevices);
console.log('getUserMedia:', !!(navigator.mediaDevices && navigator.mediaDevices.getUserMedia));
console.log('Webkit:', !!navigator.webkitGetUserMedia);
console.log('Mozilla:', !!navigator.mozGetUserMedia);
```

### **2. Cek Protocol**
- **HTTP (localhost)**: Bisa jadi ada masalah dengan permission
- **HTTPS**: Lebih aman dan reliable untuk kamera

### **3. Cek Permission**
- Pastikan browser mengizinkan akses kamera
- Cek icon 🔒 di address bar
- Cek pengaturan privacy browser

## 🛠️ **Solusi Berdasarkan Error:**

### **Error: "NotAllowedError"**
```
Solusi: Izinkan akses kamera di browser
- Klik icon kamera di address bar
- Pilih "Allow" atau "Izinkan"
- Refresh halaman
```

### **Error: "NotFoundError"**
```
Solusi: Cek hardware kamera
- Pastikan device memiliki kamera
- Cek apakah kamera tidak rusak
- Test di aplikasi lain (Zoom, Teams, dll)
```

### **Error: "NotReadableError"**
```
Solusi: Tutup aplikasi lain yang menggunakan kamera
- Tutup Zoom, Teams, Skype
- Tutup aplikasi video call lainnya
- Restart browser
```

### **Error: "OverconstrainedError"**
```
Solusi: Kamera tidak mendukung resolusi yang diminta
- Refresh halaman
- Gunakan browser yang lebih modern
- Cek driver kamera
```

## 🌐 **Browser Compatibility:**

### **✅ Browser yang Didukung:**
- **Chrome 53+** (2016+)
- **Firefox 36+** (2015+)
- **Safari 11+** (2017+)
- **Edge 12+** (2015+)

### **❌ Browser yang Tidak Didukung:**
- **Internet Explorer** (semua versi)
- **Chrome < 53**
- **Firefox < 36**
- **Safari < 11**

## 📱 **Device Compatibility:**

### **✅ Device yang Didukung:**
- **Desktop/Laptop** dengan webcam
- **Smartphone** dengan kamera depan
- **Tablet** dengan kamera

### **⚠️ Device yang Mungkin Bermasalah:**
- **Desktop tanpa webcam**
- **Device dengan kamera rusak**
- **Device dengan driver lama**

## 🔧 **Troubleshooting Lanjutan:**

### **1. Clear Browser Cache**
```
Chrome: Ctrl+Shift+Delete → Clear browsing data
Firefox: Ctrl+Shift+Delete → Clear recent history
Safari: Develop → Empty Caches
```

### **2. Reset Camera Permission**
```
Chrome: Settings → Privacy and security → Site Settings → Camera
Firefox: about:preferences#privacy → Permissions → Camera
Safari: Safari → Preferences → Websites → Camera
```

### **3. Test di Browser Lain**
- Coba buka di Chrome, Firefox, Safari
- Bandingkan hasil di masing-masing browser
- Identifikasi browser mana yang bermasalah

### **4. Cek System Requirements**
- **OS**: Windows 10+, macOS 10.12+, Ubuntu 16.04+
- **RAM**: Minimal 4GB
- **Browser**: Versi terbaru
- **Camera Driver**: Up-to-date

## 📋 **Checklist Troubleshooting:**

- [ ] Browser mendukung getUserMedia
- [ ] Permission kamera diizinkan
- [ ] Device memiliki kamera
- [ ] Kamera tidak digunakan aplikasi lain
- [ ] Protocol HTTPS (untuk production)
- [ ] Driver kamera up-to-date
- [ ] Browser versi terbaru
- [ ] Cache browser sudah di-clear

## 🆘 **Jika Masih Bermasalah:**

### **1. Debug Mode**
- Buka Console Browser (F12)
- Lihat error messages
- Cek Network tab untuk request yang gagal

### **2. Test File Terpisah**
- Gunakan `test_camera.html`
- Bandingkan dengan aplikasi utama
- Identifikasi perbedaan

### **3. Report Issue**
- Catat error message lengkap
- Catat browser dan versi
- Catat OS dan device
- Screenshot error jika ada

## 🎯 **Kesimpulan:**

Error "getUserMedia" sudah ditangani dengan:
1. **Polyfill untuk browser lama**
2. **Error handling yang robust**
3. **Fallback UI yang informatif**
4. **File test terpisah untuk diagnosis**

Jika masih ada masalah, gunakan file `test_camera.html` untuk testing independen dan ikuti checklist troubleshooting di atas.

---

**📞 Support:** Jika masalah masih berlanjut, buat issue dengan detail lengkap untuk bantuan lebih lanjut.
