<?php
// getProductSimple.php
session_start();

// Use an absolute path so it works no matter which folder this file is in.
require __DIR__ . '/connection.php';

header('Content-Type: application/json; charset=utf-8');

// Must be logged-in admin
if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Please login']);
    exit;
}

// Validate product id
$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
if ($id <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid product id']);
    exit;
}

// Load product
$rs = Database::search("
    SELECT 
        id,
        title,
        COALESCE(description, '') AS description,
        price,
        qty,
        category_id,
        brand_id,
        COALESCE(product_status_id, 0) AS status_id
    FROM product
    WHERE id = {$id}
    LIMIT 1
");

if (!$rs || $rs->num_rows === 0) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Product not found']);
    exit;
}

$p = $rs->fetch_assoc();

// Load colors
$colors = [];
$rc = Database::search("SELECT color_id FROM product_has_color WHERE product_id = {$id}");
if ($rc) {
    while ($row = $rc->fetch_assoc()) {
        $colors[] = (int)$row['color_id'];
    }
}

echo json_encode([
    'success' => true,
    'data' => [
        'id'          => (int)$p['id'],
        'title'       => (string)$p['title'],
        'description' => (string)$p['description'],
        'price'       => (float)$p['price'],
        'qty'         => (int)$p['qty'],
        'category_id' => (int)$p['category_id'],
        'brand_id'    => (int)$p['brand_id'],
        'status_id'   => (int)$p['status_id'],
        'colors'      => $colors
    ]
]);
