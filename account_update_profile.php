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
    // 2. GET & SANITIZE DATA
    $name = Database::$connection->real_escape_string($_POST['name'] ?? '');
    $email = Database::$connection->real_escape_string($_POST['email'] ?? '');
    $mobile = Database::$connection->real_escape_string($_POST['mobile'] ?? '');
    
    $address_line_1 = Database::$connection->real_escape_string($_POST['address_line_1'] ?? '');
    $address_line_2 = Database::$connection->real_escape_string($_POST['address_line_2'] ?? '');
    $zip_code = Database::$connection->real_escape_string($_POST['zip_code'] ?? '');
    $city_id = (int)($_POST['city_id'] ?? 0);

    // 3. VALIDATE DATA
    if (empty($name) || empty($email) || empty($mobile)) {
        throw new Exception('Full Name, Email, and Mobile are required.');
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email format.');
    }
    
    // Check if email is already taken by *another* user
    $email_check_rs = Database::search("SELECT id FROM user WHERE email = '$email' AND id != $user_id");
    if ($email_check_rs->num_rows > 0) {
        throw new Exception('This email is already in use by another account.');
    }

    // 4. UPDATE USER TABLE
    Database::iud("
        UPDATE user 
        SET name = '$name', email = '$email', mobile = '$mobile' 
        WHERE id = $user_id
    ");

    // 5. UPDATE/INSERT ADDRESS TABLE
    if ($city_id > 0) { // Only update address if a city is selected
        $address_rs = Database::search("SELECT id FROM user_has_address WHERE user_id = $user_id");
        if ($address_rs->num_rows == 1) {
            $address_data = $address_rs->fetch_assoc();
            $user_has_address_id = $address_data['id'];
            Database::iud("
                UPDATE user_has_address 
                SET address_line_1 = '$address_line_1', address_line_2 = '$address_line_2', zip_code = '$zip_code', city_id = $city_id 
                WHERE id = $user_has_address_id
            ");
        } else {
            Database::iud("
                INSERT INTO user_has_address (user_id, address_line_1, address_line_2, zip_code, $city_id) 
                VALUES ($user_id, '$address_line_1', '$address_line_2', '$zip_code', $city_id)
            ");
        }
    }
    
    // 6. UPDATE SESSION & RESPOND
    $_SESSION['user_name'] = $name; // Update session name
    $response['status'] = 'success';
    $response['message'] = 'Profile updated successfully!';
    $response['new_name'] = $name; // Send back new name for header
    echo json_encode($response);

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
    echo json_encode($response);
}
?>

