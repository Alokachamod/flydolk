<?php
session_start();
if (!isset($_SESSION['admin_id'])) { echo "Unauthorized"; exit; }

require "connection.php"; // provides $conn (mysqli)

$modelId   = $_POST['modelId']   ?? '';
$modelName = $_POST['modelName'] ?? '';

if (!$modelId || !ctype_digit($modelId)) { echo "Invalid model id"; exit; }
if (!$modelName) { echo "Model name is required"; exit; }
if (mb_strlen($modelName) > 100) { echo "Model name too long"; exit; }

$stmt = $conn->prepare("UPDATE model SET name = ? WHERE id = ?");
if (!$stmt) { echo "DB error"; exit; }
$stmt->bind_param("si", $modelName, $modelId);
echo $stmt->execute() ? "success" : "Update failed";
$stmt->close();
