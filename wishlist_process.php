<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'connection.php';
header('Content-Type: application/json');

$response = ['status' => 'error', 'message' => 'An unknown error occurred.'];

try {
    // 1. CHECK LOGIN
    if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
        throw new Exception('You must be logged in to manage your wishlist.');
    }
    $user_id = (int)$_SESSION['user_id'];
    Database::setUpConnection();

    // --- ROUTE 1: ADD TO WISHLIST (from shop.php or single_product.php) ---
    if (isset($_POST['product_id'])) {
        $product_id = (int)$_POST['product_id'];
        
        // Check if product is already in wishlist
        $check_rs = Database::search("
            SELECT id FROM wishlist 
            WHERE user_id = $user_id AND product_id = $product_id
        ");
        
        if ($check_rs->num_rows == 0) {
            // Not in wishlist, so add it
            Database::iud("
                INSERT INTO wishlist (user_id, product_id) 
                VALUES ($user_id, $product_id)
            ");
            $response['status'] = 'success';
            $response['message'] = 'Product added to your wishlist!';
            $response['action'] = 'added';
        } else {
            // Already in wishlist, so remove it
             Database::iud("
                DELETE FROM wishlist 
                WHERE user_id = $user_id AND product_id = $product_id
            ");
            $response['status'] = 'success';
            $response['message'] = 'Product removed from your wishlist!';
            $response['action'] = 'removed';
        }
    } 
    
    // --- ROUTE 2: REMOVE FROM WISHLIST (from wishlist.php) ---
    else if (isset($_POST['wishlist_id'])) {
        $wishlist_id = (int)$_POST['wishlist_id'];
        
        // Delete by wishlist_id, ensuring it belongs to the user
        Database::iud("
            DELETE FROM wishlist 
            WHERE id = $wishlist_id AND user_id = $user_id
        ");
        
        $response['status'] = 'success';
        $response['message'] = 'Item removed from wishlist.';
    } 
    
    // --- NO ACTION ---
    else {
        throw new Exception('Invalid request.');
    }

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>

