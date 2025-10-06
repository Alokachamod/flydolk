<?php
session_start();
require_once 'connection.php';

header('Content-Type: application/json');

$index = isset($_POST['index']) ? (int)$_POST['index'] : -1;
$quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

if ($index < 0 || $quantity < 1) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

// Check if user is logged in
if (isset($_SESSION['user']) && isset($_SESSION['user']['id'])) {
    // Logged-in user
    $userId = (int)$_SESSION['user']['id'];
    
    // Get all cart items for this user
    $cartQuery = "SELECT id, product_id FROM cart WHERE user_id = {$userId} ORDER BY id";
    $cartResult = Database::search($cartQuery);
    
    if ($cartResult && $cartResult->num_rows > $index) {
        $items = [];
        while ($row = $cartResult->fetch_assoc()) {
            $items[] = $row;
        }
        
        $cartId = $items[$index]['id'];
        $productId = $items[$index]['product_id'];
        
        // Check available stock
        $stockQuery = "SELECT qty FROM product WHERE id = {$productId}";
        $stockResult = Database::search($stockQuery);
        
        if ($stockResult && $stockResult->num_rows > 0) {
            $product = $stockResult->fetch_assoc();
            $availableQty = (int)$product['qty'];
            
            if ($quantity > $availableQty) {
                echo json_encode(['success' => false, 'message' => 'Not enough stock']);
                exit;
            }
            
            $updateQuery = "UPDATE cart SET qty = {$quantity}, updated_at = NOW() WHERE id = {$cartId}";
            Database::iud($updateQuery);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Product not found']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Cart item not found']);
    }
    
} else {
    // Guest user
    $sessionId = session_id();
    
    $cartQuery = "SELECT id, product_id FROM cart_session WHERE session_id = '{$sessionId}' ORDER BY id";
    $cartResult = Database::search($cartQuery);
    
    if ($cartResult && $cartResult->num_rows > $index) {
        $items = [];
        while ($row = $cartResult->fetch_assoc()) {
            $items[] = $row;
        }
        
        $cartId = $items[$index]['id'];
        $productId = $items[$index]['product_id'];
        
        // Check available stock
        $stockQuery = "SELECT qty FROM product WHERE id = {$productId}";
        $stockResult = Database::search($stockQuery);
        
        if ($stockResult && $stockResult->num_rows > 0) {
            $product = $stockResult->fetch_assoc();
            $availableQty = (int)$product['qty'];
            
            if ($quantity > $availableQty) {
                echo json_encode(['success' => false, 'message' => 'Not enough stock']);
                exit;
            }
            
            $updateQuery = "UPDATE cart_session SET qty = {$quantity}, updated_at = NOW() WHERE id = {$cartId}";
            Database::iud($updateQuery);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Product not found']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Cart item not found']);
    }
}
?>