<?php
/**
 * Rate Order API Endpoint
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
    http_response_code(401);
    echo json_encode(['error' => 'Authentication required']);
    exit();
}

// Check if user is a customer
if (getUserRole() !== 'customer') {
    http_response_code(403);
    echo json_encode(['error' => 'Only customers can rate orders']);
    exit();
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

// Validate required fields
$required_fields = ['order_id', 'rating'];
foreach ($required_fields as $field) {
    if (!isset($input[$field]) || empty($input[$field])) {
        http_response_code(400);
        echo json_encode(['error' => ucfirst($field) . ' is required']);
        exit();
    }
}

$order_id = (int) $input['order_id'];
$rating = (int) $input['rating'];
$comment = isset($input['comment']) ? sanitizeInput($input['comment']) : null;

// Validate rating
if ($rating < 1 || $rating > 5) {
    http_response_code(400);
    echo json_encode(['error' => 'Rating must be between 1 and 5']);
    exit();
}

try {
    $db = getDB();
    $user_id = $_SESSION['user_id'];
    
    // Check if order exists and belongs to the customer
    $stmt = $db->prepare("SELECT * FROM laundry_orders WHERE id = ? AND user_id = ?");
    $stmt->execute([$order_id, $user_id]);
    $order = $stmt->fetch();
    
    if (!$order) {
        http_response_code(404);
        echo json_encode(['error' => 'Order not found or does not belong to you']);
        exit();
    }
    
    // Check if order is delivered
    if ($order['status'] !== 'delivered') {
        http_response_code(400);
        echo json_encode(['error' => 'You can only rate delivered orders']);
        exit();
    }
    
    // Check if order is already rated
    $stmt = $db->prepare("SELECT id FROM ratings WHERE order_id = ?");
    $stmt->execute([$order_id]);
    
    if ($stmt->fetch()) {
        http_response_code(409);
        echo json_encode(['error' => 'Order has already been rated']);
        exit();
    }
    
    // Insert rating
    $stmt = $db->prepare("INSERT INTO ratings (order_id, rating, comment) VALUES (?, ?, ?)");
    $stmt->execute([$order_id, $rating, $comment]);
    
    $rating_id = $db->lastInsertId();
    
    // Get the inserted rating with order details
    $stmt = $db->prepare("
        SELECT r.*, 
               o.id as order_id,
               o.status as order_status,
               d.name as driver_name
        FROM ratings r
        JOIN laundry_orders o ON r.order_id = o.id
        LEFT JOIN users d ON o.driver_id = d.id
        WHERE r.id = ?
    ");
    $stmt->execute([$rating_id]);
    $rating_data = $stmt->fetch();
    
    // Return success response
    http_response_code(201);
    echo json_encode([
        'success' => true,
        'message' => 'Rating submitted successfully',
        'rating' => [
            'id' => $rating_data['id'],
            'order_id' => $rating_data['order_id'],
            'rating' => $rating_data['rating'],
            'comment' => $rating_data['comment'],
            'driver_name' => $rating_data['driver_name'],
            'created_at' => $rating_data['created_at']
        ]
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?> 