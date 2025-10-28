<?php
require_once 'connection.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get user ID if logged in
$user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($product_id == 0) {
    // Redirect or show error if no ID
    header('Location: shop.php');
    exit;
}

// --- 1. Fetch Main Product Details ---
$product_sql = "SELECT * FROM product WHERE id = $product_id AND product_status_id = 1";
$product_rs = Database::search($product_sql);

if ($product_rs->num_rows == 1) {
    $product_data = $product_rs->fetch_assoc();
    $category_id = $product_data['category_id']; // For related products

    // --- 2. Fetch Product Images ---
    $images_sql = "SELECT * FROM product_img WHERE product_id = $product_id";
    $product_img_rs = Database::search($images_sql);
    $images = [];
    while ($img_row = $product_img_rs->fetch_assoc()) {
        $images[] = $img_row['img_url'];
    }
    // Set a placeholder if no images are found
    $main_image = $images[0] ?? 'imgs/placeholder.png';

    // --- 3. Check if in Cart / Wishlist ---
    $in_cart = false;
    $in_wishlist = false;
    if ($user_id > 0) {
        // Check cart
        $cart_check_sql = "SELECT * FROM cart WHERE user_id = $user_id AND product_id = $product_id";
        $cart_check_rs = Database::search($cart_check_sql);
        if ($cart_check_rs->num_rows > 0) {
            $in_cart = true;
        }

        // Check wishlist
        $wishlist_check_sql = "SELECT * FROM wishlist WHERE user_id = $user_id AND product_id = $product_id";
        $wishlist_check_rs = Database::search($wishlist_check_sql);
        if ($wishlist_check_rs->num_rows > 0) {
            $in_wishlist = true;
        }
    }

    // --- 4. Fetch Related Products ---
    // FIX: Used MIN(pi.img_url) and aliased it AS img_url to fix the only_full_group_by error.
    $related_sql = "SELECT p.*, MIN(pi.img_url) AS img_url
                    FROM product p
                    LEFT JOIN product_img pi ON p.id = pi.product_id
                    WHERE p.category_id = $category_id AND p.id != $product_id AND p.product_status_id = 1
                    GROUP BY p.id
                    LIMIT 4";
    $related_rs = Database::search($related_sql);

} else {
    // No product found
    $product_data = null;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $product_data ? htmlspecialchars($product_data['title']) : 'Product Not Found'; ?> - FlyDolk</title>
    
    <!-- Bootstrap CSS CDN -->
     <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" xintegrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
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
            color: #f1f5f9; /* text-gray-100 */
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;

            /* FIX: Add padding to offset fixed-top header */
            padding-top: 140px; /* Mobile header */
        }
        @media (min-width: 992px) { /* lg breakpoint */
            body {
                padding-top: 90px; /* Desktop header */
            }
        }
        
        .bg-slate-800 { background-color: #1e293b; }
        .bg-slate-950 { background-color: #020617; }
        .text-blue-400 { color: #60a5fa; }
        .border-slate-700 { border-color: #334155; }

        /* Product Gallery */
        .main-image-container {
            background-color: #1e293b;
            border-radius: 0.5rem;
            border: 1px solid #334155;
            padding: 1rem;
        }
        #main-image {
            width: 100%;
            height: 450px;
            object-fit: contain;
            border-radius: 0.375rem;
            transition: transform 0.3s ease;
        }
        .thumbnail-gallery {
            display: flex;
            gap: 0.75rem;
            margin-top: 1rem;
        }
        .thumb-img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 0.375rem;
            border: 2px solid #334155;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .thumb-img:hover, .thumb-img.active {
            border-color: #60a5fa;
            transform: scale(1.05);
        }

        /* Product Info */
        .product-title {
            font-size: 2.5rem;
            font-weight: 900;
            color: #fff;
        }
        .product-price {
            font-size: 2.25rem;
            font-weight: 700;
            color: #60a5fa;
        }
        .stock-status {
            font-weight: 600;
            padding: 0.25rem 0.75rem;
            border-radius: 99px;
            font-size: 0.9rem;
        }
        .stock-status.in-stock {
            background-color: rgba(34, 197, 94, 0.1);
            color: #22c55e;
        }
        .stock-status.out-of-stock {
            background-color: rgba(239, 68, 68, 0.1);
            color: #ef4444;
        }

        /* Quantity Selector */
        .qty-selector {
            display: flex;
            align-items: center;
        }
        .qty-btn {
            background-color: #334155;
            color: #f1f5f9;
            border: 1px solid #475569;
            width: 40px;
            height: 40px;
            font-size: 1.25rem;
            transition: background-color 0.2s;
        }
        .qty-btn:hover {
            background-color: #475569;
        }
        #qty-input {
            width: 60px;
            height: 40px;
            text-align: center;
            background-color: #1e293b;
            border: 1px solid #475569;
            color: #f1f5f9;
            font-weight: 600;
            /* Remove spinners */
            -moz-appearance: textfield;
        }
        #qty-input::-webkit-outer-spin-button,
        #qty-input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        /* Action Buttons */
        .btn-buy-now {
            background: linear-gradient(135deg, #3b82f6, #60a5fa);
            border: none;
        }
        .btn-buy-now:hover {
            opacity: 0.9;
        }
        
        /* Wishlist Button */
        .btn-wishlist {
            border-color: #ef4444; /* red outline */
            color: #ef4444;
            transition: all 0.2s ease;
            --bs-btn-hover-bg: rgba(239, 68, 68, 0.1);
            --bs-btn-hover-border-color: #ef4444;
            --bs-btn-hover-color: #ef4444;
        }
        .btn-wishlist.active {
            background-color: #ef4444;
            color: #fff;
            border-color: #ef4444;
            --bs-btn-hover-bg: #dc2626;
            --bs-btn-hover-border-color: #dc2626;
        }

        
        /* Related Products */
        .related-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: #fff;
            border-bottom: 2px solid #334155;
            padding-bottom: 0.5rem;
        }
        .product-card {
            background-color: #1e293b; /* bg-slate-800 */
            border: 1px solid #334155; /* slate-700 */
            border-radius: 0.5rem;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        .product-card:hover {
            transform: translateY(-5px);
            border-color: #60a5fa;
        }
        .product-img {
            width: 100%;
            height: 180px;
            object-fit: cover;
        }
        .product-content {
            padding: 1rem;
        }
        .product-title-sm {
            color: #f1f5f9;
            font-size: 1rem;
            font-weight: 600;
            text-decoration: none;
        }
        .product-title-sm:hover {
            color: #60a5fa;
        }
        .product-price-sm {
            font-size: 1.1rem;
            font-weight: 700;
            color: #60a5fa;
        }
        
        /* Custom Modal */
        .fd-modal {
            background-color: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(5px);
        }
        .fd-modal-content {
            background-color: #1e293b; /* bg-slate-800 */
            border: 1px solid #334155;
        }
        .fd-modal-header, .fd-modal-footer {
            border-color: #334155;
        }

    </style>
</head>
<body>

    <?php include 'header.php'; ?>

    <main class="container my-5">
        <?php if ($product_data): ?>
            <div class="row g-4 g-lg-5">
                <!-- Product Gallery -->
                <div class="col-lg-7">
                    <div class="main-image-container shadow-lg">
                        <img src="<?php echo $main_image; ?>" alt="<?php echo htmlspecialchars($product_data['title']); ?>" id="main-image">
                    </div>
                    <?php if (count($images) > 1): ?>
                        <div class="thumbnail-gallery">
                            <?php foreach ($images as $index => $img_url): ?>
                                <img src="<?php echo $img_url; ?>" 
                                     alt="Thumbnail <?php echo $index + 1; ?>" 
                                     class="thumb-img <?php echo $index == 0 ? 'active' : ''; ?>"
                                     onclick="changeMainImage('<?php echo $img_url; ?>', this)">
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Product Info -->
                <div class="col-lg-5">
                    <h1 class="product-title"><?php echo htmlspecialchars($product_data['title']); ?></h1>
                    
                    <div class="d-flex align-items-center gap-3 my-3">
                        <span class="product-price">LKR <?php echo number_format($product_data['price'], 2); ?></span>
                        <?php if ($product_data['qty'] > 0): ?>
                            <span class="stock-status in-stock">
                                <i class="fas fa-check-circle me-1"></i> In Stock
                            </span>
                        <?php else: ?>
                            <span class="stock-status out-of-stock">
                                <i class="fas fa-times-circle me-1"></i> Out of Stock
                            </span>
                        <?php endif; ?>
                    </div>

                    <p class="fs-6 text-light opacity-75">
                        <?php echo nl2br(htmlspecialchars($product_data['description'])); ?>
                    </p>

                    <hr class="border-slate-700 my-4">

                    <!-- Actions -->
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <label for="qty-input" class="form-label mb-0 fw-bold">Quantity:</label>
                        <div class="qty-selector">
                            <button class="btn qty-btn rounded-start-2" type="button" onclick="updateQty(-1)">-</button>
                            <input type="text" id="qty-input" class="form-control rounded-0" value="1" min="1" max="<?php echo $product_data['qty']; ?>" readonly>
                            <button class="btn qty-btn rounded-end-2" type="button" onclick="updateQty(1)">+</button>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button 
                            class="btn btn-primary btn-buy-now btn-lg fw-bold py-3"
                            onclick="buyNow(<?php echo $product_id; ?>)"
                            <?php echo $product_data['qty'] <= 0 ? 'disabled' : ''; ?>>
                            Buy Now
                        </button>
                        <div class="d-flex gap-2">
                            <button 
                                class="btn btn-outline-primary btn-lg fw-bold py-3 flex-grow-1"
                                onclick="addToCart(this, <?php echo $product_id; ?>)"
                                <?php echo $in_cart || $product_data['qty'] <= 0 ? 'disabled' : ''; ?>>
                                <?php echo $in_cart ? 'In Cart' : 'Add to Cart'; ?>
                            </button>
                            <button 
                                class="btn btn-wishlist btn-lg py-3 px-4 <?php echo $in_wishlist ? 'active' : ''; ?>"
                                onclick="toggleWishlist(this, <?php echo $product_id; ?>)"
                                <?php echo $product_data['qty'] <= 0 ? 'disabled' : ''; ?>>
                                <i class="<?php echo $in_wishlist ? 'fas' : 'far'; ?> fa-heart"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Related Products -->
            <?php if ($related_rs->num_rows > 0): ?>
                <hr class="border-slate-700 my-5">
                <div class="row">
                    <div class="col-12">
                        <h2 class="related-title mb-4">You Might Also Like</h2>
                    </div>
                    <?php while ($related_product = $related_rs->fetch_assoc()): ?>
                        <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                            <div class="product-card shadow-sm h-100">
                                <a href="single_product.php?id=<?php echo $related_product['id']; ?>">
                                    <img src="<?php echo htmlspecialchars($related_product['img_url'] ?? 'imgs/placeholder.png'); ?>" class="product-img" alt="<?php echo htmlspecialchars($related_product['title']); ?>">
                                </a>
                                <div class="product-content">
                                    <a href="single_product.php?id=<?php echo $related_product['id']; ?>" class="product-title-sm mb-2 d-block">
                                        <?php echo htmlspecialchars($related_product['title']); ?>
                                    </a>
                                    <span class="product-price-sm">LKR <?php echo number_format($related_product['price'], 2); ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php endif; ?>

        <?php else: ?>
            <!-- Product Not Found -->
            <div class="text-center py-5">
                <h1 class="display-1 text-blue-400"><i class="fas fa-search"></i></h1>
                <h2 class="text-white fw-bold mt-4">Product Not Found</h2>
                <p class="text-light opacity-75 fs-5">Sorry, we couldn't find the product you're looking for.</p>
                <a href="shop.php" class="btn btn-primary btn-lg fw-bold mt-3">Back to Shop</a>
            </div>
        <?php endif; ?>
    </main>

    <!-- Custom Modal -->
    <div class="modal fade fd-modal" id="actionModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content fd-modal-content text-light">
          <div class="modal-header fd-modal-header">
            <h5 class="modal-title" id="modalLabel">Notice</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body" id="modalBody">
            <!-- Message will be injected here -->
          </div>
          <div class="modal-footer fd-modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <a href="login-signup.php" id="modalLoginButton" class="btn btn-primary fw-bold">Login / Sign Up</a>
          </div>
        </div>
      </div>
    </div>
    
    <?php include 'footer.php'; ?>

    <!-- Bootstrap JS Bundle -->
     <script src="script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" xintegrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <!-- Custom JS -->
    <script>
        const isLoggedIn = <?php echo json_encode($user_id > 0); ?>;
        const actionModal = new bootstrap.Modal(document.getElementById('actionModal'));
        const modalBody = document.getElementById('modalBody');
        const modalLoginButton = document.getElementById('modalLoginButton');
        
        <?php if ($product_data): // Only define these if product exists ?>
        const qtyInput = document.getElementById('qty-input');
        const maxQty = <?php echo $product_data['qty']; ?>;
        <?php endif; ?>

        // --- Image Gallery ---
        function changeMainImage(src, clickedThumbnail) {
            document.getElementById('main-image').src = src;
            
            // Update active state for thumbnails
            document.querySelectorAll('.thumb-img').forEach(thumb => {
                thumb.classList.remove('active');
            });
            clickedThumbnail.classList.add('active');
        }

        // --- Quantity Selector ---
        function updateQty(amount) {
            let currentQty = parseInt(qtyInput.value);
            let newQty = currentQty + amount;

            if (newQty < 1) {
                newQty = 1;
            }
            if (newQty > maxQty) {
                newQty = maxQty;
                showModal(`Sorry, we only have ${maxQty} in stock.`);
            }
            
            qtyInput.value = newQty;
        }

        // --- Modal Helper ---
        function showModal(message, showLoginButton = false) {
            modalBody.textContent = message;
            modalLoginButton.style.display = showLoginButton ? 'inline-block' : 'none';
            actionModal.show();
        }

M
        // --- Add to Cart ---
        async function addToCart(button, productId) {
            if (!isLoggedIn) {
                showModal('Please log in to add items to your cart.', true);
                return;
            }

            button.disabled = true;
            button.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Adding...';
            
            const selectedQty = qtyInput.value;

            const formData = new FormData();
            formData.append('product_id', productId);
            formData.append('qty', selectedQty);

            try {
                const response = await fetch('cart_process.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();

                if (result.status === 'success') {
                    button.textContent = 'In Cart';
                    showModal(result.message);
                } else if (result.status === 'exists') {
                    button.textContent = 'In Cart';
                    showModal(result.message);
                } else {
                    showModal(result.message || 'An error occurred.');
                    button.disabled = false;
                    button.textContent = 'Add to Cart';
                }
            } catch (error) {
                showModal('A connection error occurred.');
                button.disabled = false;
                button.textContent = 'Add to Cart';
            }
        }

        // --- Buy Now ---
        async function buyNow(productId) {
            if (!isLoggedIn) {
                showModal('Please log in to buy items.', true);
                return;
            }
            
            const selectedQty = qtyInput.value;
            const formData = new FormData();
            formData.append('product_id', productId);
            formData.append('qty', selectedQty);

            try {
                const response = await fetch('cart_process.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();

                if (result.status === 'success' || result.status === 'exists') {
                    window.location.href = 'checkout.php';
                } else {
                    showModal(result.message || 'Could not add item to cart for checkout.');
                }
            } catch (error) {
                showModal('A connection error occurred.');
            }
        }

        // --- Toggle Wishlist ---
        async function toggleWishlist(button, productId) {
            if (!isLoggedIn) {
                showModal('Please log in to manage your wishlist.', true);
                return;
            }

            button.disabled = true;
            button.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';

            const formData = new FormData();
            formData.append('product_id', productId);

            try {
                const response = await fetch('wishlist_process.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();

                if (result.status === 'added') {
                    button.classList.add('active');
                    button.innerHTML = '<i class="fas fa-heart"></i>';
                    showModal(result.message);
                } else if (result.status === 'removed') {
                    button.classList.remove('active');
                    button.innerHTML = '<i class="far fa-heart"></i>';
                    showModal(result.message);
                } else {
                    showModal(result.message || 'An error occurred.');
                    // Revert button state if error
                    if(button.classList.contains('active')) {
                        button.innerHTML = '<i class="fas fa-heart"></i>';
                    } else {
                        button.innerHTML = '<i class="far fa-heart"></i>';
                    }
                }
            } catch (error) {
                showModal('A connection error occurred.');
                // Revert button state if error
                if(button.classList.contains('active')) {
                    button.innerHTML = '<i class="fas fa-heart"></i>';
                } else {
                    button.innerHTML = '<i class="far fa-heart"></i>';
                }
            } finally {
                button.disabled = false;
            }
        }
    </script>

</body>
</html>

