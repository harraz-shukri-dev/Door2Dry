<?php
/**
 * Logout API Endpoint
 * Smart Laundry Pickup & Delivery System
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/config.php';

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit();
}

// Check if user is logged in
if (!isLoggedIn()) {
    http_response_code(400);
    echo json_encode(['error' => 'No active session to logout']);
    exit();
}

// Clear all session variables
session_unset();

// Destroy the session
session_destroy();

// Return success response
http_response_code(200);
echo json_encode([
    'success' => true,
    'message' => 'Logout successful'
]);
?> 