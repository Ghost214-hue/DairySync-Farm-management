<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../backend/pages/customers.php';
require_once __DIR__ . '/../components/sidebar.php';
require_once __DIR__ . '/../../router/urlHelper.php';

// Retrieve and clear session messages
$success = $_SESSION['customer_success'] ?? null;
$error   = $_SESSION['customer_error'] ?? null;
unset($_SESSION['customer_success']);
unset($_SESSION['customer_error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Customers | MooManager</title>
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
                <h1 class="text-4xl font-bold text-slate-800">Manage Customers</h1>
                <p class="text-slate-500 mt-2">Add and manage customers with their milk prices.</p>
            </div>
            <div class="flex gap-3 items-center">
                <a href="<?= UrlHelper::url('milk_sales') ?>" 
                   class="bg-slate-700 hover:bg-slate-800 text-white px-5 py-3 rounded-2xl font-semibold shadow-sm transition inline-flex items-center gap-2">
                    <i class="fas fa-arrow-left"></i> Back to Sales
                </a>
                <button onclick="openAddCustomerModal()" 
                        class="bg-emerald-700 hover:bg-emerald-800 text-white px-5 py-3 rounded-2xl font-semibold shadow-sm transition inline-flex items-center gap-2">
                    <i class="fas fa-plus"></i> Add Customer
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

        <!-- Stats Card -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6 mb-7">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500 mb-1">Total Customers</p>
                    <h2 class="text-4xl font-bold text-slate-800"><?= count($customers) ?></h2>
                    <p class="text-xs text-emerald-700 mt-2 font-semibold">
                        <i class="fas fa-check-circle"></i> <?= $active_count ?> Active
                    </p>
                </div>
                <div class="w-16 h-16 rounded-2xl bg-emerald-100 flex items-center justify-center">
                    <i class="fas fa-users text-emerald-700 text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Customers Table -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100">
                <h3 class="text-2xl font-bold text-slate-800">Customers List</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50">
                        <tr class="text-left text-xs uppercase tracking-wider text-slate-400">
                            <th class="px-6 py-4">Customer Name</th>
                            <th class="px-6 py-4">Price per Litre</th>
                            <th class="px-6 py-4">Contact Info</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4">Added</th>
                            <th class="px-6 py-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($customers)): ?>
                            <tr>
                                <td colspan="6" class="py-24 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="text-slate-300 text-6xl mb-4"><i class="fas fa-users"></i></div>
                                        <h3 class="text-2xl font-bold text-slate-700 mb-2">No customers yet</h3>
                                        <p class="text-slate-400 mb-4">Start by adding your first customer</p>
                                        <button onclick="openAddCustomerModal()" class="bg-emerald-700 hover:bg-emerald-800 text-white px-6 py-2 rounded-xl font-semibold transition">
                                            Add Customer
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($customers as $customer): ?>
                                <tr class="border-t hover:bg-slate-50 transition">
                                    <td class="px-6 py-4 font-semibold text-slate-800">
                                        <div class="flex items-center gap-2">
                                            <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center">
                                                <i class="fas fa-user text-emerald-700 text-sm"></i>
                                            </div>
                                            <?= htmlspecialchars($customer['customer_name']) ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-lg font-bold text-emerald-700">KSh <?= number_format($customer['price_per_litre'], 2) ?></span>
                                    </td>
                                    <td class="px-6 py-4 text-slate-600">
                                        <?= $customer['contact_info'] ? htmlspecialchars($customer['contact_info']) : '<span class="text-slate-400">-</span>' ?>
                                    </td>
                                    <td class="px-6 py-4">
                                        <?php if ($customer['status'] === 'Active'): ?>
                                            <span class="inline-flex items-center gap-1.5 bg-green-50 text-green-700 text-xs font-bold px-3 py-1 rounded-full">
                                                <i class="fas fa-circle text-xs"></i>
                                                Active
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center gap-1.5 bg-gray-100 text-gray-700 text-xs font-bold px-3 py-1 rounded-full">
                                                <i class="fas fa-circle text-xs"></i>
                                                Inactive
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-500">
                                        <?= date('M j, Y', strtotime($customer['created_at'])) ?>
                                    </td>
                                    <td class="px-6 py-4 space-x-2 flex">
                                        <button onclick="openEditCustomerModal(<?= $customer['id'] ?>, '<?= htmlspecialchars($customer['customer_name']) ?>', <?= $customer['price_per_litre'] ?>, '<?= htmlspecialchars($customer['contact_info'] ?? '') ?>')" 
                                                class="text-blue-500 hover:text-blue-700 transition" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form method="POST" style="display:inline;" onsubmit="return confirm('Toggle status?')">
                                            <input type="hidden" name="action" value="toggle_customer_status">
                                            <input type="hidden" name="customer_id" value="<?= $customer['id'] ?>">
                                            <input type="hidden" name="new_status" value="<?= $customer['status'] === 'Active' ? 'Inactive' : 'Active' ?>">
                                            <button type="submit" class="text-yellow-500 hover:text-yellow-700 transition" title="Toggle Status">
                                                <i class="fas fa-toggle-on"></i>
                                            </button>
                                        </form>
                                        <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this customer?')">
                                            <input type="hidden" name="action" value="delete_customer">
                                            <input type="hidden" name="customer_id" value="<?= $customer['id'] ?>">
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
    </main>
</div>

<!-- ============================================================
     MODAL: Add Customer
     ============================================================ -->
<div id="addCustomerModal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50">
    <div class="bg-white w-full max-w-lg rounded-3xl shadow-2xl p-7 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-slate-800">Add New Customer</h2>
            <button onclick="closeAddCustomerModal()" class="text-slate-400 hover:text-slate-600 transition">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form method="POST" class="space-y-5">
            <input type="hidden" name="action" value="add_customer">

            <!-- Customer Name -->
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Customer Name *</label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                        <i class="fas fa-user fa-sm"></i>
                    </span>
                    <input type="text" name="customer_name"
                           placeholder="e.g. John Kamau, Creamery A" maxlength="100" required
                           class="w-full border border-slate-200 rounded-xl pl-10 pr-4 py-3 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                </div>
            </div>

            <!-- Price per Litre -->
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Price per Litre (KSh) *</label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">KSh</span>
                    <input type="number" step="0.01" min="0" name="price_per_litre" 
                           placeholder="e.g. 70.00" required
                           class="w-full border border-slate-200 rounded-xl pl-14 pr-4 py-3 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                </div>
            </div>

            <!-- Contact Info (Optional) -->
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Contact Info (Optional)</label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                        <i class="fas fa-phone fa-sm"></i>
                    </span>
                    <input type="text" name="contact_info"
                           placeholder="e.g. 0712345678 or email@example.com" maxlength="255"
                           class="w-full border border-slate-200 rounded-xl pl-10 pr-4 py-3 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                </div>
            </div>

            <button type="submit"
                    class="w-full bg-emerald-700 hover:bg-emerald-800 text-white font-semibold py-3 rounded-2xl transition">
                Add Customer
            </button>
        </form>
    </div>
</div>

<!-- ============================================================
     MODAL: Edit Customer
     ============================================================ -->
<div id="editCustomerModal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50">
    <div class="bg-white w-full max-w-lg rounded-3xl shadow-2xl p-7 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-slate-800">Edit Customer</h2>
            <button onclick="closeEditCustomerModal()" class="text-slate-400 hover:text-slate-600 transition">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form method="POST" class="space-y-5">
            <input type="hidden" name="action" value="update_customer">
            <input type="hidden" name="customer_id" id="editCustomerId" value="">

            <!-- Customer Name (Display Only) -->
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Customer Name</label>
                <div class="bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-700 font-semibold">
                    <span id="editCustomerName"></span>
                </div>
            </div>

            <!-- Price per Litre -->
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Price per Litre (KSh) *</label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">KSh</span>
                    <input type="number" step="0.01" min="0" name="price_per_litre" 
                           id="editPricePerLitre" placeholder="e.g. 70.00" required
                           class="w-full border border-slate-200 rounded-xl pl-14 pr-4 py-3 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                </div>
            </div>

            <!-- Contact Info -->
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Contact Info (Optional)</label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                        <i class="fas fa-phone fa-sm"></i>
                    </span>
                    <input type="text" name="contact_info"
                           id="editContactInfo"
                           placeholder="e.g. 0712345678 or email@example.com" maxlength="255"
                           class="w-full border border-slate-200 rounded-xl pl-10 pr-4 py-3 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                </div>
            </div>

            <button type="submit"
                    class="w-full bg-emerald-700 hover:bg-emerald-800 text-white font-semibold py-3 rounded-2xl transition">
                Update Customer
            </button>
        </form>
    </div>
</div>

<!-- ============================================================
     Scripts (only UI helpers – no AJAX)
     ============================================================ -->
<script>
function openAddCustomerModal() {
    document.getElementById('addCustomerModal').classList.remove('hidden');
    document.getElementById('addCustomerModal').classList.add('flex');
}

function closeAddCustomerModal() {
    document.getElementById('addCustomerModal').classList.add('hidden');
    document.getElementById('addCustomerModal').classList.remove('flex');
}

function openEditCustomerModal(customerId, customerName, pricePerLitre, contactInfo) {
    document.getElementById('editCustomerId').value = customerId;
    document.getElementById('editCustomerName').textContent = customerName;
    document.getElementById('editPricePerLitre').value = pricePerLitre;
    document.getElementById('editContactInfo').value = contactInfo;
    
    document.getElementById('editCustomerModal').classList.remove('hidden');
    document.getElementById('editCustomerModal').classList.add('flex');
}

function closeEditCustomerModal() {
    document.getElementById('editCustomerModal').classList.add('hidden');
    document.getElementById('editCustomerModal').classList.remove('flex');
}

// Close modals on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeAddCustomerModal();
        closeEditCustomerModal();
    }
});
</script>
</body>
</html>