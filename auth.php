<?php
session_start();

function checkAuth($role = null) {
    if (!isset($_SESSION['user_id'])) {
        header("Location: /SMARTKASIR/");
        exit;
    }
    
    if ($role !== null && $_SESSION['user_role'] !== $role) {
        $user_role = $_SESSION['user_role'];
        if ($user_role == 'admin') header("Location: /SMARTKASIR/admin/dashboard.php");
        elseif ($user_role == 'kasir') header("Location: /SMARTKASIR/kasir/pos.php");
        elseif ($user_role == 'owner') header("Location: /SMARTKASIR/owner/dashboard.php");
        exit;
    }
}
?>
