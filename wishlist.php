<?php
require_once 'connection.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set header to return JSON
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Please log in to manage your wishlist.']);
    exit;
}

// Check if product_id is set
if (!isset($_POST['product_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
    exit;
}

$user_id = (int)$_SESSION['user_id'];
$product_id = (int)$_POST['product_id'];

if ($product_id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid product.']);
    exit;
}

try {
    // Check if the item is already in the wishlist
    $check_sql = "SELECT * FROM wishlist WHERE user_id = $user_id AND product_id = $product_id";
    $wishlist_rs = Database::search($check_sql);

    if ($wishlist_rs->num_rows > 0) {
        // Item exists, so remove it
        $delete_sql = "DELETE FROM wishlist WHERE user_id = $user_id AND product_id = $product_id";
        Database::iud($delete_sql);
        echo json_encode(['status' => 'removed', 'message' => 'Removed from wishlist.']);
    } else {
        // Item does not exist, so add it
        $insert_sql = "INSERT INTO wishlist (user_id, product_id) VALUES ($user_id, $product_id)";
        Database::iud($insert_sql);
        echo json_encode(['status' => 'added', 'message' => 'Added to wishlist!']);
    }

} catch (Exception $e) {
    // Handle potential duplicate entry error if UNIQUE key is violated (race condition)
    if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
        echo json_encode(['status' => 'error', 'message' => 'An error occurred. Please try again.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    }
}
?>
