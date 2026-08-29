<?php
require_once __DIR__ . '/../../backend/pages/health.php';
require_once __DIR__ . '/../components/sidebar.php';
require_once __DIR__ . '/../../router/urlHelper.php';

$success = $_SESSION['health_success'] ?? null;
$error   = $_SESSION['health_error'] ?? null;

unset($_SESSION['health_success']);
unset($_SESSION['health_error']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Health Records | DairySync</title>
    <link href="/frontend/css/output.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-emerald-50 via-lime-50 to-emerald-100 min-h-screen">

<div class="flex min-h-screen">
    <?php renderSidebar(); ?>

    <!-- Main content: add pt-20 on mobile to avoid fixed header overlap -->
    <main class="flex-1 min-w-0 p-4 pt-20 md:p-6 md:pt-6 overflow-x-auto">

        <!-- Page header (clean, no extra top bar) -->
        <div class="flex flex-col md:flex-row justify-between md:items-center gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-bold text-emerald-900">Health Records</h1>
                <p class="text-emerald-700 mt-1">Track vaccinations, treatments, and health checks.</p>
            </div>
            <button onclick="openModal()"
                    class="bg-emerald-700 hover:bg-emerald-800 text-white px-5 py-3 rounded-xl font-semibold shadow-md transition">
                <i class="fa-solid fa-plus mr-2"></i> Add Record
            </button>
        </div>

        <!-- Flash messages -->
        <?php if ($success): ?>
            <div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 px-4 py-3 rounded-lg shadow-sm">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 px-4 py-3 rounded-lg shadow-sm">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <!-- Records table -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl border border-white/60 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-emerald-50/80">
                        <tr class="text-left text-xs font-semibold text-emerald-700 uppercase tracking-wider">
                            <th class="px-6 py-4">Date</th>
                            <th class="px-6 py-4">Cow</th>
                            <th class="px-6 py-4">Condition</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4">Treatment</th>
                            <th class="px-6 py-4">Notes</th>
                            <th class="px-6 py-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($health_records)): ?>
                            <tr>
                                <td colspan="7" class="py-20 text-center">
                                    <div class="flex flex-col items-center">
                                        <i class="fa-solid fa-heart-pulse text-5xl text-emerald-200 mb-4"></i>
                                        <h3 class="text-xl font-semibold text-emerald-800">No health records</h3>
                                        <p class="text-emerald-500 mt-1">Click "Add Record" to get started.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($health_records as $record):
                                $badge = match($record['status']) {
                                    'Healthy'   => 'bg-green-100 text-green-700',
                                    'Recovered' => 'bg-blue-100 text-blue-700',
                                    'Critical'  => 'bg-red-100 text-red-700',
                                    default     => 'bg-yellow-100 text-yellow-700'
                                };
                            ?>
                            <tr class="border-t border-emerald-100 hover:bg-emerald-50/30 transition">
                                <td class="px-6 py-4 text-emerald-700"><?= date('M j, Y', strtotime($record['record_date'])) ?></td>
                                <td class="px-6 py-4 font-semibold text-emerald-900"><?= htmlspecialchars($record['cow_name']) ?></td>
                                <td class="px-6 py-4 text-emerald-800"><?= htmlspecialchars($record['condition_name']) ?></td>
                                <td class="px-6 py-4">
                                    <span class="<?= $badge ?> px-3 py-1 rounded-full text-xs font-semibold">
                                        <?= htmlspecialchars($record['status']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-emerald-700"><?= htmlspecialchars($record['treatment']) ?></td>
                                <td class="px-6 py-4 text-emerald-600"><?= htmlspecialchars($record['notes']) ?></td>
                                <td class="px-6 py-4">
                                    <form method="POST" onsubmit="return confirm('Delete this record?')">
                                        <input type="hidden" name="action" value="delete_record">
                                        <input type="hidden" name="record_id" value="<?= $record['id'] ?>">
                                        <button type="submit" class="text-red-500 hover:text-red-700 transition">
                                            <i class="fa-solid fa-trash"></i>
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

<!-- Modal (Tailwind styled) -->
<div id="recordModal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl w-full max-w-lg shadow-2xl">
        <div class="flex items-center justify-between p-6 border-b border-emerald-100">
            <h2 class="text-2xl font-bold text-emerald-900">Add Health Record</h2>
            <button onclick="closeModal()" class="text-emerald-500 hover:text-emerald-700 text-2xl">&times;</button>
        </div>
        <form method="POST" class="p-6 space-y-5">
            <input type="hidden" name="action" value="add_record">

            <div>
                <label class="block text-sm font-semibold text-emerald-800 mb-1">Cow</label>
                <select name="cow_id" required class="w-full border border-emerald-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-emerald-500">
                    <option value="">Select cow</option>
                    <?php foreach ($cows as $cow): ?>
                        <option value="<?= $cow['id'] ?>"><?= htmlspecialchars($cow['cow_name']) ?> (#<?= htmlspecialchars($cow['ear_tag']) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-emerald-800 mb-1">Condition</label>
                <input type="text" name="condition_name" required class="w-full border border-emerald-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-emerald-500">
            </div>

            <div>
                <label class="block text-sm font-semibold text-emerald-800 mb-1">Status</label>
                <select name="status" class="w-full border border-emerald-200 rounded-xl px-4 py-3">
                    <?php foreach (HEALTH_STATUSES as $status): ?>
                        <option value="<?= $status ?>"><?= $status ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-emerald-800 mb-1">Treatment</label>
                <textarea name="treatment" rows="2" class="w-full border border-emerald-200 rounded-xl px-4 py-3"></textarea>
            </div>

            <div>
                <label class="block text-sm font-semibold text-emerald-800 mb-1">Notes</label>
                <textarea name="notes" rows="2" class="w-full border border-emerald-200 rounded-xl px-4 py-3"></textarea>
            </div>

            <div>
                <label class="block text-sm font-semibold text-emerald-800 mb-1">Record Date</label>
                <input type="date" name="record_date" required class="w-full border border-emerald-200 rounded-xl px-4 py-3">
            </div>

            <button type="submit" class="w-full bg-emerald-700 hover:bg-emerald-800 text-white py-3 rounded-xl font-semibold shadow-md transition">
                Save Record
            </button>
        </form>
    </div>
</div>

<script>
function openModal() {
    const modal = document.getElementById('recordModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.style.overflow = 'hidden';
}
function closeModal() {
    const modal = document.getElementById('recordModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.style.overflow = '';
}
// Close modal on backdrop click
document.getElementById('recordModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});
</script>

</body>
</html>
