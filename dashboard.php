<?php
require_once '../auth.php';
checkAuth('owner');
require_once '../config.php';

// Fetch KPIs
$today = date('Y-m-d');
$salesToday = $pdo->query("SELECT SUM(total_amount) FROM transactions WHERE DATE(created_at) = '$today'")->fetchColumn() ?: 0;
$totalSales = $pdo->query("SELECT SUM(total_amount) FROM transactions")->fetchColumn() ?: 0;
$totalTransactions = $pdo->query("SELECT COUNT(*) FROM transactions WHERE DATE(created_at) = '$today'")->fetchColumn();

// Fetch Top Products
$topProducts = $pdo->query("SELECT p.name, SUM(td.quantity) as sold_qty FROM transaction_details td JOIN products p ON td.product_id = p.id GROUP BY p.id ORDER BY sold_qty DESC LIMIT 5")->fetchAll();

// Prediction Simple Moving Average (SMA) last 7 days
// To predict tomorrow's sale
$last7DaysSales = $pdo->query("SELECT SUM(total_amount) as daily_total FROM transactions WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) GROUP BY DATE(created_at)")->fetchAll(PDO::FETCH_COLUMN);

$prediction = 0;
if (count($last7DaysSales) > 0) {
    $prediction = array_sum($last7DaysSales) / count($last7DaysSales);
}

// Data for Chart (Last 7 Days)
$chartData = $pdo->query("SELECT DATE(created_at) as date, SUM(total_amount) as total FROM transactions WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) GROUP BY DATE(created_at) ORDER BY date ASC")->fetchAll();
$labels = [];
$totals = [];
foreach($chartData as $row) {
    $labels[] = date('d M', strtotime($row['date']));
    $totals[] = $row['total'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Owner Dashboard - SmartKasir</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="dashboard-layout">
        <div class="sidebar">
            <div class="sidebar-header"><i class="fa-solid fa-cube"></i> SmartKasir</div>
            <ul class="sidebar-menu">
                <li><a href="dashboard.php" class="active"><i class="fa-solid fa-chart-pie"></i> Analitik Bisnis</a></li>
                <li><a href="reports.php"><i class="fa-solid fa-file-invoice"></i> Laporan Keuangan</a></li>
            </ul>
            <div style="padding: 20px; border-top: 1px solid var(--border-color);">
                <a href="../logout.php" class="badge badge-danger" style="width: 100%; justify-content: center; padding: 10px;"><i class="fa-solid fa-power-off"></i> Logout Akses</a>
            </div>
        </div>
        <div class="main-content">
            <div class="topbar">
                <h2>RINGKASAN EKSEKUTIF</h2>
                <div class="user-info">
                    <img src="https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['user_name']) ?>&background=f59e0b&color=fff" alt="Avatar">
                    <?= htmlspecialchars($_SESSION['user_name']) ?> (Owner)
                </div>
            </div>
            <div class="content-area">
                
                <div class="kpi-grid">
                    <div class="kpi-card success">
                        <i class="fa-solid fa-wallet bg-icon"></i>
                        <div class="kpi-title"><i class="fa-solid fa-money-bill-trend-up"></i> Pendapatan Hari Ini</div>
                        <div class="kpi-value" style="color: var(--success);">Rp <?= number_format($salesToday, 0, ',', '.') ?></div>
                    </div>
                    <div class="kpi-card" style="border-bottom-color: var(--primary-light);">
                        <i class="fa-solid fa-vault bg-icon"></i>
                        <div class="kpi-title"><i class="fa-solid fa-piggy-bank"></i> Total Akumulasi Pendapatan</div>
                        <div class="kpi-value">Rp <?= number_format($totalSales, 0, ',', '.') ?></div>
                    </div>
                    <div class="kpi-card" style="border-bottom-color: var(--primary-light);">
                        <i class="fa-solid fa-users bg-icon"></i>
                        <div class="kpi-title"><i class="fa-solid fa-bag-shopping"></i> Pelanggan Hari Ini</div>
                        <div class="kpi-value"><?= $totalTransactions ?></div>
                    </div>
                    <div class="kpi-card warning">
                        <i class="fa-solid fa-rocket bg-icon"></i>
                        <div class="kpi-title"><i class="fa-solid fa-wand-magic-sparkles"></i> AI Prediksi Esok (SMA)</div>
                        <div class="kpi-value">Rp <?= number_format($prediction, 0, ',', '.') ?></div>
                    </div>
                </div>

                <div class="d-flex" style="gap: 30px;">
                    <div class="card" style="flex: 2;">
                        <h3 class="mb-4"><i class="fa-solid fa-chart-area"></i> Tren Pendapatan 7 Hari Terakhir</h3>
                        <div style="position: relative; height: 320px; width: 100%;">
                            <canvas id="salesChart"></canvas>
                        </div>
                    </div>
                    <div class="card" style="flex: 1;">
                        <h3 class="mb-4"><i class="fa-solid fa-crown" style="color: var(--warning);"></i> 5 Produk Terlaris</h3>
                        <ul style="padding-left: 0; list-style:none;">
                            <?php foreach($topProducts as $idx => $tp): ?>
                                <?php $medalColor = ($idx==0)? '#fbbf24': (($idx==1)? '#94a3b8': (($idx==2)? '#b45309' : 'var(--primary-color)')); ?>
                                <li class="d-flex justify-between align-center" style="padding: 12px 0; border-bottom: 1px dashed var(--border-color);">
                                    <div class="d-flex align-center gap-3">
                                        <i class="fa-solid fa-medal" style="color: <?= $medalColor ?>; font-size:1.2rem;"></i>
                                        <span style="font-weight: 600; color:var(--text-dark);"><?= htmlspecialchars($tp['name']) ?></span>
                                    </div>
                                    <span class="badge badge-success"><?= $tp['sold_qty'] ?> Terjual</span>
                                </li>
                            <?php endforeach; ?>
                            <?php if(empty($topProducts)): ?>
                                <li style="color: var(--text-muted); text-align:center; padding: 20px;">Belum ada data penjualan tersedia.</li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        const ctx = document.getElementById('salesChart').getContext('2d');
        const salesChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?= json_encode($labels) ?>,
                datasets: [{
                    label: 'Pendapatan Harian (Rp)',
                    data: <?= json_encode($totals) ?>,
                    borderColor: '#4f46e5',
                    backgroundColor: 'rgba(79, 70, 229, 0.1)',
                    pointBackgroundColor: '#4f46e5',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true, grid: { borderDash: [2, 4], color: '#e2e8f0' } },
                    x: { grid: { display: false } }
                }
            }
        });
    </script>
</body>
</html>
