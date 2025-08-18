<?php
/**
 * Konfigurasi Timezone untuk aplikasi absensi PKL
 * Mengatur zona waktu ke WIB (Waktu Indonesia Barat)
 */

// Set timezone ke Asia/Jakarta (WIB)
date_default_timezone_set('Asia/Jakarta');

// Konfigurasi timezone untuk aplikasi
return [
    'timezone' => 'Asia/Jakarta',
    'timezone_name' => 'WIB',
    'timezone_offset' => '+7',
    'timezone_description' => 'Waktu Indonesia Barat (UTC+7)',
    
    // Format waktu yang digunakan
    'date_format' => 'd/m/Y',
    'time_format' => 'H:i',
    'datetime_format' => 'd/m/Y H:i',
    'datetime_db_format' => 'Y-m-d H:i:s',
    
    // Timezone untuk database (jika diperlukan)
    'db_timezone' => '+07:00',
    
    // Timezone untuk export/import
    'export_timezone' => 'Asia/Jakarta',
    
    // Timezone untuk logging
    'log_timezone' => 'Asia/Jakarta'
];
?>
