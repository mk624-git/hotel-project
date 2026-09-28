<?php
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Top Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="dashboard.php">
                <i class="bi bi-building me-2"></i>City Stay Admin
            </a>
            <div class="ms-auto d-flex align-items-center">
                <span class="text-white me-3 d-none d-sm-inline">
                    <i class="bi bi-person-circle me-1"></i>
                    <?= htmlspecialchars($_SESSION['admin_name'] ?? $_SESSION['admin_username'] ?? 'Admin') ?>
                </span>
                <a href="logout.php" class="btn btn-outline-light btn-sm">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </a>
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <div class="d-flex">
        <div class="bg-dark text-white" style="width: 260px; height: 100vh; position: fixed; top: 56px; overflow-y: auto; z-index: 1000;">
            <div class="p-3">
                <ul class="nav flex-column">
                    <li class="nav-item mb-1">
                        <a href="dashboard.php" class="nav-link text-white rounded-3 <?= $current_page == 'dashboard.php' ? 'active bg-success' : '' ?>">
                            <i class="bi bi-speedometer2 me-2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item mb-1">
                        <a href="rooms.php" class="nav-link text-white rounded-3 <?= $current_page == 'rooms.php' ? 'active bg-success' : '' ?>">
                            <i class="bi bi-door-open me-2"></i> Rooms
                        </a>
                    </li>
                    <li class="nav-item mb-1">
                        <a href="booking.php" class="nav-link text-white rounded-3 <?= $current_page == 'booking.php' ? 'active bg-success' : '' ?>">
                            <i class="bi bi-calendar-check me-2"></i> Bookings
                        </a>
                    </li>
                    <li class="nav-item mb-1">
                        <a href="guests.php" class="nav-link text-white rounded-3 <?= $current_page == 'guests.php' ? 'active bg-success' : '' ?>">
                            <i class="bi bi-people me-2"></i> Guests
                        </a>
                    </li>
                    <li class="nav-item mb-1">
                        <a href="staff.php" class="nav-link text-white rounded-3 <?= $current_page == 'staff.php' ? 'active bg-success' : '' ?>">
                            <i class="bi bi-person-badge me-2"></i> Staff
                        </a>
                    </li>
                    <li class="nav-item mb-1">
                        <a href="billing.php" class="nav-link text-white rounded-3 <?= $current_page == 'billing.php' ? 'active bg-success' : '' ?>">
                            <i class="bi bi-receipt me-2"></i> Billing
                        </a>
                    </li>
                    <li class="nav-item mb-1">
                        <a href="settings.php" class="nav-link text-white rounded-3 <?= $current_page == 'settings.php' ? 'active bg-success' : '' ?>">
                            <i class="bi bi-gear me-2"></i> Settings
                        </a>
                    </li>
                </ul>
                <hr class="border-secondary">
                <ul class="nav flex-column">
                    <li class="nav-item mb-1">
                        <a href="../user/index.php" class="nav-link text-white rounded-3" target="_blank">
                            <i class="bi bi-globe me-2"></i> View Website
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <style>
        .nav-link {
            transition: all 0.3s ease;
            padding: 10px 15px;
            font-size: 14px;
        }
        .nav-link:hover:not(.active) {
            background: rgba(255,255,255,0.1);
        }
        .nav-link.active {
            background: #2ec1ac !important;
            color: white !important;
        }
        ::-webkit-scrollbar {
            width: 5px;
        }
        ::-webkit-scrollbar-track {
            background: #343a40;
        }
        ::-webkit-scrollbar-thumb {
            background: #2ec1ac;
            border-radius: 5px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #1a9b8a;
        }
    </style>
</head>
</html>