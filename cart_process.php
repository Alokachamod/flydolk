<?php
require_once 'connection.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set header to return JSON
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Please log in to manage your cart.']);
    exit;
}

// Check if product_id is set
if (!isset($_POST['product_id']) || !isset($_POST['qty'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
    exit;
}

$user_id = (int)$_SESSION['user_id'];
$product_id = (int)$_POST['product_id'];
$qty = (int)$_POST['qty'];

if ($product_id <= 0 || $qty <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid product details.']);
    exit;
}

try {
    // Check if the item is already in the cart
    $check_sql = "SELECT * FROM cart WHERE user_id = $user_id AND product_id = $product_id";
    $cart_rs = Database::search($check_sql);

    if ($cart_rs->num_rows > 0) {
        // Item already in cart, update quantity
        $cart_item = $cart_rs->fetch_assoc();
        $new_qty = $cart_item['qty'] + $qty;
        $update_sql = "UPDATE cart SET qty = $new_qty WHERE id = {$cart_item['id']}";
        Database::iud($update_sql);
        echo json_encode(['status' => 'exists', 'message' => 'Product quantity updated in your cart.']);
    } else {
        // Item not in cart, insert new row
        // We should also check product stock/availability here if we had that in the DB
        $insert_sql = "INSERT INTO cart (user_id, product_id, qty) VALUES ($user_id, $product_id, $qty)";
        Database::iud($insert_sql);
        echo json_encode(['status' => 'success', 'message' => 'Product added to your cart!']);
    }

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
