<?php
namespace App\Helpers;

use DateTime;
use DateTimeZone;

/**
 * Helper class untuk timezone WIB
 */
class Timezone {
    private static $timezone = 'Asia/Jakarta';
    private static $timezoneName = 'WIB';
    
    /**
     * Set timezone default ke WIB
     */
    public static function setWIB(): void {
        date_default_timezone_set(self::$timezone);
    }
    
    /**
     * Dapatkan waktu sekarang dalam WIB
     */
    public static function now(string $format = 'Y-m-d H:i:s'): string {
        return (new DateTime('now', new DateTimeZone(self::$timezone)))->format($format);
    }
    
    /**
     * Dapatkan tanggal hari ini dalam WIB
     */
    public static function today(string $format = 'Y-m-d'): string {
        return (new DateTime('now', new DateTimeZone(self::$timezone)))->format($format);
    }
    
    /**
     * Dapatkan waktu dengan offset tertentu dalam WIB
     */
    public static function timeOffset(string $offset, string $format = 'Y-m-d H:i:s'): string {
        return (new DateTime($offset, new DateTimeZone(self::$timezone)))->format($format);
    }
    
    /**
     * Konversi waktu dari timezone lain ke WIB
     */
    public static function convertToWIB(string $time, string $fromTimezone = 'UTC'): string {
        $dateTime = new DateTime($time, new DateTimeZone($fromTimezone));
        $dateTime->setTimezone(new DateTimeZone(self::$timezone));
        return $dateTime->format('Y-m-d H:i:s');
    }
    
    /**
     * Format waktu untuk display dengan timezone WIB
     */
    public static function formatForDisplay(string $time, string $format = 'd/m/Y H:i'): string {
        $dateTime = new DateTime($time);
        $dateTime->setTimezone(new DateTimeZone(self::$timezone));
        return $dateTime->format($format);
    }
    
    /**
     * Dapatkan nama timezone
     */
    public static function getTimezoneName(): string {
        return self::$timezoneName;
    }
    
    /**
     * Dapatkan timezone string
     */
    public static function getTimezone(): string {
        return self::$timezone;
    }
    
    /**
     * Dapatkan offset timezone dalam format +07:00
     */
    public static function getTimezoneOffset(): string {
        $dateTime = new DateTime('now', new DateTimeZone(self::$timezone));
        return $dateTime->format('P');
    }
    
    /**
     * Dapatkan offset timezone dalam detik
     */
    public static function getTimezoneOffsetSeconds(): int {
        $dateTime = new DateTime('now', new DateTimeZone(self::$timezone));
        return $dateTime->getOffset();
    }
    
    /**
     * Validasi apakah waktu dalam range yang diizinkan (dalam WIB)
     */
    public static function isTimeInRange(string $time, int $maxDifferenceSeconds = 300): bool {
        $inputTime = new DateTime($time);
        $inputTime->setTimezone(new DateTimeZone(self::$timezone));
        
        $currentTime = new DateTime('now', new DateTimeZone(self::$timezone));
        
        $difference = abs($inputTime->getTimestamp() - $currentTime->getTimestamp());
        
        return $difference <= $maxDifferenceSeconds;
    }
    
    /**
     * Dapatkan informasi timezone lengkap
     */
    public static function getTimezoneInfo(): array {
        $dateTime = new DateTime('now', new DateTimeZone(self::$timezone));
        
        return [
            'timezone' => self::$timezone,
            'timezone_name' => self::$timezoneName,
            'current_time' => $dateTime->format('Y-m-d H:i:s'),
            'current_date' => $dateTime->format('Y-m-d'),
            'offset' => $dateTime->format('P'),
            'offset_seconds' => $dateTime->getOffset(),
            'is_dst' => $dateTime->format('I') == '1'
        ];
    }
}
?>
