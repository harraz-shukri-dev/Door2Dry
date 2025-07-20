<?php
/**
 * Application Configuration
 * Smart Laundry Pickup & Delivery System
 */

// Session Configuration (must be set before session_start())
ini_set('session.use_cookies', 1);
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 0); // Set to 1 for HTTPS

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database configuration
require_once 'database.php';

// Application settings
define('APP_NAME', 'Door2Dry - Smart Laundry Service');
define('APP_VERSION', '1.0.0');
define('APP_URL', 'http://localhost/door2dry/');

// Security settings
define('SESSION_TIMEOUT', 3600); // 1 hour in seconds
define('SALT', 'door2dry_salt_2024'); // Change this in production

// API settings
define('API_BASE_URL', APP_URL . 'api/');

// External API keys (replace with your actual keys)
define('GOOGLE_MAPS_API_KEY', 'AIzaSyBz4qaNhEUMon1Xok7RdOW7XwUU32Jibrs');

// File upload settings
define('MAX_FILE_SIZE', 5242880); // 5MB
define('UPLOAD_DIR', '../uploads/');

// Email settings (for notifications)
define('MAIL_FROM', 'noreply@door2dry.com');
define('MAIL_FROM_NAME', 'Door2Dry System');

// Utility functions
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function getUserRole() {
    return isset($_SESSION['user_role']) ? $_SESSION['user_role'] : null;
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}

function requireRole($role) {
    requireLogin();
    if (getUserRole() !== $role) {
        header('Location: unauthorized.php');
        exit();
    }
}

function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function checkSessionTimeout() {
    if (isLoggedIn()) {
        $inactive = time() - $_SESSION['last_activity'];
        if ($inactive >= SESSION_TIMEOUT) {
            session_unset();
            session_destroy();
            header('Location: login.php?timeout=1');
            exit();
        }
        $_SESSION['last_activity'] = time();
    }
}

// Initialize session timeout check
checkSessionTimeout();

// Set timezone
date_default_timezone_set('UTC');

// Error reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Google Maps API Configuration
// Note: Replace with your actual Google Maps API key
// Enable the following APIs in Google Console:
// - Maps JavaScript API
// - Places API
// - Geocoding API
// - Routes API
?> 