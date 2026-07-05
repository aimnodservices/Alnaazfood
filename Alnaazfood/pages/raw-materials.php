<?php
// ============================================
// AL-NAAZ FOOD - Raw Materials Page
// ============================================

require_once __DIR__ . '/../config/config.php';

$page_css = 'products.css';
$page_js = 'products.js';

// Get raw materials
$raw_materials = getRows("SELECT * FROM products WHERE category = 'raw_material' ORDER BY id DESC");

// Track visitor
$ip = getClientIP();
$session_id = session_id();
$sql = "INSERT INTO visitor_analytics (ip_address, session_id, page_visited) VALUES (?, ?, ?)";
insertData($sql, [$ip, $session_id, 'raw_materials'], 'sss');

include_once __DIR__ . '/../includes/header.php';
?>

<!-- Page Banner -->
<section class="page-banner">
    <div class="container">
        <h1>🌾 Raw Materials</h1>
        <p>Premium quality raw ingredients for authentic cooking</p>
    </div>
</section>

<!-- Why Choose Us -->
<section class="section section-light">
    <div class="container">
        <div class="section-title">
            <h2>✨ Why Choose AL-NAAZ</h2>
            <p>We source the finest raw materials from trusted suppliers</p>
        </div>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">🌿</div>
                <h3>100% Natural</h3>
                <p>All our raw materials are natural, pure, and free from artificial additives.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🔬</div>
                <h3>Quality Tested</h3>
                <p>Every batch is tested for quality, purity, and freshness in our lab.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🚚</div>
                <h3>Fresh Delivery</h3>
                <p>We ensure fresh delivery with proper packaging and storage.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">💰</div>
                <h3>Best Prices</h3>
                <p>Direct sourcing from farmers ensures the best prices for you.</p>
            </div>
        </div>
    </div>
</section>

<!-- Raw Materials Details -->
<section class="section section-dark">
    <div class="container">
        <div class="section-title">
            <h2>📦 Raw Materials Collection</h2>
            <p>Explore our premium range of raw ingredients</p>
        </div>
        <div class="product-grid">
            <?php if (!empty($raw_materials)): ?>
                <?php foreach ($raw_materials as $product): ?>
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
                            <span class="category-badge cat-raw">Raw Material</span>
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
                <p style="color: var(--text-grey); text-align: center; grid-column: 1/-1;">No raw materials available.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Raw Material Enquiry -->
<section class="section section-light">
    <div class="container">
        <div class="enquiry-box">
            <div class="enquiry-content">
                <h2>📞 Raw Material Enquiry</h2>
                <p>
                    Need bulk raw materials for your business? Contact us for 
                    wholesale pricing and bulk delivery options.
                </p>
                <div class="enquiry-buttons">
                    <a href="https://wa.me/<?php echo $settings['whatsapp_number'] ?? '919876543210'; ?>?text=Hello%20AL-NAAZ%20FOOD!%20I'm%20interested%20in%20bulk%20raw%20materials." 
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