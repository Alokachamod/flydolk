<?php
session_start();
include 'connection.php';

// Set header to JSON
header('Content-Type: application/json');

// Helper function to send JSON error
function send_error($message, $sql = "")
{
    $response = ['ok' => false, 'error' => $message];
    if (!empty($sql)) {
        // In a real app, you'd log this internally and only show a generic error
        $response['error'] = "A database query failed. Check server logs.";
        // For debugging, you can uncomment the line below to see the query
        // $response['debug_query'] = htmlspecialchars($sql);
    }
    echo json_encode($response);
    exit();
}

if (!isset($_SESSION['admin_id'])) {
    send_error('Not authenticated. Please log in again.');
}

if (!isset($_POST['order_id'])) {
    send_error('Order ID not provided.');
}

$order_id = (int)$_POST['order_id'];

try {
    // 1. Get main order details
    $order_sql = "
        SELECT 
            invoice.*, 
            user.name AS customer_name, 
            user.email AS customer_email, 
            user.mobile AS customer_mobile,
            user_has_address.id AS address_book_id
        FROM invoice
        JOIN user_has_address ON invoice.user_has_address_id = user_has_address.id
        JOIN user ON user_has_address.user_id = user.id
        WHERE invoice.id = '" . $order_id . "'
    ";
    $order_rs = Database::search($order_sql);

    if (!$order_rs) {
        send_error('Database query failed (order).', $order_sql);
    }
    if ($order_rs->num_rows == 0) {
        send_error('Order not found.');
    }
    $order_data = $order_rs->fetch_assoc();

    // ... (rest of order_details calculation) ...
    $delivery_fee = (float)$order_data['delivery_fee'];
    $total = (float)$order_data['total'];
    $subtotal = $total - $delivery_fee;

    $order_details = [
        'id' => $order_data['id'],
        'date' => $order_data['created_at'], 
        'status_id' => (int)$order_data['status_id'], 
        'customer_name' => $order_data['customer_name'],
        'customer_email' => $order_data['customer_email'],
        'customer_mobile' => $order_data['customer_mobile'],
        'delivery_fee' => $delivery_fee,
        'subtotal' => $subtotal,
        'total' => $total
    ];

    // 2. Get shipping address details
    $address_book_id = $order_data['address_book_id'];
    
    // [FIX] Changed `address.line1` to `address.address_line_1` and aliased them
    // This is based on your `admin-userManagement.php` file
    $address_sql = "
        SELECT 
            address.address_line_1 AS line1, 
            address.address_line_2 AS line2, 
            city.name AS city, 
            district.name AS district, 
            province.name AS province, 
            address.zip_code
        FROM user_has_address
        JOIN address ON user_has_address.address_id = address.id
        JOIN city ON address.city_id = city.id
        JOIN district ON city.district_id = district.id
        JOIN province ON district.province_id = province.id
        WHERE user_has_address.id = '" . $address_book_id . "'
    ";
    $address_rs = Database::search($address_sql);

    if (!$address_rs) {
        send_error('Database query failed (address).', $address_sql);
    }
    
    if($address_rs->num_rows > 0) {
        $address_details = $address_rs->fetch_assoc();
    } else {
        $address_details = [
            'line1' => 'N/A', 'line2' => '', 'city' => 'N/A', 
            'district' => 'N/A', 'province' => 'N/A', 'zip_code' => 'N/A'
        ];
    }

    // 3. Get all items in the order
    $items_sql = "
        SELECT 
            invoice_item.qty,
            product.title,
            product.price,
            (SELECT img_url FROM product_img WHERE product_id = product.id ORDER BY id ASC LIMIT 1) AS img
        FROM invoice_item
        JOIN product ON invoice_item.product_id = product.id
        WHERE invoice_item.invoice_id = '" . $order_id . "'
    ";
    $items_rs = Database::search($items_sql);

    if (!$items_rs) {
        send_error('Database query failed (items).', $items_sql);
    }

    $items_list = [];
    while ($item = $items_rs->fetch_assoc()) {
        $items_list[] = $item;
    }
    
    // 4. Get all possible order statuses
    $statuses_sql = "SELECT * FROM `status`"; 
    $all_statuses_rs = Database::search($statuses_sql); 

    if (!$all_statuses_rs) {
        send_error('Database query failed (statuses).', $statuses_sql);
    }

    $all_statuses = [];
    while ($status = $all_statuses_rs->fetch_assoc()) {
        $all_statuses[] = $status;
    }

    // 5. Send the complete response
    echo json_encode([
        'ok' => true,
        'order' => $order_details,
        'address' => $address_details,
        'items' => $items_list,
        'all_statuses' => $all_statuses
    ]);

} catch (Exception $e) {
    send_error('PHP Exception: ' . $e->getMessage());
}
?>

