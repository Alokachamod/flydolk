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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>flydolk - User Management</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="imgs/Flydo.png">
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #f8f9fa
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

        .avatar {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 50%
        }

        .badge.bg-active {
            background: rgba(25, 135, 84, .1);
            color: #198754
        }

        .badge.bg-banned {
            background: rgba(220, 53, 69, .1);
            color: #dc3545
        }

        .swal2-html-container .list-group-item {
            border: none;
            border-bottom: 1px dashed #e9ecef;
            border-radius: 0;
            padding-left: 0;
            padding-right: 0
        }

        .swal2-html-container .list-group-item:last-child {
            border-bottom: none
        }

        html,
        body {
            height: 100%;
            margin: 0;
            display: flex;
            flex-direction: column;
        }
    </style>
</head>

<body>

    <header><?php include 'admin-header.php'; ?></header>

    <main class="container-fluid p-4 p-md-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h2 fw-bold">User Management</h1>
            
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
                            <option value="active">Active</option>
                            <option value="banned">Banned</option>
                            <option value="deactivated">Deactivated</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="p-3">User</th>
                                <th class="p-3">Email</th>
                                <th class="p-3">Total Orders</th>
                                <th class="p-3">Date Registered</th>
                                <th class="p-3">Status</th>
                                <th class="p-3 text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            function e($s)
                            {
                                return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
                            }
                            function initials_from_name($full)
                            {
                                $parts = preg_split('/\s+/', trim((string)$full));
                                $i1 = isset($parts[0][0]) ? strtoupper($parts[0][0]) : '';
                                $i2 = isset($parts[1][0]) ? strtoupper($parts[1][0]) : '';
                                $out = $i1 . $i2;
                                return $out !== '' ? $out : 'US';
                            }

                            // Load users list
                            $sql = "
  SELECT
      u.id,
      u.name AS full_name,
      u.email,
      u.joined_date,
      u.user_status_id,
      us.name AS status_name,
      (
        SELECT COUNT(*)
        FROM invoice i
        JOIN user_has_address uha ON uha.id = i.user_has_address_id
        WHERE uha.user_id = u.id
      ) AS total_orders
  FROM user u
  LEFT JOIN user_status us ON us.id = u.user_status_id
  ORDER BY u.id DESC
";
                            if (class_exists('Database') && method_exists('Database', 'search')) {
                                $rs = Database::search($sql);
                            } elseif (isset($conn) && $conn instanceof mysqli) {
                                $rs = $conn->query($sql);
                            } else {
                                $rs = false;
                            }

                            if ($rs === false) {
                                echo '<tr><td colspan="6" class="p-3 text-danger">Failed to load users.</td></tr>';
                            } else {
                                while ($row = $rs->fetch_assoc()) {
                                    $uid    = (int)$row['id'];
                                    $name   = $row['full_name'] ?: ('User #' . $uid);
                                    $email  = $row['email'] ?: '-';
                                    $orders = (int)$row['total_orders'];
                                    $joined = $row['joined_date'] ? date('F j, Y', strtotime($row['joined_date'])) : '-';

                                    $statusName = strtolower(trim((string)($row['status_name'] ?? '')));
                                    $bannedLike = ['banned', 'blocked', 'suspended', 'blacklisted', 'deactivated', 'inactive'];
                                    $isBanned   = in_array($statusName, $bannedLike, true);
                                    $isActive   = ($statusName === 'active');

                                    $statusClass = $isActive ? 'bg-active' : 'bg-banned';
                                    $statusLabel = $isActive ? 'Active' : (($statusName === 'deactivated' || $statusName === 'inactive') ? 'Deactivated' : 'Banned');

                                    $avatar = "https://placehold.co/100x100/EBF5FF/0D6EFD?text=" . urlencode(initials_from_name($name));
                            ?>
                                    <tr id="row-<?php echo $uid; ?>">
                                        <td class="p-3">
                                            <div class="d-flex align-items-center">
                                                <img src="<?php echo $avatar; ?>" class="avatar me-3" alt="<?php echo e($name); ?>">
                                                <span class="fw-bold"><?php echo e($name); ?></span>
                                            </div>
                                        </td>
                                        <td class="p-3"><?php echo e($email); ?></td>
                                        <td class="p-3"><?php echo $orders; ?></td>
                                        <td class="p-3"><?php echo e($joined); ?></td>
                                        <td class="p-3">
                                            <span id="badge-<?php echo $uid; ?>" class="badge rounded-pill <?php echo $statusClass; ?>">
                                                <?php echo e($statusLabel); ?>
                                            </span>
                                        </td>
                                        <td class="p-3 text-end">
                                            <!-- View -->
                                            <button class="btn btn-sm btn-outline-secondary me-1"
                                                onclick="viewUser(<?= (int)$row['id'] ?>)">
                                                <i class="bi bi-person-lines-fill"></i>
                                            </button>

                                            <!-- Ban / Unban button you already have ... -->
                                            <!-- Ban / Unban -->
                                            <?php if ($isBanned): ?>
                                                <button id="banbtn-<?php echo $uid; ?>" class="btn btn-sm btn-success me-1"
                                                    onclick="banUnbanUser(<?php echo $uid; ?>, 'Banned')" title="Unban">
                                                    <i class="bi bi-unlock-fill me-1"></i> Unban
                                                </button>
                                            <?php else: ?>
                                                <button id="banbtn-<?php echo $uid; ?>" class="btn btn-sm btn-warning me-1"
                                                    onclick="banUnbanUser(<?php echo $uid; ?>, 'Active')" title="Ban">
                                                    <i class="bi bi-slash-circle me-1"></i> Ban
                                                </button>
                                            <?php endif; ?>
                                            <!-- Delete -->
                                            <button class="btn btn-sm btn-outline-danger action-btn"
                                                data-action="delete"
                                                data-user-id="<?= $uid ?>"
                                                data-user-name="<?= e($name) ?>">
                                                <i class="bi bi-trash"></i>
                                            </button>

                                        </td>
                                    </tr>
                            <?php
                                } // while
                            } // else
                            ?>
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

    <footer><?php include 'admin-footer.php'; ?></footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // ===== Config =====
        const BANNED_LIKE = ['banned', 'blocked', 'suspended', 'blacklisted', 'deactivated', 'inactive'];

        // ===== Helpers =====
        function parseJsonTolerant(raw) {
            try {
                return JSON.parse(raw);
            } catch (e) {
                const m = raw.match(/\{[\s\S]*\}$/m);
                if (m) {
                    try {
                        return JSON.parse(m[0]);
                    } catch (e2) {}
                }
                return null;
            }
        }

        function swalError(title, text, footer = null) {
            Swal.fire({
                icon: 'error',
                title: title || 'Error',
                text: text || 'Something went wrong.',
                footer: footer || undefined
            });
        }

        // ===== View user (SweetAlert modal) =====
        function viewUser(uid) {
            const fd = new FormData();
            fd.append('id', uid);
            fetch('getUser.php', {
                    method: 'POST',
                    body: fd
                })
                .then(async r => {
                    const raw = await r.text();
                    const data = parseJsonTolerant(raw) || {};
                    if (!r.ok) {
                        swalError('Request failed', data.msg || `HTTP ${r.status}`, data.db_error ? `<code>${data.db_error}</code>` : null);
                        return;
                    }
                    if (!data.ok) {
                        swalError('Error', data.msg || 'Unknown server error', data.db_error ? `<code>${data.db_error}</code>` : null);
                        return;
                    }

                    const u = data.user || {},
                        addrs = data.addresses || [];
                    const imgs = (u.images || []).map(src => `<img src="${src}" style="width:48px;height:48px;border-radius:50%;object-fit:cover;margin-right:6px">`).join('') || '<span class="text-muted">No image</span>';
                    const addrHtml = addrs.length ?
                        addrs.map(a => `<li class="list-group-item">
            <div><b>Line 1:</b> ${a.address_line_1||'-'}</div>
            <div><b>Line 2:</b> ${a.address_line_2||'-'}</div>
            <div><b>City:</b> ${a.city||'-'} &nbsp; <b>District:</b> ${a.district||'-'} &nbsp; <b>Province:</b> ${a.province||'-'}</div>
            <div><b>Zip:</b> ${a.zip_code||'-'}</div>
          </li>`).join('') :
                        '<li class="list-group-item"><span class="text-muted">No addresses</span></li>';

                    const html = `
        <div class="mb-2"><b>Name:</b> ${u.name||'-'}</div>
        <div class="mb-2"><b>Email:</b> ${u.email||'-'}</div>
        <div class="mb-2"><b>Mobile:</b> ${u.mobile||'-'}</div>
        <div class="mb-2"><b>Password (stored):</b> <code>${u.password||'-'}</code></div>
        <div class="mb-2"><b>Status:</b> ${u.status_name||'-'} &nbsp; <b>Orders:</b> ${u.orders_count||0}</div>
        <div class="mb-3"><b>Joined:</b> ${u.joined_date ? new Date(u.joined_date).toDateString() : '-'}</div>
        <div class="mb-2"><b>Images:</b> <div class="mt-1 d-flex">${imgs}</div></div>
        <div class="mt-3"><b>Addresses</b>
          <ul class="list-group mt-2">${addrHtml}</ul>
        </div>
      `;
                    Swal.fire({
                        width: 700,
                        title: 'User #' + uid,
                        html
                    });
                })
                .catch(() => swalError('Network error', 'Please try again.'));
        }

        // ===== Ban / Unban UI switch =====
        function setRowStatus(uid, newStatusName) {
            const s = String(newStatusName || '').toLowerCase();
            const badge = document.getElementById('badge-' + uid);
            const btn = document.getElementById('banbtn-' + uid);
            if (!badge || !btn) return;

            let label, clazz;
            if (s === 'active') {
                label = 'Active';
                clazz = 'bg-active';
            } else if (BANNED_LIKE.includes(s)) {
                label = (s === 'deactivated' || s === 'inactive') ? 'Deactivated' : 'Banned';
                clazz = 'bg-banned';
            } else {
                label = 'Banned';
                clazz = 'bg-banned';
            }

            badge.textContent = label;
            badge.className = 'badge rounded-pill ' + clazz;

            if (BANNED_LIKE.includes(s)) {
                btn.className = 'btn btn-sm btn-success me-1';
                btn.innerHTML = '<i class="bi bi-unlock-fill me-1"></i> Unban';
                btn.title = 'Unban';
                btn.setAttribute('onclick', `banUnbanUser(${uid}, 'Banned')`);
            } else {
                btn.className = 'btn btn-sm btn-warning me-1';
                btn.innerHTML = '<i class="bi bi-slash-circle me-1"></i> Ban';
                btn.title = 'Ban';
                btn.setAttribute('onclick', `banUnbanUser(${uid}, 'Active')`);
            }
        }

        function banUnbanUser(userId, currentStatusLabel) {
            const s = String(currentStatusLabel || '').trim().toLowerCase();
            const isBannedNow = BANNED_LIKE.includes(s);
            const action = isBannedNow ? 'unban' : 'ban';
            const verb = isBannedNow ? 'Unban' : 'Ban';
            const text = isBannedNow ? 'This will restore access for this user.' :
                'This will prevent this user from accessing the system.';

            Swal.fire({
                icon: 'warning',
                title: `${verb} this user?`,
                text,
                showCancelButton: true,
                confirmButtonText: verb
            }).then((res) => {
                if (!res.isConfirmed) return;

                const fd = new FormData();
                fd.append('id', userId);
                fd.append('action', action);

                fetch('toggleUserBan.php', {
                        method: 'POST',
                        body: fd
                    })
                    .then(async (r) => {
                        const raw = await r.text();
                        const data = parseJsonTolerant(raw) || {};
                        if (!r.ok || !data.ok) {
                            const footer = data.db_error || data.error ? `<code>${data.db_error || data.error}</code>` : null;
                            swalError('Request failed', data.msg || `HTTP ${r.status}`, footer);
                            console.error('Server error body:', raw);
                            return null;
                        }
                        return data;
                    })
                    .then((data) => {
                        if (!data) return;
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: data.msg
                        });
                        setRowStatus(userId, data.new_status_name);
                    })
                    .catch(() => swalError('Network error', 'Please try again.'));
            });
        }

        document.addEventListener('click', (e) => {
            const btn = e.target.closest('.action-btn');
            if (!btn) return;
            if (btn.dataset.action === 'delete') {
                deleteUser(btn.dataset.userId, btn.dataset.userName);
            }
        });




        /**
         * Delete a user, with server‑side protection for users who have orders.
         * @param {number} userId 
         * @param {string} userName 
         */
        function deleteUser(userId, userName) {
            Swal.fire({
                title: "Delete user?",
                html: "You are about to permanently delete <b>" + userName + "</b>.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, delete",
                cancelButtonText: "Cancel",
                confirmButtonColor: "#d33",
            }).then((result) => {
                if (!result.isConfirmed) return;

                const form = new FormData();
                form.append("user_id", userId);

                const xhr = new XMLHttpRequest();
                xhr.open("POST", "admin-deleteUser.php", true);

                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4) {
                        // Default error bubble if the request itself failed
                        if (xhr.status !== 200) {
                            Swal.fire({
                                icon: "error",
                                title: "Request failed",
                                text: "Server error (" + xhr.status + "). Please try again.",
                            });
                            return;
                        }

                        let res;
                        try {
                            res = JSON.parse(xhr.responseText);
                        } catch (e) {
                            res = {
                                ok: false,
                                msg: "Invalid server response."
                            };
                        }

                        if (res.ok) {
                            Swal.fire({
                                icon: "success",
                                title: "Deleted",
                                text: "The user was deleted successfully.",
                            }).then(() => {
                                // Easiest: refresh. Or remove the row dynamically.
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: "error",
                                title: "Cannot delete",
                                text: res.error || res.msg || "Unknown error.",
                            });
                        }
                    }
                };

                xhr.send(form);
            });
        }
    </script>
</body>

</html>