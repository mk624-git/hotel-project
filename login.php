<?php
session_start();
require_once 'admin/inc/db_config.php';

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if (isset($_POST['login'])) {
    $email = sanitize($_POST['email']);
    $pass =$_POST['pass'];

    $res = select("SELECT * FROM `user_cred` WHERE `email`=? LIMIT 1", [$email], "s");
    
    if (mysqli_num_rows($res) == 1) {
        $user = mysqli_fetch_assoc($res);
        if (password_verify($pass,$user['password'])) {
            $_SESSION['user_id'] =$user['id'];
            $_SESSION['user_name'] =$user['name'];
            header("Location: index.php");
            exit();
        } else {
            echo "<script>alert('Galat Password!');</script>";
        }
    } else {
        echo "<script>alert('Email registered nahi hai!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card shadow p-4">
                <h3 class="text-center mb-4">Login</h3>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="pass" class="form-control" required>
                    </div>
                    <button type="submit" name="login" class="btn btn-success w-100">Login</button>
                    <p class="text-center mt-3">Naya account banayein? <a href="register.php">Register</a></p>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>