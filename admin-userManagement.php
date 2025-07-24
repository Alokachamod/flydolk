<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>flydolk - User Management</title>
    
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
        .avatar {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 50%;
        }
        /* Status Badge Styles */
        .badge.bg-active { background-color: rgba(25, 135, 84, 0.1); color: #198754; }
        .badge.bg-deactivated { background-color: rgba(220, 53, 69, 0.1); color: #dc3545; }
    </style>
</head>
<body>

    <!-- Header -->
    <header><?php include 'admin-header.php'; ?></header>

    <!-- Main User Management Page Content -->
    <main class="container-fluid p-4 p-md-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h2 fw-bold">User Management</h1>
            <button class="btn btn-outline-secondary d-flex align-items-center">
                <i class="bi bi-download me-2"></i> Export Users
            </button>
        </div>

        <div class="page-card card border-0">
            <div class="card-header bg-white border-0 pt-3">
                 <div class="row g-2 align-items-center">
                    <div class="col-lg-4 col-md-6">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control bg-light border-0" placeholder="Search by name or email...">
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-6">
                         <select class="form-select bg-light border-0">
                            <option selected>All Statuses</option>
                            <option value="1">Active</option>
                            <option value="2">Deactivated</option>
                         </select>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" class="p-3">User</th>
                                <th scope="col" class="p-3">Email</th>
                                <th scope="col" class="p-3">Total Orders</th>
                                <th scope="col" class="p-3">Date Registered</th>
                                <th scope="col" class="p-3">Status</th>
                                <th scope="col" class="p-3 text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="p-3">
                                    <div class="d-flex align-items-center">
                                        <img src="https://placehold.co/100x100/EBF5FF/0D6EFD?text=JD" class="avatar me-3" alt="John Doe">
                                        <span class="fw-bold">John Doe</span>
                                    </div>
                                </td>
                                <td class="p-3">john.doe@example.com</td>
                                <td class="p-3">12</td>
                                <td class="p-3">July 23, 2025</td>
                                <td class="p-3"><span class="badge rounded-pill bg-active">Active</span></td>
                                <td class="p-3 text-end">
                                    <button class="btn btn-sm btn-outline-secondary me-1"><i class="bi bi-person-lines-fill"></i></button>
                                    <button class="btn btn-sm btn-outline-warning me-1"><i class="bi bi-slash-circle"></i></button>
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </td>
                            </tr>
                            <tr>
                                <td class="p-3">
                                    <div class="d-flex align-items-center">
                                        <img src="https://placehold.co/100x100/EBF5FF/0D6EFD?text=JS" class="avatar me-3" alt="Jane Smith">
                                        <span class="fw-bold">Jane Smith</span>
                                    </div>
                                </td>
                                <td class="p-3">jane.smith@example.com</td>
                                <td class="p-3">8</td>
                                <td class="p-3">July 22, 2025</td>
                                <td class="p-3"><span class="badge rounded-pill bg-active">Active</span></td>
                                <td class="p-3 text-end">
                                    <button class="btn btn-sm btn-outline-secondary me-1"><i class="bi bi-person-lines-fill"></i></button>
                                    <button class="btn btn-sm btn-outline-warning me-1"><i class="bi bi-slash-circle"></i></button>
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </td>
                            </tr>
                            <tr>
                                <td class="p-3">
                                    <div class="d-flex align-items-center">
                                        <img src="https://placehold.co/100x100/EBF5FF/0D6EFD?text=PJ" class="avatar me-3" alt="Peter Jones">
                                        <span class="fw-bold">Peter Jones</span>
                                    </div>
                                </td>
                                <td class="p-3">peter.jones@example.com</td>
                                <td class="p-3">25</td>
                                <td class="p-3">July 21, 2025</td>
                                <td class="p-3"><span class="badge rounded-pill bg-deactivated">Deactivated</span></td>
                                <td class="p-3 text-end">
                                    <button class="btn btn-sm btn-outline-secondary me-1"><i class="bi bi-person-lines-fill"></i></button>
                                    <button class="btn btn-sm btn-outline-success me-1"><i class="bi bi-arrow-counterclockwise"></i></button>
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </td>
                            </tr>
                             <tr>
                                <td class="p-3">
                                    <div class="d-flex align-items-center">
                                        <img src="https://placehold.co/100x100/EBF5FF/0D6EFD?text=AW" class="avatar me-3" alt="Alice Williams">
                                        <span class="fw-bold">Alice Williams</span>
                                    </div>
                                </td>
                                <td class="p-3">alice.w@example.com</td>
                                <td class="p-3">5</td>
                                <td class="p-3">July 20, 2025</td>
                                <td class="p-3"><span class="badge rounded-pill bg-active">Active</span></td>
                                <td class="p-3 text-end">
                                    <button class="btn btn-sm btn-outline-secondary me-1"><i class="bi bi-person-lines-fill"></i></button>
                                    <button class="btn btn-sm btn-outline-warning me-1"><i class="bi bi-slash-circle"></i></button>
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </td>
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
