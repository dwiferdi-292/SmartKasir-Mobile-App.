<?php
session_start();
if (isset($_SESSION['user_id'])) {
    $base = (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], '/SMARTKASIR') !== false) ? '/SMARTKASIR' : '';
    $user_role = $_SESSION['user_role'];
    if ($user_role == 'admin') header("Location: " . $base . "/admin/dashboard.php");
    elseif ($user_role == 'kasir') header("Location: " . $base . "/kasir/pos.php");
    elseif ($user_role == 'owner') header("Location: " . $base . "/owner/dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartKasir - Login</title>
    <link rel="stylesheet" href="assets/css/style.css?v=<?= time() ?>">
</head>
<body class="login-body" style="min-height: 100vh; height: auto !important; overflow-y: auto !important; -webkit-overflow-scrolling: touch;">
    <div class="login-container">
        <div class="login-left">
            <div class="glass-panel">
                <h1>SmartKasir.</h1>
                <p style="font-size: 1.1rem; color: rgba(255,255,255,0.9); line-height: 1.6;">
                    Enterprise Point-of-Sale System.<br>
                    Scale your business effortlessly.
                </p>

            </div>
        </div>
        <div class="login-right">
            <form action="login_action.php" method="POST" class="login-form">
                <h2>Welcome Back</h2>
                <p style="color: var(--text-muted); margin-top: -20px; margin-bottom: 30px;">Sign in to your account</p>
                
                <?php if (isset($_GET['error'])): ?>
                    <div class="alert error"><i class="fa-solid fa-circle-exclamation"></i> <?php echo htmlspecialchars($_GET['error']); ?></div>
                <?php endif; ?>
                
                <?php if (isset($_GET['success'])): ?>
                    <div class="alert success"><i class="fa-solid fa-circle-check"></i> <?php echo htmlspecialchars($_GET['success']); ?></div>
                <?php endif; ?>
                
                <div class="form-group">
                    <label>Email Address</label>
                    <i class="fa-solid fa-envelope form-icon"></i>
                    <input type="email" name="email" required placeholder="name@company.com">
                </div>
                
                <div class="form-group">
                    <label>Password</label>
                    <i class="fa-solid fa-lock form-icon"></i>
                    <input type="password" name="password" required placeholder="••••••••">
                </div>
                
                <button type="submit" class="btn-primary">
                    Sign In <i class="fa-solid fa-arrow-right-to-bracket"></i>
                </button>
                
                <div style="margin-top: 20px; text-align: center; font-size: 0.9rem;">
                    <a href="forgot_password.php" style="color: var(--primary-color); text-decoration: none;">Forgot Password?</a>
                </div>
                <div style="margin-top: 15px; text-align: center; font-size: 0.9rem; color: var(--text-muted);">
                    Don't have an account? <a href="register.php" style="color: var(--primary-color); text-decoration: none; font-weight: bold;">Register Here</a>
                </div>
            </form>
        </div>
    </div>
    <script src="assets/js/app.js"></script>
</body>
</html>
