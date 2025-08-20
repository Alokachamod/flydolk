<?php
// updateProduct.php
session_start();
require 'connection.php';

if (!isset($_SESSION['admin_id'])) { echo "Please login"; exit; }

/* ---------------- helpers ---------------- */
function str_clean($v){ return trim((string)$v); }
function sql_esc_basic($s){
    // portable escaping without relying on mysqli handle
    $s = str_clean($s);
    if (function_exists('mb_substr')) { $s = mb_substr($s, 0, 50000); }
    return str_replace(
        ["\\",  "\0",  "\n", "\r", "\x1a",  "'",  '"'],
        ["\\\\","\\0","\\n","\\r","\\Z", "\\'", '\\"'],
        $s
    );
}
function only_number($s){
    // keep digits and dot (in case of decimals)
    $s = preg_replace('/[^0-9.]/', '', (string)$s);
    if ($s === '' ) { $s = '0'; }
    return $s;
}

/* --------------- read & validate --------- */
$id          = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$title       = str_clean($_POST['title'] ?? '');
$description = str_clean($_POST['description'] ?? '');
$price       = only_number($_POST['price'] ?? '0');
$qty         = isset($_POST['qty']) ? (int)$_POST['qty'] : 0;
$category_id = isset($_POST['category_id']) ? (int)$_POST['category_id'] : 0;
$brand_id    = isset($_POST['brand_id']) ? (int)$_POST['brand_id'] : 0;
$status_id   = isset($_POST['status_id']) ? (int)$_POST['status_id'] : 0;
$colorsCsv   = str_clean($_POST['colors'] ?? ''); // "1,3,5"

if ($id <= 0 || $title === '') { echo "Missing required fields"; exit; }

/* --------------- escape strings ---------- */
$titleEsc = sql_esc_basic($title);
$descEsc  = sql_esc_basic($description);
$priceEsc = sql_esc_basic($price);

/* --------------- detect status column ---- */
$colStatus = 'product_status_id';
$chkCol = Database::search("SHOW COLUMNS FROM `product` LIKE 'product_status_id'");
if (!$chkCol || $chkCol->num_rows == 0) { $colStatus = 'status_id'; }

/* --------------- update product ---------- */
Database::iud("
  UPDATE `product` SET
    `title` = '{$titleEsc}',
    `description` = '{$descEsc}',
    `price` = '{$priceEsc}',
    `qty` = {$qty},
    `category_id` = {$category_id},
    `brand_id` = {$brand_id},
    `{$colStatus}` = {$status_id}
  WHERE `id` = {$id}
");

/* --------------- colors junction table --- */
$junction = 'product_has_color';
$chkJ = Database::search("SHOW TABLES LIKE 'product_has_color'");
if (!$chkJ || $chkJ->num_rows == 0) { $junction = 'product_color'; }

/* --------------- resync colors ----------- */
Database::iud("DELETE FROM `{$junction}` WHERE `product_id` = {$id}");

$colors = array_filter(
  array_map('intval', preg_split('/\s*,\s*/', $colorsCsv, -1, PREG_SPLIT_NO_EMPTY)),
  fn($v) => $v > 0
);
foreach ($colors as $cid) {
  Database::iud("INSERT INTO `{$junction}` (`product_id`, `color_id`) VALUES ({$id}, {$cid})");
}

echo "success";
