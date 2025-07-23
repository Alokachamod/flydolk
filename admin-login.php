<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Portal | Login</title>
    <!-- Bootstrap CSS (for grid system) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Favicon -->
    <link rel="icon" href="imgs/Flydo.png" type="image/png">
    <style>
        /* Custom styles for the animated admin login page */
        :root {
            --primary-color: #4a5568;
            --secondary-color: #2d3748;
            --accent-color: #6366f1;
            --light-gray: #f7fafc;
            --text-color: #4a5568;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--light-gray);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 1rem;
        }

        .login-wrapper {
            width: 100%;
            max-width: 900px;
            min-height: 550px;
            display: flex;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            background-color: #fff;
            animation: fadeIn 0.8s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.95);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .login-banner {
            flex: 1;
            background: linear-gradient(45deg, #4f46e5, #6366f1, #818cf8, #a5b4fc);
            background-size: 400% 400%;
            animation: gradientAnimation 15s ease infinite;
            color: #fff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 2rem;
            text-align: center;
        }
        
        @keyframes gradientAnimation {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .login-banner h1 {
            font-weight: 700;
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .login-banner p {
            font-size: 1.1rem;
            max-width: 300px;
        }

        .login-form-container {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 3rem;
        }

        .login-form-container h2 {
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
            animation: slideUp 0.6s ease-out;
        }
        
        .login-form-container .welcome-text {
            color: #718096;
            margin-bottom: 2rem;
            animation: slideUp 0.7s ease-out;
        }

        .input-group {
            position: relative;
            animation: slideUp 0.8s ease-out;
        }

        .form-control {
            height: 50px;
            padding-left: 45px; /* Space for icon */
            padding-right: 45px; /* Space for toggle icon */
            font-size: 1rem;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            background-color: #f7fafc;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
            border-color: var(--accent-color);
            background-color: #fff;
        }

        .input-group .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #a0aec0;
            transition: color 0.3s;
            z-index: 2; /* Ensure icon is above input background */
        }
        
        /* --- FIX: Use :focus-within for robust icon highlighting --- */
        .input-group:focus-within .input-icon {
            color: var(--accent-color);
        }

        .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #a0aec0;
            cursor: pointer;
            transition: color 0.3s;
            z-index: 2; /* Ensure icon is above input background */
        }
        
        .toggle-password:hover {
            color: var(--accent-color);
        }

        .btn-primary {
            background-color: var(--accent-color);
            border: none;
            padding: 14px;
            font-weight: 600;
            font-size: 1rem;
            border-radius: 10px;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.2);
            animation: slideUp 0.9s ease-out;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 7px 20px rgba(99, 102, 241, 0.3);
        }
        
        .wave-emoji {
            display: inline-block;
            animation: waveAnimation 2s infinite;
            transform-origin: 70% 70%;
        }

        @keyframes waveAnimation {
            0% { transform: rotate(0deg); }
            10% { transform: rotate(14deg); }
            20% { transform: rotate(-8deg); }
            30% { transform: rotate(14deg); }
            40% { transform: rotate(-4deg); }
            50% { transform: rotate(10deg); }
            60% { transform: rotate(0deg); }
            100% { transform: rotate(0deg); }
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @media (max-width: 768px) {
            .login-banner {
                display: none;
            }
            .login-wrapper {
                flex-direction: column;
            }
             .login-form-container {
                padding: 2rem;
            }
        }

    </style>
</head>
<body>

    <div class="login-wrapper">
        <div class="login-banner">
            <i class="fas fa-shield-halved fa-5x mb-4"></i>
            <h1>Admin Secure Area</h1>
            <p>Access to this portal is restricted. Please authenticate to continue.</p>
        </div>
        <div class="login-form-container">
            <h2>Welcome Back! <span class="wave-emoji">👋</span></h2>
            <p class="welcome-text">Please sign in to access the dashboard.</p>
            
            <div class="col-12 d-none mb-3" id="loginMsgDiv">
                <div class="alert" role="alert" id="loginAlertDiv">
                   <span id="loginMsg"></span>
                </div>
            </div>

            <form id="adminLoginForm" onsubmit="return false;">
                <div class="mb-3 input-group">
                    <input type="email" class="form-control" id="adminEmail" placeholder="Email Address" required>
                    <i class="fas fa-envelope input-icon"></i>
                </div>
                <div class="mb-4 input-group">
                    <input type="password" class="form-control" id="adminPassword" placeholder="Password" required>
                    <i class="fas fa-lock input-icon"></i>
                    <i class="fas fa-eye-slash toggle-password" id="togglePassword"></i>
                </div>
                <button type="submit" class="btn btn-primary w-100" onclick="loginAdmin()">Secure Sign In</button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script.js"></script>
</body>
</html>
