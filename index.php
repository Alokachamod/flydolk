<?php

  session_start();
  
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Flydolk — Drone Showcase</title>

  <!-- Bootstrap & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

  <!-- Your main stylesheet -->
  <link rel="stylesheet" href="style.css">
</head>

<body>

    <?php include 'header.php';  ?>
  

  <!-- ===== Drone Showcase ===== -->
  <section class="showcase-wrapper ">
    <div class="showcase" id="showcasePin">
      <div class="container showcase-grid offset-lg-3">
        <div class="row align-items-center g-4">
          <!-- LEFT: text/data -->
          <div class="col-12 col-lg-4 order-2 order-lg-1">
            <div class="info-wrap" id="infoWrap">
              <h1 class="model-title display-5 mb-2" id="modelTitle">—</h1>
              <p class="mb-3" id="modelDesc">—</p>

              <div class="d-flex flex-wrap gap-2 mb-3" id="modelMeta"></div>

              <div class="d-flex flex-wrap gap-2">
                <a href="#" class="btn btn-dark pill"><i class="bi bi-bag me-2"></i>Buy now</a>
                <a href="#" class="btn btn-outline-dark pill"><i class="bi bi-heart me-2"></i>Wishlist</a>
                <a href="#" class="btn btn-outline-secondary pill"><i class="bi bi-eye me-2"></i>View</a>
              </div>

              <!-- mobile progress dots -->
              <div class="step-dots mt-4" id="stepDots"></div>
            </div>
          </div>

          <!-- RIGHT: ring & orbit -->
          <div class="col-12 col-lg-8 order-1 order-lg-2 d-flex justify-content-center">
            <div class="ring-wrap ">
              <div class="ring" id="ring"></div>
              <img id="mainDrone" class="main-drone" src="" alt="Drone">
              <div class="orbit" id="orbit"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ===== Rest of the page ===== -->
  <section class="py-5">
    <div class="container">
      
    </div>
  </section>

  <?php /* include 'admin-footer.php'; */ ?>

  <!-- JS libs -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/ScrollTrigger.min.js"></script>

  <!-- Your app logic -->
  <script src="script.js"></script>
</body>

</html>