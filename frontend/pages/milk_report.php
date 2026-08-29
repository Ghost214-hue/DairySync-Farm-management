<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../backend/reports/milk_report.php';
require_once __DIR__ . '/../components/sidebar.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Milk Production Report | MooManager</title>
    <link href="/frontend/css/output.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-[#f4f7f2] min-h-screen">
<div class="flex min-h-screen">
    <?php renderSidebar(); ?>
    <main class="flex-1 min-w-0 p-4 pt-20 md:p-7 md:pt-7">
        <div class="mb-4">
            <h1 class="text-2xl md:text-3xl font-bold text-slate-800">Milk Production Report</h1>
            <p class="text-slate-500 mt-1">Daily farm performance, cow contributions and detailed records.</p>
        </div>

        <?php $active_tab = 'milk'; require __DIR__ . '/../components/report_tabs.php'; ?>

        <!-- SECTION A: Filters -->
        <form method="GET" class="bg-white p-4 md:p-5 rounded-2xl shadow-sm border border-slate-100 mb-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:flex lg:flex-wrap gap-4 lg:items-end">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Start Date</label>
                    <input type="date" name="start_date" value="<?= htmlspecialchars($start_date) ?>" class="w-full border rounded-xl px-4 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">End Date</label>
                    <input type="date" name="end_date" value="<?= htmlspecialchars($end_date) ?>" class="w-full border rounded-xl px-4 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Select Cow</label>
                    <select name="cow_id" class="w-full border rounded-xl px-4 py-2 min-w-[180px]">
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
                    <a href="?start_date=<?= date('Y-m-01') ?>&end_date=<?= date('Y-m-t') ?>" class="inline-block mt-2 lg:mt-0 lg:ml-2 text-slate-500 hover:text-slate-700 px-3 py-2 rounded-xl">Reset</a>
                </div>
            </div>
            <!-- Quick range filters -->
            <div class="flex flex-wrap gap-2 mt-4 pt-3 border-t border-slate-100">
                <?php
                $today = date('Y-m-d');
                $quick = [
                    'Today'        => [$today, $today],
                    'Yesterday'    => [date('Y-m-d', strtotime('-1 day')), date('Y-m-d', strtotime('-1 day'))],
                    'This Week'    => [date('Y-m-d', strtotime('monday this week')), $today],
                    'This Month'   => [date('Y-m-01'), date('Y-m-t')],
                    'Last 30 Days' => [date('Y-m-d', strtotime('-29 days')), $today],
                ];
                foreach ($quick as $label => [$qs, $qe]):
                    $active = ($start_date === $qs && $end_date === $qe); ?>
                    <a href="?start_date=<?= $qs ?>&end_date=<?= $qe ?><?= $cow_id ? '&cow_id='.$cow_id : '' ?>"
                       class="px-3 py-1.5 rounded-full text-sm border transition <?= $active ? 'bg-emerald-700 text-white border-emerald-700' : 'bg-slate-50 text-slate-600 border-slate-200 hover:bg-slate-100' ?>">
                        <?= $label ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </form>

        <!-- SECTION B: Overall Period Summary -->
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6 mb-4">
            <div class="bg-white rounded-2xl p-6 shadow-md border-l-8 border-emerald-500">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-slate-500 text-sm uppercase tracking-wide">Total Milk Produced</p>
                    <i class="fas fa-glass-water text-emerald-600 text-2xl"></i>
                </div>
                <p class="text-4xl font-bold text-slate-800"><?= number_format($summary['total_milk'], 1) ?> <span class="text-lg font-normal">L</span></p>
                <p class="text-sm text-slate-400 mt-1">Avg daily: <?= number_format($summary['avg_daily'], 1) ?> L</p>
            </div>
            <div class="bg-white rounded-2xl p-6 shadow-md border-l-8 border-blue-500">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-slate-500 text-sm uppercase tracking-wide">Milk Sold / Delivered</p>
                    <i class="fas fa-truck text-blue-600 text-2xl"></i>
                </div>
                <p class="text-4xl font-bold text-slate-800"><?= number_format($summary['total_sold'], 1) ?> <span class="text-lg font-normal">L</span></p>
                <p class="text-sm text-slate-400 mt-1">Sales value: KSh <?= number_format($summary['sales_value'], 2) ?></p>
            </div>
            <div class="bg-white rounded-2xl p-6 shadow-md border-l-8 border-amber-500">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-slate-500 text-sm uppercase tracking-wide">Non‑Revenue Milk (NRM)</p>
                    <i class="fas fa-exclamation-triangle text-amber-600 text-2xl"></i>
                </div>
                <p class="text-4xl font-bold text-slate-800"><?= number_format($summary['total_nrm'], 1) ?> <span class="text-lg font-normal">L</span></p>
                <p class="text-sm text-slate-400 mt-1">NRM value: KSh <?= number_format($summary['nrm_value'], 2) ?></p>
            </div>
            <div class="bg-white rounded-2xl p-6 shadow-md border-l-8 border-purple-500">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-slate-500 text-sm uppercase tracking-wide">Herd Insights</p>
                    <i class="fas fa-chart-line text-purple-600 text-2xl"></i>
                </div>
                <p class="text-sm text-slate-600">Avg per cow: <strong><?= number_format($summary['avg_per_cow'], 1) ?> L</strong></p>
                <?php if ($best_day): ?><p class="text-sm text-slate-600">Best day: <strong><?= date('M j', strtotime($best_day['date'])) ?></strong> (<?= number_format($best_day['produced'], 1) ?> L)</p><?php endif; ?>
                <?php if ($worst_day): ?><p class="text-sm text-slate-600">Lowest day: <strong><?= date('M j', strtotime($worst_day['date'])) ?></strong> (<?= number_format($worst_day['produced'], 1) ?> L)</p><?php endif; ?>
            </div>
        </div>
        <!-- SECTION C: Daily Dairy Performance (expandable per day) -->
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden mb-8">
            <div class="px-6 py-4 border-b bg-slate-50">
                <h3 class="text-xl font-semibold">📅 Daily Dairy Performance</h3>
                <p class="text-xs text-slate-400 mt-1">Reconciliation per day: Produced − Sold = Non‑Revenue Milk. Click a day to view the cow breakdown.</p>
            </div>

            <?php if (empty($daily)): ?>
                <div class="py-10 text-center text-slate-400">No production records in the selected period.</div>
            <?php else: ?>
                <?php foreach ($daily as $d => $day): ?>
                    <?php $uid = 'day_' . str_replace('-', '', $d); ?>
                    <div class="border-t">
                        <button type="button" onclick="toggleDay('<?= $uid ?>')"
                                class="w-full text-left px-6 py-4 hover:bg-slate-50 transition flex flex-wrap items-center gap-x-6 gap-y-2">
                            <div class="w-full sm:w-auto flex-1">
                                <p class="font-bold text-slate-800"><i class="fas fa-calendar-day text-emerald-600 mr-2"></i><?= date('F j, Y', strtotime($d)) ?></p>
                            </div>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-x-6 gap-y-1 text-sm flex-[2]">
                                <span class="text-slate-500">Produced <strong class="text-emerald-700"><?= number_format($day['produced'], 1) ?> L</strong></span>
                                <span class="text-slate-500">Sold <strong class="text-blue-700"><?= number_format($day['sold'], 1) ?> L</strong></span>
                                <span class="text-slate-500">NRM <strong class="text-amber-600"><?= number_format($day['nrm'], 1) ?> L</strong></span>
                                <span class="text-slate-500">Sales <strong class="text-slate-700">KSh <?= number_format($day['sales_value'], 0) ?></strong></span>
                            </div>
                            <span class="text-emerald-700 text-sm whitespace-nowrap"><i id="icon_<?= $uid ?>" class="fas fa-chevron-down transition-transform"></i> Cow Breakdown</span>
                        </button>

                        <div id="<?= $uid ?>" class="hidden px-6 pb-5">
                            <div class="bg-slate-50 rounded-xl p-4 mb-3 grid grid-cols-2 sm:grid-cols-5 gap-4 text-center">
                                <div><p class="text-xs text-slate-400 uppercase">Produced</p><p class="text-lg font-bold text-emerald-700"><?= number_format($day['produced'], 1) ?> L</p></div>
                                <div><p class="text-xs text-slate-400 uppercase">Sold</p><p class="text-lg font-bold text-blue-700"><?= number_format($day['sold'], 1) ?> L</p></div>
                                <div><p class="text-xs text-slate-400 uppercase">NRM</p><p class="text-lg font-bold text-amber-600"><?= number_format($day['nrm'], 1) ?> L</p></div>
                                <div><p class="text-xs text-slate-400 uppercase">Sales Value</p><p class="text-lg font-bold text-slate-700">KSh <?= number_format($day['sales_value'], 2) ?></p></div>
                                <div><p class="text-xs text-slate-400 uppercase">NRM Value</p><p class="text-lg font-bold text-amber-700">KSh <?= number_format($day['nrm_value'], 2) ?></p></div>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead class="bg-slate-100 text-slate-600"><tr>
                                        <th class="px-4 py-2 text-left">Cow</th>
                                        <th class="px-4 py-2 text-right">Morning (L)</th>
                                        <th class="px-4 py-2 text-right">Evening (L)</th>
                                        <th class="px-4 py-2 text-right">Total (L)</th>
                                    </tr></thead>
                                    <tbody>
                                        <?php foreach ($day['cows'] as $c): ?>
                                            <tr class="border-t">
                                                <td class="px-4 py-2 font-medium">🐄 <?= htmlspecialchars($c['cow_name']) ?></td>
                                                <td class="px-4 py-2 text-right"><?= number_format($c['morning'], 1) ?></td>
                                                <td class="px-4 py-2 text-right"><?= number_format($c['evening'], 1) ?></td>
                                                <td class="px-4 py-2 text-right font-semibold text-emerald-700"><?= number_format($c['total'], 1) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                        <tr class="border-t bg-slate-50 font-bold">
                                            <td class="px-4 py-2">Total</td>
                                            <td class="px-4 py-2 text-right"><?= number_format(array_sum(array_column($day['cows'], 'morning')), 1) ?></td>
                                            <td class="px-4 py-2 text-right"><?= number_format(array_sum(array_column($day['cows'], 'evening')), 1) ?></td>
                                            <td class="px-4 py-2 text-right text-emerald-700"><?= number_format($day['produced'], 1) ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- SECTION C: Daily Dairy Performance (expandable per day) -->
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden mb-8">
            <div class="px-6 py-4 border-b bg-slate-50 flex justify-between items-center flex-wrap gap-3">
                <div>
                    <h3 class="text-xl font-semibold">📅 Daily Dairy Performance</h3>
                    <p class="text-xs text-slate-400 mt-1">Reconciliation per day: Produced − Sold = Non‑Revenue Milk. Click a day to view the cow breakdown.</p>
                </div>
            </div>


        <!-- SECTION D: Cow Performance Analytics -->
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden mb-8">
            <div class="px-6 py-4 border-b bg-slate-50 flex justify-between items-center flex-wrap gap-3">
                <div>
                    <h3 class="text-xl font-semibold">🐄 Cow Performance</h3>
                    <p class="text-xs text-slate-400 mt-1">Aggregated per cow for the selected period, ranked by total production.</p>
                </div>
                <button onclick="exportToExcel('cowTable', 'Milk_Cow_Performance_<?= date('Y-m-d') ?>')" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-xl text-sm transition">
                    <i class="fas fa-file-excel mr-1"></i> Export Cow Performance
                </button>
            </div>
            <div class="overflow-x-auto">
                <table id="cowTable" class="w-full min-w-[760px]">
                    <thead class="bg-slate-100"><tr class="text-left text-sm text-slate-600">
                        <th class="px-4 py-3">Cow</th>
                        <th class="px-4 py-3 text-right">Total (L)</th>
                        <th class="px-4 py-3 text-right">Morning (L)</th>
                        <th class="px-4 py-3 text-right">Evening (L)</th>
                        <th class="px-4 py-3 text-right">Days Recorded</th>
                        <th class="px-4 py-3 text-right">Avg / Day</th>
                        <th class="px-4 py-3 text-right">Best Day (L)</th>
                        <th class="px-4 py-3 text-right">Herd Contribution</th>
                    </tr></thead>
                    <tbody>
                        <?php if (empty($cow_agg)): ?>
                            <tr><td colspan="8" class="text-center py-10 text-slate-400">No cow production in the selected period.</td></tr>
                        <?php else: foreach ($cow_agg as $i => $c): ?>
                            <tr class="border-t hover:bg-slate-50">
                                <td class="px-4 py-3 font-medium">
                                    <?= htmlspecialchars($c['cow_name']) ?>
                                    <?php if ($i === 0): ?><span class="ml-1 text-xs bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded-full">🏆 Top</span><?php endif; ?>
                                    <?php if ($i === count($cow_agg) - 1 && count($cow_agg) > 1): ?><span class="ml-1 text-xs bg-red-100 text-red-600 px-2 py-0.5 rounded-full">Lowest</span><?php endif; ?>
                                </td>
                                <td class="px-4 py-3 text-right font-semibold"><?= number_format($c['total'], 1) ?></td>
                                <td class="px-4 py-3 text-right"><?= number_format($c['morning'], 1) ?></td>
                                <td class="px-4 py-3 text-right"><?= number_format($c['evening'], 1) ?></td>
                                <td class="px-4 py-3 text-right"><?= (int)$c['days'] ?></td>
                                <td class="px-4 py-3 text-right"><?= number_format($c['avg_per_day'], 2) ?></td>
                                <td class="px-4 py-3 text-right"><?= number_format($c['best'], 1) ?></td>
                                <td class="px-4 py-3 text-right">
                                    <span class="inline-flex items-center gap-2">
                                        <span class="w-16 h-2 bg-slate-100 rounded-full overflow-hidden inline-block">
                                            <span class="block h-full bg-emerald-500" style="width: <?= min(100, round($c['contribution_pct'])) ?>%"></span>
                                        </span>
                                        <?= number_format($c['contribution_pct'], 1) ?>%
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
            <?php if (empty($daily)): ?>
                <div class="py-10 text-center text-slate-400">No production records in the selected period.</div>
            <?php else: ?>
                <?php foreach ($daily as $d => $day): ?>
                    <?php $uid = 'day_' . str_replace('-', '', $d); ?>
                    <div class="border-t">
                        <!-- Day header (clickable) -->
                        <button type="button" onclick="toggleDay('<?= $uid ?>')"
                                class="w-full text-left px-6 py-4 hover:bg-slate-50 transition flex flex-wrap items-center gap-x-6 gap-y-2">
                            <div class="w-full sm:w-auto flex-1">
                                <p class="font-bold text-slate-800"><i class="fas fa-calendar-day text-emerald-600 mr-2"></i><?= date('F j, Y', strtotime($d)) ?></p>
                            </div>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-x-6 gap-y-1 text-sm flex-[2]">
                                <span class="text-slate-500">Produced <strong class="text-emerald-700"><?= number_format($day['produced'], 1) ?> L</strong></span>
                                <span class="text-slate-500">Sold <strong class="text-blue-700"><?= number_format($day['sold'], 1) ?> L</strong></span>
                                <span class="text-slate-500">NRM <strong class="text-amber-600"><?= number_format($day['nrm'], 1) ?> L</strong></span>
                                <span class="text-slate-500">Sales <strong class="text-slate-700">KSh <?= number_format($day['sales_value'], 0) ?></strong></span>
                            </div>
                            <span class="text-emerald-700 text-sm whitespace-nowrap"><i id="icon_<?= $uid ?>" class="fas fa-chevron-down transition-transform"></i> Cow Breakdown</span>
                        </button>

                        <!-- Expandable day detail -->
                        <div id="<?= $uid ?>" class="hidden px-6 pb-5">
                            <div class="bg-slate-50 rounded-xl p-4 mb-3 grid grid-cols-2 sm:grid-cols-5 gap-4 text-center">
                                <div><p class="text-xs text-slate-400 uppercase">Produced</p><p class="text-lg font-bold text-emerald-700"><?= number_format($day['produced'], 1) ?> L</p></div>
                                <div><p class="text-xs text-slate-400 uppercase">Sold</p><p class="text-lg font-bold text-blue-700"><?= number_format($day['sold'], 1) ?> L</p></div>
                                <div><p class="text-xs text-slate-400 uppercase">NRM</p><p class="text-lg font-bold text-amber-600"><?= number_format($day['nrm'], 1) ?> L</p></div>
                                <div><p class="text-xs text-slate-400 uppercase">Sales Value</p><p class="text-lg font-bold text-slate-700">KSh <?= number_format($day['sales_value'], 2) ?></p></div>
                                <div><p class="text-xs text-slate-400 uppercase">NRM Value</p><p class="text-lg font-bold text-amber-700">KSh <?= number_format($day['nrm_value'], 2) ?></p></div>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead class="bg-slate-100 text-slate-600"><tr>
                                        <th class="px-4 py-2 text-left">Cow</th>
                                        <th class="px-4 py-2 text-right">Morning (L)</th>
                                        <th class="px-4 py-2 text-right">Evening (L)</th>
                                        <th class="px-4 py-2 text-right">Total (L)</th>
                                    </tr></thead>
                                    <tbody>
                                        <?php foreach ($day['cows'] as $c): ?>
                                            <tr class="border-t">
                                                <td class="px-4 py-2 font-medium">🐄 <?= htmlspecialchars($c['cow_name']) ?></td>
                                                <td class="px-4 py-2 text-right"><?= number_format($c['morning'], 1) ?></td>
                                                <td class="px-4 py-2 text-right"><?= number_format($c['evening'], 1) ?></td>
                                                <td class="px-4 py-2 text-right font-semibold text-emerald-700"><?= number_format($c['total'], 1) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                        <tr class="border-t bg-slate-50 font-bold">
                                            <td class="px-4 py-2">Total</td>
                                            <td class="px-4 py-2 text-right"><?= number_format(array_sum(array_column($day['cows'], 'morning')), 1) ?></td>
                                            <td class="px-4 py-2 text-right"><?= number_format(array_sum(array_column($day['cows'], 'evening')), 1) ?></td>
                                            <td class="px-4 py-2 text-right text-emerald-700"><?= number_format($day['produced'], 1) ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- SECTION D: Cow Performance Analytics -->
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden mb-8">
            <div class="px-6 py-4 border-b bg-slate-50 flex justify-between items-center flex-wrap gap-3">
                <div>
                    <h3 class="text-xl font-semibold">🐄 Cow Performance</h3>
                    <p class="text-xs text-slate-400 mt-1">Aggregated per cow for the selected period, ranked by total production.</p>
                </div>
                <button onclick="exportToExcel('cowTable', 'Milk_Cow_Performance_<?= date('Y-m-d') ?>')" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-xl text-sm transition">
                    <i class="fas fa-file-excel mr-1"></i> Export Cow Performance
                </button>
            </div>
            <div class="overflow-x-auto">
                <table id="cowTable" class="w-full min-w-[760px]">
                    <thead class="bg-slate-100"><tr class="text-left text-sm text-slate-600">
                        <th class="px-4 py-3">Cow</th>
                        <th class="px-4 py-3 text-right">Total (L)</th>
                        <th class="px-4 py-3 text-right">Morning (L)</th>
                        <th class="px-4 py-3 text-right">Evening (L)</th>
                        <th class="px-4 py-3 text-right">Days Recorded</th>
                        <th class="px-4 py-3 text-right">Avg / Day</th>
                        <th class="px-4 py-3 text-right">Best Day (L)</th>
                        <th class="px-4 py-3 text-right">Herd Contribution</th>
                    </tr></thead>
                    <tbody>
                        <?php if (empty($cow_agg)): ?>
                            <tr><td colspan="8" class="text-center py-10 text-slate-400">No cow production in the selected period.</td></tr>
                        <?php else: foreach ($cow_agg as $i => $c): ?>
                            <tr class="border-t hover:bg-slate-50">
                                <td class="px-4 py-3 font-medium">
                                    <?= htmlspecialchars($c['cow_name']) ?>
                                    <?php if ($i === 0): ?><span class="ml-1 text-xs bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded-full">🏆 Top</span><?php endif; ?>
                                    <?php if ($i === count($cow_agg) - 1 && count($cow_agg) > 1): ?><span class="ml-1 text-xs bg-red-100 text-red-600 px-2 py-0.5 rounded-full">Lowest</span><?php endif; ?>
                                </td>
                                <td class="px-4 py-3 text-right font-semibold"><?= number_format($c['total'], 1) ?></td>
                                <td class="px-4 py-3 text-right"><?= number_format($c['morning'], 1) ?></td>
                                <td class="px-4 py-3 text-right"><?= number_format($c['evening'], 1) ?></td>
                                <td class="px-4 py-3 text-right"><?= (int)$c['days'] ?></td>
                                <td class="px-4 py-3 text-right"><?= number_format($c['avg_per_day'], 2) ?></td>
                                <td class="px-4 py-3 text-right"><?= number_format($c['best'], 1) ?></td>
                                <td class="px-4 py-3 text-right">
                                    <span class="inline-flex items-center gap-2">
                                        <span class="w-16 h-2 bg-slate-100 rounded-full overflow-hidden inline-block">
                                            <span class="block h-full bg-emerald-500" style="width: <?= min(100, round($c['contribution_pct'])) ?>%"></span>
                                        </span>
                                        <?= number_format($c['contribution_pct'], 1) ?>%
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- SECTION E: Detailed Milk Records (existing functionality preserved) -->
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b bg-slate-50 flex justify-between items-center flex-wrap gap-3">
                <h3 class="text-xl font-semibold">📋 Milk Records (Detailed)</h3>
                <div class="flex gap-2">
                    <button onclick="exportToExcel('dailyTable', 'Milk_Daily_Summary_<?= date('Y-m-d') ?>')"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-sm transition">
                        <i class="fas fa-file-excel mr-1"></i> Export Daily Summary
                    </button>
                    <button onclick="exportToExcel('milkTable', 'Milk_Detailed_Records_<?= date('Y-m-d') ?>')"
                            class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-xl text-sm transition">
                        <i class="fas fa-file-excel mr-1"></i> Export Detailed Records
                    </button>
                </div>
            </div>

            <!-- Hidden daily summary table used only for the Daily Summary export -->
            <table id="dailyTable" class="hidden">
                <thead><tr><th>Date</th><th>Produced (L)</th><th>Sold (L)</th><th>NRM (L)</th><th>Sales Value (KSh)</th><th>NRM Value (KSh)</th></tr></thead>
                <tbody>
                    <?php foreach ($daily as $day): ?>
                        <tr>
                            <td><?= date('Y-m-d', strtotime($day['date'])) ?></td>
                            <td><?= number_format($day['produced'], 1) ?></td>
                            <td><?= number_format($day['sold'], 1) ?></td>
                            <td><?= number_format($day['nrm'], 1) ?></td>
                            <td><?= number_format($day['sales_value'], 2) ?></td>
                            <td><?= number_format($day['nrm_value'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="overflow-x-auto">
                <table id="milkTable" class="w-full min-w-[680px]">
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
                            <tr><td colspan="5" class="text-center py-10 text-slate-400">No records found in selected period.</td></tr>
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

<!-- Export to CSV (Excel) + daily section toggle -->
<script>
function toggleDay(id) {
    const el = document.getElementById(id);
    const icon = document.getElementById('icon_' + id);
    el.classList.toggle('hidden');
    icon.style.transform = el.classList.contains('hidden') ? '' : 'rotate(180deg)';
}

function exportToExcel(tableId, filename) {
    const table = document.getElementById(tableId);
    const rows = table.querySelectorAll('tr');
    let csv = [];
    for (let row of rows) {
        const cells = row.querySelectorAll('th, td');
        const rowData = Array.from(cells).map(cell => {
            let text = cell.innerText.trim();
            text = text.replace(/[^a-zA-Z0-9\s\-\.,%]/g, '');
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
