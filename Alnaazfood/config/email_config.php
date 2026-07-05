<?php
// ============================================
// AL-NAAZ FOOD - Email Configuration
// ============================================

require_once __DIR__ . '/config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load PHPMailer (you need to install via composer or download)
// For this example, assuming PHPMailer is in vendor directory
// require_once __DIR__ . '/../vendor/autoload.php';

// Simple mail function without PHPMailer (using mail() function)
function sendEmail($to, $subject, $message, $from = null) {
    if ($from === null) {
        $from = SMTP_FROM_EMAIL;
    }
    
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: " . SMTP_FROM_NAME . " <" . $from . ">" . "\r\n";
    
    return mail($to, $subject, $message, $headers);
}

// Send OTP Email
function sendOTPEmail($email, $otp, $name = '') {
    $subject = "AL-NAAZ FOOD - OTP Verification";
    
    $message = "
    <!DOCTYPE html>
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; background: #0A0A0A; color: #F5F5F5; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; background: #1A1A1A; border-radius: 10px; border: 1px solid #D4AF37; }
            .header { text-align: center; border-bottom: 2px solid #D4AF37; padding-bottom: 20px; }
            .logo { font-size: 32px; color: #D4AF37; font-weight: bold; }
            .otp-code { font-size: 48px; color: #D4AF37; text-align: center; padding: 20px; letter-spacing: 10px; background: #0A0A0A; border-radius: 5px; margin: 20px 0; }
            .footer { text-align: center; color: #888; font-size: 12px; margin-top: 30px; border-top: 1px solid #333; padding-top: 20px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <div class='logo'>✨ AL-NAAZ FOOD</div>
                <p style='color: #D4AF37;'>Premium Spices & Food Essentials</p>
            </div>
            <h2>OTP Verification</h2>
            <p>Hello " . ($name ?: 'User') . ",</p>
            <p>Your OTP for AL-NAAZ FOOD login is:</p>
            <div class='otp-code'>" . $otp . "</div>
            <p>This OTP is valid for " . OTP_EXPIRY_MINUTES . " minutes.</p>
            <p>If you didn't request this, please ignore this email.</p>
            <div class='footer'>
                <p>© 2026 AL-NAAZ FOOD. All rights reserved.</p>
                <p>123, Food Street, Mumbai, Maharashtra, India</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    return sendEmail($email, $subject, $message);
}

// Send Order Confirmation Email
function sendOrderConfirmation($email, $orderNumber, $name = '') {
    $subject = "AL-NAAZ FOOD - Order Confirmation #" . $orderNumber;
    
    $message = "
    <!DOCTYPE html>
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; background: #0A0A0A; color: #F5F5F5; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; background: #1A1A1A; border-radius: 10px; border: 1px solid #D4AF37; }
            .header { text-align: center; border-bottom: 2px solid #D4AF37; padding-bottom: 20px; }
            .logo { font-size: 32px; color: #D4AF37; font-weight: bold; }
            .order-info { background: #0A0A0A; padding: 15px; border-radius: 5px; margin: 20px 0; }
            .footer { text-align: center; color: #888; font-size: 12px; margin-top: 30px; border-top: 1px solid #333; padding-top: 20px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <div class='logo'>✨ AL-NAAZ FOOD</div>
            </div>
            <h2>Order Confirmed! ✅</h2>
            <p>Hello " . ($name ?: 'Customer') . ",</p>
            <p>Your order has been confirmed successfully.</p>
            <div class='order-info'>
                <p><strong>Order Number:</strong> #" . $orderNumber . "</p>
                <p><strong>Status:</strong> Confirmed</p>
                <p>We will notify you when your order is shipped.</p>
            </div>
            <p>Thank you for choosing AL-NAAZ FOOD!</p>
            <div class='footer'>
                <p>© 2026 AL-NAAZ FOOD. All rights reserved.</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    return sendEmail($email, $subject, $message);
}
?>