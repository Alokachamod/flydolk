


<footer class="fd-footer fd-footer--light mt-5">
  <!-- Flight Path ribbon -->
  <div class="fd-flight-path" aria-hidden="true">
    <svg viewBox="0 0 1200 100" preserveAspectRatio="none">
      <path d="M0,60 C200,10 400,110 600,60 C800,10 1000,110 1200,60" class="fd-path"></path>
      <circle r="5" class="fd-drone"></circle>
    </svg>
  </div>

  <div class="container position-relative py-5">
    <!-- Status strip -->
    <div class="fd-status d-flex align-items-center gap-2 rounded-3 px-3 py-2">
      <span class="dot online" aria-label="System online"></span>
      <span class="label">System Status:</span>
      <strong id="fd-status-text">Online</strong>
      <span class="flex-grow-1"></span>
      <i class="fa-regular fa-clock"></i>
      <span id="fd-clock">--:--:--</span>
      <span class="fd-v-divider"></span>
      <i class="fa-solid fa-box-archive"></i>
      <span id="fd-orders">Orders served: <b>12,487</b></span>
    </div>

    <div class="row g-3 mt-3">
      <!-- Core (radar + brand + newsletter) -->
      <div class="col-lg-5">
        <div class="fd-core p-3 rounded-3">
          <div class="fd-radar mx-auto my-2">
            <div class="ring r1"></div>
            <div class="ring r2"></div>
            <div class="ring r3"></div>
            <div class="sweep"></div>
            <span class="blip b1"></span>
            <span class="blip b2"></span>
            <span class="blip b3"></span>
          </div>
          <h3 class="fd-brand text-center m-0">FLYDOLK</h3>
          <p class="fd-tag text-center">Drones • Tech • Innovation</p>

          <div class="fd-news d-flex align-items-center gap-2 rounded-pill px-2 py-1">
            <input id="fd-news-email" type="email" class="form-control border-0 bg-transparent text-light"
                   placeholder="Join mission updates (email)" aria-label="Email for newsletter">
            <button type="button" class="btn rounded-circle" onclick="fdSubscribeNews()" aria-label="Subscribe">
              <i class="fa-solid fa-paper-plane"></i>
            </button>
          </div>
          <small class="fd-privacy d-block mt-1">By subscribing, you agree to our
            <a href="/privacy.php">Privacy Policy</a>.
          </small>
        </div>
      </div>

      <!-- Explore -->
      <nav class="col-6 col-lg-2">
        <div class="fd-col h-100 rounded-3 p-3">
          <h4 class="h6">Explore</h4>
          <ul class="fd-links list-unstyled m-0">
            <li><a href="/index.php" class="fx">Home</a></li>
            <li><a href="/products.php" class="fx">Shop</a></li>
            <li><a href="/categories.php" class="fx">Categories</a></li>
            <li><a href="/about.php" class="fx">About Us</a></li>
          </ul>
        </div>
      </nav>

      <!-- Support -->
      <nav class="col-6 col-lg-2">
        <div class="fd-col h-100 rounded-3 p-3">
          <h4 class="h6">Support</h4>
          <ul class="fd-links list-unstyled m-0">
            <li><a href="/faq.php" class="fx">FAQs</a></li>
            <li><a href="/shipping-returns.php" class="fx">Shipping & Returns</a></li>
            <li><a href="/privacy.php" class="fx">Privacy Policy</a></li>
            <li><a href="/terms.php" class="fx">Terms & Conditions</a></li>
          </ul>
        </div>
      </nav>

      <!-- Contact + Social -->
      <div class="col-12 col-lg-3">
        <div class="fd-col h-100 rounded-3 p-3">
          <h4 class="h6">Contact</h4>
          <ul class="fd-contact list-unstyled m-0">
            <li><i class="fa-solid fa-location-dot me-2"></i> Colombo, Sri Lanka</li>
            <li><i class="fa-solid fa-envelope me-2"></i> <a href="mailto:contact@flydolk.com" style="text-decoration: none; color: white;">contact@flydolk.com</a></li>
            <li><i class="fa-solid fa-phone me-2"></i> <a href="tel:+94704866124" style="text-decoration: none; color: white;">+94 70 486 6124</a></li>
          </ul>
          <div class="fd-social d-flex gap-2 mt-3">
            <a href="#" aria-label="Facebook" style="text-decoration: none;"><i class="fa-brands fa-facebook-f"></i></a>
            <a href="#" aria-label="Instagram" style="text-decoration: none;"><i class="fa-brands fa-instagram"></i></a>
            <a href="#" aria-label="TikTok" style="text-decoration: none;"><i class="fa-brands fa-tiktok"></i></a>
            <a href="#" aria-label="YouTube" style="text-decoration: none;"><i class="fa-brands fa-youtube"></i></a>
            <a href="#" aria-label="LinkedIn" style="text-decoration: none;"><i class="fa-brands fa-linkedin-in"></i></a>
          </div>
        </div>
      </div>
    </div>

    <!-- Bottom strip -->
    <div class="fd-bottom d-flex align-items-center gap-3 mt-3 pt-3">
      <p class="m-0">© <span id="fd-year"></span> FLYDOLK. All rights reserved.</p>
      <button class="fd-thruster ms-auto" onclick="fdBackToTop()" aria-label="Back to top">
        <i class="fa-solid fa-jet-fighter-up"></i>
      </button>
    </div>
  </div>
</footer>




