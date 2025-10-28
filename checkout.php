<?php
require_once 'connection.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header('Location: login-signup.php'); // Redirect to login if not logged in
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - FlyDolk</title>
    
    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    
    <!-- Google Fonts (Inter) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;900&display=swap" rel="stylesheet">
    
    <!-- Font Awesome (for icons) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>

    <!-- Custom CSS -->
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #0f172a; /* bg-slate-900 */
            color: #f1f5f9;
            padding-top: 140px; /* Mobile header */
        }
        @media (min-width: 992px) { /* lg breakpoint */
            body {
                padding-top: 90px; /* Desktop header */
            }
        }
        .checkout-header {
            padding: 4rem 0;
            background-color: #020617; /* bg-slate-950 */
            text-align: center;
        }
        .bg-slate-800 {
            background-color: #1e293b;
        }
    </style>
</head>
<body>

    <?php include 'header.php'; ?>

    <main>
        <!-- Checkout Header -->
        <section class="checkout-header">
            <div class="container">
                <h1 class="display-4 fw-bolder text-white">Checkout</h1>
                <p class="fs-5 text-light opacity-75">Complete your purchase.</p>
            </div>
        </section>

        <!-- Checkout Form (Placeholder) -->
        <section class="py-5">
            <div class="container">
                <div class="row">
                    <div class="col-md-8 offset-md-2">
                        <div class="bg-slate-800 p-4 p-md-5 rounded-3">
                            <h2 class="text-white fw-bold mb-4">Checkout Page Content</h2>
                            <p class="text-light">
                                This is where your shopping cart summary, shipping address form,
                                and payment processing (like Stripe or PayPal) would go.
                            </D>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php include 'footer.php'; ?>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script.js"></script>

</body>
</html>
