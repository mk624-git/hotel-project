<?php
session_start();
require_once('inc/db_config.php');

header('Content-Type: application/json');

// ============================================
// LOGIN
// ============================================
if (isset($_POST['login']) || isset($_POST['login_submit'])) {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Please enter email and password']);
        exit;
    }
    
    $user = db_select("SELECT * FROM users WHERE email = ? AND status = 'active'", [$email], true);
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_logged_in'] = true;
        
        echo json_encode(['success' => true, 'message' => 'Login successful']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
    }
    exit;
}

// ============================================
// REGISTER
// ============================================
if (isset($_POST['register']) || isset($_POST['register_submit'])) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Validation
    if (empty($name) || empty($email) || empty($phone) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Please fill in all fields']);
        exit;
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email address']);
        exit;
    }
    
    if (strlen($password) < 6) {
        echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters']);
        exit;
    }
    
    // Check if email already exists
    $existing = db_select("SELECT id FROM users WHERE email = ?", [$email], true);
    if ($existing) {
        echo json_encode(['success' => false, 'message' => 'Email already registered']);
        exit;
    }
    
    // Hash password and insert
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $data = [
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'password' => $hashed_password,
        'status' => 'active'
    ];
    
    $id = db_insert('users', $data);
    
    if ($id) {
        $_SESSION['user_id'] = $id;
        $_SESSION['user_name'] = $name;
        $_SESSION['user_email'] = $email;
        $_SESSION['user_logged_in'] = true;
        
        echo json_encode(['success' => true, 'message' => 'Registration successful']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Registration failed. Please try again.']);
    }
    exit;
}

// ============================================
// LOGOUT
// ============================================
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header('Location: index.php');
    exit;
}

// If no action specified
echo json_encode(['success' => false, 'message' => 'Invalid request']);
exit;
?>