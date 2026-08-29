<?php
require_once __DIR__ . '/../../backend/pages/settings.php';
require_once __DIR__ . '/../components/sidebar.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farm Settings | MooManager</title>
    <link href="/frontend/css/output.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-[#f4f7f2] min-h-screen">
<div class="flex min-h-screen">
    <?php renderSidebar(); ?>
    <main class="flex-1 min-w-0 p-4 pt-20 md:p-7 md:pt-7">
        <div class="max-w-2xl mx-auto">
            <div class="bg-white rounded-2xl md:rounded-3xl shadow-sm border border-slate-100 p-5 md:p-8">
                <h1 class="text-2xl md:text-3xl font-bold text-slate-800 mb-2">Farm Settings</h1>
                <p class="text-slate-500 mb-6">Configure your farm preferences</p>

                <?php if ($message): ?>
                    <div class="mb-4 bg-green-100 text-green-700 px-4 py-3 rounded-xl"><?= htmlspecialchars($message) ?></div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div class="mb-4 bg-red-100 text-red-700 px-4 py-3 rounded-xl"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form method="POST" class="space-y-6">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Milk Price per Litre (KSh)</label>
                        <div class="flex flex-col sm:flex-row gap-3">
                            <input type="number" step="0.01" name="milk_price" value="<?= number_format($current_price, 2) ?>"
                                   class="flex-1 border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-emerald-500" required>
                            <button type="submit" class="bg-emerald-700 hover:bg-emerald-800 text-white px-6 py-3 rounded-xl font-semibold">Save</button>
                        </div>
                        <p class="text-xs text-slate-400 mt-2">This price will be used for all future milk sales income calculations.</p>
                    </div>
                </form>

                <div class="mt-8 pt-6 border-t border-slate-100">
                    <h3 class="font-semibold text-slate-700 mb-2">Note:</h3>
                    <p class="text-sm text-slate-500">Changing the milk price will affect <strong>new milk production records</strong> and the default rate when adding "Milk Sales" manually. Past records remain unchanged with their original price.</p>
                </div>
            </div>
        </div>
    </main>
</div>
</body>
</html>
