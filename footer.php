<footer class="footer">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <h5 class="fw-bold"><?= SITE_NAME ?></h5>
                <p class="text-white-50">Luxury redefined in the heart of the city. Experience world-class hospitality and unforgettable moments.</p>
                <div class="mt-3">
                    <a href="#" class="social-link"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="social-link"><i class="bi bi-twitter"></i></a>
                    <a href="#" class="social-link"><i class="bi bi-instagram"></i></a>
                    <a href="#" class="social-link"><i class="bi bi-youtube"></i></a>
                </div>
            </div>
            <div class="col-lg-2 col-md-6">
                <h5>Quick Links</h5>
                <a href="index.php">Home</a><br>
                <a href="rooms.php">Rooms</a><br>
                <a href="facilities.php">Facilities</a><br>
                <a href="about.php">About Us</a>
            </div>
            <div class="col-lg-3 col-md-6">
                <h5>Support</h5>
                <a href="contactus.php">Contact Us</a><br>
                <a href="#">Privacy Policy</a><br>
                <a href="#">Terms & Conditions</a><br>
                <a href="#">FAQ</a>
            </div>
            <div class="col-lg-3 col-md-6">
                <h5>Contact Info</h5>
                <p class="text-white-50 mb-1">
                    <i class="bi bi-geo-alt me-2"></i>123 Luxury Avenue, Downtown
                </p>
                <p class="text-white-50 mb-1">
                    <i class="bi bi-telephone me-2"></i>+91 7778889991
                </p>
                <p class="text-white-50">
                    <i class="bi bi-envelope me-2"></i>info@citystay.com
                </p>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="container">
            <p class="mb-0">&copy; <?= date('Y') ?> <?= SITE_NAME ?>. All rights reserved.</p>
        </div>
    </div>
</footer>

<style>
.footer {
    background: #1a1a2e;
    color: white;
    padding: 50px 0 0;
    margin-top: 60px;
}
.footer h5 {
    color: white;
    font-weight: 600;
    margin-bottom: 18px;
}
.footer a {
    color: rgba(255, 255, 255, 0.7);
    text-decoration: none;
    transition: all 0.3s ease;
    display: inline-block;
    margin-bottom: 6px;
}
.footer a:hover {
    color: var(--teal);
    transform: translateX(4px);
}
.footer .social-link {
    width: 38px;
    height: 38px;
    background: rgba(255, 255, 255, 0.08);
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-right: 6px;
    transition: all 0.3s ease;
    color: white;
}
.footer .social-link:hover {
    background: var(--teal);
    transform: translateY(-3px);
    color: white;
}
.footer-bottom {
    background: rgba(0, 0, 0, 0.3);
    padding: 15px 0;
    margin-top: 40px;
}
.footer-bottom p {
    color: rgba(255, 255, 255, 0.5);
    margin: 0;
    font-size: 14px;
}
</style>