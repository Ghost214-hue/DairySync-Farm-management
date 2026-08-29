<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../backend/pages/cow_profile.php';
require_once __DIR__ . '/../components/sidebar.php';
require_once __DIR__ . '/../../router/urlHelper.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cow Profile - #<?= htmlspecialchars($cow['ear_tag']) ?> | MooManager</title>
    <link href="/frontend/css/output.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
</head>
<body class="bg-gradient-to-br from-farm-green-50 via-farm-olive-50 to-farm-green-100 min-h-screen">
<div class="flex">
    <?php renderSidebar(); ?>

    <main class="flex-1 min-w-0 p-4 pt-20 md:p-6 md:pt-6">
        <!-- Back button -->
        <div class="mb-4">
            <a href="<?= UrlHelper::url('cows') ?>" class="inline-flex items-center gap-2 text-farm-green-700 hover:text-farm-green-900 text-sm font-medium">
                <i class="fas fa-arrow-left"></i> Back to Cows
            </a>
        </div>

        <!-- Cow Header Card -->
        <div class="bg-white/70 backdrop-blur-sm rounded-2xl shadow-sm border border-white/60 p-6 mb-6">
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <!-- Cow Avatar / Image -->
                    <?php if (!empty($cow['image_path'])): ?>
                    <div style="width: 96px; height: 96px; min-width: 96px; min-height: 96px;"
                         class="rounded-full overflow-hidden border-2 border-farm-green-200 shadow-sm flex-shrink-0">
                        <img src="<?= htmlspecialchars($cow['image_path']) ?>" 
                             alt="Photo of <?= htmlspecialchars($cow['cow_name'] ?? 'cow') ?>"
                             style="width: 96px; height: 96px; object-fit: cover; border-radius: 9999px;">
                    </div>
                    <?php else: ?>
                    <div class="w-20 h-20 bg-farm-green-100 rounded-full flex items-center justify-center text-farm-green-700 text-3xl flex-shrink-0">
                        <i class="fas fa-cow"></i>
                    </div>
                    <?php endif; ?>
                    <div>
                        <h1 class="text-2xl font-bold text-farm-green-900">
                            #<?= htmlspecialchars($cow['ear_tag']) ?> - <?= htmlspecialchars($cow['cow_name'] ?? 'Unnamed') ?>
                        </h1>
                        <p class="text-farm-green-600 text-sm mt-1"><?= htmlspecialchars($farm['farm_name'] ?? '') ?></p>
                        <div class="flex flex-wrap gap-2 mt-2">
                            <span class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1 rounded-full bg-blue-100 text-blue-700">
                                <?= htmlspecialchars($cow['breed']) ?>
                            </span>
                            <span class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1 rounded-full bg-purple-100 text-purple-700">
                                <?= htmlspecialchars($cow['gender']) ?>
                            </span>
                            <span class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1 rounded-full 
                                <?php
                                    $status_colors = [
                                        'Active' => 'bg-green-100 text-green-700',
                                        'Dry' => 'bg-yellow-100 text-yellow-700',
                                        'Pregnant' => 'bg-pink-100 text-pink-700',
                                        'Sick' => 'bg-red-100 text-red-700',
                                        'Sold' => 'bg-gray-100 text-gray-600',
                                        'Deceased' => 'bg-gray-200 text-gray-500',
                                    ];
                                    echo $status_colors[$cow['status']] ?? 'bg-gray-100 text-gray-600';
                                ?>">
                                <?= htmlspecialchars($cow['status']) ?>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- QR Code Section -->
                <div class="flex flex-col items-center gap-2">
                    <div id="qrcode" class="bg-white p-2 rounded-lg shadow-sm"></div>
                    <p class="text-xs text-farm-green-600 font-medium">Scan to view profile</p>
                    <button onclick="downloadQR()" class="text-xs bg-farm-green-700 hover:bg-farm-green-800 text-white px-3 py-1.5 rounded-lg transition">
                        <i class="fas fa-download mr-1"></i> Download QR
                    </button>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <!-- Total Milk -->
            <div class="bg-white/70 backdrop-blur-sm rounded-xl shadow-sm border border-white/60 p-5">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center text-blue-700">
                        <i class="fas fa-glass-water text-xl"></i>
                    </div>
                    <div>
                        <p class="text-xs text-farm-green-600 font-medium uppercase tracking-wide">Total Milk</p>
                        <p class="text-2xl font-bold text-farm-green-900"><?= number_format($total_milk, 1) ?> <span class="text-sm font-normal">L</span></p>
                    </div>
                </div>
            </div>

            <!-- Average Daily -->
            <div class="bg-white/70 backdrop-blur-sm rounded-xl shadow-sm border border-white/60 p-5">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center text-purple-700">
                        <i class="fas fa-chart-line text-xl"></i>
                    </div>
                    <div>
                        <p class="text-xs text-farm-green-600 font-medium uppercase tracking-wide">Avg Daily</p>
                        <p class="text-2xl font-bold text-farm-green-900"><?= number_format($avg_daily, 1) ?> <span class="text-sm font-normal">L</span></p>
                    </div>
                </div>
            </div>

            <!-- Records Count -->
            <div class="bg-white/70 backdrop-blur-sm rounded-xl shadow-sm border border-white/60 p-5">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center text-green-700">
                        <i class="fas fa-file-medical text-xl"></i>
                    </div>
                    <div>
                        <p class="text-xs text-farm-green-600 font-medium uppercase tracking-wide">Health Records</p>
                        <p class="text-2xl font-bold text-farm-green-900"><?= count($health_records) ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Milk Production Graph -->
        <div class="bg-white/70 backdrop-blur-sm rounded-2xl shadow-sm border border-white/60 p-6 mb-6">
            <h2 class="text-lg font-bold text-farm-green-900 mb-4">Milk Production Summary</h2>
            <?php if (!empty($milk_summary)): ?>
                <div style="height: 300px; overflow: hidden;">
                    <canvas id="milkChart"></canvas>
                </div>
            <?php else: ?>
                <p class="text-farm-green-500 text-center py-8">No milk production records yet</p>
            <?php endif; ?>
        </div>

        <!-- Recent Milk Production Records (Last 2 Months) -->
        <div class="bg-white/70 backdrop-blur-sm rounded-2xl shadow-sm border border-white/60 p-6 mb-6">
            <div class="flex justify-between items-center flex-wrap gap-2 mb-4">
                <h2 class="text-lg font-bold text-farm-green-900">Recent Milk Records</h2>
                <span class="text-xs text-farm-green-500">Last 60 days</span>
            </div>
            <?php if (empty($recent_milk)): ?>
                <p class="text-farm-green-500 text-center py-8">No milk records in the last 60 days for this cow.</p>
            <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-farm-green-50 text-farm-green-700 text-xs uppercase tracking-wider">
                        <tr>
                            <th class="px-4 py-3 text-left">Date</th>
                            <th class="px-4 py-3 text-right">Morning (L)</th>
                            <th class="px-4 py-3 text-right">Evening (L)</th>
                            <th class="px-4 py-3 text-right">Total (L)</th>
                            <th class="px-4 py-3 text-left">Notes</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-farm-green-50">
                        <?php foreach ($recent_milk as $rec): ?>
                            <tr class="hover:bg-farm-green-50/50 transition">
                                <td class="px-4 py-3 text-farm-green-800"><?= date('M j, Y', strtotime($rec['production_date'])) ?></td>
                                <td class="px-4 py-3 text-right"><?= number_format($rec['morning_litres'], 1) ?></td>
                                <td class="px-4 py-3 text-right"><?= number_format($rec['evening_litres'], 1) ?></td>
                                <td class="px-4 py-3 text-right font-semibold text-emerald-700"><?= number_format($rec['total_litres'], 1) ?></td>
                                <td class="px-4 py-3 text-farm-green-600 text-xs"><?= $rec['notes'] ? htmlspecialchars($rec['notes']) : '—' ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr class="bg-farm-green-50 font-bold text-farm-green-900">
                            <td class="px-4 py-3">Total (<?= count($recent_milk) ?> days)</td>
                            <td class="px-4 py-3 text-right"><?= number_format(array_sum(array_column($recent_milk, 'morning_litres')), 1) ?></td>
                            <td class="px-4 py-3 text-right"><?= number_format(array_sum(array_column($recent_milk, 'evening_litres')), 1) ?></td>
                            <td class="px-4 py-3 text-right text-emerald-700"><?= number_format(array_sum(array_column($recent_milk, 'total_litres')), 1) ?></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <?php endif; ?>
        </div>

        <!-- Health Records -->
        <div class="bg-white/70 backdrop-blur-sm rounded-2xl shadow-sm border border-white/60 p-6">
            <h2 class="text-lg font-bold text-farm-green-900 mb-4">Health Summary</h2>
            <?php if (!empty($health_records)): ?>
                <div class="space-y-3">
                    <?php foreach ($health_records as $record): ?>
                        <div class="flex items-start gap-3 p-3 bg-farm-green-50/50 rounded-lg">
                            <div class="w-10 h-10 bg-farm-green-100 rounded-lg flex items-center justify-center text-farm-green-700 flex-shrink-0">
                                <i class="fas fa-stethoscope"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-farm-green-900"><?= htmlspecialchars($record['treatment'] ?? 'Health Check') ?></p>
                                <p class="text-xs text-farm-green-600 mt-1"><?= htmlspecialchars($record['notes'] ?? '') ?></p>
                                <p class="text-xs text-farm-green-500 mt-1"><?= date('M j, Y', strtotime($record['record_date'])) ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-farm-green-500 text-center py-8">No health records yet</p>
            <?php endif; ?>
        </div>
    </main>
</div>

<script>
// Generate QR Code
const shareUrl = "<?= htmlspecialchars($share_link) ?>";
new QRCode(document.getElementById('qrcode'), {
    text: shareUrl,
    width: 150,
    height: 150,
    colorDark: '#2d5016',
    colorLight: '#ffffff',
    correctLevel: QRCode.CorrectLevel.H
});

// Download QR Code
function downloadQR() {
    const canvas = document.querySelector('#qrcode canvas');
    if (canvas) {
        const link = document.createElement('a');
        link.download = 'cow-<?= htmlspecialchars($cow['ear_tag']) ?>-qr.png';
        link.href = canvas.toDataURL();
        link.click();
    }
}

// Milk Production Chart
<?php if (!empty($milk_summary)): ?>
const ctx = document.getElementById('milkChart').getContext('2d');
const labels = <?= json_encode(array_reverse(array_column($milk_summary, 'production_date'))) ?>;
const data = <?= json_encode(array_reverse(array_column($milk_summary, 'total_litres'))) ?>;

new Chart(ctx, {
    type: 'line',
    data: {
        labels: labels.map(d => new Date(d).toLocaleDateString('en-US', { month: 'short', day: 'numeric' })),
        datasets: [{
            label: 'Milk Production (L)',
            data: data,
            backgroundColor: 'rgba(34, 197, 94, 0.2)',
            borderColor: 'rgba(34, 197, 94, 1)',
            borderWidth: 2,
            tension: 0.3
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: { callback: v => v + ' L' }
            }
        }
    }
});
<?php endif; ?>
</script>
</body>
</html>
