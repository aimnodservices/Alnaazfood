<?php
// ============================================
// AL-NAAZ FOOD - Home Page
// ============================================

require_once __DIR__ . '/../config/config.php';

$page_css = 'home.css';
$page_js = 'home.js';

// Get hero sections
$hero_offers = getRows("SELECT * FROM hero_section WHERE type = 'offer' AND is_active = TRUE ORDER BY display_order");
$hero_achievements = getRows("SELECT * FROM hero_section WHERE type = 'achievement' AND is_active = TRUE ORDER BY display_order");

// Get products for sections
$masala_products = getRows("SELECT * FROM products WHERE category = 'masala' AND is_top_ranked = TRUE LIMIT 4");
$best_selling = getRows("SELECT * FROM products WHERE is_best_selling = TRUE LIMIT 4");
$raw_materials = getRows("SELECT * FROM products WHERE category = 'raw_material' LIMIT 4");
$dryfruits = getRows("SELECT * FROM products WHERE category = 'dryfruit' LIMIT 4");
$seasonal_offers = getRows("SELECT * FROM seasonal_offers WHERE is_active = TRUE LIMIT 3");

// Get total products count for stats
$total_products = getCount('products');
$total_customers = getCount('users', "role = 'customer'");

// Track visitor
$ip = getClientIP();
$session_id = session_id();
$sql = "INSERT INTO visitor_analytics (ip_address, session_id, page_visited) VALUES (?, ?, ?)";
insertData($sql, [$ip, $session_id, 'home'], 'sss');

include_once __DIR__ . '/../includes/header.php';
?>

<!-- ============================================
HERO SECTION
============================================ -->
<section class="hero">
    <div class="hero-container">
        <!-- Left Side - Offers -->
        <div class="hero-left">
            <div class="badge">🌟 Premium Quality Since 2010</div>
            <h1>
                Authentic Flavors<br>
                <span>From Our Kitchen</span><br>
                To Yours
            </h1>
            <p>
                Discover the finest spices, premium dry fruits, and authentic 
                food essentials. Trusted by chefs and home cooks across India.
            </p>
            <div class="hero-buttons">
                <a href="#products" class="btn-primary">Explore Products</a>
                <a href="<?php echo SITE_URL; ?>pages/masala.php" class="btn-secondary">Shop Spices</a>
            </div>
        </div>
        
        <!-- Right Side - Achievements -->
        <div class="hero-right">
            <?php foreach ($hero_offers as $offer): ?>
                <div class="hero-card offer-card">
                    <div class="icon">🎉</div>
                    <div class="number"><?php echo $offer['title']; ?></div>
                    <div class="label"><?php echo $offer['description']; ?></div>
                    <?php if (!empty($offer['button_text']) && !empty($offer['button_link'])): ?>
                        <a href="<?php echo $offer['button_link']; ?>" class="btn-primary" style="margin-top: 10px; padding: 8px 20px; font-size: 12px;">
                            <?php echo $offer['button_text']; ?>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
            
            <?php if (!empty($hero_achievements)): ?>
                <?php foreach ($hero_achievements as $index => $achievement): ?>
                    <div class="hero-card">
                        <div class="icon">🏆</div>
                        <div class="number counter" data-target="<?php echo $index == 0 ? 15 : ($index == 1 ? 10000 : 100); ?>">
                            <?php echo $index == 0 ? '15+' : ($index == 1 ? '10,000+' : '100+'); ?>
                        </div>
                        <div class="label"><?php echo $achievement['description']; ?></div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="hero-card">
                    <div class="icon">🏆</div>
                    <div class="number">15+</div>
                    <div class="label">Years of Excellence</div>
                </div>
                <div class="hero-card">
                    <div class="icon">👥</div>
                    <div class="number">10,000+</div>
                    <div class="label">Happy Customers</div>
                </div>
                <div class="hero-card">
                    <div class="icon">⭐</div>
                    <div class="number">100+</div>
                    <div class="label">Authentic Products</div>
                </div>
                <div class="hero-card">
                    <div class="icon">🚀</div>
                    <div class="number">50+</div>
                    <div class="label">Cities Served</div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- ============================================
CHEF'S SPECIAL SECTION
============================================ -->
<section class="section section-dark">
    <div class="container">
        <div class="section-title">
            <h2>👨‍🍳 Chef's Special</h2>
            <p>Signature blends and premium products curated by our master chefs</p>
        </div>
        <div class="product-grid">
            <?php
            $chef_specials = getRows("SELECT * FROM products WHERE is_featured = TRUE LIMIT 4");
            if (empty($chef_specials)) {
                $chef_specials = getRows("SELECT * FROM products WHERE is_top_ranked = TRUE OR is_best_selling = TRUE LIMIT 4");
            }
            ?>
            <?php foreach ($chef_specials as $product): ?>
                <div class="product-card">
                    <div class="product-image">
                        <img src="<?php echo UPLOAD_URL . ($product['image'] ?: 'placeholder.jpg'); ?>" 
                             alt="<?php echo $product['name']; ?>"
                             onerror="this.src='https://via.placeholder.com/300x250/1A1A1A/D4AF37?text=AL-NAAZ'">
                        <span class="badge badge-popular">Chef's Choice</span>
                    </div>
                    <div class="product-info">
                        <span class="category-badge cat-<?php echo $product['category']; ?>">
                            <?php echo ucfirst(str_replace('_', ' ', $product['category'])); ?>
                        </span>
                        <h3><?php echo $product['name']; ?></h3>
                        <p class="desc"><?php echo substr($product['description'], 0, 80); ?>...</p>
                        <div class="price">
                            <span class="current">₹<?php echo number_format($product['discount_price'] ?: $product['price'], 2); ?></span>
                            <?php if ($product['discount_price']): ?>
                                <span class="original">₹<?php echo number_format($product['price'], 2); ?></span>
                                <span class="discount">
                                    <?php echo round((1 - $product['discount_price'] / $product['price']) * 100); ?>% OFF
                                </span>
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
        </div>
    </div>
</section>

<!-- ============================================
MASALA (SPICES) SECTION
============================================ -->
<section class="section section-light">
    <div class="container">
        <div class="section-title">
            <h2>🌶️ Premium Spices</h2>
            <p>Authentic masalas and spice blends for every dish</p>
        </div>
        <div class="product-grid">
            <?php foreach ($masala_products as $product): ?>
                <div class="product-card">
                    <div class="product-image">
                        <img src="<?php echo UPLOAD_URL . ($product['image'] ?: 'placeholder.jpg'); ?>" 
                             alt="<?php echo $product['name']; ?>"
                             onerror="this.src='https://via.placeholder.com/300x250/1A1A1A/D4AF37?text=AL-NAAZ'">
                        <?php if ($product['is_top_ranked']): ?>
                            <span class="badge badge-popular">Top Ranked</span>
                        <?php endif; ?>
                    </div>
                    <div class="product-info">
                        <span class="category-badge cat-masala">Masala</span>
                        <h3><?php echo $product['name']; ?></h3>
                        <p class="desc"><?php echo substr($product['description'], 0, 80); ?>...</p>
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
        </div>
        <div style="text-align: center; margin-top: 40px;">
            <a href="<?php echo SITE_URL; ?>pages/masala.php" class="btn-primary">View All Spices</a>
        </div>
    </div>
</section>

<!-- ============================================
PRODUCTS SECTION
============================================ -->
<section class="section section-dark">
    <div class="container">
        <div class="section-title">
            <h2>📦 Best Selling Products</h2>
            <p>Our most popular products loved by customers</p>
        </div>
        <div class="product-grid">
            <?php foreach ($best_selling as $product): ?>
                <div class="product-card">
                    <div class="product-image">
                        <img src="<?php echo UPLOAD_URL . ($product['image'] ?: 'placeholder.jpg'); ?>" 
                             alt="<?php echo $product['name']; ?>"
                             onerror="this.src='https://via.placeholder.com/300x250/1A1A1A/D4AF37?text=AL-NAAZ'">
                        <?php if ($product['is_best_selling']): ?>
                            <span class="badge badge-best">Best Seller</span>
                        <?php endif; ?>
                        <?php if ($product['is_combo']): ?>
                            <span class="badge badge-combo">Combo</span>
                        <?php endif; ?>
                    </div>
                    <div class="product-info">
                        <span class="category-badge cat-product">Product</span>
                        <h3><?php echo $product['name']; ?></h3>
                        <p class="desc"><?php echo substr($product['description'], 0, 80); ?>...</p>
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
        </div>
        <div style="text-align: center; margin-top: 40px;">
            <a href="<?php echo SITE_URL; ?>pages/products.php" class="btn-primary">View All Products</a>
        </div>
    </div>
</section>

<!-- ============================================
RAW MATERIALS SECTION
============================================ -->
<section class="section section-light">
    <div class="container">
        <div class="section-title">
            <h2>🌾 Raw Materials</h2>
            <p>Premium quality raw ingredients for your kitchen</p>
        </div>
        <div class="product-grid">
            <?php foreach ($raw_materials as $product): ?>
                <div class="product-card">
                    <div class="product-image">
                        <img src="<?php echo UPLOAD_URL . ($product['image'] ?: 'placeholder.jpg'); ?>" 
                             alt="<?php echo $product['name']; ?>"
                             onerror="this.src='https://via.placeholder.com/300x250/1A1A1A/D4AF37?text=AL-NAAZ'">
                    </div>
                    <div class="product-info">
                        <span class="category-badge cat-raw">Raw Material</span>
                        <h3><?php echo $product['name']; ?></h3>
                        <p class="desc"><?php echo substr($product['description'], 0, 80); ?>...</p>
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
        </div>
        <div style="text-align: center; margin-top: 40px;">
            <a href="<?php echo SITE_URL; ?>pages/raw-materials.php" class="btn-primary">View All Raw Materials</a>
        </div>
    </div>
</section>

<!-- ============================================
DRY FRUITS SECTION
============================================ -->
<section class="section section-dark">
    <div class="container">
        <div class="section-title">
            <h2>🥜 Premium Dry Fruits</h2>
            <p>Premium quality dry fruits and nuts for health and taste</p>
        </div>
        <div class="product-grid">
            <?php foreach ($dryfruits as $product): ?>
                <div class="product-card">
                    <div class="product-image">
                        <img src="<?php echo UPLOAD_URL . ($product['image'] ?: 'placeholder.jpg'); ?>" 
                             alt="<?php echo $product['name']; ?>"
                             onerror="this.src='https://via.placeholder.com/300x250/1A1A1A/D4AF37?text=AL-NAAZ'">
                        <?php if ($product['is_top_ranked']): ?>
                            <span class="badge badge-popular">Premium</span>
                        <?php endif; ?>
                    </div>
                    <div class="product-info">
                        <span class="category-badge cat-dryfruit">Dry Fruit</span>
                        <h3><?php echo $product['name']; ?></h3>
                        <p class="desc"><?php echo substr($product['description'], 0, 80); ?>...</p>
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
        </div>
        <div style="text-align: center; margin-top: 40px;">
            <a href="<?php echo SITE_URL; ?>pages/dryfruits.php" class="btn-primary">View All Dry Fruits</a>
        </div>
    </div>
</section>

<!-- ============================================
SEASONAL OFFERS SECTION
============================================ -->
<section class="section section-light">
    <div class="container">
        <div class="section-title">
            <h2>🎁 Seasonal Offers</h2>
            <p>Special festive deals and exclusive discounts</p>
        </div>
        <div class="product-grid">
            <?php foreach ($seasonal_offers as $offer): ?>
                <div class="product-card" style="border-color: var(--deep-red);">
                    <div class="product-image" style="background: linear-gradient(135deg, var(--deep-red), #6B0F0F);">
                        <img src="<?php echo UPLOAD_URL . ($offer['image'] ?: 'placeholder.jpg'); ?>" 
                             alt="<?php echo $offer['title']; ?>"
                             onerror="this.src='https://via.placeholder.com/300x250/1A1A1A/D4AF37?text=AL-NAAZ'">
                        <span class="badge badge-best"><?php echo $offer['discount_percent']; ?>% OFF</span>
                    </div>
                    <div class="product-info">
                        <span class="category-badge cat-seasonal">Seasonal</span>
                        <h3><?php echo $offer['title']; ?></h3>
                        <p class="desc"><?php echo substr($offer['description'], 0, 80); ?>...</p>
                        <div style="display: flex; gap: 10px; margin-top: 10px; font-size: 14px; color: var(--text-grey);">
                            <span>📅 <?php echo date('d M', strtotime($offer['start_date'])); ?> - <?php echo date('d M Y', strtotime($offer['end_date'])); ?></span>
                        </div>
                        <div style="margin-top: 15px;">
                            <a href="<?php echo SITE_URL; ?>pages/seasonal-offers.php" class="btn-primary" style="padding: 8px 25px; font-size: 14px;">
                                <i class="fas fa-gift"></i> View Offer
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div style="text-align: center; margin-top: 40px;">
            <a href="<?php echo SITE_URL; ?>pages/seasonal-offers.php" class="btn-secondary">View All Offers</a>
        </div>
    </div>
</section>

<!-- ============================================
ERP BASIC DATA SECTION
============================================ -->
<section class="section section-dark">
    <div class="container">
        <div class="section-title">
            <h2>📊 Business Insights</h2>
            <p>Trusted numbers that speak for our quality</p>
        </div>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 30px;">
            <div style="background: var(--dark-black); padding: 30px; border-radius: 15px; text-align: center; border: 1px solid rgba(212, 175, 55, 0.1);">
                <div style="font-size: 48px; color: var(--royal-gold);">👥</div>
                <div style="font-size: 36px; font-weight: 900; color: var(--royal-gold); margin: 10px 0;">
                    <?php echo number_format($total_customers > 0 ? $total_customers : 1250); ?>+
                </div>
                <div style="color: var(--text-grey);">Total Customers</div>
            </div>
            <div style="background: var(--dark-black); padding: 30px; border-radius: 15px; text-align: center; border: 1px solid rgba(212, 175, 55, 0.1);">
                <div style="font-size: 48px; color: var(--royal-gold);">📦</div>
                <div style="font-size: 36px; font-weight: 900; color: var(--royal-gold); margin: 10px 0;">
                    <?php echo number_format($total_products > 0 ? $total_products : 75); ?>+
                </div>
                <div style="color: var(--text-grey);">Total Products</div>
            </div>
            <div style="background: var(--dark-black); padding: 30px; border-radius: 15px; text-align: center; border: 1px solid rgba(212, 175, 55, 0.1);">
                <div style="font-size: 48px; color: var(--royal-gold);">⭐</div>
                <div style="font-size: 36px; font-weight: 900; color: var(--royal-gold); margin: 10px 0;">4.8★</div>
                <div style="color: var(--text-grey);">Customer Rating</div>
            </div>
            <div style="background: var(--dark-black); padding: 30px; border-radius: 15px; text-align: center; border: 1px solid rgba(212, 175, 55, 0.1);">
                <div style="font-size: 48px; color: var(--royal-gold);">🏆</div>
                <div style="font-size: 36px; font-weight: 900; color: var(--royal-gold); margin: 10px 0;">15+</div>
                <div style="color: var(--text-grey);">Years of Excellence</div>
            </div>
        </div>
    </div>
</section>

<!-- ============================================
WHOLESALE ENQUIRY SECTION
============================================ -->
<section class="section section-light">
    <div class="container">
        <div style="max-width: 800px; margin: 0 auto; text-align: center; background: var(--dark-black); padding: 50px; border-radius: 15px; border: 2px solid var(--royal-gold);">
            <div style="font-size: 60px; margin-bottom: 20px;">� wholesale</div>
            <h2 style="font-family: 'Playfair Display', serif; font-size: 36px; color: var(--royal-gold); margin-bottom: 15px;">
                Bulk Orders & Wholesale
            </h2>
            <p style="color: var(--text-grey); margin-bottom: 30px; font-size: 16px;">
                Looking for bulk quantities? Contact us directly on WhatsApp for 
                wholesale pricing, custom blends, and bulk delivery options.
            </p>
            <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
                <a href="https://wa.me/<?php echo $settings['whatsapp_number'] ?? '919876543210'; ?>?text=Hello%20AL-NAAZ%20FOOD!%20I'm%20interested%20in%20wholesale%20purchasing." 
                   target="_blank" class="btn-primary" style="background: #25D366;">
                    <i class="fab fa-whatsapp"></i> Enquire on WhatsApp
                </a>
                <a href="<?php echo SITE_URL; ?>pages/contact.php" class="btn-secondary">
                    <i class="fas fa-envelope"></i> Contact Form
                </a>
            </div>
        </div>
    </div>
</section>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>