<?php
// We need the database connection for the 'New Arrivals' section
// This will make your Database class available
require_once 'connection.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FlyDolk - Premium Drones & FPV Gear</title>
    
    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" xintegrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
    <!-- Google Fonts (Inter) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">

    <!-- Custom CSS for Dark Theme, Carousel Animations, and Bootstrap Overrides -->
    <style>
        /* --- Base & Dark Theme --- */
        body {
            font-family: 'Inter', sans-serif;
            background-color: #0f172a; /* bg-slate-900 */
            color: #f1f5f9; /* text-gray-100 */
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* --- Custom Carousel --- */
        .carousel-item-custom {
            height: 90vh; /* md:h-[90vh] */
            min-height: 600px;
            background-size: cover;
            background-position: center;
        }

        /* Ken Burns Effect */
        .carousel-item-custom .ken-burns-bg {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            background-size: cover;
            background-position: center;
            animation: kenBurns 20s infinite alternate-reverse;
            transition: opacity 1.2s ease-in-out;
        }
        
        @keyframes kenBurns {
            0% { transform: scale(1.1) translate(0, 0); opacity: 1; }
            100% { transform: scale(1) translate(5%, 5%); opacity: 1; }
        }
        
        /* Ensure the effect is running even on non-active slides */
        .carousel-item-custom:not(.active) .ken-burns-bg {
            animation-play-state: running;
        }
        
        /* Overlay */
        .carousel-overlay {
            position: absolute;
            inset: 0;
            background-color: rgba(0, 0, 0, 0.6);
        }
        
        /* Text Animations */
        .carousel-caption-custom {
            /* Position in center */
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 90%;
            max-width: 672px; /* max-w-2xl */
            text-align: center;
        }

        .slide-title, .slide-desc, .slide-btn {
            opacity: 0; /* Start invisible */
        }
        
        .carousel-item.active .slide-title {
            animation: slideUp 1s 0.3s ease-out forwards;
        }
        .carousel-item.active .slide-desc {
            animation: slideUp 1s 0.5s ease-out forwards;
        }
        .carousel-item.active .slide-btn {
            animation: slideUp 1s 0.7s ease-out forwards;
        }
        
        @keyframes slideUp {
            0% { transform: translateY(50px); opacity: 0; }
            100% { transform: translateY(0); opacity: 1; }
        }

        /* --- Component Styling --- */
        .bg-slate-800 {
            background-color: #1e293b;
        }
        .bg-slate-950 {
            background-color: #020617;
        }
        
        .text-blue-400 {
            color: #60a5fa;
        }
        
        .section-py {
            padding-top: 5rem;
            padding-bottom: 5rem;
        }
        
        /* Product Card */
        .product-card {
            background-color: #1e293b; /* bg-slate-800 */
            border: none;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }
        .product-card:hover {
            transform: scale(1.05);
            box-shadow: 0 0 20px -5px rgba(96, 165, 250, 0.3); /* hover:shadow-blue-500/20 */
        }
        .product-card .card-img-top {
            height: 12rem; /* h-48 */
            object-fit: cover;
        }
        .product-card .btn-view {
            opacity: 0;
            transform: translateX(0.5rem);
            transition: all 0.3s ease;
        }
        .product-card:hover .btn-view {
            opacity: 1;
            transform: translateX(0);
        }

        /* Category Card */
        .category-card {
            position: relative;
            height: 16rem; /* h-64 */
            overflow: hidden;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        .category-card img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        .category-card:hover img {
            transform: scale(1.1);
        }
        .category-card .card-overlay {
            position: absolute;
            inset: 0;
            background-color: rgba(0, 0, 0, 0.6);
            transition: background-color 0.3s ease;
        }
        .category-card:hover .card-overlay {
            background-color: rgba(0, 0, 0, 0.4);
        }
        .category-card .card-title-custom {
            position: absolute;
            bottom: 1.5rem;
            left: 1.5rem;
            transition: bottom 0.3s ease;
        }
        .category-card:hover .card-title-custom {
            bottom: 2rem;
        }
        
        /* Newsletter CTA */
        .bg-cta-gradient {
            background-image: linear-gradient(to right, #2563eb, #06b6d4);
        }
        .form-control-cta {
            background-color: rgba(255, 255, 255, 0.9);
        }
        .form-control-cta:focus {
            background-color: #fff;
            box-shadow: 0 0 0 0.25rem rgba(255, 255, 255, 0.5);
        }
        
    </style>
</head>
<body class="bg-dark text-light">

    <?php
    include 'header.php'; 
    ?>

    <!-- Main Content --><main>

        <!-- Hero Carousel Section -->
        <section id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                
                <!-- Slide 1: DJI Mavic 3 Pro -->
                <div class="carousel-item active carousel-item-custom">
                    <div class="ken-burns-bg" style="background-image: url('uploads/products/Slide_DJI_Mavic_3_Pro_drone_68d56eee0d37c.png');"></div>
                    <div class="carousel-overlay"></div>
                    <div class="carousel-caption-custom">
                        <h2 class="slide-title display-4 fw-bolder text-white">
                            Unleash Professional Vision
                        </h2>
                        <p class="slide-desc fs-5 text-light opacity-75 mx-auto" style="max-width: 32rem;">
                            The DJI Mavic 3 Pro: Capture breathtaking 5.1K footage with unparalleled precision.
                        </p>
                        <a href="#" class="slide-btn btn btn-primary bg-gradient btn-lg fw-bold py-3 px-5 rounded-pill shadow-lg mt-4">
                            Discover Mavic 3 Pro
                        </a>
                    </div>
                </div>

                <!-- Slide 2: DJI Air 2S -->
                <div class="carousel-item carousel-item-custom">
                    <div class="ken-burns-bg" style="background-image: url('uploads/products/Slide_Air-2S-1_68d56d5178c05.png');"></div>
                    <div class="carousel-overlay"></div>
                    <div class="carousel-caption-custom">
                        <h2 class="slide-title display-4 fw-bolder text-white">
                            Elevated Aerial Adventures
                        </h2>
                        <p class="slide-desc fs-5 text-light opacity-75 mx-auto" style="max-width: 32rem;">
                            DJI Air 2S: The compact drone that delivers stunning 5.4K video.
                        </p>
                        <a href="#" class="slide-btn btn btn-primary bg-gradient btn-lg fw-bold py-3 px-5 rounded-pill shadow-lg mt-4">
                            Explore Air 2S
                        </a>
                    </div>
                </div>

                <!-- Slide 3: DJI Mavic 2 Pro -->
                <div class="carousel-item carousel-item-custom">
                    <div class="ken-burns-bg" style="background-image: url('uploads/products/Slide_Mavic-2-Pro_68d56e6266e1e.png');"></div>
                    <div class="carousel-overlay"></div>
                    <div class="carousel-caption-custom">
                        <h2 class="slide-title display-4 fw-bolder text-white">
                            Iconic Imagery, Refined
                        </h2>
                        <p class="slide-desc fs-5 text-light opacity-75 mx-auto" style="max-width: 32rem;">
                            The legendary DJI Mavic 2 Pro: Hasselblad camera for unparalleled image quality.
                        </p>
                        <a href="#" class="slide-btn btn btn-primary bg-gradient btn-lg fw-bold py-3 px-5 rounded-pill shadow-lg mt-4">
                            View Mavic 2 Pro
                        </a>
                    </div>
                </div>

            </div>

            <!-- Carousel Controls -->
            <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </section>

        <!-- Value Propositions Section -->
        <section class="bg-slate-800 py-5">
            <div class="container">
                <div class="row row-cols-1 row-cols-md-3 g-4 g-lg-5 text-center">
                    <div class="col d-flex flex-column align-items-center">
                        <svg class="w-12 h-12 text-blue-400 mb-3" style="width: 3rem; height: 3rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16.5a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1zM16 16.5a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.5 11.667V6.5A1.5 1.5 0 0 0 16 5h-1.5a1.5 1.5 0 0 0-1.5 1.5v.333"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.5 11.667V6.5A1.5 1.5 0 0 0 12 5h-1.5a1.5 1.5 0 0 0-1.5 1.5v.333"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.5 11.667V6.5A1.5 1.5 0 0 0 8 5H6.5A1.5 1.5 0 0 0 5 6.5v.333"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 11.667a2 2 0 0 0-2 2V14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-2.333a2 2 0 0 0-2-2H5z"></path></svg>
                        <h3 class="fs-5 fw-bold text-white">Free Shipping</h3>
                        <p class="text-light opacity-75 mb-0">On all orders over $150. We ship fast and secure.</p>
                    </div>
                    <div class="col d-flex flex-column align-items-center">
                        <svg class="w-12 h-12 text-blue-400 mb-3" style="width: 3rem; height: 3rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 0a5 5 0 10-7.07 7.07l7.07-7.07zm-3.536 3.536l3.536-3.536"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17l-2 2a5 5 0 01-7.07-7.07l2-2"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7l2-2a5 5 0 017.07 7.07l-2 2"></path></svg>
                        <h3 class="fs-5 fw-bold text-white">Expert Support</h3>
                        <p class="text-light opacity-75 mb-0">Our team of drone pilots is here to help you 24/7.</p>
                    </div>
                    <div class="col d-flex flex-column align-items-center">
                        <svg class="w-12 h-12 text-blue-400 mb-3" style="width: 3rem; height: 3rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12 12 0 0012 21.054a12 12 0 008.618-15.016z"></path></svg>
                        <h3 class="fs-5 fw-bold text-white">1-Year Warranty</h3>
                        <p class="text-light opacity-75 mb-0">Fly with confidence. All products include a full warranty.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Featured Products Section -->
        <section class="section-py">
            <div class="container">
                <h2 class="display-5 fw-bolder text-white text-center mb-5">New Arrivals</h2>
                
                <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-4">
                    
                    <?php
                    // --- Load New Arrivals from Database ---

                    try {
                        // SQL Query: Select 4 newest products. 
                        // I am *assuming* your table is 'products' and you have these columns.
                        // I'm also assuming 'date_added' or 'product_id' can be used for sorting.
                        $sql = "SELECT product_id, product_name, short_description, price, image_path 
                                FROM products 
                                ORDER BY date_added DESC 
                                LIMIT 4";
                        
                        // Use your Database class and its static search method
                        $newArrivals_rs = Database::search($sql);

                        // Check if the query returned a valid result set
                        if ($newArrivals_rs && $newArrivals_rs->num_rows > 0):
                            // Loop through the results using fetch_assoc
                            while ($product = $newArrivals_rs->fetch_assoc()):
                    ?>
                                <!-- Dynamic Product Card -->
                                <div class="col">
                                    <div class="card product-card h-100 rounded-3 overflow-hidden">
                                        <!-- I'm assuming 'product_details.php' is your product page -->
                                        <a href="product_details.php?id=<?php echo htmlspecialchars($product['product_id']); ?>" class="text-decoration-none">
                                            
                                            <!-- Assumes 'image_path' is stored in the DB, e.g., 'uploads/products/drone.png' -->
                                            <img src="<?php echo htmlspecialchars($product['image_path']); ?>" 
                                                 class="card-img-top"
                                                 alt="<?php echo htmlspecialchars($product['product_name']); ?>" 
                                                 onerror="this.src='https://placehold.co/600x400/334155/fff?text=Image+Error'">
                                            
                                            <div class="card-body p-4">
                                                <h3 class="fs-5 fw-bold text-white mb-2"><?php echo htmlspecialchars($product['product_name']); ?></h3>
                                                
                                                <!-- Assumes 'short_description' is in your DB -->
                                                <p class="text-light opacity-75 mb-3 small"><?php echo htmlspecialchars($product['short_description']); ?></p>
                                                
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <!-- Assumes 'price' is a number. number_format adds decimals. -->
                                                    <span class="fs-4 fw-bold text-blue-400">$<?php echo number_format($product['price'], 2); ?></span>
                                                    <button class="btn btn-primary btn-sm btn-view fw-semibold">
                                                        View
                                                    </button>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                                <!-- End Dynamic Product Card -->

                    <?php
                            endwhile;
                        else:
                            // Show a message if no products are found
                            echo '<div class="col-12"><p class="text-light opacity-75 text-center">No new arrivals found at this time.</p></div>';
                        endif;

                    } catch (Exception $e) {
                        // Show a generic error message if the database query fails
                        // You should log the actual error: error_log($e->getMessage());
                        echo '<div class="col-12"><p class="text-light opacity-75 text-center">Could not load new products. Please check back later.</p></div>';
                    }
                    ?>

                </div>
            </div>
        </section>
        
        <!-- Categories Section -->
        <section class="section-py bg-slate-950">
            <div class="container">
                <h2 class="display-5 fw-bolder text-white text-center mb-5">Shop by Category</h2>
                <div class="row row-cols-1 row-cols-md-3 g-4">

                    <!-- Category Card 1: Cinematic Drones -->
                    <div class="col">
                        <a href="#" class="card category-card rounded-3 text-decoration-none">
                            <img src="uploads/products/Slide_DJI_Mavic_3_Pro_drone_68d56eee0d37c.png" 
                                 alt="Cinematic Drones">
                            <div class="card-overlay"></div>
                            <h3 class="card-title-custom h2 fw-bold text-white">
                                Cinematic Drones
                            </h3>
                        </a>
                    </div>

                    <!-- Category Card 2: FPV Racing -->
                    <div class="col">
                        <a href="#" class="card category-card rounded-3 text-decoration-none">
                            <img src="uploads/products/Slide_DJI_air_2_68d565fbd5016.png" 
                                 alt="FPV Racing Drones">
                            <div class="card-overlay"></div>
                            <h3 class="card-title-custom h2 fw-bold text-white">
                                FPV Racing
                            </h3>
                        </a>
                    </div>

                    <!-- Category Card 3: Parts & Goggles -->
                    <div class="col">
                        <a href="#" class="card category-card rounded-3 text-decoration-none">
                            <img src="uploads/products/Slide_mavic-3-enterprise-removebg-preview_68d56b0393cc0.png" 
                                 alt="Parts and Goggles">
                            <div class="card-overlay"></div>
                            <h3 class="card-title-custom h2 fw-bold text-white">
                                Parts & Goggles
                            </h3>
                        </a>
                    </div>

                </div>
            </div>
        </section>
        
        <!-- Newsletter CTA -->
        <section class="bg-cta-gradient py-5">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8 text-center">
                        <h2 class="display-5 fw-bolder text-white mb-3">Join the Flight Club</h2>
                        <p class="fs-5 text-white-50 max-w-2xl mx-auto mb-4">
                            Get exclusive deals, new product announcements, and expert drone tips straight to your inbox.
                        </p>
                        <form class="input-group input-group-lg" style="max-width: 32rem; margin: auto;">
                            <label for="email-address" class="visually-hidden">Email address</label>
                            <input id="email-address" name="email" type="email" autocomplete="email" required 
                                   class="form-control form-control-cta border-0" 
                                   placeholder="Enter your email">
                            <button type="submit" 
                                    class="btn btn-dark fw-semibold px-4">
                                Subscribe
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php
    // Include your existing footer file
    // This file should NOT contain <html>, <head>, or <body> tags
    include 'footer.php'; 
    ?>

    <!-- Bootstrap JS Bundle (includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" xintegrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
                    <script src="script.js"></script>
    <!-- 
        Your header.php or footer.php might already include Bootstrap JS.
        If it does, you can remove the script tag above to avoid loading it twice.
    -->
    
    <!-- Custom JS (e.g., for carousel auto-play adjustments if needed) -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // The Bootstrap carousel auto-plays by default (data-bs-ride="carousel")
            // If you need to change the interval, you can do it here:
            var heroCarousel = document.getElementById('heroCarousel');
            if (heroCarousel) {
                var carouselInstance = new bootstrap.Carousel(heroCarousel, {
                    interval: 7000, // 7 seconds per slide
                    wrap: true
                });
            }
        });
    </script>

</body>
</html>

