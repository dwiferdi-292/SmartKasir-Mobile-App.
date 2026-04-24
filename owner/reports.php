<?php
require_once '../auth.php';
checkAuth('owner');
require_once '../config.php';

$startDate = $_GET['start_date'] ?? date('Y-m-01');
$endDate = $_GET['end_date'] ?? date('Y-m-d');

$stmt = $pdo->prepare("SELECT t.*, u.name as kasir_name FROM transactions t JOIN users u ON t.user_id = u.id WHERE DATE(t.created_at) >= ? AND DATE(t.created_at) <= ? ORDER BY t.created_at DESC");
$stmt->execute([$startDate, $endDate]);
$transactions = $stmt->fetchAll();

$totalPeriod = 0;
foreach($transactions as $t) {
    $totalPeriod += $t['total_amount'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Laporan Transaksi - SmartKasir</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        @media print {
            .sidebar, .topbar, .filter-form, .btn-print { display: none !important; }
            .dashboard-layout { display: block; }
            .main-content { margin-left: 0; background: #fff;}
            .card { box-shadow: none; border: none; padding: 0;}
            table.table-modern th { background: #f1f5f9; color: #000; -webkit-print-color-adjust: exact; }
            .badge { border: 1px solid #000; color: #000; background: transparent; -webkit-print-color-adjust: exact; }
        }
    </style>
</head>
<body>
    <div class="dashboard-layout">
        <div class="sidebar">
            <div class="sidebar-header"><i class="fa-solid fa-cube"></i> SmartKasir</div>
            <ul class="sidebar-menu">
                <li><a href="dashboard.php"><i class="fa-solid fa-chart-pie"></i> Analitik Bisnis</a></li>
                <li><a href="reports.php" class="active"><i class="fa-solid fa-file-invoice"></i> Laporan Keuangan</a></li>
            </ul>
            <div style="padding: 20px; border-top: 1px solid var(--border-color);">
                <a href="../logout.php" class="badge badge-danger" style="width: 100%; justify-content: center; padding: 10px;"><i class="fa-solid fa-power-off"></i> Logout Akses</a>
            </div>
        </div>
        <div class="main-content">
            <div class="topbar">
                <h2>LAPORAN KEUANGAN</h2>
                <div class="user-info">
                    <img src="https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['user_name']) ?>&background=f59e0b&color=fff" alt="Avatar">
                    <?= htmlspecialchars($_SESSION['user_name']) ?> (Owner)
                </div>
            </div>
            <div class="content-area">
                <div class="card filter-form">
                    <form action="reports.php" method="GET" class="d-flex align-center gap-4">
                        <div style="flex: 1;">
                            <label style="font-weight: 600; font-size: 0.9rem; color: var(--text-muted);"><i class="fa-solid fa-calendar-days"></i> Dari Tanggal</label>
                            <input type="date" name="start_date" value="<?= htmlspecialchars($startDate) ?>" required class="search-bar" style="margin-bottom:0; padding:12px; margin-top:5px;">
                        </div>
                        <div style="flex: 1;">
                            <label style="font-weight: 600; font-size: 0.9rem; color: var(--text-muted);"><i class="fa-solid fa-calendar-days"></i> Hingga Tanggal</label>
                            <input type="date" name="end_date" value="<?= htmlspecialchars($endDate) ?>" required class="search-bar" style="margin-bottom:0; padding:12px; margin-top:5px;">
                        </div>
                        <div style="padding-top: 25px;">
                            <button type="submit" class="btn-primary" style="padding: 13px 20px;"><i class="fa-solid fa-filter"></i> Terapkan Filter</button>
                        </div>
                        <div style="padding-top: 25px; margin-left: auto;">
                            <button type="button" onclick="window.print()" class="btn-primary btn-print" style="background: var(--text-dark); padding: 13px 20px;"><i class="fa-solid fa-print"></i> Cetak Dokumen PDF</button>
                        </div>
                    </form>
                </div>

                <div class="card">
                    <h3 class="mb-2"><i class="fa-solid fa-book"></i> Riwayat Transaksi Penjualan</h3>
                    <p style="color: var(--text-muted); margin-bottom: 20px;">Periode: <?= date('d M Y', strtotime($startDate)) ?> s/d <?= date('d M Y', strtotime($endDate)) ?></p>
                    
                    <div style="background: var(--primary-light); border-left: 4px solid var(--primary-color); padding: 20px; border-radius: var(--radius-md); margin-bottom: 25px;">
                        <h4 style="color: var(--primary-color); margin-bottom: 5px;">Total Pendapatan Bersih Periode Ini</h4>
                        <div style="font-size: 2rem; font-weight: 800; color: var(--text-dark);">Rp <?= number_format($totalPeriod, 0, ',', '.') ?></div>
                    </div>
                    
                    <div class="table-wrapper">
                        <table class="table-modern">
                            <thead>
                                <tr>
                                    <th>Waktu Transaksi</th>
                                    <th>No. Invoice</th>
                                    <th>Kasir Bertugas</th>
                                    <th>Metode Bayar</th>
                                    <th>Total Tagihan (Rp)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($transactions as $t): ?>
                                <tr>
                                    <td>
                                        <div style="font-weight: 600; color:var(--text-dark);"><?= date('d M Y', strtotime($t['created_at'])) ?></div>
                                        <div style="font-size: 0.8rem; color:var(--text-muted);"><i class="fa-regular fa-clock"></i> <?= date('H:i', strtotime($t['created_at'])) ?> WIB</div>
                                    </td>
                                    <td><span class="badge badge-primary"><?= htmlspecialchars($t['invoice_number']) ?></span></td>
                                    <td><i class="fa-solid fa-user-tag" style="color: #94a3b8;"></i> <?= htmlspecialchars($t['kasir_name']) ?></td>
                                    <td>
                                        <?php if($t['payment_method']=='Cash'): ?>
                                            <span class="badge badge-success"><i class="fa-solid fa-money-bill"></i> Tunai</span>
                                        <?php else: ?>
                                            <span class="badge badge-warning"><i class="fa-brands fa-cc-visa"></i> Non-Tunai</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="font-weight: 700;">Rp <?= number_format($t['total_amount'], 0, ',', '.') ?></td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if(empty($transactions)): ?>
                                    <tr><td colspan="5" style="text-align:center; padding: 40px; color: var(--text-muted);"><i class="fa-solid fa-folder-open" style="font-size: 2rem; margin-bottom: 10px; display:block;"></i> Tidak ada transaksi tercatat pada periode ini.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
