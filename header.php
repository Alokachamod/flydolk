<?php
// header.php – Flydolk user-side header with session integration
// NOTE: Connection is NOT included here to prevent "freezing".
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
$userName = $isLoggedIn ? $_SESSION['user_name'] : '';
$userInitial = $isLoggedIn ? strtoupper(substr($userName, 0, 1)) : '';

// Set to 0 by default. JS will update this.
$cartCount = 0; 
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>

<header class="fd-header fixed-top">
  <!-- animated scanline -->
  <div class="fd-scanline" aria-hidden="true"></div>

  <div class="container py-2 py-lg-3">
    <div class="d-flex align-items-center gap-3">
      <!-- Brand: Logo + mini radar -->
      <a href="index.php" class="fd-brandwrap d-flex align-items-center text-decoration-none">
        <img src="imgs/Flydo white logo.png" alt="Flydolk Logo" class="fd-logo" />
        <div class="fd-radar-mini ms-2">
          <span class="ring"></span>
          <span class="sweep"></span>
        </div>
      </a>

      <!-- Desktop nav -->
      <nav class="ms-auto d-none d-lg-flex align-items-center gap-3">
        <div class="dropdown">
          <a class="fd-nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Categories</a>
          <ul class="dropdown-menu dropdown-menu-end p-2 rounded-3 shadow-sm">
            <?php
                // --- DYNAMIC CATEGORY LINKS ---
                // We must require connection.php here, *after* the page starts loading
                require_once 'connection.php';
                $header_categories_rs = Database::search("SELECT id, name FROM category ORDER BY name ASC");
                if ($header_categories_rs->num_rows > 0) {
                    while ($cat = $header_categories_rs->fetch_assoc()) {
                        echo '<li><a class="dropdown-item" href="shop.php?category=' . $cat['id'] . '">' . htmlspecialchars($cat['name']) . '</a></li>';
                    }
                } else {
                    echo '<li><a class="dropdown-item" href="shop.php">All Products</a></li>';
                }
                // --- END DYNAMIC LINKS ---
            ?>
          </ul>
        </div>
        <a class="fd-nav-link" href="shop.php">Shop</a>
        <a class="fd-nav-link" href="service.php">Service</a>
        <a class="fd-nav-link" href="contact.php">Contact</a>

        <!-- Search -->
        <div class="search-wrapper ms-2" style="min-width:260px; width:320px;">
          <input class="search-input" type="text" placeholder="Search..." id="searchInput" />
          <button class="search-button" onclick="handleSearch()">
            <i class="fa-solid fa-magnifying-glass"></i>
          </button>
        </div>

        <!-- Account / Cart -->
        <div class="d-flex align-items-center gap-2 ms-2">
          <?php if ($isLoggedIn): ?>
            <!-- Logged In User -->
            <div class="dropdown">
              <button class="fd-icon-btn dropdown-toggle border-0" type="button" data-bs-toggle="dropdown" aria-label="Account Menu">
                <div class="fd-user-avatar">
                  <?= htmlspecialchars($userInitial) ?>
                </div>
              </button>
              <ul class="dropdown-menu dropdown-menu-end p-2 rounded-3 shadow-sm" style="min-width: 200px;">
                <li class="px-3 py-2 border-bottom">
                  <small class="text-muted">Signed in as</small>
                  <div class="fw-bold"><?= htmlspecialchars($userName) ?></div>
                </li>
                <li><a class="dropdown-item rounded-2" href="account.php"><i class="fa-regular fa-user me-2"></i>My Account</a></li>
                <li><a class="dropdown-item rounded-2" href="orders.php"><i class="fa-solid fa-box me-2"></i>My Orders</a></li>
                <li><a class="dropdown-item rounded-2" href="wishlist.php"><i class="fa-regular fa-heart me-2"></i>Wishlist</a></li>
                <li><hr class="dropdown-divider my-2"></li>
                <li><a class="dropdown-item rounded-2 text-danger" href="logout.php"><i class="fa-solid fa-right-from-bracket me-2"></i>Logout</a></li>
              </ul>
            </div>
          <?php else: ?>
            <!-- Not Logged In -->
            <a href="login-signup.php" class="fd-icon-btn" aria-label="Sign In">
              <i class="fa-regular fa-user"></i>
            </a>
          <?php endif; ?>
          
          <a href="cart.php" class="fd-icon-btn position-relative" aria-label="Cart" id="header-cart-btn-desktop">
            <i class="fa-solid fa-cart-shopping"></i>
            <!-- DYNAMIC BADGE -->
            <span class="fd-badge" id="header-cart-count-desktop" style="display: none;">0</span>
            <!-- END DYNAMIC BADGE -->
          </a>
        </div>
      </nav>

      <!-- Mobile: cart + burger -->
      <div class="ms-auto d-lg-none d-flex align-items-center gap-2">
        <a href="cart.php" class="fd-icon-btn position-relative" aria-label="Cart" id="header-cart-btn-mobile">
            <i class="fa-solid fa-cart-shopping"></i>
            <!-- DYNAMIC BADGE (MOBILE) -->
            <span class="fd-badge" id="header-cart-count-mobile" style="display: none;">0</span>
            <!-- END DYNAMIC BADGE (MOBILE) -->
        </a>
        <button class="navbar-toggler fd-burger" type="button" data-bs-toggle="offcanvas" data-bs-target="#fdOffcanvas" aria-label="Open menu">
          <span></span><span></span><span></span>
        </button>
      </div>
    </div>

    <!-- Mobile search row -->
    <div class="d-lg-none mt-2">
      <div class="search-wrapper">
        <input class="search-input" type="text" placeholder="Search..." id="searchInputMobile" />
        <button class="search-button" onclick="handleSearch(true)">
          <i class="fa-solid fa-magnifying-glass"></i>
        </button>
      </div>
    </div>
  </div>

  <!-- Offcanvas (mobile menu) -->
  <div class="offcanvas offcanvas-end fd-offcanvas" tabindex="-1" id="fdOffcanvas">
    <div class="offcanvas-header">
      <h5 class="offcanvas-title text-light">Menu</h5>
      <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
      <?php if ($isLoggedIn): ?>
        <!-- Mobile: Logged In User Info -->
        <div class="fd-mobile-user-info mb-3 p-3 rounded-3" style="background: rgba(13, 177, 253, 0.1); border: 1px solid rgba(13, 177, 253, 0.2);">
          <div class="d-flex align-items-center gap-2">
            <div class="fd-user-avatar-large">
              <?= htmlspecialchars($userInitial) ?>
            </div>
            <div>
              <small class="text-muted d-block">Welcome back</small>
              <strong class="text-light"><?= htmlspecialchars($userName) ?></strong>
            </div>
          </div>
        </div>
      <?php endif; ?>

      <a class="fd-nav-link d-block mb-2" href="shop.php">Shop</a>
      <div class="dropdown mb-2">
        <a class="fd-nav-link dropdown-toggle d-inline-block" href="#" data-bs-toggle="dropdown">Categories</a>
        <ul class="dropdown-menu p-2 rounded-3 shadow-sm">
           <?php
                // --- DYNAMIC CATEGORY LINKS (MOBILE) ---
                // No need to query again, $header_categories_rs is already available if included
                if (isset($header_categories_rs) && $header_categories_rs->num_rows > 0) {
                    // Reset pointer if already used
                    $header_categories_rs->data_seek(0); 
                    while ($cat = $header_categories_rs->fetch_assoc()) {
                        echo '<li><a class="dropdown-item" href="shop.php?category=' . $cat['id'] . '">' . htmlspecialchars($cat['name']) . '</a></li>';
                    }
                } else {
                    // Fallback if query failed or no categories
                     echo '<li><a class="dropdown-item" href="shop.php">All Products</a></li>';
                }
                // --- END DYNAMIC LINKS ---
            ?>
        </ul>
      </div>
      <a class="fd-nav-link d-block mb-2" href="service.php">Service</a>
      <a class="fd-nav-link d-block mb-2" href="contact.php">Contact</a>
      <hr class="border-secondary">
      
      <?php if ($isLoggedIn): ?>
        <!-- Mobile: Logged In Menu -->
        <a class="fd-nav-link d-block mb-2" href="account.php"><i class="fa-regular fa-user me-2"></i>My Account</a>
        <a class="fd-nav-link d-block mb-2" href="orders.php"><i class="fa-solid fa-box me-2"></i>My Orders</a>
        <a class="fd-nav-link d-block mb-2" href="wishlist.php"><i class="fa-regular fa-heart me-2"></i>Wishlist</a>
        <a class="fd-nav-link d-block mb-2" href="cart.php"><i class="fa-solid fa-cart-shopping me-2"></i>Cart</a>
        <hr class="border-secondary">
        <a class="fd-nav-link d-block text-danger" href="logout.php" onclick="return confirm('Are you sure you want to logout?')">
          <i class="fa-solid fa-right-from-bracket me-2"></i>Logout
        </a>
      <?php else: ?>
        <!-- Mobile: Not Logged In Menu -->
        <a class="fd-nav-link d-block mb-2" href="login-signup.php"><i class="fa-solid fa-right-to-bracket me-2"></i>Sign In / Sign Up</a>
        <a class="fd-nav-link d-block" href="cart.php"><i class="fa-solid fa-cart-shopping me-2"></i>Cart</a>
      <?php endif; ?>
    </div>
  </div>
</header>

<style>
/* User Avatar Styles */
.fd-user-avatar {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  background: linear-gradient(135deg, var(--fd-accent), var(--fd-accent-2));
  color: #061018;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 0.9rem;
  border: 2px solid var(--fd-line);
}

.fd-user-avatar-large {
  width: 48px;
  height: 48px;
  border-radius: 50%;
  background: linear-gradient(135deg, var(--fd-accent), var(--fd-accent-2));
  color: #061018;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 1.2rem;
  border: 2px solid var(--fd-accent);
}

.fd-icon-btn.dropdown-toggle::after {
  display: none;
}

.dropdown-menu {
  background: var(--fd-panel);
  border: 1px solid var(--fd-line);
  margin-top: 0.5rem;
}

.dropdown-item {
  color: var(--fd-text);
  transition: all 0.2s ease;
}

.dropdown-item:hover {
  background: rgba(13, 177, 253, 0.1);
  color: var(--fd-accent);
}

.dropdown-item.text-danger:hover {
  background: rgba(239, 68, 68, 0.1);
  color: #ef4444;
}

.dropdown-divider {
  border-color: var(--fd-line);
}

.fd-mobile-user-info {
  animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>

<script>
// Search functionality
function handleSearch(isMobile = false) {
  const inputId = isMobile ? 'searchInputMobile' : 'searchInput';
  const searchInput = document.getElementById(inputId);
  const query = searchInput.value.trim();
  
  if (query) {
    window.location.href = `shop.php?search=${encodeURIComponent(query)}`;
  }
}

// Allow Enter key to trigger search
document.addEventListener('DOMContentLoaded', function() {
  const desktopInput = document.getElementById('searchInput');
  const mobileInput = document.getElementById('searchInputMobile');
  
  if (desktopInput) {
    desktopInput.addEventListener('keypress', function(e) {
      if (e.key === 'Enter') {
        handleSearch(false);
      }
    });
  }
  
  if (mobileInput) {
    mobileInput.addEventListener('keypress', function(e) {
      if (e.key === 'Enter') {
        handleSearch(true);
      }
    });
  }

  // --- ASYNC CART COUNT ---
  // This runs after the page loads, so it doesn't "freeze" the site.
  fetch('get_cart_count.php')
    .then(response => response.json())
    .then(data => {
      const cartCount = data.cart_count;
      if (cartCount > 0) {
        const desktopBadge = document.getElementById('header-cart-count-desktop');
        const mobileBadge = document.getElementById('header-cart-count-mobile');
        
        if (desktopBadge) {
            desktopBadge.textContent = cartCount;
            desktopBadge.style.display = 'flex'; // Use 'flex' as per your .fd-badge style
        }
        if (mobileBadge) {
            mobileBadge.textContent = cartCount;
            mobileBadge.style.display = 'flex';
        }
      }
    })
    .catch(error => {
      console.error('Error fetching cart count:', error);
    });
  // --- END ASYNC CART COUNT ---
});
</script>

