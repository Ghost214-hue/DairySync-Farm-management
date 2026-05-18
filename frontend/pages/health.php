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
    <title>Health Records</title>

    <link href="/farm-management/frontend/css/output.css" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body class="bg-gray-50">

<div class="flex min-h-screen">

    <?php renderSidebar(); ?>

    <main class="flex-1 p-7">

        <!-- HEADER -->
        <div class="flex items-center justify-between mb-8">

            <div>
                <h1 class="text-4xl font-bold text-gray-800">
                    Health Records
                </h1>

                <p class="text-gray-500 mt-1">
                    Track vaccinations, treatments, and health checks.
                </p>
            </div>

            <button
                onclick="openModal()"
                class="bg-green-700 hover:bg-green-800 text-white px-5 py-3 rounded-xl font-semibold shadow-sm"
            >
                <i class="fa-solid fa-plus mr-2"></i>
                Add Record
            </button>

        </div>

        <!-- ALERTS -->
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

        <!-- TABLE -->
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">

            <div class="overflow-x-auto">

                <table class="w-full">

                    <thead class="bg-gray-50">

                    <tr class="text-left text-xs uppercase text-gray-400 tracking-wider">

                        <th class="px-6 py-5">Date</th>
                        <th class="px-6 py-5">Cow</th>
                        <th class="px-6 py-5">Condition</th>
                        <th class="px-6 py-5">Status</th>
                        <th class="px-6 py-5">Treatment</th>
                        <th class="px-6 py-5">Notes</th>
                        <th class="px-6 py-5">Actions</th>

                    </tr>

                    </thead>

                    <tbody>

                    <?php if (empty($health_records)): ?>

                        <tr>

                            <td colspan="7" class="py-24 text-center">

                                <div class="flex flex-col items-center">

                                    <i class="fa-solid fa-heart-pulse text-6xl text-gray-200 mb-5"></i>

                                    <h3 class="text-2xl font-bold text-gray-700">
                                        No health records
                                    </h3>

                                    <p class="text-gray-400 mt-2">
                                        Add your first health record.
                                    </p>

                                </div>

                            </td>

                        </tr>

                    <?php else: ?>

                        <?php foreach ($health_records as $record): ?>

                            <?php

                            $badge = match($record['status']) {
                                'Healthy' => 'bg-green-100 text-green-700',
                                'Recovered' => 'bg-blue-100 text-blue-700',
                                'Critical' => 'bg-red-100 text-red-700',
                                default => 'bg-yellow-100 text-yellow-700'
                            };

                            ?>

                            <tr class="border-t border-gray-100 hover:bg-gray-50">

                                <td class="px-6 py-5 text-gray-600">
                                    <?= date('M j, Y', strtotime($record['record_date'])) ?>
                                </td>

                                <td class="px-6 py-5 font-semibold text-gray-700">
                                    <?= htmlspecialchars($record['cow_name']) ?>
                                </td>

                                <td class="px-6 py-5 text-gray-700">
                                    <?= htmlspecialchars($record['condition_name']) ?>
                                </td>

                                <td class="px-6 py-5">

                                    <span class="<?= $badge ?> px-3 py-1 rounded-full text-xs font-semibold">
                                        <?= htmlspecialchars($record['status']) ?>
                                    </span>

                                </td>

                                <td class="px-6 py-5 text-gray-600">
                                    <?= htmlspecialchars($record['treatment']) ?>
                                </td>

                                <td class="px-6 py-5 text-gray-500">
                                    <?= htmlspecialchars($record['notes']) ?>
                                </td>

                                <td class="px-6 py-5">

                                    <form method="POST">

                                        <input type="hidden" name="action" value="delete_record">
                                        <input type="hidden" name="record_id" value="<?= $record['id'] ?>">

                                        <button
                                            type="submit"
                                            class="text-red-500 hover:text-red-700"
                                        >
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

<!-- MODAL -->
<div
    id="recordModal"
    class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50"
>

    <div class="bg-white rounded-3xl w-full max-w-lg p-7">

        <div class="flex items-center justify-between mb-6">

            <h2 class="text-2xl font-bold text-gray-800">
                Add Health Record
            </h2>

            <button onclick="closeModal()">
                <i class="fa-solid fa-xmark text-gray-500"></i>
            </button>

        </div>

        <form method="POST" class="space-y-5">

            <input type="hidden" name="action" value="add_record">

            <div>

                <label class="block text-sm font-semibold mb-2">
                    Cow
                </label>

                <select
                    name="cow_id"
                    required
                    class="w-full border border-gray-200 rounded-xl px-4 py-3"
                >

                    <option value="">Select cow</option>

                    <?php foreach ($cows as $cow): ?>

                        <option value="<?= $cow['id'] ?>">
                            <?= htmlspecialchars($cow['cow_name']) ?>
                            (#<?= htmlspecialchars($cow['ear_tag']) ?>)
                        </option>

                    <?php endforeach; ?>

                </select>

            </div>

            <div>

                <label class="block text-sm font-semibold mb-2">
                    Condition
                </label>

                <input
                    type="text"
                    name="condition_name"
                    required
                    class="w-full border border-gray-200 rounded-xl px-4 py-3"
                >

            </div>

            <div>

                <label class="block text-sm font-semibold mb-2">
                    Status
                </label>

                <select
                    name="status"
                    class="w-full border border-gray-200 rounded-xl px-4 py-3"
                >

                    <?php foreach (HEALTH_STATUSES as $status): ?>

                        <option value="<?= $status ?>">
                            <?= $status ?>
                        </option>

                    <?php endforeach; ?>

                </select>

            </div>

            <div>

                <label class="block text-sm font-semibold mb-2">
                    Treatment
                </label>

                <textarea
                    name="treatment"
                    rows="3"
                    class="w-full border border-gray-200 rounded-xl px-4 py-3"
                ></textarea>

            </div>

            <div>

                <label class="block text-sm font-semibold mb-2">
                    Notes
                </label>

                <textarea
                    name="notes"
                    rows="3"
                    class="w-full border border-gray-200 rounded-xl px-4 py-3"
                ></textarea>

            </div>

            <div>

                <label class="block text-sm font-semibold mb-2">
                    Record Date
                </label>

                <input
                    type="date"
                    name="record_date"
                    required
                    class="w-full border border-gray-200 rounded-xl px-4 py-3"
                >

            </div>

            <button
                type="submit"
                class="w-full bg-green-700 hover:bg-green-800 text-white py-3 rounded-xl font-semibold"
            >
                Save Record
            </button>

        </form>

    </div>

</div>

<script>

function openModal() {
    document.getElementById('recordModal').classList.remove('hidden');
    document.getElementById('recordModal').classList.add('flex');
}

function closeModal() {
    document.getElementById('recordModal').classList.add('hidden');
    document.getElementById('recordModal').classList.remove('flex');
}

</script>

</body>
</html>