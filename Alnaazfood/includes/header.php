<?php
// ============================================
// AL-NAAZ FOOD - Header
// ============================================

// Get current page for active class
$current_page = basename($_SERVER['PHP_SELF']);
$current_dir = basename(dirname($_SERVER['PHP_SELF']));

// Get website settings
$settings = [];
$settings_result = executeQuery("SELECT setting_key, setting_value FROM website_settings");
if ($settings_result) {
    while ($row = $settings_result->fetch_assoc()) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?> - <?php echo SITE_TAGLINE; ?></title>
    
    <!-- Favicon -->
    <link rel="icon" href="<?php echo SITE_URL; ?>assets/images/favicon.ico">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>assets/css/style.css">
    
    <!-- Page specific CSS -->
    <?php if (isset($page_css)): ?>
        <link rel="stylesheet" href="<?php echo SITE_URL; ?>assets/css/<?php echo $page_css; ?>">
    <?php endif; ?>
</head>
<body>
    <!-- ============================================
    NAVIGATION BAR
    ============================================ -->
    <nav class="navbar" id="mainNav">
        <div class="nav-container">
            <!-- Logo -->
            <div class="nav-logo">
                <a href="<?php echo SITE_URL; ?>">
                    <span class="logo-icon">✨</span>
                    <span class="logo-text">AL-NAAZ <span>FOOD</span></span>
                </a>
            </div>
            
            <!-- Nav Links -->
            <div class="nav-links" id="navLinks">
                <ul>
                    <li><a href="<?php echo SITE_URL; ?>" class="<?php echo ($current_page == 'index.php' || $current_page == '') ? 'active' : ''; ?>"><i class="fas fa-home"></i> Home</a></li>
                    <li><a href="<?php echo SITE_URL; ?>masala" class="<?php echo ($current_page == 'masala.php' || $current_page == 'masala') ? 'active' : ''; ?>"><i class="fas fa-pepper-hot"></i> Spices</a></li>
                    <li><a href="<?php echo SITE_URL; ?>products" class="<?php echo ($current_page == 'products.php' || $current_page == 'products') ? 'active' : ''; ?>"><i class="fas fa-box"></i> Products</a></li>
                    <li><a href="<?php echo SITE_URL; ?>raw-materials" class="<?php echo ($current_page == 'raw-materials.php' || $current_page == 'raw-materials') ? 'active' : ''; ?>"><i class="fas fa-seedling"></i> Raw Materials</a></li>
                    <li><a href="<?php echo SITE_URL; ?>dryfruits" class="<?php echo ($current_page == 'dryfruits.php' || $current_page == 'dryfruits') ? 'active' : ''; ?>"><i class="fas fa-nut"></i> Dry Fruits</a></li>
                    <li><a href="<?php echo SITE_URL; ?>seasonal-offers" class="<?php echo ($current_page == 'seasonal-offers.php' || $current_page == 'seasonal-offers') ? 'active' : ''; ?>"><i class="fas fa-gift"></i> Offers</a></li>
                    <li><a href="<?php echo SITE_URL; ?>catering" class="<?php echo ($current_page == 'catering.php' || $current_page == 'catering') ? 'active' : ''; ?>"><i class="fas fa-utensils"></i> Catering</a></li>
                    <li><a href="<?php echo SITE_URL; ?>owner" class="<?php echo ($current_page == 'owner-details.php' || $current_page == 'owner') ? 'active' : ''; ?>"><i class="fas fa-user-tie"></i> Owner</a></li>
                    <li><a href="<?php echo SITE_URL; ?>contact" class="<?php echo ($current_page == 'contact.php' || $current_page == 'contact') ? 'active' : ''; ?>"><i class="fas fa-envelope"></i> Contact</a></li>
                </ul>
            </div>
            
            <!-- Right Side - Login/Auth -->
            <div class="nav-right">
                <?php if (isLoggedIn()): ?>
                    <?php if (isOwner()): ?>
                        <a href="<?php echo SITE_URL; ?>admin" class="nav-btn owner-btn">
                            <i class="fas fa-crown"></i> Admin
                        </a>
                    <?php else: ?>
                        <span class="user-greeting">
                            <i class="fas fa-user"></i> <?php echo $_SESSION['user_name']; ?>
                        </span>
                    <?php endif; ?>
                    <a href="<?php echo SITE_URL; ?>logout" class="nav-btn logout-btn">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                <?php else: ?>
                    <a href="<?php echo SITE_URL; ?>login" class="nav-btn login-btn">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </a>
                    <a href="<?php echo SITE_URL; ?>register" class="nav-btn register-btn">
                        <i class="fas fa-user-plus"></i> Register
                    </a>
                <?php endif; ?>
                
                <!-- Cart -->
                <a href="#" class="nav-btn cart-btn" onclick="openCart()">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="cart-count" id="cartCount">0</span>
                </a>
                
                <!-- Mobile Menu Toggle -->
                <button class="nav-toggle" id="navToggle" onclick="toggleNav()">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
    </nav>

    <!-- Mobile Menu Overlay -->
    <div class="nav-overlay" id="navOverlay" onclick="toggleNav()"></div>

    <!-- ============================================
    CART SIDEBAR
    ============================================ -->
    <div class="cart-sidebar" id="cartSidebar">
        <div class="cart-header">
            <h3><i class="fas fa-shopping-cart"></i> Your Cart</h3>
            <button class="cart-close" onclick="closeCart()"><i class="fas fa-times"></i></button>
        </div>
        <div class="cart-items" id="cartItems">
            <div class="empty-cart">
                <i class="fas fa-shopping-basket"></i>
                <p>Your cart is empty</p>
                <a href="<?php echo SITE_URL; ?>products" class="btn-primary">Browse Products</a>
            </div>
        </div>
        <div class="cart-footer" style="display: none;">
            <div class="cart-total">
                <span>Total:</span>
                <span id="cartTotal">₹0.00</span>
            </div>
            <button class="btn-primary checkout-btn" onclick="checkout()">
                <i class="fas fa-credit-card"></i> Checkout
            </button>
        </div>
    </div>