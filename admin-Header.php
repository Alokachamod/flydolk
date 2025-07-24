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
    </style>
</head>
<body>

    <!-- Header -->
    <header class="admin-header sticky-top">
        <nav class="navbar navbar-expand-lg">
            <div class="container-fluid">
                <!-- Logo -->
                <a class="navbar-brand" href="dashboard.html">flydolk</a>

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
                            </ul>
                        </li>
                    </ul>
                </div>
                
                <!-- Right Side Icons -->
                <div class="d-flex align-items-center gap-3">
                    <a href="#" class="nav-link"><i class="bi bi-chat-dots header-icon"></i></a>
                    
                    <!-- Notification Dropdown -->
                    <div class="dropdown">
                         <a href="#" class="nav-link dropdown-toggle" id="notificationDropdownLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-bell header-icon"></i>
                         </a>
                         <ul class="dropdown-menu dropdown-menu-end p-2 notification-dropdown" aria-labelledby="notificationDropdownLink">
                             <li class="px-2 py-1">
                                 <h6 class="fw-bold mb-0">Notifications</h6>
                             </li>
                             <li><hr class="dropdown-divider"></li>
                             
                             <!-- Notification Item -->
                             <li class="notification-item p-2 d-flex align-items-start unread">
                                <div class="icon text-success me-3 pt-1"><i class="bi bi-box-seam-fill"></i></div>
                                <div class="flex-grow-1">
                                    <p class="mb-1"><strong>New Order:</strong> #ORD-00452 has been placed by John Doe.</p>
                                    <small class="text-muted">15 minutes ago</small>
                                </div>
                            </li>

                            <!-- Notification Item -->
                            <li class="notification-item p-2 d-flex align-items-start unread">
                                <div class="icon text-warning me-3 pt-1"><i class="bi bi-exclamation-triangle-fill"></i></div>
                                <div class="flex-grow-1">
                                    <p class="mb-1"><strong>Low Stock Alert:</strong> "DJI Mavic 3 Propellers" has only 5 items left.</p>
                                    <small class="text-muted">1 hour ago</small>
                                </div>
                            </li>

                             <!-- Notification Item -->
                            <li class="notification-item p-2 d-flex align-items-start">
                                <div class="icon text-primary me-3 pt-1"><i class="bi bi-person-plus-fill"></i></div>
                                <div class="flex-grow-1">
                                    <p class="mb-1"><strong>New User:</strong> Jane Smith has registered an account.</p>
                                    <small class="text-muted">3 hours ago</small>
                                </div>
                            </li>
                            
                             <li><hr class="dropdown-divider"></li>
                             <li><a class="dropdown-item text-center text-primary" href="notifications.html">View All Notifications</a></li>
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
            
            const currentPage = 'dashboard.html';
            const managementPages = ['management.html', 'admin-management.html'];
            
            const mainNavLinks = document.querySelectorAll('#adminNavbar .nav-link');
            const managementDropdownToggle = document.querySelector('#adminNavbar .dropdown-toggle');

            mainNavLinks.forEach(link => {
                // Check if it's not a dropdown toggle
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
        });
    </script>
</body>
</html>
