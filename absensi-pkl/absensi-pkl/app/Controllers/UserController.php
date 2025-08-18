<?php
namespace App\Controllers;

use App\Models\{User, Database};

class UserController {
    private $userModel;
    private $db;

    public function __construct() {
        $this->db = new Database(require __DIR__.'/../../config/db.php');
        $this->userModel = new User($this->db);
    }

    // Halaman form ubah password
    public function changePassword(): void {
        // Cek apakah user sudah login
        if (!isset($_SESSION['user'])) {
            header("Location: ?r=auth/login");
            exit;
        }
        
        include __DIR__ . "/../Views/user/change_password.php";
    }

    // Proses ubah password
    public function changePasswordProcess(): void {
        // Cek apakah user sudah login
        if (!isset($_SESSION['user'])) {
            header("Location: ?r=auth/login");
            exit;
        }

        $username = $_SESSION['user']['username'];
        $oldPassword = $_POST['old_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        $user = $this->userModel->findByUsername($username);

        if (!$user || !password_verify($oldPassword, $user['password_hash'])) {
            $_SESSION['error'] = "Password lama salah!";
            header("Location: ?r=user/changePassword");
            exit;
        }

        if ($newPassword !== $confirmPassword) {
            $_SESSION['error'] = "Konfirmasi password baru tidak sama!";
            header("Location: ?r=user/changePassword");
            exit;
        }

        $hashed = password_hash($newPassword, PASSWORD_BCRYPT);
        $this->userModel->updatePassword($user['id'], $hashed);

        $_SESSION['success'] = "Password berhasil diubah!";
        header("Location: ?r=user/changePassword");
        exit;
    }
}
