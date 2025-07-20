<?php
require "connection.php";

$name = $_POST['n'];
$email = $_POST['e'];
$password = $_POST['p'];
$mobile = $_POST['m'];


if (empty($name)) {
    echo ("Please enter your Name !!!");
} else if (strlen($name) > 50) {
    echo ("First Name must have less than 50 characters");
} else  if (empty($email)) {
    echo ("Please enter your Email !!!");
} else if (strlen($email) >= 100) {
    echo ("Email must have less than 100 characters");
} else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo ("Invalid Email Address !!!");
} else if (empty($password)) {
    echo ("Please enter your password !!!");
} else if (strlen($password) < 8 || strlen($password) > 20) {
    echo ("Password must be between 8 - 20 characters");
} else if (empty($mobile)) {
    echo ("Please enter your Mobile Number !!!");
} else if (strlen($mobile) != 10) {
    echo ("Your mobile Number must have 10 characters");
} else if (!preg_match("/07[0,1,2,4,5,6,7,8][0-9]/", $mobile)) {
    echo ("Invalid Mobile Number !!!");
} else {
    $rs = Database::search("SELECT * FROM user WHERE email = '" . $email . "' OR mobile = '" . $mobile . "'");
    $n = $rs->num_rows;

    if ($n > 0) {
        echo ("Email or Mobile Number already exists !!!");
    } else {
        
        $d = new DateTime();
        $date = $d->format("Y-m-d H:i:s");
        $password = password_hash($password, PASSWORD_DEFAULT);
        Database::iud("INSERT INTO user (name, email, password, mobile, joined_date, user_status_id) VALUES ('" . $name . "', '" . $email . "', '" . $password . "', '" . $mobile . "', '" . $date . "', 1)");
  
        echo ("success");
    }
}