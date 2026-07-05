<?php
// ============================================
// AL-NAAZ FOOD - User Registration
// ============================================

require_once __DIR__ . '/../config/config.php';

// Redirect if already logged in
if (isLoggedIn()) {
    redirect('pages/index.php');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validation
    if (empty($name) || empty($email) || empty($password)) {
        $error = 'Please fill all required fields';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } else {
        // Check if email already exists
        $existing = getRow("SELECT id FROM users WHERE email = ?", [$email], 's');
        if ($existing) {
            $error = 'Email already registered. Please login or use different email.';
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert user
            $sql = "INSERT INTO users (name, email, phone, password, role, is_verified) VALUES (?, ?, ?, ?, 'customer', TRUE)";
            $result = insertData($sql, [$name, $email, $phone, $hashed_password], 'ssss');
            
            if ($result) {
                $success = 'Registration successful! Please login.';
                
                // Send welcome email
                try {
                    $subject = "Welcome to AL-NAAZ FOOD!";
                    $message = "
                    <html>
                    <head><style>
                        body { font-family: Arial, sans-serif; background: #0A0A0A; color: #F5F5F5; }
                        .container { max-width: 600px; margin: 0 auto; padding: 20px; background: #1A1A1A; border-radius: 10px; border: 1px solid #D4AF37; }
                        .header { text-align: center; border-bottom: 2px solid #D4AF37; padding-bottom: 20px; }
                        .logo { font-size: 32px; color: #D4AF37; font-weight: bold; }
                        .footer { text-align: center; color: #888; font-size: 12px; margin-top: 30px; border-top: 1px solid #333; padding-top: 20px; }
                    </style></head>
                    <body>
                        <div class='container'>
                            <div class='header'><div class='logo'>✨ AL-NAAZ FOOD</div></div>
                            <h2>Welcome, $name! 👋</h2>
                            <p>Thank you for registering with AL-NAAZ FOOD.</p>
                            <p>You can now explore our premium collection of spices and food essentials.</p>
                            <p style='text-align: center;'><a href='" . SITE_URL . "auth/login.php' style='background: #D4AF37; color: #0A0A0A; padding: 10px 30px; text-decoration: none; border-radius: 5px; display: inline-block;'>Login Now</a></p>
                            <div class='footer'><p>© 2026 AL-NAAZ FOOD. All rights reserved.</p></div>
                        </div>
                    </body>
                    </html>
                    ";
                    sendEmail($email, $subject, $message);
                } catch (Exception $e) {
                    // Email error, but registration successful
                }
            } else {
                $error = 'Registration failed. Please try again.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | AL-NAAZ FOOD</title>
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
        .register-container {
            background: #1A1A1A;
            padding: 50px;
            border-radius: 15px;
            border: 1px solid #D4AF37;
            max-width: 500px;
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
        .login-link { text-align: center; margin-top: 20px; color: #888; }
        .login-link a { color: #D4AF37; text-decoration: none; }
        .login-link a:hover { text-decoration: underline; }
        .divider {
            display: flex;
            align-items: center;
            margin: 25px 0;
            color: #555;
        }
        .divider::before, .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #333;
        }
        .divider::before { margin-right: 15px; }
        .divider::after { margin-left: 15px; }
        .google-btn {
            width: 100%;
            padding: 12px;
            background: #0A0A0A;
            border: 1px solid #333;
            border-radius: 8px;
            color: #F5F5F5;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        .google-btn:hover {
            border-color: #D4AF37;
            background: #1A1A1A;
        }
        @media (max-width: 500px) {
            .register-container { padding: 30px 20px; margin: 20px; }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="logo">✨ AL-NAAZ <span>FOOD</span></div>
        <div class="subtitle">Create your account</div>
        
        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label>Full Name *</label>
                <input type="text" name="name" required value="<?php echo $_POST['name'] ?? ''; ?>">
            </div>
            
            <div class="form-group">
                <label>Email Address *</label>
                <input type="email" name="email" required value="<?php echo $_POST['email'] ?? ''; ?>">
            </div>
            
            <div class="form-group">
                <label>Phone Number</label>
                <input type="tel" name="phone" value="<?php echo $_POST['phone'] ?? ''; ?>">
            </div>
            
            <div class="form-group">
                <label>Password *</label>
                <input type="password" name="password" required>
            </div>
            
            <div class="form-group">
                <label>Confirm Password *</label>
                <input type="password" name="confirm_password" required>
            </div>
            
            <button type="submit" class="btn">Create Account</button>
        </form>
        
        <div class="divider">or continue with</div>
        
        <a href="google-auth.php" style="text-decoration: none;">
            <button class="google-btn">
                <svg width="20" height="20" viewBox="0 0 24 24">
                    <path fill="#D4AF37" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#D4AF37" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#D4AF37" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                    <path fill="#D4AF37" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                </svg>
                Sign up with Google
            </button>
        </a>
        
        <div class="login-link">
            Already have an account? <a href="login.php">Login here</a>
        </div>
        
        <div style="text-align: center; margin-top: 20px;">
            <a href="<?php echo SITE_URL; ?>pages/index.php" style="color: #555; text-decoration: none; font-size: 14px;">← Back to Home</a>
        </div>
    </div>
</body>
</html>