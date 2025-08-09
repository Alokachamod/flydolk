<?php
include 'connection.php';

$mn = $_POST['mname'];



// Check for duplicates
$rs = Database::search("SELECT * FROM `model` WHERE `name` = '" . $mn . "'");

if ($rs->num_rows == 0) {
    // If no duplicates and brand ID is valid, insert the new model
    Database::iud("INSERT INTO `model`(`name`) VALUES ('" . $mn . "')");
    echo ("success");
} else {
    echo ("duplicate");
}
?>