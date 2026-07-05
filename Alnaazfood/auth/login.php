<?php
// ============================================
// AL-NAAZ FOOD - User Login
// ============================================

require_once __DIR__ . '/../config/config.php';

// Redirect if already logged in
if (isLoggedIn()) {
    if (isOwner()) {
        redirect('admin/dashboard.php');
    } else {
        redirect('pages/index.php');
    }
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Please enter email and password';
    } else {
        // Get user
        $user = getRow("SELECT * FROM users WHERE email = ?", [$email], 's');
        
        if ($user && password_verify($password, $user['password'])) {
            // Set session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            
            // Log login
            error_log("User logged in: " . $user['email'] . " at " . date('Y-m-d H:i:s'));
            
            // Redirect based on role
            if ($user['role'] === 'owner') {
                redirect('admin/dashboard.php');
            } else {
                redirect('pages/index.php');
            }
        } else {
            $error = 'Invalid email or password';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | AL-NAAZ FOOD</title>
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
        .register-link { text-align: center; margin-top: 20px; color: #888; }
        .register-link a { color: #D4AF37; text-decoration: none; }
        .register-link a:hover { text-decoration: underline; }
        .owner-link { 
            text-align: center; 
            margin-top: 15px; 
            padding-top: 15px;
            border-top: 1px solid #333;
        }
        .owner-link a { 
            color: #8B1A1A; 
            text-decoration: none;
            font-size: 14px;
        }
        .owner-link a:hover { text-decoration: underline; }
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
            .login-container { padding: 30px 20px; margin: 20px; }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">✨ AL-NAAZ <span>FOOD</span></div>
        <div class="subtitle">Welcome back! Login to your account</div>
        
        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" required value="<?php echo $_POST['email'] ?? ''; ?>">
            </div>
            
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            
            <button type="submit" class="btn">Login</button>
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
                Login with Google
            </button>
        </a>
        
        <div class="register-link">
            Don't have an account? <a href="register.php">Register here</a>
        </div>
        
        <div class="owner-link">
            <a href="owner-login.php">🔑 Owner Login (OTP)</a>
        </div>
        
        <div style="text-align: center; margin-top: 20px;">
            <a href="<?php echo SITE_URL; ?>pages/index.php" style="color: #555; text-decoration: none; font-size: 14px;">← Back to Home</a>
        </div>
    </div>
</body>
</html>