<?php $app = require __DIR__.'/../../../config/app.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Absensi PKL</title>
    
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
                <a href="<?= $homeUrl ?>" class="logo" title="Kembali ke Dashboard">
                    🏠 Home
                </a>
            <?php else: ?>
          
            <?php endif; ?>
        </div>
        
        <div class="topbar-right">
            <?php if(!empty($_SESSION['user'])): ?>
                <div class="user-info">
                    <strong><?= htmlspecialchars($_SESSION['user']['name']) ?></strong>
                    <span class="user-role">(<?= htmlspecialchars(ucfirst($_SESSION['user']['role'])) ?>)</span>
                </div>
                <?php if($_SESSION['user']['role'] === 'admin'): ?>
                    <a href="?r=profile" class="btn btn-info" title="Profile Administrator">
                        ⚙️ Profile
                    </a>
                <?php endif; ?>
                <a href="?r=user/changePassword" class="btn btn-secondary" title="Ubah Password">
                    🔐 Ubah Password
                </a>
                <a href="?r=auth/logout" class="btn btn-danger" title="Logout">
                    🚪 Logout
                </a>
            <?php endif; ?>
        </div>
    </nav>
    
    <!-- Main Content Container -->
    <main class="container">