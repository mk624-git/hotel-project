<?php
require_once(__DIR__ . '/../inc/db_config.php');

// Fetch facilities from database
$facilities = db_select("SELECT * FROM facilities WHERE status = 1 ORDER BY id ASC");

// If no facilities in DB, use fallback
if (empty($facilities)) {
    $facilities = [
        ['name' => 'Free WiFi', 'icon' => 'wifi.svg', 'description' => 'High-speed internet access throughout the hotel'],
        ['name' => 'Swimming Pool', 'icon' => 'pool.svg', 'description' => 'Outdoor swimming pool with sun loungers'],
        ['name' => 'Fitness Center', 'icon' => 'gym.svg', 'description' => 'Modern gym equipment for your workout'],
        ['name' => 'Parking', 'icon' => 'parking.svg', 'description' => 'Complimentary valet parking service'],
        ['name' => 'Spa', 'icon' => 'spa.svg', 'description' => 'Relaxing spa and massage services'],
        ['name' => 'Restaurant', 'icon' => 'restaurant.svg', 'description' => 'Multi-cuisine restaurant with buffet'],
        ['name' => 'Bar', 'icon' => 'bar.svg', 'description' => 'Exclusive bar with premium beverages'],
        ['name' => 'Conference Room', 'icon' => 'conference.svg', 'description' => 'Well-equipped meeting and conference rooms']
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Facilities - <?= SITE_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <?php require_once(__DIR__ . '/../inc/links.php'); ?>
    <style>
        .facility-card {
            border: none;
            border-radius: 16px;
            padding: 30px 20px;
            text-align: center;
            background: white;
            box-shadow: 0 2px 15px rgba(0,0,0,0.06);
            transition: all 0.3s ease;
            height: 100%;
        }
        .facility-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 40px rgba(0,0,0,0.1);
        }
        .facility-card .icon-wrapper {
            width: 80px;
            height: 80px;
            background: #e8f8f5;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            transition: all 0.3s ease;
        }
        .facility-card:hover .icon-wrapper {
            background: var(--teal);
        }
        .facility-card .icon-wrapper img {
            width: 40px;
            transition: all 0.3s ease;
        }
        .facility-card:hover .icon-wrapper img {
            filter: brightness(0) invert(1);
        }
        .facility-card h5 {
            font-weight: 600;
            margin-bottom: 8px;
        }
        .facility-card p {
            color: #636e72;
            font-size: 14px;
            margin-bottom: 0;
        }
        .page-header {
            background: linear-gradient(135deg, #2ec1ac 0%, #1a9b8a 100%);
            color: white;
            padding: 60px 0;
            margin-bottom: 50px;
        }
    </style>
</head>
<body class="bg-light">
    <?php require_once(__DIR__ . '/../inc/header.php'); ?>

    <!-- Page Header -->
    <div class="page-header text-center">
        <div class="container">
            <h1 class="fw-bold mb-2">Our Facilities</h1>
            <p class="lead mb-0">World-class amenities for an unforgettable stay</p>
        </div>
    </div>

    <div class="container">
        <div class="row g-4">
            <?php foreach ($facilities as $facility): ?>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="facility-card">
                        <div class="icon-wrapper">
                            <img src="../images/features/<?= htmlspecialchars($facility['icon'] ?? 'wifi.svg') ?>" 
                                 alt="<?= htmlspecialchars($facility['name']) ?>">
                        </div>
                        <h5><?= htmlspecialchars($facility['name']) ?></h5>
                        <p><?= htmlspecialchars($facility['description'] ?? '') ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <?php require_once(__DIR__ . '/../inc/footer.php'); ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>