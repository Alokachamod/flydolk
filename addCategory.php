<?php
// manage categories

//echo ("manage categories");


include 'connection.php';

$c = $_POST['c'];

//echo ("get category" . $c);

$rs = Database::search("SELECT * FROM `category` WHERE `name` LIKE '%" . $c . "%'");
if ($rs->num_rows == 1) {
    $row = $rs->fetch_assoc();
   echo ("Category already exists !!!");
   
} else {
    
    Database::iud("INSERT INTO `category` (`name`) VALUES ('" . $c . "')");
    echo ("success");

}
