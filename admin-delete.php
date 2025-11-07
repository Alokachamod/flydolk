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

// Use your table names: `admin_panel` and `admin_type`
$admin_rs = Database::search("
    SELECT at.type 
    FROM admin_panel ap 
    JOIN admin_type at ON ap.admin_type_id = at.id 
    WHERE ap.id = " . (int)$_SESSION['admin_id']
);
$admin_data = $admin_rs->fetch_assoc();

// Check for 'type' column
// [FIX] Changed 'Super Admin' to 'Admin' based on your request.
if (!$admin_data || $admin_data['type'] !== 'Admin') {
    send_error('You do not have permission to delete admins.');
}

if (empty($_POST['id'])) {
    send_error('No admin ID provided.');
}

$admin_id = (int)$_POST['id'];

if ($admin_id == 1 || $admin_id == $_SESSION['admin_id']) {
    send_error('You cannot delete this protected admin.');
}

try {
    // Use your table name: `admin_panel`
    Database::search("DELETE FROM admin_panel WHERE id = " . $admin_id);
    echo json_encode(['ok' => true, 'message' => 'Admin has been deleted.']);
} catch (Exception $e) {
    send_error('Database error: ' . $e->getMessage());
}
?>