<?php
include "connection.php";

$b = $_POST['b'];

//echo ("get brand: " . $b);

$rs = Database::search("SELECT * FROM brand WHERE name = '".$b."'");
if ($rs->num_rows == 1) {
    $row = $rs->fetch_assoc();
   echo ("This Brand name already exists !!!");
   
} else {
    
    Database::iud("INSERT INTO `brand` (`name`) VALUES ('" . $b . "')");
    echo ("success");

}


?>