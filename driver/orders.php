<?php
require_once '../config/config.php';

// Require driver login
requireRole('driver');

$user_name = $_SESSION['user_name'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders - Door2Dry Driver</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Add SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-material-ui/material-ui.css" rel="stylesheet">
    <!-- Add SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .orders-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .filter-group {
            display: flex;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
        }

        .select-container {
            position: relative;
            min-width: 200px;
        }

        .select-container i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
            pointer-events: none;
        }

        .select-container select {
            width: 100%;
            padding: 8px 12px 8px 35px;
            border: 1px solid #ddd;
            border-radius: 6px;
            appearance: none;
            background: #fff;
            font-size: 14px;
            color: #333;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .select-container select:hover {
            border-color: #aaa;
        }

        .select-container select:focus {
            border-color: #007bff;
            outline: none;
            box-shadow: 0 0 0 2px rgba(0,123,255,0.25);
        }

        .order-card {
            background: #fff;
            border-radius: 12px;
            margin-bottom: 20px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }

        .order-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }

        .order-header {
            padding: 15px 20px;
            background: #f8f9fa;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .order-id {
            font-weight: 600;
            color: #333;
            font-size: 1.1rem;
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .status-badge.requested {
            background: #fff3cd;
            color: #856404;
        }

        .status-badge.picked_up {
            background: #cce5ff;
            color: #004085;
        }

        .status-badge.washing {
            background: #d1ecf1;
            color: #0c5460;
        }

        .status-badge.delivered {
            background: #d4edda;
            color: #155724;
        }

        .order-body {
            padding: 20px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }

        .info-section {
            margin-bottom: 20px;
        }

        .info-section:last-child {
            margin-bottom: 0;
        }

        .info-section h4 {
            margin: 0 0 10px 0;
            color: #555;
            font-size: 1rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .info-group {
            margin-bottom: 12px;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .info-label {
            color: #666;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .info-value {
            color: #333;
            font-size: 1rem;
        }

        .order-footer {
            padding: 15px 20px;
            background: #f8f9fa;
            border-top: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }

        .order-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            outline: none;
        }

        .btn-primary {
            background: #007bff;
            color: white;
        }

        .btn-success {
            background: #28a745;
            color: white;
        }

        .btn-warning {
            background: #ffc107;
            color: #333;
        }

        .btn:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        .map-preview {
            width: 100%;
            height: 200px;
            border-radius: 8px;
            overflow: hidden;
            margin-top: 10px;
            background: #f8f9fa;
            position: relative;
        }

        .map-loading {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: #f8f9fa;
            color: #666;
        }

        .map-loading i {
            font-size: 2rem;
            margin-bottom: 10px;
            color: #007bff;
        }

        .map-loading p {
            margin: 0;
            font-size: 0.9rem;
        }

        @media (max-width: 768px) {
            .orders-toolbar {
                flex-direction: column;
                align-items: stretch;
            }

            .filter-group {
                flex-direction: column;
                width: 100%;
            }

            .select-container {
                width: 100%;
            }

            .order-footer {
                flex-direction: column;
                align-items: stretch;
            }

            .order-actions {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }
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
                        <a href="dashboard.php" class="nav-item">
                            <i class="fas fa-home"></i>
                            <span>Dashboard</span>
                        </a>
                        <a href="orders.php" class="nav-item active">
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

    <!-- Dashboard Content -->
    <div class="dashboard-content">
        <div class="container">
            <!-- Page Title -->
            <div class="welcome-section">
                <div class="welcome-content">
                    <h1 class="welcome-title">Orders Management</h1>
                    <p class="welcome-subtitle">View and manage your delivery orders</p>
                </div>
            </div>

            <!-- Orders Section -->
            <div class="section-card">
                <div class="orders-toolbar">
                    <div class="filter-group">
                        <div class="select-container">
                            <i class="fas fa-filter"></i>
                            <select class="form-control" id="status-filter">
                                <option value="">All Orders</option>
                                <option value="available">Available Orders</option>
                                <option value="requested">Assigned - Pickup Pending</option>
                                <option value="picked_up">Picked Up</option>
                                <option value="washing">Washing</option>
                                <option value="delivered">Delivered</option>
                            </select>
                        </div>
                        <div class="select-container">
                            <i class="fas fa-sort"></i>
                            <select class="form-control" id="sort-filter">
                                <option value="latest">Latest First</option>
                                <option value="oldest">Oldest First</option>
                                <option value="nearest">Nearest First</option>
                            </select>
                        </div>
                        <button class="control-btn" onclick="loadOrders()">
                            <i class="fas fa-sync-alt"></i>
                            <span>Refresh</span>
                        </button>
                    </div>
                    <div class="map-controls">
                        <button class="control-btn" onclick="getCurrentLocation()">
                            <i class="fas fa-location-dot"></i>
                            <span>Update Location</span>
                        </button>
                    </div>
                </div>
                
                <div id="orders-container">
                    <div id="orders-list">
                        <div class="loading-spinner">
                            <div class="spinner">
                                <div class="bounce1"></div>
                                <div class="bounce2"></div>
                                <div class="bounce3"></div>
                            </div>
                            <p>Loading orders...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="dashboard-footer">
        <div class="container">
            <div class="footer-content">
                <p>&copy; 2024 Door2Dry. All rights reserved.</p>
                <div class="footer-links">
                    <a href="#" class="footer-link">Privacy Policy</a>
                    <a href="#" class="footer-link">Terms of Service</a>
                    <a href="#" class="footer-link">Support</a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Inject Google Maps API key for JavaScript
        window.GOOGLE_MAPS_API_KEY = '<?php echo GOOGLE_MAPS_API_KEY; ?>';
    </script>
    <script src="../assets/js/app.js"></script>
    <script src="../assets/js/maps.js"></script>
    <script>
        let myLocation = null;
        
        // Custom OrderManager for driver orders page
        class DriverOrderManager extends OrderManager {
            static getOrderActions(order) {
                let actions = '';
                
                if (!order.driver_id && order.status === 'requested') {
                    actions += `
                        <button class="btn btn-success" onclick="assignOrderToMe(${order.id})">
                            <i class="fas fa-hand-pointer"></i>
                            <span>Take Order</span>
                        </button>
                    `;
                } else if (order.driver_id == <?php echo $_SESSION['user_id']; ?>) {
                    if (order.status === 'requested') {
                        actions += `
                            <button class="btn btn-primary" onclick="updateOrderStatus(${order.id}, 'picked_up')">
                                <i class="fas fa-box"></i>
                                <span>Mark Picked Up</span>
                            </button>
                            <button class="btn btn-warning" onclick="getDirections(${order.id})">
                                <i class="fas fa-route"></i>
                                <span>Get Directions</span>
                            </button>
                        `;
                    } else if (order.status === 'picked_up') {
                        actions += `
                            <button class="btn btn-primary" onclick="updateOrderStatus(${order.id}, 'washing')">
                                <i class="fas fa-soap"></i>
                                <span>Mark Washing</span>
                            </button>
                        `;
                    } else if (order.status === 'washing') {
                        actions += `
                            <button class="btn btn-success" onclick="updateOrderStatus(${order.id}, 'delivered')">
                                <i class="fas fa-check"></i>
                                <span>Mark Delivered</span>
                            </button>
                            <button class="btn btn-warning" onclick="getDirections(${order.id})">
                                <i class="fas fa-route"></i>
                                <span>Get Directions</span>
                            </button>
                        `;
                    }
                }
                
                return actions;
            }

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
                    <div class="order-card">
                        <div class="order-header">
                            <span class="order-id">Order #${order.id}</span>
                            <span class="status-badge ${order.status}">
                                <i class="fas fa-${getStatusIcon(order.status)}"></i>
                                ${formatStatus(order.status)}
                            </span>
                        </div>
                        
                        <div class="order-body">
                            <div class="order-info">
                                <div class="info-section">
                                    <h4>
                                        <i class="fas fa-user"></i>
                                        Customer Details
                                    </h4>
                                    <div class="info-group">
                                        <span class="info-label">Name</span>
                                        <span class="info-value">${order.customer_name}</span>
                                    </div>
                                    <div class="info-group">
                                        <span class="info-label">Phone</span>
                                        <span class="info-value">${order.customer_phone}</span>
                                    </div>
                                </div>

                                <div class="info-section">
                                    <h4>
                                        <i class="fas fa-box"></i>
                                        Order Details
                                    </h4>
                                    <div class="info-group">
                                        <span class="info-label">Items</span>
                                        <span class="info-value">${order.items}</span>
                                    </div>
                                    ${order.special_notes ? `
                                    <div class="info-group">
                                        <span class="info-label">Special Notes</span>
                                        <span class="info-value">${order.special_notes}</span>
                                    </div>
                                    ` : ''}
                                </div>
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
                                            ${new Date(order.pickup_date).toLocaleDateString()} at ${formatTimeToAMPM(order.pickup_time)}
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

                // Initialize maps for each order only if Google Maps is loaded
                if (window.google) {
                    this.initializeOrderMaps(orders);
                } else {
                    // Wait for Google Maps to load
                    document.addEventListener('googleMapsReady', () => {
                        this.initializeOrderMaps(orders);
                    });
                }
            }

            static initializeOrderMaps(orders) {
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
        }

        // Initialize the page
        document.addEventListener('DOMContentLoaded', function() {
            // Set initial status filter from URL parameter
            const urlParams = new URLSearchParams(window.location.search);
            const statusParam = urlParams.get('status');
            if (statusParam) {
                const statusFilter = document.getElementById('status-filter');
                if (statusParam === 'available') {
                    statusFilter.value = 'available';
                } else if (statusParam === 'active') {
                    statusFilter.value = 'requested';
                } else if (statusParam === 'delivered') {
                    statusFilter.value = 'delivered';
                } else {
                    statusFilter.value = statusParam;
                }
            }
            
            loadOrders();
            
            // Add event listeners for filters
            document.getElementById('status-filter').addEventListener('change', loadOrders);
            document.getElementById('sort-filter').addEventListener('change', loadOrders);
            
            // Get initial location
            getCurrentLocation();
        });

        function formatTimeToAMPM(time) {
            if (!time) return '';
            const [hours, minutes] = time.split(':');
            const hour = parseInt(hours);
            const ampm = hour >= 12 ? 'PM' : 'AM';
            const formattedHour = hour % 12 || 12;
            return `${formattedHour}:${minutes} ${ampm}`;
        }

        function getStatusIcon(status) {
            const icons = {
                'requested': 'clock',
                'picked_up': 'truck',
                'washing': 'soap',
                'delivered': 'check-circle'
            };
            return icons[status] || 'info-circle';
        }

        function formatStatus(status) {
            const labels = {
                'requested': 'Pickup Requested',
                'picked_up': 'Picked Up',
                'washing': 'Being Washed',
                'delivered': 'Delivered'
            };
            return labels[status] || status.replace('_', ' ');
        }

        async function loadOrders() {
            const status = document.getElementById('status-filter').value;
            const sort = document.getElementById('sort-filter').value;
            
            try {
                Utils.showLoading('orders-list');
                const response = await fetch('../api/get_orders.php' + (status ? `?status=${status}` : ''));
                
                if (!response.ok) {
                    throw new Error('Failed to fetch orders');
                }
                
                const data = await response.json();
                
                if (!data.success) {
                    throw new Error(data.error || 'Failed to load orders');
                }

                let orders = data.orders;

                // Apply sorting
                if (sort === 'oldest') {
                    orders.sort((a, b) => new Date(a.created_at) - new Date(b.created_at));
                } else if (sort === 'nearest' && myLocation) {
                    orders.sort((a, b) => {
                        const distA = calculateDistance(myLocation, { lat: a.address_lat, lng: a.address_lng });
                        const distB = calculateDistance(myLocation, { lat: b.address_lat, lng: b.address_lng });
                        return distA - distB;
                    });
                }

                DriverOrderManager.renderOrders(orders, 'orders-list');
            } catch (error) {
                Utils.showAlert('Error loading orders: ' + error.message, 'danger');
                document.getElementById('orders-list').innerHTML = `
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <h3>Error Loading Orders</h3>
                        <p>There was a problem loading your orders. Please try again.</p>
                        <button class="control-btn" onclick="loadOrders()">
                            <i class="fas fa-sync-alt"></i>
                            <span>Try Again</span>
                        </button>
                    </div>
                `;
            }
        }

        function calculateDistance(point1, point2) {
            const R = 6371; // Earth's radius in km
            const dLat = (point2.lat - point1.lat) * Math.PI / 180;
            const dLon = (point2.lng - point1.lng) * Math.PI / 180;
            const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                     Math.cos(point1.lat * Math.PI / 180) * Math.cos(point2.lat * Math.PI / 180) *
                     Math.sin(dLon/2) * Math.sin(dLon/2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
            return R * c;
        }

        async function getCurrentLocation() {
            try {
                const location = await mapsService.getCurrentLocation();
                myLocation = location;
                
                // If sorting by nearest, reload orders
                if (document.getElementById('sort-filter').value === 'nearest') {
                    loadOrders();
                }
                
                Utils.showAlert('Location updated successfully', 'success');
            } catch (error) {
                Utils.showAlert('Unable to get your location: ' + error, 'error');
            }
        }

        async function assignOrderToMe(orderId) {
            if (!confirm('Are you sure you want to take this order?')) {
                return;
            }
            
            try {
                await API.updateStatus(orderId, 'requested');
                Utils.showAlert('Order assigned to you successfully', 'success');
                loadOrders();
            } catch (error) {
                Utils.showAlert('Error assigning order: ' + error.message, 'error');
            }
        }

        async function updateOrderStatus(orderId, newStatus) {
            if (!confirm(`Are you sure you want to mark this order as ${formatStatus(newStatus)}?`)) {
                return;
            }
            
            try {
                await API.updateStatus(orderId, newStatus);
                Utils.showAlert('Order status updated successfully', 'success');
                loadOrders();
            } catch (error) {
                Utils.showAlert('Error updating order status: ' + error.message, 'error');
            }
        }

        function getDirections(orderId) {
            window.location.href = `dashboard.php?order=${orderId}`;
        }

        // Override the OrderManager class for this page
        window.OrderManager = DriverOrderManager;

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
    </script>
</body>
</html> 