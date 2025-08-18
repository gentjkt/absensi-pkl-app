<?php
declare(strict_types=1);

return [
    // Nama aplikasi
    'name' => 'Absensi PKL',
    'version' => '1.0.0',
    'description' => 'Sistem Absensi Praktik Kerja Lapangan',
    
    // Konfigurasi session
    'session_name' => 'absensi_pkl_session',
    'session_lifetime' => 7200, // 2 jam untuk production
    
    // Konfigurasi timezone
    'timezone' => 'Asia/Jakarta',
    
    // Konfigurasi upload
    'upload_path' => __DIR__ . '/../public/uploads/',
    'max_file_size' => 10 * 1024 * 1024, // 10MB untuk production
    'allowed_image_types' => ['image/jpeg', 'image/png'],
    
    // Konfigurasi GPS
    'gps_accuracy_threshold' => 50, // Lebih ketat untuk production
    'gps_timeout' => 15, // Lebih cepat untuk production
    
    // Konfigurasi aplikasi
    'debug' => false,
    'maintenance_mode' => false,
    
    // Konfigurasi email
    'mail' => [
        'host' => 'smtp.gmail.com',
        'port' => 587,
        'encryption' => 'tls',
        'username' => 'your-email@gmail.com',
        'password' => 'your-app-password',
        'from_address' => 'noreply@yourdomain.com',
        'from_name' => 'Sistem Absensi PKL'
    ],
    
    // Konfigurasi notifikasi
    'notifications' => [
        'email_enabled' => true,
        'sms_enabled' => false,
        'push_enabled' => false
    ],
    
    // Konfigurasi keamanan
    'security' => [
        'password_min_length' => 8,
        'session_regenerate_id' => true,
        'csrf_token_expiry' => 3600,
        'max_login_attempts' => 5,
        'lockout_duration' => 900 // 15 menit
    ],
    
    // Konfigurasi logging
    'logging' => [
        'enabled' => true,
        'level' => 'info',
        'file' => __DIR__ . '/../logs/app.log',
        'max_files' => 30
    ]
];
