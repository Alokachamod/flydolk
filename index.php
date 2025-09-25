<?php
include 'connection.php';
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
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="bootstrap.css">

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
      <!-- Featured products (from DB with Database class) -->
      <div class="col-12 mt-4">
        <h3 class="h4 fw-bold mb-3">Featured Drones</h3>

        <?php
        require_once __DIR__ . '/connection.php';

        $sql = "
      SELECT p.id, p.title, p.price, c.name AS category,
             (SELECT img_url FROM product_img WHERE product_id = p.id LIMIT 1) AS img_url
      FROM product p
      LEFT JOIN category c ON c.id = p.category_id
      LEFT JOIN product_status ps ON ps.id = p.product_status_id
      WHERE ps.name = 'active' OR ps.name IS NULL
      ORDER BY p.create_at DESC
      LIMIT 8
    ";

        $result = Database::search($sql);
        ?>

        <?php if ($result && $result->num_rows > 0): ?>
          <div class="row g-4">
            <?php while ($row = $result->fetch_assoc()):
              $id    = (int)$row['id'];
              $name  = htmlspecialchars($row['title'] ?? '—', ENT_QUOTES, 'UTF-8');
              $cat   = htmlspecialchars($row['category'] ?? '', ENT_QUOTES, 'UTF-8');
              $img   = htmlspecialchars($row['img_url'] ?? 'imgs/no-image.png', ENT_QUOTES, 'UTF-8');
              $price = number_format((float)($row['price'] ?? 0), 0, '.', ',');
            ?>
              <div class="col-12 col-sm-6 col-lg-3">
                <a href="product.php?id=<?= $id ?>" class="card prod-card h-100 text-decoration-none">
                  <div class="ratio ratio-1x1 bg-light d-flex align-items-center justify-content-center">
                    <img src="<?= $img ?>" class="p-3 img-fluid" alt="<?= $name ?>">
                  </div>
                  <div class="card-body">
                    <?php if ($cat): ?><div class="small text-muted"><?= $cat ?></div><?php endif; ?>
                    <div class="fw-semibold text-dark"><?= $name ?></div>
                    <div class="fw-bold">LKR <?= $price ?></div>
                  </div>
                </a>
              </div>
            <?php endwhile; ?>
          </div>
        <?php else: ?>
          <div class="alert alert-info mb-0">No products found (or query returned empty).</div>
        <?php endif; ?>
      </div>
      <!-- /Featured products -->

    </div>
  </section>

  <?php include 'footer.php';  ?>

  <!-- JS libs -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/ScrollTrigger.min.js"></script>

  <!-- Your app logic -->
  <script src="script.js"></script>
</body>

</html>