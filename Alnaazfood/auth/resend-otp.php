<?php
// ============================================
// AL-NAAZ FOOD - Resend OTP API
// ============================================

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/email_config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email'] ?? '');
    
    if (empty($email)) {
        echo json_encode(['success' => false, 'message' => 'Email is required']);
        exit;
    }
    
    // Generate new OTP
    $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    $expires_at = date('Y-m-d H:i:s', strtotime('+' . OTP_EXPIRY_MINUTES . ' minutes'));
    
    // Store OTP
    $sql = "INSERT INTO otp_verification (email, otp, expires_at) VALUES (?, ?, ?)";
    insertData($sql, [$email, $otp, $expires_at], 'sss');
    
    // Delete old OTPs
    $sql = "DELETE FROM otp_verification WHERE email = ? AND is_used = TRUE OR expires_at < NOW()";
    executeQuery($sql, [$email], 's');
    
    // Send OTP
    if (sendOTPEmail($email, $otp)) {
        echo json_encode(['success' => true, 'message' => 'OTP resent successfully. Check your email.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to send OTP']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>