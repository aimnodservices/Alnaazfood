<?php
// ============================================
// AL-NAAZ FOOD - Logout
// ============================================

require_once __DIR__ . '/../config/config.php';

// Clear all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to home with logout message
header("Location: " . SITE_URL . "pages/index.php?logout=success");
exit();
?>