<?php
// ============================================
// AL-NAAZ FOOD - Contact Page
// ============================================

require_once __DIR__ . '/../config/config.php';

$page_css = 'contact.css';
$page_js = 'contact.js';

// Get settings
$settings = [];
$settings_result = executeQuery("SELECT setting_key, setting_value FROM website_settings");
if ($settings_result) {
    while ($row = $settings_result->fetch_assoc()) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
}

// Handle contact form submission
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $subject = sanitize($_POST['subject'] ?? '');
    $msg = sanitize($_POST['message'] ?? '');
    
    if (empty($name) || empty($email) || empty($msg)) {
        $error = 'Please fill all required fields';
    } else {
        $sql = "INSERT INTO contact_enquiries (name, email, phone, message, type) VALUES (?, ?, ?, ?, 'general')";
        $result = insertData($sql, [$name, $email, $phone, $msg], 'ssss');
        
        if ($result) {
            $message = 'Message sent successfully! We\'ll get back to you soon.';
        } else {
            $error = 'Failed to send message. Please try again.';
        }
    }
}

// Track visitor
$ip = getClientIP();
$session_id = session_id();
$sql = "INSERT INTO visitor_analytics (ip_address, session_id, page_visited) VALUES (?, ?, ?)";
insertData($sql, [$ip, $session_id, 'contact'], 'sss');

include_once __DIR__ . '/../includes/header.php';
?>

<!-- Page Banner -->
<section class="page-banner">
    <div class="container">
        <h1>📞 Contact Us</h1>
        <p>We'd love to hear from you</p>
    </div>
</section>

<!-- Contact Section -->
<section class="section section-light">
    <div class="container">
        <div class="contact-wrapper">
            <!-- Contact Info -->
            <div class="contact-info">
                <h2>Get in Touch</h2>
                <p>Have questions? We're here to help!</p>
                
                <div class="info-item">
                    <div class="icon"><i class="fas fa-map-marker-alt"></i></div>
                    <div>
                        <h4>Address</h4>
                        <p><?php echo $settings['contact_address'] ?? '123, Food Street, Mumbai, Maharashtra, India - 400001'; ?></p>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="icon"><i class="fas fa-phone"></i></div>
                    <div>
                        <h4>Phone</h4>
                        <p><a href="tel:<?php echo $settings['contact_phone'] ?? '+919876543210'; ?>"><?php echo $settings['contact_phone'] ?? '+91 98765 43210'; ?></a></p>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="icon"><i class="fas fa-envelope"></i></div>
                    <div>
                        <h4>Email</h4>
                        <p><a href="mailto:<?php echo $settings['contact_email'] ?? 'info@alnaazfood.com'; ?>"><?php echo $settings['contact_email'] ?? 'info@alnaazfood.com'; ?></a></p>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="icon"><i class="fab fa-whatsapp"></i></div>
                    <div>
                        <h4>WhatsApp</h4>
                        <p><a href="https://wa.me/<?php echo $settings['whatsapp_number'] ?? '919876543210'; ?>" target="_blank">Chat with us</a></p>
                    </div>
                </div>
                
                <div class="social-links">
                    <?php if (!empty($settings['facebook_url'])): ?>
                        <a href="<?php echo $settings['facebook_url']; ?>" target="_blank"><i class="fab fa-facebook-f"></i></a>
                    <?php endif; ?>
                    <?php if (!empty($settings['instagram_url'])): ?>
                        <a href="<?php echo $settings['instagram_url']; ?>" target="_blank"><i class="fab fa-instagram"></i></a>
                    <?php endif; ?>
                    <?php if (!empty($settings['youtube_url'])): ?>
                        <a href="<?php echo $settings['youtube_url']; ?>" target="_blank"><i class="fab fa-youtube"></i></a>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Contact Form -->
            <div class="contact-form-wrapper">
                <h2>Send a Message</h2>
                
                <?php if ($message): ?>
                    <div class="success-msg">✅ <?php echo $message; ?></div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                    <div class="error-msg">❌ <?php echo $error; ?></div>
                <?php endif; ?>
                
                <form method="POST" class="contact-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Your Name *</label>
                            <input type="text" name="name" required placeholder="John Doe">
                        </div>
                        <div class="form-group">
                            <label>Email Address *</label>
                            <input type="email" name="email" required placeholder="john@example.com">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Phone Number</label>
                            <input type="tel" name="phone" placeholder="+91 98765 43210">
                        </div>
                        <div class="form-group">
                            <label>Subject</label>
                            <input type="text" name="subject" placeholder="How can we help?">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Message *</label>
                        <textarea name="message" rows="5" required placeholder="Your message here..."></textarea>
                    </div>
                    <button type="submit" class="btn-primary">Send Message</button>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Map Section -->
<?php if (!empty($settings['map_embed'])): ?>
<section class="section section-dark">
    <div class="container">
        <div class="section-title">
            <h2>📍 Find Us</h2>
        </div>
        <div class="map-container">
            <iframe 
                src="<?php echo $settings['map_embed']; ?>" 
                width="100%" 
                height="400" 
                style="border:0; border-radius: 15px;" 
                allowfullscreen="" 
                loading="lazy">
            </iframe>
        </div>
    </div>
</section>
<?php endif; ?>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>