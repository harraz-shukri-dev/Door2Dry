<?php
require_once '../config/config.php';

// Require driver login
requireRole('driver');

$user_name = $_SESSION['user_name'];
$user_email = $_SESSION['user_email'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Driver Dashboard - Door2Dry</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Add SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-material-ui/material-ui.css" rel="stylesheet">
    <!-- Add SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            display: flex;
            align-items: flex-start;
            gap: 15px;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .stat-card:nth-child(1) .stat-icon {
            background: #fff3cd;
            color: #ffc107;
        }

        .stat-card:nth-child(2) .stat-icon {
            background: #cfe2ff;
            color: #0d6efd;
        }

        .stat-card:nth-child(3) .stat-icon {
            background: #d1e7dd;
            color: #198754;
        }

        .stat-card:nth-child(4) .stat-icon {
            background: #f8d7da;
            color: #dc3545;
        }

        .stat-content {
            flex: 1;
        }

        .stat-content h3 {
            margin: 0 0 5px 0;
            font-size: 1.1rem;
            color: #333;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 600;
            margin: 10px 0;
            color: #666;
        }

        .stat-number.has-orders {
            color: #0d6efd;
        }

        .stat-content p {
            margin: 0 0 15px 0;
            color: #666;
            font-size: 0.9rem;
        }

        .stat-content .btn {
            width: 100%;
            padding: 8px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .map-section {
            margin-bottom: 30px;
        }

        .map-container {
            position: relative;
            border-radius: 12px;
            overflow: hidden;
            height: 400px;
        }

        .map-element {
            width: 100%;
            height: 100%;
        }

        .route-info {
            position: absolute;
            top: 20px;
            right: 20px;
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            max-width: 250px;
            z-index: 1;
        }

        .route-info-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
            color: #333;
        }

        .route-info-header h3 {
            margin: 0;
            font-size: 1rem;
        }

        .route-details {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .route-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.9rem;
            color: #666;
        }

        .route-item strong {
            color: #333;
        }

        .orders-section {
            margin-bottom: 30px;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .section-title {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #333;
        }

        .section-title h2 {
            margin: 0;
            font-size: 1.2rem;
        }

        .section-actions {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .section-actions .btn,
        .section-actions .control-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .section-actions .control-btn {
            background: white;
            border: 1px solid #ddd;
            color: #666;
        }

        .section-actions .control-btn:hover {
            background: #f8f9fa;
            border-color: #aaa;
        }

        .map-controls {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .control-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 6px;
            border: 1px solid #ddd;
            background: white;
            color: #666;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .control-btn:hover {
            background: #f8f9fa;
            border-color: #aaa;
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }

            .map-controls {
                flex-direction: column;
                width: 100%;
            }

            .control-btn {
                width: 100%;
                justify-content: center;
            }
        }

        .orders-list {
            display: grid;
            gap: 20px;
            margin-top: 20px;
        }

        .order-preview {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .order-preview:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }

        .order-preview-header {
            padding: 15px 20px;
            background: #f8f9fa;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .order-preview-id {
            font-weight: 600;
            color: #333;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .order-preview-content {
            padding: 20px;
        }

        .order-preview-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .info-block {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .info-block-label {
            font-size: 0.9rem;
            color: #666;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .info-block-value {
            font-size: 1rem;
            color: #333;
            font-weight: 500;
        }

        .order-preview-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .empty-state .empty-icon {
            font-size: 3rem;
            color: #adb5bd;
            margin-bottom: 20px;
        }

        .empty-state h3 {
            color: #495057;
            margin-bottom: 10px;
            font-size: 1.2rem;
        }

        .empty-state p {
            color: #6c757d;
            margin-bottom: 20px;
        }

        .loading-spinner {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .spinner {
            display: flex;
            gap: 6px;
            margin-bottom: 15px;
        }

        .spinner > div {
            width: 12px;
            height: 12px;
            background-color: #007bff;
            border-radius: 100%;
            display: inline-block;
            animation: bounce 1.4s infinite ease-in-out both;
        }

        .spinner .bounce1 {
            animation-delay: -0.32s;
        }

        .spinner .bounce2 {
            animation-delay: -0.16s;
        }

        @keyframes bounce {
            0%, 80%, 100% { 
                transform: scale(0);
            } 
            40% { 
                transform: scale(1.0);
            }
        }

        .loading-spinner p {
            color: #666;
            margin: 0;
        }

        @media (max-width: 768px) {
            .order-preview-info {
                grid-template-columns: 1fr;
            }

            .order-preview-actions {
                flex-direction: column;
            }

            .order-preview-actions .btn {
                width: 100%;
            }
        }

        #location-status {
            font-size: 1.5rem;
            color: #666;
            margin: 10px 0;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 40px;
        }

        #location-status i {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
                opacity: 1;
            }
            50% {
                transform: scale(1.2);
                opacity: 0.7;
            }
            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        .stat-card:nth-child(4) .stat-icon {
            background: #e1f5fe;
            color: #03a9f4;
        }

        .stat-card:nth-child(4) .btn-info {
            background: #03a9f4;
            color: white;
            border: none;
            width: 100%;
            padding: 8px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .stat-card:nth-child(4) .btn-info:hover {
            background: #0288d1;
            transform: translateY(-1px);
        }
    </style>
</head>
<body class="dashboard-body driver-theme">
    <div id="alert-container"></div>
    
    <!-- Modern Header -->
    <header class="dashboard-header">
        <div class="container">
            <div class="header-content">
                <div class="header-left">
                    <a href="dashboard.php" class="dashboard-logo">
                        <i class="fas fa-truck-fast"></i>
                        <span>Door2Dry</span>
                    </a>
                    <nav class="dashboard-nav">
                        <a href="dashboard.php" class="nav-item active">
                            <i class="fas fa-home"></i>
                            <span>Dashboard</span>
                        </a>
                        <a href="orders.php" class="nav-item">
                            <i class="fas fa-list"></i>
                            <span>Orders</span>
                        </a>
                    </nav>
                </div>
                
                <div class="header-right">
                    <div class="user-menu">
                        <div class="user-info">
                            <div class="user-avatar">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="user-details">
                                <span class="user-name"><?php echo htmlspecialchars($user_name); ?></span>
                                <span class="user-role">Driver</span>
                            </div>
                        </div>
                        <button class="logout-btn" onclick="logout()">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Logout</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="dashboard-content">
        <div class="container">
            <div class="welcome-section">
                <div class="welcome-content">
                    <h1 class="welcome-title">Welcome back, <?php echo htmlspecialchars($user_name); ?>!</h1>
                    <p class="welcome-subtitle">Here's your delivery dashboard overview</p>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="stats-section">
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-box-open"></i>
                        </div>
                        <div class="stat-content">
                            <h3>Available Orders</h3>
                            <div class="stat-number" id="available-orders-count">-</div>
                            <p>Orders waiting for pickup</p>
                            <a href="orders.php?status=available" class="btn btn-warning">
                                <i class="fas fa-arrow-right"></i>
                                View Available
                            </a>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                        <div class="stat-content">
                            <h3>Active Orders</h3>
                            <div class="stat-number" id="active-orders-count">-</div>
                            <p>Orders in progress</p>
                            <a href="orders.php?status=requested" class="btn btn-primary">
                                <i class="fas fa-arrow-right"></i>
                                View Active
                            </a>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-content">
                            <h3>Completed Today</h3>
                            <div class="stat-number" id="completed-orders-count">-</div>
                            <p>Orders delivered today</p>
                            <a href="orders.php?status=delivered" class="btn btn-success">
                                <i class="fas fa-arrow-right"></i>
                                View Completed
                            </a>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-location-dot"></i>
                        </div>
                        <div class="stat-content">
                            <h3>Quick Actions</h3>
                            <div class="stat-number" id="location-status">
                                <i class="fas fa-crosshairs"></i>
                            </div>
                            <p>Update your current location</p>
                            <button class="btn btn-info" onclick="getCurrentLocation()">
                                <i class="fas fa-location-dot"></i>
                                Update Location
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Map Section -->
            <div class="map-section">
                <div class="section-card">
                    <div class="section-header">
                        <div class="section-title">
                            <i class="fas fa-map-marked-alt"></i>
                            <h2>Orders Map</h2>
                        </div>
                        <div class="map-controls">
                            <button class="control-btn" onclick="showAllOrders()">
                                <i class="fas fa-globe"></i>
                                <span>All Orders</span>
                            </button>
                            <button class="control-btn" onclick="showMyOrders()">
                                <i class="fas fa-user-check"></i>
                                <span>My Orders</span>
                            </button>
                            <button class="control-btn" onclick="showAvailableOrders()">
                                <i class="fas fa-box"></i>
                                <span>Available</span>
                            </button>
                            <button class="control-btn" onclick="getCurrentLocation()">
                                <i class="fas fa-location-dot"></i>
                                <span>My Location</span>
                            </button>
                        </div>
                    </div>
                    <div class="map-container">
                        <div id="driver-map-container" class="map-element"></div>
                        <div id="route-info" class="route-info" style="display: none;">
                            <div class="route-info-header">
                                <i class="fas fa-route"></i>
                                <h3>Route Information</h3>
                            </div>
                            <div class="route-details">
                                <div class="route-item">
                                    <i class="fas fa-road"></i>
                                    <span>Distance: </span>
                                    <strong id="route-distance">-</strong>
                                </div>
                                <div class="route-item">
                                    <i class="fas fa-clock"></i>
                                    <span>Duration: </span>
                                    <strong id="route-duration">-</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Orders Sections -->
            <div class="orders-section">
                <div class="section-card">
                    <div class="section-header">
                        <div class="section-title">
                            <i class="fas fa-clock"></i>
                            <h2>My Active Orders</h2>
                        </div>
                        <div class="section-actions">
                            <a href="orders.php?status=requested" class="btn btn-outline-primary">
                                <i class="fas fa-external-link-alt"></i>
                                View All Active
                            </a>
                            <button class="control-btn" onclick="loadActiveOrders()">
                                <i class="fas fa-sync-alt"></i>
                                <span>Refresh</span>
                            </button>
                        </div>
                    </div>
                    <div class="orders-container">
                        <div id="active-orders-container" class="orders-list">
                            <div class="loading-spinner">
                                <div class="spinner">
                                    <div class="bounce1"></div>
                                    <div class="bounce2"></div>
                                    <div class="bounce3"></div>
                                </div>
                                <p>Loading active orders...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="orders-section">
                <div class="section-card">
                    <div class="section-header">
                        <div class="section-title">
                            <i class="fas fa-box-open"></i>
                            <h2>Available Orders</h2>
                        </div>
                        <div class="section-actions">
                            <a href="orders.php?status=available" class="btn btn-outline-warning">
                                <i class="fas fa-external-link-alt"></i>
                                View All Available
                            </a>
                            <button class="control-btn" onclick="loadAvailableOrders()">
                                <i class="fas fa-sync-alt"></i>
                                <span>Refresh</span>
                            </button>
                        </div>
                    </div>
                    <div class="orders-container">
                        <div id="available-orders-container" class="orders-list">
                            <div class="loading-spinner">
                                <div class="spinner">
                                    <div class="bounce1"></div>
                                    <div class="bounce2"></div>
                                    <div class="bounce3"></div>
                                </div>
                                <p>Loading available orders...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 Door2Dry. All rights reserved. | BITP3123 Group Project</p>
        </div>
    </footer>

    <script>
        // Inject Google Maps API key for JavaScript
        window.GOOGLE_MAPS_API_KEY = '<?php echo GOOGLE_MAPS_API_KEY; ?>';
    </script>
    <script src="../assets/js/app.js"></script>
    <script src="../assets/js/maps.js"></script>
    <script>
        let driverMap = null;
        let currentLocationMarker = null;
        let allOrders = [];
        let myLocation = null;
        
        // Custom OrderManager for driver dashboard
        class DriverOrderManager extends OrderManager {
            static renderOrders(orders, containerId) {
                const container = document.getElementById(containerId);
                if (!container) return;
                
                if (orders.length === 0) {
                    container.innerHTML = `
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="fas fa-box-open"></i>
                            </div>
                            <h3>No Orders Found</h3>
                            <p>No orders match the selected filter.</p>
                        </div>
                    `;
                    return;
                }
                
                container.innerHTML = orders.map(order => `
                    <div class="order-preview">
                        <div class="order-preview-header">
                            <div class="order-preview-id">
                                <i class="fas fa-box"></i>
                                Order #${order.id}
                            </div>
                            <span class="status-badge ${order.status}">
                                <i class="fas fa-${getStatusIcon(order.status)}"></i>
                                ${formatStatus(order.status)}
                            </span>
                        </div>
                        
                        <div class="order-preview-content">
                            <div class="order-preview-info">
                                <div class="info-block">
                                    <span class="info-block-label">
                                        <i class="fas fa-user"></i>
                                        Customer
                                    </span>
                                    <span class="info-block-value">${order.customer_name}</span>
                                </div>
                                
                                <div class="info-block">
                                    <span class="info-block-label">
                                        <i class="fas fa-box"></i>
                                        Items
                                    </span>
                                    <span class="info-block-value">${order.items}</span>
                                </div>
                                
                                <div class="info-block">
                                    <span class="info-block-label">
                                        <i class="fas fa-map-marker-alt"></i>
                                        Address
                                    </span>
                                    <span class="info-block-value">${order.address}</span>
                                </div>
                                
                                ${order.pickup_date ? `
                                <div class="info-block">
                                    <span class="info-block-label">
                                        <i class="fas fa-clock"></i>
                                        Preferred Pickup
                                    </span>
                                    <span class="info-block-value">
                                        ${new Date(order.pickup_date).toLocaleDateString()} at ${Utils.formatTimeToAMPM(order.pickup_time)}
                                    </span>
                                </div>
                                ` : ''}
                            </div>

                            <div class="order-info">
                                <div class="info-section">
                                    <h4>
                                        <i class="fas fa-map-marker-alt"></i>
                                        Location Details
                                    </h4>
                                    <div class="info-group">
                                        <span class="info-label">Address</span>
                                        <span class="info-value">${order.address}</span>
                                    </div>
                                    ${order.pickup_date ? `
                                    <div class="info-group">
                                        <span class="info-label">Preferred Pickup</span>
                                        <span class="info-value">
                                            ${new Date(order.pickup_date).toLocaleDateString()} at ${Utils.formatTimeToAMPM(order.pickup_time)}
                                        </span>
                                    </div>
                                    ` : ''}
                                </div>
                                
                                <div class="map-preview" id="map-${order.id}">
                                    ${!window.google ? `
                                    <div class="map-loading">
                                        <i class="fas fa-map-marked-alt"></i>
                                        <p>Map loading...</p>
                                    </div>
                                    ` : ''}
                                </div>
                            </div>
                        </div>

                        <div class="order-footer">
                            <div class="order-dates">
                                <small>Created: ${new Date(order.created_at).toLocaleString()}</small>
                                ${order.updated_at ? `
                                <small>Updated: ${new Date(order.updated_at).toLocaleString()}</small>
                                ` : ''}
                            </div>
                            <div class="order-actions">
                                ${this.getOrderActions(order)}
                            </div>
                        </div>
                    </div>
                `).join('');

                // Initialize maps for each order
                DriverOrderManager.initializeOrderMaps(orders);
            }

            static initializeOrderMaps(orders) {
                // If Google Maps is not loaded yet, wait for it
                if (!window.google) {
                    document.addEventListener('googleMapsReady', () => {
                        this.initializeOrderMaps(orders);
                    });
                    return;
                }

                orders.forEach(order => {
                    if (order.address_lat && order.address_lng) {
                        const mapElement = document.getElementById(`map-${order.id}`);
                        if (mapElement) {
                            const map = new google.maps.Map(mapElement, {
                                center: { lat: parseFloat(order.address_lat), lng: parseFloat(order.address_lng) },
                                zoom: 15,
                                disableDefaultUI: true,
                                styles: [
                                    {
                                        featureType: "poi",
                                        elementType: "labels",
                                        stylers: [{ visibility: "off" }]
                                    }
                                ]
                            });

                            new google.maps.Marker({
                                position: { lat: parseFloat(order.address_lat), lng: parseFloat(order.address_lng) },
                                map: map,
                                title: `Order #${order.id} Location`
                            });
                        }
                    }
                });
            }

            static getOrderActions(order) {
                const actions = [];
                
                if (!order.driver_id) {
                    actions.push(`
                        <button class="btn btn-primary" onclick="takeOrder(${order.id})">
                            <i class="fas fa-hand-pointer"></i>
                            Take Order
                        </button>
                    `);
                } else if (order.driver_id == <?php echo $_SESSION['user_id']; ?>) {
                if (order.status === 'requested') {
                        actions.push(`
                            <button class="btn btn-primary" onclick="updateOrderStatus(${order.id}, 'picked_up')">
                                <i class="fas fa-truck"></i>
                                Mark as Picked Up
                        </button>
                        `);
                } else if (order.status === 'picked_up') {
                        actions.push(`
                            <button class="btn btn-primary" onclick="updateOrderStatus(${order.id}, 'washing')">
                                <i class="fas fa-soap"></i>
                                Mark as Washing
                        </button>
                        `);
                } else if (order.status === 'washing') {
                        actions.push(`
                            <button class="btn btn-success" onclick="updateOrderStatus(${order.id}, 'delivered')">
                                <i class="fas fa-check-circle"></i>
                                Mark as Delivered
                        </button>
                        `);
                    }
                }
                
                if (order.address_lat && order.address_lng) {
                    actions.push(`
                        <button class="btn btn-info" onclick="calculateRouteToOrder(${order.id})">
                            <i class="fas fa-route"></i>
                            Get Directions
                        </button>
                    `);
                }
                
                return actions.join('');
            }
        }
        
        // Initialize the page
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize statistics
            updateDashboardStats();
            
            // Load orders
            loadActiveOrders();
            loadAvailableOrders();
            loadAllOrdersForMap();
            
            // Set up auto-refresh
            setInterval(updateDashboardStats, 60000); // Update stats every minute
            setInterval(loadActiveOrders, 60000); // Refresh active orders every minute
            setInterval(loadAvailableOrders, 60000); // Refresh available orders every minute
            setInterval(loadAllOrdersForMap, 60000); // Refresh map orders every minute
            
            // Listen for Google Maps ready event
            document.addEventListener('googleMapsReady', function() {
                initializeDriverMap();
            });
            
            // Listen for Google Maps error event
            document.addEventListener('googleMapsError', function(event) {
                console.warn('Google Maps failed to load, map features disabled:', event.detail);
                document.getElementById('driver-map-container').innerHTML = 
                    '<div class="alert alert-warning">Map unavailable - Google Maps failed to load. Please check your connection or disable ad blockers.</div>';
            });
            
            // Fallback: Check if already loaded
            setTimeout(() => {
                if (typeof google !== 'undefined' && google.maps) {
                    initializeDriverMap();
                }
            }, 500);
        });
        
        // Load all orders for map
        async function loadAllOrdersForMap() {
            try {
                // Get all types of orders
                const [availableResponse, requestedResponse, pickedUpResponse, washingResponse, deliveredResponse] = await Promise.all([
                    API.getOrders('available'),
                    API.getOrders('requested'),
                    API.getOrders('picked_up'),
                    API.getOrders('washing'),
                    API.getOrders('delivered')
                ]);

                // Combine all orders
                allOrders = [
                    ...availableResponse.orders,
                    ...requestedResponse.orders,
                    ...pickedUpResponse.orders,
                    ...washingResponse.orders,
                    ...deliveredResponse.orders
                ];

                // If map is initialized, refresh the markers
                if (driverMap) {
                    showAllOrders();
                }
            } catch (error) {
                console.error('Error loading orders for map:', error);
                Utils.showAlert('Error loading orders for map', 'error');
            }
        }
        
        // Initialize driver map
        function initializeDriverMap() {
            try {
                driverMap = mapsService.initMap('driver-map-container', {
                    zoom: 12,
                    center: { lat: 3.1390, lng: 101.6869 } // Kuala Lumpur, Malaysia
                });
                
                if (driverMap) {
                    showAllOrders(); // Show all orders by default
                    
                    // Listen for route calculation events
                    document.addEventListener('routeCalculated', function(event) {
                        const result = event.detail.result;
                        if (result && result.routes && result.routes[0]) {
                            const route = result.routes[0];
                            const leg = route.legs[0];
                            
                            // Update route information
                            document.getElementById('route-distance').textContent = leg.distance.text;
                            document.getElementById('route-duration').textContent = leg.duration.text;
                            document.getElementById('route-info').style.display = 'block';
                        }
                    });
                }
            } catch (error) {
                console.error('Error initializing driver map:', error);
                document.getElementById('driver-map-container').innerHTML = `
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        Error initializing map. Please refresh the page.
                    </div>
                `;
            }
        }
        
        // Add order marker to map
        function addOrderMarker(order) {
            if (!order.address_lat || !order.address_lng) return;

            const isMyOrder = order.driver_id == <?php echo $_SESSION['user_id']; ?>;
            const isAvailable = !order.driver_id && order.status === 'requested';
            
            let markerColor = '#6c757d'; // Default gray
            let markerIcon = '📦';
            
            if (isMyOrder) {
                markerColor = '#28a745'; // Green for my orders
                markerIcon = '✅';
            } else if (isAvailable) {
                markerColor = '#ffc107'; // Yellow for available orders
                markerIcon = '📋';
            }
            
            const infoContent = `
                <div class="map-info-window">
                    <h6>Order #${order.id}</h6>
                    <p><strong>Status:</strong> ${formatStatus(order.status)}</p>
                    <p><strong>Address:</strong> ${order.address}</p>
                    <p><strong>Items:</strong> ${order.items}</p>
                    <p><strong>Customer:</strong> ${order.customer_name}</p>
                    ${isMyOrder ? `
                        <button class="btn btn-sm btn-primary" onclick="calculateRouteToOrder(${order.id})">
                            <i class="fas fa-route"></i>
                            Get Directions
                        </button>
                    ` : ''}
                    ${isAvailable ? `
                        <button class="btn btn-sm btn-success" onclick="assignOrderToMe(${order.id})">
                            <i class="fas fa-hand-pointer"></i>
                            Take Order
                        </button>
                    ` : ''}
                </div>
            `;
            
            mapsService.addMarker(
                parseFloat(order.address_lat),
                parseFloat(order.address_lng),
                {
                    title: `Order #${order.id} - ${order.customer_name}`,
                    infoContent: infoContent,
                    icon: {
                        url: `data:image/svg+xml;charset=UTF-8,${encodeURIComponent(`
                            <svg width="30" height="30" viewBox="0 0 30 30" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="15" cy="15" r="14" fill="${markerColor}" stroke="white" stroke-width="2"/>
                                <text x="15" y="20" text-anchor="middle" fill="white" font-size="14">${markerIcon}</text>
                            </svg>
                        `)}`,
                        scaledSize: new google.maps.Size(30, 30)
                    }
                }
            );
        }
        
        // Show all orders on map
        function showAllOrders() {
            if (!driverMap) return;
            
            mapsService.clearMarkers();
            
            if (currentLocationMarker) {
                currentLocationMarker.setMap(driverMap);
            }
            
            allOrders.forEach(order => {
                if (order.address_lat && order.address_lng) {
                    addOrderMarker(order);
                }
            });
            
            if (allOrders.length > 0) {
                mapsService.fitMapToMarkers();
            }
        }
        
        // Show my orders on map
        function showMyOrders() {
            if (!driverMap) return;
            
            mapsService.clearMarkers();
            
            if (currentLocationMarker) {
                currentLocationMarker.setMap(driverMap);
            }
            
            const myOrders = allOrders.filter(order => order.driver_id == <?php echo $_SESSION['user_id']; ?>);
            myOrders.forEach(order => {
                if (order.address_lat && order.address_lng) {
                    addOrderMarker(order);
                }
            });
            
            if (myOrders.length > 0) {
                mapsService.fitMapToMarkers();
            }
        }
        
        // Show available orders on map
        function showAvailableOrders() {
            if (!driverMap) return;
            
            mapsService.clearMarkers();
            
            if (currentLocationMarker) {
                currentLocationMarker.setMap(driverMap);
            }
            
            const availableOrders = allOrders.filter(order => !order.driver_id && order.status === 'requested');
            availableOrders.forEach(order => {
                if (order.address_lat && order.address_lng) {
                    addOrderMarker(order);
                }
            });
            
            if (availableOrders.length > 0) {
                mapsService.fitMapToMarkers();
            }
        }

        // Format status for display
        function formatStatus(status) {
            const labels = {
                'requested': 'Pickup Requested',
                'picked_up': 'Picked Up',
                'washing': 'Being Washed',
                'delivered': 'Delivered'
            };
            return labels[status] || status.replace('_', ' ');
        }

        // Get status icon
        function getStatusIcon(status) {
            const icons = {
                'requested': 'clock',
                'picked_up': 'truck',
                'washing': 'soap',
                'delivered': 'check-circle',
                'available': 'box-open'
            };
            return icons[status] || 'info-circle';
        }

        // Load active orders
        async function loadActiveOrders() {
            try {
                const response = await API.getOrders('requested');
                const pickedUpResponse = await API.getOrders('picked_up');
                const washingResponse = await API.getOrders('washing');
                
                // Combine all active orders
                const activeOrders = [
                    ...response.orders,
                    ...pickedUpResponse.orders,
                    ...washingResponse.orders
                ];
                
                // Sort by status priority and then by date
                activeOrders.sort((a, b) => {
                    const statusPriority = {
                        'requested': 1,
                        'picked_up': 2,
                        'washing': 3
                    };
                    if (statusPriority[a.status] !== statusPriority[b.status]) {
                        return statusPriority[a.status] - statusPriority[b.status];
                    }
                    return new Date(b.created_at) - new Date(a.created_at);
                });

                DriverOrderManager.renderOrders(activeOrders, 'active-orders-container');
            } catch (error) {
                console.error('Error loading active orders:', error);
                document.getElementById('active-orders-container').innerHTML = `
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <h3>Error Loading Orders</h3>
                        <p>There was a problem loading your active orders. Please try again.</p>
                        <button class="control-btn" onclick="loadActiveOrders()">
                            <i class="fas fa-sync-alt"></i>
                            <span>Try Again</span>
                        </button>
                    </div>
                `;
            }
        }
        
        // Load available orders
        async function loadAvailableOrders() {
            try {
                // Show loading state
                    document.getElementById('available-orders-container').innerHTML = `
                    <div class="loading-spinner">
                        <div class="spinner">
                            <div class="bounce1"></div>
                            <div class="bounce2"></div>
                            <div class="bounce3"></div>
                        </div>
                        <p>Loading available orders...</p>
                        </div>
                    `;

                // Fetch available orders
                const response = await API.getOrders('available');
                
                if (!response.success) {
                    throw new Error(response.error || 'Failed to load orders');
                }

                // Render orders
                DriverOrderManager.renderOrders(response.orders, 'available-orders-container');

                // Update count in stats
                const availableCount = response.orders.length;
                const countElement = document.getElementById('available-orders-count');
                if (countElement) {
                    countElement.textContent = availableCount;
                    countElement.classList.toggle('has-orders', availableCount > 0);
                }

            } catch (error) {
                console.error('Error loading available orders:', error);
                document.getElementById('available-orders-container').innerHTML = `
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <h3>Error Loading Orders</h3>
                        <p>${error.message || 'There was a problem loading available orders. Please try again.'}</p>
                        <button class="btn btn-primary" onclick="loadAvailableOrders()">
                            <i class="fas fa-sync-alt"></i>
                            Try Again
                        </button>
                    </div>
                `;
            }
        }
        
        // Calculate route to order
        function calculateRouteToOrder(orderId) {
            const order = allOrders.find(o => o.id == orderId);
            if (!order || !order.address_lat || !order.address_lng) {
                Utils.showAlert('Order location not available', 'error');
                return;
            }
            
            if (!myLocation) {
                getCurrentLocation().then(() => {
                    calculateRoute(order);
                }).catch(error => {
                    Utils.showAlert('Unable to get your location: ' + error, 'error');
                        });
                } else {
                calculateRoute(order);
            }
        }
        
        // Calculate route helper
        function calculateRoute(order) {
            const origin = new google.maps.LatLng(myLocation.lat, myLocation.lng);
            const destination = new google.maps.LatLng(order.address_lat, order.address_lng);
            
            mapsService.calculateRoute(origin, destination);
        }
        
        // Get current location
        async function getCurrentLocation() {
            const locationStatus = document.getElementById('location-status');
            
            try {
                // Show loading state
                locationStatus.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                
                const location = await mapsService.getCurrentLocation();
                myLocation = location;
                
                // Update location status with success icon
                locationStatus.innerHTML = '<i class="fas fa-check-circle" style="color: #28a745;"></i>';
                setTimeout(() => {
                    locationStatus.innerHTML = '<i class="fas fa-crosshairs"></i>';
                }, 3000);
                
                // If sorting by nearest, reload orders
                if (document.getElementById('sort-filter')?.value === 'nearest') {
                    loadOrders();
                }
                
                Utils.showAlert('Location updated successfully', 'success');
            } catch (error) {
                // Show error icon
                locationStatus.innerHTML = '<i class="fas fa-exclamation-circle" style="color: #dc3545;"></i>';
                setTimeout(() => {
                    locationStatus.innerHTML = '<i class="fas fa-crosshairs"></i>';
                }, 3000);
                
                Utils.showAlert('Unable to get your location: ' + error, 'error');
            }
        }

        // Update logout function with SweetAlert2
        function logout() {
            Swal.fire({
                title: 'Ready to Leave?',
                text: "You will be logged out of your account",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: '<i class="fas fa-sign-out-alt"></i> Logout',
                cancelButtonText: '<i class="fas fa-times"></i> Cancel',
                customClass: {
                    confirmButton: 'btn btn-primary',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: true,
                background: '#fff',
                borderRadius: '12px',
                heightAuto: false,
                padding: '2em'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading state
                    Swal.fire({
                        title: 'Logging Out',
                        text: 'Please wait...',
                        icon: 'info',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        willOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Perform logout
                    fetch('../api/logout.php', {
                        method: 'POST',
                        credentials: 'include'
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: 'Goodbye!',
                                text: 'You have been successfully logged out',
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.href = 'login.php';
                            });
            } else {
                            throw new Error(data.error || 'Logout failed');
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            title: 'Error!',
                            text: error.message || 'Failed to logout. Please try again.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    });
                }
            });
        }

        // Override the OrderManager class for this page
        window.OrderManager = DriverOrderManager;

        // Update dashboard statistics
        async function updateDashboardStats() {
            try {
                // Get available orders (unassigned)
                const availableResponse = await API.getOrders('available');
                const availableCount = availableResponse.orders.length;
                document.getElementById('available-orders-count').textContent = availableCount;
                document.getElementById('available-orders-count').classList.toggle('has-orders', availableCount > 0);

                // Get active orders (assigned to me with status 'requested', 'picked_up', or 'washing')
                const activeResponse = await API.getOrders('requested');
                const pickedUpResponse = await API.getOrders('picked_up');
                const washingResponse = await API.getOrders('washing');
                const activeCount = activeResponse.orders.length + pickedUpResponse.orders.length + washingResponse.orders.length;
                document.getElementById('active-orders-count').textContent = activeCount;
                document.getElementById('active-orders-count').classList.toggle('has-orders', activeCount > 0);

                // Get completed orders (delivered today)
                const deliveredResponse = await API.getOrders('delivered');
                const today = new Date().toISOString().split('T')[0];
                const completedToday = deliveredResponse.orders.filter(order => 
                    order.updated_at.startsWith(today)
                ).length;
                document.getElementById('completed-orders-count').textContent = completedToday;
                document.getElementById('completed-orders-count').classList.toggle('has-orders', completedToday > 0);
                
            } catch (error) {
                console.error('Error updating dashboard stats:', error);
                Utils.showAlert('Error updating dashboard statistics', 'error');
                }
            }
    </script>
</body>
</html> 