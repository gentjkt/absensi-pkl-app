# 🏢 Sistem Absensi online basis GPS dan Selfi

Sistem absensi modern untuk monitoring kehadiran siswa PKL dengan fitur GPS tracking, selfie verification, dan real-time monitoring.

## ✨ Fitur Utama

- 🔐 **Sistem Autentikasi Multi-Role** (Admin, Pembimbing, Siswa)
- 📍 **GPS Tracking & Geolocation** - Verifikasi lokasi absensi
- 📸 **WebRTC Camera & Selfie** - Verifikasi kehadiran dengan foto
- 📊 **Dashboard Real-time** - Monitoring absensi secara real-time
- 📤 **Export/Import Data** - Dukungan CSV untuk data siswa
- 📝 **Audit Log System** - Pencatatan semua aktivitas sistem
- 📱 **Responsive Design** - Kompatibel dengan semua device
- 🎨 **Modern UI/UX** - Interface yang user-friendly

## 🛠️ Teknologi yang Digunakan

- **Backend**: PHP 8+ dengan OOP dan MVC Architecture
- **Database**: MySQL dengan PDO
- **Frontend**: HTML5, CSS3 (Custom CSS Framework), JavaScript
- **Libraries**: Leaflet.js (Maps), WebRTC (Camera)
- **Security**: CSRF Protection, Password Hashing, Session Management

## 📋 Persyaratan Sistem

- PHP 8.0 atau lebih tinggi
- MySQL 5.7 atau lebih tinggi
- Web server (Apache/Nginx)
- Browser modern dengan dukungan Geolocation API
- Ekstensi PHP: PDO, PDO_MySQL, JSON

## 🚀 Instalasi

### 1. Clone Repository
```bash
git clone https://github.com/gentjkt/absensi-pkl-app.git
cd absensi-pkl
```

### 2. Setup Database
```bash
# Import database schema
mysql -u root -p < db_absensi_pkl.sql

# Atau buat database manual:
CREATE DATABASE db_absensi_pkl CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 3. Konfigurasi Database
Edit file `config/db.php`:
```php
<?php
return [
    'host' => 'localhost',
    'name' => 'db_absensi_pkl',
    'user' => 'root',
    'pass' => 'password_anda',
    'charset' => 'utf8mb4',
];
```

### 4. Setup Web Server
- Pastikan folder `public/` adalah document root
- Atau buat virtual host yang mengarah ke folder `public/`

### 5. Test Instalasi
```bash
php test_final.php
```

## 🔑 Login Default

| Role | Username | Password |
|------|----------|----------|
| 👨‍💼 Admin | `admin` | `123456` |
| 👨‍🏫 Pembimbing | `pemb1` | `123456` |
| 👨‍🎓 Siswa | `siswa1` | `123456` |

⚠️ **PENTING**: Ubah password default setelah login pertama!

## 📁 Struktur Project

```
absensi-pkl/
├── app/
│   ├── Controllers/          # Controller classes
│   ├── Models/              # Database models
│   ├── Views/               # View templates
│   ├── Helpers/             # Utility classes
│   └── autoload.php         # Class autoloader
├── config/                  # Configuration files
├── public/                  # Public assets & entry point
│   ├── assets/
│   │   ├── css/            # Stylesheets
│   │   └── js/             # JavaScript files
│   └── index.php           # Main entry point
├── db_absensi_pkl.sql      # Database schema
└── README.md               # This file
```

## 🎯 Cara Penggunaan

### Untuk Admin
1. Login dengan akun admin
2. Kelola tempat PKL (tambah/edit lokasi)
3. Kelola data siswa dan pembimbing
4. Import data siswa dari CSV
5. Export laporan absensi
6. Monitor audit log

### Untuk Pembimbing
1. Login dengan akun pembimbing
2. Lihat daftar siswa yang dibimbing
3. Monitor absensi siswa
4. Generate laporan per siswa

### Untuk Siswa
1. Login dengan akun siswa
2. Absen dengan GPS location
3. Ambil selfie untuk verifikasi
4. Lihat riwayat absensi

## 🔧 Troubleshooting

### Masalah Login
- Pastikan database terhubung dengan benar
- Cek apakah tabel `users` sudah terisi
- Pastikan password hash sudah benar

### Masalah GPS
- Pastikan browser mengizinkan akses lokasi
- Cek apakah device mendukung GPS
- Pastikan HTTPS (untuk production)

### Masalah Camera
- Pastikan browser mendukung WebRTC
- Cek permission camera di browser
- Pastikan tidak ada aplikasi lain yang menggunakan camera

## 📱 Fitur Mobile

- Responsive design untuk semua ukuran layar
- Touch-friendly interface
- Optimized untuk mobile browser
- GPS tracking yang akurat di mobile

## 🔒 Keamanan

- CSRF protection untuk semua form
- Password hashing dengan bcrypt
- Session management yang aman
- Input validation dan sanitization
- SQL injection protection dengan prepared statements

## 📊 Monitoring & Reporting

- Real-time dashboard
- Export data ke CSV
- Filter berdasarkan tanggal, siswa, lokasi
- Audit log untuk semua aktivitas
- Statistik kehadiran

## 🤝 Kontribusi

1. Fork repository
2. Buat feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add some AmazingFeature'`)
4. Push ke branch (`git push origin feature/AmazingFeature`)
5. Buat Pull Request

## 📄 License

Project ini dilisensikan di bawah MIT License - lihat file [LICENSE](LICENSE) untuk detail.

## 📞 Support

Jika ada pertanyaan atau masalah:
- Buat issue di GitHub
- Hubungi tim development
- Cek dokumentasi lengkap

## 🎉 Credits

- **Framework**: Custom MVC PHP
- **UI/UX**: Modern CSS Framework
- **Icons**: Emoji & Unicode symbols
- **Maps**: Leaflet.js
- **Camera**: WebRTC API

---

**Dibuat dengan ❤️ untuk dunia pendidikan Indonesia**
