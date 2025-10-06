<?php
session_start();
require_once 'connection.php';

header('Content-Type: application/json');

$index = isset($_POST['index']) ? (int)$_POST['index'] : -1;

if ($index < 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

// Check if user is logged in
if (isset($_SESSION['user']) && isset($_SESSION['user']['id'])) {
    // Logged-in user
    $userId = (int)$_SESSION['user']['id'];
    
    // Get all cart items for this user
    $cartQuery = "SELECT id FROM cart WHERE user_id = {$userId} ORDER BY id";
    $cartResult = Database::search($cartQuery);
    
    if ($cartResult && $cartResult->num_rows > $index) {
        $items = [];
        while ($row = $cartResult->fetch_assoc()) {
            $items[] = $row['id'];
        }
        
        $cartId = $items[$index];
        $deleteQuery = "DELETE FROM cart WHERE id = {$cartId}";
        Database::iud($deleteQuery);
        
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Cart item not found']);
    }
    
} else {
    // Guest user
    $sessionId = session_id();
    
    $cartQuery = "SELECT id FROM cart_session WHERE session_id = '{$sessionId}' ORDER BY id";
    $cartResult = Database::search($cartQuery);
    
    if ($cartResult && $cartResult->num_rows > $index) {
        $items = [];
        while ($row = $cartResult->fetch_assoc()) {
            $items[] = $row['id'];
        }
        
        $cartId = $items[$index];
        $deleteQuery = "DELETE FROM cart_session WHERE id = {$cartId}";
        Database::iud($deleteQuery);
        
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Cart item not found']);
    }
}
?>