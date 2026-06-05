<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /farm-management/h3j5n8q1');
    exit();
}

require_once __DIR__ . '/../../backend/config/database.php';
require_once __DIR__ . '/../components/sidebar.php';


$conn = getDatabase();
$user_id = (int)$_SESSION['user_id'];

// ─────────────────────────────────────────────────────────────────────────
// Filters (Income & Expenses share date range, but separate category filters)
// ─────────────────────────────────────────────────────────────────────────
$start_date = $_GET['start_date'] ?? date('Y-m-01');
$end_date   = $_GET['end_date']   ?? date('Y-m-t');
$income_source = $_GET['income_source'] ?? '';
$expense_category = $_GET['expense_category'] ?? '';
$customer   = trim($_GET['customer'] ?? '');

$start_date = date('Y-m-d', strtotime($start_date));
$end_date   = date('Y-m-d', strtotime($end_date));

// ─────────────────────────────────────────────────────────────────────────
// INCOME QUERIES
// ─────────────────────────────────────────────────────────────────────────
$income_where = "user_id = ? AND income_date BETWEEN ? AND ?";
$income_params = [$user_id, $start_date, $end_date];
$income_types = "iss";

if ($income_source !== '') {
    $income_where .= " AND source = ?";
    $income_params[] = $income_source;
    $income_types .= "s";
}
if ($customer !== '') {
    $income_where .= " AND customer_name = ?";
    $income_params[] = $customer;
    $income_types .= "s";
}

// Income Summary
$inc_sum_sql = "SELECT SUM(total_amount) AS total_income FROM income WHERE $income_where";
$stmt = $conn->prepare($inc_sum_sql);
$stmt->bind_param($income_types, ...$income_params);
$stmt->execute();
$inc_sum = $stmt->get_result()->fetch_assoc();
$stmt->close();
$total_income = $inc_sum['total_income'] ?? 0;
$total_nrm = 0;

// Calculate NRM separately from production and milk sales totals across the filtered date range.
if ($income_source === '' || $income_source === 'Milk Sales') {
    $nrm_sql = "SELECT SUM(GREATEST(0, IFNULL(mp.total, 0) - IFNULL(ms.total, 0))) AS total_nrm
                FROM (
                    SELECT production_date, SUM(morning_litres + evening_litres) AS total
                    FROM milk_production
                    WHERE user_id = ? AND production_date BETWEEN ? AND ?
                    GROUP BY production_date
                ) mp
                LEFT JOIN (
                    SELECT income_date, SUM(litres) AS total
                    FROM income
                    WHERE user_id = ? AND source = 'Milk Sales' AND income_date BETWEEN ? AND ?
                    GROUP BY income_date
                ) ms ON mp.production_date = ms.income_date";
    $stmt = $conn->prepare($nrm_sql);
    $stmt->bind_param("ississ", $user_id, $start_date, $end_date, $user_id, $start_date, $end_date);
    $stmt->execute();
    $nrm_sum = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $total_nrm = $nrm_sum['total_nrm'] ?? 0;
}

// Income records (for table & export)
$inc_records_sql = "SELECT income_date, source, customer_name, litres, rate_per_litre, total_amount, nrm_value 
                    FROM income WHERE $income_where ORDER BY income_date DESC LIMIT 500";
$stmt = $conn->prepare($inc_records_sql);
$stmt->bind_param($income_types, ...$income_params);
$stmt->execute();
$income_records = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Top income source
$top_src_sql = "SELECT source, SUM(total_amount) AS amt FROM income WHERE $income_where GROUP BY source ORDER BY amt DESC LIMIT 1";
$stmt = $conn->prepare($top_src_sql);
$stmt->bind_param($income_types, ...$income_params);
$stmt->execute();
$top_source = $stmt->get_result()->fetch_assoc();
$stmt->close();

// ─────────────────────────────────────────────────────────────────────────
// EXPENSE QUERIES
// ─────────────────────────────────────────────────────────────────────────
$exp_where = "user_id = ? AND expense_date BETWEEN ? AND ?";
$exp_params = [$user_id, $start_date, $end_date];
$exp_types = "iss";

if ($expense_category !== '') {
    $exp_where .= " AND category = ?";
    $exp_params[] = $expense_category;
    $exp_types .= "s";
}

// Expense Summary
$exp_sum_sql = "SELECT SUM(amount) AS total_expenses FROM expenses WHERE $exp_where";
$stmt = $conn->prepare($exp_sum_sql);
$stmt->bind_param($exp_types, ...$exp_params);
$stmt->execute();
$exp_sum = $stmt->get_result()->fetch_assoc();
$stmt->close();
$total_expenses = $exp_sum['total_expenses'] ?? 0;
$net_profit = $total_income - $total_expenses;

// Expense records (for table & export)
$exp_records_sql = "SELECT expense_date, category, description, amount FROM expenses WHERE $exp_where ORDER BY expense_date DESC LIMIT 500";
$stmt = $conn->prepare($exp_records_sql);
$stmt->bind_param($exp_types, ...$exp_params);
$stmt->execute();
$expense_records = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Top expense category
$top_cat_sql = "SELECT category, SUM(amount) AS amt FROM expenses WHERE $exp_where GROUP BY category ORDER BY amt DESC LIMIT 1";
$stmt = $conn->prepare($top_cat_sql);
$stmt->bind_param($exp_types, ...$exp_params);
$stmt->execute();
$top_category = $stmt->get_result()->fetch_assoc();
$stmt->close();

// For category dropdown
$cat_list = [];
$cat_sql = "SELECT DISTINCT category FROM expenses WHERE user_id = ? ORDER BY category";
$stmt = $conn->prepare($cat_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$cat_res = $stmt->get_result();
while ($c = $cat_res->fetch_assoc()) $cat_list[] = $c['category'];
$stmt->close();

// Customers who have bought milk, used by the income report customer filter.
$customer_list = [];
$customer_sql = "SELECT DISTINCT customer_name
                 FROM income
                 WHERE user_id = ?
                   AND source = 'Milk Sales'
                   AND customer_name IS NOT NULL
                   AND TRIM(customer_name) <> ''
                 ORDER BY customer_name";
$stmt = $conn->prepare($customer_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$customer_res = $stmt->get_result();
while ($row = $customer_res->fetch_assoc()) $customer_list[] = $row['customer_name'];
$stmt->close();

$conn->close();

// Days difference for averages
$days_diff = max(1, (strtotime($end_date) - strtotime($start_date)) / 86400 + 1);
$avg_daily_income = $total_income / $days_diff;
$avg_daily_expense = $total_expenses / $days_diff;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finance Report | MooManager</title>
    <link href="/farm-management/frontend/css/output.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-[#f4f7f2] min-h-screen">
<div class="flex min-h-screen">
    <?php renderSidebar(); ?>
    <main class="flex-1 min-w-0 p-4 pt-20 md:p-7 md:pt-7">
        <h1 class="text-2xl md:text-3xl font-bold text-slate-800 mb-4">Finance Report (Income + Expenses)</h1>
        <?php $active_tab = 'income'; require __DIR__ . '/../components/report_tabs.php'; ?>

        <!-- Combined Filters -->
        <form method="GET" class="bg-white p-4 md:p-5 rounded-2xl shadow-sm border mb-6 grid grid-cols-1 sm:grid-cols-2 lg:flex lg:flex-wrap gap-4 lg:items-end">
            <div><label class="block text-sm font-medium mb-1">Start Date</label><input type="date" name="start_date" value="<?= htmlspecialchars($start_date) ?>" class="w-full border rounded-xl px-4 py-2"></div>
            <div><label class="block text-sm font-medium mb-1">End Date</label><input type="date" name="end_date" value="<?= htmlspecialchars($end_date) ?>" class="w-full border rounded-xl px-4 py-2"></div>
            <div><label class="block text-sm font-medium mb-1">Income Source</label>
                <select name="income_source" class="w-full border rounded-xl px-4 py-2">
                    <option value="">All Sources</option>
                    <option value="Milk Sales" <?= $income_source === 'Milk Sales' ? 'selected' : '' ?>>Milk Sales</option>
                    <option value="Cow Sales" <?= $income_source === 'Cow Sales' ? 'selected' : '' ?>>Cow Sales</option>
                    <option value="Manure" <?= $income_source === 'Manure' ? 'selected' : '' ?>>Manure</option>
                    <option value="Other" <?= $income_source === 'Other' ? 'selected' : '' ?>>Other</option>
                </select>
            </div>
            <div><label class="block text-sm font-medium mb-1">Expense Category</label>
                <select name="expense_category" class="w-full border rounded-xl px-4 py-2">
                    <option value="">All Categories</option>
                    <?php foreach ($cat_list as $cat): ?>
                        <option value="<?= htmlspecialchars($cat) ?>" <?= $expense_category === $cat ? 'selected' : '' ?>><?= htmlspecialchars($cat) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div><label class="block text-sm font-medium mb-1">Milk Customer</label>
                <select name="customer" class="w-full border rounded-xl px-4 py-2">
                    <option value="">All Customers</option>
                    <?php foreach ($customer_list as $customer_name): ?>
                        <option value="<?= htmlspecialchars($customer_name) ?>" <?= $customer === $customer_name ? 'selected' : '' ?>>
                            <?= htmlspecialchars($customer_name) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div><button type="submit" class="bg-emerald-700 text-white px-5 py-2 rounded-xl"><i class="fas fa-filter"></i> Apply</button></div>
        </form>

        <!-- Summary Cards (6 cards – bigger layout) -->
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-2xl p-6 shadow-md border-l-8 border-emerald-500">
                <div class="flex items-center justify-between mb-2"><p class="text-slate-500 text-sm uppercase">Total Income</p><i class="fas fa-coins text-emerald-600 text-2xl"></i></div>
                <p class="text-4xl font-bold text-emerald-700">KSh <?= number_format($total_income, 2) ?></p>
                <p class="text-sm text-slate-400 mt-1">Avg daily: KSh <?= number_format($avg_daily_income, 2) ?></p>
            </div>
            <div class="bg-white rounded-2xl p-6 shadow-md border-l-8 border-red-500">
                <div class="flex items-center justify-between mb-2"><p class="text-slate-500 text-sm uppercase">Total Expenses</p><i class="fas fa-receipt text-red-600 text-2xl"></i></div>
                <p class="text-4xl font-bold text-red-600">KSh <?= number_format($total_expenses, 2) ?></p>
                <p class="text-sm text-slate-400 mt-1">Avg daily: KSh <?= number_format($avg_daily_expense, 2) ?></p>
            </div>
            <div class="bg-white rounded-2xl p-6 shadow-md border-l-8 border-blue-500">
                <div class="flex items-center justify-between mb-2"><p class="text-slate-500 text-sm uppercase">Net Profit</p><i class="fas fa-chart-line text-blue-600 text-2xl"></i></div>
                <p class="text-4xl font-bold <?= $net_profit >= 0 ? 'text-green-700' : 'text-red-600' ?>">KSh <?= number_format($net_profit, 2) ?></p>
            </div>
            <div class="bg-white rounded-2xl p-6 shadow-md border-l-8 border-amber-500">
                <div class="flex items-center justify-between mb-2"><p class="text-slate-500 text-sm uppercase">Top Income Source</p><i class="fas fa-trophy text-amber-600 text-2xl"></i></div>
                <?php if ($top_source): ?><p class="text-2xl font-semibold"><?= htmlspecialchars($top_source['source']) ?></p><p class="text-emerald-700">KSh <?= number_format($top_source['amt'], 2) ?></p><?php else: ?><p>—</p><?php endif; ?>
            </div>
            <div class="bg-white rounded-2xl p-6 shadow-md border-l-8 border-purple-500">
                <div class="flex items-center justify-between mb-2"><p class="text-slate-500 text-sm uppercase">Top Expense Category</p><i class="fas fa-chart-pie text-purple-600 text-2xl"></i></div>
                <?php if ($top_category): ?><p class="text-2xl font-semibold"><?= htmlspecialchars($top_category['category']) ?></p><p class="text-red-600">KSh <?= number_format($top_category['amt'], 2) ?></p><?php else: ?><p>—</p><?php endif; ?>
            </div>
            <div class="bg-white rounded-2xl p-6 shadow-md border-l-8 border-cyan-500">
                <div class="flex items-center justify-between mb-2"><p class="text-slate-500 text-sm uppercase">NRM Value (Milk Sales)</p><i class="fas fa-exclamation-triangle text-cyan-600 text-2xl"></i></div>
                <p class="text-4xl font-bold text-cyan-700">KSh <?= number_format($total_nrm, 2) ?></p>
            </div>
        </div>

        <!-- Income Table with Export -->
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden mb-8">
            <div class="px-6 py-4 border-b bg-slate-50 flex justify-between items-center">
                <h3 class="text-xl font-semibold">📋 Income Records</h3>
                <button onclick="exportToExcel('incomeTable', 'Income_Report_<?= date('Y-m-d') ?>')" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-xl text-sm"><i class="fas fa-file-excel"></i> Export to Excel</button>
            </div>
            <div class="overflow-x-auto">
                <table id="incomeTable" class="w-full min-w-[760px]">
                    <thead class="bg-slate-100"><tr class="text-left text-sm text-slate-600">
                        <th class="px-4 py-3">Date</th><th class="px-4 py-3">Source</th><th class="px-4 py-3">Customer</th><th class="px-4 py-3">Litres</th><th class="px-4 py-3">Rate (KSh)</th><th class="px-4 py-3">Amount (KSh)</th><th class="px-4 py-3">NRM Value</th>
                    </tr></thead>
                    <tbody>
                        <?php if (empty($income_records)): ?><tr><td colspan="7" class="text-center py-10 text-slate-400">No income records.<?php else: foreach ($income_records as $r): ?>
                            <tr class="border-t hover:bg-slate-50">
                                <td class="px-4 py-3"><?= date('M j, Y', strtotime($r['income_date'])) ?></td>
                                <td class="px-4 py-3"><?= htmlspecialchars($r['source']) ?></td>
                                <td class="px-4 py-3"><?= $r['customer_name'] ? '<span class="bg-blue-100 text-blue-800 px-2 py-0.5 rounded-full text-xs">'.htmlspecialchars($r['customer_name']).'</span>' : '—' ?></td>
                                <td class="px-4 py-3"><?= $r['litres'] ? number_format($r['litres'], 1) : '—' ?></td>
                                <td class="px-4 py-3"><?= $r['rate_per_litre'] ? 'KSh '.number_format($r['rate_per_litre'], 2) : '—' ?></td>
                                <td class="px-4 py-3 font-semibold text-emerald-700">KSh <?= number_format($r['total_amount'], 2) ?></td>
                                <td class="px-4 py-3 text-amber-600">KSh <?= number_format($r['nrm_value'] ?? 0, 2) ?></td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Expense Table with Export -->
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b bg-slate-50 flex justify-between items-center">
                <h3 class="text-xl font-semibold">📋 Expense Records</h3>
                <button onclick="exportToExcel('expenseTable', 'Expense_Report_<?= date('Y-m-d') ?>')" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-xl text-sm"><i class="fas fa-file-excel"></i> Export to Excel</button>
            </div>
            <div class="overflow-x-auto">
                <table id="expenseTable" class="w-full min-w-[640px]">
                    <thead class="bg-slate-100"><tr class="text-left text-sm text-slate-600">
                        <th class="px-4 py-3">Date</th><th class="px-4 py-3">Category</th><th class="px-4 py-3">Description</th><th class="px-4 py-3">Amount (KSh)</th>
                    </tr></thead>
                    <tbody>
                        <?php if (empty($expense_records)): ?><tr><td colspan="4" class="text-center py-10 text-slate-400">No expense records.<?php else: foreach ($expense_records as $e): ?>
                            <tr class="border-t hover:bg-slate-50">
                                <td class="px-4 py-3"><?= date('M j, Y', strtotime($e['expense_date'])) ?></td>
                                <td class="px-4 py-3"><?= htmlspecialchars($e['category']) ?></td>
                                <td class="px-4 py-3"><?= htmlspecialchars($e['description']) ?></td>
                                <td class="px-4 py-3 font-semibold text-red-600">KSh <?= number_format($e['amount'], 2) ?></td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<script>
function exportToExcel(tableId, filename) {
    const table = document.getElementById(tableId);
    const rows = table.querySelectorAll('tr');
    let csv = [];
    for (let row of rows) {
        const cells = row.querySelectorAll('th, td');
        const rowData = Array.from(cells).map(cell => {
            let text = cell.innerText.trim();
            // Remove any HTML span tags that might remain
            text = text.replace(/[^a-zA-Z0-9\s\-\.,]/g, '');
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
