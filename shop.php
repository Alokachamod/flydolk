<?php
require_once 'connection.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get user ID if logged in
$user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;

// --- Database Query Setup ---

// Filters
$category = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$brand = isset($_GET['brand']) ? (int)$_GET['brand'] : 0;
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'default';

// Base Query
// We join product_img to get the first image
$sql = "SELECT p.*, 
        pi.img_url"; // FIX: Changed from img_url_1 to img_url

// Conditionally add cart column
if ($user_id > 0) {
    // If logged in, join to check cart
    $sql .= ", c.id AS cart_id";
} else {
    // If not logged in, add NULL placeholder so the loop doesn't break
    $sql .= ", NULL AS cart_id";
}
        
$sql .= " FROM product p
          LEFT JOIN product_img pi ON p.id = pi.product_id";

// Conditionally join cart table
if ($user_id > 0) {
    // These joins will FAIL if the 'cart' table doesn't exist, but it does.
    $sql .= " LEFT JOIN cart c ON p.id = c.product_id AND c.user_id = $user_id";
}

$sql .= " WHERE p.product_status_id = 1"; // Assuming 1 is 'Active'

// Apply Filters
$where_clauses = [];
if ($category > 0) {
    $where_clauses[] = "p.category_id = $category";
}
if ($brand > 0) {
    $where_clauses[] = "p.brand_id = $brand";
}
if (!empty($where_clauses)) {
    $sql .= " AND " . implode(" AND ", $where_clauses);
}

// Apply Sorting
switch ($sort) {
    case 'price_asc':
        $sql .= " ORDER BY p.price ASC";
        break;
    case 'price_desc':
        $sql .= " ORDER BY p.price DESC";
        break;
    case 'name_asc':
        $sql .= " ORDER BY p.title ASC";
        break;
    default:
        $sql .= " ORDER BY p.create_at DESC";
}

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$products_per_page = 12;
$offset = ($page - 1) * $products_per_page;

$total_products_rs = Database::search($sql);
$total_products = $total_products_rs->num_rows;
$total_pages = ceil($total_products / $products_per_page);

$sql .= " LIMIT $products_per_page OFFSET $offset";
$product_rs = Database::search($sql);

// Fetch filter data
$category_rs = Database::search("SELECT * FROM category");
$brand_rs = Database::search("SELECT * FROM brand");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop All Products - FlyDolk</title>
    
    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" xintegrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
    <!-- Google Fonts (Inter) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;900&display=swap" rel="stylesheet">
    
    <!-- Font Awesome (for icons) -->
     <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>

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

            /* --- FIX: Add padding to offset fixed-top header --- */
            padding-top: 140px; /* Mobile header */
        }
        @media (min-width: 992px) { /* lg breakpoint */
            body {
                padding-top: 90px; /* Desktop header */
            }
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
        
        /* Page Header */
        .shop-header {
            padding: 4rem 0;
            background-color: #020617; /* bg-slate-950 */
            text-align: center;
        }

        /* Filter Bar */
        .filter-bar {
            background-color: #1e293b; /* bg-slate-800 */
            border-bottom: 1px solid #334155;
        }
        .form-select-dark {
            background-color: #334155; /* slate-700 */
            color: #f1f5f9;
            border: 1px solid #475569; /* slate-600 */
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%2394a3b8' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");
        }
        .form-select-dark:focus {
            border-color: #60a5fa;
            box-shadow: 0 0 0 0.25rem rgba(96, 165, 250, 0.25);
        }

        /* Product Card */
        .product-card {
            background-color: #1e293b; /* bg-slate-800 */
            border: 1px solid #334155; /* slate-700 */
            border-radius: 0.5rem;
            overflow: hidden;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
        }
        .product-card:hover {
            transform: translateY(-5px);
            border-color: #60a5fa; /* text-blue-400 */
        }
        .product-img-wrap {
            position: relative;
            background-color: #334155;
        }
        .product-img {
            width: 100%;
            height: 220px;
            object-fit: cover;
            transition: opacity 0.3s ease;
        }
        .product-card:hover .product-img {
            opacity: 0.8;
        }
        .product-content {
            padding: 1.25rem;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }
        .product-title {
            color: #f1f5f9;
            font-size: 1.1rem;
            font-weight: 600;
            text-decoration: none;
        }
        .product-title:hover {
            color: #60a5fa;
        }
        .product-price {
            font-size: 1.5rem;
            font-weight: 700;
            color: #60a5fa;
            margin-top: auto; /* Pushes price to bottom */
            margin-bottom: 1rem;
        }

        /* Action Buttons */
        .btn-buy-now {
            background: linear-gradient(135deg, #3b82f6, #60a5fa); /* blue-500 to blue-400 */
            border: none;
        }
        .btn-buy-now:hover {
            opacity: 0.9;
        }
        
        /* Pagination */
        .pagination .page-item .page-link {
            background-color: #1e293b;
            color: #f1f5f9;
            border-color: #334155;
        }
        .pagination .page-item.active .page-link {
            background-color: #60a5fa;
            border-color: #60a5fa;
            color: #020617;
        }
        .pagination .page-item.disabled .page-link {
            background-color: #334155;
            color: #94a3b8;
            border-color: #334155;
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
        .fd-modal-header {
            border-bottom: 1px solid #334155;
        }
        .fd-modal-footer {
            border-top: 1px solid #334155;
        }

    </style>
</head>
<body class="bg-dark text-light">

    <?php include 'header.php'; ?>

    <!-- Main Content --><main>

        <!-- Shop Header -->
        <section class="shop-header">
            <div class="container">
                <h1 class="display-4 fw-bolder text-white">Shop All Products</h1>
                <p class="fs-5 text-light opacity-75">Find the perfect gear for your next flight.</p>
            </div>
        </section>

        <!-- Filter Bar -->
        <section class="filter-bar py-3 sticky-top shadow-sm">
            <div class="container">
                <form id="filterForm" action="shop.php" method="GET">
                    <div class="row g-2 align-items-center">
                        <div class="col-md-3">
                            <label for="category" class="visually-hidden">Category</label>
                            <select name="category" id="category" class="form-select form-select-dark" onchange="this.form.submit()">
                                <option value="0" <?php echo ($category == 0) ? 'selected' : ''; ?>>All Categories</option>
                                <?php while ($cat = $category_rs->fetch_assoc()): ?>
                                <option value="<?php echo $cat['id']; ?>" <?php echo ($category == $cat['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="brand" class="visually-hidden">Brand</label>
                            <select name="brand" id="brand" class="form-select form-select-dark" onchange="this.form.submit()">
                                <option value="0" <?php echo ($brand == 0) ? 'selected' : ''; ?>>All Brands</option>
                                <?php while ($br = $brand_rs->fetch_assoc()): ?>
                                <option value="<?php echo $br['id']; ?>" <?php echo ($brand == $br['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($br['name']); ?>
                                </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-3 ms-auto">
                            <label for="sort" class="visually-hidden">Sort By</label>
                            <select name="sort" id="sort" class="form-select form-select-dark" onchange="this.form.submit()">
                                <option value="default" <?php echo ($sort == 'default') ? 'selected' : ''; ?>>Sort: Newest</option>
                                <option value="price_asc" <?php echo ($sort == 'price_asc') ? 'selected' : ''; ?>>Sort: Price Low to High</option>
                                <option value="price_desc" <?php echo ($sort == 'price_desc') ? 'selected' : ''; ?>>Sort: Price High to Low</option>
                                <option value="name_asc" <?php echo ($sort == 'name_asc') ? 'selected' : ''; ?>>Sort: Name A-Z</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
        </section>

        <!-- Product Grid -->
        <section class="py-5">
            <div class="container">
                <div class="row g-4">
                    <?php if ($product_rs->num_rows > 0): ?>
                        <?php while ($product = $product_rs->fetch_assoc()): ?>
                            <?php 
                                $product_id = $product['id'];
                                $product_title = htmlspecialchars($product['title']);
                                $product_price = htmlspecialchars($product['price']);
                                $product_img = htmlspecialchars($product['img_url'] ?? 'imgs/placeholder.png'); // FIX: Changed from img_url_1 to img_url
                                
                                // Check if in cart from our joined query
                                $in_cart = !is_null($product['cart_id']);
                            ?>
                            <div class="col-lg-3 col-md-4 col-sm-6">
                                <div class="product-card shadow-sm">
                                    <div class="product-img-wrap">
                                        <a href="single_product.php?id=<?php echo $product_id; ?>">
                                            <img src="<?php echo $product_img; ?>" class="product-img" alt="<?php echo $product_title; ?>">
                                        </a>
                                        <!-- Wishlist Button Removed -->
                                    </div>
                                    <div class="product-content">
                                        <a href="single_product.php?id=<?php echo $product_id; ?>" class="product-title mb-2">
                                            <?php echo $product_title; ?>
                                        </a>
                                        <span class="product-price">LKR <?php echo number_format($product_price, 2); ?></span>
                                        
                                        <!-- Action Buttons -->
                                        <div class="d-grid gap-2">
                                            <button 
                                                class="btn btn-primary btn-buy-now fw-bold"
                                                onclick="buyNow(<?php echo $product_id; ?>)">
                                                Buy Now
                                            </button>
                                            <button 
                                                class="btn btn-outline-primary fw-bold" 
                                                onclick="addToCart(this, <?php echo $product_id; ?>)"
                                                <?php echo $in_cart ? 'disabled' : ''; ?>>
                                                <?php echo $in_cart ? 'In Cart' : 'Add to Cart'; ?>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="col-12">
                            <div class="text-center p-5 bg-slate-800 rounded-3">
                                <h3 class="text-white">No Products Found</h3>
                                <p class="text-light opacity-75">Try adjusting your filters or check back later.</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                <nav aria-label="Product pagination" class="mt-5 d-flex justify-content-center">
                    <ul class="pagination pagination-lg">
                        <?php 
                        // Build query string for pagination links
                        $params = $_GET;
                        unset($params['page']);
                        $query_string = http_build_query($params);
                        ?>

                        <!-- Previous Button -->
                        <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $page - 1; ?>&<?php echo $query_string; ?>" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>

                        <!-- Page Numbers -->
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>&<?php echo $query_string; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>

                        <!-- Next Button -->
                        <li class="page-item <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $page + 1; ?>&<?php echo $query_string; ?>" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    </ul>
                </nav>
                <?php endif; ?>

            </div>
        </section>

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

    <!-- Bootstrap JS Bundle (includes Popper) -->
     <script src="script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" xintegrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <!-- Custom JS for Cart/Wishlist -->
    <script>
        // Check login state (from header.php)
        const isLoggedIn = <?php echo json_encode($user_id > 0); ?>;
        const actionModal = new bootstrap.Modal(document.getElementById('actionModal'));
        const modalBody = document.getElementById('modalBody');
        const modalLoginButton = document.getElementById('modalLoginButton');

        // Shows a modal with a custom message
        function showModal(message, showLoginButton = false) {
            modalBody.textContent = message;
            modalLoginButton.style.display = showLoginButton ? 'inline-block' : 'none';
            actionModal.show();
        }

        // --- Add to Cart ---
        async function addToCart(button, productId) {
            if (!isLoggedIn) {
                showModal('Please log in to add items to your cart.', true);
                return;
            }

            button.disabled = true;
            button.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Adding...';

            const formData = new FormData();
            formData.append('product_id', productId);
            formData.append('qty', 1);

            try {
                const response = await fetch('cart_process.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();

                if (result.status === 'success') {
                    button.textContent = 'In Cart';
                    // Optionally, update header cart count
                    // updateCartCount(result.cart_count); 
                    showModal(result.message);
                } else if (result.status === 'exists') { // Handle case where item already in cart
                    button.textContent = 'In Cart';
                    showModal(result.message);
                } else {
                    showModal(result.message || 'An error occurred.');
                    button.disabled = false;
                    button.textContent = 'Add to Cart';
                }
            } catch (error) {
                console.error('Error:', error);
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
            
            // 1. Add to cart
            const formData = new FormData();
            formData.append('product_id', productId);
            formData.append('qty', 1);

            try {
                const response = await fetch('cart_process.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();

                // 2. Redirect to checkout
                if (result.status === 'success' || result.status === 'exists') {
                    window.location.href = 'checkout.php';
                } else {
                    showModal(result.message || 'Could not add item to cart for checkout.');
                }
            } catch (error) {
                showModal('A connection error occurred.');
            }
        }

        // --- Toggle Wishlist (Functionality Removed) ---
        function toggleWishlist(button, productId) {
            // This function is no longer used but we can leave it
            // or show a modal. Let's show a modal.
            showModal('Wishlist functionality is not currently available.');
        }

    </script>

</body>
</html>

