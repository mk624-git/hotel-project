<?php
session_start();
require_once('inc/db_config.php');

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

$id = $_GET['id'] ?? 0;

$booking = db_select("
    SELECT b.*, g.name as guest_name, g.email, g.phone, g.address,
           r.room_number, r.room_type, r.price as room_price
    FROM bookings b 
    LEFT JOIN guests g ON b.guest_id = g.id 
    LEFT JOIN rooms r ON b.room_id = r.id 
    WHERE b.id = ?
", [$id], true);

if (!$booking) {
    header("Location: booking.php");
    exit;
}

$nights = getNights($booking['check_in'], $booking['check_out']);
$total = $booking['total_amount'] ?? ($booking['room_price'] * $nights);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Details - <?= SITE_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Poppins', sans-serif; }
        .main-content { margin-left: 260px; padding: 20px; padding-top: 80px; }
        .card-custom {
            border: none;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .status-badge {
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
        }
        .detail-label {
            font-weight: 600;
            color: #636e72;
        }
        .btn-custom {
            background: #2ec1ac;
            border: none;
            color: white;
            border-radius: 10px;
            padding: 8px 20px;
        }
        .btn-custom:hover {
            background: #1a9b8a;
            color: white;
        }
        .text-teal {
            color: #2ec1ac;
        }
        @media (max-width: 768px) {
            .main-content { margin-left: 0; padding: 15px; padding-top: 70px; }
        }
    </style>
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="main-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-0">Booking Details</h4>
                <p class="text-muted mb-0">#<?= htmlspecialchars($booking['booking_id'] ?? 'BK' . str_pad($booking['id'], 4, '0', STR_PAD_LEFT)) ?></p>
            </div>
            <div>
                <!-- FIXED: Changed to booking.php (singular) -->
                <a href="booking.php" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-2"></i> Back
                </a>
                <a href="billing.php?invoice=<?= $booking['id'] ?>" class="btn btn-custom">
                    <i class="bi bi-receipt me-2"></i> Invoice
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card card-custom">
                    <div class="card-body p-4">
                        <div class="row">
                            <div class="col-md-6">
                                <p class="detail-label">Booking ID</p>
                                <p class="fw-bold"><?= htmlspecialchars($booking['booking_id'] ?? 'BK' . str_pad($booking['id'], 4, '0', STR_PAD_LEFT)) ?></p>
                                
                                <p class="detail-label mt-3">Status</p>
                                <span class="status-badge bg-<?= getBookingStatusBadge($booking['status']) ?> text-white">
                                    <?= ucfirst($booking['status']) ?>
                                </span>
                                
                                <p class="detail-label mt-3">Room</p>
                                <p class="fw-bold"><?= htmlspecialchars($booking['room_number'] ?? 'N/A') ?> 
                                    <small class="text-muted">(<?= htmlspecialchars($booking['room_type'] ?? '') ?>)</small>
                                </p>
                                
                                <p class="detail-label mt-3">Price per night</p>
                                <p><?= formatCurrency($booking['room_price'] ?? 0) ?></p>
                            </div>
                            <div class="col-md-6">
                                <p class="detail-label">Check-in</p>
                                <p class="fw-bold"><?= date('l, d M Y', strtotime($booking['check_in'])) ?></p>
                                
                                <p class="detail-label mt-3">Check-out</p>
                                <p class="fw-bold"><?= date('l, d M Y', strtotime($booking['check_out'])) ?></p>
                                
                                <p class="detail-label mt-3">Nights</p>
                                <p><?= $nights ?> nights</p>
                                
                                <p class="detail-label mt-3">Total Amount</p>
                                <p class="h5 text-teal"><?= formatCurrency($total) ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card card-custom">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3"><i class="bi bi-person me-2"></i>Guest Details</h5>
                        <p><strong>Name:</strong> <?= htmlspecialchars($booking['guest_name'] ?? 'Walk-in') ?></p>
                        <p><strong>Email:</strong> <?= htmlspecialchars($booking['email'] ?? 'N/A') ?></p>
                        <p><strong>Phone:</strong> <?= htmlspecialchars($booking['phone'] ?? 'N/A') ?></p>
                        <p><strong>Address:</strong> <?= htmlspecialchars($booking['address'] ?? 'N/A') ?></p>
                        <p><strong>Guests:</strong> <?= $booking['num_adults'] ?> Adults, <?= $booking['num_children'] ?> Children</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>