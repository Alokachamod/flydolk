<?php
include 'connection.php';


$cname = $_POST['cname'];

//echo $cname . " " . $ccode;

$rs = Database::search("SELECT * FROM `color` WHERE `name` = '" . $cname . "'");
if ($rs->num_rows == 0) {
    Database::iud("INSERT INTO `color`(`name`) VALUES ('" . $cname . "')");
    echo ("success");
} else {
    echo ("duplicate");
}

?>