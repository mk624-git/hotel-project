<?php
require_once(__DIR__ . '/../inc/db_config.php');

$id   = (int)($_GET['id'] ?? 0);
$room = $id ? db_select("SELECT * FROM rooms WHERE id = ?", [$id], true) : null;

if (!$room) {
    redirect('rooms.php', 'Room not found.', 'error');
}

$amenities = array_filter(array_map('trim', explode(',', $room['amenities'] ?? '')));
$bookable  = $room['status'] !== 'maintenance';

// Aur rooms suggest karne ke liye
$other_rooms = db_select("SELECT * FROM rooms WHERE id != ? AND status = 'available' ORDER BY room_number ASC LIMIT 3", [$id]);

$placeholder = 'data:image/svg+xml;utf8,' . rawurlencode(
    '<svg xmlns="http://www.w3.org/2000/svg" width="800" height="500"><rect width="100%" height="100%" fill="#e9ecef"/>'
    . '<text x="50%" y="50%" fill="#adb5bd" font-family="Arial" font-size="32" text-anchor="middle">City Stay</text></svg>'
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= e($room['room_type']) ?> - Room Details - <?= SITE_NAME ?></title>
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
        .box {
            background: white;
            border-radius: 16px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.06);
            padding: 25px;
        }
        .room-img { width: 100%; height: 380px; object-fit: cover; border-radius: 16px; }
        .room-price { font-size: 2rem; font-weight: 700; color: var(--teal, #2ec1ac); }
        .room-price small { font-size: 14px; font-weight: 400; color: #636e72; }
        .amenity-tag {
            background: #e8f8f5;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 13px;
            margin: 3px;
            display: inline-block;
        }
        .mini-card { border: none; border-radius: 16px; overflow: hidden; box-shadow: 0 2px 15px rgba(0,0,0,0.06); height: 100%; }
        .mini-card img { height: 160px; object-fit: cover; }
    </style>
</head>
<body class="bg-light">
    <?php require_once(__DIR__ . '/../inc/header.php'); ?>

    <div class="page-header text-center">
        <div class="container">
            <h1 class="fw-bold mb-2"><?= e($room['room_type']) ?></h1>
            <p class="lead mb-0">Room <?= e($room['room_number']) ?></p>
        </div>
    </div>

    <div class="container mb-5">
        <?php showFlashMessage(); ?>

        <div class="row g-4">
            <div class="col-lg-7">
                <img class="room-img"
                     src="<?= !empty($room['image']) ? '../images/rooms/' . e($room['image']) : $placeholder ?>"
                     onerror="this.onerror=null;this.src='<?= $placeholder ?>'"
                     alt="<?= e($room['room_type']) ?>">
            </div>

            <div class="col-lg-5">
                <div class="box h-100">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h3 class="fw-bold mb-0"><?= e($room['room_type']) ?></h3>
                        <span class="badge bg-<?= getRoomStatusBadge($room['status']) ?>"><?= e(ucfirst($room['status'])) ?></span>
                    </div>

                    <div class="room-price mb-3"><?= formatCurrency($room['price']) ?> <small>per night</small></div>

                    <p class="mb-2"><i class="bi bi-door-open me-2"></i>Room No: <strong><?= e($room['room_number']) ?></strong></p>
                    <p class="mb-3"><i class="bi bi-people me-2"></i>Up to <strong><?= (int)$room['capacity'] ?></strong> guests</p>

                    <h6 class="fw-bold">Amenities</h6>
                    <div class="mb-4">
                        <?php if ($amenities): ?>
                            <?php foreach ($amenities as $amenity): ?>
                                <span class="amenity-tag"><?= e($amenity) ?></span>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <span class="text-muted small">No amenities listed.</span>
                        <?php endif; ?>
                    </div>

                    <?php if ($bookable): ?>
                        <a href="booking.php?room_id=<?= (int)$room['id'] ?>" class="btn custom-bg w-100 py-2">
                            <i class="bi bi-calendar-check me-1"></i> Book Now
                        </a>
                    <?php else: ?>
                        <button class="btn btn-secondary w-100 py-2" disabled>Currently unavailable</button>
                    <?php endif; ?>
                    <a href="rooms.php" class="btn btn-outline-dark w-100 mt-2">
                        <i class="bi bi-arrow-left me-1"></i> Back to all rooms
                    </a>
                </div>
            </div>
        </div>

        <?php if (!empty($other_rooms)): ?>
            <h4 class="fw-bold mt-5 mb-3">You may also like</h4>
            <div class="row g-4">
                <?php foreach ($other_rooms as $r): ?>
                    <div class="col-md-4">
                        <div class="card mini-card">
                            <img class="card-img-top"
                                 src="<?= !empty($r['image']) ? '../images/rooms/' . e($r['image']) : $placeholder ?>"
                                 onerror="this.onerror=null;this.src='<?= $placeholder ?>'"
                                 alt="<?= e($r['room_type']) ?>">
                            <div class="card-body">
                                <h6 class="fw-bold"><?= e($r['room_type']) ?></h6>
                                <p class="text-muted mb-2"><?= formatCurrency($r['price']) ?> / night</p>
                                <a href="room-details.php?id=<?= (int)$r['id'] ?>" class="btn btn-sm btn-outline-dark">View</a>
                                <a href="booking.php?room_id=<?= (int)$r['id'] ?>" class="btn btn-sm custom-bg">Book</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <?php require_once(__DIR__ . '/../inc/footer.php'); ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>