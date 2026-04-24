<?php
session_start();
if (isset($_SESSION['user_id'])) {
    $user_role = $_SESSION['user_role'];
    if ($user_role == 'admin') header("Location: /SMARTKASIR/admin/dashboard.php");
    elseif ($user_role == 'kasir') header("Location: /SMARTKASIR/kasir/pos.php");
    elseif ($user_role == 'owner') header("Location: /SMARTKASIR/owner/dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartKasir - Login</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="login-body">
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
            </form>
        </div>
    </div>
    <script src="assets/js/app.js"></script>
</body>
</html>
