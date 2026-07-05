<?php
// ============================================
// AL-NAAZ FOOD - Owner Details Page
// ============================================

require_once __DIR__ . '/../config/config.php';

$page_css = 'owner.css';
$page_js = 'owner.js';

// Get owner details
$owner = getRow("SELECT * FROM owner_details LIMIT 1");

// Get settings
$settings = [];
$settings_result = executeQuery("SELECT setting_key, setting_value FROM website_settings");
if ($settings_result) {
    while ($row = $settings_result->fetch_assoc()) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
}

// Track visitor
$ip = getClientIP();
$session_id = session_id();
$sql = "INSERT INTO visitor_analytics (ip_address, session_id, page_visited) VALUES (?, ?, ?)";
insertData($sql, [$ip, $session_id, 'owner_details'], 'sss');

include_once __DIR__ . '/../includes/header.php';
?>

<!-- Page Banner -->
<section class="page-banner" style="background: linear-gradient(135deg, #1A0A0A, var(--royal-gold));">
    <div class="container">
        <h1>👑 Meet the Owner</h1>
        <p>The vision behind AL-NAAZ FOOD</p>
    </div>
</section>

<!-- Owner Profile -->
<section class="section section-light">
    <div class="container">
        <?php if ($owner): ?>
            <div class="owner-profile">
                <div class="owner-image">
                    <?php if (!empty($owner['image'])): ?>
                        <img src="<?php echo UPLOAD_URL . $owner['image']; ?>" alt="<?php echo $owner['name']; ?>">
                    <?php else: ?>
                        <div class="owner-avatar">
                            <i class="fas fa-user-tie" style="font-size: 80px; color: var(--royal-gold);"></i>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="owner-details">
                    <h2><?php echo $owner['name']; ?></h2>
                    <div class="designation"><?php echo $owner['designation']; ?></div>
                    <div class="owner-bio">
                        <p><?php echo $owner['bio']; ?></p>
                    </div>
                    <div class="owner-contact-info">
                        <?php if (!empty($owner['email'])): ?>
                            <div class="contact-item">
                                <i class="fas fa-envelope"></i>
                                <a href="mailto:<?php echo $owner['email']; ?>"><?php echo $owner['email']; ?></a>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($owner['phone'])): ?>
                            <div class="contact-item">
                                <i class="fas fa-phone"></i>
                                <a href="tel:<?php echo $owner['phone']; ?>"><?php echo $owner['phone']; ?></a>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($owner['address'])): ?>
                            <div class="contact-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <span><?php echo $owner['address']; ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="owner-social">
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
            </div>
        <?php else: ?>
            <div style="text-align: center; padding: 60px 20px;">
                <div style="font-size: 60px; margin-bottom: 20px;">👤</div>
                <h3 style="color: var(--text-grey);">Owner details not available</h3>
                <p style="color: var(--text-grey); margin-top: 10px;">Please contact the admin for owner information.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Contact Section -->
<section class="section section-dark">
    <div class="container">
        <div class="section-title">
            <h2>📞 Get in Touch</h2>
            <p>Reach out to us for any inquiries</p>
        </div>
        <div class="contact-grid">
            <div class="contact-card">
                <div class="icon"><i class="fas fa-map-marker-alt"></i></div>
                <h3>Address</h3>
                <p><?php echo $settings['contact_address'] ?? '123, Food Street, Mumbai, Maharashtra, India - 400001'; ?></p>
            </div>
            <div class="contact-card">
                <div class="icon"><i class="fas fa-phone"></i></div>
                <h3>Phone</h3>
                <p><a href="tel:<?php echo $settings['contact_phone'] ?? '+919876543210'; ?>"><?php echo $settings['contact_phone'] ?? '+91 98765 43210'; ?></a></p>
            </div>
            <div class="contact-card">
                <div class="icon"><i class="fas fa-envelope"></i></div>
                <h3>Email</h3>
                <p><a href="mailto:<?php echo $settings['contact_email'] ?? 'info@alnaazfood.com'; ?>"><?php echo $settings['contact_email'] ?? 'info@alnaazfood.com'; ?></a></p>
            </div>
            <div class="contact-card">
                <div class="icon"><i class="fab fa-whatsapp"></i></div>
                <h3>WhatsApp</h3>
                <p><a href="https://wa.me/<?php echo $settings['whatsapp_number'] ?? '919876543210'; ?>" target="_blank">Chat on WhatsApp</a></p>
            </div>
        </div>
    </div>
</section>

<!-- Map -->
<?php if (!empty($settings['map_embed'])): ?>
    <section class="section section-light">
        <div class="container">
            <div class="section-title">
                <h2>📍 Find Us</h2>
                <p>Visit our store location</p>
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