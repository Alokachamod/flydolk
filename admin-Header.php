<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>flydolk - Admin Header</title>
    
    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="imgs/Flydo.png">

    <style>
        /* Shared Styles */
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
        }

        /* Header Styles */
        .admin-header {
            background-color: #ffffff;
            border-bottom: 1px solid #dee2e6;
        }

        .navbar-brand {
            font-weight: 700;
            color: #343a40;
        }

        .nav-link {
            font-weight: 500;
            color: #495057;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid transparent;
            transition: color 0.2s ease-in-out;
        }

        .nav-link:hover {
            color: #0d6efd;
        }

        /* The active state for the underline */
        .nav-link.active {
            color: #0d6efd;
            font-weight: 600;
            border-bottom-color: #0d6efd;
        }
        
        .header-icon {
            font-size: 1.25rem;
        }

        /* Notification Dropdown Styles */
        .notification-dropdown {
            width: 350px;
            border-radius: 0.75rem;
        }
        .notification-item {
            border-bottom: 1px solid #e9ecef;
            white-space: normal; /* Allow text to wrap */
        }
        .notification-item:last-child {
            border-bottom: none;
        }
         .notification-item .icon {
            font-size: 1.25rem;
        }
        .notification-item.unread {
            background-color: #f8f9fa;
        }
        
        /* [NEW] Notification Badge Style */
        .nav-link .badge {
            font-size: 0.6rem;
            padding: 0.25em 0.45em;
            position: absolute;
            top: 4px;
            right: -4px;
        }
    </style>
</head>
<body>

    <!-- Header -->
    <header class="admin-header sticky-top">
        <nav class="navbar navbar-expand-lg">
            <div class="container-fluid">
                <!-- Logo -->
                <a class="navbar-brand" href="admin-dashboard.php">flydolk</a>

                <!-- Responsive Toggle Button -->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar" aria-controls="adminNavbar" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!-- Collapsible Navigation Links -->
                <div class="collapse navbar-collapse" id="adminNavbar">
                    <ul class="navbar-nav mx-auto mb-2 mb-lg-0 gap-3">
                        <li class="nav-item">
                            <a class="nav-link" href="admin-dashboard.php">Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="admin-productManagement.php">Products</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="admin-orderManagement.php">Orders</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="admin-userManagement.php">Users</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Management
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="admin-subManagement.php">Product Attributes</a></li>
                                <li><a class="dropdown-item" href="admin-panelManagement.php">Admin Panel</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="admin-reports.php">Reports</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
                
                <!-- Right Side Icons -->
                <div class="d-flex align-items-center gap-3">
                    
                    <!-- Notification Dropdown -->
                    <div class="dropdown">
                         <a href="#" class="nav-link dropdown-toggle position-relative" id="notificationDropdownLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-bell header-icon" id="notificationBell"></i>
                            <!-- [NEW] Badge for unread count -->
                            <span id="notificationBadge" class="badge rounded-pill bg-danger d-none"></span>
                         </a>
                         <ul class="dropdown-menu dropdown-menu-end p-2 notification-dropdown" aria-labelledby="notificationDropdownLink" id="notificationList">
                             
                             <!-- Header -->
                             <li class="px-2 py-1">
                                 <h6 class="fw-bold mb-0">Notifications</h6>
                             </li>
                             <li><hr class="dropdown-divider"></li>
                             
                             <!-- [NEW] Content will be injected by JavaScript -->
                             <li class="notification-item p-3 text-center text-muted d-none" id="noNotifications">
                                No new notifications
                             </li>
                             
                             <li class="notification-item p-3 text-center" id="notificationLoader">
                                <div class="spinner-border spinner-border-sm text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                             </li>
                             
                             <!-- Footer -->
                             <li><hr class="dropdown-divider"></li>
                             <li><a class="dropdown-item text-center text-primary" href="#">View All Notifications</a></li>
                         </ul>
                    </div>
                </div>

            </div>
        </nav>
    </header>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- JavaScript for Header Logic -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            
            // --- 1. Active Page Link Logic ---
            const currentPage = window.location.pathname.split('/').pop();
            const managementPages = ['admin-subManagement.php', 'admin-panelManagement.php', 'admin-reports.php'];
            
            const mainNavLinks = document.querySelectorAll('#adminNavbar .nav-link');
            const managementDropdownToggle = document.querySelector('#adminNavbar .dropdown-toggle');

            mainNavLinks.forEach(link => {
                if (!link.classList.contains('dropdown-toggle')) {
                    const linkPage = link.getAttribute('href');
                    if (linkPage === currentPage) {
                        link.classList.add('active');
                    }
                }
            });

            // Special handling for the management dropdown
            if (managementPages.includes(currentPage)) {
                managementDropdownToggle.classList.add('active');
            }
            
            // --- 2. [NEW] Notification Fetch Logic ---
            const notificationList = document.getElementById('notificationList');
            const notificationBadge = document.getElementById('notificationBadge');
            const loader = document.getElementById('notificationLoader');
            const noNotifications = document.getElementById('noNotifications');
            
            async function fetchNotifications() {
                try {
                    const response = await fetch('admin-getNotifications.php');
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    
                    const data = await response.json();
                    
                    if (data.ok) {
                        loader.classList.add('d-none'); // Hide loader
                        
                        // Set badge count
                        if (data.unread_count > 0) {
                            notificationBadge.textContent = data.unread_count;
                            notificationBadge.classList.remove('d-none');
                        }
                        
                        // Populate notification list
                        if (data.notifications.length > 0) {
                            data.notifications.forEach(noti => {
                                const itemHTML = `
                                <li class="notification-item p-2 d-flex align-items-start unread">
                                    <div class="icon ${noti.color} me-3 pt-1"><i class="bi ${noti.icon}"></i></div>
                                    <div class="flex-grow-1">
                                        <p class="mb-1"><strong>${noti.title}:</strong> ${noti.message}</p>
                                        <small class="text-muted">${noti.time}</small>
                                    </div>
                                </li>
                                `;
                                // Insert *before* the first divider
                                notificationList.querySelector('hr').insertAdjacentHTML('beforebegin', itemHTML);
                            });
                        } else {
                            // Show "No notifications" message
                            noNotifications.classList.remove('d-none');
                        }
                    } else {
                        throw new Error(data.error || 'Failed to load notifications.');
                    }
                    
                } catch (error) {
                    console.error('Error fetching notifications:', error);
                    loader.innerHTML = '<span class="text-danger small p-2">Could not load</span>';
                }
            }
            
            // Fetch notifications when the page loads
            fetchNotifications();
        });
    </script>
</body>
</html>
