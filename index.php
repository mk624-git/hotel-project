<?php
require_once(__DIR__ . '/../inc/db_config.php');

// Fetch data
$contactr = db_select("SELECT * FROM contactdetails WHERE id = 1", [], true);
$facilities = db_select("SELECT * FROM facilities WHERE status = 1 ORDER BY id ASC LIMIT 6");
$carousel_images = db_select("SELECT * FROM carousel WHERE status = 1 ORDER BY id ASC");
$rooms = db_select("SELECT * FROM rooms WHERE status = 'available' LIMIT 3");
$testimonials = db_select("SELECT * FROM testimonials WHERE status = 1 ORDER BY id DESC LIMIT 4");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Home - <?= SITE_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
    <?php require_once(__DIR__ . '/../inc/links.php'); ?>
    <style>
        .hero-slider {
            border-radius: 0;
            overflow: hidden;
        }
        .hero-slider .swiper-slide {
            height: 550px;
        }
        .hero-slider .swiper-slide img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .hero-slider .swiper-slide .slide-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            color: white;
            text-align: center;
        }
        .hero-slider .swiper-slide .slide-overlay h1 {
            font-size: 3.5rem;
            font-weight: 700;
            text-shadow: 0 2px 10px rgba(0,0,0,0.3);
        }
        .hero-slider .swiper-slide .slide-overlay p {
            font-size: 1.2rem;
            opacity: 0.9;
        }
        .availability-form {
            margin-top: -50px;
            position: relative;
            z-index: 2;
        }
        .room-card {
            border: none;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 2px 15px rgba(0,0,0,0.06);
            transition: all 0.3s ease;
            height: 100%;
        }
        .room-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 40px rgba(0,0,0,0.1);
        }
        .room-card .card-img-top {
            height: 200px;
            object-fit: cover;
        }
        .facility-icon {
            width: 70px;
            height: 70px;
            background: #e8f8f5;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 12px;
            transition: all 0.3s ease;
        }
        .facility-icon img {
            width: 32px;
        }
        .facility-item:hover .facility-icon {
            background: var(--teal);
        }
        .facility-item:hover .facility-icon img {
            filter: brightness(0) invert(1);
        }
        .testimonial-card {
            border: none;
            border-radius: 16px;
            padding: 25px;
            background: white;
            box-shadow: 0 2px 15px rgba(0,0,0,0.06);
            height: 100%;
        }
        .testimonial-card .stars {
            color: #f59e0b;
            margin-bottom: 10px;
        }
        .testimonial-card .quote {
            font-style: italic;
            color: #4a5568;
        }
        .testimonial-card .author {
            font-weight: 600;
            margin-top: 10px;
        }
        .section-title {
            text-align: center;
            margin-bottom: 40px;
        }
        .section-title h2 {
            font-weight: 700;
            font-size: 2.2rem;
        }
        .section-title .h-line {
            width: 60px;
            height: 3px;
            background: var(--teal);
            margin: 10px auto;
            border-radius: 10px;
        }
        .section-title p {
            color: #636e72;
            max-width: 600px;
            margin: 10px auto 0;
        }
        @media (max-width: 768px) {
            .hero-slider .swiper-slide { height: 350px; }
            .hero-slider .swiper-slide .slide-overlay h1 { font-size: 2rem; }
            .availability-form { margin-top: 20px; }
        }
    </style>
</head>
<body class="bg-light">
    <?php require_once(__DIR__ . '/../inc/header.php'); ?>

    <!-- Hero Slider -->
    <div class="hero-slider swiper mySwiper">
        <div class="swiper-wrapper">
            <?php if (!empty($carousel_images)): ?>
                <?php foreach ($carousel_images as $image): ?>
                    <div class="swiper-slide">
                        <img src="../images/carousel/<?= htmlspecialchars($image['image']) ?>" 
                             alt="<?= htmlspecialchars($image['title'] ?? '') ?>">
                        <div class="slide-overlay">
                            <h1><?= htmlspecialchars($image['title'] ?? 'Welcome to City Stay') ?></h1>
                            <p><?= htmlspecialchars($image['description'] ?? 'Luxury redefined') ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <?php for ($i = 1; $i <= 3; $i++): ?>
                    <div class="swiper-slide">
                        <img src="../images/carousel/<?= $i ?>.png" alt="Slide <?= $i ?>">
                        <div class="slide-overlay">
                            <h1>Welcome to City Stay</h1>
                            <p>Luxury redefined in the heart of the city</p>
                        </div>
                    </div>
                <?php endfor; ?>
            <?php endif; ?>
        </div>
        <div class="swiper-button-next"></div>
        <div class="swiper-button-prev"></div>
        <div class="swiper-pagination"></div>
    </div>

    <!-- Availability Form -->
    <div class="container availability-form">
        <div class="row">
            <div class="col-lg-12">
                <div class="bg-white shadow p-4 rounded-4">
                    <h5 class="fw-bold mb-3"><i class="bi bi-calendar-check me-2 text-teal"></i>Check Availability</h5>
                    <form action="rooms.php" method="GET">
                        <div class="row align-items-end g-3">
                            <div class="col-lg-3 col-md-6">
                                <label class="form-label fw-semibold">Check-in</label>
                                <input type="date" name="check_in" class="form-control shadow-none" required>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <label class="form-label fw-semibold">Check-out</label>
                                <input type="date" name="check_out" class="form-control shadow-none" required>
                            </div>
                            <div class="col-lg-2 col-md-4">
                                <label class="form-label fw-semibold">Adults</label>
                                <select name="adults" class="form-select shadow-none">
                                    <option value="1">1</option>
                                    <option value="2" selected>2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                </select>
                            </div>
                            <div class="col-lg-2 col-md-4">
                                <label class="form-label fw-semibold">Children</label>
                                <select name="children" class="form-select shadow-none">
                                    <option value="0" selected>0</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                </select>
                            </div>
                            <div class="col-lg-2 col-md-4">
                                <button type="submit" class="btn custom-bg w-100 py-2">
                                    <i class="bi bi-search me-1"></i> Search
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Rooms Section -->
    <div class="container mt-5 pt-4">
        <div class="section-title">
            <h2>Our Rooms</h2>
            <div class="h-line"></div>
            <p>Comfortable and luxurious accommodations for every traveler</p>
        </div>
        <div class="row g-4">
            <?php if (!empty($rooms)): ?>
                <?php foreach ($rooms as $room): ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="room-card">
                            <img src="../images/rooms/<?= htmlspecialchars($room['image'] ?? '1.jpg') ?>" 
                                 class="card-img-top" alt="<?= htmlspecialchars($room['room_type']) ?>">
                            <div class="card-body">
                                <h5 class="fw-bold"><?= htmlspecialchars($room['room_type']) ?></h5>
                                <p class="text-teal fw-bold"><?= formatCurrency($room['price']) ?> <small class="text-muted fw-normal">/ night</small></p>
                                <p class="text-muted small"><i class="bi bi-people me-1"></i> <?= $room['capacity'] ?> guests</p>
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
            <?php else: ?>
                <?php for ($i = 1; $i <= 3; $i++): ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="room-card">
                            <img src="../images/rooms/1.jpg" class="card-img-top" alt="Room">
                            <div class="card-body">
                                <h5 class="fw-bold"><?= ['Simple Room', 'Deluxe Room', 'Suite Room'][$i-1] ?></h5>
                                <p class="text-teal fw-bold">₹<?= [2000, 3500, 6500][$i-1] ?> <small class="text-muted fw-normal">/ night</small></p>
                                <p class="text-muted small"><i class="bi bi-people me-1"></i> <?= [2, 3, 4][$i-1] ?> guests</p>
                                <div class="d-flex gap-2">
                                    <a href="#" class="btn custom-bg btn-sm flex-grow-1">
                                        <i class="bi bi-calendar-check me-1"></i> Book Now
                                    </a>
                                    <a href="#" class="btn btn-outline-dark btn-sm">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endfor; ?>
            <?php endif; ?>
        </div>
        <div class="text-center mt-4">
            <a href="rooms.php" class="btn btn-outline-dark">View All Rooms <i class="bi bi-arrow-right ms-1"></i></a>
        </div>
    </div>

    <!-- Facilities Section -->
    <div class="container mt-5 pt-4">
        <div class="section-title">
            <h2>Our Facilities</h2>
            <div class="h-line"></div>
            <p>World-class amenities for an unforgettable stay</p>
        </div>
        <div class="row g-4 justify-content-center">
            <?php if (!empty($facilities)): ?>
                <?php foreach ($facilities as $facility): ?>
                    <div class="col-lg-2 col-md-3 col-4">
                        <div class="text-center facility-item p-3">
                            <div class="facility-icon">
                                <img src="../images/features/<?= htmlspecialchars($facility['icon'] ?? 'wifi.svg') ?>" 
                                     alt="<?= htmlspecialchars($facility['name']) ?>">
                            </div>
                            <h6 class="fw-semibold"><?= htmlspecialchars($facility['name']) ?></h6>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <?php 
                $fallback = [
                    ['icon' => 'wifi.svg', 'name' => 'Free WiFi'],
                    ['icon' => 'pool.svg', 'name' => 'Swimming Pool'],
                    ['icon' => 'gym.svg', 'name' => 'Gym'],
                    ['icon' => 'parking.svg', 'name' => 'Parking'],
                    ['icon' => 'spa.svg', 'name' => 'Spa'],
                    ['icon' => 'restaurant.svg', 'name' => 'Restaurant']
                ];
                foreach ($fallback as $item): 
                ?>
                    <div class="col-lg-2 col-md-3 col-4">
                        <div class="text-center facility-item p-3">
                            <div class="facility-icon">
                                <img src="../images/features/<?= $item['icon'] ?>" alt="<?= $item['name'] ?>">
                            </div>
                            <h6 class="fw-semibold"><?= $item['name'] ?></h6>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <div class="text-center mt-4">
            <a href="facilities.php" class="btn btn-outline-dark">View All Facilities <i class="bi bi-arrow-right ms-1"></i></a>
        </div>
    </div>

    <!-- Testimonials Section -->
    <?php if (!empty($testimonials)): ?>
    <div class="container mt-5 pt-4">
        <div class="section-title">
            <h2>What Our Guests Say</h2>
            <div class="h-line"></div>
        </div>
        <div class="row g-4">
            <?php foreach ($testimonials as $testimonial): ?>
                <div class="col-lg-3 col-md-6">
                    <div class="testimonial-card">
                        <div class="stars">
                            <?php for ($i = 1; $i <= ($testimonial['rating'] ?? 5); $i++): ?>
                                <i class="bi bi-star-fill"></i>
                            <?php endfor; ?>
                            <?php for ($i = ($testimonial['rating'] ?? 5); $i < 5; $i++): ?>
                                <i class="bi bi-star"></i>
                            <?php endfor; ?>
                        </div>
                        <p class="quote">"<?= htmlspecialchars($testimonial['message'] ?? '') ?>"</p>
                        <p class="author">- <?= htmlspecialchars($testimonial['name'] ?? 'Guest') ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Reach Us Section -->
    <div class="container mt-5 pt-4">
        <div class="section-title">
            <h2>Reach Us</h2>
            <div class="h-line"></div>
        </div>
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="bg-white rounded-4 shadow p-3">
                    <iframe class="w-100 rounded-3" height="350px" 
                            src="<?= htmlspecialchars($contactr['map_url'] ?? 'https://www.google.com/maps/embed') ?>" 
                            style="border:0;" allowfullscreen="" loading="lazy">
                    </iframe>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="bg-white rounded-4 shadow p-4 mb-3">
                    <h5 class="fw-bold"><i class="bi bi-telephone-fill text-teal me-2"></i>Call Us</h5>
                    <a href="tel:+<?= htmlspecialchars($contactr['phone1'] ?? '917778889991') ?>" class="d-block text-decoration-none text-dark">
                        +<?= htmlspecialchars($contactr['phone1'] ?? '917778889991') ?>
                    </a>
                    <?php if (!empty($contactr['phone2'])): ?>
                        <a href="tel:+<?= htmlspecialchars($contactr['phone2']) ?>" class="d-block text-decoration-none text-dark">
                            +<?= htmlspecialchars($contactr['phone2']) ?>
                        </a>
                    <?php endif; ?>
                </div>
                <div class="bg-white rounded-4 shadow p-4">
                    <h5 class="fw-bold"><i class="bi bi-envelope-fill text-teal me-2"></i>Email</h5>
                    <a href="mailto:<?= htmlspecialchars($contactr['email'] ?? 'info@citystay.com') ?>" class="text-decoration-none">
                        <?= htmlspecialchars($contactr['email'] ?? 'info@citystay.com') ?>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <?php require_once(__DIR__ . '/../inc/footer.php'); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script>
        var swiper = new Swiper(".mySwiper", {
            loop: true,
            autoplay: { delay: 4000, disableOnInteraction: false },
            speed: 800,
            navigation: { nextEl: ".swiper-button-next", prevEl: ".swiper-button-prev" },
            pagination: { el: ".swiper-pagination", clickable: true },
        });
    </script>
</body>
</html>