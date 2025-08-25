<div class="login-container">
    <div class="login-card">
        <div class="login-header">
            <div class="login-logo">🏢</div>
            <h1><?= htmlspecialchars($app['name']) ?></h1>
            <p class="login-subtitle"><?= htmlspecialchars($app['description']) ?></p>
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

            <?php if (!isset($captchaEnabled) || $captchaEnabled): ?>
                <div class="form-group">
                    <label for="captcha">🧩 Kode Keamanan (CAPTCHA)</label>
                    <div style="display:flex; align-items:center; gap:12px; margin-bottom:8px;">
                        <img 
                            src="?r=auth/captcha" 
                            alt="CAPTCHA" 
                            id="captcha-img" 
                            style="border:1px solid #ddd; border-radius:6px; height:42px;"
                        >
                        <button type="button" class="btn" id="captcha-refresh" title="Muat ulang CAPTCHA">🔄</button>
                    </div>
                    <input 
                        type="text"
                        id="captcha"
                        name="captcha"
                        placeholder="Masukkan kode pada gambar"
                        autocomplete="off"
                    >
                </div>
            <?php endif; ?>
            
            <button type="submit" class="btn btn-primary btn-block">
                🚀 Masuk ke Sistem
            </button>
        </form>

        <?php if (!isset($captchaEnabled) || $captchaEnabled): ?>
            <script>
            (function(){
                var img = document.getElementById('captcha-img');
                var btn = document.getElementById('captcha-refresh');
                function refresh(){
                    var url = '?r=auth/captcha&ts=' + Date.now();
                    img.setAttribute('src', url);
                }
                if (btn) btn.addEventListener('click', refresh);
                if (img) img.addEventListener('click', refresh);
            })();
            </script>
        <?php endif; ?>

        
    </div>
</div>