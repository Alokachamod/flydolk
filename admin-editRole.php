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
    send_error('You do not have permission to edit roles.');
}

if (empty($_POST['id']) || empty($_POST['role_id'])) {
    send_error('Missing parameters.');
}

$admin_id = (int)$_POST['id'];
$role_id = (int)$_POST['role_id']; // This is `admin_type_id`

if ($admin_id == 1 || $admin_id == $_SESSION['admin_id']) {
    send_error('You cannot change the role of this protected admin.');
}

try {
    // Update `admin_panel` and set `admin_type_id`
    Database::search("
        UPDATE admin_panel 
        SET admin_type_id = " . $role_id . " 
        WHERE id = " . $admin_id . "
    ");
    echo json_encode(['ok' => true, 'message' => 'Admin role updated.']);
} catch (Exception $e) {
    send_error('Database error: ' . $e->getMessage());
}
?>