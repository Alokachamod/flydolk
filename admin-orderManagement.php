<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>flydolk - Order Management</title>
    
    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        /* Page Styles */
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
        }

        .page-card {
            background-color: #ffffff;
            border: 1px solid #dee2e6;
            border-radius: 0.75rem;
            box-shadow: 0 2px 8px rgba(0,0,0,.05);
        }
        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
        }
        .table th {
            font-weight: 600;
        }

        /* Status Badge Styles */
        .badge.bg-processing { background-color: rgba(13, 110, 253, 0.1); color: #0d6efd; }
        .badge.bg-shipped { background-color: rgba(255, 193, 7, 0.15); color: #ffc107; }
        .badge.bg-delivered { background-color: rgba(25, 135, 84, 0.1); color: #198754; }
        .badge.bg-cancelled { background-color: rgba(220, 53, 69, 0.1); color: #dc3545; }
    </style>
</head>
<body>

    <!-- Header -->
     <header><?php include 'admin-header.php'; ?></header>

    <!-- Main Order Management Page Content -->
    <main class="container-fluid p-4 p-md-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h2 fw-bold">Orders</h1>
            <button class="btn btn-outline-secondary d-flex align-items-center">
                <i class="bi bi-download me-2"></i> Export Orders
            </button>
        </div>

        <div class="page-card card border-0">
            <div class="card-header bg-white border-0 pt-3">
                 <div class="row g-2 align-items-center">
                    <div class="col-lg-4 col-md-6">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control bg-light border-0" placeholder="Search by Order ID or Customer...">
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-6">
                         <select class="form-select bg-light border-0">
                            <option selected>All Statuses</option>
                            <option value="1">Processing</option>
                            <option value="2">Shipped</option>
                            <option value="3">Delivered</option>
                            <option value="4">Cancelled</option>
                         </select>
                    </div>
                     <div class="col-lg-3 col-md-6">
                         <input type="date" class="form-control bg-light border-0">
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" class="p-3">Order ID</th>
                                <th scope="col" class="p-3">Customer</th>
                                <th scope="col" class="p-3">Date</th>
                                <th scope="col" class="p-3">Total</th>
                                <th scope="col" class="p-3">Status</th>
                                <th scope="col" class="p-3 text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="p-3 fw-bold">#ORD-00452</td>
                                <td class="p-3">John Doe</td>
                                <td class="p-3">July 23, 2025</td>
                                <td class="p-3">$2,963.10</td>
                                <td class="p-3"><span class="badge rounded-pill bg-processing">Processing</span></td>
                                <td class="p-3 text-end"><a href="#" class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye"></i> View</a></td>
                            </tr>
                            <tr>
                                <td class="p-3 fw-bold">#ORD-00451</td>
                                <td class="p-3">Jane Smith</td>
                                <td class="p-3">July 22, 2025</td>
                                <td class="p-3">$1,750.00</td>
                                <td class="p-3"><span class="badge rounded-pill bg-shipped">Shipped</span></td>
                                <td class="p-3 text-end"><a href="#" class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye"></i> View</a></td>
                            </tr>
                             <tr>
                                <td class="p-3 fw-bold">#ORD-00450</td>
                                <td class="p-3">Peter Jones</td>
                                <td class="p-3">July 21, 2025</td>
                                <td class="p-3">$699.00</td>
                                <td class="p-3"><span class="badge rounded-pill bg-delivered">Delivered</span></td>
                                <td class="p-3 text-end"><a href="#" class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye"></i> View</a></td>
                            </tr>
                             <tr>
                                <td class="p-3 fw-bold">#ORD-00449</td>
                                <td class="p-3">Alice Williams</td>
                                <td class="p-3">July 20, 2025</td>
                                <td class="p-3">$1,099.00</td>
                                <td class="p-3"><span class="badge rounded-pill bg-cancelled">Cancelled</span></td>
                                <td class="p-3 text-end"><a href="#" class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye"></i> View</a></td>
                            </tr>
                             <tr>
                                <td class="p-3 fw-bold">#ORD-00448</td>
                                <td class="p-3">Sam Brown</td>
                                <td class="p-3">July 19, 2025</td>
                                <td class="p-3">$759.00</td>
                                <td class="p-3"><span class="badge rounded-pill bg-delivered">Delivered</span></td>
                                <td class="p-3 text-end"><a href="#" class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye"></i> View</a></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white">
                <nav class="d-flex justify-content-end">
                    <ul class="pagination mb-0">
                        <li class="page-item disabled"><a class="page-link" href="#">Previous</a></li>
                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                        <li class="page-item"><a class="page-link" href="#">3</a></li>
                        <li class="page-item"><a class="page-link" href="#">Next</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </main>
    <footer>
        <?php include 'admin-footer.php'; ?>
    </footer>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
