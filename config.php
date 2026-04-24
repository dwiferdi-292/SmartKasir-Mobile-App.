<?php
// Setel ke 'development' saat di Laragon/lokal.
// Ubah ke 'production' sebelum file di-upload ke Hosting.
define('ENVIRONMENT', 'development');

// ==============================================================
// 4. Aktivasi HTTPS secara paksa di Production (Keamanan Jaringan)
// ==============================================================
if (ENVIRONMENT === 'production') {
    if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on') {
        header("Location: https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
        exit;
    }
}

// ==============================================================
// 2. Kredensial Database Terpisah (Lokal vs Hosting)
// ==============================================================
if (ENVIRONMENT === 'production') {
    // -> GANTI BARIS INI DENGAN DATA DARI CPANEL HOSTING ANDA NANTI <-
    $host = 'localhost';
    $dbname = 'u1234567_smartkasir'; // Contoh nama DB di hosting
    $user = 'u1234567_admin';        // Contoh User DB di hosting
    $pass = 'P@ssw0rdKuat123!';      // Password DB di hosting
} else {
    // -> SETELAN DEFAULT LARAGON LOKAL <-
    $host = 'localhost';
    $dbname = 'smartkasir';
    $user = 'root';
    $pass = '';
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    
    // ==============================================================
    // 3. Matikan Pesan Error Database di Production (Mencegah Kebocoran)
    // ==============================================================
    if (ENVIRONMENT === 'development') {
        // Tampilkan Error PDO untuk membantu debugging di Laragon
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } else {
        // Mode diam (Silent Mode) di Production
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
        error_reporting(0);
        ini_set('display_errors', 0);
    }
    
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    if (ENVIRONMENT === 'development') {
        die("Koneksi Database Gagal: " . $e->getMessage());
    } else {
        die("Maaf, sistem sedang dalam perbaikan jaringan. Silakan ulangi beberapa saat lagi.");
    }
}
?>
