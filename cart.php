<?php
require_once 'connection.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. CHECK LOGIN
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header('Location: login-signup.php?redirect=cart');
    exit;
}
$user_id = (int)$_SESSION['user_id'];

// 2. FETCH CART ITEMS
$cart_rs = Database::search("
    SELECT c.id AS cart_id, p.id AS product_id, p.title, p.price, p.qty AS stock_qty, c.qty AS cart_qty, MIN(pi.img_url) AS img_url 
    FROM cart c
    JOIN product p ON c.product_id = p.id
    LEFT JOIN product_img pi ON p.id = pi.product_id
    WHERE c.user_id = $user_id
    GROUP BY c.id
    ORDER BY c.added_at DESC
");
$num_rows = $cart_rs->num_rows;
$shipping = ($num_rows > 0) ? 500.00 : 0.00; // Example shipping fee
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart - FlyDolk</title>
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
            /* FIX: Add padding to offset fixed-top header */
            padding-top: 140px; /* Mobile header */
        }
        @media (min-width: 992px) { /* lg breakpoint */
            body {
                padding-top: 90px; /* Desktop header */
            }
        }
        .bg-slate-800 { background-color: #1e293b; }
        .border-slate-700 { border-color: #334155; }
        
        .cart-item-card {
            background-color: #1e293b;
            border: 1px solid #334155;
            border-radius: 0.5rem;
        }
        .cart-img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 0.25rem;
        }
        .summary-card {
            background-color: #1e293b;
            border: 1px solid #334155;
            border-radius: 0.5rem;
        }
        .qty-input {
            width: 60px;
            text-align: center;
            background-color: #0f172a;
            border-color: #334155;
            color: #f1f5f9;
        }
        .qty-btn {
            background-color: #334155;
            color: #f1f5f9;
            border: none;
            width: 35px;
            height: 35px;
            border-radius: 50% !important;
        }
        .qty-btn:hover {
            background-color: #475569;
        }
        .remove-btn {
            color: #f87171;
            font-size: 0.9rem;
        }
        .remove-btn:hover {
            color: #ef4444;
            text-decoration: underline;
        }
        .empty-cart-card {
            background-color: #1e293b;
            border: 1px solid #334155;
            border-radius: 0.5rem;
            padding: 4rem 2rem;
            text-align: center;
        }
    </style>
</head>
<body>

    <?php include 'header.php'; ?>

    <main class="container my-5">
        <h1 class="display-5 fw-bold text-white mb-4">Your Cart</h1>
        
        <div class="row g-4">
            
            <?php if ($num_rows > 0): ?>
            <!-- Wrap cart and summary in a form -->
            <form action="checkout.php" method="POST" id="cart-form" class="row g-4">

                <!-- Left Side: Cart Items -->
                <div class="col-lg-8">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="form-check fs-5">
                            <input class="form-check-input" type="checkbox" id="select-all-checkbox">
                            <label class="form-check-label text-white" for="select-all-checkbox">
                                Select All (<?php echo $num_rows; ?> items)
                            </label>
                        </div>
                    </div>

                    <div class="d-flex flex-column gap-3">
                        <?php while($item = $cart_rs->fetch_assoc()): ?>
                        <!-- Cart Item Card -->
                        <div class="cart-item-card p-3 d-flex flex-column flex-md-row gap-3 align-items-start align-items-md-center">
                            <!-- Checkbox -->
                            <div class="form-check">
                                <input class="form-check-input cart-item-checkbox" 
                                       type="checkbox" 
                                       name="selected_items[]" 
                                       value="<?php echo $item['cart_id']; ?>"
                                       data-price="<?php echo $item['price']; ?>"
                                       data-cart-id="<?php echo $item['cart_id']; ?>">
                            </div>

                            <!-- Image -->
                            <img src="<?php echo htmlspecialchars($item['img_url'] ?? 'imgs/placeholder.png'); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>" class="cart-img">
                            
                            <!-- Title & Price -->
                            <div class="flex-grow-1">
                                <a href="single_product.php?id=<?php echo $item['product_id']; ?>" class="text-white fs-5 fw-bold text-decoration-none">
                                    <?php echo htmlspecialchars($item['title']); ?>
                                </a>
                                <div class="text-muted">LKR <?php echo number_format($item['price'], 2); ?></div>
                                <button type="button" class="btn btn-link p-0 text-decoration-none remove-btn" onclick="removeItem(<?php echo $item['cart_id']; ?>, this)">
                                    Remove
                                </button>
                            </div>
                            
                            <!-- Quantity & Total -->
                            <div class="d-flex align-items-center gap-3">
                                <!-- Quantity -->
                                <div class="input-group" style="width: auto;">
                                    <button class="btn qty-btn" type="button" onclick="updateQty(<?php echo $item['cart_id']; ?>, -1, this)">-</button>
                                    <input type="text" class="form-control qty-input" value="<?php echo $item['cart_qty']; ?>" 
                                           aria-label="Quantity" readonly 
                                           id="qty-<?php echo $item['cart_id']; ?>"
                                           data-stock="<?php echo $item['stock_qty']; ?>">
                                    <button class="btn qty-btn" type="button" onclick="updateQty(<?php echo $item['cart_id']; ?>, 1, this)">+</button>
                                </div>
                                <!-- Item Total -->
                                <div class="text-white fs-5 fw-bold" style="width: 120px; text-align: right;">
                                    LKR <span class="item-total" id="total-<?php echo $item['cart_id']; ?>">
                                        <?php echo number_format($item['price'] * $item['cart_qty'], 2); ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    </div>
                </div>

                <!-- Right Side: Order Summary -->
                <div class="col-lg-4">
                    <div class="summary-card p-4 position-sticky" style="top: 150px;">
                        <h3 class="text-white fw-bold mb-3">Order Summary</h3>
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Selected Subtotal</span>
                            <span class="text-light fw-bold">LKR <span id="summary-subtotal">0.00</span></span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Shipping</span>
                            <span class="text-light fw-bold">LKR <span id="summary-shipping">0.00</span></span>
                        </div>
                        
                        <hr class="border-slate-700">
                        
                        <div class="d-flex justify-content-between mb-4">
                            <span class="text-white fs-5 fw-bold">Total</span>
                            <span class="text-white fs-5 fw-bold">LKR <span id="summary-total">0.00</span></span>
                        </div>
                        
                        <button type="submit" id="checkout-btn" class="btn btn-primary btn-lg w-100 fw-bold" disabled>
                            Proceed to Checkout
                        </button>
                        <div id="checkout-error" class="text-danger mt-2 text-center" style="display: none;">
                            Please select at least one item to check out.
                        </div>
                    </div>
                </div>

            </form>
            
            <?php else: ?>
            <!-- Empty Cart -->
            <div class="col-12">
                <div class="empty-cart-card">
                    <i class="fas fa-shopping-cart fa-3x text-muted mb-4"></i>
                    <h2 class="text-white fw-bold mb-3">Your Cart is Empty</h2>
                    <p class="text-muted mb-4">Looks like you haven't added anything to your cart yet.</p>
                    <a href="shop.php" class="btn btn-primary btn-lg fw-bold">Start Shopping</a>
                </div>
            </div>
            <?php endif; ?>
            
        </div>
    </main>

    <?php include 'footer.php'; ?>

    <script src="script.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        const shippingFee = <?php echo $shipping; ?>;

        document.addEventListener('DOMContentLoaded', function() {
            const selectAllCheckbox = document.getElementById('select-all-checkbox');
            const itemCheckboxes = document.querySelectorAll('.cart-item-checkbox');
            const checkoutBtn = document.getElementById('checkout-btn');
            const checkoutError = document.getElementById('checkout-error');

            function updateTotals() {
                let subtotal = 0;
                let itemsSelected = false;

                itemCheckboxes.forEach(checkbox => {
                    if (checkbox.checked) {
                        itemsSelected = true;
                        const card = checkbox.closest('.cart-item-card');
                        const price = parseFloat(checkbox.dataset.price);
                        const quantityInput = card.querySelector('.qty-input');
                        const quantity = parseInt(quantityInput.value);
                        
                        subtotal += price * quantity;
                    }
                });

                const finalShipping = itemsSelected ? shippingFee : 0;
                const total = subtotal + finalShipping;

                document.getElementById('summary-subtotal').textContent = subtotal.toFixed(2);
                document.getElementById('summary-shipping').textContent = finalShipping.toFixed(2);
                document.getElementById('summary-total').textContent = total.toFixed(2);

                // Enable/disable checkout button
                if (itemsSelected) {
                    checkoutBtn.disabled = false;
                    checkoutError.style.display = 'none';
                } else {
                    checkoutBtn.disabled = true;
                    checkoutError.style.display = 'none'; // Only show error on *click*
                }
            }

            // --- Event Listeners ---

            if (selectAllCheckbox) {
                selectAllCheckbox.addEventListener('change', function() {
                    itemCheckboxes.forEach(checkbox => {
                        checkbox.checked = selectAllCheckbox.checked;
                    });
                    updateTotals();
                });
            }

            itemCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    if (!this.checked) {
                        selectAllCheckbox.checked = false;
                    } else {
                        // Check if all others are checked
                        if (document.querySelectorAll('.cart-item-checkbox:checked').length === itemCheckboxes.length) {
                            selectAllCheckbox.checked = true;
                        }
                    }
                    updateTotals();
                });
            });

            document.getElementById('cart-form').addEventListener('submit', function(e) {
                if (document.querySelectorAll('.cart-item-checkbox:checked').length === 0) {
                    e.preventDefault(); // Stop form submission
                    checkoutError.style.display = 'block';
                }
            });

            // Initial calculation
            updateTotals();
        });

        // --- AJAX Functions ---

        function updateQty(cartId, change, btn) {
            const qtyInput = document.getElementById('qty-' + cartId);
            const stock = parseInt(qtyInput.dataset.stock);
            let newQty = parseInt(qtyInput.value) + change;

            // Validate quantity
            if (newQty < 1) newQty = 1;
            if (newQty > stock) {
                alert('Cannot add more. Only ' + stock + ' items in stock.');
                newQty = stock;
            }

            // Disable buttons during update
            btn.disabled = true;
            
            fetch('cart_update_process.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `action=update&cart_id=${cartId}&qty=${newQty}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // Update local values
                    qtyInput.value = newQty;
                    const price = parseFloat(document.querySelector(`[data-cart-id="${cartId}"]`).dataset.price);
                    document.getElementById('total-' + cartId).textContent = (price * newQty).toFixed(2);
                    
                    // Re-calculate totals for selected items
                    updateTotals(); 
                } else {
                    alert(data.message || 'Failed to update quantity.');
                }
            })
            .finally(() => {
                btn.disabled = false;
            });
        }

        function removeItem(cartId, btn) {
            if (!confirm('Are you sure you want to remove this item?')) {
                return;
            }

            btn.disabled = true;
            
            fetch('cart_update_process.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `action=remove&cart_id=${cartId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // Remove item from page
                    btn.closest('.cart-item-card').remove();
                    
                    // Re-calculate totals
                    updateTotals(); 
                    
                    // Update header cart count (async)
                    fetch('get_cart_count.php').then(r => r.json()).then(d => updateHeaderCount(d.cart_count));

                    // Check if cart is now empty
                    if (document.querySelectorAll('.cart-item-checkbox').length === 0) {
                        location.reload(); // Easiest way to show "Empty Cart" message
                    }
                } else {
                    alert(data.message || 'Failed to remove item.');
                    btn.disabled = false;
                }
            });
        }
        
        // This function must exist for removeItem to work
        function updateHeaderCount(cartCount) {
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
                 if (desktopBadge) desktopBadge.style.display = 'none';
                 if (mobileBadge) mobileBadge.style.display = 'none';
             }
        }
    </script>
</body>
</html>

