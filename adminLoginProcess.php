<?php
session_start();
require 'connection.php';


$ae = $_POST['ae'];
$ap = $_POST['ap'];

if (empty($ae)) {
    echo "Please enter your email.";
} else if (strlen($ae) >= 100) {
    echo "Email must have less than 100 characters.";
} else if (!filter_var($ae, FILTER_VALIDATE_EMAIL)) {
    echo "Invalid Email Address !!!";
} else if (empty($ap)) {
    echo "Please enter your password !!!";
} else if (strlen($ap) < 8 || strlen($ap) > 20) {
    echo "Password must be between 8 and 20 characters.";
} else {
    $rs = Database::search("SELECT * FROM admin_panel WHERE email = '" . $ae . "'");
    $n = $rs->num_rows;

    if ($n == 1) {
        $data = $rs->fetch_assoc();
        if ($ap == $data['password']) {
            
            $_SESSION['admin_id'] = $data['id'];
            $_SESSION['admin_name'] = $data['name'];
            echo "success";
        } else {
            echo "Incorrect password.";
        }
    } else {
        echo "Email not found.";
    }
}
