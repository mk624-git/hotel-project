<?php
require_once('inc/db_config.php');

echo "<h1>Creating Admin User</h1>";

$username = 'admin';
$password = 'admin123';
$hash = password_hash($password, PASSWORD_DEFAULT);

try {
    // Check if admin exists
    $check = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
    $check->execute([$username]);
    $exists = $check->fetch();
    
    if ($exists) {
        echo "Admin already exists. Updating password...<br>";
        $stmt = $pdo->prepare("UPDATE admins SET password = ? WHERE username = ?");
        $stmt->execute([$hash, $username]);
        echo "✅ Password updated!<br>";
    } else {
        echo "Creating new admin...<br>";
        $stmt = $pdo->prepare("INSERT INTO admins (username, password, full_name, email, role, status) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$username, $hash, 'Administrator', 'admin@citystay.com', 'admin', 'active']);
        echo "✅ Admin created!<br>";
    }
    
    echo "<br><strong>Login Credentials:</strong><br>";
    echo "Username: <strong>$username</strong><br>";
    echo "Password: <strong>$password</strong><br>";
    echo "<br><a href='login.php'>Go to Login</a>";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>