<?php
// header.php – Flydolk user-side header with session integration
require_once 'connection.php'; // Ensure connection is available
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
$userName = '';
$userInitial = '';
$userImageUrl = ''; // <-- NEW: Variable for profile image

if ($isLoggedIn) {
    $user_id = (int)$_SESSION['user_id'];
    
    // Fetch user name
    $user_rs = Database::search("SELECT name FROM user WHERE id = $user_id");
    if ($user_rs->num_rows > 0) {
        $user_data = $user_rs->fetch_assoc();
        $userName = $user_data['name'];
        $userInitial = strtoupper(substr($userName, 0, 1));
        $_SESSION['user_name'] = $userName; // Ensure session is updated
    }

    // --- NEW: Fetch user profile image ---
    $img_rs = Database::search("SELECT url FROM user_img WHERE user_id = $user_id");
    if ($img_rs->num_rows == 1) {
        $userImageUrl = $img_rs->fetch_assoc()['url'];
    }
    // --- END NEW ---
}

// Fetch categories from the database
$categories_html = '';
$category_rs = Database::search("SELECT id, name FROM category");
if ($category_rs->num_rows > 0) {
    while ($category = $category_rs->fetch_assoc()) {
        $categories_html .= '<li><a class="dropdown-item" href="shop.php?category=' . $category['id'] . '">' . htmlspecialchars($category['name']) . '</a></li>';
    }
} else {
    $categories_html = '<li><a class="dropdown-item" href="#">No categories found</a></li>';
}

// Get cart count
$cart_count = 0;
// We will fetch this via AJAX, so we just set a placeholder
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
            <?php echo $categories_html; // Dynamic categories ?>
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
                <!-- **NEW: LOGIC TO SHOW IMAGE OR INITIAL** -->
                <div id="header-avatar-container">
                  <?php if (!empty($userImageUrl)): ?>
                    <img src="<?php echo htmlspecialchars($userImageUrl); ?>" alt="User" class="fd-user-avatar-img">
                  <?php else: ?>
                    <div class="fd-user-avatar">
                      <?php echo htmlspecialchars($userInitial); ?>
                    </div>
                  <?php endif; ?>
                </div>
              </button>
              <ul class="dropdown-menu dropdown-menu-end p-2 rounded-3 shadow-sm" style="min-width: 200px;">
                <li class="px-3 py-2 border-bottom">
                  <small class="text-muted">Signed in as</small>
                  <div class="fw-bold"><?php echo htmlspecialchars($userName); ?></div>
                </li>
                <li><a class="dropdown-item rounded-2" href="account.php"><i class="fa-regular fa-user me-2"></i>My Account</a></li>
                <li><a class="dropdown-item rounded-2" href="order_history.php"><i class="fa-solid fa-box me-2"></i>My Orders</a></li>
                <li><a class="dropdown-item rounded-2" href="wishlist.php"><i class="fa-regular fa-heart me-2"></i>Wishlist</a></li>
                <li><hr class="dropdown-divider my-2"></li>
                <li><a class="dropdown-item rounded-2 text-danger" href="logout.php" onclick="return confirm('Are you sure you want to logout?');"><i class="fa-solid fa-right-from-bracket me-2"></i>Logout</a></li>
              </ul>
            </div>
          <?php else: ?>
            <!-- Not Logged In -->
            <a href="login-signup.php" class="fd-icon-btn" aria-label="Sign In">
              <i class="fa-regular fa-user"></i>
            </a>
          <?php endif; ?>
          
          <a href="cart.php" class="fd-icon-btn position-relative" aria-label="Cart">
            <i class="fa-solid fa-cart-shopping"></i>
            <span class="fd-badge" id="cart-count-badge">0</span> <!-- Placeholder -->
          </a>
        </div>
      </nav>

      <!-- Mobile: cart + burger -->
      <div class="ms-auto d-lg-none d-flex align-items-center gap-2">
        <a href="cart.php" class="fd-icon-btn position-relative" aria-label="Cart">
            <i class="fa-solid fa-cart-shopping"></i>
            <span class="fd-badge" id="cart-count-badge-mobile">0</span> <!-- Placeholder -->
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
            <!-- **NEW: LOGIC TO SHOW IMAGE OR INITIAL** -->
            <div id="mobile-avatar-container">
              <?php if (!empty($userImageUrl)): ?>
                <img src="<?php echo htmlspecialchars($userImageUrl); ?>" alt="User" class="fd-user-avatar-img-large">
              <?php else: ?>
                <div class="fd-user-avatar-large">
                  <?php echo htmlspecialchars($userInitial); ?>
                </div>
              <?php endif; ?>
            </div>
            <div>
              <small class="text-muted d-block">Welcome back</small>
              <strong class="text-light"><?php echo htmlspecialchars($userName); ?></strong>
            </div>
          </div>
        </div>
      <?php endif; ?>

      <a class="fd-nav-link d-block mb-2" href="shop.php">Shop</a>
      <div class="dropdown mb-2">
        <a class="fd-nav-link dropdown-toggle d-inline-block" href="#" data-bs-toggle="dropdown">Categories</a>
        <ul class="dropdown-menu p-2 rounded-3 shadow-sm">
           <?php echo $categories_html; // Dynamic categories ?>
        </ul>
      </div>
      <a class="fd-nav-link d-block mb-2" href="service.php">Service</a>
      <a class="fd-nav-link d-block mb-2" href="contact.php">Contact</a>
      <hr class="border-secondary">
      
      <?php if ($isLoggedIn): ?>
        <!-- Mobile: Logged In Menu -->
        <a class="fd-nav-link d-block mb-2" href="account.php"><i class="fa-regular fa-user me-2"></i>My Account</a>
        <a class="fd-nav-link d-block mb-2" href="order_history.php"><i class="fa-solid fa-box me-2"></i>My Orders</a>
        <a class="fd-nav-link d-block mb-2" href="wishlist.php"><i class="fa-regular fa-heart me-2"></i>Wishlist</a>
        <a class="fd-nav-link d-block mb-2" href="cart.php"><i class="fa-solid fa-cart-shopping me-2"></i>Cart</a>
        <hr class="border-secondary">
        <a class="fd-nav-link d-block text-danger" href="logout.php" onclick="return confirm('Are you sure you want to logout?')"><i class="fa-solid fa-right-from-bracket me-2"></i>Logout</a>
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

/* **NEW: STYLE FOR IMAGE AVATAR** */
.fd-user-avatar-img {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  object-fit: cover;
  border: 2px solid var(--fd-accent-2);
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

/* **NEW: STYLE FOR LARGE IMAGE AVATAR** */
.fd-user-avatar-img-large {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    object-fit: cover;
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
  
  // Asynchronous Cart Count Fetch
  fetch('get_cart_count.php')
    .then(response => response.json())
    .then(data => {
      if (data.status === 'success') {
        const count = data.cart_count;
        const badgeDesktop = document.getElementById('cart-count-badge');
        const badgeMobile = document.getElementById('cart-count-badge-mobile');
        if (badgeDesktop) badgeDesktop.textContent = count;
        if (badgeMobile) badgeMobile.textContent = count;
      }
    })
    .catch(error => {
      console.error('Error fetching cart count:', error);
    });
});
</script>

