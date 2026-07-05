<?php
// ============================================
// AL-NAAZ FOOD - Google OAuth Callback
// ============================================

require_once __DIR__ . '/../config/config.php';

// Simplified callback handler
// For full implementation, use google-api-php-client

$code = $_GET['code'] ?? '';

if (empty($code)) {
    $_SESSION['flash_message'] = 'Google login failed';
    $_SESSION['flash_type'] = 'error';
    redirect('auth/login.php');
}

// Exchange code for access token
// This requires cURL to Google's OAuth endpoint
// For demo, showing structure:

/*
$token_url = "https://oauth2.googleapis.com/token";
$post_data = [
    'code' => $code,
    'client_id' => GOOGLE_CLIENT_ID,
    'client_secret' => GOOGLE_CLIENT_SECRET,
    'redirect_uri' => GOOGLE_REDIRECT_URI,
    'grant_type' => 'authorization_code'
];

$ch = curl_init($token_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
$response = curl_exec($ch);
curl_close($ch);

$token_data = json_decode($response, true);

if (isset($token_data['access_token'])) {
    // Get user info
    $userinfo_url = "https://www.googleapis.com/oauth2/v2/userinfo";
    $ch = curl_init($userinfo_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $token_data['access_token']]);
    $user_response = curl_exec($ch);
    curl_close($ch);
    
    $user_data = json_decode($user_response, true);
    
    // Check if user exists
    $user = getRow("SELECT * FROM users WHERE email = ? OR google_id = ?", [$user_data['email'], $user_data['id']], 'ss');
    
    if ($user) {
        // Login existing user
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];
        
        // Update google_id if not set
        if (empty($user['google_id'])) {
            updateData("UPDATE users SET google_id = ? WHERE id = ?", [$user_data['id'], $user['id']], 'si');
        }
    } else {
        // Create new user
        $sql = "INSERT INTO users (name, email, google_id, role, is_verified) VALUES (?, ?, ?, 'customer', TRUE)";
        $user_id = insertData($sql, [$user_data['name'], $user_data['email'], $user_data['id']], 'sss');
        
        $_SESSION['user_id'] = $user_id;
        $_SESSION['user_name'] = $user_data['name'];
        $_SESSION['user_email'] = $user_data['email'];
        $_SESSION['user_role'] = 'customer';
    }
    
    $redirect = $_SESSION['redirect_after_google'] ?? SITE_URL . 'pages/index.php';
    unset($_SESSION['redirect_after_google']);
    header('Location: ' . $redirect);
    exit();
} else {
    $_SESSION['flash_message'] = 'Google authentication failed';
    $_SESSION['flash_type'] = 'error';
    redirect('auth/login.php');
}
*/

// For demo, redirect to login
$_SESSION['flash_message'] = 'Google OAuth setup required. Please configure Google Client ID and Secret.';
$_SESSION['flash_type'] = 'error';
redirect('auth/login.php');
?>