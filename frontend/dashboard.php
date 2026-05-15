<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../backend/auth/dashboard.php';
require_once __DIR__ . '/components/sidebar.php';
require_once __DIR__ . '/../router/urlHelper.php';

$today_milk = 0.0;     // litres
$total_income = 0;     // dollars
$healthy_cows = $total_cows ?? 0;
$health_alerts = [];  
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | MooManager</title>
    <link href="/farm-management/frontend/css/output.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Chart.js for the milk production chart (optional) -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</head>
<body class="bg-gradient-to-br from-farm-green-50 via-farm-olive-50 to-farm-green-100 min-h-screen">
    <div class="flex">
        <!-- Sidebar Component -->
        <?php renderSidebar(); ?>

        <!-- Main Content -->
        <main class="flex-1 p-6">
            <!-- Top Bar -->
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-farm-green-900">Dashboard</h1>
                    <p class="text-farm-green-700">Welcome back! Here's what's happening on your farm.</p>
                </div>
                <div class="glass-card px-4 py-2">
                    <i class="fas fa-bell text-farm-green-700"></i>
                    <span class="ml-2 text-farm-green-800"><?= date('F j, Y') ?></span>
                </div>
            </div>

            <!-- Stats Cards Row -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="glass-card p-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-farm-green-600 text-sm">Total Cows</p>
                            <p class="text-3xl font-bold text-farm-green-900"><?= $total_cows ?></p>
                        </div>
                        <i class="fas fa-paw text-4xl text-farm-green-400"></i>
                    </div>
                </div>
                <div class="glass-card p-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-farm-green-600 text-sm">Milk Today</p>
                            <p class="text-3xl font-bold text-farm-green-900"><?= number_format($today_milk, 1) ?> L</p>
                        </div>
                        <i class="fas fa-tint text-4xl text-farm-blue-400"></i>
                    </div>
                </div>
                <div class="glass-card p-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-farm-green-600 text-sm">Total Income</p>
                            <p class="text-3xl font-bold text-farm-green-900">$<?= number_format($total_income) ?></p>
                        </div>
                        <i class="fas fa-dollar-sign text-4xl text-green-500"></i>
                    </div>
                </div>
                <div class="glass-card p-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-farm-green-600 text-sm">Healthy Cows</p>
                            <p class="text-3xl font-bold text-farm-green-900"><?= $healthy_cows ?></p>
                        </div>
                        <i class="fas fa-heartbeat text-4xl text-red-400"></i>
                    </div>
                </div>
            </div>

            <!-- Chart and Health Alerts Row -->
            <div class="grid md:grid-cols-3 gap-6 mb-8">
                <!-- Milk Production Chart (Last 7 days) -->
                <div class="glass-card p-5 md:col-span-2">
                    <h3 class="text-lg font-semibold text-farm-green-900 mb-4">Milk Production (Last 7 Days)</h3>
                    <canvas id="milkChart" height="200"></canvas>
                </div>
                <!-- Recent Health Alerts -->
                <div class="glass-card p-5">
                    <h3 class="text-lg font-semibold text-farm-green-900 mb-4">Recent Health Alerts</h3>
                    <?php if (empty($health_alerts)): ?>
                        <div class="text-center py-8">
                            <i class="fas fa-check-circle text-green-500 text-4xl mb-2"></i>
                            <p class="text-farm-green-700">All Cows Healthy!</p>
                            <p class="text-sm text-farm-green-500 mt-1">No health alerts at the moment.</p>
                        </div>
                    <?php else: ?>
                        <ul class="space-y-2">
                            <?php foreach ($health_alerts as $alert): ?>
                                <li class="p-2 bg-red-50/50 rounded-lg text-red-700 text-sm">
                                    <i class="fas fa-exclamation-triangle mr-2"></i> <?= htmlspecialchars($alert) ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Quick Actions Row -->
            <div class="glass-card p-5">
                <h3 class="text-lg font-semibold text-farm-green-900 mb-4">Quick Actions</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <a href="<?= UrlHelper::url('cows', ['action' => 'record_milk']) ?>" class="text-center p-4 rounded-xl bg-white/30 hover:bg-white/50 transition">
                        <i class="fas fa-tint text-farm-green-600 text-2xl mb-2 block"></i>
                        <span class="text-sm text-farm-green-800">Record Milk</span>
                    </a>
                    <a href="<?= UrlHelper::url('health', ['action' => 'checkup']) ?>" class="text-center p-4 rounded-xl bg-white/30 hover:bg-white/50 transition">
                        <i class="fas fa-stethoscope text-farm-green-600 text-2xl mb-2 block"></i>
                        <span class="text-sm text-farm-green-800">Health Check</span>
                    </a>
                    <a href="#" class="text-center p-4 rounded-xl bg-white/30 hover:bg-white/50 transition">
                        <i class="fas fa-plus-circle text-red-500 text-2xl mb-2 block"></i>
                        <span class="text-sm text-farm-green-800">Add Expense</span>
                    </a>
                    <a href="#" class="text-center p-4 rounded-xl bg-white/30 hover:bg-white/50 transition">
                        <i class="fas fa-hand-holding-usd text-green-600 text-2xl mb-2 block"></i>
                        <span class="text-sm text-farm-green-800">Add Income</span>
                    </a>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Dummy data for the chart – replace with PHP-generated array from backend
        const ctx = document.getElementById('milkChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Day 1', 'Day 2', 'Day 3', 'Day 4', 'Day 5', 'Day 6', 'Today'],
                datasets: [{
                    label: 'Milk (Litres)',
                    data: [0, 0, 0, 0, 0, 0, <?= $today_milk ?>],
                    borderColor: '#2d6a4f',
                    backgroundColor: 'rgba(45, 106, 79, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { position: 'top' }
                }
            }
        });
    </script>
</body>
</html>