<?php
require_once('inc/db_config.php');

$password = 'admin123';
$new_hash = password_hash($password, PASSWORD_DEFAULT);

// Update the admin password
$stmt = $pdo->prepare("UPDATE admins SET password = ? WHERE username = 'admin'");
$result = $stmt->execute([$new_hash]);

if ($result) {
    echo "<h1 style='color:green'>✅ Password updated successfully!</h1>";
    echo "<p>New hash: <code>" . htmlspecialchars($new_hash) . "</code></p>";
    echo "<p>Password: <strong>admin123</strong></p>";
    echo "<a href='login.php'>Go to Login</a>";
} else {
    echo "<h1 style='color:red'>❌ Failed to update password</h1>";
}
?>