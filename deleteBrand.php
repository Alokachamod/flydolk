<?php
include 'connection.php';

$b = $_POST['brandId'];

Database::iud("DELETE FROM `brand` WHERE `id` = '" . $b . "'");
echo ("success");

?>