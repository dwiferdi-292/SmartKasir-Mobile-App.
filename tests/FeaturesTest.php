<?php
use PHPUnit\Framework\TestCase;

final class FeaturesTest extends TestCase
{
    private array $users = [
        'kasir@smartkasir.com' => ['password' => 'password123', 'role' => 'kasir'],
    ];

    private array $products = [
        ['id' => 1, 'name' => 'French Fries', 'sku' => 'FF-001'],
        ['id' => 2, 'name' => 'Cafe Latte', 'sku' => 'CL-002'],
        ['id' => 3, 'name' => 'Fried Rice', 'sku' => 'FR-003'],
    ];

    private array $orders = [
        ['id' => 101, 'date' => '2026-11-01'],
        ['id' => 102, 'date' => '2026-11-15'],
        ['id' => 103, 'date' => '2026-11-25'],
    ];

    /** TC-F1-01 */
    public function test_F1_01_form_kosong_ditolak(): void
    {
        // Arrange
        $email = "";
        $password = "";

        // Act
        $result = authenticate_user($email, $password, $this->users);

        // Assert
        $this->assertSame(false, $result['status']);
        $this->assertSame("Harap isi email dan password", $result['message']);
    }

    /** TC-F1-02 */
    public function test_F1_02_email_tidak_terdaftar(): void
    {
        // Arrange
        $email = "unknown@mail.com";
        $password = "pass";

        // Act
        $result = authenticate_user($email, $password, $this->users);

        // Assert
        $this->assertSame(false, $result['status']);
        $this->assertSame("Email tidak terdaftar", $result['message']);
    }

    /** TC-F1-03 */
    public function test_F1_03_password_salah(): void
    {
        // Arrange
        $email = "kasir@smartkasir.com";
        $password = "salah";

        // Act
        $result = authenticate_user($email, $password, $this->users);

        // Assert
        $this->assertSame(false, $result['status']);
        $this->assertSame("Password salah", $result['message']);
    }

    /** TC-F1-04 */
    public function test_F1_04_login_valid(): void
    {
        // Arrange
        $email = "kasir@smartkasir.com";
        $password = "password123";

        // Act
        $result = authenticate_user($email, $password, $this->users);

        // Assert
        $this->assertSame(true, $result['status']);
        $this->assertSame("Login berhasil", $result['message']);
    }

    /** TC-F2-01 */
    public function test_F2_01_kata_kunci_kosong_semua_produk(): void
    {
        // Arrange
        $keyword = "";

        // Act
        $result = search_catalog($keyword, $this->products);

        // Assert
        $this->assertCount(3, $result);
    }

    /** TC-F2-02 */
    public function test_F2_02_cari_latte_satu_produk(): void
    {
        // Arrange
        $keyword = "latte";

        // Act
        $result = search_catalog($keyword, $this->products);

        // Assert
        $this->assertCount(1, $result);
    }

    /** TC-F2-03 */
    public function test_F2_03_produk_tidak_ada(): void
    {
        // Arrange
        $keyword = "burger";

        // Act
        $result = search_catalog($keyword, $this->products);

        // Assert
        $this->assertCount(0, $result);
    }

    /** TC-F3-01 */
    public function test_F3_01_kuantitas_nol_ditolak(): void
    {
        // Arrange
        $qty = 0;
        $stock = 10;

        // Act
        $result = validate_cart_item($qty, $stock);

        // Assert
        $this->assertSame(false, $result['valid']);
        $this->assertSame(0, $result['adjusted_qty']);
    }

    /** TC-F3-02 */
    public function test_F3_02_stok_nol_ditolak(): void
    {
        // Arrange
        $qty = 2;
        $stock = 0;

        // Act
        $result = validate_cart_item($qty, $stock);

        // Assert
        $this->assertSame(false, $result['valid']);
        $this->assertSame(0, $result['adjusted_qty']);
    }

    /** TC-F3-03 */
    public function test_F3_03_melebihi_stok_dibatasi(): void
    {
        // Arrange
        $qty = 15;
        $stock = 10;

        // Act
        $result = validate_cart_item($qty, $stock);

        // Assert
        $this->assertSame(false, $result['valid']);
        $this->assertSame(10, $result['adjusted_qty']);
    }

    /** TC-F3-04 */
    public function test_F3_04_kuantitas_valid(): void
    {
        // Arrange
        $qty = 3;
        $stock = 10;

        // Act
        $result = validate_cart_item($qty, $stock);

        // Assert
        $this->assertSame(true, $result['valid']);
        $this->assertSame(3, $result['adjusted_qty']);
    }

    /** TC-F5-01 */
    public function test_F5_01_tanggal_terbalik_ditolak(): void
    {
        // Arrange
        $start = "2026-11-20";
        $end = "2026-11-10";

        // Act
        $result = filter_order_history($start, $end, $this->orders);

        // Assert
        $this->assertSame(false, $result['valid']);
        $this->assertCount(0, $result['data']);
    }

    /** TC-F5-02 */
    public function test_F5_02_rentang_valid_satu_transaksi(): void
    {
        // Arrange
        $start = "2026-11-10";
        $end = "2026-11-20";

        // Act
        $result = filter_order_history($start, $end, $this->orders);

        // Assert
        $this->assertSame(true, $result['valid']);
        $this->assertCount(1, $result['data']);
    }

    /** TC-F5-03 */
    public function test_F5_03_tanggal_kosong_ditolak(): void
    {
        // Arrange
        $start = "";
        $end = "2026-11-20";

        // Act
        $result = filter_order_history($start, $end, $this->orders);

        // Assert
        $this->assertSame(false, $result['valid']);
        $this->assertCount(0, $result['data']);
    }

    /** TC-F6-01 */
    public function test_F6_01_restok_negatif_ditolak(): void
    {
        // Arrange
        $addedQty = -5;
        $currentStock = 20;

        // Act
        $result = process_quick_restock($addedQty, $currentStock);

        // Assert
        $this->assertSame(false, $result['success']);
        $this->assertSame(20, $result['new_stock']);
    }

    /** TC-F6-02 */
    public function test_F6_02_restok_di_atas_batas_ditolak(): void
    {
        // Arrange
        $addedQty = 1500;
        $currentStock = 20;

        // Act
        $result = process_quick_restock($addedQty, $currentStock);

        // Assert
        $this->assertSame(false, $result['success']);
        $this->assertSame(20, $result['new_stock']);
    }

    /** TC-F6-03 */
    public function test_F6_03_restok_valid(): void
    {
        // Arrange
        $addedQty = 50;
        $currentStock = 20;

        // Act
        $result = process_quick_restock($addedQty, $currentStock);

        // Assert
        $this->assertSame(true, $result['success']);
        $this->assertSame(70, $result['new_stock']);
    }

    /** TC-F7-01 */
    public function test_F7_01_harga_di_bawah_minimal(): void
    {
        // Arrange
        $price = 500;

        // Act
        $result = validate_product_price($price);

        // Assert
        $this->assertSame(false, $result['valid']);
    }

    /** TC-F7-02 */
    public function test_F7_02_harga_di_atas_maksimal(): void
    {
        // Arrange
        $price = 15000000;

        // Act
        $result = validate_product_price($price);

        // Assert
        $this->assertSame(false, $result['valid']);
    }

    /** TC-F7-03 */
    public function test_F7_03_harga_valid(): void
    {
        // Arrange
        $price = 25000;

        // Act
        $result = validate_product_price($price);

        // Assert
        $this->assertSame(true, $result['valid']);
    }

    /** TC-F7-04 */
    public function test_F7_04_harga_bukan_angka(): void
    {
        // Arrange
        $price = "abc";

        // Act
        $result = validate_product_price($price);

        // Assert
        $this->assertSame(false, $result['valid']);
    }

    /** TC-F8-01 */
    public function test_F8_01_format_salah(): void
    {
        // Arrange
        $email = "invalid-email-format";

        // Act
        $result = validate_cashier_email($email);

        // Assert
        $this->assertSame(false, $result['valid']);
    }

    /** TC-F8-02 */
    public function test_F8_02_format_valid(): void
    {
        // Arrange
        $email = "kasir.baru@smartkasir.com";

        // Act
        $result = validate_cashier_email($email);

        // Assert
        $this->assertSame(true, $result['valid']);
    }

    /** TC-F8-03 */
    public function test_F8_03_email_kosong(): void
    {
        // Arrange
        $email = "";

        // Act
        $result = validate_cashier_email($email);

        // Assert
        $this->assertSame(false, $result['valid']);
    }
}
