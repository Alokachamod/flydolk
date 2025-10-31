<?php
session_start();
include 'connection.php';

header('Content-Type: application/json');

function send_error($message)
{
    echo json_encode(['ok' => false, 'error' => $message]);
    exit();
}

if (!isset($_SESSION['admin_id'])) {
    send_error('Not authenticated.');
}

if (!isset($_POST['order_id']) || !isset($_POST['status_id'])) {
    send_error('Missing parameters. Order ID or Status ID not provided.');
}

// [FIX] Treat order_id as a string and escape it
$order_id = addslashes($_POST['order_id']);
$status_id = (int)$_POST['status_id']; // Status ID is still a number

if (empty($order_id) || $status_id <= 0) {
    send_error('Invalid Order ID or Status ID.');
}

try {
    // Check if status exists
    $status_rs = Database::search("SELECT * FROM `status` WHERE id = " . $status_id); 
    if (!$status_rs) {
         send_error('Database query failed (status check).');
    }
    if ($status_rs->num_rows == 0) {
        send_error('Invalid status ID.');
    }

    // [FIX] Update all rows matching the string `order_id`
    $update_sql = "
        UPDATE invoice 
        SET status_id = " . $status_id . " 
        WHERE order_id = '" . $order_id . "'
    ";
    Database::search($update_sql);
    
    echo json_encode(['ok' => true, 'message' => 'Status updated successfully.']);

} catch (Exception $e) {
    send_error('Database error: ' . $e->getMessage());
}
?>

