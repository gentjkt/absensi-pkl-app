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
    <h2>🔍 Debug CSS Hamburger Menu</h2>
    
    <div class="debug-section">
        <h3>📱 Status CSS Hamburger</h3>
        <div id="debugInfo">
            <p><strong>Session User:</strong> <span id="sessionStatus">Checking...</span></p>
            <p><strong>Mobile Left Menu Toggle:</strong> <span id="leftToggleStatus">Checking...</span></p>
            <p><strong>Mobile Left Menu Button:</strong> <span id="leftBtnStatus">Checking...</span></p>
            <p><strong>Window Width:</strong> <span id="windowWidth">Checking...</span></p>
            <p><strong>CSS Display Property:</strong> <span id="cssDisplay">Checking...</span></p>
            <p><strong>CSS Visibility Property:</strong> <span id="cssVisibility">Checking...</span></p>
            <p><strong>CSS Opacity Property:</strong> <span id="cssOpacity">Checking...</span></p>
        </div>
    </div>
    
    <div class="debug-section">
        <h3>🧪 Test CSS Hamburger</h3>
        <button id="forceShowBtn" class="btn btn-success">Force Show Hamburger</button>
        <button id="forceHideBtn" class="btn btn-danger">Force Hide Hamburger</button>
        <button id="toggleDebugBtn" class="btn btn-info">Toggle Debug Borders</button>
        <button id="testCSSBtn" class="btn btn-warning">Test CSS Properties</button>
        <button id="inspectElementBtn" class="btn btn-secondary">Inspect Element</button>
    </div>
    
    <div class="debug-section">
        <h3>📋 Console Log</h3>
        <div id="consoleLog" style="background: #f5f5f5; padding: 10px; border-radius: 5px; font-family: monospace; max-height: 200px; overflow-y: auto;">
            <p>Console log akan muncul di sini...</p>
        </div>
    </div>
    
    <div class="debug-section">
        <h3>🎨 CSS Debug Info</h3>
        <div style="background: #fff3cd; padding: 15px; border-radius: 5px; border: 1px solid #ffeaa7;">
            <h4>CSS yang seharusnya aktif:</h4>
            <ul>
                <li><code>.mobile-left-menu-toggle { display: none; }</code> - Desktop default</li>
                <li><code>@media (max-width: 768px) { .mobile-left-menu-toggle { display: block !important; } }</code> - Mobile</li>
                <li><code>.mobile-left-menu-btn { display: flex; }</code> - Button styling</li>
                <li><code>.mobile-left-menu-btn span { width: 20px; height: 2px; background: white; }</code> - Hamburger lines</li>
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
                <li>Gunakan "Inspect Element" untuk melihat CSS</li>
            </ol>
        </div>
    </div>
    
    <div class="debug-section">
        <h3>🐛 Troubleshooting CSS</h3>
        <div style="background: #f8d7da; padding: 15px; border-radius: 5px;">
            <h4>Masalah CSS yang mungkin terjadi:</h4>
            <ul>
                <li><strong>CSS Not Loading:</strong> File CSS tidak ter-load</li>
                <li><strong>CSS Override:</strong> CSS lain mengoverride style hamburger</li>
                <li><strong>Media Query Issue:</strong> Media query tidak berfungsi</li>
                <li><strong>CSS Specificity:</strong> CSS tidak cukup spesifik</li>
                <li><strong>Browser Cache:</strong> CSS lama masih tersimpan</li>
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

.debug-border .mobile-left-menu-btn span {
    border: 1px solid green !important;
    background: green !important;
}

/* Force show hamburger for debugging */
.force-show .mobile-left-menu-toggle {
    display: block !important;
    background: rgba(255, 255, 0, 0.3) !important;
    border: 2px solid yellow !important;
    padding: 10px !important;
    margin: 5px !important;
    position: relative !important;
    z-index: 1000 !important;
}

.force-show .mobile-left-menu-btn {
    background: rgba(255, 255, 0, 0.5) !important;
    border: 2px solid orange !important;
}

.force-show .mobile-left-menu-btn span {
    background: black !important;
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.5) !important;
}

/* Ensure elements are visible for debugging */
.mobile-left-menu-toggle {
    pointer-events: auto !important;
}

.mobile-left-menu-btn {
    pointer-events: auto !important;
}

/* Test CSS untuk memastikan hamburger terlihat */
.test-hamburger .mobile-left-menu-toggle {
    display: block !important;
    background: rgba(255, 0, 255, 0.3) !important;
    border: 3px solid magenta !important;
    padding: 15px !important;
    margin: 10px !important;
    min-width: 60px !important;
    min-height: 60px !important;
}

.test-hamburger .mobile-left-menu-btn {
    background: rgba(255, 0, 255, 0.5) !important;
    border: 2px solid purple !important;
    width: 50px !important;
    height: 50px !important;
}

.test-hamburger .mobile-left-menu-btn span {
    background: white !important;
    width: 25px !important;
    height: 3px !important;
    margin: 2px 0 !important;
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.5) !important;
}
</style>

<script>
// Debug script untuk CSS hamburger
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
    const windowWidth = window.innerWidth;
    
    // Update status display
    document.getElementById('sessionStatus').textContent = sessionUser ? `Logged in as ${sessionUser.name}` : 'Not logged in';
    document.getElementById('leftToggleStatus').textContent = mobileLeftMenuToggle ? 'Found ✓' : 'Not Found ✗';
    document.getElementById('leftBtnStatus').textContent = mobileLeftMenuBtn ? 'Found ✓' : 'Not Found ✗';
    document.getElementById('windowWidth').textContent = `${windowWidth}px`;
    
    // Check CSS properties
    if (mobileLeftMenuToggle) {
        const computedStyle = window.getComputedStyle(mobileLeftMenuToggle);
        const display = computedStyle.display;
        const visibility = computedStyle.visibility;
        const opacity = computedStyle.opacity;
        
        document.getElementById('cssDisplay').textContent = `display: ${display}`;
        document.getElementById('cssVisibility').textContent = `visibility: ${visibility}`;
        document.getElementById('cssOpacity').textContent = `opacity: ${opacity}`;
        
        log(`CSS Status - Display: ${display}, Visibility: ${visibility}, Opacity: ${opacity}`, 'info');
    } else {
        document.getElementById('cssDisplay').textContent = 'Element not found';
        document.getElementById('cssVisibility').textContent = 'Element not found';
        document.getElementById('cssOpacity').textContent = 'Element not found';
    }
    
    log(`Status Update - Toggle: ${mobileLeftMenuToggle ? 'Found' : 'Not Found'}, Button: ${mobileLeftMenuBtn ? 'Found' : 'Not Found'}, Width: ${windowWidth}px`, 'info');
}

// Test functions
document.getElementById('forceShowBtn').addEventListener('click', function() {
    const mobileLeftMenuToggle = document.querySelector('.mobile-left-menu-toggle');
    if (mobileLeftMenuToggle) {
        document.body.classList.add('force-show');
        log('Hamburger forced to show with yellow background', 'success');
    } else {
        log('Mobile left menu toggle element not found', 'error');
    }
});

document.getElementById('forceHideBtn').addEventListener('click', function() {
    document.body.classList.remove('force-show');
    log('Hamburger forced to hide', 'success');
});

document.getElementById('toggleDebugBtn').addEventListener('click', function() {
    document.body.classList.toggle('debug-border');
    const isActive = document.body.classList.contains('debug-border');
    log(`Debug borders ${isActive ? 'enabled' : 'disabled'}`, isActive ? 'success' : 'info');
});

document.getElementById('testCSSBtn').addEventListener('click', function() {
    document.body.classList.toggle('test-hamburger');
    const isActive = document.body.classList.contains('test-hamburger');
    log(`Test hamburger CSS ${isActive ? 'enabled' : 'disabled'}`, isActive ? 'success' : 'info');
});

document.getElementById('inspectElementBtn').addEventListener('click', function() {
    const mobileLeftMenuToggle = document.querySelector('.mobile-left-menu-toggle');
    if (mobileLeftMenuToggle) {
        log('Inspect element: mobile-left-menu-toggle', 'info');
        log('Right-click on the element and select "Inspect"', 'warning');
        log('Check the Styles tab to see applied CSS', 'info');
        
        // Highlight element
        mobileLeftMenuToggle.style.outline = '3px solid red';
        mobileLeftMenuToggle.style.outlineOffset = '2px';
        
        setTimeout(() => {
            mobileLeftMenuToggle.style.outline = '';
            mobileLeftMenuToggle.style.outlineOffset = '';
        }, 3000);
    } else {
        log('Element not found for inspection', 'error');
    }
});

// Initial status check
document.addEventListener('DOMContentLoaded', function() {
    log('DOM loaded, checking CSS hamburger...', 'info');
    updateStatus();
    
    // Check every 2 seconds
    setInterval(updateStatus, 2000);
    
    // Check if elements exist
    const mobileLeftMenuToggle = document.querySelector('.mobile-left-menu-toggle');
    const mobileLeftMenuBtn = document.getElementById('mobileLeftMenuBtn');
    
    if (mobileLeftMenuToggle) {
        log('Mobile left menu toggle element found', 'success');
        
        // Check CSS properties
        const computedStyle = window.getComputedStyle(mobileLeftMenuToggle);
        log(`Initial CSS - Display: ${computedStyle.display}, Visibility: ${computedStyle.visibility}`, 'info');
        
        // Check if CSS is working
        const display = computedStyle.display;
        if (display === 'none') {
            log('⚠️ CSS Issue: Element has display: none', 'warning');
        } else if (display === 'block') {
            log('✅ CSS Working: Element has display: block', 'success');
        } else {
            log(`ℹ️ CSS Status: Element has display: ${display}`, 'info');
        }
        
        // Add click event for testing
        mobileLeftMenuToggle.addEventListener('click', function() {
            log('Mobile left menu toggle clicked', 'info');
        });
    } else {
        log('Mobile left menu toggle element NOT found!', 'error');
    }
    
    if (mobileLeftMenuBtn) {
        log('Mobile left menu button found', 'success');
        
        // Check button CSS
        const computedStyle = window.getComputedStyle(mobileLeftMenuBtn);
        log(`Button CSS - Display: ${computedStyle.display}, Width: ${computedStyle.width}, Height: ${computedStyle.height}`, 'info');
    } else {
        log('Mobile left menu button NOT found!', 'error');
    }
    
    // Check CSS file loading
    const cssLinks = document.querySelectorAll('link[rel="stylesheet"]');
    log(`Found ${cssLinks.length} CSS files`, 'info');
    
    cssLinks.forEach((link, index) => {
        log(`CSS ${index + 1}: ${link.href}`, 'info');
    });
});

// Window resize handler
window.addEventListener('resize', function() {
    const width = window.innerWidth;
    log(`Window resized to ${width}px`, 'info');
    
    // Check if mobile breakpoint is active
    if (width <= 768) {
        log('📱 Mobile breakpoint active (≤768px)', 'success');
        const mobileLeftMenuToggle = document.querySelector('.mobile-left-menu-toggle');
        if (mobileLeftMenuToggle) {
            const computedStyle = window.getComputedStyle(mobileLeftMenuToggle);
            log(`Mobile CSS - Display: ${computedStyle.display}`, 'info');
            
            // Check if media query is working
            if (computedStyle.display === 'block') {
                log('✅ Media query working: display: block', 'success');
            } else {
                log('❌ Media query NOT working: display: ' + computedStyle.display, 'error');
            }
        }
    } else {
        log('🖥️ Desktop breakpoint active (>768px)', 'info');
    }
});

// Add some visual debugging
setTimeout(function() {
    log('Adding visual debugging elements...', 'info');
    
    // Add debug info to page
    const debugDiv = document.createElement('div');
    debugDiv.style.cssText = 'position: fixed; top: 10px; right: 10px; background: rgba(0,0,0,0.8); color: white; padding: 10px; border-radius: 5px; font-size: 12px; z-index: 10000;';
    debugDiv.innerHTML = `
        <strong>CSS Debug Info:</strong><br>
        Width: <span id="liveWidth">${window.innerWidth}px</span><br>
        Mobile: <span id="liveMobile">${window.innerWidth <= 768 ? 'Yes' : 'No'}</span><br>
        Toggle: <span id="liveToggle">${document.querySelector('.mobile-left-menu-toggle') ? 'Found' : 'Not Found'}</span><br>
        Display: <span id="liveDisplay">${document.querySelector('.mobile-left-menu-toggle') ? window.getComputedStyle(document.querySelector('.mobile-left-menu-toggle')).display : 'N/A'}</span>
    `;
    document.body.appendChild(debugDiv);
    
    // Update live debug info
    setInterval(function() {
        const toggle = document.querySelector('.mobile-left-menu-toggle');
        document.getElementById('liveWidth').textContent = `${window.innerWidth}px`;
        document.getElementById('liveMobile').textContent = window.innerWidth <= 768 ? 'Yes' : 'No';
        document.getElementById('liveToggle').textContent = toggle ? 'Found' : 'Not Found';
        document.getElementById('liveDisplay').textContent = toggle ? window.getComputedStyle(toggle).display : 'N/A';
    }, 1000);
    
}, 1000);
</script>

<?php include 'app/Views/layouts/footer.php'; ?>
