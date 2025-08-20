<?php
session_start();
require "connection.php"; // provides Database::search / ::iud

if (!isset($_SESSION["admin_id"])) { echo "Please login first."; exit; }
if (empty($_POST["productId"]))   { echo "Product id missing"; exit; }

$pid = (int)$_POST["productId"];
if ($pid <= 0) { echo "Invalid product id"; exit; }

// ---------- helpers ----------
function tblExists($name){
    $r = Database::search("SHOW TABLES LIKE '$name'");
    return ($r && $r->num_rows > 0);
}

// ---------- 1) If invoices reference this product -> SOFT DELETE ----------
$hasInvoices = 0;
if (tblExists('invoice')) {
    $r = Database::search("SELECT COUNT(*) AS c FROM invoice WHERE product_id = $pid");
    if ($r) { $hasInvoices = ((int)$r->fetch_assoc()['c'] > 0); }
}

if ($hasInvoices) {
    // try to pick a good status id (archived/inactive/deleted…)
    $inactiveId = 0;
    if (tblExists('product_status')) {
        $rs = Database::search("SELECT id, name FROM product_status");
        if ($rs) {
            while ($row = $rs->fetch_assoc()) {
                $n = strtolower(trim($row['name']));
                if (in_array($n, ['archived','inactive','deleted','not for sale','disabled','hidden'])) {
                    $inactiveId = (int)$row['id'];
                    break;
                }
            }
        }
    }

    $sql = "UPDATE product SET qty = 0";
    if ($inactiveId > 0) { $sql .= ", product_status_id = $inactiveId"; }
    $sql .= " WHERE id = $pid";
    Database::iud($sql);

    // Return success (front-end already checks for "success")
    echo "success";  // soft-deleted due to invoices
    exit;
}

// ---------- 2) HARD DELETE path (no invoices) ----------
// Collect image paths to remove from disk after commit
$imgPaths = [];
if (tblExists('product_img')) {
    $rs = Database::search("SELECT img_url AS p FROM product_img WHERE product_id = $pid");
    if ($rs) { while ($row = $rs->fetch_assoc()) { if (!empty($row['p'])) $imgPaths[] = $row['p']; } }
}

try {
    Database::iud("START TRANSACTION");

    // Delete child/mapping rows first (per ERD)
    if (tblExists('product_has_color')) {
        Database::iud("DELETE FROM product_has_color WHERE product_id = $pid");
    }
    if (tblExists('product_img')) {
        Database::iud("DELETE FROM product_img WHERE product_id = $pid");
    }

    // Finally, delete the product
    $affected = Database::iud("DELETE FROM product WHERE id = $pid");
    if ($affected === 0) {
        Database::iud("ROLLBACK");
        echo "Product not found";
        exit;
    }

    Database::iud("COMMIT");

    // Best-effort file removal (after commit)
    foreach ($imgPaths as $p) {
        $clean = str_replace(["..", "\0"], "", $p);
        if (is_file($clean)) { @unlink($clean); }
        elseif (is_file(__DIR__ . "/" . $clean)) { @unlink(__DIR__ . "/" . $clean); }
    }

    echo "success";  // hard-deleted
} catch (Throwable $e) {
    try { Database::iud("ROLLBACK"); } catch (Throwable $ignore) {}
    echo "Error deleting product: " . $e->getMessage();
}
