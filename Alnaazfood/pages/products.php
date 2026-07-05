<?php
// ============================================
// AL-NAAZ FOOD - Products Page
// ============================================

require_once __DIR__ . '/../config/config.php';

$page_css = 'products.css';
$page_js = 'products.js';

// Get all products with filters
$category = isset($_GET['category']) ? sanitize($_GET['category']) : '';
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';

$sql = "SELECT * FROM products WHERE category IN ('product', 'masala', 'raw_material', 'dryfruit')";
$params = [];
$types = '';

if (!empty($category) && $category != 'all') {
    $sql .= " AND category = ?";
    $params[] = $category;
    $types .= 's';
}

if (!empty($search)) {
    $sql .= " AND (name LIKE ? OR description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $types .= 'ss';
}

$sql .= " ORDER BY id DESC";

$products = getRows($sql, $params, $types);

// Get top selling
$top_selling = getRows("SELECT * FROM products WHERE is_best_selling = TRUE LIMIT 4");
$combos = getRows("SELECT * FROM products WHERE is_combo = TRUE LIMIT 4");

// Track visitor
$ip = getClientIP();
$session_id = session_id();
$sql = "INSERT INTO visitor_analytics (ip_address, session_id, page_visited) VALUES (?, ?, ?)";
insertData($sql, [$ip, $session_id, 'products'], 'sss');

include_once __DIR__ . '/../includes/header.php';
?>

<!-- Page Banner -->
<section class="page-banner">
    <div class="container">
        <h1>📦 Our Products</h1>
        <p>Premium quality products for your kitchen</p>
    </div>
</section>

<!-- Filters -->
<section class="section section-light" style="padding: 30px 0;">
    <div class="container">
        <form method="GET" class="filter-form">
            <div class="filter-row">
                <div class="filter-group">
                    <label>Category:</label>
                    <select name="category" onchange="this.form.submit()">
                        <option value="all">All Categories</option>
                        <option value="masala" <?php echo $category == 'masala' ? 'selected' : ''; ?>>🌶️ Spices</option>
                        <option value="product" <?php echo $category == 'product' ? 'selected' : ''; ?>>📦 Products</option>
                        <option value="raw_material" <?php echo $category == 'raw_material' ? 'selected' : ''; ?>>🌾 Raw Materials</option>
                        <option value="dryfruit" <?php echo $category == 'dryfruit' ? 'selected' : ''; ?>>🥜 Dry Fruits</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label>Search:</label>
                    <input type="text" name="search" placeholder="Search products..." value="<?php echo $search; ?>">
                </div>
                <div class="filter-group" style="display: flex; align-items: flex-end;">
                    <button type="submit" class="btn-primary" style="padding: 10px 30px; font-size: 14px;">
                        <i class="fas fa-search"></i> Search
                    </button>
                    <?php if (!empty($search) || !empty($category)): ?>
                        <a href="<?php echo SITE_URL; ?>pages/products.php" class="btn-secondary" style="padding: 10px 20px; font-size: 14px; margin-left: 10px;">
                            <i class="fas fa-times"></i> Clear
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </div>
</section>

<!-- Top Selling Products -->
<section class="section section-dark">
    <div class="container">
        <div class="section-title">
            <h2>🔥 Top Selling Products</h2>
            <p>Our most popular products loved by customers</p>
        </div>
        <div class="product-grid">
            <?php if (!empty($top_selling)): ?>
                <?php foreach ($top_selling as $product): ?>
                    <div class="product-card">
                        <div class="product-image">
                            <img src="<?php echo UPLOAD_URL . ($product['image'] ?: 'placeholder.jpg'); ?>" 
                                 alt="<?php echo $product['name']; ?>"
                                 onerror="this.src='https://via.placeholder.com/300x250/1A1A1A/D4AF37?text=AL-NAAZ'">
                            <span class="badge badge-best">Best Seller</span>
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
                <p style="color: var(--text-grey); text-align: center; grid-column: 1/-1;">No top selling products found.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Combo Offers -->
<section class="section section-light">
    <div class="container">
        <div class="section-title">
            <h2>🎯 Combo Offers</h2>
            <p>Special combos at unbeatable prices</p>
        </div>
        <div class="product-grid">
            <?php if (!empty($combos)): ?>
                <?php foreach ($combos as $product): ?>
                    <div class="product-card" style="border-color: var(--royal-gold);">
                        <div class="product-image">
                            <img src="<?php echo UPLOAD_URL . ($product['image'] ?: 'placeholder.jpg'); ?>" 
                                 alt="<?php echo $product['name']; ?>"
                                 onerror="this.src='https://via.placeholder.com/300x250/1A1A1A/D4AF37?text=AL-NAAZ'">
                            <span class="badge badge-combo">Combo</span>
                        </div>
                        <div class="product-info">
                            <span class="category-badge cat-product">Combo</span>
                            <h3><?php echo $product['name']; ?></h3>
                            <p class="desc"><?php echo substr($product['description'], 0, 80); ?>...</p>
                            <div class="price">
                                <span class="current">₹<?php echo number_format($product['discount_price'] ?: $product['price'], 2); ?></span>
                                <?php if ($product['discount_price']): ?>
                                    <span class="original">₹<?php echo number_format($product['price'], 2); ?></span>
                                    <span class="discount">SAVE <?php echo round((1 - $product['discount_price'] / $product['price']) * 100); ?>%</span>
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
                <p style="color: var(--text-grey); text-align: center; grid-column: 1/-1;">No combo offers available.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- All Products -->
<section class="section section-dark">
    <div class="container">
        <div class="section-title">
            <h2>📦 All Products</h2>
            <p>Complete collection of premium products</p>
        </div>
        <div class="product-grid">
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $product): ?>
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
                            <span class="category-badge cat-<?php echo $product['category']; ?>">
                                <?php echo ucfirst(str_replace('_', ' ', $product['category'])); ?>
                            </span>
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
            <?php else: ?>
                <p style="color: var(--text-grey); text-align: center; grid-column: 1/-1;">No products found.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Reservation Section -->
<section class="section section-light">
    <div class="container">
        <div class="reservation-box">
            <h2>📋 Bulk Reservation</h2>
            <p>
                Reserve bulk quantities of our products for your business or event. 
                We ensure timely delivery and best prices.
            </p>
            <div class="reservation-form">
                <form action="<?php echo SITE_URL; ?>api/reserve-product.php" method="POST">
                    <div class="form-row">
                        <div class="form-group">
                            <input type="text" name="name" placeholder="Your Name" required>
                        </div>
                        <div class="form-group">
                            <input type="email" name="email" placeholder="Your Email" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <input type="tel" name="phone" placeholder="Phone Number" required>
                        </div>
                        <div class="form-group">
                            <input type="text" name="product" placeholder="Product Name" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <input type="number" name="quantity" placeholder="Quantity" required>
                        </div>
                        <div class="form-group">
                            <input type="date" name="delivery_date" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <textarea name="message" placeholder="Special Requirements (optional)" rows="3"></textarea>
                    </div>
                    <button type="submit" class="btn-primary">Submit Reservation</button>
                </form>
            </div>
        </div>
    </div>
</section>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>