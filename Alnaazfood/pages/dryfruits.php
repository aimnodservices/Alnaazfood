<?php
// ============================================
// AL-NAAZ FOOD - Dry Fruits Page
// ============================================

require_once __DIR__ . '/../config/config.php';

$page_css = 'products.css';
$page_js = 'products.js';

// Get dry fruits
$top_dryfruits = getRows("SELECT * FROM products WHERE category = 'dryfruit' AND is_top_ranked = TRUE ORDER BY id DESC");
$all_dryfruits = getRows("SELECT * FROM products WHERE category = 'dryfruit' ORDER BY id DESC");

// Track visitor
$ip = getClientIP();
$session_id = session_id();
$sql = "INSERT INTO visitor_analytics (ip_address, session_id, page_visited) VALUES (?, ?, ?)";
insertData($sql, [$ip, $session_id, 'dryfruits'], 'sss');

include_once __DIR__ . '/../includes/header.php';
?>

<!-- Page Banner -->
<section class="page-banner">
    <div class="container">
        <h1>🥜 Premium Dry Fruits</h1>
        <p>Premium quality nuts and dry fruits for health and taste</p>
    </div>
</section>

<!-- Top Ranked Dry Fruits -->
<section class="section section-light">
    <div class="container">
        <div class="section-title">
            <h2>🏆 Premium Selection</h2>
            <p>Our finest quality dry fruits and nuts</p>
        </div>
        <div class="product-grid">
            <?php if (!empty($top_dryfruits)): ?>
                <?php foreach ($top_dryfruits as $product): ?>
                    <div class="product-card">
                        <div class="product-image">
                            <img src="<?php echo UPLOAD_URL . ($product['image'] ?: 'placeholder.jpg'); ?>" 
                                 alt="<?php echo $product['name']; ?>"
                                 onerror="this.src='https://via.placeholder.com/300x250/1A1A1A/D4AF37?text=AL-NAAZ'">
                            <span class="badge badge-popular">Premium</span>
                        </div>
                        <div class="product-info">
                            <span class="category-badge cat-dryfruit">Dry Fruit</span>
                            <h3><?php echo $product['name']; ?></h3>
                            <p class="desc"><?php echo substr($product['description'], 0, 100); ?>...</p>
                            <div class="price">
                                <span class="current">₹<?php echo number_format($product['discount_price'] ?: $product['price'], 2); ?></span>
                                <?php if ($product['discount_price']): ?>
                                    <span class="original">₹<?php echo number_format($product['price'], 2); ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="actions">
                                <button class="btn-add" onclick="addToCart(<?php echo $product['id']; ?>, '<?php echo addslashes($product['name']); ?>', <?php echo $product['discount_price'] ?: $product['price']; ?>, '<?php echo $product['image']; ?>')">
                                    <i class="fas fa-cart-plus"></i> Add to Cart
                                </button>
                                <button class="btn-whatsapp" onclick="whatsappEnquiry('<?php echo addslashes($product['name']); ?>', <?php echo $product['id']; ?>)">
                                    <i class="fab fa-whatsapp"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="color: var(--text-grey); text-align: center; grid-column: 1/-1;">No premium dry fruits available.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- All Dry Fruits -->
<section class="section section-dark">
    <div class="container">
        <div class="section-title">
            <h2>📦 Complete Dry Fruits Collection</h2>
            <p>Explore our entire range of premium dry fruits and nuts</p>
        </div>
        <div class="product-grid">
            <?php if (!empty($all_dryfruits)): ?>
                <?php foreach ($all_dryfruits as $product): ?>
                    <div class="product-card">
                        <div class="product-image">
                            <img src="<?php echo UPLOAD_URL . ($product['image'] ?: 'placeholder.jpg'); ?>" 
                                 alt="<?php echo $product['name']; ?>"
                                 onerror="this.src='https://via.placeholder.com/300x250/1A1A1A/D4AF37?text=AL-NAAZ'">
                            <?php if ($product['is_best_selling']): ?>
                                <span class="badge badge-best">Best Seller</span>
                            <?php endif; ?>
                        </div>
                        <div class="product-info">
                            <span class="category-badge cat-dryfruit">Dry Fruit</span>
                            <h3><?php echo $product['name']; ?></h3>
                            <p class="desc"><?php echo substr($product['description'], 0, 100); ?>...</p>
                            <div class="price">
                                <span class="current">₹<?php echo number_format($product['discount_price'] ?: $product['price'], 2); ?></span>
                                <?php if ($product['discount_price']): ?>
                                    <span class="original">₹<?php echo number_format($product['price'], 2); ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="actions">
                                <button class="btn-add" onclick="addToCart(<?php echo $product['id']; ?>, '<?php echo addslashes($product['name']); ?>', <?php echo $product['discount_price'] ?: $product['price']; ?>, '<?php echo $product['image']; ?>')">
                                    <i class="fas fa-cart-plus"></i> Add to Cart
                                </button>
                                <button class="btn-whatsapp" onclick="whatsappEnquiry('<?php echo addslashes($product['name']); ?>', <?php echo $product['id']; ?>)">
                                    <i class="fab fa-whatsapp"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="color: var(--text-grey); text-align: center; grid-column: 1/-1;">No dry fruits available.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Wholesale Enquiry -->
<section class="section section-light">
    <div class="container">
        <div class="enquiry-box">
            <div class="enquiry-content">
                <h2>📞 Dry Fruits Enquiry</h2>
                <p>
                    Looking for bulk dry fruits for your business? Contact us for 
                    wholesale pricing and bulk delivery.
                </p>
                <div class="enquiry-buttons">
                    <a href="https://wa.me/<?php echo $settings['whatsapp_number'] ?? '919876543210'; ?>?text=Hello%20AL-NAAZ%20FOOD!%20I'm%20interested%20in%20bulk%20dry%20fruits." 
                       target="_blank" class="btn-primary" style="background: #25D366;">
                        <i class="fab fa-whatsapp"></i> Enquire on WhatsApp
                    </a>
                    <a href="<?php echo SITE_URL; ?>pages/contact.php" class="btn-secondary">
                        <i class="fas fa-envelope"></i> Contact Form
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>