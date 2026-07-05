<?php
// ============================================
// AL-NAAZ FOOD - Google OAuth Login
// ============================================

require_once __DIR__ . '/../config/config.php';

// Google OAuth 2.0 - Simplified version
// For full implementation, you need to install:
// composer require google/apiclient

// If you have google-api-php-client installed
/*
require_once __DIR__ . '/../vendor/autoload.php';

$client = new Google_Client();
$client->setClientId(GOOGLE_CLIENT_ID);
$client->setClientSecret(GOOGLE_CLIENT_SECRET);
$client->setRedirectUri(GOOGLE_REDIRECT_URI);
$client->addScope("email");
$client->addScope("profile");

// Generate auth URL
$auth_url = $client->createAuthUrl();
header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
exit();
*/

// Simplified Google Login (using Google's OAuth 2.0 without library)
// This redirects to Google's OAuth consent screen
// You need to set up OAuth 2.0 credentials in Google Cloud Console

// Store the current URL to redirect back after login
$_SESSION['redirect_after_google'] = $_SERVER['HTTP_REFERER'] ?? SITE_URL . 'pages/index.php';

// Google OAuth URL
$google_oauth_url = "https://accounts.google.com/o/oauth2/v2/auth?" . http_build_query([
    'client_id' => GOOGLE_CLIENT_ID,
    'redirect_uri' => GOOGLE_REDIRECT_URI,
    'response_type' => 'code',
    'scope' => 'email profile',
    'access_type' => 'online',
    'prompt' => 'select_account'
]);

header('Location: ' . $google_oauth_url);
exit();
?>