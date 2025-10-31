<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// This page shows the "Order Complete" alert

// Get the order_id from the URL
$order_id = $_GET['order_id'] ?? null;
if (!$order_id) {
    // If no order ID, just show a generic message or redirect to home
    header('Location: index.php');
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Complete - FlyDolk</title>
    <link rel="icon" href="imgs/Flydo.png">
    <link rel="stylesheet" href="style.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts (Inter) -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;900&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #0f172a;
            color: #f1f5f9;
            padding-top: 140px; /* Mobile header */
        }
        @media (min-width: 992px) { /* lg breakpoint */
            body { padding-top: 90px; }
        }
        .success-card {
            background-color: #1e293b;
            border: 1px solid #334155;
            border-radius: 0.5rem;
        }
        .icon-success {
            font-size: 5rem;
            color: #22c55e; /* green-500 */
        }
        .order-id {
            font-family: monospace;
            font-size: 1.25rem;
            font-weight: 700;
            color: #60a5fa; /* blue-400 */
            background-color: #0f172a;
            padding: 0.5rem 1rem;
            border-radius: 0.25rem;
            border: 1px dashed #334155;
        }
    </style>
</head>
<body>

    <?php include 'header.php'; ?>

    <main class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="success-card text-center p-4 p-md-5">
                    
                    <div class="mb-4">
                        <i class="fas fa-check-circle icon-success"></i>
                    </div>
                    
                    <h1 class="display-5 fw-bold text-white mb-3">Thank You!</h1>
                    <p class="fs-4 text-white">Your order has been placed.</p>
                    
                    <div class="my-4">
                        <p class="text-muted mb-2">Your Order ID is:</p>
                        <div class="order-id"><?php echo htmlspecialchars($order_id); ?></div>
                    </div>
                    
                    <p class="text-muted">You will receive an email confirmation shortly. You can also view your invoice now.</p>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-center mt-4">
                        <a href="invoice.php?order_id=<?php echo urlencode($order_id); ?>" class="btn btn-primary btn-lg fw-bold" target="_blank">
                            <i class="fas fa-receipt me-2"></i> View Invoice
                        </a>
                        <a href="shop.php" class="btn btn-outline-light btn-lg">
                            Continue Shopping
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </main>

    <?php include 'footer.php'; ?>

    <script src="script.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

