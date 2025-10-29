<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'connection.php'; // Assuming admin is in a subfolder

// --- Admin Session Check ---
// I am assuming your admin session variable is 'admin_user_id'
// Please change this if it is different (e.g., 'admin_email')
if (!isset($_SESSION['admin_user_id']) || empty($_SESSION['admin_user_id'])) {
    header('Location: login.php'); // Redirect to admin login
    exit;
}

// Fetch all possible order statuses
Database::setUpConnection();
$status_rs = Database::search("SELECT * FROM status");
$statuses = [];
while ($status_row = $status_rs->fetch_assoc()) {
    $statuses[] = $status_row;
}

// Fetch all orders
// This query groups all invoice items by order_id and joins to get customer info
$orders_rs = Database::search("
    SELECT 
        i.order_id, 
        MAX(i.created_at) AS created_at,
        SUM(i.total_amount) AS total_amount,
        i.status_id,
        s.name AS status_name,
        u.name AS user_name,
        uha.user_id
    FROM invoice i
    JOIN status s ON i.status_id = s.id
    JOIN user_has_address uha ON i.user_has_address_id = uha.id
    JOIN user u ON uha.user_id = u.id
    GROUP BY i.order_id
    ORDER BY MAX(i.created_at) DESC
");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Management - FlyDolk Admin</title>
    
    <link rel="stylesheet" href="style.css">
    <!-- Assuming you have admin-specific CSS and Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;900&display=swap" rel="stylesheet">
    
    <!-- Simple styling for the admin page -->
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
        }
        .wrapper {
            display: flex;
        }
        /* Assuming a simple sidebar styling from your frontend files */
        #sidebar {
            min-width: 250px;
            max-width: 250px;
            background: #0f172a; /* Dark admin sidebar */
            color: #fff;
            min-height: 100vh;
            transition: all 0.3s;
        }
        #sidebar .list-group-item {
            background: #0f172a;
            color: #adb5bd;
            border: none;
        }
        #sidebar .list-group-item.active,
        #sidebar .list-group-item:hover {
            background: #1e293b;
            color: #fff;
        }
        #content {
            width: 100%;
            padding: 20px;
        }
        .status-select {
            min-width: 150px;
        }
        
        /* Toast notification for status update */
        .toast-container {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1100;
        }
    </style>
</head>
<body>
    <?php include 'admin_header.php'; ?>

<!-- Toast Notification -->
<div class="toast-container">
    <div id="status-toast" class="toast align-items-center" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">
                <!-- Message will be set by JS -->
            </div>
            <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

<div class="wrapper">
    <!-- Include your Admin Sidebar/Header -->
    <?php include 'admin_header.php'; // This file should contain your sidebar navigation ?>

    <!-- Page Content -->
    <div id="content">
        <nav class="navbar navbar-expand-lg navbar-light bg-light mb-4 shadow-sm">
            <div class="container-fluid">
                <span class="navbar-brand mb-0 h1">Order Management</span>
            </div>
        </nav>

        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title">All Orders</h5>
                <p class="card-subtitle mb-2 text-muted">
                    View and manage all customer orders.
                </p>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">Order ID</th>
                                <th scope="col">Customer</th>
                                <th scope="col">Date</th>
                                <th scope="col" class="text-end">Total (LKR)</th>
                                <th scope="col">Status</th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($orders_rs->num_rows > 0): ?>
                                <?php while($order = $orders_rs->fetch_assoc()): ?>
                                <tr>
                                    <th scope="row">#<?php echo htmlspecialchars($order['order_id']); ?></th>
                                    <td><?php echo htmlspecialchars($order['user_name']); ?></td>
                                    <td><?php echo date("Y-m-d, g:i a", strtotime($order['created_at'])); ?></td>
                                    <td class="text-end"><?php echo number_format($order['total_amount'] + 500, 2); ?></td>
                                    <td>
                                        <select class="form-select form-select-sm status-select" 
                                                data-order-id="<?php echo htmlspecialchars($order['order_id']); ?>"
                                                onchange="updateStatus(this)">
                                            <?php foreach ($statuses as $status): ?>
                                                <option value="<?php echo $status['id']; ?>" <?php echo ($status['id'] == $order['status_id']) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($status['name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td>
                                        <a href="admin_view_order.php?order_id=<?php echo htmlspecialchars($order['order_id']); ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="fa-solid fa-eye me-1"></i> View
                                        </a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted">No orders found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include 'admin-footer.php'?>
<script src="script.js"></script>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    const toastElement = document.getElementById('status-toast');
    const toast = new bootstrap.Toast(toastElement);

    function showToast(message, isSuccess = true) {
        const toastBody = toastElement.querySelector('.toast-body');
        
        toastElement.classList.remove('text-bg-success', 'text-bg-danger');
        if (isSuccess) {
            toastElement.classList.add('text-bg-success');
        } else {
            toastElement.classList.add('text-bg-danger');
        }
        
        toastBody.textContent = message;
        toast.show();
    }

    function updateStatus(selectElement) {
        const orderId = selectElement.dataset.orderId;
        const statusId = selectElement.value;
        
        const formData = new FormData();
        formData.append('order_id', orderId);
        formData.append('status_id', statusId);

        fetch('admin_update_status.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                showToast('Status updated successfully!');
            } else {
                showToast('Error: ' + data.message, false);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('A connection error occurred.', false);
        });
    }
</script>

</body>
</html>
