<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FlydoLK | Login & Sign Up</title>
    <link rel="icon" href="imgs/Flydo.png">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/a81368914c.js" crossorigin="anonymous"></script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <!-- Favicon -->
    <link rel="icon" href="imgs/Flydo.png" type="image/png">
    <style>
        /* Basic Reset and Styling */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Poppins', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: linear-gradient(to right, #56ccf2, #2f80ed);
            overflow: hidden;
        }

        /* Main Container */
        .login-container {
            background-color: #fff;
            border-radius: 20px;
            box-shadow: 0 14px 28px rgba(0, 0, 0, 0.25), 0 10px 10px rgba(0, 0, 0, 0.22);
            position: relative;
            overflow: hidden;
            width: 768px;
            max-width: 100%;
            min-height: 520px;
            /* Increased height for more fields */
        }

        /* Form Container Styling */
        .form-container {
            position: absolute;
            top: 0;
            height: 100%;
            transition: all 0.6s ease-in-out;
        }

        .sign-in-container {
            left: 0;
            width: 50%;
            z-index: 2;
        }

        .sign-up-container {
            left: 0;
            width: 50%;
            opacity: 0;
            z-index: 1;
        }

        /* Active State Transitions */
        .login-container.right-panel-active .sign-in-container {
            transform: translateX(100%);
        }

        .login-container.right-panel-active .sign-up-container {
            transform: translateX(100%);
            opacity: 1;
            z-index: 5;
            animation: show 0.6s;
        }

        @keyframes show {

            0%,
            49.99% {
                opacity: 0;
                z-index: 1;
            }

            50%,
            100% {
                opacity: 1;
                z-index: 5;
            }
        }

        /* Form Elements */
        form {
            background-color: #fff;
            display: flex;
            flex-direction: column;
            padding: 0 40px;
            height: 100%;
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        h1 {
            font-weight: bold;
            margin: 0;
            margin-bottom: 1rem;
        }

        span {
            font-size: 12px;
            margin-bottom: 10px;
        }

        a {
            color: #333;
            font-size: 14px;
            text-decoration: none;
            margin: 15px 0;
        }

        a.forgot-password {
            transition: color 0.2s ease;
        }

        a.forgot-password:hover {
            color: #2f80ed;
        }


        input,
        select {
            background-color: #eee;
            border: none;
            padding: 10px 15px;
            margin: 6px 0;
            width: 100%;
            border-radius: 8px;
            outline: none;
            font-size: 14px;
        }

        .name-container {
            display: flex;
            gap: 8px;
            /* Adds space between the inputs */
            width: 100%;
        }

        button {
            border-radius: 20px;
            border: 1px solid #2f80ed;
            background-color: #2f80ed;
            color: #fff;
            font-size: 12px;
            font-weight: bold;
            padding: 12px 45px;
            letter-spacing: 1px;
            text-transform: uppercase;
            transition: transform 80ms ease-in;
            cursor: pointer;
            margin-top: 10px;
        }

        button:active {
            transform: scale(0.95);
        }

        button:focus {
            outline: none;
        }

        button.ghost {
            background-color: transparent;
            border-color: #fff;
        }

        /* Overlay Container */
        .overlay-container {
            position: absolute;
            top: 0;
            left: 50%;
            width: 50%;
            height: 100%;
            overflow: hidden;
            transition: transform 0.6s ease-in-out;
            z-index: 100;
        }

        .login-container.right-panel-active .overlay-container {
            transform: translateX(-100%);
        }

        .overlay {
            background: #2f80ed;
            background: linear-gradient(to right, #56ccf2, #2f80ed);
            background-repeat: no-repeat;
            background-size: cover;
            background-position: 0 0;
            color: #fff;
            position: relative;
            left: -100%;
            height: 100%;
            width: 200%;
            transform: translateX(0);
            transition: transform 0.6s ease-in-out;
        }

        .login-container.right-panel-active .overlay {
            transform: translateX(50%);
        }

        .overlay-panel {
            position: absolute;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            padding: 0 40px;
            text-align: center;
            top: 0;
            height: 100%;
            width: 50%;
            transform: translateX(0);
            transition: transform 0.6s ease-in-out;
        }

        .overlay-panel img {
            margin-bottom: 1rem;
        }

        .overlay-panel p {
            font-size: 14px;
            font-weight: 300;
            line-height: 20px;
            letter-spacing: 0.5px;
            margin: 20px 0 30px;
        }

        .overlay-left {
            transform: translateX(-20%);
        }

        .login-container.right-panel-active .overlay-left {
            transform: translateX(0);
        }

        .overlay-right {
            right: 0;
            transform: translateX(0);
        }

        .login-container.right-panel-active .overlay-right {
            transform: translateX(20%);
        }

        /* Bootstrap Responsive Overrides */
        @media (max-width: 767.98px) {
            .login-container {
                min-height: 100vh;
                border-radius: 0;
            }

            .overlay-container {
                display: none;
            }

            .form-container {
                width: 100%;
                position: relative;
                /* Change position for mobile flow */
            }

            .sign-in-container {
                opacity: 1;
                z-index: 2;
            }

            .sign-up-container {
                opacity: 0;
                z-index: 1;
            }

            .login-container.right-panel-active .sign-in-container {
                transform: none;
                opacity: 0;
                z-index: 1;
            }

            .login-container.right-panel-active .sign-up-container {
                transform: none;
                opacity: 1;
                z-index: 5;
                animation: none;
            }
        }
    </style>
</head>

<body>

    <div class="login-container" id="container">
        <!-- Sign Up Form -->
        <div class="form-container sign-up-container">
            <!-- Your existing sign-up form -->
            <form action="#">
                <h1>Create Account</h1>
                <br>
                <div class="col-12 d-none" id="msgdiv">
                    <div class="alert alert-danger" role="alert" id="alertdiv">
                        <i class="bi bi-x-octagon-fill fs-5" id="msg">

                        </i>
                    </div>
                </div>
                <input type="text" placeholder="Name" id="name" />
                <input type="email" placeholder="Email" id="email" />
                <input type="password" placeholder="Password" id="password" />
                <input type="tel" placeholder="Mobile" id="mobile" />
                <button type="button" onclick="signUp();">Sign Up</button> <!-- Added type="button" -->
                <a href="#" class="d-md-none mt-3" id="signInMobile">Already have an account? Sign In</a>
            </form>
        </div>

        <!-- Sign In Form -->
        <div class="form-container sign-in-container">
             <!-- Your existing sign-in form -->
            <form action="#">
                <h1>Sign in</h1>
                <span>or use your account</span>
                <br>
                <input type="email" placeholder="Email" id="e"/>
                <input type="password" placeholder="Password" id="p"/>
                <!-- UPDATED LINK: Opens the modal -->
                <a href="#" class="forgot-password" data-bs-toggle="modal" data-bs-target="#forgotPasswordModal">Forgot your password?</a>
                <button type="button" onclick="signin();">Sign In</button> <!-- Added type="button" -->
                <a href="#" class="d-md-none mt-3" id="signUpMobile">Don't have an account? Sign Up</a>
            </form>
        </div>

        <!-- Overlay for sliding animation -->
        <div class="overlay-container d-none d-md-block">
            <div class="overlay">
                <!-- Left Overlay Panel -->
                <div class="overlay-panel overlay-left">
                    <!-- Replace this with your actual logo -->
                    <img src="imgs/Flydo.png" alt="Logo" height="75">
                    <h1>Welcome Back!</h1>
                    <p>To keep connected with us please login with your personal info</p>
                    <button class="ghost" id="signIn">Sign In</button>
                </div>
                <!-- Right Overlay Panel -->
                <div class="overlay-panel overlay-right">
                    <!-- Replace this with your actual logo -->
                    <img src="imgs/Flydo.png" alt="Logo" height="75">
                    <h1>Hello, Friend!</h1>
                    <p>Enter your personal details and start your journey with us</p>
                    <button class="ghost" id="signUp">Sign Up</button>
                </div>
            </div>
        </div>
    </div>

    <!-- NEW: Forgot Password Modal -->
    <div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-labelledby="forgotPasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="forgotPasswordModalLabel">Reset Your Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted">Enter the email address associated with your account, and we'll send you a link to reset your password.</p>
                    <div class="mb-3">
                        <label for="forgotEmail" class="form-label">Email address</label>
                        <input type="email" class="form-control" id="forgotEmail" placeholder="name@example.com">
                    </div>
                    <!-- Alert for messages -->
                    <div id="forgotAlertContainer"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="sendResetLinkBtn" onclick="sendResetLink();">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display: none;"></span>
                        Send Reset Link
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- END NEW MODAL -->


    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- UPDATED: Use your existing script.js -->
    <script src="script.js"></script>
    
    <script>
        const signUpButton = document.getElementById('signUp');
        const signInButton = document.getElementById('signIn');
        const container = document.getElementById('container');

        const signUpButtonMobile = document.getElementById('signUpMobile');
        const signInButtonMobile = document.getElementById('signInMobile');

        // Desktop Toggle
        if (signUpButton && signInButton) {
            signUpButton.addEventListener('click', () => {
                container.classList.add('right-panel-active');
            });

            signInButton.addEventListener('click', () => {
                container.classList.remove('right-panel-active');
            });
        }

        // Mobile Toggle
        signUpButtonMobile.addEventListener('click', (e) => {
            e.preventDefault();
            container.classList.add('right-panel-active');
        });

        signInButtonMobile.addEventListener('click', (e) => {
            e.preventDefault();
            container.classList.remove('right-panel-active');
        });

        // --- NEW SCRIPT.JS FUNCTIONS ---
        // (You can merge this with your existing script.js)

        // Sign In (from your existing file)
        function signin() {
            var email = document.getElementById("e").value;
            var password = document.getElementById("p").value;
            
            var f = new FormData();
            f.append("e", email);
            f.append("p", password);

            var r = new XMLHttpRequest();
            r.onreadystatechange = function() {
                if (r.readyState == 4 && r.status == 200) {
                    var t = r.responseText;
                    if (t == "success") {
                        Swal.fire({
                            icon: 'success',
                            title: 'Signed In Successfully',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            window.location = "index.php";
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: t
                        });
                    }
                }
            };
            r.open("POST", "signInProcess.php", true);
            r.send(f);
        }

        // Sign Up (from your existing file)
        function signUp() {
            var name = document.getElementById("name").value;
            var email = document.getElementById("email").value;
            var password = document.getElementById("password").value;
            var mobile = document.getElementById("mobile").value;

            var f = new FormData();
            f.append("n", name);
            f.append("e", email);
            f.append("p", password);
            f.append("m", mobile);

            var r = new XMLHttpRequest();
            r.onreadystatechange = function() {
                if (r.readyState == 4 && r.status == 200) {
                    var t = r.responseText;
                    if (t == "success") {
                        Swal.fire({
                            icon: 'success',
                            title: 'Account Created',
                            text: 'You can now sign in.',
                            showConfirmButton: false,
                            timer: 2000
                        }).then(() => {
                            // Automatically switch to sign-in view
                            container.classList.remove('right-panel-active');
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: t
                        });
                    }
                }
            };
            r.open("POST", "signupprocess.php", true);
            r.send(f);
        }


        // --- NEW: Forgot Password Function ---
        function sendResetLink() {
            const email = document.getElementById('forgotEmail').value;
            const btn = document.getElementById('sendResetLinkBtn');
            const spinner = btn.querySelector('.spinner-border');
            const alertContainer = document.getElementById('forgotAlertContainer');

            // Show loading state
            btn.disabled = true;
            spinner.style.display = 'inline-block';
            alertContainer.innerHTML = '';

            const formData = new FormData();
            formData.append('email', email);

            fetch('forgot_password_process.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(text => {
                if (text === 'success') {
                    alertContainer.innerHTML = '<div class="alert alert-success" role="alert">Success! Please check your email for the reset link.</div>';
                    btn.style.display = 'none'; // Hide button after success
                } else {
                    alertContainer.innerHTML = `<div class="alert alert-danger" role="alert">${text}</div>`;
                    btn.disabled = false;
                    spinner.style.display = 'none';
                }
            })
            .catch(error => {
                alertContainer.innerHTML = '<div class="alert alert-danger" role="alert">A connection error occurred. Please try again.</div>';
                btn.disabled = false;
                spinner.style.display = 'none';
            });
        }
    </script>

</body>

</html>