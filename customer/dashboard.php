<?php
require_once '../config/config.php';

// Require customer login
requireRole('customer');

$user_name = $_SESSION['user_name'];
$user_email = $_SESSION['user_email'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard - Door2Dry</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Add SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-material-ui/material-ui.css" rel="stylesheet">
    <!-- Add SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="dashboard-body">
    <div id="alert-container"></div>
    
    <!-- Modern Header -->
    <header class="dashboard-header">
        <div class="container">
            <div class="header-content">
                <div class="header-left">
                    <a href="dashboard.php" class="dashboard-logo">
                        <i class="fas fa-tshirt"></i>
                        <span>Door2Dry</span>
                    </a>
                    <nav class="dashboard-nav">
                        <a href="dashboard.php" class="nav-item active">
                            <i class="fas fa-home"></i>
                            <span>Dashboard</span>
                        </a>
                        <a href="new-order.php" class="nav-item">
                            <i class="fas fa-plus-circle"></i>
                            <span>New Order</span>
                        </a>
                        <a href="orders.php" class="nav-item">
                            <i class="fas fa-list"></i>
                            <span>My Orders</span>
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
                                <span class="user-email"><?php echo htmlspecialchars($user_email); ?></span>
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
            <!-- Welcome Section -->
            <div class="welcome-section">
                <div class="welcome-content">
                    <h1 class="welcome-title">Welcome back, <?php echo htmlspecialchars($user_name); ?>!</h1>
                    <p class="welcome-subtitle">Manage your laundry orders and track deliveries</p>
                </div>
                <div class="quick-actions">
                    <a href="new-order.php" class="quick-action primary">
                        <i class="fas fa-plus"></i>
                        <span>New Order</span>
                    </a>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="stats-section">
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <div class="stat-content">
                            <h3 class="stat-number" id="total-orders">0</h3>
                            <p class="stat-label">Total Orders</p>
                                        </div>
                                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon active">
                            <i class="fas fa-clock"></i>
                                </div>
                        <div class="stat-content">
                            <h3 class="stat-number" id="active-orders">0</h3>
                            <p class="stat-label">Active Orders</p>
                                        </div>
                                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon completed">
                            <i class="fas fa-check-circle"></i>
                                </div>
                        <div class="stat-content">
                            <h3 class="stat-number" id="completed-orders">0</h3>
                            <p class="stat-label">Completed</p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon rating">
                            <i class="fas fa-star"></i>
                                </div>
                        <div class="stat-content">
                            <h3 class="stat-number" id="avg-rating">0.0</h3>
                            <p class="stat-label">Avg Rating</p>
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
                            <h2>Order Tracking</h2>
                        </div>
                        <div class="map-controls">
                            <button class="control-btn active" onclick="showAllMyOrders()">
                                <i class="fas fa-globe"></i>
                                <span>All Orders</span>
                            </button>
                            <button class="control-btn" onclick="showActiveOrders()">
                                <i class="fas fa-spinner"></i>
                                <span>Active</span>
                            </button>
                            <button class="control-btn" onclick="centerOnMyAddress()">
                                <i class="fas fa-home"></i>
                                <span>My Address</span>
                            </button>
                        </div>
                    </div>
                    <div class="map-container">
                        <div id="customer-map-container" class="map-element"></div>
                    </div>
                </div>
            </div>

            <!-- Recent Orders Section -->
            <div class="recent-orders-section">
                <div class="recent-orders-card">
                    <div class="recent-orders-header">
                        <div class="recent-orders-title">
                            <i class="fas fa-history"></i>
                            <h2>Recent Orders</h2>
                        </div>
                        <a href="orders.php" class="recent-orders-view-all">
                            <span>View All</span>
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                    <div class="recent-orders-content">
                        <div id="recent-orders-container" class="recent-orders-list">
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
        let customerMap = null;
        let customerOrders = [];
        let customerAddressMarker = null;
        let currentUser = null;
        
        // Custom OrderManager for customer dashboard
        class CustomerOrderManager extends OrderManager {
            static formatDate(dateString) {
                const date = new Date(dateString);
                const options = { 
                    year: 'numeric', 
                    month: 'short', 
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                };
                return date.toLocaleDateString('en-US', options);
            }

            static getStatusClass(status) {
                const statusClasses = {
                    'requested': 'status-requested',
                    'picked_up': 'status-picked-up',
                    'washing': 'status-washing',
                    'delivered': 'status-delivered'
                };
                return statusClasses[status] || 'status-unknown';
            }

            static getStatusIcon(status) {
                const statusIcons = {
                    'requested': 'fa-clock',
                    'picked_up': 'fa-truck',
                    'washing': 'fa-soap',
                    'delivered': 'fa-check-circle'
                };
                return statusIcons[status] || 'fa-question-circle';
            }

            static getOrderActions(order) {
                return '';
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
                            <h3>No orders yet</h3>
                            <p>Create your first order to get started!</p>
                            <a href="new-order.php" class="btn btn-primary">
                                <i class="fas fa-plus"></i>
                                Create Order
                            </a>
                        </div>
                    `;
                    return;
                }
                
                let html = '';
                orders.forEach(order => {
                    const statusIcon = {
                        'requested': 'fa-clock',
                        'picked_up': 'fa-truck',
                        'washing': 'fa-soap',
                        'delivered': 'fa-check-circle'
                    }[order.status] || 'fa-clock';
                    
                    const statusText = {
                        'requested': 'Requested',
                        'picked_up': 'Picked Up',
                        'washing': 'Washing',
                        'delivered': 'Delivered'
                    }[order.status] || 'Unknown';

                    const driverInfo = order.driver_name ? `
                        <div class="recent-order-detail">
                            <i class="fas fa-user-tie"></i>
                            <span>Driver: ${order.driver_name}</span>
                        </div>
                    ` : '';
                    
                    html += `
                        <div class="recent-order-item">
                            <div class="recent-order-info">
                                <div class="recent-order-icon ${order.status}">
                                    <i class="fas ${statusIcon}"></i>
                                </div>
                                <div class="recent-order-details">
                                    <div class="recent-order-id">Order #${order.id}</div>
                                    <div class="recent-order-date">${CustomerOrderManager.formatDate(order.created_at)}</div>
                                    <div class="recent-order-detail">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <span>${order.address}</span>
                                    </div>
                                    <div class="recent-order-detail">
                                        <i class="fas fa-tshirt"></i>
                                        <span>${order.items} items</span>
                                    </div>
                                    ${driverInfo}
                                </div>
                            </div>
                            <div class="recent-order-status ${order.status}">
                                <i class="fas ${statusIcon}"></i>
                                ${statusText}
                            </div>
                            <div class="recent-order-actions">
                                ${this.getOrderActions(order)}
                            </div>
                        </div>
                    `;
                });
                
                container.innerHTML = html;
            }
        }
        
        // Load recent orders on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadRecentOrders();
            loadUserProfile();
            loadOrderStats();
            
            // Listen for Google Maps ready event
            document.addEventListener('googleMapsReady', function() {
                initializeCustomerMap();
            });
            
            // Listen for Google Maps error event
            document.addEventListener('googleMapsError', function(event) {
                console.warn('Google Maps failed to load, map features disabled:', event.detail);
                document.getElementById('customer-map-container').innerHTML = 
                    '<div class="map-error"><i class="fas fa-exclamation-triangle"></i><p>Map unavailable - Please check your connection</p></div>';
            });
            
            // Fallback: Check if already loaded
            setTimeout(() => {
                if (typeof google !== 'undefined' && google.maps) {
                    initializeCustomerMap();
                }
            }, 500);
        });
        
        // Load user profile
        async function loadUserProfile() {
            try {
                const response = await API.getUserProfile();
                if (response.success) {
                    currentUser = response.user;
                    console.log('User profile loaded:', currentUser);
                } else {
                    console.error('Failed to load user profile:', response.error);
                }
            } catch (error) {
                console.error('Error loading user profile:', error);
            }
        }
        
        // Load order statistics
        async function loadOrderStats() {
            try {
                const response = await API.getOrders();
                const orders = response.orders;
                
                // Calculate stats
                const totalOrders = orders.length;
                const activeOrders = orders.filter(order => order.status !== 'delivered').length;
                const completedOrders = orders.filter(order => order.status === 'delivered').length;
                
                // Calculate average rating (if available)
                const ratedOrders = orders.filter(order => order.rating);
                const avgRating = ratedOrders.length > 0 
                    ? (ratedOrders.reduce((sum, order) => sum + parseInt(order.rating), 0) / ratedOrders.length).toFixed(1)
                    : '0.0';
                
                // Update UI
                document.getElementById('total-orders').textContent = totalOrders;
                document.getElementById('active-orders').textContent = activeOrders;
                document.getElementById('completed-orders').textContent = completedOrders;
                document.getElementById('avg-rating').textContent = avgRating;
                
                // Animate counters
                animateCounters();
                
            } catch (error) {
                console.error('Error loading order stats:', error);
            }
        }
        
        // Animate counter numbers
        function animateCounters() {
            const counters = document.querySelectorAll('.stat-number');
            counters.forEach(counter => {
                const target = parseInt(counter.textContent) || parseFloat(counter.textContent);
                const increment = target / 20;
                let current = 0;
                
                const timer = setInterval(() => {
                    current += increment;
                    if (current >= target) {
                        current = target;
                        clearInterval(timer);
                    }
                    counter.textContent = counter.id === 'avg-rating' ? current.toFixed(1) : Math.floor(current);
                }, 50);
            });
        }
        
        // Initialize customer map
        function initializeCustomerMap() {
            try {
                customerMap = mapsService.initMap('customer-map-container', {
                    zoom: 12,
                    center: { lat: 3.1390, lng: 101.6869 } // Kuala Lumpur, Malaysia
                });
                
                if (customerMap) {
                    loadCustomerOrdersOnMap();
                }
            } catch (error) {
                console.error('Error initializing customer map:', error);
            }
        }
        
        // Load customer orders on map
        async function loadCustomerOrdersOnMap() {
            try {
                const response = await API.getOrders();
                customerOrders = response.orders;
                
                // Clear existing markers
                mapsService.clearMarkers();
                
                // Add markers for orders with coordinates
                customerOrders.forEach(order => {
                    if (order.address_lat && order.address_lng) {
                        addCustomerOrderMarker(order);
                    }
                });
                
                // Fit map to show all markers
                if (customerOrders.length > 0) {
                    mapsService.fitMapToMarkers();
                }
            } catch (error) {
                console.error('Error loading customer orders on map:', error);
            }
        }
        
        // Add customer order marker to map
        function addCustomerOrderMarker(order) {
            let markerColor = '#6c757d'; // Default gray
            let markerIcon = '📦';
            
            switch (order.status) {
                case 'requested':
                    markerColor = '#ffc107'; // Yellow
                    markerIcon = '📋';
                    break;
                case 'picked_up':
                    markerColor = '#17a2b8'; // Blue
                    markerIcon = '🚚';
                    break;
                case 'washing':
                    markerColor = '#007bff'; // Blue
                    markerIcon = '🧺';
                    break;
                case 'delivered':
                    markerColor = '#28a745'; // Green
                    markerIcon = '✅';
                    break;
            }
            
            const infoContent = `
                <div class="map-info-window">
                    <div class="info-header">
                        <h6>Order #${order.id}</h6>
                        <div class="info-status ${CustomerOrderManager.getStatusClass(order.status)}">
                            <i class="fas ${CustomerOrderManager.getStatusIcon(order.status)}"></i>
                            <span>${order.status.replace('_', ' ')}</span>
                        </div>
                    </div>
                    <div class="info-content">
                        <div class="info-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>${order.address}</span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-tshirt"></i>
                            <span>${order.items}</span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-calendar"></i>
                            <span>${new Date(order.created_at).toLocaleDateString()}</span>
                        </div>
                        ${order.driver_name ? `
                            <div class="info-item">
                                <i class="fas fa-user-tie"></i>
                                <span>${order.driver_name}</span>
                            </div>
                        ` : ''}
                    </div>
                    ${order.status === 'delivered' ? `
                        <div class="info-actions">
                            <button class="info-btn" onclick="rateOrder(${order.id})">
                                <i class="fas fa-star"></i>
                                Rate Order
                            </button>
                        </div>
                    ` : ''}
                </div>
            `;
            
            mapsService.addMarker(
                parseFloat(order.address_lat),
                parseFloat(order.address_lng),
                {
                    title: `Order #${order.id} - ${order.status}`,
                    infoContent: infoContent,
                    icon: {
                        url: `data:image/svg+xml;charset=UTF-8,${encodeURIComponent(`
                            <svg width="30" height="30" viewBox="0 0 30 30" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="15" cy="15" r="14" fill="${markerColor}" stroke="white" stroke-width="2"/>
                                <text x="15" y="20" text-anchor="middle" fill="white" font-size="12">${markerIcon}</text>
                            </svg>
                        `)}`,
                        scaledSize: new google.maps.Size(30, 30)
                    }
                }
            );
        }
        
        // Map control functions
        function showAllMyOrders(event = null) {
            // Update active button
            document.querySelectorAll('.control-btn').forEach(btn => btn.classList.remove('active'));
            if (event && event.target) {
                event.target.closest('.control-btn').classList.add('active');
            } else {
                // If no event (initial load), select the first button
                document.querySelector('.control-btn:first-child')?.classList.add('active');
            }
            
            mapsService.clearMarkers();
            
            if (customerAddressMarker) {
                customerAddressMarker.setMap(customerMap);
            }
            
            customerOrders.forEach(order => {
                if (order.address_lat && order.address_lng) {
                    addCustomerOrderMarker(order);
                }
            });
            
            if (customerOrders.length > 0) {
                mapsService.fitMapToMarkers();
                Utils.showAlert('Showing all your orders on the map', 'info');
            } else {
                Utils.showAlert('You have no orders yet', 'info');
            }
        }
        
        function showActiveOrders(event = null) {
            // Update active button
            document.querySelectorAll('.control-btn').forEach(btn => btn.classList.remove('active'));
            if (event && event.target) {
                event.target.closest('.control-btn').classList.add('active');
            } else {
                // If no event (programmatic call), select the second button
                document.querySelector('.control-btn:nth-child(2)')?.classList.add('active');
            }
            
            mapsService.clearMarkers();
            
            if (customerAddressMarker) {
                customerAddressMarker.setMap(customerMap);
            }
            
            const activeOrders = customerOrders.filter(order => order.status !== 'delivered');
            activeOrders.forEach(order => {
                if (order.address_lat && order.address_lng) {
                    addCustomerOrderMarker(order);
                }
            });
            
            if (activeOrders.length > 0) {
                mapsService.fitMapToMarkers();
                Utils.showAlert(`Showing ${activeOrders.length} active orders on the map`, 'info');
            } else {
                Utils.showAlert('You have no active orders at the moment', 'info');
            }
        }
        
        function centerOnMyAddress(event = null) {
            // Update active button
            document.querySelectorAll('.control-btn').forEach(btn => btn.classList.remove('active'));
            if (event && event.target) {
                event.target.closest('.control-btn').classList.add('active');
            } else {
                // If no event (programmatic call), select the last button
                document.querySelector('.control-btn:last-child')?.classList.add('active');
            }
            
            if (!currentUser) {
                Utils.showAlert('User profile not loaded yet. Please wait...', 'warning');
                return;
            }
            
            // Check if user has coordinates stored
            if (currentUser.has_coordinates && currentUser.address_lat && currentUser.address_lng) {
                const userLocation = { 
                    lat: parseFloat(currentUser.address_lat), 
                    lng: parseFloat(currentUser.address_lng) 
                };
                
                // Add or update customer address marker
                if (customerAddressMarker) {
                    customerAddressMarker.setPosition(new google.maps.LatLng(userLocation.lat, userLocation.lng));
                } else {
                    customerAddressMarker = mapsService.addMarker(
                        userLocation.lat,
                        userLocation.lng,
                        {
                            title: 'My Address',
                            infoContent: `<div class="map-info-window">
                                <div class="info-header">
                                    <h6>My Address</h6>
                                </div>
                                <div class="info-content">
                                    <div class="info-item">
                                        <i class="fas fa-home"></i>
                                        <span>${currentUser.address || 'Your registered address'}</span>
                                    </div>
                                </div>
                            </div>`,
                            icon: {
                                url: `data:image/svg+xml;charset=UTF-8,${encodeURIComponent(`
                                    <svg width="30" height="30" viewBox="0 0 30 30" xmlns="http://www.w3.org/2000/svg">
                                        <circle cx="15" cy="15" r="14" fill="#dc3545" stroke="white" stroke-width="2"/>
                                        <text x="15" y="20" text-anchor="middle" fill="white" font-size="14">🏠</text>
                                    </svg>
                                `)}`,
                                scaledSize: new google.maps.Size(30, 30)
                            }
                        }
                    );
                }
                
                // Center map on customer address
                customerMap.setCenter(new google.maps.LatLng(userLocation.lat, userLocation.lng));
                customerMap.setZoom(15);
                
                Utils.showAlert('Centered on your registered address', 'success');
            } else {
                Utils.showAlert('Your address coordinates are not available. Please update your profile.', 'warning');
            }
        }
        
        async function loadRecentOrders() {
            try {
                const response = await API.getOrders();
                
                // Show only the 5 most recent orders
                const recentOrders = response.orders.slice(0, 5);
                
                    CustomerOrderManager.renderOrders(recentOrders, 'recent-orders-container');
            } catch (error) {
                Utils.showAlert('Error loading recent orders: ' + error.message, 'danger');
                document.getElementById('recent-orders-container').innerHTML = `
                    <div class="error-state">
                        <i class="fas fa-exclamation-circle"></i>
                        <p>Error loading orders. Please try again later.</p>
                    </div>
                `;
            }
        }
        
        async function rateOrder(orderId) {
            // Create a more sophisticated rating modal
            const modal = document.createElement('div');
            modal.className = 'rating-modal';
            modal.innerHTML = `
                <div class="modal-backdrop"></div>
                <div class="modal-content">
                    <div class="modal-header">
                        <h3>Rate Your Order</h3>
                        <button class="modal-close" onclick="closeRatingModal()">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="rating-section">
                            <label>How was our service?</label>
                            <div class="star-rating">
                                <i class="fas fa-star" data-rating="1"></i>
                                <i class="fas fa-star" data-rating="2"></i>
                                <i class="fas fa-star" data-rating="3"></i>
                                <i class="fas fa-star" data-rating="4"></i>
                                <i class="fas fa-star" data-rating="5"></i>
                            </div>
                        </div>
                        <div class="comment-section">
                            <label for="rating-comment">Additional comments (optional)</label>
                            <textarea id="rating-comment" placeholder="Tell us about your experience..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn-secondary" onclick="closeRatingModal()">Cancel</button>
                        <button class="btn-primary" onclick="submitRating(${orderId})" disabled>Submit Rating</button>
                    </div>
                </div>
            `;
            
            document.body.appendChild(modal);
            
            // Add star rating functionality
            let selectedRating = 0;
            const stars = modal.querySelectorAll('.star-rating i');
            const submitBtn = modal.querySelector('.btn-primary');
            
            stars.forEach((star, index) => {
                star.addEventListener('click', () => {
                    selectedRating = index + 1;
                    updateStarDisplay(selectedRating);
                    submitBtn.disabled = false;
                });
                
                star.addEventListener('mouseenter', () => {
                    updateStarDisplay(index + 1);
                });
            });
            
            modal.querySelector('.star-rating').addEventListener('mouseleave', () => {
                updateStarDisplay(selectedRating);
            });
            
            function updateStarDisplay(rating) {
                stars.forEach((star, index) => {
                    if (index < rating) {
                        star.classList.add('active');
                    } else {
                        star.classList.remove('active');
                    }
                });
            }
            
            // Store rating for submission
            modal.selectedRating = selectedRating;
            
            // Add to window for global access
            window.currentRatingModal = modal;
            window.selectedRating = selectedRating;
        }
        
        function closeRatingModal() {
            const modal = document.querySelector('.rating-modal');
            if (modal) {
                modal.remove();
            }
        }
        
        async function submitRating(orderId) {
            const modal = window.currentRatingModal;
            const rating = window.selectedRating || modal.querySelectorAll('.star-rating i.active').length;
            const comment = modal.querySelector('#rating-comment').value;
            
            if (!rating || rating < 1 || rating > 5) {
                Utils.showAlert('Please select a rating', 'warning');
                return;
            }
            
            try {
                const response = await API.rateOrder(orderId, rating, comment);
                
                if (response.success) {
                    Utils.showAlert('Thank you for your rating!', 'success');
                    loadRecentOrders(); // Reload orders
                    loadOrderStats(); // Reload stats
                    closeRatingModal();
                } else {
                    Utils.showAlert(response.message || 'Failed to submit rating', 'danger');
                }
            } catch (error) {
                Utils.showAlert('Error submitting rating: ' + error.message, 'danger');
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
        window.OrderManager = CustomerOrderManager;
    </script>
</body>
</html> 