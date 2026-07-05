<?php
// ============================================
// AL-NAAZ FOOD - Edit Owner (Admin)
// ============================================

require_once __DIR__ . '/../config/config.php';

if (!isLoggedIn() || !isOwner()) {
    redirect('auth/login.php');
}

$page_css = 'admin.css';
$page_js = 'admin.js';

$message = '';
$error = '';

// Get owner details
$owner = getRow("SELECT * FROM owner_details LIMIT 1");

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $designation = sanitize($_POST['designation'] ?? '');
    $bio = sanitize($_POST['bio'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $address = sanitize($_POST['address'] ?? '');
    
    if ($owner) {
        $sql = "UPDATE owner_details SET name = ?, designation = ?, bio = ?, email = ?, phone = ?, address = ? WHERE id = ?";
        $result = executeQuery($sql, [$name, $designation, $bio, $email, $phone, $address, $owner['id']], 'ssssssi');
    } else {
        $sql = "INSERT INTO owner_details (name, designation, bio, email, phone, address) VALUES (?, ?, ?, ?, ?, ?)";
        $result = insertData($sql, [$name, $designation, $bio, $email, $phone, $address], 'ssssss');
    }
    
    if ($result) {
        $message = 'Owner details updated successfully!';
        $owner = getRow("SELECT * FROM owner_details LIMIT 1");
    } else {
        $error = 'Failed to update owner details';
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
                <li><a href="edit-products.php"><i class="fas fa-box"></i> Edit Products</a></li>
                <li><a href="edit-masala.php"><i class="fas fa-pepper-hot"></i> Edit Masala</a></li>
                <li><a href="edit-raw.php"><i class="fas fa-seedling"></i> Edit Raw Materials</a></li>
                <li><a href="edit-dryfruit.php"><i class="fas fa-nut"></i> Edit Dry Fruits</a></li>
                <li><a href="edit-owner.php" class="active"><i class="fas fa-user-tie"></i> Edit Owner</a></li>
                <li><a href="edit-settings.php"><i class="fas fa-cog"></i> Settings</a></li>
                <li><a href="<?php echo SITE_URL; ?>erp/dashboard.php"><i class="fas fa-chart-bar"></i> ERP</a></li>
                <li><a href="<?php echo SITE_URL; ?>auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </nav>
    </aside>

    <main class="admin-main">
        <div class="admin-header">
            <h1>👑 Edit Owner Details</h1>
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
                <h3>Owner Information</h3>
            </div>
            <div class="card-body">
                <form method="POST" class="edit-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" name="name" value="<?php echo $owner['name'] ?? ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Designation</label>
                            <input type="text" name="designation" value="<?php echo $owner['designation'] ?? ''; ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Bio</label>
                        <textarea name="bio" rows="4"><?php echo $owner['bio'] ?? ''; ?></textarea>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" value="<?php echo $owner['email'] ?? ''; ?>">
                        </div>
                        <div class="form-group">
                            <label>Phone</label>
                            <input type="text" name="phone" value="<?php echo $owner['phone'] ?? ''; ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Address</label>
                        <textarea name="address" rows="3"><?php echo $owner['address'] ?? ''; ?></textarea>
                    </div>
                    <button type="submit" class="btn-save">💾 Save Owner Details</button>
                </form>
            </div>
        </div>
    </main>
</div>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>