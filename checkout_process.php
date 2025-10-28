<?php
// This file handles a "Cash on Delivery" order placement.
// It now reads *only* the selected items from the POST data.

require_once 'connection.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// --- 1. CHECK LOGIN & METHOD ---
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header('Location: login-signup.php?redirect=checkout');
    exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: checkout.php?error=Invalid access method.');
    exit;
}

$user_id = (int)$_SESSION['user_id'];
Database::setUpConnection(); // Ensure connection is active

// --- 2. GET SELECTED ITEMS ---
$selected_items = $_POST['selected_items'] ?? null;
$cart_item_ids_sql = [];

if ($selected_items && is_array($selected_items)) {
    // Sanitize selected items
    foreach ($selected_items as $item_id) {
        $cart_item_ids_sql[] = (int)$item_id;
    }
}

if (empty($cart_item_ids_sql)) {
    // No items were submitted
    header('Location: cart.php?error=no_selection');
    exit;
}

$cart_id_list = implode(',', $cart_item_ids_sql);
$cart_id_where_clause = "AND c.id IN ($cart_id_list)";


// --- 3. START DATABASE TRANSACTION ---
Database::$connection->begin_transaction();

try {
    // --- 4. SAVE/UPDATE ADDRESS ---
    
    // Sanitize POST data
    $address1 = Database::$connection->real_escape_string($_POST['address_line_1']);
    $address2 = Database::$connection->real_escape_string($_POST['address_line_2']);
    $zip = Database::$connection->real_escape_string($_POST['zip_code']);
    $city_id = (int)$_POST['city_id']; // Get the ID directly

    // Check if city ID is valid
    if ($city_id <= 0) {
        throw new Exception("Please select a valid city.");
    }
    
    // Save/Update user_has_address
    $sql = "INSERT INTO user_has_address (user_id, address_line_1, address_line_2, zip_code, city_id)
            VALUES ($user_id, '$address1', '$address2', '$zip', $city_id)
            ON DUPLICATE KEY UPDATE
            address_line_1 = VALUES(address_line_1),
            address_line_2 = VALUES(address_line_2),
            zip_code = VALUES(zip_code),
            city_id = VALUES(city_id)";
    Database::iud($sql);
    
    // Get the ID of the address we just saved/updated
    $address_id_rs = Database::search("SELECT id FROM user_has_address WHERE user_id = $user_id");
    $user_address_id = $address_id_rs->fetch_assoc()['id'];
    if (!$user_address_id) {
        throw new Exception("Could not save or find user address.");
    }
    
    // --- 5. PROCESS THE ORDER ---
    
    // Get *SELECTED* cart items
    $cart_rs = Database::search("
        SELECT c.id AS cart_id, c.product_id, c.qty, p.price, p.qty AS stock_qty
        FROM cart c
        JOIN product p ON c.product_id = p.id
        WHERE c.user_id = $user_id $cart_id_where_clause
    ");
    
    if ($cart_rs->num_rows == 0) {
        throw new Exception("Your selected items could not be found.");
    }
    
    // Create a new unique Order ID
    $order_id = 'FD-' . $user_id . '-' . time();
    $status_id = 1; // 'Pending'
    $created_at = date('Y-m-d H:i:s');
    $shipping = 500.00; // Same as on checkout page
    $subtotal = 0;

    $cart_items = [];
    while ($item = $cart_rs->fetch_assoc()) {
        $cart_items[] = $item;
        $subtotal += $item['price'] * $item['qty'];
        
        // Check stock
        if ($item['qty'] > $item['stock_qty']) {
            throw new Exception("Not enough stock for product ID " . $item['product_id']);
        }
    }
    
    $grand_total = $subtotal + $shipping;

    // Loop again to save to invoice and update stock
    foreach ($cart_items as $item) {
        $product_id = $item['product_id'];
        $qty = $item['qty'];
        $unit_price = $item['price'];
        $total_amount = $unit_price * $qty;
        
        // Insert into invoice
        $invoice_sql = "
            INSERT INTO invoice (order_id, product_id, qty, unit_price, total_amount, user_has_address_id, status_id, created_at)
            VALUES ('$order_id', $product_id, $qty, '$unit_price', '$total_amount', $user_address_id, $status_id, '$created_at')
        ";
        Database::iud($invoice_sql);
        
        // Update product stock
        $stock_sql = "UPDATE product SET qty = qty - $qty WHERE id = $product_id";
        Database::iud($stock_sql);
    }
    
    // --- 6. CLEAR *ONLY THE PURCHASED ITEMS* FROM THE CART ---
    Database::iud("DELETE FROM cart WHERE user_id = $user_id AND id IN ($cart_id_list)");
    
    // --- 7. COMMIT AND REDIRECT ---
    Database::$connection->commit();
    
    // Redirect to success page
    header('Location: order_success.php?order_id=' . $order_id);
    exit;
    
} catch (Exception $e) {
    // Something went wrong, roll back changes
    Database::$connection->rollback();
    
    // Redirect back to checkout with an error
    // We can't post to checkout, so we redirect to cart with the error
    header('Location: cart.php?error=' . urlencode($e->getMessage()));
    exit;
}
?>

