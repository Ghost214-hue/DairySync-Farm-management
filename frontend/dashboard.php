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
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Dashboard | MooManager</title>
    <link href="/frontend/css/output.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gradient-to-br from-emerald-50 via-lime-50 to-emerald-100 min-h-screen">

<div class="flex min-h-screen">
    <?php renderSidebar(); ?>

    <!-- Main content: add pt-20 on mobile to avoid fixed header overlap -->
    <main class="flex-1 min-w-0 p-4 pt-20 md:p-6 md:pt-6 overflow-x-auto">
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between md:items-center gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-bold text-emerald-900">Dashboard</h1>
                <p class="text-emerald-700 mt-1">Welcome back, <?= htmlspecialchars($user['username'] ?? 'Farmer') ?></p>
            </div>
            <div class="bg-white/70 backdrop-blur-sm border border-white/60 px-4 py-2 rounded-xl shadow-sm">
                <span class="text-emerald-800 font-medium"><?= date('F j, Y') ?></span>
            </div>
        </div>

        <!-- Stats Grid (unchanged but Tailwind color classes) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Cows -->
            <div class="stat-card bg-white/80 backdrop-blur-sm rounded-2xl p-5 border border-white/60 shadow-sm hover:shadow-md transition-transform hover:-translate-y-0.5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-emerald-600 mb-1">Total Cows</p>
                        <h2 class="text-3xl font-bold text-emerald-900"><?= number_format($total_cows) ?></h2>
                        <?php if (isset($trends['cows_change'])): ?>
                            <p class="text-xs mt-1 <?= $trends['cows_change'] >= 0 ? 'text-green-600' : 'text-red-500' ?>">
                                <i class="fas fa-arrow-<?= $trends['cows_change'] >= 0 ? 'up' : 'down' ?>"></i>
                                <?= abs($trends['cows_change']) ?> vs last month
                            </p>
                        <?php endif; ?>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-emerald-100 flex items-center justify-center">
                        <i class="fas fa-cow text-emerald-700 text-2xl"></i>
                    </div>
                </div>
            </div>
            <!-- ... other stat cards same structure ... -->
        </div>

        <!-- Charts and Alerts -->
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-8">
            <!-- Milk Chart -->
            <div class="xl:col-span-2 bg-white/80 backdrop-blur-sm rounded-2xl p-6 border border-white/60 shadow-sm">
                <div class="flex items-center justify-between mb-5">
                    <h2 class="text-lg font-bold text-emerald-900">Milk Production (Last 7 Days)</h2>
                    <span class="text-xs text-emerald-600 bg-emerald-50 px-2 py-1 rounded-full">Litres</span>
                </div>
                <canvas id="milkChart" height="110"></canvas>
            </div>
            <!-- Alerts -->
            <div class="bg-white/80 backdrop-blur-sm rounded-2xl p-6 border border-white/60 shadow-sm">
                <h2 class="text-lg font-bold text-emerald-900 mb-5 flex items-center gap-2">
                    <i class="fas fa-bell text-emerald-600"></i> Alerts & Reminders
                </h2>
                <?php if (empty($health_alerts) && empty($upcoming_alerts)): ?>
                    <div class="flex flex-col items-center justify-center py-10 text-center">
                        <div class="w-16 h-16 rounded-full bg-green-100 flex items-center justify-center mb-4">
                            <i class="fas fa-check text-green-600 text-2xl"></i>
                        </div>
                        <p class="font-semibold text-emerald-900">All good!</p>
                        <p class="text-sm text-emerald-600 mt-1">No active alerts or reminders.</p>
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

        <!-- Recent Milk Records and Quick Actions -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent Milk Records -->
            <div class="bg-white/80 backdrop-blur-sm rounded-2xl p-6 border border-white/60 shadow-sm">
                <h2 class="text-lg font-bold text-emerald-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-history text-emerald-600"></i> Recent Milk Records
                </h2>
                <?php if (empty($recent_milks)): ?>
                    <p class="text-emerald-500 text-center py-6">No milk records yet today.</p>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="text-emerald-700 border-b border-emerald-200">
                                <tr><th class="text-left py-2">Cow</th><th>Quantity</th><th>Session</th><th>Date</th></tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_milks as $milk): ?>
                                <tr class="border-b border-emerald-100">
                                    <td class="py-2 font-medium"><?= htmlspecialchars($milk['cow_name']) ?></td>
                                    <td><?= number_format($milk['quantity'], 1) ?> L</td>
                                    <td><?= htmlspecialchars($milk['session']) ?></td>
                                    <td class="text-emerald-600"><?= date('M d', strtotime($milk['production_date'])) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
                <div class="mt-4 text-right">
                    <a href="<?= UrlHelper::url('milk_production') ?>" class="text-sm text-emerald-700 hover:underline">View all →</a>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white/80 backdrop-blur-sm rounded-2xl p-6 border border-white/60 shadow-sm">
                <h2 class="text-lg font-bold text-emerald-900 mb-5">Quick Actions</h2>
                <div class="grid grid-cols-2 gap-4">
                    <a href="<?= UrlHelper::url('cows') ?>" class="group bg-emerald-50 hover:bg-emerald-100 transition rounded-2xl p-5 text-center">
                        <i class="fas fa-cow text-3xl text-emerald-700 mb-3"></i>
                        <p class="font-medium text-emerald-900">Manage Cows</p>
                    </a>
                    <!-- Other action buttons -->
                </div>
            </div>
        </div>
    </main>
</div>

<script>
const ctx = document.getElementById('milkChart');
if (ctx) {
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
}
</script>
</body>
</html>
