<?php
use PHPUnit\Framework\TestCase;

final class CheckoutTest extends TestCase
{
    /** TC-F4-01 */
    public function test_F4_01_keranjang_kosong_ditolak(): void
    {
        // Arrange
        $cart = [];
        $total = 0;
        $cash = 50000;
        $method = "TUNAI";
        $stock = 10;

        // Act
        $result = process_checkout($cart, $total, $cash, $method, $stock);

        // Assert
        $this->assertSame(["Keranjang kosong", 0], $result);
    }

    /** TC-F4-02 */
    public function test_F4_02_uang_kurang_ditolak(): void
    {
        // Arrange
        $cart = [['id' => 1, 'qty' => 2]];
        $total = 24000;
        $cash = 20000;
        $method = "TUNAI";
        $stock = 10;

        // Act
        $result = process_checkout($cart, $total, $cash, $method, $stock);

        // Assert
        $this->assertSame(["Uang kurang", 0], $result);
    }

    /** TC-F4-03 */
    public function test_F4_03_stok_habis_ditolak(): void
    {
        // Arrange
        $cart = [['id' => 1, 'qty' => 2]];
        $total = 24000;
        $cash = 50000;
        $method = "TUNAI";
        $stock = 0;

        // Act
        $result = process_checkout($cart, $total, $cash, $method, $stock);

        // Assert
        $this->assertSame(["Stok habis", 0], $result);
    }

    /** TC-F4-04 */
    public function test_F4_04_tunai_sukses_dengan_kembalian(): void
    {
        // Arrange
        $cart = [['id' => 1, 'qty' => 2]];
        $total = 24000;
        $cash = 50000;
        $method = "TUNAI";
        $stock = 10;

        // Act
        $result = process_checkout($cart, $total, $cash, $method, $stock);

        // Assert
        $this->assertSame(["Sukses", 26000], $result);
    }

    /** TC-F4-05 */
    public function test_F4_05_qris_sukses_tanpa_kembalian(): void
    {
        // Arrange
        $cart = [['id' => 1, 'qty' => 1]];
        $total = 24000;
        $cash = 24000;
        $method = "QRIS";
        $stock = 10;

        // Act
        $result = process_checkout($cart, $total, $cash, $method, $stock);

        // Assert
        $this->assertSame(["Sukses", 0], $result);
    }
}
