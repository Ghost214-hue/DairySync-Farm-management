<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /farm-management/h3j5n8q1');
    exit();
}

require_once __DIR__ . '/../../backend/config/database.php';
require_once __DIR__ . '/../../backend/helpers/SettingsHelper.php';
require_once __DIR__ . '/../components/sidebar.php';

$conn = getDatabase();
$user_id = (int)$_SESSION['user_id'];
$settings = new SettingsHelper($user_id);
$milk_price = $settings->getMilkPrice();

// Filters
$start_date = $_GET['start_date'] ?? date('Y-m-01');
$end_date   = $_GET['end_date']   ?? date('Y-m-t');
$cow_id     = isset($_GET['cow_id']) && $_GET['cow_id'] !== '' ? (int)$_GET['cow_id'] : null;

$start_date = date('Y-m-d', strtotime($start_date));
$end_date   = date('Y-m-d', strtotime($end_date));

// ─────────────────────────────────────────────────────────────────────────
// 1. Summary query (cards)
// ─────────────────────────────────────────────────────────────────────────
$summary_sql = "
    SELECT 
        SUM(morning_litres + evening_litres) AS total_milk
    FROM milk_production
    WHERE user_id = ? AND production_date BETWEEN ? AND ?
";
$params = [$user_id, $start_date, $end_date];
$types = "iss";
if ($cow_id) {
    $summary_sql .= " AND cow_id = ?";
    $params[] = $cow_id;
    $types .= "i";
}
$stmt = $conn->prepare($summary_sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$summary = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Total sold milk from sales records in the same period
$sold_sql = "SELECT SUM(litres) AS total_sold FROM income WHERE user_id = ? AND source = 'Milk Sales' AND income_date BETWEEN ? AND ?";
$stmt = $conn->prepare($sold_sql);
$stmt->bind_param("iss", $user_id, $start_date, $end_date);
$stmt->execute();
$sold_res = $stmt->get_result()->fetch_assoc();
$stmt->close();
$total_sold = (float)($sold_res['total_sold'] ?? 0);
$total_nrm = max(0, (float)($summary['total_milk'] ?? 0) - $total_sold);
$summary['total_sold'] = $total_sold;
$summary['total_nrm'] = $total_nrm;
$summary['nrm_value'] = $total_nrm * $milk_price;

// ─────────────────────────────────────────────────────────────────────────
// 2. Detailed records (for table & export)
// ─────────────────────────────────────────────────────────────────────────
$detail_sql = "
    SELECT 
        mp.production_date,
        c.cow_name,
        mp.morning_litres,
        mp.evening_litres,
        mp.morning_litres + mp.evening_litres AS total_litres
    FROM milk_production mp
    LEFT JOIN cows c ON mp.cow_id = c.id
    WHERE mp.user_id = ? AND mp.production_date BETWEEN ? AND ?
";
$params2 = [$user_id, $start_date, $end_date];
$types2 = "iss";
if ($cow_id) {
    $detail_sql .= " AND mp.cow_id = ?";
    $params2[] = $cow_id;
    $types2 .= "i";
}
$detail_sql .= " ORDER BY mp.production_date DESC, mp.id DESC";
$stmt = $conn->prepare($detail_sql);
$stmt->bind_param($types2, ...$params2);
$stmt->execute();
$records = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// ─────────────────────────────────────────────────────────────────────────
// 3. Cow dropdown list
// ─────────────────────────────────────────────────────────────────────────
$cows = [];
$cow_query = "SELECT id, cow_name FROM cows WHERE user_id = ? ORDER BY cow_name";
$stmt = $conn->prepare($cow_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$cow_res = $stmt->get_result();
while ($c = $cow_res->fetch_assoc()) {
    $cows[] = $c;
}
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Milk Production Report | MooManager</title>
    <link href="/farm-management/frontend/css/output.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-[#f4f7f2] min-h-screen">
<div class="flex min-h-screen">
    <?php renderSidebar(); ?>
    <main class="flex-1 p-7">
        <div class="mb-4">
            <h1 class="text-3xl font-bold text-slate-800">📊 Milk Production Report</h1>
        </div>

        <?php $active_tab = 'milk'; require __DIR__ . '/../components/report_tabs.php'; ?>

        <!-- Filter Form -->
        <form method="GET" class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 mb-6 flex flex-wrap gap-4 items-end">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Start Date</label>
                <input type="date" name="start_date" value="<?= htmlspecialchars($start_date) ?>" class="border rounded-xl px-4 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">End Date</label>
                <input type="date" name="end_date" value="<?= htmlspecialchars($end_date) ?>" class="border rounded-xl px-4 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Select Cow</label>
                <select name="cow_id" class="border rounded-xl px-4 py-2 min-w-[180px]">
                    <option value="">All Cows</option>
                    <?php foreach ($cows as $cow): ?>
                        <option value="<?= $cow['id'] ?>" <?= ($cow_id == $cow['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cow['cow_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <button type="submit" class="bg-emerald-700 text-white px-5 py-2 rounded-xl hover:bg-emerald-800">
                    <i class="fas fa-filter"></i> Apply
                </button>
                <a href="?start_date=<?= date('Y-m-01') ?>&end_date=<?= date('Y-m-t') ?>" class="ml-2 text-slate-500 hover:text-slate-700 px-3 py-2 rounded-xl">Reset</a>
            </div>
        </form>

        <!-- Summary Cards (bigger, with icons and left border) -->
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-2xl p-6 shadow-md border-l-8 border-emerald-500">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-slate-500 text-sm uppercase tracking-wide">Total Milk</p>
                    <i class="fas fa-glass-water text-emerald-600 text-2xl"></i>
                </div>
                <p class="text-4xl font-bold text-slate-800"><?= number_format($summary['total_milk'] ?? 0, 1) ?> <span class="text-lg font-normal">L</span></p>
            </div>
            <div class="bg-white rounded-2xl p-6 shadow-md border-l-8 border-blue-500">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-slate-500 text-sm uppercase tracking-wide">Sold Milk</p>
                    <i class="fas fa-coins text-blue-600 text-2xl"></i>
                </div>
                <p class="text-4xl font-bold text-slate-800"><?= number_format($summary['total_sold'] ?? 0, 1) ?> <span class="text-lg font-normal">L</span></p>
            </div>
            <div class="bg-white rounded-2xl p-6 shadow-md border-l-8 border-amber-500">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-slate-500 text-sm uppercase tracking-wide">Non‑Revenue Milk (NRM)</p>
                    <i class="fas fa-exclamation-triangle text-amber-600 text-2xl"></i>
                </div>
                <p class="text-4xl font-bold text-slate-800"><?= number_format($summary['total_nrm'] ?? 0, 1) ?> <span class="text-lg font-normal">L</span></p>
            </div>
            <div class="bg-white rounded-2xl p-6 shadow-md border-l-8 border-purple-500">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-slate-500 text-sm uppercase tracking-wide">NRM Value</p>
                    <i class="fas fa-chart-line text-purple-600 text-2xl"></i>
                </div>
                <p class="text-4xl font-bold text-slate-800">KSh <?= number_format($summary['nrm_value'] ?? 0, 2) ?></p>
            </div>
        </div>

        <!-- Detailed Table with Export Button -->
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b bg-slate-50 flex justify-between items-center flex-wrap gap-3">
                <h3 class="text-xl font-semibold">📋 Milk Records (Detailed)</h3>
                <button onclick="exportToExcel('milkTable', 'Milk_Production_Report_<?= date('Y-m-d') ?>')" 
                        class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-xl text-sm transition">
                    <i class="fas fa-file-excel mr-1"></i> Export to Excel
                </button>
            </div>
            <div class="overflow-x-auto">
                <table id="milkTable" class="w-full">
                    <thead class="bg-slate-100">
                        <tr class="text-left text-sm text-slate-600">
                            <th class="px-4 py-3">Date</th>
                            <th class="px-4 py-3">Cow</th>
                            <th class="px-4 py-3">Morning (L)</th>
                            <th class="px-4 py-3">Evening (L)</th>
                            <th class="px-4 py-3">Total (L)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($records)): ?>
                            <tr><td colspan="8" class="text-center py-10 text-slate-400">No records found in selected period.</td></tr>
                        <?php else: ?>
                            <?php foreach ($records as $r): ?>
                                <tr class="border-t hover:bg-slate-50">
                                    <td class="px-4 py-3"><?= date('M j, Y', strtotime($r['production_date'])) ?></td>
                                    <td class="px-4 py-3 font-medium"><?= htmlspecialchars($r['cow_name'] ?? '?') ?></td>
                                    <td class="px-4 py-3"><?= number_format($r['morning_litres'], 1) ?></td>
                                    <td class="px-4 py-3"><?= number_format($r['evening_litres'], 1) ?></td>
                                    <td class="px-4 py-3 font-semibold"><?= number_format($r['total_litres'], 1) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<!-- Export to CSV (Excel) Function -->
<script>
function exportToExcel(tableId, filename) {
    const table = document.getElementById(tableId);
    const rows = table.querySelectorAll('tr');
    let csv = [];
    for (let row of rows) {
        const cells = row.querySelectorAll('th, td');
        const rowData = Array.from(cells).map(cell => {
            let text = cell.innerText.trim();
            // Remove any leftover HTML tags or special characters
            text = text.replace(/[^a-zA-Z0-9\s\-\.,]/g, '');
            // Wrap in quotes and escape quotes
            return `"${text.replace(/"/g, '""')}"`;
        }).join(',');
        csv.push(rowData);
    }
    const blob = new Blob(["\uFEFF" + csv.join('\n')], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    link.href = url;
    link.setAttribute('download', `${filename}.csv`);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    URL.revokeObjectURL(url);
}
</script>

</body>
</html>