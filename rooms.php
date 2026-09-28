<?php
require_once(__DIR__ . '/../inc/db_config.php');

// Fetch all rooms
$rooms = db_select("SELECT * FROM rooms ORDER BY room_number ASC");

// Get room types for filter
$room_types = db_select("SELECT DISTINCT room_type FROM rooms ORDER BY room_type ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Our Rooms - <?= SITE_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <?php require_once(__DIR__ . '/../inc/links.php'); ?>
    <style>
        .page-header {
            background: linear-gradient(135deg, #2ec1ac 0%, #1a9b8a 100%);
            color: white;
            padding: 60px 0;
            margin-bottom: 50px;
        }
        .room-card {
            border: none;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 2px 15px rgba(0,0,0,0.06);
            transition: all 0.3s ease;
            height: 100%;
            background: white;
        }
        .room-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 40px rgba(0,0,0,0.1);
        }
        .room-card .card-img-top {
            height: 240px;
            object-fit: cover;
        }
        .room-card .card-body {
            padding: 20px;
        }
        .room-card .room-price {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--teal);
        }
        .room-card .room-price small {
            font-size: 14px;
            font-weight: 400;
            color: #636e72;
        }
        .room-card .amenity-tag {
            background: #f0f0f0;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            margin: 2px;
            display: inline-block;
        }
        .room-card .status-badge {
            padding: 4px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }
        .filter-sidebar {
            position: sticky;
            top: 80px;
        }
        .filter-sidebar .card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.06);
        }
        @media (max-width: 768px) {
            .filter-sidebar {
                position: relative;
                top: 0;
                margin-bottom: 20px;
            }
        }
    </style>
</head>
<body class="bg-light">
    <?php require_once(__DIR__ . '/../inc/header.php'); ?>

    <!-- Page Header -->
    <div class="page-header text-center">
        <div class="container">
            <h1 class="fw-bold mb-2">Our Rooms</h1>
            <p class="lead mb-0">Comfortable and luxurious accommodations for every traveler</p>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <!-- Filter Sidebar -->
            <div class="col-lg-3 col-md-12 mb-4">
                <div class="filter-sidebar">
                    <div class="card p-3">
                        <h5 class="fw-bold mb-3"><i class="bi bi-funnel me-2"></i>Filters</h5>
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Check Availability</label>
                            <input type="date" class="form-control shadow-none mb-2" placeholder="Check-in">
                            <input type="date" class="form-control shadow-none" placeholder="Check-out">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Room Type</label>
                            <select class="form-select shadow-none">
                                <option value="">All Types</option>
                                <?php foreach ($room_types as $type): ?>
                                    <option value="<?= htmlspecialchars($type['room_type']) ?>">
                                        <?= htmlspecialchars($type['room_type']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Price Range</label>
                            <div class="d-flex gap-2">
                                <input type="number" class="form-control shadow-none" placeholder="Min" value="0">
                                <input type="number" class="form-control shadow-none" placeholder="Max" value="10000">
                            </div>
                        </div>
                        
                        <button class="btn custom-bg w-100">Apply Filters</button>
                    </div>
                </div>
            </div>

            <!-- Room List -->
            <div class="col-lg-9 col-md-12">
                <div class="row g-4">
                    <?php if (empty($rooms)): ?>
                        <div class="col-12">
                            <div class="text-center py-5">
                                <i class="bi bi-door-open fs-1 text-muted"></i>
                                <h4 class="mt-3">No rooms available</h4>
                                <p class="text-muted">Please check back later for availability.</p>
                            </div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($rooms as $room): 
                            $status_badge = [
                                'available' => 'success',
                                'occupied' => 'warning',
                                'maintenance' => 'danger',
                                'reserved' => 'info'
                            ][$room['status']] ?? 'secondary';
                            $amenities = explode(',', $room['amenities'] ?? '');
                        ?>
                        <div class="col-lg-6 col-md-6">
                            <div class="room-card">
                                <img src="../images/rooms/<?= htmlspecialchars($room['image'] ?? '1.jpg') ?>" 
                                     class="card-img-top" alt="<?= htmlspecialchars($room['room_type']) ?>">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h5 class="fw-bold mb-0"><?= htmlspecialchars($room['room_type']) ?></h5>
                                        <span class="status-badge bg-<?= $status_badge ?> text-white">
                                            <?= ucfirst($room['status']) ?>
                                        </span>
                                    </div>
                                    <div class="room-price mb-2">
                                        <?= formatCurrency($room['price']) ?> <small>per night</small>
                                    </div>
                                    <p class="text-muted small mb-2">
                                        <i class="bi bi-people me-1"></i> <?= $room['capacity'] ?> guests
                                    </p>
                                    <div class="mb-3">
                                        <?php foreach ($amenities as $amenity): ?>
                                            <?php if (trim($amenity)): ?>
                                                <span class="amenity-tag"><?= trim($amenity) ?></span>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <a href="booking.php?room_id=<?= $room['id'] ?>" class="btn custom-bg btn-sm flex-grow-1">
                                            <i class="bi bi-calendar-check me-1"></i> Book Now
                                        </a>
                                        <a href="room-details.php?id=<?= $room['id'] ?>" class="btn btn-outline-dark btn-sm">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php require_once(__DIR__ . '/../inc/footer.php'); ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>