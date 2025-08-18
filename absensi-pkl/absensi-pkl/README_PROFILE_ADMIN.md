# Fitur Profile Administrator

## Deskripsi
Fitur Profile Administrator memungkinkan administrator untuk mengelola pengaturan aplikasi dan informasi sekolah secara dinamis melalui interface web.

## Fitur Utama

### 1. Pengaturan Sekolah
- **Nama Sekolah**: Nama lengkap sekolah yang akan ditampilkan di aplikasi
- **Alamat Sekolah**: Alamat lengkap sekolah
- **Nomor Telepon**: Kontak telepon sekolah
- **Email Sekolah**: Alamat email resmi sekolah
- **Website Sekolah**: URL website resmi sekolah

### 2. Pengaturan Sistem
- **Judul Aplikasi**: Nama aplikasi yang ditampilkan di header
- **Radius GPS**: Jarak maksimal untuk absensi (dalam meter)
- **Ukuran Upload Maksimal**: Batas ukuran file upload (dalam MB)
- **Timeout Session**: Waktu timeout session (dalam detik)

### 3. Fitur Tambahan
- **Reset Pengaturan**: Kembalikan semua pengaturan ke nilai default
- **Validasi Input**: Validasi otomatis berdasarkan tipe data
- **Audit Log**: Mencatat semua perubahan pengaturan
- **CSRF Protection**: Keamanan dari serangan CSRF

## Cara Menggunakan

### 1. Akses Menu Profile
- Login sebagai administrator
- Klik tombol "⚙️ Profile" di header atau dashboard
- Atau akses langsung: `?r=profile`

### 2. Edit Pengaturan
- Pilih kategori pengaturan yang ingin diubah
- Edit nilai sesuai kebutuhan
- Klik tombol "💾 Simpan" untuk menyimpan perubahan

### 3. Reset Pengaturan
- Scroll ke bagian "⚠️ Zona Berbahaya"
- Klik tombol "🔄 Reset Semua Pengaturan ke Default"
- Konfirmasi tindakan

## Struktur Database

### Tabel: app_settings
```sql
CREATE TABLE app_settings (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    setting_type VARCHAR(20) DEFAULT 'text',
    description TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
```

### Tipe Data yang Didukung
- **text**: Teks biasa
- **email**: Alamat email
- **url**: URL website
- **number**: Angka

## File yang Dibuat/Dimodifikasi

### 1. Model
- `app/Models/AppSettings.php` - Model untuk mengelola pengaturan

### 2. Controller
- `app/Controllers/ProfileController.php` - Controller untuk halaman profile

### 3. View
- `app/Views/admin/profile.php` - Interface halaman profile

### 4. Routing
- `public/index.php` - Menambahkan route untuk profile

### 5. CSS
- `public/assets/css/style.css` - Menambahkan style untuk tombol info

### 6. SQL
- `create_app_settings_table.sql` - Script untuk membuat tabel

## Keamanan

### 1. Autentikasi
- Hanya administrator yang dapat mengakses
- Menggunakan `Auth::requireRole('admin')`

### 2. CSRF Protection
- Menggunakan helper CSRF yang sudah ada
- Validasi token di setiap form submission

### 3. Validasi Input
- Validasi berdasarkan tipe data
- Sanitasi output menggunakan `htmlspecialchars()`

### 4. Audit Log
- Mencatat semua perubahan pengaturan
- Informasi user, waktu, dan detail perubahan

## Penggunaan dalam Aplikasi

### 1. Mengambil Pengaturan
```php
use App\Models\AppSettings;

$appSettings = new AppSettings($db);
$schoolName = $appSettings->getSetting('school_name');
$gpsRadius = $appSettings->getSetting('gps_radius');
```

### 2. Update Pengaturan
```php
$appSettings->updateSetting('school_name', 'SMK Negeri 1 Jakarta');
```

### 3. Update Multiple Settings
```php
$settings = [
    'school_name' => 'SMK Negeri 1 Jakarta',
    'gps_radius' => '200'
];
$appSettings->updateMultipleSettings($settings);
```

## Troubleshooting

### 1. Tabel Tidak Terbuat
- Jalankan script SQL: `create_app_settings_table.sql`
- Pastikan database memiliki permission untuk CREATE TABLE

### 2. Error CSRF
- Pastikan session sudah dimulai
- Periksa apakah helper CSRF sudah di-load

### 3. Permission Denied
- Pastikan user login sebagai administrator
- Periksa role di session

## Pengembangan Selanjutnya

### 1. Fitur Tambahan
- Upload logo sekolah
- Pengaturan tema aplikasi
- Konfigurasi email SMTP
- Pengaturan backup otomatis

### 2. Integrasi
- Integrasi dengan sistem notifikasi
- Export/import pengaturan
- API untuk mobile app

### 3. Monitoring
- Dashboard monitoring perubahan pengaturan
- Notifikasi perubahan real-time
- History lengkap perubahan

## Catatan Penting

1. **Backup**: Selalu backup database sebelum melakukan perubahan besar
2. **Testing**: Test fitur di environment development terlebih dahulu
3. **Documentation**: Update dokumentasi setiap ada perubahan
4. **Security**: Review keamanan secara berkala
5. **Performance**: Monitor performa query database

## Support

Jika ada masalah atau pertanyaan, silakan hubungi tim development atau buat issue di repository.
