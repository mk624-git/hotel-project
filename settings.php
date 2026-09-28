<?php
session_start();
require_once('inc/db_config.php');

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

// Update Admin Profile
if (isset($_POST['update_profile'])) {
    $data = [
        'full_name' => sanitize($_POST['full_name']),
        'email' => sanitize($_POST['email']),
        'phone' => sanitize($_POST['phone'])
    ];
    
    if (!empty($_POST['new_password'])) {
        $data['password'] = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
    }
    
    db_update('admins', $data, 'id = ?', [$_SESSION['admin_id']]);
    $_SESSION['success'] = "Profile updated successfully!";
    header("Location: settings.php");
    exit;
}

// Get admin details
$admin = db_select("SELECT * FROM admins WHERE id = ?", [$_SESSION['admin_id']], true);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - <?php echo SITE_NAME; ?></title>
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
            padding: 8px 20px;
        }
        .btn-custom:hover {
            background: #1a9b8a;
            color: white;
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
                <h4 class="fw-bold mb-0">Settings</h4>
                <p class="text-muted mb-0">Manage your admin profile</p>
            </div>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="bi bi-check-circle-fill me-2"></i> <?= $_SESSION['success'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <div class="row">
            <div class="col-lg-8">
                <div class="card card-custom">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4">Profile Information</h5>
                        <form method="POST">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">Username</label>
                                    <input type="text" class="form-control" value="<?= htmlspecialchars($admin['username'] ?? '') ?>" disabled>
                                    <small class="text-muted">Username cannot be changed</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">Full Name</label>
                                    <input type="text" name="full_name" class="form-control" value="<?= htmlspecialchars($admin['full_name'] ?? '') ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">Email</label>
                                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($admin['email'] ?? '') ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">Phone</label>
                                    <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($admin['phone'] ?? '') ?>">
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="form-label fw-semibold">New Password</label>
                                    <input type="password" name="new_password" class="form-control" placeholder="Leave blank to keep current password">
                                </div>
                            </div>
                            <button type="submit" name="update_profile" class="btn btn-custom">
                                <i class="bi bi-save me-2"></i> Update Profile
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card card-custom">
                    <div class="card-body p-4 text-center">
                        <i class="bi bi-person-circle fs-1 text-muted"></i>
                        <h5 class="mt-2"><?= htmlspecialchars($admin['full_name'] ?? $admin['username'] ?? 'Admin') ?></h5>
                        <p class="text-muted mb-0"><?= htmlspecialchars($admin['username'] ?? '') ?></p>
                        <p class="text-muted small">Role: <?= htmlspecialchars($admin['role'] ?? 'Admin') ?></p>
                        <hr>
                        <p class="small text-muted mb-0">
                            <i class="bi bi-clock me-1"></i>
                            Member since <?= date('d M Y', strtotime($admin['created_at'] ?? 'now')) ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>