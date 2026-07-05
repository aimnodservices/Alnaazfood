<?php
// ============================================
// AL-NAAZ FOOD - Edit Hero Section (Admin)
// ============================================

require_once __DIR__ . '/../config/config.php';

if (!isLoggedIn() || !isOwner()) {
    redirect('auth/login.php');
}

$page_css = 'admin.css';
$page_js = 'admin.js';

$message = '';
$error = '';

// Get hero sections
$hero_items = getRows("SELECT * FROM hero_section ORDER BY type, display_order");

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_hero'])) {
        $type = sanitize($_POST['type'] ?? 'offer');
        $title = sanitize($_POST['title'] ?? '');
        $description = sanitize($_POST['description'] ?? '');
        $button_text = sanitize($_POST['button_text'] ?? '');
        $button_link = sanitize($_POST['button_link'] ?? '');
        $display_order = (int)($_POST['display_order'] ?? 0);
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        
        $sql = "INSERT INTO hero_section (type, title, description, button_text, button_link, display_order, is_active) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $result = insertData($sql, [$type, $title, $description, $button_text, $button_link, $display_order, $is_active], 'sssssii');
        
        if ($result) {
            $message = 'Hero section added successfully!';
        } else {
            $error = 'Failed to add hero section';
        }
    } elseif (isset($_POST['update_hero'])) {
        $id = (int)$_POST['id'];
        $type = sanitize($_POST['type'] ?? 'offer');
        $title = sanitize($_POST['title'] ?? '');
        $description = sanitize($_POST['description'] ?? '');
        $button_text = sanitize($_POST['button_text'] ?? '');
        $button_link = sanitize($_POST['button_link'] ?? '');
        $display_order = (int)($_POST['display_order'] ?? 0);
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        
        $sql = "UPDATE hero_section SET type = ?, title = ?, description = ?, button_text = ?, button_link = ?, display_order = ?, is_active = ? WHERE id = ?";
        $result = executeQuery($sql, [$type, $title, $description, $button_text, $button_link, $display_order, $is_active, $id], 'sssssiii');
        
        if ($result) {
            $message = 'Hero section updated successfully!';
        } else {
            $error = 'Failed to update hero section';
        }
    } elseif (isset($_POST['delete_hero'])) {
        $id = (int)$_POST['id'];
        $sql = "DELETE FROM hero_section WHERE id = ?";
        $result = executeQuery($sql, [$id], 'i');
        
        if ($result) {
            $message = 'Hero section deleted successfully!';
        } else {
            $error = 'Failed to delete hero section';
        }
    }
}

// Refresh hero items
$hero_items = getRows("SELECT * FROM hero_section ORDER BY type, display_order");

include_once __DIR__ . '/../includes/header.php';
?>

<div class="admin-wrapper">
    <aside class="admin-sidebar">
        <!-- Same sidebar -->
        <div class="sidebar-header">
            <h3>👑 AL-NAAZ</h3>
            <p>Admin Panel</p>
        </div>
        <nav class="sidebar-nav">
            <ul>
                <li><a href="dashboard.php"><i class="fas fa-chart-pie"></i> Dashboard</a></li>
                <li><a href="analytics.php"><i class="fas fa-chart-line"></i> Analytics</a></li>
                <li><a href="edit-home.php"><i class="fas fa-home"></i> Edit Home</a></li>
                <li><a href="edit-hero.php" class="active"><i class="fas fa-image"></i> Edit Hero</a></li>
                <li><a href="edit-products.php"><i class="fas fa-box"></i> Edit Products</a></li>
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
            <h1>Edit Hero Section</h1>
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

        <!-- Add New -->
        <div class="admin-card" style="margin-bottom: 30px;">
            <div class="card-header">
                <h3>➕ Add New Hero Item</h3>
            </div>
            <div class="card-body">
                <form method="POST" class="edit-form">
                    <input type="hidden" name="add_hero" value="1">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Type</label>
                            <select name="type">
                                <option value="offer">Offer</option>
                                <option value="achievement">Achievement</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Display Order</label>
                            <input type="number" name="display_order" value="0">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Title</label>
                        <input type="text" name="title" placeholder="e.g., 20% Off" required>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <input type="text" name="description" placeholder="Brief description" required>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Button Text (optional)</label>
                            <input type="text" name="button_text" placeholder="Shop Now">
                        </div>
                        <div class="form-group">
                            <label>Button Link (optional)</label>
                            <input type="text" name="button_link" placeholder="#products">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="is_active" checked> Active
                        </label>
                    </div>
                    <button type="submit" class="btn-save">➕ Add Hero Item</button>
                </form>
            </div>
        </div>

        <!-- Existing Items -->
        <div class="admin-card">
            <div class="card-header">
                <h3>📋 Existing Hero Items</h3>
            </div>
            <div class="card-body">
                <?php if (!empty($hero_items)): ?>
                    <?php foreach ($hero_items as $item): ?>
                        <form method="POST" class="edit-form" style="border-bottom: 1px solid rgba(212, 175, 55, 0.1); padding-bottom: 20px; margin-bottom: 20px;">
                            <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Type</label>
                                    <select name="type">
                                        <option value="offer" <?php echo $item['type'] == 'offer' ? 'selected' : ''; ?>>Offer</option>
                                        <option value="achievement" <?php echo $item['type'] == 'achievement' ? 'selected' : ''; ?>>Achievement</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Order</label>
                                    <input type="number" name="display_order" value="<?php echo $item['display_order']; ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Title</label>
                                <input type="text" name="title" value="<?php echo $item['title']; ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Description</label>
                                <input type="text" name="description" value="<?php echo $item['description']; ?>" required>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Button Text</label>
                                    <input type="text" name="button_text" value="<?php echo $item['button_text']; ?>">
                                </div>
                                <div class="form-group">
                                    <label>Button Link</label>
                                    <input type="text" name="button_link" value="<?php echo $item['button_link']; ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label>
                                    <input type="checkbox" name="is_active" <?php echo $item['is_active'] ? 'checked' : ''; ?>> Active
                                </label>
                            </div>
                            <div style="display: flex; gap: 10px;">
                                <button type="submit" name="update_hero" class="btn-sm" style="background: var(--royal-gold); color: var(--primary-black); padding: 8px 20px; border: none; border-radius: 8px; cursor: pointer;">Update</button>
                                <button type="submit" name="delete_hero" class="btn-sm" style="background: var(--deep-red); color: white; padding: 8px 20px; border: none; border-radius: 8px; cursor: pointer;" onclick="return confirm('Delete this hero item?')">Delete</button>
                            </div>
                        </form>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="color: var(--text-grey); text-align: center;">No hero items available</p>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>