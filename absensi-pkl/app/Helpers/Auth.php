<?php
namespace App\Helpers;
class Auth {
    public static function requireRole(string ...$roles): void {
        if (empty($_SESSION['user'])) { header('Location: ?r=auth/login'); exit; }
        if (!in_array($_SESSION['user']['role'], $roles, true)) { http_response_code(403); echo 'Forbidden'; exit; }
    }
    public static function user(): ?array { return $_SESSION['user'] ?? null; }
    public static function login(array $user): void { $_SESSION['user'] = $user; }
    public static function logout(): void { $_SESSION = []; session_destroy(); }
    public static function isLoggedIn(): bool { return !empty($_SESSION['user']); }
}