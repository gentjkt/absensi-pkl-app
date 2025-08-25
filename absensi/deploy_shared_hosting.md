# 🏠 Panduan Deployment Aplikasi Absensi PKL di Shared Hosting

## 🎯 **Overview**
Panduan ini menjelaskan langkah-langkah deployment aplikasi absensi PKL di shared hosting dengan cPanel, termasuk konfigurasi database, file upload, dan troubleshooting.

## 🚀 **Platform yang Didukung**
- **Hostinger** - cPanel dengan PHP 8.1+
- **Niagahoster** - cPanel dengan PHP 8.0+
- **IDCloudHost** - cPanel dengan PHP 8.1+
- **RumahWeb** - cPanel dengan PHP 8.0+
- **JagoanHosting** - cPanel dengan PHP 8.1+
- **HostGator** - cPanel dengan PHP 8.0+
- **Bluehost** - cPanel dengan PHP 8.0+

## 📋 **Persyaratan Shared Hosting**
- **PHP**: 8.0 atau lebih tinggi
- **MySQL**: 5.7+ atau MariaDB 10.2+
- **Storage**: Minimal 1GB
- **Bandwidth**: Minimal 10GB/bulan
- **PHP Extensions**: PDO, PDO_MySQL, JSON, cURL, GD
- **cPanel**: Versi terbaru dengan File Manager

## 🛠️ **Langkah-langkah Deployment**

### **1. Persiapan File**
```bash
# Struktur file yang akan diupload:
absensi-pkl/
├── app/
│   ├── Controllers/
│   ├── Models/
│   ├── Views/
│   └── Helpers/
├── config/
│   └── db.php
├── public/
│   ├── index.php
│   ├── .htaccess
│   ├── uploads/
│   └── assets/
├── db_absensi_pkl.sql
├── add_tempat_pkl_columns.sql
└── README_INSTALASI_CLOUD_HOSTING.md
```

### **2. Upload File ke Hosting**
1. **Buka cPanel** → **File Manager**
2. **Navigate ke `public_html`** (atau folder yang diinginkan)
3. **Upload semua file** dengan struktur yang sama
4. **Set permission** folder:
   - `public/uploads/` → 755
   - `public/uploads/.htaccess` → 644

### **3. Buat Database**
1. **Buka cPanel** → **MySQL Databases**
2. **Buat database baru**:
   - Database Name: `absensi_pkl`
   - Collation: `utf8mb4_unicode_ci`
3. **Buat user database**:
   - Username: `absensi_user`
   - Password: `password_kuat_disini`
4. **Berikan privileges**: `ALL PRIVILEGES`

### **4. Konfigurasi Database**
1. **Edit file `config/db.php`**:
```php
<?php
return [
    'host' => 'localhost', // Biasanya localhost untuk shared hosting
    'dbname' => 'username_hosting_absensi_pkl', // Format: username_hosting_namadb
    'username' => 'username_hosting_absensi_user', // Format: username_hosting_userdb
    'password' => 'password_kuat_disini',
    'charset' => 'utf8mb4',
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]
];
```

**Catatan**: Format nama database dan user di shared hosting biasanya:
- Database: `username_hosting_absensi_pkl`
- User: `username_hosting_absensi_user`

### **5. Import Database Schema**
1. **Buka cPanel** → **phpMyAdmin**
2. **Pilih database** yang sudah dibuat
3. **Import file `db_absensi_pkl.sql`**
4. **Jalankan script tambah kolom**:
```sql
-- Tambah kolom untuk fitur import tempat PKL
ALTER TABLE tempat_pkl ADD COLUMN pemilik VARCHAR(100) DEFAULT '' AFTER nama;
ALTER TABLE tempat_pkl ADD COLUMN alamat TEXT DEFAULT '' AFTER pemilik;
```

### **6. Konfigurasi .htaccess**
1. **Pastikan file `.htaccess` ada** di folder `public/`
2. **Jika menggunakan subfolder**, update `.htaccess`:
```apache
RewriteEngine On
RewriteBase /nama_subfolder/public/

# Jika file/directory tidak ada, redirect ke index.php
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php?r=$1 [QSA,L]

# Security headers
Header always set X-Content-Type-Options nosniff
Header always set X-Frame-Options DENY
Header always set X-XSS-Protection "1; mode=block"
```

### **7. Set Permission File**
```bash
# Via File Manager atau SSH (jika tersedia)
chmod 755 public/uploads
chmod 644 public/uploads/.htaccess
chmod 644 config/db.php
chmod 644 public/.htaccess
```

### **8. Test Aplikasi**
1. **Buka browser** dan akses domain
2. **Test login admin** dengan kredensial default
3. **Test fitur upload** file
4. **Test fitur import** tempat PKL

## 🔧 **Konfigurasi Tambahan**

### **PHP Settings via .htaccess**
```apache
# Tambahkan di .htaccess untuk override PHP settings
php_value upload_max_filesize 10M
php_value post_max_size 10M
php_value max_execution_time 300
php_value memory_limit 256M
php_value date.timezone "Asia/Jakarta"
```

### **Error Reporting (Development)**
```apache
# Hanya untuk development/testing
php_flag display_errors on
php_flag display_startup_errors on
php_value error_reporting E_ALL
```

### **Security Headers**
```apache
# Security headers tambahan
Header always set Referrer-Policy "strict-origin-when-cross-origin"
Header always set Permissions-Policy "geolocation=(), microphone=(), camera=()"
Header always set Content-Security-Policy "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval'; style-src 'self' 'unsafe-inline';"
```

## 🚨 **Troubleshooting**

### **Error 500 Internal Server Error**
```bash
# 1. Cek error log di cPanel
cPanel → Error Logs

# 2. Cek permission file
chmod 644 config/db.php
chmod 755 public/uploads

# 3. Cek syntax PHP
# Upload file test.php dengan isi:
<?php phpinfo(); ?>

# 4. Cek .htaccess
# Comment semua rules, test satu per satu
```

### **Database Connection Error**
```bash
# 1. Cek kredensial database
# Pastikan format: username_hosting_namadb

# 2. Test koneksi via phpMyAdmin
# Login dengan user yang dibuat

# 3. Cek host database
# Biasanya 'localhost' untuk shared hosting

# 4. Cek PHP PDO extension
# Buat file test.php:
<?php
var_dump(extension_loaded('pdo'));
var_dump(extension_loaded('pdo_mysql'));
?>
```

### **Upload File Error**
```bash
# 1. Cek permission folder uploads
chmod 755 public/uploads

# 2. Cek PHP upload settings
# Buat file phpinfo.php:
<?php phpinfo(); ?>
# Cari: upload_max_filesize, post_max_size

# 3. Cek .htaccess untuk override
php_value upload_max_filesize 10M
php_value post_max_size 10M
```

### **.htaccess Not Working**
```bash
# 1. Cek mod_rewrite enabled
# Contact hosting provider

# 2. Test dengan rules sederhana
RewriteEngine On
RewriteRule ^test$ test.php [L]

# 3. Cek AllowOverride setting
# Biasanya sudah enabled di shared hosting
```

## 📱 **Mobile Optimization**

### **Responsive Design**
- Semua view sudah responsive
- Test di berbagai device
- Optimize untuk mobile loading

### **Touch-friendly Interface**
- Button size minimal 44x44px
- Spacing yang nyaman untuk touch
- Swipe gestures untuk mobile

## 🔒 **Security Best Practices**

### **File Protection**
```apache
# Protect sensitive files
<Files "config/db.php">
    Order allow,deny
    Deny from all
</Files>

<Files ".env">
    Order allow,deny
    Deny from all
</Files>
```

### **Directory Protection**
```apache
# Protect app folder
<Directory "app">
    Order allow,deny
    Deny from all
</Directory>

# Protect config folder
<Directory "config">
    Order allow,deny
    Deny from all
</Directory>
```

### **Upload Security**
```apache
# Restrict upload types
<FilesMatch "\.(php|php3|php4|php5|phtml|pl|py|jsp|asp|sh|cgi)$">
    Order Deny,Allow
    Deny from all
</FilesMatch>
```

## 📊 **Performance Optimization**

### **Caching Strategy**
```apache
# Browser caching
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"
    ExpiresByType image/png "access plus 1 month"
    ExpiresByType image/jpg "access plus 1 month"
    ExpiresByType image/jpeg "access plus 1 month"
</IfModule>

# Compression
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/plain
    AddOutputFilterByType DEFLATE text/html
    AddOutputFilterByType DEFLATE text/xml
    AddOutputFilterByType DEFLATE text/css
    AddOutputFilterByType DEFLATE application/xml
    AddOutputFilterByType DEFLATE application/xhtml+xml
    AddOutputFilterByType DEFLATE application/rss+xml
    AddOutputFilterByType DEFLATE application/javascript
    AddOutputFilterByType DEFLATE application/x-javascript
</IfModule>
```

### **Image Optimization**
- Compress images sebelum upload
- Use WebP format jika didukung
- Implement lazy loading
- Responsive images dengan srcset

## 🔄 **Maintenance dan Update**

### **Regular Backup**
1. **Database backup** via phpMyAdmin
2. **File backup** via File Manager
3. **Automated backup** jika tersedia

### **Update Aplikasi**
1. **Backup dulu** sebelum update
2. **Upload file baru** dengan struktur yang sama
3. **Update database** jika ada migration
4. **Test fitur** setelah update

### **Monitoring**
1. **Check error logs** secara berkala
2. **Monitor disk usage**
3. **Check bandwidth usage**
4. **Test performance** aplikasi

## 📞 **Support dan Dokumentasi**

### **Hosting Provider Support**
- **Hostinger**: Live chat 24/7, ticket system
- **Niagahoster**: Live chat, WhatsApp, ticket
- **IDCloudHost**: Live chat, ticket system
- **RumahWeb**: Live chat, ticket system

### **Community Support**
- Stack Overflow
- GitHub Issues
- PHP Community Forums
- Indonesian Web Developer Groups

### **Resources**
- [cPanel Documentation](https://docs.cpanel.net/)
- [PHP Documentation](https://www.php.net/docs.php)
- [MySQL Documentation](https://dev.mysql.com/doc/)
- [Apache .htaccess Guide](https://httpd.apache.org/docs/current/howto/htaccess.html)

---

**Dibuat oleh:** AI Assistant  
**Tanggal:** 2025-01-21  
**Versi:** 1.0.0  
**Update:** Deployment Shared Hosting
