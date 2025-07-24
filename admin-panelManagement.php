<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>flydolk - Admin Management</title>
    
    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="imgs/Flydo.png">
    <link rel="stylesheet" href="style.css">
    
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
    </style>
</head>
<body>
    <!-- Header -->
    <header><?php include 'admin-header.php'; ?></header>

    <!-- Main Admin Management Page Content -->
    <main class="container-fluid p-4 p-md-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h2 fw-bold">Admin Management</h1>
            <button class="btn btn-primary d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#addAdminModal">
                <i class="bi bi-person-plus-fill me-2"></i> Add Admin
            </button>
        </div>

        <div class="page-card card border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" class="p-3">Admin User</th>
                                <th scope="col" class="p-3">Role</th>
                                <th scope="col" class="p-3">Last Login</th>
                                <th scope="col" class="p-3 text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="p-3">
                                    <div class="d-flex align-items-center">
                                        <img src="https://placehold.co/100x100/EBF5FF/0D6EFD?text=SA" class="avatar me-3" alt="Super Admin">
                                        <div>
                                            <div class="fw-bold">Admin User</div>
                                            <div class="text-muted small">admin@flydolk.com</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-3"><span class="badge bg-primary">Super Admin</span></td>
                                <td class="p-3">July 23, 2025 - 09:00 PM</td>
                                <td class="p-3 text-end">
                                    <button class="btn btn-sm btn-outline-secondary me-1" disabled><i class="bi bi-pencil-square"></i></button>
                                    <button class="btn btn-sm btn-outline-danger" disabled><i class="bi bi-trash"></i></button>
                                </td>
                            </tr>
                            <tr>
                                <td class="p-3">
                                    <div class="d-flex align-items-center">
                                        <img src="https://placehold.co/100x100/EBF5FF/0D6EFD?text=PM" class="avatar me-3" alt="Product Manager">
                                        <div>
                                            <div class="fw-bold">Product Manager</div>
                                            <div class="text-muted small">products@flydolk.com</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-3"><span class="badge bg-secondary">Product Manager</span></td>
                                <td class="p-3">July 22, 2025 - 10:15 AM</td>
                                <td class="p-3 text-end">
                                    <button class="btn btn-sm btn-outline-secondary me-1" data-bs-toggle="modal" data-bs-target="#editAdminModal"><i class="bi bi-pencil-square"></i></button>
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <!-- Add Admin Modal -->
    <div class="modal fade" id="addAdminModal" tabindex="-1" aria-labelledby="addAdminModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title h5 fw-bold" id="addAdminModalLabel">Add New Admin</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="mb-3">
                            <label for="addAdminEmail" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="addAdminEmail" placeholder="name@example.com">
                        </div>
                        <div class="mb-3">
                            <label for="addAdminPassword" class="form-label">Password</label>
                            <input type="password" class="form-control" id="addAdminPassword" placeholder="Enter a strong password">
                        </div>
                         <div class="mb-3">
                            <label for="addAdminRole" class="form-label">Role</label>
                            <select class="form-select" id="addAdminRole">
                                <option selected>Select a role...</option>
                                <option value="1">Super Admin</option>
                                <option value="2">Product Manager</option>
                                <option value="3">Order Manager</option>
                                <option value="4">Support Staff</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary">Add Admin</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Edit Admin Modal (for Promote/Demote) -->
    <div class="modal fade" id="editAdminModal" tabindex="-1" aria-labelledby="editAdminModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title h5 fw-bold" id="editAdminModalLabel">Promote / Demote Admin</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="mb-3">
                            <label for="editAdminEmail" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="editAdminEmail" value="products@flydolk.com" readonly>
                        </div>
                         <div class="mb-3">
                            <label for="editAdminRole" class="form-label">Role</label>
                            <select class="form-select" id="editAdminRole">
                                <option value="1">Super Admin</option>
                                <option value="2" selected>Product Manager</option>
                                <option value="3">Order Manager</option>
                                <option value="4">Support Staff</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary">Save Changes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer><?php include 'admin-footer.php'; ?></footer>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
