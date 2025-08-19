<?php
// updateProductSimple.php
session_start();
require 'connection.php';
if (!isset($_SESSION['admin_id'])) { echo "Please login"; exit; }

$id          = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$title       = $_POST['title'] ?? '';
$description = $_POST['description'] ?? '';
$price       = $_POST['price'] ?? '0';
$qty         = isset($_POST['qty']) ? (int)$_POST['qty'] : 0;
$category_id = isset($_POST['category_id']) ? (int)$_POST['category_id'] : 0;
$brand_id    = isset($_POST['brand_id']) ? (int)$_POST['brand_id'] : 0;
$status_id   = isset($_POST['status_id']) ? (int)$_POST['status_id'] : 0;
$colorsCsv   = $_POST['colors'] ?? ''; // "1,3,5"

if ($id<=0 || $title==='') { echo "Missing required fields"; exit; }

// basic escaping (depends on your Database helper)
$titleEsc = Database::$connection->real_escape_string($title);
$descEsc  = Database::$connection->real_escape_string($description);
$priceEsc = Database::$connection->real_escape_string($price);

$colStatus = "product_status_id";
$rsCol = Database::search("SHOW COLUMNS FROM product LIKE 'product_status_id'");
if (!$rsCol || $rsCol->num_rows==0) $colStatus = "status_id";

Database::iud("
  UPDATE product SET
    title = '{$titleEsc}',
    description = '{$descEsc}',
    price = '{$priceEsc}',
    qty = {$qty},
    category_id = {$category_id},
    brand_id = {$brand_id},
    {$colStatus} = {$status_id}
  WHERE id = {$id}
");

Database::iud("DELETE FROM product_color WHERE product_id = {$id}");
$colors = array_filter(array_map('intval', explode(',', $colorsCsv)));
foreach ($colors as $cid) {
  Database::iud("INSERT INTO product_color (product_id, color_id) VALUES ({$id}, {$cid})");
}

echo "success";
