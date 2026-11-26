<?php
require_once __DIR__ . '/functions.php';

interface InventoryRepository
{
    public function getStock(int $productId): int;
    public function reduceStock(int $productId, int $qty): void;
}

interface StockMutationLogger
{
    public function logMutation(int $productId, int $qty, int $previousStock, int $currentStock, string $notes): void;
}

class PdoInventoryRepository implements InventoryRepository
{
    public function __construct(private PDO $pdo) {}

    public function getStock(int $productId): int
    {
        // FOR UPDATE mengunci baris selama transaksi berjalan
        $stmt = $this->pdo->prepare("SELECT quantity FROM inventory WHERE product_id = ? FOR UPDATE");
        $stmt->execute([$productId]);
        return (int) $stmt->fetchColumn();
    }

    public function reduceStock(int $productId, int $qty): void
    {
        $stmt = $this->pdo->prepare("UPDATE inventory SET quantity = quantity - ? WHERE product_id = ?");
        $stmt->execute([$qty, $productId]);
    }
}

class PdoStockMutationLogger implements StockMutationLogger
{
    public function __construct(private PDO $pdo, private int $userId) {}

    public function logMutation(int $productId, int $qty, int $previousStock, int $currentStock, string $notes): void
    {
        $stmt = $this->pdo->prepare("INSERT INTO stock_logs (product_id, type, quantity, previous_stock, current_stock, notes, user_id) VALUES (?, 'keluar', ?, ?, ?, ?, ?)");
        $stmt->execute([$productId, $qty, $previousStock, $currentStock, $notes, $this->userId]);
    }
}

class CheckoutService
{
    public function __construct(
        private InventoryRepository $inventory,
        private StockMutationLogger $logger
    ) {}

    /** Mengembalikan [status, kembalian] memakai process_checkout(). */
    public function check(array $cart, $total, $cash, string $paymentMethod): array
    {
        $requested = [];
        foreach ($cart as $item) {
            $id = (int) $item['id'];
            $requested[$id] = ($requested[$id] ?? 0) + (int) $item['qty'];
        }

        $available = 1;
        foreach ($requested as $id => $qty) {
            if ($this->inventory->getStock($id) < $qty) {
                $available = 0;
            }
        }

        // Nilai di form POS: "Tunai (Cash)" atau "QRIS / Transfer"
        $method = stripos($paymentMethod, 'tunai') === 0 ? "TUNAI" : "NON_TUNAI";

        return process_checkout($cart, $total, $cash, $method, $available);
    }

    public function recordStockOut(array $cart, string $notes): void
    {
        foreach ($cart as $item) {
            $id = (int) $item['id'];
            $qty = (int) $item['qty'];
            $previous = $this->inventory->getStock($id);
            $this->inventory->reduceStock($id, $qty);
            $this->logger->logMutation($id, $qty, $previous, max(0, $previous - $qty), $notes);
        }
    }
}
