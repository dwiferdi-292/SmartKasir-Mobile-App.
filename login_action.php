<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        header("Location: index.php?error=Harap isi email dan password");
        exit;
    }

    $stmt = $pdo->prepare("SELECT id, name, role, password FROM users WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_role'] = $user['role'];

        if ($user['role'] == 'admin') {
            header("Location: admin/dashboard.php");
        } elseif ($user['role'] == 'kasir') {
            header("Location: kasir/pos.php");
        } else {
            header("Location: owner/dashboard.php");
        }
        exit;
    } else {
        header("Location: index.php?error=Email atau Password salah");
        exit;
    }
} else {
    header("Location: index.php");
    exit;
}
?>
