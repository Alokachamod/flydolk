<?php
require_once 'connection.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. CHECK LOGIN
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header('Location: login-signup.php?redirect=invoice');
    exit;
}
$user_id = (int)$_SESSION['user_id'];

// 2. CHECK FOR ORDER ID
if (!isset($_GET['order_id']) || empty($_GET['order_id'])) {
    die("No order ID provided.");
}
$order_id = $_GET['order_id'];

// --- 3. FIX IS HERE ---
// We must set up the connection *before* using it to escape strings.
Database::setUpConnection();
$safe_order_id = Database::$connection->real_escape_string($order_id);

// 4. FETCH INVOICE DATA FROM DATABASE
// This query joins all necessary tables to build a complete invoice.
$invoice_rs = Database::search("
    SELECT 
        i.order_id, i.created_at, i.qty, i.unit_price, i.total_amount,
        p.title AS product_title,
        s.name AS status_name,
        uha.address_line_1, uha.address_line_2, uha.zip_code,
        city.name AS city_name,
        district.name AS district_name,
        province.name AS province_name,
        usr.name AS user_name, usr.email AS user_email, usr.mobile AS user_mobile
    FROM invoice i
    JOIN product p ON i.product_id = p.id
    JOIN status s ON i.status_id = s.id
    JOIN user_has_address uha ON i.user_has_address_id = uha.id
    JOIN user usr ON uha.user_id = usr.id
    JOIN city ON uha.city_id = city.id
    JOIN district ON city.district_id = district.id
    JOIN province ON district.province_id = province.id
    WHERE i.order_id = '$safe_order_id' 
    AND usr.id = $user_id
");

if ($invoice_rs->num_rows == 0) {
    die("Could not find this invoice or it does not belong to you.");
}

$invoice_items = [];
$invoice_details = null;
$subtotal = 0;
$shipping_fee = 500.00; // Same fixed shipping fee

while ($row = $invoice_rs->fetch_assoc()) {
    if ($invoice_details === null) {
        $invoice_details = $row; // Save all the common data from the first row
    }
    $invoice_items[] = $row; // Add item to the list
    $subtotal += $row['total_amount']; // Add to subtotal
}
$grand_total = $subtotal + $shipping_fee;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - <?php echo htmlspecialchars($invoice_details['order_id']); ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts (Inter) -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;900&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa; /* Light background for printing */
            color: #212529;
        }
        .invoice-container {
            max-width: 800px;
            margin: 40px auto;
            padding: 30px;
            background-color: #ffffff;
            border: 1px solid #dee2e6;
            border-radius: 0.5rem;
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,.05);
        }
        .invoice-header {
            border-bottom: 2px solid #0d6efd;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .invoice-brand {
            font-size: 2.5rem;
            font-weight: 900;
            color: #0f172a; /* Dark FlyDolk color */
        }
        .invoice-details, .customer-details {
            margin-bottom: 30px;
        }
        .table {
            border-color: #dee2e6;
        }
        .table th {
            background-color: #f8f9fa;
        }
        .totals-table {
            width: 300px;
            margin-left: auto;
        }
        .print-button {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
        }
        @media print {
            body {
                background-color: #ffffff;
            }
            .invoice-container {
                margin: 0;
                padding: 0;
                border: none;
                box-shadow: none;
                max-width: 100%;
            }
            .print-button {
                display: none;
            }
        }
    </style>
</head>
<body>

    <div class="invoice-container">
        <!-- Header: Brand and Invoice Title -->
        <div class="invoice-header row">
            <div class="col-sm-6">
                <div class="invoice-brand">FLYDOLK</div>
                <div class="text-muted">Invoice / Receipt</div>
            </div>
            <div class="col-sm-6 text-sm-end">
                <h2 class="mb-1">Invoice</h2>
                <div class="text-muted">#<?php echo htmlspecialchars($invoice_details['order_id']); ?></div>
            </div>
        </div>

        <!-- Details: Customer and Order -->
        <div class="row invoice-details">
            <div class="col-sm-6 customer-details">
                <strong>Billed To:</strong>
                <p class="mb-0"><?php echo htmlspecialchars($invoice_details['user_name']); ?></p>
                <p class="mb-0"><?php echo htmlspecialchars($invoice_details['address_line_1']); ?></p>
                <?php if(!empty($invoice_details['address_line_2'])): ?>
                    <p class="mb-0"><?php echo htmlspecialchars($invoice_details['address_line_2']); ?></p>
                <?php endif; ?>
                <p class="mb-0">
                    <?php echo htmlspecialchars($invoice_details['city_name']); ?>, 
                    <?php echo htmlspecialchars($invoice_details['district_name']); ?>
                </p>
                <p class="mb-0"><?php echo htmlspecialchars($invoice_details['province_name']); ?>, <?php echo htmlspecialchars($invoice_details['zip_code']); ?></p>
                <p class="mb-0"><?php echo htmlspecialchars($invoice_details['user_email']); ?></p>
                <p class="mb-0"><?php echo htmlspecialchars($invoice_details['user_mobile']); ?></p>
            </div>
            <div class="col-sm-6 text-sm-end">
                <div class="mb-2">
                    <strong>Order Date:</strong>
                    <div><?php echo date("F j, Y, g:i a", strtotime($invoice_details['created_at'])); ?></div>
                </div>
                <div class="mb-2">
                    <strong>Order Status:</strong>
                    <div><?php echo htmlspecialchars($invoice_details['status_name']); ?></div>
                </div>
                <div>
                    <strong>Payment Method:</strong>
                    <div>Cash on Delivery (COD)</div>
                </div>
            </div>
        </div>

        <!-- Order Items Table -->
        <table class="table table-bordered align-middle mb-4">
            <thead class="table-light">
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Product</th>
                    <th scope="col" class="text-end">Unit Price</th>
                    <th scope="col" class="text-end">Quantity</th>
                    <th scope="col" class="text-end">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $item_number = 1;
                foreach($invoice_items as $item): 
                ?>
                <tr>
                    <th scope="row"><?php echo $item_number++; ?></th>
                    <td><?php echo htmlspecialchars($item['product_title']); ?></td>
                    <td class="text-end">LKR <?php echo number_format($item['unit_price'], 2); ?></td>
                    <td class="text-end"><?php echo $item['qty']; ?></td>
                    <td class="text-end">LKR <?php echo number_format($item['total_amount'], 2); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Totals Section -->
        <div class="row">
            <div class="col-sm-7">
                <p class="text-muted"><strong>Notes:</strong> Thank you for shopping with FlyDolk. All sales are final. Please contact support for any issues regarding your order.</p>
            </div>
            <div class="col-sm-5">
                <table class="table totals-table">
                    <tbody>
                        <tr>
                            <td class="text-muted">Subtotal</td>
                            <td class="text-end fw-bold">LKR <?php echo number_format($subtotal, 2); ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Shipping</td>
                            <td class="text-end fw-bold">LKR <?php echo number_format($shipping_fee, 2); ?></td>
                        </tr>
                        <tr>
                            <td class="text-white bg-primary fs-5 fw-bold">Total</td>
                            <td class="text-white bg-primary fs-5 fw-bold text-end">LKR <?php echo number_format($grand_total, 2); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <button class="btn btn-primary btn-lg print-button shadow" onclick="window.print()">
        <i class="fas fa-print me-2"></i> Print Invoice
    </button>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

