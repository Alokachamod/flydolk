<?php
session_start();
include 'connection.php';
header('Content-Type: application/json');

function send_error($message) {
    echo json_encode(['ok' => false, 'error' => $message]);
    exit();
}

if (!isset($_SESSION['admin_id'])) {
    send_error('Not authenticated.');
}

// Check permissions using `admin_panel` and `admin_type`
$admin_rs = Database::search("
    SELECT at.type 
    FROM admin_panel ap 
    JOIN admin_type at ON ap.admin_type_id = at.id 
    WHERE ap.id = " . (int)$_SESSION['admin_id']
);
$admin_data = $admin_rs->fetch_assoc();

// [FIX] Changed 'Super Admin' to 'Admin' based on your request.
if (!$admin_data || $admin_data['type'] !== 'Admin') {
    send_error('You do not have permission to add admins.');
}

// Check for `name` field
if (empty($_POST['name']) || empty($_POST['email']) || empty($_POST['password']) || empty($_POST['role_id'])) {
    send_error('Please fill out all fields.');
}

$name = addslashes($_POST['name']);
$email = addslashes($_POST['email']);
$password = $_POST['password'];
$role_id = (int)$_POST['role_id']; // This is `admin_type_id`
$added_at = date('Y-m-d H:i:s'); // Get current timestamp

// Validate email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    send_error('Invalid email format.');
}

// Check `admin_panel`
$check_rs = Database::search("SELECT * FROM admin_panel WHERE email = '" . $email . "'");
if ($check_rs->num_rows > 0) {
    send_error('An admin with this email already exists.');
}

// Hash the password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Insert into `admin_panel`
try {
    Database::search("
        INSERT INTO admin_panel (name, email, password, added_at, admin_type_id) 
        VALUES ('" . $name . "', '" . $email . "', '" . $hashed_password . "', '" . $added_at . "', " . $role_id . ")
    ");
    echo json_encode(['ok' => true, 'message' => 'Admin added successfully.']);
} catch (Exception $e) {
    send_error('Database error: ' . $e->getMessage());
}
?>