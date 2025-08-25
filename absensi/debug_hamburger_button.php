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
    <h2>🔍 Debug Tombol Hamburger</h2>
    
    <div class="debug-section">
        <h3>📱 Status Tombol Hamburger</h3>
        <div id="debugInfo">
            <p><strong>Session User:</strong> <span id="sessionStatus">Checking...</span></p>
            <p><strong>Mobile Menu Toggle Element:</strong> <span id="toggleStatus">Checking...</span></p>
            <p><strong>Mobile Menu Button:</strong> <span id="btnStatus">Checking...</span></p>
            <p><strong>Window Width:</strong> <span id="windowWidth">Checking...</span></p>
            <p><strong>CSS Display Property:</strong> <span id="cssDisplay">Checking...</span></p>
        </div>
    </div>
    
    <div class="debug-section">
        <h3>🧪 Test CSS</h3>
        <button id="forceShowBtn" class="btn btn-success">Force Show Hamburger</button>
        <button id="forceHideBtn" class="btn btn-danger">Force Hide Hamburger</button>
        <button id="toggleDebugBtn" class="btn btn-info">Toggle Debug Borders</button>
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
                <li><code>.mobile-menu-toggle { display: none; }</code> - Desktop default</li>
                <li><code>@media (max-width: 768px) { .mobile-menu-toggle { display: block !important; } }</code> - Mobile</li>
                <li><code>.mobile-menu-btn { display: flex; }</code> - Button styling</li>
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
                <li>Lihat apakah tombol hamburger muncul</li>
                <li>Jika tidak muncul, gunakan button "Force Show Hamburger"</li>
            </ol>
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
.debug-border .mobile-menu-toggle {
    border: 3px solid red !important;
    background: rgba(255, 0, 0, 0.1) !important;
}

.debug-border .mobile-menu-btn {
    border: 2px solid blue !important;
    background: rgba(0, 0, 255, 0.1) !important;
}

/* Force show hamburger for debugging */
.force-show .mobile-menu-toggle {
    display: block !important;
    background: rgba(255, 255, 0, 0.3) !important;
    border: 2px solid yellow !important;
    padding: 10px !important;
    margin: 5px !important;
}

.force-show .mobile-menu-btn {
    background: rgba(255, 255, 0, 0.5) !important;
    border: 2px solid orange !important;
}
</style>

<script>
// Debug script untuk tombol hamburger
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
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const windowWidth = window.innerWidth;
    
    // Update status display
    document.getElementById('sessionStatus').textContent = sessionUser ? `Logged in as ${sessionUser.name}` : 'Not logged in';
    document.getElementById('toggleStatus').textContent = mobileMenuToggle ? 'Found ✓' : 'Not Found ✗';
    document.getElementById('btnStatus').textContent = mobileMenuBtn ? 'Found ✓' : 'Not Found ✗';
    document.getElementById('windowWidth').textContent = `${windowWidth}px`;
    
    // Check CSS display property
    if (mobileMenuToggle) {
        const computedStyle = window.getComputedStyle(mobileMenuToggle);
        const display = computedStyle.display;
        const visibility = computedStyle.visibility;
        const opacity = computedStyle.opacity;
        document.getElementById('cssDisplay').textContent = `display: ${display}, visibility: ${visibility}, opacity: ${opacity}`;
        
        log(`CSS Status - Display: ${display}, Visibility: ${visibility}, Opacity: ${opacity}`, 'info');
    } else {
        document.getElementById('cssDisplay').textContent = 'Element not found';
    }
    
    log(`Status Update - Toggle: ${mobileMenuToggle ? 'Found' : 'Not Found'}, Button: ${mobileMenuBtn ? 'Found' : 'Not Found'}, Width: ${windowWidth}px`, 'info');
}

// Test functions
document.getElementById('forceShowBtn').addEventListener('click', function() {
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    if (mobileMenuToggle) {
        mobileMenuToggle.classList.add('force-show');
        log('Hamburger button forced to show', 'success');
    } else {
        log('Mobile menu toggle element not found', 'error');
    }
});

document.getElementById('forceHideBtn').addEventListener('click', function() {
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    if (mobileMenuToggle) {
        mobileMenuToggle.classList.remove('force-show');
        log('Hamburger button forced to hide', 'success');
    }
});

document.getElementById('toggleDebugBtn').addEventListener('click', function() {
    document.body.classList.toggle('debug-border');
    const isActive = document.body.classList.contains('debug-border');
    log(`Debug borders ${isActive ? 'enabled' : 'disabled'}`, isActive ? 'success' : 'info');
});

// Initial status check
document.addEventListener('DOMContentLoaded', function() {
    log('DOM loaded, checking hamburger button...', 'info');
    updateStatus();
    
    // Check every 2 seconds
    setInterval(updateStatus, 2000);
    
    // Check if elements exist
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    
    if (mobileMenuToggle) {
        log('Mobile menu toggle element found', 'success');
        
        // Check CSS properties
        const computedStyle = window.getComputedStyle(mobileMenuToggle);
        log(`Initial CSS - Display: ${computedStyle.display}, Visibility: ${computedStyle.visibility}`, 'info');
        
        // Add click event for testing
        mobileMenuToggle.addEventListener('click', function() {
            log('Mobile menu toggle clicked', 'info');
        });
    } else {
        log('Mobile menu toggle element NOT found!', 'error');
    }
    
    if (mobileMenuBtn) {
        log('Mobile menu button found', 'success');
    } else {
        log('Mobile menu button NOT found!', 'error');
    }
});

// Window resize handler
window.addEventListener('resize', function() {
    const width = window.innerWidth;
    log(`Window resized to ${width}px`, 'info');
    
    // Check if mobile breakpoint is active
    if (width <= 768) {
        log('Mobile breakpoint active (≤768px)', 'success');
        const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
        if (mobileMenuToggle) {
            const computedStyle = window.getComputedStyle(mobileMenuToggle);
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
        Toggle: <span id="liveToggle">${document.querySelector('.mobile-menu-toggle') ? 'Found' : 'Not Found'}</span>
    `;
    document.body.appendChild(debugDiv);
    
    // Update live debug info
    setInterval(function() {
        document.getElementById('liveWidth').textContent = `${window.innerWidth}px`;
        document.getElementById('liveMobile').textContent = window.innerWidth <= 768 ? 'Yes' : 'No';
        document.getElementById('liveToggle').textContent = document.querySelector('.mobile-menu-toggle') ? 'Found' : 'Not Found';
    }, 1000);
    
}, 1000);
</script>

<?php include 'app/Views/layouts/footer.php'; ?>
