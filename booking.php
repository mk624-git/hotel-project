<?php
session_start();
require_once 'admin/inc/db_config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: rooms.php");
    exit();
}

$room_id = sanitize($_GET['id']);
$user_id =$_SESSION['user_id'];

$room_res = select("SELECT * FROM `rooms` WHERE `id`=? LIMIT 1", [$room_id], "i");
$room = mysqli_fetch_assoc($room_res);

if (isset($_POST['pay_now'])) {
    $check_in = sanitize($_POST['check_in']);
    $check_out = sanitize($_POST['check_out']);

    $query = "INSERT INTO `booking_order` (`user_id`, `room_id`, `check_in`, `check_out`, `price`, `booking_status`) VALUES (?, ?, ?, ?, ?, 'booked')";
    $values = [$user_id, $room_id,$check_in, $check_out,$room['price']];

    if (insert($query,$values, 'iissi')) {
        header("Location: booking-confirmation.php");
        exit();
    } else {
        echo "<script>alert('Booking failed!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Book Room</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow p-4">
                <h3>Confirm Booking for <?= $room['name'] ?></h3>
                <p class="fw-bold">Price: ₹<?= $room['price'] ?> per night</p>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Check-in Date</label>
                        <input type="date" name="check_in" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Check-out Date</label>
                        <input type="date" name="check_out" class="form-control" required>
                    </div>
                    <button type="submit" name="pay_now" class="btn btn-primary w-100">Confirm & Book</button>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>