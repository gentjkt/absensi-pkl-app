<div class="profile-container">
    <div class="profile-header">
        <h2>⚙️ Profile Administrator</h2>
        <p>Kelola pengaturan aplikasi dan informasi sekolah</p>
    </div>

    <?php if (!empty($message)): ?>
        <div class="alert alert-success">
            <strong>✅ Berhasil:</strong> <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger">
            <strong>❌ Error:</strong> <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <div class="profile-content">
        <!-- Pengaturan Sekolah -->
        <div class="settings-section">
            <div class="section-header">
                <h3>🏫 Pengaturan Sekolah</h3>
                <p>Informasi dasar sekolah yang akan ditampilkan di aplikasi</p>
            </div>
            
            <form method="POST" action="?r=profile/update" class="settings-form">
                <input type="hidden" name="csrf_token" value="<?= \App\Helpers\CSRF::token() ?>">
                
                <?php foreach ($schoolSettings as $setting): ?>
                    <div class="form-group">
                        <label for="setting_<?= $setting['setting_key'] ?>">
                            <?= htmlspecialchars($setting['description']) ?>
                        </label>
                        
                        <?php if ($setting['setting_type'] === 'email'): ?>
                            <input type="email" 
                                   id="setting_<?= $setting['setting_key'] ?>" 
                                   name="setting_<?= $setting['setting_key'] ?>" 
                                   value="<?= htmlspecialchars($setting['setting_value']) ?>" 
                                   class="form-control"
                                   placeholder="Masukkan <?= strtolower($setting['description']) ?>">
                        <?php elseif ($setting['setting_type'] === 'url'): ?>
                            <input type="url" 
                                   id="setting_<?= $setting['setting_key'] ?>" 
                                   name="setting_<?= $setting['setting_key'] ?>" 
                                   value="<?= htmlspecialchars($setting['setting_value']) ?>" 
                                   class="form-control"
                                   placeholder="Masukkan <?= strtolower($setting['description']) ?>">
                        <?php else: ?>
                            <input type="text" 
                                   id="setting_<?= $setting['setting_key'] ?>" 
                                   name="setting_<?= $setting['setting_key'] ?>" 
                                   value="<?= htmlspecialchars($setting['setting_value']) ?>" 
                                   class="form-control"
                                   placeholder="Masukkan <?= strtolower($setting['description']) ?>">
                        <?php endif; ?>
                        
                        <small class="form-text">
                            Tipe: <?= ucfirst($setting['setting_type']) ?> | 
                            Terakhir diupdate: <?= date('d/m/Y H:i', strtotime($setting['updated_at'])) ?>
                        </small>
                    </div>
                <?php endforeach; ?>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        💾 Simpan Pengaturan Sekolah
                    </button>
                </div>
            </form>
        </div>

        <!-- Pengaturan Sistem -->
        <div class="settings-section">
            <div class="section-header">
                <h3>🔧 Pengaturan Sistem</h3>
                <p>Konfigurasi teknis aplikasi yang mempengaruhi kinerja sistem</p>
            </div>
            
            <form method="POST" action="?r=profile/update" class="settings-form">
                <input type="hidden" name="csrf_token" value="<?= \App\Helpers\CSRF::token() ?>">
                
                <?php foreach ($systemSettings as $setting): ?>
                    <div class="form-group">
                        <label for="setting_<?= $setting['setting_key'] ?>">
                            <?= htmlspecialchars($setting['description']) ?>
                        </label>
                        
                        <?php if ($setting['setting_type'] === 'number'): ?>
                            <input type="number" 
                                   id="setting_<?= $setting['setting_key'] ?>" 
                                   name="setting_<?= $setting['setting_key'] ?>" 
                                   value="<?= htmlspecialchars($setting['setting_value']) ?>" 
                                   class="form-control"
                                   min="1"
                                   step="1"
                                   placeholder="Masukkan <?= strtolower($setting['description']) ?>">
                        <?php elseif ($setting['setting_type'] === 'boolean'): ?>
                            <div style="display:flex; align-items:center; gap:12px;">
                                <input type="hidden" name="setting_<?= $setting['setting_key'] ?>" value="0">
                                <input type="checkbox"
                                       id="setting_<?= $setting['setting_key'] ?>"
                                       name="setting_<?= $setting['setting_key'] ?>"
                                       value="1"
                                       <?= ($setting['setting_value'] === '1' || strtolower($setting['setting_value']) === 'true' || strtolower($setting['setting_value']) === 'on' || strtolower($setting['setting_value']) === 'yes') ? 'checked' : '' ?>
                                >
                                <label for="setting_<?= $setting['setting_key'] ?>" style="margin:0; font-weight: normal;">Aktif</label>
                            </div>
                        <?php else: ?>
                            <input type="text" 
                                   id="setting_<?= $setting['setting_key'] ?>" 
                                   name="setting_<?= $setting['setting_key'] ?>" 
                                   value="<?= htmlspecialchars($setting['setting_value']) ?>" 
                                   class="form-control"
                                   placeholder="Masukkan <?= strtolower($setting['description']) ?>">
                        <?php endif; ?>
                        
                        <small class="form-text">
                            Tipe: <?= ucfirst($setting['setting_type']) ?> | 
                            Terakhir diupdate: <?= date('d/m/Y H:i', strtotime($setting['updated_at'])) ?>
                        </small>
                    </div>
                <?php endforeach; ?>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        💾 Simpan Pengaturan Sistem
                    </button>
                </div>
            </form>
        </div>

        <!-- Pengaturan Lainnya -->
        <?php if (!empty($appSettings)): ?>
        <div class="settings-section">
            <div class="section-header">
                <h3>📋 Pengaturan Lainnya</h3>
                <p>Konfigurasi tambahan aplikasi</p>
            </div>
            
            <form method="POST" action="?r=profile/update" class="settings-form">
                <input type="hidden" name="csrf_token" value="<?= \App\Helpers\CSRF::token() ?>">
                
                <?php foreach ($appSettings as $setting): ?>
                    <div class="form-group">
                        <label for="setting_<?= $setting['setting_key'] ?>">
                            <?= htmlspecialchars($setting['description']) ?>
                        </label>
                        
                        <input type="text" 
                               id="setting_<?= $setting['setting_key'] ?>" 
                               name="setting_<?= $setting['setting_key'] ?>" 
                               value="<?= htmlspecialchars($setting['setting_value']) ?>" 
                               class="form-control"
                               placeholder="Masukkan <?= strtolower($setting['description']) ?>">
                        
                        <small class="form-text">
                            Tipe: <?= ucfirst($setting['setting_type']) ?> | 
                            Terakhir diupdate: <?= date('d/m/Y H:i', strtotime($setting['updated_at'])) ?>
                        </small>
                    </div>
                <?php endforeach; ?>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        💾 Simpan Pengaturan Lainnya
                    </button>
                </div>
            </form>
        </div>
        <?php endif; ?>

        <!-- Backup dan Restore Database -->
        <div class="settings-section database-section">
            <div class="section-header">
                <h3>💾 Backup dan Restore Database</h3>
                <p>Kelola backup dan restore database sistem</p>
            </div>
            
            <div class="database-actions">
                <div class="action-group">
                    <h4>📤 Backup Database</h4>
                    <p>Download backup lengkap database dalam format SQL</p>
                    <form method="POST" action="?r=profile/backup" class="inline-form">
                        <input type="hidden" name="csrf_token" value="<?= \App\Helpers\CSRF::token() ?>">
                        <button type="submit" class="btn btn-success">
                            💾 Download Backup SQL
                        </button>
                    </form>
                </div>
                
                <div class="action-group">
                    <h4>📥 Restore Database</h4>
                    <p>Restore database dari file backup SQL</p>
                    <form method="POST" action="?r=profile/restore" enctype="multipart/form-data" class="inline-form">
                        <input type="hidden" name="csrf_token" value="<?= \App\Helpers\CSRF::token() ?>">
                        <div class="file-input-group">
                            <input type="file" name="sql_file" accept=".sql" required class="file-input">
                            <button type="submit" class="btn btn-warning" 
                                    onclick="return confirm('⚠️ PERHATIAN! Restore database akan menimpa semua data yang ada. Tindakan ini tidak dapat dibatalkan. Lanjutkan?')">
                                🔄 Restore Database
                            </button>
                        </div>
                        <small class="form-text">
                            <strong>⚠️ Peringatan:</strong> Restore akan menimpa semua data yang ada. 
                            Pastikan Anda telah melakukan backup terlebih dahulu.
                        </small>
                    </form>
                </div>

                <div class="action-group" style="border-color:#dc3545;background:rgba(220,53,69,0.05)">
                    <h4>🗑️ Reset Database ke Kondisi Awal</h4>
                    <p><strong>PERINGATAN:</strong> Tindakan ini akan <u>MENGHAPUS SELURUH DATA</u> dan mengembalikan database sesuai file <code>db_absensi_pkl.sql</code>.</p>
                    <form method="POST" action="?r=profile/resetDatabase" class="inline-form"
                          onsubmit="return confirm('⚠️ SANGAT BERBAHAYA! Semua data akan DIHAPUS dan database dikembalikan ke kondisi awal. Tindakan ini tidak dapat dibatalkan. Yakin lanjutkan?')">
                        <input type="hidden" name="csrf_token" value="<?= \App\Helpers\CSRF::token() ?>">
                        <button type="submit" class="btn btn-danger">
                            🗑️ Reset Database Sekarang
                        </button>
                    </form>
                    <small class="form-text">
                        Disarankan melakukan <strong>Backup</strong> terlebih dahulu sebelum reset.
                    </small>
                </div>
            </div>
        </div>


<style>
.profile-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.profile-header {
    text-align: center;
    margin-bottom: 30px;
    padding: 20px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 15px;
}

.profile-header h2 {
    margin: 0 0 10px 0;
    font-size: 2.5rem;
}

.profile-header p {
    margin: 0;
    font-size: 1.1rem;
    opacity: 0.9;
}

.settings-section {
    background: white;
    border-radius: 12px;
    padding: 25px;
    margin-bottom: 25px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    border: 1px solid #e9ecef;
}

.section-header {
    margin-bottom: 25px;
    padding-bottom: 15px;
    border-bottom: 2px solid #f8f9fa;
}

.section-header h3 {
    margin: 0 0 8px 0;
    color: #495057;
    font-size: 1.4rem;
}

.section-header p {
    margin: 0;
    color: #6c757d;
    font-size: 0.95rem;
}

.settings-form {
    display: grid;
    gap: 20px;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.form-group label {
    font-weight: 600;
    color: #495057;
    font-size: 0.95rem;
}

.form-control {
    padding: 12px 16px;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    font-size: 1rem;
    transition: all 0.3s ease;
}

.form-control:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.form-text {
    color: #6c757d;
    font-size: 0.85rem;
}

.form-actions {
    display: flex;
    justify-content: flex-end;
    padding-top: 15px;
    border-top: 1px solid #f8f9fa;
}

.btn {
    padding: 12px 24px;
    border: none;
    border-radius: 8px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
}

.btn-danger {
    background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
    color: white;
}

.btn-danger:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(255, 107, 107, 0.3);
}

.danger-zone {
    border: 2px solid #ff6b6b;
    background: linear-gradient(135deg, #fff5f5 0%, #ffe8e8 100%);
}

.danger-zone .section-header h3 {
    color: #d63031;
}

.danger-zone .section-header p {
    color: #e17055;
}

.danger-actions {
    text-align: center;
    padding: 20px;
    background: rgba(255, 107, 107, 0.1);
    border-radius: 8px;
}

.database-section {
    border: 2px solid #28a745;
    background: linear-gradient(135deg, #f8fff9 0%, #f0fff4 100%);
}

.database-section .section-header h3 {
    color: #28a745;
}

.database-section .section-header p {
    color: #38a169;
}

.database-actions {
    display: grid;
    gap: 25px;
}

.action-group {
    padding: 20px;
    background: rgba(40, 167, 69, 0.05);
    border-radius: 8px;
    border: 1px solid rgba(40, 167, 69, 0.2);
}

.action-group h4 {
    margin: 0 0 10px 0;
    color: #28a745;
    font-size: 1.1rem;
}

.action-group p {
    margin: 0 0 15px 0;
    color: #6c757d;
    font-size: 0.9rem;
}

.inline-form {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.file-input-group {
    display: flex;
    gap: 10px;
    align-items: center;
}

.file-input {
    flex: 1;
    padding: 8px 12px;
    border: 2px solid #e9ecef;
    border-radius: 6px;
    font-size: 0.9rem;
}

.file-input:focus {
    outline: none;
    border-color: #28a745;
}

.btn-warning {
    background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
    color: #212529;
}

.btn-warning:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(255, 193, 7, 0.3);
}

.alert {
    padding: 15px 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    border-left: 4px solid;
}

.alert-success {
    background: #d4edda;
    border-color: #28a745;
    color: #155724;
}

.alert-danger {
    background: #f8d7da;
    border-color: #dc3545;
    color: #721c24;
}

@media (max-width: 768px) {
    .profile-container {
        padding: 15px;
    }
    
    .profile-header h2 {
        font-size: 2rem;
    }
    
    .settings-section {
        padding: 20px;
    }
    
    .form-actions {
        justify-content: center;
    }
    
    .btn {
        width: 100%;
        justify-content: center;
    }
}
</style>
