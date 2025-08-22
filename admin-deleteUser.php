<?php
// admin-deleteUser.php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['ok' => false, 'error' => 'Unauthorized']);
    exit;
}

require 'connection.php'; // must expose Database::iud / ::search or $connection

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['ok' => false, 'error' => 'Invalid request method']);
        exit;
    }

    if (!isset($_POST['user_id']) || !is_numeric($_POST['user_id'])) {
        echo json_encode(['ok' => false, 'error' => 'Missing or invalid user id']);
        exit;
    }

    $userId = (int)$_POST['user_id'];

    // 1) Check whether this user has invoices via their addresses.
    // EER shows: invoice -> user_has_address (user_id)
    $stmt = Database::search("
        SELECT COUNT(*) AS c
        FROM invoice i
        INNER JOIN user_has_address uha ON i.user_has_address_id = uha.id
        WHERE uha.user_id = {$userId}
    ");
    $row = $stmt->fetch_assoc();
    $hasOrders = (int)$row['c'] > 0;

    if ($hasOrders) {
        // Safer to prevent hard delete to avoid FK issues
        echo json_encode([
            'ok' => false,
            'error' => "User has orders and can't be deleted. Consider banning instead."
        ]);
        exit;
    }

    // 2) Begin a transaction (if your Database class supports it)
    Database::iud("START TRANSACTION");

    // 3) Delete dependents first (FK order matters)
    // 3a) user_img
    Database::iud("DELETE FROM user_img WHERE user_id = {$userId}");

    // 3b) user_has_address (only safe because we already checked invoices=0)
    Database::iud("DELETE FROM user_has_address WHERE user_id = {$userId}");

    // 4) Finally delete the user
    $affected = Database::iud("DELETE FROM user WHERE id = {$userId}");

    // If nothing deleted, user might not exist
    if ($affected === 0) {
        Database::iud("ROLLBACK");
        echo json_encode(['ok' => false, 'error' => 'User not found or already deleted']);
        exit;
    }

    Database::iud("COMMIT");
    echo json_encode(['ok' => true]);
    exit;

} catch (Throwable $e) {
    // Roll back if possible
    try { Database::iud("ROLLBACK"); } catch (Throwable $ee) {}
    echo json_encode(['ok' => false, 'error' => 'Server exception: ' . $e->getMessage()]);
    exit;
}
