/**
 * Google Maps Integration for Door2Dry
 * Smart Laundry Pickup & Delivery System
 */

class GoogleMapsService {
    constructor() {
        this.map = null;
        this.autocomplete = null;
        this.directionsService = null;
        this.directionsRenderer = null;
        this.markers = [];
        this.infoWindows = [];
    }

    /**
     * Initialize Google Maps API
     */
    async init() {
        try {
            // Check if Google Maps API is already loaded
            if (typeof google !== 'undefined' && google.maps) {
                console.log('Google Maps API already loaded');
                return true;
            }

            // Load Google Maps API dynamically with proper async loading
            const script = document.createElement('script');
            script.src = `https://maps.googleapis.com/maps/api/js?key=${GOOGLE_MAPS_API_KEY}&libraries=places&loading=async&callback=initGoogleMapsCallback`;
            script.async = true;
            script.defer = true;
            document.head.appendChild(script);

            return new Promise((resolve, reject) => {
                window.initGoogleMapsCallback = () => {
                    console.log('Google Maps API loaded successfully');
                    resolve(true);
                };
                
                script.onerror = (error) => {
                    console.error('Failed to load Google Maps API:', error);
                    console.warn('This might be caused by:');
                    console.warn('1. Ad blocker blocking Google services');
                    console.warn('2. Network connectivity issues');
                    console.warn('3. Invalid API key or billing issues');
                    console.warn('4. API quotas exceeded');
                    reject(new Error('Google Maps API failed to load'));
                };
                
                // Add timeout for loading
                setTimeout(() => {
                    if (!window.google || !window.google.maps) {
                        console.error('Google Maps API loading timeout');
                        reject(new Error('Google Maps API loading timeout'));
                    }
                }, 10000); // 10 second timeout
            });
        } catch (error) {
            console.error('Error initializing Google Maps:', error);
            return false;
        }
    }

    /**
     * Initialize address autocomplete for input fields
     * Uses legacy Autocomplete API with future-proofing for PlaceAutocompleteElement
     */
    initAddressAutocomplete(inputId, options = {}) {
        try {
            const input = document.getElementById(inputId);
            if (!input) {
                console.error(`Input element with ID '${inputId}' not found`);
                return null;
            }

            // Validate that the element is an input element (not textarea)
            if (input.tagName.toLowerCase() !== 'input') {
                console.error(`Element with ID '${inputId}' is not an input element (found: ${input.tagName}). Google Maps Autocomplete requires an input element.`);
                console.warn('Consider changing textarea elements to input type="text" for autocomplete to work.');
                return null;
            }

            // For now, continue using the legacy API with enhanced error handling
            // Note: Legacy API is supported until at least March 2026
            const defaultOptions = {
                types: ['address'],
                fields: ['formatted_address', 'geometry', 'name', 'address_components'],
                componentRestrictions: { country: 'my' }, // Restrict to Malaysia
                ...options
            };

            this.autocomplete = new google.maps.places.Autocomplete(input, defaultOptions);

            // Add event listener for place selection
            this.autocomplete.addListener('place_changed', () => {
                const place = this.autocomplete.getPlace();
                if (place.geometry && place.geometry.location) {
                    this.handlePlaceSelected(place, inputId);
                } else if (place.name) {
                    // If no geometry but has name, try to geocode
                    this.geocodeAddress(place.name).then(result => {
                        this.handlePlaceSelected({
                            ...place,
                            geometry: { location: { lat: () => result.lat, lng: () => result.lng } },
                            formatted_address: result.formatted_address
                        }, inputId);
                    }).catch(error => {
                        console.warn('Could not geocode selected place:', error);
                        // Show user feedback
                        const validation = document.getElementById('address-validation');
                        if (validation) {
                            validation.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Could not verify address location';
                            validation.className = 'address-validation invalid';
                        }
                    });
                } else {
                    console.warn('Selected place has no geometry or location data');
                }
            });

            // Add CSS class for styling
            input.classList.add('gm-autocomplete-input');

            console.log(`Address autocomplete initialized successfully for ${inputId}`);
            return this.autocomplete;
        } catch (error) {
            console.error('Error initializing address autocomplete:', error);
            
            // Show user feedback about the error
            const validation = document.getElementById('address-validation');
            if (validation) {
                validation.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Address autocomplete failed to initialize';
                validation.className = 'address-validation invalid';
            }
            
            return null;
        }
    }

    /**
     * Handle place selection from autocomplete
     */
    handlePlaceSelected(place, inputId) {
        try {
            const address = place.formatted_address;
            const lat = place.geometry.location.lat();
            const lng = place.geometry.location.lng();

            // Store coordinates in hidden fields if they exist
            const latField = document.getElementById(inputId + '_lat');
            const lngField = document.getElementById(inputId + '_lng');
            
            if (latField) latField.value = lat;
            if (lngField) lngField.value = lng;

            // Trigger custom event for other components to listen
            const event = new CustomEvent('addressSelected', {
                detail: { address, lat, lng, place }
            });
            document.dispatchEvent(event);

            console.log('Address selected:', { address, lat, lng });
        } catch (error) {
            console.error('Error handling place selection:', error);
        }
    }

    /**
     * Initialize map with given options
     */
    initMap(containerId, options = {}) {
        try {
            const container = document.getElementById(containerId);
            if (!container) {
                console.error(`Container with ID '${containerId}' not found`);
                return null;
            }

            const defaultOptions = {
                zoom: 10,
                center: { lat: 3.1390, lng: 101.6869 }, // Kuala Lumpur, Malaysia
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                ...options
            };

            this.map = new google.maps.Map(container, defaultOptions);
            
            // Initialize directions service
            this.directionsService = new google.maps.DirectionsService();
            this.directionsRenderer = new google.maps.DirectionsRenderer();
            this.directionsRenderer.setMap(this.map);

            return this.map;
        } catch (error) {
            console.error('Error initializing map:', error);
            return null;
        }
    }

    /**
     * Add marker to map
     */
    addMarker(lat, lng, options = {}) {
        try {
            const position = new google.maps.LatLng(lat, lng);
            
            const defaultOptions = {
                position: position,
                map: this.map,
                title: 'Location',
                ...options
            };

            const marker = new google.maps.Marker(defaultOptions);
            this.markers.push(marker);

            // Add info window if content is provided
            if (options.infoContent) {
                const infoWindow = new google.maps.InfoWindow({
                    content: options.infoContent
                });

                marker.addListener('click', () => {
                    // Close all other info windows
                    this.infoWindows.forEach(window => window.close());
                    infoWindow.open(this.map, marker);
                });

                this.infoWindows.push(infoWindow);
            }

            return marker;
        } catch (error) {
            console.error('Error adding marker:', error);
            return null;
        }
    }

    /**
     * Clear all markers from map
     */
    clearMarkers() {
        // Close all info windows
        this.infoWindows.forEach(infoWindow => infoWindow.close());
        this.infoWindows = [];

        // Remove all markers
        this.markers.forEach(marker => marker.setMap(null));
        this.markers = [];
    }

    /**
     * Calculate and display route between two points
     */
    calculateRoute(origin, destination, options = {}) {
        try {
            const request = {
                origin: origin,
                destination: destination,
                travelMode: google.maps.TravelMode.DRIVING,
                ...options
            };

            this.directionsService.route(request, (result, status) => {
                if (status === 'OK') {
                    this.directionsRenderer.setDirections(result);
                    
                    // Trigger custom event with route information
                    const event = new CustomEvent('routeCalculated', {
                        detail: { result, status }
                    });
                    document.dispatchEvent(event);
                } else {
                    console.error('Directions request failed:', status);
                }
            });
        } catch (error) {
            console.error('Error calculating route:', error);
        }
    }

    /**
     * Fit map to show all markers
     */
    fitMapToMarkers() {
        if (this.map && this.markers.length > 0) {
            const bounds = new google.maps.LatLngBounds();
            this.markers.forEach(marker => {
                bounds.extend(marker.getPosition());
            });
            this.map.fitBounds(bounds);

            // If only one marker, zoom out a bit
            if (this.markers.length === 1) {
                const listener = google.maps.event.addListener(this.map, 'idle', () => {
                    if (this.map.getZoom() > 15) this.map.setZoom(15);
                    google.maps.event.removeListener(listener);
                });
            }
        }
    }

    /**
     * Geocode address to coordinates
     */
    geocodeAddress(address) {
        return new Promise((resolve, reject) => {
            const geocoder = new google.maps.Geocoder();
            geocoder.geocode({ address: address }, (results, status) => {
                if (status === 'OK' && results[0]) {
                    const location = results[0].geometry.location;
                    resolve({
                        lat: location.lat(),
                        lng: location.lng(),
                        formatted_address: results[0].formatted_address
                    });
                } else {
                    reject('Geocoding failed: ' + status);
                }
            });
        });
    }

    /**
     * Reverse geocode coordinates to address
     */
    reverseGeocode(lat, lng) {
        return new Promise((resolve, reject) => {
            const geocoder = new google.maps.Geocoder();
            const latlng = new google.maps.LatLng(lat, lng);
            
            geocoder.geocode({ location: latlng }, (results, status) => {
                if (status === 'OK' && results[0]) {
                    resolve(results[0].formatted_address);
                } else {
                    reject('Reverse geocoding failed: ' + status);
                }
            });
        });
    }

    /**
     * Get current user location
     */
    getCurrentLocation() {
        return new Promise((resolve, reject) => {
            if (!navigator.geolocation) {
                reject('Geolocation is not supported by this browser');
                return;
            }

            navigator.geolocation.getCurrentPosition(
                (position) => {
                    resolve({
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    });
                },
                (error) => {
                    reject('Error getting location: ' + error.message);
                }
            );
        });
    }

    /**
     * Center map on specific coordinates
     */
    centerMap(lat, lng, zoom = 15) {
        if (this.map) {
            this.map.setCenter({ lat, lng });
            this.map.setZoom(zoom);
        }
    }
}

// Global instance
window.mapsService = new GoogleMapsService();

// Utility functions for easy access
window.initAddressAutocomplete = function(inputId, options = {}) {
    return window.mapsService.initAddressAutocomplete(inputId, options);
};

window.initMap = function(containerId, options = {}) {
    return window.mapsService.initMap(containerId, options);
};

window.addMapMarker = function(lat, lng, options = {}) {
    return window.mapsService.addMarker(lat, lng, options);
};

window.calculateMapRoute = function(origin, destination, options = {}) {
    return window.mapsService.calculateRoute(origin, destination, options);
};

window.geocodeAddress = function(address) {
    return window.mapsService.geocodeAddress(address);
};

window.getCurrentLocation = function() {
    return window.mapsService.getCurrentLocation();
};

// Initialize maps service when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Only initialize if Google Maps API key is available
    if (
      typeof GOOGLE_MAPS_API_KEY !== "undefined" &&
      GOOGLE_MAPS_API_KEY !== "YOUR_GOOGLE_MAPS_API_KEY_HERE"
    ) {
      // Add a small delay to ensure DOM is fully ready
      setTimeout(() => {
        window.mapsService
          .init()
          .then(() => {
            console.log("Google Maps service initialized successfully");
            // Trigger custom event to notify other components
            document.dispatchEvent(new CustomEvent('googleMapsReady'));
          })
          .catch((error) => {
            console.error("Failed to initialize Google Maps service:", error);
            // Trigger error event
            document.dispatchEvent(new CustomEvent('googleMapsError', { detail: error }));
          });
      }, 100);
    } else {
      console.warn("Google Maps API key not configured. Please set your API key in config.php");
    }
}); 