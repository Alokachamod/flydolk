<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'connection.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy - FlyDolk</title>
    <link rel="stylesheet" href="style.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts (Inter) -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;900&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #0f172a;
            color: #f1f5f9;
            padding-top: 140px; /* Mobile header */
        }
        @media (min-width: 992px) { /* lg breakpoint */
            body {
                padding-top: 90px; /* Desktop header */
            }
        }
        
        .policy-container {
            background-color: #1e293b;
            border: 1px solid #334155;
            border-radius: 0.5rem;
            line-height: 1.8;
            color: #cbd5e1;
        }
        .policy-container h2 {
            color: #fff;
            font-weight: 700;
            border-bottom: 2px solid #334155;
            padding-bottom: 0.5rem;
        }
        .policy-container h3 {
            color: #f1f5f9;
            font-weight: 600;
            margin-top: 1.5rem;
        }
        .policy-container a {
            color: #60a5fa;
            text-decoration: none;
        }
        .policy-container a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <?php include 'header.php'; ?>

    <main class="container my-5">
        <div class="text-center mb-5">
            <h1 class="display-5 fw-bold text-white">Privacy Policy</h1>
            <p class="fs-5 text-muted">Your privacy is important to us.</p>
        </div>
        
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <div class="policy-container p-4 p-md-5">
                    <p>Last updated: <?php echo date("F j, Y"); ?></p>
                    
                    <p>FlyDolk ("us", "we", or "our") operates the flydolk.com website (the "Service"). This page informs you of our policies regarding the collection, use, and disclosure of personal data when you use our Service and the choices you have associated with that data.</p>
                    
                    <h2>Information Collection and Use</h2>
                    <p>We collect several different types of information for various purposes to provide and improve our Service to you.</p>

                    <h3>Types of Data Collected</h3>
                    <ul>
                        <li><strong>Personal Data:</strong> While using our Service, we may ask you to provide us with certain personally identifiable information that can be used to contact or identify you ("Personal Data"). This includes, but is not limited to: Email address, First name and last name, Phone number, Address (Line 1, Line 2, City, Province, Zip Code).</li>
                        <li><strong>Usage Data:</strong> We may also collect information on how the Service is accessed and used ("Usage Data"). This Usage Data may include information such as your computer's Internet Protocol address (e.g. IP address), browser type, browser version, the pages of our Service that you visit, the time and date of your visit, the time spent on those pages, unique device identifiers and other diagnostic data.</li>
                    </ul>

                    <h2>Use of Data</h2>
                    <p>FlyDolk uses the collected data for various purposes:</p>
                    <ul>
                        <li>To provide and maintain the Service</li>
                        <li>To process your orders and manage your account</li>
                        <li>To notify you about changes to our Service</li>
                        <li>To provide customer care and support</li>
                        <li>To detect, prevent and address technical issues</li>
                    </ul>

                    <h2>Data Security</h2>
                    <p>The security of your data is important to us, but remember that no method of transmission over the Internet, or method of electronic storage is 100% secure. While we strive to use commercially acceptable means to protect your Personal Data, we cannot guarantee its absolute security.</p>

                    <h2>Your Rights</h2>
                    <p>You have the right to access, update, or delete the information we have on you. You can do this at any time by visiting the "My Account" section of our website after logging in.</p>

                    <h2>Contact Us</h2>
                    <p>If you have any questions about this Privacy Policy, please contact us by email at <a href="mailto:contact@flydolk.com">contact@flydolk.com</a>.</p>
                </div>
            </div>
        </div>
    </main>

    <?php include 'footer.php'; ?>

    <script src="script.js"></script>
    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
