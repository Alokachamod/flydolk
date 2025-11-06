<?php
require 'connection.php';

// 1. Get email and code from URL
$email = $_GET['email'] ?? null;
$code = $_GET['code'] ?? null;

$is_valid_link = false;
$error_message = null;

if ($email && $code) {
    Database::setUpConnection();
    $safe_email = Database::$connection->real_escape_string($email);
    $safe_code = Database::$connection->real_escape_string($code);

    // 2. Check if email and code match in the database
    $rs = Database::search("SELECT * FROM user WHERE email = '$safe_email' AND verification_code = '$safe_code'");
    
    if ($rs->num_rows == 1) {
        $is_valid_link = true;
    } else {
        $error_message = "This password reset link is invalid or has expired.";
    }
} else {
    $error_message = "Invalid password reset link.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password - FlyDolk</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: linear-gradient(to right, #56ccf2, #2f80ed);
        }
        .reset-container {
            background-color: #fff;
            border-radius: 20px;
            box-shadow: 0 14px 28px rgba(0, 0, 0, 0.25), 0 10px 10px rgba(0, 0, 0, 0.22);
            padding: 40px;
            width: 450px;
            max-width: 90%;
            text-align: center;
        }
        .reset-container h1 {
            font-weight: bold;
            margin-bottom: 1.5rem;
        }
        .form-control {
            background-color: #eee;
            border: none;
            padding: 10px 15px;
            margin: 8px 0;
            border-radius: 8px;
        }
        .btn-primary {
            border-radius: 20px;
            border: 1px solid #2f80ed;
            background-color: #2f80ed;
            color: #fff;
            font-size: 12px;
            font-weight: bold;
            padding: 12px 45px;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-top: 15px;
        }
    </style>
</head>
<body>

    <div class="reset-container">
        <img src="imgs/Flydo.png" alt="Logo" height="60" class="mb-3">
        <h1>Set New Password</h1>

        <?php if ($is_valid_link): ?>
            <p class="text-muted">Please enter your new password below. Make sure it's at least 8 characters long.</p>
            
            <div id="resetAlertContainer"></div>

            <form id="resetForm" onsubmit="return false;">
                <!-- Hidden fields to pass email and code -->
                <input type="hidden" id="email" value="<?php echo htmlspecialchars($email); ?>">
                <input type="hidden" id="code" value="<?php echo htmlspecialchars($code); ?>">

                <div class="form-floating mb-3">
                    <input type="password" class="form-control" id="newPassword" placeholder="New Password" required>
                    <label for="newPassword">New Password</label>
                </div>
                <div class="form-floating">
                    <input type="password" class="form-control" id="confirmPassword" placeholder="Confirm New Password" required>
                    <label for="confirmPassword">Confirm New Password</label>
                </div>
                
                <button type="button" class="btn btn-primary" id="resetPassBtn" onclick="updatePassword();">
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display: none;"></span>
                    Update Password
                </button>
            </form>
        
        <?php else: ?>
            <div class="alert alert-danger" role="alert">
                <strong>Error:</strong> <?php echo htmlspecialchars($error_message); ?>
            </div>
            <a href="login-signup.php" class="btn btn-primary">Back to Login</a>
        <?php endif; ?>

    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function updatePassword() {
            const newPass = document.getElementById('newPassword').value;
            const confirmPass = document.getElementById('confirmPassword').value;
            const email = document.getElementById('email').value;
            const code = document.getElementById('code').value;
            
            const btn = document.getElementById('resetPassBtn');
            const spinner = btn.querySelector('.spinner-border');
            const alertContainer = document.getElementById('resetAlertContainer');

            // Client-side validation
            if (newPass.length < 8) {
                alertContainer.innerHTML = '<div class="alert alert-danger" role="alert">Password must be at least 8 characters long.</div>';
                return;
            }
            if (newPass !== confirmPass) {
                alertContainer.innerHTML = '<div class="alert alert-danger" role="alert">Passwords do not match.</div>';
                return;
            }

            // Show loading state
            btn.disabled = true;
            spinner.style.display = 'inline-block';
            alertContainer.innerHTML = '';

            const formData = new FormData();
            formData.append('email', email);
            formData.append('code', code);
            formData.append('new_password', newPass);

            fetch('reset_password_process.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(text => {
                if (text === 'success') {
                    document.getElementById('resetForm').style.display = 'none';
                    alertContainer.innerHTML = '<div class="alert alert-success" role="alert"><strong>Success!</strong> Your password has been updated. You can now log in.</div><a href="login-signup.php" class="btn btn-primary">Back to Login</a>';
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