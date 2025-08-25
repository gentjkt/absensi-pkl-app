<?php
// File debug khusus untuk menu mobile
session_start();

// Simulasi user login untuk testing
$_SESSION['user'] = [
    'id' => 1,
    'username' => 'admin',
    'name' => 'Administrator',
    'role' => 'admin'
];

// Include header
include 'app/Views/layouts/header.php';
?>

<div class="card">
    <h2>🔍 Debug Menu Mobile</h2>
    
    <div class="debug-section">
        <h3>📱 Status Menu Mobile</h3>
        <div id="debugInfo">
            <p><strong>Mobile Menu Button:</strong> <span id="btnStatus">Checking...</span></p>
            <p><strong>Mobile Menu Element:</strong> <span id="menuStatus">Checking...</span></p>
            <p><strong>Window Width:</strong> <span id="windowWidth">Checking...</span></p>
            <p><strong>CSS Loaded:</strong> <span id="cssStatus">Checking...</span></p>
        </div>
    </div>
    
    <div class="debug-section">
        <h3>🧪 Test Menu Mobile</h3>
        <button id="testBtn" class="btn btn-primary">Test Toggle Menu</button>
        <button id="forceShowBtn" class="btn btn-success">Force Show Menu</button>
        <button id="forceHideBtn" class="btn btn-danger">Force Hide Menu</button>
    </div>
    
    <div class="debug-section">
        <h3>📋 Console Log</h3>
        <div id="consoleLog" style="background: #f5f5f5; padding: 10px; border-radius: 5px; font-family: monospace; max-height: 200px; overflow-y: auto;">
            <p>Console log akan muncul di sini...</p>
        </div>
    </div>
    
    <div class="debug-section">
        <h3>🎨 CSS Debug</h3>
        <div style="background: #fff3cd; padding: 15px; border-radius: 5px; border: 1px solid #ffeaa7;">
            <h4>CSS yang seharusnya aktif:</h4>
            <ul>
                <li><code>.mobile-menu-toggle { display: none; }</code> - Desktop</li>
                <li><code>.mobile-menu { transform: translateY(-100%); }</code> - Hidden by default</li>
                <li><code>@media (max-width: 768px) { .mobile-menu-toggle { display: block; } }</code> - Mobile</li>
            </ul>
        </div>
    </div>
</div>

<style>
.debug-section {
    margin-bottom: 20px;
    padding: 15px;
    border: 1px solid #ddd;
    border-radius: 8px;
}

.debug-section h3 {
    margin-top: 0;
    color: #2c3e50;
}

#consoleLog p {
    margin: 5px 0;
    font-size: 12px;
}

.log-info { color: #3498db; }
.log-success { color: #27ae60; }
.log-error { color: #e74c3c; }
.log-warning { color: #f39c12; }
</style>

<script>
// Debug script untuk menu mobile
function log(message, type = 'info') {
    const consoleLog = document.getElementById('consoleLog');
    const logEntry = document.createElement('p');
    logEntry.className = `log-${type}`;
    logEntry.textContent = `[${new Date().toLocaleTimeString()}] ${message}`;
    consoleLog.appendChild(logEntry);
    consoleLog.scrollTop = consoleLog.scrollHeight;
    console.log(`[${type.toUpperCase()}] ${message}`);
}

function updateStatus() {
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const mobileMenu = document.getElementById('mobileMenu');
    const windowWidth = window.innerWidth;
    
    // Update status display
    document.getElementById('btnStatus').textContent = mobileMenuBtn ? 'Found ✓' : 'Not Found ✗';
    document.getElementById('menuStatus').textContent = mobileMenu ? 'Found ✓' : 'Not Found ✗';
    document.getElementById('windowWidth').textContent = `${windowWidth}px`;
    
    // Check CSS
    const computedStyle = window.getComputedStyle(mobileMenuBtn || document.body);
    const btnDisplay = mobileMenuBtn ? computedStyle.display : 'N/A';
    document.getElementById('cssStatus').textContent = btnDisplay;
    
    log(`Status Update - Button: ${mobileMenuBtn ? 'Found' : 'Not Found'}, Menu: ${mobileMenu ? 'Found' : 'Not Found'}, Width: ${windowWidth}px`, 'info');
}

// Test functions
document.getElementById('testBtn').addEventListener('click', function() {
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const mobileMenu = document.getElementById('mobileMenu');
    
    if (mobileMenuBtn && mobileMenu) {
        mobileMenu.classList.toggle('active');
        mobileMenuBtn.classList.toggle('active');
        log('Menu toggled manually', 'success');
    } else {
        log('Cannot toggle menu - elements not found', 'error');
    }
});

document.getElementById('forceShowBtn').addEventListener('click', function() {
    const mobileMenu = document.getElementById('mobileMenu');
    if (mobileMenu) {
        mobileMenu.classList.add('active');
        log('Menu forced to show', 'success');
    }
});

document.getElementById('forceHideBtn').addEventListener('click', function() {
    const mobileMenu = document.getElementById('mobileMenu');
    if (mobileMenu) {
        mobileMenu.classList.remove('active');
        log('Menu forced to hide', 'success');
    }
});

// Initial status check
document.addEventListener('DOMContentLoaded', function() {
    log('DOM loaded, checking menu elements...', 'info');
    updateStatus();
    
    // Check every second
    setInterval(updateStatus, 1000);
    
    // Test mobile menu functionality
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const mobileMenu = document.getElementById('mobileMenu');
    
    if (mobileMenuBtn && mobileMenu) {
        log('Mobile menu elements found, adding event listeners', 'success');
        
        mobileMenuBtn.addEventListener('click', function() {
            log('Mobile menu button clicked', 'info');
            mobileMenu.classList.toggle('active');
            mobileMenuBtn.classList.toggle('active');
        });
        
        // Close on outside click
        document.addEventListener('click', function(event) {
            if (!mobileMenuBtn.contains(event.target) && !mobileMenu.contains(event.target)) {
                mobileMenu.classList.remove('active');
                mobileMenuBtn.classList.remove('active');
                log('Menu closed by outside click', 'info');
            }
        });
        
        log('Event listeners added successfully', 'success');
    } else {
        log('Mobile menu elements not found!', 'error');
    }
});

// Window resize handler
window.addEventListener('resize', function() {
    const width = window.innerWidth;
    log(`Window resized to ${width}px`, 'info');
    
    if (width > 768) {
        const mobileMenu = document.getElementById('mobileMenu');
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        if (mobileMenu) mobileMenu.classList.remove('active');
        if (mobileMenuBtn) mobileMenuBtn.classList.remove('active');
        log('Menu auto-closed (desktop size)', 'info');
    }
});
</script>

<?php include 'app/Views/layouts/footer.php'; ?>
