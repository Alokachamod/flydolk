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
    $phone = filter_var(trim($_POST['phone']), FILTER_SANITIZE_STRING);
    $model = filter_var(trim($_POST['model']), FILTER_SANITIZE_STRING);
    $service_type = filter_var(trim($_POST['service_type']), FILTER_SANITIZE_STRING);
    $description = filter_var(trim($_POST['description']), FILTER_SANITIZE_STRING);

    // Basic validation
    if (empty($name) || !filter_var($email, FILTER_VALIDATE_EMAIL) || empty($service_type) || empty($description)) {
        $form_error = "Please fill out all required fields (Name, Email, Service, Description).";
    } else {
        // --- Send Email ---
        // (This uses the email from your footer.php)
        $to = "contact@flydolk.com";
        $subject = "New FlyDolk Service Request from: $name";
        
        $body = "You have received a new service request:\n\n";
        $body .= "Name: $name\n";
        $body .= "Email: $email\n";
        $body .= "Phone: $phone\n\n";
        $body .= "Drone Model: $model\n";
        $body .= "Service Type: $service_type\n\n";
        $body .= "Description of Issue:\n$description\n";
        
        $headers = "From: $email";

        // Note: mail() function's success depends on your server's sendmail configuration.
        if (mail($to, $subject, $body, $headers)) {
            $form_success = "Thank you! Your service request has been submitted. We will contact you shortly.";
        } else {
            $form_error = "There was an error sending your request. Please try again or email us directly.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expert Drone Services - FlyDolk</title>
    
    <link rel="icon" href="imgs/Flydo.png">
    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" xintegrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
    <!-- Google Fonts (Inter) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;900&display=swap" rel="stylesheet">
    
    <!-- Font Awesome (for icons) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <link rel="stylesheet" href="style.css">

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
        .service-header {
            padding: 4rem 0;
            background-color: #020617; /* bg-slate-950 */
            text-align: center;
        }

        /* Service Card */
        .service-card {
            background-color: #1e293b; /* bg-slate-800 */
            border: 1px solid #334155; /* slate-700 */
            border-radius: 0.5rem;
            padding: 2rem;
            transition: all 0.3s ease;
            height: 100%;
        }
        .service-card:hover {
            transform: translateY(-5px);
            border-color: #60a5fa; /* text-blue-400 */
        }
        .service-card .icon {
            font-size: 2.5rem;
            color: #60a5fa; /* text-blue-400 */
            margin-bottom: 1.5rem;
        }
        
        /* "How it Works" Timeline */
        .timeline-step {
            position: relative;
            padding-left: 2.5rem;
            padding-bottom: 2rem;
            border-left: 2px solid #334155; /* slate-700 */
        }
        .timeline-step:last-child {
            border-left: 2px solid transparent;
            padding-bottom: 0;
        }
        .timeline-step::before {
            content: '';
            position: absolute;
            left: -0.8rem;
            top: 0;
            width: 1.5rem;
            height: 1.5rem;
            border-radius: 50%;
            background-color: #1e293b;
            border: 4px solid #60a5fa; /* text-blue-400 */
        }
        .timeline-step h3 {
            color: #60a5fa; /* text-blue-400 */
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

    </style>
</head>
<body class="bg-dark text-light">

    <?php include 'header.php'; ?>

    <!-- Main Content --><main>

        <!-- Service Header -->
        <section class="service-header">
            <div class="container">
                <h1 class="display-4 fw-bolder text-white">Expert Drone Services</h1>
                <p class="fs-5 text-light opacity-75">Get your gear back in the air, fast.</p>
            </div>
        </section>

        <!-- Our Services Section -->
        <section class="section-py">
            <div class="container">
                <h2 class="display-5 fw-bolder text-white text-center mb-5">What We Do</h2>
                <div class="row g-4">
                    <div class="col-lg-4">
                        <div class="service-card">
                            <div class="icon"><i class="fa-solid fa-screwdriver-wrench"></i></div>
                            <h3 class="fs-4 fw-bold text-white mb-3">Crash Repair & Diagnostics</h3>
                            <p class="text-light opacity-75">
                                From broken arms to gimbal failures, our certified technicians will diagnose
                                and repair your drone to manufacturer standards.
                            </p>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="service-card">
                            <div class="icon"><i class="fa-solid fa-satellite-dish"></i></div>
                            <h3 class="fs-4 fw-bold text-white mb-3">Maintenance & Tune-Ups</h3>
                            <p class="text-light opacity-75">
                                Keep your drone in peak condition with firmware updates, sensor calibration,
                                battery health checks, and a full systems diagnostic.
                            </p>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="service-card">
                            <div class="icon"><i class="fa-solid fa-briefcase"></i></div>
                            <h3 class="fs-4 fw-bold text-white mb-3">Enterprise & Custom Builds</h3>
                            <p class="text-light opacity-75">
                                We offer professional services including fleet management, custom FPV
                                builds for cinematography, and payload integration.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- How It Works & Form Section -->
        <section class="section-py bg-slate-950">
            <div class="container">
                <div class="row g-5 align-items-center">
                    
                    <!-- How It Works -->
                    <div class="col-lg-5">
                        <h2 class="display-5 fw-bolder text-white mb-4">How It Works</h2>
                        <div class="timeline">
                            <div class="timeline-step">
                                <h3 class="fs-5 fw-bold mb-1">1. Submit a Request</h3>
                                <p class="text-light opacity-75">Fill out the form with details about your drone and the issue you're facing.</p>
                            </div>
                            <div class="timeline-step">
                                <h3 class="fs-5 fw-bold mb-1">2. Receive Your Quote</h3>
                                <p class="text-light opacity-75">Our team will review your request and send you a detailed service quote and shipping instructions.</p>
                            </div>
                            <div class="timeline-step">
                                <h3 class="fs-5 fw-bold mb-1">3. Fly Again</h3>
                                <p class="text-light opacity-75">We complete the service, test your drone, and ship it back to you, mission-ready.</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Service Form -->
                    <div class="col-lg-7">
                        <div class="bg-slate-800 p-4 p-md-5 rounded-3">
                            <h2 class="fs-2 fw-bolder text-white mb-4">Request Service</h2>

                            <?php if ($form_success): ?>
                                <div class="alert alert-success"><?php echo $form_success; ?></div>
                            <?php endif; ?>
                            <?php if ($form_error): ?>
                                <div class="alert alert-danger"><?php echo $form_error; ?></div>
                            <?php endif; ?>

                            <form action="service.php" method="POST">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control form-control-dark" id="name" name="name" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control form-control-dark" id="email" name="email" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="phone" class="form-label">Phone Number (Optional)</label>
                                        <input type="tel" class="form-control form-control-dark" id="phone" name="phone">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="model" class="form-label">Drone Model</label>
                                        <input type="text" class="form-control form-control-dark" id="model" name="model" placeholder="e.g. DJI Mavic 3 Pro">
                                    </div>
                                    <div class="col-12">
                                        <label for="service_type" class="form-label">Service Type <span class="text-danger">*</span></label>
                                        <select id="service_type" name="service_type" class="form-select form-select-dark" required>
                                            <option value="" selected disabled>-- Select a service --</option>
                                            <option value="Crash Repair & Diagnostics">Crash Repair & Diagnostics</option>
                                            <option value="Maintenance & Tune-Up">Maintenance & Tune-Up</option>
                                            <option value="Enterprise & Custom Build">Enterprise & Custom Build</option>
                                            <option value="Other">Other (Please describe below)</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label for="description" class="form-label">Describe your issue <span class="text-danger">*</span></label>
                                        <textarea class="form-control form-control-dark" id="description" name="description" rows="5" required></textarea>
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold">Submit Service Request</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </main>

    <?php include 'footer.php'; ?>

    <!-- Bootstrap JS Bundle (includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" xintegrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="script.js"></script>
</body>
</html>
