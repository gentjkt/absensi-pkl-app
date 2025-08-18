<?php
namespace App\Models;

use App\Models\Database;

class AppSettings extends BaseModel {
    
    public function __construct(Database $db) {
        parent::__construct($db);
        $this->initTable();
    }
    
    /**
     * Implementasi method abstract dari BaseModel
     */
    protected function getTableName(): string {
        return 'app_settings';
    }
    
    /**
     * Inisialisasi tabel pengaturan jika belum ada
     */
    private function initTable(): void {
        $sql = "CREATE TABLE IF NOT EXISTS {$this->getTableName()} (
            id INT AUTO_INCREMENT PRIMARY KEY,
            setting_key VARCHAR(100) UNIQUE NOT NULL,
            setting_value TEXT,
            setting_type VARCHAR(20) DEFAULT 'text',
            description TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $this->db->exec($sql);
        
        // Insert default settings jika belum ada
        $this->insertDefaultSettings();
    }
    
    /**
     * Insert pengaturan default
     */
    private function insertDefaultSettings(): void {
        $defaultSettings = [
            [
                'setting_key' => 'school_name',
                'setting_value' => 'SMK Negeri 1 Contoh',
                'setting_type' => 'text',
                'description' => 'Nama Sekolah'
            ],
            [
                'setting_key' => 'school_address',
                'setting_value' => 'Jl. Contoh No. 123, Kota Contoh',
                'setting_type' => 'text',
                'description' => 'Alamat Sekolah'
            ],
            [
                'setting_key' => 'school_phone',
                'setting_value' => '(021) 1234-5678',
                'setting_type' => 'text',
                'description' => 'Nomor Telepon Sekolah'
            ],
            [
                'setting_key' => 'school_email',
                'setting_value' => 'info@smkn1contoh.sch.id',
                'setting_type' => 'email',
                'description' => 'Email Sekolah'
            ],
            [
                'setting_key' => 'school_website',
                'setting_value' => 'https://www.smkn1contoh.sch.id',
                'setting_type' => 'url',
                'description' => 'Website Sekolah'
            ],
            [
                'setting_key' => 'app_title',
                'setting_value' => 'Sistem Absensi PKL',
                'setting_type' => 'text',
                'description' => 'Judul Aplikasi'
            ],
            [
                'setting_key' => 'gps_radius',
                'setting_value' => '150',
                'setting_type' => 'number',
                'description' => 'Radius GPS untuk Absensi (meter)'
            ],
            [
                'setting_key' => 'max_upload_size',
                'setting_value' => '5',
                'setting_type' => 'number',
                'description' => 'Ukuran Maksimal Upload (MB)'
            ],
            [
                'setting_key' => 'session_timeout',
                'setting_value' => '3600',
                'setting_type' => 'number',
                'description' => 'Timeout Session (detik)'
            ]
        ];
        
        foreach ($defaultSettings as $setting) {
            $this->insertIfNotExists($setting);
        }
    }
    
    /**
     * Insert setting jika belum ada
     */
    private function insertIfNotExists(array $setting): void {
        $sql = "INSERT IGNORE INTO {$this->getTableName()} (setting_key, setting_value, setting_type, description) 
                VALUES (:key, :value, :type, :description)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':key', $setting['setting_key']);
        $stmt->bindParam(':value', $setting['setting_value']);
        $stmt->bindParam(':type', $setting['setting_type']);
        $stmt->bindParam(':description', $setting['description']);
        $stmt->execute();
    }
    
    /**
     * Ambil semua pengaturan
     */
    public function getAllSettings(): array {
        $sql = "SELECT * FROM {$this->getTableName()} ORDER BY id ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    /**
     * Ambil pengaturan berdasarkan key
     */
    public function getSetting(string $key): ?string {
        $sql = "SELECT setting_value FROM {$this->getTableName()} WHERE setting_key = :key";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':key', $key);
        $stmt->execute();
        
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result ? $result['setting_value'] : null;
    }
    
    /**
     * Update pengaturan
     */
    public function updateSetting(string $key, string $value): bool {
        $sql = "UPDATE {$this->getTableName()} SET setting_value = :value, updated_at = CURRENT_TIMESTAMP 
                WHERE setting_key = :key";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':value', $value);
        $stmt->bindParam(':key', $key);
        
        return $stmt->execute();
    }
    
    /**
     * Update multiple settings
     */
    public function updateMultipleSettings(array $settings): bool {
        $this->db->beginTransaction();
        
        try {
            foreach ($settings as $key => $value) {
                $this->updateSetting($key, $value);
            }
            
            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
    
    /**
     * Hapus pengaturan
     */
    public function deleteSetting(string $key): bool {
        $sql = "DELETE FROM {$this->getTableName()} WHERE setting_key = :key";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':key', $key);
        
        return $stmt->execute();
    }
    
    /**
     * Validasi pengaturan berdasarkan tipe
     */
    public function validateSetting(string $key, string $value, string $type): bool {
        switch ($type) {
            case 'email':
                return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
            case 'url':
                return filter_var($value, FILTER_VALIDATE_URL) !== false;
            case 'number':
                return is_numeric($value);
            case 'text':
            default:
                return !empty(trim($value));
        }
    }
}
