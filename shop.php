<?php
// We need the database connection for everything
require_once 'connection.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// --- Pagination Logic ---
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$products_per_page = 12; // 12 products per page (4 cols x 3 rows)
$offset = ($page - 1) * $products_per_page;

// --- Filter & Sort Logic ---
$category_filter = isset($_GET['category']) && is_numeric($_GET['category']) ? (int)$_GET['category'] : 0;
$brand_filter = isset($_GET['brand']) && is_numeric($_GET['brand']) ? (int)$_GET['brand'] : 0;
$sort_order = isset($_GET['sort']) ? $_GET['sort'] : 'newest';

// Build WHERE clauses for filtering
$where_clauses = ["p.product_status_id = 1"]; // Only show active products
if ($category_filter > 0) {
    $where_clauses[] = "p.category_id = $category_filter";
}
if ($brand_filter > 0) {
    $where_clauses[] = "p.brand_id = $brand_filter";
}
$sql_where = "WHERE " . implode(" AND ", $where_clauses);

// Build ORDER BY for sorting
$sql_order = "";
switch ($sort_order) {
    case 'price_asc':
        $sql_order = "ORDER BY p.price ASC";
        break;
    case 'price_desc':
        $sql_order = "ORDER BY p.price DESC";
        break;
    case 'name_asc':
        $sql_order = "ORDER BY p.title ASC";
        break;
    case 'name_desc':
        $sql_order = "ORDER BY p.title DESC";
        break;
    default:
        $sql_order = "ORDER BY p.create_at DESC"; // Assumes 'create_at' from your schema
}

// --- Data Fetching ---
$products = [];
$total_products = 0;
$total_pages = 1;

try {
    // --- Get Total Products for Pagination ---
    $sql_count = "SELECT COUNT(DISTINCT p.id) as total FROM product p $sql_where";
    $count_rs = Database::search($sql_count);
    if ($count_rs) {
        $total_products = (int)$count_rs->fetch_assoc()['total'];
        $total_pages = ceil($total_products / $products_per_page);
    }

    // --- Get Products for Current Page ---
    // This query JOINS product with product_img
    // It uses a subquery to get only the *first* (or MIN) image URL for each product
    $sql_products = "
        SELECT 
            p.id, 
            p.title, 
            p.price, 
            p.description,
            (SELECT img_url FROM product_img pi WHERE pi.product_id = p.id ORDER BY id ASC LIMIT 1) as img_url
        FROM product p
        $sql_where
        $sql_order
        LIMIT $products_per_page
        OFFSET $offset
    ";
    
    $products_rs = Database::search($sql_products);

    // --- Get Categories & Brands for Filters ---
    $categories_rs = Database::search("SELECT id, name FROM category ORDER BY name ASC");
    $brands_rs = Database::search("SELECT id, name FROM brand ORDER BY name ASC");

} catch (Exception $e) {
    // Handle database errors gracefully
    // error_log($e->getMessage());
    $error_message = "Could not load shop data. Please try again later.";
}

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
    <link rel="stylesheet" href="style.css">

    <!-- Custom CSS (Copied from index.php for consistency) -->
    <style>
        /* --- Base & Dark Theme --- */
        body {
            font-family: 'Inter', sans-serif;
            background-color: #0f172a; /* bg-slate-900 */
            color: #f1f5f9; /* text-gray-100 */
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
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
        .section-py {
            padding-top: 5rem;
            padding-bottom: 5rem;
        }
        
        /* Product Card */
        .product-card {
            background-color: #1e293b; /* bg-slate-800 */
            border: none;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }
        .product-card:hover {
            transform: scale(1.05);
            box-shadow: 0 0 20px -5px rgba(96, 165, 250, 0.3); /* hover:shadow-blue-500/20 */
        }
        .product-card .card-img-top {
            height: 12rem; /* h-48 */
            object-fit: cover;
        }
        .product-card .btn-view {
            opacity: 0;
            transform: translateX(0.5rem);
            transition: all 0.3s ease;
        }
        .product-card:hover .btn-view {
            opacity: 1;
            transform: translateX(0);
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
            padding: 1rem;
            border-radius: 0.5rem;
        }
        .filter-bar .form-select,
        .filter-bar .btn {
            background-color: #334155; /* slate-700 */
            color: #f1f5f9;
            border: 1px solid #475569; /* slate-600 */
        }
        .filter-bar .form-select:focus {
            background-color: #334155;
            color: #f1f5f9;
            border-color: #60a5fa; /* text-blue-400 */
            box-shadow: 0 0 0 0.25rem rgba(96, 165, 250, 0.25);
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
            background-color: #1e293b;
            color: #475569;
            border-color: #334155;
        }
        .pagination .page-item:not(.active) .page-link:hover {
            background-color: #334155;
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
                <p class="fs-5 text-light opacity-75">Find the perfect drone for your mission.</p>
            </div>
        </section>

        <!-- Filters & Products Section -->
        <section class="section-py">
            <div class="container">
                
                <?php if (isset($error_message)): ?>
                    <div class="alert alert-danger"><?php echo $error_message; ?></div>
                <?php else: ?>

                    <!-- Filter Bar -->
                    <form action="shop.php" method="GET" class="filter-bar mb-4">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-3">
                                <label for="category" class="form-label small">Category</label>
                                <select name="category" id="category" class="form-select">
                                    <option value="0" <?php echo ($category_filter == 0) ? 'selected' : ''; ?>>All Categories</option>
                                    <?php while ($cat = $categories_rs->fetch_assoc()): ?>
                                        <option value="<?php echo $cat['id']; ?>" <?php echo ($category_filter == $cat['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($cat['name']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="brand" class="form-label small">Brand</label>
                                <select name="brand" id="brand" class="form-select">
                                    <option value="0" <?php echo ($brand_filter == 0) ? 'selected' : ''; ?>>All Brands</option>
                                    <?php while ($brand = $brands_rs->fetch_assoc()): ?>
                                        <option value="<?php echo $brand['id']; ?>" <?php echo ($brand_filter == $brand['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($brand['name']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="sort" class="form-label small">Sort By</label>
                                <select name="sort" id="sort" class="form-select">
                                    <option value="newest" <?php echo ($sort_order == 'newest') ? 'selected' : ''; ?>>Newest</option>
                                    <option value="price_asc" <?php echo ($sort_order == 'price_asc') ? 'selected' : ''; ?>>Price: Low to High</option>
                                    <option value="price_desc" <?php echo ($sort_order == 'price_desc') ? 'selected' : ''; ?>>Price: High to Low</option>
                                    <option value="name_asc" <?php echo ($sort_order == 'name_asc') ? 'selected' : ''; ?>>Name: A to Z</option>
                                    <option value="name_desc" <?php echo ($sort_order == 'name_desc') ? 'selected' : ''; ?>>Name: Z to A</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
                            </div>
                        </div>
                    </form>

                    <!-- Product Grid -->
                    <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-4">
                        <?php if ($products_rs && $products_rs->num_rows > 0): ?>
                            <?php while ($product = $products_rs->fetch_assoc()): ?>
                                <!-- Dynamic Product Card -->
                                <div class="col">
                                    <div class="card product-card h-100 rounded-3 overflow-hidden">
                                        <a href="product_details.php?id=<?php echo htmlspecialchars($product['id']); ?>" class="text-decoration-none">
                                            
                                            <?php 
                                            // Use a placeholder if img_url is null or empty
                                            $image_url = $product['img_url'] ?? 'https://placehold.co/600x400/1e293b/f1f5f9?text=No+Image';
                                            ?>
                                            <img src="<?php echo htmlspecialchars($image_url); ?>" 
                                                 class="card-img-top"
                                                 alt="<?php echo htmlspecialchars($product['title']); ?>" 
                                                 onerror="this.src='https://placehold.co/600x400/1e293b/f1f5f9?text=Image+Error'">
                                            
                                            <div class="card-body p-4">
                                                <h3 class="fs-5 fw-bold text-white mb-2"><?php echo htmlspecialchars($product['title']); ?></h3>
                                                
                                                <p class="text-light opacity-75 mb-3 small">
                                                    <?php echo htmlspecialchars(substr($product['description'], 0, 75)) . '...'; ?>
                                                </p>
                                                
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <span class="fs-4 fw-bold text-blue-400">$<?php echo number_format($product['price'], 2); ?></span>
                                                    <span class="btn btn-primary btn-sm btn-view fw-semibold">
                                                        View
                                                    </span>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                                <!-- End Dynamic Product Card -->
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="col-12">
                                <p class="text-light opacity-75 text-center fs-5">No products found matching your criteria.</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Pagination -->
                    <?php if ($total_pages > 1): ?>
                        <nav aria-label="Product pagination" class="mt-5 d-flex justify-content-center">
                            <ul class="pagination">
                                <?php
                                // --- Pagination Link Logic ---
                                // This preserves the other filters when changing pages
                                $query_params = $_GET;
                                
                                // "Previous" Button
                                $prev_page = $page - 1;
                                $query_params['page'] = $prev_page;
                                $prev_href = 'shop.php?' . http_build_query($query_params);
                                $prev_disabled = ($page <= 1) ? 'disabled' : '';
                                ?>
                                <li class="page-item <?php echo $prev_disabled; ?>">
                                    <a class="page-link" href="<?php echo $prev_href; ?>" aria-label="Previous">
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
                                </li>

                                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                    <?php
                                    $query_params['page'] = $i;
                                    $page_href = 'shop.php?' . http_build_query($query_params);
                                    $active_class = ($i == $page) ? 'active' : '';
                                    ?>
                                    <li class="page-item <?php echo $active_class; ?>">
                                        <a class="page-link" href="<?php echo $page_href; ?>"><?php echo $i; ?></a>
                                    </li>
                                <?php endfor; ?>

                                <?php
                                // "Next" Button
                                $next_page = $page + 1;
                                $query_params['page'] = $next_page;
                                $next_href = 'shop.php?' . http_build_query($query_params);
                                $next_disabled = ($page >= $total_pages) ? 'disabled' : '';
                                ?>
                                <li class="page-item <?php echo $next_disabled; ?>">
                                    <a class="page-link" href="<?php echo $next_href; ?>" aria-label="Next">
                                        <span aria-hidden="true">&raquo;</span>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    <?php endif; ?>
                
                <?php endif; // End error message check ?>

            </div>
        </section>

    </main>

    <?php include 'footer.php'; ?>


    <!-- Bootstrap JS Bundle (includes Popper) -->
     <script src="script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" xintegrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

</body>
</html>
