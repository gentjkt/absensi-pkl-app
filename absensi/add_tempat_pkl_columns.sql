-- Script SQL untuk menambahkan kolom pemilik dan alamat ke tabel tempat_pkl
-- Jalankan script ini di phpMyAdmin atau MySQL client

USE db_absensi_pkl;

-- 1. Tambah kolom pemilik
ALTER TABLE tempat_pkl 
ADD COLUMN pemilik VARCHAR(100) DEFAULT '' AFTER nama;

-- 2. Tambah kolom alamat
ALTER TABLE tempat_pkl 
ADD COLUMN alamat TEXT DEFAULT '' AFTER pemilik;

-- 3. Verifikasi struktur tabel
DESCRIBE tempat_pkl;

-- 4. Update data yang sudah ada (opsional)
-- UPDATE tempat_pkl SET pemilik = 'Pemilik Default', alamat = 'Alamat Default' WHERE pemilik IS NULL OR pemilik = '';

-- 5. Tampilkan data yang sudah ada
SELECT id, nama, pemilik, alamat, lat, lng, radius_m FROM tempat_pkl ORDER BY id;
