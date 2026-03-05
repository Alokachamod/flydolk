<?php
session_start();
require 'connection.php';
require 'Exception.php';
require 'PHPMailer.php';
require 'SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$email = $_POST['email'];

if (empty($email)) {
    echo "Please enter your email address.";
} else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "Invalid email address.";
} else {
    Database::setUpConnection();
    $safe_email = Database::$connection->real_escape_string($email);

    $rs = Database::search("SELECT * FROM user WHERE email = '$safe_email'");
    if ($rs->num_rows == 1) {
        $user_data = $rs->fetch_assoc();

        // Generate a unique verification code
        $verification_code = uniqid();

        // Update the user's row with this code
        Database::iud("UPDATE user SET verification_code = '$verification_code' WHERE email = '$safe_email'");

        // --- Send Email ---
        $mail = new PHPMailer(true);
        try {
            //Server settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com'; // Your SMTP server
            $mail->SMTPAuth   = true;

            // --- !! ---
            // **THIS IS THE FIX:**
            // 1. Enter your full Gmail address.
            // 2. Enter the 16-digit "App Password" you generated in your Google Account.
            // --- !! ---
            $mail->Username   = 'your_email@gmail.com'; // <-- FIX 1: YOUR GMAIL
            $mail->Password   = 'add your password';    // <-- FIX 2: YOUR 16-DIGIT APP PASSWORD

            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = 465;

            //Recipients
            // The "From" address MUST be the same as your $mail->Username
            $mail->setFrom('your_email@gmail.com', 'FlyDolk Password Reset');
            $mail->addAddress($email, $user_data['name']);

            //Content
            $mail->isHTML(true);
            $mail->Subject = 'Reset Your FlyDolk Password';

            // Get the current server host
            $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
            $host = $_SERVER['HTTP_HOST'];
            $reset_link = $protocol . "://" . $host . "/flydolk/reset_password.php?email=" . urlencode($email) . "&code=" . urlencode($verification_code);

            $mail->Body    = "
                <div style='font-family: Arial, sans-serif; line-height: 1.6;'>
                    <h2>Password Reset Request</h2>
                    <p>Hello " . htmlspecialchars($user_data['name']) . ",</p>
                    <p>We received a request to reset your password. Please click the button below to set a new password. This link is valid for 1 hour.</p>
                    <a href='" . $reset_link . "' style='background-color: #2f80ed; color: white; padding: 12px 20px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 20px 0;'>
                        Reset Your Password
                    </a>
                    <p>If you did not request a password reset, please ignore this email.</p>
                    <p>Thanks,<br>The FlyDolk Team</p>
                    <hr>
                    <p style='font-size: 0.9em; color: #777;'>If you're having trouble clicking the button, copy and paste this URL into your browser:</p>
                    <p style='font-size: 0.9em; color: #777; word-break: break-all;'>" . $reset_link . "</p>
                </div>
            ";

            $mail->send();
            echo 'success';
        } catch (Exception $e) {
            // This error message is what you are seeing.
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        echo "No account found with that email address.";
    }
}
