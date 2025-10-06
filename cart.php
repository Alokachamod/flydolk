<?php
require_once 'connection.php';
session_start();

// For now, we'll use session-based cart
// Later, this can be moved to database for logged-in users
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$cartItems = $_SESSION['cart'];
$cartTotal = 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Shopping Cart - Flydolk</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <link rel="stylesheet" href="style.css">
    
    <style>
        .cart-page {
            padding-top: calc(var(--header-h, 96px) + 40px);
            padding-bottom: 60px;
            min-height: 100vh;
            background: #f8fafc;
        }
        
        .page-header {
            text-align: center;
            margin-bottom: 3rem;
            opacity: 0;
            transform: translateY(-20px);
        }
        
        .page-title {
            font-size: 2.5rem;
            font-weight: 800;
            color: #0b2239;
            margin-bottom: 0.5rem;
        }
        
        .page-subtitle {
            color: var(--fd-dim);
            font-size: 1.1rem;
        }
        
        .cart-container {
            opacity: 0;
            transform: translateY(30px);
        }
        
        .cart-items-section {
            background: #fff;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .cart-item {
            display: flex;
            gap: 1.5rem;
            padding: 1.5rem;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            margin-bottom: 1rem;
            background: #fff;
            transition: all 0.3s ease;
        }
        
        .cart-item:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            border-color: var(--fd-accent);
        }
        
        .item-image {
            width: 120px;
            height: 120px;
            flex-shrink: 0;
            background: #f8fafc;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        
        .item-image img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            padding: 0.5rem;
        }
        
        .item-details {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        
        .item-title {
            font-weight: 700;
            color: #0b2239;
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
        }
        
        .item-title a {
            color: #0b2239;
            text-decoration: none;
        }
        
        .item-title a:hover {
            color: var(--fd-accent);
        }
        
        .item-meta {
            color: var(--fd-dim);
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }
        
        .item-color {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .color-dot {
            width: 16px;
            height: 16px;
            border-radius: 50%;
            border: 1px solid #e2e8f0;
        }
        
        .item-controls {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .qty-controls {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .qty-btn {
            width: 36px;
            height: 36px;
            border: 1px solid #e2e8f0;
            background: #fff;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
            color: #64748b;
        }
        
        .qty-btn:hover {
            border-color: var(--fd-accent);
            background: var(--fd-accent);
            color: #fff;
        }
        
        .qty-display {
            min-width: 50px;
            text-align: center;
            font-weight: 700;
            color: #0b2239;
        }
        
        .item-price-section {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .item-price {
            font-size: 1.3rem;
            font-weight: 800;
            color: #0b3d60;
        }
        
        .btn-remove {
            width: 36px;
            height: 36px;
            border: 1px solid #fee2e2;
            background: #fef2f2;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #dc2626;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .btn-remove:hover {
            background: #fee2e2;
            border-color: #dc2626;
        }
        
        .empty-cart {
            text-align: center;
            padding: 4rem 2rem;
            background: #fff;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
        }
        
        .empty-cart i {
            font-size: 5rem;
            color: #cbd5e1;
            margin-bottom: 1.5rem;
        }
        
        .empty-cart h3 {
            color: #0b2239;
            margin-bottom: 1rem;
        }
        
        .empty-cart p {
            color: var(--fd-dim);
            margin-bottom: 2rem;
        }
        
        .cart-summary {
            background: #fff;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            padding: 2rem;
            position: sticky;
            top: calc(var(--header-h, 96px) + 20px);
            opacity: 0;
            transform: translateX(30px);
        }
        
        .summary-title {
            font-size: 1.5rem;
            font-weight: 800;
            color: #0b2239;
            margin-bottom: 1.5rem;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .summary-row:last-of-type {
            border-bottom: none;
        }
        
        .summary-label {
            color: #64748b;
            font-size: 0.95rem;
        }
        
        .summary-value {
            font-weight: 700;
            color: #0b2239;
        }
        
        .summary-total {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.5rem 0;
            margin-top: 1rem;
            border-top: 2px solid #e2e8f0;
        }
        
        .total-label {
            font-size: 1.2rem;
            font-weight: 700;
            color: #0b2239;
        }
        
        .total-value {
            font-size: 1.8rem;
            font-weight: 800;
            color: #0b3d60;
        }
        
        .btn-checkout {
            width: 100%;
            background: var(--fd-accent);
            color: #061018;
            border: none;
            padding: 1rem;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1.1rem;
            margin-top: 1rem;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
        }
        
        .btn-checkout:hover {
            background: #0c9ce0;
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(13, 177, 253, 0.3);
            color: #061018;
        }
        
        .btn-continue {
            width: 100%;
            background: #f8fafc;
            color: #64748b;
            border: 1px solid #e2e8f0;
            padding: 0.75rem;
            border-radius: 12px;
            font-weight: 600;
            margin-top: 0.75rem;
            transition: all 0.2s ease;
        }
        
        .btn-continue:hover {
            background: #fff;
            border-color: var(--fd-accent);
            color: var(--fd-accent);
        }
        
        .promo-section {
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e2e8f0;
        }
        
        .promo-input-group {
            display: flex;
            gap: 0.5rem;
        }
        
        .promo-input {
            flex: 1;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 0.75rem;
        }
        
        .promo-input:focus {
            outline: none;
            border-color: var(--fd-accent);
        }
        
        .btn-apply {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            font-weight: 600;
            color: #64748b;
            transition: all 0.2s ease;
        }
        
        .btn-apply:hover {
            background: var(--fd-accent);
            border-color: var(--fd-accent);
            color: #061018;
        }
        
        .security-badge {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1rem;
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 10px;
            margin-top: 1.5rem;
            color: #166534;
            font-size: 0.9rem;
        }
        
        .security-badge i {
            font-size: 1.5rem;
            color: #22c55e;
        }
        
        @media (max-width: 991.98px) {
            .page-title {
                font-size: 2rem;
            }
            
            .cart-item {
                flex-direction: column;
            }
            
            .item-image {
                width: 100%;
                height: 200px;
            }
            
            .cart-summary {
                position: static;
                transform: none;
            }
        }
        
        @media (max-width: 575.98px) {
            .item-controls {
                flex-direction: column;
                align-items: stretch;
            }
            
            .item-price-section {
                flex-direction: column;
                align-items: stretch;
            }
            
            .item-price {
                text-align: center;
            }
        }
    </style>
</head>

<body>
    <?php include 'header.php'; ?>
    
    <main class="cart-page">
        <div class="container">
            <!-- Page Header -->
            <div class="page-header" id="pageHeader">
                <h1 class="page-title">Shopping Cart</h1>
                <p class="page-subtitle">Review your items before checkout</p>
            </div>
            
            <div class="row g-4">
                <!-- Cart Items -->
                <div class="col-lg-8">
                    <div class="cart-container" id="cartContainer">
                        <?php if (empty($cartItems)): ?>
                        <!-- Empty Cart State -->
                        <div class="empty-cart">
                            <i class="fa-solid fa-cart-shopping"></i>
                            <h3>Your cart is empty</h3>
                            <p>Looks like you haven't added any items to your cart yet.</p>
                            <a href="shop.php" class="btn btn-primary btn-lg">
                                <i class="fa-solid fa-store me-2"></i>Continue Shopping
                            </a>
                        </div>
                        <?php else: ?>
                        <!-- Cart Items -->
                        <div class="cart-items-section" id="cartItems">
                            <?php foreach ($cartItems as $index => $item): 
                                // Fetch product details from database
                                $productId = (int)$item['product_id'];
                                $query = "
                                    SELECT p.id, p.title, p.price, p.qty,
                                           (SELECT img_url FROM product_img WHERE product_id = p.id LIMIT 1) AS img_url
                                    FROM product p
                                    WHERE p.id = {$productId}
                                ";
                                $result = Database::search($query);
                                
                                if ($result && $result->num_rows > 0):
                                    $product = $result->fetch_assoc();
                                    $itemTotal = $product['price'] * $item['quantity'];
                                    $cartTotal += $itemTotal;
                            ?>
                            <div class="cart-item" data-index="<?= $index ?>">
                                <div class="item-image">
                                    <img src="<?= htmlspecialchars($product['img_url'] ?? 'imgs/no-image.png') ?>" 
                                         alt="<?= htmlspecialchars($product['title']) ?>">
                                </div>
                                
                                <div class="item-details">
                                    <div>
                                        <h3 class="item-title">
                                            <a href="product.php?id=<?= $product['id'] ?>">
                                                <?= htmlspecialchars($product['title']) ?>
                                            </a>
                                        </h3>
                                        <div class="item-meta">
                                            <?php if (isset($item['color'])): ?>
                                            <span class="item-color">
                                                <span class="color-dot" style="background: #ddd;"></span>
                                                Color: <?= htmlspecialchars($item['color']) ?>
                                            </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    
                                    <div class="item-controls">
                                        <div class="qty-controls">
                                            <button class="qty-btn" onclick="updateQuantity(<?= $index ?>, -1)">
                                                <i class="fa-solid fa-minus"></i>
                                            </button>
                                            <div class="qty-display" id="qty-<?= $index ?>">
                                                <?= $item['quantity'] ?>
                                            </div>
                                            <button class="qty-btn" onclick="updateQuantity(<?= $index ?>, 1)">
                                                <i class="fa-solid fa-plus"></i>
                                            </button>
                                        </div>
                                        
                                        <div class="item-price-section">
                                            <div class="item-price" id="price-<?= $index ?>">
                                                LKR <?= number_format($itemTotal, 0, '.', ',') ?>
                                            </div>
                                            <button class="btn-remove" onclick="removeItem(<?= $index ?>)" title="Remove item">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php 
                                endif;
                            endforeach; 
                            ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Cart Summary -->
                <?php if (!empty($cartItems)): ?>
                <div class="col-lg-4">
                    <div class="cart-summary" id="cartSummary">
                        <h2 class="summary-title">Order Summary</h2>
                        
                        <div class="summary-row">
                            <span class="summary-label">Subtotal</span>
                            <span class="summary-value" id="subtotal">LKR <?= number_format($cartTotal, 0, '.', ',') ?></span>
                        </div>
                        
                        <div class="summary-row">
                            <span class="summary-label">Shipping</span>
                            <span class="summary-value">
                                <?php 
                                $shipping = $cartTotal >= 100000 ? 0 : 2500;
                                echo $shipping == 0 ? 'FREE' : 'LKR ' . number_format($shipping, 0, '.', ',');
                                ?>
                            </span>
                        </div>
                        
                        <div class="summary-row">
                            <span class="summary-label">Tax (Estimated)</span>
                            <span class="summary-value" id="tax">
                                LKR <?php 
                                $tax = $cartTotal * 0.1; // 10% tax
                                echo number_format($tax, 0, '.', ',');
                                ?>
                            </span>
                        </div>
                        
                        <div class="summary-total">
                            <span class="total-label">Total</span>
                            <span class="total-value" id="total">
                                LKR <?= number_format($cartTotal + $shipping + $tax, 0, '.', ',') ?>
                            </span>
                        </div>
                        
                        <button class="btn-checkout" onclick="proceedToCheckout()">
                            <i class="fa-solid fa-lock"></i>
                            Proceed to Checkout
                        </button>
                        
                        <button class="btn-continue" onclick="window.location.href='shop.php'">
                            <i class="fa-solid fa-arrow-left me-2"></i>
                            Continue Shopping
                        </button>
                        
                        <div class="promo-section">
                            <label class="summary-label mb-2 d-block">Promo Code</label>
                            <div class="promo-input-group">
                                <input type="text" class="promo-input" placeholder="Enter code" id="promoCode">
                                <button class="btn-apply" onclick="applyPromo()">Apply</button>
                            </div>
                        </div>
                        
                        <div class="security-badge">
                            <i class="fa-solid fa-shield-halved"></i>
                            <div>
                                <strong>Secure Checkout</strong><br>
                                Your payment information is protected
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
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
        gsap.to('#pageHeader', {
            opacity: 1,
            y: 0,
            duration: 0.8,
            ease: 'power3.out'
        });
        
        gsap.to('#cartContainer', {
            opacity: 1,
            y: 0,
            duration: 0.8,
            delay: 0.2,
            ease: 'power3.out'
        });
        
        gsap.to('#cartSummary', {
            opacity: 1,
            x: 0,
            duration: 0.8,
            delay: 0.4,
            ease: 'power3.out'
        });
        
        // Update quantity
        function updateQuantity(index, delta) {
            const qtyDisplay = document.getElementById(`qty-${index}`);
            let currentQty = parseInt(qtyDisplay.textContent);
            let newQty = Math.max(1, currentQty + delta);
            
            // AJAX call to update session/database
            fetch('updateCart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `index=${index}&quantity=${newQty}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    qtyDisplay.textContent = newQty;
                    updateTotals();
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }
        
        // Remove item
        function removeItem(index) {
            Swal.fire({
                title: 'Remove Item?',
                text: 'Are you sure you want to remove this item from your cart?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Yes, remove it',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('removeFromCart.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `index=${index}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        }
                    });
                }
            });
        }
        
        // Update totals (placeholder - implement actual calculation)
        function updateTotals() {
            // This would recalculate subtotal, tax, shipping, and total
            console.log('Updating totals...');
        }
        
        // Apply promo code
        function applyPromo() {
            const promoCode = document.getElementById('promoCode').value.trim();
            
            if (!promoCode) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Enter Promo Code',
                    text: 'Please enter a promo code to apply.'
                });
                return;
            }
            
            // TODO: Implement promo code validation
            Swal.fire({
                icon: 'info',
                title: 'Invalid Code',
                text: 'The promo code you entered is not valid.'
            });
        }
        
        // Proceed to checkout
        function proceedToCheckout() {
            // TODO: Check if user is logged in
            window.location.href = 'checkout.php';
        }
    </script>
</body>
</html>
