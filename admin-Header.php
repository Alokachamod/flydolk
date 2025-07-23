<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Drone Admin Panel Header - Simple & Clean</title>
    
    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" xintegrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Google Fonts: Inter for a clean, professional look -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Custom CSS for the Simple Theme -->
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa; /* Light gray background */
        }

        .navbar {
            background-color: #ffffff;
            box-shadow: 0 2px 4px rgba(0,0,0,.05);
        }

        .navbar-brand .logo-text {
            font-weight: 600;
            color: #212529;
        }
        
        .navbar-brand .logo-icon {
            color: var(--bs-primary);
        }

        .navbar-nav .nav-link {
            font-weight: 500;
            color: #6c757d; /* Muted gray for non-active links */
            padding-top: 0.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 3px solid transparent; /* Placeholder for active state */
            transition: color 0.2s ease-in-out;
        }
        
        .navbar-nav .nav-link:hover,
        .navbar-nav .nav-link:focus {
            color: #212529; /* Darker on hover */
        }

        /* Simple underline for the active navigation link */
        .navbar-nav .nav-item.active .nav-link {
            color: var(--bs-primary);
            font-weight: 600;
            border-bottom: 3px solid var(--bs-primary);
        }
        
        /* Dropdown menu styling */
        .dropdown-menu {
            border-radius: 0.5rem;
            border: 1px solid #dee2e6;
            box-shadow: 0 4px 12px rgba(0,0,0,.1);
        }
        .dropdown-item {
            font-weight: 500;
        }
        .dropdown-item:active {
             background-color: var(--bs-primary);
             color: #fff;
        }
        
        .notification-icon {
            color: #6c757d;
        }
        .notification-icon:hover {
            color: #212529;
        }
        
        .notification-badge {
            top: 0px;
            right: -5px;
            border: 2px solid #ffffff;
        }
    </style>
</head>
<body>

    <!-- Header Component -->
    <header>
        <nav class="navbar navbar-expand-lg navbar-light">
            <div class="container-fluid">
                <!-- 1. Logo (Left) -->
                <a class="navbar-brand d-flex align-items-center" href="#">
                    <i class="bi bi-fan fs-2 logo-icon me-2"></i> <!-- Simple Propeller Icon -->
                    <span class="logo-text">DroneAdmin</span>
                </a>

                <!-- Mobile Menu Button (Hamburger) -->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#main-nav" aria-controls="main-nav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!-- Collapsible Wrapper -->
                <div class="collapse navbar-collapse" id="main-nav">
                    <!-- 2. Navigation Links (Center) -->
                    <ul class="navbar-nav mx-auto mb-2 mb-lg-0 gy-2 text-center">
                        <li class="nav-item active mx-lg-2"><a class="nav-link" href="#">Dashboard</a></li>
                        <li class="nav-item mx-lg-2"><a class="nav-link" href="#">Products</a></li>
                        <li class="nav-item mx-lg-2"><a class="nav-link" href="#">Orders</a></li>
                        <li class="nav-item mx-lg-2"><a class="nav-link" href="#">Users</a></li>
                        <!-- Management Dropdown -->
                        <li class="nav-item dropdown mx-lg-2">
                            <a class="nav-link dropdown-toggle" href="#" id="managementDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Management
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="managementDropdown">
                                <li><a class="dropdown-item" href="#">Category Management</a></li>
                                <li><a class="dropdown-item" href="#">Color Management</a></li>
                                <li><a class="dropdown-item" href="#">Brand Management</a></li>
                                <li><a class="dropdown-item" href="#">Model Management</a></li>
                            </ul>
                        </li>
                    </ul>

                    <!-- 3. Icons (Right) -->
                    <div class="d-flex align-items-center justify-content-center mt-3 mt-lg-0">
                        <!-- Message Icon -->
                        <a href="#" class="notification-icon me-4">
                            <i class="bi bi-envelope fs-5"></i>
                        </a>
                        
                        <!-- Notification Icon with Badge -->
                        <a href="#" class="position-relative notification-icon">
                            <i class="bi bi-bell fs-5"></i>
                            <span class="position-absolute notification-badge translate-middle p-1 bg-danger border border-light rounded-circle">
                                <span class="visually-hidden">New alerts</span>
                            </span>
                        </a>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" xintegrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
