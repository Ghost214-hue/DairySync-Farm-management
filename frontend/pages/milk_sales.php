<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../backend/pages/milk_sales.php';
require_once __DIR__ . '/../components/sidebar.php';
require_once __DIR__ . '/../../router/urlHelper.php';

$success = $_SESSION['milk_success'] ?? null;
$error   = $_SESSION['milk_error'] ?? null;
unset($_SESSION['milk_success']);
unset($_SESSION['milk_error']);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Milk Sales | MooManager</title>
    <link href="/farm-management/frontend/css/output.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-[#f4f7f2] min-h-screen">
<div class="flex min-h-screen">
    <?php renderSidebar(); ?>
    <main class="flex-1 min-w-0 p-4 pt-20 md:p-7 md:pt-7">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8 flex-wrap gap-4">
            <div>
                <h1 class="text-4xl font-bold text-slate-800">Milk Sales</h1>
                <p class="text-slate-500 mt-2">Record and track milk sales to customers daily.</p>
            </div>
            <div class="flex gap-3 items-center">
                <button onclick="openSaleModal()" 
                        class="bg-emerald-700 hover:bg-emerald-800 text-white px-5 py-3 rounded-2xl font-semibold shadow-sm transition inline-flex items-center gap-2">
                    <i class="fas fa-plus"></i> Record Sale
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
        
        <!-- No Stock Warning -->
        <?php if ($today_milk > 0 && $remaining_milk <= 0): ?>
            <div class="mb-5 bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded-xl flex items-center gap-3">
                <i class="fas fa-exclamation-triangle text-xl"></i>
                <div>
                    <strong>No Milk Available Today</strong>
                    <p class="text-sm">All <?= number_format($today_milk, 1) ?> litres from today's production have been sold. You can still backdate sales to previous days.</p>
                </div>
            </div>
        <?php endif; ?>

        <!-- Stats Row -->
        <div class="grid md:grid-cols-3 gap-6 mb-7">
            <!-- Today's Production -->
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6">
                <div class="w-14 h-14 rounded-2xl bg-blue-100 flex items-center justify-center mb-5">
                    <i class="fas fa-glass-water text-blue-700 text-xl"></i>
                </div>
                <h2 class="text-5xl font-bold text-slate-800"><?= number_format($today_milk, 1) ?> L</h2>
                <p class="text-slate-500 mt-2">Today's Production</p>
                <span class="bg-blue-100 text-blue-700 text-sm px-3 py-1 rounded-full font-medium inline-block mt-2">Today</span>
            </div>
            <!-- Remaining Milk -->
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6">
                <div class="w-14 h-14 rounded-2xl bg-emerald-100 flex items-center justify-center mb-5">
                    <i class="fas fa-droplet text-emerald-700 text-xl"></i>
                </div>
                <h2 class="text-5xl font-bold text-slate-800"><?= number_format($remaining_milk, 1) ?> L</h2>
                <p class="text-slate-500 mt-2">Remaining Milk (Today)</p>
                <span class="bg-green-100 text-green-700 text-sm px-3 py-1 rounded-full font-medium inline-block mt-2">
                    NRM value: KSh <?= number_format(max(0, $remaining_milk * 70), 2) ?>
                </span>
            </div>
            <!-- Today's Sales -->
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-500 mb-1">Today's Sales</p>
                        <h2 class="text-4xl font-bold text-amber-600"><?= number_format($today_sales, 1) ?> L</h2>
                        <p class="text-xs text-slate-400 mt-2">
                            Revenue: KSh <?= number_format($today_sales_amount, 2) ?>
                        </p>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-amber-100 flex items-center justify-center">
                        <i class="fas fa-chart-line text-amber-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Milk Sales Table -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100">
                <h3 class="text-2xl font-bold text-slate-800">Sales Records</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50">
                        <tr class="text-left text-xs uppercase tracking-wider text-slate-400">
                            <th class="px-6 py-4">Date</th>
                            <th class="px-6 py-4">Customer</th>
                            <th class="px-6 py-4">Litres</th>
                            <th class="px-6 py-4">Rate/L</th>
                            <th class="px-6 py-4">Total Amount</th>
                            <th class="px-6 py-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($milk_sales)): ?>
                            <tr>
                                <td colspan="6" class="py-24 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="text-slate-300 text-6xl mb-4"><i class="fas fa-shopping-cart"></i></div>
                                        <h3 class="text-2xl font-bold text-slate-700 mb-2">No sales recorded</h3>
                                        <p class="text-slate-400">Start recording milk sales to customers.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($milk_sales as $sale): ?>
                                <tr class="border-t hover:bg-slate-50 transition">
                                    <td class="px-6 py-4 text-slate-700"><?= date('M j, Y', strtotime($sale['income_date'])) ?></td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center gap-1.5 bg-blue-50 text-blue-700 text-xs font-medium px-2.5 py-1 rounded-full">
                                            <i class="fas fa-user fa-xs"></i>
                                            <?= htmlspecialchars($sale['customer_name']) ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4"><?= number_format($sale['litres'], 1) ?> L</td>
                                    <td class="px-6 py-4 text-slate-600">KSh <?= number_format($sale['rate_per_litre'], 2) ?></td>
                                    <td class="px-6 py-4 font-semibold text-emerald-700">KSh <?= number_format($sale['total_amount'], 2) ?></td>
                                    <td class="px-6 py-4">
                                        <form method="POST" action="<?= UrlHelper::url('milk_sales') ?>" onsubmit="return confirm('Delete this sale record?')" style="display:inline;">
                                            <input type="hidden" name="action" value="delete_sale">
                                            <input type="hidden" name="sale_id" value="<?= $sale['id'] ?>">
                                            <input type="hidden" name="form_token" value="<?= $form_token ?>">
                                            <button type="submit" class="text-red-500 hover:text-red-700 transition" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
        <div class="mt-6 flex justify-center">
            <div class="flex items-center gap-2 bg-white rounded-2xl shadow-sm border border-slate-100 px-4 py-2">
                <?php if ($page > 1): ?>
                    <a href="?page=<?= $page - 1 ?>" class="px-3 py-2 rounded-xl hover:bg-slate-100 text-slate-600 transition">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                <?php endif; ?>
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?= $i ?>"
                       class="px-4 py-2 rounded-xl transition <?= $i == $page ? 'bg-emerald-700 text-white' : 'text-slate-600 hover:bg-slate-100' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
                <?php if ($page < $total_pages): ?>
                    <a href="?page=<?= $page + 1 ?>" class="px-3 py-2 rounded-xl hover:bg-slate-100 text-slate-600 transition">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </main>
</div>

<!-- ======================== MODAL ======================== -->
<div id="saleModal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50">
    <div class="bg-white w-full max-w-lg rounded-3xl shadow-2xl p-7 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-slate-800">Record Milk Sale</h2>
            <button onclick="closeSaleModal()" class="text-slate-400 hover:text-slate-600 transition">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form method="POST" action="<?= UrlHelper::url('milk_sales') ?>" class="space-y-5" id="saleForm">
            <input type="hidden" name="action" value="add_sale">
            <input type="hidden" name="form_token" value="<?= $form_token ?>">

            <!-- Sale Date -->
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Sale Date</label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                        <i class="fas fa-calendar fa-sm"></i>
                    </span>
                    <input type="date" name="sale_date" 
                           value="<?= date('Y-m-d') ?>" 
                           max="<?= date('Y-m-d') ?>"
                           required
                           class="w-full border border-slate-200 rounded-xl pl-10 pr-4 py-3 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                </div>
                <p class="text-xs text-slate-500 mt-1">You can select today or any previous date</p>
            </div>

            <!-- Customer Selection -->
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Customer</label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                        <i class="fas fa-user fa-sm"></i>
                    </span>
                    <select name="customer_id" id="customerSelect" required
                            class="w-full border border-slate-200 rounded-xl pl-10 pr-4 py-3 focus:outline-none focus:ring-2 focus:ring-emerald-400 appearance-none">
                        <option value="">-- Select Customer --</option>
                        <?php foreach ($customers as $customer): ?>
                            <option value="<?= $customer['id'] ?>" data-price="<?= $customer['price_per_litre'] ?>">
                                <?= htmlspecialchars($customer['customer_name']) ?> (KSh <?= number_format($customer['price_per_litre'], 2) ?>/L)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Litres Sold -->
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Litres Sold</label>
                <div class="relative">
                    <span class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400">L</span>
                    <input type="number" step="0.1" min="0" 
                           id="litresSoldInput" name="litres_sold" value="0" required
                           class="w-full border border-slate-200 rounded-xl px-4 py-3 pr-8 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                </div>
            </div>

            <!-- Price per Litre (Display) -->
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Price per Litre (KSh)</label>
                <div class="bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-700 font-semibold">
                    <span id="pricePerLitre">0.00</span>
                </div>
            </div>

            <!-- Total Amount (Auto-calculated) -->
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Total Amount (KSh)</label>
                <div class="bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-700 font-bold text-lg">
                    <span id="totalAmount">0.00</span>
                </div>
                <p class="text-xs text-slate-400 mt-1" id="calculation"></p>
            </div>

            <button type="submit"
                    id="submitBtn"
                    onclick="this.disabled=true; this.form.submit();"
                    class="w-full bg-emerald-700 hover:bg-emerald-800 text-white font-semibold py-3 rounded-2xl transition">
                Record Sale
            </button>
        </form>
    </div>
</div>

<script>
const customersData = <?php echo json_encode($customers); ?>;

function openSaleModal() {
    const modal = document.getElementById('saleModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeSaleModal() {
    const modal = document.getElementById('saleModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

document.addEventListener('DOMContentLoaded', function() {
    const customerSelect = document.getElementById('customerSelect');
    const litresInput = document.querySelector('input[name="litres_sold"]');
    const priceSpan = document.getElementById('pricePerLitre');
    const totalSpan = document.getElementById('totalAmount');
    const calcSpan = document.getElementById('calculation');

    function updateCalculations() {
        const litres = parseFloat(litresInput.value) || 0;
        const selectedOption = customerSelect.options[customerSelect.selectedIndex];
        const price = parseFloat(selectedOption.dataset.price) || 0;
        const total = litres * price;

        priceSpan.textContent = price.toFixed(2);
        totalSpan.textContent = total.toFixed(2);
        calcSpan.textContent = `${litres.toFixed(1)} L × KSh ${price.toFixed(2)} = KSh ${total.toFixed(2)}`;
    }

    customerSelect.addEventListener('change', updateCalculations);
    litresInput.addEventListener('input', updateCalculations);
    updateCalculations();
});
</script>
</body>
</html>