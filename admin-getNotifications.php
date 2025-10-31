<?php
session_start();
include 'connection.php';
header('Content-Type: application/json');

// Helper function to format time difference
function time_ago($datetime, $full = false) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array(
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    );
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ago' : 'just now';
}

// Check session
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['ok' => false, 'error' => 'Not authenticated.']);
    exit();
}

// --- Fetch Notifications ---
$notifications = [];
$unread_count = 0;
// We will consider notifications "unread" if they are from the last 7 days
$seven_days_ago = (new DateTime())->modify('-7 days')->format('Y-m-d H:i:s');


// 1. New Orders (Pending)
$new_order_rs = Database::search("
    SELECT i.order_id, i.created_at 
    FROM invoice i 
    JOIN status s ON i.status_id = s.id 
    WHERE s.name = 'Pending' AND i.created_at >= '" . $seven_days_ago . "'
    GROUP BY i.order_id
    ORDER BY i.created_at DESC
");

if ($new_order_rs && $new_order_rs->num_rows > 0) {
    $unread_count += $new_order_rs->num_rows;
    while ($row = $new_order_rs->fetch_assoc()) {
        $notifications[] = [
            'icon' => 'bi-box-seam-fill',
            'color' => 'text-success',
            'title' => 'New Order',
            'message' => 'Order ' . htmlspecialchars($row['order_id']) . ' was placed.',
            'time' => time_ago($row['created_at'])
        ];
    }
}

// 2. Low Stock (qty <= 5)
// Define low stock threshold
$low_stock_threshold = 5;
$low_stock_rs = Database::search("
    SELECT title, qty 
    FROM product 
    WHERE qty <= " . $low_stock_threshold . " AND qty > 0
    ORDER BY qty ASC
");

if ($low_stock_rs && $low_stock_rs->num_rows > 0) {
    // We only show one alert for low stock, but count all for the badge
    $unread_count += $low_stock_rs->num_rows;
    $first_low_stock = $low_stock_rs->fetch_assoc();
    $notifications[] = [
        'icon' => 'bi-exclamation-triangle-fill',
        'color' => 'text-warning',
        'title' => 'Low Stock Alert',
        'message' => '"' . htmlspecialchars($first_low_stock['title']) . '" has only ' . $first_low_stock['qty'] . ' items left.',
        'time' => 'Inventory' // Not time-based
    ];
}

// 3. New Customers (Joined in last 7 days)
$new_user_rs = Database::search("
    SELECT name, joined_date 
    FROM user 
    WHERE joined_date >= '" . $seven_days_ago . "'
    ORDER BY joined_date DESC
");

if ($new_user_rs && $new_user_rs->num_rows > 0) {
    $unread_count += $new_user_rs->num_rows;
    $first_new_user = $new_user_rs->fetch_assoc();
    $notifications[] = [
        'icon' => 'bi-person-plus-fill',
        'color' => 'text-primary',
        'title' => 'New Customer',
        'message' => htmlspecialchars($first_new_user['name']) . ' registered an account.',
        'time' => time_ago($first_new_user['joined_date'])
    ];
}


// --- Final Output ---
// Sort notifications by time (newest first), but keep low stock at the top
usort($notifications, function($a, $b) {
    if ($a['title'] == 'Low Stock Alert') return -1;
    if ($b['title'] == 'Low Stock Alert') return 1;
    return strtotime($b['time'] ?? 'now') - strtotime($a['time'] ?? 'now');
});

echo json_encode([
    'ok' => true,
    'notifications' => array_slice($notifications, 0, 5), // Send max 5
    'unread_count' => $unread_count
]);
?>

