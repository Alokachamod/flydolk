<?php
require 'connection.php'; // provides Database::iud(), Database::search(), Database::$connection

// ------------------------------------------------------------
// helpers
// ------------------------------------------------------------
function intOrNull($v) {
  if ($v === null || $v === '') return null;
  return (int)$v;
}
function esc($s) {
  return Database::$connection->real_escape_string($s ?? '');
}

// ------------------------------------------------------------
// read + validate fields
// ------------------------------------------------------------
$name     = trim($_POST['pName']     ?? '');
$desc     = trim($_POST['pDesc']     ?? '');
$price    = trim($_POST['pPrice']    ?? '');
$category = intOrNull($_POST['pCategory'] ?? null);
$brand    = intOrNull($_POST['pBrand']    ?? null);
$stock    = intOrNull($_POST['pStock']    ?? 0);
$status   = trim($_POST['pStatus']   ?? 'active'); // e.g., active/inactive
$colors   = $_POST['pColor'] ?? [];               // array of color IDs (optional)

// basic checks
if ($name === '' || $price === '' || $category === null || $brand === null) {
  http_response_code(400);
  echo "Missing required fields.";
  exit;
}
if (!is_numeric($price)) {
  http_response_code(400);
  echo "Invalid price.";
  exit;
}

// ------------------------------------------------------------
// files (IMPORTANT: PHP exposes 'images[]' as $_FILES['images'])
// ------------------------------------------------------------
$files = $_FILES['images'] ?? ($_FILES['images[]'] ?? null);

if (!$files || empty($files['name']) || (is_array($files['name']) && $files['name'][0] === '')) {
  http_response_code(400);
  echo "Please upload at least one image.";
  exit;
}

// upload target
$uploadDir = __DIR__ . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'products' . DIRECTORY_SEPARATOR;
if (!is_dir($uploadDir)) {
  // create directory tree if missing
  if (!mkdir($uploadDir, 0775, true) && !is_dir($uploadDir)) {
    http_response_code(500);
    echo "Failed to prepare upload directory.";
    exit;
  }
}

$allowedMimes = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
$savedPaths   = [];

// normalize arrays
$names    = (array)$files['name'];
$tmpNames = (array)$files['tmp_name'];
$types    = (array)$files['type'];
$errors   = (array)$files['error'];
$sizes    = (array)$files['size'];

$slug = preg_replace('/[^a-z0-9]+/i', '-', strtolower($name));
for ($i = 0; $i < count($names); $i++) {
  if ($errors[$i] !== UPLOAD_ERR_OK) { continue; }

  $tmp = $tmpNames[$i];
  if (!$tmp || !is_uploaded_file($tmp)) { continue; }

  // verify real image
  $info = @getimagesize($tmp);
  if ($info === false) { continue; }

  $mime = $info['mime'] ?? $types[$i] ?? '';
  if (!isset($allowedMimes[$mime])) { continue; }

  // unique filename
  $ext      = $allowedMimes[$mime];
  $filename = $slug . '-' . uniqid('', true) . '.' . $ext;
  $dest     = $uploadDir . $filename;

  if (!move_uploaded_file($tmp, $dest)) { continue; }

  // store relative path (what you save in DB)
  $savedPaths[] = 'uploads/products/' . $filename;
}

if (empty($savedPaths)) {
  http_response_code(400);
  echo "No valid images uploaded.";
  exit;
}

// ------------------------------------------------------------
// DB insert (transaction)
// ------------------------------------------------------------
try {
  // start transaction
  Database::iud("START TRANSACTION");

  $now = date('Y-m-d H:i:s');

  // product
  $qProduct = sprintf(
    "INSERT INTO product (title, description, price, category_id, brand_id, qty, product_status_id, create_at)
     VALUES ('%s','%s', %f, %d, %d, %d, '%s', '%s')",
    esc($name), esc($desc), (float)$price, (int)$category, (int)$brand, (int)($stock ?? 0), esc($status), $now
  );
  Database::iud($qProduct);

  // get new product id
  $rs    = Database::search("SELECT LAST_INSERT_ID() AS id");
  $row   = $rs->fetch_assoc();
  $pid   = (int)($row['id'] ?? 0);

  if ($pid <= 0) {
    Database::iud("ROLLBACK");
    http_response_code(500);
    echo "Failed to create product.";
    exit;
  }

  // colors (optional)
  if (is_array($colors)) {
    foreach ($colors as $cid) {
      if ($cid === '' || $cid === null) continue;
      $cid = (int)$cid;
      if ($cid <= 0) continue;
      Database::iud("INSERT INTO product_has_color (product_id, color_id) VALUES ($pid, $cid)");
    }
  }

  // images
  foreach ($savedPaths as $p) {
    Database::iud("INSERT INTO product_img (product_id, img_url) VALUES ($pid, '" . esc($p) . "')");
  }

  // commit
  Database::iud("COMMIT");
  echo "success";
} catch (Throwable $e) {
  // rollback and clean up uploaded files
  Database::iud("ROLLBACK");
  foreach ($savedPaths as $p) {
    $absolute = __DIR__ . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $p);
    if (is_file($absolute)) { @unlink($absolute); }
  }
  http_response_code(500);
  echo "Failed to add product: " . $e->getMessage();
}
