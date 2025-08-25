# 🏫 Troubleshooting Login Title & School Name

## Masalah: Nama Sekolah dan Judul Tidak Muncul di Halaman Login

### 🔍 **Langkah Debugging:**

#### **Step 1: Periksa URL yang Diakses**
```
❌ SALAH: http://localhost/absensi/public/index.php
✅ BENAR: http://localhost/absensi/public/index.php?r=auth/login
```

#### **Step 2: Periksa Config Loading di AuthController**
```php
// File: app/Controllers/AuthController.php
public function login(): void
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // ... login logic ...
        if ($user && password_verify(...)) {
            // ... success login ...
        } else {
            $error = 'Username atau password salah';
            // Load config untuk view
            $app = require __DIR__ . '/../../config/app.php';
            $this->view('auth/login', ['error' => $error, 'app' => $app]);
        }
    } else {
        // Load config untuk view
        $app = require __DIR__ . '/../../config/app.php';
        $this->view('auth/login', ['app' => $app]);
    }
}
```

#### **Step 3: Periksa View Login**
```php
// File: app/Views/auth/login.php
<div class="login-header">
    <div class="login-logo">🏢</div>
    <h1><?= htmlspecialchars($app['name']) ?></h1>        // ← Pastikan ini ada
    <p class="login-subtitle"><?= htmlspecialchars($app['description']) ?></p>
</div>
```

### 🧪 **File Test yang Tersedia:**

#### **1. Test Login Config Loading:**
```
URL: http://localhost/absensi/test_login_config.php
```
- Test config loading
- Verifikasi app name dan description
- Test login page include
- Test header include

#### **2. Test App Title Configuration:**
```
URL: http://localhost/absensi/test_app_title.php
```
- Test config loading
- Verifikasi app name dan description
- Test header include

### 🎯 **Solusi Umum:**

#### **Masalah 1: Config Tidak Dikirim ke View**
**Gejala:** `$app` variable tidak tersedia di view login
**Solusi:**
```php
// Di AuthController, pastikan config dikirim ke view
$app = require __DIR__ . '/../../config/app.php';
$this->view('auth/login', ['app' => $app]);
```

#### **Masalah 2: URL Routing Salah**
**Gejala:** Halaman tidak menampilkan login form
**Solusi:**
```
// Gunakan URL yang benar
http://localhost/absensi/public/index.php?r=auth/login
```

#### **Masalah 3: Config File Not Found**
**Gejala:** Error "require config/app.php failed"
**Solusi:**
```php
// Periksa path relatif dari AuthController
$app = require __DIR__ . '/../../config/app.php';

// Atau gunakan path absolut
$app = require 'config/app.php';
```

#### **Masalah 4: Variable Scope di View**
**Gejala:** `$app` undefined di view
**Solusi:**
```php
// Di view, pastikan variable $app tersedia
<?php if (isset($app) && !empty($app)): ?>
    <h1><?= htmlspecialchars($app['name']) ?></h1>
    <p><?= htmlspecialchars($app['description']) ?></p>
<?php else: ?>
    <h1>Absensi PKL</h1>
    <p>Sistem Absensi Praktik Kerja Lapangan</p>
<?php endif; ?>
```

### 🔧 **Debugging Step-by-Step:**

#### **Step 1: Cek URL Access**
1. Buka browser
2. Akses: `http://localhost/absensi/public/index.php?r=auth/login`
3. Pastikan halaman login muncul
4. Periksa apakah ada error di console

#### **Step 2: Cek Config Loading**
1. Buka file `test_login_config.php`
2. Periksa apakah config ter-load
3. Verifikasi app name dan description
4. Test login page include

#### **Step 3: Cek AuthController**
1. Buka file `app/Controllers/AuthController.php`
2. Pastikan config di-load sebelum view
3. Pastikan config dikirim ke view
4. Periksa path ke config file

#### **Step 4: Cek View Login**
1. Buka file `app/Views/auth/login.php`
2. Pastikan menggunakan `$app['name']`
3. Pastikan menggunakan `$app['description']`
4. Test dengan variable check

#### **Step 5: Test Browser Title**
1. Buka halaman login
2. Periksa title di browser tab
3. Pastikan title sesuai dengan config
4. Test di halaman lain untuk konsistensi

### 📱 **Test Responsif:**

#### **Desktop (>768px):**
- Title muncul di browser tab
- Nama sekolah muncul di header login
- Description muncul di subtitle
- Config tetap ter-load

#### **Tablet (≤768px):**
- Title tetap muncul
- Responsive design bekerja
- Config tetap ter-load
- Layout menyesuaikan

#### **Mobile (≤480px):**
- Title tetap muncul
- Layout mobile bekerja
- Config tetap ter-load
- Touch-friendly

### 🚨 **Common Issues:**

#### **Issue 1: Config Not Sent to View**
```
Problem: $app variable not available in login view
Solution: Send config from AuthController to view
```

#### **Issue 2: Wrong URL Access**
```
Problem: Accessing wrong URL without routing
Solution: Use ?r=auth/login parameter
```

#### **Issue 3: Config Path Wrong**
```
Problem: require config/app.php failed
Solution: Check relative path from controller
```

#### **Issue 4: View Not Using Config**
```
Problem: View still using hardcoded values
Solution: Replace with <?= htmlspecialchars($app['name']) ?>
```

### 📋 **Checklist Debugging:**

- [ ] URL menggunakan routing yang benar (`?r=auth/login`)
- [ ] Config file `config/app.php` ada dan dapat diakses
- [ ] AuthController mengirim config ke view
- [ ] Variable `$app` tersedia di view login
- [ ] View menggunakan `$app['name']` dan `$app['description']`
- [ ] Browser title menampilkan app name yang benar
- [ ] Nama sekolah muncul di header login
- [ ] Description muncul di subtitle login
- [ ] Tidak ada error PHP di console atau log

### 🆘 **Jika Masih Bermasalah:**

#### **1. Gunakan file test:**
```
http://localhost/absensi/test_login_config.php
```

#### **2. Periksa error log** PHP untuk detail error

#### **3. Test config loading** secara manual

#### **4. Periksa struktur folder** dan path

#### **5. Test di browser berbeda** untuk memastikan bukan browser-specific

### 🔧 **Quick Fix:**

Jika nama sekolah dan judul masih tidak muncul:

#### **1. Periksa AuthController:**
```php
// Pastikan config dikirim ke view
$app = require __DIR__ . '/../../config/app.php';
$this->view('auth/login', ['app' => $app]);
```

#### **2. Update View Login:**
```php
<h1><?= htmlspecialchars($app['name']) ?></h1>
<p class="login-subtitle"><?= htmlspecialchars($app['description']) ?></p>
```

#### **3. Test URL yang Benar:**
```
http://localhost/absensi/public/index.php?r=auth/login
```

#### **4. Debug Config Values:**
```php
// Tambahkan ini sementara untuk debug
echo "App Name: " . $app['name'];
echo "App Description: " . $app['description'];
```

### 📞 **Support:**

Jika masalah masih berlanjut, silakan:
1. Screenshot error yang muncul
2. Copy-paste error dari console browser
3. Jelaskan langkah yang sudah dilakukan
4. Sebutkan browser dan OS yang digunakan
5. Gunakan file test untuk informasi detail

### 🎯 **Keunggulan Troubleshooting Guide:**

- ✅ **Step-by-step debugging** yang jelas
- ✅ **Solusi umum** untuk masalah login title yang sering terjadi
- ✅ **File test** yang tersedia untuk debugging
- ✅ **Checklist** yang lengkap
- ✅ **Quick fix** untuk masalah mendesak
- ✅ **Support** yang jelas untuk masalah lanjutan

