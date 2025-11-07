<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin-login.php");
    exit();
}
include 'connection.php';
// Get the ID of the currently logged-in admin (for disabling self-delete)
$current_admin_id = $_SESSION['admin_id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>flydolk - Admin Management</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="imgs/Flydo.png">
    <link rel="stylesheet" href="style.css">
    
    <style>
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
        .table-hover tbody tr:hover { background-color: #f8f9fa; }
        .table th { font-weight: 600; }
        .avatar {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 50%;
            background-color: #e9ecef;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }
        .avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }
    </style>
</head>
<body>
    <header><?php include 'admin-Header.php'; ?></header>

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
                                <th scope="col" class="p-3">Date Joined</th>
                                <th scope="col" class="p-3 text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="adminTableBody">
                            <?php
                            // Use your table names: `admin_panel` and `admin_type`
                            $admin_rs = Database::search("
                                SELECT ap.id, ap.name, ap.email, ap.added_at, at.type AS role_name
                                FROM admin_panel ap
                                LEFT JOIN admin_type at ON ap.admin_type_id = at.id
                                ORDER BY ap.id ASC
                            ");
                            
                            if ($admin_rs && $admin_rs->num_rows > 0) {
                                while ($admin = $admin_rs->fetch_assoc()) {
                                    $name = $admin['name'] ?? explode('@', $admin['email'])[0];
                                    $initials = strtoupper(substr($name, 0, 2));
                                    
                                    $is_protected = ($admin['id'] == $current_admin_id || $admin['id'] == 1);
                                    
                                    $joined_date = $admin['added_at'] ? date("M j, Y, g:i A", strtotime($admin['added_at'])) : 'N/A';
                            ?>
                            <tr id="admin-row-<?php echo $admin['id']; ?>">
                                <td class="p-3">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar me-3">
                                            <span><?php echo htmlspecialchars($initials); ?></span>
                                        </div>
                                        <div>
                                            <div class="fw-bold"><?php echo htmlspecialchars($name); ?></div>
                                            <div class="text-muted small"><?php echo htmlspecialchars($admin['email']); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-3">
                                    <span class="badge <?php echo ($admin['role_name'] == 'Super Admin' ? 'bg-primary' : 'bg-secondary'); ?>">
                                        <?php echo htmlspecialchars($admin['role_name'] ?? 'No Role'); ?>
                                    </span>
                                </td>
                                <td class="p-3"><?php echo $joined_date; ?></td>
                                <td class="p-3 text-end">
                                    <button class="btn btn-sm btn-outline-secondary me-1" 
                                            onclick="openEditModal(<?php echo $admin['id']; ?>)" 
                                            <?php echo ($is_protected ? 'disabled' : ''); ?>>
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" 
                                            onclick="deleteAdmin(<?php echo $admin['id']; ?>, '<?php echo addslashes($admin['email']); ?>')" 
                                            <?php echo ($is_protected ? 'disabled' : ''); ?>>
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php
                                } // end while
                            } else {
                                echo '<tr><td colspan="4" class="p-3 text-center text-muted">No admin users found.</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <?php
    // Fetch roles from `admin_type`
    $role_rs = Database::search("SELECT id, type FROM admin_type ORDER BY type ASC");
    $roles = [];
    if ($role_rs) {
        while ($role = $role_rs->fetch_assoc()) {
            $roles[] = $role;
        }
    }
    ?>

    <!-- Add Admin Modal -->
    <div class="modal fade" id="addAdminModal" tabindex="-1" aria-labelledby="addAdminModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title h5 fw-bold" id="addAdminModalLabel">Add New Admin</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="addAdminName" class="form-label">Name</label>
                        <input type="text" class="form-control" id="addAdminName" placeholder="e.g., John Doe">
                    </div>
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
                            <option value="0" selected>Select a role...</option>
                            <?php foreach ($roles as $role): ?>
                                <option value="<?php echo $role['id']; ?>"><?php echo htmlspecialchars($role['type']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="addAdminSaveBtn" onclick="addAdmin()">Add Admin</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Edit Admin Modal -->
    <div class="modal fade" id="editAdminModal" tabindex="-1" aria-labelledby="editAdminModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title h5 fw-bold" id="editAdminModalLabel">Edit Admin Role</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="editAdminId">
                    <div class="mb-3">
                        <label for="editAdminEmail" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="editAdminEmail" readonly>
                    </div>
                     <div class="mb-3">
                        <label for="editAdminRole" class="form-label">Role</label>
                        <select class="form-select" id="editAdminRole">
                            <?php foreach ($roles as $role): ?>
                                <option value="<?php echo $role['id']; ?>"><?php echo htmlspecialchars($role['type']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="editAdminSaveBtn" onclick="editAdminRole()">Save Changes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer><?php include 'admin-footer.php'; ?></footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Modals
        let addAdminModal = null;
        let editAdminModal = null;

        document.addEventListener('DOMContentLoaded', () => {
            addAdminModal = new bootstrap.Modal(document.getElementById('addAdminModal'));
            editAdminModal = new bootstrap.Modal(document.getElementById('editAdminModal'));
        });

        // --- Add Admin ---
        async function addAdmin() {
            const name = document.getElementById('addAdminName').value;
            const email = document.getElementById('addAdminEmail').value;
            const password = document.getElementById('addAdminPassword').value;
            const roleId = document.getElementById('addAdminRole').value;
            const btn = document.getElementById('addAdminSaveBtn');

            if (!name || !email || !password || roleId == "0") {
                Swal.fire('Missing Data', 'Please fill out all fields.', 'warning');
                return;
            }

            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Adding...';

            const formData = new FormData();
            formData.append('name', name);
            formData.append('email', email);
            formData.append('password', password);
            formData.append('role_id', roleId);

            try {
                // [FIX] Use your file name: admin-add.php
                const response = await fetch('admin-add.php', { method: 'POST', body: formData });
                const data = await response.json();

                if (data.ok) {
                    addAdminModal.hide();
                    Swal.fire('Success!', data.message, 'success').then(() => {
                        location.reload();
                    });
                    document.getElementById('addAdminName').value = '';
                    document.getElementById('addAdminEmail').value = '';
                    document.getElementById('addAdminPassword').value = '';
                    document.getElementById('addAdminRole').value = '0';
                } else {
                    Swal.fire('Error', data.error, 'error');
                }
            } catch (error) {
                Swal.fire('Error', 'An unexpected error occurred. Check if admin-add.php exists.', 'error');
                console.error('Fetch Error:', error);
            } finally {
                btn.disabled = false;
                btn.innerHTML = 'Add Admin';
            }
        }

        // --- Open Edit Modal ---
        async function openEditModal(adminId) {
            const emailEl = document.getElementById('editAdminEmail');
            const roleEl = document.getElementById('editAdminRole');
            const idEl = document.getElementById('editAdminId');
            
            emailEl.value = 'Loading...';
            roleEl.disabled = true;
            idEl.value = adminId;

            editAdminModal.show();

            try {
                const formData = new FormData();
                formData.append('id', adminId);
                // [FIX] Use your file name: admin-getdetails.php
                const response = await fetch('admin-getdetails.php', { method: 'POST', body: formData });
                const data = await response.json();

                if (data.ok) {
                    emailEl.value = data.admin.email;
                    roleEl.value = data.admin.admin_type_id; 
                    roleEl.disabled = false;
                } else {
                    Swal.fire('Error', data.error, 'error');
                    editAdminModal.hide();
                }
            } catch (error) {
                Swal.fire('Error', 'Could not fetch admin details.', 'error');
                editAdminModal.hide();
            }
        }

        // --- Edit Admin Role ---
        async function editAdminRole() {
            const id = document.getElementById('editAdminId').value;
            const roleId = document.getElementById('editAdminRole').value;
            const btn = document.getElementById('editAdminSaveBtn');

            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...';

            const formData = new FormData();
            formData.append('id', id);
            formData.append('role_id', roleId);

            try {
                // [FIX] Use your file name: admin-editrole.php
                const response = await fetch('admin-editrole.php', { method: 'POST', body: formData });
                const data = await response.json();

                if (data.ok) {
                    editAdminModal.hide();
                    Swal.fire('Success!', data.message, 'success').then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Error', data.error, 'error');
                }
            } catch (error) {
                Swal.fire('Error', 'An unexpected error occurred.', 'error');
            } finally {
                btn.disabled = false;
                btn.innerHTML = 'Save Changes';
            }
        }

        // --- Delete Admin ---
        function deleteAdmin(adminId, adminEmail) {
            Swal.fire({
                title: 'Are you sure?',
                html: `You are about to permanently delete <strong>${adminEmail}</strong>. This action cannot be undone.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!'
            }).then(async (result) => {
                if (result.isConfirmed) {
                    const formData = new FormData();
                    formData.append('id', adminId);

                    try {
                        // [FIX] Use your file name: admin-delete.php
                        const response = await fetch('admin-delete.php', { method: 'POST', body: formData });
                        const data = await response.json();

                        if (data.ok) {
                            Swal.fire('Deleted!', data.message, 'success');
                            document.getElementById(`admin-row-${adminId}`).remove();
                        } else {
                            Swal.fire('Error', data.error, 'error');
                        }
                    } catch (error) {
                        Swal.fire('Error', 'An unexpected error occurred.', 'error');
                    }
                }
            });
        }
    </script>
</body>
</html>