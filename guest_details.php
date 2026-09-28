<?php
session_start();
require_once('inc/db_config.php');

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

$id = $_GET['id'] ?? 0;

$guest = db_select("SELECT * FROM guests WHERE id = ?", [$id], true);

if (!$guest) {
    header("Location: guests.php");
    exit;
}

// Fetch guest's booking history
$bookings = db_select("
    SELECT b.*, r.room_number, r.room_type 
    FROM bookings b 
    LEFT JOIN rooms r ON b.room_id = r.id 
    WHERE b.guest_id = ? 
    ORDER BY b.created_at DESC
", [$id]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guest Details - <?= SITE_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Poppins', sans-serif; }
        .main-content { margin-left: 260px; padding: 20px; padding-top: 80px; }
        .card-custom { border: none; border-radius: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .detail-label { font-weight: 600; color: #636e72; }
        @media (max-width: 768px) { .main-content { margin-left: 0; padding: 15px; padding-top: 70px; } }
    </style>
</head>
<body>
<?php include 'sidebar.php'; ?>
<div class="main-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-0">Guest Details</h4>
                <p class="text-muted mb-0"><?= htmlspecialchars($guest['name']) ?></p>
            </div>
            <a href="guests.php" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-2"></i> Back
            </a>
        </div>

        <div class="row">
            <div class="col-lg-6">
                <div class="card card-custom">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3"><i class="bi bi-person me-2"></i>Personal Information</h5>
                        <p><span class="detail-label">Name:</span> <?= htmlspecialchars($guest['name']) ?></p>
                        <p><span class="detail-label">Email:</span> <?= htmlspecialchars($guest['email'] ?? 'N/A') ?></p>
                        <p><span class="detail-label">Phone:</span> <?= htmlspecialchars($guest['phone'] ?? 'N/A') ?></p>
                        <p><span class="detail-label">Address:</span> <?= htmlspecialchars($guest['address'] ?? 'N/A') ?></p>
                        <p><span class="detail-label">City:</span> <?= htmlspecialchars($guest['city'] ?? 'N/A') ?></p>
                        <p><span class="detail-label">State:</span> <?= htmlspecialchars($guest['state'] ?? 'N/A') ?></p>
                        <p><span class="detail-label">Pincode:</span> <?= htmlspecialchars($guest['pincode'] ?? 'N/A') ?></p>
                        <p><span class="detail-label">ID Proof:</span> <?= htmlspecialchars($guest['id_proof_type'] ?? 'N/A') ?> 
                            <?php if (!empty($guest['id_proof_number'])): ?>
                                (<?= htmlspecialchars($guest['id_proof_number']) ?>)
                            <?php endif; ?>
                        </p>
                        <p><span class="detail-label">Registered:</span> <?= date('d M Y, h:i A', strtotime($guest['created_at'])) ?></p>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card card-custom">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3"><i class="bi bi-calendar me-2"></i>Booking History</h5>
                        <?php if (empty($bookings)): ?>
                            <p class="text-muted">No bookings found for this guest.</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Booking ID</th>
                                            <th>Room</th>
                                            <th>Check-in</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($bookings as $booking): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($booking['booking_id'] ?? '#' . $booking['id']) ?></td>
                                            <td><?= htmlspecialchars($booking['room_number'] ?? 'N/A') ?></td>
                                            <td><?= date('d M Y', strtotime($booking['check_in'])) ?></td>
                                            <td>
                                                <span class="badge bg-<?= getBookingStatusBadge($booking['status']) ?>">
                                                    <?= ucfirst($booking['status']) ?>
                                                </span>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>