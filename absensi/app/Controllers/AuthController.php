<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Database;
use App\Models\User;
use App\Models\AuditLog;
use App\Helpers\Response;
use App\Helpers\CSRF;
use App\Helpers\Auth;

class AuthController extends Controller
{
    public function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!CSRF::verify($_POST['csrf'] ?? '')) {
                http_response_code(400);
                echo 'Invalid CSRF';
                return;
            }

            // Cek apakah CAPTCHA diaktifkan melalui AppSettings
            $db = new Database(require __DIR__ . '/../../config/db.php');
            $appSettings = new \App\Models\AppSettings($db);
            $captchaEnabled = (string)$appSettings->getSetting('login_captcha_enabled');
            $captchaEnabledBool = in_array(strtolower($captchaEnabled), ['1','true','on','yes'], true);

            if ($captchaEnabledBool) {
                // Validasi CAPTCHA sederhana (hanya jika aktif)
                $inputCaptcha = strtolower(trim((string)($_POST['captcha'] ?? '')));
                $sessionCaptcha = strtolower((string)($_SESSION['captcha_login'] ?? ''));
                // Hapus kode CAPTCHA dari sesi untuk mencegah replay
                unset($_SESSION['captcha_login']);
                if ($inputCaptcha === '' || $sessionCaptcha === '' || $inputCaptcha !== $sessionCaptcha) {
                    $error = 'Kode CAPTCHA salah atau kosong';
                    $app = require __DIR__ . '/../../config/app.php';
                    $this->view('auth/login', ['error' => $error, 'app' => $app, 'captchaEnabled' => true]);
                    return;
                }
            }

            $m = new User($db);
            $username = trim((string)($_POST['username'] ?? ''));
            $user = $m->findByUsername($username);

            if ($user && password_verify((string)($_POST['password'] ?? ''), $user['password_hash'])) {
                unset($user['password_hash']);
                Auth::login($user);

                // Log login
                (new AuditLog($db))->add((int)$user['id'], 'login', '');

                if ($user['role'] === 'admin') {
                    Response::redirect('?r=admin/dashboard');
                } elseif ($user['role'] === 'pembimbing') {
                    Response::redirect('?r=pembimbing/dashboard');
                } else {
                    Response::redirect('?r=student/dashboard');
                }
            } else {
                $error = 'Username atau password salah';
                // Load config untuk view
                $app = require __DIR__ . '/../../config/app.php';
                $this->view('auth/login', ['error' => $error, 'app' => $app]);
            }
        } else {
            // Load config untuk view + status captcha
            $db = new Database(require __DIR__ . '/../../config/db.php');
            $appSettings = new \App\Models\AppSettings($db);
            $captchaEnabled = (string)$appSettings->getSetting('login_captcha_enabled');
            $captchaEnabledBool = in_array(strtolower($captchaEnabled), ['1','true','on','yes'], true);
            $app = require __DIR__ . '/../../config/app.php';
            $this->view('auth/login', ['app' => $app, 'captchaEnabled' => $captchaEnabledBool]);
        }
    }

    /**
     * Endpoint untuk menghasilkan gambar CAPTCHA login.
     * - Menyimpan kode di session `captcha_login`
     * - Mengembalikan gambar PNG dengan sedikit noise
     */
    public function captcha(): void
    {
        // Panjang kode CAPTCHA
        $length = 5;
        $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789'; // tanpa karakter ambigu
        $code = '';
        for ($i = 0; $i < $length; $i++) {
            $code .= $alphabet[random_int(0, strlen($alphabet) - 1)];
        }

        // Simpan ke session untuk validasi login
        $_SESSION['captcha_login'] = $code;

        // Parameter gambar
        $width = 160;
        $height = 50;

        // Pastikan GD tersedia
        if (!function_exists('imagecreatetruecolor')) {
            http_response_code(500);
            header('Content-Type: text/plain');
            echo 'GD library not available';
            return;
        }

        $img = imagecreatetruecolor($width, $height);
        $bg = imagecolorallocate($img, 245, 246, 250);
        imagefilledrectangle($img, 0, 0, $width, $height, $bg);

        // Garis noise
        for ($i = 0; $i < 8; $i++) {
            $color = imagecolorallocate($img, random_int(150, 200), random_int(150, 200), random_int(150, 200));
            imageline($img, random_int(0, $width), random_int(0, $height), random_int(0, $width), random_int(0, $height), $color);
        }

        // Titik noise
        for ($i = 0; $i < 300; $i++) {
            $color = imagecolorallocate($img, random_int(180, 220), random_int(180, 220), random_int(180, 220));
            imagesetpixel($img, random_int(0, $width - 1), random_int(0, $height - 1), $color);
        }

        // Tulis teks
        $textColor = imagecolorallocate($img, 50, 50, 50);
        $fontSize = 5; // font built-in
        $textBoxWidth = imagefontwidth($fontSize) * strlen($code);
        $textBoxHeight = imagefontheight($fontSize);
        $x = (int)(($width - $textBoxWidth) / 2);
        $y = (int)(($height - $textBoxHeight) / 2);
        imagestring($img, $fontSize, $x, $y, $code, $textColor);

        // Output gambar
        header('Content-Type: image/png');
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Pragma: no-cache');
        imagepng($img);
        imagedestroy($img);
    }

    public function logout(): void
    {
        $db = new Database(require __DIR__ . '/../../config/db.php');
        (new AuditLog($db))->add($_SESSION['user']['id'] ?? null, 'logout', '');
        Auth::logout();
        Response::redirect('?r=auth/login');
    }

    public function changePassword(): void
    {
        Auth::requireRole('admin', 'pembimbing', 'siswa');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!CSRF::verify($_POST['csrf'] ?? '')) {
                http_response_code(400);
                echo 'Invalid CSRF';
                return;
            }

            $old = (string)($_POST['old'] ?? '');
            $new = (string)($_POST['new'] ?? '');
            $confirm = (string)($_POST['confirm'] ?? '');

            if ($new === '' || $new !== $confirm) {
                $error = 'Konfirmasi password tidak cocok';
                $this->view('auth/change_password', ['error' => $error]);
                return;
            }

            $db = new Database(require __DIR__ . '/../../config/db.php');
            $st = $db->pdo()->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
            $st->execute([$_SESSION['user']['id']]);
            $user = $st->fetch();

            if (!$user || !password_verify($old, $user['password_hash'])) {
                $error = 'Password lama salah';
                $this->view('auth/change_password', ['error' => $error]);
                return;
            }

            $hash = password_hash($new, PASSWORD_DEFAULT);
            $st = $db->pdo()->prepare('UPDATE users SET password_hash = ? WHERE id = ?');
            $st->execute([$hash, $_SESSION['user']['id']]);

            (new AuditLog($db))->add($_SESSION['user']['id'], 'change_password', '');
            $success = 'Password berhasil diubah';
            $this->view('auth/change_password', ['success' => $success]);
        } else {
            $this->view('auth/change_password');
        }
    }
}
