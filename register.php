<?php
require_once 'admin/inc/db_config.php';

if (isset($_POST['register'])) {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $phonenum = sanitize($_POST['phonenum']);
    $pass = password_hash($_POST['pass'], PASSWORD_BCRYPT);

    $check_user = select("SELECT * FROM `user_cred` WHERE `email`=? OR `phonenum`=? LIMIT 1", [$email,$phonenum], "ss");
    
    if (mysqli_num_rows($check_user) > 0) {
        echo "<script>alert('Email ya Phone Number pehle se registered hai!');</script>";
    } else {
        $query = "INSERT INTO `user_cred` (`name`, `email`, `phonenum`, `password`) VALUES (?, ?, ?, ?)";
        $values = [$name,$email, $phonenum,$pass];
        if (insert($query,$values, 'ssss')) {
            echo "<script>alert('Registration successful! Ab aap login kar sakte hain.'); window.location.href='login.php';</script>";
        } else {
            echo "<script>alert('Kuch galat hua. Phir se koshish karein.');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow p-4">
                <h3 class="text-center mb-4">Register</h3>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone Number</label>
                        <input type="text" name="phonenum" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="pass" class="form-control" required>
                    </div>
                    <button type="submit" name="register" class="btn btn-primary w-100">Register</button>
                    <p class="text-center mt-3">Pehle se account hai? <a href="login.php">Login karein</a></p>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>