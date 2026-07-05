<?php
// ============================================
// AL-NAAZ FOOD - Edit Settings (Admin)
// ============================================

require_once __DIR__ . '/../config/config.php';

if (!isLoggedIn() || !isOwner()) {
    redirect('auth/login.php');
}

// Redirect to edit-home
redirect('edit-home.php');
?>