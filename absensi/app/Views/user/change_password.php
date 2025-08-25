<div class="change-password-container">
    <div class="change-password-card">
        <div class="change-password-header">
            <h2>🔐 Ubah Password</h2>
            <p class="change-password-subtitle">Perbarui password akun Anda untuk keamanan yang lebih baik</p>
        </div>

        <?php if (!empty($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <span class="alert-icon">⚠️</span>
                <span class="alert-message"><?= $_SESSION['error']; unset($_SESSION['error']); ?></span>
            </div>
        <?php endif; ?>

        <?php if (!empty($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <span class="alert-icon">✅</span>
                <span class="alert-message"><?= $_SESSION['success']; unset($_SESSION['success']); ?></span>
            </div>
        <?php endif; ?>

        <form method="POST" action="?r=user/changePasswordProcess" class="change-password-form">
            <div class="form-group">
                <label for="old_password" class="form-label">
                    <span class="label-icon">🔒</span>
                    Password Lama
                </label>
                <input type="password" 
                       id="old_password"
                       name="old_password" 
                       class="form-input" 
                       placeholder="Masukkan password lama Anda"
                       required>
                <div class="input-focus-border"></div>
            </div>

            <div class="form-group">
                <label for="new_password" class="form-label">
                    <span class="label-icon">🆕</span>
                    Password Baru
                </label>
                <input type="password" 
                       id="new_password"
                       name="new_password" 
                       class="form-input" 
                       placeholder="Masukkan password baru"
                       required>
                <div class="input-focus-border"></div>
                <div class="password-strength" id="password-strength">
                    <div class="strength-bar">
                        <div class="strength-fill" id="strength-fill"></div>
                    </div>
                    <span class="strength-text" id="strength-text">Kekuatan password</span>
                </div>
            </div>

            <div class="form-group">
                <label for="confirm_password" class="form-label">
                    <span class="label-icon">✅</span>
                    Konfirmasi Password Baru
                </label>
                <input type="password" 
                       id="confirm_password"
                       name="confirm_password" 
                       class="form-input" 
                       placeholder="Konfirmasi password baru"
                       required>
                <div class="input-focus-border"></div>
                <div class="password-match" id="password-match">
                    <span class="match-icon" id="match-icon">⏳</span>
                    <span class="match-text" id="match-text">Menunggu input password</span>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary btn-submit">
                    <span class="btn-icon">💾</span>
                    <span class="btn-text">Simpan Password Baru</span>
                </button>
                <a href="javascript:history.back()" class="btn btn-secondary btn-cancel">
                    <span class="btn-icon">↩️</span>
                    <span class="btn-text">Kembali</span>
                </a>
            </div>
        </form>

        <div class="change-password-footer">
            <div class="password-tips">
                <h4>💡 Tips Password yang Aman:</h4>
                <ul>
                    <li>Gunakan minimal 8 karakter</li>
                    <li>Kombinasikan huruf besar, huruf kecil, angka, dan simbol</li>
                    <li>Hindari informasi pribadi seperti nama atau tanggal lahir</li>
                    <li>Jangan gunakan password yang sama untuk akun lain</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<style>
/* ===== CHANGE PASSWORD STYLES ===== */
.change-password-container {
    min-height: 100vh;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.change-password-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border-radius: 20px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    padding: 40px;
    width: 100%;
    max-width: 500px;
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.change-password-header {
    text-align: center;
    margin-bottom: 30px;
}

.change-password-header h2 {
    color: #2c3e50;
    font-size: 2.5rem;
    margin: 0 0 10px 0;
    font-weight: 700;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.change-password-subtitle {
    color: #7f8c8d;
    font-size: 1.1rem;
    margin: 0;
    line-height: 1.5;
}

/* Alert Styles */
.alert {
    padding: 15px 20px;
    border-radius: 12px;
    margin-bottom: 25px;
    display: flex;
    align-items: center;
    gap: 12px;
    font-weight: 500;
    border: 1px solid transparent;
}

.alert-danger {
    background: rgba(231, 76, 60, 0.1);
    border-color: rgba(231, 76, 60, 0.3);
    color: #c0392b;
}

.alert-success {
    background: rgba(46, 204, 113, 0.1);
    border-color: rgba(46, 204, 113, 0.3);
    color: #27ae60;
}

.alert-icon {
    font-size: 1.2rem;
}

/* Form Styles */
.change-password-form {
    margin-bottom: 30px;
}

.form-group {
    margin-bottom: 25px;
    position: relative;
}

.form-label {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #2c3e50;
    font-weight: 600;
    margin-bottom: 8px;
    font-size: 1rem;
}

.label-icon {
    font-size: 1.1rem;
}

.form-input {
    width: 100%;
    padding: 15px 20px;
    border: 2px solid #e1e8ed;
    border-radius: 12px;
    font-size: 1rem;
    transition: all 0.3s ease;
    background: rgba(255, 255, 255, 0.9);
    box-sizing: border-box;
}

.form-input:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    background: white;
}

.form-input::placeholder {
    color: #bdc3c7;
}

.input-focus-border {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 0;
    height: 2px;
    background: linear-gradient(90deg, #667eea, #764ba2);
    transition: width 0.3s ease;
}

.form-input:focus + .input-focus-border {
    width: 100%;
}

/* Password Strength Indicator */
.password-strength {
    margin-top: 10px;
    padding: 10px;
    background: rgba(236, 240, 241, 0.5);
    border-radius: 8px;
}

.strength-bar {
    width: 100%;
    height: 6px;
    background: #ecf0f1;
    border-radius: 3px;
    overflow: hidden;
    margin-bottom: 8px;
}

.strength-fill {
    height: 100%;
    width: 0%;
    transition: all 0.3s ease;
    border-radius: 3px;
}

.strength-fill.weak { width: 25%; background: #e74c3c; }
.strength-fill.fair { width: 50%; background: #f39c12; }
.strength-fill.good { width: 75%; background: #f1c40f; }
.strength-fill.strong { width: 100%; background: #27ae60; }

.strength-text {
    font-size: 0.9rem;
    color: #7f8c8d;
    font-weight: 500;
}

/* Password Match Indicator */
.password-match {
    margin-top: 10px;
    padding: 10px;
    background: rgba(236, 240, 241, 0.5);
    border-radius: 8px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.match-icon {
    font-size: 1rem;
}

.match-text {
    font-size: 0.9rem;
    color: #7f8c8d;
    font-weight: 500;
}

.password-match.matching .match-icon { color: #27ae60; }
.password-match.matching .match-text { color: #27ae60; }
.password-match.not-matching .match-icon { color: #e74c3c; }
.password-match.not-matching .match-text { color: #e74c3c; }

/* Form Actions */
.form-actions {
    display: flex;
    gap: 15px;
    margin-top: 30px;
    flex-wrap: wrap;
}

.btn {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 15px 25px;
    border: none;
    border-radius: 12px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    text-align: center;
    min-width: 140px;
    justify-content: center;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
}

.btn-secondary {
    background: rgba(127, 140, 141, 0.1);
    color: #7f8c8d;
    border: 2px solid rgba(127, 140, 141, 0.3);
}

.btn-secondary:hover {
    background: rgba(127, 140, 141, 0.2);
    border-color: rgba(127, 140, 141, 0.5);
}

.btn-icon {
    font-size: 1.1rem;
}

/* Footer Tips */
.change-password-footer {
    margin-top: 30px;
    padding-top: 25px;
    border-top: 1px solid rgba(189, 195, 199, 0.3);
}

.password-tips h4 {
    color: #2c3e50;
    margin: 0 0 15px 0;
    font-size: 1.1rem;
    font-weight: 600;
}

.password-tips ul {
    margin: 0;
    padding-left: 20px;
    color: #7f8c8d;
    line-height: 1.6;
}

.password-tips li {
    margin-bottom: 8px;
    font-size: 0.95rem;
}

/* Responsive Design */
@media (max-width: 768px) {
    .change-password-container {
        padding: 15px;
    }
    
    .change-password-card {
        padding: 25px;
        border-radius: 15px;
    }
    
    .change-password-header h2 {
        font-size: 2rem;
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .btn {
        width: 100%;
    }
}

@media (max-width: 480px) {
    .change-password-card {
        padding: 20px;
        border-radius: 12px;
    }
    
    .change-password-header h2 {
        font-size: 1.8rem;
    }
    
    .form-input {
        padding: 12px 15px;
    }
    
    .btn {
        padding: 12px 20px;
        font-size: 0.95rem;
    }
}

/* Animation */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.change-password-card {
    animation: fadeInUp 0.6s ease-out;
}

/* Hover Effects */
.form-input:hover {
    border-color: #bdc3c7;
    background: rgba(255, 255, 255, 0.95);
}

.btn:hover {
    transform: translateY(-1px);
}

/* Focus States */
.form-input:focus {
    transform: scale(1.02);
}

/* Loading States */
.btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
}
</style>

<script>
// Password strength checker
document.getElementById('new_password').addEventListener('input', function() {
    const password = this.value;
    const strengthFill = document.getElementById('strength-fill');
    const strengthText = document.getElementById('strength-text');
    
    let strength = 0;
    let text = 'Kekuatan password';
    
    if (password.length >= 8) strength += 25;
    if (/[a-z]/.test(password)) strength += 25;
    if (/[A-Z]/.test(password)) strength += 25;
    if (/[0-9!@#$%^&*]/.test(password)) strength += 25;
    
    strengthFill.className = 'strength-fill';
    if (strength <= 25) {
        strengthFill.classList.add('weak');
        text = 'Lemah';
    } else if (strength <= 50) {
        strengthFill.classList.add('fair');
        text = 'Cukup';
    } else if (strength <= 75) {
        strengthFill.classList.add('good');
        text = 'Baik';
    } else {
        strengthFill.classList.add('strong');
        text = 'Kuat';
    }
    
    strengthText.textContent = text;
});

// Password match checker
function checkPasswordMatch() {
    const newPassword = document.getElementById('new_password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    const passwordMatch = document.getElementById('password-match');
    const matchIcon = document.getElementById('match-icon');
    const matchText = document.getElementById('match-text');
    
    if (confirmPassword === '') {
        passwordMatch.className = 'password-match';
        matchIcon.textContent = '⏳';
        matchText.textContent = 'Menunggu input password';
    } else if (newPassword === confirmPassword) {
        passwordMatch.className = 'password-match matching';
        matchIcon.textContent = '✅';
        matchText.textContent = 'Password cocok';
    } else {
        passwordMatch.className = 'password-match not-matching';
        matchIcon.textContent = '❌';
        matchText.textContent = 'Password tidak cocok';
    }
}

document.getElementById('new_password').addEventListener('input', checkPasswordMatch);
document.getElementById('confirm_password').addEventListener('input', checkPasswordMatch);

// Form submission enhancement
document.querySelector('.change-password-form').addEventListener('submit', function(e) {
    const newPassword = document.getElementById('new_password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    
    if (newPassword !== confirmPassword) {
        e.preventDefault();
        alert('Password baru dan konfirmasi password tidak cocok!');
        return false;
    }
    
    if (newPassword.length < 8) {
        e.preventDefault();
        alert('Password baru minimal 8 karakter!');
        return false;
    }
    
    // Show loading state
    const submitBtn = document.querySelector('.btn-submit');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="btn-icon">⏳</span><span class="btn-text">Menyimpan...</span>';
});

// Add some interactive effects
document.querySelectorAll('.form-input').forEach(input => {
    input.addEventListener('focus', function() {
        this.parentElement.classList.add('focused');
    });
    
    input.addEventListener('blur', function() {
        this.parentElement.classList.remove('focused');
    });
});

// Smooth scroll to form on page load
window.addEventListener('load', function() {
    document.querySelector('.change-password-form').scrollIntoView({
        behavior: 'smooth',
        block: 'center'
    });
});
</script>
