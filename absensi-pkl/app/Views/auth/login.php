<div class="login-container">
    <div class="login-card">
        <div class="login-header">
            <div class="login-logo">🏢</div>
            <h1>Absensi Online</h1>
            <p class="login-subtitle"><h1><?= htmlspecialchars($schoolName ?? 'Sistem Absensi Praktik Kerja Lapangan') ?></h1></p>
        </div>
        
        <?php if(!empty($error)): ?>
            <div class="alert alert-danger">
                <strong>❌ Error:</strong> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        
        <form method="post" action="?r=auth/login" class="login-form">
            <?= \App\Helpers\CSRF::field() ?>
            
            <div class="form-group">
                <label for="username">👤 Username</label>
                <input 
                    type="text" 
                    id="username" 
                    name="username" 
                    required 
                    placeholder="Masukkan username Anda"
                    autocomplete="username"
                >
            </div>
            
            <div class="form-group">
                <label for="password">🔒 Password</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    required 
                    placeholder="Masukkan password Anda"
                    autocomplete="current-password"
                >
            </div>
            
            <button type="submit" class="btn btn-primary btn-block">
                🚀 Masuk ke Sistem
            </button>
        </form>
        
        <div class="login-info">
          
            <p class="muted text-center">
                <small>⚠️ Password default: <strong>123456</strong> - Ubah setelah login pertama!</small>
            </p>
        </div>
        
        
    </div>
</div>