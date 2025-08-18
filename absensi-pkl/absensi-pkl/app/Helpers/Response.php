<?php
namespace App\Helpers;
class Response {
    public static function redirect(string $url): void { header('Location: ' . $url); exit; }
    public static function json(array $data): void {
        header('Content-Type: application/json'); echo json_encode($data); exit;
    }
    public static function e(?string $str): string {
        return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
    }
}