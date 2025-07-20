<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Door2Dry - Smart Laundry Pickup & Delivery</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <header class="auth-header">
        <div class="container">
            <a href="index.php" class="auth-logo">
                <i class="fas fa-tshirt"></i>
                <span>Door2Dry</span>
            </a>
            <nav class="auth-nav">
                <a href="#features" class="nav-link">
                    <i class="fas fa-star"></i>
                    Features
                </a>
                <a href="#how-it-works" class="nav-link">
                    <i class="fas fa-cogs"></i>
                    How It Works
                </a>
                <a href="customer/login.php" class="nav-link">
                    <i class="fas fa-user"></i>
                    Customer Portal
                </a>
                <a href="driver/login.php" class="nav-link">
                    <i class="fas fa-truck"></i>
                    Driver Portal
                </a>
            </nav>
        </div>
    </header>

    <div class="main-content">
        <!-- Hero Section -->
        <section class="modern-hero">
            <div class="hero-background">
                <div class="hero-shapes">
                    <div class="shape shape-1"></div>
                    <div class="shape shape-2"></div>
                    <div class="shape shape-3"></div>
                </div>
            </div>
            <div class="container">
                <div class="hero-content">
                    <div class="hero-text">
                        <h1 class="hero-title">
                            Smart Laundry
                            <span class="gradient-text">Pickup & Delivery</span>
                        </h1>
                        <p class="hero-subtitle">
                            Professional laundry service at your doorstep with real-time tracking, and seamless experience powered by modern technology.
                        </p>
                        <div class="hero-buttons">
                            <a href="customer/register.php" class="btn-modern primary">
                                <i class="fas fa-rocket"></i>
                                Get Started Free
                            </a>
                            <a href="#how-it-works" class="btn-modern secondary">
                                <i class="fas fa-play"></i>
                                See How It Works
                            </a>
                        </div>
                        <div class="hero-stats">
                            <div class="stat">
                                <div class="stat-number" style="color: white;">24/7</div>
                                <div class="stat-label" style="color: white;">Available</div>
                            </div>
                            <div class="stat">
                                <div class="stat-number" style="color: white;">99%</div>
                                <div class="stat-label" style="color: white;">Satisfaction</div>
                            </div>
                            <div class="stat">
                                <div class="stat-number" style="color: white;">48h</div>
                                <div class="stat-label" style="color: white;">Turnaround</div>
                            </div>
                        </div>
                    </div>
                    <div class="hero-visual">
                        <div class="hero-card">
                            <div class="card-header">
                                <div class="card-title">
                                    <i class="fas fa-tshirt"></i>
                                    New Order
                                </div>
                                <div class="card-status">
                                    <i class="fas fa-circle"></i>
                                    Live
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="order-item">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span>Pickup: 123 Main St</span>
                                </div>
                                <div class="order-item">
                                    <i class="fas fa-clock"></i>
                                    <span>Today, 2:00 PM</span>
                                </div>
                                <div class="order-item">
                                    <i class="fas fa-box"></i>
                                    <span>Order Ready</span>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="progress-bar">
                                    <div class="progress-fill"></div>
                                </div>
                                <span class="progress-text">Processing...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section id="features" class="modern-features">
            <div class="container">
                <div class="section-header">
                    <h2 class="section-title">Why Choose Door2Dry?</h2>
                    <p class="section-subtitle">Experience the future of laundry service with our cutting-edge features</p>
                </div>
                <div class="features-grid">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                        <div class="feature-content">
                            <h3>Easy Ordering</h3>
                            <p>Place orders online with just a few clicks. Specify pickup address and special instructions with our intuitive interface.</p>
                            <div class="feature-highlight">
                                <i class="fas fa-check"></i>
                                <span>One-click ordering</span>
                            </div>
                        </div>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <div class="feature-content">
                            <h3>Secure Verification</h3>
                            <p>Secure pickup and delivery confirmation using unique order IDs. No more mix-ups or lost items.</p>
                            <div class="feature-highlight">
                                <i class="fas fa-shield-alt"></i>
                                <span>100% Secure</span>
                            </div>
                        </div>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="feature-content">
                            <h3>GPS Tracking</h3>
                            <p>Track your order status from pickup to delivery with live GPS updates and real-time notifications.</p>
                            <div class="feature-highlight">
                                <i class="fas fa-satellite"></i>
                                <span>Live tracking</span>
                            </div>
                        </div>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="feature-content">
                            <h3>Fast Turnaround</h3>
                            <p>Get your clothes back in 48 hours or less with our efficient processing and delivery system.</p>
                            <div class="feature-highlight">
                                <i class="fas fa-bolt"></i>
                                <span>48h delivery</span>
                            </div>
                        </div>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-star"></i>
                        </div>
                        <div class="feature-content">
                            <h3>Premium Quality</h3>
                            <p>Professional cleaning with eco-friendly detergents and state-of-the-art equipment for best results.</p>
                            <div class="feature-highlight">
                                <i class="fas fa-leaf"></i>
                                <span>Eco-friendly</span>
                            </div>
                        </div>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-headset"></i>
                        </div>
                        <div class="feature-content">
                            <h3>24/7 Support</h3>
                            <p>Round-the-clock customer support to help you with any questions or concerns about your orders.</p>
                            <div class="feature-highlight">
                                <i class="fas fa-phone"></i>
                                <span>Always available</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- How It Works Section -->
        <section id="how-it-works" class="modern-process">
            <div class="container">
                <div class="section-header">
                    <h2 class="section-title">How It Works</h2>
                    <p class="section-subtitle">Simple, fast, and efficient laundry service in 4 easy steps</p>
                </div>
                <div class="process-timeline">
                    <div class="process-step">
                        <div class="step-number">1</div>
                        <div class="step-content">
                            <div class="step-icon">
                                <i class="fas fa-plus-circle"></i>
                            </div>
                            <h3>Place Order</h3>
                            <p>Create your laundry order with pickup details, special instructions, and preferred time slot.</p>
                        </div>
                    </div>
                    <div class="process-step">
                        <div class="step-number">2</div>
                        <div class="step-content">
                            <div class="step-icon">
                                <i class="fas fa-truck"></i>
                            </div>
                            <h3>Pickup Scheduled</h3>
                            <p>Our driver receives your order and schedules pickup within 24 hours at your convenience.</p>
                        </div>
                    </div>
                    <div class="process-step">
                        <div class="step-number">3</div>
                        <div class="step-content">
                            <div class="step-icon">
                                <i class="fas fa-tshirt"></i>
                            </div>
                            <h3>Professional Cleaning</h3>
                            <p>Your items are professionally cleaned using eco-friendly detergents and modern equipment.</p>
                        </div>
                    </div>
                    <div class="process-step">
                        <div class="step-number">4</div>
                        <div class="step-content">
                            <div class="step-icon">
                                <i class="fas fa-home"></i>
                            </div>
                            <h3>Delivery</h3>
                            <p>Clean clothes delivered to your door with secure order confirmation and quality guarantee.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Portals Section -->
        <section class="modern-portals">
            <div class="container">
                <div class="section-header">
                    <h2 class="section-title">Access Your Portal</h2>
                    <p class="section-subtitle">Choose your portal and start your laundry journey</p>
                </div>
                <div class="portals-grid">
                    <div class="portal-card customer">
                        <div class="portal-icon">
                            <i class="fas fa-user-circle"></i>
                        </div>
                        <div class="portal-content">
                            <h3>Customer Portal</h3>
                            <p>Place orders, track status, and rate your experience with our seamless platform.</p>
                            <ul class="portal-features">
                                <li><i class="fas fa-check"></i> Order Management</li>
                                <li><i class="fas fa-check"></i> Real-time Tracking</li>
                                <li><i class="fas fa-check"></i> Secure Verification</li>
                                <li><i class="fas fa-check"></i> Rating System</li>
                            </ul>
                        </div>
                        <div class="portal-actions">
                            <a href="customer/login.php" class="btn-modern primary">Login</a>
                            <a href="customer/register.php" class="btn-modern secondary">Register</a>
                        </div>
                    </div>
                    <div class="portal-card driver">
                        <div class="portal-icon">
                            <i class="fas fa-truck"></i>
                        </div>
                        <div class="portal-content">
                            <h3>Driver Portal</h3>
                            <p>Manage orders, verify deliveries, update status, and track deliveries with our driver-focused tools.</p>
                            <ul class="portal-features">
                                <li><i class="fas fa-check"></i> Order Management</li>
                                <li><i class="fas fa-check"></i> Real-time Tracking</li>
                                <li><i class="fas fa-check"></i> GPS Navigation</li>
                                <li><i class="fas fa-check"></i> Status Updates</li>
                            </ul>
                        </div>
                        <div class="portal-actions">
                            <a href="driver/login.php" class="btn-modern primary">Driver Login</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Technology Section -->
        <section class="technology-section">
            <div class="container">
                <h2 class="text-center mb-5">Built with Modern Technology</h2>
                <div class="row">
                    <div class="col-md-4 mb-4">
                        <div class="text-center">
                            <h5>🔒 Secure & Safe</h5>
                            <p>Advanced security with password hashing, session management, and input validation.</p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="text-center">
                            <h5>📱 Mobile Friendly</h5>
                            <p>Responsive design that works perfectly on all devices - desktop, tablet, and mobile.</p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="text-center">
                            <h5>⚡ Fast & Reliable</h5>
                            <p>Optimized performance with efficient database queries and modern web technologies.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="modern-cta">
            <div class="cta-background">
                <div class="cta-shapes">
                    <div class="cta-shape cta-shape-1"></div>
                    <div class="cta-shape cta-shape-2"></div>
                </div>
            </div>
            <div class="container">
                <div class="cta-content">
                    <h2 class="cta-title">Ready to Transform Your Laundry Experience?</h2>
                    <p class="cta-subtitle">Join thousands of satisfied customers who have already made the switch to Door2Dry</p>
                    <a href="customer/register.php" class="btn-modern cta-button">
                        <i class="fas fa-rocket"></i>
                        Start Your Journey Today
                    </a>
                    <div class="cta-features">
                        <div class="cta-feature">
                            <i class="fas fa-gift"></i>
                            <span>Free Registration</span>
                        </div>
                        <div class="cta-feature">
                            <i class="fas fa-clock"></i>
                            <span>24/7 Support</span>
                        </div>
                        <div class="cta-feature">
                            <i class="fas fa-shield-alt"></i>
                            <span>100% Secure</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <footer class="modern-footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-brand">
                    <a href="index.php" class="footer-logo">
                        <i class="fas fa-tshirt"></i>
                        <span>Door2Dry</span>
                    </a>
                    <p>Smart Laundry Pickup & Delivery System</p>
                </div>
                <div class="footer-links">
                    <div class="footer-section">
                        <h4>Quick Links</h4>
                        <a href="#features">Features</a>
                        <a href="#how-it-works">How It Works</a>
                        <a href="customer/register.php">Get Started</a>
                    </div>
                    <div class="footer-section">
                        <h4>Portals</h4>
                        <a href="customer/login.php">Customer Portal</a>
                        <a href="driver/login.php">Driver Portal</a>
                    </div>
                    <div class="footer-section">
                        <h4>Technology</h4>
                        <p>Built with PHP, MySQL, and Modern Web Technologies</p>
                        
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 Door2Dry. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Mobile menu toggle
        document.querySelector('.mobile-menu-toggle').addEventListener('click', function() {
            document.querySelector('.modern-nav').classList.toggle('active');
        });

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
    </script>
</body>
</html> 
</html>