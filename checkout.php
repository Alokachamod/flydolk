<?php
require_once 'connection.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. CHECK LOGIN
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header('Location: login-signup.php?redirect=checkout');
    exit;
}
$user_id = (int)$_SESSION['user_id'];

// --- !! ---
// **THE FIX IS HERE:**
// We now use the SESSION to store the checkout items.
// --- !! ---

$cart_item_ids_sql = [];
$error_message = null; // We will use this to show errors instead of redirecting

// 2. CHECK FOR ITEMS
if (isset($_POST['selected_items']) && is_array($_POST['selected_items'])) {
    // User just arrived from cart.php. Save items to session.
    $_SESSION['checkout_items'] = [];
    foreach ($_POST['selected_items'] as $item_id) {
        $cart_item_ids_sql[] = (int)$item_id;
    }
    $_SESSION['checkout_items'] = $cart_item_ids_sql;
    
} else if (isset($_SESSION['checkout_items']) && !empty($_SESSION['checkout_items'])) {
    // User reloaded the page. Get items from session.
    $cart_item_ids_sql = $_SESSION['checkout_items'];
    
} else {
    // User has no items in POST or SESSION.
    // **THIS IS THE FIX:** We stop redirecting and show an error instead.
    $error_message = "Your session expired or no items were selected. Please <a href='cart.php'>return to your cart</a>.";
}


// 3. BUILD SQL FROM SAVED IDs
$cart_id_list = "";
if (!empty($cart_item_ids_sql)) {
    $cart_id_list = implode(',', $cart_item_ids_sql);
    $cart_id_where_clause = "AND c.id IN ($cart_id_list)";
} else {
    $cart_id_where_clause = "AND 1=0"; // No items, so select nothing
}

// 4. FETCH USER'S NAME & ADDRESS
// **UPDATE:** Fetch user's name for pre-filling the form.
$user_rs = Database::search("SELECT name FROM user WHERE id = $user_id");
$user_data = $user_rs->fetch_assoc();
$user_full_name = $user_data['name'] ?? '';

// Split the name into first and last
$name_parts = explode(' ', $user_full_name, 2);
$user_fname = $name_parts[0];
$user_lname = $name_parts[1] ?? ''; // Use the rest as last name, or empty string

$address_rs = Database::search("
    SELECT uha.*, c.id AS city_id, d.id AS district_id, p.id AS province_id 
    FROM user_has_address uha
    LEFT JOIN city c ON uha.city_id = c.id
    LEFT JOIN district d ON c.district_id = d.id
    LEFT JOIN province p ON d.province_id = p.id
    WHERE uha.user_id = $user_id
");
$address = $address_rs->fetch_assoc();
if (!$address) {
    $address = [
        'address_line_1' => '', 'address_line_2' => '', 'zip_code' => '', 
        'city_id' => null, 'district_id' => null, 'province_id' => null
    ];
}

// 5. FETCH ALL PROVINCES FOR DROPDOWN
$provinces_rs = Database::search("SELECT * FROM province ORDER BY name ASC");

// 6. FETCH DISTRICTS & CITIES *IF* AN ADDRESS IS SAVED
$districts_rs = null;
if ($address['province_id']) {
    $districts_rs = Database::search("SELECT * FROM district WHERE province_id = " . $address['province_id'] . " ORDER BY name ASC");
}
$cities_rs = null;
if ($address['district_id']) {
    $cities_rs = Database::search("SELECT * FROM city WHERE district_id = " . $address['district_id'] . " ORDER BY name ASC");
}


// 7. FETCH *SELECTED* CART ITEMS (This is the *correct* query)
$cart_rs_fixed = null;
if (!$error_message) { // Only fetch if we have items
    $cart_rs_fixed = Database::search("
        SELECT c.id AS cart_id, p.title, p.price, c.qty, MIN(pi.img_url) AS img_url 
        FROM cart c
        JOIN product p ON c.product_id = p.id
        LEFT JOIN product_img pi ON p.id = pi.product_id
        WHERE c.user_id = $user_id $cart_id_where_clause
        GROUP BY c.id
    ");

    if ($cart_rs_fixed->num_rows == 0) {
         unset($_SESSION['checkout_items']); // Clear the bad session
         $error_message = "Could not find your selected cart items. Please <a href='cart.php'>return to your cart</a>.";
    }
}

$subtotal = 0;
$shipping = ($cart_rs_fixed && $cart_rs_fixed->num_rows > 0) ? 500.00 : 0.00; // Example shipping fee

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - FlyDolk</title>
    <link rel="icon" href="imgs/Flydo.png">
    <link rel="stylesheet" href="style.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts (Inter) -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;900&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    
    <!-- Internal Stylesheet -->
    <style>
        /* Common Styles for FlyDolk */
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

        /* Form Styles (from checkout.php) */
        .form-control-dark, .form-select-dark {
            background-color: #1e293b;
            border-color: #334155;
            color: #f1f5f9;
        }
        .form-control-dark:focus, .form-select-dark:focus {
            background-color: #1e293b;
            border-color: #60a5fa;
            color: #f1f5f9;
            box-shadow: 0 0 0 0.25rem rgba(96, 165, 250, 0.25);
        }
        .form-control-dark::placeholder { color: #64748b; }
        .form-select-dark:disabled {
            background-color: #334155;
            opacity: 0.7;
        }

        /* Checkout Summary Styles (from checkout.php) */
        .summary-card {
            background-color: #1e293b;
            border: 1px solid #334155;
            border-radius: 0.5rem;
        }
        .summary-img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 0.25rem;
        }
        #submit-btn {
            font-size: 1.1rem;
            font-weight: 600;
        }
    </style>
</head>
<body>

    <?php include 'header.php'; ?>

    <main class="container my-5">
        
        <?php if(isset($_GET['error']) && !isset($error_message)): /* Show URL error if no local error */ ?>
            <div class="alert alert-danger" role="alert">
                <strong>Error:</strong> <?php echo htmlspecialchars($_GET['error']); ?>
            </div>
        <?php endif; ?>

        <h1 class="display-5 fw-bold text-white mb-4">Checkout</h1>
        
        <?php if($error_message): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error_message; ?>
            </div>
        <?php else: ?>
            <form id="checkout-form" action="checkout_process.php" method="POST">
                <div class="row g-4">
                    
                    <!-- Left Side: Address & Payment -->
                    <div class="col-lg-7">
                        <!-- Shipping Address -->
                        <div class="mb-4">
                            <h3 class="text-white fw-bold mb-3">Shipping Address</h3>
                            <div class="bg-slate-800 p-4 rounded-3 border border-slate-700">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="fname" class="form-label">First Name</label>
                                        <!-- **UPDATE:** Pre-filled value -->
                                        <input type="text" class="form-control form-control-dark" id="fname" name="fname" required value="<?php echo htmlspecialchars($user_fname); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="lname" class="form-label">Last Name</label>
                                        <!-- **UPDATE:** Pre-filled value -->
                                        <input type="text" class="form-control form-control-dark" id="lname" name="lname" required value="<?php echo htmlspecialchars($user_lname); ?>">
                                    </div>
                                    <div class="col-12">
                                        <label for="address1" class="form-label">Address Line 1</label>
                                        <input type="text" class="form-control form-control-dark" id="address1" name="address_line_1" value="<?php echo htmlspecialchars($address['address_line_1']); ?>" required>
                                    </div>
                                    <div class="col-12">
                                        <label for="address2" class="form-label">Address Line 2 <span class="text-muted">(Optional)</span></label>
                                        <input type="text" class="form-control form-control-dark" id="address2" name="address_line_2" value="<?php echo htmlspecialchars($address['address_line_2']); ?>">
                                    </div>

                                    <!-- Province Dropdown -->
                                    <div class="col-md-6">
                                        <label for="province" class="form-label">Province</label>
                                        <select class="form-select form-select-dark" id="province" name="province_id" required>
                                            <option value="">Select Province</option>
                                            <?php 
                                            // Reset pointer to loop again
                                            $provinces_rs->data_seek(0);
                                            while($province = $provinces_rs->fetch_assoc()): 
                                            ?>
                                                <option value="<?php echo $province['id']; ?>" <?php echo ($province['id'] == $address['province_id']) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($province['name']); ?>
                                                </option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>
                                    
                                    <!-- District Dropdown -->
                                    <div class="col-md-6">
                                        <label for="district" class="form-label">District</label>
                                        <select class="form-select form-select-dark" id="district" name="district_id" required <?php echo !$address['province_id'] ? 'disabled' : ''; ?>>
                                            <option value="">Select District</option>
                                            <?php if($districts_rs): ?>
                                                <?php while($district = $districts_rs->fetch_assoc()): ?>
                                                    <option value="<?php echo $district['id']; ?>" <?php echo ($district['id'] == $address['district_id']) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($district['name']); ?>
                                                    </option>
                                                <?php endwhile; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>

                                    <!-- City Dropdown -->
                                    <div class="col-md-6">
                                        <label for="city" class="form-label">City</label>
                                        <select class="form-select form-select-dark" id="city" name="city_id" required <?php echo !$address['district_id'] ? 'disabled' : ''; ?>>
                                            <option value="">Select City</option>
                                            <?php if($cities_rs): ?>
                                                <?php while($city = $cities_rs->fetch_assoc()): ?>
                                                    <option value="<?php echo $city['id']; ?>" <?php echo ($city['id'] == $address['city_id']) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($city['name']); ?>
                                                    </option>
                                                <?php endwhile; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                    
                                    <!-- Zip Code -->
                                    <div class="col-md-6">
                                        <label for="zip" class="form-label">Zip Code</label>
                                        <input type="text" class="form-control form-control-dark" id="zip" name="zip_code" value="<?php echo htmlspecialchars($address['zip_code']); ?>" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Method -->
                        <div>
                            <h3 class="text-white fw-bold mb-3">Payment Method</h3>
                            <div class="bg-slate-800 p-4 rounded-3 border border-slate-700">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="payment_method" id="cod" value="cod" checked>
                                    <label class="form-check-label text-white fs-5" for="cod">
                                        Cash on Delivery
                                    </label>
                                    <div class="form-text text-muted">Pay with cash upon delivery of your order.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Right Side: Order Summary -->
                    <div class="col-lg-5">
                        <div class="summary-card p-4 position-sticky" style="top: 150px;">
                            <h3 class="text-white fw-bold mb-3">Order Summary</h3>
                            
                            <!-- 
                            This DIV is now empty. 
                            The JavaScript at the bottom will correctly populate it.
                            -->
                            <div id="summary-items-list" class="d-flex flex-column gap-3 mb-3" style="max-height: 300px; overflow-y: auto;">
                                <!-- Items will be populated by JavaScript -->
                            </div>
                            
                            <hr class="border-slate-700">
                            
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Subtotal</span>
                                <span class="text-light fw-bold">LKR <span id="summary-subtotal">0.00</span></span>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span class="text-muted">Shipping</span>
                                <span class="text-light fw-bold">LKR <?php echo number_format($shipping, 2); ?></span>
                            </div>
                            
                            <hr class="border-slate-700">
                            
                            <div class="d-flex justify-content-between mb-4">
                                <span class="text-white fs-5 fw-bold">Total</span>
                                <span class="text-white fs-5 fw-bold">LKR <span id="summary-total">0.00</span></span>
                            </div>
                            
                            <button id="submit-btn" type="submit" class="btn btn-primary btn-lg w-100 fw-bold">
                                <div class="spinner-border spinner-border-sm" role="status" style="display: none;">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <span class="btn-text">Place Order (COD)</span>
                            </button>
                        </div>
                    </div>
                    
                </div>
            </form>
        <?php endif; // End check for $error_message ?>
    </main>

    <?php include 'footer.php'; ?>

    <script src="script.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Internal Page-Specific JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            
            // Only run this JS if the form actually exists on the page
            const checkoutForm = document.getElementById('checkout-form');
            if (checkoutForm) { 
            
                const provinceSelect = document.getElementById('province');
                const districtSelect = document.getElementById('district');
                const citySelect = document.getElementById('city');

                function fetchLocations(url, selectElement, placeholder) {
                    selectElement.disabled = true;
                    selectElement.innerHTML = `<option value="">Loading...</option>`;
                    
                    fetch(url)
                        .then(response => response.json())
                        .then(data => {
                            selectElement.innerHTML = `<option value="">${placeholder}</option>`;
                            if (data.status === 'success' && data.locations.length > 0) {
                                data.locations.forEach(location => {
                                    const option = document.createElement('option');
                                    option.value = location.id;
                                    option.textContent = location.name;
                                    selectElement.appendChild(option);
                                });
                                selectElement.disabled = false;
                            } else {
                                selectElement.innerHTML = `<option value="">No locations found</option>`;
                            }
                        })
                        .catch(error => {
                            console.error('Error fetching locations:', error);
                            selectElement.innerHTML = `<option value="">Error loading</option>`;
                        });
                }

                if(provinceSelect) {
                    provinceSelect.addEventListener('change', function() {
                        const provinceId = this.value;
                        citySelect.innerHTML = '<option value="">Select City</option>';
                        citySelect.disabled = true;
                        if (provinceId) {
                            fetchLocations(`get_locations.php?province_id=${provinceId}`, districtSelect, 'Select District');
                        } else {
                            districtSelect.innerHTML = '<option value="">Select District</option>';
                            districtSelect.disabled = true;
                        }
                    });
                }

                if(districtSelect) {
                    districtSelect.addEventListener('change', function() {
                        const districtId = this.value;
                        if (districtId) {
                            fetchLocations(`get_locations.php?district_id=${districtId}`, citySelect, 'Select City');
                        } else {
                            citySelect.innerHTML = '<option value="">Select City</option>';
                            citySelect.disabled = true;
                        }
                    });
                }

                // Form submission loading state
                checkoutForm.addEventListener('submit', function() {
                    const submitBtn = document.getElementById('submit-btn');
                    const btnText = submitBtn.querySelector('.btn-text');
                    const spinner = submitBtn.querySelector('.spinner-border');
                    
                    submitBtn.disabled = true;
                    spinner.style.display = 'inline-block';
                    btnText.style.display = 'none';
                });
                
                // --- Summary Calculation ---
                const summaryItemsList = document.getElementById('summary-items-list');
                
                // This PHP block will render the items AND the hidden inputs
                <?php
                if ($cart_rs_fixed): // Only run if the query was successful
                    // We use the $cart_rs_fixed query result from line 90
                    while($item = $cart_rs_fixed->fetch_assoc()):
                        $item_total = $item['price'] * $item['qty'];
                        $subtotal += $item_total; // Add to PHP subtotal
                ?>
                
                // Render item HTML
                summaryItemsList.innerHTML += `
                    <div class="d-flex align-items-center gap-3">
                        <img src="<?php echo htmlspecialchars($item['img_url'] ?? 'imgs/placeholder.png'); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>" class="summary-img">
                        <div class="flex-grow-1">
                            <h6 class="text-white mb-0"><?php echo htmlspecialchars($item['title']); ?></h6>
                            <span class="text-muted">Qty: <?php echo $item['qty']; ?></span>
                        </div>
                        <span class="text-light fw-bold">LKR <?php echo number_format($item_total, 2); ?></span>
                    </div>
                `;
                
                // Render hidden input for the form.
                // This is NOT needed because the session handles it.
                // We just need the items to be in the session.
                
                <?php 
                    endwhile; 
                endif; // End $cart_rs_fixed check
                ?>
                
                // Update the summary totals on the page
                const shipping = <?php echo $shipping; ?>;
                document.getElementById('summary-subtotal').textContent = '<?php echo number_format($subtotal, 2); ?>';
                document.getElementById('summary-total').textContent = '<?php echo number_format($subtotal + $shipping, 2); ?>';

            } // End of checkoutForm check
        });
    </script>

</body>
</html>

