<?php
// Start the session on every page
session_start();

// Check if the admin is logged in. If not, kick them back to the login page.
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin-login.php");
    exit(); // Stop the script from running
}

// Include the database connection file
include 'connection.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>flydolk - Management</title>

    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="bootstrap.css">

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
        /* --- ADD THESE LINES --- */
        display: flex;
        flex-direction: column;
        min-height: 100vh;
        /* ----------------------- */
    }

    /* --- ADD THIS RULE --- */
    main {
        flex-grow: 1;
    }
    /* ----------------------- */

    .page-card {
        background-color: #ffffff;
        border: 1px solid #dee2e6;
        border-radius: 0.75rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, .05);
    }

        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
        }

        .table th {
            font-weight: 600;
        }

        .form-label {
            font-weight: 500;
        }

        /* Tab Styles */
        .nav-tabs .nav-link {
            font-weight: 600;
            color: #6c757d;
        }

        .nav-tabs .nav-link.active {
            color: var(--bs-primary);
            border-color: #dee2e6 #dee2e6 #fff;
        }

        .color-swatch {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            border: 1px solid #dee2e6;
            display: inline-block;
            vertical-align: middle;
        }
    </style>
</head>

<body>

    <!-- Header -->
    <header><?php include 'admin-header.php'; ?></header>

    <!-- Main Management Page Content -->
    <main class="container-fluid p-4 p-md-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h2 fw-bold">Management</h1>
        </div>

        <div class="page-card card border-0">
            <!-- Tab Navigation -->
            <div class="card-header bg-white border-bottom-0">
                <ul class="nav nav-tabs card-header-tabs" id="managementTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="categories-tab" data-bs-toggle="tab" data-bs-target="#categories-tab-pane" type="button" role="tab">Categories</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="brands-tab" data-bs-toggle="tab" data-bs-target="#brands-tab-pane" type="button" role="tab">Brands</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="colors-tab" data-bs-toggle="tab" data-bs-target="#colors-tab-pane" type="button" role="tab">Colors</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="models-tab" data-bs-toggle="tab" data-bs-target="#models-tab-pane" type="button" role="tab">Models</button>
                    </li>
                </ul>
            </div>

            <!-- Tab Content -->
            <div class="card-body">
                <div class="tab-content" id="managementTabContent">

                    <!-- Categories Pane -->
                    <div class="tab-pane fade show active" id="categories-tab-pane" role="tabpanel">
                        <div class="d-flex justify-content-end mb-3">
                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#categoryModal"><i class="bi bi-plus-circle me-1"></i> Add Category</button>
                        </div>
                        <?php
                        //seach for categories in the database
                        $rs = Database::search("SELECT*From `category`");
                        ?>
                        <table class="table table-hover align-middle col-6 col-md-10">
                            <thead class="table-light">
                                <tr>
                                    <th class="col-11">Category Name</th>
                                    <th class="text-end col-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // 4. Loop through the database results
                                // fetch_assoc() gets one row at a time
                                while ($row = $rs->fetch_assoc()) {
                                ?>
                                    <tr>
                                        <td><?php echo $row['name']; ?></td>
                                        <td><button class="btn btn-sm btn-outline-danger" onclick="deleteCategory(<?php echo $row['id']; ?>, '<?php echo addslashes($row['name']); ?>')">
                                                <i class="bi bi-trash"></i>
                                            </button></td>
                                    </tr>

                            </tbody>
                        <?php
                                }
                        ?>
                        </table>
                    </div>

                    <!-- Brands Pane -->
                    <div class="tab-pane fade" id="brands-tab-pane" role="tabpanel">
                        <div class="d-flex justify-content-end mb-3">
                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#brandModal"><i class="bi bi-plus-circle me-1"></i> Add Brand</button>
                        </div>

                        <?php
                        //search brands in the database
                        $rs = Database::search("SELECT * FROM `brand`");
                        ?>
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Brand Name</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <?php
                            while ($row = $rs->fetch_assoc()) {
                            ?>
                            <tbody>
                                <tr>
                                    <td><?php echo $row['name']; ?></td>
                                    <td class="text-end"><button class="btn btn-sm btn-outline-danger" onclick="deleteBrand(<?php echo $row['id']; ?>, '<?php echo addslashes($row['name']); ?>')"><i class="bi bi-trash"></i></button></td>
                                </tr>

                            </tbody>
                            <?php
                            }
                            ?>
                        </table>
                    </div>

                    <!-- Colors Pane -->

                    <?php 
                     $rs = Database::search("SELECT * FROM `color`");
                    ?>
                    <div class="tab-pane fade" id="colors-tab-pane" role="tabpanel">
                        <div class="d-flex justify-content-end mb-3">
                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#colorModal"><i class="bi bi-plus-circle me-1"></i> Add Color</button>
                        </div>
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Color</th>
                                    <th>Name</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <?php
                            while ($row = $rs->fetch_assoc()) {
                            ?>
                            <tbody>
                                <tr>
                                    <td><span class="color-swatch" style="background-color: <?php echo $row['color_code']; ?>;" id="ccode"></span></td>
                                    <td ><?php echo $row['name']; ?></td>
                                    <td class="text-end"><button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></td>
                                </tr>
                               
                            </tbody>
                            <?php
                            }
                            ?>
                        </table>
                    </div>

                    <!-- Models Pane -->
                    <div class="tab-pane fade" id="models-tab-pane" role="tabpanel">
                        <div class="d-flex justify-content-end mb-3">
                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modelModal"><i class="bi bi-plus-circle me-1"></i> Add Model</button>
                        </div>
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Model Name</th>
                                    <th>Brand</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Mavic 3 Pro</td>
                                    <td>DJI</td>
                                    <td class="text-end"><button class="btn btn-sm btn-outline-secondary me-1"><i class="bi bi-pencil-square"></i></button><button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></td>
                                </tr>
                                <tr>
                                    <td>EVO II</td>
                                    <td>Autel</td>
                                    <td class="text-end"><button class="btn btn-sm btn-outline-secondary me-1"><i class="bi bi-pencil-square"></i></button><button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Modals for each management type -->
    <!-- Category Modal -->
    <div class="modal fade" id="categoryModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Add/Edit Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label">Category Name</label><input type="text" class="form-control" id="category"></div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>

                    <button type="button" class="btn btn-primary" onclick="addCategory();"> Save </button>

                </div>
            </div>
        </div>
    </div>


    <!-- Brand Modal -->
    <div class="modal fade" id="brandModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Add/Edit Brand</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label">Brand Name</label><input type="text" class="form-control" id="bname"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="addBrand();">Save</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Color Modal -->
    <div class="modal fade" id="colorModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Add/Edit Color</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label">Color Name</label><input type="text" class="form-control" id="cname" id="cname"></div>
                    <div class="mb-3"><label class="form-label">Color Code</label><input type="color" class="form-control form-control-color w-100" id="ccode"></div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button><button type="button" class="btn btn-primary" onclick="addColor();">Save</button></div>
            </div>
        </div>
    </div>
    <!-- Model Modal -->
    <div class="modal fade" id="modelModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Add/Edit Model</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label">Model Name</label><input type="text" class="form-control" id=""></div>
                    <div class="mb-3"><label class="form-label">Brand</label><select class="form-select">
                            <option>DJI</option>
                            <option>Autel</option>
                        </select></div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button><button type="button" class="btn btn-primary">Save</button></div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer><?php include 'admin-footer.php'; ?></footer>
    <script src="script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>