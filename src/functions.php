<?php
/**
 * SmartKasir - fungsi logika 8 fitur (FS-001 s.d. FS-008).
 */

/**
 * FITUR 1: Validasi Autentikasi Login Kasir (FS-001 / RQ-001, RQ-002)
 */
function authenticate_user($email, $password, $usersDatabase) {
    if (empty($email) || empty($password)) {
        return ["status" => false, "message" => "Harap isi email dan password", "role" => null];
    }
    if (!isset($usersDatabase[$email])) {
        return ["status" => false, "message" => "Email tidak terdaftar", "role" => null];
    }
    $user = $usersDatabase[$email];
    if ($user['password'] !== $password) {
        return ["status" => false, "message" => "Password salah", "role" => null];
    }
    return ["status" => true, "message" => "Login berhasil", "role" => $user['role']];
}

/**
 * FITUR 2: Pencarian Katalog Produk Real-time (FS-002 / RQ-003)
 */
function search_catalog($keyword, $products) {
    $keyword = trim(strtolower($keyword));
    if ($keyword === "") {
        return $products; // Kembalikan semua jika kata kunci kosong
    }
    $results = [];
    foreach ($products as $p) {
        if (strpos(strtolower($p['name']), $keyword) !== false || strpos(strtolower($p['sku']), $keyword) !== false) {
            $results[] = $p;
        }
    }
    return $results;
}

/**
 * FITUR 3: Pembatasan Kuantitas Keranjang & Validasi Stok (FS-003 / RQ-004, RQ-005, RQ-006)
 */
function validate_cart_item($qty, $stock) {
    if ($qty <= 0) {
        return ["valid" => false, "adjusted_qty" => 0, "message" => "Kuantitas minimal 1"];
    }
    if ($stock <= 0) {
        return ["valid" => false, "adjusted_qty" => 0, "message" => "Stok produk habis"];
    }
    if ($qty > $stock) {
        return ["valid" => false, "adjusted_qty" => $stock, "message" => "Kuantitas melebihi sisa stok"];
    }
    return ["valid" => true, "adjusted_qty" => $qty, "message" => "Item valid"];
}

/**
 * FITUR 4: Validasi Checkout & Pembayaran Kasir POS (FS-004 / RQ-007, RQ-008, RQ-009)
 */
function process_checkout($cart, $total, $cash, $method, $stock) {
    $status = "";
    $change = 0;

    if (empty($cart)) {
        $status = "Keranjang kosong";
    } elseif ($cash < $total) {
        $status = "Uang kurang";
    } elseif ($stock <= 0) {
        $status = "Stok habis";
    } else {
        if ($method === "TUNAI") {
            $change = $cash - $total;
        } else {
            $change = 0;
        }
        $status = "Sukses";
    }

    return [$status, $change];
}

/**
 * FITUR 5: Filter Rentang Tanggal Riwayat Pemesanan (FS-005 / RQ-010)
 */
function filter_order_history($startDate, $endDate, $orders) {
    if (empty($startDate) || empty($endDate)) {
        return ["valid" => false, "data" => [], "message" => "Tanggal mulai dan akhir wajib diisi"];
    }
    if (strtotime($startDate) > strtotime($endDate)) {
        return ["valid" => false, "data" => [], "message" => "Tanggal mulai tidak boleh lebih besar dari tanggal akhir"];
    }
    $filtered = [];
    foreach ($orders as $order) {
        $orderDate = strtotime($order['date']);
        if ($orderDate >= strtotime($startDate) && $orderDate <= strtotime($endDate)) {
            $filtered[] = $order;
        }
    }
    return ["valid" => true, "data" => $filtered, "message" => "Filter berhasil"];
}

/**
 * FITUR 6: Validasi Quick Restock Barang & Mutasi (FS-006 / RQ-011)
 */
function process_quick_restock($addedQty, $currentStock) {
    if (!is_int($addedQty) || $addedQty <= 0) {
        return ["success" => false, "new_stock" => $currentStock, "message" => "Jumlah restok harus bilangan bulat positif"];
    }
    if ($addedQty > 1000) {
        return ["success" => false, "new_stock" => $currentStock, "message" => "Batas maksimal sekali restok adalah 1000 unit"];
    }
    $newStock = $currentStock + $addedQty;
    return ["success" => true, "new_stock" => $newStock, "message" => "Restok berhasil"];
}

/**
 * FITUR 7: Validasi Harga Jual Produk Baru (FS-007 / Master Produk)
 */
function validate_product_price($price) {
    if (!is_numeric($price)) {
        return ["valid" => false, "message" => "Harga harus berupa angka numerik"];
    }
    if ($price < 1000) {
        return ["valid" => false, "message" => "Harga minimal produk adalah Rp 1.000"];
    }
    if ($price > 10000000) {
        return ["valid" => false, "message" => "Harga maksimal produk adalah Rp 10.000.000"];
    }
    return ["valid" => true, "message" => "Harga valid"];
}

/**
 * FITUR 8: Validasi Format Email Akun Pengguna (FS-008 / Registrasi)
 */
function validate_cashier_email($email) {
    if (empty($email)) {
        return ["valid" => false, "message" => "Email tidak boleh kosong"];
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ["valid" => false, "message" => "Format email tidak valid"];
    }
    return ["valid" => true, "message" => "Format email valid"];
}
