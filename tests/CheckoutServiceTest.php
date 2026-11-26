<?php
use PHPUnit\Framework\TestCase;

final class CheckoutServiceTest extends TestCase
{
    /** TD-01: Stub menggantikan query tabel inventory */
    public function test_TD_01_stub_stok_nol_menolak_checkout(): void
    {
        // Arrange
        $inventory = $this->createStub(InventoryRepository::class);
        $inventory->method('getStock')->willReturn(0);
        $logger = $this->createStub(StockMutationLogger::class);
        $service = new CheckoutService($inventory, $logger);

        // Act
        $result = $service->check([['id' => 2, 'qty' => 1]], 10000, 20000, "Tunai (Cash)");

        // Assert
        $this->assertSame(["Stok habis", 0], $result);
    }

    /** TD-02: Mock memverifikasi interaksi pencatatan mutasi stok */
    public function test_TD_02_mock_mutasi_stok_dicatat_satu_kali(): void
    {
        // Arrange
        $inventory = $this->createStub(InventoryRepository::class);
        $inventory->method('getStock')->willReturn(100);
        $logger = $this->createMock(StockMutationLogger::class);

        // Assert (ekspektasi mock harus dipasang sebelum Act)
        $logger->expects($this->once())
            ->method('logMutation')
            ->with(1, 3, 100, 97, "Penjualan INV-TEST");

        // Act
        $service = new CheckoutService($inventory, $logger);
        $service->recordStockOut([['id' => 1, 'qty' => 3]], "Penjualan INV-TEST");
    }
}
