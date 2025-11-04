<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'connection.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAQs - FlyDolk</title>
    <link rel="stylesheet" href="style.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts (Inter) -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;900&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #0f172a;
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
        .border-slate-700 { border-color: #334155; }
        .text-blue-400 { color: #60a5fa; }

        /* Dark Accordion Styles */
        .accordion-dark .accordion-item {
            background-color: #1e293b;
            border: 1px solid #334155;
            color: #f1f5f9;
        }
        .accordion-dark .accordion-header .accordion-button {
            background-color: #1e293b;
            color: #f1f5f9;
            font-weight: 600;
            font-size: 1.1rem;
            box-shadow: none; /* Remove focus shadow */
        }
        .accordion-dark .accordion-button:not(.collapsed) {
            background-color: #334155;
            color: #f1f5f9;
        }
        .accordion-dark .accordion-body {
            background-color: #0f172a;
            color: #cbd5e1;
            line-height: 1.7;
        }
        .accordion-dark .accordion-button::after {
            /* Custom chevron for dark mode */
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23f1f5f9'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");
        }
        .accordion-dark .accordion-button:not(.collapsed)::after {
             background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23f1f5f9'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");
        }
    </style>
</head>
<body>

    <?php include 'header.php'; ?>

    <main class="container my-5">
        <div class="text-center mb-5">
            <h1 class="display-5 fw-bold text-white">Frequently Asked Questions</h1>
            <p class="fs-5 text-muted">Find answers to common questions about our products and services.</p>
        </div>
        
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <div class="accordion accordion-dark" id="faqAccordion">
                    
                    <!-- FAQ Item 1 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingOne">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                What is your shipping policy?
                            </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                We offer island-wide shipping across Sri Lanka. Standard shipping typically takes 2-4 business days. We also offer free shipping on all orders over LKR 50,000. You will receive a tracking number as soon as your order is dispatched.
                            </div>
                        </div>
                    </div>
                    
                    <!-- FAQ Item 2 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingTwo">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                What is your return policy?
                            </button>
                        </h2>
                        <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                We accept returns within 14 days of purchase for items that are in their original, unopened packaging. If you receive a defective item, please contact us immediately at <a href="mailto:contact@flydolk.com" class="text-blue-400">contact@flydolk.com</a>, and we will arrange for a replacement or repair.
                            </div>
                        </div>
                    </div>
                    
                    <!-- FAQ Item 3 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingThree">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                Do your drones come with a warranty?
                            </button>
                        </h2>
                        <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Yes, all drones sold by FlyDolk come with a standard 1-year manufacturer's warranty that covers manufacturing defects. This warranty does not cover accidental damage, such as crashes. We also offer extended care plans for purchase.
                            </div>
                        </div>
                    </div>
                    
                    <!-- FAQ Item 4 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingFour">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                Do I need a license to fly these drones in Sri Lanka?
                            </button>
                        </h2>
                        <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Drone regulations in Sri Lanka are managed by the Civil Aviation Authority of Sri Lanka (CAASL). For most of our consumer drones (under 1kg and flown for recreational purposes), registration is required, but a full pilot's license may not be. We strongly advise you to check the official CAASL website for the most up-to-date regulations before flying.
                            </div>
                        </div>
                    </div>

                    <!-- FAQ Item 5 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingFive">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                                Are these drones good for beginners?
                            </button>
                        </h2>
                        <div id="collapseFive" class="accordion-collapse collapse" aria-labelledby="headingFive" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Absolutely! We have a wide range of drones. Models in the "Mini" series are perfect for beginners as they are lightweight, easy to control, and packed with safety features like obstacle avoidance and automated return-to-home.
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </main>

    <?php include 'footer.php'; ?>

    <script src="script.js"></script>
    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

