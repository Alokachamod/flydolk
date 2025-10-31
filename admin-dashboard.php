<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin-login.php");
    exit();
}
include 'connection.php';

// --- 1. KPI Card Data ---

// Total Revenue (Only from completed/delivered/shipped orders)
$revenue_rs = Database::search("
    SELECT SUM(i.total_amount) AS total_revenue 
    FROM invoice i 
    JOIN status s ON i.status_id = s.id 
    WHERE s.name IN ('Completed', 'Delivered', 'Shipped')
");
$revenue_data = $revenue_rs->fetch_assoc();
$total_revenue = $revenue_data['total_revenue'] ?? 0;

// Total Orders (Unique)
$orders_rs = Database::search("SELECT COUNT(DISTINCT order_id) AS total_orders FROM invoice WHERE order_id IS NOT NULL AND order_id != ''");
$orders_data = $orders_rs->fetch_assoc();
$total_orders = $orders_data['total_orders'] ?? 0;

// Total Customers
$users_rs = Database::search("SELECT COUNT(id) AS total_users FROM user");
$users_data = $users_rs->fetch_assoc();
$total_users = $users_data['total_users'] ?? 0;

// Pending Orders (Unique)
$pending_rs = Database::search("
    SELECT COUNT(DISTINCT i.order_id) AS pending_orders 
    FROM invoice i 
    JOIN status s ON i.status_id = s.id 
    WHERE s.name = 'Pending' AND i.order_id IS NOT NULL AND i.order_id != ''
");
$pending_data = $pending_rs->fetch_assoc();
$total_pending = $pending_data['pending_orders'] ?? 0;


// --- 2. Chart Data (Sales This Year) ---
$sales_rs = Database::search("
    SELECT 
        MONTH(created_at) AS month_num,
        SUM(total_amount) AS monthly_sales
    FROM invoice
    WHERE YEAR(created_at) = YEAR(CURDATE())
    GROUP BY MONTH(created_at)
    ORDER BY month_num ASC;
");

// Initialize 12 months with 0 sales
$chart_labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
$chart_data = array_fill(0, 12, 0);

if ($sales_rs) {
    while ($row = $sales_rs->fetch_assoc()) {
        $month_index = (int)$row['month_num'] - 1; // 1 (Jan) becomes 0
        $chart_data[$month_index] = (float)$row['monthly_sales'];
    }
}

// Convert PHP arrays to JSON for JavaScript
$chart_labels_json = json_encode($chart_labels);
$chart_data_json = json_encode($chart_data);

// --- 3. Recent Orders Table Data ---
$recent_orders_rs = Database::search("
    SELECT 
        i.order_id, 
        MAX(i.created_at) AS created_at, 
        SUM(i.total_amount) AS total, 
        MAX(s.name) AS status_name,
        MAX(u.name) AS customer_name
    FROM invoice i
    LEFT JOIN `status` s ON i.status_id = s.id
    LEFT JOIN user_has_address uha ON i.user_has_address_id = uha.id
    LEFT JOIN user u ON uha.user_id = u.id
    WHERE i.order_id IS NOT NULL AND i.order_id != ''
    GROUP BY i.order_id
    ORDER BY MAX(i.created_at) DESC
    LIMIT 5;
");

// Status badge classes (from orderManagement page)
$status_classes = [
    'pending' => 'bg-warning text-dark',
    'processing' => 'bg-info text-dark',
    'shipped' => 'bg-primary',
    'delivered' => 'bg-success',
    'completed' => 'bg-success',
    'cancelled' => 'bg-danger',
    'refunded' => 'bg-secondary',
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>flydolk - Admin Dashboard</title>
    
    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="icon" type="image/x-icon" href="imgs/Flydo.png">

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        /* This styling ensures the dashboard is visually consistent with the header and footer */
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa; /* Light gray background */
            color: #212529;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        main { flex: 1 0 auto; }
        footer { flex-shrink: 0; }

        .kpi-card {
            background-color: #ffffff;
            border: 1px solid #dee2e6;
            border-radius: 0.75rem;
            box-shadow: 0 2px 8px rgba(0,0,0,.05);
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }
        .kpi-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0,0,0,.08);
        }
        .kpi-card .card-title {
            font-weight: 500;
            color: #6c757d;
        }
        .kpi-card .card-text {
            font-weight: 700;
            color: #212529;
        }
        .kpi-card .icon-bg {
            padding: 0.75rem;
            border-radius: 50%;
        }
        .text-success-light { color: #198754; }
        .bg-success-light { background-color: rgba(25, 135, 84, 0.1); }
        .text-primary-light { color: #0d6efd; }
        .bg-primary-light { background-color: rgba(13, 110, 253, 0.1); }
        .text-warning-light { color: #ffc107; }
        .bg-warning-light { background-color: rgba(255, 193, 7, 0.1); }
        .text-danger-light { color: #dc3545; }
        .bg-danger-light { background-color: rgba(220, 53, 69, 0.1); }

        .chart-card, .table-card {
            background-color: #ffffff;
            border: 1px solid #dee2e6;
            border-radius: 0.75rem;
            box-shadow: 0 2px 8px rgba(0,0,0,.05);
        }

        .table-card .table {
            margin-bottom: 0;
        }
        .table-card .table th {
            font-weight: 600;
        }
        .table-card .table td {
            white-space: nowrap;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <?php include 'admin-Header.php'; ?>
    </header>
    
    <!-- Main Dashboard Content -->
    <main class="container-fluid p-4 p-md-5">
        
        <!-- Dashboard Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h2 fw-bold">Dashboard</h1>
            <!-- [FIX] Changed this from a modal button to a link to the new reports page -->
            <a href="admin-reports.php" class="btn btn-primary d-flex align-items-center">
                <i class="bi bi-file-earmark-bar-graph me-2"></i> Generate Reports
            </a>
        </div>

        <!-- KPI Cards Row -->
        <div class="row g-4 mb-5">
            <div class="col-xl-3 col-md-6">
                <div class="kpi-card card border-0 h-100">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-uppercase">Total Revenue</h6>
                            <p class="card-text fs-2">LKR <?php echo number_format($total_revenue, 2); ?></p>
                        </div>
                        <div class="icon-bg bg-success-light">
                            <i class="bi bi-cash-stack fs-2 text-success-light"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="kpi-card card border-0 h-100">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-uppercase">Orders</h6>
                            <p class="card-text fs-2"><?php echo number_format($total_orders); ?></p>
                        </div>
                        <div class="icon-bg bg-primary-light">
                            <i class="bi bi-box-seam fs-2 text-primary-light"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="kpi-card card border-0 h-100">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-uppercase">Customers</h6>
                            <p class="card-text fs-2"><?php echo number_format($total_users); ?></p>
                        </div>
                        <div class="icon-bg bg-warning-light">
                            <i class="bi bi-people fs-2 text-warning-light"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                 <div class="kpi-card card border-0 h-100">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-uppercase">Pending</h6>
                            <p class="card-text fs-2"><?php echo number_format($total_pending); ?></p>
                        </div>
                        <div class="icon-bg bg-danger-light">
                            <i class="bi bi-hourglass-split fs-2 text-danger-light"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts and Tables Row -->
        <div class="row g-4">
            <!-- Sales Chart -->
            <div class="col-lg-7">
                <div class="chart-card card border-0 h-100">
                    <div class="card-header bg-white border-0 pt-3">
                        <h5 class="card-title fw-bold">Sales Overview (This Year)</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Recent Orders Table -->
            <div class="col-lg-5">
                <div class="table-card card border-0 h-100">
                     <div class="card-header bg-white border-0 pt-3">
                        <h5 class="card-title fw-bold">Recent Orders</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th class="p-3">Order ID</th>
                                        <th class="p-3">Customer</th>
                                        <th class="p-3">Total</th>
                                        <th class="p-3">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($recent_orders_rs && $recent_orders_rs->num_rows > 0) {
                                        while ($row = $recent_orders_rs->fetch_assoc()) {
                                            $status_name = strtolower(htmlspecialchars($row['status_name'] ?? 'N/A'));
                                            $badge_class = $status_classes[$status_name] ?? 'bg-light text-dark';
                                    ?>
                                    <tr>
                                        <td class="p-3 fw-bold">
                                            <a href="admin-orderManagement.php" class="text-decoration-none">
                                                <?php echo htmlspecialchars($row['order_id']); ?>
                                            </a>
                                        </td>
                                        <td class="p-3"><?php echo htmlspecialchars($row['customer_name'] ?? 'Unknown User'); ?></td>
                                        <td class="p-3">LKR <?php echo number_format($row['total'], 2); ?></td>
                                        <td class="p-3">
                                            <span class="badge <?php echo $badge_class; ?>">
                                                <?php echo htmlspecialchars($row['status_name'] ?? 'N/A'); ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php
                                        } // end while
                                    } else {
                                        echo '<tr><td colspan="4" class="p-3 text-center text-muted">No recent orders found.</td></tr>';
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    
    <!-- [FIX] Removed the placeholder "Create Report Modal" as it is now a separate page -->
    
    <!-- Footer -->
     <footer>
        <?php
        include 'admin-footer.php'
        ?>
     </footer>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Chart.js Initialization -->
    <script>
        const ctx = document.getElementById('salesChart').getContext('2d');
        const salesChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo $chart_labels_json; ?>, // Inject PHP data
                datasets: [{
                    label: 'Sales',
                    data: <?php echo $chart_data_json; ?>, // Inject PHP data
                    backgroundColor: 'rgba(13, 110, 253, 0.1)',
                    borderColor: 'rgba(13, 110, 253, 1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: 'rgba(13, 110, 253, 1)',
                    pointBorderColor: '#fff',
                    pointHoverRadius: 6,
                    pointRadius: 4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { 
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    // Format as currency
                                    label += new Intl.NumberFormat('en-US', { style: 'currency', currency: 'LKR' }).format(context.parsed.y);
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    y: { 
                        beginAtZero: true, 
                        grid: { color: '#e9ecef' },
                        ticks: {
                            callback: function(value, index, ticks) {
                                // Format Y-axis as currency
                                return 'LKR ' + value.toLocaleString();
                            }
                        }
                    },
                    x: { grid: { display: false } }
                }
            }
        });

        // [NEW] Set default dates in the modal
        document.addEventListener('DOMContentLoaded', () => {
            const today = new Date().toISOString().split('T')[0];
            const dateToEl = document.getElementById('dateTo');
            const dateFromEl = document.getElementById('dateFrom');

            if(dateToEl) {
                dateToEl.value = today;
            }
            if(dateFromEl) {
                // Default to 30 days ago
                const thirtyDaysAgo = new Date(Date.now() - 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0];
                dateFromEl.value = thirtyDaysAgo;
            }
        });
    </script>
</body>
</html>


