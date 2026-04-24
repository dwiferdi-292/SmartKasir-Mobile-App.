<?php
require_once 'config.php';

try {
    $pdo->beginTransaction();

    // 1. Insert 10 Dummy Products
    $products = [
        ['SKU-001', 'Kopi Susu Gula Aren', 18000, 10000],
        ['SKU-002', 'Americano (Hot/Ice)', 15000, 8000],
        ['SKU-003', 'Cafe Latte', 20000, 12000],
        ['SKU-004', 'Matcha Latte', 22000, 14000],
        ['SKU-005', 'Chocolate Ice', 20000, 12000],
        ['SKU-006', 'Lemon Tea', 12000, 5000],
        ['SKU-007', 'French Fries', 15000, 8000],
        ['SKU-008', 'Chicken Wings', 25000, 15000],
        ['SKU-009', 'Nasi Goreng Spesial', 28000, 18000],
        ['SKU-010', 'Mie Goreng Telur', 20000, 12000],
    ];

    $productIds = [];
    $stmtProd = $pdo->prepare("INSERT INTO products (sku, name, price, cost) VALUES (?, ?, ?, ?)");
    $stmtInv = $pdo->prepare("INSERT INTO inventory (product_id, quantity, min_stock) VALUES (?, ?, ?)");
    
    foreach ($products as $p) {
        // Only insert if sku doesn't exist
        $check = $pdo->prepare("SELECT id FROM products WHERE sku = ?");
        $check->execute([$p[0]]);
        if($check->rowCount() == 0) {
            $stmtProd->execute($p);
            $pid = $pdo->lastInsertId();
            $productIds[] = ['id' => $pid, 'price' => $p[2]];
            
            // Random stock between 5 and 50. Min stock 10.
            $qty = rand(5, 50);
            $stmtInv->execute([$pid, $qty, 10]);
        }
    }

    if (empty($productIds)) {
        // If already run, fetch old products
        $productIdsFetch = $pdo->query("SELECT id, price FROM products")->fetchAll();
        $productIds = $productIdsFetch;
    }

    // 2. Fetch User IDs
    $kasirId = $pdo->query("SELECT id FROM users WHERE role = 'kasir' LIMIT 1")->fetchColumn();
    if (!$kasirId) {
        $stmtInsBase = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES ('Kasir Dummy', 'kasir@smartkasir.com', ?, 'kasir')");
        $stmtInsBase->execute(['$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi']); // 'password'
        $kasirId = $pdo->lastInsertId();
    }

    // 3. Generate Transactions for the last 14 days
    $stmtTrx = $pdo->prepare("INSERT INTO transactions (invoice_number, user_id, total_amount, paid_amount, payment_method, created_at) VALUES (?, ?, ?, ?, ?, ?)");
    $stmtDetail = $pdo->prepare("INSERT INTO transaction_details (transaction_id, product_id, quantity, price, subtotal) VALUES (?, ?, ?, ?, ?)");

    for ($i = 14; $i >= 0; $i--) {
        $date = date('Y-m-d H:i:s', strtotime("-$i days"));
        // 2 to 6 transactions per day
        $trxCount = rand(2, 6);
        
        for ($t = 0; $t < $trxCount; $t++) {
            $inv = 'INV-' . date('Ymd', strtotime($date)) . '-' . rand(1000, 9999);
            
            // Random products in cart
            $cartItemsCount = rand(1, 4);
            $cart = [];
            $totalAmount = 0;
            
            for ($c = 0; $c < $cartItemsCount; $c++) {
                $randProd = $productIds[array_rand($productIds)];
                $qty = rand(1, 3);
                $subtotal = $randProd['price'] * $qty;
                $totalAmount += $subtotal;
                $cart[] = [
                    'product_id' => $randProd['id'],
                    'qty' => $qty,
                    'price' => $randProd['price'],
                    'subtotal' => $subtotal
                ];
            }
            
            // Payment method
            $paymentMethod = rand(0, 1) ? 'Cash' : 'Transfer';
            // Paid amount slightly higher or equal (rounded to thousands)
            $paidAmount = $totalAmount + (rand(0, 5) * 5000);
            
            $stmtTrx->execute([$inv, $kasirId, $totalAmount, $paidAmount, $paymentMethod, $date]);
            $trxId = $pdo->lastInsertId();
            
            foreach ($cart as $item) {
                $stmtDetail->execute([$trxId, $item['product_id'], $item['qty'], $item['price'], $item['subtotal']]);
            }
        }
    }

    $pdo->commit();
    echo "<div style='font-family:sans-serif; text-align:center; margin-top:50px;'>";
    echo "<h1 style='color: #10b981;'>Data Simulasi Berhasil Ditanamkan! ✅</h1>";
    echo "<p>Sistem telah membuat <strong>10 Jenis Produk Menu</strong> (dengan jumlah stok acak).</p>";
    echo "<p>Sistem juga menyimulasikan puluhan <strong>Transaksi Penjualan 14 Hari ke belakang</strong>.</p>";
    echo "<a href='index.php' style='display:inline-block; padding:10px 20px; background:#6366f1; color:#fff; text-decoration:none; border-radius:5px; margin-top:20px;'>Kembali ke Halaman Login</a>";
    echo "</div>";

} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "Gagal: " . $e->getMessage();
}
