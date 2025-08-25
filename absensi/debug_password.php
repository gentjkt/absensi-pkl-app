<?php
// File debug untuk troubleshooting masalah password
session_start();
require_once 'app/autoload.php';

use App\Models\{User, Database};

echo "<h2>Debug Password Change</h2>";

// Cek session
echo "<h3>Session Info:</h3>";
if (isset($_SESSION['user'])) {
    echo "User logged in: " . $_SESSION['user']['username'] . "<br>";
    echo "User ID: " . $_SESSION['user']['id'] . "<br>";
} else {
    echo "No user logged in<br>";
}

// Cek database connection
echo "<h3>Database Connection:</h3>";
try {
    $db = new Database(require 'config/db.php');
    echo "Database connection: OK<br>";
    
    // Cek tabel users
    $stmt = $db->pdo()->query("SHOW TABLES LIKE 'users'");
    if ($stmt->rowCount() > 0) {
        echo "Table 'users': EXISTS<br>";
        
        // Cek struktur tabel
        $stmt = $db->pdo()->query("DESCRIBE users");
        echo "Table structure:<br>";
        while ($row = $stmt->fetch()) {
            echo "- " . $row['Field'] . " (" . $row['Type'] . ")<br>";
        }
        
        // Cek data user jika login
        if (isset($_SESSION['user'])) {
            $stmt = $db->pdo()->prepare("SELECT id, username, role FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['user']['id']]);
            $user = $stmt->fetch();
            
            if ($user) {
                echo "User data found: ID=" . $user['id'] . ", Username=" . $user['username'] . ", Role=" . $user['role'] . "<br>";
            } else {
                echo "User data NOT found in database<br>";
            }
        }
    } else {
        echo "Table 'users': NOT EXISTS<br>";
    }
    
} catch (Exception $e) {
    echo "Database error: " . $e->getMessage() . "<br>";
}

// Test password hashing
echo "<h3>Password Hashing Test:</h3>";
$testPassword = "test123";
$hash = password_hash($testPassword, PASSWORD_BCRYPT);
echo "Test password: " . $testPassword . "<br>";
echo "Hash: " . $hash . "<br>";
echo "Verify test: " . (password_verify($testPassword, $hash) ? "OK" : "FAILED") . "<br>";

echo "<hr>";
echo "<a href='public/?r=user/changePassword'>Kembali ke Change Password</a>";
?>
