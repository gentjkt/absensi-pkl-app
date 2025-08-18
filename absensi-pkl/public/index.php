<?php
declare(strict_types=1);

session_name((require __DIR__.'/../config/app.php')['session_name']);
session_start();

require __DIR__.'/../app/autoload.php';

use App\Controllers\{AuthController, AdminController, StudentController, PembimbingController, UserController, ProfileController};

$appCfg = require __DIR__.'/../config/app.php';

// ambil route dari query string, contoh: ?r=auth/login
$r = $_GET['r'] ?? 'auth/login';
[$c, $a] = array_pad(explode('/', $r, 2), 2, 'index');

$controller = null;

// routing utama
switch($c){
    case 'auth': $controller = new AuthController($appCfg); break;
    case 'admin': $controller = new AdminController($appCfg); break;
    case 'student': $controller = new StudentController($appCfg); break;
    case 'pembimbing': $controller = new PembimbingController($appCfg); break;
    case 'user': $controller = new UserController($appCfg); break;
    case 'profile': $controller = new ProfileController($appCfg); break;
    default:
        $controller = new AuthController($appCfg);
        $a = 'login';
}

// cek apakah method ada di controller
if (!method_exists($controller, $a)) {
    http_response_code(404);
    echo 'Not Found';
    exit;
}

// jalankan method sesuai route
$controller->$a();
