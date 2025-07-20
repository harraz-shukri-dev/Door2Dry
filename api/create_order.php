<?php
/**
 * Create Order API Endpoint
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
    echo json_encode(['error' => 'Only customers can create orders']);
    exit();
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

// Validate required fields
$required_fields = ['address', 'items'];
foreach ($required_fields as $field) {
    if (!isset($input[$field]) || empty($input[$field])) {
        http_response_code(400);
        echo json_encode(['error' => ucfirst($field) . ' is required']);
        exit();
    }
}

$address = sanitizeInput($input['address']);
$items = sanitizeInput($input['items']);
$special_notes = isset($input['special_notes']) ? sanitizeInput($input['special_notes']) : null;
$pickup_date = isset($input['pickup_date']) ? sanitizeInput($input['pickup_date']) : null;
$pickup_time = isset($input['pickup_time']) ? sanitizeInput($input['pickup_time']) : null;
$address_lat = isset($input['address_lat']) && is_numeric($input['address_lat']) ? (float)$input['address_lat'] : null;
$address_lng = isset($input['address_lng']) && is_numeric($input['address_lng']) ? (float)$input['address_lng'] : null;

// Validate pickup date if provided
if ($pickup_date && !strtotime($pickup_date)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid pickup date format']);
    exit();
}

// Validate pickup time if provided
if ($pickup_time) {
    if (!preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $pickup_time)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid pickup time format. Use HH:MM format']);
        exit();
    }
}

try {
    $db = getDB();
    
    // Insert new order
    $stmt = $db->prepare("INSERT INTO laundry_orders (user_id, address, address_lat, address_lng, items, special_notes, pickup_date, pickup_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$_SESSION['user_id'], $address, $address_lat, $address_lng, $items, $special_notes, $pickup_date, $pickup_time]);
    
    $order_id = $db->lastInsertId();
    
    // Add to order status history
    $stmt = $db->prepare("INSERT INTO order_status_history (order_id, new_status, changed_by) VALUES (?, 'requested', ?)");
    $stmt->execute([$order_id, $_SESSION['user_id']]);
    
    // Get the created order with user info
    $stmt = $db->prepare("
        SELECT o.*, u.name as customer_name, u.email as customer_email 
        FROM laundry_orders o 
        JOIN users u ON o.user_id = u.id 
        WHERE o.id = ?
    ");
    $stmt->execute([$order_id]);
    $order = $stmt->fetch();
    
    // Return success response
    http_response_code(201);
    echo json_encode([
        'success' => true,
        'message' => 'Order created successfully',
        'order' => [
            'id' => $order['id'],
            'address' => $order['address'],
            'items' => $order['items'],
            'special_notes' => $order['special_notes'],
            'status' => $order['status'],
            'pickup_date' => $order['pickup_date'],
            'pickup_time' => $order['pickup_time'],
            'created_at' => $order['created_at'],
            'customer_name' => $order['customer_name']
        ]
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?> 