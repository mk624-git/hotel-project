<?php
session_start();
require_once 'admin/inc/db_config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id =$_SESSION['user_id'];

if (isset($_POST['update_profile'])) {
    $name = sanitize($_POST['name']);
    $phonenum = sanitize($_POST['phonenum']);

    $q = "UPDATE `user_cred` SET `name`=?, `phonenum`=? WHERE `id`=?";
    if (update($q, [$name, $phonenum,$user_id], 'ssi')) {
        $_SESSION['user_name'] =$name;
        echo "<script>alert('Profile updated successfully!');</script>";
    }
}

$res = select("SELECT * FROM `user_cred` WHERE `id`=? LIMIT 1", [$user_id], "i");
$user = mysqli_fetch_assoc($res);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow p-4">
                <h3 class="mb-4">My Profile</h3>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="name" class="form-control" value="<?= $user['name'] ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email (Non-editable)</label>
                        <input type="email" class="form-control" value="<?= $user['email'] ?>" readonly disabled>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone Number</label>
                        <input type="text" name="phonenum" class="form-control" value="<?= $user['phonenum'] ?>" required>
                    </div>
                    <button type="submit" name="update_profile" class="btn btn-warning w-100">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>