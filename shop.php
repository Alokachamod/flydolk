<?php
require_once 'connection.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// --- CHECK LOGIN STATUS (for cart/wishlist) ---
$is_logged_in = isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
$user_id = $is_logged_in ? (int)$_SESSION['user_id'] : 0;

// --- 1. FILTER & SORT LOGIC ---

// Search
$is_search = false;
$search_term = '';
$search_sql = '';
$search_param = ''; // For pagination/filter links
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $is_search = true;
    // Sanitize the search term
    Database::setUpConnection(); // FIX: Establish connection before using it
    $search_term = Database::$connection->real_escape_string($_GET['search']);
    $search_sql = " AND (p.title LIKE '%$search_term%' OR p.description LIKE '%$search_term%') ";
    $search_param = '&search=' . urlencode($_GET['search']);
}

// Category Filter
$filter_sql = '';
$category_param = ''; // For pagination/filter links
$current_category = null;
if (isset($_GET['category']) && !empty($_GET['category'])) {
    $category_id = (int)$_GET['category'];
    $filter_sql .= " AND p.category_id = $category_id";
    $category_param = '&category=' . $category_id;
    // Get category name for display
    $cat_name_rs = Database::search("SELECT name FROM category WHERE id = $category_id");
    if ($cat_name_rs->num_rows > 0) $current_category = $cat_name_rs->fetch_assoc()['name'];
}

// Brand Filter
$brand_param = ''; // For pagination/filter links
$current_brand = null;
if (isset($_GET['brand']) && !empty($_GET['brand'])) {
    $brand_id = (int)$_GET['brand'];
    $filter_sql .= " AND p.brand_id = $brand_id";
    $brand_param = '&brand=' . $brand_id;
    // Get brand name for display
    $brand_name_rs = Database::search("SELECT name FROM brand WHERE id = $brand_id");
    if ($brand_name_rs->num_rows > 0) $current_brand = $brand_name_rs->fetch_assoc()['name'];
}

// Sort
$sort_sql = " ORDER BY p.create_at DESC"; // Default sort
$sort_param = ''; // For pagination/filter links
$current_sort = 'Newest';
if (isset($_GET['sort'])) {
    $sort_param = '&sort=' . $_GET['sort'];
    switch ($_GET['sort']) {
        case 'price_asc':
            $sort_sql = " ORDER BY p.price ASC";
            $current_sort = 'Price: Low to High';
            break;
        case 'price_desc':
            $sort_sql = " ORDER BY p.price DESC";
            $current_sort = 'Price: High to Low';
            break;
        case 'name_asc':
            $sort_sql = " ORDER BY p.title ASC";
            $current_sort = 'Name: A to Z';
            break;
        case 'name_desc':
            $sort_sql = " ORDER BY p.title DESC";
            $current_sort = 'Name: Z to A';
            break;
        default:
            $current_sort = 'Newest';
            break;
    }
}

// --- 2. PAGINATION LOGIC ---
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$results_per_page = 8;
$offset = ($page - 1) * $results_per_page;

// --- 3. DATABASE QUERIES ---

// Get Categories for filter dropdown
$categories_rs = Database::search("SELECT * FROM category ORDER BY name ASC");
// Get Brands for filter dropdown
$brands_rs = Database::search("SELECT * FROM brand ORDER BY name ASC");

// Main query for products
$sql = "SELECT 
            p.*, 
            MIN(pi.img_url) AS img_url
            " . ($is_logged_in ? ", (SELECT COUNT(id) FROM cart WHERE product_id = p.id AND user_id = $user_id) AS in_cart" : "") . "
        FROM product p 
        LEFT JOIN product_img pi ON p.id = pi.product_id
        WHERE p.product_status_id = 1 
        $search_sql
        $filter_sql
        GROUP BY p.id
        $sort_sql
        LIMIT $results_per_page OFFSET $offset";
$products_rs = Database::search($sql);

// Query for total number of products for pagination
$total_sql = "SELECT COUNT(DISTINCT p.id) AS total
              FROM product p
              WHERE p.product_status_id = 1
              $search_sql
              $filter_sql";
$total_rs = Database::search($total_sql);
$total_results = $total_rs->fetch_assoc()['total'];
$total_pages = ceil($total_results / $results_per_page);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop - FlyDolk</title>
    
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
        .text-blue-400 { color: #60a5fa; }
        .border-slate-700 { border-color: #334155; }
        .dropdown-menu-dark .dropdown-item:hover {
            background-color: rgba(96, 165, 250, 0.1);
            color: #60a5fa;
        }
        
        /* Product Card */
        .product-card {
            background-color: #1e293b; /* bg-slate-800 */
            border: 1px solid #334155; /* border-slate-700 */
            border-radius: 0.5rem;
            overflow: hidden;
            transition: all 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3), 0 0 15px rgba(96, 165, 250, 0.1);
            border-color: #4b5563;
        }
        .product-card-img-container {
            position: relative;
            background-color: #0f172a;
        }
        .product-card-img {
            width: 100%;
            height: 220px;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        .product-card:hover .product-card-img {
            transform: scale(1.05);
        }
        .product-card-body {
            padding: 1.25rem;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }
        .product-card-title {
            color: #fff;
            font-weight: 600;
            font-size: 1.1rem;
            text-decoration: none;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            min-height: 2.75rem; /* 2 lines */
        }
        .product-card-title:hover {
            color: #60a5fa;
        }
        .product-card-price {
            font-size: 1.5rem;
            font-weight: 700;
            color: #60a5fa;
            margin-bottom: 1rem;
        }
        .product-card-actions {
            margin-top: auto; /* Pushes buttons to the bottom */
        }
        
        .btn-buy-now {
            flex-grow: 1;
        }
        
        /* Pagination */
        .pagination .page-link {
            background-color: #1e293b;
            border-color: #334155;
            color: #f1f5f9;
        }
        .pagination .page-link:hover {
            background-color: #334155;
            color: #fff;
        }
        .pagination .page-item.active .page-link {
            background-color: #60a5fa;
            border-color: #60a5fa;
            color: #0f172a;
        }
        
        /* No Products Found */
        .no-products-container {
            background-color: #1e293b;
            border: 1px dashed #334155;
            border-radius: 0.5rem;
            padding: 4rem;
            text-align: center;
        }
        .no-products-icon {
            font-size: 4rem;
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
        
        <!-- Page Title -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="display-5 fw-bold text-white m-0">Shop Products</h1>
        </div>

        <!-- Search Results Message -->
        <?php if ($is_search): ?>
            <h4 class="text-white mb-4">
                Showing results for: <span class="text-blue-400 fst-italic">"<?php echo htmlspecialchars($_GET['search']); ?>"</span>
            </h4>
        <?php endif; ?>

        <!-- Filter/Sort Bar -->
        <div class="d-flex flex-wrap gap-2 align-items-center mb-4 p-3 bg-slate-800 rounded-3 border border-slate-700">
            <span class="fw-bold text-light me-2">Filters:</span>
            <!-- Category Dropdown -->
            <div class="dropdown">
                <button class="btn btn-dark dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <?php echo $current_category ?? 'Category'; ?>
                </button>
                <ul class="dropdown-menu dropdown-menu-dark">
                    <li><a class="dropdown-item" href="?<?php echo $brand_param; ?><?php echo $sort_param; ?><?php echo $search_param; ?>">All Categories</a></li>
                    <?php while ($category = $categories_rs->fetch_assoc()): ?>
                        <li><a class="dropdown-item" href="?category=<?php echo $category['id']; ?><?php echo $brand_param; ?><?php echo $sort_param; ?><?php echo $search_param; ?>">
                            <?php echo htmlspecialchars($category['name']); ?>
                        </a></li>
                    <?php endwhile; ?>
                </ul>
            </div>
            
            <!-- Brand Dropdown -->
            <div class="dropdown">
                <button class="btn btn-dark dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                     <?php echo $current_brand ?? 'Brand'; ?>
                </button>
                <ul class="dropdown-menu dropdown-menu-dark">
                    <li><a class="dropdown-item" href="?<?php echo $category_param; ?><?php echo $sort_param; ?><?php echo $search_param; ?>">All Brands</a></li>
                    <?php while ($brand = $brands_rs->fetch_assoc()): ?>
                        <li><a class="dropdown-item" href="?brand=<?php echo $brand['id']; ?><?php echo $category_param; ?><?php echo $sort_param; ?><?php echo $search_param; ?>">
                            <?php echo htmlspecialchars($brand['name']); ?>
                        </a></li>
                    <?php endwhile; ?>
                </ul>
            </div>
            
            <div class="flex-grow-1"></div>
            
            <!-- Sort Dropdown -->
            <span class="fw-bold text-light me-2">Sort By:</span>
            <div class="dropdown">
                <button class="btn btn-dark dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <?php echo $current_sort; ?>
                </button>
                <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end">
                    <li><a class="dropdown-item" href="?sort=new<?php echo $category_param; ?><?php echo $brand_param; ?><?php echo $search_param; ?>">Newest</a></li>
                    <li><a class="dropdown-item" href="?sort=price_asc<?php echo $category_param; ?><?php echo $brand_param; ?><?php echo $search_param; ?>">Price: Low to High</a></li>
                    <li><a class="dropdown-item" href="?sort=price_desc<?php echo $category_param; ?><?php echo $brand_param; ?><?php echo $search_param; ?>">Price: High to Low</a></li>
                    <li><a class="dropdown-item" href="?sort=name_asc<?php echo $category_param; ?><?php echo $brand_param; ?><?php echo $search_param; ?>">Name: A to Z</a></li>
                    <li><a class="dropdown-item" href="?sort=name_desc<?php echo $category_param; ?><?php echo $brand_param; ?><?php echo $search_param; ?>">Name: Z to A</a></li>
                </ul>
            </div>
        </div>

        <!-- Product Grid -->
        <div class="row g-4">
            <?php if ($products_rs->num_rows > 0): ?>
                <?php while ($product = $products_rs->fetch_assoc()): ?>
                    <div class="col-md-4 col-lg-3">
                        <div class="product-card">
                            <div class="product-card-img-container">
                                <a href="single_product.php?id=<?php echo $product['id']; ?>">
                                    <img src="<?php echo htmlspecialchars($product['img_url'] ?? 'imgs/placeholder.png'); ?>" class="product-card-img" alt="<?php echo htmlspecialchars($product['title']); ?>">
                                </a>
                            </div>
                            <div class="product-card-body">
                                <a href="single_product.php?id=<?php echo $product['id']; ?>" class="product-card-title mb-2">
                                    <?php echo htmlspecialchars($product['title']); ?>
                                </a>
                                <span class="product-card-price">LKR <?php echo number_format($product['price'], 2); ?></span>
                                
                                <div class="product-card-actions">
                                    <div class="d-flex gap-2">
                                        <!-- Add to Cart / Buy Now -->
                                        <?php if ($product['qty'] > 0): ?>
                                            <button class="btn btn-primary fw-bold btn-buy-now" 
                                                    onclick="handleCartAction(<?php echo $product['id']; ?>, true)">
                                                Buy Now
                                            </button>
                                            <button class="btn btn-outline-primary" 
                                                    id="cart-btn-<?php echo $product['id']; ?>"
                                                    onclick="handleCartAction(<?php echo $product['id']; ?>, false)"
                                                    title="Add to Cart">
                                                <i class="fas <?php echo ($is_logged_in && $product['in_cart'] > 0) ? 'fa-check' : 'fa-shopping-cart'; ?>"></i>
                                            </button>
                                        <?php else: ?>
                                            <button class="btn btn-secondary disabled w-100">Out of Stock</button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <!-- No Products Found -->
                <div class="col-12">
                    <div class="no-products-container">
                        <i class="fas fa-search no-products-icon mb-3"></i>
                        <h2 class="text-white fw-bold">No Products Found</h2>
                        <p class="text-light opacity-75 fs-5">
                            <?php if ($is_search): ?>
                                We couldn't find any products matching your search "<?php echo htmlspecialchars($_GET['search']); ?>".
                            <?php else: ?>
                                We couldn't find any products with the selected filters.
                            <?php endif; ?>
                        </p>
                        <a href="shop.php" class="btn btn-primary btn-lg fw-bold mt-3">
                            <i class="fas fa-undo me-2"></i> Clear Filters
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <nav aria-label="Page navigation" class="mt-5 d-flex justify-content-center">
                <ul class="pagination pagination-lg">
                    <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $page - 1; ?><?php echo $search_param; ?><?php echo $category_param; ?><?php echo $brand_param; ?><?php echo $sort_param; ?>" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                    <?php endif; ?>
                    
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?><?php echo $search_param; ?><?php echo $category_param; ?><?php echo $brand_param; ?><?php echo $sort_param; ?>">
                                <?php echo $i; ?>
                            </a>
                        </li>
                    <?php endfor; ?>
                    
                    <?php if ($page < $total_pages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $page + 1; ?><?php echo $search_param; ?><?php echo $category_param; ?><?php echo $brand_param; ?><?php echo $sort_param; ?>" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
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
            <button typetype="button" class="btn btn-primary" id="modalActionBtn" style="display: none;">Login</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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
        const actionModal = new bootstrap.Modal(document.getElementById('actionModal'));
        const modalBody = document.getElementById('modalBody');
        const modalActionBtn = document.getElementById('modalActionBtn');

        function showModal(message, showLoginBtn = false) {
            modalBody.textContent = message;
            if (showLoginBtn) {
                modalActionBtn.style.display = 'block';
                modalActionBtn.onclick = () => { window.location.href = 'login-signup.php'; };
            } else {
                modalActionBtn.style.display = 'none';
            }
            actionModal.show();
        }

        async function handleCartAction(productId, isBuyNow = false) {
            <?php if (!$is_logged_in): ?>
                showModal('Please log in to add items to your cart.', true);
                return;
            <?php endif; ?>

            const formData = new FormData();
            formData.append('product_id', productId);
            formData.append('qty', 1); // Default qty for shop page
            if (isBuyNow) {
                formData.append('buy_now', 1);
            }

            try {
                const response = await fetch('cart_process.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();

                if (result.status === 'success') {
                    if (isBuyNow) {
                        window.location.href = 'cart.php'; // Or checkout.php
                    } else {
                        // Update cart button icon
                        const cartBtn = document.getElementById(`cart-btn-${productId}`);
                        if (cartBtn) {
                            cartBtn.innerHTML = '<i class="fas fa-check"></i>';
                        }
                        showModal(result.message);
                        // Update header cart count
                        updateHeaderCartCount(result.cart_count);
                    }
                } else {
                    showModal(result.message);
                }
            } catch (error) {
                showModal('A connection error occurred.');
            }
        }
        
        function updateHeaderCartCount(cartCount) {
             const desktopBadge = document.getElementById('header-cart-count-desktop');
            const mobileBadge = document.getElementById('header-cart-count-mobile');
            
            if (cartCount > 0) {
                if (desktopBadge) {
                    desktopBadge.textContent = cartCount;
                    desktopBadge.style.display = 'flex';
                }
                if (mobileBadge) {
                    mobileBadge.textContent = cartCount;
                    mobileBadge.style.display = 'flex';
                }
            } else {
                if (desktopBadge) {
                    desktopBadge.textContent = '0';
                    desktopBadge.style.display = 'none';
                }
                if (mobileBadge) {
                    mobileBadge.textContent = '0';
                    mobileBadge.style.display = 'none';
                }
            }
        }
    </script>

</body>
</html>

