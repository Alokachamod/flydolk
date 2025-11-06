<?php
require 'connection.php';

$email = $_POST['email'] ?? null;
$code = $_POST['code'] ?? null;
$new_password = $_POST['new_password'] ?? null;

// Validation
if (empty($email) || empty($code) || empty($new_password)) {
    echo "Invalid request.";
    exit;
}
if (strlen($new_password) < 8 || strlen($new_password) > 20) {
    echo "Password must be between 8 and 20 characters.";
    exit;
}

Database::setUpConnection();
$safe_email = Database::$connection->real_escape_string($email);
$safe_code = Database::$connection->real_escape_string($code);

// 1. Verify the code and email are still valid
$rs = Database::search("SELECT * FROM user WHERE email = '$safe_email' AND verification_code = '$safe_code'");

if ($rs->num_rows == 1) {
    // 2. Hash the new password
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    
    // 3. Update the password and clear the verification code (so it can't be used again)
    Database::iud("
        UPDATE user 
        SET password = '$hashed_password', verification_code = NULL 
        WHERE email = '$safe_email'
    ");
    
    echo "success";
} else {
    echo "This password reset link is invalid or has expired. Please try again.";
}
?>