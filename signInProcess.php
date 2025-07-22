<?php
session_start();
require 'connection.php';

$email = $_POST['e'];
$password = $_POST['p'];


if(empty($email)){
    echo "Please enter Your email.";
}else if(strlen($email) >= 100){
    echo "Email must have less than 100 characters.";
} else if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
    echo "Invalid Email Address !!!";
} else if(empty($password)){
    echo "Please enter your password !!!";
} else if(strlen($password) < 8 || strlen($password) > 20){
    echo "Password must be between 8 and 20 characters.";
} else {
    $rs = Database::search("SELECT * FROM user WHERE email = '" . $email . "'");
    $n = $rs->num_rows;

    if ($n == 1) {
        $data = $rs->fetch_assoc();
        if (password_verify($password, $data['password'])) {
            $_SESSION['user_id'] = $data['id'];
            $_SESSION['user_name'] = $data['name'];
            echo "success";
        } else {
            echo "Incorrect password.";
        }
    } else {
        echo "Email not found.";
    }
}

?>