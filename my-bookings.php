<?php
session_start();
require_once 'admin/inc/db_config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];$query = "SELECT b.*, r.name AS room_name FROM `booking_order` b JOIN `rooms` r ON b.room_id = r.id WHERE b.user_id = ? ORDER BY b.id DESC";
$res = select($query, [$user_id], "i");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Bookings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2 class="mb-4">My Bookings</h2>
    <div class="table-responsive bg-white p-3 shadow rounded">
        <table class="table table-bordered align-middle">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Room Name</th>
                    <th>Check-in</th>
                    <th>Check-out</th>
                    <th>Price</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $i = 1;
                while ($row = mysqli_fetch_assoc($res)) {
                    echo "<tr>
                        <td>{$i}</td>
                        <td>{$row['room_name']}</td>
                        <td>{$row['check_in']}</td>
                        <td>{$row['check_out']}</td>
                        <td>₹{$row['price']}</td>
                        <td><span class='badge bg-success'>{$row['booking_status']}</span></td>
                    </tr>";
                    $i++;
                }
                if ($i == 1) {
                    echo "<tr><td colspan='6' class='text-center'>Koi booking nahi mili.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>