<?php
// ============================================
// AL-NAAZ FOOD - Main Configuration
// ============================================

// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database
require_once __DIR__ . '/database.php';

// Site Configuration
define('SITE_NAME', 'AL-NAAZ FOOD');
define('SITE_TAGLINE', 'Premium Spices & Food Essentials');
define('SITE_URL', 'http://localhost/al-naaz-food/');
define('ADMIN_EMAIL', 'owner@alnaazfood.com');

// Timezone
date_default_timezone_set('Asia/Kolkata');

// Error Reporting (Turn off in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Upload Paths
define('UPLOAD_PATH', $_SERVER['DOCUMENT_ROOT'] . '/al-naaz-food/assets/uploads/');
define('UPLOAD_URL', SITE_URL . 'assets/uploads/');

// Google OAuth Configuration
define('GOOGLE_CLIENT_ID', 'YOUR_GOOGLE_CLIENT_ID');
define('GOOGLE_CLIENT_SECRET', 'YOUR_GOOGLE_CLIENT_SECRET');
define('GOOGLE_REDIRECT_URI', SITE_URL . 'auth/google-callback.php');

// Email Configuration (SMTP)
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'your-email@gmail.com');
define('SMTP_PASSWORD', 'your-app-password');
define('SMTP_FROM_EMAIL', 'noreply@alnaazfood.com');
define('SMTP_FROM_NAME', 'AL-NAAZ FOOD');

// OTP Settings
define('OTP_EXPIRY_MINUTES', 10);
define('OTP_LENGTH', 6);

// Order Settings
define('ADVANCE_AMOUNT', 100); // ₹100 advance required

// Security
define('SALT', 'al_naaz_food_salt_2026_secure');

// Helper Functions
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isOwner() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'owner';
}

function isCustomer() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'customer';
}

function getCurrentUser() {
    if (isLoggedIn()) {
        return [
            'id' => $_SESSION['user_id'],
            'name' => $_SESSION['user_name'],
            'email' => $_SESSION['user_email'],
            'role' => $_SESSION['user_role']
        ];
    }
    return null;
}

function redirect($url) {
    header("Location: " . SITE_URL . $url);
    exit();
}

function showMessage($message, $type = 'success') {
    $_SESSION['flash_message'] = $message;
    $_SESSION['flash_type'] = $type;
}

function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        $type = $_SESSION['flash_type'] ?? 'success';
        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_type']);
        return ['message' => $message, 'type' => $type];
    }
    return null;
}

function generateOrderNumber() {
    return 'ALN-' . date('Ymd') . '-' . rand(1000, 9999);
}

function sanitize($input) {
    return htmlspecialchars(strip_tags(trim($input)));
}

// API Response function
function jsonResponse($data, $status = 200) {
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit();
}

// Check if request is AJAX
function isAjax() {
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

// Get visitor IP
function getClientIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        return $_SERVER['REMOTE_ADDR'];
    }
}

// Generate random string
function generateRandomString($length = 10) {
    return bin2hex(random_bytes($length));
}
?>