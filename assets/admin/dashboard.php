<?php
require_once '../auth.php';
checkAuth('admin');
require_once '../config.php';

// Notification: get products with low stock
$stmtLowStock = $pdo->query("SELECT p.name, i.quantity, i.min_stock FROM inventory i JOIN products p ON i.product_id = p.id WHERE i.quantity <= i.min_stock");
$lowStocks = $stmtLowStock->fetchAll();

// KPI Data
$totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalProducts = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$totalTransactions = $pdo->query("SELECT COUNT(*) FROM transactions")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - SmartKasir</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="dashboard-layout">
        <div class="sidebar">
            <div class="sidebar-header"><i class="fa-solid fa-cube"></i> SmartKasir</div>
            <ul class="sidebar-menu">
                <li><a href="dashboard.php" class="active"><i class="fa-solid fa-house"></i> Dashboard</a></li>
                <li><a href="products.php"><i class="fa-solid fa-boxes-stacked"></i> Produk & Stok</a></li>
                <li><a href="users.php"><i class="fa-solid fa-users-gear"></i> Kelola User</a></li>
            </ul>
        </div>
        <div class="main-content">
            <div class="topbar">
                <h2>DASHBOARD</h2>
                <div class="d-flex align-center gap-4">
                    <a href="../logout.php" class="badge badge-danger"><i class="fa-solid fa-power-off"></i> Logout</a>
                    <div class="user-info">
                        <img src="https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['user_name']) ?>&background=4f46e5&color=fff" alt="Avatar">
                        <?= htmlspecialchars($_SESSION['user_name']) ?>
                    </div>
                </div>
            </div>
            <div class="content-area">
                
                <?php if (count($lowStocks) > 0): ?>
                    <div class="card" style="border-left: 4px solid var(--warning); background: #fffdf4;">
                        <h3 class="d-flex align-center gap-3" style="color: var(--warning);"><i class="fa-solid fa-triangle-exclamation"></i> Peringatan Stok Habis</h3>
                        <p class="mt-4" style="color: var(--text-dark);">Beberapa produk telah mencapai batas persediaan minimum:</p>
                        <ul style="margin-top:10px; margin-left: 20px; line-height: 1.8;">
                            <?php foreach($lowStocks as $s): ?>
                                <li><strong><?= htmlspecialchars($s['name']) ?></strong> <span class="badge badge-danger" style="margin-left: 10px;">Stok: <?= $s['quantity'] ?></span> (Min: <?= $s['min_stock'] ?>)</li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <div class="kpi-grid">
                    <div class="kpi-card">
                        <i class="fa-solid fa-users bg-icon"></i>
                        <div class="kpi-title"><i class="fa-solid fa-user"></i> Total Pengguna</div>
                        <div class="kpi-value"><?= $totalUsers ?></div>
                    </div>
                    <div class="kpi-card success">
                        <i class="fa-solid fa-box-open bg-icon"></i>
                        <div class="kpi-title"><i class="fa-solid fa-tags"></i> Total Produk</div>
                        <div class="kpi-value"><?= $totalProducts ?></div>
                    </div>
                    <div class="kpi-card warning">
                        <i class="fa-solid fa-receipt bg-icon"></i>
                        <div class="kpi-title"><i class="fa-solid fa-file-invoice-dollar"></i> Transaksi Tercatat</div>
                        <div class="kpi-value"><?= $totalTransactions ?></div>
                    </div>
                </div>

                <div class="card">
                    <h3 class="mb-4">Informasi Sistem</h3>
                    <p style="color: var(--text-muted); line-height: 1.6;">
                        Selamat datang di panel administrasi. Gunakan navigasi di sebelah kiri untuk mengatur stok barang dan menambahkan staf baru.<br>
                        Sistem sedang aktif dan siap menerima data operasional toko Anda.
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
