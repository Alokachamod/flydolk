<?php
include 'connection.php';
$b = $_POST['colorCode'];
Database::iud("DELETE FROM `color` WHERE `color_code` = '" . $b . "'");
echo ("success");
?>