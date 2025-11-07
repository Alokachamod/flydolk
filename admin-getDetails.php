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

if (empty($_POST['id'])) {
    send_error('No admin ID provided.');
}

$admin_id = (int)$_POST['id'];

try {
    // Select from `admin_panel` and get `admin_type_id`
    $rs = Database::search("SELECT id, email, admin_type_id FROM admin_panel WHERE id = " . $admin_id);
    if ($rs->num_rows == 0) {
        send_error('Admin not found.');
    }
    
    $admin_data = $rs->fetch_assoc();
    echo json_encode(['ok' => true, 'admin' => $admin_data]);
    
} catch (Exception $e) {
    send_error('Database error: ' . $e->getMessage());
}
?>