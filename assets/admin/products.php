<?php
require_once '../auth.php';
checkAuth('admin');
require_once '../config.php';

// Handle Add / Update Product
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $sku = $_POST['sku'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $cost = $_POST['cost'];
    $qty = $_POST['quantity'];
    $min_stock = $_POST['min_stock'];

    if ($_POST['action'] == 'add') {
        try {
            $pdo->beginTransaction();
            $stmt = $pdo->prepare("INSERT INTO products (sku, name, price, cost) VALUES (?, ?, ?, ?)");
            $stmt->execute([$sku, $name, $price, $cost]);
            $productId = $pdo->lastInsertId();

            $stmtInv = $pdo->prepare("INSERT INTO inventory (product_id, quantity, min_stock) VALUES (?, ?, ?)");
            $stmtInv->execute([$productId, $qty, $min_stock]);
            
            $pdo->commit();
            header("Location: products.php?success=Produk berhasil ditambahkan");
            exit;
        } catch(PDOException $e) {
            $pdo->rollBack();
            header("Location: products.php?error=Gagal menambah produk: " . urlencode($e->getMessage()));
            exit;
        }
    } elseif ($_POST['action'] == 'delete') {
        $id = $_POST['id'];
        $pdo->prepare("DELETE FROM products WHERE id = ?")->execute([$id]);
        header("Location: products.php?success=Produk dihapus");
        exit;
    }
}

// Fetch all products with inventory
$products = $pdo->query("SELECT p.*, i.quantity, i.min_stock FROM products p LEFT JOIN inventory i ON p.id = i.product_id ORDER BY p.id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Kelola Produk - SmartKasir</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="dashboard-layout">
        <div class="sidebar">
            <div class="sidebar-header"><i class="fa-solid fa-cube"></i> SmartKasir</div>
            <ul class="sidebar-menu">
                <li><a href="dashboard.php"><i class="fa-solid fa-house"></i> Dashboard</a></li>
                <li><a href="products.php" class="active"><i class="fa-solid fa-boxes-stacked"></i> Produk & Stok</a></li>
                <li><a href="users.php"><i class="fa-solid fa-users-gear"></i> Kelola User</a></li>
            </ul>
        </div>
        <div class="main-content">
            <div class="topbar">
                <h2>PRODUK & INVENTARIS</h2>
                <div class="d-flex align-center gap-4">
                    <a href="../logout.php" class="badge badge-danger"><i class="fa-solid fa-power-off"></i> Logout</a>
                    <div class="user-info">
                        <img src="https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['user_name']) ?>&background=4f46e5&color=fff" alt="Avatar">
                        <?= htmlspecialchars($_SESSION['user_name']) ?>
                    </div>
                </div>
            </div>
            <div class="content-area">
                <?php if(isset($_GET['success'])): ?><div class="alert success"><i class="fa-solid fa-check-circle"></i> <?= htmlspecialchars($_GET['success']) ?></div><?php endif; ?>
                <?php if(isset($_GET['error'])): ?><div class="alert error"><i class="fa-solid fa-triangle-exclamation"></i> <?= htmlspecialchars($_GET['error']) ?></div><?php endif; ?>
                
                <div class="d-flex" style="gap: 30px;">
                    <div class="card" style="flex: 1; height: max-content;">
                        <h3 class="mb-4"><i class="fa-solid fa-plus-circle"></i> Tambah Produk Baru</h3>
                        <form action="products.php" method="POST">
                            <input type="hidden" name="action" value="add">
                            <div class="form-group no-icon"><label>SKU (Kode Barang)</label><input type="text" name="sku" required></div>
                            <div class="form-group no-icon"><label>Nama Produk</label><input type="text" name="name" required></div>
                            <div class="form-group no-icon"><label>Harga Jual (Rp)</label><input type="number" name="price" required></div>
                            <div class="form-group no-icon"><label>Harga Modal/HPP (Rp)</label><input type="number" name="cost" required></div>
                            <div class="d-flex gap-3">
                                <div class="form-group no-icon" style="flex:1;"><label>Stok Awal</label><input type="number" name="quantity" value="0" required></div>
                                <div class="form-group no-icon" style="flex:1;"><label>Stok Min</label><input type="number" name="min_stock" value="10" required></div>
                            </div>
                            <button type="submit" class="btn-primary"><i class="fa-solid fa-floppy-disk"></i> Simpan Produk</button>
                        </form>
                    </div>

                    <div class="card" style="flex: 2.5;">
                        <h3 class="mb-4 d-flex justify-between align-center">
                            <span><i class="fa-solid fa-list"></i> Daftar Master Produk</span>
                            <span class="badge badge-primary">Total: <?= count($products) ?> Item</span>
                        </h3>
                        <div class="table-wrapper">
                            <table class="table-modern">
                                <thead>
                                    <tr>
                                        <th>SKU</th>
                                        <th>Detail Produk</th>
                                        <th>Harga Jual</th>
                                        <th>Stok</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($products as $p): ?>
                                    <tr>
                                        <td><span class="badge badge-primary"><?= htmlspecialchars($p['sku']) ?></span></td>
                                        <td>
                                            <div style="font-weight: 600; color:var(--text-dark);"><?= htmlspecialchars($p['name']) ?></div>
                                            <div style="font-size: 0.8rem; color:var(--text-muted);">Modal: Rp <?= number_format($p['cost'], 0, ',', '.') ?></div>
                                        </td>
                                        <td style="font-weight: 600;">Rp <?= number_format($p['price'], 0, ',', '.') ?></td>
                                        <td>
                                            <?php if($p['quantity'] <= $p['min_stock']): ?>
                                                <span class="badge badge-danger"><i class="fa-solid fa-exclamation-circle"></i> <?= $p['quantity'] ?></span>
                                            <?php else: ?>
                                                <span class="badge badge-success"><i class="fa-solid fa-check"></i> <?= $p['quantity'] ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <form action="products.php" method="POST" style="display:inline;" onsubmit="return confirm('Hapus produk permanen?');">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                                <button type="submit" class="btn-danger"><i class="fa-solid fa-trash"></i> Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
