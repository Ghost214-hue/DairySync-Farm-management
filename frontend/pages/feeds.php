<?php
require_once __DIR__ . '/../../backend/pages/feeds.php';
require_once __DIR__ . '/../components/sidebar.php';
require_once __DIR__ . '/../../router/urlHelper.php';

$success_msg = $_SESSION['feed_success'] ?? null;
unset($_SESSION['feed_success']);

$error_msg = $_SESSION['feed_error'] ?? null;
unset($_SESSION['feed_error']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feed Management | MooManager</title>

    <link href="/frontend/css/output.css" rel="stylesheet">

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gradient-to-br from-farm-green-50 via-farm-olive-50 to-farm-green-100 min-h-screen">

<div class="flex min-h-screen">

    <?php renderSidebar(); ?>

    <main class="flex-1 min-w-0 p-4 pt-20 md:p-6 md:pt-6">

        <!-- HEADER -->
        <div class="flex items-center justify-between mb-8">

            <div>
                <h1 class="text-3xl font-bold text-farm-green-900">
                    Feed Management
                </h1>

                <p class="text-farm-green-700 mt-1">
                    Record purchased feeds and monitor feed inventory costs.
                </p>
            </div>

            <button onclick="openModal()"
                    class="bg-farm-green-700 hover:bg-farm-green-800 text-white px-5 py-3 rounded-xl shadow-md transition flex items-center gap-2">

                <i class="fas fa-plus"></i>
                Add Feed
            </button>
        </div>

        <!-- FLASH -->
        <?php if ($success_msg): ?>
            <div class="mb-5 bg-green-100 border border-green-200 text-green-700 px-4 py-3 rounded-xl">
                <?= htmlspecialchars($success_msg) ?>
            </div>
        <?php endif; ?>

        <?php if ($error_msg): ?>
            <div class="mb-5 bg-red-100 border border-red-200 text-red-700 px-4 py-3 rounded-xl">
                <?= htmlspecialchars($error_msg) ?>
            </div>
        <?php endif; ?>

        <!-- STATS -->
        <div class="grid md:grid-cols-3 gap-6 mb-8">

            <div class="bg-white/70 backdrop-blur-sm rounded-2xl shadow p-6">
                <div class="flex justify-between items-center">

                    <div>
                        <p class="text-sm text-farm-green-600">
                            Feed Records
                        </p>

                        <h2 class="text-3xl font-bold text-farm-green-900 mt-2">
                            <?= $total_feed_records ?>
                        </h2>
                    </div>

                    <div class="w-14 h-14 rounded-xl bg-farm-green-100 flex items-center justify-center">
                        <i class="fas fa-box text-farm-green-700 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white/70 backdrop-blur-sm rounded-2xl shadow p-6">
                <div class="flex justify-between items-center">

                    <div>
                        <p class="text-sm text-farm-green-600">
                            Total Feed Quantity
                        </p>

                        <h2 class="text-3xl font-bold text-farm-green-900 mt-2">
                            <?= number_format($total_feed_quantity, 1) ?> kg
                        </h2>
                    </div>

                    <div class="w-14 h-14 rounded-xl bg-blue-100 flex items-center justify-center">
                        <i class="fas fa-weight-hanging text-blue-700 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white/70 backdrop-blur-sm rounded-2xl shadow p-6">
                <div class="flex justify-between items-center">

                    <div>
                        <p class="text-sm text-farm-green-600">
                            Total Feed Cost
                        </p>

                        <h2 class="text-3xl font-bold text-farm-green-900 mt-2">
                            KSh <?= number_format($total_feed_cost, 2) ?>
                        </h2>
                    </div>

                    <div class="w-14 h-14 rounded-xl bg-yellow-100 flex items-center justify-center">
                        <i class="fas fa-coins text-yellow-700 text-xl"></i>
                    </div>
                </div>
            </div>

        </div>

        <!-- TABLE -->
        <div class="bg-white/70 backdrop-blur-sm rounded-2xl shadow overflow-hidden">

            <div class="px-6 py-5 border-b border-farm-green-100">
                <h2 class="text-lg font-semibold text-farm-green-900">
                    Feed Purchase Records
                </h2>
            </div>

            <div class="overflow-x-auto">

                <table class="w-full text-sm">

                    <thead class="bg-farm-green-50 text-farm-green-700 uppercase text-xs">
                        <tr>
                            <th class="px-6 py-4 text-left">Date</th>
                            <th class="px-6 py-4 text-left">Feed</th>
                            <th class="px-6 py-4 text-left">Type</th>
                            <th class="px-6 py-4 text-left">Quantity</th>
                            <th class="px-6 py-4 text-left">Cost</th>
                            <th class="px-6 py-4 text-left">Supplier</th>
                            <th class="px-6 py-4 text-left">Actions</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-farm-green-50">

                    <?php if (empty($feeds)): ?>

                        <tr>
                            <td colspan="7" class="py-20 text-center">

                                <div class="flex flex-col items-center text-farm-green-400">

                                    <i class="fas fa-box-open text-6xl mb-4"></i>

                                    <p class="text-lg font-semibold text-farm-green-700">
                                        No feed records
                                    </p>

                                    <p class="text-sm mt-1">
                                        Start recording feed purchases.
                                    </p>
                                </div>
                            </td>
                        </tr>

                    <?php else: ?>

                        <?php foreach ($feeds as $feed): ?>

                            <tr class="hover:bg-farm-green-50/50 transition">

                                <td class="px-6 py-4">
                                    <?= date('M j, Y', strtotime($feed['purchase_date'])) ?>
                                </td>

                                <td class="px-6 py-4 font-semibold text-farm-green-900">
                                    <?= htmlspecialchars($feed['feed_name']) ?>
                                </td>

                                <td class="px-6 py-4">
                                    <?= htmlspecialchars($feed['feed_type']) ?>
                                </td>

                                <td class="px-6 py-4">
                                    <?= number_format($feed['quantity_kg'], 1) ?> kg
                                </td>

                                <td class="px-6 py-4">
                                    KSh <?= number_format($feed['cost'], 2) ?>
                                </td>

                                <td class="px-6 py-4">
                                    <?= htmlspecialchars($feed['supplier']) ?>
                                </td>

                                <td class="px-6 py-4">

                                    <form method="POST">

                                        <input type="hidden"
                                               name="action"
                                               value="delete_feed">

                                        <input type="hidden"
                                               name="feed_id"
                                               value="<?= $feed['id'] ?>">

                                        <!-- Token is optional for delete, but we include it anyway -->
                                        <input type="hidden" name="form_token" value="<?= $form_token ?>">

                                        <button type="submit"
                                                onclick="return confirm('Delete this feed record?')"
                                                class="text-red-500 hover:text-red-700">

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

<!-- ADD MODAL -->
<div id="feedModal"
     class="fixed inset-0 bg-black/40 backdrop-blur-sm hidden items-center justify-center z-50 p-4">

    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg">

        <div class="flex justify-between items-center px-6 py-4 border-b">
            <h2 class="text-xl font-bold text-farm-green-900">
                Add Feed Record
            </h2>

            <button onclick="closeModal()"
                    class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form method="POST" class="p-6 space-y-4" id="addFeedForm">

            <input type="hidden" name="action" value="add_feed">
            <input type="hidden" name="form_token" value="<?= $form_token ?>">

            <div class="grid grid-cols-2 gap-4">

                <div>
                    <label class="block text-sm font-medium mb-1">
                        Feed Name
                    </label>

                    <input type="text"
                           name="feed_name"
                           required
                           class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-farm-green-400">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">
                        Feed Type
                    </label>

                    <input type="text"
                           name="feed_type"
                           placeholder="Dairy Meal"
                           class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-farm-green-400">
                </div>

            </div>

            <div class="grid grid-cols-2 gap-4">

                <div>
                    <label class="block text-sm font-medium mb-1">
                        Quantity (kg)
                    </label>

                    <input type="number"
                           step="0.1"
                           min="0"
                           name="quantity_kg"
                           class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-farm-green-400">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">
                        Cost
                    </label>

                    <input type="number"
                           step="0.01"
                           min="0"
                           name="cost"
                           class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-farm-green-400">
                </div>

            </div>

            <div class="grid grid-cols-2 gap-4">

                <div>
                    <label class="block text-sm font-medium mb-1">
                        Supplier
                    </label>

                    <input type="text"
                           name="supplier"
                           class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-farm-green-400">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">
                        Purchase Date
                    </label>

                    <input type="date"
                           name="purchase_date"
                           value="<?= date('Y-m-d') ?>"
                           class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-farm-green-400">
                </div>

            </div>

            <div>
                <label class="block text-sm font-medium mb-1">
                    Notes
                </label>

                <textarea name="notes"
                          rows="3"
                          class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-farm-green-400"></textarea>
            </div>

            <button type="submit"
                    id="submitBtn"
                    onclick="this.disabled=true; this.form.submit();"
                    class="w-full bg-farm-green-700 hover:bg-farm-green-800 text-white py-3 rounded-xl font-semibold transition">

                Save Feed Record
            </button>

        </form>

    </div>
</div>

<script>
function openModal() {
    document.getElementById('feedModal').classList.remove('hidden');
    document.getElementById('feedModal').classList.add('flex');
}

function closeModal() {
    document.getElementById('feedModal').classList.add('hidden');
    document.getElementById('feedModal').classList.remove('flex');
}
</script>

</body>
</html>