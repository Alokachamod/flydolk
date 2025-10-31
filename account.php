<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'connection.php';

// 1. CHECK LOGIN
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header('Location: login-signup.php?redirect=account');
    exit;
}
$user_id = (int)$_SESSION['user_id'];

// 2. FETCH ALL USER DATA
Database::setUpConnection();
// This query MUST be "SELECT *" to get all fields
$user_rs = Database::search("SELECT * FROM user WHERE id = $user_id"); 
$user_data = $user_rs->fetch_assoc();
if (!$user_data) {
    // This should not happen if user is logged in, but good to check
    die("Error: Could not find user data.");
}

$address_rs = Database::search("
    SELECT uha.*, c.id AS city_id, d.id AS district_id, p.id AS province_id 
    FROM user_has_address uha
    LEFT JOIN city c ON uha.city_id = c.id
    LEFT JOIN district d ON c.district_id = d.id
    LEFT JOIN province p ON d.province_id = p.id
    WHERE uha.user_id = $user_id
");
$address = $address_rs->fetch_assoc();
if (!$address) {
    $address = [
        'address_line_1' => '', 'address_line_2' => '', 'zip_code' => '', 
        'city_id' => null, 'district_id' => null, 'province_id' => null
    ];
}

$img_rs = Database::search("SELECT url FROM user_img WHERE user_id = $user_id");
$user_img_url = 'imgs/default_avatar.png'; // Default
if ($img_rs->num_rows == 1) {
    $user_img_url = $img_rs->fetch_assoc()['url'];
}

// 3. FETCH PROVINCES, DISTRICTS, CITIES (for dropdowns)
$provinces_rs = Database::search("SELECT * FROM province ORDER BY name ASC");
$districts_rs = null;
if ($address['province_id']) {
    $districts_rs = Database::search("SELECT * FROM district WHERE province_id = " . $address['province_id'] . " ORDER BY name ASC");
}
$cities_rs = null;
if ($address['district_id']) {
    $cities_rs = Database::search("SELECT * FROM city WHERE district_id = " . $address['district_id'] . " ORDER BY name ASC");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account - FlyDolk</title>
    <link rel="icon" href="imgs/Flydo.png">
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
            padding-top: 140px; /* Mobile header */
        }
        @media (min-width: 992px) { /* lg breakpoint */
            body {
                padding-top: 90px; /* Desktop header */
            }
        }
        .bg-slate-800 { background-color: #1e293b; }
        .bg-slate-900 { background-color: #0f172a; }
        .border-slate-700 { border-color: #334155; }
        
        .account-sidebar .list-group-item {
            background-color: transparent;
            border-color: #334155;
            color: #cbd5e1;
            font-weight: 600;
            transition: all 0.2s ease;
        }
        .account-sidebar .list-group-item:hover,
        .account-sidebar .list-group-item.active {
            background-color: #334155;
            color: #f1f5f9;
            border-left: 4px solid var(--fd-accent, #0ea5e9);
        }

        .form-control-dark, .form-select-dark {
            background-color: #1e293b;
            border-color: #334155;
            color: #f1f5f9;
        }
        .form-control-dark:focus, .form-select-dark:focus {
            background-color: #1e293b;
            border-color: #60a5fa;
            color: #f1f5f9;
            box-shadow: 0 0 0 0.25rem rgba(96, 165, 250, 0.25);
        }
        .form-control-dark::placeholder { color: #64748b; }

        .profile-img-container {
            position: relative;
            width: 150px;
            height: 150px;
        }
        #profile-img-preview {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
            border: 4px solid #334155;
        }
        .profile-img-container .btn-overlay {
            position: absolute;
            bottom: 5px;
            right: 5px;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .profile-img-container:hover .btn-overlay {
            opacity: 1;
        }
        #profile-image-input {
            display: none;
        }
    </style>
</head>
<body>

    <?php include 'header.php'; ?>

    <main class="container my-5">
        <div class="row g-4">
            
            <!-- Sidebar -->
            <div class="col-lg-3">
                <nav class="list-group account-sidebar bg-slate-800 p-2 rounded-3 border border-slate-700">
                    <a href="account.php" class="list-group-item list-group-item-action active" aria-current="true">
                        <i class="fa-regular fa-user me-2"></i> My Profile
                    </a>
                    <a href="order_history.php" class="list-group-item list-group-item-action">
                        <i class="fa-solid fa-box me-2"></i> My Orders
                    </a>
                    <a href="wishlist.php" class="list-group-item list-group-item-action">
                        <i class="fa-regular fa-heart me-2"></i> Wishlist
                    </a>
                    <a href="logout.php" class="list-group-item list-group-item-action text-danger" onclick="return confirm('Are you sure you want to logout?');">
                        <i class="fa-solid fa-right-from-bracket me-2"></i> Logout
                    </a>
                </nav>
            </div>

            <!-- Content Area -->
            <div class="col-lg-9">
                <h1 class="display-6 fw-bold text-white mb-4">My Profile</h1>
                
                <!-- Alert Placeholder -->
                <div id="alert-placeholder"></div>

                <!-- Profile Image Form -->
                <div class="bg-slate-800 p-4 rounded-3 border border-slate-700 mb-4">
                    <form id="profile-image-form">
                        <h4 class="text-white fw-bold mb-3">Profile Picture</h4>
                        <div class="d-flex align-items-center">
                            <div class="profile-img-container me-3">
                                <img src="<?php echo htmlspecialchars($user_img_url); ?>" alt="Profile Image" id="profile-img-preview">
                                <label for="profile-image-input" class="btn btn-primary btn-sm rounded-circle btn-overlay">
                                    <i class="fa-solid fa-camera"></i>
                                </label>
                                <input type="file" id="profile-image-input" name="profile_image" accept="image/png, image/jpeg">
                            </div>
                            <div>
                                <button type="submit" class="btn btn-primary" id="upload-image-btn" style="display: none; ">
                                    <span class="spinner-border spinner-border-sm" role="status" style="display: none;"></span>
                                    Upload Image
                                </button>
                                <div class="form-text text-muted">Upload a new JPG or PNG image.</div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Profile Details Form -->
                <div class="bg-slate-800 p-4 rounded-3 border border-slate-700">
                    <form id="profile-details-form">
                        <h4 class="text-white fw-bold mb-3">Personal & Address Details</h4>
                        
                        <!-- Personal Details -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Full Name</label>
                                <!-- FIX: Use null coalescing ?? '' to prevent errors if key is missing or NULL -->
                                <input type="text" class="form-control form-control-dark" id="name" name="name" value="<?php echo htmlspecialchars($user_data['name'] ?? ''); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email Address</label>
                                <!-- FIX: Use null coalescing ?? '' to prevent errors -->
                                <input type="email" class="form-control form-control-dark" id="email" name="email" value="<?php echo htmlspecialchars($user_data['email'] ?? ''); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="mobile" class="form-label">Mobile Number</label>
                                <!-- FIX: Use null coalescing ?? '' to prevent errors -->
                                <input type="tel" class="form-control form-control-dark" id="mobile" name="mobile" value="<?php echo htmlspecialchars($user_data['mobile'] ?? ''); ?>" required>
                            </div>
                        </div>

                        <!-- Address Details -->
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="address1" class="form-label">Address Line 1</label>
                                <input type="text" class="form-control form-control-dark" id="address1" name="address_line_1" value="<?php echo htmlspecialchars($address['address_line_1'] ?? ''); ?>">
                            </div>
                            <div class="col-12">
                                <label for="address2" class="form-label">Address Line 2 <span class="text-muted">(Optional)</span></label>
                                <input type="text" class="form-control form-control-dark" id="address2" name="address_line_2" value="<?php echo htmlspecialchars($address['address_line_2'] ?? ''); ?>">
                            </div>
                            <!-- Province Dropdown -->
                            <div class="col-md-6">
                                <label for="province" class="form-label">Province</label>
                                <select class="form-select form-select-dark" id="province" name="province_id">
                                    <option value="">Select Province</option>
                                    <?php 
                                    $provinces_rs->data_seek(0);
                                    while($province = $provinces_rs->fetch_assoc()): ?>
                                        <option value="<?php echo $province['id']; ?>" <?php echo ($province['id'] == ($address['province_id'] ?? null)) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($province['name']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <!-- District Dropdown -->
                            <div class="col-md-6">
                                <label for="district" class="form-label">District</label>
                                <select class="form-select form-select-dark" id="district" name="district_id" <?php echo !($address['province_id'] ?? null) ? 'disabled' : ''; ?>>
                                    <option value="">Select District</option>
                                    <?php if($districts_rs): ?>
                                        <?php while($district = $districts_rs->fetch_assoc()): ?>
                                            <option value="<?php echo $district['id']; ?>" <?php echo ($district['id'] == ($address['district_id'] ?? null)) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($district['name']); ?>
                                            </option>
                                        <?php endwhile; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <!-- City Dropdown -->
                            <div class="col-md-6">
                                <label for="city" class="form-label">City</label>
                                <select class="form-select form-select-dark" id="city" name="city_id" <?php echo !($address['district_id'] ?? null) ? 'disabled' : ''; ?>>
                                    <option value="">Select City</option>
                                    <?php if($cities_rs): ?>
                                        <?php while($city = $cities_rs->fetch_assoc()): ?>
                                            <option value="<?php echo $city['id']; ?>" <?php echo ($city['id'] == ($address['city_id'] ?? null)) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($city['name']); ?>
                                            </option>
                                        <?php endwhile; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <!-- Zip Code -->
                            <div class="col-md-6">
                                <label for="zip" class="form-label">Zip Code</label>
                                <input type="text" class="form-control form-control-dark" id="zip" name="zip_code" value="<?php echo htmlspecialchars($address['zip_code'] ?? ''); ?>">
                            </div>
                        </div>

                        <hr class="border-slate-700 my-4">

                        <button type="submit" class="btn btn-primary btn-lg" id="save-details-btn">
                            <span class="spinner-border spinner-border-sm" role="status" style="display: none;"></span>
                            Save Changes
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </main>

    <?php include 'footer.php'; ?>

    <!-- Bootstrap JS -->
     <script src="script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            
            // --- Location Dropdown Logic (from checkout) ---
            const provinceSelect = document.getElementById('province');
            const districtSelect = document.getElementById('district');
            const citySelect = document.getElementById('city');

            function fetchLocations(url, selectElement, placeholder) {
                selectElement.disabled = true;
                selectElement.innerHTML = `<option value="">Loading...</option>`;
                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        selectElement.innerHTML = `<option value="">${placeholder}</option>`;
                        if (data.status === 'success' && data.locations.length > 0) {
                            data.locations.forEach(location => {
                                const option = document.createElement('option');
                                option.value = location.id;
                                option.textContent = location.name;
                                selectElement.appendChild(option);
                            });
                            selectElement.disabled = false;
                        } else {
                            selectElement.innerHTML = `<option value="">No locations found</option>`;
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching locations:', error);
                        selectElement.innerHTML = `<option value="">Error loading</option>`;
                    });
            }

            if(provinceSelect) {
                provinceSelect.addEventListener('change', function() {
                    const provinceId = this.value;
                    citySelect.innerHTML = '<option value="">Select City</option>';
                    citySelect.disabled = true;
                    if (provinceId) {
                        fetchLocations(`get_locations.php?province_id=${provinceId}`, districtSelect, 'Select District');
                    } else {
                        districtSelect.innerHTML = '<option value="">Select District</option>';
                        districtSelect.disabled = true;
                    }
                });
            }

            if(districtSelect) {
                districtSelect.addEventListener('change', function() {
                    const districtId = this.value;
                    if (districtId) {
                        fetchLocations(`get_locations.php?district_id=${districtId}`, citySelect, 'Select City');
                    } else {
                        citySelect.innerHTML = '<option value="">Select City</option>';
                        citySelect.disabled = true;
                    }
                });
            }
            
            // --- Alert Function ---
            const alertPlaceholder = document.getElementById('alert-placeholder');
            function showAlert(message, type) {
                const wrapper = document.createElement('div');
                wrapper.innerHTML = [
                    `<div class="alert alert-${type} alert-dismissible" role="alert">`,
                    `   <div>${message}</div>`,
                    '   <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>',
                    '</div>'
                ].join('');
                alertPlaceholder.innerHTML = ''; // Clear previous alerts
                alertPlaceholder.append(wrapper);
                window.scrollTo(0, 0); // Scroll to top to see alert
            }
            
            // --- Profile Details Form (AJAX) ---
            const detailsForm = document.getElementById('profile-details-form');
            const saveBtn = document.getElementById('save-details-btn');
            
            if(detailsForm) {
                detailsForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    setButtonLoading(saveBtn, true);
                    
                    const formData = new FormData(this);
                    
                    fetch('account_update_profile.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            showAlert(data.message, 'success');
                            // If name was updated, refresh header
                            if (data.new_name) {
                                const userNameDiv = document.querySelector('.dropdown-menu .fw-bold');
                                if(userNameDiv) userNameDiv.textContent = data.new_name;
                                const mobileUserNameDiv = document.querySelector('.fd-mobile-user-info .text-light');
                                if(mobileUserNameDiv) mobileUserNameDiv.textContent = data.new_name;
                            }
                        } else {
                            showAlert(data.message, 'danger');
                        }
                    })
                    .catch(error => {
                        showAlert('An error occurred. Please try again.', 'danger');
                        console.error('Error:', error);
                    })
                    .finally(() => {
                        setButtonLoading(saveBtn, false);
                    });
                });
            }
            
            // --- Profile Image Form (AJAX) ---
            const imageForm = document.getElementById('profile-image-form');
            const imageInput = document.getElementById('profile-image-input');
            const imagePreview = document.getElementById('profile-img-preview');
            const uploadBtn = document.getElementById('upload-image-btn');
            
            if (imageInput) {
                imageInput.addEventListener('change', function() {
                    const file = this.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            imagePreview.src = e.target.result;
                        }
                        reader.readAsDataURL(file);
                        uploadBtn.style.display = 'inline-block'; // Show upload button
                    }
                });
            }
            
            if (imageForm) {
                imageForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    setButtonLoading(uploadBtn, true);
                    
                    const formData = new FormData(this);
                    
                    fetch('account_update_image.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            showAlert(data.message, 'success');
                            uploadBtn.style.display = 'none'; // Hide button again
                            
                            // **UPDATE HEADER AVATAR**
                            const newImgSrc = data.new_image_url + '?t=' + new Date().getTime(); // Cache buster
                            imagePreview.src = newImgSrc; // Update profile page preview
                            const headerAvatar = `<img src="${newImgSrc}" alt="User" class="fd-user-avatar-img">`;
                            const mobileAvatar = `<img src="${newImgSrc}" alt="User" class="fd-user-avatar-img-large">`;
                            
                            document.getElementById('header-avatar-container').innerHTML = headerAvatar;
                            document.getElementById('mobile-avatar-container').innerHTML = mobileAvatar;
                            
                        } else {
                            showAlert(data.message, 'danger');
                        }
                    })
                    .catch(error => {
                        showAlert('An error occurred. Please try again.', 'danger');
                        console.error('Error:', error);
                    })
                    .finally(() => {
                        setButtonLoading(uploadBtn, false);
                    });
                });
            }

            // --- Utility Functions ---
            function setButtonLoading(button, isLoading) {
                const spinner = button.querySelector('.spinner-border');
                if (isLoading) {
                    button.disabled = true;
                    if (spinner) spinner.style.display = 'inline-block';
                } else {
                    button.disabled = false;
                    if (spinner) spinner.style.display = 'none';
                }
            }
        });
    </script>
</body>
</html>

