<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin-login.php");
    exit();
}
include 'connection.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>flydolk - Products</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="icon" type="image/x-icon" href="imgs/Flydo.png">
    <link rel="stylesheet" href="style.css">

    <style>
        /* ---- Page base ---- */
        * {
            -ms-overflow-style: none;
            scrollbar-width: none
        }

        *::-webkit-scrollbar {
            display: none
        }

        body {
            overflow-y: scroll;
            font-family: system-ui, -apple-system, "Inter", Segoe UI, Roboto, Helvetica, Arial, sans-serif;
            background: #f8f9fa;
        }

        .page-card {
            background: #fff;
            border: 1px solid #dee2e6;
            border-radius: .75rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .05)
        }

        .table-hover tbody tr:hover {
            background: #f8f9fa
        }

        .table th {
            font-weight: 600
        }

        .product-image {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: .5rem
        }

        .modal-header {
            border-bottom: 1px solid #dee2e6
        }

        .modal-footer {
            border-top: 1px solid #dee2e6;
            background: #f8f9fa
        }

        .form-label {
            font-weight: 500
        }

        /* ---- Dropzone ---- */
        .image-dropzone {
            border: 2px dashed #dee2e6;
            border-radius: .75rem;
            padding: 2rem;
            text-align: center;
            cursor: pointer;
            transition: border-color .2s, background-color .2s
        }

        .image-dropzone:hover {
            border-color: #0d6efd;
            background: #f8f9fa
        }

        .image-dropzone i {
            font-size: 2.5rem;
            color: #adb5bd
        }

        .image-dropzone p {
            margin-top: 1rem;
            color: #6c757d
        }

        .image-dropzone.dz-hover {
            border-color: #0d6efd;
            background: #f0f7ff
        }

        #imgPreview {
            display: flex;
            flex-wrap: wrap;
            gap: .75rem
        }

        .preview-item {
            width: 96px;
            height: 96px;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid rgba(0, 0, 0, .08);
            box-shadow: 0 2px 8px rgba(0, 0, 0, .06);
            position: relative
        }

        .preview-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block
        }

        .preview-remove {
            position: absolute;
            top: 4px;
            right: 6px;
            border: 0;
            background: rgba(0, 0, 0, .5);
            color: #fff;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            line-height: 20px;
            cursor: pointer;
            font-size: 14px
        }

        /* ---- Color swatches ---- */
        .color-swatch-input {
            position: absolute;
            opacity: 0;
            pointer-events: none
        }

        .color-swatch {
            width: 42px;
            height: 42px;
            border-radius: 999px;
            border: 1px solid rgba(0, 0, 0, .08);
            box-shadow: inset 0 0 0 1px rgba(0, 0, 0, .05), 0 2px 8px rgba(0, 0, 0, .06);
            display: inline-block;
            cursor: pointer;
            transition: transform .15s, box-shadow .15s, outline-color .15s;
            position: relative;
            overflow: hidden
        }

        .color-swatch-input:checked+.color-swatch {
            outline: 3px solid #0db1fd;
            outline-offset: 2px;
            transform: scale(1.06);
            box-shadow: 0 6px 14px rgba(13, 177, 253, .25)
        }

        .color-chip {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            padding: .35rem .6rem;
            border-radius: 999px;
            background: #f8f9fa;
            border: 1px solid rgba(0, 0, 0, .06);
            font-size: .9rem
        }

        .color-chip .dot {
            width: 16px;
            height: 16px;
            border-radius: 999px;
            border: 1px solid rgba(0, 0, 0, .08)
        }

        html,
        body {
            height: 100%
        }

        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column
        }

        main {
            flex: 1 0 auto
        }

        footer {
            flex-shrink: 0
        }

        /* ===== Mini Rich Text Editor ===== */
        .mini-editor {
            border: 1px solid #dee2e6;
            border-radius: .5rem;
            background: #fff
        }

        .mini-toolbar {
            display: flex;
            gap: .25rem;
            padding: .4rem;
            border-bottom: 1px solid #dee2e6;
            background: #f8f9fa;
            flex-wrap: wrap;
        }

        .mini-btn {
            border: 1px solid transparent;
            background: #fff;
            padding: .25rem .5rem;
            border-radius: .35rem;
            cursor: pointer
        }

        .mini-btn:hover {
            border-color: #ced4da;
            background: #fff
        }

        .mini-area {
            min-height: 180px;
            padding: .75rem;
            outline: 0;
            line-height: 1.5
        }

        .mini-area.is-empty:before {
            content: attr(data-placeholder);
            color: #adb5bd;
            pointer-events: none;
        }

        .mini-area:focus {
            box-shadow: inset 0 0 0 1px #0d6efd33
        }
    </style>
</head>

<body>
    <?php include 'admin-Header.php'; ?>

    <main class="container-fluid p-4 p-md-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h2 fw-bold">Products</h1>
            <button class="btn btn-primary d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#addProductModal">
                <i class="bi bi-plus-circle me-2"></i> Add New Product
            </button>
        </div>

        <div class="page-card card border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="p-3">Product Name</th>
                                <th class="p-3">Category</th>
                                <th class="p-3">Brand</th>
                                <th class="p-3">Price</th>
                                <th class="p-3">Stock</th>
                                <th class="p-3 text-end">Actions</th>
                            </tr>
                        </thead>
                        <?php
                        $rs = Database::search("
              SELECT p.id, p.title, p.price, p.qty,
                     c.name AS category, b.name AS brand,
                     (SELECT img_url FROM product_img WHERE product_id=p.id ORDER BY img_url ASC LIMIT 1) AS img
              FROM product p
              JOIN category c ON c.id = p.category_id
              JOIN brand    b ON b.id = p.brand_id
              ORDER BY p.id DESC
            ");
                        ?>
                        <tbody id="productTableBody">
                            <?php while ($r = $rs->fetch_assoc()) { ?>
                                <tr data-id="<?= (int)$r['id'] ?>">
                                    <td class="p-3">
                                        <div class="d-flex align-items-center">
                                            <img src="<?= htmlspecialchars($r['img'] ?: 'assets/img/placeholder.png', ENT_QUOTES) ?>" class="product-image me-3" alt="">
                                            <span class="fw-bold"><?= htmlspecialchars($r['title'], ENT_QUOTES) ?></span>
                                        </div>
                                    </td>
                                    <td class="p-3"><?= htmlspecialchars($r['category']) ?></td>
                                    <td class="p-3"><?= htmlspecialchars($r['brand']) ?></td>
                                    <td class="p-3">LKR <?= number_format((float)$r['price'], 2) ?></td>
                                    <td class="p-3">
                                        <?= (int)$r['qty'] ?>
                                        <span class="badge <?= ($r['qty'] > 0 ? 'bg-success' : 'bg-danger') ?>">
                                            <?= ($r['qty'] > 0 ? 'In Stock' : 'Out of Stock') ?>
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="openEditProduct(<?= (int)$r['id'] ?>)">
                                            <i class="bi bi-pencil-square"></i> Edit
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger ms-2"
                                            onclick='deleteProduct(<?= (int)$r["id"] ?>, <?= json_encode($r["title"]) ?>)'>
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
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
                    <div class="row g-4">
                        <!-- Left -->
                        <div class="col-lg-8">
                            <div class="page-card card border-0 mb-4">
                                <div class="card-body p-4">
                                    <h5 class="mb-3 fw-bold">Product Information</h5>
                                    <div class="mb-3">
                                        <label class="form-label" for="pName">Product Name</label>
                                        <input type="text" id="pName" class="form-control" placeholder="e.g., DJI Mavic 3 Pro">
                                    </div>

                                    <!-- Custom mini editor (visible) + hidden textarea (pDesc) -->
                                    <div class="mb-3">
                                        <label class="form-label" for="pDescEditor">Description</label>
                                        <div class="mini-editor" data-for="pDesc">
                                            <div class="mini-toolbar">
                                                <button type="button" class="mini-btn" data-cmd="bold"><i class="bi bi-type-bold"></i></button>
                                                <button type="button" class="mini-btn" data-cmd="italic"><i class="bi bi-type-italic"></i></button>
                                                <button type="button" class="mini-btn" data-cmd="underline"><i class="bi bi-type-underline"></i></button>
                                                <span class="mx-1">|</span>
                                                <button type="button" class="mini-btn" data-cmd="insertUnorderedList"><i class="bi bi-list-ul"></i></button>
                                                <button type="button" class="mini-btn" data-cmd="insertOrderedList"><i class="bi bi-list-ol"></i></button>
                                                <span class="mx-1">|</span>
                                                <button type="button" class="mini-btn" data-cmd="h1">H1</button>
                                                <button type="button" class="mini-btn" data-cmd="h2">H2</button>
                                                <button type="button" class="mini-btn" data-cmd="paragraph">P</button>
                                                <span class="mx-1">|</span>
                                                <button type="button" class="mini-btn" data-cmd="link"><i class="bi bi-link-45deg"></i></button>
                                                <button type="button" class="mini-btn" data-cmd="removeFormat"><i class="bi bi-eraser"></i></button>
                                            </div>
                                            <div id="pDescEditor" class="mini-area" contenteditable="true" data-placeholder="Provide a detailed description..."></div>
                                            <textarea id="pDesc" name="pDesc" class="d-none"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="page-card card border-0">
                                <div class="card-body p-4">
                                    <h5 class="mb-3 fw-bold">Media</h5>
                                    <div class="image-dropzone">
                                        <input type="file" id="imgUpload" class="d-none" name="images[]" multiple accept="image/*">
                                        <label for="imgUpload" class="w-100 m-0">
                                            <i class="bi bi-cloud-arrow-up"></i>
                                            <p class="mb-0"><b>Click to upload</b> or drag and drop.</p>
                                            <small class="text-muted">PNG, JPG, GIF, WEBP up to 10MB each</small>
                                        </label>
                                    </div>
                                    <div id="imgPreview" class="mt-3"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Right -->
                        <div class="col-lg-4">
                            <div class="page-card card border-0 mb-4">
                                <div class="card-body p-4">
                                    <h5 class="mb-3 fw-bold">Pricing</h5>
                                    <label class="form-label" for="pPrice">Price</label>
                                    <div class="input-group">
                                        <span class="input-group-text">LKR</span>
                                        <input type="text" id="pPrice" class="form-control" placeholder="0.00">
                                    </div>
                                </div>
                            </div>

                            <?php
                            $rsCat = Database::search("SELECT * FROM `category`");
                            $rsBrand = Database::search("SELECT * FROM `brand`");
                            $rsColor = Database::search("SELECT * FROM `color`");
                            ?>
                            <div class="page-card card border-0 mb-4">
                                <div class="card-body p-4">
                                    <h5 class="mb-3 fw-bold">Organization</h5>

                                    <div class="mb-3">
                                        <label class="form-label" for="pCategory">Category</label>
                                        <select id="pCategory" class="form-select">
                                            <option selected>Select...</option>
                                            <?php while ($row = $rsCat->fetch_assoc()) { ?>
                                                <option value="<?= $row['id']; ?>"><?= htmlspecialchars($row['name']); ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label" for="pBrand">Brand</label>
                                        <select id="pBrand" class="form-select">
                                            <option selected>Select...</option>
                                            <?php while ($row = $rsBrand->fetch_assoc()) { ?>
                                                <option value="<?= $row['id']; ?>"><?= htmlspecialchars($row['name']); ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label d-block">Colors</label>
                                        <div id="selectedColors" class="d-flex flex-wrap gap-2 mb-2"></div>
                                        <div class="d-flex flex-wrap gap-3">
                                            <?php while ($row = $rsColor->fetch_assoc()) {
                                                $id   = (int)$row['id'];
                                                $name = htmlspecialchars($row['name']);
                                            ?>
                                                <div class="position-relative">
                                                    <input type="checkbox" class="color-swatch-input" id="color-<?= $id ?>" name="pColor[]" value="<?= $id ?>" data-name="<?= $name ?>" data-color="">
                                                    <label for="color-<?= $id ?>" class="color-swatch" title="<?= $name ?>" data-fallback="<?= $name ?>"></label>
                                                </div>
                                            <?php } ?>
                                        </div>
                                        <small class="text-muted d-block mt-2">Click circles to select multiple colors.</small>
                                    </div>
                                </div>
                            </div>

                            <div class="page-card card border-0">
                                <div class="card-body p-4">
                                    <h5 class="mb-3 fw-bold">Stock & Status</h5>
                                    <div class="mb-3">
                                        <label class="form-label" for="pStock">Stock Quantity</label>
                                        <input type="number" id="pStock" class="form-control" placeholder="0">
                                    </div>
                                    <?php $rsStatus = Database::search("SELECT * FROM `product_status`"); ?>
                                    <div>
                                        <label class="form-label" for="pStatus">Status</label>
                                        <select id="pStatus" class="form-select">
                                            <option value="0">Select Status</option>
                                            <?php while ($row = $rsStatus->fetch_assoc()) { ?>
                                                <option value="<?= $row['id']; ?>"><?= htmlspecialchars($row['name']); ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                        </div><!-- /Right -->
                    </div><!-- /row -->
                </div><!-- /modal-body -->

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="addProduct()">Save Product</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Product Modal -->
    <div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title h2 fw-bold" id="editProductModalLabel">Edit Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    <input type="hidden" id="epId">

                    <div class="row g-4">
                        <!-- Left -->
                        <div class="col-lg-8">
                            <div class="page-card card border-0 mb-4">
                                <div class="card-body p-4">
                                    <h5 class="mb-3 fw-bold">Product Information</h5>

                                    <div class="mb-3">
                                        <label class="form-label" for="epName">Product Name</label>
                                        <input type="text" id="epName" class="form-control" placeholder="Product name">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label" for="epDescEditor">Description</label>
                                        <div class="mini-editor" data-for="epDesc">
                                            <div class="mini-toolbar">
                                                <button type="button" class="mini-btn" data-cmd="bold"><i class="bi bi-type-bold"></i></button>
                                                <button type="button" class="mini-btn" data-cmd="italic"><i class="bi bi-type-italic"></i></button>
                                                <button type="button" class="mini-btn" data-cmd="underline"><i class="bi bi-type-underline"></i></button>
                                                <span class="mx-1">|</span>
                                                <button type="button" class="mini-btn" data-cmd="insertUnorderedList"><i class="bi bi-list-ul"></i></button>
                                                <button type="button" class="mini-btn" data-cmd="insertOrderedList"><i class="bi bi-list-ol"></i></button>
                                                <span class="mx-1">|</span>
                                                <button type="button" class="mini-btn" data-cmd="h1">H1</button>
                                                <button type="button" class="mini-btn" data-cmd="h2">H2</button>
                                                <button type="button" class="mini-btn" data-cmd="paragraph">P</button>
                                                <span class="mx-1">|</span>
                                                <button type="button" class="mini-btn" data-cmd="link"><i class="bi bi-link-45deg"></i></button>
                                                <button type="button" class="mini-btn" data-cmd="removeFormat"><i class="bi bi-eraser"></i></button>
                                            </div>
                                            <div id="epDescEditor" class="mini-area" contenteditable="true" data-placeholder="Description..."></div>
                                            <textarea id="epDesc" name="epDesc" class="d-none"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right -->
                        <div class="col-lg-4">
                            <div class="page-card card border-0 mb-4">
                                <div class="card-body p-4">
                                    <h5 class="mb-3 fw-bold">Pricing</h5>
                                    <label class="form-label" for="epPrice">Price</label>
                                    <div class="input-group">
                                        <span class="input-group-text">LKR</span>
                                        <input type="text" id="epPrice" class="form-control" placeholder="0.00">
                                    </div>
                                </div>
                            </div>

                            <?php
                            $epCat   = Database::search("SELECT * FROM `category`");
                            $epBrand = Database::search("SELECT * FROM `brand`");
                            $epColor = Database::search("SELECT * FROM `color`");
                            $epStat  = Database::search("SELECT * FROM `product_status`");
                            ?>
                            <div class="page-card card border-0 mb-4">
                                <div class="card-body p-4">
                                    <h5 class="mb-3 fw-bold">Organization</h5>

                                    <div class="mb-3">
                                        <label class="form-label" for="epCategory">Category</label>
                                        <select id="epCategory" class="form-select">
                                            <option value="0">Select...</option>
                                            <?php while ($row = $epCat->fetch_assoc()) { ?>
                                                <option value="<?= $row['id']; ?>"><?= htmlspecialchars($row['name']); ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label" for="epBrand">Brand</label>
                                        <select id="epBrand" class="form-select">
                                            <option value="0">Select...</option>
                                            <?php while ($row = $epBrand->fetch_assoc()) { ?>
                                                <option value="<?= $row['id']; ?>"><?= htmlspecialchars($row['name']); ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label d-block">Colors</label>
                                        <div id="epSelectedColors" class="d-flex flex-wrap gap-2 mb-2"></div>
                                        <div id="epColorGrid" class="d-flex flex-wrap gap-3">
                                            <?php while ($row = $epColor->fetch_assoc()) {
                                                $id = (int)$row['id'];
                                                $name = htmlspecialchars($row['name']); ?>
                                                <div class="position-relative">
                                                    <input type="checkbox" class="color-swatch-input" id="ep-color-<?= $id ?>" value="<?= $id ?>" data-name="<?= $name ?>">
                                                    <label for="ep-color-<?= $id ?>" class="color-swatch" title="<?= $name ?>"></label>
                                                </div>
                                            <?php } ?>
                                        </div>
                                        <small class="text-muted d-block mt-2">Click to select multiple colors.</small>
                                    </div>
                                </div>
                            </div>

                            <div class="page-card card border-0">
                                <div class="card-body p-4">
                                    <h5 class="mb-3 fw-bold">Stock & Status</h5>
                                    <div class="mb-3">
                                        <label class="form-label" for="epStock">Stock Quantity</label>
                                        <input type="number" id="epStock" class="form-control" placeholder="0">
                                    </div>
                                    <div>
                                        <label class="form-label" for="epStatus">Status</label>
                                        <select id="epStatus" class="form-select">
                                            <option value="0">Select Status</option>
                                            <?php while ($row = $epStat->fetch_assoc()) { ?>
                                                <option value="<?= $row['id']; ?>"><?= htmlspecialchars($row['name']); ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                        </div><!-- /Right -->
                    </div><!-- /row -->
                </div><!-- /modal-body -->

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="updateProductSimple()">Save changes</button>
                </div>
            </div>
        </div>
    </div>

    <footer><?php include 'admin-footer.php'; ?></footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="script.js"></script>

    <!-- Mini editor engine (shared for both modals) -->
    <script>
        function initSimpleEditor(editorId, hiddenId) {
            const area = document.getElementById(editorId);
            if (!area) return;
            const container = area.closest('.mini-editor');
            const toolbar = container.querySelector('.mini-toolbar');

            function exec(cmd, val = null) {
                if (cmd === 'h1' || cmd === 'h2' || cmd === 'paragraph') {
                    const tag = cmd === 'h1' ? 'H1' : cmd === 'h2' ? 'H2' : 'P';
                    document.execCommand('formatBlock', false, tag);
                } else if (cmd === 'link') {
                    const url = prompt('Enter URL (https://...)');
                    if (url && /^https?:\/\//i.test(url)) document.execCommand('createLink', false, url);
                } else {
                    document.execCommand(cmd, false, val);
                }
                area.focus();
                placeholderCheck();
            }

            toolbar.addEventListener('click', e => {
                const btn = e.target.closest('[data-cmd]');
                if (!btn) return;
                exec(btn.dataset.cmd);
            });

            area.addEventListener('paste', e => {
                e.preventDefault();
                const text = (e.clipboardData || window.clipboardData).getData('text');
                document.execCommand('insertText', false, text);
            });

            function placeholderCheck() {
                const html = area.innerHTML.replace(/<br>/gi, '').trim();
                area.classList.toggle('is-empty', html === '');
            }
            area.addEventListener('input', placeholderCheck);
            placeholderCheck();

            function cleanHTML(html) {
                const allowed = new Set(['B', 'STRONG', 'I', 'EM', 'U', 'BR', 'P', 'UL', 'OL', 'LI', 'A', 'H1', 'H2', 'H3']);
                const root = document.createElement('div');
                root.innerHTML = html;
                (function walk(node) {
                    [...node.childNodes].forEach(n => {
                        if (n.nodeType === 1) {
                            if (!allowed.has(n.tagName)) {
                                n.replaceWith(...n.childNodes);
                                return;
                            }
                            [...n.attributes].forEach(attr => {
                                const an = attr.name.toLowerCase();
                                if (n.tagName === 'A' && (an === 'href' || an === 'target' || an === 'rel')) return;
                                n.removeAttribute(attr.name);
                            });
                            if (n.tagName === 'A') {
                                let href = n.getAttribute('href') || '';
                                if (!/^https?:\/\//i.test(href)) n.removeAttribute('href');
                                n.setAttribute('rel', 'noopener noreferrer');
                                n.setAttribute('target', '_blank');
                            }
                            walk(n);
                        } else if (n.nodeType === 8) {
                            n.remove();
                        }
                    });
                })(root);
                return root.innerHTML;
            }

            area._syncToHidden = () => {
                const html = cleanHTML(area.innerHTML);
                document.getElementById(hiddenId).value = html;
            };
        }

        // Init editors when modals open (so DOM is present)
        document.addEventListener('DOMContentLoaded', () => {
            const addModal = document.getElementById('addProductModal');
            const editModal = document.getElementById('editProductModal');

            if (addModal) {
                addModal.addEventListener('shown.bs.modal', () => {
                    initSimpleEditor('pDescEditor', 'pDesc');
                }, {
                    once: true
                });
            }
            if (editModal) {
                editModal.addEventListener('shown.bs.modal', () => {
                    initSimpleEditor('epDescEditor', 'epDesc');
                }, {
                    once: true
                });
            }
        });
    </script>

    <!-- Color swatches + image preview from your previous page -->
    <script>
        /* ---------- Color swatches (no hex in DB; generate from name) ---------- */
        document.addEventListener("DOMContentLoaded", function() {
            const selectedWrap = document.getElementById("selectedColors");
            const inputs = document.querySelectorAll(".color-swatch-input");
            const NAME_MAP = {
                red: "#FF3B30",
                black: "#111111",
                white: "#FFFFFF",
                blue: "#007AFF",
                green: "#34C759",
                yellow: "#FFD60A",
                orange: "#FF9500",
                purple: "#AF52DE",
                pink: "#FF2D55",
                gray: "#8E8E93",
                grey: "#8E8E93",
                brown: "#A2845E",
                darkGray: "#4A4A4A",
                brightyellow: "#FFD60A"
            };

            function nameToHSL(name) {
                let h = 0;
                for (let i = 0; i < name.length; i++) h = (h * 31 + name.charCodeAt(i)) >>> 0;
                const hue = h % 360,
                    sat = 55 + (h % 30),
                    light = 45 + (h % 10);
                return `hsl(${hue} ${sat}% ${light}%)`;
            }

            function needsWhiteTick(rgb) {
                const m = rgb.match(/rgb\((\d+),\s*(\d+),\s*(\d+)\)/);
                if (!m) return false;
                const lin = m.slice(1).map(n => {
                    n = +n / 255;
                    return n <= 0.03928 ? n / 12.92 : Math.pow((n + 0.055) / 1.055, 2.4);
                });
                const L = 0.2126 * lin[0] + 0.7152 * lin[1] + 0.0722 * lin[2];
                return L < 0.5;
            }

            function paintSwatches() {
                inputs.forEach(inp => {
                    const label = document.querySelector(`label[for="${inp.id}"]`);
                    if (!label) return;
                    const rawName = (inp.dataset.name || "").trim();
                    const key = rawName.toLowerCase();
                    let cssColor = NAME_MAP[key] || (inp.dataset.color || "").trim();
                    if (!cssColor) cssColor = nameToHSL(rawName);
                    label.style.background = cssColor;
                    label.dataset.empty = "";
                    const probe = document.createElement("div");
                    probe.style.display = "none";
                    probe.style.background = cssColor;
                    document.body.appendChild(probe);
                    const rgb = getComputedStyle(probe).backgroundColor;
                    document.body.removeChild(probe);
                    label.style.color = needsWhiteTick(rgb) ? "#FFFFFF" : "#111111";
                });
            }

            function renderChips() {
                if (!selectedWrap) return;
                selectedWrap.innerHTML = "";
                inputs.forEach(inp => {
                    if (inp.checked) {
                        const name = inp.dataset.name || "Color";
                        const chip = document.createElement("span");
                        chip.className = "color-chip";
                        const dot = document.createElement("span");
                        dot.className = "dot";
                        const label = document.querySelector(`label[for="${inp.id}"]`);
                        dot.style.background = label ? label.style.background : "#e9ecef";
                        const txt = document.createElement("span");
                        txt.textContent = name;
                        const x = document.createElement("button");
                        x.type = "button";
                        x.textContent = "×";
                        x.style.border = "none";
                        x.style.background = "transparent";
                        x.style.fontSize = "16px";
                        x.style.lineHeight = "1";
                        x.style.cursor = "pointer";
                        x.addEventListener("click", () => {
                            inp.checked = false;
                            inp.dispatchEvent(new Event("change", {
                                bubbles: true
                            }));
                        });
                        chip.append(dot, txt, x);
                        selectedWrap.appendChild(chip);
                    }
                });
            }
            paintSwatches();
            renderChips();
            document.addEventListener("change", e => {
                if (e.target.classList.contains("color-swatch-input")) renderChips();
            });
        });

        /* ---------- Drag & Drop + Preview for images ---------- */
        document.addEventListener("DOMContentLoaded", function() {
            const dropzone = document.querySelector(".image-dropzone");
            const fileInput = document.getElementById("imgUpload");
            const previewWrap = document.getElementById("imgPreview");
            if (!dropzone || !fileInput || !previewWrap) return;

            const MAX_FILES = 10,
                MAX_SIZE_MB = 10;
            const allowed = ["image/png", "image/jpeg", "image/gif", "image/webp"];
            let fileList = [];
            const bytesToMB = b => b / (1024 * 1024);

            function renderPreviews() {
                previewWrap.innerHTML = "";
                fileList.forEach((file, idx) => {
                    const item = document.createElement("div");
                    item.className = "preview-item";
                    const img = document.createElement("img");
                    item.appendChild(img);
                    const btn = document.createElement("button");
                    btn.type = "button";
                    btn.className = "preview-remove";
                    btn.textContent = "×";
                    btn.addEventListener("click", () => {
                        fileList.splice(idx, 1);
                        syncInput();
                        renderPreviews();
                    });
                    item.appendChild(btn);
                    const reader = new FileReader();
                    reader.onload = e => {
                        img.src = e.target.result;
                    };
                    reader.readAsDataURL(file);
                    previewWrap.appendChild(item);
                });
            }

            function syncInput() {
                const dt = new DataTransfer();
                fileList.forEach(f => dt.items.add(f));
                fileInput.files = dt.files;
            }

            function validateAndAdd(files) {
                for (const f of files) {
                    if (!allowed.includes(f.type)) {
                        Swal.fire({
                            icon: "error",
                            title: "Invalid file",
                            text: `${f.name} not supported.`
                        });
                        continue;
                    }
                    if (bytesToMB(f.size) > MAX_SIZE_MB) {
                        Swal.fire({
                            icon: "error",
                            title: "Too large",
                            text: `${f.name} exceeds ${MAX_SIZE_MB} MB.`
                        });
                        continue;
                    }
                    if (fileList.length >= MAX_FILES) {
                        Swal.fire({
                            icon: "warning",
                            title: "Limit reached",
                            text: `Max ${MAX_FILES} images.`
                        });
                        break;
                    }
                    fileList.push(f);
                }
                syncInput();
                renderPreviews();
            }
            fileInput.addEventListener("change", () => {
                validateAndAdd(fileInput.files);
                fileInput.value = "";
            });
            ["dragenter", "dragover"].forEach(evt => dropzone.addEventListener(evt, e => {
                e.preventDefault();
                e.stopPropagation();
                dropzone.classList.add("dz-hover");
            }));
            ["dragleave", "drop"].forEach(evt => dropzone.addEventListener(evt, e => {
                e.preventDefault();
                e.stopPropagation();
                dropzone.classList.remove("dz-hover");
            }));
            dropzone.addEventListener("drop", e => {
                const files = e.dataTransfer.files;
                if (files && files.length) validateAndAdd(files);
            });
            dropzone.addEventListener("click", () => fileInput.click());
        });
    </script>
</body>

</html>