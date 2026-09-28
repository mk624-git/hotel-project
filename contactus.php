<?php
require_once(__DIR__ . '/../inc/db_config.php');

// Fetch contact details directly from database
$contact = db_select("SELECT * FROM contactdetails WHERE id = 1", [], true);

// If no contact details found, use defaults
if (!$contact) {
    $contact = [
        'address' => '123 Luxury Avenue, Downtown, City - 400001',
        'phone1' => '917778889991',
        'phone2' => '918888999922',
        'email' => 'info@citystay.com',
        'map_url' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3719.9886443657786!2d72.84024807490332!3d21.19266858055627!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x39540b90d238b771%3A0x6b8d42d32e92c24c!2sSurat%2C%20Gujarat!5e0!3m2!1sen!2sin!4v1700000000000!5m2!1sen!2sin',
        'facebook' => '#',
        'twitter' => '#',
        'instagram' => '#'
    ];
}

$success_message = '';
$error_message = '';

// Handle contact form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_contact'])) {
    $name = sanitize($_POST['name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $subject = sanitize($_POST['subject'] ?? '');
    $message = sanitize($_POST['message'] ?? '');
    
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error_message = 'Please fill in all fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Please enter a valid email address.';
    } else {
        // Save to database (create a contact_messages table)
        try {
            $data = [
                'name' => $name,
                'email' => $email,
                'subject' => $subject,
                'message' => $message,
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            // Insert into database
            $result = db_insert('contact_messages', $data);
            
            if ($result) {
                $success_message = 'Thank you for contacting us! We will get back to you soon.';
                
                // Optional: Send email notification to admin
                // mail('admin@citystay.com', 'New Contact Message: ' . $subject, "From: $name\nEmail: $email\n\n$message");
            } else {
                $error_message = 'Failed to send message. Please try again later.';
            }
        } catch (Exception $e) {
            $error_message = 'An error occurred. Please try again later.';
            error_log("Contact form error: " . $e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Contact Us - <?= SITE_NAME ?></title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/simple.css">
    
    <style>
        * { font-family: 'Poppins', sans-serif; }
        .contact-header {
            background: linear-gradient(135deg, #2ec1ac 0%, #1a9b8a 100%);
            color: white;
            padding: 60px 0;
            margin-bottom: 50px;
        }
        .contact-card {
            transition: all 0.3s ease;
            border: none;
            border-radius: 15px;
            height: 100%;
        }
        .contact-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .contact-icon {
            width: 60px;
            height: 60px;
            background: #2ec1ac;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
        }
        .contact-icon i {
            font-size: 28px;
            color: white;
        }
        .form-control:focus,
        .form-select:focus {
            border-color: #2ec1ac;
            box-shadow: 0 0 0 0.2rem rgba(46, 193, 172, 0.25);
        }
        .map-container {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .social-link {
            transition: all 0.3s ease;
            display: inline-block;
        }
        .social-link:hover {
            transform: translateY(-3px);
        }
        .btn-send {
            background: #2ec1ac;
            border: none;
            color: white;
            padding: 10px 30px;
            border-radius: 50px;
            transition: all 0.3s;
        }
        .btn-send:hover {
            background: #1a9b8a;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(46, 193, 172, 0.3);
            color: white;
        }
        @media (max-width: 768px) {
            .contact-header { padding: 40px 0; }
        }
    </style>
</head>

<body class="bg-light">
    <?php require_once(__DIR__ . '/../inc/header.php'); ?>

    <!-- Header Section -->
    <div class="contact-header text-center">
        <div class="container">
            <h1 class="fw-bold mb-3">Get In Touch</h1>
            <p class="lead mb-0">We'd love to hear from you. Send us a message and we'll respond as soon as possible.</p>
        </div>
    </div>

    <div class="container">
        <!-- Contact Info Cards -->
        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="contact-card bg-white text-center p-4 shadow-sm rounded-4">
                    <div class="contact-icon">
                        <i class="bi bi-geo-alt-fill"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Visit Us</h5>
                    <p class="text-muted mb-0"><?= htmlspecialchars($contact['address'] ?? '123 Luxury Avenue, Downtown') ?></p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="contact-card bg-white text-center p-4 shadow-sm rounded-4">
                    <div class="contact-icon">
                        <i class="bi bi-telephone-fill"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Call Us</h5>
                    <p class="text-muted mb-0">
                        <a href="tel:+<?= htmlspecialchars($contact['phone1'] ?? '917778889991') ?>" class="text-decoration-none">
                            +<?= htmlspecialchars($contact['phone1'] ?? '917778889991') ?>
                        </a>
                    </p>
                    <?php if (!empty($contact['phone2'])): ?>
                        <p class="text-muted mb-0 mt-2">
                            <a href="tel:+<?= htmlspecialchars($contact['phone2']) ?>" class="text-decoration-none">
                                +<?= htmlspecialchars($contact['phone2']) ?>
                            </a>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-md-4">
                <div class="contact-card bg-white text-center p-4 shadow-sm rounded-4">
                    <div class="contact-icon">
                        <i class="bi bi-envelope-fill"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Email Us</h5>
                    <p class="text-muted mb-0">
                        <a href="mailto:<?= htmlspecialchars($contact['email'] ?? 'info@citystay.com') ?>" class="text-decoration-none">
                            <?= htmlspecialchars($contact['email'] ?? 'info@citystay.com') ?>
                        </a>
                    </p>
                </div>
            </div>
        </div>

        <div class="row g-5">
            <!-- Contact Form -->
            <div class="col-lg-6">
                <div class="bg-white rounded-4 shadow-sm p-4">
                    <h3 class="fw-bold mb-4">Send Us a Message</h3>
                    
                    <?php if ($success_message): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            <?= $success_message ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($error_message): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <?= $error_message ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="" id="contactForm">
                        <div class="mb-3">
                            <label for="name" class="form-label fw-bold">Your Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control shadow-none" id="name" name="name" placeholder="Enter your full name" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label fw-bold">Email Address <span class="text-danger">*</span></label>
                            <input type="email" class="form-control shadow-none" id="email" name="email" placeholder="Enter your email address" required>
                        </div>
                        <div class="mb-3">
                            <label for="subject" class="form-label fw-bold">Subject <span class="text-danger">*</span></label>
                            <input type="text" class="form-control shadow-none" id="subject" name="subject" placeholder="Enter subject" required>
                        </div>
                        <div class="mb-4">
                            <label for="message" class="form-label fw-bold">Message <span class="text-danger">*</span></label>
                            <textarea class="form-control shadow-none" id="message" name="message" rows="5" placeholder="Write your message here..." required></textarea>
                        </div>
                        <button type="submit" name="submit_contact" class="btn btn-send">
                            Send Message <i class="bi bi-send ms-2"></i>
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Map & Social -->
            <div class="col-lg-6">
                <div class="bg-white rounded-4 shadow-sm p-4">
                    <h3 class="fw-bold mb-4">Find Us Here</h3>
                    <div class="map-container mb-4">
                        <iframe 
                            class="w-100" 
                            height="300px" 
                            src="<?= htmlspecialchars($contact['map_url'] ?? 'https://www.google.com/maps/embed') ?>" 
                            style="border:0;" 
                            allowfullscreen="" 
                            loading="lazy">
                        </iframe>
                    </div>
                    
                    <h3 class="fw-bold mb-3">Connect With Us</h3>
                    <div class="d-flex gap-3 flex-wrap">
                        <?php if (!empty($contact['facebook'])): ?>
                            <a href="<?= htmlspecialchars($contact['facebook']) ?>" target="_blank" class="social-link">
                                <div class="contact-icon" style="width: 50px; height: 50px;">
                                    <i class="bi bi-facebook fs-4"></i>
                                </div>
                            </a>
                        <?php endif; ?>
                        
                        <?php if (!empty($contact['twitter'])): ?>
                            <a href="<?= htmlspecialchars($contact['twitter']) ?>" target="_blank" class="social-link">
                                <div class="contact-icon" style="width: 50px; height: 50px;">
                                    <i class="bi bi-twitter fs-4"></i>
                                </div>
                            </a>
                        <?php endif; ?>
                        
                        <?php if (!empty($contact['instagram'])): ?>
                            <a href="<?= htmlspecialchars($contact['instagram']) ?>" target="_blank" class="social-link">
                                <div class="contact-icon" style="width: 50px; height: 50px;">
                                    <i class="bi bi-instagram fs-4"></i>
                                </div>
                            </a>
                        <?php endif; ?>
                        
                        <a href="#" class="social-link">
                            <div class="contact-icon" style="width: 50px; height: 50px; background: #0077b5;">
                                <i class="bi bi-linkedin fs-4"></i>
                            </div>
                        </a>
                        <a href="#" class="social-link">
                            <div class="contact-icon" style="width: 50px; height: 50px; background: #ff0000;">
                                <i class="bi bi-youtube fs-4"></i>
                            </div>
                        </a>
                    </div>
                    
                    <hr class="my-4">
                    
                    <div class="business-hours">
                        <h5 class="fw-bold mb-3"><i class="bi bi-clock-history me-2"></i>Business Hours</h5>
                        <p class="mb-1"><strong>Front Desk:</strong> 24/7</p>
                        <p class="mb-1"><strong>Restaurant:</strong> 7:00 AM - 11:00 PM</p>
                        <p class="mb-1"><strong>Spa:</strong> 10:00 AM - 8:00 PM</p>
                        <p class="mb-0"><strong>Room Service:</strong> 24/7</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- FAQ Section -->
    <div class="container mt-5 mb-5">
        <h2 class="text-center fw-bold mb-4">Frequently Asked Questions</h2>
        <div class="row g-4">
            <div class="col-md-6">
                <div class="bg-white rounded-4 shadow-sm p-4 h-100">
                    <h5><i class="bi bi-question-circle-fill text-primary me-2"></i> How do I make a reservation?</h5>
                    <p class="text-muted mb-0">You can book directly through our website, call our reservation desk, or use popular booking platforms.</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="bg-white rounded-4 shadow-sm p-4 h-100">
                    <h5><i class="bi bi-question-circle-fill text-primary me-2"></i> What is your cancellation policy?</h5>
                    <p class="text-muted mb-0">Free cancellation up to 48 hours before check-in. Cancellations within 48 hours may incur charges.</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="bg-white rounded-4 shadow-sm p-4 h-100">
                    <h5><i class="bi bi-question-circle-fill text-primary me-2"></i> Do you offer airport transfers?</h5>
                    <p class="text-muted mb-0">Yes, we offer airport pickup and drop-off services. Please inform us at least 24 hours in advance.</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="bg-white rounded-4 shadow-sm p-4 h-100">
                    <h5><i class="bi bi-question-circle-fill text-primary me-2"></i> Is parking available?</h5>
                    <p class="text-muted mb-0">Yes, we have complimentary parking for all our guests.</p>
                </div>
            </div>
        </div>
    </div>

    <?php require_once(__DIR__ . '/../inc/footer.php'); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.getElementById('contactForm')?.addEventListener('submit', function(e) {
            const name = document.getElementById('name').value.trim();
            const email = document.getElementById('email').value.trim();
            const subject = document.getElementById('subject').value.trim();
            const message = document.getElementById('message').value.trim();
            
            if (!name || !email || !subject || !message) {
                e.preventDefault();
                alert('Please fill in all fields');
                return false;
            }
            
            if (!email.includes('@') || !email.includes('.')) {
                e.preventDefault();
                alert('Please enter a valid email address');
                return false;
            }
        });
    </script>
</body>
</html>