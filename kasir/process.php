<?php
require_once '../auth.php';
checkAuth('kasir');
require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cart_data'])) {
    $cartData = json_decode($_POST['cart_data'], true);
    $totalAmount = $_POST['total_amount'];
    $paidAmount = $_POST['paid_amount'];
    $paymentMethod = $_POST['payment_method'];

    if (empty($cartData)) {
        header("Location: pos.php?error=Keranjang belanja kosong!");
        exit;
    }
    
    if ($paidAmount < $totalAmount) {
        header("Location: pos.php?error=Uang diterima kurang dari total bayar!");
        exit;
    }

    try {
        $pdo->beginTransaction();
        
        $invoiceNumber = 'INV-' . date('YmdHis') . '-' . rand(100, 999);
        
        $stmt = $pdo->prepare("INSERT INTO transactions (invoice_number, user_id, total_amount, paid_amount, payment_method) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$invoiceNumber, $_SESSION['user_id'], $totalAmount, $paidAmount, $paymentMethod]);
        $transactionId = $pdo->lastInsertId();

        $stmtDetail = $pdo->prepare("INSERT INTO transaction_details (transaction_id, product_id, quantity, price, subtotal) VALUES (?, ?, ?, ?, ?)");
        $stmtReduceStock = $pdo->prepare("UPDATE inventory SET quantity = quantity - ? WHERE product_id = ?");

        foreach ($cartData as $item) {
            $sub = $item['price'] * $item['qty'];
            $stmtDetail->execute([$transactionId, $item['id'], $item['qty'], $item['price'], $sub]);
            $stmtReduceStock->execute([$item['qty'], $item['id']]);
        }

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
