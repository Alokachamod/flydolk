<?php
// We might not query the DB, but connection is good for session/header consistency
require_once 'connection.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Simple Form Processing
$form_success = null;
$form_error = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and collect form data
    $name = filter_var(trim($_POST['name']), FILTER_SANITIZE_STRING);
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $subject = filter_var(trim($_POST['subject']), FILTER_SANITIZE_STRING);
    $message = filter_var(trim($_POST['message']), FILTER_SANITIZE_STRING);

    // Basic validation
    if (empty($name) || !filter_var($email, FILTER_VALIDATE_EMAIL) || empty($subject) || empty($message)) {
        $form_error = "Please fill out all required fields.";
    } else {
        // --- Send Email ---
        // (This uses the email from your footer.php)
        $to = "contact@flydolk.com";
        $email_subject = "New Contact Form Message: $subject";
        
        $body = "You have received a new message from the contact form:\n\n";
        $body .= "Name: $name\n";
        $body .= "Email: $email\n";
        $body .= "Subject: $subject\n\n";
        $body .= "Message:\n$message\n";
        
        $headers = "From: $email";

        // Note: mail() function's success depends on your server's sendmail configuration.
        if (mail($to, $email_subject, $body, $headers)) {
            $form_success = "Thank you! Your message has been sent. We will get back to you soon.";
        } else {
            $form_error = "There was an error sending your message. Please try again or email us directly.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - FlyDolk</title>
    
    <!-- Bootstrap CSS CDN -->
     <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" xintegrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
    <!-- Google Fonts (Inter) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;900&display=swap" rel="stylesheet">
    
    <!-- Font Awesome (for icons) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>

    <!-- Custom CSS (Consistent Dark Theme) -->
    <style>
        /* --- Base & Dark Theme --- */
        body {
            font-family: 'Inter', sans-serif;
            background-color: #0f172a; /* bg-slate-900 */
            color: #f1f5f9; /* text-gray-100 */
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;

            /* --- FIX: Add padding to offset fixed-top header --- */
            /* Mobile header is taller due to two rows (brand + search) */
            padding-top: 140px;
        }

        /* Adjust padding for the shorter desktop header */
        @media (min-width: 992px) { /* lg breakpoint */
            body {
                padding-top: 90px;
            }
        }

        /* --- Component Styling --- */
        .bg-slate-800 {
            background-color: #1e293b;
        }
        .bg-slate-950 {
            background-color: #020617;
        }
        .text-blue-400 {
            color: #60a5fa;
        }
        .section-py {
            padding-top: 5rem;
            padding-bottom: 5rem;
        }
        
        /* Page Header */
        .contact-header {
            padding: 4rem 0;
            background-color: #020617; /* bg-slate-950 */
            text-align: center;
        }

        /* Dark Form */
        .form-control-dark, .form-select-dark {
            background-color: #334155; /* slate-700 */
            color: #f1f5f9;
            border: 1px solid #475569; /* slate-600 */
        }
        .form-control-dark:focus, .form-select-dark:focus {
            background-color: #334155;
            color: #f1f5f9;
            border-color: #60a5fa; /* text-blue-400 */
            box-shadow: 0 0 0 0.25rem rgba(96, 165, 250, 0.25);
        }
        .form-control-dark::placeholder {
            color: #94a3b8; /* slate-400 */
        }

        /* Contact Info Card */
        .contact-info-card {
            background-color: #1e293b; /* bg-slate-800 */
            border-radius: 0.5rem;
            padding: 2rem;
            height: 100%;
        }
        .contact-info-list {
            list-style: none;
            padding-left: 0;
        }
        .contact-info-list li {
            display: flex;
            align-items: flex-start;
            margin-bottom: 1.25rem;
        }
        .contact-info-list .icon {
            font-size: 1.25rem;
            color: #60a5fa; /* text-blue-400 */
            margin-right: 1rem;
            width: 20px;
            text-align: center;
        }
        .contact-info-list a {
            color: #f1f5f9;
            text-decoration: none;
            transition: color 0.2s ease;
        }
        .contact-info-list a:hover {
            color: #60a5fa; /* text-blue-400 */
        }

        /* Social Icons */
        .social-icons a {
            color: #94a3b8; /* slate-400 */
            font-size: 1.5rem;
            margin-right: 1.25rem;
            transition: all 0.2s ease;
        }
        .social-icons a:hover {
            color: #f1f5f9;
            transform: scale(1.1);
        }

        /* Map Embed */
        .map-container {
            position: relative;
            overflow: hidden;
            padding-top: 40%; /* 16:9 aspect ratio */
            height: 0;
        }
        .map-container iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: 0;
            filter: grayscale(1) contrast(1.2) opacity(0.8); /* Dark mode map */
        }
        
    </style>
</head>
<body class="bg-dark text-light">

    <?php include 'header.php'; ?>

    <!-- Main Content --><main>

        <!-- Contact Header -->
        <section class="contact-header">
            <div class="container">
                <h1 class="display-4 fw-bolder text-white">Get In Touch</h1>
                <p class="fs-5 text-light opacity-75">We're here to help. Reach out with any questions or inquiries.</p>
            </div>
        </section>

        <!-- Form and Info Section -->
        <section class="section-py">
            <div class="container">
                <div class="row g-4 g-lg-5">
                    
                    <!-- Contact Form -->
                    <div class="col-lg-7">
                        <div class="bg-slate-800 p-4 p-md-5 rounded-3">
                            <h2 class="fs-2 fw-bolder text-white mb-4">Send Us a Message</h2>

                            <?php if ($form_success): ?>
                                <div class="alert alert-success"><?php echo $form_success; ?></div>
                            <?php endif; ?>
                            <?php if ($form_error): ?>
                                <div class="alert alert-danger"><?php echo $form_error; ?></div>
                            <?php endif; ?>

                            <form action="contact.php" method="POST">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control form-control-dark" id="name" name="name" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control form-control-dark" id="email" name="email" required>
                                    </div>
                                    <div class="col-12">
                                        <label for="subject" class="form-label">Subject <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control form-control-dark" id="subject" name="subject" required>
                                    </div>
                                    <div class="col-12">
                                        <label for="message" class="form-label">Message <span class="text-danger">*</span></label>
                                        <textarea class="form-control form-control-dark" id="message" name="message" rows="6" required></textarea>
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold">Send Message</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Contact Info -->
                    <div class="col-lg-5">
                        <div class="contact-info-card">
                            <h2 class="fs-2 fw-bolder text-white mb-4">Contact Details</h2>
                            <p class="text-light opacity-75 mb-4">
                                You can also reach us directly via phone, email, or visit our office.
                            </p>
                            
                            <!-- Info pulled from your footer.php -->
                            <ul class="contact-info-list">
                                <li>
                                    <span class="icon"><i class="fa-solid fa-location-dot"></i></span>
                                    <span>Colombo, Sri Lanka</span>
                                </li>
                                <li>
                                    <span class="icon"><i class="fa-solid fa-phone"></i></span>
                                    <a href="tel:+94704866124">+94 70 486 6124</a>
                                </li>
                                <li>
                                    <span class="icon"><i class="fa-solid fa-envelope"></i></span>
                                    <a href="mailto:contact@flydolk.com">contact@flydolk.com</a>
                                </li>
                            </ul>

                            <hr class="border-secondary my-4">

                            <h3 class="fs-5 fw-bold text-white mb-3">Follow Us</h3>
                            <div class="social-icons">
                                <!-- Icons pulled from your footer.php -->
                                <a href="#" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                                <a href="#" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
                                <a href="#" aria-label="TikTok"><i class="fa-brands fa-tiktok"></i></a>
                                <a href="#" aria-label="YouTube"><i class="fa-brands fa-youtube"></i></a>
                                <a href="#" aria-label="LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- Map Section -->
        <section class="map-container">
            <iframe 
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d126743.5858604312!2d79.7861612760207!3d6.921838856861502!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3ae253d10f7a7003%3A0x320b2e4d32d3838d!2sColombo!5e0!3m2!1sen!2slk!4v1678888888888!5m2!1sen!2slk" 
                width="600" 
                height="450" 
                allowfullscreen="" 
                loading="lazy" 
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </section>

    </main>

    <?php include 'footer.php'; ?>

    <!-- Bootstrap JS Bundle (includes Popper) -->
     <script src="script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" xintegrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

</body>
</html>

