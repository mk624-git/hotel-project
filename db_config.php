<?php
define('DB_SERVER',   'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME',     'citystay');

/* ---------- Site ---------- */
define('SITE_NAME', 'City Stay Hotel');
define('SITE_URL',  'http://localhost/citystay/');
define('USER_URL',  SITE_URL . 'user/');
define('ADMIN_URL', SITE_URL . 'admin/');
define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'] . '/citystay/');

// Development me true, live server pe false kar dena
define('APP_DEBUG', true);

date_default_timezone_set('Asia/Kolkata');

error_reporting(E_ALL);
ini_set('display_errors', APP_DEBUG ? '1' : '0');
ini_set('log_errors', '1');

/* ---------- Session (sirf ek jagah se start hoga) ---------- */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* ---------- PDO Connection ---------- */
try {
    $pdo = new PDO(
        "mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USERNAME,
        DB_PASSWORD,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
    // MySQL ka time bhi India time (IST) me rakho, PHP ke timezone se match karne ke liye
    $pdo->exec("SET time_zone = '+05:30'");
} catch (PDOException $e) {
    error_log("DB Connection Failed: " . $e->getMessage());
    die(APP_DEBUG
        ? "Database Connection Failed: " . $e->getMessage()
        : "Service temporarily unavailable. Please try again later.");
}

/* ---------- Helpers ---------- */

// Table/column name safe hai ya nahi (sirf letters, numbers, underscore)
function db_ident($name) {
    if (!preg_match('/^[A-Za-z0-9_]+$/', $name)) {
        throw new InvalidArgumentException("Invalid identifier: $name");
    }
    return "`$name`";
}

// HTML escape shortcut
function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

/* ---------- Database functions ---------- */

function db_select($query, $params = [], $single = false) {
    global $pdo;
    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        return $single ? $stmt->fetch() : $stmt->fetchAll();
    } catch (Exception $e) {
        error_log("DB Select Error: " . $e->getMessage() . " | SQL: " . $query);
        if (APP_DEBUG) {
            echo '<div style="background:#fff3cd;padding:8px;margin:5px;border:1px solid #ffc107;font-size:13px">'
               . '<b>DB Error:</b> ' . e($e->getMessage()) . '</div>';
        }
        return $single ? false : [];
    }
}

function db_insert($table, $data) {
    global $pdo;
    try {
        $columns      = implode(", ", array_map('db_ident', array_keys($data)));
        $placeholders = ":" . implode(", :", array_keys($data));

        $sql  = "INSERT INTO " . db_ident($table) . " ($columns) VALUES ($placeholders)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($data);

        return $pdo->lastInsertId();
    } catch (Exception $e) {
        error_log("Insert Error: " . $e->getMessage());
        return false;
    }
}

/**
 * Use:
 *   db_update('rooms', ['price' => 3000], 'id = :id', ['id' => 5]);   // named
 *   db_update('rooms', ['price' => 3000], 'id = ?', [5]);             // positional
 */
function db_update($table, $data, $where, $where_params = []) {
    global $pdo;
    try {
        $is_positional = !empty($where_params) && array_is_list($where_params);
        $set    = [];
        $params = [];

        if ($is_positional) {
            foreach ($data as $key => $value) {
                $set[]    = db_ident($key) . " = ?";
                $params[] = $value;
            }
            $params = array_merge($params, $where_params);
        } else {
            foreach ($data as $key => $value) {
                $set[] = db_ident($key) . " = :set_$key";   // set_ prefix se naam ki takkar nahi hogi
                $params["set_$key"] = $value;
            }
            $params = array_merge($params, $where_params);
        }

        $sql  = "UPDATE " . db_ident($table) . " SET " . implode(", ", $set) . " WHERE $where";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->rowCount();
    } catch (Exception $e) {
        error_log("Update Error: " . $e->getMessage());
        return false;
    }
}

function db_delete($table, $where, $params = []) {
    global $pdo;
    try {
        $sql  = "DELETE FROM " . db_ident($table) . " WHERE $where";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    } catch (Exception $e) {
        error_log("Delete Error: " . $e->getMessage());
        return false;
    }
}

/* ---------- General functions ---------- */

function getNights($check_in, $check_out) {
    $date1 = new DateTime($check_in);
    $date2 = new DateTime($check_out);
    return $date1->diff($date2)->days;
}

function formatCurrency($amount) {
    return '₹' . number_format((float)$amount, 0);
}

function sanitize($input) {
    if (is_array($input)) {
        return array_map('sanitize', $input);
    }
    return htmlspecialchars(strip_tags(trim($input)));
}

/* ---------- User / Auth ---------- */

function isUserLoggedIn() {
    return !empty($_SESSION['user_id']);
}

function getCurrentUser() {
    if (isUserLoggedIn()) {
        return db_select("SELECT * FROM users WHERE id = ?", [$_SESSION['user_id']], true);
    }
    return null;
}

function isAdminLoggedIn() {
    return !empty($_SESSION['admin_id']);
}

/* ---------- Badges ---------- */

function getRoomStatusBadge($status) {
    $classes = [
        'available'   => 'success',
        'occupied'    => 'warning',
        'maintenance' => 'danger',
        'reserved'    => 'info',
    ];
    return $classes[$status] ?? 'secondary';
}

function getBookingStatusBadge($status) {
    $classes = [
        'pending'     => 'warning',
        'confirmed'   => 'success',
        'checked_in'  => 'primary',
        'checked_out' => 'info',
        'cancelled'   => 'danger',
    ];
    return $classes[$status] ?? 'secondary';
}

/* ---------- Booking ---------- */

// Unique booking code (bookings.booking_code column chahiye, SQL file me hai)
function generateBookingId() {
    do {
        $code  = 'BK' . date('Ymd') . random_int(1000, 9999);
        $exists = db_select("SELECT id FROM bookings WHERE booking_code = ?", [$code], true);
    } while ($exists);
    return $code;
}

// Overlap rule: naya check_in < purana check_out  AND  naya check_out > purana check_in
// (same din checkout aur checkin allowed hai)
function isRoomAvailable($room_id, $check_in, $check_out, $exclude_booking_id = null) {
    $sql = "SELECT COUNT(*) AS count FROM bookings
            WHERE room_id = ?
            AND status NOT IN ('cancelled', 'checked_out')
            AND check_in < ?
            AND check_out > ?";
    $params = [$room_id, $check_out, $check_in];

    if ($exclude_booking_id) {
        $sql .= " AND id != ?";
        $params[] = $exclude_booking_id;
    }

    $result = db_select($sql, $params, true);
    return $result && $result['count'] == 0;
}

/* ---------- Redirect & Flash ---------- */

// Ye function header() use karta hai, isliye HTML output se PEHLE hi call karna
function redirect($url, $message = '', $type = 'success') {
    if ($message) {
        $_SESSION['flash_message'] = $message;
        $_SESSION['flash_type']    = $type;
    }
    header("Location: $url");
    exit;
}

function showFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $type  = $_SESSION['flash_type'] ?? 'success';
        $class = $type === 'success' ? 'alert-success' : 'alert-danger';
        $icon  = $type === 'success' ? 'check-circle' : 'exclamation-triangle';
        echo '<div class="alert ' . $class . ' alert-dismissible fade show" role="alert">
                <i class="bi bi-' . $icon . '-fill me-2"></i>
                ' . e($_SESSION['flash_message']) . '
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
              </div>';
        unset($_SESSION['flash_message'], $_SESSION['flash_type']);
    }
}