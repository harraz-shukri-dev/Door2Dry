<?php
require_once '../config/config.php';

// Require customer login
requireRole('customer');

$user_name = $_SESSION['user_name'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - Door2Dry</title>
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
                        <a href="dashboard.php" class="nav-item">
                            <i class="fas fa-home"></i>
                            <span>Dashboard</span>
                        </a>
                        <a href="new-order.php" class="nav-item">
                            <i class="fas fa-plus-circle"></i>
                            <span>New Order</span>
                        </a>
                        <a href="orders.php" class="nav-item active">
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
                                <span class="user-email"><?php echo htmlspecialchars($_SESSION['user_email']); ?></span>
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
                    <h1 class="welcome-title">My Orders</h1>
                    <p class="welcome-subtitle">Track and manage your laundry orders</p>
                </div>
                <div class="quick-actions">
                    <a href="new-order.php" class="quick-action primary">
                        <i class="fas fa-plus"></i>
                        <span>New Order</span>
                    </a>
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
                                <option value="requested">Requested</option>
                                <option value="picked_up">Picked Up</option>
                                <option value="washing">Washing</option>
                                <option value="delivered">Delivered</option>
                            </select>
                        </div>
                        <button class="control-btn" onclick="loadOrders()">
                            <i class="fas fa-sync-alt"></i>
                            <span>Refresh</span>
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

    <!-- Rating Modal -->
    <div id="rating-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title">
                    <i class="fas fa-star text-warning"></i>
                    <h4>Rate Your Order</h4>
                </div>
                <button class="modal-close" onclick="closeRatingModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="rating-form">
                    <input type="hidden" id="rating-order-id" name="order_id">
                    
                    <div class="form-group rating-group">
                        <label class="form-label">How was your experience?</label>
                        <div class="star-rating">
                            <span class="star" data-rating="1" title="Poor">★</span>
                            <span class="star" data-rating="2" title="Fair">★</span>
                            <span class="star" data-rating="3" title="Good">★</span>
                            <span class="star" data-rating="4" title="Very Good">★</span>
                            <span class="star" data-rating="5" title="Excellent">★</span>
                        </div>
                        <input type="hidden" id="rating-value" name="rating" required>
                        <div class="rating-text">Select your rating</div>
                    </div>
                    
                    <div class="form-group">
                        <label for="comment" class="form-label">
                            Share your thoughts
                            <span class="text-muted">(optional)</span>
                        </label>
                        <textarea class="form-control" id="comment" name="comment" rows="3" 
                                  placeholder="Tell us what you liked or how we can improve..."></textarea>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="closeRatingModal()">
                            <i class="fas fa-times"></i>
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i>
                            Submit Rating
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="../assets/js/app.js"></script>
    <script>
        // Custom OrderManager for customer orders page
        class CustomerOrderManager extends OrderManager {
            static getOrderActions(order) {
                let actions = '';
                
                if (order.status === 'delivered' && !order.rating) {
                    actions += `
                        <button class="control-btn success" onclick="openRatingModal(${order.id})">
                            <i class="fas fa-star"></i>
                            <span>Rate Order</span>
                        </button>
                    `;
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
                            <h3>No orders found</h3>
                            <p>Create your first order to get started!</p>
                            <a href="new-order.php" class="quick-action primary">
                                <i class="fas fa-plus"></i>
                                <span>Create Order</span>
                            </a>
                        </div>
                    `;
                    return;
                }
                
                let html = '';
                orders.forEach(order => {
                    const statusIcon = {
                        'requested': 'clock',
                        'picked_up': 'truck',
                        'washing': 'soap',
                        'delivered': 'check-circle'
                    }[order.status] || 'info-circle';
                    
                    const statusText = {
                        'requested': 'Pickup Requested',
                        'picked_up': 'Picked Up',
                        'washing': 'Being Washed',
                        'delivered': 'Delivered'
                    }[order.status] || order.status;
                    
                    html += `
                        <div class="order-card">
                            <div class="order-header">
                                <span class="order-id">Order #${order.id}</span>
                                <span class="order-status ${order.status}">
                                    <i class="fas fa-${statusIcon}"></i>
                                    ${statusText}
                                </span>
                            </div>
                            <div class="order-body">
                                <div class="order-info">
                                    <div class="info-group">
                                        <span class="info-label">
                                            <i class="fas fa-calendar"></i>
                                            Created
                                        </span>
                                        <span class="info-value">${new Date(order.created_at).toLocaleString()}</span>
                                    </div>
                                    <div class="info-group">
                                        <span class="info-label">
                                            <i class="fas fa-tshirt"></i>
                                            Items
                                        </span>
                                        <span class="info-value">${order.items}</span>
                                    </div>
                                    ${order.driver_name ? `
                                        <div class="info-group">
                                            <span class="info-label">
                                                <i class="fas fa-user"></i>
                                                Driver
                                            </span>
                                            <span class="info-value">${order.driver_name}</span>
                                        </div>
                                    ` : ''}
                                    ${order.rating ? `
                                        <div class="info-group">
                                            <span class="info-label">
                                                <i class="fas fa-star"></i>
                                                Your Rating
                                            </span>
                                            <span class="info-value rating-stars">
                                                ${'★'.repeat(order.rating)}${'☆'.repeat(5 - order.rating)}
                                            </span>
                                        </div>
                                    ` : ''}
                                </div>
                                <div class="order-info">
                                    <div class="info-group">
                                        <span class="info-label">
                                            <i class="fas fa-map-marker-alt"></i>
                                            Pickup Address
                                        </span>
                                        <span class="address-value">${order.address}</span>
                                    </div>
                                    ${order.pickup_date ? `
                                        <div class="info-group">
                                            <span class="info-label">
                                                <i class="fas fa-calendar"></i>
                                                Pickup Date
                                            </span>
                                            <span class="info-value">${new Date(order.pickup_date).toLocaleDateString()}</span>
                                        </div>
                                        <div class="info-group">
                                            <span class="info-label">
                                                <i class="fas fa-clock"></i>
                                                Pickup Time
                                            </span>
                                            <span class="info-value">${formatTimeToAMPM(order.pickup_time)}</span>
                                        </div>
                                    ` : ''}
                                    ${order.special_notes ? `
                                        <div class="info-group">
                                            <span class="info-label">
                                                <i class="fas fa-sticky-note"></i>
                                                Special Instructions
                                            </span>
                                            <span class="info-value">${order.special_notes}</span>
                                        </div>
                                    ` : ''}
                                    ${order.comment ? `
                                        <div class="info-group">
                                            <span class="info-label">
                                                <i class="fas fa-comment"></i>
                                                Your Feedback
                                            </span>
                                            <span class="info-value">${order.comment}</span>
                                        </div>
                                    ` : ''}
                                </div>
                                
                                ${order.rating ? `
                                    <div class="order-rating">
                                        <div class="info-item">
                                            <span class="info-label">
                                                <i class="fas fa-star"></i>
                                                Your Rating
                                            </span>
                                            <span class="info-value">
                                                ${'★'.repeat(order.rating)}${'☆'.repeat(5 - order.rating)}
                                            </span>
                                        </div>
                                        ${order.comment ? `
                                            <div class="info-item mt-2">
                                                <span class="info-label">
                                                    <i class="fas fa-comment"></i>
                                                    Your Feedback
                                                </span>
                                                <span class="info-value">${order.comment}</span>
                                            </div>
                                        ` : ''}
                                    </div>
                                ` : ''}
                            </div>
                            ${this.getOrderActions(order) ? `
                                <div class="order-footer">
                                    <div class="order-actions">
                                        ${this.getOrderActions(order)}
                                    </div>
                                </div>
                            ` : ''}
                        </div>
                    `;
                });
                
                container.innerHTML = html;
            }
        }
        
        // Load orders on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadOrders();
            
            // Add event listener for status filter
            const statusFilter = document.getElementById('status-filter');
            statusFilter.addEventListener('change', function() {
                loadOrders();
                
                // Update filter label
                const selectedOption = statusFilter.options[statusFilter.selectedIndex];
                document.getElementById('current-filter').textContent = selectedOption.text;
            });
            
            // Setup star rating
            setupStarRating();
        });
        
        async function loadOrders() {
            const statusFilter = document.getElementById('status-filter');
            const status = statusFilter.value;
            
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
                
                const ordersList = document.getElementById('orders-list');
                
                if (data.orders.length === 0) {
                    ordersList.innerHTML = `
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="fas fa-box-open"></i>
                            </div>
                            <h3>No Orders Found</h3>
                            <p>${status ? 'No orders match the selected filter.' : 'You haven\'t placed any orders yet.'}</p>
                            <a href="new-order.php" class="quick-action primary">
                                <i class="fas fa-plus"></i>
                                <span>Create New Order</span>
                            </a>
                        </div>
                    `;
                } else {
                    ordersList.innerHTML = data.orders.map(order => `
                        <div class="order-card ${order.status}">
                            <div class="order-header">
                                <h3>Order #${order.id}</h3>
                                <span class="status-badge ${order.status}">${formatStatus(order.status)}</span>
                            </div>
                            
                            <div class="order-body">
                                <div class="order-info-column">
                                    <!-- Timing Information -->
                                    ${order.pickup_date ? `
                                    <div class="info-section">
                                        <h4>Pickup Details</h4>
                                        <div class="info-group">
                                            <span class="info-label">
                                                <i class="fas fa-calendar"></i>
                                                Date
                                            </span>
                                            <span class="info-value">${new Date(order.pickup_date).toLocaleDateString()}</span>
                                        </div>
                                        <div class="info-group">
                                            <span class="info-label">
                                                <i class="fas fa-clock"></i>
                                                Time
                                            </span>
                                            <span class="info-value">${formatTimeToAMPM(order.pickup_time)}</span>
                                        </div>
                                    </div>
                                    ` : ''}
                                    
                                    <!-- Location Information -->
                                    <div class="info-section">
                                        <h4>Location</h4>
                                        <div class="info-group">
                                            <span class="info-label">
                                                <i class="fas fa-map-marker-alt"></i>
                                                Address
                                            </span>
                                            <span class="info-value">${order.address}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="order-info-column">
                                    <!-- Order Details -->
                                    <div class="info-section">
                                        <h4>Order Details</h4>
                                        <div class="info-group">
                                            <span class="info-label">
                                                <i class="fas fa-tshirt"></i>
                                                Items
                                            </span>
                                            <span class="info-value">${order.items}</span>
                                        </div>
                                        ${order.special_notes ? `
                                        <div class="info-group">
                                            <span class="info-label">
                                                <i class="fas fa-sticky-note"></i>
                                                Notes
                                            </span>
                                            <span class="info-value">${order.special_notes}</span>
                                        </div>
                                        ` : ''}
                                    </div>

                                    <!-- Driver Information -->
                                    ${order.driver_name ? `
                                    <div class="info-section">
                                        <h4>Driver</h4>
                                        <div class="info-group">
                                            <span class="info-label">
                                                <i class="fas fa-user"></i>
                                                Name
                                            </span>
                                            <span class="info-value">${order.driver_name}</span>
                                        </div>
                                        <div class="info-group">
                                            <span class="info-label">
                                                <i class="fas fa-phone"></i>
                                                Phone
                                            </span>
                                            <span class="info-value">${order.driver_phone}</span>
                                        </div>
                                    </div>
                                    ` : ''}
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
                                    ${CustomerOrderManager.getOrderActions(order)}
                                </div>
                            </div>
                        </div>
                    `).join('');
                }
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
        
        function openRatingModal(orderId) {
            document.getElementById('rating-order-id').value = orderId;
            document.getElementById('rating-value').value = '';
            document.getElementById('comment').value = '';
            updateStars(0);
            document.querySelector('.rating-text').textContent = 'Select your rating';
            
            const modal = document.getElementById('rating-modal');
            modal.classList.add('show');
            document.body.style.overflow = 'hidden';
        }
        
        function closeRatingModal() {
            const modal = document.getElementById('rating-modal');
            modal.classList.remove('show');
            document.body.style.overflow = '';
        }
        
        function setupStarRating() {
            const stars = document.querySelectorAll('.star');
            const ratingText = document.querySelector('.rating-text');
            const ratingTexts = {
                1: 'Poor - Not satisfied',
                2: 'Fair - Could be better',
                3: 'Good - Satisfied',
                4: 'Very Good - Happy',
                5: 'Excellent - Very happy'
            };

            stars.forEach(star => {
                star.addEventListener('mouseover', function() {
                    const rating = this.dataset.rating;
                    updateStars(rating);
                    ratingText.textContent = ratingTexts[rating];
                });

                star.addEventListener('click', function() {
                    const rating = this.dataset.rating;
                    document.getElementById('rating-value').value = rating;
                    updateStars(rating, true);
                    ratingText.textContent = ratingTexts[rating];
                });
            });

            document.querySelector('.star-rating').addEventListener('mouseleave', function() {
                const currentRating = document.getElementById('rating-value').value;
                if (currentRating) {
                    updateStars(currentRating);
                    ratingText.textContent = ratingTexts[currentRating];
                } else {
                    updateStars(0);
                    ratingText.textContent = 'Select your rating';
                }
            });
        }
        
        function updateStars(rating, permanent = false) {
            const stars = document.querySelectorAll('.star');
            stars.forEach(star => {
                const starRating = star.dataset.rating;
                if (starRating <= rating) {
                    star.style.color = '#ffc107';
                    if (permanent) star.classList.add('active');
                } else {
                    star.style.color = '#ddd';
                    if (permanent) star.classList.remove('active');
                }
            });
        }
        
        // Handle rating form submission
        document.getElementById('rating-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const orderId = document.getElementById('rating-order-id').value;
            const rating = document.getElementById('rating-value').value;
            const comment = document.getElementById('comment').value;
            
            if (!rating) {
                Utils.showAlert('Please select a rating', 'warning');
                return;
            }
            
            try {
                const response = await API.rateOrder(orderId, rating, comment);
                if (response.success) {
                    Utils.showAlert('Thank you for your feedback!', 'success');
                    closeRatingModal();
                    loadOrders(); // Refresh orders list
                } else {
                    throw new Error(response.error || 'Failed to submit rating');
                }
            } catch (error) {
                Utils.showAlert('Error submitting rating: ' + error.message, 'danger');
            }
        });
        
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

        function formatTimeToAMPM(time) {
            if (!time) return '';
            const [hours, minutes] = time.split(':');
            const hour = parseInt(hours);
            const ampm = hour >= 12 ? 'PM' : 'AM';
            const formattedHour = hour % 12 || 12;
            return `${formattedHour}:${minutes} ${ampm}`;
        }

        function formatStatus(status) {
            const statusMap = {
                'requested': 'Pickup Requested',
                'picked_up': 'Picked Up',
                'washing': 'Being Washed',
                'delivered': 'Delivered'
            };
            return statusMap[status] || status.charAt(0).toUpperCase() + status.slice(1);
        }
    </script>
    
    <style>
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .modal.show {
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 1;
        }

        .modal-content {
            background: #fff;
            border-radius: 12px;
            width: 90%;
            max-width: 500px;
            position: relative;
            transform: translateY(-20px);
            transition: transform 0.3s ease;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .modal.show .modal-content {
            transform: translateY(0);
        }

        .modal-header {
            padding: 20px;
            border-bottom: 1px solid #eee;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .modal-title {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .modal-title i {
            font-size: 24px;
            color: #ffc107;
        }

        .modal-title h4 {
            margin: 0;
            font-size: 1.25rem;
            color: #333;
        }

        .modal-close {
            background: none;
            border: none;
            padding: 8px;
            cursor: pointer;
            color: #666;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .modal-close:hover {
            background: #f8f9fa;
            color: #333;
        }

        .modal-body {
            padding: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
        }

        .text-muted {
            color: #6c757d;
            font-weight: normal;
            font-size: 0.9em;
        }

        .rating-group {
            text-align: center;
            padding: 20px 0;
        }

        .star-rating {
            display: flex;
            gap: 8px;
            justify-content: center;
            margin: 15px 0;
        }

        .star {
            font-size: 32px;
            color: #ddd;
            cursor: pointer;
            transition: color 0.2s ease;
        }

        .star:hover,
        .star.active {
            color: #ffc107;
        }

        .star:hover ~ .star {
            color: #ddd;
        }

        .rating-text {
            margin-top: 8px;
            color: #666;
            font-size: 0.9em;
        }

        .form-control {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #80bdff;
            outline: none;
            box-shadow: 0 0 0 3px rgba(0,123,255,0.25);
        }

        textarea.form-control {
            resize: vertical;
            min-height: 100px;
        }

        .modal-footer {
            padding: 20px 0 0;
            display: flex;
            justify-content: flex-end;
            gap: 12px;
        }

        .btn {
            padding: 10px 20px;
            border-radius: 6px;
            font-size: 0.9rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
        }

        .btn i {
            font-size: 1rem;
        }

        .btn-secondary {
            background: #f8f9fa;
            color: #333;
        }

        .btn-secondary:hover {
            background: #e2e6ea;
        }

        .btn-primary {
            background: #007bff;
            color: white;
        }

        .btn-primary:hover {
            background: #0056b3;
        }

        @media (max-width: 576px) {
            .modal-content {
                width: 95%;
                margin: 10px;
            }

            .star {
                font-size: 28px;
            }

            .modal-footer {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</body>
</html> 