<?php
/**
 * Get Orders API Endpoint
 * Smart Laundry Pickup & Delivery System
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/config.php';

// Only allow GET requests
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
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

try {
    $db = getDB();
    $user_role = getUserRole();
    $user_id = $_SESSION['user_id'];
    
    if ($user_role === 'customer') {
        // Get orders for the customer
        $status_filter = isset($_GET['status']) ? sanitizeInput($_GET['status']) : null;
        
        $query = "
            SELECT o.*, 
                   d.name as driver_name,
                   d.phone as driver_phone
            FROM laundry_orders o
            LEFT JOIN users d ON o.driver_id = d.id
            WHERE o.user_id = ?";
            
        if ($status_filter) {
            $query .= " AND o.status = ?";
        }
        
        $query .= " ORDER BY o.created_at DESC";
        
        $stmt = $db->prepare($query);
        
        if ($status_filter) {
            $stmt->execute([$user_id, $status_filter]);
        } else {
            $stmt->execute([$user_id]);
        }
        
    } elseif ($user_role === 'driver') {
        // Get orders assigned to the driver or unassigned orders
        $status_filter = isset($_GET['status']) ? sanitizeInput($_GET['status']) : null;
        
        if ($status_filter) {
            if ($status_filter === 'available') {
                // Get unassigned orders
                try {
                    $stmt = $db->prepare("
                        SELECT o.*, 
                               c.name as customer_name,
                               c.phone as customer_phone,
                               c.address as customer_address
                        FROM laundry_orders o
                        JOIN users c ON o.user_id = c.id
                        WHERE o.driver_id IS NULL 
                        AND o.status = 'requested'
                        AND o.created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
                        ORDER BY o.created_at ASC
                    ");
                    
                    if (!$stmt->execute()) {
                        throw new PDOException("Failed to execute available orders query");
                    }
                    
                } catch (PDOException $e) {
                    error_log("Error fetching available orders: " . $e->getMessage());
                    http_response_code(500);
                    echo json_encode([
                        'success' => false,
                        'error' => 'Failed to fetch available orders. Please try again.'
                    ]);
                    exit();
                }
            } else {
                // Get assigned orders with specific status
                $stmt = $db->prepare("
                    SELECT o.*, 
                           c.name as customer_name,
                           c.phone as customer_phone
                    FROM laundry_orders o
                    JOIN users c ON o.user_id = c.id
                    WHERE o.driver_id = ? AND o.status = ?
                    ORDER BY o.created_at DESC
                ");
                $stmt->execute([$user_id, $status_filter]);
            }
        } else {
            // Get all orders assigned to the driver
            $stmt = $db->prepare("
                SELECT o.*, 
                       c.name as customer_name,
                       c.phone as customer_phone
                FROM laundry_orders o
                JOIN users c ON o.user_id = c.id
                WHERE o.driver_id = ?
                ORDER BY 
                    CASE o.status
                        WHEN 'requested' THEN 1
                        WHEN 'picked_up' THEN 2
                        WHEN 'washing' THEN 3
                        WHEN 'delivered' THEN 4
                        ELSE 5
                    END,
                    o.created_at DESC
            ");
            $stmt->execute([$user_id]);
        }
        
    } else {
        http_response_code(403);
        echo json_encode(['error' => 'Invalid user role']);
        exit();
    }
    
    $orders = $stmt->fetchAll();
    
    // Format orders for response
    $formatted_orders = [];
    foreach ($orders as $order) {
        $formatted_order = [
            'id' => $order['id'],
            'user_id' => $order['user_id'],
            'driver_id' => $order['driver_id'],
            'address' => $order['address'],
            'address_lat' => $order['address_lat'],
            'address_lng' => $order['address_lng'],
            'items' => $order['items'],
            'special_notes' => $order['special_notes'],
            'status' => $order['status'],
            'pickup_date' => $order['pickup_date'],
            'pickup_time' => $order['pickup_time'],
            'delivery_date' => $order['delivery_date'],
            'created_at' => $order['created_at'],
            'updated_at' => $order['updated_at']
        ];
        
        if ($user_role === 'customer') {
            $formatted_order['driver_name'] = $order['driver_name'];
            $formatted_order['driver_phone'] = $order['driver_phone'];
        } else {
            $formatted_order['customer_name'] = $order['customer_name'];
            $formatted_order['customer_phone'] = $order['customer_phone'];
        }
        
        $formatted_orders[] = $formatted_order;
    }
    
    // Return success response
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'orders' => $formatted_orders,
        'count' => count($formatted_orders)
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?> 