<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'connection.php';
header('Content-Type: application/json');

$response = ['status' => 'error', 'message' => 'An unknown error occurred.'];
$cart_count = 0; // Initialize cart count

try {
    // 1. CHECK LOGIN
    if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
        throw new Exception('Please log in to add items to your cart.');
    }
    $user_id = (int)$_SESSION['user_id'];
    Database::setUpConnection(); // Set up connection

    // 2. GET DATA
    $product_id = (int)($_POST['product_id'] ?? 0);
    $qty = (int)($_POST['qty'] ?? 1); // Default to 1
    $buy_now = (bool)($_POST['buy_now'] ?? false);

    if ($product_id <= 0) {
        throw new Exception('Invalid Product ID.');
    }
    if ($qty <= 0) {
        throw new Exception('Invalid Quantity.');
    }

    // 3. CHECK STOCK
    $stock_rs = Database::search("SELECT qty FROM product WHERE id = $product_id");
    if ($stock_rs->num_rows == 0) {
        throw new Exception('Product not found.');
    }
    $stock_qty = (int)$stock_rs->fetch_assoc()['qty'];
    

    // 4. CHECK CART
    $cart_rs = Database::search("
        SELECT id, qty FROM cart 
        WHERE user_id = $user_id AND product_id = $product_id
    ");
    
    $total_qty_needed = $qty;
    if ($cart_rs->num_rows == 1) {
        $cart_item = $cart_rs->fetch_assoc();
        $total_qty_needed = $cart_item['qty'] + $qty; // Total qty if we add
    }
    
    // Check if total needed exceeds stock
    if ($stock_qty < $total_qty_needed) {
         throw new Exception('Not enough stock. Only ' . $stock_qty . ' available.');
    }

    // 5. ADD/UPDATE CART
    if ($cart_rs->num_rows == 1) {
        // Product is already in cart, UPDATE quantity
        $cart_id = $cart_item['id'];
        Database::iud("
            UPDATE cart SET qty = $total_qty_needed 
            WHERE id = $cart_id
        ");
        $response['message'] = 'Cart updated successfully!';
    } else {
        // Product is not in cart, INSERT new row
        Database::iud("
            INSERT INTO cart (user_id, product_id, qty, added_at, updated_at) 
            VALUES ($user_id, $product_id, $qty, NOW(), NOW())
        ");
        $response['message'] = 'Product added to cart!';
    }
    
    $response['status'] = 'success';
    
    // --- !! NEW CODE TO FIX BADGE !! ---
    // 6. GET NEW TOTAL CART COUNT
    $count_rs = Database::search("SELECT COUNT(id) AS count FROM cart WHERE user_id = $user_id");
    if ($count_rs && $count_rs->num_rows > 0) {
        $cart_count = (int)$count_rs->fetch_assoc()['count'];
    }
    $response['cart_count'] = $cart_count; // Add count to the response
    // --- !! END NEW CODE !! ---

    if ($buy_now) {
        $response['redirect'] = 'checkout.php';
    }

} catch (mysqli_sql_exception $e) {
    // Handle database errors
    $response['message'] = 'Database error: ' . $e->getMessage();
} catch (Exception $e) {
    // Handle other errors (like login check)
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>

