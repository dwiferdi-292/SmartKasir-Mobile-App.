<?php
// Deteksi otomatis: Jika dibuka di hosting InfinityFree, otomatis aktifkan mode production
if (isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], 'infinityfreeapp.com') !== false) {
    define('ENVIRONMENT', 'production');
} else {
    define('ENVIRONMENT', 'development');
}

// ==============================================================
// 1. Aktivasi HTTPS di Hosting Production
// ==============================================================
if (ENVIRONMENT === 'production') {
    if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on') {
        if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
            // Didukung reverse-proxy SSL InfinityFree
        } else {
            header("Location: https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
            exit;
        }
    }
}

// ==============================================================
// 2. Kredensial Database (Otomatis: Hosting InfinityFree vs Lokal Laragon)
// ==============================================================
if (ENVIRONMENT === 'production') {
    // -> SETELAN HOSTING INFINITYFREE ANDA <-
    $host = 'sql303.infinityfree.com';
    $port = 3306;
    $dbname = 'if0_43065153_u123_smartkasir';
    $user = 'if0_43065153';
    $pass = 'Vdhj4IptjSOCP'; // Sesuai password vPanel InfinityFree Anda
} else {
    // -> SETELAN DEFAULT LARAGON LOKAL <-
    $host = 'localhost';
    $port = 3306;
    $dbname = 'smartkasir';
    $user = 'root';
    $pass = '';
}

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8", $user, $pass);
    
    if (ENVIRONMENT === 'development') {
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } else {
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
        error_reporting(0);
        ini_set('display_errors', 0);
    }
    
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // ==============================================================
    // Inisialisasi otomatis tabel stock_logs jika belum ada di database
    // ==============================================================
    $pdo->exec("CREATE TABLE IF NOT EXISTS stock_logs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        product_id INT NOT NULL,
        type ENUM('masuk', 'keluar', 'penyesuaian') NOT NULL,
        quantity INT NOT NULL,
        previous_stock INT NOT NULL DEFAULT 0,
        current_stock INT NOT NULL DEFAULT 0,
        notes VARCHAR(255) NULL,
        user_id INT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX (product_id),
        INDEX (created_at)
    )");

    // ==============================================================
    // Inisialisasi otomatis tabel password_resets untuk fitur Lupa Password
    // ==============================================================
    $pdo->exec("CREATE TABLE IF NOT EXISTS password_resets (
        id INT AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(100) NOT NULL,
        token VARCHAR(100) NOT NULL,
        otp_code VARCHAR(10) NOT NULL,
        expires_at DATETIME NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX (email),
        INDEX (token),
        INDEX (otp_code)
    )");

} catch (PDOException $e) {
    if (ENVIRONMENT === 'development') {
        die("Koneksi Database Gagal: " . $e->getMessage());
    } else {
        die("Koneksi Database Gagal: " . $e->getMessage());
    }
}
?>
