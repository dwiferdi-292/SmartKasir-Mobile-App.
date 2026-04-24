<?php
require_once '../auth.php';
checkAuth('kasir');
require_once '../config.php';

if (!isset($_GET['id'])) {
    header("Location: pos.php");
    exit;
}

$stmtTrx = $pdo->prepare("SELECT t.*, u.name as kasir_name FROM transactions t JOIN users u ON t.user_id = u.id WHERE t.id = ?");
$stmtTrx->execute([$_GET['id']]);
$trx = $stmtTrx->fetch();

if (!$trx) {
    die("Transaksi tidak ditemukan.");
}

$stmtItems = $pdo->prepare("SELECT td.*, p.name FROM transaction_details td JOIN products p ON td.product_id = p.id WHERE td.transaction_id = ?");
$stmtItems->execute([$trx['id']]);
$items = $stmtItems->fetchAll();

$kembalian = $trx['paid_amount'] - $trx['total_amount'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Print Struk - <?= htmlspecialchars($trx['invoice_number']) ?></title>
    <style>
        body { font-family: 'Courier New', Courier, monospace; margin: 0; padding: 20px; font-size: 14px; color: #000; }
        .receipt { max-width: 300px; margin: 0 auto; border: 1px dashed #000; padding: 15px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; font-size: 20px; }
        .header p { margin: 5px 0 0; font-size: 12px; }
        .divider { border-bottom: 1px dashed #000; margin: 10px 0; }
        .info { font-size: 12px; margin-bottom: 10px; }
        table { width: 100%; font-size: 12px; }
        th, td { text-align: left; padding: 2px 0; }
        th.right, td.right { text-align: right; }
        .total-section { margin-top: 10px; font-size: 14px; font-weight: bold; }
        .footer { text-align: center; margin-top: 20px; font-size: 12px; }
        @media print {
            body { padding: 0; }
            .receipt { max-width: 100%; border: none; }
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="no-print" style="text-align: center; margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; font-size: 16px; cursor: pointer;">Cetak Struk</button>
        <a href="pos.php" style="padding: 10px 20px; font-size: 16px; text-decoration: none; background: #e2e8f0; color: #000; border: 1px solid #cbd5e1; margin-left: 10px;">Kembali ke POS</a>
    </div>

    <div class="receipt">
        <div class="header">
            <h2>SMARTKASIR</h2>
            <p>Jl. Contoh No. 123, Kota Anda</p>
        </div>
        
        <div class="info">
            Tgl  : <?= date('d M Y H:i', strtotime($trx['created_at'])) ?><br>
            No   : <?= $trx['invoice_number'] ?><br>
            Kasir: <?= htmlspecialchars($trx['kasir_name']) ?>
        </div>
        
        <div class="divider"></div>
        
        <table>
            <?php foreach($items as $i): ?>
            <tr>
                <td colspan="3"><?= htmlspecialchars($i['name']) ?></td>
            </tr>
            <tr>
                <td><?= $i['quantity'] ?> x</td>
                <td><?= number_format($i['price'], 0, ',', '.') ?></td>
                <td class="right"><?= number_format($i['subtotal'], 0, ',', '.') ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
        
        <div class="divider"></div>
        
        <table class="total-section">
            <tr>
                <td>Total</td>
                <td class="right">Rp <?= number_format($trx['total_amount'], 0, ',', '.') ?></td>
            </tr>
            <tr>
                <td>Bayar (<?= $trx['payment_method'] ?>)</td>
                <td class="right">Rp <?= number_format($trx['paid_amount'], 0, ',', '.') ?></td>
            </tr>
            <tr>
                <td>Kembali</td>
                <td class="right">Rp <?= number_format($kembalian, 0, ',', '.') ?></td>
            </tr>
        </table>
        
        <div class="divider"></div>
        
        <div class="footer">
            <p>Terima Kasih Atas Kunjungan Anda</p>
            <p>Barang yang sudah dibeli tidak dapat ditukar/dikembalikan.</p>
        </div>
    </div>
</body>
</html>
