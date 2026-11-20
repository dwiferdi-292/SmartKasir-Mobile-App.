<?php
require_once '../auth.php';
checkAuth('kasir');
require_once '../config.php';

// Badge count untuk stok kritis
$lowStockCount = (int)$pdo->query("SELECT COUNT(*) FROM inventory WHERE quantity <= min_stock")->fetchColumn();

// Filter Tanggal
$filterRange = $_GET['range'] ?? 'today';
$startDate = $_GET['start_date'] ?? date('Y-m-d');
$endDate = $_GET['end_date'] ?? date('Y-m-d');
$search = trim($_GET['search'] ?? '');

if ($filterRange === 'today') {
    $startDate = date('Y-m-d');
    $endDate = date('Y-m-d');
} elseif ($filterRange === '7days') {
    $startDate = date('Y-m-d', strtotime('-6 days'));
    $endDate = date('Y-m-d');
} elseif ($filterRange === '30days') {
    $startDate = date('Y-m-d', strtotime('-29 days'));
    $endDate = date('Y-m-d');
} elseif ($filterRange === 'all') {
    $startDate = '2000-01-01';
    $endDate = date('Y-m-d');
}

// Query Transaksi
$sql = "SELECT t.*, u.name as kasir_name 
        FROM transactions t 
        JOIN users u ON t.user_id = u.id 
        WHERE DATE(t.created_at) >= ? AND DATE(t.created_at) <= ?";
$params = [$startDate, $endDate];

if (!empty($search)) {
    $sql .= " AND (t.invoice_number LIKE ? OR t.payment_method LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$sql .= " ORDER BY t.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$transactions = $stmt->fetchAll();

// Ambil item details untuk semua transaksi dalam periode ini
$trxIds = array_column($transactions, 'id');
$itemsByTrx = [];
if (!empty($trxIds)) {
    $inPlaceholders = implode(',', array_fill(0, count($trxIds), '?'));
    $stmtItems = $pdo->prepare("SELECT td.*, p.name as product_name, p.sku 
                               FROM transaction_details td 
                               JOIN products p ON td.product_id = p.id 
                               WHERE td.transaction_id IN ($inPlaceholders)");
    $stmtItems->execute($trxIds);
    $allItems = $stmtItems->fetchAll();
    foreach ($allItems as $it) {
        $itemsByTrx[$it['transaction_id']][] = $it;
    }
}

// KPI Kalkulasi
$totalOmzet = 0;
$totalCash = 0;
$totalNonCash = 0;
$totalItemsSold = 0;

foreach ($transactions as $t) {
    $totalOmzet += (float)$t['total_amount'];
    if (stripos($t['payment_method'], 'Cash') !== false || stripos($t['payment_method'], 'Tunai') !== false) {
        $totalCash += (float)$t['total_amount'];
    } else {
        $totalNonCash += (float)$t['total_amount'];
    }
    if (isset($itemsByTrx[$t['id']])) {
        foreach ($itemsByTrx[$t['id']] as $it) {
            $totalItemsSold += (int)$it['quantity'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pemesanan - SmartKasir</title>
    <link rel="stylesheet" href="../assets/css/style.css?v=<?= time() ?>">
    <style>
        .filter-chip {
            padding: 8px 16px;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 600;
            background: #f1f5f9;
            color: var(--text-muted);
            border: 1px solid var(--border-color);
            transition: all 0.2s;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .filter-chip:hover {
            border-color: var(--primary-color);
            color: var(--primary-color);
        }
        .filter-chip.active {
            background: var(--primary-light);
            color: var(--primary-color);
            border-color: var(--primary-color);
        }
    </style>
</head>
<body>
    <!-- Topbar Kasir -->
    <div class="topbar pos-topbar">
        <div class="d-flex align-center gap-3">
            <div style="font-size: 1.5rem; font-weight: 800; color: var(--primary-color); letter-spacing: -1px;">
                <i class="fa-solid fa-cube"></i> SmartKasir <span style="color:var(--text-muted); font-weight:400; font-size:1rem;" class="hide-mobile">| Kasir</span>
            </div>
        </div>

        <!-- Menu Navigasi Kasir -->
        <nav class="kasir-nav">
            <a href="pos.php" class="kasir-nav-item">
                <i class="fa-solid fa-cash-register"></i>
                <span>Kasir (POS)</span>
            </a>
            <a href="orders.php" class="kasir-nav-item active">
                <i class="fa-solid fa-clock-rotate-left"></i>
                <span>Riwayat Pemesanan</span>
            </a>
            <a href="stock.php" class="kasir-nav-item">
                <i class="fa-solid fa-boxes-stacked"></i>
                <span>Manajemen Stok</span>
                <?php if ($lowStockCount > 0): ?>
                    <span class="nav-badge" title="<?= $lowStockCount ?> produk stok menipis"><?= $lowStockCount ?></span>
                <?php endif; ?>
            </a>
        </nav>

        <div class="d-flex align-center gap-3">
            <a href="../logout.php" class="btn-logout"><i class="fa-solid fa-power-off"></i> Keluar</a>
            <div class="user-info">
                <img src="https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['user_name']) ?>&background=10b981&color=fff" alt="Avatar">
                <?= htmlspecialchars($_SESSION['user_name']) ?> (Kasir)
            </div>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="content-area" style="max-width: 1400px; margin: 0 auto; padding: 30px 20px;">
        <!-- Header Page -->
        <div class="d-flex justify-between align-center mb-4" style="flex-wrap: wrap; gap: 15px;">
            <div>
                <h1 style="font-size: 1.8rem; font-weight: 800; color: var(--text-dark); margin-bottom: 5px;">
                    <i class="fa-solid fa-receipt" style="color: var(--primary-color);"></i> Riwayat Pemesanan & Transaksi
                </h1>
                <p style="color: var(--text-muted); font-size: 0.95rem;">
                    Pantau daftar semua transaksi penjualan, cek rincian belanja pelanggan, dan cetak ulang struk.
                </p>
            </div>
            <div>
                <a href="pos.php" class="btn-primary" style="padding: 12px 24px; text-decoration: none; width: auto;">
                    <i class="fa-solid fa-plus-circle"></i> Transaksi Baru (POS)
                </a>
            </div>
        </div>

        <!-- KPI Summary Cards -->
        <div class="kpi-grid">
            <div class="kpi-card">
                <i class="fa-solid fa-receipt bg-icon"></i>
                <div class="kpi-title"><i class="fa-solid fa-file-invoice"></i> Total Transaksi</div>
                <div class="kpi-value"><?= count($transactions) ?></div>
            </div>
            <div class="kpi-card success">
                <i class="fa-solid fa-wallet bg-icon"></i>
                <div class="kpi-title"><i class="fa-solid fa-coins"></i> Total Omzet Penjualan</div>
                <div class="kpi-value">Rp <?= number_format($totalOmzet, 0, ',', '.') ?></div>
            </div>
            <div class="kpi-card">
                <i class="fa-solid fa-money-bill-wave bg-icon"></i>
                <div class="kpi-title"><i class="fa-solid fa-money-bill"></i> Tunai (Cash)</div>
                <div class="kpi-value">Rp <?= number_format($totalCash, 0, ',', '.') ?></div>
            </div>
            <div class="kpi-card warning">
                <i class="fa-solid fa-qrcode bg-icon"></i>
                <div class="kpi-title"><i class="fa-solid fa-credit-card"></i> Non-Tunai / QRIS</div>
                <div class="kpi-value">Rp <?= number_format($totalNonCash, 0, ',', '.') ?></div>
            </div>
        </div>

        <!-- Filter & Search Card -->
        <div class="card mb-4" style="padding: 22px;">
            <form action="orders.php" method="GET" class="d-flex align-center gap-3" style="flex-wrap: wrap;">
                <!-- Filter Chips -->
                <div class="d-flex align-center gap-2" style="flex-wrap: wrap;">
                    <a href="orders.php?range=today" class="filter-chip <?= $filterRange === 'today' ? 'active' : '' ?>">
                        <i class="fa-regular fa-calendar"></i> Hari Ini
                    </a>
                    <a href="orders.php?range=7days" class="filter-chip <?= $filterRange === '7days' ? 'active' : '' ?>">
                        <i class="fa-solid fa-calendar-week"></i> 7 Hari Terakhir
                    </a>
                    <a href="orders.php?range=30days" class="filter-chip <?= $filterRange === '30days' ? 'active' : '' ?>">
                        <i class="fa-regular fa-calendar-days"></i> 30 Hari
                    </a>
                    <a href="orders.php?range=all" class="filter-chip <?= $filterRange === 'all' ? 'active' : '' ?>">
                        <i class="fa-solid fa-list-check"></i> Semua
                    </a>
                </div>

                <!-- Custom Date Range -->
                <input type="hidden" name="range" value="custom">
                <div class="d-flex align-center gap-2" style="margin-left: auto; flex-wrap: wrap;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600;">Dari:</span>
                        <input type="date" name="start_date" value="<?= htmlspecialchars($startDate) ?>" class="search-bar" style="padding: 10px 14px; font-size: 0.9rem; width: auto; border: 1px solid var(--border-color);">
                    </div>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600;">Sampai:</span>
                        <input type="date" name="end_date" value="<?= htmlspecialchars($endDate) ?>" class="search-bar" style="padding: 10px 14px; font-size: 0.9rem; width: auto; border: 1px solid var(--border-color);">
                    </div>
                    <div style="position: relative;">
                        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Cari No. Invoice / Metode..." class="search-bar" style="padding: 10px 14px 10px 36px; font-size: 0.9rem; width: 220px; border: 1px solid var(--border-color);">
                        <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 0.85rem;"></i>
                    </div>
                    <button type="submit" class="btn-primary" style="padding: 10px 18px; width: auto; font-size: 0.9rem;">
                        <i class="fa-solid fa-filter"></i> Filter
                    </button>
                    <?php if (!empty($search) || $filterRange === 'custom'): ?>
                        <a href="orders.php?range=today" class="btn-danger" style="padding: 10px 14px; text-decoration: none; font-size: 0.9rem;" title="Reset filter">
                            <i class="fa-solid fa-rotate-left"></i> Reset
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- Tabel Transaksi -->
        <div class="card">
            <div class="d-flex justify-between align-center mb-4">
                <h3 style="font-size: 1.15rem; font-weight: 700; color: var(--text-dark);">
                    <i class="fa-solid fa-list-check" style="color: var(--primary-color);"></i> Daftar Pemesanan Pelanggan
                </h3>
                <span class="badge badge-primary">Ditemukan: <?= count($transactions) ?> Transaksi</span>
            </div>

            <div class="table-wrapper">
                <table class="table-modern">
                    <thead>
                        <tr>
                            <th>No. Invoice</th>
                            <th>Waktu & Tanggal</th>
                            <th>Kasir</th>
                            <th>Metode Bayar</th>
                            <th>Total Tagihan</th>
                            <th>Uang Bayar / Kembali</th>
                            <th style="text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($transactions)): ?>
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 50px 20px; color: var(--text-muted);">
                                    <i class="fa-regular fa-folder-open" style="font-size: 3rem; opacity: 0.4; margin-bottom: 12px; display: block;"></i>
                                    <div style="font-size: 1.1rem; font-weight: 600; color: var(--text-dark);">Belum Ada Riwayat Pemesanan</div>
                                    <p style="font-size: 0.9rem; margin-top: 4px;">Tidak ada transaksi yang cocok dengan filter atau tanggal yang dipilih.</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($transactions as $t): 
                                $items = $itemsByTrx[$t['id']] ?? [];
                                $itemCount = array_sum(array_column($items, 'quantity'));
                                $kembalian = max(0, $t['paid_amount'] - $t['total_amount']);
                                $itemsJson = htmlspecialchars(json_encode([
                                    'id' => $t['id'],
                                    'invoice' => $t['invoice_number'],
                                    'date' => date('d M Y H:i', strtotime($t['created_at'])),
                                    'kasir' => $t['kasir_name'],
                                    'payment_method' => $t['payment_method'],
                                    'total' => (float)$t['total_amount'],
                                    'paid' => (float)$t['paid_amount'],
                                    'kembalian' => (float)$kembalian,
                                    'items' => $items
                                ]), ENT_QUOTES, 'UTF-8');
                            ?>
                                <tr>
                                    <td>
                                        <div style="font-weight: 700; color: var(--primary-color); font-size: 0.95rem;">
                                            <?= htmlspecialchars($t['invoice_number']) ?>
                                        </div>
                                        <div style="font-size: 0.8rem; color: var(--text-muted);">
                                            <i class="fa-solid fa-boxes-packing"></i> <?= $itemCount ?> item dibeli
                                        </div>
                                    </td>
                                    <td>
                                        <div style="font-weight: 600; color: var(--text-dark);">
                                            <?= date('d M Y', strtotime($t['created_at'])) ?>
                                        </div>
                                        <div style="font-size: 0.8rem; color: var(--text-muted);">
                                            <i class="fa-regular fa-clock"></i> <?= date('H:i', strtotime($t['created_at'])) ?> WIB
                                        </div>
                                    </td>
                                    <td>
                                        <span style="font-weight: 600; color: var(--text-dark); display: inline-flex; align-items: center; gap: 6px;">
                                            <i class="fa-solid fa-user-circle" style="color: #94a3b8;"></i>
                                            <?= htmlspecialchars($t['kasir_name']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if (stripos($t['payment_method'], 'Cash') !== false || stripos($t['payment_method'], 'Tunai') !== false): ?>
                                            <span class="badge badge-success"><i class="fa-solid fa-money-bill-wave"></i> <?= htmlspecialchars($t['payment_method']) ?></span>
                                        <?php else: ?>
                                            <span class="badge badge-warning"><i class="fa-solid fa-credit-card"></i> <?= htmlspecialchars($t['payment_method']) ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div style="font-weight: 800; font-size: 1.05rem; color: var(--text-dark);">
                                            Rp <?= number_format($t['total_amount'], 0, ',', '.') ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div style="font-size: 0.9rem; color: var(--text-dark);">
                                            Bayar: <strong>Rp <?= number_format($t['paid_amount'], 0, ',', '.') ?></strong>
                                        </div>
                                        <div style="font-size: 0.8rem; color: var(--success); font-weight: 600;">
                                            Kembali: Rp <?= number_format($kembalian, 0, ',', '.') ?>
                                        </div>
                                    </td>
                                    <td style="text-align: center;">
                                        <div style="display: inline-flex; gap: 8px;">
                                            <button type="button" class="btn-primary" style="padding: 8px 12px; font-size: 0.85rem; width: auto; background: #f1f5f9; color: var(--primary-color); border: 1px solid var(--border-color);" onclick='showOrderDetail(<?= $itemsJson ?>)'>
                                                <i class="fa-solid fa-eye"></i> Detail
                                            </button>
                                            <a href="print.php?id=<?= $t['id'] ?>" target="_blank" class="btn-primary" style="padding: 8px 12px; font-size: 0.85rem; width: auto; text-decoration: none;" title="Cetak Ulang Struk">
                                                <i class="fa-solid fa-print"></i> Struk
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Detail Pemesanan -->
    <div class="modal-backdrop" id="orderModal">
        <div class="modal-card">
            <div class="modal-header">
                <div>
                    <h3 id="modalInvoiceTitle" style="color: var(--primary-color); font-weight: 800;">INV-XXXXX</h3>
                    <div id="modalDateTime" style="font-size: 0.85rem; color: var(--text-muted); margin-top: 3px;"></div>
                </div>
                <button type="button" class="modal-close" onclick="closeOrderModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div style="background: #f8fafc; border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 15px; margin-bottom: 20px;">
                    <div class="d-flex justify-between" style="font-size: 0.9rem; margin-bottom: 8px;">
                        <span style="color: var(--text-muted);">Kasir Bertugas:</span>
                        <strong id="modalKasirName">-</strong>
                    </div>
                    <div class="d-flex justify-between" style="font-size: 0.9rem;">
                        <span style="color: var(--text-muted);">Metode Pembayaran:</span>
                        <strong id="modalPaymentMethod">-</strong>
                    </div>
                </div>

                <h4 style="font-size: 0.95rem; font-weight: 700; margin-bottom: 12px; color: var(--text-dark);">
                    <i class="fa-solid fa-basket-shopping" style="color: var(--primary-color);"></i> Rincian Menu / Barang
                </h4>

                <div class="table-wrapper" style="margin-bottom: 20px;">
                    <table class="table-modern" style="font-size: 0.9rem;">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th style="text-align: center;">Qty</th>
                                <th style="text-align: right;">Harga</th>
                                <th style="text-align: right;">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody id="modalItemsBody">
                            <!-- Populated via JS -->
                        </tbody>
                    </table>
                </div>

                <div style="border-top: 2px dashed var(--border-color); padding-top: 15px;">
                    <div class="d-flex justify-between" style="font-size: 1.1rem; font-weight: 800; color: var(--text-dark); margin-bottom: 8px;">
                        <span>Total Tagihan:</span>
                        <span id="modalTotalAmount" style="color: var(--primary-color);">Rp 0</span>
                    </div>
                    <div class="d-flex justify-between" style="font-size: 0.95rem; color: var(--text-muted); margin-bottom: 6px;">
                        <span>Uang Diterima:</span>
                        <span id="modalPaidAmount" style="font-weight: 600; color: var(--text-dark);">Rp 0</span>
                    </div>
                    <div class="d-flex justify-between" style="font-size: 0.95rem; color: var(--success); font-weight: 700;">
                        <span>Kembalian:</span>
                        <span id="modalChangeAmount">Rp 0</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-primary" style="width: auto; background: #e2e8f0; color: var(--text-dark);" onclick="closeOrderModal()">Tutup</button>
                <a id="modalPrintBtn" href="#" target="_blank" class="btn-primary" style="width: auto; text-decoration: none;">
                    <i class="fa-solid fa-print"></i> Cetak Ulang Struk
                </a>
            </div>
        </div>
    </div>

    <script>
        function showOrderDetail(data) {
            document.getElementById('modalInvoiceTitle').innerText = data.invoice;
            document.getElementById('modalDateTime').innerText = data.date;
            document.getElementById('modalKasirName').innerText = data.kasir;
            document.getElementById('modalPaymentMethod').innerText = data.payment_method;
            document.getElementById('modalTotalAmount').innerText = 'Rp ' + Number(data.total).toLocaleString('id-ID');
            document.getElementById('modalPaidAmount').innerText = 'Rp ' + Number(data.paid).toLocaleString('id-ID');
            document.getElementById('modalChangeAmount').innerText = 'Rp ' + Number(data.kembalian).toLocaleString('id-ID');
            document.getElementById('modalPrintBtn').href = 'print.php?id=' + data.id;

            const tbody = document.getElementById('modalItemsBody');
            tbody.innerHTML = '';
            if (data.items && data.items.length > 0) {
                data.items.forEach(item => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>
                            <div style="font-weight: 600; color: var(--text-dark);">${item.product_name}</div>
                            <div style="font-size: 0.75rem; color: var(--text-muted);">${item.sku || '-'}</div>
                        </td>
                        <td style="text-align: center; font-weight: 700;">${item.quantity}</td>
                        <td style="text-align: right;">Rp ${Number(item.price).toLocaleString('id-ID')}</td>
                        <td style="text-align: right; font-weight: 700;">Rp ${Number(item.subtotal).toLocaleString('id-ID')}</td>
                    `;
                    tbody.appendChild(tr);
                });
            } else {
                tbody.innerHTML = '<tr><td colspan="4" style="text-align:center; padding: 20px; color: var(--text-muted);">Tidak ada rincian item.</td></tr>';
            }

            document.getElementById('orderModal').classList.add('active');
        }

        function closeOrderModal() {
            document.getElementById('orderModal').classList.remove('active');
        }

        // Close on escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeOrderModal();
        });

        // Close on clicking backdrop
        document.getElementById('orderModal').addEventListener('click', (e) => {
            if (e.target.id === 'orderModal') closeOrderModal();
        });
    </script>
</body>
</html>
