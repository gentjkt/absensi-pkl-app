<?php
session_start();

// Simulasi user login
$_SESSION['user'] = [
    'id' => 1,
    'username' => 'admin',
    'name' => 'Administrator',
    'role' => 'admin'
];

include 'app/Views/layouts/header.php';
?>

<div class="card">
    <h2>🔍 Debug Mobile Left Menu</h2>
    
    <div class="debug-section">
        <h3>📱 Status Mobile Left Menu</h3>
        <div id="debugInfo">
            <p><strong>Session User:</strong> <span id="sessionStatus">Checking...</span></p>
            <p><strong>Mobile Left Menu Toggle:</strong> <span id="leftToggleStatus">Checking...</span></p>
            <p><strong>Mobile Left Menu Button:</strong> <span id="leftBtnStatus">Checking...</span></p>
            <p><strong>Mobile Left Menu:</strong> <span id="leftMenuStatus">Checking...</span></p>
            <p><strong>Window Width:</strong> <span id="windowWidth">Checking...</span></p>
            <p><strong>CSS Display Property:</strong> <span id="cssDisplay">Checking...</span></p>
        </div>
    </div>
    
    <div class="debug-section">
        <h3>🧪 Test Mobile Left Menu</h3>
        <button id="forceShowLeftBtn" class="btn btn-success">Force Show Left Menu</button>
        <button id="forceHideLeftBtn" class="btn btn-danger">Force Hide Left Menu</button>
        <button id="toggleDebugBtn" class="btn btn-info">Toggle Debug Borders</button>
        <button id="testClickBtn" class="btn btn-warning">Test Click Event</button>
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
                <li><code>.mobile-left-menu-toggle { display: none; }</code> - Desktop default</li>
                <li><code>@media (max-width: 768px) { .mobile-left-menu-toggle { display: block !important; } }</code> - Mobile</li>
                <li><code>.mobile-left-menu { display: none; transform: translateX(-100%); }</code> - Hidden by default</li>
                <li><code>.mobile-left-menu.active { transform: translateX(0); }</code> - Active state</li>
            </ul>
        </div>
    </div>
    
    <div class="debug-section">
        <h3>🔧 Manual Test</h3>
        <div style="background: #e8f5e8; padding: 15px; border-radius: 5px;">
            <p><strong>Langkah Test:</strong></p>
            <ol>
                <li>Resize browser ke ukuran mobile (≤768px)</li>
                <li>Refresh halaman</li>
                <li>Lihat apakah tombol hamburger kiri muncul</li>
                <li>Klik tombol untuk membuka menu</li>
                <li>Jika tidak bekerja, gunakan button "Force Show Left Menu"</li>
            </ol>
        </div>
    </div>
    
    <div class="debug-section">
        <h3>🐛 Troubleshooting</h3>
        <div style="background: #f8d7da; padding: 15px; border-radius: 5px;">
            <h4>Masalah yang mungkin terjadi:</h4>
            <ul>
                <li><strong>JavaScript Error:</strong> Event listener tidak terpasang</li>
                <li><strong>CSS Conflict:</strong> Style tidak diterapkan dengan benar</li>
                <li><strong>Element Not Found:</strong> ID atau class tidak cocok</li>
                <li><strong>Z-Index Issue:</strong> Menu tertutup elemen lain</li>
                <li><strong>Transform Issue:</strong> CSS transform tidak bekerja</li>
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

/* Debug borders */
.debug-border .mobile-left-menu-toggle {
    border: 3px solid red !important;
    background: rgba(255, 0, 0, 0.1) !important;
}

.debug-border .mobile-left-menu-btn {
    border: 2px solid blue !important;
    background: rgba(0, 0, 255, 0.1) !important;
}

.debug-border .mobile-left-menu {
    border: 3px solid green !important;
    background: rgba(0, 255, 0, 0.1) !important;
}

/* Force show left menu for debugging */
.force-show-left .mobile-left-menu {
    display: block !important;
    transform: translateX(0) !important;
    background: rgba(255, 255, 0, 0.3) !important;
    border: 2px solid yellow !important;
    z-index: 10000 !important;
}

.force-show-left .mobile-left-menu-toggle {
    display: block !important;
    background: rgba(255, 255, 0, 0.3) !important;
    border: 2px solid yellow !important;
    padding: 10px !important;
    margin: 5px !important;
}

/* Ensure mobile left menu is visible for debugging */
.mobile-left-menu {
    pointer-events: auto !important;
}

.mobile-left-menu-toggle {
    pointer-events: auto !important;
}
</style>

<script>
// Debug script untuk mobile left menu
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
    const sessionUser = <?= json_encode($_SESSION['user'] ?? null) ?>;
    const mobileLeftMenuToggle = document.querySelector('.mobile-left-menu-toggle');
    const mobileLeftMenuBtn = document.getElementById('mobileLeftMenuBtn');
    const mobileLeftMenu = document.getElementById('mobileLeftMenu');
    const windowWidth = window.innerWidth;
    
    // Update status display
    document.getElementById('sessionStatus').textContent = sessionUser ? `Logged in as ${sessionUser.name}` : 'Not logged in';
    document.getElementById('leftToggleStatus').textContent = mobileLeftMenuToggle ? 'Found ✓' : 'Not Found ✗';
    document.getElementById('leftBtnStatus').textContent = mobileLeftMenuBtn ? 'Found ✓' : 'Not Found ✗';
    document.getElementById('leftMenuStatus').textContent = mobileLeftMenu ? 'Found ✓' : 'Not Found ✗';
    document.getElementById('windowWidth').textContent = `${windowWidth}px`;
    
    // Check CSS display property
    if (mobileLeftMenuToggle) {
        const computedStyle = window.getComputedStyle(mobileLeftMenuToggle);
        const display = computedStyle.display;
        const visibility = computedStyle.visibility;
        const opacity = computedStyle.opacity;
        document.getElementById('cssDisplay').textContent = `display: ${display}, visibility: ${visibility}, opacity: ${opacity}`;
        
        log(`CSS Status - Display: ${display}, Visibility: ${visibility}, Opacity: ${opacity}`, 'info');
    } else {
        document.getElementById('cssDisplay').textContent = 'Element not found';
    }
    
    log(`Status Update - Toggle: ${mobileLeftMenuToggle ? 'Found' : 'Not Found'}, Button: ${mobileLeftMenuBtn ? 'Found' : 'Not Found'}, Menu: ${mobileLeftMenu ? 'Found' : 'Not Found'}, Width: ${windowWidth}px`, 'info');
}

// Test functions
document.getElementById('forceShowLeftBtn').addEventListener('click', function() {
    const mobileLeftMenu = document.getElementById('mobileLeftMenu');
    const mobileLeftMenuToggle = document.querySelector('.mobile-left-menu-toggle');
    
    if (mobileLeftMenu && mobileLeftMenuToggle) {
        document.body.classList.add('force-show-left');
        log('Mobile left menu forced to show', 'success');
    } else {
        log('Mobile left menu elements not found', 'error');
    }
});

document.getElementById('forceHideLeftBtn').addEventListener('click', function() {
    document.body.classList.remove('force-show-left');
    log('Mobile left menu forced to hide', 'success');
});

document.getElementById('toggleDebugBtn').addEventListener('click', function() {
    document.body.classList.toggle('debug-border');
    const isActive = document.body.classList.contains('debug-border');
    log(`Debug borders ${isActive ? 'enabled' : 'disabled'}`, isActive ? 'success' : 'info');
});

document.getElementById('testClickBtn').addEventListener('click', function() {
    const mobileLeftMenuBtn = document.getElementById('mobileLeftMenuBtn');
    if (mobileLeftMenuBtn) {
        log('Testing click event on mobile left menu button', 'info');
        mobileLeftMenuBtn.click();
    } else {
        log('Mobile left menu button not found for click test', 'error');
    }
});

// Initial status check
document.addEventListener('DOMContentLoaded', function() {
    log('DOM loaded, checking mobile left menu...', 'info');
    updateStatus();
    
    // Check every 2 seconds
    setInterval(updateStatus, 2000);
    
    // Check if elements exist
    const mobileLeftMenuToggle = document.querySelector('.mobile-left-menu-toggle');
    const mobileLeftMenuBtn = document.getElementById('mobileLeftMenuBtn');
    const mobileLeftMenu = document.getElementById('mobileLeftMenu');
    
    if (mobileLeftMenuToggle) {
        log('Mobile left menu toggle element found', 'success');
        
        // Check CSS properties
        const computedStyle = window.getComputedStyle(mobileLeftMenuToggle);
        log(`Initial CSS - Display: ${computedStyle.display}, Visibility: ${computedStyle.visibility}`, 'info');
        
        // Add click event for testing
        mobileLeftMenuToggle.addEventListener('click', function() {
            log('Mobile left menu toggle clicked', 'info');
        });
    } else {
        log('Mobile left menu toggle element NOT found!', 'error');
    }
    
    if (mobileLeftMenuBtn) {
        log('Mobile left menu button found', 'success');
        
        // Test click event
        mobileLeftMenuBtn.addEventListener('click', function() {
            log('Mobile left menu button clicked!', 'success');
            
            // Check if menu exists
            const mobileLeftMenu = document.getElementById('mobileLeftMenu');
            if (mobileLeftMenu) {
                log('Mobile left menu found, toggling...', 'info');
                mobileLeftMenu.classList.toggle('active');
                this.classList.toggle('active');
                log(`Menu active state: ${mobileLeftMenu.classList.contains('active')}`, 'info');
            } else {
                log('Mobile left menu not found for toggle', 'error');
            }
        });
    } else {
        log('Mobile left menu button NOT found!', 'error');
    }
    
    if (mobileLeftMenu) {
        log('Mobile left menu found', 'success');
        
        // Check initial state
        const isActive = mobileLeftMenu.classList.contains('active');
        log(`Initial menu state: ${isActive ? 'Active' : 'Inactive'}`, 'info');
        
        // Check CSS properties
        const computedStyle = window.getComputedStyle(mobileLeftMenu);
        log(`Menu CSS - Display: ${computedStyle.display}, Transform: ${computedStyle.transform}`, 'info');
    } else {
        log('Mobile left menu NOT found!', 'error');
    }
});

// Window resize handler
window.addEventListener('resize', function() {
    const width = window.innerWidth;
    log(`Window resized to ${width}px`, 'info');
    
    // Check if mobile breakpoint is active
    if (width <= 768) {
        log('Mobile breakpoint active (≤768px)', 'success');
        const mobileLeftMenuToggle = document.querySelector('.mobile-left-menu-toggle');
        if (mobileLeftMenuToggle) {
            const computedStyle = window.getComputedStyle(mobileLeftMenuToggle);
            log(`Mobile CSS - Display: ${computedStyle.display}`, 'info');
        }
    } else {
        log('Desktop breakpoint active (>768px)', 'info');
    }
});

// Add some visual debugging
setTimeout(function() {
    log('Adding visual debugging elements...', 'info');
    
    // Add debug info to page
    const debugDiv = document.createElement('div');
    debugDiv.style.cssText = 'position: fixed; top: 10px; right: 10px; background: rgba(0,0,0,0.8); color: white; padding: 10px; border-radius: 5px; font-size: 12px; z-index: 10000;';
    debugDiv.innerHTML = `
        <strong>Debug Info:</strong><br>
        Width: <span id="liveWidth">${window.innerWidth}px</span><br>
        Mobile: <span id="liveMobile">${window.innerWidth <= 768 ? 'Yes' : 'No'}</span><br>
        Left Toggle: <span id="liveLeftToggle">${document.querySelector('.mobile-left-menu-toggle') ? 'Found' : 'Not Found'}</span><br>
        Left Menu: <span id="liveLeftMenu">${document.getElementById('mobileLeftMenu') ? 'Found' : 'Not Found'}</span>
    `;
    document.body.appendChild(debugDiv);
    
    // Update live debug info
    setInterval(function() {
        document.getElementById('liveWidth').textContent = `${window.innerWidth}px`;
        document.getElementById('liveMobile').textContent = window.innerWidth <= 768 ? 'Yes' : 'No';
        document.getElementById('liveLeftToggle').textContent = document.querySelector('.mobile-left-menu-toggle') ? 'Found' : 'Not Found';
        document.getElementById('liveLeftMenu').textContent = document.getElementById('mobileLeftMenu') ? 'Found' : 'Not Found';
    }, 1000);
    
}, 1000);
</script>

<?php include 'app/Views/layouts/footer.php'; ?>
