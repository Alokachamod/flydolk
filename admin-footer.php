<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel Footer</title>
    
    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        /* This styling ensures the footer is visually consistent with the header */
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa; /* Match the main body background */
        }

        .footer {
            background-color: #ffffff; /* Same as header */
            border-top: 1px solid #dee2e6; /* Subtle top border for separation */
            padding-top: 1.5rem;
            padding-bottom: 1.5rem;
            font-size: 0.9rem;
            color: #6c757d; /* Muted gray text, same as non-active header links */
        }

        .footer .nav-link {
            color: #6c757d;
            padding: 0;
            transition: color 0.2s ease-in-out;
        }

        .footer .nav-link:hover {
            color: var(--bs-primary); /* Use primary color on hover, like header */
        }

        .footer .social-icons a {
            color: #6c757d;
            transition: color 0.2s ease-in-out;
        }
        
        .footer .social-icons a:hover {
            color: var(--bs-primary);
        }
    </style>
</head>
<body>

<!-- Footer Component -->
<footer class="footer">
    <div class="container-fluid">
        <div class="d-flex flex-wrap justify-content-between align-items-center">
            
            <!-- Copyright Text (Left) -->
            <div class="col-md-4 mb-2 mb-md-0 text-center text-md-start">
                <span>&copy; 2025 flydolk. All Rights Reserved.</span>
            </div>

            <!-- Social Icons (Center) -->
            <div class="col-md-4 d-flex align-items-center justify-content-center mb-2 mb-md-0 social-icons">
                <a href="#" class="mx-2 fs-5"><i class="bi bi-twitter-x"></i></a>
                <a href="#" class="mx-2 fs-5"><i class="bi bi-github"></i></a>
                <a href="#" class="mx-2 fs-5"><i class="bi bi-slack"></i></a>
            </div>

            <!-- Footer Links (Right) -->
            <ul class="nav col-md-4 justify-content-center justify-content-md-end">
                <li class="nav-item"><a href="#" class="nav-link px-2">Help</a></li>
                <li class="nav-item"><a href="#" class="nav-link px-2">Documentation</a></li>
                <li class="nav-item"><a href="#" class="nav-link px-2">Contact</a></li>
            </ul>
        </div>
    </div>
</footer>

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
