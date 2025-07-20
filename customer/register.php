<?php
require_once '../config/config.php';

// Redirect if already logged in
if (isLoggedIn() && getUserRole() === 'customer') {
    header('Location: dashboard.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Registration - Door2Dry</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="auth-body register-page">
    <div id="alert-container"></div>
    
    <!-- Background Elements -->
    <div class="auth-background">
        <div class="auth-shapes">
            <div class="auth-shape auth-shape-1"></div>
            <div class="auth-shape auth-shape-2"></div>
            <div class="auth-shape auth-shape-3"></div>
        </div>
    </div>

    <!-- Header -->
    <header class="auth-header">
        <div class="container">
            <a href="../index.php" class="auth-logo">
                <i class="fas fa-tshirt"></i>
                <span>Door2Dry</span>
            </a>
            <nav class="auth-nav">
                <a href="../driver/login.php" class="nav-link">
                    <i class="fas fa-truck"></i>
                    Driver Portal
                </a>
            </nav>
        </div>
    </header>
    
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-card-header">
                <div class="auth-icon">
                    <i class="fas fa-user-plus"></i>
                </div>
                <h1 class="auth-title">Create Account</h1>
                <p class="auth-subtitle">Join Door2Dry for convenient laundry service</p>
            </div>

            <div class="auth-card-body">
                <form id="register-form" class="auth-form">
                    <div class="form-field">
                        <label for="name" class="field-label">Full Name</label>
                        <div class="field-wrapper">
                            <i class="fas fa-user field-icon"></i>
                            <input type="text" class="field-input" id="name" name="name" placeholder="Enter your full name" required>
                        </div>
                    </div>

                    <div class="form-field">
                        <label for="email" class="field-label">Email Address</label>
                        <div class="field-wrapper">
                            <i class="fas fa-envelope field-icon"></i>
                            <input type="email" class="field-input" id="email" name="email" placeholder="Enter your email" required>
                        </div>
                    </div>
                    
                    <div class="form-field">
                        <label for="password" class="field-label">Password</label>
                        <div class="field-wrapper">
                            <i class="fas fa-lock field-icon"></i>
                            <input type="password" class="field-input" id="password" name="password" placeholder="Create a password" required>
                            <button type="button" class="field-toggle" onclick="togglePassword()">
                                <i class="fas fa-eye" id="password-toggle-icon"></i>
                            </button>
                        </div>
                        <small class="field-hint">Minimum 6 characters</small>
                    </div>

                    <div class="form-field">
                        <label for="phone" class="field-label">Phone Number</label>
                        <div class="field-wrapper">
                            <i class="fas fa-phone field-icon"></i>
                            <input type="tel" class="field-input" id="phone" name="phone" placeholder="Enter your phone number">
                        </div>
                    </div>

                    <div class="form-field">
                        <label for="address" class="field-label">Address</label>
                        <div class="field-wrapper">
                            <i class="fas fa-map-marker-alt field-icon"></i>
                            <input type="text" class="field-input" id="address" name="address" placeholder="Enter your full address">
                            <input type="hidden" id="address_lat" name="address_lat">
                            <input type="hidden" id="address_lng" name="address_lng">
                        </div>
                        <div id="address-validation" class="address-validation"></div>
                        <small class="field-hint">Start typing your address to see suggestions</small>
                    </div>

                    <!-- Interactive Map for Address Selection -->
                    <div class="form-field">
                        <label class="field-label">Pin Your Exact Location</label>
                        <div class="map-controls">
                            <button type="button" class="map-btn" onclick="getCurrentLocationForAddress()">
                                <i class="fas fa-location-arrow"></i>
                                Use My Location
                            </button>
                        </div>
                        <div id="address-map" class="map-container"></div>
                        <div id="map-status" class="map-status">
                            <i class="fas fa-info-circle"></i>
                            <span>Click on the map to pin your exact location</span>
                        </div>
                    </div>
                    
                    <button type="submit" class="auth-btn primary">
                        <span class="btn-text">Create Account</span>
                        <i class="fas fa-arrow-right btn-icon"></i>
                    </button>
                </form>
            </div>

            <div class="auth-card-footer">
                <div class="auth-divider">
                    <span>Already have an account?</span>
                </div>
                <a href="login.php" class="auth-btn secondary">
                    <i class="fas fa-sign-in-alt"></i>
                    Sign In
                </a>
            </div>
        </div>
    </div>

    <script>
        // Inject Google Maps API key for JavaScript
        window.GOOGLE_MAPS_API_KEY = '<?php echo GOOGLE_MAPS_API_KEY; ?>';
    </script>
    <script src="../assets/js/app.js"></script>
    <script src="../assets/js/maps.js"></script>
    <script>
        // Initialize maps when DOM is loaded
        document.addEventListener('DOMContentLoaded', async function() {
            try {
                // Initialize Google Maps API
                await window.mapsService.init();
                
                // Trigger ready event
                document.dispatchEvent(new Event('googleMapsReady'));
            } catch (error) {
                console.error('Failed to initialize Google Maps:', error);
                // Trigger error event
                document.dispatchEvent(new CustomEvent('googleMapsError', {
                    detail: error.message
                }));
            }
        });

        // Password toggle functionality
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('password-toggle-icon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.className = 'fas fa-eye-slash';
            } else {
                passwordInput.type = 'password';
                toggleIcon.className = 'fas fa-eye';
            }
        }

        // Form submission
        document.getElementById('register-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            // Validate form
            const validationRules = {
                name: {
                    required: 'Full name is required'
                },
                email: {
                    required: 'Email is required',
                    email: 'Please enter a valid email address'
                },
                password: {
                    required: 'Password is required',
                    minLength: {
                        value: 6,
                        message: 'Password must be at least 6 characters'
                    }
                },
                phone: {
                    required: 'Phone number is required'
                },
                address: {
                    required: 'Address is required'
                }
            };
            
            if (!FormValidator.validateForm('register-form', validationRules)) {
                return;
            }
            
            try {
                const submitBtn = this.querySelector('button[type="submit"]');
                const btnText = submitBtn.querySelector('.btn-text');
                const btnIcon = submitBtn.querySelector('.btn-icon');
                
                // Update button state
                submitBtn.disabled = true;
                submitBtn.classList.add('loading');
                btnText.textContent = 'Creating Account...';
                btnIcon.className = 'fas fa-spinner fa-spin btn-icon';

                // Prepare registration data
                const registrationData = {
                    name: formData.get('name'),
                    email: formData.get('email'),
                    password: formData.get('password'),
                    phone: formData.get('phone'),
                    address: formData.get('address'),
                    address_lat: formData.get('address_lat'),
                    address_lng: formData.get('address_lng'),
                    role: 'customer'
                };

                // Send registration request
                const response = await API.register(registrationData);

                if (response.success) {
                    btnText.textContent = 'Success!';
                    btnIcon.className = 'fas fa-check btn-icon';
                    Utils.showAlert('Registration successful! Redirecting...', 'success');
                    
                    setTimeout(() => {
                        window.location.href = 'dashboard.php';
                    }, 1000);
                } else {
                    throw new Error(response.error || 'Registration failed');
                }
            } catch (error) {
                Utils.showAlert(error.message || 'Registration failed', 'danger');
                
                // Reset button
                const submitBtn = this.querySelector('button[type="submit"]');
                const btnText = submitBtn.querySelector('.btn-text');
                const btnIcon = submitBtn.querySelector('.btn-icon');
                
                submitBtn.disabled = false;
                submitBtn.classList.remove('loading');
                btnText.textContent = 'Create Account';
                btnIcon.className = 'fas fa-arrow-right btn-icon';
            }
        });

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
                            <i class="fas fa-exclamation-triangle"></i>
                            <span>Address autocomplete unavailable. You can still enter your address manually.</span>
                        </div>
                    `;
                }
                
                // Show map error message
                const mapContainer = document.getElementById('address-map');
                if (mapContainer) {
                    mapContainer.innerHTML = `
                        <div class="map-error">
                            <i class="fas fa-map-marked-alt"></i>
                            <span>Map unavailable. Please enter your address manually.</span>
                        </div>
                    `;
                }
            });
        });

        function initializeAddressAutocomplete() {
            // Initialize address autocomplete
            const autocomplete = window.mapsService.initAddressAutocomplete('address', {
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
                if (addressMap) {
                    updateMapMarker(lat, lng, address, 'autocomplete');
                }
                
                console.log('Address selected for registration:', { address, lat, lng });
            });
        }
        
        function initializeAddressMap() {
            // Use the global mapsService instance
            
            // Default location: Kuala Lumpur, Malaysia
            const defaultLocation = { lat: 3.1390, lng: 101.6869 };
            
            // Initialize map
            addressMap = window.mapsService.initMap('address-map', defaultLocation, 13);
            
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
                if (window.mapsService.reverseGeocode) {
                    window.mapsService.reverseGeocode(lat, lng).then(address => {
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
                mapStatus.innerHTML = '<small><i class="fas fa-map-marker-alt"></i> Click anywhere on the map to pin your location</small>';
                mapStatus.className = 'text-success';
            }
        }
        
        function updateMapMarker(lat, lng, address, source) {
            const location = { lat, lng };
            
            // Remove existing marker
            if (addressMarker) {
                addressMarker.setMap(null);
            }
            
            // Add new marker
            addressMarker = window.mapsService.addMarker(location.lat, location.lng, {
                title: 'Your Location',
                infoContent: `
                    <div class="map-info-window">
                        <h6>📍 Your Location</h6>
                        ${address ? `<p><strong>Address:</strong> ${address}</p>` : ''}
                        <p><strong>Coordinates:</strong> ${lat.toFixed(6)}, ${lng.toFixed(6)}</p>
                        <small>Source: ${source}</small>
                    </div>
                `
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
        
        // Get current location for address
        async function getCurrentLocationForAddress() {
            const button = document.querySelector('button[onclick="getCurrentLocationForAddress()"]');
            const originalText = button.innerHTML;
            
            try {
                // Update button state
                button.disabled = true;
                button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Getting location...';
                
                // Get current location
                const location = await window.mapsService.getCurrentLocation();
                
                // Update map marker
                updateMapMarker(location.lat, location.lng, null, 'gps');
                
                // Update hidden fields
                document.getElementById('address_lat').value = location.lat;
                document.getElementById('address_lng').value = location.lng;
                
                // Try to reverse geocode to get address
                if (window.mapsService.reverseGeocode) {
                    try {
                        const address = await window.mapsService.reverseGeocode(location.lat, location.lng);
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
                            mapStatus.innerHTML = '<small><i class="fas fa-exclamation-triangle text-warning"></i> Location found but address not available. Please enter address manually.</small>';
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
    </script>
</body>
</html> 