<?php
declare(strict_types=1);

return [
    // Nama aplikasi
    'name' => 'Absensi PKL',
    'version' => '1.0.0',
    'description' => 'Sistem Absensi Praktik Kerja Lapangan',
    
    // Konfigurasi session
    'session_name' => 'absensi_pkl_session',
    'session_lifetime' => 3600, // 1 jam
    
    // Konfigurasi timezone
    'timezone' => 'Asia/Jakarta',
    
    // Konfigurasi upload
    'upload_path' => __DIR__ . '/../public/uploads/',
    'max_file_size' => 5 * 1024 * 1024, // 5MB
    'allowed_image_types' => ['image/jpeg', 'image/png', 'image/gif'],
    
    // Konfigurasi GPS
    'gps_accuracy_threshold' => 100, // meter
    'gps_timeout' => 30, // detik
    
    // Konfigurasi aplikasi
    'debug' => false,
    'maintenance_mode' => false,
    
    // Konfigurasi email (untuk fitur future)
    'mail' => [
        'host' => 'smtp.gmail.com',
        'port' => 587,
        'encryption' => 'tls',
        'username' => '',
        'password' => '',
        'from_address' => 'noreply@absensi-pkl.com',
        'from_name' => 'Sistem Absensi PKL'
    ],
    
    // Konfigurasi notifikasi
    'notifications' => [
        'email_enabled' => false,
        'sms_enabled' => false,
        'push_enabled' => false
    ]
];