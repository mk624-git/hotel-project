<?php
// user/api/bookings.php - Bookings API endpoints
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Include database configuration
require_once('../inc/db_config.php');

// Get request method and path
$method = $_SERVER['REQUEST_METHOD'];
$path = $_SERVER['PATH_INFO'] ?? '/';
$path = trim($path, '/');

// Route the request
switch ($method) {
    case 'GET':
        if ($path === 'my-bookings') {
            handleGetMyBookings();
        } elseif (is_numeric($path)) {
            handleGetBooking($path);
        } else {
            handleGetAllBookings();
        }
        break;
    case 'POST':
        handleCreateBooking();
        break;
    case 'PUT':
        if (is_numeric($path)) {
            handleUpdateBooking($path);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Booking ID required']);
        }
        break;
    case 'DELETE':
        if (is_numeric($path)) {
            handleCancelBooking($path);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Booking ID required']);
        }
        break;
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
}

function handleGetAllBookings() {
    // Check if admin (you can add admin authentication here)
    session_start();
    
    $conn = getDBConnection();
    
    // Get pagination parameters
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
    $offset = ($page - 1) * $limit;
    
    // Get status filter
    $status = isset($_GET['status']) ? $_GET['status'] : '';
    
    // Build query
    $sql = "SELECT b.*, r.name as room_name, r.price, u.name as customer_name, u.email, u.phone 
            FROM bookings b
            JOIN rooms r ON b.room_id = r.srno
            JOIN users u ON b.user_id = u.id";
    
    if ($status) {
        $sql .= " WHERE b.status = ?";
        $params = [$status];
    } else {
        $params = [];
    }
    
    $sql .= " ORDER BY b.created_at DESC LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;
    
    $stmt = $conn->prepare($sql);
    if (!empty($params)) {
        $types = str_repeat('s', count($params) - 2) . 'ii';
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $bookings = $result->fetch_all(MYSQLI_ASSOC);
    
    // Get total count
    $countSql = "SELECT COUNT(*) as total FROM bookings";
    if ($status) {
        $countSql .= " WHERE status = ?";
        $countStmt = $conn->prepare($countSql);
        $countStmt->bind_param("s", $status);
    } else {
        $countStmt = $conn->prepare($countSql);
    }
    $countStmt->execute();
    $countResult = $countStmt->get_result();
    $total = $countResult->fetch_assoc()['total'];
    
    echo json_encode([
        'success' => true,
        'data' => $bookings,
        'pagination' => [
            'current_page' => $page,
            'per_page' => $limit,
            'total' => $total,
            'total_pages' => ceil($total / $limit)
        ]
    ]);
}

function handleGetMyBookings() {
    session_start();
    
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Please login to view your bookings']);
        return;
    }
    
    $userId = $_SESSION['user_id'];
    $conn = getDBConnection();
    
    $sql = "SELECT b.*, r.name as room_name, r.price, r.image,
            DATEDIFF(b.check_out, b.check_in) as nights,
            (DATEDIFF(b.check_out, b.check_in) * r.price) as total_price
            FROM bookings b
            JOIN rooms r ON b.room_id = r.srno
            WHERE b.user_id = ?
            ORDER BY b.created_at DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $bookings = $result->fetch_all(MYSQLI_ASSOC);
    
    echo json_encode([
        'success' => true,
        'data' => $bookings
    ]);
}

function handleGetBooking($id) {
    session_start();
    
    $conn = getDBConnection();
    
    $sql = "SELECT b.*, r.name as room_name, r.price, r.description, r.image,
            u.name as customer_name, u.email, u.phone, u.address
            FROM bookings b
            JOIN rooms r ON b.room_id = r.srno
            JOIN users u ON b.user_id = u.id
            WHERE b.id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $booking = $result->fetch_assoc();
    
    if (!$booking) {
        http_response_code(404);
        echo json_encode(['error' => 'Booking not found']);
        return;
    }
    
    // Check if user owns this booking or is admin
    if (!isset($_SESSION['user_id']) || ($booking['user_id'] != $_SESSION['user_id'] && !isset($_SESSION['is_admin']))) {
        http_response_code(403);
        echo json_encode(['error' => 'Access denied']);
        return;
    }
    
    echo json_encode(['success' => true, 'data' => $booking]);
}

function handleCreateBooking() {
    session_start();
    
    // Get input data
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        // Try form data
        $roomId = $_POST['room_id'] ?? null;
        $checkIn = $_POST['check_in'] ?? null;
        $checkOut = $_POST['check_out'] ?? null;
        $adults = $_POST['adults'] ?? 1;
        $children = $_POST['children'] ?? 0;
        $specialRequests = $_POST['special_requests'] ?? '';
        $fullName = $_POST['full_name'] ?? null;
        $email = $_POST['email'] ?? null;
        $phone = $_POST['phone'] ?? null;
    } else {
        $roomId = $input['room_id'] ?? null;
        $checkIn = $input['check_in'] ?? null;
        $checkOut = $input['check_out'] ?? null;
        $adults = $input['adults'] ?? 1;
        $children = $input['children'] ?? 0;
        $specialRequests = $input['special_requests'] ?? '';
        $fullName = $input['full_name'] ?? null;
        $email = $input['email'] ?? null;
        $phone = $input['phone'] ?? null;
    }
    
    // Validate input
    $errors = [];
    if (!$roomId) $errors[] = 'Room ID is required';
    if (!$checkIn) $errors[] = 'Check-in date is required';
    if (!$checkOut) $errors[] = 'Check-out date is required';
    if (!$fullName) $errors[] = 'Full name is required';
    if (!$email) $errors[] = 'Email is required';
    if (!$phone) $errors[] = 'Phone number is required';
    
    if (!empty($errors)) {
        http_response_code(400);
        echo json_encode(['error' => implode(', ', $errors)]);
        return;
    }
    
    // Validate dates
    $checkInDate = new DateTime($checkIn);
    $checkOutDate = new DateTime($checkOut);
    $today = new DateTime();
    
    if ($checkInDate < $today) {
        http_response_code(400);
        echo json_encode(['error' => 'Check-in date cannot be in the past']);
        return;
    }
    
    if ($checkOutDate <= $checkInDate) {
        http_response_code(400);
        echo json_encode(['error' => 'Check-out must be after check-in']);
        return;
    }
    
    $nights = $checkInDate->diff($checkOutDate)->days;
    if ($nights > 30) {
        http_response_code(400);
        echo json_encode(['error' => 'Maximum booking duration is 30 nights']);
        return;
    }
    
    $conn = getDBConnection();
    
    // Get room details
    $sql = "SELECT price, adults, children FROM rooms WHERE srno = ? AND status = 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $roomId);
    $stmt->execute();
    $result = $stmt->get_result();
    $room = $result->fetch_assoc();
    
    if (!$room) {
        http_response_code(404);
        echo json_encode(['error' => 'Room not found']);
        return;
    }
    
    // Check capacity
    if ($adults > $room['adults']) {
        http_response_code(400);
        echo json_encode(['error' => 'Maximum adults capacity is ' . $room['adults']]);
        return;
    }
    
    if ($children > $room['children']) {
        http_response_code(400);
        echo json_encode(['error' => 'Maximum children capacity is ' . $room['children']]);
        return;
    }
    
    // Check if room is available for these dates
    $sql = "SELECT id FROM bookings 
            WHERE room_id = ? 
            AND status IN ('confirmed', 'pending')
            AND ((check_in <= ? AND check_out >= ?) 
                 OR (check_in BETWEEN ? AND ?)
                 OR (check_out BETWEEN ? AND ?))";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issssss", $roomId, $checkOut, $checkIn, $checkIn, $checkOut, $checkIn, $checkOut);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        http_response_code(409);
        echo json_encode(['error' => 'Room is not available for selected dates']);
        return;
    }
    
    // Get or create user
    $userId = null;
    
    if (isset($_SESSION['user_id'])) {
        $userId = $_SESSION['user_id'];
    } else {
        // Check if user exists by email
        $sql = "SELECT id FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            $userId = $row['id'];
        } else {
            // Create new user
            $sql = "INSERT INTO users (name, email, phone, status, created_at) VALUES (?, ?, ?, 1, NOW())";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $fullName, $email, $phone);
            $stmt->execute();
            $userId = $conn->insert_id;
        }
    }
    
    // Calculate total amount
    $totalAmount = $room['price'] * $nights;
    
    // Create booking
    $sql = "INSERT INTO bookings (user_id, room_id, check_in, check_out, adults, children, special_requests, total_amount, status, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iissiisd", $userId, $roomId, $checkIn, $checkOut, $adults, $children, $specialRequests, $totalAmount);
    
    if ($stmt->execute()) {
        $bookingId = $conn->insert_id;
        
        echo json_encode([
            'success' => true,
            'booking_id' => $bookingId,
            'message' => 'Booking created successfully',
            'data' => [
                'id' => $bookingId,
                'room_id' => $roomId,
                'check_in' => $checkIn,
                'check_out' => $checkOut,
                'nights' => $nights,
                'total_amount' => $totalAmount,
                'status' => 'pending'
            ]
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to create booking: ' . $conn->error]);
    }
}

function handleUpdateBooking($id) {
    session_start();
    
    // Check if user is admin (you can implement admin check here)
    if (!isset($_SESSION['is_admin'])) {
        http_response_code(403);
        echo json_encode(['error' => 'Admin access required']);
        return;
    }
    
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid input']);
        return;
    }
    
    $conn = getDBConnection();
    
    // Build update query
    $updates = [];
    $params = [];
    $types = '';
    
    if (isset($input['status'])) {
        $updates[] = "status = ?";
        $params[] = $input['status'];
        $types .= 's';
    }
    
    if (isset($input['special_requests'])) {
        $updates[] = "special_requests = ?";
        $params[] = $input['special_requests'];
        $types .= 's';
    }
    
    if (empty($updates)) {
        http_response_code(400);
        echo json_encode(['error' => 'No fields to update']);
        return;
    }
    
    $params[] = $id;
    $types .= 'i';
    
    $sql = "UPDATE bookings SET " . implode(', ', $updates) . " WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Booking updated successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to update booking']);
    }
}

function handleCancelBooking($id) {
    session_start();
    
    $conn = getDBConnection();
    
    // Check if user owns this booking
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Please login to cancel booking']);
        return;
    }
    
    $userId = $_SESSION['user_id'];
    
    $sql = "UPDATE bookings SET status = 'cancelled' WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $id, $userId);
    
    if ($stmt->execute() && $stmt->affected_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'Booking cancelled successfully']);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Booking not found or you do not have permission']);
    }
}
?>