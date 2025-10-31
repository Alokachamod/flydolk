<?php
// --- THIS MUST BE THE VERY FIRST LINE ---
// This ensures the session is started before any other code (even connection.php)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// --- END FIX ---

require_once 'connection.php';
// We don't need a separate session_start() here anymore.
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FlyDolk - Premium Drones & FPV Gear</title>
    <link rel="icon" href="imgs/Flydo.png">
    <link rel="stylesheet" href="style.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Google Fonts (Inter) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;900&display=swap" rel="stylesheet">
    
    <!-- Font Awesome (for icons) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>

    <style>
        /* Base styles */
        body {
            font-family: 'Inter', sans-serif;
            background-color: #0f172a; /* Dark slate background */
            color: #f1f5f9;
            /* FIX: Add padding to offset fixed-top header */
            padding-top: 140px; /* Mobile header */
        }
        @media (min-width: 992px) { /* lg breakpoint */
            body {
                padding-top: 90px; /* Desktop header */
            }
        }
        .bg-slate-800 { background-color: #1e293b; }
        .bg-slate-950 { background-color: #020617; }
        .border-slate-700 { border-color: #334155; }
        .text-blue-400 { color: #60a5fa; }

        /* Carousel Styles */
        #heroCarousel .carousel-item {
            height: 85vh; /* 85% of viewport height */
            min-height: 600px;
            background-size: cover;
            background-position: center;
        }

        /* Ken Burns Effect */
        #heroCarousel .carousel-item .ken-burns-bg {
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

        /* Slide Content Animation */
        #heroCarousel .carousel-caption {
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            background-color: rgba(0, 0, 0, 0.6);
        }
        
        #heroCarousel .carousel-item .slide-content {
            max-width: 700px;
        }

        #heroCarousel .carousel-item .slide-title,
        #heroCarousel .carousel-item .slide-desc,
        #heroCarousel .carousel-item .slide-btn {
            opacity: 0;
            transform: translateY(50px);
        }

        #heroCarousel .carousel-item.active .slide-title {
            animation: slideUp 1s 0.3s ease-out forwards;
        }
        #heroCarousel .carousel-item.active .slide-desc {
            animation: slideUp 1s 0.5s ease-out forwards;
        }
        #heroCarousel .carousel-item.active .slide-btn {
            animation: slideUp 1s 0.7s ease-out forwards;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(50px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Product Card Styles */
        .product-card {
            background-color: #1e293b;
            border: 1px solid #334155;
            border-radius: 0.5rem;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2), 0 0 15px rgba(96, 165, 250, 0.1);
        }
        .product-card .card-img-top {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        .product-card .card-body {
            padding: 1.25rem;
        }
        .product-card .view-btn {
            opacity: 0;
            transform: translateX(10px);
            transition: all 0.3s ease;
        }
        .product-card:hover .view-btn {
            opacity: 1;
            transform: translateX(0);
        }

        /* Category Card Styles */
        .category-card {
            border-radius: 0.5rem;
            overflow: hidden;
            position: relative;
            height: 250px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
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
        .category-card-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.8) 0%, rgba(0,0,0,0) 60%);
            transition: background 0.3s ease;
        }
        .category-card:hover .category-card-overlay {
            background: linear-gradient(to top, rgba(0,0,0,0.9) 0%, rgba(0,0,0,0.2) 60%);
        }
        .category-card h3 {
            position: absolute;
            bottom: 1.5rem;
            left: 1.5rem;
            font-size: 1.75rem;
            font-weight: 700;
            color: #fff;
            transition: transform 0.3s ease;
        }
        .category-card:hover h3 {
            transform: translateY(-5px);
        }
    </style>
</head>
<body>

    <!-- Header -->
    <?php include 'header.php'; ?>

    <main>
        <!-- Hero Carousel -->
        <section class="mb-5">
            <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="7000">
                <!-- Carousel Indicators -->
                <div class="carousel-indicators">
                    <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                    <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
                    <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
                </div>
                
                <!-- Carousel Inner -->
                <div class="carousel-inner">
                    <!-- Slide 1 -->
                    <div class="carousel-item active">
                        <div class="ken-burns-bg" style="background-image: url('uploads/products/Slide_DJI_Mavic_3_Pro_drone_68d56eee0d37c.png');"></div>
                        <div class="carousel-caption">
                            <div class="slide-content">
                                <h1 class="slide-title display-3 fw-bolder text-white">Unleash Professional Vision</h1>
                                <p class="slide-desc fs-5 text-light mb-4">The DJI Mavic 3 Pro: Capture breathtaking 5.1K footage with unparalleled precision.</p>
                                <a href="#" class="slide-btn btn btn-primary btn-lg fw-bold py-3 px-5 rounded-pill">
                                    Discover Mavic 3 Pro
                                </a>
                            </div>
                        </div>
                    </div>
                    <!-- Slide 2 -->
                    <div class="carousel-item">
                        <div class="ken-burns-bg" style="background-image: url('uploads/products/Slide_Air-2S-1_68d56d5178c05.png');"></div>
                        <div class="carousel-caption">
                            <div class="slide-content">
                                <h1 class="slide-title display-3 fw-bolder text-white">Elevated Aerial Adventures</h1>
                                <p class="slide-desc fs-5 text-light mb-4">DJI Air 2S: The compact drone that delivers stunning 5.4K video.</p>
                                <a href="#" class="slide-btn btn btn-primary btn-lg fw-bold py-3 px-5 rounded-pill">
                                    Explore Air 2S
                                </a>
                            </div>
                        </div>
                    </div>
                    <!-- Slide 3 -->
                    <div class="carousel-item">
                        <div class="ken-burns-bg" style="background-image: url('uploads/products/Slide_Mavic-2-Pro_68d56e6266e1e.png');"></div>
                        <div class="carousel-caption">
                            <div class="slide-content">
                                <h1 class="slide-title display-3 fw-bolder text-white">Iconic Imagery, Refined</h1>
                                <p class="slide-desc fs-5 text-light mb-4">The legendary DJI Mavic 2 Pro: Hasselblad camera for unparalleled image quality.</p>
                                <a href="#" class="slide-btn btn btn-primary btn-lg fw-bold py-3 px-5 rounded-pill">
                                    View Mavic 2 Pro
                                </a>
                            </div>
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
            </div>
        </section>

        <!-- Value Propositions -->
        <section class="container my-5 py-4">
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="d-flex align-items-center p-3 bg-slate-800 rounded-3">
                        <i class="fas fa-shipping-fast fa-2x text-blue-400 me-3"></i>
                        <div>
                            <h5 class="text-white fw-bold mb-0">Free Shipping</h5>
                            <p class="text-light mb-0">On all orders over LKR 50,000</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="d-flex align-items-center p-3 bg-slate-800 rounded-3">
                        <i class="fas fa-headset fa-2x text-blue-400 me-3"></i>
                        <div>
                            <h5 class="text-white fw-bold mb-0">Expert Support</h5>
                            <p class="text-light mb-0">Our team of drone pilots is here 24/7.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="d-flex align-items-center p-3 bg-slate-800 rounded-3">
                        <i class="fas fa-shield-alt fa-2x text-blue-400 me-3"></i>
                        <div>
                            <h5 class="text-white fw-bold mb-0">1-Year Warranty</h5>
                            <p class="text-light mb-0">Fly with confidence. Full warranty included.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- New Arrivals -->
        <section class="container my-5 py-4">
            <h2 class="text-3xl md:text-4xl fw-bolder text-white text-center mb-5">New Arrivals</h2>
            <div class="row g-4">
                <?php
                // Fetch 4 newest products
                $product_rs = Database::search("
                    SELECT p.*, MIN(pi.img_url) AS img_url 
                    FROM product p
                    LEFT JOIN product_img pi ON p.id = pi.product_id
                    GROUP BY p.id
                    ORDER BY p.create_at DESC 
                    LIMIT 4
                ");

                if ($product_rs->num_rows > 0) {
                    while ($product = $product_rs->fetch_assoc()) {
                        $image_path = $product['img_url'] ?? 'imgs/placeholder.png'; // Fallback image
                ?>
                <div class="col-sm-6 col-lg-3">
                    <div class="product-card h-100">
                        <a href="single_product.php?id=<?php echo $product['id']; ?>" class="text-decoration-none">
                            <img src="<?php echo htmlspecialchars($image_path); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($product['title']); ?>">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title text-white fw-bold mb-2"><?php echo htmlspecialchars($product['title']); ?></h5>
                                <p class="card-text text-light mb-4"><?php echo htmlspecialchars(substr($product['description'], 0, 50)); ?>...</p>
                                
                                <div class="mt-auto d-flex justify-content-between align-items-center">
                                    <span class="fs-4 fw-bold text-blue-400">LKR <?php echo number_format($product['price'], 2); ?></span>
                                    <span class="btn btn-outline-primary view-btn">View</span>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
                <?php
                    }
                } else {
                    echo '<p class="text-center text-light">No new products found.</p>';
                }
                ?>
            </div>
        </section>
        
        <!-- Shop by Category -->
        <section class="container my-5 py-4">
            <h2 class="text-3xl md:text-4xl fw-bolder text-white text-center mb-5">Shop by Category</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <a href="shop.php?category=1" class="category-card d-block text-decoration-none">
                        <img src="uploads/products/Slide_DJI_Mavic_3_Pro_drone_68d56eee0d37c.png" alt="Cinematic Drones">
                        <div class="category-card-overlay"></div>
                        <h3>Cinematic Drones</h3>
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="shop.php?category=2" class="category-card d-block text-decoration-none">
                        <img src="uploads/products/Slide_DJI_air_2_68d565fbd5016.png" alt="FPV Racing Drones">
                        <div class="category-card-overlay"></div>
                        <h3>FPV Racing</h3>
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="shop.php?category=3" class="category-card d-block text-decoration-none">
                        <img src="uploads/products/Slide_mavic-3-enterprise-removebg-preview_68d56b0393cc0.png" alt="Parts and Goggles">
                        <div class="category-card-overlay"></div>
                        <h3>Parts & Goggles</h3>
                    </a>
                </div>
            </div>
        </section>
        
        <!-- Newsletter Call-to-Action -->
        <section class="bg-slate-800 py-5">
            <div class="container text-center py-5">
                <h2 class="display-5 fw-bolder text-white mb-4">Join the Flight Club</h2>
                <p class="fs-5 text-light max-w-2xl mx-auto mb-4">
                    Get exclusive deals, new product announcements, and expert drone tips straight to your inbox.
                </p>
                <form class="d-flex flex-column flex-sm-row max-w-lg mx-auto gap-2">
                    <label for="email-address" class="visually-hidden">Email address</label>
                    <input id="email-address" name="email" type="email" autocomplete="email" required 
                           class="form-control form-control-lg bg-light border-0 text-dark" 
                           placeholder="Enter your email">
                    <button type="submit" 
                            class="btn btn-primary btn-lg fw-bold flex-shrink-0">
                        Subscribe
                    </button>
                </form>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <?php include 'footer.php'; ?>

    <script src="script.js"></script>
    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

