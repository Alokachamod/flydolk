<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'connection.php';

// 1. CHECK LOGIN
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header('Location: login-signup.php?redirect=order_history');
    exit;
}
$user_id = (int)$_SESSION['user_id'];

// 2. FETCH ORDER HISTORY (Grouped by Order ID)
$orders_rs = Database::search("
    SELECT 
        i.order_id, 
        i.created_at, 
        s.name AS status_name,
        SUM(i.total_amount) AS total_amount,
        (SELECT p.title FROM invoice inv JOIN product p ON inv.product_id = p.id WHERE inv.order_id = i.order_id LIMIT 1) AS first_product_title,
        (SELECT MIN(pi.img_url) FROM invoice inv JOIN product p ON inv.product_id = p.id JOIN product_img pi ON p.id = pi.product_id WHERE inv.order_id = i.order_id) AS first_product_image,
        COUNT(i.id) AS item_count
    FROM invoice i
    JOIN status s ON i.status_id = s.id
    JOIN user_has_address uha ON i.user_has_address_id = uha.id
    WHERE uha.user_id = $user_id
    GROUP BY i.order_id, i.created_at, s.name
    ORDER BY i.created_at DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order History - FlyDolk</title>
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="imgs/Flydo.png">
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
            body {
                padding-top: 90px; /* Desktop header */
            }
        }
        .bg-slate-800 { background-color: #1e293b; }
        .border-slate-700 { border-color: #334155; }
        
        .account-sidebar .list-group-item {
            background-color: transparent;
            border-color: #334155;
            color: #cbd5e1;
            font-weight: 600;
            transition: all 0.2s ease;
        }
        .account-sidebar .list-group-item:hover,
        .account-sidebar .list-group-item.active {
            background-color: #334155;
            color: #f1f5f9;
            border-left: 4px solid var(--fd-accent, #0ea5e9);
        }
        .order-card {
            background-color: #1e293b;
            border: 1px solid #334155;
            border-radius: 0.5rem;
            transition: all 0.2s ease;
        }
        .order-card:hover {
            border-color: #3b82f6;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.1);
        }
        .order-img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 0.25rem;
        }
        .status-badge {
            font-size: 0.8rem;
            font-weight: 600;
        }
    </style>
</head>
<body>

    <?php include 'header.php'; ?>

    <main class="container my-5">
        <div class="row g-4">
            
            <!-- Sidebar -->
            <div class="col-lg-3">
                <nav class="list-group account-sidebar bg-slate-800 p-2 rounded-3 border border-slate-700">
                    <a href="account.php" class="list-group-item list-group-item-action">
                        <i class="fa-regular fa-user me-2"></i> My Profile
                    </a>
                    <a href="order_history.php" class="list-group-item list-group-item-action active" aria-current="true">
                        <i class="fa-solid fa-box me-2"></i> My Orders
                    </a>
                    <a href="wishlist.php" class="list-group-item list-group-item-action">
                        <i class="fa-regular fa-heart me-2"></i> Wishlist
                    </a>
                    <a href="logout.php" class="list-group-item list-group-item-action text-danger" onclick="return confirm('Are you sure you want to logout?');">
                        <i class="fa-solid fa-right-from-bracket me-2"></i> Logout
                    </a>
                </nav>
            </div>

            <!-- Content Area -->
            <div class="col-lg-9">
                <h1 class="display-6 fw-bold text-white mb-4">Order History</h1>
                
                <div class="d-flex flex-column gap-3">
                    <?php if ($orders_rs->num_rows == 0): ?>
                        <div class="bg-slate-800 p-4 rounded-3 border border-slate-700 text-center">
                            <h4 class="text-white">No Orders Found</h4>
                            <p class="text-muted">You haven't placed any orders yet.</p>
                            <a href="shop.php" class="btn btn-primary">Start Shopping</a>
                        </div>
                    <?php else: ?>
                        <?php while($order = $orders_rs->fetch_assoc()): 
                            $shipping = 500; // Fixed shipping
                            $grand_total = $order['total_amount'] + $shipping;
                            $other_items = $order['item_count'] - 1;
                        ?>
                            <div class="order-card p-3">
                                <div class="row g-3">
                                    <!-- Order Info Header -->
                                    <div class="col-12 border-bottom border-slate-700 pb-3">
                                        <div class="d-flex flex-wrap justify-content-between align-items-center">
                                            <div>
                                                <div class="text-muted small">ORDER PLACED</div>
                                                <div class="text-light fw-bold"><?php echo date("F j, Y", strtotime($order['created_at'])); ?></div>
                                            </div>
                                            <div>
                                                <div class="text-muted small">TOTAL</div>
                                                <div class="text-light fw-bold">LKR <?php echo number_format($grand_total, 2); ?></div>
                                            </div>
                                            <div>
                                                <div class="text-muted small">ORDER #</div>
                                                <div class="text-light fw-bold"><?php echo htmlspecialchars($order['order_id']); ?></div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Order Body -->
                                    <div class="col-12">
                                        <div class="d-flex gap-3">
                                            <img src="<?php echo htmlspecialchars($order['first_product_image'] ?? 'imgs/placeholder.png'); ?>" alt="Product" class="order-img">
                                            <div class="flex-grow-1">
                                                <h5 class="text-white mb-1"><?php echo htmlspecialchars($order['first_product_title']); ?></h5>
                                                <?php if($other_items > 0): ?>
                                                    <p class="text-muted small">+ <?php echo $other_items; ?> other item(s)</p>
                                                <?php endif; ?>
                                                <span class="badge status-badge bg-info text-dark"><?php echo htmlspecialchars($order['status_name']); ?></span>
                                            </div>
                                            <div class="align-self-center">
                                                <a href="invoice.php?order_id=<?php echo htmlspecialchars($order['order_id']); ?>" class="btn btn-outline-primary">
                                                    View Invoice
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php endif; ?>
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

