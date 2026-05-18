<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../backend/auth/dashboard.php';
require_once __DIR__ . '/components/sidebar.php';
require_once __DIR__ . '/../router/urlHelper.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | MooManager</title>
    <link href="/farm-management/frontend/css/output.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* subtle custom transition */
        .stat-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 20px -10px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body class="bg-gradient-to-br from-farm-green-50 via-farm-olive-50 to-farm-green-100 min-h-screen">

<div class="flex min-h-screen">
    <?php renderSidebar(); ?>

    <main class="flex-1 p-6 overflow-x-hidden">
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between md:items-center gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-bold text-farm-green-900">Dashboard</h1>
                <p class="text-farm-green-700 mt-1">
                    Welcome back, <?= htmlspecialchars($user['username'] ?? 'Farmer') ?>
                </p>
            </div>
            <div class="bg-white/70 backdrop-blur-sm border border-white/60 px-4 py-2 rounded-xl shadow-sm">
                <span class="text-farm-green-800 font-medium"><?= date('F j, Y') ?></span>
            </div>
        </div>

        <!-- Stats Cards with trends -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Cows -->
            <div class="stat-card bg-white/80 backdrop-blur-sm rounded-2xl p-5 border border-white/60 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-farm-green-600 mb-1">Total Cows</p>
                        <h2 class="text-3xl font-bold text-farm-green-900"><?= number_format($total_cows) ?></h2>
                        <?php if (isset($trends['cows_change'])): ?>
                            <p class="text-xs mt-1 <?= $trends['cows_change'] >= 0 ? 'text-green-600' : 'text-red-500' ?>">
                                <i class="fas fa-arrow-<?= $trends['cows_change'] >= 0 ? 'up' : 'down' ?>"></i>
                                <?= abs($trends['cows_change']) ?> vs last month
                            </p>
                        <?php endif; ?>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-farm-green-100 flex items-center justify-center">
                        <i class="fas fa-cow text-farm-green-700 text-2xl"></i>
                    </div>
                </div>
            </div>

            <!-- Milk Today + avg per cow -->
            <div class="stat-card bg-white/80 backdrop-blur-sm rounded-2xl p-5 border border-white/60 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-farm-green-600 mb-1">Milk Today</p>
                        <h2 class="text-3xl font-bold text-farm-green-900"><?= number_format($today_milk, 1) ?> L</h2>
                        <?php if (isset($avg_milk_per_cow)): ?>
                            <p class="text-xs text-farm-green-600 mt-1">Ø <?= number_format($avg_milk_per_cow, 1) ?> L / cow</p>
                        <?php endif; ?>
                        <?php if (isset($trends['milk_change'])): ?>
                            <p class="text-xs <?= $trends['milk_change'] >= 0 ? 'text-green-600' : 'text-red-500' ?>">
                                <i class="fas fa-arrow-<?= $trends['milk_change'] >= 0 ? 'up' : 'down' ?>"></i>
                                <?= abs($trends['milk_change']) ?>% vs yesterday
                            </p>
                        <?php endif; ?>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-blue-100 flex items-center justify-center">
                        <i class="fas fa-tint text-blue-600 text-2xl"></i>
                    </div>
                </div>
            </div>

            <!-- Net Profit (Income - Expenses) -->
            <div class="stat-card bg-white/80 backdrop-blur-sm rounded-2xl p-5 border border-white/60 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-farm-green-600 mb-1">Net Profit</p>
                        <h2 class="text-3xl font-bold text-farm-green-900">KSh <?= number_format($net_profit) ?></h2>
                        <p class="text-xs text-farm-green-600 mt-1">Income: KSh <?= number_format($total_income) ?> | Expenses: KSh <?= number_format($total_expenses) ?></p>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-yellow-100 flex items-center justify-center">
                        <i class="fas fa-chart-line text-yellow-600 text-2xl"></i>
                    </div>
                </div>
            </div>

            <!-- Feed Cost (month) -->
            <div class="stat-card bg-white/80 backdrop-blur-sm rounded-2xl p-5 border border-white/60 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-farm-green-600 mb-1">Feed Cost (This Month)</p>
                        <h2 class="text-3xl font-bold text-farm-green-900">KSh <?= number_format($feed_cost_month) ?></h2>
                        <p class="text-xs text-farm-green-600 mt-1">last 30 days</p>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-purple-100 flex items-center justify-center">
                        <i class="fas fa-seedling text-purple-600 text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts + Alerts + Recent Activity (3 columns on large) -->
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-8">
            <!-- Milk Production Chart (weekly) -->
            <div class="xl:col-span-2 bg-white/80 backdrop-blur-sm rounded-2xl p-6 border border-white/60 shadow-sm">
                <div class="flex items-center justify-between mb-5">
                    <h2 class="text-lg font-bold text-farm-green-900">Milk Production (Last 7 Days)</h2>
                    <span class="text-xs text-farm-green-600 bg-farm-green-50 px-2 py-1 rounded-full">Litres</span>
                </div>
                <canvas id="milkChart" height="110"></canvas>
            </div>

            <!-- Smart Alerts & Reminders -->
            <div class="bg-white/80 backdrop-blur-sm rounded-2xl p-6 border border-white/60 shadow-sm">
                <h2 class="text-lg font-bold text-farm-green-900 mb-5 flex items-center gap-2">
                    <i class="fas fa-bell text-farm-green-600"></i> Alerts & Reminders
                </h2>
                <?php if (empty($health_alerts) && empty($upcoming_alerts)): ?>
                    <div class="flex flex-col items-center justify-center py-10 text-center">
                        <div class="w-16 h-16 rounded-full bg-green-100 flex items-center justify-center mb-4">
                            <i class="fas fa-check text-green-600 text-2xl"></i>
                        </div>
                        <p class="font-semibold text-farm-green-900">All good!</p>
                        <p class="text-sm text-farm-green-600 mt-1">No active alerts or reminders.</p>
                    </div>
                <?php else: ?>
                    <div class="space-y-3 max-h-80 overflow-y-auto pr-1">
                        <?php foreach ($health_alerts as $alert): ?>
                            <div class="p-3 rounded-xl bg-red-50 border-l-4 border-red-400 shadow-sm">
                                <div class="flex items-start gap-3">
                                    <i class="fas fa-exclamation-triangle text-red-500 mt-0.5"></i>
                                    <p class="text-sm font-medium text-red-700"><?= htmlspecialchars($alert) ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <?php foreach ($upcoming_alerts as $reminder): ?>
                            <div class="p-3 rounded-xl bg-amber-50 border-l-4 border-amber-400 shadow-sm">
                                <div class="flex items-start gap-3">
                                    <i class="fas fa-calendar-alt text-amber-600 mt-0.5"></i>
                                    <p class="text-sm font-medium text-amber-700"><?= htmlspecialchars($reminder) ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Recent Activity: Milk Records & Quick Actions -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Recent Milk Records -->
            <div class="bg-white/80 backdrop-blur-sm rounded-2xl p-6 border border-white/60 shadow-sm">
                <h2 class="text-lg font-bold text-farm-green-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-history text-farm-green-600"></i> Recent Milk Records
                </h2>
                <?php if (empty($recent_milks)): ?>
                    <p class="text-farm-green-500 text-center py-6">No milk records yet today.</p>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="text-farm-green-700 border-b border-farm-green-200">
                                <tr>
                                    <th class="text-left py-2">Cow</th>
                                    <th class="text-left">Quantity</th>
                                    <th class="text-left">Session</th>
                                    <th class="text-left">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_milks as $milk): ?>
                                <tr class="border-b border-farm-green-100">
                                    <td class="py-2 font-medium"><?= htmlspecialchars($milk['cow_name']) ?></td>
                                    <td><?= number_format($milk['quantity'], 1) ?> L</td>
                                    <td><?= htmlspecialchars($milk['session']) ?></td>
                                    <td class="text-farm-green-600"><?= date('M d', strtotime($milk['production_date'])) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
                <div class="mt-4 text-right">
                    <a href="<?= UrlHelper::url('milk_production') ?>" class="text-sm text-farm-green-700 hover:underline">View all →</a>
                </div>
            </div>

            <!-- Quick Actions (stay same but modern) -->
            <div class="bg-white/80 backdrop-blur-sm rounded-2xl p-6 border border-white/60 shadow-sm">
                <h2 class="text-lg font-bold text-farm-green-900 mb-5">Quick Actions</h2>
                <div class="grid grid-cols-2 sm:grid-cols-2 gap-4">
                    <a href="<?= UrlHelper::url('cows') ?>" class="group bg-farm-green-50 hover:bg-farm-green-100 transition rounded-2xl p-5 text-center">
                        <i class="fas fa-cow text-3xl text-farm-green-700 mb-3"></i>
                        <p class="font-medium text-farm-green-900">Manage Cows</p>
                    </a>
                    <a href="<?= UrlHelper::url('health') ?>" class="group bg-red-50 hover:bg-red-100 transition rounded-2xl p-5 text-center">
                        <i class="fas fa-heartbeat text-3xl text-red-500 mb-3"></i>
                        <p class="font-medium text-red-700">Health Records</p>
                    </a>
                    <a href="<?= UrlHelper::url('milk_production') ?>" class="group bg-blue-50 hover:bg-blue-100 transition rounded-2xl p-5 text-center">
                        <i class="fas fa-tint text-3xl text-blue-600 mb-3"></i>
                        <p class="font-medium text-blue-700">Milk Production</p>
                    </a>
                    <a href="<?= UrlHelper::url('income') ?>" class="group bg-yellow-50 hover:bg-yellow-100 transition rounded-2xl p-5 text-center">
                        <i class="fas fa-coins text-3xl text-yellow-600 mb-3"></i>
                        <p class="font-medium text-yellow-700">Income</p>
                    </a>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
const ctx = document.getElementById('milkChart');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?= json_encode($milk_chart_labels) ?>,
        datasets: [{
            label: 'Milk (Litres)',
            data: <?= json_encode($milk_chart_data) ?>,
            borderColor: '#166534',
            backgroundColor: 'rgba(22, 101, 52, 0.08)',
            tension: 0.4,
            fill: true,
            pointBackgroundColor: '#15803d',
            pointBorderColor: '#fff',
            pointRadius: 4,
            pointHoverRadius: 6
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false }, tooltip: { callbacks: { label: (ctx) => `${ctx.raw} L` } } },
        scales: { y: { beginAtZero: true, title: { display: true, text: 'Litres' } } }
    }
});
</script>
</body>
</html>