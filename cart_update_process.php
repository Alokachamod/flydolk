<?php
require_once 'connection.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: application/json');

// --- CHECK LOGIN ---
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Please log in to manage your cart.']);
    exit;
}
$user_id = (int)$_SESSION['user_id'];

// --- GET POST DATA ---
$action = $_POST['action'] ?? null;
$cart_id = (int)($_POST['cart_id'] ?? 0);

if (empty($action) || empty($cart_id)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
    exit;
}

Database::setUpConnection();

try {
    if ($action === 'update') {
        // --- UPDATE QUANTITY ---
        $qty = (int)($_POST['qty'] ?? 1);
        if ($qty < 1) {
            $qty = 1;
        }

        // Check stock
        $stock_check_rs = Database::search("
            SELECT p.qty 
            FROM product p
            JOIN cart c ON p.id = c.product_id
            WHERE c.id = $cart_id AND c.user_id = $user_id
        ");

        if ($stock_check_rs->num_rows > 0) {
            $stock = $stock_check_rs->fetch_assoc()['qty'];
            if ($qty > $stock) {
                echo json_encode(['status' => 'error', 'message' => 'Not enough stock. Only ' . $stock . ' items available.']);
                exit;
            }
            
            // Update quantity
            Database::iud("UPDATE cart SET qty = $qty, updated_at = NOW() WHERE id = $cart_id AND user_id = $user_id");
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Item not found in your cart.']);
        }

    } elseif ($action === 'remove') {
        // --- REMOVE ITEM ---
        Database::iud("DELETE FROM cart WHERE id = $cart_id AND user_id = $user_id");
        
        if (Database::$connection->affected_rows > 0) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Item not found or already removed.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid action.']);
    }

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'An error occurred: ' . $e->getMessage()]);
}
?>

