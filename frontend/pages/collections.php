<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../backend/pages/collections.php';
require_once __DIR__ . '/../components/sidebar.php';
require_once __DIR__ . '/../../router/urlHelper.php';

$success = $_SESSION['collection_success'] ?? null;
$error   = $_SESSION['collection_error'] ?? null;
unset($_SESSION['collection_success']);
unset($_SESSION['collection_error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Collections | DairySync</title>
    <link href="/frontend/css/output.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-[#f4f7f2] min-h-screen">
<div class="flex min-h-screen">
    <?php renderSidebar(); ?>
    <main class="flex-1 min-w-0 p-4 pt-20 md:p-7 md:pt-7">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8 flex-wrap gap-4">
            <div>
                <h1 class="text-4xl font-bold text-slate-800">Collections</h1>
                <p class="text-slate-500 mt-2">Track customer payments and outstanding balances.</p>
            </div>
            <div class="flex gap-3 items-center">
                <button onclick="openPaymentModal()" 
                        class="bg-emerald-700 hover:bg-emerald-800 text-white px-5 py-3 rounded-2xl font-semibold shadow-sm transition inline-flex items-center gap-2">
                    <i class="fas fa-plus"></i> Record Payment
                </button>
            </div>
        </div>

        <!-- Alerts -->
        <?php if ($success): ?>
            <div class="mb-5 bg-green-100 border border-green-200 text-green-700 px-4 py-3 rounded-xl flex items-center gap-3">
                <i class="fas fa-check-circle"></i>
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="mb-5 bg-red-100 border border-red-200 text-red-700 px-4 py-3 rounded-xl flex items-center gap-3">
                <i class="fas fa-exclamation-circle"></i>
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <!-- Summary Cards -->
        <div class="grid md:grid-cols-4 gap-6 mb-7">
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6">
                <div class="w-14 h-14 rounded-2xl bg-blue-100 flex items-center justify-center mb-4">
                    <i class="fas fa-users text-blue-700 text-xl"></i>
                </div>
                <h2 class="text-3xl font-bold text-slate-800"><?= number_format(count($customers_summary)) ?></h2>
                <p class="text-slate-500 mt-1">Total Customers</p>
            </div>
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6">
                <div class="w-14 h-14 rounded-2xl bg-purple-100 flex items-center justify-center mb-4">
                    <i class="fas fa-bottle-water text-purple-700 text-xl"></i>
                </div>
                <h2 class="text-3xl font-bold text-slate-800"><?= number_format($totals['total_litres'], 1) ?> L</h2>
                <p class="text-slate-500 mt-1">Total Litres Sold</p>
            </div>
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6">
                <div class="w-14 h-14 rounded-2xl bg-emerald-100 flex items-center justify-center mb-4">
                    <i class="fas fa-money-bill-wave text-emerald-700 text-xl"></i>
                </div>
                <h2 class="text-3xl font-bold text-slate-800">KSh <?= number_format($totals['total_sales'], 2) ?></h2>
                <p class="text-slate-500 mt-1">Total Sales</p>
            </div>
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6">
                <div class="w-14 h-14 rounded-2xl bg-amber-100 flex items-center justify-center mb-4">
                    <i class="fas fa-clock text-amber-700 text-xl"></i>
                </div>
                <h2 class="text-3xl font-bold text-amber-600">KSh <?= number_format($totals['total_balance'], 2) ?></h2>
                <p class="text-slate-500 mt-1">Outstanding Balance</p>
            </div>
        </div>

        <!-- Customers Table -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100">
                <h3 class="text-2xl font-bold text-slate-800">Customer Payment Summary</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50">
                        <tr class="text-left text-xs uppercase tracking-wider text-slate-400">
                            <th class="px-6 py-4">Customer</th>
                            <th class="px-6 py-4">Litres Sold</th>
                            <th class="px-6 py-4">Total Sales</th>
                            <th class="px-6 py-4">Amount Paid</th>
                            <th class="px-6 py-4">Balance</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($customers_summary)): ?>
                            <tr>
                                <td colspan="7" class="py-24 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="text-slate-300 text-6xl mb-4"><i class="fas fa-receipt"></i></div>
                                        <h3 class="text-2xl font-bold text-slate-700 mb-2">No collections yet</h3>
                                        <p class="text-slate-400">Start recording milk sales and payments to see summaries here.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($customers_summary as $summary): ?>
                                <tr class="border-t hover:bg-slate-50 transition">
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center gap-1.5 bg-blue-50 text-blue-700 text-sm font-medium px-3 py-1.5 rounded-full">
                                            <i class="fas fa-user fa-xs"></i>
                                            <?= htmlspecialchars($summary['customer_name']) ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4"><?= number_format($summary['total_litres'], 1) ?> L</td>
                                    <td class="px-6 py-4 font-semibold text-slate-700">KSh <?= number_format($summary['total_sales'], 2) ?></td>
                                    <td class="px-6 py-4 font-semibold text-emerald-700">KSh <?= number_format($summary['total_paid'], 2) ?></td>
                                    <td class="px-6 py-4 font-semibold <?= $summary['balance'] > 0 ? 'text-red-600' : 'text-green-600' ?>">
                                        KSh <?= number_format($summary['balance'], 2) ?>
                                    </td>
                                    <td class="px-6 py-4">
                                        <?php if ($summary['status'] === 'Paid'): ?>
                                            <span class="bg-green-100 text-green-700 text-xs font-medium px-3 py-1 rounded-full">Paid</span>
                                        <?php elseif ($summary['status'] === 'Overdue'): ?>
                                            <span class="bg-red-100 text-red-700 text-xs font-medium px-3 py-1 rounded-full">Overdue</span>
                                        <?php else: ?>
                                            <span class="bg-amber-100 text-amber-700 text-xs font-medium px-3 py-1 rounded-full">Partial</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4">
                                        <button onclick="openPaymentModalFor(<?= $summary['customer_id'] ?>, '<?= htmlspecialchars($summary['customer_name']) ?>')" 
                                                class="text-emerald-600 hover:text-emerald-800 transition" title="Record Payment">
                                            <i class="fas fa-money-bill-wave"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<!-- ======================== PAYMENT MODAL ======================== -->
<div id="paymentModal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50">
    <div class="bg-white w-full max-w-lg rounded-3xl shadow-2xl p-7 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-slate-800">Record Payment</h2>
            <button onclick="closePaymentModal()" class="text-slate-400 hover:text-slate-600 transition">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form method="POST" action="<?= UrlHelper::url('collections') ?>" class="space-y-5" id="paymentForm">
            <input type="hidden" name="action" value="record_payment">

            <!-- Customer Selection -->
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Customer</label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                        <i class="fas fa-user fa-sm"></i>
                    </span>
                    <select name="customer_id" id="paymentCustomerId" required
                            class="w-full border border-slate-200 rounded-xl pl-10 pr-4 py-3 focus:outline-none focus:ring-2 focus:ring-emerald-400 appearance-none">
                        <option value="">-- Select Customer --</option>
                        <?php foreach ($all_customers as $customer): ?>
                            <option value="<?= $customer['id'] ?>">
                                <?= htmlspecialchars($customer['customer_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php if (empty($all_customers)): ?>
                    <p class="text-xs text-red-500 mt-1">No active customers found. Please add customers first.</p>
                <?php endif; ?>
            </div>

            <!-- Amount Paid -->
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Amount Paid (KSh)</label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 font-semibold">KSh</span>
                    <input type="number" step="0.01" min="0" name="amount_paid" required
                           class="w-full border border-slate-200 rounded-xl pl-16 pr-4 py-3 focus:outline-none focus:ring-2 focus:ring-emerald-400"
                           placeholder="0.00">
                </div>
            </div>

            <!-- Payment Date -->
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Payment Date</label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                        <i class="fas fa-calendar fa-sm"></i>
                    </span>
                    <input type="date" name="payment_date" value="<?= date('Y-m-d') ?>" required
                           class="w-full border border-slate-200 rounded-xl pl-10 pr-4 py-3 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                </div>
            </div>

            <!-- Payment Method -->
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Payment Method</label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                        <i class="fas fa-wallet fa-sm"></i>
                    </span>
                    <select name="payment_method" required
                            class="w-full border border-slate-200 rounded-xl pl-10 pr-4 py-3 focus:outline-none focus:ring-2 focus:ring-emerald-400 appearance-none">
                        <option value="Cash">Cash</option>
                        <option value="M-Pesa">M-Pesa</option>
                        <option value="Bank Transfer">Bank Transfer</option>
                        <option value="Cheque">Cheque</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
            </div>

            <!-- Reference Number -->
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Reference Number (Optional)</label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                        <i class="fas fa-hashtag fa-sm"></i>
                    </span>
                    <input type="text" name="reference_number" 
                           class="w-full border border-slate-200 rounded-xl pl-10 pr-4 py-3 focus:outline-none focus:ring-2 focus:ring-emerald-400"
                           placeholder="Transaction ID / Receipt No.">
                </div>
            </div>

            <button type="submit"
                    class="w-full bg-emerald-700 hover:bg-emerald-800 text-white font-semibold py-3 rounded-2xl transition">
                Record Payment
            </button>
        </form>
    </div>
</div>

<script>
const customersData = <?php echo json_encode($all_customers); ?>;

function openPaymentModal() {
    // Reset form
    document.getElementById('paymentForm').reset();
    document.getElementById('paymentCustomerId').selectedIndex = 0;
    
    const modal = document.getElementById('paymentModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function openPaymentModalFor(customerId, customerName) {
    document.getElementById('paymentCustomerId').value = customerId;
    
    const modal = document.getElementById('paymentModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closePaymentModal() {
    const modal = document.getElementById('paymentModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

// Close modal on backdrop click
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('paymentModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closePaymentModal();
            }
        });
    }
});
</script>
</body>
</html>