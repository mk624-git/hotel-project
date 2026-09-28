<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Booking Successful</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light text-center py-5">
<div class="container">
    <div class="card p-5 shadow d-inline-block">
        <h1 class="text-success">🎉 Booking Successful!</h1>
        <p class="lead mt-3">Aapka room safaltapurvak book ho gaya hai.</p>
        <div class="mt-4">
            <a href="my-bookings.php" class="btn btn-primary">My Bookings Dekhein</a>
            <a href="index.php" class="btn btn-outline-secondary">Home Page</a>
        </div>
    </div>
</div>
</body>
</html>