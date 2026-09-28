<?php
require_once('inc/db_config.php');

echo "<h1>Password Hash Test</h1>";

// Get the admin from database
$admin = db_select("SELECT * FROM admins WHERE username = 'admin'", [], true);

if ($admin) {
    echo "<h3>Admin found in database:</h3>";
    echo "<pre>";
    print_r($admin);
    echo "</pre>";
    
    $hash = $admin['password'];
    $password = 'admin123';
    
    echo "<h3>Testing password_verify():</h3>";
    echo "Password: <strong>$password</strong><br>";
    echo "Hash from DB: <code>" . htmlspecialchars($hash) . "</code><br>";
    echo "Hash length: " . strlen($hash) . " characters<br>";
    
    $result = password_verify($password, $hash);
    
    echo "<h3>Result: " . ($result ? "✅ SUCCESS - Password matches!" : "❌ FAILED - Password does NOT match") . "</h3>";
    
    // Try to generate a new hash to compare
    $new_hash = password_hash($password, PASSWORD_DEFAULT);
    echo "<h3>New hash for '$password':</h3>";
    echo "<code>" . htmlspecialchars($new_hash) . "</code><br>";
    echo "New hash length: " . strlen($new_hash) . " characters<br>";
    
    // Check if new hash verifies
    $new_result = password_verify($password, $new_hash);
    echo "New hash verification: " . ($new_result ? "✅ OK" : "❌ FAILED") . "<br>";
    
    // Update with new hash if needed
    echo "<h3>Update with new hash?</h3>";
    echo "<a href='update_hash.php' class='btn btn-danger'>Click here to update admin password with new hash</a>";
    
} else {
    echo "<h3 style='color:red'>❌ Admin not found in database!</h3>";
}
?>