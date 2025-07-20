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
    <title>New Order - Door2Dry</title>
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
                        <a href="new-order.php" class="nav-item active">
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
                    <h1 class="welcome-title">Create New Order</h1>
                    <p class="welcome-subtitle">Schedule a pickup for your laundry service</p>
                </div>
            </div>

            <!-- Order Form Section -->
            <div class="section-card">
                <form id="order-form" class="order-form">
                    <div class="form-group">
                        <label for="address" class="form-label">
                            <i class="fas fa-map-marker-alt"></i>
                            Pickup Address
                        </label>
                        <div class="address-input-container">
                            <input type="text" class="form-control" id="address" name="address" required 
                                   placeholder="Enter full address including street, city, state, zip code">
                        </div>
                        <input type="hidden" id="address_lat" name="address_lat">
                        <input type="hidden" id="address_lng" name="address_lng">
                        <div id="address-validation" class="address-validation"></div>
                        <small class="form-text text-muted">
                            <i class="fas fa-info-circle"></i>
                            Start typing your address to see suggestions, or click on the map below
                        </small>
                    </div>
                    
                    <!-- Interactive Map for Pickup Location -->
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-map"></i>
                            Pin Your Pickup Location
                        </label>
                        <div class="map-controls">
                            <button type="button" class="control-btn" onclick="getCurrentLocationForPickup()">
                                <i class="fas fa-location-arrow"></i>
                                <span>Use My Location</span>
                            </button>
                        </div>
                        <div id="address-map" class="map-element"></div>
                        <div id="map-status" class="text-info">
                            <small><i class="fas fa-info-circle"></i> Click on the map to pin your pickup location</small>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="items" class="form-label">
                            <i class="fas fa-tshirt"></i>
                            Items Description
                        </label>
                        <textarea class="form-control" id="items" name="items" rows="4" required 
                                  placeholder="e.g., 3 shirts, 2 pants, 1 dress, 4 towels, bedsheets"></textarea>
                        <small class="form-text text-muted">
                            <i class="fas fa-info-circle"></i>
                            Please describe the items and quantities
                        </small>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="pickup_date" class="form-label">
                                <i class="fas fa-calendar"></i>
                                Preferred Pickup Date
                            </label>
                            <input type="date" class="form-control" id="pickup_date" name="pickup_date" 
                                   min="<?php echo date('Y-m-d'); ?>">
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle"></i>
                                Select your preferred date
                            </small>
                        </div>
                        
                        <div class="form-group col-md-6">
                            <label for="pickup_time" class="form-label">
                                <i class="fas fa-clock"></i>
                                Preferred Pickup Time
                            </label>
                            <select class="form-control" id="pickup_time" name="pickup_time">
                                <option value="">Select time</option>
                                <option value="09:00">9:00 AM</option>
                                <option value="10:00">10:00 AM</option>
                                <option value="11:00">11:00 AM</option>
                                <option value="12:00">12:00 PM</option>
                                <option value="13:00">1:00 PM</option>
                                <option value="14:00">2:00 PM</option>
                                <option value="15:00">3:00 PM</option>
                                <option value="16:00">4:00 PM</option>
                                <option value="17:00">5:00 PM</option>
                            </select>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle"></i>
                                Select your preferred time
                            </small>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="special_notes" class="form-label">
                            <i class="fas fa-sticky-note"></i>
                            Special Instructions
                        </label>
                        <textarea class="form-control" id="special_notes" name="special_notes" rows="3" 
                                  placeholder="Any special washing instructions, delicate items, stain removal, etc."></textarea>
                    </div>
                    
                    <div class="info-card">
                        <div class="info-card-header">
                            <i class="fas fa-info-circle"></i>
                            <strong>Service Information</strong>
                        </div>
                        <ul class="info-list">
                            <li><i class="fas fa-clock"></i> Pickup within 24 hours of order placement</li>
                            <li><i class="fas fa-calendar-days"></i> Standard wash and fold: 2-3 business days</li>
                            <li><i class="fas fa-bell"></i> Notifications at each stage of the process</li>
                            <li><i class="fas fa-money-bill-wave"></i> Payment collected upon delivery</li>
                        </ul>
                    </div>
                    
                    <button type="submit" class="submit-btn">
                        <i class="fas fa-paper-plane"></i>
                        <span>Place Order</span>
                    </button>
                </form>
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
        // Initialize address autocomplete and map when Google Maps API is loaded
        let addressMap = null;
        let addressMarker = null;
        
        document.addEventListener('DOMContentLoaded', function() {
            // Listen for Google Maps ready event
            document.addEventListener('googleMapsReady', function() {
                initializeAddressAutocomplete();
                initializeAddressMap();
            });
            
            // Listen for Google Maps error event
            document.addEventListener('googleMapsError', function(event) {
                console.warn('Google Maps failed to load, address autocomplete disabled:', event.detail);
                
                // Show detailed error message to user
                const validation = document.getElementById('address-validation');
                if (validation) {
                    validation.innerHTML = `
                        <div class="maps-error-notice">
                            <h6>⚠️ Address autocomplete unavailable</h6>
                            <p>Google Maps failed to load. This might be due to:</p>
                            <ul>
                                <li>Ad blocker blocking Google services</li>
                                <li>Network connectivity issues</li>
                                <li>Browser privacy settings</li>
                            </ul>
                            <p><small>You can still enter your pickup address manually.</small></p>
                        </div>
                    `;
                    validation.className = 'address-validation warning';
                }
                
                // Show map error message
                const mapContainer = document.getElementById('address-map');
                if (mapContainer) {
                    mapContainer.innerHTML = `
                        <div class="alert alert-warning h-100 d-flex align-items-center justify-content-center">
                            <div class="text-center">
                                <h6>⚠️ Map unavailable</h6>
                                <p>Google Maps failed to load. Please enter your pickup address manually in the field above.</p>
                            </div>
                        </div>
                    `;
                }
            });
            
            // Fallback: Check if already loaded
            setTimeout(() => {
                if (typeof google !== 'undefined' && google.maps) {
                    initializeAddressAutocomplete();
                    initializeAddressMap();
                }
            }, 500);
        });

        function initializeAddressAutocomplete() {
            // Initialize address autocomplete
            const autocomplete = initAddressAutocomplete('address', {
                types: ['address'],
                componentRestrictions: { country: 'my' } // Restrict to Malaysia
            });

            // Listen for address selection
            document.addEventListener('addressSelected', function(event) {
                const { address, lat, lng } = event.detail;
                
                // Update validation status
                const validation = document.getElementById('address-validation');
                if (validation) {
                    validation.innerHTML = '<i class="fas fa-check-circle"></i> Address verified (autocomplete)';
                    validation.className = 'address-validation valid';
                }
                
                // Update map marker if map is loaded
                if (addressMap && mapsService) {
                    updateMapMarker(lat, lng, address, 'autocomplete');
                }
                
                console.log('Address selected for new order:', { address, lat, lng });
            });
        }
        
        function initializeAddressMap() {
            // Use the global mapsService instance
            
            // Default location: Kuala Lumpur, Malaysia
            const defaultLocation = { lat: 3.1390, lng: 101.6869 };
            
            // Initialize map
            addressMap = mapsService.initMap('address-map', defaultLocation, 13);
            
            // Add click listener to map
            addressMap.addListener('click', function(event) {
                const lat = event.latLng.lat();
                const lng = event.latLng.lng();
                
                // Update marker position
                updateMapMarker(lat, lng, null, 'map');
                
                // Update hidden fields
                document.getElementById('address_lat').value = lat;
                document.getElementById('address_lng').value = lng;
                
                // Try to reverse geocode to get address
                if (mapsService.reverseGeocode) {
                    mapsService.reverseGeocode(lat, lng).then(address => {
                        if (address) {
                            document.getElementById('address').value = address;
                            updateValidationStatus('map', address);
                        }
                    }).catch(error => {
                        console.warn('Reverse geocoding failed:', error);
                        updateValidationStatus('map', null);
                    });
                } else {
                    updateValidationStatus('map', null);
                }
            });
            
            // Update map status
            const mapStatus = document.getElementById('map-status');
            if (mapStatus) {
                mapStatus.innerHTML = '<small><i class="fas fa-map-marker-alt"></i> Click anywhere on the map to pin your pickup location</small>';
                mapStatus.className = 'text-success';
            }
        }
        
        function updateMapMarker(lat, lng, address, source) {
            const location = { lat, lng };
            
            // Remove existing marker
            if (addressMarker) {
                addressMarker.setMap(null);
            }
            
            // Determine marker color and icon based on source
            let markerColor = '#28a745'; // Default green for autocomplete
            let markerIcon = '📍';
            let sourceText = 'Address autocomplete';
            
            if (source === 'map') {
                markerColor = '#007bff'; // Blue for map clicks
                sourceText = 'Map click';
            } else if (source === 'gps') {
                markerColor = '#fd7e14'; // Orange for GPS
                markerIcon = '🎯';
                sourceText = 'GPS location';
            }

            // Add new marker
            addressMarker = mapsService.addMarker(lat, lng, {
                title: 'Pickup Location',
                infoContent: `
                    <div class="map-info-window">
                        <h6>${markerIcon} Pickup Location</h6>
                        ${address ? `<p><strong>Address:</strong> ${address}</p>` : ''}
                        <p><strong>Coordinates:</strong> ${lat.toFixed(6)}, ${lng.toFixed(6)}</p>
                        <small>Source: ${sourceText}</small>
                    </div>
                `,
                icon: {
                    url: `data:image/svg+xml;charset=UTF-8,${encodeURIComponent(`
                        <svg width="30" height="30" viewBox="0 0 30 30" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="15" cy="15" r="14" fill="${markerColor}" stroke="white" stroke-width="2"/>
                            <text x="15" y="20" text-anchor="middle" fill="white" font-size="16">${markerIcon}</text>
                        </svg>
                    `)}`,
                    scaledSize: new google.maps.Size(30, 30)
                }
            });
            
            // Center map on marker
            addressMap.setCenter(location);
            addressMap.setZoom(16);
        }
        
        function updateValidationStatus(source, address) {
            const validation = document.getElementById('address-validation');
            if (validation) {
                if (source === 'autocomplete') {
                    validation.innerHTML = '<i class="fas fa-check-circle"></i> Address verified (autocomplete)';
                    validation.className = 'address-validation valid';
                } else if (source === 'map') {
                    if (address) {
                        validation.innerHTML = '<i class="fas fa-map-marker-alt"></i> Location pinned (address found)';
                        validation.className = 'address-validation valid';
                    } else {
                        validation.innerHTML = '<i class="fas fa-map-marker-alt"></i> Location pinned (please enter address above)';
                        validation.className = 'address-validation warning';
                    }
                } else if (source === 'gps') {
                    if (address) {
                        validation.innerHTML = '<i class="fas fa-location-arrow"></i> GPS location found (address detected)';
                        validation.className = 'address-validation valid';
                    } else {
                        validation.innerHTML = '<i class="fas fa-location-arrow"></i> GPS location found (please enter address above)';
                        validation.className = 'address-validation warning';
                    }
                }
            }
        }
        
        // Get current location for pickup
        async function getCurrentLocationForPickup() {
            const button = document.querySelector('button[onclick="getCurrentLocationForPickup()"]');
            const originalText = button.innerHTML;
            
            try {
                // Update button state
                button.disabled = true;
                button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Getting location...';
                
                // Get current location
                const location = await mapsService.getCurrentLocation();
                
                // Update map marker
                updateMapMarker(location.lat, location.lng, null, 'gps');
                
                // Update hidden fields
                document.getElementById('address_lat').value = location.lat;
                document.getElementById('address_lng').value = location.lng;
                
                // Try to reverse geocode to get address
                if (mapsService.reverseGeocode) {
                    try {
                        const address = await mapsService.reverseGeocode(location.lat, location.lng);
                        document.getElementById('address').value = address;
                        updateValidationStatus('gps', address);
                        
                        // Update map status
                        const mapStatus = document.getElementById('map-status');
                        if (mapStatus) {
                            mapStatus.innerHTML = '<small><i class="fas fa-check-circle text-success"></i> Current location detected successfully!</small>';
                        }
                    } catch (error) {
                        console.warn('Reverse geocoding failed:', error);
                        updateValidationStatus('gps', null);
                        
                        // Update map status
                        const mapStatus = document.getElementById('map-status');
                        if (mapStatus) {
                            mapStatus.innerHTML = '<small><i class="fas fa-exclamation-triangle text-warning"></i> Location found but address not available. Please enter pickup address manually.</small>';
                        }
                    }
                } else {
                    updateValidationStatus('gps', null);
                }
                
                // Reset button
                button.disabled = false;
                button.innerHTML = originalText;
                
            } catch (error) {
                console.error('Error getting current location:', error);
                
                // Show error message
                const mapStatus = document.getElementById('map-status');
                if (mapStatus) {
                    mapStatus.innerHTML = `<small><i class="fas fa-exclamation-circle text-danger"></i> ${error.message || 'Unable to get your location'}</small>`;
                }
                
                // Reset button
                button.disabled = false;
                button.innerHTML = originalText;
            }
        }
 
        document.getElementById('order-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const orderData = {
                address: formData.get('address'),
                items: formData.get('items'),
                pickup_date: formData.get('pickup_date'),
                pickup_time: formData.get('pickup_time'),
                special_notes: formData.get('special_notes'),
                address_lat: formData.get('address_lat'),
                address_lng: formData.get('address_lng')
            };
            
            // Remove empty fields
            Object.keys(orderData).forEach(key => {
                if (!orderData[key] || orderData[key].trim() === '') {
                    delete orderData[key];
                }
            });
            
            // Validate form
            const validationRules = {
                address: {
                    required: 'Pickup address is required',
                    minLength: 'Please provide a complete address'
                },
                items: {
                    required: 'Items description is required',
                    minLength: 'Please describe the items to be washed'
                }
            };

            // Additional validation for pickup time
            const pickupDate = formData.get('pickup_date');
            const pickupTime = formData.get('pickup_time');
            if (pickupDate && !pickupTime) {
                Utils.showAlert('Please select a pickup time for your chosen date', 'warning');
                document.getElementById('pickup_time').focus();
                return;
            }
            
            if (!FormValidator.validateForm('order-form', validationRules)) {
                return;
            }
            
            try {
                const submitBtn = this.querySelector('button[type="submit"]');
                submitBtn.disabled = true;
                submitBtn.textContent = 'Creating Order...';
                
                const response = await API.createOrder(orderData);
                
                if (response.success) {
                    Utils.showAlert('Order created successfully! Your order ID is #' + response.order.id, 'success');
                    
                    // Show success message
                    setTimeout(() => {
                        Utils.showAlert('Your order has been created successfully!', 'success');
                    }, 1000);
                    
                    // Reset form
                    this.reset();
                    
                    // Redirect to orders page after delay
                    setTimeout(() => {
                        window.location.href = 'orders.php';
                    }, 3000);
                } else {
                    Utils.showAlert(response.message || 'Failed to create order', 'danger');
                }
            } catch (error) {
                Utils.showAlert('Error creating order: ' + error.message, 'danger');
            } finally {
                const submitBtn = this.querySelector('button[type="submit"]');
                submitBtn.disabled = false;
                submitBtn.textContent = 'Place Order';
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
        
        // Set minimum date to today and handle date/time field interaction
        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date().toISOString().split('T')[0];
            const pickupDate = document.getElementById('pickup_date');
            const pickupTime = document.getElementById('pickup_time');
            
            pickupDate.min = today;

            // Enable/disable time selection based on date
            pickupDate.addEventListener('change', function() {
                if (this.value) {
                    pickupTime.removeAttribute('disabled');
                    pickupTime.setAttribute('required', 'required');
                } else {
                    pickupTime.setAttribute('disabled', 'disabled');
                    pickupTime.removeAttribute('required');
                    pickupTime.value = '';
                }
            });

            // Initialize time field state
            if (!pickupDate.value) {
                pickupTime.setAttribute('disabled', 'disabled');
            }
        });
    </script>
</body>
</html> 