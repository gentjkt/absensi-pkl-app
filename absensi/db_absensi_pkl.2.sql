-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 16 Agu 2025 pada 21.17
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_absensi_pkl`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `absensi`
--

CREATE TABLE `absensi` (
  `id` int(11) NOT NULL,
  `siswa_id` int(11) NOT NULL,
  `waktu` datetime NOT NULL,
  `lat` decimal(10,7) NOT NULL,
  `lng` decimal(10,7) NOT NULL,
  `jarak_m` decimal(10,2) NOT NULL,
  `selfie_path` varchar(255) DEFAULT NULL,
  `jenis_absen` enum('datang','pulang') NOT NULL DEFAULT 'datang'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `absensi`
--

INSERT INTO `absensi` (`id`, `siswa_id`, `waktu`, `lat`, `lng`, `jarak_m`, `selfie_path`, `jenis_absen`) VALUES
(1, 3, '2025-08-16 19:38:23', -6.2088000, 106.8456000, 50.50, 'uploads/test_selfie.jpg', 'datang'),
(2, 4, '2025-08-16 19:44:11', -7.3782945, 110.2666495, 11.63, 'uploads/selfie_20250816_194411_4_e8cface8.jpg', 'datang'),
(3, 3, '2025-08-16 20:22:35', -6.2088000, 106.8456000, 50.50, NULL, 'pulang'),
(4, 4, '2025-08-16 20:33:11', -7.3782160, 110.2666050, 1.64, 'uploads/selfie_20250816_203311_4_e8b8830a.jpg', 'pulang');

-- --------------------------------------------------------

--
-- Struktur dari tabel `audit_log`
--

CREATE TABLE `audit_log` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `detail` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `audit_log`
--

INSERT INTO `audit_log` (`id`, `user_id`, `action`, `detail`, `created_at`) VALUES
(1, 1, 'login', '', '2025-08-16 15:45:15'),
(2, 3, 'login', '', '2025-08-16 15:47:43'),
(3, 3, 'login', '', '2025-08-16 15:48:09'),
(4, 3, 'login', '', '2025-08-16 15:48:13'),
(5, 3, 'login', '', '2025-08-16 15:48:20'),
(6, 3, 'login', '', '2025-08-16 15:49:22'),
(7, 3, 'login', '', '2025-08-16 15:53:15'),
(8, 3, 'login', '', '2025-08-16 15:54:00'),
(9, 3, 'logout', '', '2025-08-16 15:54:16'),
(10, 3, 'login', '', '2025-08-16 15:54:24'),
(11, 3, 'logout', '', '2025-08-16 15:55:56'),
(12, 2, 'login', '', '2025-08-16 15:56:07'),
(13, 1, 'login', '', '2025-08-16 15:57:46'),
(14, 1, 'login', '', '2025-08-16 15:58:05'),
(15, 1, 'login', '', '2025-08-16 15:58:42'),
(16, 3, 'logout', '', '2025-08-16 16:00:48'),
(17, 3, 'login', '', '2025-08-16 16:01:01'),
(18, 1, 'login', '', '2025-08-16 16:07:58'),
(19, 1, 'logout', '', '2025-08-16 16:08:21'),
(20, 1, 'login', '', '2025-08-16 16:08:29'),
(21, 1, 'create_tempat', 'Kafi Komputer', '2025-08-16 16:11:00'),
(22, 1, 'login', '', '2025-08-16 16:11:12'),
(23, 1, 'create_siswa', 'aku', '2025-08-16 16:11:31'),
(24, 1, 'login', '', '2025-08-16 16:12:56'),
(25, 2, 'login', '', '2025-08-16 16:29:58'),
(26, 1, 'login', '', '2025-08-16 16:30:14'),
(27, 3, 'login', '', '2025-08-16 16:44:57'),
(28, 1, 'export_csv', '', '2025-08-16 16:48:03'),
(29, 3, 'login', '', '2025-08-16 16:54:15'),
(30, 2, 'login', '', '2025-08-16 16:54:56'),
(31, 3, 'login', '', '2025-08-16 16:56:20'),
(32, 3, 'login', '', '2025-08-16 16:56:47'),
(33, 3, 'login', '', '2025-08-16 16:56:50'),
(34, 3, 'logout', '', '2025-08-16 16:59:05'),
(35, 3, 'logout', '', '2025-08-16 16:59:12'),
(36, 3, 'login', '', '2025-08-16 16:59:29'),
(37, 3, 'login', '', '2025-08-16 16:59:53'),
(38, 3, 'logout', '', '2025-08-16 17:01:03'),
(39, 3, 'login', '', '2025-08-16 17:01:15'),
(40, 3, 'logout', '', '2025-08-16 17:03:30'),
(41, 2, 'login', '', '2025-08-16 17:03:38'),
(42, 2, 'login', '', '2025-08-16 17:04:00'),
(43, 1, 'tambah_siswa', 'NIS: 4123123123, Nama: Ahmad', '2025-08-16 17:13:56'),
(44, 1, 'tambah_siswa', 'NIS: 123456789, Nama: Budi', '2025-08-16 17:14:25'),
(45, 3, 'logout', '', '2025-08-16 17:14:34'),
(46, 5, 'login', '', '2025-08-16 17:14:48'),
(47, 2, 'login', '', '2025-08-16 17:15:48'),
(48, 1, 'login', '', '2025-08-16 17:19:26'),
(49, 2, 'login', '', '2025-08-16 17:21:31'),
(50, 1, 'tambah_tempat_pkl', 'Nama: Muffaindo, Lat: -7.4011, Lng: 110.2312, Radius: 50', '2025-08-16 17:34:23'),
(51, 1, 'tambah_siswa', 'NIS: 11223344, Nama: Muffi', '2025-08-16 17:35:12'),
(52, 5, 'logout', '', '2025-08-16 17:35:20'),
(53, 6, 'login', '', '2025-08-16 17:35:33'),
(54, 6, 'logout', '', '2025-08-16 17:38:43'),
(55, 5, 'login', '', '2025-08-16 17:38:56'),
(56, 1, 'login', '', '2025-08-16 17:39:22'),
(57, 1, 'login', '', '2025-08-16 17:40:28'),
(58, 1, 'edit_tempat_pkl', 'ID: 2, Nama: Kafi Komputer, Lat: -7.3782021, Lng: 110.2666, Radius: 150', '2025-08-16 17:42:37'),
(59, 5, 'logout', '', '2025-08-16 17:43:38'),
(60, 5, 'login', '', '2025-08-16 17:43:54'),
(61, 5, 'absen', 'Siswa: Budi, Jarak: 11.63m, Lokasi: -7.3782945, 110.2666495', '2025-08-16 17:44:11'),
(62, 1, 'edit_pembimbing', 'ID: 2, Nama: Dra. Siti Nurhaliza, M.Pd, NIP: 198502022010022002', '2025-08-16 17:55:58'),
(63, 1, 'tambah_pembimbing', 'Nama: Budi Purwanto, NIP: 12345678945555555555', '2025-08-16 17:56:20'),
(64, 1, 'edit_pembimbing', 'ID: 4, Nama: Budi Purwanto, NIP: 123456789455555555, Username: pemb2, Password: diubah', '2025-08-16 18:09:46'),
(65, 1, 'edit_pembimbing', 'ID: 4, Nama: Budi Purwanto, NIP: 123456789455555555, Username: pemb2, Password: diubah', '2025-08-16 18:10:01'),
(66, 2, 'logout', '', '2025-08-16 18:10:09'),
(67, 7, 'login', '', '2025-08-16 18:11:16'),
(68, 1, 'edit_siswa', 'ID: 3, Nama: Ahmad', '2025-08-16 18:11:59'),
(69, 5, 'login', '', '2025-08-16 18:14:46'),
(70, NULL, 'logout', '', '2025-08-16 18:17:51'),
(71, 4, 'login', '', '2025-08-16 18:18:20'),
(72, 5, 'logout', '', '2025-08-16 18:23:20'),
(73, 4, 'login', '', '2025-08-16 18:23:37'),
(74, 4, 'logout', '', '2025-08-16 18:24:34'),
(75, 5, 'login', '', '2025-08-16 18:24:46'),
(76, 5, 'login', '', '2025-08-16 18:32:03'),
(77, 5, 'absen', 'Absen pulang - Lat: -7.378216, Lng: 110.266605, Jarak: 1.64m', '2025-08-16 18:33:11'),
(78, 5, 'login', '', '2025-08-16 18:33:27'),
(79, 1, 'login', '', '2025-08-16 18:40:34'),
(80, 1, 'login', '', '2025-08-16 18:43:29'),
(81, 1, 'login', '', '2025-08-16 18:44:00'),
(82, 1, 'logout', '', '2025-08-16 18:45:50'),
(83, 1, 'login', '', '2025-08-16 18:47:47'),
(84, 1, 'logout', '', '2025-08-16 18:47:59'),
(85, 1, 'login', '', '2025-08-16 18:50:24'),
(86, 1, 'logout', '', '2025-08-16 18:57:13'),
(87, 1, 'login', '', '2025-08-16 18:57:24'),
(88, 4, 'logout', '', '2025-08-16 18:58:27'),
(89, 7, 'logout', '', '2025-08-16 19:02:22'),
(90, 1, 'login', '', '2025-08-16 19:03:13'),
(91, 1, 'login', '', '2025-08-16 19:15:49'),
(92, 1, 'logout', '', '2025-08-16 19:16:08'),
(93, 3, 'login', '', '2025-08-16 19:16:18'),
(94, 3, 'logout', '', '2025-08-16 19:16:34'),
(95, 3, 'login', '', '2025-08-16 19:16:39');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pembimbing`
--

CREATE TABLE `pembimbing` (
  `id` int(11) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `nip` varchar(18) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `pembimbing`
--

INSERT INTO `pembimbing` (`id`, `nama`, `nip`, `user_id`, `created_at`, `updated_at`) VALUES
(2, 'Dra. Siti Nurhaliza, M.Pd', '198502022010022002', 8, '2025-08-16 17:55:27', '2025-08-16 18:07:49'),
(3, 'Ir. Bambang Setiawan, M.T', '198503032010032003', 9, '2025-08-16 17:55:27', '2025-08-16 18:07:49'),
(4, 'Budi Purwanto', '123456789455555555', 7, '2025-08-16 17:56:20', '2025-08-16 18:07:49');

-- --------------------------------------------------------

--
-- Struktur dari tabel `siswa`
--

CREATE TABLE `siswa` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `nis` varchar(50) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `kelas` varchar(50) NOT NULL,
  `pembimbing_id` int(11) NOT NULL,
  `tempat_pkl_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `siswa`
--

INSERT INTO `siswa` (`id`, `user_id`, `nis`, `nama`, `kelas`, `pembimbing_id`, `tempat_pkl_id`) VALUES
(1, 3, '1234567890', 'Siswa Satu', 'XII TKJ 1', 2, 1),
(2, NULL, '212321323', 'aku', 'tjkt', 2, 2),
(3, 4, '4123123123', 'Ahmad', 'XIITJKT2', 7, 2),
(4, 5, '123456789', 'Budi', 'XII', 2, 2),
(5, 6, '11223344', 'Muffi', 'XIIDKV', 2, 3);

-- --------------------------------------------------------

--
-- Struktur dari tabel `tempat_pkl`
--

CREATE TABLE `tempat_pkl` (
  `id` int(11) NOT NULL,
  `nama` varchar(150) NOT NULL,
  `lat` decimal(10,7) NOT NULL,
  `lng` decimal(10,7) NOT NULL,
  `radius_m` int(11) NOT NULL DEFAULT 150
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `tempat_pkl`
--

INSERT INTO `tempat_pkl` (`id`, `nama`, `lat`, `lng`, `radius_m`) VALUES
(1, 'PT Contoh Teknologi', -6.2000000, 106.8166667, 150),
(2, 'Kafi Komputer', -7.3782021, 110.2666000, 150),
(3, 'Muffaindo', -7.4011000, 110.2312000, 50);

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('admin','pembimbing','siswa') NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `password_hash`, `role`, `name`) VALUES
(1, 'admin', '$2y$10$OA.A8EQCWYEj0WJbQaFBD.t/rcqQxr5LA/0qONoakAuGlUtKXca2i', 'admin', 'Administrator'),
(2, 'pemb1', '$2y$10$OA.A8EQCWYEj0WJbQaFBD.t/rcqQxr5LA/0qONoakAuGlUtKXca2i', 'pembimbing', 'Pembimbing Satu'),
(3, 'siswa1', '$2y$10$CmDo47qu7VMWjtcSMOu82OAgyTvvOLmAhp/30wn/wAofjMmmqvNiu', 'siswa', 'Siswa Satu'),
(4, '4123123123', '$2y$10$VBM/Yhiy0GpwUWv4oGC0p.E5OR/WhWKapzz5qmsevQ2YKKAIaaYfi', 'siswa', 'Ahmad'),
(5, '123456789', '$2y$10$6ul045vKi7mrZZnQMnrZ8uEYs8NqyOYRWkVN0egT9bbxwF1Imx7KG', 'siswa', 'Budi'),
(6, '11223344', '$2y$10$0zsmRSHQqc3md1bPVMXsQudZrGSdT4ci4qIn.RjkCkJxvsnLFAelC', 'siswa', 'Muffi'),
(7, 'pemb123456789455555555', '$2y$10$dHPpf3IK.RbtnpqLF1NNV.OxzEF7N1viCAQbj55zp.c31IabAjZ56', 'pembimbing', 'Budi Purwanto'),
(8, 'pemb198502022010022002', '$2y$10$y/i3CO0WCg.g8em6ap5IkubXpSN3Cxq.5tgKkTGqZv7KIzOAk72HC', 'pembimbing', 'Dra. Siti Nurhaliza, M.Pd'),
(9, 'pemb198503032010032003', '$2y$10$ouaQM5buOT5rys9wLyggu.t5jE3qcWSolNrVb3.u6YDPSlfkXS1qm', 'pembimbing', 'Ir. Bambang Setiawan, M.T');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `absensi`
--
ALTER TABLE `absensi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `siswa_id` (`siswa_id`);

--
-- Indeks untuk tabel `audit_log`
--
ALTER TABLE `audit_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `pembimbing`
--
ALTER TABLE `pembimbing`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nip` (`nip`);

--
-- Indeks untuk tabel `siswa`
--
ALTER TABLE `siswa`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `pembimbing_id` (`pembimbing_id`),
  ADD KEY `tempat_pkl_id` (`tempat_pkl_id`);

--
-- Indeks untuk tabel `tempat_pkl`
--
ALTER TABLE `tempat_pkl`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `absensi`
--
ALTER TABLE `absensi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `audit_log`
--
ALTER TABLE `audit_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=96;

--
-- AUTO_INCREMENT untuk tabel `pembimbing`
--
ALTER TABLE `pembimbing`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `siswa`
--
ALTER TABLE `siswa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `tempat_pkl`
--
ALTER TABLE `tempat_pkl`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `absensi`
--
ALTER TABLE `absensi`
  ADD CONSTRAINT `absensi_ibfk_1` FOREIGN KEY (`siswa_id`) REFERENCES `siswa` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `audit_log`
--
ALTER TABLE `audit_log`
  ADD CONSTRAINT `audit_log_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `siswa`
--
ALTER TABLE `siswa`
  ADD CONSTRAINT `siswa_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `siswa_ibfk_2` FOREIGN KEY (`pembimbing_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `siswa_ibfk_3` FOREIGN KEY (`tempat_pkl_id`) REFERENCES `tempat_pkl` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
