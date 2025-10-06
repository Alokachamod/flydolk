<?php
require_once 'connection.php';

// Get product ID from URL
$productId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($productId <= 0) {
    header('Location: shop.php');
    exit;
}

// Fetch product details
$productQuery = "
    SELECT 
        p.id, 
        p.title, 
        p.description,
        p.price, 
        p.qty, 
        p.category_id, 
        p.brand_id,
        c.name AS category_name, 
        b.name AS brand_name
    FROM product p
    LEFT JOIN category c ON c.id = p.category_id
    LEFT JOIN brand b ON b.id = p.brand_id
    WHERE p.id = {$productId}
";

$productResult = Database::search($productQuery);

if (!$productResult || $productResult->num_rows == 0) {
    header('Location: shop.php');
    exit;
}

$product = $productResult->fetch_assoc();

// Fetch product images
$imagesQuery = "SELECT img_url FROM product_img WHERE product_id = {$productId}";
$imagesResult = Database::search($imagesQuery);
$images = [];
if ($imagesResult && $imagesResult->num_rows > 0) {
    while ($img = $imagesResult->fetch_assoc()) {
        $images[] = $img['img_url'];
    }
}
if (empty($images)) {
    $images[] = 'imgs/no-image.png';
}

// Fetch product colors
$colorsQuery = "
    SELECT c.id, c.name 
    FROM color c
    INNER JOIN product_has_color phc ON phc.color_id = c.id
    WHERE phc.product_id = {$productId}
";
$colorsResult = Database::search($colorsQuery);
$colors = [];
if ($colorsResult && $colorsResult->num_rows > 0) {
    while ($color = $colorsResult->fetch_assoc()) {
        $colors[] = $color;
    }
}

// Fetch related products (same category, different product)
$relatedQuery = "
    SELECT 
        p.id, 
        p.title, 
        p.price,
        (SELECT img_url FROM product_img WHERE product_id = p.id LIMIT 1) AS img_url
    FROM product p
    WHERE p.category_id = {$product['category_id']} 
    AND p.id != {$productId}
    ORDER BY RAND()
    LIMIT 4
";
$relatedResult = Database::search($relatedQuery);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($product['title']) ?> - Flydolk</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <link rel="stylesheet" href="style.css">
    
    <style>
        .product-detail {
            padding-top: calc(var(--header-h, 96px) + 40px);
            padding-bottom: 60px;
            min-height: 100vh;
        }
        
        .breadcrumb {
            background: transparent;
            padding: 0;
            margin-bottom: 2rem;
            opacity: 0;
            transform: translateY(-20px);
        }
        
        .breadcrumb-item a {
            color: var(--fd-accent);
            text-decoration: none;
        }
        
        .breadcrumb-item.active {
            color: var(--fd-dim);
        }
        
        /* Image Gallery */
        .gallery-container {
            opacity: 0;
            transform: translateX(-30px);
        }
        
        .main-image-wrapper {
            position: relative;
            background: #f8fafc;
            border-radius: 20px;
            overflow: hidden;
            aspect-ratio: 1/1;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }
        
        .main-image {
            width: 100%;
            height: 100%;
            object-fit: contain;
            padding: 2rem;
        }
        
        .zoom-badge {
            position: absolute;
            top: 20px;
            right: 20px;
            background: rgba(255,255,255,0.95);
            border-radius: 10px;
            padding: 0.5rem 1rem;
            font-size: 0.85rem;
            color: #64748b;
            border: 1px solid #e2e8f0;
        }
        
        .thumbnail-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
            gap: 0.75rem;
            margin-top: 1rem;
        }
        
        .thumbnail {
            aspect-ratio: 1/1;
            background: #f8fafc;
            border-radius: 12px;
            border: 2px solid #e2e8f0;
            overflow: hidden;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .thumbnail:hover {
            border-color: var(--fd-accent);
            transform: translateY(-2px);
        }
        
        .thumbnail.active {
            border-color: var(--fd-accent);
            box-shadow: 0 0 0 3px rgba(13, 177, 253, 0.2);
        }
        
        .thumbnail img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            padding: 0.5rem;
        }
        
        /* Product Info */
        .product-info {
            opacity: 0;
            transform: translateX(30px);
        }
        
        .product-category {
            color: var(--fd-accent);
            font-size: 0.9rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 0.5rem;
        }
        
        .product-title {
            font-size: 2.5rem;
            font-weight: 800;
            color: #0b2239;
            line-height: 1.2;
            margin-bottom: 1rem;
        }
        
        .product-price {
            font-size: 2.5rem;
            font-weight: 800;
            color: #0b3d60;
            margin-bottom: 1.5rem;
        }
        
        .stock-status {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 1.5rem;
        }
        
        .stock-status.in-stock {
            background: #d1fae5;
            color: #065f46;
        }
        
        .stock-status.low-stock {
            background: #fef3c7;
            color: #92400e;
        }
        
        .stock-status.out-of-stock {
            background: #fee2e2;
            color: #991b1b;
        }
        
        .stock-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            animation: pulse 2s infinite;
        }
        
        .in-stock .stock-dot {
            background: #10b981;
        }
        
        .low-stock .stock-dot {
            background: #f59e0b;
        }
        
        .out-of-stock .stock-dot {
            background: #ef4444;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        
        .color-selector {
            margin-bottom: 1.5rem;
        }
        
        .color-title {
            font-weight: 700;
            color: #0b2239;
            margin-bottom: 0.75rem;
            font-size: 0.95rem;
        }
        
        .color-options {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
        }
        
        .color-option {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            border: 2px solid #e2e8f0;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8fafc;
            font-size: 0.75rem;
            font-weight: 600;
            color: #64748b;
        }
        
        .color-option:hover {
            border-color: var(--fd-accent);
            transform: translateY(-2px);
        }
        
        .color-option.active {
            border-color: var(--fd-accent);
            box-shadow: 0 0 0 3px rgba(13, 177, 253, 0.2);
        }
        
        .color-option.active::after {
            content: '\f00c';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            position: absolute;
            top: -6px;
            right: -6px;
            background: var(--fd-accent);
            color: #fff;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            font-size: 0.6rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .quantity-selector {
            margin-bottom: 1.5rem;
        }
        
        .qty-controls {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .qty-btn {
            width: 44px;
            height: 44px;
            border: 1px solid #e2e8f0;
            background: #fff;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 1.2rem;
            color: #64748b;
        }
        
        .qty-btn:hover {
            border-color: var(--fd-accent);
            background: var(--fd-accent);
            color: #fff;
        }
        
        .qty-display {
            min-width: 60px;
            height: 44px;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.1rem;
            background: #f8fafc;
        }
        
        .action-buttons {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .btn-add-cart {
            flex: 1;
            background: var(--fd-accent);
            color: #061018;
            border: none;
            padding: 1rem 2rem;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
        }
        
        .btn-add-cart:hover {
            background: #0c9ce0;
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(13, 177, 253, 0.3);
            color: #061018;
        }
        
        .btn-wishlist-large {
            width: 60px;
            height: 60px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            color: #64748b;
            transition: all 0.3s ease;
        }
        
        .btn-wishlist-large:hover {
            background: #fee;
            color: #e11d48;
            border-color: #fecdd3;
        }
        
        .product-meta {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            padding: 1.5rem;
            background: #f8fafc;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
        }
        
        .meta-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: #64748b;
            font-size: 0.9rem;
        }
        
        .meta-item i {
            color: var(--fd-accent);
            width: 20px;
        }
        
        .meta-item strong {
            color: #0b2239;
        }
        
        /* Description Section */
        .description-section {
            margin-top: 4rem;
            opacity: 0;
            transform: translateY(30px);
        }
        
        .section-title {
            font-size: 1.8rem;
            font-weight: 800;
            color: #0b2239;
            margin-bottom: 1.5rem;
        }
        
        .description-content {
            background: #fff;
            padding: 2rem;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            line-height: 1.8;
            color: #475569;
        }
        
        /* Related Products */
        .related-section {
            margin-top: 4rem;
            opacity: 0;
            transform: translateY(30px);
        }
        
        .related-product-card {
            background: #fff;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            overflow: hidden;
            transition: all 0.3s ease;
            height: 100%;
        }
        
        .related-product-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 30px rgba(13, 177, 253, 0.15);
            border-color: var(--fd-accent);
        }
        
        .related-img-wrapper {
            aspect-ratio: 1/1;
            background: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .related-img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            padding: 1rem;
        }
        
        .related-body {
            padding: 1rem;
        }
        
        .related-title {
            font-weight: 700;
            color: #0b2239;
            margin-bottom: 0.5rem;
            font-size: 1rem;
        }
        
        .related-price {
            font-weight: 800;
            color: #0b3d60;
            font-size: 1.2rem;
        }
        
        @media (max-width: 991.98px) {
            .product-title {
                font-size: 2rem;
            }
            
            .product-price {
                font-size: 2rem;
            }
            
            .gallery-container,
            .product-info {
                transform: none;
            }
        }
    </style>
</head>

<body>
    <?php include 'header.php'; ?>
    
    <main class="product-detail">
        <div class="container">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb" id="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item"><a href="shop.php">Shop</a></li>
                    <?php if ($product['category_name']): ?>
                    <li class="breadcrumb-item"><a href="categories.php?c=<?= urlencode(strtolower($product['category_name'])) ?>"><?= htmlspecialchars($product['category_name']) ?></a></li>
                    <?php endif; ?>
                    <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($product['title']) ?></li>
                </ol>
            </nav>
            
            <div class="row g-5">
                <!-- Gallery -->
                <div class="col-lg-6">
                    <div class="gallery-container" id="gallery">
                        <div class="main-image-wrapper">
                            <img src="<?= htmlspecialchars($images[0]) ?>" alt="<?= htmlspecialchars($product['title']) ?>" class="main-image" id="mainImage">
                            <div class="zoom-badge">
                                <i class="fa-solid fa-magnifying-glass-plus me-2"></i>Click to zoom
                            </div>
                        </div>
                        
                        <?php if (count($images) > 1): ?>
                        <div class="thumbnail-grid">
                            <?php foreach ($images as $index => $img): ?>
                            <div class="thumbnail <?= $index === 0 ? 'active' : '' ?>" onclick="changeImage('<?= htmlspecialchars($img) ?>', this)">
                                <img src="<?= htmlspecialchars($img) ?>" alt="Thumbnail <?= $index + 1 ?>">
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Product Info -->
                <div class="col-lg-6">
                    <div class="product-info" id="productInfo">
                        <div class="product-category">
                            <?= htmlspecialchars($product['category_name'] ?? 'Drones') ?>
                            <?php if ($product['brand_name']): ?>
                                • <?= htmlspecialchars($product['brand_name']) ?>
                            <?php endif; ?>
                        </div>
                        
                        <h1 class="product-title"><?= htmlspecialchars($product['title']) ?></h1>
                        
                        <div class="product-price">LKR <?= number_format($product['price'], 0, '.', ',') ?></div>
                        
                        <?php
                        $qty = (int)$product['qty'];
                        if ($qty > 10) {
                            $statusClass = 'in-stock';
                            $statusText = 'In Stock';
                        } elseif ($qty > 0) {
                            $statusClass = 'low-stock';
                            $statusText = "Only {$qty} left";
                        } else {
                            $statusClass = 'out-of-stock';
                            $statusText = 'Out of Stock';
                        }
                        ?>
                        
                        <div class="stock-status <?= $statusClass ?>">
                            <span class="stock-dot"></span>
                            <?= $statusText ?>
                        </div>
                        
                        <?php if (!empty($colors)): ?>
                        <div class="color-selector">
                            <div class="color-title">Color:</div>
                            <div class="color-options">
                                <?php foreach ($colors as $index => $color): ?>
                                <div class="color-option <?= $index === 0 ? 'active' : '' ?>" 
                                     onclick="selectColor(this)"
                                     title="<?= htmlspecialchars($color['name']) ?>">
                                    <?= strtoupper(substr($color['name'], 0, 2)) ?>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($qty > 0): ?>
                        <div class="quantity-selector">
                            <div class="color-title">Quantity:</div>
                            <div class="qty-controls">
                                <button class="qty-btn" onclick="changeQty(-1)">
                                    <i class="fa-solid fa-minus"></i>
                                </button>
                                <div class="qty-display" id="qtyDisplay">1</div>
                                <button class="qty-btn" onclick="changeQty(1)">
                                    <i class="fa-solid fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="action-buttons">
                            <button class="btn-add-cart" onclick="addToCart()">
                                <i class="fa-solid fa-cart-plus"></i>
                                Add to Cart
                            </button>
                            <button class="btn-wishlist-large">
                                <i class="fa-regular fa-heart"></i>
                            </button>
                        </div>
                        <?php endif; ?>
                        
                        <div class="product-meta">
                            <div class="meta-item">
                                <i class="fa-solid fa-truck-fast"></i>
                                <span><strong>Free shipping</strong> on orders over LKR 100,000</span>
                            </div>
                            <div class="meta-item">
                                <i class="fa-solid fa-shield-halved"></i>
                                <span><strong>1 Year</strong> warranty included</span>
                            </div>
                            <div class="meta-item">
                                <i class="fa-solid fa-rotate-left"></i>
                                <span><strong>30 Days</strong> return policy</span>
                            </div>
                            <div class="meta-item">
                                <i class="fa-solid fa-headset"></i>
                                <span><strong>24/7</strong> customer support</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Description -->
            <div class="description-section" id="descSection">
                <h2 class="section-title">Product Description</h2>
                <div class="description-content">
                    <?php 
                    $desc = $product['description'] ?? 'No description available for this product.';
                    echo $desc;
                    ?>
                </div>
            </div>
            
            <!-- Related Products -->
            <?php if ($relatedResult && $relatedResult->num_rows > 0): ?>
            <div class="related-section" id="relatedSection">
                <h2 class="section-title">You May Also Like</h2>
                <div class="row g-4">
                    <?php while ($related = $relatedResult->fetch_assoc()): ?>
                    <div class="col-6 col-md-3">
                        <a href="product.php?id=<?= $related['id'] ?>" class="text-decoration-none">
                            <div class="related-product-card">
                                <div class="related-img-wrapper">
                                    <img src="<?= htmlspecialchars($related['img_url'] ?? 'imgs/no-image.png') ?>" 
                                         alt="<?= htmlspecialchars($related['title']) ?>" 
                                         class="related-img">
                                </div>
                                <div class="related-body">
                                    <div class="related-title"><?= htmlspecialchars($related['title']) ?></div>
                                    <div class="related-price">LKR <?= number_format($related['price'], 0, '.', ',') ?></div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </main>
    
    <?php include 'footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/ScrollTrigger.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="script.js"></script>
    
    <script>
        gsap.registerPlugin(ScrollTrigger);
        
        // Animations
        gsap.to('#breadcrumb', {
            opacity: 1,
            y: 0,
            duration: 0.6,
            ease: 'power2.out'
        });
        
        gsap.to('#gallery', {
            opacity: 1,
            x: 0,
            duration: 0.8,
            delay: 0.2,
            ease: 'power3.out'
        });
        
        gsap.to('#productInfo', {
            opacity: 1,
            x: 0,
            duration: 0.8,
            delay: 0.4,
            ease: 'power3.out'
        });
        
        gsap.to('#descSection', {
            opacity: 1,
            y: 0,
            duration: 0.8,
            scrollTrigger: {
                trigger: '#descSection',
                start: 'top 80%'
            }
        });
        
        gsap.to('#relatedSection', {
            opacity: 1,
            y: 0,
            duration: 0.8,
            scrollTrigger: {
                trigger: '#relatedSection',
                start: 'top 80%'
            }
        });
        
        // Image gallery
        function changeImage(src, thumbnail) {
            document.getElementById('mainImage').src = src;
            document.querySelectorAll('.thumbnail').forEach(t => t.classList.remove('active'));
            thumbnail.classList.add('active');
        }
        
        // Color selection
        function selectColor(element) {
            document.querySelectorAll('.color-option').forEach(c => c.classList.remove('active'));
            element.classList.add('active');
        }
        
        // Quantity control
        let quantity = 1;
        const maxQty = <?= $qty ?>;
        
        function changeQty(delta) {
            quantity = Math.max(1, Math.min(maxQty, quantity + delta));
            document.getElementById('qtyDisplay').textContent = quantity;
        }
        
        // Add to cart
        function addToCart() {
            const productId = <?= $productId ?>;
            const selectedColor = document.querySelector('.color-option.active')?.textContent || 'default';
            
            Swal.fire({
                icon: 'success',
                title: 'Added to Cart!',
                text: `${quantity} item(s) added to your cart`,
                showConfirmButton: true,
                confirmButtonText: 'View Cart',
                showCancelButton: true,
                cancelButtonText: 'Continue Shopping',
                confirmButtonColor: '#0db1fd'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'cart.php';
                }
            });
            
            // TODO: Implement actual cart functionality
            console.log('Add to cart:', {
                productId: productId,
                quantity: quantity,
                color: selectedColor
            });
        }
    </script>
</body>
</html>