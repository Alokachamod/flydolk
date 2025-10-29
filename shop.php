<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'connection.php';

// --- 1. SETUP LOGGED IN STATE ---
$user_id = (int)($_SESSION['user_id'] ?? 0);

// --- 2. FILTER & SEARCH LOGIC ---
$search = '';
$category_filter = '';
$brand_filter = '';
$sort_order = 'ORDER BY p.create_at DESC';
$where_clauses = [];
$params = [];
$param_types = '';
$page_title = "Shop All Products";
$page_subtitle = "Browse our full collection of high-tech drones and accessories.";

// Handle Search
if (isset($_GET['search']) && !empty($_GET['search'])) {
    Database::setUpConnection(); // Need connection for real_escape_string
    $search = Database::$connection->real_escape_string($_GET['search']);
    $where_clauses[] = "(p.title LIKE ? OR p.description LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $param_types .= 'ss';
    $page_title = "Search Results for '$search'";
    $page_subtitle = "Showing products matching your search query.";
}

// Handle Category Filter
if (isset($_GET['category']) && !empty($_GET['category'])) {
    $category_id = (int)$_GET['category'];
    if ($category_id > 0) {
        $where_clauses[] = "p.category_id = ?";
        $params[] = $category_id;
        $param_types .= 'i';
        
        // Get category name for title
        $cat_name_rs = Database::search("SELECT name FROM category WHERE id = $category_id");
        if ($cat_name_rs->num_rows == 1) {
            $page_title = htmlspecialchars($cat_name_rs->fetch_assoc()['name']);
            $page_subtitle = "Shop all products in the $page_title category.";
        }
    }
}

// Handle Brand Filter
if (isset($_GET['brand']) && !empty($_GET['brand'])) {
    $brand_id = (int)$_GET['brand'];
    if ($brand_id > 0) {
        $where_clauses[] = "p.brand_id = ?";
        $params[] = $brand_id;
        $param_types .= 'i';
    }
}

// Handle Sorting
$sort_map = [
    'date_desc' => 'ORDER BY p.create_at DESC',
    'date_asc' => 'ORDER BY p.create_at ASC',
    'price_asc' => 'ORDER BY p.price ASC',
    'price_desc' => 'ORDER BY p.price DESC',
    'title_asc' => 'ORDER BY p.title ASC',
];
$sort_key = $_GET['sort'] ?? 'date_desc';
if (isset($sort_map[$sort_key])) {
    $sort_order = $sort_map[$sort_key];
}

// --- 3. BUILD FINAL WHERE CLAUSE ---
$where_sql = '';
if (!empty($where_clauses)) {
    $where_sql = "WHERE " . implode(' AND ', $where_clauses);
}

// --- 4. PAGINATION LOGIC ---
$page = (int)($_GET['page'] ?? 1);
if ($page < 1) $page = 1;
$products_per_page = 8;
$offset = ($page - 1) * $products_per_page;

// --- 5. DATABASE QUERIES ---
Database::setUpConnection();

// Get total product count for pagination
$count_sql = "SELECT COUNT(DISTINCT p.id) AS total FROM product p $where_sql";
$stmt_count = Database::$connection->prepare($count_sql);
if (!empty($params)) {
    $stmt_count->bind_param($param_types, ...$params);
}
$stmt_count->execute();
$total_products = (int)$stmt_count->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total_products / $products_per_page);

// Get products for the current page
$sql = "
    SELECT 
        p.id, p.title, p.price, p.qty,
        MIN(pi.img_url) AS img_url
    FROM product p
    LEFT JOIN product_img pi ON p.id = pi.product_id
    $where_sql
    GROUP BY p.id
    $sort_order
    LIMIT ? OFFSET ?
";

// Add LIMIT and OFFSET params
$params[] = $products_per_page;
$params[] = $offset;
$param_types .= 'ii';

$stmt = Database::$connection->prepare($sql);
$stmt->bind_param($param_types, ...$params);
$stmt->execute();
$product_rs = $stmt->get_result();

// Get all categories and brands for filters
$category_rs = Database::search("SELECT * FROM category ORDER BY name ASC");
$brand_rs = Database::search("SELECT * FROM brand ORDER BY name ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - FlyDolk</title>
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
        
        .product-card {
            background-color: #1e293b;
            border: 1px solid #334155;
            border-radius: 0.5rem;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.3), 0 0 15px rgba(14, 165, 233, 0.3);
            border-color: #0ea5e9;
        }
        .product-img-container {
            height: 220px;
            background-color: #0f172a;
        }
        .product-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        .product-card:hover .product-img {
            transform: scale(1.05);
        }
        .product-body {
            padding: 1.25rem;
        }
        .product-title {
            color: #fff;
            font-weight: 700;
            font-size: 1.1rem;
            text-decoration: none;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            height: 2.75rem; /* 1.1rem * 1.25 line-height * 2 lines */
        }
        .product-title:hover {
            color: #38bdf8;
        }
        .product-price {
            color: #38bdf8;
            font-weight: 900;
            font-size: 1.5rem;
        }
        .out-of-stock {
            color: #fb7185;
            font-weight: 600;
            font-size: 1.2rem;
        }
        .product-actions {
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .product-card:hover .product-actions {
            opacity: 1;
        }
        .btn-icon {
            color: #94a3b8;
            background: #334155;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            transition: all 0.2s ease;
        }
        .btn-icon:hover, .btn-icon.active {
            color: #fff;
            background: #0ea5e9;
        }
        .btn-icon.active {
            color: #fff;
            background: #e11d48; /* Red for active wishlist */
        }
        
        .form-select-dark {
            background-color: #1e293b;
            border-color: #334155;
            color: #f1f5f9;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%239ca3af' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");
        }
        .form-select-dark:focus {
            background-color: #1e293b;
            border-color: #60a5fa;
            color: #f1f5f9;
            box-shadow: 0 0 0 0.25rem rgba(96, 165, 250, 0.25);
        }

        .pagination .page-item .page-link {
            background-color: #1e293b;
            border-color: #334155;
            color: #94a3b8;
        }
        .pagination .page-item.active .page-link {
            background-color: #0ea5e9;
            border-color: #0ea5e9;
            color: #fff;
        }
        .pagination .page-item.disabled .page-link {
            background-color: #334155;
            border-color: #334155;
            color: #64748b;
        }
        .pagination .page-item .page-link:hover {
            background-color: #334155;
        }
        
        /* Custom Toast Modal */
        #toast-modal {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translate(-50%, 200%);
            background-color: #1e293b;
            color: #f1f5f9;
            padding: 1rem 1.5rem;
            border-radius: 0.5rem;
            border: 1px solid #334155;
            box-shadow: 0 10px 20px rgba(0,0,0,0.3);
            z-index: 1100;
            transition: transform 0.4s ease-out;
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        #toast-modal.show {
            transform: translate(-50%, 0);
        }
        #toast-modal.success { border-left: 4px solid #22c55e; }
        #toast-modal.error { border-left: 4px solid #ef4444; }
        #toast-modal-icon { font-size: 1.5rem; }
        #toast-modal.success #toast-modal-icon { color: #22c55e; }
        #toast-modal.error #toast-modal-icon { color: #ef4444; }
    </style>
</head>
<body>

    <?php include 'header.php'; ?>

    <main class="container my-5">
        <!-- Page Title -->
        <div class="text-center mb-5">
            <h1 class="display-5 fw-bold text-white"><?php echo $page_title; ?></h1>
            <p class="fs-5 text-muted"><?php echo $page_subtitle; ?></p>
        </div>

        <!-- Filter Bar -->
        <div class="d-flex flex-wrap gap-2 align-items-center bg-slate-800 p-3 rounded-3 border border-slate-700 mb-4">
            <form id="filter-form" action="shop.php" method="GET" class="d-flex flex-wrap gap-2 flex-grow-1">
                <!-- Hidden search input to persist search -->
                <?php if (!empty($search)): ?>
                    <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
                <?php endif; ?>
                
                <div class="flex-grow-1" style="min-width: 150px;">
                    <label for="category" class="form-label small text-muted">Category</label>
                    <select name="category" id="category" class="form-select form-select-dark" onchange="this.form.submit()">
                        <option value="">All Categories</option>
                        <?php while ($cat = $category_rs->fetch_assoc()): ?>
                            <option value="<?php echo $cat['id']; ?>" <?php echo (($_GET['category'] ?? '') == $cat['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="flex-grow-1" style="min-width: 150px;">
                    <label for="brand" class="form-label small text-muted">Brand</label>
                    <select name="brand" id="brand" class="form-select form-select-dark" onchange="this.form.submit()">
                        <option value="">All Brands</option>
                        <?php while ($brand = $brand_rs->fetch_assoc()): ?>
                            <option value="<?php echo $brand['id']; ?>" <?php echo (($_GET['brand'] ?? '') == $brand['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($brand['name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="flex-grow-1" style="min-width: 150px;">
                    <label for="sort" class="form-label small text-muted">Sort By</label>
                    <select name="sort" id="sort" class="form-select form-select-dark" onchange="this.form.submit()">
                        <option value="date_desc" <?php echo ($sort_key == 'date_desc') ? 'selected' : ''; ?>>Newest</option>
                        <option value="price_asc" <?php echo ($sort_key == 'price_asc') ? 'selected' : ''; ?>>Price: Low to High</option>
                        <option value="price_desc" <?php echo ($sort_key == 'price_desc') ? 'selected' : ''; ?>>Price: High to Low</option>
                        <option value="title_asc" <?php echo ($sort_key == 'title_asc') ? 'selected' : ''; ?>>Name: A to Z</option>
                    </select>
                </div>
            </form>
        </div>

        <!-- Product Grid -->
        <div class="row g-4">
            <?php if ($product_rs->num_rows > 0): ?>
                <?php while ($product = $product_rs->fetch_assoc()): ?>
                    <div class="col-md-6 col-lg-4 col-xl-3">
                        <div class="product-card">
                            <a href="single_product.php?id=<?php echo $product['id']; ?>" class="d-block product-img-container">
                                <img src="<?php echo htmlspecialchars($product['img_url'] ?? 'imgs/placeholder.png'); ?>" 
                                     alt="<?php echo htmlspecialchars($product['title']); ?>" 
                                     class="product-img"
                                     onerror="this.onerror=null; this.src='imgs/placeholder.png';">
                            </a>
                            <div class="product-body">
                                <a href="single_product.php?id=<?php echo $product['id']; ?>" class="product-title mb-2">
                                    <?php echo htmlspecialchars($product['title']); ?>
                                </a>
                                
                                <?php if ((int)$product['qty'] > 0): ?>
                                    <div class="product-price mb-3">
                                        LKR <?php echo number_format($product['price'], 2); ?>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <button class="btn btn-primary" onclick="addToCart(<?php echo $product['id']; ?>)">
                                            <i class="fa-solid fa-cart-plus me-2"></i> Add to Cart
                                        </button>
                                        <div class="product-actions">
                                            <button class="btn-icon" title="Buy Now" onclick="buyNow(<?php echo $product['id']; ?>)">
                                                <i class="fa-solid fa-bolt"></i>
                                            </button>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <div class="out-of-stock mb-3">
                                        Out of Stock
                                    </div>
                                    <button class="btn btn-secondary" disabled>
                                        Out of Stock
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="text-center p-5 bg-slate-800 rounded-3 border border-slate-700">
                        <h3 class="text-white">No Products Found</h3>
                        <p class="text-muted">Try adjusting your filters or search terms.</p>
                        <a href="shop.php" class="btn btn-primary mt-3">Clear All Filters</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <nav aria-label="Page navigation" class="mt-5 d-flex justify-content-center">
                <ul class="pagination">
                    <?php 
                    // Build query string for pagination links
                    $query_params = $_GET;
                    unset($query_params['page']);
                    $query_string = http_build_query($query_params);
                    ?>
                    
                    <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $page - 1; ?>&<?php echo $query_string; ?>">Previous</a>
                    </li>
                    
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?>&<?php echo $query_string; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>
                    
                    <li class="page-item <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $page + 1; ?>&<?php echo $query_string; ?>">Next</a>
                    </li>
                </ul>
            </nav>
        <?php endif; ?>
    </main>

    <!-- Toast Modal -->
    <div id="toast-modal">
        <span id="toast-modal-icon"><i class="fa-solid fa-check-circle"></i></span>
        <span id="toast-modal-message">Success!</span>
    </div>

    <?php include 'footer.php'; ?>

    <script src="script.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // --- Toast Modal Logic ---
        const toastModal = document.getElementById('toast-modal');
        const toastIcon = document.getElementById('toast-modal-icon');
        const toastMessage = document.getElementById('toast-modal-message');
        let toastTimeout;

        function showToast(message, isSuccess = true) {
            clearTimeout(toastTimeout); // Clear any existing timer
            
            toastMessage.textContent = message;
            if (isSuccess) {
                toastModal.className = 'success';
                toastIcon.innerHTML = '<i class="fa-solid fa-check-circle"></i>';
            } else {
                toastModal.className = 'error';
                toastIcon.innerHTML = '<i class="fa-solid fa-times-circle"></i>';
            }
            
            toastModal.classList.add('show');
            
            toastTimeout = setTimeout(() => {
                toastModal.classList.remove('show');
            }, 3000); // Hide after 3 seconds
        }

        // --- Add to Cart Logic ---
        function addToCart(productId, buyNow = false) {
            const formData = new FormData();
            formData.append('product_id', productId);
            formData.append('qty', 1); // Default qty from shop page is 1
            if (buyNow) {
                formData.append('buy_now', 'true');
            }

            fetch('cart_process.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    if (data.redirect) {
                        window.location.href = data.redirect; // Redirect for Buy Now
                    } else {
                        showToast(data.message, true); // Show success toast
                        // --- !! NEW CODE TO FIX BADGE !! ---
                        if (typeof data.cart_count !== 'undefined') {
                            // Call the global function from header.php
                            updateHeaderCartCount(data.cart_count);
                        }
                        // --- !! END NEW CODE !! ---
                    }
                } else {
                    showToast(data.message, false); // Show error toast
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('A connection error occurred.', false);
            });
        }
        
        // --- Buy Now Logic ---
        function buyNow(productId) {
            addToCart(productId, true); // Call addToCart with buyNow flag
        }
        
    </script>
</body>
</html>

