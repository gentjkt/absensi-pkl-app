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

            $db = new Database(require __DIR__ . '/../../config/db.php');
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
                
                // Ambil pengaturan sekolah untuk subtitle
                $db = new Database(require __DIR__ . '/../../config/db.php');
                try {
                    $appSettings = new \App\Models\AppSettings($db);
                    $schoolName = $appSettings->getSetting('school_name') ?? 'Sistem Absensi PKL';
                } catch (Exception $e) {
                    $schoolName = 'Sistem Absensi PKL';
                }
                
                $this->view('auth/login', ['error' => $error, 'schoolName' => $schoolName]);
            }
        } else {
            // Ambil pengaturan sekolah untuk subtitle
            $db = new Database(require __DIR__ . '/../../config/db.php');
            try {
                $appSettings = new \App\Models\AppSettings($db);
                $schoolName = $appSettings->getSetting('school_name') ?? 'Sistem Absensi PKL';
            } catch (Exception $e) {
                $schoolName = 'Sistem Absensi PKL';
            }
            
            $this->view('auth/login', ['schoolName' => $schoolName]);
        }
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
