<?php
session_start();
require_once('inc/db_config.php');

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

// Fetch rooms for dropdown
$rooms = db_select("SELECT * FROM rooms WHERE status IN ('available', 'reserved') ORDER BY room_number ASC");

// Fetch guests for dropdown
$guests = db_select("SELECT * FROM guests ORDER BY name ASC");

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_booking'])) {
    $room_id = $_POST['room_id'];
    $guest_id = $_POST['guest_id'] ?: null;
    $guest_name = sanitize($_POST['guest_name'] ?? '');
    $guest_email = sanitize($_POST['guest_email'] ?? '');
    $guest_phone = sanitize($_POST['guest_phone'] ?? '');
    $check_in = $_POST['check_in'];
    $check_out = $_POST['check_out'];
    $adults = $_POST['adults'] ?? 1;
    $children = $_POST['children'] ?? 0;
    $status = $_POST['status'] ?? 'pending';
    
    // Validate dates
    if (empty($check_in) || empty($check_out)) {
        $_SESSION['error'] = "Please select check-in and check-out dates.";
        header("Location: booking_add.php");
        exit;
    }
    
    if (strtotime($check_in) >= strtotime($check_out)) {
        $_SESSION['error'] = "Check-out date must be after check-in date.";
        header("Location: booking_add.php");
        exit;
    }
    
    // Check room availability
    $available = isRoomAvailable($room_id, $check_in, $check_out);
    if (!$available) {
        $_SESSION['error'] = "Room is not available for the selected dates.";
        header("Location: booking_add.php");
        exit;
    }
    
    // Calculate total amount
    $nights = getNights($check_in, $check_out);
    $room = db_select("SELECT price FROM rooms WHERE id = ?", [$room_id], true);
    $total_amount = $room['price'] * $nights;
    
    // If guest_id is empty but guest_name is provided, create new guest
    if (empty($guest_id) && !empty($guest_name)) {
        $guest_data = [
            'name' => $guest_name,
            'email' => $guest_email,
            'phone' => $guest_phone
        ];
        $guest_id = db_insert('guests', $guest_data);
    }
    
    // Prepare booking data
    $booking_data = [
        'booking_id' => generateBookingId(),
        'room_id' => $room_id,
        'guest_id' => $guest_id,
        'guest_name' => $guest_name,
        'guest_email' => $guest_email,
        'guest_phone' => $guest_phone,
        'check_in' => $check_in,
        'check_out' => $check_out,
        'num_adults' => $adults,
        'num_children' => $children,
        'total_amount' => $total_amount,
        'status' => $status
    ];
    
    $result = db_insert('bookings', $booking_data);
    
    if ($result) {
        // Update room status if booking is confirmed
        if ($status == 'confirmed') {
            db_update('rooms', ['status' => 'reserved'], 'id = ?', [$room_id]);
        }
        $_SESSION['success'] = "Booking created successfully! Booking ID: " . $booking_data['booking_id'];
        header("Location: booking.php");  // FIXED: Changed from bookings.php to booking.php
        exit;
    } else {
        $_SESSION['error'] = "Failed to create booking. Please try again.";
        header("Location: booking_add.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Booking - <?= SITE_NAME ?></title>
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
        .btn-custom {
            background: #2ec1ac;
            border: none;
            color: white;
            border-radius: 10px;
            padding: 10px 25px;
        }
        .btn-custom:hover {
            background: #1a9b8a;
            color: white;
        }
        .form-control:focus, .form-select:focus {
            border-color: #2ec1ac;
            box-shadow: 0 0 0 3px rgba(46, 193, 172, 0.1);
        }
        .form-label {
            font-weight: 600;
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
                <h4 class="fw-bold mb-0"><i class="bi bi-plus-circle me-2 text-success"></i>New Booking</h4>
                <p class="text-muted mb-0">Create a new booking for a guest</p>
            </div>
            <!-- FIXED: Changed to booking.php (singular) -->
            <a href="booking.php" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-2"></i> Back to Bookings
            </a>
        </div>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="bi bi-exclamation-circle-fill me-2"></i> <?= $_SESSION['error'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <div class="card card-custom">
            <div class="card-body p-4">
                <form method="POST" action="">
                    <div class="row">
                        <!-- Room Selection -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Select Room <span class="text-danger">*</span></label>
                            <select name="room_id" class="form-select" required>
                                <option value="">-- Select Room --</option>
                                <?php foreach ($rooms as $room): ?>
                                    <option value="<?= $room['id'] ?>">
                                        <?= htmlspecialchars($room['room_number']) ?> - <?= htmlspecialchars($room['room_type']) ?> 
                                        (₹<?= number_format($room['price']) ?>/night)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (empty($rooms)): ?>
                                <small class="text-danger">No rooms available. <a href="rooms.php">Add a room</a> first.</small>
                            <?php endif; ?>
                        </div>

                        <!-- Guest Selection -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Select Guest</label>
                            <select name="guest_id" class="form-select" id="guestSelect">
                                <option value="">-- New Guest (fill details below) --</option>
                                <?php foreach ($guests as $guest): ?>
                                    <option value="<?= $guest['id'] ?>">
                                        <?= htmlspecialchars($guest['name']) ?> - <?= htmlspecialchars($guest['email'] ?? $guest['phone'] ?? '') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted">Select existing or fill details below for new guest</small>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Guest Details -->
                        <div class="col-md-12">
                            <h6 class="fw-bold mb-3"><i class="bi bi-person me-2"></i>Guest Details</h6>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Guest Name <span class="text-danger">*</span></label>
                            <input type="text" name="guest_name" class="form-control" id="guestName" 
                                   placeholder="Full name" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="guest_email" class="form-control" id="guestEmail" 
                                   placeholder="email@example.com">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Phone <span class="text-danger">*</span></label>
                            <input type="text" name="guest_phone" class="form-control" id="guestPhone" 
                                   placeholder="+91 9876543210" required>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Booking Dates -->
                        <div class="col-md-12">
                            <h6 class="fw-bold mb-3"><i class="bi bi-calendar me-2"></i>Booking Dates</h6>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Check-in <span class="text-danger">*</span></label>
                            <input type="date" name="check_in" class="form-control" id="checkIn" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Check-out <span class="text-danger">*</span></label>
                            <input type="date" name="check_out" class="form-control" id="checkOut" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Adults</label>
                            <select name="adults" class="form-select">
                                <option value="1">1</option>
                                <option value="2" selected>2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Children</label>
                            <select name="children" class="form-select">
                                <option value="0" selected>0</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Status -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Booking Status</label>
                            <select name="status" class="form-select">
                                <option value="pending">Pending</option>
                                <option value="confirmed" selected>Confirmed</option>
                                <option value="checked_in">Checked In</option>
                                <option value="checked_out">Checked Out</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3 d-flex align-items-end">
                            <button type="submit" name="add_booking" class="btn btn-custom w-100">
                                <i class="bi bi-save me-2"></i> Create Booking
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById('guestSelect')?.addEventListener('change', function() {
    const selected = this.options[this.selectedIndex];
    if (this.value) {
        const text = selected.text;
        const parts = text.split(' - ');
        const name = parts[0] || '';
        const contact = parts[1] || '';
        
        document.getElementById('guestName').value = name.trim();
        if (contact.includes('@')) {
            document.getElementById('guestEmail').value = contact.trim();
            document.getElementById('guestPhone').value = '';
        } else {
            document.getElementById('guestPhone').value = contact.trim();
            document.getElementById('guestEmail').value = '';
        }
    }
});

document.getElementById('checkIn')?.addEventListener('change', function() {
    const checkIn = this.value;
    const checkOut = document.getElementById('checkOut');
    if (checkIn) {
        checkOut.min = checkIn;
        const nextDay = new Date(checkIn);
        nextDay.setDate(nextDay.getDate() + 1);
        if (!checkOut.value || new Date(checkOut.value) <= new Date(checkIn)) {
            checkOut.value = nextDay.toISOString().split('T')[0];
        }
    }
});

document.addEventListener('DOMContentLoaded', function() {
    const today = new Date().toISOString().split('T')[0];
    const tomorrow = new Date(Date.now() + 86400000).toISOString().split('T')[0];
    
    const checkIn = document.getElementById('checkIn');
    const checkOut = document.getElementById('checkOut');
    
    if (checkIn) {
        checkIn.min = today;
        checkIn.value = today;
    }
    if (checkOut) {
        checkOut.min = tomorrow;
        checkOut.value = tomorrow;
    }
});
</script>
</body>
</html>