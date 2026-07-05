<?php
// ============================================
// AL-NAAZ FOOD - Catering Booking Page
// ============================================

require_once __DIR__ . '/../config/config.php';

$page_css = 'catering.css';
$page_js = 'catering.js';

// Track visitor
$ip = getClientIP();
$session_id = session_id();
$sql = "INSERT INTO visitor_analytics (ip_address, session_id, page_visited) VALUES (?, ?, ?)";
insertData($sql, [$ip, $session_id, 'catering'], 'sss');

include_once __DIR__ . '/../includes/header.php';
?>

<!-- Page Banner -->
<section class="page-banner">
    <div class="container">
        <h1>🍽️ Catering & Event Booking</h1>
        <p>Make your events memorable with AL-NAAZ FOOD</p>
    </div>
</section>

<!-- Catering Info -->
<section class="section section-light">
    <div class="container">
        <div class="catering-info">
            <div class="catering-grid">
                <div class="catering-card">
                    <div class="icon">🎉</div>
                    <h3>Birthday Parties</h3>
                    <p>Special menus for birthday celebrations with customized spice blends</p>
                </div>
                <div class="catering-card">
                    <div class="icon">💍</div>
                    <h3>Weddings</h3>
                    <p>Premium catering services for weddings with authentic flavors</p>
                </div>
                <div class="catering-card">
                    <div class="icon">🏢</div>
                    <h3>Corporate Events</h3>
                    <p>Professional catering for corporate meetings and events</p>
                </div>
                <div class="catering-card">
                    <div class="icon">🎊</div>
                    <h3>Festival Celebrations</h3>
                    <p>Special festival menus with traditional dishes and flavors</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Booking Form -->
<section class="section section-dark">
    <div class="container">
        <div class="section-title">
            <h2>📋 Make a Booking</h2>
            <p>Fill in the details and we'll get back to you within 24 hours</p>
        </div>
        <div class="booking-form-container">
            <form action="<?php echo SITE_URL; ?>api/catering-book.php" method="POST" class="booking-form">
                <div class="form-row">
                    <div class="form-group">
                        <label>Full Name *</label>
                        <input type="text" name="name" required placeholder="Your full name">
                    </div>
                    <div class="form-group">
                        <label>Email Address *</label>
                        <input type="email" name="email" required placeholder="your@email.com">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Phone Number *</label>
                        <input type="tel" name="phone" required placeholder="+91 98765 43210">
                    </div>
                    <div class="form-group">
                        <label>Event Type *</label>
                        <select name="event_type" required>
                            <option value="">Select Event Type</option>
                            <option value="birthday">Birthday Party</option>
                            <option value="wedding">Wedding</option>
                            <option value="corporate">Corporate Event</option>
                            <option value="festival">Festival Celebration</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Event Date *</label>
                        <input type="date" name="event_date" required>
                    </div>
                    <div class="form-group">
                        <label>Number of Guests *</label>
                        <input type="number" name="guest_count" required placeholder="Approximate count">
                    </div>
                </div>
                <div class="form-group">
                    <label>Special Requirements</label>
                    <textarea name="special_requirements" placeholder="Dietary restrictions, preferred cuisine, etc." rows="4"></textarea>
                </div>
                <button type="submit" class="btn-primary" style="width: 100%; padding: 16px;">
                    <i class="fas fa-calendar-check"></i> Submit Booking Request
                </button>
            </form>
        </div>
    </div>
</section>

<!-- Contact for Catering -->
<section class="section section-light">
    <div class="container">
        <div class="catering-contact">
            <h2>📞 Need Immediate Assistance?</h2>
            <p>Call us directly or WhatsApp for quick booking</p>
            <div class="contact-buttons">
                <a href="tel:<?php echo $settings['contact_phone'] ?? '+919876543210'; ?>" class="btn-primary">
                    <i class="fas fa-phone"></i> Call Now
                </a>
                <a href="https://wa.me/<?php echo $settings['whatsapp_number'] ?? '919876543210'; ?>?text=Hello%20AL-NAAZ%20FOOD!%20I%20need%20catering%20booking%20assistance." 
                   target="_blank" class="btn-primary" style="background: #25D366;">
                    <i class="fab fa-whatsapp"></i> WhatsApp
                </a>
            </div>
        </div>
    </div>
</section>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>