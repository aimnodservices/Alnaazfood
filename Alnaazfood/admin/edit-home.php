<?php
// ============================================
// AL-NAAZ FOOD - Edit Home Page (Admin)
// ============================================

require_once __DIR__ . '/../config/config.php';

if (!isLoggedIn() || !isOwner()) {
    redirect('auth/login.php');
}

$page_css = 'admin.css';
$page_js = 'admin.js';

$message = '';
$error = '';

// Get current settings
$settings = [];
$settings_result = executeQuery("SELECT setting_key, setting_value FROM website_settings");
if ($settings_result) {
    while ($row = $settings_result->fetch_assoc()) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $site_name = sanitize($_POST['site_name'] ?? '');
    $site_tagline = sanitize($_POST['site_tagline'] ?? '');
    $contact_email = sanitize($_POST['contact_email'] ?? '');
    $contact_phone = sanitize($_POST['contact_phone'] ?? '');
    $contact_address = sanitize($_POST['contact_address'] ?? '');
    $map_embed = $_POST['map_embed'] ?? '';
    $whatsapp_number = sanitize($_POST['whatsapp_number'] ?? '');
    $facebook_url = sanitize($_POST['facebook_url'] ?? '');
    $instagram_url = sanitize($_POST['instagram_url'] ?? '');
    $youtube_url = sanitize($_POST['youtube_url'] ?? '');
    
    // Update settings
    $updates = [
        'site_name' => $site_name,
        'site_tagline' => $site_tagline,
        'contact_email' => $contact_email,
        'contact_phone' => $contact_phone,
        'contact_address' => $contact_address,
        'map_embed' => $map_embed,
        'whatsapp_number' => $whatsapp_number,
        'facebook_url' => $facebook_url,
        'instagram_url' => $instagram_url,
        'youtube_url' => $youtube_url
    ];
    
    foreach ($updates as $key => $value) {
        $sql = "UPDATE website_settings SET setting_value = ? WHERE setting_key = ?";
        executeQuery($sql, [$value, $key], 'ss');
    }
    
    $message = 'Home page settings updated successfully!';
}

include_once __DIR__ . '/../includes/header.php';
?>

<div class="admin-wrapper">
    <aside class="admin-sidebar">
        <!-- Same sidebar as dashboard -->
        <div class="sidebar-header">
            <h3>👑 AL-NAAZ</h3>
            <p>Admin Panel</p>
        </div>
        <nav class="sidebar-nav">
            <ul>
                <li><a href="dashboard.php"><i class="fas fa-chart-pie"></i> Dashboard</a></li>
                <li><a href="analytics.php"><i class="fas fa-chart-line"></i> Analytics</a></li>
                <li><a href="edit-home.php" class="active"><i class="fas fa-home"></i> Edit Home</a></li>
                <li><a href="edit-hero.php"><i class="fas fa-image"></i> Edit Hero</a></li>
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
            <h1>Edit Home Page</h1>
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

        <div class="admin-card">
            <div class="card-header">
                <h3>General Settings</h3>
            </div>
            <div class="card-body">
                <form method="POST" class="edit-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Site Name</label>
                            <input type="text" name="site_name" value="<?php echo $settings['site_name'] ?? SITE_NAME; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Site Tagline</label>
                            <input type="text" name="site_tagline" value="<?php echo $settings['site_tagline'] ?? SITE_TAGLINE; ?>" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Contact Email</label>
                            <input type="email" name="contact_email" value="<?php echo $settings['contact_email'] ?? ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Contact Phone</label>
                            <input type="text" name="contact_phone" value="<?php echo $settings['contact_phone'] ?? ''; ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Contact Address</label>
                        <textarea name="contact_address" rows="3"><?php echo $settings['contact_address'] ?? ''; ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>WhatsApp Number (for enquiries)</label>
                        <input type="text" name="whatsapp_number" value="<?php echo $settings['whatsapp_number'] ?? ''; ?>" placeholder="919876543210">
                    </div>

                    <div class="form-group">
                        <label>Google Maps Embed Code</label>
                        <textarea name="map_embed" rows="4"><?php echo $settings['map_embed'] ?? ''; ?></textarea>
                        <small style="color: var(--text-grey);">Get embed code from Google Maps</small>
                    </div>

                    <h4 style="color: var(--royal-gold); margin: 20px 0 15px;">Social Media Links</h4>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Facebook URL</label>
                            <input type="url" name="facebook_url" value="<?php echo $settings['facebook_url'] ?? ''; ?>" placeholder="https://facebook.com/...">
                        </div>
                        <div class="form-group">
                            <label>Instagram URL</label>
                            <input type="url" name="instagram_url" value="<?php echo $settings['instagram_url'] ?? ''; ?>" placeholder="https://instagram.com/...">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>YouTube URL</label>
                            <input type="url" name="youtube_url" value="<?php echo $settings['youtube_url'] ?? ''; ?>" placeholder="https://youtube.com/...">
                        </div>
                    </div>

                    <button type="submit" class="btn-save">💾 Save Settings</button>
                </form>
            </div>
        </div>
    </main>
</div>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>