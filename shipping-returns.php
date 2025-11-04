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
    <title>Shipping & Returns - FlyDolk</title>
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
            border-bottom: 2px solid #384456ff;
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
            <h1 class="display-5 fw-bold text-white">Shipping & Returns</h1>
            <p class="fs-5 text-muted">Our policies for shipping and returns.</p>
        </div>
        
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <div class="policy-container p-4 p-md-5">
                    <h2>Shipping Policy</h2>
                    <p>
                        FlyDolk is committed to getting your order to you as quickly and safely as possible. We currently ship to all locations within Sri Lanka.
                    </p>
                    
                    <h3>Processing Time</h3>
                    <p>
                        All orders are processed within 1-2 business days (excluding weekends and holidays) after receiving your order confirmation email. You will receive another notification when your order has shipped.
                    </p>

                    <h3>Shipping Rates & Estimates</h3>
                    <ul>
                        <li><strong>Standard Shipping:</strong> LKR 500.00. Estimated 2-4 business days.</li>
                        <li><strong>Free Shipping:</strong> We offer free standard shipping on all orders over LKR 50,000.</li>
                        <li><strong>Expedited Shipping:</strong> Please contact us for expedited shipping options and quotes.</li>
                    </ul>

                    <h3>Order Tracking</h3>
                    <p>
                        When your order has shipped, you will receive an email notification from us which will include a tracking number you can use to check its status. Please allow 48 hours for the tracking information to become available.
                    </p>

                    <h2 class="mt-5">Return Policy</h2>
                    <p>
                        We want you to be completely satisfied with your purchase. If you are not, we are here to help.
                    </p>
                    
                    <h3>30-Day Returns</h3>
                    <p>
                        We accept returns up to 30 days after delivery, if the item is unused and in its original condition (including all packaging and accessories), and we will refund the full order amount minus the shipping costs for the return.
                    </p>

                    <h3>Defective or Damaged Items</h3>
                    <p>
                        In the event that your order arrives damaged in any way, please email us as soon as possible at <a href="mailto:contact@flydolk.com">contact@flydolk.com</a> with your order number and a photo of the item’s condition. We address these on a case-by-case basis but will try our best to work towards a satisfactory solution, such as a replacement or repair.
                    </p>

                    <h3>How to Initiate a Return</h3>
                    <p>
                        To initiate a return, please complete the following steps:
                    </p>
                    <ol>
                        <li>Reply to your order confirmation email to request which items you would like to return.</li>
                        <li>Once your request is approved, we will send you a return shipping label and instructions.</li>
                        <li>Pack the item securely and send it back to us.</li>
                        <li>Once we receive and inspect the item, we will process your refund.</li>
                    </ol>
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
