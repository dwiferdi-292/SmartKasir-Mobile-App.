<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role']; // 'kasir' or 'owner'

    // Check if email already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        $error = "Email sudah terdaftar. Silakan gunakan email lain atau login.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $email, $password, $role]);
            
            // Redirect to login with success message
            header("Location: index.php?success=" . urlencode("Registrasi berhasil! Silakan login dengan akun baru Anda."));
            exit;
        } catch(PDOException $e) {
            $error = "Terjadi kesalahan sistem saat mendaftar.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - SmartKasir</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="login-body">
    <div class="login-container">
        <div class="login-left">
            <div class="glass-panel">
                <h1>Bergabunglah.</h1>
                <p style="font-size: 1.1rem; color: rgba(255,255,255,0.9); line-height: 1.6;">
                    Buat akun baru untuk mulai menggunakan<br>
                    Sistem Point-of-Sale terbaik.
                </p>
            </div>
        </div>
        <div class="login-right">
            <form action="register.php" method="POST" class="login-form">
                <h2>Buat Akun Baru</h2>
                <p style="color: var(--text-muted); margin-top: -20px; margin-bottom: 30px;">Daftarkan bisnis atau diri Anda</p>
                
                <?php if (isset($error)): ?>
                    <div class="alert error"><i class="fa-solid fa-circle-exclamation"></i> <?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                
                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <i class="fa-solid fa-user form-icon"></i>
                    <input type="text" name="name" required placeholder="Nama Anda">
                </div>

                <div class="form-group">
                    <label>Email Address</label>
                    <i class="fa-solid fa-envelope form-icon"></i>
                    <input type="email" name="email" required placeholder="name@company.com">
                </div>
                
                <div class="form-group">
                    <label>Password</label>
                    <i class="fa-solid fa-lock form-icon"></i>
                    <input type="password" name="password" required placeholder="••••••••" minlength="4">
                </div>

                <div class="form-group no-icon">
                    <label>Pilih Role Akun</label>
                    <select name="role" required style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid var(--border-color);">
                        <option value="kasir">Kasir (Operasional)</option>
                        <option value="owner">Owner (Pemilik Bisnis)</option>
                    </select>
                </div>
                
                <button type="submit" class="btn-primary" style="margin-top: 10px;">
                    Daftar Sekarang <i class="fa-solid fa-user-plus"></i>
                </button>
                
                <div style="margin-top: 20px; text-align: center; font-size: 0.9rem; color: var(--text-muted);">
                    Sudah punya akun? <a href="index.php" style="color: var(--primary-color); text-decoration: none; font-weight: bold;">Login di sini</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
