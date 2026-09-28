<?php
session_start();
require_once('inc/db_config.php');

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

// Generate Invoice
if (isset($_GET['invoice'])) {
    $booking_id = $_GET['invoice'];
    $booking = db_select("
        SELECT b.*, g.name as guest_name, g.email, g.phone, g.address,
               r.room_number, r.room_type, r.price as room_price
        FROM bookings b 
        LEFT JOIN guests g ON b.guest_id = g.id 
        LEFT JOIN rooms r ON b.room_id = r.id 
        WHERE b.id = ?
    ", [$booking_id], true);
    
    if ($booking) {
        $nights = getNights($booking['check_in'], $booking['check_out']);
        $total = $booking['total_amount'] ?? ($booking['room_price'] * $nights);
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>Invoice - <?= $booking['booking_id'] ?? '#' . str_pad($booking['id'], 4, '0', STR_PAD_LEFT) ?></title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
            <style>
                @media print { .no-print { display: none !important; } }
                .invoice-container { max-width: 800px; margin: 30px auto; padding: 40px; background: white; border-radius: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
                .invoice-header { border-bottom: 2px solid #2ec1ac; padding-bottom: 20px; margin-bottom: 20px; }
                .invoice-title { color: #2ec1ac; font-weight: 700; }
                .total-amount { font-size: 24px; font-weight: 700; color: #2ec1ac; }
                .btn-custom { background: #2ec1ac; border: none; color: white; padding: 8px 20px; border-radius: 8px; }
                .btn-custom:hover { background: #1a9b8a; color: white; }
            </style>
        </head>
        <body class="bg-light py-5">
            <div class="invoice-container">
                <div class="invoice-header d-flex justify-content-between align-items-start">
                    <div>
                        <h2 class="invoice-title">CITY STAY HOTEL</h2>
                        <p class="text-muted mb-0">123 Luxury Avenue, Downtown</p>
                        <p class="text-muted">Phone: +91 7778889991</p>
                    </div>
                    <div class="text-end">
                        <h4>INVOICE</h4>
                        <p class="mb-0"><strong>#<?= $booking['booking_id'] ?? 'INV-' . str_pad($booking['id'], 5, '0', STR_PAD_LEFT) ?></strong></p>
                        <p class="text-muted">Date: <?= date('d M Y') ?></p>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-6">
                        <h6>Bill To:</h6>
                        <p class="mb-1"><strong><?= htmlspecialchars($booking['guest_name'] ?? 'Walk-in') ?></strong></p>
                        <p class="mb-1"><?= htmlspecialchars($booking['email'] ?? '') ?></p>
                        <p class="mb-0"><?= htmlspecialchars($booking['phone'] ?? '') ?></p>
                    </div>
                    <div class="col-6 text-end">
                        <h6>Booking Details:</h6>
                        <p class="mb-1">Room: <strong><?= htmlspecialchars($booking['room_number'] ?? 'N/A') ?></strong></p>
                        <p class="mb-1">Type: <?= htmlspecialchars($booking['room_type'] ?? '') ?></p>
                        <p class="mb-0">Nights: <strong><?= $nights ?></strong></p>
                    </div>
                </div>

                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr><th>Description</th><th class="text-center">Qty</th><th class="text-center">Rate</th><th class="text-end">Amount</th></tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Room <?= htmlspecialchars($booking['room_number'] ?? '') ?> - <?= htmlspecialchars($booking['room_type'] ?? '') ?><br><small class="text-muted">Check-in: <?= date('d M Y', strtotime($booking['check_in'])) ?> | Check-out: <?= date('d M Y', strtotime($booking['check_out'])) ?></small></td>
                            <td class="text-center"><?= $nights ?></td>
                            <td class="text-center"><?= formatCurrency($booking['room_price'] ?? 0) ?></td>
                            <td class="text-end"><?= formatCurrency($booking['room_price'] * $nights) ?></td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr><td colspan="3" class="text-end"><strong>Total Amount</strong></td><td class="text-end total-amount"><?= formatCurrency($booking['room_price'] * $nights) ?></td></tr>
                    </tfoot>
                </table>

                <div class="row mt-4">
                    <div class="col-6"><p class="text-muted small mb-0">Thank you for choosing City Stay Hotel!</p></div>
                    <div class="col-6 text-end">
                        <button onclick="window.print()" class="btn btn-custom no-print"><i class="bi bi-printer"></i> Print</button>
                        <a href="billing.php" class="btn btn-secondary no-print">Back</a>
                    </div>
                </div>
            </div>
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        </body>
        </html>
        <?php
        exit;
    }
}

// Fetch Completed Bookings for Billing
$billing_records = db_select("
    SELECT b.*, g.name as guest_name, g.email, g.phone,
           r.room_number, r.room_type, r.price as room_price
    FROM bookings b 
    LEFT JOIN guests g ON b.guest_id = g.id 
    LEFT JOIN rooms r ON b.room_id = r.id 
    WHERE b.status = 'checked_out' 
    ORDER BY b.created_at DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Billing & Invoices - <?= SITE_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Poppins', sans-serif; }
        .main-content { margin-left: 260px; padding: 20px; padding-top: 80px; }
        .card-custom { border: none; border-radius: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .btn-custom { background: #2ec1ac; border: none; color: white; border-radius: 10px; padding: 8px 20px; }
        .btn-custom:hover { background: #1a9b8a; color: white; }
        .invoice-id { font-weight: 600; color: #2ec1ac; }
        @media (max-width: 768px) { .main-content { margin-left: 0; padding: 15px; padding-top: 70px; } }
    </style>
</head>
<body>
<?php include 'sidebar.php'; ?>
<div class="main-content">
    <div class="container-fluid">
        <h4 class="fw-bold mb-4">Billing & Invoices</h4>
        <div class="card card-custom">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr><th>Invoice ID</th><th>Guest</th><th>Room</th><th>Check-in</th><th>Check-out</th><th>Nights</th><th>Total</th><th>Action</th></tr>
                        </thead>
                        <tbody>
                            <?php if (empty($billing_records)): ?>
                                <tr><td colspan="8" class="text-center py-4 text-muted">No completed stays for billing</td></tr>
                            <?php else: ?>
                                <?php foreach ($billing_records as $bill): 
                                    $nights = getNights($bill['check_in'], $bill['check_out']);
                                    $total = $bill['total_amount'] ?? ($bill['room_price'] * $nights);
                                ?>
                                <tr>
                                    <td><span class="invoice-id">#<?= $bill['booking_id'] ?? 'INV-' . str_pad($bill['id'], 5, '0', STR_PAD_LEFT) ?></span></td>
                                    <td><strong><?= htmlspecialchars($bill['guest_name'] ?? 'Walk-in') ?></strong></td>
                                    <td><?= htmlspecialchars($bill['room_number'] ?? 'N/A') ?></td>
                                    <td><?= date('d M Y', strtotime($bill['check_in'])) ?></td>
                                    <td><?= date('d M Y', strtotime($bill['check_out'])) ?></td>
                                    <td><?= $nights ?></td>
                                    <td class="fw-bold"><?= formatCurrency($total) ?></td>
                                    <td><a href="?invoice=<?= $bill['id'] ?>" class="btn btn-sm btn-custom" target="_blank"><i class="bi bi-download me-1"></i> Invoice</a></td>
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