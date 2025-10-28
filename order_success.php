<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// --- CHECK LOGIN ---
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header('Location: login-signup.php');
    exit;
}

// Get order info from redirect
$order_id = $_GET['order_id'] ?? null;
$error_message = null;

if (!$order_id) {
    $error_message = "No order ID was provided. Your order may not have been completed.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmed - FlyDolk</title>
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
            padding-top: 140px;
        }
        @media (min-width: 992px) { body { padding-top: 90px; } }
        .bg-slate-800 { background-color: #1e293b; }
        .border-slate-700 { border-color: #334155; }
        .icon-success {
            font-size: 5rem;
            color: #22c55e; /* text-green-500 */
        }
        .icon-fail {
            font-size: 5rem;
            color: #ef4444; /* text-red-500 */
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <main class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-8 text-center">
                <div class="bg-slate-800 p-5 rounded-3 border border-slate-700">
                    
                    <?php if ($error_message): ?>
                        <!-- Payment Failed -->
                        <i class="fas fa-times-circle icon-fail mb-4"></i>
                        <h1 class="display-5 fw-bold text-white mb-3">Order Failed</h1>
                        <p class="fs-5 text-light opacity-75 mb-4">
                            <?php echo htmlspecialchars($error_message); ?>
                        </p>
                        <a href="checkout.php" class="btn btn-primary btn-lg fw-bold">Try Again</a>
                    
                    <?php else: ?>
                        <!-- Payment Succeeded -->
                        <i class="fas fa-check-circle icon-success mb-4"></i>
                        <h1 class="display-5 fw-bold text-white mb-3">Thank You!</h1>
                        <p class="fs-5 text-light opacity-75 mb-2">
                            Your order has been placed successfully.
                        </Player>
                        <p class="fs-5 text-light opacity-75 mb-4">
                            Your Order ID is: <strong class="text-white"><?php echo htmlspecialchars($order_id); ?></strong>
                        </p>
                        <a href="shop.php" class="btn btn-primary btn-lg fw-bold">Continue Shopping</a>
                    
                    <?php endif; ?>
                    
                </div>
            </div>
        </div>
    </main>
    
    <?php include 'footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

