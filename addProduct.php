<?php
// addProduct.php
require 'connection.php';
Database::setUpConnection();
$cn = Database::$connection;
$cn->set_charset('utf8mb4');

// ---- Read POST (keys must match JS) ----
$title   = $_POST['pName']     ?? '';
$desc    = $_POST['pDesc']     ?? '';   // HTML from custom editor
$price   = $_POST['pPrice']    ?? '0';
$catId   = $_POST['pCategory'] ?? '0';
$brandId = $_POST['pBrand']    ?? '0';
$qty     = $_POST['pStock']    ?? '0';
$status  = $_POST['pStatus']   ?? '0';

// Colors (array)
$colorIds = isset($_POST['pColor']) ? (array)$_POST['pColor'] : [];

// ---- Basic validation ----
if ($title === '' || !is_numeric($price) || !is_numeric($qty) || !is_numeric($catId) || !is_numeric($brandId)) {
  http_response_code(400);
  echo "Invalid inputs";
  exit;
}

// ---- Ensure column exists: product.description (TEXT) ----
// If your schema uses another name (e.g., details), change the SQL below accordingly.

// ---- Insert product ----
$stmt = $cn->prepare("
  INSERT INTO product
    (title, description, price, qty, category_id, brand_id, product_status_id, create_at)
  VALUES
    (?, ?, ?, ?, ?, ?, ?, NOW())
");
if (!$stmt) {
  echo "Prepare failed: " . $cn->error;
  exit;
}
$stmt->bind_param("ssdiisi", $title, $desc, $price, $qty, $catId, $brandId, $status);
if (!$stmt->execute()) {
  echo "DB error: " . $stmt->error;
  exit;
}
$productId = $stmt->insert_id;
$stmt->close();

// ---- Optional: link colors if mapping table exists ----
if (!empty($colorIds)) {
  $mapTable = null;
  $res = $cn->query("SHOW TABLES LIKE 'product_color'");
  if ($res && $res->num_rows) $mapTable = 'product_color';
  else {
    $res2 = $cn->query("SHOW TABLES LIKE 'product_has_color'");
    if ($res2 && $res2->num_rows) $mapTable = 'product_has_color';
  }
  if ($mapTable) {
    $sql = "INSERT INTO `$mapTable` (product_id, color_id) VALUES (?, ?)";
    if ($ps = $cn->prepare($sql)) {
      foreach ($colorIds as $cid) {
        $cid = (int)$cid;
        $ps->bind_param("ii", $productId, $cid);
        $ps->execute();
      }
      $ps->close();
    }
  }
}

// ---- Handle images ----
$uploadDir = __DIR__ . '/uploads/products/';
if (!is_dir($uploadDir)) {
  @mkdir($uploadDir, 0777, true);
}

if (!empty($_FILES['images']['name']) && is_array($_FILES['images']['name'])) {
  // table: product_img (product_id, img_url)
  if ($ps = $cn->prepare("INSERT INTO product_img (product_id, img_url) VALUES (?, ?)")) {
    for ($i = 0; $i < count($_FILES['images']['name']); $i++) {
      if ($_FILES['images']['error'][$i] === UPLOAD_ERR_OK) {
        $tmp  = $_FILES['images']['tmp_name'][$i];
        $name = $_FILES['images']['name'][$i];
        $ext  = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        $safe = preg_replace('/[^a-zA-Z0-9_\-]/', '_', pathinfo($name, PATHINFO_FILENAME));
        $file = $safe . '_' . uniqid() . '.' . $ext;
        if (move_uploaded_file($tmp, $uploadDir . $file)) {
          $relPath = 'uploads/products/' . $file;
          $ps->bind_param("is", $productId, $relPath);
          $ps->execute();
        }
      }
    }
    $ps->close();
  }
}

echo "success";
