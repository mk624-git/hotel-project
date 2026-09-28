<?php
session_start();
require_once('inc/db_config.php');

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

// Handle Add Staff
if (isset($_POST['add_staff'])) {
    $data = [
        'name' => sanitize($_POST['name']),
        'role' => sanitize($_POST['role']),
        'email' => sanitize($_POST['email']),
        'phone' => sanitize($_POST['phone']),
        'salary' => $_POST['salary'],
        'status' => $_POST['status'] ?? 'active'
    ];
    
    $result = db_insert('staff', $data);
    if ($result) {
        $_SESSION['success'] = "Staff member added successfully!";
    } else {
        $_SESSION['error'] = "Failed to add staff member.";
    }
    header("Location: staff.php");
    exit;
}

// Handle Edit Staff
if (isset($_POST['edit_staff'])) {
    $id = $_POST['staff_id'];
    $data = [
        'name' => sanitize($_POST['name']),
        'role' => sanitize($_POST['role']),
        'email' => sanitize($_POST['email']),
        'phone' => sanitize($_POST['phone']),
        'salary' => $_POST['salary'],
        'status' => $_POST['status']
    ];
    
    $result = db_update('staff', $data, 'id = ?', [$id]);
    if ($result !== false) {
        $_SESSION['success'] = "Staff member updated successfully!";
    } else {
        $_SESSION['error'] = "Failed to update staff member.";
    }
    header("Location: staff.php");
    exit;
}

// Handle Delete Staff
if (isset($_GET['delete'])) {
    db_delete('staff', 'id = ?', [$_GET['delete']]);
    $_SESSION['success'] = "Staff member deleted successfully!";
    header("Location: staff.php");
    exit;
}

// Fetch All Staff
$staff = db_select("SELECT * FROM staff ORDER BY created_at DESC");

// Fetch staff for editing
$edit_staff = null;
if (isset($_GET['edit'])) {
    $edit_staff = db_select("SELECT * FROM staff WHERE id = ?", [$_GET['edit']], true);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Management - <?php echo SITE_NAME; ?></title>
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
        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
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
                <h4 class="fw-bold mb-0">Staff Management</h4>
                <p class="text-muted mb-0">Manage all hotel staff members</p>
            </div>
            <button class="btn btn-custom" data-bs-toggle="modal" data-bs-target="#addStaffModal">
                <i class="bi bi-plus-circle me-2"></i> Add New Staff
            </button>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="bi bi-check-circle-fill me-2"></i> <?= $_SESSION['success'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="bi bi-exclamation-circle-fill me-2"></i> <?= $_SESSION['error'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <div class="card card-custom">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Role</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Salary</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($staff)): ?>
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">
                                        <i class="bi bi-person-badge fs-1 d-block mb-2"></i>
                                        No staff members found
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($staff as $member): ?>
                                <tr>
                                    <td>#<?= str_pad($member['id'], 4, '0', STR_PAD_LEFT) ?></td>
                                    <td><strong><?= htmlspecialchars($member['name']) ?></strong></td>
                                    <td><?= htmlspecialchars($member['role']) ?></td>
                                    <td><?= htmlspecialchars($member['email'] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($member['phone'] ?? 'N/A') ?></td>
                                    <td><?= formatCurrency($member['salary'] ?? 0) ?></td>
                                    <td>
                                        <span class="status-badge bg-<?= ($member['status'] ?? 'active') == 'active' ? 'success' : 'danger' ?> text-white">
                                            <?= ucfirst($member['status'] ?? 'active') ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="?edit=<?= $member['id'] ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="?delete=<?= $member['id'] ?>" 
                                           class="btn btn-sm btn-outline-danger"
                                           onclick="return confirm('Delete this staff member?')">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
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

<!-- Add Staff Modal -->
<div class="modal fade" id="addStaffModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-person-plus me-2 text-success"></i>Add New Staff</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Role <span class="text-danger">*</span></label>
                        <input type="text" name="role" class="form-control" required placeholder="e.g. Receptionist, Manager">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email</label>
                        <input type="email" name="email" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Phone</label>
                        <input type="text" name="phone" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Monthly Salary <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">₹</span>
                            <input type="number" name="salary" class="form-control" required min="0">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Status</label>
                        <select name="status" class="form-select">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="add_staff" class="btn btn-custom">Add Staff</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Staff Modal -->
<?php if ($edit_staff): ?>
<div class="modal fade show" id="editStaffModal" tabindex="-1" style="display: block; background: rgba(0,0,0,0.5);">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="staff_id" value="<?= $edit_staff['id'] ?>">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-pencil-square me-2 text-warning"></i>Edit Staff</h5>
                    <a href="staff.php" class="btn-close"></a>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($edit_staff['name']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Role <span class="text-danger">*</span></label>
                        <input type="text" name="role" class="form-control" value="<?= htmlspecialchars($edit_staff['role']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email</label>
                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($edit_staff['email']) ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Phone</label>
                        <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($edit_staff['phone']) ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Monthly Salary <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">₹</span>
                            <input type="number" name="salary" class="form-control" value="<?= $edit_staff['salary'] ?>" required min="0">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Status</label>
                        <select name="status" class="form-select">
                            <option value="active" <?= ($edit_staff['status'] ?? 'active') == 'active' ? 'selected' : '' ?>>Active</option>
                            <option value="inactive" <?= ($edit_staff['status'] ?? 'active') == 'inactive' ? 'selected' : '' ?>>Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="staff.php" class="btn btn-secondary">Cancel</a>
                    <button type="submit" name="edit_staff" class="btn btn-custom">Update Staff</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>