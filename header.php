<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Header</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="bootstrap.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
  <link rel="icon" href="imgs/Flydo.png" type="logo">
</head>

<body>
  <div class="container-fluid">
    <div class="col-12">
      <div class="row">
        <nav class="navbar navbar-expand-lg col-10 offset-1 mt-3 bg-info rounded-3 shadow-sm">
          <div class="container-fluid">
            <a class="navbar-brand" href="#">
              <!-- Using a placeholder image -->
              <img src="imgs/Flydo.png" alt="Logo" height="48">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
              <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse offset-3" id="navbarSupportedContent">
              <!-- Search Form with Animated Search Bar -->
              <form class="d-flex" role="search" onsubmit="event.preventDefault();">
                <div class="search-wrapper">
                  <input type="search" class="search-input" placeholder="Search...">
                  <button class="search-button" type="button" onclick="handleSearch()">
                    <img class="" height="20px" src="imgs/search.png"/>
                  </button>
                </div>
              </form>
              <!-- Navigation Links -->
              <ul class="navbar-nav me-auto mb-2 mb-lg-0 offset-3">
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
                    <a class="nav-link" href="#">Services</a>
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
  <script src="script.js"> </script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
    integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
    crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.min.js"
    integrity="sha384-7qAoOXltbVP82dhxHAUje59V5r2YsVfBafyUDxEdApLPmcdhBPg1DKg1ERo0BZlK"
    crossorigin="anonymous"></script>
</body>

</html>