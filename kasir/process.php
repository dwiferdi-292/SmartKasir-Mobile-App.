<?php
require_once '../auth.php';
checkAuth('kasir');
require_once '../config.php';
require_once '../src/CheckoutService.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cart_data'])) {
    $cartData = json_decode($_POST['cart_data'], true) ?: [];
    $totalAmount = $_POST['total_amount'];
    $paidAmount = $_POST['paid_amount'];
    $paymentMethod = $_POST['payment_method'];

    $service = new CheckoutService(
        new PdoInventoryRepository($pdo),
        new PdoStockMutationLogger($pdo, (int) $_SESSION['user_id'])
    );

    try {
        $pdo->beginTransaction();

        [$status] = $service->check($cartData, $totalAmount, $paidAmount, $paymentMethod);
        if ($status !== "Sukses") {
            $pdo->rollBack();
            $messages = [
                "Keranjang kosong" => "Keranjang belanja kosong!",
                "Uang kurang" => "Uang diterima kurang dari total bayar!",
                "Stok habis" => "Stok produk tidak mencukupi!",
            ];
            header("Location: pos.php?error=" . $messages[$status]);
            exit;
        }
        
        $invoiceNumber = 'INV-' . date('YmdHis') . '-' . rand(100, 999);
        
        $stmt = $pdo->prepare("INSERT INTO transactions (invoice_number, user_id, total_amount, paid_amount, payment_method) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$invoiceNumber, $_SESSION['user_id'], $totalAmount, $paidAmount, $paymentMethod]);
        $transactionId = $pdo->lastInsertId();

        $stmtDetail = $pdo->prepare("INSERT INTO transaction_details (transaction_id, product_id, quantity, price, subtotal) VALUES (?, ?, ?, ?, ?)");

        foreach ($cartData as $item) {
            $sub = $item['price'] * $item['qty'];
            $stmtDetail->execute([$transactionId, $item['id'], $item['qty'], $item['price'], $sub]);
        }

        // Kurangi stok dan catat mutasi
        $service->recordStockOut($cartData, "Penjualan " . $invoiceNumber);

        $pdo->commit();
        header("Location: print.php?id=" . $transactionId);
        exit;
    } catch(PDOException $e) {
        $pdo->rollBack();
        header("Location: pos.php?error=Terjadi kesalahan sistem: " . urlencode($e->getMessage()));
        exit;
    }
} else {
    header("Location: pos.php");
    exit;
}
?>
