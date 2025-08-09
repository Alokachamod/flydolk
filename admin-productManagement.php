<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>flydolk - Products</title>
    
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
        .product-image {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 0.5rem;
        }
        .bg-success-light { background-color: rgba(25, 135, 84, 0.1); }
        .text-success-light { color: #198754; }
        .bg-warning-light { background-color: rgba(255, 193, 7, 0.1); }
        .text-warning-light { color: #ffc107; }
        .bg-danger-light { background-color: rgba(220, 53, 69, 0.1); }
        .text-danger-light { color: #dc3545; }

        /* Modal Specific Styles */
        .modal-header {
            border-bottom: 1px solid #dee2e6;
        }
        .modal-footer {
            border-top: 1px solid #dee2e6;
            background-color: #f8f9fa;
        }
        .form-label {
            font-weight: 500;
        }
        .image-dropzone {
            border: 2px dashed #dee2e6;
            border-radius: 0.75rem;
            padding: 2rem;
            text-align: center;
            cursor: pointer;
            transition: border-color 0.2s ease-in-out, background-color 0.2s ease-in-out;
        }
        .image-dropzone:hover {
            border-color: var(--bs-primary);
            background-color: #f8f9fa;
        }
        .image-dropzone i {
            font-size: 2.5rem;
            color: #adb5bd;
        }
        .image-dropzone p {
            margin-top: 1rem;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <?php
    include 'admin-Header.php'; // Include the header component
    ?>
    
    <!-- Main Products Page Content -->
    <main class="container-fluid p-4 p-md-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h2 fw-bold">Products</h1>
            <!-- This button now triggers the modal -->
            <button class="btn btn-primary d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#addProductModal">
                <i class="bi bi-plus-circle me-2"></i> Add New Product
            </button>
        </div>

        <div class="page-card card border-0">
            <div class="card-header bg-white border-0 pt-3">
                 <div class="row g-2 align-items-center">
                    <div class="col-lg-4 col-md-6">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control bg-light border-0" placeholder="Search products...">
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-6">
                         <select class="form-select bg-light border-0">
                            <option selected>All Categories</option>
                            <option value="1">Professional</option>
                            <option value="2">Hobbyist</option>
                            <option value="3">FPV Racing</option>
                        </select>
                    </div>
                     <div class="col-lg-2 col-md-6">
                         <select class="form-select bg-light border-0">
                            <option selected>All Brands</option>
                            <option value="1">DJI</option>
                            <option value="2">Autel</option>
                            <option value="3">Parrot</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" class="p-3">Product Name</th>
                                <th scope="col" class="p-3">Category</th>
                                <th scope="col" class="p-3">Brand</th>
                                <th scope="col" class="p-3">Price</th>
                                <th scope="col" class="p-3">Stock</th>
                                <th scope="col" class="p-3 text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="p-3"><div class="d-flex align-items-center"><img src="https://placehold.co/100x100/EBF5FF/0D6EFD?text=M3P" class="product-image me-3" alt="DJI Mavic 3 Pro"><span class="fw-bold">DJI Mavic 3 Pro</span></div></td>
                                <td class="p-3">Professional</td>
                                <td class="p-3">DJI</td>
                                <td class="p-3">$2,199.00</td>
                                <td class="p-3"><span class="badge bg-success-light text-success-light">In Stock (42)</span></td>
                                <td class="p-3 text-end"><button class="btn btn-sm btn-outline-secondary me-1"><i class="bi bi-pencil-square"></i></button><button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></td>
                            </tr>
                            <tr>
                                <td class="p-3"><div class="d-flex align-items-center"><img src="https://placehold.co/100x100/EBF5FF/0D6EFD?text=AE2" class="product-image me-3" alt="Autel EVO II"><span class="fw-bold">Autel EVO II</span></div></td>
                                <td class="p-3">Professional</td>
                                <td class="p-3">Autel</td>
                                <td class="p-3">$1,750.00</td>
                                <td class="p-3"><span class="badge bg-success-light text-success-light">In Stock (25)</span></td>
                                <td class="p-3 text-end"><button class="btn btn-sm btn-outline-secondary me-1"><i class="bi bi-pencil-square"></i></button><button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></td>
                            </tr>
                             <tr>
                                <td class="p-3"><div class="d-flex align-items-center"><img src="https://placehold.co/100x100/EBF5FF/0D6EFD?text=PA" class="product-image me-3" alt="Parrot Anafi"><span class="fw-bold">Parrot Anafi</span></div></td>
                                <td class="p-3">Hobbyist</td>
                                <td class="p-3">Parrot</td>
                                <td class="p-3">$699.00</td>
                                <td class="p-3"><span class="badge bg-warning-light text-warning-light">Low Stock (5)</span></td>
                                <td class="p-3 text-end"><button class="btn btn-sm btn-outline-secondary me-1"><i class="bi bi-pencil-square"></i></button><button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></td>
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

    <!-- Add Product Modal -->
    <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title h2 fw-bold" id="addProductModalLabel">Add New Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <!-- Form Content from the previous 'Add Product' page -->
                    <div class="row g-4">
                        <!-- Left Column: Main Product Details -->
                        <div class="col-lg-8">
                            <div class="page-card card border-0 mb-4">
                                <div class="card-body p-4">
                                    <h5 class="mb-3 fw-bold">Product Information</h5>
                                    <div class="mb-3">
                                        <label for="productName" class="form-label">Product Name</label>
                                        <input type="text" class="form-control" id="productName" placeholder="e.g., DJI Mavic 3 Pro">
                                    </div>
                                    
                                    <div>
                                        <label for="productDescription" class="form-label">Description</label>
                                        <textarea class="form-control" id="productDescription" rows="6" placeholder="Provide a detailed description..."></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="page-card card border-0">
                                <div class="card-body p-4">
                                    <h5 class="mb-3 fw-bold">Media</h5>
                                    <div class="image-dropzone">
                                        <input type="file" id="imageUpload" class="d-none" multiple>
                                        <label for="imageUpload" class="w-100">
                                            <i class="bi bi-cloud-arrow-up"></i>
                                            <p class="mb-0"><b>Click to upload</b> or drag and drop.</p>
                                            <small class="text-muted">PNG, JPG, GIF up to 10MB</small>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Right Column: Pricing, Stock, and Organization -->
                        <div class="col-lg-4">
                            <div class="page-card card border-0 mb-4">
                                <div class="card-body p-4">
                                    <h5 class="mb-3 fw-bold">Pricing</h5>
                                    <label for="productPrice" class="form-label">Price</label>
                                    <div class="input-group"><span class="input-group-text">$</span><input type="text" class="form-control" id="productPrice" placeholder="0.00"></div>
                                </div>
                            </div>
                            <div class="page-card card border-0 mb-4">
                                <div class="card-body p-4">
                                    <h5 class="mb-3 fw-bold">Organization</h5>
                                    <div class="mb-3">
                                        <label for="productCategory" class="form-label">Category</label>
                                        <select class="form-select" id="productCategory"><option selected>Select...</option><option value="1">Professional</option><option value="2">Hobbyist</option></select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="productBrand" class="form-label">Brand</label>
                                        <select class="form-select" id="productBrand"><option selected>Select...</option><option value="1">DJI</option><option value="2">Autel</option></select>
                                    </div>
                                    <div>
                                        <label for="productModel" class="form-label">Color</label>
                                        <select class="form-select" id="productModel"><option selected>Select...</option><option value="1">Mavic Series</option><option value="2">EVO Series</option></select>
                                    </div>
                                </div>
                            </div>
                            <div class="page-card card border-0">
                                <div class="card-body p-4">
                                    <h5 class="mb-3 fw-bold">Stock & Status</h5>
                                    <div class="mb-3">
                                        <label for="productStock" class="form-label">Stock Quantity</label>
                                        <input type="number" class="form-control" id="productStock" placeholder="0">
                                    </div>
                                    <div>
                                        <label for="productStatus" class="form-label">Status</label>
                                        <select class="form-select" id="productStatus"><option value="1" selected>Published</option><option value="2">Draft</option></select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary">Save Product</button>
                </div>
            </div>
        </div>
    </div>

    <?php
    include 'admin-footer.php'; // Include the footer component
    ?>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
