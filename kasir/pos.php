<?php
require_once '../auth.php';
checkAuth('kasir');
require_once '../config.php';

// Fetch products with stock > 0
$products = $pdo->query("SELECT p.*, i.quantity FROM products p JOIN inventory i ON p.id = i.product_id ORDER BY p.name ASC")->fetchAll();

// Jika database produk masih kosong di hosting, otomatis tanamkan 10 menu default agar tidak kosong/blank
if (empty($products)) {
    $defaultMenuItems = [
        ['SKU-001', 'Kopi Susu Gula Aren', 18000, 10000, 35],
        ['SKU-002', 'Americano (Hot/Ice)', 15000, 8000, 42],
        ['SKU-003', 'Cafe Latte', 20000, 12000, 44],
        ['SKU-004', 'Matcha Latte', 22000, 14000, 28],
        ['SKU-005', 'Chocolate Ice', 20000, 12000, 50],
        ['SKU-006', 'Lemon Tea', 12000, 5000, 30],
        ['SKU-007', 'French Fries', 15000, 8000, 40],
        ['SKU-008', 'Chicken Wings', 25000, 15000, 25],
        ['SKU-009', 'Nasi Goreng Spesial', 28000, 18000, 20],
        ['SKU-010', 'Mie Goreng Telur', 20000, 12000, 30]
    ];
    $stmtP = $pdo->prepare("INSERT INTO products (sku, name, price, cost) VALUES (?, ?, ?, ?)");
    $stmtI = $pdo->prepare("INSERT INTO inventory (product_id, quantity, min_stock) VALUES (?, ?, ?)");
    foreach ($defaultMenuItems as $item) {
        $check = $pdo->prepare("SELECT id FROM products WHERE sku = ?");
        $check->execute([$item[0]]);
        if (!$check->fetchColumn()) {
            $stmtP->execute([$item[0], $item[1], $item[2], $item[3]]);
            $pId = $pdo->lastInsertId();
            $stmtI->execute([$pId, $item[4], 10]);
        }
    }
    // Query ulang setelah ditanamkan
    $products = $pdo->query("SELECT p.*, i.quantity FROM products p JOIN inventory i ON p.id = i.product_id ORDER BY p.name ASC")->fetchAll();
}

// Badge count untuk stok kritis
$lowStockCount = (int)$pdo->query("SELECT COUNT(*) FROM inventory WHERE quantity <= min_stock")->fetchColumn();
// Helper untuk menyesuaikan gambar otomatis berdasarkan nama menu
function getProductImage($name) {
    $n = strtolower($name);

    // Kategori Minuman (Coffee & Beverages)
    if (strpos($n, 'gula aren') !== false || strpos($n, 'kopi susu') !== false) {
        return 'https://images.unsplash.com/photo-1517701550927-30cf4ba1dba5?auto=format&fit=crop&w=400&q=80'; // Es Kopi Susu
    }
    if (strpos($n, 'americano') !== false) {
        return 'https://images.unsplash.com/photo-1551030173-122aabc4489c?auto=format&fit=crop&w=400&q=80'; // Americano Segar Dingin
    }
    if (strpos($n, 'matcha') !== false) {
        return 'https://images.unsplash.com/photo-1536256263959-770b48d82b0a?auto=format&fit=crop&w=400&q=80'; // Matcha Latte
    }
    if (strpos($n, 'latte') !== false) {
        return 'https://images.unsplash.com/photo-1570968915860-54d5c301fa9f?auto=format&fit=crop&w=400&q=80'; // Cafe Latte Art
    }
    if (strpos($n, 'chocolate') !== false || strpos($n, 'cokelat') !== false) {
        return 'https://images.unsplash.com/photo-1542990253-0d0f5be5f0ed?auto=format&fit=crop&w=400&q=80'; // Chocolate Ice Dingin
    }
    if (strpos($n, 'lemon') !== false) {
        return 'https://images.unsplash.com/photo-1556679343-c7306c1976bc?auto=format&fit=crop&w=400&q=80'; // Iced Lemon Tea
    }
    if (strpos($n, 'kopi') !== false || strpos($n, 'coffee') !== false || strpos($n, 'espresso') !== false) {
        return 'https://images.unsplash.com/photo-1509042239860-f550ce710b93?auto=format&fit=crop&w=400&q=80'; // Kopi Hitam
    }
    if (strpos($n, 'teh') !== false || strpos($n, 'tea') !== false) {
        return 'https://images.unsplash.com/photo-1576092768241-dec231879fc3?auto=format&fit=crop&w=400&q=80'; // Teh
    }
    if (strpos($n, 'jus') !== false || strpos($n, 'juice') !== false) {
        return 'https://images.unsplash.com/photo-1613478223719-2ab802602423?auto=format&fit=crop&w=400&q=80'; // Aneka Jus
    }

    // Kategori Makanan & Snack
    if (strpos($n, 'french') !== false || strpos($n, 'frens') !== false || strpos($n, 'fries') !== false || strpos($n, 'kentang') !== false) {
        return 'https://images.unsplash.com/photo-1630384060421-cb20d0e0649d?auto=format&fit=crop&w=400&q=80'; // Kentang Goreng / French Fries Renyah
    }
    if (strpos($n, 'wings') !== false || strpos($n, 'sayap') !== false) {
        return 'https://images.unsplash.com/photo-1567620832903-9fc6debc209f?auto=format&fit=crop&w=400&q=80'; // Chicken Wings
    }
    if (strpos($n, 'nasi goreng') !== false || strpos($n, 'nasi') !== false) {
        return 'https://images.unsplash.com/photo-1603133872878-684f208fb84b?auto=format&fit=crop&w=400&q=80'; // Nasi Goreng Spesial Telur
    }
    if (strpos($n, 'mie goreng') !== false || strpos($n, 'mie') !== false || strpos($n, 'noodle') !== false) {
        return 'https://images.unsplash.com/photo-1612927601601-6638404737ce?auto=format&fit=crop&w=400&q=80'; // Mie Goreng Telur
    }
    if (strpos($n, 'ayam') !== false || strpos($n, 'chicken') !== false) {
        return 'https://images.unsplash.com/photo-1626082927389-6cd097cdc6ec?auto=format&fit=crop&w=400&q=80'; // Ayam Goreng Krispi
    }
    if (strpos($n, 'burger') !== false) {
        return 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?auto=format&fit=crop&w=400&q=80'; // Burger
    }
    if (strpos($n, 'pizza') !== false) {
        return 'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?auto=format&fit=crop&w=400&q=80'; // Pizza
    }
    if (strpos($n, 'donat') !== false || strpos($n, 'donut') !== false) {
        return 'https://images.unsplash.com/photo-1551024601-bec78aea704b?auto=format&fit=crop&w=400&q=80'; // Donat
    }
    if (strpos($n, 'roti') !== false || strpos($n, 'toast') !== false) {
        return 'https://images.unsplash.com/photo-1584776296944-ab6fb57b0bdd?auto=format&fit=crop&w=400&q=80'; // Roti Bakar / Toast
    }

    // Default estetik Cafe/Resto Dish
    return 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=400&q=80';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS - SmartKasir</title>
    <link rel="stylesheet" href="../assets/css/style.css?v=<?= time() ?>">
    <style>
        .pos-topbar { background: #fff; box-shadow: var(--shadow-sm); }
        @media (min-width: 769px) {
            body { overflow: hidden; }
        }
        @media (max-width: 768px) {
            body { 
                overflow-y: auto !important; 
                height: auto !important; 
                min-height: 100vh; 
                -webkit-overflow-scrolling: touch; 
            }
            .pos-layout { 
                height: auto !important; 
                overflow: visible !important; 
            }
            .pos-left { 
                height: auto !important; 
                overflow: visible !important; 
            }
            .pos-right { 
                height: auto !important; 
                overflow: visible !important; 
            }
        }
    </style>
</head>
<body>
    <div class="topbar pos-topbar">
        <div class="d-flex align-center gap-3">
            <button class="mobile-toggle"><i class="fa-solid fa-bars"></i></button>
            <div style="font-size: 1.5rem; font-weight: 800; color: var(--primary-color); letter-spacing: -1px;">
                <i class="fa-solid fa-cube"></i> SmartKasir <span style="color:var(--text-muted); font-weight:400; font-size:1rem;" class="hide-mobile">| Kasir</span>
            </div>
        </div>

        <!-- Menu Navigasi Kasir -->
        <nav class="kasir-nav">
            <a href="pos.php" class="kasir-nav-item active">
                <i class="fa-solid fa-cash-register"></i>
                <span>Kasir (POS)</span>
            </a>
            <a href="orders.php" class="kasir-nav-item">
                <i class="fa-solid fa-clock-rotate-left"></i>
                <span>Riwayat Pemesanan</span>
            </a>
            <a href="stock.php" class="kasir-nav-item">
                <i class="fa-solid fa-boxes-stacked"></i>
                <span>Manajemen Stok</span>
                <?php if ($lowStockCount > 0): ?>
                    <span class="nav-badge" title="<?= $lowStockCount ?> produk stok menipis"><?= $lowStockCount ?></span>
                <?php endif; ?>
            </a>
        </nav>

        <div class="d-flex align-center gap-3">
            <a href="../logout.php" class="btn-logout"><i class="fa-solid fa-power-off"></i> Keluar</a>
            <div class="user-info">
                <img src="https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['user_name']) ?>&background=10b981&color=fff" alt="Avatar">
                <?= htmlspecialchars($_SESSION['user_name']) ?>
            </div>
        </div>
    </div>
    
    <div class="pos-layout">
        <div class="pos-left">
            <div class="search-wrapper">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" id="searchProduct" class="search-bar" placeholder="Cari nama produk atau SKU...">
            </div>
            
            <div class="product-grid">
                <?php 
                foreach($products as $p): 
                    $imgUrl = getProductImage($p['name']);
                ?>
                    <div class="product-card <?= $p['quantity'] <= 0 ? 'disabled' : '' ?>" 
                         onclick="<?= $p['quantity'] > 0 ? "addToCart({$p['id']}, '{$p['name']}', {$p['price']}, {$p['quantity']})" : "" ?>"
                         style="padding: 0; overflow: hidden; display: flex; flex-direction: column;">
                        
                        <div style="height: 140px; width: 100%; background: url('<?= $imgUrl ?>') center/cover; position: relative;">
                            <?php if($p['quantity'] <= 0): ?>
                                <div class="stock-badge" style="top: 10px; right: 10px;"><i class="fa-solid fa-xmark"></i> Habis</div>
                            <?php else: ?>
                                <div class="stock-badge" style="background:var(--success); top: 10px; right: 10px;"><i class="fa-solid fa-layer-group"></i> Sisa <?= $p['quantity'] ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <div style="padding: 15px; display: flex; flex-direction: column; flex: 1;">
                            <h3 style="font-size: 0.95rem; line-height: 1.4; margin-bottom: auto; margin-top:0;"><?= htmlspecialchars($p['name']) ?></h3>
                            <div class="price" style="font-size: 1.1rem; margin-top: 10px;">Rp <?= number_format($p['price'], 0, ',', '.') ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <div class="pos-right">
            <div class="cart-header">
                <i class="fa-solid fa-basket-shopping" style="color: var(--primary-color);"></i> Detail Pesanan
            </div>
            <div class="cart-area" id="cartItems">
                <!-- Cart items will be rendered here by JS -->
                <div style="text-align: center; color: #94a3b8; margin-top: 80px;">
                    <i class="fa-solid fa-cart-arrow-down" style="font-size: 4rem; opacity: 0.5; margin-bottom: 20px;"></i>
                    <h3 style="font-weight: 500;">Belum ada menu yang dipilih</h3>
                    <p style="font-size: 0.9rem;">Klik kotak menu di samping untuk menambahkan ke keranjang.</p>
                </div>
            </div>
            
            <div class="cart-summary">
                <form action="process.php" method="POST">
                    <input type="hidden" name="cart_data" id="cartDataInput" required>
                    <input type="hidden" name="total_amount" id="cartTotalInput" required>
                    
                    <div class="summary-row" style="margin-bottom: 5px;">
                        <span>Total Tagihan</span>
                    </div>
                    <div class="summary-row" style="border-bottom: 1px dashed var(--border-color); padding-bottom: 15px; margin-bottom: 15px;">
                        <span id="cartTotal" class="summary-total">Rp 0</span>
                    </div>
                    
                    <div class="d-flex gap-3 mb-4">
                        <div class="form-group no-icon" style="flex:1.2; margin-bottom:0;">
                            <label><i class="fa-solid fa-wallet"></i> Tipe Bayar</label>
                            <select name="payment_method" required>
                                <option value="Tunai (Cash)">💵 Tunai (Cash)</option>
                                <option value="QRIS / Transfer">💳 QRIS / Transfer</option>
                            </select>
                        </div>
                        <div class="form-group no-icon" style="flex:1; margin-bottom:0;">
                            <label><i class="fa-solid fa-money-bill-wave"></i> Uang Diterima (Rp)</label>
                            <input type="number" name="paid_amount" required min="1" placeholder="0">
                        </div>
                    </div>
                    
                    <button type="submit" class="btn-primary btn-checkout" style="width:100%;">
                        <i class="fa-solid fa-check-circle"></i> Proses & Cetak Struk
                    </button>
                    
                    <?php if(isset($_GET['error'])): ?>
                        <div class="alert error" style="margin-top: 15px;"><i class="fa-solid fa-triangle-exclamation"></i> <?= htmlspecialchars($_GET['error']) ?></div>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
    <script src="../assets/js/app.js"></script>
</body>
</html>
