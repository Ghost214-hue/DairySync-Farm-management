<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../backend/pages/income.php';
require_once __DIR__ . '/../components/sidebar.php';
require_once __DIR__ . '/../../router/urlHelper.php';

$income_success = $_SESSION['income_success'] ?? null;
$income_error   = $_SESSION['income_error'] ?? null;
$expense_success = $_SESSION['expense_success'] ?? null;
$expense_error   = $_SESSION['expense_error'] ?? null;

unset($_SESSION['income_success'], $_SESSION['income_error'], $_SESSION['expense_success'], $_SESSION['expense_error']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Income & Expenses | MooManager</title>
    <link href="/farm-management/frontend/css/output.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .income-fields-group { transition: all 0.2s ease; }
    </style>
</head>
<body class="bg-[#f4f7f2] min-h-screen">

<div class="flex min-h-screen">
    <?php renderSidebar(); ?>

    <main class="flex-1 p-7">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-4xl font-bold text-slate-800">Finance Overview</h1>
                <p class="text-slate-500 mt-2">Track income, expenses and profitability</p>
            </div>
            <div class="flex gap-3">
                <button onclick="openIncomeModal()" class="bg-emerald-700 hover:bg-emerald-800 text-white px-5 py-3 rounded-2xl font-semibold shadow-sm transition">
                    <i class="fas fa-plus mr-2"></i> Add Income
                </button>
                <button onclick="openExpenseModal()" class="bg-red-600 hover:bg-red-700 text-white px-5 py-3 rounded-2xl font-semibold shadow-sm transition">
                    <i class="fas fa-plus mr-2"></i> Add Expense
                </button>
            </div>
        </div>

        <!-- Combined Summary Cards (now 4 cards including price editor) -->
        <div class="grid md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6">
                <div class="w-14 h-14 rounded-2xl bg-emerald-100 flex items-center justify-center mb-5">
                    <i class="fas fa-coins text-emerald-700 text-xl"></i>
                </div>
                <h2 class="text-4xl font-bold text-slate-800">KSh <?= number_format($total_income) ?></h2>
                <p class="text-slate-500 mt-2">Total Income</p>
            </div>
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6">
                <div class="w-14 h-14 rounded-2xl bg-red-100 flex items-center justify-center mb-5">
                    <i class="fas fa-receipt text-red-600 text-xl"></i>
                </div>
                <h2 class="text-4xl font-bold text-slate-800">KSh <?= number_format($total_expenses) ?></h2>
                <p class="text-slate-500 mt-2">Total Expenses</p>
            </div>
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6">
                <div class="w-14 h-14 rounded-2xl bg-green-100 flex items-center justify-center mb-5">
                    <i class="fas fa-chart-line text-green-700 text-xl"></i>
                </div>
                <h2 class="text-4xl font-bold text-slate-800 <?= $net_profit >= 0 ? 'text-green-700' : 'text-red-600' ?>">
                    KSh <?= number_format($net_profit) ?>
                </h2>
                <p class="text-slate-500 mt-2">Net Profit</p>
            </div>
            <!-- Milk Price Editor Card -->
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-500 mb-1">Current Milk Price</p>
                        <form method="POST" class="flex items-center gap-2">
                            <input type="hidden" name="action" value="update_milk_price">
                            <div class="flex items-center bg-slate-50 rounded-xl border border-slate-200 overflow-hidden">
                                <span class="px-3 text-slate-600 font-medium">KSh</span>
                                <input type="number" step="0.01" name="milk_price" value="<?= number_format($default_milk_price, 2) ?>" 
                                       class="w-28 py-2 px-2 border-0 focus:ring-0 text-right font-semibold">
                            </div>
                            <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white px-3 py-2 rounded-xl text-sm">
                                <i class="fas fa-save"></i>
                            </button>
                        </form>
                        <p class="text-xs text-slate-400 mt-2">per litre – applies to new milk sales</p>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-amber-100 flex items-center justify-center">
                        <i class="fas fa-tag text-amber-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Two-Column Layout: Income Table (left) | Expenses Table (right) -->
        <div class="grid lg:grid-cols-2 gap-8">
            <!-- LEFT: Income Records -->
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-100">
                    <h3 class="text-2xl font-bold text-slate-800">Income Records</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-slate-50">
                            <tr class="text-left text-xs uppercase tracking-wider text-slate-400">
                                <th class="px-6 py-4">Date</th>
                                <th class="px-6 py-4">Source</th>
                                <th class="px-6 py-4">Litres</th>
                                <th class="px-6 py-4">Rate</th>
                                <th class="px-6 py-4">Amount</th>
                             </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($income_records)): ?>
                                <tr><td colspan="5" class="py-20 text-center text-slate-400">No income records yet.</td></tr>
                            <?php else: ?>
                                <?php foreach ($income_records as $inc): ?>
                                    <tr class="border-t hover:bg-slate-50 transition">
                                        <td class="px-6 py-4 text-slate-700"><?= date('M j, Y', strtotime($inc['income_date'])) ?></td>
                                        <td class="px-6 py-4 font-medium"><?= htmlspecialchars($inc['source']) ?></td>
                                        <td class="px-6 py-4"><?= $inc['litres'] ? number_format($inc['litres'], 1) : '-' ?></td>
                                        <td class="px-6 py-4"><?= $inc['rate_per_litre'] ? 'KSh '.number_format($inc['rate_per_litre'], 2) : '-' ?></td>
                                        <td class="px-6 py-4 font-bold text-emerald-700">KSh <?= number_format($inc['total_amount']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <?php if ($income_total_pages > 1): ?>
                    <div class="px-6 py-4 border-t border-slate-100 flex justify-center">
                        <div class="flex gap-2">
                            <?php for ($i = 1; $i <= $income_total_pages; $i++): ?>
                                <a href="?income_page=<?= $i ?>&expense_page=<?= $expense_page ?>" 
                                   class="px-3 py-1 rounded-lg <?= $i == $income_page ? 'bg-emerald-700 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' ?>">
                                    <?= $i ?>
                                </a>
                            <?php endfor; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- RIGHT: Expense Records -->
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-100">
                    <h3 class="text-2xl font-bold text-slate-800">Expense Records</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-slate-50">
                            <tr class="text-left text-xs uppercase tracking-wider text-slate-400">
                                <th class="px-6 py-4">Date</th>
                                <th class="px-6 py-4">Category</th>
                                <th class="px-6 py-4">Description</th>
                                <th class="px-6 py-4">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($expenses)): ?>
                                <tr><td colspan="4" class="py-20 text-center text-slate-400">No expenses recorded yet.</td></tr>
                            <?php else: ?>
                                <?php foreach ($expenses as $exp): ?>
                                    <tr class="border-t hover:bg-slate-50 transition">
                                        <td class="px-6 py-4 text-slate-700"><?= date('M j, Y', strtotime($exp['expense_date'])) ?></td>
                                        <td class="px-6 py-4"><span class="bg-slate-100 text-slate-700 px-3 py-1 rounded-full text-sm"><?= htmlspecialchars($exp['category']) ?></span></td>
                                        <td class="px-6 py-4 text-slate-700"><?= htmlspecialchars($exp['description']) ?></td>
                                        <td class="px-6 py-4 font-bold text-red-600">KSh <?= number_format($exp['amount']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <?php if ($expense_total_pages > 1): ?>
                    <div class="px-6 py-4 border-t border-slate-100 flex justify-center">
                        <div class="flex gap-2">
                            <?php for ($i = 1; $i <= $expense_total_pages; $i++): ?>
                                <a href="?expense_page=<?= $i ?>&income_page=<?= $income_page ?>" 
                                   class="px-3 py-1 rounded-lg <?= $i == $expense_page ? 'bg-red-600 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' ?>">
                                    <?= $i ?>
                                </a>
                            <?php endfor; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>

<!-- MODAL: ADD INCOME -->
<div id="incomeModal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50">
    <div class="bg-white w-full max-w-lg rounded-3xl shadow-2xl p-7">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-slate-800">Add Income</h2>
            <button onclick="closeIncomeModal()" class="text-slate-400 hover:text-slate-600"><i class="fas fa-times text-xl"></i></button>
        </div>
        <?php if ($income_error): ?>
            <div class="mb-4 bg-red-100 text-red-700 px-4 py-2 rounded-xl"><?= htmlspecialchars($income_error) ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <input type="hidden" name="action" value="add_income">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Source</label>
                    <select name="source" id="modalIncomeSource" class="w-full border border-slate-200 rounded-xl px-4 py-3" required>
                        <option value="Milk Sales">Milk Sales</option>
                        <option value="Cow Sales">Cow Sales</option>
                        <option value="Manure">Manure</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div id="modalMilkFields" class="income-fields-group">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Litres</label>
                            <input type="number" step="0.1" name="litres" class="w-full border border-slate-200 rounded-xl px-4 py-3" value="0">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Rate per Litre (KSh)</label>
                            <input type="number" step="0.01" name="rate_per_litre" class="w-full border border-slate-200 rounded-xl px-4 py-3" value="<?= number_format($default_milk_price, 2) ?>">
                        </div>
                    </div>
                </div>
                <div id="modalManualAmountField" class="income-fields-group" style="display: none;">
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Total Amount (KSh)</label>
                    <input type="number" step="0.01" name="total_amount" class="w-full border border-slate-200 rounded-xl px-4 py-3" placeholder="Enter amount">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Income Date</label>
                    <input type="date" name="income_date" value="<?= date('Y-m-d') ?>" class="w-full border border-slate-200 rounded-xl px-4 py-3" required>
                </div>
                <button type="submit" class="w-full bg-emerald-700 hover:bg-emerald-800 text-white font-semibold py-3 rounded-2xl transition">Save Income</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL: ADD EXPENSE -->
<div id="expenseModal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50">
    <div class="bg-white w-full max-w-lg rounded-3xl shadow-2xl p-7">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-slate-800">Add Expense</h2>
            <button onclick="closeExpenseModal()" class="text-slate-400 hover:text-slate-600"><i class="fas fa-times text-xl"></i></button>
        </div>
        <?php if ($expense_error): ?>
            <div class="mb-4 bg-red-100 text-red-700 px-4 py-2 rounded-xl"><?= htmlspecialchars($expense_error) ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <input type="hidden" name="action" value="add_expense">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Category</label>
                    <select name="category" class="w-full border border-slate-200 rounded-xl px-4 py-3" required>
                        <option value="">Select category</option>
                        <option>Transport</option><option>Feed</option><option>Vet</option>
                        <option>Labour</option><option>Equipment</option><option>Other</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Description</label>
                    <input type="text" name="description" class="w-full border border-slate-200 rounded-xl px-4 py-3" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Amount (KSh)</label>
                    <input type="number" step="0.01" name="amount" class="w-full border border-slate-200 rounded-xl px-4 py-3" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Expense Date</label>
                    <input type="date" name="expense_date" value="<?= date('Y-m-d') ?>" class="w-full border border-slate-200 rounded-xl px-4 py-3" required>
                </div>
                <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-3 rounded-2xl transition">Save Expense</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openIncomeModal() { document.getElementById('incomeModal').classList.remove('hidden'); document.getElementById('incomeModal').classList.add('flex'); }
    function closeIncomeModal() { document.getElementById('incomeModal').classList.add('hidden'); document.getElementById('incomeModal').classList.remove('flex'); }
    function openExpenseModal() { document.getElementById('expenseModal').classList.remove('hidden'); document.getElementById('expenseModal').classList.add('flex'); }
    function closeExpenseModal() { document.getElementById('expenseModal').classList.add('hidden'); document.getElementById('expenseModal').classList.remove('flex'); }

    const modalSource = document.getElementById('modalIncomeSource');
    const modalMilkFields = document.getElementById('modalMilkFields');
    const modalManualField = document.getElementById('modalManualAmountField');

    function toggleModalIncomeFields() {
        if (modalSource.value === 'Milk Sales') {
            modalMilkFields.style.display = 'block';
            modalManualField.style.display = 'none';
        } else {
            modalMilkFields.style.display = 'none';
            modalManualField.style.display = 'block';
        }
    }
    modalSource.addEventListener('change', toggleModalIncomeFields);
    toggleModalIncomeFields();

    window.onclick = function(event) {
        if (event.target === document.getElementById('incomeModal')) closeIncomeModal();
        if (event.target === document.getElementById('expenseModal')) closeExpenseModal();
    }
</script>
</body>
</html>