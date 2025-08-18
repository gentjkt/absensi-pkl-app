ABSENSI PKL - PHP 8 OOP (MVC Sederhana) + GPS + Selfie
=====================================================

1) Persiapan
   - XAMPP (PHP 8+), aktifkan Apache & MySQL.
   - Buat folder: htdocs/absensi-pkl, lalu salin semua isi proyek ini ke sana.

2) Import Database
   - Buka phpMyAdmin -> Import -> pilih file db_absensi_pkl.sql.
   - Database: db_absensi_pkl (akan dibuat otomatis bila belum ada).

3) Konfigurasi Aplikasi
   - Edit config/db.php jika username/password MySQL Anda berbeda.
   - Edit config/app.php -> set 'base_url' contoh: http://localhost/absensi-pkl/public
   - GANTI 'csrf_key' dengan string acak (panjang).

4) Jalankan
   - Akses: http://localhost/absensi-pkl/public
   - Login:
     * admin / 123456 (admin)
     * pemb1 / 123456 (pembimbing)
     * siswa1 / 123456 (siswa)

5) Alur Singkat
   - Admin: tambah Tempat PKL (nama + lat,lng + radius), tambah data Siswa (kaitkan pembimbing & tempat).
   - Siswa: buka Dashboard -> izinkan lokasi & kamera -> Ambil Selfie -> Kirim Absen.
   - Pembimbing: melihat absensi siswa bimbingan.
   - Admin: export CSV (bisa dibuka di Excel). PDF dapat dilakukan via "Print to PDF" dari browser pada halaman laporan.

6) Keamanan & Catatan
   - Semua query menggunakan PDO Prepared Statements.
   - Upload selfie disimpan ke /public/uploads/selfies dan dicegah eksekusi skrip via .htaccess.
   - Ukuran maksimum file selfie default 2MB, atur di config/app.php.
   - Hanya menerima JPEG/PNG; pengambilan foto wajib dari kamera (getUserMedia) di sisi klien.
   - Radius validasi memakai rumus Haversine di server.
   - Untuk domain/HTTPS: akses kamera & lokasi lebih stabil melalui HTTPS atau localhost.

7) Struktur Folder
   app/
     Controllers/ (Auth, Admin, Student, Pembimbing)
     Models/ (Database, BaseModel, User, Siswa, TempatPKL, Absensi)
     Views/ (layouts/, auth/, student/, admin/, pembimbing/)
     Helpers/ (Auth, CSRF, Response, Geo)
   public/
     assets/js (camera.js) | assets/css (style.css)
     uploads/selfies (.htaccess proteksi)
     index.php (front controller)
   config/ (app.php, db.php)
   db_absensi_pkl.sql
   README_INSTALL.txt

8) Pengembangan Lanjut (opsional)
   - Tambah fitur filter laporan (tanggal, kelas, tempat) dan tombol "Cetak/Print" untuk PDF.
   - Tambah pengelolaan akun (ubah password, reset).
   - Tambah fitur import CSV untuk data siswa massal.
   - Tambah halaman peta gabungan (Leaflet) untuk melihat heatmap kehadiran.

Selesai. Selamat mencoba!