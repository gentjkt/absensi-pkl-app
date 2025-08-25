<?php
namespace App\Helpers;
class CSRF {
    public static function token(): string {
        if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
        return $_SESSION['csrf'];
    }
    public static function verify(?string $token): bool {
        return isset($_SESSION['csrf']) && hash_equals($_SESSION['csrf'], (string)$token);
    }
    public static function field(): string {
        return '<input type="hidden" name="csrf" value="'.self::token().'">';
    }
}