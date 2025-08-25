<?php
session_start();

// Simulasi user login
$_SESSION['user'] = [
    'id' => 1,
    'username' => 'admin',
    'name' => 'Administrator',
    'role' => 'admin'
];

// Load config
$app = require 'config/app.php';

echo "<h2>🔧 Test App Title Configuration</h2>";

echo "<div style='background: #e8f5e8; padding: 15px; border-radius: 8px; margin-bottom: 20px;'>";
echo "<h3>✅ Config Values:</h3>";
echo "<ul>";
echo "<li><strong>App Name:</strong> " . htmlspecialchars($app['name']) . "</li>";
echo "<li><strong>App Version:</strong> " . htmlspecialchars($app['version']) . "</li>";
echo "<li><strong>App Description:</strong> " . htmlspecialchars($app['description']) . "</li>";
echo "<li><strong>Session Name:</strong> " . htmlspecialchars($app['session_name']) . "</li>";
echo "<li><strong>Timezone:</strong> " . htmlspecialchars($app['timezone']) . "</li>";
echo "</ul>";
echo "</div>";

echo "<div style='background: #fff3cd; padding: 15px; border-radius: 8px; margin-bottom: 20px;'>";
echo "<h3>🔍 Test Results:</h3>";

// Test 1: Check if config is loaded
if (isset($app['name']) && !empty($app['name'])) {
    echo "<p>✅ <strong>Config loaded:</strong> App name is '" . htmlspecialchars($app['name']) . "'</p>";
} else {
    echo "<p>❌ <strong>Config error:</strong> App name is empty or not set</p>";
}

// Test 2: Check if title is consistent
$expectedTitle = 'Absensi PKL';
if ($app['name'] === $expectedTitle) {
    echo "<p>✅ <strong>Title consistency:</strong> App name matches expected title</p>";
} else {
    echo "<p>⚠️ <strong>Title mismatch:</strong> Expected '" . $expectedTitle . "' but got '" . htmlspecialchars($app['name']) . "'</p>";
}

// Test 3: Check if description is set
if (isset($app['description']) && !empty($app['description'])) {
    echo "<p>✅ <strong>Description:</strong> " . htmlspecialchars($app['description']) . "</p>";
} else {
    echo "<p>❌ <strong>Description error:</strong> App description is empty or not set</p>";
}

echo "</div>";

echo "<div style='background: #f8d7da; padding: 15px; border-radius: 8px;'>";
echo "<h3>🐛 Troubleshooting:</h3>";
echo "<ul>";
echo "<li>Jika title tidak muncul, periksa file <code>config/app.php</code></li>";
echo "<li>Pastikan variable <code>\$app</code> tersedia di view</li>";
echo "<li>Periksa apakah config file ter-load dengan benar</li>";
echo "<li>Pastikan path ke config file benar</li>";
echo "</ul>";
echo "</div>";

echo "<div style='background: #e8f5e8; padding: 15px; border-radius: 8px;'>";
echo "<h3>🔧 Next Steps:</h3>";
echo "<ol>";
echo "<li>Test halaman login: <code>http://localhost/?r=auth/login</code></li>";
echo "<li>Periksa title di browser tab</li>";
echo "<li>Pastikan title konsisten di semua halaman</li>";
echo "<li>Test responsive design dengan resize browser</li>";
echo "</ol>";
echo "</div>";

// Test header include
echo "<h3>📱 Test Header Include:</h3>";
echo "<div style='border: 2px solid #ddd; padding: 20px; border-radius: 8px;'>";
echo "<p><strong>Testing header include...</strong></p>";

try {
    include 'app/Views/layouts/header.php';
    echo "<p>✅ <strong>Header loaded successfully</strong></p>";
} catch (Exception $e) {
    echo "<p>❌ <strong>Header error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "</div>";
?>

<style>
body {
    font-family: 'Inter', sans-serif;
    line-height: 1.6;
    margin: 20px;
    background: #f8f9fa;
}

h2, h3 {
    color: #2c3e50;
}

.test-section {
    margin-bottom: 20px;
    padding: 15px;
    border-radius: 8px;
}

.success { background: #d4edda; border: 1px solid #c3e6cb; }
.warning { background: #fff3cd; border: 1px solid #ffeaa7; }
.danger { background: #f8d7da; border: 1px solid #f5c6cb; }
.info { background: #d1ecf1; border: 1px solid #bee5eb; }

code {
    background: #f8f9fa;
    padding: 2px 6px;
    border-radius: 4px;
    font-family: 'Courier New', monospace;
    border: 1px solid #e9ecef;
}
</style>
