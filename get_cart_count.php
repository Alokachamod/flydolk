<?php
require_once 'connection.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');
$cartCount = 0;

if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
    $userId = (int)$_SESSION['user_id'];
    $cart_sql = "SELECT COUNT(id) AS cart_count FROM cart WHERE user_id = $userId";
    $cart_rs = Database::search($cart_sql);
    
    if ($cart_rs->num_rows > 0) {
        $cart_data = $cart_rs->fetch_assoc();
        $cartCount = (int)$cart_data['cart_count'];
    }
}

echo json_encode(['cart_count' => $cartCount]);
?>
