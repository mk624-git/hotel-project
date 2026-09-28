<?php
session_start();
$base = $_SERVER['DOCUMENT_ROOT'] . '/citystay/';
require_once($base . 'admin/inc/db_config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $room_id = $_POST['room_id'];
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $check_in = $_POST['check_in'];
    $check_out = $_POST['check_out'];
    $adults = $_POST['adults'];
    $children = $_POST['children'];
    $price = $_POST['price'];
    
    // Calculate total price
    $days = (strtotime($check_out) - strtotime($check_in)) / (60 * 60 * 24);
    $total_price = $price * $days;
    
    // Check if room is available for the selected dates
    $check_query = "SELECT * FROM bookings WHERE room_id = ? AND status IN ('pending', 'confirmed') 
                    AND ((check_in BETWEEN ? AND ?) OR (check_out BETWEEN ? AND ?) 
                    OR (? BETWEEN check_in AND check_out) OR (? BETWEEN check_in AND check_out))";
    
    $existing_bookings = db_select($check_query, [$room_id, $check_in, $check_out, $check_in, $check_out, $check_in, $check_out]);
    
    if (!empty($existing_bookings)) {
        // Room is already booked for these dates
        $_SESSION['error'] = "Sorry, this room is not available for the selected dates. Please choose different dates.";
        header("Location: rooms.php?" . http_build_query($_POST));
        exit;
    }
    
    // Insert booking
    $query = "INSERT INTO bookings (room_id, full_name, email, phone, check_in, check_out, adults, children, total_price, status, booking_date) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())";
    
    $result = db_insert($query, [$room_id, $full_name, $email, $phone, $check_in, $check_out, $adults, $children, $total_price]);
    
    if ($result) {
        // Send confirmation email (optional - implement if you have mail server)
        $to = $email;
        $subject = "Booking Confirmation - City Stay Hotel";
        $message = "Dear $full_name,\n\n";
        $message .= "Thank you for booking with City Stay Hotel!\n\n";
        $message .= "Booking Details:\n";
        $message .= "Booking ID: #$result\n";
        $message .= "Room ID: $room_id\n";
        $message .= "Check-in: $check_in\n";
        $message .= "Check-out: $check_out\n";
        $message .= "Total Price: ₹" . number_format($total_price, 2) . "\n\n";
        $message .= "We will contact you shortly to confirm your booking.\n\n";
        $message .= "Thank you,\nCity Stay Hotel Team";
        
        // Uncomment if you have mail configured
        // mail($to, $subject, $message);
        
        $_SESSION['success'] = "Booking successful! Your booking ID is #$result. We will contact you shortly.";
        header("Location: booking_confirmation.php?id=$result");
    } else {
        $_SESSION['error'] = "Booking failed. Please try again or contact support.";
        header("Location: rooms.php?" . http_build_query($_POST));
    }
} else {
    header("Location: rooms.php");
    exit;
}
?>