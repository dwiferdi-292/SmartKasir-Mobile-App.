<?php
session_start();

function checkAuth($role = null) {
    $base = (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], '/SMARTKASIR') !== false) ? '/SMARTKASIR' : '';

    if (!isset($_SESSION['user_id'])) {
        header("Location: " . $base . "/");
        exit;
    }
    
    if ($role !== null) {
        $roles = is_array($role) ? $role : [$role];
        if (!in_array($_SESSION['user_role'], $roles)) {
            $user_role = $_SESSION['user_role'];
            if ($user_role == 'admin') header("Location: " . $base . "/admin/dashboard.php");
            elseif ($user_role == 'kasir') header("Location: " . $base . "/kasir/pos.php");
            elseif ($user_role == 'owner') header("Location: " . $base . "/owner/dashboard.php");
            exit;
        }
    }
}
?>
