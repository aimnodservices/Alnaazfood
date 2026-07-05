<?php
// ============================================
// AL-NAAZ FOOD - Owner OTP Login
// ============================================

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/email_config.php';

// Redirect if already logged in as owner
if (isLoggedIn() && isOwner()) {
    redirect('admin/dashboard.php');
}

$step = isset($_SESSION['otp_step']) ? $_SESSION['otp_step'] : 1;
$email = $_SESSION['otp_email'] ?? '';
$error = '';
$success = '';

// Step 1: Send OTP
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_otp'])) {
    $email = sanitize($_POST['email'] ?? '');
    
    if (empty($email)) {
        $error = 'Please enter your email';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email';
    } else {
        // Check if this is owner email
        $user = getRow("SELECT * FROM users WHERE email = ? AND role = 'owner'", [$email], 's');
        
        if (!$user) {
            $error = 'No owner account found with this email';
        } else {
            // Generate OTP
            $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $expires_at = date('Y-m-d H:i:s', strtotime('+' . OTP_EXPIRY_MINUTES . ' minutes'));
            
            // Store OTP in database
            $sql = "INSERT INTO otp_verification (email, otp, expires_at) VALUES (?, ?, ?)";
            insertData($sql, [$email, $otp, $expires_at], 'sss');
            
            // Delete old OTPs
            $sql = "DELETE FROM otp_verification WHERE email = ? AND is_used = TRUE OR expires_at < NOW()";
            executeQuery($sql, [$email], 's');
            
            // Send OTP email
            if (sendOTPEmail($email, $otp, $user['name'])) {
                $_SESSION['otp_email'] = $email;
                $_SESSION['otp_step'] = 2;
                $success = 'OTP sent to your email. Please check your inbox.';
                header('Refresh: 2');
            } else {
                $error = 'Failed to send OTP. Please try again.';
            }
        }
    }
}

// Step 2: Verify OTP
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verify_otp'])) {
    $otp = sanitize($_POST['otp'] ?? '');
    $email = $_SESSION['otp_email'] ?? '';
    
    if (empty($otp) || empty($email)) {
        $error = 'Please enter OTP';
    } else {
        // Verify OTP
        $sql = "SELECT * FROM otp_verification WHERE email = ? AND otp = ? AND is_used = FALSE AND expires_at > NOW() ORDER BY id DESC LIMIT 1";
        $otp_record = getRow($sql, [$email, $otp], 'ss');
        
        if ($otp_record) {
            // Mark OTP as used
            $sql = "UPDATE otp_verification SET is_used = TRUE WHERE id = ?";
            executeQuery($sql, [$otp_record['id']], 'i');
            
            // Get user
            $user = getRow("SELECT * FROM users WHERE email = ? AND role = 'owner'", [$email], 's');
            
            if ($user) {
                // Set session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];
                
                // Clear OTP session
                unset($_SESSION['otp_step']);
                unset($_SESSION['otp_email']);
                
                // Log login
                error_log("Owner logged in: " . $user['email'] . " at " . date('Y-m-d H:i:s'));
                
                redirect('admin/dashboard.php');
            }
        } else {
            $error = 'Invalid or expired OTP. Please try again.';
        }
    }
}

// Clear session if back button
if (isset($_GET['clear'])) {
    unset($_SESSION['otp_step']);
    unset($_SESSION['otp_email']);
    redirect('owner-login.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Owner Login | AL-NAAZ FOOD</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Georgia', serif; 
            background: #0A0A0A; 
            color: #F5F5F5; 
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-image: radial-gradient(circle at 20% 50%, #1A0A0A 0%, #0A0A0A 100%);
        }
        .login-container {
            background: #1A1A1A;
            padding: 50px;
            border-radius: 15px;
            border: 1px solid #D4AF37;
            max-width: 450px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(212, 175, 55, 0.1);
        }
        .logo { 
            text-align: center; 
            font-size: 28px; 
            color: #D4AF37; 
            font-weight: bold;
            margin-bottom: 5px;
        }
        .logo span { color: #8B1A1A; }
        .subtitle { text-align: center; color: #888; margin-bottom: 30px; font-size: 14px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; color: #D4AF37; font-size: 14px; }
        input {
            width: 100%;
            padding: 12px 15px;
            background: #0A0A0A;
            border: 1px solid #333;
            border-radius: 8px;
            color: #F5F5F5;
            font-size: 16px;
            transition: all 0.3s;
        }
        input:focus {
            outline: none;
            border-color: #D4AF37;
            box-shadow: 0 0 20px rgba(212, 175, 55, 0.1);
        }
        .btn {
            width: 100%;
            padding: 14px;
            background: #D4AF37;
            color: #0A0A0A;
            border: none;
            border-radius: 8px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
        }
        .btn:hover {
            background: #B8960F;
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(212, 175, 55, 0.3);
        }
        .error {
            background: #8B1A1A;
            color: #F5F5F5;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }
        .success {
            background: #1A4A1A;
            color: #F5F5F5;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }
        .back-link { text-align: center; margin-top: 20px; color: #888; }
        .back-link a { color: #D4AF37; text-decoration: none; }
        .back-link a:hover { text-decoration: underline; }
        .otp-info {
            background: #0A0A0A;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            color: #888;
            font-size: 14px;
            margin-bottom: 20px;
        }
        .otp-info strong { color: #D4AF37; }
        .resend {
            text-align: center;
            margin-top: 15px;
        }
        .resend a {
            color: #D4AF37;
            text-decoration: none;
            font-size: 14px;
        }
        .resend a:hover { text-decoration: underline; }
        @media (max-width: 500px) {
            .login-container { padding: 30px 20px; margin: 20px; }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">✨ AL-NAAZ <span>FOOD</span></div>
        <div class="subtitle">👑 Owner Login (OTP Verification)</div>
        
        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if ($step == 1): ?>
            <form method="POST">
                <div class="form-group">
                    <label>Registered Owner Email</label>
                    <input type="email" name="email" placeholder="owner@alnaazfood.com" required>
                </div>
                <button type="submit" name="send_otp" class="btn">Send OTP</button>
            </form>
        <?php else: ?>
            <div class="otp-info">
                OTP sent to <strong><?php echo $email; ?></strong><br>
                Valid for <?php echo OTP_EXPIRY_MINUTES; ?> minutes
            </div>
            
            <form method="POST">
                <div class="form-group">
                    <label>Enter OTP</label>
                    <input type="text" name="otp" placeholder="Enter 6-digit OTP" maxlength="6" required>
                </div>
                <button type="submit" name="verify_otp" class="btn">Verify OTP</button>
            </form>
            
            <div class="resend">
                <a href="owner-login.php?clear=1">← Go Back</a> | 
                <a href="#" onclick="resendOTP()">Resend OTP</a>
            </div>
            
            <script>
                function resendOTP() {
                    if (confirm('Resend OTP to <?php echo $email; ?>?')) {
                        fetch('resend-otp.php', {
                            method: 'POST',
                            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                            body: 'email=<?php echo $email; ?>'
                        })
                        .then(response => response.json())
                        .then(data => {
                            alert(data.message);
                        })
                        .catch(error => {
                            alert('Error resending OTP');
                        });
                    }
                }
            </script>
        <?php endif; ?>
        
        <div class="back-link">
            <a href="login.php">← Back to Customer Login</a>
        </div>
        
        <div style="text-align: center; margin-top: 20px;">
            <a href="<?php echo SITE_URL; ?>pages/index.php" style="color: #555; text-decoration: none; font-size: 14px;">← Back to Home</a>
        </div>
    </div>
</body>
</html>