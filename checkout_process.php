<?php
// This file handles all the backend logic for placing an order.

// --- 1. SETUP & EMAIL (PHPMailer) ---
// We need PHPMailer to send the invoice email
// You MUST install it first by running this command in your project folder:
// composer require phpmailer/phpmailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader
$phpMailerAutoload = __DIR__ . '/vendor/autoload.php';
$phpMailerInstalled = file_exists($phpMailerAutoload);
if ($phpMailerInstalled) {
    require $phpMailerAutoload;
}

require_once 'connection.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// --- 2. SECURITY & DATA PREPARATION ---
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header('Location: login-signup.php?redirect=checkout');
    exit;
}

// Get user ID and email (for the invoice)
$user_id = (int)$_SESSION['user_id'];
// **FIX IS HERE:** Changed 'fname, lname' to 'name'
$user_rs = Database::search("SELECT email, name FROM user WHERE id = $user_id");
$user_data = $user_rs->fetch_assoc();
$user_email = $user_data['email'];
// **FIX IS HERE:** Use the single 'name' column
$user_full_name = $user_data['name'] ?? 'Valued Customer';

// Get selected cart items from the SESSION (set by checkout.php)
if (!isset($_SESSION['checkout_items']) || empty($_SESSION['checkout_items'])) {
    header('Location: cart.php?error=session_expired');
    exit;
}
$cart_item_ids = $_SESSION['checkout_items'];
$cart_id_list = implode(',', $cart_item_ids);

// Get all POST data and sanitize it
// **FIX IS HERE:** Default 'fname' to the user's full name, 'lname' to empty
$fname = $_POST['fname'] ?? $user_full_name;
$lname = $_POST['lname'] ?? '';
$address_line_1 = $_POST['address_line_1'] ?? '';
$address_line_2 = $_POST['address_line_2'] ?? '';
$province_id = (int)($_POST['province_id'] ?? 0);
$district_id = (int)($_POST['district_id'] ?? 0);
$city_id = (int)($_POST['city_id'] ?? 0);
$zip_code = $_POST['zip_code'] ?? '';
$shipping_fee = 500.00; // Same as checkout.php

// --- 3. DATABASE TRANSACTION ---
// This is critical. All queries must succeed, or none will.
Database::setUpConnection();
Database::$connection->begin_transaction();

try {
    // --- 4. CHECK STOCK & GET ITEMS ---
    $cart_items_rs = Database::search("
        SELECT c.id AS cart_id, p.id AS product_id, p.title, p.price, p.qty AS stock_qty, c.qty AS order_qty
        FROM cart c
        JOIN product p ON c.product_id = p.id
        WHERE c.user_id = $user_id AND c.id IN ($cart_id_list)
    ");
    
    if ($cart_items_rs->num_rows == 0) {
        throw new Exception("No valid items found in your cart.");
    }
    
    // Check stock levels *before* doing anything else
    $items_to_process = [];
    $total_order_amount = 0;
    while ($item = $cart_items_rs->fetch_assoc()) {
        if ($item['order_qty'] > $item['stock_qty']) {
            throw new Exception("Not enough stock for: " . $item['title'] . ". Only " . $item['stock_qty'] . " available.");
        }
        $items_to_process[] = $item;
        $total_order_amount += $item['price'] * $item['order_qty'];
    }
    
    $grand_total = $total_order_amount + $shipping_fee;

    // --- 5. SAVE/UPDATE USER ADDRESS ---
    $address_rs = Database::search("SELECT * FROM user_has_address WHERE user_id = $user_id");
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
            INSERT INTO user_has_address (user_id, address_line_1, address_line_2, zip_code, city_id) 
            VALUES ($user_id, '$address_line_1', '$address_line_2', '$zip_code', $city_id)
        ");
        $user_has_address_id = Database::$connection->insert_id;
    }

    // --- 6. GENERATE ORDER ID & SAVE INVOICE ITEMS ---
    $order_id_string = "FLYD-" . time() . rand(100, 999);
    $order_date = date('Y-m-d H:i:s');
    
    foreach ($items_to_process as $item) {
        $product_id = $item['product_id'];
        $order_qty = $item['order_qty'];
        $unit_price = $item['price'];
        $item_total = $unit_price * $order_qty;

        // **TASK: UPDATE PRODUCT QTY IN DATABASE**
        Database::iud("
            UPDATE product 
            SET qty = qty - $order_qty 
            WHERE id = $product_id
        ");

        // **TASK: SAVE TO INVOICE TABLE**
        Database::iud("
            INSERT INTO invoice (order_id, product_id, user_has_address_id, created_at, qty, unit_price, total_amount, status_id) 
            VALUES (
                '$order_id_string', 
                $product_id, 
                $user_has_address_id, 
                '$order_date', 
                $order_qty, 
                $unit_price, 
                $item_total, 
                1 
            )
        ");
    }
    
    // --- 7. TASK: REMOVE FROM CART ---
    Database::iud("
        DELETE FROM cart 
        WHERE user_id = $user_id AND id IN ($cart_id_list)
    ");

    // --- 8. COMMIT & CLEANUP ---
    Database::$connection->commit();
    unset($_SESSION['checkout_items']); // Clear the session
    
    // --- 9. TASK: SEND INVOICE EMAIL ---
    if ($phpMailerInstalled) {
        // We need to fetch the full invoice details to build the email
        // We'll reuse the logic from invoice.php (but simplified)
        $invoice_html = "<h1>Order Confirmation: $order_id_string</h1>";
        $invoice_html .= "<p>Thank you for your order, $fname!</p>"; // This will use the full name
        $invoice_html .= "<p>Your order will be shipped to: $address_line_1, $zip_code</p>";
        $invoice_html .= "<table border='1' cellpadding='5' cellspacing='0'><tr><th>Product</th><th>Qty</th><th>Total</th></tr>";
        
        foreach ($items_to_process as $item) {
            $invoice_html .= "<tr>";
            $invoice_html .= "<td>" . $item['title'] . "</td>";
            $invoice_html .= "<td>" . $item['order_qty'] . "</td>";
            $invoice_html .= "<td>LKR " . number_format($item['price'] * $item['order_qty'], 2) . "</td>";
            $invoice_html .= "</tr>";
        }
        $invoice_html .= "</table>";
        $invoice_html .= "<p>Subtotal: LKR " . number_format($total_order_amount, 2) . "</p>";
        $invoice_html .= "<p>Shipping: LKR " . number_format($shipping_fee, 2) . "</p>";
        $invoice_html .= "<h3>Total: LKR " . number_format($grand_total, 2) . "</h3>";
        
        // Send the email (this will fail if you don't set up credentials)
        try {
            $mail = new PHPMailer(true);
            // $mail->SMTPDebug = SMTP::DEBUG_SERVER; // Enable for debugging
            $mail->isSMTP();
            $mail->Host       = 'smtp.example.com'; // **SET YOUR SMTP HOST**
            $mail->SMTPAuth   = true;
            $mail->Username   = 'you@example.com';  // **SET YOUR SMTP USERNAME**
            $mail->Password   = 'your_password';    // **SET YOUR SMTP PASSWORD**
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom('contact@flydolk.com', 'FlyDolk');
            // **FIX IS HERE:** This will use the full name from $fname and an empty $lname
            $mail->addAddress($user_email, $fname . ' ' . $lname);
            $mail->addReplyTo('contact@flydolk.com', 'FlyDolk');

            $mail->isHTML(true);
            $mail->Subject = 'Your FlyDolk Order Confirmation (' . $order_id_string . ')';
            $mail->Body    = $invoice_html;
            $mail->AltBody = 'Your order has been placed. Order ID: ' . $order_id_string;

            $mail->send();
        } catch (Exception $e) {
            // Email failed, but the order was still placed.
            // We can log this error, but we don't stop the user.
            error_log("Email (PHPMailer) failed to send: " . $mail->ErrorInfo);
        }
    }

    // --- 10. REDIRECT TO SUCCESS ---
    header('Location: order_success.php?order_id=' . $order_id_string);
    exit;

} catch (Exception $e) {
    // --- 11. HANDLE ERRORS ---
    Database::$connection->rollback(); // Undo all changes
    // Go back to checkout with the error message
    header('Location: checkout.php?error=' . urlencode($e->getMessage()));
    exit;
}
?>

