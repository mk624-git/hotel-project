<?php
require_once(__DIR__ . '/db_config.php');
$isLoggedIn = isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
$userName = $_SESSION['user_name'] ?? '';
$userId = $_SESSION['user_id'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- CSRF Protection -->
    <meta name="csrf-token" content="<?= bin2hex(random_bytes(32)) ?>">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-white px-lg-3 py-lg-2 shadow-sm sticky-top">
    <div class="container-fluid">
        <a class="navbar-brand me-5 fw-bold fs-3 h-font" href="index.php">
            <i class="bi bi-building me-2" style="color: #2ec1ac;"></i>CITY Stay
        </a>
        <button class="navbar-toggler shadow-none" type="button" data-bs-toggle="collapse"
            data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
            aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <?php
                $current_page = basename($_SERVER['PHP_SELF']);
                $pages = [
                    'index.php' => 'Home',
                    'rooms.php' => 'Rooms',
                    'facilities.php' => 'Facilities',
                    'contactus.php' => 'Contact Us',
                    'about.php' => 'About'
                ];
                foreach ($pages as $file => $name):
                    $active = ($current_page == $file) ? 'active' : '';
                ?>
                    <li class="nav-item">
                        <a class="nav-link <?= $active ?>" href="<?= $file ?>"><?= $name ?></a>
                    </li>
                <?php endforeach; ?>
            </ul>
            <div class="d-flex align-items-center">
                <?php if ($isLoggedIn): ?>
                    <div class="dropdown">
                        <button class="btn btn-outline-teal shadow-none dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle me-1"></i> <?= htmlspecialchars($userName) ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                            <li><a class="dropdown-item" href="my-bookings.php">
                                <i class="bi bi-calendar-check me-2"></i>My Bookings
                            </a></li>
                            <li><a class="dropdown-item" href="profile.php">
                                <i class="bi bi-person me-2"></i>Profile
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="logout.php">
                                <i class="bi bi-box-arrow-right me-2"></i>Logout
                            </a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <button type="button" class="btn btn-outline-teal shadow-none me-2" data-bs-toggle="modal" data-bs-target="#loginModal">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Login
                    </button>
                    <button type="button" class="btn btn-teal shadow-none" data-bs-toggle="modal" data-bs-target="#registerModal">
                        <i class="bi bi-person-plus me-1"></i> Register
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<style>
.btn-teal {
    background: #2ec1ac;
    color: white;
    border: 1px solid #2ec1ac;
}
.btn-teal:hover {
    background: #1a9b8a;
    border-color: #1a9b8a;
    color: white;
}
.btn-outline-teal {
    color: #2ec1ac;
    border: 1px solid #2ec1ac;
}
.btn-outline-teal:hover {
    background: #2ec1ac;
    color: white;
}
.nav-link.active {
    color: #2ec1ac !important;
    font-weight: 600;
}
.nav-link {
    font-weight: 500;
    padding: 8px 16px;
    border-radius: 8px;
    transition: all 0.3s ease;
}
.nav-link:hover:not(.active) {
    background: #e8f8f5;
    color: #2ec1ac;
}
.dropdown-item:active {
    background: #e8f8f5;
    color: #2d3436;
}
</style>

<!-- ============================================
   LOGIN MODAL
   ============================================ -->
<div class="modal fade" id="loginModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-box-arrow-in-right text-teal me-2"></i>Login
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div id="loginAlert" class="alert d-none"></div>
                <form id="loginForm" method="POST" action="auth.php">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-envelope"></i>
                            </span>
                            <input type="email" name="email" id="loginEmail" class="form-control border-start-0" 
                                   placeholder="your@email.com" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-lock"></i>
                            </span>
                            <input type="password" name="password" id="loginPassword" class="form-control border-start-0" 
                                   placeholder="Enter your password" required>
                        </div>
                    </div>
                    <button type="submit" name="login" class="btn btn-teal w-100 py-2 fw-semibold">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Login
                    </button>
                    <p class="text-center mt-3 mb-0">
                        <small class="text-muted">Don't have an account? <a href="#" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#registerModal" class="text-teal text-decoration-none">Register</a></small>
                    </p>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- ============================================
   REGISTER MODAL
   ============================================ -->
<div class="modal fade" id="registerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-person-plus text-teal me-2"></i>Create Account
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div id="registerAlert" class="alert d-none"></div>
                <form id="registerForm" method="POST" action="auth.php">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Full Name</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="bi bi-person"></i>
                                </span>
                                <input type="text" name="name" id="regName" class="form-control border-start-0" 
                                       placeholder="John Doe" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="bi bi-envelope"></i>
                                </span>
                                <input type="email" name="email" id="regEmail" class="form-control border-start-0" 
                                       placeholder="your@email.com" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Phone Number</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="bi bi-phone"></i>
                                </span>
                                <input type="tel" name="phone" id="regPhone" class="form-control border-start-0" 
                                       placeholder="+91 9876543210" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="bi bi-lock"></i>
                                </span>
                                <input type="password" name="password" id="regPassword" class="form-control border-start-0" 
                                       placeholder="Min 6 characters" required minlength="6">
                            </div>
                        </div>
                    </div>
                    <button type="submit" name="register" class="btn btn-teal w-100 py-2 fw-semibold mt-4">
                        <i class="bi bi-person-plus me-2"></i>Create Account
                    </button>
                    <p class="text-center mt-3 mb-0">
                        <small class="text-muted">Already have an account? <a href="#" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#loginModal" class="text-teal text-decoration-none">Login</a></small>
                    </p>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
// LOGIN FORM HANDLER
document.getElementById('loginForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    fetch('auth.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            const alert = document.getElementById('loginAlert');
            alert.className = 'alert alert-danger alert-dismissible fade show';
            alert.innerHTML = data.message + '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
            alert.classList.remove('d-none');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        const alert = document.getElementById('loginAlert');
        alert.className = 'alert alert-danger alert-dismissible fade show';
        alert.innerHTML = 'An error occurred. Please try again.<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
        alert.classList.remove('d-none');
    });
});

// REGISTER FORM HANDLER
document.getElementById('registerForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    fetch('auth.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            const alert = document.getElementById('registerAlert');
            alert.className = 'alert alert-danger alert-dismissible fade show';
            alert.innerHTML = data.message + '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
            alert.classList.remove('d-none');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        const alert = document.getElementById('registerAlert');
        alert.className = 'alert alert-danger alert-dismissible fade show';
        alert.innerHTML = 'An error occurred. Please try again.<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
        alert.classList.remove('d-none');
    });
});

// MODAL SWITCH - Login to Register
document.querySelectorAll('[data-bs-toggle="modal"]').forEach(btn => {
    btn.addEventListener('click', function(e) {
        const target = this.getAttribute('data-bs-target');
        const dismiss = this.getAttribute('data-bs-dismiss');
        if (dismiss) {
            const modal = document.querySelector(dismiss);
            if (modal) {
                const bsModal = bootstrap.Modal.getInstance(modal);
                if (bsModal) bsModal.hide();
            }
        }
    });
});
</script>