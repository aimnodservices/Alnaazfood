<?php
// ============================================
// AL-NAAZ FOOD - Edit Products (Admin)
// ============================================

require_once __DIR__ . '/../config/config.php';

if (!isLoggedIn() || !isOwner()) {
    redirect('auth/login.php');
}

$page_css = 'admin.css';
$page_js = 'admin.js';

$message = '';
$error = '';

// Get all products
$products = getRows("SELECT * FROM products ORDER BY category, name");

// Handle add product
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $category = sanitize($_POST['category'] ?? '');
    $name = sanitize($_POST['name'] ?? '');
    $description = sanitize($_POST['description'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $discount_price = (float)($_POST['discount_price'] ?? 0);
    $stock = (int)($_POST['stock'] ?? 0);
    $is_top_ranked = isset($_POST['is_top_ranked']) ? 1 : 0;
    $is_best_selling = isset($_POST['is_best_selling']) ? 1 : 0;
    $is_combo = isset($_POST['is_combo']) ? 1 : 0;
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    
    if ($category && $name && $price > 0) {
        $sql = "INSERT INTO products (category, name, description, price, discount_price, stock, is_top_ranked, is_best_selling, is_combo, is_featured) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $result = insertData($sql, [$category, $name, $description, $price, $discount_price, $stock, $is_top_ranked, $is_best_selling, $is_combo, $is_featured], 'sssddiiiii');
        
        if ($result) {
            $message = 'Product added successfully!';
            $products = getRows("SELECT * FROM products ORDER BY category, name");
        } else {
            $error = 'Failed to add product';
        }
    } else {
        $error = 'Please fill all required fields';
    }
}

// Handle update product
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_product'])) {
    $id = (int)$_POST['id'];
    $category = sanitize($_POST['category'] ?? '');
    $name = sanitize($_POST['name'] ?? '');
    $description = sanitize($_POST['description'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $discount_price = (float)($_POST['discount_price'] ?? 0);
    $stock = (int)($_POST['stock'] ?? 0);
    $is_top_ranked = isset($_POST['is_top_ranked']) ? 1 : 0;
    $is_best_selling = isset($_POST['is_best_selling']) ? 1 : 0;
    $is_combo = isset($_POST['is_combo']) ? 1 : 0;
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    
    $sql = "UPDATE products SET category = ?, name = ?, description = ?, price = ?, discount_price = ?, stock = ?, is_top_ranked = ?, is_best_selling = ?, is_combo = ?, is_featured = ? WHERE id = ?";
    $result = executeQuery($sql, [$category, $name, $description, $price, $discount_price, $stock, $is_top_ranked, $is_best_selling, $is_combo, $is_featured, $id], 'sssddiiiiii');
    
    if ($result) {
        $message = 'Product updated successfully!';
        $products = getRows("SELECT * FROM products ORDER BY category, name");
    } else {
        $error = 'Failed to update product';
    }
}

// Handle delete product
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $sql = "DELETE FROM products WHERE id = ?";
    $result = executeQuery($sql, [$id], 'i');
    
    if ($result) {
        $message = 'Product deleted successfully!';
        $products = getRows("SELECT * FROM products ORDER BY category, name");
    } else {
        $error = 'Failed to delete product';
    }
}

include_once __DIR__ . '/../includes/header.php';
?>

<div class="admin-wrapper">
    <aside class="admin-sidebar">
        <div class="sidebar-header">
            <h3>👑 AL-NAAZ</h3>
            <p>Admin Panel</p>
        </div>
        <nav class="sidebar-nav">
            <ul>
                <li><a href="dashboard.php"><i class="fas fa-chart-pie"></i> Dashboard</a></li>
                <li><a href="analytics.php"><i class="fas fa-chart-line"></i> Analytics</a></li>
                <li><a href="edit-home.php"><i class="fas fa-home"></i> Edit Home</a></li>
                <li><a href="edit-hero.php"><i class="fas fa-image"></i> Edit Hero</a></li>
                <li><a href="edit-products.php" class="active"><i class="fas fa-box"></i> Edit Products</a></li>
                <li><a href="edit-masala.php"><i class="fas fa-pepper-hot"></i> Edit Masala</a></li>
                <li><a href="edit-raw.php"><i class="fas fa-seedling"></i> Edit Raw Materials</a></li>
                <li><a href="edit-dryfruit.php"><i class="fas fa-nut"></i> Edit Dry Fruits</a></li>
                <li><a href="edit-owner.php"><i class="fas fa-user-tie"></i> Edit Owner</a></li>
                <li><a href="edit-settings.php"><i class="fas fa-cog"></i> Settings</a></li>
                <li><a href="<?php echo SITE_URL; ?>erp/dashboard.php"><i class="fas fa-chart-bar"></i> ERP</a></li>
                <li><a href="<?php echo SITE_URL; ?>auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </nav>
    </aside>

    <main class="admin-main">
        <div class="admin-header">
            <h1>📦 Edit Products</h1>
        </div>

        <?php if ($message): ?>
            <div style="background: #1A4A1A; color: #F5F5F5; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                ✅ <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div style="background: #8B1A1A; color: #F5F5F5; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                ❌ <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <!-- Add Product -->
        <div class="admin-card" style="margin-bottom: 30px;">
            <div class="card-header">
                <h3>➕ Add New Product</h3>
            </div>
            <div class="card-body">
                <form method="POST" class="edit-form">
                    <input type="hidden" name="add_product" value="1">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Category *</label>
                            <select name="category" required>
                                <option value="masala">🌶️ Masala</option>
                                <option value="product">📦 Product</option>
                                <option value="raw_material">🌾 Raw Material</option>
                                <option value="dryfruit">🥜 Dry Fruit</option>
                                <option value="seasonal">🎁 Seasonal</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Product Name *</label>
                            <input type="text" name="name" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" rows="3"></textarea>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Price (₹) *</label>
                            <input type="number" name="price" step="0.01" required>
                        </div>
                        <div class="form-group">
                            <label>Discount Price (₹)</label>
                            <input type="number" name="discount_price" step="0.01" value="0">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Stock</label>
                            <input type="number" name="stock" value="0">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label><input type="checkbox" name="is_top_ranked"> Top Ranked</label>
                        </div>
                        <div class="form-group">
                            <label><input type="checkbox" name="is_best_selling"> Best Selling</label>
                        </div>
                        <div class="form-group">
                            <label><input type="checkbox" name="is_combo"> Combo</label>
                        </div>
                        <div class="form-group">
                            <label><input type="checkbox" name="is_featured"> Featured</label>
                        </div>
                    </div>
                    <button type="submit" class="btn-save">➕ Add Product</button>
                </form>
            </div>
        </div>

        <!-- Product List -->
        <div class="admin-card">
            <div class="card-header">
                <h3>📋 All Products</h3>
                <span style="color: var(--text-grey); font-size: 14px;">Total: <?php echo count($products); ?></span>
            </div>
            <div class="card-body" style="overflow-x: auto;">
                <?php if (!empty($products)): ?>
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Category</th>
                                <th>Name</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $product): ?>
                                <tr>
                                    <td><?php echo $product['id']; ?></td>
                                    <td>
                                        <span class="category-badge cat-<?php echo $product['category']; ?>">
                                            <?php echo ucfirst(str_replace('_', ' ', $product['category'])); ?>
                                        </span>
                                    </td>
                                    <td><?php echo $product['name']; ?></td>
                                    <td>₹<?php echo number_format($product['price'], 2); ?></td>
                                    <td><?php echo $product['stock']; ?></td>
                                    <td>
                                        <?php if ($product['is_top_ranked']): ?>
                                            <span class="status-badge" style="background: rgba(212, 175, 55, 0.2); color: var(--royal-gold);">Top</span>
                                        <?php endif; ?>
                                        <?php if ($product['is_best_selling']): ?>
                                            <span class="status-badge" style="background: rgba(76, 175, 80, 0.2); color: #4CAF50;">Best</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="?delete=<?php echo $product['id']; ?>" onclick="return confirm('Delete this product?')" style="color: var(--deep-red);">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p style="color: var(--text-grey); text-align: center;">No products found</p>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>