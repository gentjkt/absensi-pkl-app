<?php $app = require __DIR__.'/../../../config/app.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($app['name']) ?></title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="" crossorigin=""/>
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- Header Navigation -->
    <nav class="topbar">
        <div class="topbar-left">
            <?php if(!empty($_SESSION['user'])): ?>
                <!-- Mobile Left Hamburger Menu -->
                <div class="mobile-left-menu-toggle">
                    <button id="mobileLeftMenuBtn" class="mobile-left-menu-btn" aria-label="Toggle Left Menu">
                        <span></span>
                        <span></span>
                        <span></span>
                    </button>
                </div>
                
                <!-- Desktop Logo -->
                <?php 
                $homeUrl = '';
                switch($_SESSION['user']['role']) {
                    case 'admin':
                        $homeUrl = '?r=admin/dashboard';
                        break;
                    case 'siswa':
                        $homeUrl = '?r=student/dashboard';
                        break;
                    case 'pembimbing':
                        $homeUrl = '?r=pembimbing/dashboard';
                        break;
                    default:
                        $homeUrl = '?';
                }
                ?>
                <a href="<?= $homeUrl ?>" class="logo desktop-only" title="Kembali ke Dashboard">
                    🏠 Home
                </a>
                
                <?php if($_SESSION['user']['role'] === 'admin'): ?>
                    <!-- Menu Navigasi Admin - Desktop -->
                    <div class="admin-nav desktop-only">
                        <a href="?r=profile" class="btn btn-info" title="Profile Administrator">
                        ⚙️ Profile
                        </a>
                        <a href="?r=admin/absensi" class="btn btn-primary" title="Absensi">
                            📊 Lihat semua absensi
                        </a>
                        <a href="?r=admin/siswa" class="btn btn-primary" title="Kelola Siswa">
                            👥 Kelola Siswa
                        </a>
                        <a href="?r=admin/pembimbing" class="btn btn-primary" title="Kelola Pembimbing">
                            👨‍🏫 Kelola Pembimbing
                        </a>
                        <a href="?r=admin/tempat" class="btn btn-primary" title="KelolaTempat PKL">
                            🏢 Tempat PKL
                        </a>
                        <a href="?r=admin/report" class="btn btn-primary" title="Laporan">
                            📋 Laporan
                        </a>
                    </div>
                <?php endif; ?>
            <?php else: ?>
          
            <?php endif; ?>
        </div>
        
        <div class="topbar-right">
            <?php if(!empty($_SESSION['user'])): ?>
        
                <!-- User Info - Desktop -->
                <div class="user-info desktop-only">
                    <strong><?= htmlspecialchars($_SESSION['user']['name']) ?></strong>
                    <span class="user-role">(<?= htmlspecialchars(ucfirst($_SESSION['user']['role'])) ?>)</span>
                </div>
                
                <!-- User Actions - Desktop -->
                <div class="user-actions desktop-only">
                    <?php if($_SESSION['user']['role'] === 'admin'): ?>
                    
                    <?php endif; ?>
                    <a href="?r=user/changePassword" class="btn btn-secondary" title="Ubah Password">
                        🔐 Ubah Password
                    </a>
                    <a href="?r=auth/logout" class="btn btn-danger" title="Logout">
                        🚪 Logout
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </nav>
    
    <!-- Mobile Left Menu Dropdown -->
    <?php if(!empty($_SESSION['user'])): ?>
    <div id="mobileLeftMenu" class="mobile-left-menu">
        <div class="mobile-left-menu-header">
            <div class="mobile-left-menu-title">
                <h3>📱 Menu Utama</h3>
            </div>
        </div>
        
        <div class="mobile-left-menu-content">
            <!-- Home Menu -->
            <div class="mobile-left-menu-section">
                <h4>🏠 Beranda</h4>
                <a href="<?= $homeUrl ?>" class="mobile-left-menu-item">
                    🏠 Dashboard
                </a>
            </div>
            
            <?php if($_SESSION['user']['role'] === 'admin'): ?>
                <!-- Menu Admin Mobile -->
                <div class="mobile-left-menu-section">
                    <h4>📋 Menu Admin</h4>
                    <a href="?r=profile" class="mobile-left-menu-item">
                        ⚙️ Profile
                    </a>
                    <a href="?r=admin/absensi" class="mobile-left-menu-item">
                        📊 Lihat semua absensi
                    </a>
                    <a href="?r=admin/siswa" class="mobile-left-menu-item">
                        👥 Kelola Siswa
                    </a>
                    <a href="?r=admin/pembimbing" class="mobile-left-menu-item">
                        👨‍🏫 Kelola Pembimbing
                    </a>
                    <a href="?r=admin/tempat" class="mobile-left-menu-item">
                        🏢 Tempat PKL
                    </a>
                    <a href="?r=admin/report" class="mobile-left-menu-item">
                        📋 Laporan
                    </a>
                </div>
            <?php endif; ?>
            
            <!-- Menu User Mobile -->
            <div class="mobile-left-menu-section">
                <h4>👤 Menu User</h4>
                <a href="?r=user/changePassword" class="mobile-left-menu-item">
                    🔐 Ubah Password
                </a>
                <a href="?r=auth/logout" class="mobile-left-menu-item mobile-left-menu-danger">
                    🚪 Logout
                </a>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Main Content Container -->
    <main class="container">
    
    <script>
    // Mobile Menu Toggle
    document.addEventListener('DOMContentLoaded', function() {
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const mobileMenu = document.getElementById('mobileMenu');
        const mobileLeftMenuBtn = document.getElementById('mobileLeftMenuBtn');
        const mobileLeftMenu = document.getElementById('mobileLeftMenu');
        
        // Right Mobile Menu (existing)
        if (mobileMenuBtn && mobileMenu) {
            mobileMenuBtn.addEventListener('click', function() {
                mobileMenu.classList.toggle('active');
                mobileMenuBtn.classList.toggle('active');
                // Close left menu if open
                if (mobileLeftMenu) mobileLeftMenu.classList.remove('active');
                if (mobileLeftMenuBtn) mobileLeftMenuBtn.classList.remove('active');
            });
            
            // Tutup menu saat klik di luar menu
            document.addEventListener('click', function(event) {
                if (!mobileMenuBtn.contains(event.target) && !mobileMenu.contains(event.target)) {
                    mobileMenu.classList.remove('active');
                    mobileMenuBtn.classList.remove('active');
                }
            });
        }
        
        // Left Mobile Menu (new)
        if (mobileLeftMenuBtn && mobileLeftMenu) {
            mobileLeftMenuBtn.addEventListener('click', function() {
                mobileLeftMenu.classList.toggle('active');
                mobileLeftMenuBtn.classList.toggle('active');
                // Close right menu if open
                if (mobileMenu) mobileMenu.classList.remove('active');
                if (mobileMenuBtn) mobileMenuBtn.classList.remove('active');
            });
            
            // Tutup menu saat klik di luar menu
            document.addEventListener('click', function(event) {
                if (!mobileLeftMenuBtn.contains(event.target) && !mobileLeftMenu.contains(event.target)) {
                    mobileLeftMenu.classList.remove('active');
                    mobileLeftMenuBtn.classList.remove('active');
                }
            });
        }
        
        // Tutup semua menu saat resize window ke desktop
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                if (mobileMenu) mobileMenu.classList.remove('active');
                if (mobileMenuBtn) mobileMenuBtn.classList.remove('active');
                if (mobileLeftMenu) mobileLeftMenu.classList.remove('active');
                if (mobileLeftMenuBtn) mobileLeftMenuBtn.classList.remove('active');
            }
        });
    });
    </script>