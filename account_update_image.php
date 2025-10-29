<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'connection.php';
header('Content-Type: application/json');

$response = ['status' => 'error', 'message' => 'An unknown error occurred.'];

// 1. CHECK LOGIN
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    $response['message'] = 'Error: You must be logged in.';
    echo json_encode($response);
    exit;
}
$user_id = (int)$_SESSION['user_id'];
Database::setUpConnection();

try {
    // 2. CHECK FILE UPLOAD
    if (!isset($_FILES['profile_image']) || $_FILES['profile_image']['error'] != UPLOAD_ERR_OK) {
        throw new Exception('No file uploaded or an upload error occurred.');
    }

    $file = $_FILES['profile_image'];
    $file_name = $file['name'];
    $file_tmp_name = $file['tmp_name'];
    $file_size = $file['size'];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    
    $allowed_ext = ['jpg', 'jpeg', 'png'];

    // 3. VALIDATE FILE
    if (!in_array($file_ext, $allowed_ext)) {
        throw new Exception('Invalid file type. Only JPG, JPEG, and PNG are allowed.');
    }
    if ($file_size > 2097152) { // 2MB limit
        throw new Exception('File size must be less than 2MB.');
    }

    // 4. CREATE UPLOAD DIRECTORY (if it doesn't exist)
    // --- UPDATED PATH ---
    $upload_dir = 'uploads/users/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // 5. CREATE NEW FILENAME & MOVE FILE
    $new_file_name = 'user_' . $user_id . '_' . time() . '.' . $file_ext;
    $file_destination = $upload_dir . $new_file_name;
    
    if (!move_uploaded_file($file_tmp_name, $file_destination)) {
        throw new Exception('Failed to move uploaded file.');
    }

    // 6. UPDATE DATABASE
    $safe_url = Database::$connection->real_escape_string($file_destination);
    
    // Check if user already has an image
    $img_rs = Database::search("SELECT id, url FROM user_img WHERE user_id = $user_id");
    if ($img_rs->num_rows == 1) {
        // Update existing record
        $old_img = $img_rs->fetch_assoc();
        Database::iud("UPDATE user_img SET url = '$safe_url' WHERE user_id = $user_id");
        // Delete old file from server
        if (file_exists($old_img['url']) && $old_img['url'] != 'imgs/default_avatar.png') {
            unlink($old_img['url']);
        }
    } else {
        // Insert new record
        Database::iud("INSERT INTO user_img (user_id, url) VALUES ($user_id, '$safe_url')");
    }

    // 7. RESPOND
    $response['status'] = 'success';
    $response['message'] = 'Profile image updated!';
    $response['new_image_url'] = $safe_url;
    echo json_encode($response);

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
    echo json_encode($response);
}
?>

