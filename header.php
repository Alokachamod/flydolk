
  <div class="container-fluid">
    <div class="col-12">
      <div class="row">
        <nav class="navbar navbar-expand-lg col-10 offset-1 mt-3 bg-info rounded-3 shadow-sm">
          <div class="container-fluid">
            <a class="navbar-brand" href="index.php">
              <!-- Using a placeholder image -->
              <img src="imgs/Flydo.png" alt="Logo" height="48">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
              <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse offset-3 col-8" id="navbarSupportedContent">
              <!-- Search Form with Animated Search Bar -->
              <form class="d-flex" role="search" onsubmit="event.preventDefault();">
                <div class="search-wrapper offset-1">
                  <input type="search" class="search-input" placeholder="Search...">
                  <button class="search-button" type="button" onclick="handleSearch()">
                    <img class="" height="20px" src="imgs/search.png"/>
                  </button>
                </div>
              </form>
              <!-- Navigation Links -->
              <ul class="navbar-nav me-auto mb-2 mb-lg-0 offset-2">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Categories
                    </a>
                    <ul class="dropdown-menu mt-4">
                        <li><a class="dropdown-item" href="#">Drones</a></li>
                        <li><a class="dropdown-item" href="#">Action Cameras</a></li>
                        <li><a class="dropdown-item" href="#">Accessories</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="#">All Categories</a></li>
                    </ul>
                </li>
                <!-- END: BOOTSTRAP DROPDOWN AS A NAV LINK -->

                <!-- Other example nav items -->
                <li class="nav-item">
                    <a class="nav-link" href="#">Shop</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Service</a>
                </li>
                 <li class="nav-item">
                    <a class="nav-link" href="#">Contact Us</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Account</a>
                </li>
              </ul>

              
            </div>
          </div>
        </nav>
      </div>
    </div>
  </div>
