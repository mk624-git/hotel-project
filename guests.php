<?php
session_start();
require_once('inc/db_config.php');

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

// Handle Delete Guest
if (isset($_GET['delete'])) {
    $bookings = db_select("SELECT COUNT(*) as count FROM bookings WHERE guest_id = ?", [$_GET['delete']], true);
    if ($bookings['count'] > 0) {
        $_SESSION['error'] = "Cannot delete guest with existing bookings.";
    } else {
        db_delete('guests', 'id = ?', [$_GET['delete']]);
        $_SESSION['success'] = "Guest deleted successfully!";
    }
    header("Location: guests.php");
    exit;
}

// Handle Add Guest
if (isset($_POST['add_guest'])) {
    $data = [
        'name' => sanitize($_POST['name']),
        'email' => sanitize($_POST['email'] ?? ''),
        'phone' => sanitize($_POST['phone'] ?? ''),
        'address' => sanitize($_POST['address'] ?? ''),
        'city' => sanitize($_POST['city'] ?? ''),
        'state' => sanitize($_POST['state'] ?? ''),
        'pincode' => sanitize($_POST['pincode'] ?? ''),
        'id_proof_type' => sanitize($_POST['id_proof_type'] ?? ''),
        'id_proof_number' => sanitize($_POST['id_proof_number'] ?? '')
    ];
    
    // Remove empty values to avoid SQL errors if columns don't exist
    $data = array_filter($data, function($value) {
        return $value !== '' && $value !== null;
    });
    
    $result = db_insert('guests', $data);
    if ($result) {
        $_SESSION['success'] = "Guest added successfully!";
    } else {
        $_SESSION['error'] = "Failed to add guest.";
    }
    header("Location: guests.php");
    exit;
}

// Fetch All Guests
$guests = db_select("SELECT * FROM guests ORDER BY created_at DESC");

// Debug: Check if columns exist
$firstGuest = !empty($guests) ? $guests[0] : [];
$hasIdProofType = isset($firstGuest['id_proof_type']);
$hasCity = isset($firstGuest['city']);
$hasState = isset($firstGuest['state']);
$hasPincode = isset($firstGuest['pincode']);
$hasIdProofNumber = isset($firstGuest['id_proof_number']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guests Management - <?php echo SITE_NAME; ?></title>
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
        .badge-soft {
            background: #f0f0f0;
            color: #333;
            padding: 4px 12px;
            border-radius: 20px;
            font-weight: 500;
            font-size: 12px;
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
                <h4 class="fw-bold mb-0">Guests Management</h4>
                <p class="text-muted mb-0">Manage all hotel guests</p>
            </div>
            <button class="btn btn-custom" data-bs-toggle="modal" data-bs-target="#addGuestModal">
                <i class="bi bi-plus-circle me-2"></i> Add New Guest
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
                                <th>Email</th>
                                <th>Phone</th>
                                <?php if ($hasCity): ?>
                                    <th>City</th>
                                <?php endif; ?>
                                <?php if ($hasIdProofType): ?>
                                    <th>ID Proof</th>
                                <?php endif; ?>
                                <th>Registered</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($guests)): ?>
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">
                                        <i class="bi bi-people fs-1 d-block mb-2"></i>
                                        No guests found
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($guests as $guest): ?>
                                <tr>
                                    <td>#<?= str_pad($guest['id'], 4, '0', STR_PAD_LEFT) ?></td>
                                    <td><strong><?= htmlspecialchars($guest['name'] ?? '') ?></strong></td>
                                    <td><?= htmlspecialchars($guest['email'] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($guest['phone'] ?? 'N/A') ?></td>
                                    <?php if ($hasCity): ?>
                                        <td><?= htmlspecialchars($guest['city'] ?? '') ?></td>
                                    <?php endif; ?>
                                    <?php if ($hasIdProofType): ?>
                                        <td>
                                            <?php if (!empty($guest['id_proof_type'])): ?>
                                                <span class="badge-soft">
                                                    <?= htmlspecialchars($guest['id_proof_type']) ?>
                                                    <?php if (!empty($guest['id_proof_number'])): ?>
                                                        <small>(<?= htmlspecialchars($guest['id_proof_number']) ?>)</small>
                                                    <?php endif; ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">N/A</span>
                                            <?php endif; ?>
                                        </td>
                                    <?php endif; ?>
                                    <td><?= date('d M Y', strtotime($guest['created_at'] ?? 'now')) ?></td>
                                    <td>
                                        <a href="guest_details.php?id=<?= $guest['id'] ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="?delete=<?= $guest['id'] ?>" 
                                           class="btn btn-sm btn-outline-danger"
                                           onclick="return confirm('Delete this guest?')">
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

<!-- Add Guest Modal -->
<div class="modal fade" id="addGuestModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-person-plus me-2 text-success"></i>Add New Guest</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" name="email" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Phone</label>
                            <input type="text" name="phone" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">ID Proof Type</label>
                            <select name="id_proof_type" class="form-select">
                                <option value="">Select</option>
                                <option value="Aadhar">Aadhar Card</option>
                                <option value="Passport">Passport</option>
                                <option value="Driving License">Driving License</option>
                                <option value="Voter ID">Voter ID</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">ID Proof Number</label>
                            <input type="text" name="id_proof_number" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Pincode</label>
                            <input type="text" name="pincode" class="form-control">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-semibold">Address</label>
                            <textarea name="address" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">City</label>
                            <input type="text" name="city" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">State</label>
                            <input type="text" name="state" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="add_guest" class="btn btn-custom">Add Guest</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>