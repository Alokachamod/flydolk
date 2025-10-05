<?php
include 'connection.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Shop - Flydolk Drones</title>
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <link rel="stylesheet" href="style.css">
  
  <style>
    /* Shop-specific styles */
    .shop-hero {
      background: linear-gradient(135deg, #0c0f14 0%, #1a1f2e 100%);
      padding: 120px 0 80px;
      margin-top: calc(var(--header-h, 96px) + 1px);
      position: relative;
      overflow: hidden;
    }
    
    .shop-hero::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: radial-gradient(circle at 20% 50%, rgba(13, 177, 253, 0.1), transparent 50%),
                  radial-gradient(circle at 80% 80%, rgba(53, 242, 192, 0.08), transparent 50%);
      pointer-events: none;
    }
    
    .shop-hero h1 {
      color: var(--fd-text);
      font-weight: 800;
      font-size: 3rem;
      margin-bottom: 1rem;
      opacity: 0;
      transform: translateY(30px);
    }
    
    .shop-hero p {
      color: var(--fd-dim);
      font-size: 1.1rem;
      opacity: 0;
      transform: translateY(20px);
    }
    
    .filter-sidebar {
      background: #f8f9fa;
      border-radius: 16px;
      padding: 1.5rem;
      position: sticky;
      top: calc(var(--header-h, 96px) + 20px);
      opacity: 0;
      transform: translateX(-30px);
    }
    
    .filter-section {
      margin-bottom: 1.5rem;
      padding-bottom: 1.5rem;
      border-bottom: 1px solid #dee2e6;
    }
    
    .filter-section:last-child {
      border-bottom: none;
      margin-bottom: 0;
      padding-bottom: 0;
    }
    
    .filter-title {
      font-weight: 700;
      font-size: 0.95rem;
      color: #0b2239;
      margin-bottom: 1rem;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    
    .form-check-input:checked {
      background-color: var(--fd-accent);
      border-color: var(--fd-accent);
    }
    
    .product-card {
      background: #fff;
      border-radius: 16px;
      overflow: hidden;
      border: 1px solid #eef0f3;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.04);
      transition: all 0.3s ease;
      opacity: 0;
      transform: translateY(30px);
    }
    
    .product-card:hover {
      transform: translateY(-8px);
      box-shadow: 0 12px 30px rgba(13, 177, 253, 0.15);
      border-color: var(--fd-accent);
    }
    
    .product-img-wrapper {
      aspect-ratio: 1/1;
      background: #f8fafc;
      display: flex;
      align-items: center;
      justify-content: center;
      overflow: hidden;
      position: relative;
    }
    
    .product-img {
      width: 100%;
      height: 100%;
      object-fit: contain;
      padding: 1.5rem;
      transition: transform 0.3s ease;
    }
    
    .product-card:hover .product-img {
      transform: scale(1.05);
    }
    
    .product-badge {
      position: absolute;
      top: 12px;
      right: 12px;
      background: var(--fd-accent);
      color: #061018;
      padding: 0.35rem 0.75rem;
      border-radius: 20px;
      font-size: 0.75rem;
      font-weight: 700;
      letter-spacing: 0.3px;
    }
    
    .product-body {
      padding: 1.25rem;
    }
    
    .product-category {
      color: var(--fd-dim);
      font-size: 0.85rem;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      margin-bottom: 0.5rem;
    }
    
    .product-title {
      font-weight: 700;
      color: #0b2239;
      font-size: 1.1rem;
      margin-bottom: 0.75rem;
      line-height: 1.3;
    }
    
    .product-price {
      font-size: 1.5rem;
      font-weight: 800;
      color: #0b3d60;
      margin-bottom: 1rem;
    }
    
    .product-actions {
      display: flex;
      gap: 0.5rem;
    }
    
    .btn-view {
      flex: 1;
      background: var(--fd-accent);
      color: #061018;
      border: none;
      padding: 0.65rem;
      border-radius: 10px;
      font-weight: 600;
      transition: all 0.2s ease;
    }
    
    .btn-view:hover {
      background: #0c9ce0;
      transform: translateY(-2px);
      box-shadow: 0 6px 16px rgba(13, 177, 253, 0.3);
    }
    
    .btn-wishlist {
      width: 44px;
      height: 44px;
      background: #f8fafc;
      border: 1px solid #e2e8f0;
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #64748b;
      transition: all 0.2s ease;
    }
    
    .btn-wishlist:hover {
      background: #fee;
      color: #e11d48;
      border-color: #fecdd3;
    }
    
    .sort-controls {
      background: #fff;
      border-radius: 12px;
      padding: 1rem;
      border: 1px solid #eef0f3;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
      margin-bottom: 2rem;
      display: flex;
      align-items: center;
      justify-content: space-between;
      flex-wrap: wrap;
      gap: 1rem;
      opacity: 0;
      transform: translateY(20px);
    }
    
    .results-count {
      color: var(--fd-dim);
      font-weight: 500;
    }
    
    .view-toggle {
      display: flex;
      gap: 0.5rem;
    }
    
    .view-btn {
      width: 40px;
      height: 40px;
      border: 1px solid #e2e8f0;
      background: #fff;
      border-radius: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #64748b;
      transition: all 0.2s ease;
    }
    
    .view-btn.active {
      background: var(--fd-accent);
      color: #061018;
      border-color: var(--fd-accent);
    }
    
    .empty-state {
      text-align: center;
      padding: 4rem 1rem;
      color: var(--fd-dim);
    }
    
    .empty-state i {
      font-size: 4rem;
      margin-bottom: 1rem;
      color: #cbd5e1;
    }
    
    @media (max-width: 991.98px) {
      .shop-hero h1 {
        font-size: 2rem;
      }
      
      .filter-sidebar {
        position: static;
        margin-bottom: 2rem;
      }
    }
  </style>
</head>

<body>
  <?php include 'header.php'; ?>
  
  <!-- Hero Section -->
  <section class="shop-hero">
    <div class="container">
      <div class="text-center position-relative">
        <h1 id="shopTitle">Explore Our Drones</h1>
        <p id="shopSubtitle">Premium DJI drones for every mission</p>
      </div>
    </div>
  </section>
  
  <!-- Shop Content -->
  <section class="py-5">
    <div class="container">
      <div class="row g-4">
        <!-- Sidebar Filters -->
        <aside class="col-lg-3">
          <div class="filter-sidebar" id="filterSidebar">
            <!-- Categories -->
            <div class="filter-section">
              <h3 class="filter-title">Categories</h3>
              <?php
              $catQuery = "SELECT id, name FROM category ORDER BY name";
              $catResult = Database::search($catQuery);
              
              if ($catResult && $catResult->num_rows > 0) {
                while ($cat = $catResult->fetch_assoc()) {
                  $catId = (int)$cat['id'];
                  $catName = htmlspecialchars($cat['name'], ENT_QUOTES, 'UTF-8');
                  echo '<div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" value="'.$catId.'" id="cat'.$catId.'" onchange="filterProducts()">
                    <label class="form-check-label" for="cat'.$catId.'">'.$catName.'</label>
                  </div>';
                }
              }
              ?>
            </div>
            
            <!-- Brands -->
            <div class="filter-section">
              <h3 class="filter-title">Brands</h3>
              <?php
              $brandQuery = "SELECT id, name FROM brand ORDER BY name";
              $brandResult = Database::search($brandQuery);
              
              if ($brandResult && $brandResult->num_rows > 0) {
                while ($brand = $brandResult->fetch_assoc()) {
                  $brandId = (int)$brand['id'];
                  $brandName = htmlspecialchars($brand['name'], ENT_QUOTES, 'UTF-8');
                  echo '<div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" value="'.$brandId.'" id="brand'.$brandId.'" onchange="filterProducts()">
                    <label class="form-check-label" for="brand'.$brandId.'">'.$brandName.'</label>
                  </div>';
                }
              }
              ?>
            </div>
            
            <!-- Price Range -->
            <div class="filter-section">
              <h3 class="filter-title">Price Range</h3>
              <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" value="0-500000" id="price1" onchange="filterProducts()">
                <label class="form-check-label" for="price1">Under LKR 500,000</label>
              </div>
              <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" value="500000-1000000" id="price2" onchange="filterProducts()">
                <label class="form-check-label" for="price2">LKR 500,000 - 1,000,000</label>
              </div>
              <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" value="1000000-9999999" id="price3" onchange="filterProducts()">
                <label class="form-check-label" for="price3">Over LKR 1,000,000</label>
              </div>
            </div>
          </div>
        </aside>
        
        <!-- Product Grid -->
        <main class="col-lg-9">
          <!-- Sort Controls -->
          <div class="sort-controls" id="sortControls">
            <div class="results-count">
              <strong id="productCount">0</strong> products found
            </div>
            
            <div class="d-flex align-items-center gap-3">
              <select class="form-select form-select-sm" style="width: auto;" onchange="sortProducts(this.value)">
                <option value="newest">Newest First</option>
                <option value="price-low">Price: Low to High</option>
                <option value="price-high">Price: High to Low</option>
                <option value="name">Name: A to Z</option>
              </select>
              
              <div class="view-toggle d-none d-md-flex">
                <button class="view-btn active" onclick="setView('grid')" title="Grid view">
                  <i class="fa-solid fa-grid"></i>
                </button>
                <button class="view-btn" onclick="setView('list')" title="List view">
                  <i class="fa-solid fa-list"></i>
                </button>
              </div>
            </div>
          </div>
          
          <!-- Products -->
          <div class="row g-4" id="productsContainer">
            <?php
            $productQuery = "
              SELECT p.id, p.title, p.price, p.qty, 
                     c.name AS category, b.name AS brand,
                     (SELECT img_url FROM product_img WHERE product_id = p.id LIMIT 1) AS img_url
              FROM product p
              LEFT JOIN category c ON c.id = p.category_id
              LEFT JOIN brand b ON b.id = p.brand_id
              LEFT JOIN product_status ps ON ps.id = p.product_status_id
              WHERE ps.name = 'active' OR ps.name IS NULL
              ORDER BY p.create_at DESC
            ";
            
            $productResult = Database::search($productQuery);
            
            if ($productResult && $productResult->num_rows > 0) {
              while ($product = $productResult->fetch_assoc()) {
                $id = (int)$product['id'];
                $title = htmlspecialchars($product['title'] ?? '—', ENT_QUOTES, 'UTF-8');
                $category = htmlspecialchars($product['category'] ?? '', ENT_QUOTES, 'UTF-8');
                $brand = htmlspecialchars($product['brand'] ?? '', ENT_QUOTES, 'UTF-8');
                $price = number_format((float)($product['price'] ?? 0), 0, '.', ',');
                $qty = (int)($product['qty'] ?? 0);
                $img = htmlspecialchars($product['img_url'] ?? 'imgs/no-image.png', ENT_QUOTES, 'UTF-8');
                
                $catId = isset($product['category_id']) ? (int)$product['category_id'] : 0;
                $brandId = isset($product['brand_id']) ? (int)$product['brand_id'] : 0;
                
                echo '<div class="col-12 col-sm-6 col-xl-4 product-item" 
                      data-category="'.$catId.'" 
                      data-brand="'.$brandId.'" 
                      data-price="'.(float)($product['price'] ?? 0).'"
                      data-name="'.$title.'">
                  <article class="product-card h-100">
                    <div class="product-img-wrapper">';
                
                if ($qty < 5 && $qty > 0) {
                  echo '<span class="product-badge">Low Stock</span>';
                } elseif ($qty == 0) {
                  echo '<span class="product-badge bg-danger">Out of Stock</span>';
                }
                
                echo '<img src="'.$img.'" alt="'.$title.'" class="product-img">
                    </div>
                    <div class="product-body">
                      <div class="product-category">'.$category.($brand ? ' • '.$brand : '').'</div>
                      <h3 class="product-title">'.$title.'</h3>
                      <div class="product-price">LKR '.$price.'</div>
                      <div class="product-actions">
                        <a href="product.php?id='.$id.'" class="btn btn-view">
                          <i class="fa-solid fa-eye me-2"></i>View Details
                        </a>
                        <button class="btn-wishlist" title="Add to wishlist">
                          <i class="fa-regular fa-heart"></i>
                        </button>
                      </div>
                    </div>
                  </article>
                </div>';
              }
            } else {
              echo '<div class="col-12">
                <div class="empty-state">
                  <i class="fa-solid fa-box-open"></i>
                  <h3>No Products Found</h3>
                  <p>Check back soon for new arrivals!</p>
                </div>
              </div>';
            }
            ?>
          </div>
        </main>
      </div>
    </div>
  </section>
  
  <?php include 'footer.php'; ?>
  
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/ScrollTrigger.min.js"></script>
  <script src="script.js"></script>
  
  <script>
    gsap.registerPlugin(ScrollTrigger);
    
    // Hero animations
    gsap.to('#shopTitle', {
      opacity: 1,
      y: 0,
      duration: 0.8,
      ease: 'power3.out'
    });
    
    gsap.to('#shopSubtitle', {
      opacity: 1,
      y: 0,
      duration: 0.8,
      delay: 0.2,
      ease: 'power3.out'
    });
    
    // Sidebar animation
    gsap.to('#filterSidebar', {
      opacity: 1,
      x: 0,
      duration: 0.6,
      delay: 0.3,
      ease: 'power2.out'
    });
    
    // Sort controls animation
    gsap.to('#sortControls', {
      opacity: 1,
      y: 0,
      duration: 0.6,
      delay: 0.4,
      ease: 'power2.out'
    });
    
    // Product cards stagger animation
    gsap.to('.product-card', {
      opacity: 1,
      y: 0,
      duration: 0.6,
      stagger: 0.1,
      delay: 0.5,
      ease: 'power2.out',
      scrollTrigger: {
        trigger: '#productsContainer',
        start: 'top 80%'
      }
    });
    
    // Update product count
    function updateProductCount() {
      const visible = document.querySelectorAll('.product-item:not([style*="display: none"])').length;
      document.getElementById('productCount').textContent = visible;
    }
    updateProductCount();
    
    // Filter products
    function filterProducts() {
      const categories = Array.from(document.querySelectorAll('[id^="cat"]:checked')).map(cb => cb.value);
      const brands = Array.from(document.querySelectorAll('[id^="brand"]:checked')).map(cb => cb.value);
      const prices = Array.from(document.querySelectorAll('[id^="price"]:checked')).map(cb => cb.value);
      
      document.querySelectorAll('.product-item').forEach(item => {
        const cat = item.dataset.category;
        const brand = item.dataset.brand;
        const price = parseFloat(item.dataset.price);
        
        let show = true;
        
        if (categories.length > 0 && !categories.includes(cat)) show = false;
        if (brands.length > 0 && !brands.includes(brand)) show = false;
        
        if (prices.length > 0) {
          let matchPrice = false;
          prices.forEach(range => {
            const [min, max] = range.split('-').map(Number);
            if (price >= min && price <= max) matchPrice = true;
          });
          if (!matchPrice) show = false;
        }
        
        item.style.display = show ? '' : 'none';
      });
      
      updateProductCount();
    }
    
    // Sort products
    function sortProducts(sortBy) {
      const container = document.getElementById('productsContainer');
      const items = Array.from(container.querySelectorAll('.product-item'));
      
      items.sort((a, b) => {
        if (sortBy === 'price-low') {
          return parseFloat(a.dataset.price) - parseFloat(b.dataset.price);
        } else if (sortBy === 'price-high') {
          return parseFloat(b.dataset.price) - parseFloat(a.dataset.price);
        } else if (sortBy === 'name') {
          return a.dataset.name.localeCompare(b.dataset.name);
        }
        return 0;
      });
      
      items.forEach(item => container.appendChild(item));
    }
    
    // View toggle (grid/list)
    function setView(view) {
      document.querySelectorAll('.view-btn').forEach(btn => btn.classList.remove('active'));
      event.target.closest('.view-btn').classList.add('active');
      
      const container = document.getElementById('productsContainer');
      if (view === 'list') {
        container.querySelectorAll('.product-item').forEach(item => {
          item.classList.remove('col-sm-6', 'col-xl-4');
          item.classList.add('col-12');
        });
      } else {
        container.querySelectorAll('.product-item').forEach(item => {
          item.classList.remove('col-12');
          item.classList.add('col-sm-6', 'col-xl-4');
        });
      }
    }
  </script>
</body>
</html>