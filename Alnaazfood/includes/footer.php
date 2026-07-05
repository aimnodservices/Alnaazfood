<?php
// ============================================
// AL-NAAZ FOOD - Footer
// ============================================

// Get settings for footer
$settings = [];
$settings_result = executeQuery("SELECT setting_key, setting_value FROM website_settings");
if ($settings_result) {
    while ($row = $settings_result->fetch_assoc()) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
}

// Get owner details
$owner = getRow("SELECT * FROM owner_details LIMIT 1");
?>
    <!-- ============================================
    FOOTER
    ============================================ -->
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-grid">
                <!-- Brand -->
                <div class="footer-brand">
                    <h3 class="footer-logo">✨ AL-NAAZ <span>FOOD</span></h3>
                    <p class="footer-tagline"><?php echo SITE_TAGLINE; ?></p>
                    <p class="footer-desc">
                        Premium spices, authentic flavors, and food essentials 
                        for every kitchen. Trusted by chefs and home cooks across India.
                    </p>
                    <div class="footer-social">
                        <?php if (!empty($settings['facebook_url'])): ?>
                            <a href="<?php echo $settings['facebook_url']; ?>" target="_blank"><i class="fab fa-facebook-f"></i></a>
                        <?php endif; ?>
                        <?php if (!empty($settings['instagram_url'])): ?>
                            <a href="<?php echo $settings['instagram_url']; ?>" target="_blank"><i class="fab fa-instagram"></i></a>
                        <?php endif; ?>
                        <?php if (!empty($settings['youtube_url'])): ?>
                            <a href="<?php echo $settings['youtube_url']; ?>" target="_blank"><i class="fab fa-youtube"></i></a>
                        <?php endif; ?>
                        <?php if (!empty($settings['whatsapp_number'])): ?>
                            <a href="https://wa.me/<?php echo $settings['whatsapp_number']; ?>" target="_blank"><i class="fab fa-whatsapp"></i></a>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Quick Links -->
                <div class="footer-links">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="<?php echo SITE_URL; ?>pages/index.php">Home</a></li>
                        <li><a href="<?php echo SITE_URL; ?>pages/masala.php">Spices</a></li>
                        <li><a href="<?php echo SITE_URL; ?>pages/products.php">Products</a></li>
                        <li><a href="<?php echo SITE_URL; ?>pages/raw-materials.php">Raw Materials</a></li>
                        <li><a href="<?php echo SITE_URL; ?>pages/dryfruits.php">Dry Fruits</a></li>
                        <li><a href="<?php echo SITE_URL; ?>pages/seasonal-offers.php">Seasonal Offers</a></li>
                    </ul>
                </div>
                
                <!-- Contact -->
                <div class="footer-contact">
                    <h4>Contact Us</h4>
                    <ul>
                        <?php if (!empty($settings['contact_phone'])): ?>
                            <li><i class="fas fa-phone"></i> <a href="tel:<?php echo $settings['contact_phone']; ?>"><?php echo $settings['contact_phone']; ?></a></li>
                        <?php endif; ?>
                        <?php if (!empty($settings['contact_email'])): ?>
                            <li><i class="fas fa-envelope"></i> <a href="mailto:<?php echo $settings['contact_email']; ?>"><?php echo $settings['contact_email']; ?></a></li>
                        <?php endif; ?>
                        <?php if (!empty($settings['contact_address'])): ?>
                            <li><i class="fas fa-map-marker-alt"></i> <?php echo $settings['contact_address']; ?></li>
                        <?php endif; ?>
                        <?php if (!empty($settings['whatsapp_number'])): ?>
                            <li><i class="fab fa-whatsapp"></i> <a href="https://wa.me/<?php echo $settings['whatsapp_number']; ?>" target="_blank">WhatsApp</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
                
                <!-- Owner -->
                <div class="footer-owner">
                    <h4>Owner</h4>
                    <?php if ($owner): ?>
                        <div class="owner-mini">
                            <?php if (!empty($owner['image'])): ?>
                                <img src="<?php echo UPLOAD_URL . $owner['image']; ?>" alt="<?php echo $owner['name']; ?>">
                            <?php else: ?>
                                <div class="owner-avatar"><i class="fas fa-user-tie"></i></div>
                            <?php endif; ?>
                            <div>
                                <strong><?php echo $owner['name']; ?></strong>
                                <span><?php echo $owner['designation']; ?></span>
                            </div>
                        </div>
                        <a href="<?php echo SITE_URL; ?>pages/owner-details.php" class="footer-btn">View Owner Details</a>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Copyright -->
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved.</p>
                <p>Made with <i class="fas fa-heart" style="color: #8B1A1A;"></i> in India</p>
            </div>
        </div>
    </footer>

    <!-- ============================================
    SCRIPTS
    ============================================ -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="<?php echo SITE_URL; ?>assets/js/main.js"></script>
    
    <?php if (isset($page_js)): ?>
        <script src="<?php echo SITE_URL; ?>assets/js/<?php echo $page_js; ?>"></script>
    <?php endif; ?>
</body>
</html>