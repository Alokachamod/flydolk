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

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        /* This styling ensures the dashboard is visually consistent with the header and footer */
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa; /* Light gray background */
            color: #212529;
        }

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
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <?php include 'admin-header.php'; ?>
    </header>
    
    <!-- Main Dashboard Content -->
    <main class="container-fluid p-4 p-md-5">
        
        <!-- Dashboard Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h2 fw-bold">Dashboard</h1>
            <button class="btn btn-primary d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#createReportModal">
                <i class="bi bi-plus-circle me-2"></i> Create Report
            </button>
        </div>

        <!-- KPI Cards Row -->
        <div class="row g-4 mb-5">
            <div class="col-xl-3 col-md-6">
                <div class="kpi-card card border-0 h-100">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-uppercase">Total Revenue</h6>
                            <p class="card-text fs-2">$48,329</p>
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
                            <p class="card-text fs-2">1,204</p>
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
                            <p class="card-text fs-2">351</p>
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
                            <p class="card-text fs-2">12</p>
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
                        <h5 class="card-title fw-bold">Sales Overview</h5>
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
                                <tbody>
                                    <tr>
                                        <td class="p-3"><div class="d-flex align-items-center"><i class="bi bi-circle-fill text-success me-3"></i><div><div class="fw-bold">#ORD-00452</div><small class="text-muted">DJI Mavic 3 Pro</small></div></div></td>
                                        <td class="text-end fw-bold p-3">$2,199.00</td>
                                    </tr>
                                    <tr>
                                        <td class="p-3"><div class="d-flex align-items-center"><i class="bi bi-circle-fill text-success me-3"></i><div><div class="fw-bold">#ORD-00451</div><small class="text-muted">Autel EVO II</small></div></div></td>
                                        <td class="text-end fw-bold p-3">$1,750.00</td>
                                    </tr>
                                    <tr>
                                        <td class="p-3"><div class="d-flex align-items-center"><i class="bi bi-circle-fill text-warning me-3"></i><div><div class="fw-bold">#ORD-00450</div><small class="text-muted">Parrot Anafi</small></div></div></td>
                                        <td class="text-end fw-bold p-3">$699.00</td>
                                    </tr>
                                    <tr>
                                        <td class="p-3"><div class="d-flex align-items-center"><i class="bi bi-circle-fill text-danger me-3"></i><div><div class="fw-bold">#ORD-00449</div><small class="text-muted">Skydio 2+</small></div></div></td>
                                        <td class="text-end fw-bold p-3">$1,099.00</td>
                                    </tr>
                                     <tr>
                                        <td class="p-3"><div class="d-flex align-items-center"><i class="bi bi-circle-fill text-success me-3"></i><div><div class="fw-bold">#ORD-00448</div><small class="text-muted">DJI Mini 3</small></div></div></td>
                                        <td class="text-end fw-bold p-3">$759.00</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    
    <!-- Create Report Modal -->
    <div class="modal fade" id="createReportModal" tabindex="-1" aria-labelledby="createReportModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createReportModalLabel">Create New Report</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="mb-3">
                            <label for="reportType" class="form-label">Report Type</label>
                            <select class="form-select" id="reportType">
                                <option selected>Choose report type...</option>
                                <option value="sales">Sales Report</option>
                                <option value="inventory">Inventory Report</option>
                                <option value="customers">Customer Report</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="dateRange" class="form-label">Date Range</label>
                            <select class="form-select" id="dateRange">
                                <option selected>Choose date range...</option>
                                <option value="7">Last 7 Days</option>
                                <option value="30">Last 30 Days</option>
                                <option value="90">Last 90 Days</option>
                                <option value="custom">Custom Range</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="exportFormat" class="form-label">Export Format</label>
                            <select class="form-select" id="exportFormat">
                                <option selected>Choose format...</option>
                                <option value="pdf">PDF</option>
                                <option value="csv">CSV</option>
                                <option value="excel">Excel</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary">Generate Report</button>
                </div>
            </div>
        </div>
    </div>
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
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
                datasets: [{
                    label: 'Sales',
                    data: [12000, 19000, 15000, 21000, 18000, 25000, 22000],
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
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#e9ecef' } },
                    x: { grid: { display: false } }
                }
            }
        });
    </script>
</body>
</html>
