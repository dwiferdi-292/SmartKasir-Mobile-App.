<?php
require_once '../auth.php';
checkAuth('kasir');
require_once '../config.php';

// Fetch products with stock > 0
$products = $pdo->query("SELECT p.*, i.quantity FROM products p JOIN inventory i ON p.id = i.product_id ORDER BY p.name ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>POS - SmartKasir</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .pos-topbar { background: #fff; box-shadow: var(--shadow-sm); }
    </style>
</head>
<body style="overflow: hidden;">
    <div class="topbar pos-topbar">
        <div style="font-size: 1.5rem; font-weight: 800; color: var(--primary-color); letter-spacing: -1px;">
            <i class="fa-solid fa-cube"></i> SmartKasir <span style="color:var(--text-muted); font-weight:400; font-size:1rem;">| Point Of Sale</span>
        </div>
        <div class="d-flex align-center gap-4">
            <a href="../logout.php" class="badge badge-danger"><i class="fa-solid fa-power-off"></i> Tutup Kasir</a>
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
                $images = [
                    'https://images.unsplash.com/photo-1559525839-b184a4d698c7?auto=format&fit=crop&w=300&q=80',
                    'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?auto=format&fit=crop&w=300&q=80',
                    'https://images.unsplash.com/photo-1572442388796-11668a67e53d?auto=format&fit=crop&w=300&q=80',
                    'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?auto=format&fit=crop&w=300&q=80',
                    'https://images.unsplash.com/photo-1551024601-bec78aea704b?auto=format&fit=crop&w=300&q=80'
                ];
                foreach($products as $idx => $p): 
                    $imgUrl = $images[$idx % count($images)];
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
