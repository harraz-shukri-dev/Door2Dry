// Door2Dry Smart Laundry Service - Main JavaScript

// Configuration  
const API_BASE_URL = '/api/';

// Google Maps API Key (injected from PHP)
const GOOGLE_MAPS_API_KEY =
  typeof window.GOOGLE_MAPS_API_KEY !== "undefined"
    ? window.GOOGLE_MAPS_API_KEY
    : "YOUR_GOOGLE_MAPS_API_KEY_HERE";

// Utility Functions
const Utils = {
    showAlert: function(message, type = 'info') {
        const alertContainer = document.getElementById('alert-container');
        
        // Create alert box
        const alertBox = document.createElement('div');
        alertBox.className = `alert-box ${type}`;
        
        // Create alert icon based on type
        const icon = document.createElement('i');
        icon.className = 'alert-icon fas ' + this.getAlertIcon(type);
        
        // Create content container
        const content = document.createElement('div');
        content.className = 'alert-content';
        
        // Create message
        const messageEl = document.createElement('p');
        messageEl.className = 'alert-message';
        messageEl.textContent = message;
        content.appendChild(messageEl);
        
        // Create close button
        const closeBtn = document.createElement('button');
        closeBtn.className = 'alert-close';
        closeBtn.innerHTML = '<i class="fas fa-times"></i>';
        closeBtn.onclick = () => this.removeAlert(alertBox);
        
        // Assemble alert box
        alertBox.appendChild(icon);
        alertBox.appendChild(content);
        alertBox.appendChild(closeBtn);
        
        // Add to container
        alertContainer.appendChild(alertBox);
        
        // Auto remove after delay
        setTimeout(() => {
            if (alertBox.parentNode === alertContainer) {
                this.removeAlert(alertBox);
            }
        }, 5000);
        
        return alertBox;
    },
    
    removeAlert: function(alertBox) {
        alertBox.classList.add('removing');
        setTimeout(() => {
            if (alertBox.parentNode) {
                alertBox.parentNode.removeChild(alertBox);
            }
        }, 300);
    },
    
    getAlertIcon: function(type) {
        switch (type) {
            case 'success':
                return 'fa-check-circle';
            case 'danger':
                return 'fa-exclamation-circle';
            case 'warning':
                return 'fa-exclamation-triangle';
            case 'info':
            default:
                return 'fa-info-circle';
        }
    },

    showLoading(elementId) {
        const element = document.getElementById(elementId);
        if (element) {
            element.innerHTML = '<div class="loading"><div class="spinner"></div></div>';
        }
    },

    hideLoading(elementId) {
        const element = document.getElementById(elementId);
        if (element) {
            element.innerHTML = '';
        }
    },

    formatDate(dateString) {
        if (!dateString) return 'N/A';
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    },

    getStatusBadge(status) {
        const badges = {
            'requested': 'badge-requested',
            'picked_up': 'badge-picked-up',
            'washing': 'badge-washing',
            'delivered': 'badge-delivered'
        };
        return badges[status] || 'badge-secondary';
    },

    sanitizeInput(input) {
        const div = document.createElement('div');
        div.textContent = input;
        return div.innerHTML;
    },

    validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    },

    formatTimeToAMPM(time) {
        if (!time) return 'N/A';
        
        // Handle both HH:mm:ss and HH:mm formats
        const timeParts = time.split(':');
        let hours = parseInt(timeParts[0]);
        const minutes = timeParts[1];
        
        // Convert to 12-hour format
        const ampm = hours >= 12 ? 'PM' : 'AM';
        hours = hours % 12;
        hours = hours ? hours : 12; // Convert 0 to 12
        
        // Format with leading zeros for hours and minutes
        const formattedHours = hours.toString().padStart(2, '0');
        
        return `${formattedHours}:${minutes} ${ampm}`;
    }
};

// API Communication Class
class API {
    static async request(endpoint, options = {}, retries = 2) {
        const url = API_BASE_URL + endpoint;
        
        const defaultOptions = {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
            },
            credentials: 'same-origin'
        };

        const requestOptions = { ...defaultOptions, ...options };

        try {
            const response = await fetch(url, requestOptions);
            let data;
            
            try {
                data = await response.json();
            } catch (parseError) {
                console.error('Failed to parse JSON response:', parseError);
                throw new Error('Invalid server response format');
            }

            // Check for specific error conditions
            if (!response.ok) {
                const errorMessage = data.error || `Request failed with status ${response.status}`;
                
                // Check if we should retry
                if (retries > 0 && response.status >= 500) {
                    console.log(`Retrying request to ${endpoint}. Attempts remaining: ${retries}`);
                    await new Promise(resolve => setTimeout(resolve, 1000)); // Wait 1 second before retry
                    return this.request(endpoint, options, retries - 1);
                }
                
                throw new Error(errorMessage);
            }

            // Validate response format
            if (!data.hasOwnProperty('success')) {
                console.error('Invalid API response format:', data);
                throw new Error('Invalid response format from server');
            }

            return data;
        } catch (error) {
            // Log the error for debugging
            console.error('API Error:', error);
            
            // Check for network errors
            if (!navigator.onLine) {
                throw new Error('No internet connection. Please check your network and try again.');
            }
            
            // Check for timeout
            if (error.name === 'TimeoutError') {
                throw new Error('Request timed out. Please try again.');
            }
            
            // Re-throw the error with a user-friendly message
            throw new Error(error.message || 'An unexpected error occurred. Please try again later.');
        }
    }

    static async login(email, password) {
        return this.request('login.php', {
            method: 'POST',
            body: JSON.stringify({ email, password })
        });
    }

    static async register(userData) {
        return this.request('register.php', {
            method: 'POST',
            body: JSON.stringify(userData)
        });
    }

    static async createOrder(orderData) {
        return this.request('create_order.php', {
            method: 'POST',
            body: JSON.stringify(orderData)
        });
    }

    static async getOrders(status = null) {
        try {
            let endpoint = 'get_orders.php';
            if (status) {
                endpoint += `?status=${encodeURIComponent(status)}`;
            }
            return await this.request(endpoint);
        } catch (error) {
            console.error('Error fetching orders:', error);
            throw new Error(`Failed to load ${status || ''} orders: ${error.message}`);
        }
    }

    static async updateStatus(orderId, status) {
        const data = { order_id: orderId, status };
        return this.request('update_status.php', {
            method: 'POST',
            body: JSON.stringify(data)
        });
    }

    static async rateOrder(orderId, rating, comment = null) {
        const data = { order_id: orderId, rating };
        if (comment) {
            data.comment = comment;
        }
        return this.request('rate_order.php', {
            method: 'POST',
            body: JSON.stringify(data)
        });
    }

    static async logout() {
        return this.request('logout.php', {
            method: 'POST'
        });
    }

    static async getUserProfile() {
        return this.request('get_user_profile.php');
    }
}

// Order Management Class
class OrderManager {
    static renderOrders(orders, containerId) {
        const container = document.getElementById(containerId);
        if (!container) return;

        if (orders.length === 0) {
            container.innerHTML = `
                <div class="card">
                    <div class="card-body text-center">
                        <p>No orders found.</p>
                    </div>
                </div>
            `;
            return;
        }

        const ordersHtml = orders.map(order => this.renderOrderCard(order)).join('');
        container.innerHTML = ordersHtml;
    }

    static renderOrderCard(order) {
        const statusBadge = Utils.getStatusBadge(order.status);
        const createdAt = Utils.formatDate(order.created_at);
        const pickupDate = order.pickup_date ? Utils.formatDate(order.pickup_date) : 'Not set';
        const deliveryDate = order.delivery_date ? Utils.formatDate(order.delivery_date) : 'Not set';

        return `
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Order #${order.id}</h5>
                    <span class="badge ${statusBadge}">${order.status.replace('_', ' ')}</span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Address:</strong> ${order.address}</p>
                            <p><strong>Items:</strong> ${order.items}</p>
                            <p><strong>Special Notes:</strong> ${order.special_notes || 'None'}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Created:</strong> ${createdAt}</p>
                            <p><strong>Pickup Date:</strong> ${pickupDate}</p>
                            <p><strong>Delivery Date:</strong> ${deliveryDate}</p>
                            ${order.driver_name ? `<p><strong>Driver:</strong> ${order.driver_name}</p>` : ''}
                            ${order.customer_name ? `<p><strong>Customer:</strong> ${order.customer_name}</p>` : ''}
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-end align-items-center">
                    <div>
                        ${this.getOrderActions(order)}
                    </div>
                </div>
            </div>
        `;
    }

    static getOrderActions(order) {
        // Return action buttons based on order status and user role
        // This will be customized in each portal
        return '';
    }

    static async loadOrders(containerId, status = null) {
        Utils.showLoading(containerId);
        
        try {
            const response = await API.getOrders(status);
            this.renderOrders(response.orders, containerId);
        } catch (error) {
            Utils.showAlert('Error loading orders: ' + error.message, 'danger');
            document.getElementById(containerId).innerHTML = `
                <div class="alert alert-danger">
                    Error loading orders: ${error.message}
                </div>
            `;
        }
    }
}

// Form Validation Class
class FormValidator {
    static validateForm(formId, rules) {
        const form = document.getElementById(formId);
        if (!form) return false;

        let isValid = true;
        const formData = new FormData(form);

        for (const field in rules) {
            const value = formData.get(field);
            const fieldRules = rules[field];
            const fieldElement = form.querySelector(`[name="${field}"]`);

            // Remove existing error classes
            fieldElement.classList.remove('is-invalid');

            // Check required
            if (fieldRules.required && (!value || value.trim() === '')) {
                this.showFieldError(fieldElement, fieldRules.required);
                isValid = false;
                continue;
            }

            // Check email
            if (fieldRules.email && value && !Utils.validateEmail(value)) {
                this.showFieldError(fieldElement, fieldRules.email);
                isValid = false;
                continue;
            }

            // Check min length
            if (fieldRules.minLength && value && value.length < fieldRules.minLength) {
                this.showFieldError(fieldElement, fieldRules.minLength);
                isValid = false;
                continue;
            }

            // Check max length
            if (fieldRules.maxLength && value && value.length > fieldRules.maxLength) {
                this.showFieldError(fieldElement, fieldRules.maxLength);
                isValid = false;
                continue;
            }
        }

        return isValid;
    }

    static showFieldError(fieldElement, message) {
        fieldElement.classList.add('is-invalid');
        
        // Remove existing error message
        const existingError = fieldElement.parentElement.querySelector('.invalid-feedback');
        if (existingError) {
            existingError.remove();
        }

        // Add new error message
        const errorDiv = document.createElement('div');
        errorDiv.className = 'invalid-feedback';
        errorDiv.textContent = message;
        fieldElement.parentElement.appendChild(errorDiv);
    }

    static clearErrors(formId) {
        const form = document.getElementById(formId);
        if (!form) return;

        const invalidFields = form.querySelectorAll('.is-invalid');
        invalidFields.forEach(field => {
            field.classList.remove('is-invalid');
        });

        const errorMessages = form.querySelectorAll('.invalid-feedback');
        errorMessages.forEach(error => {
            error.remove();
        });
    }
}

// Global Functions


// Initialize app when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Create alert container if it doesn't exist
    if (!document.getElementById('alert-container')) {
        const alertContainer = document.createElement('div');
        alertContainer.id = 'alert-container';
        alertContainer.className = 'container mt-3';
        document.body.insertBefore(alertContainer, document.body.firstChild);
    }

    // Add event listeners for forms
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            // Form submission will be handled by specific page scripts
        });
    });

    // Auto-refresh orders every 30 seconds if on dashboard
    if (document.getElementById('orders-container')) {
        setInterval(() => {
            if (document.getElementById('orders-container')) {
                OrderManager.loadOrders('orders-container');
            }
        }, 30000);
    }
});

// Export for use in other scripts
window.Utils = Utils;
window.API = API;
window.OrderManager = OrderManager;
window.FormValidator = FormValidator;

// Order Tracking Functions
async function showAllMyOrders() {
    try {
        // Update button states
        document.querySelectorAll('.map-controls .control-btn').forEach(btn => btn.classList.remove('active'));
        document.querySelector('.map-controls .control-btn:first-child').classList.add('active');

        // Get all orders
        const response = await API.getOrders();
        if (response.success) {
            // Clear existing markers
            window.mapsService.clearMarkers();
            
            // Add markers for each order
            response.orders.forEach(order => {
                const { lat, lng } = order.pickup_location;
                const markerOptions = {
                    title: `Order #${order.id}`,
                    infoContent: `
                        <div class="map-info-window">
                            <h6>Order #${order.id}</h6>
                            <p><strong>Status:</strong> ${order.status}</p>
                            <p><strong>Pickup:</strong> ${order.pickup_address}</p>
                            <p><strong>Date:</strong> ${Utils.formatDate(order.created_at)}</p>
                        </div>
                    `
                };
                window.mapsService.addMarker(lat, lng, markerOptions);
            });

            // Fit map to show all markers
            window.mapsService.fitMapToMarkers();
        }
    } catch (error) {
        console.error('Error loading orders:', error);
        Utils.showAlert('Failed to load orders on map', 'danger');
    }
}

async function showActiveOrders() {
    try {
        // Update button states
        document.querySelectorAll('.map-controls .control-btn').forEach(btn => btn.classList.remove('active'));
        document.querySelector('.map-controls .control-btn:nth-child(2)').classList.add('active');

        // Get active orders (status not 'delivered')
        const response = await API.getOrders();
        if (response.success) {
            // Filter active orders
            const activeOrders = response.orders.filter(order => order.status !== 'delivered');
            
            // Clear existing markers
            window.mapsService.clearMarkers();
            
            // Add markers for active orders
            activeOrders.forEach(order => {
                const { lat, lng } = order.pickup_location;
                const markerOptions = {
                    title: `Order #${order.id}`,
                    infoContent: `
                        <div class="map-info-window">
                            <h6>Order #${order.id}</h6>
                            <p><strong>Status:</strong> ${order.status}</p>
                            <p><strong>Pickup:</strong> ${order.pickup_address}</p>
                            <p><strong>Date:</strong> ${Utils.formatDate(order.created_at)}</p>
                        </div>
                    `
                };
                window.mapsService.addMarker(lat, lng, markerOptions);
            });

            // Fit map to show all markers
            window.mapsService.fitMapToMarkers();
        }
    } catch (error) {
        console.error('Error loading active orders:', error);
        Utils.showAlert('Failed to load active orders on map', 'danger');
    }
}

async function centerOnMyAddress() {
    try {
        // Update button states
        document.querySelectorAll('.map-controls .control-btn').forEach(btn => btn.classList.remove('active'));
        document.querySelector('.map-controls .control-btn:last-child').classList.add('active');

        // Get user profile to get their address
        const response = await API.getUserProfile();
        if (response.success && response.user.address_lat && response.user.address_lng) {
            // Center map on user's address
            window.mapsService.centerMap(
                parseFloat(response.user.address_lat),
                parseFloat(response.user.address_lng),
                15
            );

            // Add marker for user's address
            window.mapsService.addMarker(
                parseFloat(response.user.address_lat),
                parseFloat(response.user.address_lng),
                {
                    title: 'Your Location',
                    infoContent: `
                        <div class="map-info-window">
                            <h6>📍 Your Location</h6>
                            <p>${response.user.address}</p>
                        </div>
                    `
                }
            );
        } else {
            Utils.showAlert('Could not find your address', 'warning');
        }
    } catch (error) {
        console.error('Error centering on address:', error);
        Utils.showAlert('Failed to center map on your address', 'danger');
    }
}

// Initialize map when DOM is loaded
document.addEventListener('DOMContentLoaded', async function() {
    // Initialize map if we're on the dashboard
    const mapContainer = document.getElementById('customer-map-container');
    if (mapContainer) {
        try {
            // Initialize Google Maps
            await window.mapsService.init();
            
            // Initialize map in container
            const map = window.mapsService.initMap('customer-map-container', {
                zoom: 12,
                styles: [
                    {
                        featureType: "poi",
                        elementType: "labels",
                        stylers: [{ visibility: "off" }]
                    }
                ]
            });

            if (!map) {
                throw new Error('Map initialization failed');
            }

            // Wait a moment for the map to be fully ready
            await new Promise(resolve => setTimeout(resolve, 500));
            
            try {
                await showAllMyOrders();
            } catch (error) {
                console.error('Error showing initial orders:', error);
                Utils.showAlert('Failed to load initial orders', 'warning');
            }
        } catch (error) {
            console.error('Failed to initialize map:', error);
            Utils.showAlert('Failed to load the map', 'danger');
        }
    }
}); 