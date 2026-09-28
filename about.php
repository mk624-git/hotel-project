<?php
require_once(__DIR__ . '/../inc/db_config.php');

// Fetch counts
$rooms_count = db_select("SELECT COUNT(*) as count FROM rooms", [], true)['count'] ?? 0;
$facilities_count = db_select("SELECT COUNT(*) as count FROM facilities WHERE status = 1", [], true)['count'] ?? 0;
$staff_count = db_select("SELECT COUNT(*) as count FROM staff WHERE status = 'active'", [], true)['count'] ?? 0;

// Fetch some random rooms for showcase
$featured_rooms = db_select("SELECT * FROM rooms WHERE status = 'available' LIMIT 3");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - <?= SITE_NAME ?></title>
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
        .about-image {
            border-radius: 16px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.1);
        }
        .stat-box {
            background: white;
            padding: 25px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 2px 15px rgba(0,0,0,0.06);
            transition: all 0.3s ease;
        }
        .stat-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.1);
        }
        .stat-box .number {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--teal);
        }
        .stat-box .label {
            color: #636e72;
            font-weight: 500;
        }
        .value-box {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.06);
            height: 100%;
        }
        .value-box i {
            color: var(--teal);
            font-size: 28px;
        }
        .value-box h3 {
            font-weight: 700;
            margin: 10px 0 5px;
        }
        .value-box p {
            color: #636e72;
            margin-bottom: 0;
            font-size: 14px;
        }
    </style>
</head>
<body class="bg-light">
    <?php require_once(__DIR__ . '/../inc/header.php'); ?>

    <!-- Page Header -->
    <div class="page-header text-center">
        <div class="container">
            <h1 class="fw-bold mb-2">About City Stay</h1>
            <p class="lead mb-0">Luxury redefined in the heart of the city</p>
        </div>
    </div>

    <div class="container">
        <!-- Main About Content -->
        <div class="row g-5 align-items-center mb-5">
            <div class="col-lg-6">
                <h2 class="fw-bold mb-3">Welcome to City Stay</h2>
                <p class="text-muted">City Stay is a premier hotel offering luxury accommodations with modern amenities. We pride ourselves on providing exceptional service and unforgettable experiences to every guest who walks through our doors.</p>
                <p class="text-muted">With <?= $rooms_count ?>+ luxurious rooms and <?= $facilities_count ?>+ world-class facilities, we ensure your stay is comfortable, memorable, and truly special.</p>
                <div class="row g-3 mt-3">
                    <div class="col-6">
                        <div class="value-box text-center">
                            <i class="bi bi-door-open"></i>
                            <h3><?= $rooms_count ?>+</h3>
                            <p>Luxury Rooms</p>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="value-box text-center">
                            <i class="bi bi-grid-3x3-gap-fill"></i>
                            <h3><?= $facilities_count ?>+</h3>
                            <p>World-class Facilities</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <img src="../images/about/about.jpg" class="img-fluid about-image" alt="City Stay Hotel">
            </div>
        </div>

        <!-- Stats -->
        <div class="row g-4 mb-5">
            <div class="col-md-3 col-6">
                <div class="stat-box">
                    <div class="number"><?= $rooms_count ?>+</div>
                    <div class="label">Rooms</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-box">
                    <div class="number"><?= $facilities_count ?>+</div>
                    <div class="label">Facilities</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-box">
                    <div class="number"><?= $staff_count ?>+</div>
                    <div class="label">Staff Members</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-box">
                    <div class="number">500+</div>
                    <div class="label">Happy Guests</div>
                </div>
            </div>
        </div>

        <!-- Our Rooms Showcase -->
        <?php if (!empty($featured_rooms)): ?>
        <div class="mt-5">
            <h3 class="fw-bold text-center mb-4">Our Featured Rooms</h3>
            <div class="row g-4">
                <?php foreach ($featured_rooms as $room): ?>
                <div class="col-md-4">
                    <div class="card h-100">
                        <img src="../images/rooms/<?= htmlspecialchars($room['image'] ?? '1.jpg') ?>" 
                             class="card-img-top" alt="<?= htmlspecialchars($room['room_type']) ?>">
                        <div class="card-body">
                            <h5><?= htmlspecialchars($room['room_type']) ?></h5>
                            <p class="text-muted"><?= formatCurrency($room['price']) ?> per night</p>
                            <a href="room-details.php?id=<?= $room['id'] ?>" class="btn btn-sm custom-bg">View Details</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <?php require_once(__DIR__ . '/../inc/footer.php'); ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>