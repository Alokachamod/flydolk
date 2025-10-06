<?php
session_start();
require_once 'connection.php';

header('Content-Type: application/json');

// Get POST data
$productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
$colorId = isset($_POST['color_id']) ? (int)$_POST['color_id'] : null;

// Validate inputs
if ($productId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid product']);
    exit;
}

if ($quantity < 1) {
    $quantity = 1;
}

// Check if product exists and get available quantity
$checkQuery = "SELECT id, qty FROM product WHERE id = {$productId}";
$checkResult = Database::search($checkQuery);

if (!$checkResult || $checkResult->num_rows == 0) {
    echo json_encode(['success' => false, 'message' => 'Product not found']);
    exit;
}

$product = $checkResult->fetch_assoc();
$availableQty = (int)$product['qty'];

if ($availableQty < $quantity) {
    echo json_encode(['success' => false, 'message' => 'Not enough stock available']);
    exit;
}

// Check if user is logged in
if (isset($_SESSION['user']) && isset($_SESSION['user']['id'])) {
    // Logged-in user - use cart table
    $userId = (int)$_SESSION['user']['id'];
    
    // Check if item already exists in cart
    $colorCondition = $colorId ? "AND color_id = {$colorId}" : "AND color_id IS NULL";
    $checkCartQuery = "
        SELECT id, qty 
        FROM cart 
        WHERE user_id = {$userId} 
        AND product_id = {$productId} 
        {$colorCondition}
    ";
    
    $cartResult = Database::search($checkCartQuery);
    
    if ($cartResult && $cartResult->num_rows > 0) {
        // Item exists - update quantity
        $cartItem = $cartResult->fetch_assoc();
        $newQty = (int)$cartItem['qty'] + $quantity;
        
        // Check if new quantity exceeds available stock
        if ($newQty > $availableQty) {
            echo json_encode(['success' => false, 'message' => 'Cannot add more than available stock']);
            exit;
        }
        
        $updateQuery = "
            UPDATE cart 
            SET qty = {$newQty}, 
                updated_at = NOW() 
            WHERE id = {$cartItem['id']}
        ";
        
        Database::iud($updateQuery);
        echo json_encode(['success' => true, 'message' => 'Cart updated successfully']);
        
    } else {
        // New item - insert
        $colorValue = $colorId ? $colorId : 'NULL';
        $insertQuery = "
            INSERT INTO cart (user_id, product_id, qty, color_id, added_at) 
            VALUES ({$userId}, {$productId}, {$quantity}, {$colorValue}, NOW())
        ";
        
        Database::iud($insertQuery);
        echo json_encode(['success' => true, 'message' => 'Added to cart successfully']);
    }
    
} else {
    // Guest user - use cart_session table
    $sessionId = session_id();
    
    if (empty($sessionId)) {
        echo json_encode(['success' => false, 'message' => 'Session error']);
        exit;
    }
    
    // Check if item already exists in session cart
    $colorCondition = $colorId ? "AND color_id = {$colorId}" : "AND color_id IS NULL";
    $checkSessionQuery = "
        SELECT id, qty 
        FROM cart_session 
        WHERE session_id = '{$sessionId}' 
        AND product_id = {$productId} 
        {$colorCondition}
    ";
    
    $sessionResult = Database::search($checkSessionQuery);
    
    if ($sessionResult && $sessionResult->num_rows > 0) {
        // Item exists - update quantity
        $sessionItem = $sessionResult->fetch_assoc();
        $newQty = (int)$sessionItem['qty'] + $quantity;
        
        // Check if new quantity exceeds available stock
        if ($newQty > $availableQty) {
            echo json_encode(['success' => false, 'message' => 'Cannot add more than available stock']);
            exit;
        }
        
        $updateQuery = "
            UPDATE cart_session 
            SET qty = {$newQty}, 
                updated_at = NOW() 
            WHERE id = {$sessionItem['id']}
        ";
        
        Database::iud($updateQuery);
        echo json_encode(['success' => true, 'message' => 'Cart updated successfully']);
        
    } else {
        // New item - insert
        $colorValue = $colorId ? $colorId : 'NULL';
        $insertQuery = "
            INSERT INTO cart_session (session_id, product_id, qty, color_id, added_at) 
            VALUES ('{$sessionId}', {$productId}, {$quantity}, {$colorValue}, NOW())
        ";
        
        Database::iud($insertQuery);
        echo json_encode(['success' => true, 'message' => 'Added to cart successfully']);
    }
}
?>