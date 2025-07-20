<?php
require_once '../config/config.php';

// Redirect if already logged in
if (isLoggedIn() && getUserRole() === 'driver') {
    header('Location: dashboard.php');
    exit();
}

// Handle logout message
$logout_message = '';
if (isset($_GET['logout']) && $_GET['logout'] == '1') {
    $logout_message = 'You have been logged out successfully.';
}

// Handle timeout message
$timeout_message = '';
if (isset($_GET['timeout']) && $_GET['timeout'] == '1') {
    $timeout_message = 'Your session has expired. Please log in again.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Driver Login - Door2Dry</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="auth-body driver-theme">
    <div id="alert-container"></div>
    
    <!-- Background Elements -->
    <div class="auth-background">
        <div class="auth-shapes">
            <div class="auth-shape auth-shape-1"></div>
            <div class="auth-shape auth-shape-2"></div>
            <div class="auth-shape auth-shape-3"></div>
        </div>
    </div>

    <!-- Header -->
    <header class="auth-header">
        <div class="container">
            <a href="../index.php" class="auth-logo">
                <i class="fas fa-tshirt"></i>
                <span>Door2Dry</span>
            </a>
            <nav class="auth-nav">
                <a href="../customer/login.php" class="nav-link">
                    <i class="fas fa-user"></i>
                    Customer Portal
                </a>
            </nav>
        </div>
    </header>
    
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-card-header">
                <div class="auth-icon driver-icon">
                    <i class="fas fa-truck"></i>
                </div>
                <h1 class="auth-title">Driver Portal</h1>
                <p class="auth-subtitle">Access your delivery dashboard</p>
            </div>

            <div class="auth-card-body">
                <?php if ($logout_message): ?>
                    <div class="auth-alert success">
                        <i class="fas fa-check-circle"></i>
                        <span><?php echo $logout_message; ?></span>
                    </div>
                <?php endif; ?>
                
                <?php if ($timeout_message): ?>
                    <div class="auth-alert warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span><?php echo $timeout_message; ?></span>
                    </div>
                <?php endif; ?>
                
                <form id="login-form" class="auth-form">
                    <div class="form-field">
                        <label for="email" class="field-label">Email Address</label>
                        <div class="field-wrapper">
                            <i class="fas fa-envelope field-icon"></i>
                            <input type="email" class="field-input" id="email" name="email" placeholder="Enter your email" required>
                        </div>
                    </div>
                    
                    <div class="form-field">
                        <label for="password" class="field-label">Password</label>
                        <div class="field-wrapper">
                            <i class="fas fa-lock field-icon"></i>
                            <input type="password" class="field-input" id="password" name="password" placeholder="Enter your password" required>
                            <button type="button" class="field-toggle" onclick="togglePassword()">
                                <i class="fas fa-eye" id="password-toggle-icon"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-options">
                        <label class="checkbox-wrapper">
                            <input type="checkbox" class="checkbox-input">
                            <span class="checkbox-checkmark"></span>
                            <span class="checkbox-label">Remember me</span>
                        </label>
                        <a href="#" class="forgot-link">Forgot Password?</a>
                    </div>
                    
                    <button type="submit" class="auth-btn primary driver-btn">
                        <span class="btn-text">Access Dashboard</span>
                        <i class="fas fa-arrow-right btn-icon"></i>
                    </button>
                </form>
            </div>

            <div class="auth-card-footer">
                <div class="auth-divider">
                    <span>Need a driver account?</span>
                </div>
                <p class="driver-contact">
                    <i class="fas fa-phone"></i>
                    Contact your administrator or support team
                </p>
            </div>
        </div>
    </div>

    <script src="../assets/js/app.js"></script>
    <script>
        // Password toggle functionality
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('password-toggle-icon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.className = 'fas fa-eye-slash';
            } else {
                passwordInput.type = 'password';
                toggleIcon.className = 'fas fa-eye';
            }
        }

        // Form submission
        document.getElementById('login-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const email = formData.get('email');
            const password = formData.get('password');
            
            // Validate form
            const validationRules = {
                email: {
                    required: 'Email is required',
                    email: 'Please enter a valid email address'
                },
                password: {
                    required: 'Password is required'
                }
            };
            
            if (!FormValidator.validateForm('login-form', validationRules)) {
                return;
            }
            
            try {
                const submitBtn = this.querySelector('button[type="submit"]');
                const btnText = submitBtn.querySelector('.btn-text');
                const btnIcon = submitBtn.querySelector('.btn-icon');
                
                // Update button state
                submitBtn.disabled = true;
                submitBtn.classList.add('loading');
                btnText.textContent = 'Accessing...';
                btnIcon.className = 'fas fa-spinner fa-spin btn-icon';
                
                const response = await API.login(email, password);
                
                if (response.success) {
                    // Check if user is a driver
                    if (response.user.role === 'driver') {
                        btnText.textContent = 'Success!';
                        btnIcon.className = 'fas fa-check btn-icon';
                        Utils.showAlert('Login successful! Redirecting...', 'success');
                        
                        setTimeout(() => {
                            window.location.href = 'dashboard.php';
                        }, 1000);
                    } else {
                        Utils.showAlert('Access denied. This is the driver portal.', 'danger');
                        resetButton();
                    }
                } else {
                    Utils.showAlert(response.message || 'Login failed', 'danger');
                    resetButton();
                }
            } catch (error) {
                Utils.showAlert('Login failed: ' + error.message, 'danger');
                resetButton();
            }
            
            function resetButton() {
                const submitBtn = document.querySelector('button[type="submit"]');
                const btnText = submitBtn.querySelector('.btn-text');
                const btnIcon = submitBtn.querySelector('.btn-icon');
                
                submitBtn.disabled = false;
                submitBtn.classList.remove('loading');
                btnText.textContent = 'Access Dashboard';
                btnIcon.className = 'fas fa-arrow-right btn-icon';
            }
        });

        // Add floating animation to auth shapes
        document.addEventListener('DOMContentLoaded', function() {
            const shapes = document.querySelectorAll('.auth-shape');
            shapes.forEach((shape, index) => {
                shape.style.animationDelay = `${index * 2}s`;
            });
        });
    </script>
</body>
</html> 