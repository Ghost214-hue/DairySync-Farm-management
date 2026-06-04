<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../backend/pages/milk_production.php';
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
    <title>Milk Production | MooManager</title>
    <link href="/farm-management/frontend/css/output.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-[#f4f7f2] min-h-screen">

<div class="flex min-h-screen">
    <?php renderSidebar(); ?>

    <main class="flex-1 p-7">

        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-4xl font-bold text-slate-800">Milk Production</h1>
                <p class="text-slate-500 mt-2">Track daily milk per cow – morning and evening production.</p>
            </div>
            <div class="flex gap-3 items-center">
                <!-- Current milk price badge + edit button -->
                <div class="bg-white/80 backdrop-blur-sm rounded-full px-4 py-2 border border-slate-200 flex items-center gap-2 shadow-sm">
                    <span class="text-sm text-slate-600">Milk price:</span>
                    <span class="font-bold text-emerald-700">KSh <?= number_format($milk_price, 2) ?></span>
                    <button onclick="openPriceModal()" class="text-slate-400 hover:text-emerald-600 transition">
                        <i class="fas fa-pen fa-xs"></i>
                    </button>
                </div>
                <button onclick="openModal(null, null, null, null, null, null)"
                        class="bg-emerald-700 hover:bg-emerald-800 text-white px-5 py-3 rounded-2xl font-semibold shadow-sm transition">
                    <i class="fas fa-plus mr-2"></i> Add / Edit Record
                </button>
            </div>
        </div>

        <!-- Alerts -->
        <?php if ($success): ?>
            <div class="mb-5 bg-green-100 border border-green-200 text-green-700 px-4 py-3 rounded-xl">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="mb-5 bg-red-100 border border-red-200 text-red-700 px-4 py-3 rounded-xl">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <!-- Stats Row (3 cards) -->
        <div class="grid md:grid-cols-3 gap-6 mb-7">

            <!-- Today's Milk -->
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6">
                <div class="w-14 h-14 rounded-2xl bg-emerald-100 flex items-center justify-center mb-5">
                    <i class="fas fa-glass-water text-emerald-700 text-xl"></i>
                </div>
                <h2 class="text-5xl font-bold text-slate-800"><?= number_format($today_milk, 1) ?> L</h2>
                <p class="text-slate-500 mt-2">Today's Total Production</p>
                <span class="bg-green-100 text-green-700 text-sm px-3 py-1 rounded-full font-medium inline-block mt-2">Today</span>
            </div>

            <!-- Total Records -->
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6">
                <div class="w-14 h-14 rounded-2xl bg-emerald-100 flex items-center justify-center mb-5">
                    <i class="fas fa-chart-line text-emerald-700 text-xl"></i>
                </div>
                <h2 class="text-5xl font-bold text-slate-800"><?= $total_records ?></h2>
                <p class="text-slate-500 mt-2">Total Records</p>
            </div>

        </div>

        <!-- Milk Records Table -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100">
                <h3 class="text-2xl font-bold text-slate-800">Daily Milk Records</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50">
                        <tr class="text-left text-xs uppercase tracking-wider text-slate-400">
                            <th class="px-6 py-4">Date</th>
                            <th class="px-6 py-4">Cow</th>
                            <th class="px-6 py-4">Morning (L)</th>
                            <th class="px-6 py-4">Evening (L)</th>
                            <th class="px-6 py-4">Total (L)</th>
                            <th class="px-6 py-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($milk_records)): ?>
                            <tr>
                                <td colspan="6" class="py-24 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="text-slate-300 text-6xl mb-4"><i class="fas fa-glass-water"></i></div>
                                        <h3 class="text-2xl font-bold text-slate-700 mb-2">No milk records</h3>
                                        <p class="text-slate-400">Start recording daily milk production.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($milk_records as $record): ?>
                                <tr class="border-t hover:bg-slate-50 transition">
                                    <td class="px-6 py-4 text-slate-700"><?= date('M j, Y', strtotime($record['production_date'])) ?></td>
                                    <td class="px-6 py-4 font-semibold text-slate-800"><?= htmlspecialchars($record['cow_name'] ?: 'Unknown Cow') ?></td>
                                    <td class="px-6 py-4"><?= number_format($record['morning_litres'], 1) ?> L</td>
                                    <td class="px-6 py-4"><?= number_format($record['evening_litres'], 1) ?> L</td>
                                    <td class="px-6 py-4 font-semibold text-emerald-700"><?= number_format($record['total_litres'], 1) ?> L</td>
                                    <td class="px-6 py-4">
                                        <div class="flex gap-2">
                                            <button onclick="openModal(
                                                <?= $record['id'] ?>,
                                                <?= $record['cow_id'] ?>,
                                                '<?= $record['production_date'] ?>',
                                                <?= $record['morning_litres'] ?>,
                                                <?= $record['evening_litres'] ?>,
                                                '<?= addslashes($record['notes'] ?? '') ?>'
                                            )" class="text-blue-500 hover:text-blue-700 transition">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form method="POST" onsubmit="return confirm('Delete this record?')">
                                                <input type="hidden" name="action" value="delete_record">
                                                <input type="hidden" name="record_id" value="<?= $record['id'] ?>">
                                                <button type="submit" class="text-red-500 hover:text-red-700 transition">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Monthly Total Card -->
        <div class="mt-8 bg-gradient-to-r from-emerald-50 to-teal-50 rounded-3xl shadow-sm border border-emerald-100 p-6">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 rounded-2xl bg-emerald-200 flex items-center justify-center">
                        <i class="fas fa-calendar-alt text-emerald-700 text-2xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-emerald-700 font-medium">Total Milk Production</p>
                        <p class="text-3xl font-bold text-emerald-900"><?= number_format($current_month_total, 1) ?> L</p>
                        <p class="text-xs text-emerald-600 mt-1">For <?= date('F Y') ?> (resets monthly)</p>
                    </div>
                </div>
                <div class="bg-white/60 px-4 py-2 rounded-xl">
                    <i class="fas fa-chart-simple text-emerald-600 mr-1"></i>
                    <span class="text-sm font-medium text-emerald-800">
                        Monthly average: <?= $total_records > 0 ? number_format($current_month_total / max(1, date('d')), 1) : 0 ?> L/day
                    </span>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
        <div class="mt-6 flex justify-center">
            <div class="flex items-center gap-2 bg-white rounded-2xl shadow-sm border border-slate-100 px-4 py-2">
                <?php if ($page > 1): ?>
                    <a href="?page=<?= $page - 1 ?>" class="px-3 py-2 rounded-xl hover:bg-slate-100 text-slate-600">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                <?php endif; ?>
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?= $i ?>"
                       class="px-4 py-2 rounded-xl <?= $i == $page ? 'bg-emerald-700 text-white' : 'text-slate-600 hover:bg-slate-100' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
                <?php if ($page < $total_pages): ?>
                    <a href="?page=<?= $page + 1 ?>" class="px-3 py-2 rounded-xl hover:bg-slate-100 text-slate-600">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

    </main>
</div>

<!-- ============================================================
     MODAL: Add / Edit Milk Record
     ============================================================ -->
<div id="milkModal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50">
    <div class="bg-white w-full max-w-lg rounded-3xl shadow-2xl p-7 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-6">
            <h2 id="modalTitle" class="text-2xl font-bold text-slate-800">Add Milk Record</h2>
            <button onclick="closeModal()" class="text-slate-400 hover:text-slate-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form id="milkForm" method="POST" class="space-y-5">
            <input type="hidden" name="action" value="add_record">
            <input type="hidden" id="recordId" name="record_id" value="0">

            <!-- Cow -->
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Cow</label>
                <select id="cowId" name="cow_id" required
                        class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                    <option value="">Choose cow</option>
                    <?php foreach ($cows as $cow): ?>
                        <option value="<?= $cow['id'] ?>">
                            <?= htmlspecialchars($cow['cow_name']) ?> (<?= htmlspecialchars($cow['ear_tag']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Date -->
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Production Date</label>
                <input type="date" id="prodDate" name="production_date"
                       value="<?= date('Y-m-d') ?>" required
                       class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-emerald-400">
            </div>

            <!-- Morning & Evening -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Morning (Litres)</label>
                    <input type="number" step="0.1" min="0" id="morningLitres" name="morning_litres" value="0"
                           class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Evening (Litres)</label>
                    <input type="number" step="0.1" min="0" id="eveningLitres" name="evening_litres" value="0"
                           class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                </div>
            </div>



            <!-- Notes -->
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Notes</label>
                <textarea id="notes" name="notes" rows="2"
                          class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-emerald-400"></textarea>
            </div>

            <button type="submit"
                    class="w-full bg-emerald-700 hover:bg-emerald-800 text-white font-semibold py-3 rounded-2xl transition">
                Save Record
            </button>
        </form>
    </div>
</div>

<!-- ============================================================
     MODAL: Edit Milk Price
     ============================================================ -->
<div id="priceModal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50">
    <div class="bg-white w-full max-w-md rounded-3xl shadow-2xl p-7">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-slate-800">Edit Milk Price</h2>
            <button onclick="closePriceModal()" class="text-slate-400 hover:text-slate-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form method="POST" class="space-y-5">
            <input type="hidden" name="action" value="update_price">
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Price per Litre (KSh)</label>
                <input type="number" step="0.01" name="milk_price"
                       value="<?= number_format($milk_price, 2) ?>"
                       class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-emerald-400"
                       required>
            </div>
            <button type="submit"
                    class="w-full bg-emerald-700 hover:bg-emerald-800 text-white font-semibold py-3 rounded-2xl transition">
                Save Price
            </button>
        </form>
    </div>
</div>

<!-- ============================================================
     Scripts
     ============================================================ -->
<script>
function openModal(id, cowId, date, morning, evening, notes) {
    const modal         = document.getElementById('milkModal');
    const title         = document.getElementById('modalTitle');
    const recordIdField = document.getElementById('recordId');
    const cowSelect     = document.getElementById('cowId');
    const dateField     = document.getElementById('prodDate');
    const morningField  = document.getElementById('morningLitres');
    const eveningField  = document.getElementById('eveningLitres');
    const notesField    = document.getElementById('notes');

    if (id) {
        title.innerText      = 'Edit Milk Record';
        recordIdField.value  = id;
        cowSelect.value      = cowId;
        dateField.value      = date;
        morningField.value   = morning;
        eveningField.value   = evening;
        notesField.value     = notes || '';
    } else {
        title.innerText      = 'Add Milk Record';
        recordIdField.value  = 0;
        cowSelect.value      = '';
        dateField.value      = '<?= date('Y-m-d') ?>';
        morningField.value   = 0;
        eveningField.value   = 0;
        notesField.value     = '';
    }

    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeModal() {
    const modal = document.getElementById('milkModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function openPriceModal() {
    const modal = document.getElementById('priceModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closePriceModal() {
    const modal = document.getElementById('priceModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}
</script>

</body>
</html>