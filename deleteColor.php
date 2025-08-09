<?php
include 'connection.php';
$b = $_POST['colorId'];
Database::iud("DELETE FROM `color` WHERE `id` = '" . $b . "'");
echo ("success");
?>