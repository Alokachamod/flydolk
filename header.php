<?php
// header.php — Flydolk user-side header (Bootstrap 5)
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>

<header class="fd-header fixed-top ">
  <!-- animated scanline -->
  <div class="fd-scanline" aria-hidden="true"></div>

  <div class="container py-2 py-lg-3">
    <div class="d-flex align-items-center gap-3">
      <!-- Brand: Logo + mini radar -->
      <a href="index.php" class="fd-brandwrap d-flex align-items-center text-decoration-none">
        <!-- If your filename has a space, keep %20 -->
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
            <li><a class="dropdown-item" href="/categories.php?c=mavic">Mavic Series</a></li>
            <li><a class="dropdown-item" href="/categories.php?c=air">Air Series</a></li>
            <li><a class="dropdown-item" href="/categories.php?c=mini">Mini Series</a></li>
            <li><a class="dropdown-item" href="/categories.php?c=enterprise">Enterprise</a></li>
          </ul>
        </div>
        <a class="fd-nav-link" href="shop.php">Shop</a>
        <a class="fd-nav-link" href="/service.php">Service</a>
        <a class="fd-nav-link" href="/contact.php">Contact</a>

        <!-- Search (uses your existing classes) -->
        <div class="search-wrapper ms-2" style="min-width:260px; width:320px;">
          <input class="search-input" type="text" placeholder="Search..." />
          <button class="search-button" onclick="handleSearch()">
            <i class="fa-solid fa-magnifying-glass"></i>
          </button>
        </div>

        <!-- Account / Cart -->
        <div class="d-flex align-items-center gap-2 ms-2">
          <a href="/account.php" class="fd-icon-btn" aria-label="Account">
            <i class="fa-regular fa-user"></i>
          </a>
          <a href="/cart.php" class="fd-icon-btn position-relative" aria-label="Cart">
            <i class="fa-solid fa-cart-shopping"></i>
            <span class="fd-badge">2</span>
          </a>
        </div>
      </nav>

      <!-- Mobile: cart + burger -->
      <div class="ms-auto d-lg-none d-flex align-items-center gap-2">
        <a href="/cart.php" class="fd-icon-btn" aria-label="Cart"><i class="fa-solid fa-cart-shopping"></i></a>
        <button class="navbar-toggler fd-burger" type="button" data-bs-toggle="offcanvas" data-bs-target="#fdOffcanvas" aria-label="Open menu">
          <span></span><span></span><span></span>
        </button>
      </div>
    </div>

    <!-- Mobile search row -->
    <div class="d-lg-none mt-2">
      <div class="search-wrapper">
        <input class="search-input" type="text" placeholder="Search..." />
        <button class="search-button" onclick="handleSearch()">
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
      <a class="fd-nav-link d-block mb-2" href="/products.php">Shop</a>
      <div class="dropdown mb-2">
        <a class="fd-nav-link dropdown-toggle d-inline-block" href="#" data-bs-toggle="dropdown">Categories</a>
        <ul class="dropdown-menu p-2 rounded-3 shadow-sm">
          <li><a class="dropdown-item" href="/categories.php?c=mavic">Mavic Series</a></li>
          <li><a class="dropdown-item" href="/categories.php?c=air">Air Series</a></li>
          <li><a class="dropdown-item" href="/categories.php?c=mini">Mini Series</a></li>
          <li><a class="dropdown-item" href="/categories.php?c=enterprise">Enterprise</a></li>
        </ul>
      </div>
      <a class="fd-nav-link d-block mb-2" href="/service.php">Service</a>
      <a class="fd-nav-link d-block mb-2" href="/contact.php">Contact</a>
      <hr class="border-secondary">
      <a class="fd-nav-link d-block mb-2" href="/account.php"><i class="fa-regular fa-user me-2"></i>Account</a>
      <a class="fd-nav-link d-block" href="/cart.php"><i class="fa-solid fa-cart-shopping me-2"></i>Cart</a>
    </div>
  </div>
</header>
