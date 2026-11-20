<?php
require_once '../auth.php';
checkAuth('kasir');
require_once '../config.php';

// Handle Action Restok atau Penyesuaian Stok
$successMsg = '';
$errorMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'restock') {
        $productId = (int)$_POST['product_id'];
        $qtyAdded = (int)$_POST['quantity'];
        $notes = trim($_POST['notes'] ?? 'Restok Barang Kasir');

        if ($qtyAdded <= 0) {
            $errorMsg = 'Jumlah stok yang ditambahkan harus lebih dari 0.';
        } else {
            try {
                $pdo->beginTransaction();
                // Ambil stok sebelumnya
                $stmtCheck = $pdo->prepare("SELECT quantity FROM inventory WHERE product_id = ? FOR UPDATE");
                $stmtCheck->execute([$productId]);
                $prevStock = (int)$stmtCheck->fetchColumn();
                $newStock = $prevStock + $qtyAdded;

                // Update inventory
                $stmtUpdate = $pdo->prepare("UPDATE inventory SET quantity = ? WHERE product_id = ?");
                $stmtUpdate->execute([$newStock, $productId]);

                // Catat di log
                $stmtLog = $pdo->prepare("INSERT INTO stock_logs (product_id, type, quantity, previous_stock, current_stock, notes, user_id) VALUES (?, 'masuk', ?, ?, ?, ?, ?)");
                $stmtLog->execute([$productId, $qtyAdded, $prevStock, $newStock, $notes, $_SESSION['user_id']]);

                $pdo->commit();
                header("Location: stock.php?success=" . urlencode("Stok berhasil ditambahkan (+$qtyAdded unit)"));
                exit;
            } catch (Exception $e) {
                $pdo->rollBack();
                $errorMsg = 'Terjadi kesalahan: ' . $e->getMessage();
            }
        }
    } elseif ($_POST['action'] === 'adjust') {
        $productId = (int)$_POST['product_id'];
        $adjustType = $_POST['adjust_type']; // 'tambah', 'kurang', 'set'
        $adjustVal = (int)$_POST['adjust_value'];
        $notes = trim($_POST['notes'] ?? 'Penyesuaian Fisik Stok');

        try {
            $pdo->beginTransaction();
            $stmtCheck = $pdo->prepare("SELECT quantity FROM inventory WHERE product_id = ? FOR UPDATE");
            $stmtCheck->execute([$productId]);
            $prevStock = (int)$stmtCheck->fetchColumn();

            if ($adjustType === 'tambah') {
                $newStock = $prevStock + $adjustVal;
                $changeQty = $adjustVal;
            } elseif ($adjustType === 'kurang') {
                $newStock = max(0, $prevStock - $adjustVal);
                $changeQty = -$adjustVal;
            } elseif ($adjustType === 'set') {
                $newStock = max(0, $adjustVal);
                $changeQty = $newStock - $prevStock;
            }

            // Update inventory
            $stmtUpdate = $pdo->prepare("UPDATE inventory SET quantity = ? WHERE product_id = ?");
            $stmtUpdate->execute([$newStock, $productId]);

            // Catat di log
            $stmtLog = $pdo->prepare("INSERT INTO stock_logs (product_id, type, quantity, previous_stock, current_stock, notes, user_id) VALUES (?, 'penyesuaian', ?, ?, ?, ?, ?)");
            $stmtLog->execute([$productId, $changeQty, $prevStock, $newStock, $notes, $_SESSION['user_id']]);

            $pdo->commit();
            header("Location: stock.php?success=" . urlencode("Penyesuaian stok berhasil disimpan (Stok sekarang: $newStock)"));
            exit;
        } catch (Exception $e) {
            $pdo->rollBack();
            $errorMsg = 'Gagal menyesuaikan stok: ' . $e->getMessage();
        }
    }
}

// Ambil pesan dari URL jika ada
if (isset($_GET['success'])) $successMsg = $_GET['success'];
if (isset($_GET['error'])) $errorMsg = $_GET['error'];

// Tab aktif (products / logs)
$activeTab = $_GET['tab'] ?? 'products';
$filterStatus = $_GET['status'] ?? 'all';
$search = trim($_GET['search'] ?? '');

// Ambil daftar produk dan inventaris
$sqlProducts = "SELECT p.*, i.quantity, i.min_stock 
                FROM products p 
                JOIN inventory i ON p.id = i.product_id 
                WHERE 1=1";
$paramsProd = [];

if (!empty($search)) {
    $sqlProducts .= " AND (p.name LIKE ? OR p.sku LIKE ?)";
    $paramsProd[] = "%$search%";
    $paramsProd[] = "%$search%";
}

if ($filterStatus === 'low') {
    $sqlProducts .= " AND i.quantity > 0 AND i.quantity <= i.min_stock";
} elseif ($filterStatus === 'out') {
    $sqlProducts .= " AND i.quantity <= 0";
} elseif ($filterStatus === 'safe') {
    $sqlProducts .= " AND i.quantity > i.min_stock";
}

$sqlProducts .= " ORDER BY (i.quantity <= i.min_stock) DESC, p.name ASC";
$stmtProducts = $pdo->prepare($sqlProducts);
$stmtProducts->execute($paramsProd);
$products = $stmtProducts->fetchAll();

// KPI Data
$totalSKU = (int)$pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$totalPhysicalStock = (int)$pdo->query("SELECT COALESCE(SUM(quantity), 0) FROM inventory")->fetchColumn();
$lowStockCount = (int)$pdo->query("SELECT COUNT(*) FROM inventory WHERE quantity > 0 AND quantity <= min_stock")->fetchColumn();
$outOfStockCount = (int)$pdo->query("SELECT COUNT(*) FROM inventory WHERE quantity <= 0")->fetchColumn();

// Ambil data log mutasi stok jika di tab logs
$logs = [];
if ($activeTab === 'logs') {
    $sqlLogs = "SELECT sl.*, p.name as product_name, p.sku, u.name as user_name 
                FROM stock_logs sl 
                JOIN products p ON sl.product_id = p.id 
                LEFT JOIN users u ON sl.user_id = u.id 
                ORDER BY sl.created_at DESC 
                LIMIT 100";
    $logs = $pdo->query($sqlLogs)->fetchAll();
}

// Ambil list semua produk untuk modal pilihan
$allProducts = $pdo->query("SELECT id, name, sku FROM products ORDER BY name ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Stok Kasir - SmartKasir</title>
    <link rel="stylesheet" href="../assets/css/style.css?v=<?= time() ?>">
    <style>
        .filter-chip {
            padding: 8px 16px;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 600;
            background: #f1f5f9;
            color: var(--text-muted);
            border: 1px solid var(--border-color);
            transition: all 0.2s;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .filter-chip:hover {
            border-color: var(--primary-color);
            color: var(--primary-color);
        }
        .filter-chip.active {
            background: var(--primary-light);
            color: var(--primary-color);
            border-color: var(--primary-color);
        }
    </style>
</head>
<body>
    <!-- Topbar Kasir -->
    <div class="topbar pos-topbar">
        <div class="d-flex align-center gap-3">
            <div style="font-size: 1.5rem; font-weight: 800; color: var(--primary-color); letter-spacing: -1px;">
                <i class="fa-solid fa-cube"></i> SmartKasir <span style="color:var(--text-muted); font-weight:400; font-size:1rem;" class="hide-mobile">| Kasir</span>
            </div>
        </div>

        <!-- Menu Navigasi Kasir -->
        <nav class="kasir-nav">
            <a href="pos.php" class="kasir-nav-item">
                <i class="fa-solid fa-cash-register"></i>
                <span>Kasir (POS)</span>
            </a>
            <a href="orders.php" class="kasir-nav-item">
                <i class="fa-solid fa-clock-rotate-left"></i>
                <span>Riwayat Pemesanan</span>
            </a>
            <a href="stock.php" class="kasir-nav-item active">
                <i class="fa-solid fa-boxes-stacked"></i>
                <span>Manajemen Stok</span>
                <?php if (($lowStockCount + $outOfStockCount) > 0): ?>
                    <span class="nav-badge" title="<?= $lowStockCount + $outOfStockCount ?> produk perlu perhatian"><?= $lowStockCount + $outOfStockCount ?></span>
                <?php endif; ?>
            </a>
        </nav>

        <div class="d-flex align-center gap-3">
            <a href="../logout.php" class="btn-logout"><i class="fa-solid fa-power-off"></i> Keluar</a>
            <div class="user-info">
                <img src="https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['user_name']) ?>&background=10b981&color=fff" alt="Avatar">
                <?= htmlspecialchars($_SESSION['user_name']) ?> (Kasir)
            </div>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="content-area" style="max-width: 1400px; margin: 0 auto; padding: 30px 20px;">
        <!-- Header Page -->
        <div class="d-flex justify-between align-center mb-4" style="flex-wrap: wrap; gap: 15px;">
            <div>
                <h1 style="font-size: 1.8rem; font-weight: 800; color: var(--text-dark); margin-bottom: 5px;">
                    <i class="fa-solid fa-boxes-stacked" style="color: var(--primary-color);"></i> Manajemen & Kontrol Stok
                </h1>
                <p style="color: var(--text-muted); font-size: 0.95rem;">
                    Pantau ketersediaan barang real-time, lakukan restok cepat, atau sesuaikan fisik stok (stok opname).
                </p>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn-primary" onclick="openRestockModal()" style="padding: 12px 20px; width: auto; background: var(--success);">
                    <i class="fa-solid fa-truck-ramp-box"></i> Restok Cepat
                </button>
                <button type="button" class="btn-primary" onclick="openAdjustModal()" style="padding: 12px 20px; width: auto; background: var(--primary-color);">
                    <i class="fa-solid fa-sliders"></i> Penyesuaian Stok
                </button>
            </div>
        </div>

        <?php if ($successMsg): ?>
            <div class="alert success"><i class="fa-solid fa-check-circle"></i> <?= htmlspecialchars($successMsg) ?></div>
        <?php endif; ?>
        <?php if ($errorMsg): ?>
            <div class="alert error"><i class="fa-solid fa-triangle-exclamation"></i> <?= htmlspecialchars($errorMsg) ?></div>
        <?php endif; ?>

        <!-- KPI Cards -->
        <div class="kpi-grid">
            <div class="kpi-card">
                <i class="fa-solid fa-tags bg-icon"></i>
                <div class="kpi-title"><i class="fa-solid fa-barcode"></i> Total Ragam SKU</div>
                <div class="kpi-value"><?= $totalSKU ?> Item</div>
            </div>
            <div class="kpi-card success">
                <i class="fa-solid fa-cubes bg-icon"></i>
                <div class="kpi-title"><i class="fa-solid fa-warehouse"></i> Total Unit Fisik</div>
                <div class="kpi-value"><?= number_format($totalPhysicalStock, 0, ',', '.') ?> <span style="font-size: 1rem; font-weight: 500; color: var(--text-muted);">Pcs</span></div>
            </div>
            <div class="kpi-card warning">
                <i class="fa-solid fa-triangle-exclamation bg-icon"></i>
                <div class="kpi-title"><i class="fa-solid fa-bell"></i> Stok Kritis / Menipis</div>
                <div class="kpi-value"><?= $lowStockCount ?> Item</div>
            </div>
            <div class="kpi-card" style="border-bottom-color: var(--danger);">
                <i class="fa-solid fa-circle-xmark bg-icon"></i>
                <div class="kpi-title" style="color: var(--danger);"><i class="fa-solid fa-ban"></i> Stok Habis (Kosong)</div>
                <div class="kpi-value" style="color: var(--danger);"><?= $outOfStockCount ?> Item</div>
            </div>
        </div>

        <!-- Navigation Tabs (Status Stok vs Riwayat Mutasi) -->
        <div class="tab-group">
            <a href="stock.php?tab=products" class="tab-btn <?= $activeTab === 'products' ? 'active' : '' ?>">
                <i class="fa-solid fa-list-check"></i> Status & Persediaan Produk
            </a>
            <a href="stock.php?tab=logs" class="tab-btn <?= $activeTab === 'logs' ? 'active' : '' ?>">
                <i class="fa-solid fa-clock-rotate-left"></i> Riwayat Mutasi Stok (Kartu Stok)
            </a>
        </div>

        <?php if ($activeTab === 'products'): ?>
            <!-- Filter & Pencarian Bar -->
            <div class="card mb-4" style="padding: 20px;">
                <form action="stock.php" method="GET" class="d-flex align-center gap-3" style="flex-wrap: wrap;">
                    <input type="hidden" name="tab" value="products">

                    <div class="d-flex align-center gap-2" style="flex-wrap: wrap;">
                        <a href="stock.php?tab=products&status=all" class="filter-chip <?= $filterStatus === 'all' ? 'active' : '' ?>">
                            <i class="fa-solid fa-layer-group"></i> Semua Produk (<?= $totalSKU ?>)
                        </a>
                        <a href="stock.php?tab=products&status=safe" class="filter-chip <?= $filterStatus === 'safe' ? 'active' : '' ?>">
                            <i class="fa-solid fa-circle-check" style="color: var(--success);"></i> Stok Aman
                        </a>
                        <a href="stock.php?tab=products&status=low" class="filter-chip <?= $filterStatus === 'low' ? 'active' : '' ?>">
                            <i class="fa-solid fa-triangle-exclamation" style="color: var(--warning);"></i> Menipis (<?= $lowStockCount ?>)
                        </a>
                        <a href="stock.php?tab=products&status=out" class="filter-chip <?= $filterStatus === 'out' ? 'active' : '' ?>">
                            <i class="fa-solid fa-circle-xmark" style="color: var(--danger);"></i> Habis (<?= $outOfStockCount ?>)
                        </a>
                    </div>

                    <div style="margin-left: auto; display: flex; align-items: center; gap: 8px;">
                        <input type="hidden" name="status" value="<?= htmlspecialchars($filterStatus) ?>">
                        <div style="position: relative;">
                            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Cari nama / SKU produk..." class="search-bar" style="padding: 10px 14px 10px 36px; font-size: 0.9rem; width: 240px; border: 1px solid var(--border-color);">
                            <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 0.85rem;"></i>
                        </div>
                        <button type="submit" class="btn-primary" style="padding: 10px 18px; width: auto; font-size: 0.9rem;">
                            <i class="fa-solid fa-search"></i> Cari
                        </button>
                        <?php if (!empty($search)): ?>
                            <a href="stock.php?tab=products&status=<?= $filterStatus ?>" class="btn-danger" style="padding: 10px 14px; text-decoration: none; font-size: 0.9rem;" title="Hapus pencarian">
                                <i class="fa-solid fa-xmark"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <!-- Tabel Produk & Stok -->
            <div class="card">
                <div class="d-flex justify-between align-center mb-4">
                    <h3 style="font-size: 1.15rem; font-weight: 700; color: var(--text-dark);">
                        <i class="fa-solid fa-cubes-stacked" style="color: var(--primary-color);"></i> Daftar Inventaris Kasir
                    </h3>
                    <span class="badge badge-primary">Menampilkan: <?= count($products) ?> Produk</span>
                </div>

                <div class="table-wrapper">
                    <table class="table-modern">
                        <thead>
                            <tr>
                                <th>SKU</th>
                                <th>Nama Produk</th>
                                <th>Harga Jual</th>
                                <th style="text-align: center;">Batas Minimum</th>
                                <th style="text-align: center;">Stok Saat Ini</th>
                                <th>Status Stok</th>
                                <th style="text-align: center;">Aksi Operasional</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($products)): ?>
                                <tr>
                                    <td colspan="7" style="text-align: center; padding: 45px 20px; color: var(--text-muted);">
                                        <i class="fa-solid fa-box-open" style="font-size: 2.8rem; opacity: 0.4; margin-bottom: 12px; display: block;"></i>
                                        <div style="font-size: 1rem; font-weight: 600; color: var(--text-dark);">Produk Tidak Ditemukan</div>
                                        <p style="font-size: 0.85rem; margin-top: 4px;">Coba gunakan kata kunci pencarian lain atau ubah filter status.</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($products as $p): 
                                    $isOut = $p['quantity'] <= 0;
                                    $isLow = !$isOut && ($p['quantity'] <= $p['min_stock']);
                                    $isSafe = $p['quantity'] > $p['min_stock'];
                                ?>
                                    <tr>
                                        <td>
                                            <span class="badge badge-primary"><?= htmlspecialchars($p['sku']) ?></span>
                                        </td>
                                        <td>
                                            <div style="font-weight: 700; color: var(--text-dark);"><?= htmlspecialchars($p['name']) ?></div>
                                            <div style="font-size: 0.8rem; color: var(--text-muted);">Modal: Rp <?= number_format($p['cost'], 0, ',', '.') ?></div>
                                        </td>
                                        <td style="font-weight: 600; color: var(--text-dark);">
                                            Rp <?= number_format($p['price'], 0, ',', '.') ?>
                                        </td>
                                        <td style="text-align: center; color: var(--text-muted); font-weight: 600;">
                                            <?= $p['min_stock'] ?> Unit
                                        </td>
                                        <td style="text-align: center;">
                                            <span style="font-size: 1.2rem; font-weight: 800; color: <?= $isOut ? 'var(--danger)' : ($isLow ? 'var(--warning)' : 'var(--success)') ?>;">
                                                <?= $p['quantity'] ?>
                                            </span>
                                            <span style="font-size: 0.8rem; color: var(--text-muted);">Pcs</span>
                                        </td>
                                        <td>
                                            <?php if ($isOut): ?>
                                                <span class="badge badge-danger"><i class="fa-solid fa-circle-xmark"></i> Stok Habis</span>
                                            <?php elseif ($isLow): ?>
                                                <span class="badge badge-warning"><i class="fa-solid fa-triangle-exclamation"></i> Menipis (≤ <?= $p['min_stock'] ?>)</span>
                                            <?php else: ?>
                                                <span class="badge badge-success"><i class="fa-solid fa-check"></i> Stok Aman</span>
                                            <?php endif; ?>
                                        </td>
                                        <td style="text-align: center;">
                                            <div style="display: inline-flex; gap: 8px;">
                                                <button type="button" class="btn-primary" style="padding: 7px 12px; font-size: 0.8rem; width: auto; background: var(--success);" onclick="openRestockFor(<?= $p['id'] ?>, '<?= htmlspecialchars(addslashes($p['name'])) ?>', <?= $p['quantity'] ?>)" title="Restok produk ini">
                                                    <i class="fa-solid fa-plus"></i> Tambah
                                                </button>
                                                <button type="button" class="btn-primary" style="padding: 7px 12px; font-size: 0.8rem; width: auto; background: #f1f5f9; color: var(--primary-color); border: 1px solid var(--border-color);" onclick="openAdjustFor(<?= $p['id'] ?>, '<?= htmlspecialchars(addslashes($p['name'])) ?>', <?= $p['quantity'] ?>)" title="Sesuaikan / Stok Opname">
                                                    <i class="fa-solid fa-pen-to-square"></i> Opname
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        <?php else: ?>
            <!-- Tab: Riwayat Mutasi Stok (Kartu Stok) -->
            <div class="card">
                <div class="d-flex justify-between align-center mb-4">
                    <div>
                        <h3 style="font-size: 1.15rem; font-weight: 700; color: var(--text-dark);">
                            <i class="fa-solid fa-clock-rotate-left" style="color: var(--primary-color);"></i> Log Pergerakan & Mutasi Stok (Kartu Stok)
                        </h3>
                        <p style="font-size: 0.85rem; color: var(--text-muted); margin-top: 3px;">
                            Mencatat otomatis semua stok keluar (penjualan kasir), stok masuk (restok), serta penyesuaian fisik barang.
                        </p>
                    </div>
                    <span class="badge badge-primary">100 Log Terbaru</span>
                </div>

                <div class="table-wrapper">
                    <table class="table-modern">
                        <thead>
                            <tr>
                                <th>Waktu & Tanggal</th>
                                <th>Produk</th>
                                <th>Jenis Mutasi</th>
                                <th style="text-align: center;">Perubahan Qty</th>
                                <th style="text-align: center;">Stok (Sebelum &rarr; Sesudah)</th>
                                <th>Keterangan / Alasan</th>
                                <th>Petugas</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($logs)): ?>
                                <tr>
                                    <td colspan="7" style="text-align: center; padding: 45px 20px; color: var(--text-muted);">
                                        <i class="fa-regular fa-clipboard" style="font-size: 2.8rem; opacity: 0.4; margin-bottom: 12px; display: block;"></i>
                                        <div style="font-size: 1rem; font-weight: 600; color: var(--text-dark);">Belum Ada Riwayat Mutasi Stok</div>
                                        <p style="font-size: 0.85rem; margin-top: 4px;">Log akan tercatat otomatis setiap transaksi penjualan atau restok dilakukan.</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($logs as $l): 
                                    $isMasuk = $l['type'] === 'masuk';
                                    $isKeluar = $l['type'] === 'keluar';
                                ?>
                                    <tr>
                                        <td>
                                            <div style="font-weight: 600; color: var(--text-dark);"><?= date('d M Y', strtotime($l['created_at'])) ?></div>
                                            <div style="font-size: 0.8rem; color: var(--text-muted);"><i class="fa-regular fa-clock"></i> <?= date('H:i', strtotime($l['created_at'])) ?> WIB</div>
                                        </td>
                                        <td>
                                            <div style="font-weight: 700; color: var(--text-dark);"><?= htmlspecialchars($l['product_name']) ?></div>
                                            <div style="font-size: 0.75rem; color: var(--text-muted);"><?= htmlspecialchars($l['sku']) ?></div>
                                        </td>
                                        <td>
                                            <?php if ($isMasuk): ?>
                                                <span class="badge badge-success"><i class="fa-solid fa-arrow-down"></i> Stok Masuk</span>
                                            <?php elseif ($isKeluar): ?>
                                                <span class="badge badge-danger"><i class="fa-solid fa-arrow-up"></i> Stok Keluar</span>
                                            <?php else: ?>
                                                <span class="badge badge-warning"><i class="fa-solid fa-sliders"></i> Penyesuaian</span>
                                            <?php endif; ?>
                                        </td>
                                        <td style="text-align: center;">
                                            <span style="font-weight: 800; font-size: 1.05rem; color: <?= $isMasuk ? 'var(--success)' : ($isKeluar ? 'var(--danger)' : 'var(--warning)') ?>;">
                                                <?= ($l['quantity'] > 0 && !$isKeluar ? '+' : '') . $l['quantity'] ?>
                                            </span>
                                        </td>
                                        <td style="text-align: center; font-size: 0.9rem;">
                                            <span style="color: var(--text-muted);"><?= $l['previous_stock'] ?></span>
                                            <i class="fa-solid fa-arrow-right" style="font-size: 0.75rem; margin: 0 6px; color: #94a3b8;"></i>
                                            <strong style="color: var(--text-dark);"><?= $l['current_stock'] ?></strong>
                                        </td>
                                        <td>
                                            <div style="font-size: 0.9rem; color: var(--text-dark); font-weight: 500;">
                                                <?= htmlspecialchars($l['notes'] ?: '-') ?>
                                            </div>
                                        </td>
                                        <td>
                                            <span style="font-size: 0.85rem; color: var(--text-dark); display: inline-flex; align-items: center; gap: 5px;">
                                                <i class="fa-solid fa-user-tag" style="color: #94a3b8;"></i>
                                                <?= htmlspecialchars($l['user_name'] ?: 'Sistem') ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Modal Restok Cepat -->
    <div class="modal-backdrop" id="restockModal">
        <div class="modal-card">
            <div class="modal-header">
                <h3><i class="fa-solid fa-truck-ramp-box" style="color: var(--success);"></i> Restok Cepat Barang Masuk</h3>
                <button type="button" class="modal-close" onclick="closeRestockModal()">&times;</button>
            </div>
            <form action="stock.php" method="POST">
                <input type="hidden" name="action" value="restock">
                <div class="modal-body">
                    <div class="form-group no-icon">
                        <label>Pilih Produk</label>
                        <select name="product_id" id="restockProductSelect" required>
                            <option value="">-- Pilih Produk yang Diisi Stok --</option>
                            <?php foreach ($allProducts as $ap): ?>
                                <option value="<?= $ap['id'] ?>"><?= htmlspecialchars($ap['name']) ?> (<?= htmlspecialchars($ap['sku']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group no-icon">
                        <label>Jumlah Tambahan Stok (Unit/Pcs)</label>
                        <input type="number" name="quantity" min="1" placeholder="Misal: 50" required>
                    </div>

                    <div class="form-group no-icon">
                        <label>Catatan / Sumber Restok (Opsional)</label>
                        <input type="text" name="notes" placeholder="Contoh: Kiriman Gudang Utama / Supplier PT XYZ">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-primary" style="width: auto; background: #e2e8f0; color: var(--text-dark);" onclick="closeRestockModal()">Batal</button>
                    <button type="submit" class="btn-primary" style="width: auto; background: var(--success);">
                        <i class="fa-solid fa-check"></i> Simpan Stok Masuk
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Penyesuaian Stok (Stok Opname) -->
    <div class="modal-backdrop" id="adjustModal">
        <div class="modal-card">
            <div class="modal-header">
                <h3><i class="fa-solid fa-sliders" style="color: var(--primary-color);"></i> Penyesuaian Stok (Stok Opname)</h3>
                <button type="button" class="modal-close" onclick="closeAdjustModal()">&times;</button>
            </div>
            <form action="stock.php" method="POST">
                <input type="hidden" name="action" value="adjust">
                <div class="modal-body">
                    <div class="form-group no-icon">
                        <label>Pilih Produk</label>
                        <select name="product_id" id="adjustProductSelect" required>
                            <option value="">-- Pilih Produk --</option>
                            <?php foreach ($allProducts as $ap): ?>
                                <option value="<?= $ap['id'] ?>"><?= htmlspecialchars($ap['name']) ?> (<?= htmlspecialchars($ap['sku']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group no-icon">
                        <label>Metode Penyesuaian</label>
                        <select name="adjust_type" id="adjustTypeSelect" required onchange="updateAdjustHelper()">
                            <option value="set">Tetapkan Stok Fisik Riil Sekarang (Stok Opname)</option>
                            <option value="kurang">Kurangi Stok (Barang Rusak / Expired / Hilang)</option>
                            <option value="tambah">Tambah Stok (Koreksi Selisih Lebih / Retur)</option>
                        </select>
                    </div>

                    <div class="form-group no-icon">
                        <label id="adjustValueLabel">Jumlah Stok Fisik Riil (Unit)</label>
                        <input type="number" name="adjust_value" id="adjustValueInput" min="0" placeholder="0" required>
                    </div>

                    <div class="form-group no-icon">
                        <label>Alasan Penyesuaian</label>
                        <input type="text" name="notes" placeholder="Contoh: Stok opname berkala, 2 barang pecah" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-primary" style="width: auto; background: #e2e8f0; color: var(--text-dark);" onclick="closeAdjustModal()">Batal</button>
                    <button type="submit" class="btn-primary" style="width: auto;">
                        <i class="fa-solid fa-floppy-disk"></i> Simpan Penyesuaian
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openRestockModal() {
            document.getElementById('restockProductSelect').value = '';
            document.getElementById('restockModal').classList.add('active');
        }

        function openRestockFor(id, name, stock) {
            document.getElementById('restockProductSelect').value = id;
            document.getElementById('restockModal').classList.add('active');
        }

        function closeRestockModal() {
            document.getElementById('restockModal').classList.remove('active');
        }

        function openAdjustModal() {
            document.getElementById('adjustProductSelect').value = '';
            document.getElementById('adjustModal').classList.add('active');
            updateAdjustHelper();
        }

        function openAdjustFor(id, name, stock) {
            document.getElementById('adjustProductSelect').value = id;
            document.getElementById('adjustValueInput').value = stock;
            document.getElementById('adjustModal').classList.add('active');
            updateAdjustHelper();
        }

        function closeAdjustModal() {
            document.getElementById('adjustModal').classList.remove('active');
        }

        function updateAdjustHelper() {
            const type = document.getElementById('adjustTypeSelect').value;
            const label = document.getElementById('adjustValueLabel');
            if (type === 'set') {
                label.innerText = 'Jumlah Stok Fisik Riil Sekarang (Unit)';
            } else if (type === 'kurang') {
                label.innerText = 'Jumlah yang Dikurangi (Unit)';
            } else {
                label.innerText = 'Jumlah yang Ditambahkan (Unit)';
            }
        }

        // Close on escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeRestockModal();
                closeAdjustModal();
            }
        });

        // Close on clicking backdrop
        document.getElementById('restockModal').addEventListener('click', (e) => {
            if (e.target.id === 'restockModal') closeRestockModal();
        });
        document.getElementById('adjustModal').addEventListener('click', (e) => {
            if (e.target.id === 'adjustModal') closeAdjustModal();
        });
    </script>
</body>
</html>
