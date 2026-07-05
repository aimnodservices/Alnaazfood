<?php
// ============================================
// AL-NAAZ FOOD - Edit Masala (Admin)
// ============================================

require_once __DIR__ . '/../config/config.php';

if (!isLoggedIn() || !isOwner()) {
    redirect('auth/login.php');
}

// Redirect to edit-products with category filter
redirect('edit-products.php?category=masala');
?>