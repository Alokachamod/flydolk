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
    <title>Terms & Conditions - FlyDolk</title>
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
            <h1 class="display-5 fw-bold text-white">Terms & Conditions</h1>
            <p class="fs-5 text-muted">Please read these terms carefully before using our service.</p>
        </div>
        
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <div class="policy-container p-4 p-md-5">
                    <p>Last updated: <?php echo date("F j, Y"); ?></p>
                    
                    <h2>1. Agreement to Terms</h2>
                    <p>By accessing or using our Service (flydolk.com), you agree to be bound by these Terms. If you disagree with any part of the terms, then you may not access the Service. These Terms apply to all visitors, users, and others who access or use the Service.</p>

                    <h2>2. User Accounts</h2>
                    <p>When you create an account with us, you must provide us with information that is accurate, complete, and current at all times. Failure to do so constitutes a breach of the Terms, which may result in immediate termination of your account on our Service. You are responsible for safeguarding the password that you use to access the Service and for any activities or actions under your password.</p>

                    <h2>3. Products and Pricing</h2>
                    <p>We reserve the right to refuse or cancel any order at any time for reasons including but not limited to: product availability, errors in the description or price of the product, or error in your order. Prices for our products are subject to change without notice. We shall not be liable to you or to any third-party for any modification, price change, suspension, or discontinuance of the Service.</p>
                    <p>All prices are listed in Sri Lankan Rupees (LKR).</p>

                    <h2>4. Intellectual Property</h2>
                    <p>The Service and its original content, features, and functionality are and will remain the exclusive property of FlyDolk and its licensors. The Service is protected by copyright, trademark, and other laws of both Sri Lanka and foreign countries. Our trademarks may not be used in connection with any product or service without the prior written consent of FlyDolk.</p>

                    <h2>5. Limitation of Liability</h2>
                    <p>In no event shall FlyDolk, nor its directors, employees, partners, agents, suppliers, or affiliates, be liable for any indirect, incidental, special, consequential or punitive damages, including without limitation, loss of profits, data, use, goodwill, or other intangible losses, resulting from your access to or use of or inability to access or use the Service.</p>
                    <p>Your use of any drone or related product is at your sole risk. You are responsible for complying with all local and national laws regarding drone operation, including any registration or licensing requirements set by the Civil Aviation Authority of Sri Lanka (CAASL).</p>

                    <h2>6. Governing Law</h2>
                    <p>These Terms shall be governed and construed in accordance with the laws of Sri Lanka, without regard to its conflict of law provisions.</p>
                    
                    <h2>7. Changes</h2>
                    <p>We reserve the right, at our sole discretion, to modify or replace these Terms at any time. We will provide notice of any changes by posting the new Terms on this page.</p>
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
