<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unique Footer Design</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            /* Added to demonstrate footer at the bottom */
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background-color: #f0f2f5;
        }
        main {
            flex-grow: 1;
        }

        /* Minor style to ensure link hover is consistent */
        .footer-link:hover {
            color: var(--bs-light) !important;
        }
    </style>
</head>
<body>


    <!-- START: UNIQUE FOOTER COMPONENT (BOOTSTRAP VERSION) -->
    <footer class="bg-dark text-white-50 pt-5 pb-4">
        <div class="container">
            <div class="row g-5">
                <!-- About Section -->
                <div class="col-lg-6 col-md-12">
                    <a href="#" class="text-white text-decoration-none fs-4 fw-bold">FlydoLK</a>
                    <p class="mt-3"> re.</p>
                </div>

                <!-- Quick Links Section -->
                <div class="col-lg-3 col-md-6">
                    <h5 class="text-white fw-medium mb-4">Quick Links</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none footer-link">Home</a></li>
                        <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none footer-link">About Us</a></li>
                        <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none footer-link">Services</a></li>
                        <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none footer-link">Portfolio</a></li>
                    </ul>
                </div>

                <!-- Resources Section -->
                <div class="col-lg-3 col-md-6">
                    <h5 class="text-white fw-medium mb-4">Resources</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none footer-link">Blog</a></li>
                        <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none footer-link">Case Studies</a></li>
                        <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none footer-link">Help & FAQ</a></li>
                        <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none footer-link">Privacy Policy</a></li>
                    </ul>
                </div>
            </div>

            <!-- Bottom Bar -->
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center border-top border-secondary mt-5 pt-4">
                <div class="text-center text-sm-start">
                    <span class="mb-2 mb-sm-0">&copy; 2025 FlydoLK. All Rights Reserved.</span>
                    <span class="d-none d-sm-inline mx-1">|</span>
                    <span class="d-block d-sm-inline mt-2 mt-sm-0">Designed by <a href="https://www.alokadev.dev" class="text-white-50 footer-link text-decoration-none">Alokadev</a></span>
                </div>
                <div class="text-center text-sm-end mt-3 mt-sm-0">
                    <a href="#" class="text-white-50 ms-sm-4 text-decoration-none footer-link"><i class="bi bi-twitter-x"></i></a>
                    <a href="#" class="text-white-50 ms-4 text-decoration-none footer-link"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="text-white-50 ms-4 text-decoration-none footer-link"><i class="bi bi-instagram"></i></a>
                    <a href="#" class="text-white-50 ms-4 text-decoration-none footer-link"><i class="bi bi-linkedin"></i></a>
                </div>
            </div>
        </div>
    </footer>
    <!-- END: UNIQUE FOOTER COMPONENT -->

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
