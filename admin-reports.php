<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin-login.php");
    exit();
}
include 'connection.php';

// Initialize variables
$report_type = $_POST['report_type'] ?? null;
$date_range = $_POST['date_range'] ?? 'last_30_days';
$report_data = null;
$report_headers = [];
$report_title = "Reports";
$start_date = '';
$end_date = '';

// --- Handle Report Generation ---
if ($report_type) {
    // --- 1. Determine Date Range ---
    date_default_timezone_set('Asia/Colombo'); 
    $end_date_time = new DateTime();
    
    switch ($date_range) {
        case 'last_7_days':
            $start_date_time = (new DateTime())->modify('-7 days');
            break;
        case 'last_30_days':
            $start_date_time = (new DateTime())->modify('-30 days');
            break;
        case 'this_year':
            $start_date_time = new DateTime(date('Y-01-01'));
            break;
        case 'all_time':
        default:
            $start_date_time = new DateTime('1970-01-01');
            break;
    }
    
    // Format for SQL
    $start_date = $start_date_time->format('Y-m-d 00:00:00');
    $end_date = $end_date_time->format('Y-m-d 23:59:59');

    $sql_date_condition = " AND i.created_at BETWEEN '" . addslashes($start_date) . "' AND '" . addslashes($end_date) . "'";
    if ($date_range == 'all_time') {
        $sql_date_condition = ''; // No date filter for all time
    }

    // --- 2. Build SQL Query Based on Report Type ---
    $sql = "";
    switch ($report_type) {
        case 'sales_summary':
            $report_title = "Sales Summary Report";
            $report_headers = ["Order ID", "Date", "Customer", "Items", "Status", "Total Amount"];
            $sql = "
                SELECT 
                    i.order_id, 
                    MAX(i.created_at) AS order_date, 
                    MAX(u.name) AS customer_name,
                    COUNT(*) AS items_count, -- [FIX] Changed from COUNT(i.id) to COUNT(*) as invoice table has no 'id'
                    MAX(s.name) AS status_name,
                    SUM(i.total_amount) AS total
                FROM invoice i
                LEFT JOIN `status` s ON i.status_id = s.id
                LEFT JOIN user_has_address uha ON i.user_has_address_id = uha.id
                LEFT JOIN user u ON uha.user_id = u.id
                WHERE i.order_id IS NOT NULL AND i.order_id != ''
                {$sql_date_condition}
                GROUP BY i.order_id
                ORDER BY order_date DESC;
            ";
            break;
        
        case 'inventory_status':
            $report_title = "Inventory Status Report";
            $report_headers = ["Product ID", "Product Name", "Category", "Brand", "Stock Quantity", "Status"];
            $sql = "
                SELECT 
                    p.id, 
                    p.title, 
                    c.name AS category_name, 
                    b.name AS brand_name, 
                    p.qty, 
                    ps.name AS status_name
                FROM product p
                LEFT JOIN category c ON p.category_id = c.id
                LEFT JOIN brand b ON p.brand_id = b.id
                LEFT JOIN product_status ps ON p.product_status_id = ps.id
                ORDER BY p.qty ASC, p.title ASC;
            ";
            // Note: Date range doesn't apply to inventory status
            break;
            
        case 'customer_summary':
            $report_title = "Customer Summary Report";
            $report_headers = ["User ID", "Customer Name", "Email", "Total Orders", "Total Spent"];
            $sql = "
                SELECT 
                    u.id, 
                    u.name, 
                    u.email, 
                    COUNT(DISTINCT i.order_id) AS total_orders,
                    SUM(i.total_amount) AS total_spent
                FROM user u
                LEFT JOIN user_has_address uha ON u.id = uha.user_id
                LEFT JOIN invoice i ON uha.id = i.user_has_address_id
                WHERE (i.order_id IS NOT NULL AND i.order_id != '')
                {$sql_date_condition}
                GROUP BY u.id
                ORDER BY total_spent DESC;
            ";
            break;
    }

    // --- 3. Fetch Data ---
    if (!empty($sql)) {
        $rs = Database::search($sql);
        if ($rs) {
            $report_data = [];
            while ($row = $rs->fetch_assoc()) {
                $report_data[] = $row;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>flydolk - Reports</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="icon" type="image/x-icon" href="imgs/Flydo.png">
    <link rel="stylesheet" href="style.css">
    
    <style>
        /* Shared admin styles */
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        main { flex: 1 0 auto; }
        footer { flex-shrink: 0; }
        .page-card {
            background-color: #ffffff;
            border: 1px solid #dee2e6;
            border-radius: 0.75rem;
            box-shadow: 0 2px 8px rgba(0,0,0,.05);
        }
        .table-hover tbody tr:hover { background: #f8f9fa }
        .table th { font-weight: 600 }
        .form-label { font-weight: 500; }
    </style>
</head>
<body>
    <header>
        <?php include 'admin-Header.php'; ?>
    </header>
    
    <main class="container-fluid p-4 p-md-5">
        
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h2 fw-bold">Generate Reports</h1>
        </div>

        <!-- Report Generation Form -->
        <div class="page-card card border-0 mb-4">
            <div class="card-body p-4">
                <form method="POST" action="admin-reports.php">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label for="report_type" class="form-label">Report Type</label>
                            <select class="form-select" id="report_type" name="report_type" required>
                                <option value="">Select a report...</option>
                                <option value="sales_summary" <?php echo ($report_type == 'sales_summary' ? 'selected' : ''); ?>>Sales Summary</option>
                                <option value="inventory_status" <?php echo ($report_type == 'inventory_status' ? 'selected' : ''); ?>>Inventory Status</option>
                                <option value="customer_summary" <?php echo ($report_type == 'customer_summary' ? 'selected' : ''); ?>>Customer Summary</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="date_range" class="form-label">Date Range</label>
                            <select class="form-select" id="date_range" name="date_range">
                                <option value="last_7_days" <?php echo ($date_range == 'last_7_days' ? 'selected' : ''); ?>>Last 7 Days</option>
                                <option value="last_30_days" <?php echo ($date_range == 'last_30_days' ? 'selected' : ''); ?>>Last 30 Days</option>
                                <option value="this_year" <?php echo ($date_range == 'this_year' ? 'selected' : ''); ?>>This Year</option>
                                <option value="all_time" <?php echo ($date_range == 'all_time' ? 'selected' : ''); ?>>All Time</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-play-fill me-1"></i> Generate Report
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Report Results -->
        <?php if ($report_data !== null): ?>
            <div class="page-card card border-0">
                <div class="card-header bg-white border-0 pt-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title fw-bold mb-0"><?php echo htmlspecialchars($report_title); ?></h5>
                    <button class="btn btn-sm btn-outline-success" disabled>
                        <i class="bi bi-file-earmark-spreadsheet me-1"></i> Export to CSV (Soon)
                    </button>
                </div>
                
                <?php if (count($report_data) > 0): ?>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <?php foreach ($report_headers as $header): ?>
                                            <th class="p-3"><?php echo $header; ?></th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($report_data as $row): ?>
                                        <tr>
                                            <?php foreach ($row as $key => $cell): ?>
                                                <td class="p-3">
                                                    <?php 
                                                        // Simple formatting for common fields
                                                        if (in_array($key, ['total', 'total_spent', 'total_amount'])) {
                                                            echo 'LKR ' . number_format((float)$cell, 2);
                                                        } elseif ($key == 'order_date') {
                                                            echo date("M j, Y, g:i A", strtotime($cell));
                                                        } else {
                                                            echo htmlspecialchars($cell); 
                                                        }
                                                    ?>
                                                </td>
                                            <?php endforeach; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Show if the report ran but found 0 rows -->
                    <div class="card-body text-center text-muted p-5">
                        <i class="bi bi-info-circle fs-3 d-block mb-2"></i>
                        No data found for the selected criteria.
                    </div>
                <?php endif; ?>
            </div>
        <?php elseif ($report_type): ?>
             <!-- Show if the report type was selected but something went wrong (e.g., $report_data is null) -->
             <div class="page-card card border-0">
                <div class="card-body text-center text-danger p-5">
                    <i class="bi bi-exclamation-triangle fs-3 d-block mb-2"></i>
                    Could not generate report.
                </div>
             </div>
        <?php endif; ?>

    </main>
    
    <footer>
        <?php include 'admin-footer.php'; ?>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

