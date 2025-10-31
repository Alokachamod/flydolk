<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'connection.php';

// 1. CHECK LOGIN
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header('Location: login-signup.php?redirect=wishlist');
    exit;
}
$user_id = (int)$_SESSION['user_id'];

// 2. FETCH WISHLIST ITEMS
$wishlist_rs = Database::search("
    SELECT 
        w.id AS wishlist_id,
        p.id AS product_id,
        p.title,
        p.price,
        p.qty,
        MIN(pi.img_url) AS img_url
    FROM wishlist w
    JOIN product p ON w.product_id = p.id
    LEFT JOIN product_img pi ON p.id = pi.product_id
    WHERE w.user_id = $user_id
    GROUP BY w.id, p.id
    ORDER BY p.title ASC
");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Wishlist - FlyDolk</title>
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
        
        /* Product Card Style (from shop.php) */
        .product-card {
            background-color: #1e293b;
            border: 1px solid #334155;
            border-radius: 0.5rem;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.3);
            border-color: #3b82f6;
        }
        .product-card a {
            text-decoration: none;
            color: #f1f5f9;
        }
        .product-img {
            height: 200px;
            width: 100%;
            object-fit: cover;
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
                    <a href="order_history.php" class="list-group-item list-group-item-action">
                        <i class="fa-solid fa-box me-2"></i> My Orders
                    </a>
                    <a href="wishlist.php" class="list-group-item list-group-item-action active" aria-current="true">
                        <i class="fa-regular fa-heart me-2"></i> Wishlist
                    </a>
                    <a href="logout.php" class="list-group-item list-group-item-action text-danger" onclick="return confirm('Are you sure you want to logout?');">
                        <i class="fa-solid fa-right-from-bracket me-2"></i> Logout
                    </a>
                </nav>
            </div>

            <!-- Content Area -->
            <div class="col-lg-9">
                <h1 class="display-6 fw-bold text-white mb-4">My Wishlist</h1>
                
                <div id="alert-placeholder"></div>

                <div class="row g-3">
                    <?php if ($wishlist_rs->num_rows == 0): ?>
                        <div class="col-12">
                            <div class="bg-slate-800 p-4 rounded-3 border border-slate-700 text-center">
                                <h4 class="text-white">Your Wishlist is Empty</h4>
                                <p class="text-muted">You haven't added any products to your wishlist yet.</p>
                                <a href="shop.php" class="btn btn-primary">Start Shopping</a>
                            </div>
                        </div>
                    <?php else: ?>
                        <?php while($item = $wishlist_rs->fetch_assoc()): ?>
                            <div class="col-md-6 col-lg-4" id="wishlist-item-<?php echo $item['wishlist_id']; ?>">
                                <div class="product-card h-100">
                                    <a href="single_product.php?id=<?php echo $item['product_id']; ?>">
                                        <img src="<?php echo htmlspecialchars($item['img_url'] ?? 'imgs/placeholder.png'); ?>" class="product-img" alt="<?php echo htmlspecialchars($item['title']); ?>">
                                    </a>
                                    <div class="p-3 d-flex flex-column h-100">
                                        <a href="single_product.php?id=<?php echo $item['product_id']; ?>">
                                            <h5 class="fw-bold mb-1"><?php echo htmlspecialchars($item['title']); ?></h5>
                                        </a>
                                        <div class="mb-2">
                                            <span class="fs-4 fw-bold text-primary">LKR <?php echo number_format($item['price'], 2); ?></span>
                                        </div>
                                        
                                        <div class="mt-auto">
                                            <button class="btn btn-primary w-100 mb-2" onclick="addToCart(<?php echo $item['product_id']; ?>, this)">
                                                <i class="fa-solid fa-cart-shopping me-2"></i>Add to Cart
                                            </button>
                                            <button class="btn btn-outline-danger w-100" onclick="removeFromWishlist(<?php echo $item['wishlist_id']; ?>, this)">
                                                <i class="fa-regular fa-trash-can me-2"></i>Remove
                                            </button>
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

    <!-- Bootstrap JS -->
     <script src="script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Alert Function
        const alertPlaceholder = document.getElementById('alert-placeholder');
        function showAlert(message, type) {
            const wrapper = document.createElement('div');
            wrapper.innerHTML = [
                `<div class="alert alert-${type} alert-dismissible" role="alert">`,
                `   <div>${message}</div>`,
                '   <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>',
                '</div>'
            ].join('');
            alertPlaceholder.append(wrapper);
        }

        // Add to Cart
        function addToCart(productId, button) {
            button.disabled = true;
            button.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Adding...';

            const formData = new FormData();
            formData.append('product_id', productId);
            formData.append('action', 'add');

            fetch('cart_process.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    showAlert('Product added to cart!', 'success');
                    // Update header cart count
                    document.getElementById('cart-count-badge').textContent = data.cart_count;
                    document.getElementById('cart-count-badge-mobile').textContent = data.cart_count;
                } else {
                    showAlert(data.message, 'danger');
                }
            })
            .catch(error => {
                showAlert('An error occurred.', 'danger');
            })
            .finally(() => {
                button.disabled = false;
                button.innerHTML = '<i class="fa-solid fa-cart-shopping me-2"></i>Add to Cart';
            });
        }

        // Remove from Wishlist
        function removeFromWishlist(wishlistId, button) {
            if (!confirm('Are you sure you want to remove this from your wishlist?')) {
                return;
            }

            button.disabled = true;
            button.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Removing...';
            
            const formData = new FormData();
            formData.append('wishlist_id', wishlistId);

            fetch('wishlist_process.php', { // Assuming wishlist_process.php can handle removal
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    showAlert('Item removed from wishlist.', 'success');
                    // Remove card from page
                    document.getElementById('wishlist-item-' + wishlistId).remove();
                } else {
                    showAlert(data.message, 'danger');
                }
            })
            .catch(error => {
                showAlert('An error occurred.', 'danger');
            })
            .finally(() => {
                button.disabled = false;
                button.innerHTML = '<i class="fa-regular fa-trash-can me-2"></i>Remove';
            });
        }
    </script>
</body>
</html>

