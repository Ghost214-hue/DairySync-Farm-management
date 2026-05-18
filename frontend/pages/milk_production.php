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
                <h1 class="text-4xl font-bold text-slate-800">
                    Milk Production
                </h1>

                <p class="text-slate-500 mt-2">
                    Track daily milk yields and monitor production trends.
                </p>
            </div>

            <button
                onclick="openModal()"
                class="bg-emerald-700 hover:bg-emerald-800 text-white px-5 py-3 rounded-2xl font-semibold shadow-sm transition">
                <i class="fas fa-plus mr-2"></i>
                Record Milk
            </button>
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

        <!-- Stats -->
        <div class="grid md:grid-cols-2 gap-6 mb-7">

            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6">
                <div class="flex items-start justify-between">
                    <div>
                        <div class="w-14 h-14 rounded-2xl bg-emerald-100 flex items-center justify-center mb-5">
                            <i class="fas fa-glass-water text-emerald-700 text-xl"></i>
                        </div>

                        <h2 class="text-5xl font-bold text-slate-800">
                            <?= number_format($today_milk, 1) ?>L
                        </h2>

                        <p class="text-slate-500 mt-2">
                            Today's Production
                        </p>
                    </div>

                    <span class="bg-green-100 text-green-700 text-sm px-3 py-1 rounded-full font-medium">
                        Today
                    </span>
                </div>
            </div>

            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6">
                <div class="w-14 h-14 rounded-2xl bg-emerald-100 flex items-center justify-center mb-5">
                    <i class="fas fa-chart-line text-emerald-700 text-xl"></i>
                </div>

                <h2 class="text-5xl font-bold text-slate-800">
                    <?= $total_records ?>
                </h2>

                <p class="text-slate-500 mt-2">
                    Total Records
                </p>
            </div>

        </div>

        <!-- Table -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">

            <div class="px-6 py-5 border-b border-slate-100">
                <h3 class="text-2xl font-bold text-slate-800">
                    All Milk Records
                </h3>
            </div>

            <div class="overflow-x-auto">

                <table class="w-full">

                    <thead class="bg-slate-50">
                    <tr class="text-left text-xs uppercase tracking-wider text-slate-400">
                        <th class="px-6 py-4">Date</th>
                        <th class="px-6 py-4">Cow</th>
                        <th class="px-6 py-4">Session</th>
                        <th class="px-6 py-4">Quantity (L)</th>
                        <th class="px-6 py-4">Quality</th>
                        <th class="px-6 py-4">Actions</th>
                    </tr>
                    </thead>

                    <tbody>

                    <?php if (empty($milk_records)): ?>

                        <tr>
                            <td colspan="6" class="py-24 text-center">

                                <div class="flex flex-col items-center">

                                    <div class="text-slate-300 text-6xl mb-4">
                                        <i class="fas fa-glass-water"></i>
                                    </div>

                                    <h3 class="text-2xl font-bold text-slate-700 mb-2">
                                        No milk records
                                    </h3>

                                    <p class="text-slate-400">
                                        Start recording milk production.
                                    </p>

                                </div>

                            </td>
                        </tr>

                    <?php else: ?>

                        <?php foreach ($milk_records as $record): ?>

                            <tr class="border-t border-slate-100 hover:bg-slate-50 transition">

                                <td class="px-6 py-4 text-slate-700">
                                    <?= date('M j, Y', strtotime($record['production_date'])) ?>
                                </td>

                                <td class="px-6 py-4 font-semibold text-slate-800">
                                    <?= htmlspecialchars($record['cow_name'] ?: 'Unknown Cow') ?>
                                </td>

                                <td class="px-6 py-4">
                                    <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-sm">
                                        <?= htmlspecialchars($record['session']) ?>
                                    </span>
                                </td>

                                <td class="px-6 py-4 font-semibold text-slate-700">
                                    <?= number_format($record['quantity'], 1) ?> L
                                </td>

                                <td class="px-6 py-4">

                                    <?php
                                    $qualityColor = match($record['quality']) {
                                        'Excellent' => 'bg-green-100 text-green-700',
                                        'Good' => 'bg-emerald-100 text-emerald-700',
                                        'Average' => 'bg-yellow-100 text-yellow-700',
                                        default => 'bg-red-100 text-red-700'
                                    };
                                    ?>

                                    <span class="<?= $qualityColor ?> px-3 py-1 rounded-full text-sm">
                                        <?= htmlspecialchars($record['quality']) ?>
                                    </span>
                                </td>

                                <td class="px-6 py-4">

                                    <form method="POST">

                                        <input type="hidden" name="action" value="delete_record">
                                        <input type="hidden" name="record_id" value="<?= $record['id'] ?>">

                                        <button
                                            type="submit"
                                            class="text-red-500 hover:text-red-700 transition">
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

    </main>

</div>

<!-- MODAL -->
<div
    id="milkModal"
    class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50">

    <div class="bg-white w-full max-w-lg rounded-3xl shadow-2xl p-7">

        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-slate-800">
                Record Milk
            </h2>

            <button onclick="closeModal()" class="text-slate-400 hover:text-slate-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <form method="POST" class="space-y-5">

            <input type="hidden" name="action" value="add_record">

            <!-- Cow -->
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">
                    Select Cow
                </label>

                <select
                    name="cow_id"
                    required
                    class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-emerald-500">

                    <option value="">Choose cow</option>

                    <?php foreach ($cows as $cow): ?>
                        <option value="<?= $cow['id'] ?>">
                            <?= htmlspecialchars($cow['cow_name']) ?>
                            (<?= htmlspecialchars($cow['ear_tag']) ?>)
                        </option>
                    <?php endforeach; ?>

                </select>
            </div>

            <!-- Quantity -->
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">
                    Quantity (Litres)
                </label>

                <input
                    type="number"
                    step="0.1"
                    min="0"
                    name="quantity"
                    required
                    class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <!-- Grid -->
            <div class="grid grid-cols-2 gap-4">

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Session
                    </label>

                    <select
                        name="session_type"
                        class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-emerald-500">

                        <option>Morning</option>
                        <option>Afternoon</option>
                        <option>Evening</option>

                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Quality
                    </label>

                    <select
                        name="quality"
                        class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-emerald-500">

                        <option>Excellent</option>
                        <option selected>Good</option>
                        <option>Average</option>
                        <option>Poor</option>

                    </select>
                </div>

            </div>

            <!-- Date -->
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">
                    Production Date
                </label>

                <input
                    type="date"
                    name="production_date"
                    value="<?= date('Y-m-d') ?>"
                    required
                    class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <!-- Notes -->
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">
                    Notes
                </label>

                <textarea
                    name="notes"
                    rows="3"
                    class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-emerald-500"></textarea>
            </div>

            <button
                type="submit"
                class="w-full bg-emerald-700 hover:bg-emerald-800 text-white font-semibold py-3 rounded-2xl transition">
                Save Record
            </button>

        </form>

    </div>
</div>

<script>
function openModal() {
    document.getElementById('milkModal').classList.remove('hidden');
    document.getElementById('milkModal').classList.add('flex');
}

function closeModal() {
    document.getElementById('milkModal').classList.add('hidden');
    document.getElementById('milkModal').classList.remove('flex');
}
</script>

</body>
</html>