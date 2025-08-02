<?php
include 'connection.php';

$c = $_POST['categoryId'];

Database::iud("DELETE FROM `category` WHERE `id` = '" . $c . "'");
echo ("success");

?>