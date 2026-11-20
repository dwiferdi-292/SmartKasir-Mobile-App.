<?php
session_start();
require_once 'config.php';

// Deteksi otomatis base URL untuk link reset
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];
$base_url = (strpos($_SERVER['REQUEST_URI'], '/SMARTKASIR') !== false) ? '/SMARTKASIR' : '';
$full_base = $protocol . $host . $base_url;

$step = isset($_GET['step']) ? $_GET['step'] : 'request';
$error = '';
$success = '';
$info = '';
$simulated_email_data = null;

// Jika ada token dari URL (link email diklik)
if (isset($_GET['token']) && isset($_GET['email'])) {
    $token = trim($_GET['token']);
    $email = trim($_GET['email']);

    $stmt = $pdo->prepare("SELECT * FROM password_resets WHERE email = ? AND token = ? AND expires_at >= NOW() ORDER BY id DESC LIMIT 1");
    $stmt->execute([$email, $token]);
    $resetData = $stmt->fetch();

    if ($resetData) {
        $step = 'verify';
        $_SESSION['reset_email'] = $email;
        $_SESSION['reset_otp'] = $resetData['otp_code'];
        $info = "Tautan verifikasi valid. Silakan masukkan kata sandi baru Anda.";
    } else {
        $error = "Tautan reset kata sandi tidak valid atau telah kedaluwarsa. Silakan ajukan ulang.";
        $step = 'request';
    }
}

// -------------------------------------------------------------
// PROSES FORM POST
// -------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // =========================================================
    // TAHAP 1: PERMINTAAN KODE RESET VIA EMAIL
    // =========================================================
    if ($action === 'send_code') {
        $email = trim($_POST['email'] ?? '');

        if (empty($email)) {
            $error = "Harap masukkan alamat email Anda.";
        } else {
            // Periksa keberadaan email di tabel users
            $stmt = $pdo->prepare("SELECT id, name, role FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user) {
                // Generate OTP 6 Digit & Token Aman
                $otp_code = strval(rand(100000, 999999));
                $token = bin2hex(random_bytes(24));
                $expires_at = date('Y-m-d H:i:s', strtotime('+15 minutes'));

                // Hapus token lama untuk email ini
                $delStmt = $pdo->prepare("DELETE FROM password_resets WHERE email = ?");
                $delStmt->execute([$email]);

                // Simpan token baru
                $insStmt = $pdo->prepare("INSERT INTO password_resets (email, token, otp_code, expires_at) VALUES (?, ?, ?, ?)");
                $insStmt->execute([$email, $token, $otp_code, $expires_at]);

                // Susun Konten Email HTML
                $reset_link = $full_base . "/forgot_password.php?token=" . urlencode($token) . "&email=" . urlencode($email);
                
                $subject = "Kode Verifikasi Reset Password - SmartKasir";
                $headers = "MIME-Version: 1.0\r\n";
                $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
                $headers .= "From: SmartKasir System <no-reply@" . parse_url($full_base, PHP_URL_HOST) . ">\r\n";
                
                $mail_body = "
                <html>
                <body style='font-family: Arial, sans-serif; background-color: #f4f6f9; padding: 20px; color: #333;'>
                    <div style='max-width: 500px; margin: auto; background: #ffffff; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.08);'>
                        <div style='background: linear-gradient(135deg, #4f46e5, #7c3aed); padding: 25px; text-align: center; color: white;'>
                            <h2 style='margin: 0; font-size: 22px;'>SmartKasir POS</h2>
                            <p style='margin: 5px 0 0; opacity: 0.85;'>Permintaan Atur Ulang Kata Sandi</p>
                        </div>
                        <div style='padding: 25px;'>
                            <p>Halo <strong>" . htmlspecialchars($user['name']) . "</strong>,</p>
                            <p>Kami menerima permintaan untuk mengatur ulang kata sandi akun SmartKasir Anda. Gunakan kode verifikasi berikut:</p>
                            <div style='text-align: center; margin: 25px 0;'>
                                <span style='display: inline-block; font-size: 32px; font-weight: bold; letter-spacing: 6px; color: #4f46e5; background: #eef2ff; padding: 12px 24px; border-radius: 8px; border: 1px dashed #6366f1;'>
                                    {$otp_code}
                                </span>
                            </div>
                            <p style='text-align: center; font-size: 13px; color: #6b7280;'>Kode berlaku selama <strong>15 menit</strong>.</p>
                            <div style='text-align: center; margin-top: 20px;'>
                                <a href='{$reset_link}' style='display: inline-block; background: #4f46e5; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: bold;'>Atur Ulang Sandi Otomatis</a>
                            </div>
                            <hr style='border: none; border-top: 1px solid #e5e7eb; margin: 25px 0;'>
                            <p style='font-size: 12px; color: #9ca3af;'>Jika Anda tidak merasa meminta reset kata sandi, abaikan email ini. Akun Anda tetap aman.</p>
                        </div>
                    </div>
                </body>
                </html>";

                // Kirim email nyata (akan bekerja jika mail server lokal / hosting aktif)
                @mail($email, $subject, $mail_body, $headers);

                // Simpan data ke session & sediakan simulasi preview untuk pengujian praktikum
                $_SESSION['reset_email'] = $email;
                $_SESSION['reset_otp'] = $otp_code;
                $_SESSION['reset_token'] = $token;
                
                $simulated_email_data = [
                    'email' => $email,
                    'otp' => $otp_code,
                    'link' => $reset_link,
                    'user' => $user['name'] . ' (' . ucfirst($user['role']) . ')'
                ];

                $step = 'verify';
                $success = "Kode verifikasi 6 digit telah dibuat dan dikirim ke alamat email Anda.";
            } else {
                $error = "Alamat email tidak ditemukan dalam sistem SmartKasir.";
            }
        }
    }

    // =========================================================
    // TAHAP 2: VERIFIKASI KODE OTP & GANTI PASSWORD BARU
    // =========================================================
    elseif ($action === 'verify_reset') {
        $email = trim($_POST['email'] ?? ($_SESSION['reset_email'] ?? ''));
        $otp_input = trim($_POST['otp_code'] ?? '');
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        if (empty($email) || empty($otp_input) || empty($new_password)) {
            $error = "Semua kolom wajib diisi!";
            $step = 'verify';
        } elseif ($new_password !== $confirm_password) {
            $error = "Konfirmasi kata sandi baru tidak cocok!";
            $step = 'verify';
        } elseif (strlen($new_password) < 4) {
            $error = "Kata sandi baru minimal terdiri dari 4 karakter.";
            $step = 'verify';
        } else {
            // Validasi OTP di database
            $stmt = $pdo->prepare("SELECT * FROM password_resets WHERE email = ? AND otp_code = ? AND expires_at >= NOW() ORDER BY id DESC LIMIT 1");
            $stmt->execute([$email, $otp_input]);
            $resetRecord = $stmt->fetch();

            if ($resetRecord) {
                // Update password baru di database
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $updateStmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
                $updateStmt->execute([$hashed_password, $email]);

                // Hapus token reset yang telah digunakan
                $delStmt = $pdo->prepare("DELETE FROM password_resets WHERE email = ?");
                $delStmt->execute([$email]);

                // Bersihkan session reset
                unset($_SESSION['reset_email'], $_SESSION['reset_otp'], $_SESSION['reset_token']);

                $step = 'completed';
                $success = "Kata sandi Anda berhasil diperbarui! Silakan login dengan kata sandi baru.";
            } else {
                $error = "Kode verifikasi (OTP) tidak valid atau telah kedaluwarsa. Silakan periksa kembali.";
                $step = 'verify';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Kata Sandi - SmartKasir</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .login-body {
            height: auto !important;
            min-height: 100vh;
            overflow-y: auto !important;
            -webkit-overflow-scrolling: touch;
        }
        .login-container {
            min-height: 100vh;
            height: auto;
        }
        .demo-sim-box {
            background: #eff6ff;
            border: 1.5px dashed #3b82f6;
            border-radius: 10px;
            padding: 14px 16px;
            margin-bottom: 20px;
            font-size: 0.9rem;
            color: #1e3a8a;
        }
        .otp-display {
            display: inline-block;
            font-size: 1.6rem;
            font-weight: 800;
            letter-spacing: 5px;
            color: #1d4ed8;
            background: #ffffff;
            padding: 6px 14px;
            border-radius: 6px;
            border: 1px solid #bfdbfe;
            margin: 6px 0;
        }
        .btn-fill-otp {
            display: inline-block;
            background: #2563eb;
            color: white;
            font-size: 0.8rem;
            padding: 5px 12px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            margin-left: 8px;
            cursor: pointer;
            border: none;
        }
        .btn-fill-otp:hover {
            background: #1d4ed8;
        }
    </style>
</head>
<body class="login-body">
    <div class="login-container">
        <!-- Panel Kiri Branding -->
        <div class="login-left" style="background: linear-gradient(135deg, #4f46e5, #9333ea);">
            <div class="glass-panel">
                <h1>SmartKasir.</h1>
                <p style="font-size: 1.1rem; color: rgba(255,255,255,0.9); line-height: 1.6;">
                    Pemulihan Kata Sandi Akun Kasir &amp; POS.<br>
                    Cepat, aman, dan terintegrasi via email.
                </p>
                <div style="margin-top: 30px; font-size: 0.85rem; color: rgba(255,255,255,0.8);">
                    <i class="fa-solid fa-shield-halved"></i> Dilindungi dengan verifikasi kode OTP 6-digit &amp; enkripsi keamanan tinggi.
                </div>
            </div>
        </div>

        <!-- Panel Kanan Form -->
        <div class="login-right">
            <div class="login-form">

                <!-- NOTIFIKASI ERROR -->
                <?php if (!empty($error)): ?>
                    <div class="alert error" style="margin-bottom: 20px;">
                        <i class="fa-solid fa-triangle-exclamation"></i> <?= htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <!-- NOTIFIKASI INFO -->
                <?php if (!empty($info)): ?>
                    <div class="alert" style="background: #e0f2fe; color: #0369a1; border-left: 4px solid #0284c7; padding: 12px 15px; border-radius: 6px; margin-bottom: 20px;">
                        <i class="fa-solid fa-circle-info"></i> <?= htmlspecialchars($info); ?>
                    </div>
                <?php endif; ?>

                <!-- ============================================================== -->
                <!-- TAHAP 1: FORM INPUT EMAIL UNTUK MINTA KODE                     -->
                <!-- ============================================================== -->
                <?php if ($step === 'request'): ?>
                    <h2>Lupa Kata Sandi?</h2>
                    <p style="color: var(--text-muted); margin-top: -15px; margin-bottom: 25px;">
                        Masukkan alamat email yang terdaftar untuk menerima kode verifikasi OTP.
                    <form action="forgot_password.php" method="POST">
                        <input type="hidden" name="action" value="send_code">

                        <div class="form-group">
                            <label>Alamat Email Terdaftar</label>
                            <i class="fa-solid fa-envelope form-icon"></i>
                            <input type="email" id="email_input" name="email" required placeholder="nama@smartkasir.com" value="<?= htmlspecialchars($_SESSION['reset_email'] ?? '') ?>">
                        </div>

                        <button type="submit" class="btn-primary" style="margin-top: 10px; background: #4f46e5;">
                            Kirim Kode Verifikasi ke Email <i class="fa-solid fa-paper-plane"></i>
                        </button>
                    </form>

                <!-- ============================================================== -->
                <!-- TAHAP 2: VERIFIKASI KODE OTP & INPUT PASSWORD BARU            -->
                <!-- ============================================================== -->
                <?php elseif ($step === 'verify'): ?>
                    <h2>Verifikasi &amp; Sandi Baru</h2>
                    <p style="color: var(--text-muted); margin-top: -15px; margin-bottom: 20px;">
                        Masukkan 6 digit kode OTP dan buat kata sandi baru Anda.
                    </p>

                    <!-- KOTAK SIMULASI EMAIL (Sangat berguna untuk demo di localhost / InfinityFree) -->
                    <?php if (isset($_SESSION['reset_otp']) || isset($simulated_email_data)): ?>
                        <?php 
                            $active_otp = $_SESSION['reset_otp'] ?? $simulated_email_data['otp']; 
                            $active_email = $_SESSION['reset_email'] ?? $simulated_email_data['email'];
                        ?>
                        <div class="demo-sim-box">
                            <div style="font-weight: bold; margin-bottom: 4px;">
                                <i class="fa-solid fa-envelope-circle-check"></i> Email Terkirim ke: <u><?= htmlspecialchars($active_email); ?></u>
                            </div>
                            <div style="font-size: 0.85rem; color: #475569; margin-bottom: 6px;">
                                Di lingkungan pengujian praktikum, Anda dapat melihat kode OTP secara langsung:
                            </div>
                            <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap;">
                                <div>
                                    <span style="font-size: 0.8rem; color: #64748b;">Kode OTP Anda:</span><br>
                                    <span class="otp-display" id="otp_val"><?= htmlspecialchars($active_otp); ?></span>
                                </div>
                                <button type="button" onclick="autoFillOtp('<?= htmlspecialchars($active_otp); ?>')" class="btn-fill-otp">
                                    <i class="fa-solid fa-paste"></i> Masukkan Otomatis
                                </button>
                            </div>
                        </div>
                    <?php endif; ?>

                    <form action="forgot_password.php" method="POST">
                        <input type="hidden" name="action" value="verify_reset">
                        <input type="hidden" name="email" value="<?= htmlspecialchars($_SESSION['reset_email'] ?? '') ?>">

                        <div class="form-group">
                            <label>Kode Verifikasi (OTP 6 Digit)</label>
                            <i class="fa-solid fa-shield-halved form-icon"></i>
                            <input type="text" id="otp_input" name="otp_code" required maxlength="6" placeholder="Contoh: 123456" style="letter-spacing: 4px; font-weight: bold; font-size: 1.1rem;">
                        </div>

                        <div class="form-group">
                            <label>Kata Sandi Baru</label>
                            <i class="fa-solid fa-lock form-icon"></i>
                            <input type="password" name="new_password" required placeholder="Minimal 4 karakter" minlength="4">
                        </div>

                        <div class="form-group">
                            <label>Konfirmasi Kata Sandi Baru</label>
                            <i class="fa-solid fa-check-double form-icon"></i>
                            <input type="password" name="confirm_password" required placeholder="Ulangi kata sandi baru">
                        </div>

                        <button type="submit" class="btn-primary" style="margin-top: 10px; background: #059669;">
                            Simpan Kata Sandi Baru <i class="fa-solid fa-check"></i>
                        </button>
                    </form>

                    <div style="margin-top: 15px; text-align: center;">
                        <a href="forgot_password.php?step=request" style="font-size: 0.85rem; color: #6b7280; text-decoration: none;">
                            <i class="fa-solid fa-arrow-rotate-left"></i> Salah email? Kirim ulang ke email lain
                        </a>
                    </div>

                <!-- ============================================================== -->
                <!-- TAHAP 3: SUKSES RESET PASSWORD                                 -->
                <!-- ============================================================== -->
                <?php elseif ($step === 'completed'): ?>
                    <div style="text-align: center; padding: 20px 0;">
                        <div style="width: 70px; height: 70px; background: #dcfce7; color: #16a34a; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2rem; margin: 0 auto 20px;">
                            <i class="fa-solid fa-circle-check"></i>
                        </div>
                        <h2 style="color: #0f172a; margin-bottom: 10px;">Sandi Berhasil Diatur Ulang!</h2>
                        <p style="color: #64748b; line-height: 1.6; margin-bottom: 25px;">
                            Kata sandi akun Anda telah diperbarui. Anda sekarang dapat masuk kembali ke SmartKasir dengan kredensial baru.
                        </p>
                        <a href="index.php" class="btn-primary" style="display: block; text-align: center; text-decoration: none; background: #4f46e5; padding: 12px;">
                            Masuk ke Halaman Login <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>
                <?php endif; ?>

                <div style="margin-top: 25px; text-align: center; font-size: 0.9rem;">
                    <a href="index.php" style="color: var(--text-muted); text-decoration: none;">
                        <i class="fa-solid fa-arrow-left"></i> Kembali ke Halaman Login
                    </a>
                </div>

            </div>
        </div>
    </div>

    <script>
        function autoFillOtp(otp) {
            const otpInput = document.getElementById('otp_input');
            if (otpInput) {
                otpInput.value = otp;
                otpInput.focus();
            }
        }
    </script>
</body>
</html>
