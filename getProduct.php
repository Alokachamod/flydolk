<?php
// getProductSimple.php
session_start();
require 'connection.php';
if (!isset($_SESSION['admin_id'])) {
    echo "Please login";
    exit;
}

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
if ($id <= 0) {
    echo "Invalid product id";
    exit;
}

$rs = Database::search("
  SELECT id, title,
         COALESCE(description, '') AS description,
         price, qty, category_id, brand_id,
         COALESCE(product_status_id, product_status_id, 0) AS status_id
  FROM product
  WHERE id = {$id}
  LIMIT 1
");
if (!$rs || $rs->num_rows == 0) {
    echo "Product not found";
    exit;
}
$p = $rs->fetch_assoc();

$colors = [];
$rc = Database::search("SELECT color_id FROM product_has_color WHERE product_id = {$id}");
if ($rc) {
    while ($c = $rc->fetch_assoc()) {
        $colors[] = (int)$c['color_id'];
    }
}

function clean($s)
{
    $s = str_replace(["\r", "\n", "|"], " ", (string)$s);
    return trim($s);
}

echo "success"
   ."|".$p['id']
   ."|".cleanPipe($p['title'])
   ."|".cleanPipe($p['description'])
   ."|".$p['price']
   ."|".(int)$p['qty']
   ."|".(int)$p['category_id']
   ."|".(int)$p['brand_id']
   ."|".(int)$p['status_id']
   ."|".implode(",", $colors);

