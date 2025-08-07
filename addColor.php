<?php
include 'connection.php';


$cname = $_POST['cname'];
$ccode = $_POST['ccode'];

//echo $cname . " " . $ccode;

$rs = Database::search("SELECT * FROM `color` WHERE `name` = '" . $cname . "' OR `color_code` = '" . $ccode . "'");
if ($rs->num_rows == 0) {
    Database::iud("INSERT INTO `color`(`name`, `color_code`) VALUES ('" . $cname . "','" . $ccode . "')");
    echo ("success");
} else {
    echo ("duplicate");
}

?>