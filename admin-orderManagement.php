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
    <title>flydolk - Order Management</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="icon" type="image/x-icon" href="imgs/Flydo.png">
    <link rel="stylesheet" href="style.css">

    <style>
        /* Styles from your other pages */
        body {
            font-family: system-ui, -apple-system, "Inter", Segoe UI, Roboto, Helvetica, Arial, sans-serif;
            background: #f8f9fa;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        main { flex: 1 0 auto; }
        footer { flex-shrink: 0; }

        .page-card {
            background: #fff;
            border: 1px solid #dee2e6;
            border-radius: .75rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .05)
        }
        .table-hover tbody tr:hover { background: #f8f9fa }
        .table th { font-weight: 600 }
        
        /* Modal styles */
        .modal-header { border-bottom: 1px solid #dee2e6 }
        .modal-footer { border-top: 1px solid #dee2e6; background: #f8f9fa }
        .modal-section { border-bottom: 1px dashed #dee2e6; padding-bottom: 1rem; margin-bottom: 1rem; }
        .modal-section:last-child { border-bottom: 0; padding-bottom: 0; margin-bottom: 0; }
        .item-image {
            width: 48px;
            height: 48px;
            object-fit: cover;
            border-radius: .5rem;
        }
        
    </style>
</head>

<body>
    <?php include 'admin-Header.php'; ?>

    <main class="container-fluid p-4 p-md-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h2 fw-bold">Order Management</h1>
        </div>

        <div class="page-card card border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="p-3">Order ID</th>
                                <th class="p-3">Customer</th>
                                <th class="p-3">Date</th>
                                <th class="p-3">Total</th>
                                <th class="p-3">Status</th>
                                <th class="p-3 text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // A map to associate status names with Bootstrap badge colors
                            $status_classes = [
                                'pending' => 'bg-warning text-dark',
                                'processing' => 'bg-info text-dark',
                                'shipped' => 'bg-primary',
                                'delivered' => 'bg-success',
                                'completed' => 'bg-success',
                                'cancelled' => 'bg-danger',
                                'refunded' => 'bg-secondary',
                            ];

                            // [FIX] Changed 'order_status s' back to 'status s'
                            $main_order_sql = "
                                SELECT 
                                    i.id, 
                                    i.created_at, 
                                    i.total_amount, 
                                    s.name AS status_name,
                                    u.name AS customer_name
                                FROM invoice i
                                JOIN `status` s ON i.status_id = s.id
                                JOIN user_has_address uha ON i.user_has_address_id = uha.id
                                JOIN user u ON uha.user_id = u.id
                                ORDER BY i.id DESC
                            ";
                            $rs = Database::search($main_order_sql);

                            // [FIX] Add check for query failure
                            if (!$rs) {
                                echo '<tr><td colspan="6" class="p-3 text-danger">Error loading orders. Query failed.</td></tr>';
                            } else {
                                while ($row = $rs->fetch_assoc()) {
                                    $status_name = strtolower(htmlspecialchars($row['status_name']));
                                    // Get the badge class, or use 'bg-light text-dark' as a default
                                    $badge_class = $status_classes[$status_name] ?? 'bg-light text-dark';
                            ?>
                                <tr>
                                    <td class="p-3 fw-bold">#<?php echo htmlspecialchars($row['id']); ?></td>
                                    <td class="p-3"><?php echo htmlspecialchars($row['customer_name']); ?></td>
                                    <td class="p-3"><?php echo date("M j, Y, g:i A", strtotime($row['created_at'])); ?></td>
                                    <td class="p-3">LKR <?php echo number_format($row['total_amount'], 2); ?></td>
                                    <td class="p-3">
                                        <span class="badge <?php echo $badge_class; ?>" id="status-badge-<?php echo $row['id']; ?>">
                                            <?php echo htmlspecialchars($row['status_name']); ?>
                                        </span>
                                    </td>
                                    <td class="p-3 text-end">
                                        <button type="button" class="btn btn-sm btn-outline-primary" 
                                            onclick="openOrderModal(<?php echo $row['id']; ?>)">
                                            <i class="bi bi-eye-fill"></i> View Details
                                        </button>
                                    </td>
                                </tr>
                            <?php 
                                } // end while
                            } // end else
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <!-- View Order Details Modal -->
    <div class="modal fade" id="viewOrderModal" tabindex="-1" aria-labelledby="viewOrderModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title h2 fw-bold" id="viewOrderModalLabel">Order Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    <!-- This div will show a loading spinner -->
                    <div id="modalLoader" class="text-center p-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>

                    <!-- This content will be hidden until data is loaded -->
                    <div id="modalContent" class="d-none">
                        
                        <div class="row g-4">
                            <!-- Left Column: Items & Totals -->
                            <div class="col-lg-8">
                                <div class="page-card card border-0 mb-4">
                                    <div class="card-body p-4">
                                        <h5 class="mb-3 fw-bold">Order Items</h5>
                                        <div class="table-responsive">
                                            <table class="table align-middle">
                                                <tbody id="modalOrderItemsTable">
                                                    <!-- Items will be injected here by JavaScript -->
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="card-footer bg-white p-4">
                                        <div class="row">
                                            <div class="col-md-6 offset-md-6">
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span class="text-muted">Subtotal:</span>
                                                    <span class="fw-bold" id="modalSubtotal">LKR 0.00</span>
                                                </div>
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span class="text-muted">Delivery Fee:</span>
                                                    <span class="fw-bold" id="modalShipping">LKR 0.00</span>
                                                </div>
                                                <hr>
                                                <div class="d-flex justify-content-between fs-5">
                                                    <span class="fw-bold">Total:</span>
                                                    <span class="fw-bold text-primary" id="modalTotal">LKR 0.00</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Column: Customer & Status -->
                            <div class="col-lg-4">
                                <div class="page-card card border-0 mb-4">
                                    <div class="card-body p-4">
                                        <h5 class="mb-3 fw-bold">Customer</h5>
                                        <p class="mb-1 fw-bold" id="modalCustomerName"></p>
                                        <p class="mb-1 text-muted"><i class="bi bi-envelope me-2"></i><span id="modalCustomerEmail"></span></p>
                                        <p class="mb-0 text-muted"><i class="bi bi-phone me-2"></i><span id="modalCustomerMobile"></span></p>
                                    </div>
                                </div>

                                <div class="page-card card border-0 mb-4">
                                    <div class="card-body p-4">
                                        <h5 class="mb-3 fw-bold">Shipping Address</h5>
                                        <p class="mb-1" id="modalAddress1"></p>
                                        <p class="mb-1" id="modalAddress2"></p>
                                        <p class="mb-1"><span id="modalCity"></span>, <span id="modalProvince"></span></p>
                                        <p class="mb-0"><span id="modalDistrict"></span>, <span id="modalZip"></span></p>
                                    </div>
                                </div>

                                <div class="page-card card border-0">
                                    <div class="card-body p-4">
                                        <h5 class="mb-3 fw-bold">Order Status</h5>
                                        
                                        <input type="hidden" id="modalOrderId" value="">
                                        <input type="hidden" id="modalCurrentStatusId" value="">

                                        <label for="modalStatusSelect" class="form-label">Update Status</label>
                                        <select id="modalStatusSelect" class="form-select">
                                            <!-- Statuses will be injected here by JavaScript -->
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div><!-- /modal-body -->

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="updateStatusBtn" onclick="updateOrderStatus()">
                        Update Status
                    </button>
                </div>
            </div>
        </div>
    </div>

    <footer><?php include 'admin-footer.php'; ?></footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="script.js"></script>

    <script>
        // Store modal instance
        let orderModal = null;
        document.addEventListener('DOMContentLoaded', () => {
            orderModal = new bootstrap.Modal(document.getElementById('viewOrderModal'));
        });

        // Helper to format currency
        function formatPrice(price) {
            return 'LKR ' + parseFloat(price).toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        /**
         * This function is called when you click "View Details".
         * It fetches data from your `admin-getOrderDetails.php` file.
         */
        async function openOrderModal(orderId) {
            // Show the modal
            orderModal.show();

            // Show loader, hide content
            document.getElementById('modalLoader').classList.remove('d-none');
            document.getElementById('modalLoader').innerHTML = '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>'; // Reset loader
            document.getElementById('modalContent').classList.add('d-none');
            document.getElementById('updateStatusBtn').disabled = true;

            const formData = new FormData();
            formData.append('order_id', orderId);

            let data; // Define data outside try block

            try {
                // [FIX] Start of the one and only try block
                const response = await fetch('admin-getOrderDetails.php', {
                    method: 'POST',
                    body: formData
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                // Try to parse the JSON
                data = await response.json();

                // --- We only get here if the JSON was valid ---

                if (!data.ok) {
                    // [FIX] Show the detailed error from the PHP file
                    let errorMsg = data.error || 'Failed to fetch order details.';
                    if (data.debug_query) {
                        errorMsg += `<br><small class="text-muted">Query: ${data.debug_query}</small>`;
                    }
                    
                    orderModal.hide(); // Hide the broken modal
                    Swal.fire({
                        icon: 'error',
                        title: 'Failed to Load Details',
                        html: errorMsg,
                    });
                    return; // Stop execution
                }

                // --- Populate Modal Content (still inside the try block) ---
                const order = data.order;
                const address = data.address;
                const items = data.items;
                const allStatuses = data.all_statuses;

                // 1. Order Details
                document.getElementById('viewOrderModalLabel').textContent = `Order Details #${order.id}`;
                document.getElementById('modalOrderId').value = order.id;

                // 2. Customer
                document.getElementById('modalCustomerName').textContent = order.customer_name;
                document.getElementById('modalCustomerEmail').textContent = order.customer_email;
                document.getElementById('modalCustomerMobile').textContent = order.customer_mobile;

                // 3. Address
                document.getElementById('modalAddress1').textContent = address.line1;
                document.getElementById('modalAddress2').textContent = address.line2 || '';
                document.getElementById('modalCity').textContent = address.city;
                document.getElementById('modalProvince').textContent = address.province;
                document.getElementById('modalDistrict').textContent = address.district;
                document.getElementById('modalZip').textContent = address.zip_code;
                
                // Hide address line 2 if it's empty
                document.getElementById('modalAddress2').style.display = address.line2 ? 'block' : 'none';

                // 4. Totals
                document.getElementById('modalSubtotal').textContent = formatPrice(order.subtotal);
                document.getElementById('modalShipping').textContent = formatPrice(order.delivery_fee);
                document.getElementById('modalTotal').textContent = formatPrice(order.total);

                // 5. Items Table
                const itemsTableBody = document.getElementById('modalOrderItemsTable');
                itemsTableBody.innerHTML = ''; // Clear old items
                items.forEach(item => {
                    const row = `
                        <tr>
                            <td><img src="${item.img || 'https://placehold.co/100x100/EBF5FF/0D6EFD?text=IMG'}" class="item-image" alt="${item.title}"></td>
                            <td>
                                <span class="fw-bold d-block">${item.title}</span>
                                <small class="text-muted">Price: ${formatPrice(item.price)}</small>
                            </td>
                            <td class="text-muted">x ${item.qty}</td>
                            <td class="fw-bold text-end">${formatPrice(item.price * item.qty)}</td>
                        </tr>
                    `;
                    itemsTableBody.insertAdjacentHTML('beforeend', row);
                });

                // 6. Status Select Box
                const statusSelect = document.getElementById('modalStatusSelect');
                statusSelect.innerHTML = ''; // Clear old options
                
                // Get the current status ID from the order data (thanks to our update)
                const currentStatusId = order.status_id;
                document.getElementById('modalCurrentStatusId').value = currentStatusId;
                
                allStatuses.forEach(status => {
                    const option = document.createElement('option');
                    option.value = status.id;
                    option.textContent = status.name;
                    if (status.id == currentStatusId) { // Compare by ID
                        option.selected = true;
                    }
                    statusSelect.appendChild(option);
                });


                // Hide loader, show content
                document.getElementById('modalLoader').classList.add('d-none');
                document.getElementById('modalContent').classList.remove('d-none');
                document.getElementById('updateStatusBtn').disabled = false;


            } catch (error) { // [FIX] This is now the one and only catch block
                // [FIX] If parsing fails (like "Unexpected token <"),
                // or if the fetch fails, show a SweetAlert and close the modal.
                console.error('Error fetching order details:', error);
                orderModal.hide(); // Hide the broken modal
                Swal.fire({
                    icon: 'error',
                    title: 'Failed to Load Details',
                    html: `The server returned an error: <br><pre style="text-align: left; background: #f8f9fa; padding: 10px; border-radius: 5px; margin-top: 10px;">${error.message}</pre>`,
                });
                return; // Stop execution
            }
            
            // [FIX] Removed the stray catch block that was here.
        }

        /**
         * This function is called by the "Update Status" button.
         * It sends data to your `admin-updateOrderStatus.php` file.
         */
        async function updateOrderStatus() {
            const orderId = document.getElementById('modalOrderId').value;
            const newStatusId = document.getElementById('modalStatusSelect').value;
            const newStatusName = document.getElementById('modalStatusSelect').options[document.getElementById('modalStatusSelect').selectedIndex].text;
            
            const btn = document.getElementById('updateStatusBtn');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Updating...';

            const formData = new FormData();
            formData.append('order_id', orderId);
            formData.append('status_id', newStatusId);

            try {
                const response = await fetch('admin-updateOrderStatus.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (!data.ok) {
                    throw new Error(data.error || 'Failed to update status.');
                }

                // Success!
                orderModal.hide();
                Swal.fire({
                    icon: 'success',
                    title: 'Status Updated!',
                    text: `Order #${orderId} has been updated to "${newStatusName}".`,
                    timer: 2000,
                    showConfirmButton: false
                });

                // Update the badge on the main table
                const badge = document.getElementById(`status-badge-${orderId}`);
                if (badge) {
                    badge.textContent = newStatusName;
                    // You can also update the badge color here if needed
                    // This requires a map of status names to badge classes, just like in the PHP
                    const statusClasses = {
                        'pending': 'bg-warning text-dark',
                        'processing': 'bg-info text-dark',
                        'shipped': 'bg-primary',
                        'delivered': 'bg-success',
                        'completed': 'bg-success',
                        'cancelled': 'bg-danger',
                        'refunded': 'bg-secondary',
                    };
                    badge.className = `badge ${statusClasses[newStatusName.toLowerCase()] || 'bg-light text-dark'}`;
                }

            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Update Failed',
                    text: error.message
                });
            } finally {
                btn.disabled = false;
                btn.innerHTML = 'Update Status';
            }
        }
    </script>
</body>
</html>

