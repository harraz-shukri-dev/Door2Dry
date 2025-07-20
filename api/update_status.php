<?php
/**
 * Update Status API Endpoint
 * Smart Laundry Pickup & Delivery System
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: PUT, POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/config.php';

// Allow PUT and POST requests
if (!in_array($_SERVER['REQUEST_METHOD'], ['PUT', 'POST'])) {
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

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

// Validate required fields
$required_fields = ['order_id', 'status'];
foreach ($required_fields as $field) {
    if (!isset($input[$field]) || empty($input[$field])) {
        http_response_code(400);
        echo json_encode(['error' => ucfirst($field) . ' is required']);
        exit();
    }
}

$order_id = (int) $input['order_id'];
$new_status = sanitizeInput($input['status']);

// Validate status
$valid_statuses = ['requested', 'picked_up', 'washing', 'delivered'];
if (!in_array($new_status, $valid_statuses)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid status. Must be one of: ' . implode(', ', $valid_statuses)]);
    exit();
}

try {
    $db = getDB();
    $user_role = getUserRole();
    $user_id = $_SESSION['user_id'];
    
    // Get current order details
    $stmt = $db->prepare("SELECT * FROM laundry_orders WHERE id = ?");
    $stmt->execute([$order_id]);
    $order = $stmt->fetch();
    
    if (!$order) {
        http_response_code(404);
        echo json_encode(['error' => 'Order not found']);
        exit();
    }
    
    // Check permissions based on user role
    if ($user_role === 'customer') {
        // Customers can only cancel their own orders (change to requested)
        if ($order['user_id'] != $user_id) {
            http_response_code(403);
            echo json_encode(['error' => 'You can only modify your own orders']);
            exit();
        }
        
        if ($new_status !== 'requested') {
            http_response_code(403);
            echo json_encode(['error' => 'Customers can only request order cancellation']);
            exit();
        }
        
    } elseif ($user_role === 'driver') {
        // Drivers can update status of orders assigned to them or claim unassigned orders
        if ($order['driver_id'] && $order['driver_id'] != $user_id) {
            http_response_code(403);
            echo json_encode(['error' => 'You can only modify orders assigned to you']);
            exit();
        }
        
        // Special case: Taking an order (assigning to self)
        $is_taking_order = !$order['driver_id'] && $order['status'] === 'requested' && $new_status === 'requested';
        
        if ($is_taking_order) {
            // Just assign the driver without changing status
            $stmt = $db->prepare("UPDATE laundry_orders SET driver_id = ? WHERE id = ?");
            if (!$stmt->execute([$user_id, $order_id])) {
                throw new PDOException("Failed to assign order to driver");
            }
        } else {
            // Regular status update
            $current_status = $order['status'];
            $valid_transitions = [
                'requested' => ['picked_up'],
                'picked_up' => ['washing'],
                'washing' => ['delivered'],
                'delivered' => [] // Final status
            ];
            
            if ($current_status === $new_status) {
                http_response_code(400);
                echo json_encode(['error' => 'Order is already in ' . $new_status . ' status']);
                exit();
            }
            
            // Validate status progression
            if ($new_status !== 'requested' && !in_array($new_status, $valid_transitions[$current_status])) {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid status transition from ' . $current_status . ' to ' . $new_status]);
                exit();
            }
            
            // Update order status
            $update_fields = ['status' => $new_status];
            
            // Set delivery date if order is delivered
            if ($new_status === 'delivered') {
                $update_fields['delivery_date'] = date('Y-m-d');
            }
            
            $set_clause = [];
            $values = [];
            foreach ($update_fields as $field => $value) {
                $set_clause[] = "$field = ?";
                $values[] = $value;
            }
            $values[] = $order_id;
            
            $stmt = $db->prepare("UPDATE laundry_orders SET " . implode(', ', $set_clause) . " WHERE id = ?");
            if (!$stmt->execute($values)) {
                throw new PDOException("Failed to update order status");
            }
            
            // Add to order status history
            $stmt = $db->prepare("INSERT INTO order_status_history (order_id, previous_status, new_status, changed_by) VALUES (?, ?, ?, ?)");
            if (!$stmt->execute([$order_id, $current_status, $new_status, $user_id])) {
                throw new PDOException("Failed to record status history");
            }
        }
        
    } else {
        http_response_code(403);
        echo json_encode(['error' => 'Invalid user role']);
        exit();
    }
    
    // Get updated order
    $stmt = $db->prepare("
        SELECT o.*, 
               c.name as customer_name,
               d.name as driver_name
        FROM laundry_orders o
        JOIN users c ON o.user_id = c.id
        LEFT JOIN users d ON o.driver_id = d.id
        WHERE o.id = ?
    ");
    $stmt->execute([$order_id]);
    $updated_order = $stmt->fetch();
    
    // Return success response
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Order status updated successfully',
        'order' => [
            'id' => $updated_order['id'],
            'status' => $updated_order['status'],
            'customer_name' => $updated_order['customer_name'],
            'driver_name' => $updated_order['driver_name'],
            'updated_at' => $updated_order['updated_at']
        ]
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?> 