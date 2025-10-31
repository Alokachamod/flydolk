<?php
session_start();
// Suppress all HTML errors to ensure we only output JSON
ini_set('display_errors', 0);
error_reporting(0);

include 'connection.php';

// Set header to JSON
header('Content-Type: application/json');

// Helper function to send JSON error
function send_error($message, $debug_info = "")
{
    $response = ['ok' => false, 'error' => $message];
    if (!empty($debug_info)) {
        $response['debug'] = $debug_info;
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

// [FIX] Treat order_id as a string and escape it
$order_id = addslashes($_POST['order_id']);
if (empty($order_id)) {
    send_error('Invalid Order ID.');
}

// We will wrap each query in its own try/catch to find the error
$order_data = null;
$address_details = null;
$items_list = [];
$all_statuses = [];

// --- Query 1: Get Order Data ---
// [FIX] Use quotes around $order_id
try {
    $order_sql = "
        SELECT 
            invoice.user_has_address_id,
            invoice.status_id,
            invoice.created_at,
            user.name AS customer_name, 
            user.email AS customer_email, 
            user.mobile AS customer_mobile,
            user_has_address.id AS address_book_id
        FROM invoice
        LEFT JOIN user_has_address ON invoice.user_has_address_id = user_has_address.id
        LEFT JOIN user ON user_has_address.user_id = user.id
        WHERE invoice.order_id = '" . $order_id . "'
        LIMIT 1
    ";
    $order_rs = Database::search($order_sql);

    if (!$order_rs) {
        send_error('Query 1 (Order) failed.', 'Check table/column names: invoice, user_has_address, user');
    }
    if ($order_rs->num_rows == 0) {
        // This error now only happens if the order_id itself is not in the invoice table
        send_error('Order ID not found in invoice table.');
    }
    $order_data = $order_rs->fetch_assoc();

} catch (Exception $e) {
    send_error('PHP Exception on Query 1 (Order)', $e->getMessage());
}

// --- Query 2: Get Address Data ---
try {
    $address_book_id = $order_data['address_book_id'];
    
    // Check if the address_book_id itself was NULL
    if (empty($address_book_id)) {
        $address_details = [
            'line1' => 'No Address Linked', 'line2' => '', 'city' => 'N/A', 
            'district' => 'N/A', 'province' => 'N/A', 'zip_code' => 'N/A'
        ];
    } else {
        $address_sql = "
            SELECT 
                user_has_address.address_line_1 AS line1, 
                user_has_address.address_line_2 AS line2, 
                user_has_address.zip_code,
                city.name AS city, 
                district.name AS district, 
                province.name AS province 
            FROM user_has_address
            LEFT JOIN city ON user_has_address.city_id = city.id
            LEFT JOIN district ON city.district_id = district.id
            LEFT JOIN province ON district.province_id = province.id
            WHERE user_has_address.id = " . (int)$address_book_id . "
        ";
        $address_rs = Database::search($address_sql);

        if (!$address_rs) {
             send_error('Query 2 (Address) failed.', 'Check table/column names: user_has_address, city, district, province');
        }
        
        if($address_rs->num_rows > 0) {
            $address_details = $address_rs->fetch_assoc();
        } else {
            $address_details = [
                'line1' => 'Address not found', 'line2' => '', 'city' => 'N/A', 
                'district' => 'N/A', 'province' => 'N/A', 'zip_code' => 'N/A'
            ];
        }
    }
} catch (Exception $e) {
    send_error('PHP Exception on Query 2 (Address)', $e->getMessage());
}

// --- Query 3: Get Items Data ---
try {
    // [FIX] Use quotes around $order_id
    $items_sql = "
        SELECT 
            invoice.qty,
            invoice.unit_price,
            invoice.total_amount,
            product.title,
            (SELECT img_url FROM product_img WHERE product_id = product.id ORDER BY img_url ASC LIMIT 1) AS img
        FROM invoice
        JOIN product ON invoice.product_id = product.id
        WHERE invoice.order_id = '" . $order_id . "'
    ";
    $items_rs = Database::search($items_sql);

    if (!$items_rs) {
        send_error('Query 3 (Items) failed.', 'Check table/column names: invoice, product, product_img');
    }
     if ($items_rs->num_rows == 0) {
        send_error('No items found for this order.');
    }

    while ($item = $items_rs->fetch_assoc()) {
        $items_list[] = $item;
    }
} catch (Exception $e) {
    send_error('PHP Exception on Query 3 (Items)', $e->getMessage());
}
    
// --- Query 4: Get Statuses Data ---
try {
    $statuses_sql = "SELECT * FROM `status`"; 
    $all_statuses_rs = Database::search($statuses_sql); 

    if (!$all_statuses_rs) {
        send_error('Query 4 (Statuses) failed.', 'Check table name: status');
    }

    while ($status = $all_statuses_rs->fetch_assoc()) {
        $all_statuses[] = $status;
    }
} catch (Exception $e) {
    send_error('PHP Exception on Query 4 (Statuses)', $e->getMessage());
}

// --- If all queries succeeded, build and send the response ---

$subtotal = 0;
foreach ($items_list as $item) {
    $subtotal += (float)$item['total_amount'];
}
$delivery_fee = 0.00; 
$total = $subtotal + $delivery_fee;

$order_details = [
    'id' => $order_id, // Send back the string ID
    'date' => $order_data['created_at'], // [FIX] Use the date from Query 1
    'status_id' => (int)$order_data['status_id'], 
    'customer_name' => $order_data['customer_name'] ?? 'Unknown User',
    'customer_email' => $order_data['customer_email'] ?? 'N/A',
    'customer_mobile' => $order_data['customer_mobile'] ?? 'N/A',
    'delivery_fee' => $delivery_fee,
    'subtotal' => $subtotal,
    'total' => $total
];

$address_details_safe = [
    'line1' => $address_details['line1'] ?? 'N/A',
    'line2' => $address_details['line2'] ?? '',
    'city' => $address_details['city'] ?? 'N/A',
    'district' => $address_details['district'] ?? 'N/A',
    'province' => $address_details['province'] ?? 'N/A',
    'zip_code' => $address_details['zip_code'] ?? 'N/A',
];


echo json_encode([
    'ok' => true,
    'order' => $order_details,
    'address' => $address_details_safe,
    'items' => $items_list,
    'all_statuses' => $all_statuses
]);
?>

