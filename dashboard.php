<?php
session_start();
require_once('inc/db_config.php');

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

// Fetch Statistics
$total_rooms = db_select("SELECT COUNT(*) as count FROM rooms", [], true)['count'] ?? 0;
$available_rooms = db_select("SELECT COUNT(*) as count FROM rooms WHERE status = 'available'", [], true)['count'] ?? 0;
$occupied_rooms = db_select("SELECT COUNT(*) as count FROM rooms WHERE status = 'occupied'", [], true)['count'] ?? 0;
$total_bookings = db_select("SELECT COUNT(*) as count FROM bookings", [], true)['count'] ?? 0;
$total_revenue = db_select("SELECT SUM(total_amount) as revenue FROM bookings WHERE status = 'checked_out'", [], true)['revenue'] ?? 0;

// Recent Bookings
$recent_bookings = db_select("
    SELECT b.*, g.name as guest_name, r.room_number, r.room_type 
    FROM bookings b 
    LEFT JOIN guests g ON b.guest_id = g.id 
    LEFT JOIN rooms r ON b.room_id = r.id 
    ORDER BY b.created_at DESC LIMIT 5
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - City Stay Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        .main-content { margin-left: 250px; padding: 20px; }
        .stat-card { transition: all 0.3s ease; }
        .stat-card:hover { transform: translateY(-8px); box-shadow: 0 10px 25px rgba(0,0,0,0.15); }
    </style>
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="main-content">
    <div class="container-fluid">
        <h2 class="mb-4">Dashboard Overview</h2>

        <!-- Stats Cards -->
        <div class="row g-4 mb-5">
            <div class="col-xl-3 col-md-6">
                <div class="card stat-card bg-primary text-white h-100">
                    <div class="card-body">
                        <i class="bi bi-door-open fs-1 mb-3"></i>
                        <h5>Total Rooms</h5>
                        <h2><?= $total_rooms ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card stat-card bg-success text-white h-100">
                    <div class="card-body">
                        <i class="bi bi-check-circle fs-1 mb-3"></i>
                        <h5>Available Rooms</h5>
                        <h2><?= $available_rooms ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card stat-card bg-warning text-white h-100">
                    <div class="card-body">
                        <i class="bi bi-person-fill fs-1 mb-3"></i>
                        <h5>Occupied Rooms</h5>
                        <h2><?= $occupied_rooms ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card stat-card bg-info text-white h-100">
                    <div class="card-body">
                        <i class="bi bi-currency-rupee fs-1 mb-3"></i>
                        <h5>Total Revenue</h5>
                        <h2>₹<?= number_format($total_revenue) ?></h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Bookings -->
        <div class="card">
            <div class="card-header">
                <h5>Recent Bookings</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Booking ID</th>
                                <th>Guest</th>
                                <th>Room</th>
                                <th>Check-in</th>
                                <th>Check-out</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recent_bookings)): ?>
                                <tr><td colspan="6" class="text-center py-4">No bookings found</td></tr>
                            <?php else: ?>
                                <?php foreach ($recent_bookings as $booking): ?>
                                <tr>
                                    <td>#<?= str_pad($booking['id'], 4, '0', STR_PAD_LEFT) ?></td>
                                    <td><?= htmlspecialchars($booking['guest_name'] ?? 'Walk-in') ?></td>
                                    <td><?= htmlspecialchars($booking['room_number'] ?? 'N/A') ?></td>
                                    <td><?= $booking['check_in'] ?></td>
                                    <td><?= $booking['check_out'] ?></td>
                                    <td>
                                        <span class="badge bg-<?= $booking['status'] === 'confirmed' ? 'success' : 'warning' ?>">
                                            <?= ucfirst($booking['status']) ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>