# 🏷️ Troubleshooting App Title

## Masalah: App Title Tidak Sesuai dengan Halaman Login

### 🔍 **Langkah Debugging:**

#### **Step 1: Periksa Config File**
```php
// File: config/app.php
return [
    'name' => 'Absensi PKL',           // ← Ini yang digunakan sebagai title
    'version' => '1.0.0',
    'description' => 'Sistem Absensi Praktik Kerja Lapangan',
    // ... konfigurasi lainnya
];
```

#### **Step 2: Periksa Header Include**
```php
// File: app/Views/layouts/header.php
<?php $app = require __DIR__.'/../../../config/app.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title><?= htmlspecialchars($app['name']) ?></title>  // ← Pastikan ini ada
```

#### **Step 3: Periksa Halaman Login**
```php
// File: app/Views/auth/login.php
<div class="login-header">
    <h1><?= htmlspecialchars($app['name']) ?></h1>        // ← Gunakan config
    <p class="login-subtitle"><?= htmlspecialchars($app['description']) ?></p>
</div>
```

### 🧪 **File Test yang Tersedia:**

#### **1. Test App Title Configuration:**
```
URL: http://localhost/test_app_title.php
```
- Test config loading
- Verifikasi app name dan description
- Test header include
- Troubleshooting guide

### 🎯 **Solusi Umum:**

#### **Masalah 1: Config Tidak Ter-load**
**Gejala:** `$app` variable tidak tersedia
**Solusi:**
```php
// Pastikan config file ada dan dapat diakses
$app = require __DIR__.'/../../../config/app.php';

// Atau gunakan path absolut
$app = require 'config/app.php';
```

#### **Masalah 2: Title Hardcoded**
**Gejala:** Title masih "Absensi PKL" hardcoded
**Solusi:**
```html
<!-- SALAH -->
<title>Absensi PKL</title>

<!-- BENAR -->
<title><?= htmlspecialchars($app['name']) ?></title>
```

#### **Masalah 3: Variable Scope**
**Gejala:** `$app` tidak tersedia di view
**Solusi:**
```php
// Di controller, pass config ke view
$this->view('auth/login', ['app' => $this->config]);

// Atau di view, load config langsung
<?php $app = require 'config/app.php'; ?>
```

#### **Masalah 4: Path Config Salah**
**Gejala:** Config file tidak ditemukan
**Solusi:**
```php
// Periksa struktur folder
absensi/
├── config/
│   └── app.php          // ← Pastikan file ada
├── app/
│   └── Views/
│       └── layouts/
│           └── header.php
```

### 🔧 **Debugging Step-by-Step:**

#### **Step 1: Cek Config File**
1. Buka file `config/app.php`
2. Pastikan `name` dan `description` ada
3. Periksa syntax PHP yang benar
4. Test dengan `test_app_title.php`

#### **Step 2: Cek Header Include**
1. Buka file `app/Views/layouts/header.php`
2. Pastikan config di-load di awal file
3. Periksa apakah `$app` tersedia
4. Pastikan title menggunakan `$app['name']`

#### **Step 3: Cek Halaman Login**
1. Buka file `app/Views/auth/login.php`
2. Pastikan menggunakan `$app['name']` bukan hardcoded
3. Periksa apakah config tersedia di view
4. Test halaman login di browser

#### **Step 4: Test Browser Title**
1. Buka halaman login: `http://localhost/?r=auth/login`
2. Periksa title di browser tab
3. Pastikan title sesuai dengan config
4. Test di halaman lain untuk konsistensi

### 📱 **Test Responsif:**

#### **Desktop (>768px):**
- Title muncul di browser tab
- Title konsisten dengan config
- Header menggunakan config app name

#### **Tablet (≤768px):**
- Title tetap muncul
- Responsive design bekerja
- Config tetap ter-load

#### **Mobile (≤480px):**
- Title tetap muncul
- Layout mobile bekerja
- Config tetap ter-load

### 🚨 **Common Issues:**

#### **Issue 1: Config File Not Found**
```
Problem: require config/app.php failed
Solution: Periksa path dan struktur folder
```

#### **Issue 2: Variable Not Available**
```
Problem: $app is undefined
Solution: Pastikan config di-load sebelum digunakan
```

#### **Issue 3: Title Still Hardcoded**
```
Problem: Title masih "Absensi PKL" hardcoded
Solution: Ganti dengan <?= htmlspecialchars($app['name']) ?>
```

#### **Issue 4: Path Issues**
```
Problem: __DIR__ path tidak benar
Solution: Periksa struktur folder dan path relatif
```

### 📋 **Checklist Debugging:**

- [ ] Config file `config/app.php` ada dan dapat diakses
- [ ] Variable `$app` tersedia di header
- [ ] Title menggunakan `<?= htmlspecialchars($app['name']) ?>`
- [ ] Halaman login menggunakan config app name
- [ ] Browser title menampilkan app name yang benar
- [ ] Config app name dan description ter-load dengan benar
- [ ] Tidak ada error PHP di console atau log

### 🆘 **Jika Masih Bermasalah:**

#### **1. Gunakan file test:**
```
http://localhost/test_app_title.php
```

#### **2. Periksa error log** PHP untuk detail error

#### **3. Test config loading** secara manual

#### **4. Periksa struktur folder** dan path

#### **5. Test di browser berbeda** untuk memastikan bukan browser-specific

### 🔧 **Quick Fix:**

Jika app title masih tidak bekerja, coba langkah ini:

#### **1. Periksa Config Loading:**
```php
// Di header.php, pastikan ini ada di awal
<?php $app = require __DIR__.'/../../../config/app.php'; ?>
```

#### **2. Update Title Tag:**
```html
<title><?= htmlspecialchars($app['name']) ?></title>
```

#### **3. Update Login Header:**
```php
<h1><?= htmlspecialchars($app['name']) ?></h1>
<p class="login-subtitle"><?= htmlspecialchars($app['description']) ?></p>
```

#### **4. Test Config Values:**
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
- ✅ **Solusi umum** untuk masalah app title yang sering terjadi
- ✅ **File test** yang tersedia untuk debugging
- ✅ **Checklist** yang lengkap
- ✅ **Quick fix** untuk masalah mendesak
- ✅ **Support** yang jelas untuk masalah lanjutan
