<?php
echo "<h1>Direct PHP password_verify() Test</h1>";

$password = 'admin123';

// The correct hash for 'admin123'
$hash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';

echo "Password: <strong>$password</strong><br>";
echo "Hash: <code>" . htmlspecialchars($hash) . "</code><br>";
echo "Hash length: " . strlen($hash) . "<br><br>";

$result = password_verify($password, $hash);

echo "<h2>Result: " . ($result ? "✅ SUCCESS" : "❌ FAILED") . "</h2>";

// Test with a new hash
$new_hash = password_hash($password, PASSWORD_DEFAULT);
echo "<br><strong>New hash generated:</strong><br>";
echo "<code>" . htmlspecialchars($new_hash) . "</code><br>";
echo "New hash length: " . strlen($new_hash) . "<br>";

$new_result = password_verify($password, $new_hash);
echo "New hash verification: " . ($new_result ? "✅ SUCCESS" : "❌ FAILED") . "<br>";

// Show PHP version info
echo "<br><strong>PHP Info:</strong><br>";
echo "PHP Version: " . phpversion() . "<br>";
echo "password_hash available: " . (function_exists('password_hash') ? "✅ Yes" : "❌ No") . "<br>";
echo "password_verify available: " . (function_exists('password_verify') ? "✅ Yes" : "❌ No") . "<br>";
?>