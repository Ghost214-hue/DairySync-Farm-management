<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../backend/pages/cows.php';
require_once __DIR__ . '/../components/sidebar.php';
require_once __DIR__ . '/../../router/urlHelper.php';

// Flash messages
$success_msg = $_SESSION['cow_success'] ?? null; unset($_SESSION['cow_success']);
$error_msg   = $_SESSION['cow_error']   ?? null; unset($_SESSION['cow_error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cows | MooManager</title>
    <link href="/frontend/css/output.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-farm-green-50 via-farm-olive-50 to-farm-green-100 min-h-screen">
<div class="flex">

    <?php renderSidebar(); ?>

    <!-- ── Main Content ───────────────────────────────────────────────────── -->
    <main class="flex-1 min-w-0 p-4 pt-20 md:p-6 md:pt-6">

        <!-- Top bar -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-bold text-farm-green-900">Cows</h1>
                <p class="text-farm-green-600 text-sm mt-1">Manage your herd and track individual cows.</p>
            </div>
            <button onclick="openModal('addCowModal')"
                    class="inline-flex items-center gap-2 bg-farm-green-700 hover:bg-farm-green-800 text-white font-semibold px-5 py-2.5 rounded-xl shadow-md transition-all duration-200 whitespace-nowrap">
                <i class="fas fa-plus text-sm"></i> Add Cow
            </button>
        </div>

        <!-- Flash messages -->
        <?php if ($success_msg): ?>
            <div class="mb-6 flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl">
                <i class="fas fa-check-circle text-green-500"></i>
                <span><?= htmlspecialchars($success_msg) ?></span>
                <button onclick="this.parentElement.remove()" class="ml-auto text-green-400 hover:text-green-600"><i class="fas fa-times"></i></button>
            </div>
        <?php endif; ?>
        <?php if ($error_msg): ?>
            <div class="mb-6 flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl">
                <i class="fas fa-exclamation-circle text-red-500"></i>
                <span><?= htmlspecialchars($error_msg) ?></span>
                <button onclick="this.parentElement.remove()" class="ml-auto text-red-400 hover:text-red-600"><i class="fas fa-times"></i></button>
            </div>
        <?php endif; ?>

        <!-- Status summary pills -->
        <div class="flex flex-wrap gap-3 mb-6">
            <?php
            $pill_colors = [
                'Active'   => 'bg-green-100 text-green-800',
                'Dry'      => 'bg-yellow-100 text-yellow-800',
                'Pregnant' => 'bg-pink-100 text-pink-800',
                'Sick'     => 'bg-red-100 text-red-800',
            ];
            foreach ($status_counts as $label => $count):
                $color = $pill_colors[$label] ?? 'bg-gray-100 text-gray-700';
            ?>
                <span class="<?= $color ?> text-xs font-semibold px-3 py-1.5 rounded-full">
                    <?= $label ?>: <?= $count ?>
                </span>
            <?php endforeach; ?>
            <span class="bg-farm-green-100 text-farm-green-800 text-xs font-semibold px-3 py-1.5 rounded-full">
                Total: <?= $total_cows ?>
            </span>
        </div>

        <!-- Search + Table card -->
        <div class="bg-white/70 backdrop-blur-sm rounded-2xl shadow-sm border border-white/60 overflow-hidden">

            <!-- Search bar -->
            <div class="flex items-center gap-3 px-5 py-4 border-b border-farm-green-100">
                <div class="relative flex-1 max-w-sm">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-farm-green-400 text-sm"></i>
                    <input type="text" id="cowSearch" placeholder="Search cows..."
                           class="w-full pl-9 pr-4 py-2 text-sm bg-white/80 border border-farm-green-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-farm-green-400 text-farm-green-900 placeholder-farm-green-400">
                </div>
                <!-- Status filter -->
                <select id="statusFilter"
                        class="text-sm bg-white/80 border border-farm-green-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-farm-green-400 text-farm-green-800">
                    <option value="">All Statuses</option>
                    <?php foreach (ALLOWED_STATUSES as $s): ?>
                        <option value="<?= $s ?>"><?= $s ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-sm" id="cowsTable">
                    <thead>
                        <tr class="bg-farm-green-50/80 text-farm-green-700 text-xs uppercase tracking-wider">
                            <th class="px-5 py-3 text-left font-semibold">Tag #</th>
                            <th class="px-5 py-3 text-left font-semibold">Name</th>
                            <th class="px-5 py-3 text-left font-semibold">Breed</th>
                            <th class="px-5 py-3 text-left font-semibold">Gender</th>
                            <th class="px-5 py-3 text-left font-semibold">Birth Date</th>
                            <th class="px-5 py-3 text-left font-semibold">Weight</th>
                            <th class="px-5 py-3 text-left font-semibold">Status</th>
                            <th class="px-5 py-3 text-left font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="cowsTableBody" class="divide-y divide-farm-green-50">

                        <?php if (empty($cows)): ?>
                        <tr>
                            <td colspan="8" class="py-20 text-center">
                                <div class="flex flex-col items-center gap-3 text-farm-green-400">
                                    <!-- Cow silhouette SVG -->
                                    <svg viewBox="0 0 80 64" class="w-20 h-16 opacity-30" fill="currentColor">
                                        <ellipse cx="40" cy="40" rx="24" ry="18"/>
                                        <ellipse cx="22" cy="26" rx="8"  ry="10"/>
                                        <ellipse cx="58" cy="26" rx="8"  ry="10"/>
                                        <rect x="28" y="56" width="7" height="8" rx="3.5"/>
                                        <rect x="45" y="56" width="7" height="8" rx="3.5"/>
                                        <circle cx="16" cy="24" r="3.5" fill="white" fill-opacity="0.6"/>
                                        <circle cx="64" cy="24" r="3.5" fill="white" fill-opacity="0.6"/>
                                    </svg>
                                    <p class="text-base font-semibold text-farm-green-600">No cows registered yet</p>
                                    <p class="text-sm">Add your first cow to get started.</p>
                                </div>
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($cows as $cow):
                                $status_badge = [
                                    'Active'   => 'bg-green-100 text-green-700',
                                    'Dry'      => 'bg-yellow-100 text-yellow-700',
                                    'Pregnant' => 'bg-pink-100 text-pink-700',
                                    'Sick'     => 'bg-red-100 text-red-700',
                                    'Sold'     => 'bg-gray-100 text-gray-600',
                                    'Deceased' => 'bg-gray-200 text-gray-500',
                                ][$cow['status']] ?? 'bg-gray-100 text-gray-600';

                                $dob_display = $cow['birth_date']
                                    ? date('M j, Y', strtotime($cow['birth_date']))
                                    : '—';
                                $weight_display = $cow['weight_kg']
                                    ? number_format($cow['weight_kg'], 1) . ' kg'
                                    : '—';
                            ?>
                            <tr class="hover:bg-farm-green-50/50 transition-colors cow-row"
                                data-name="<?= strtolower(htmlspecialchars($cow['name'])) ?>"
                                data-tag="<?= strtolower(htmlspecialchars($cow['tag_number'])) ?>"
                                data-breed="<?= strtolower(htmlspecialchars($cow['breed'])) ?>"
                                data-status="<?= htmlspecialchars($cow['status']) ?>">

                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <?php if (!empty($cow['image_path'])): ?>
                                        <div class="w-10 h-10 rounded-full overflow-hidden border-2 border-farm-green-200 flex-shrink-0">
                                            <img src="<?= htmlspecialchars($cow['image_path']) ?>" alt="<?= htmlspecialchars($cow['name'] ?? 'Cow') ?>" class="w-full h-full object-cover">
                                        </div>
                                        <?php else: ?>
                                        <div class="w-10 h-10 bg-farm-green-100 rounded-full flex items-center justify-center text-farm-green-600 text-sm flex-shrink-0">
                                            <i class="fas fa-cow"></i>
                                        </div>
                                        <?php endif; ?>
                                        <span class="font-mono font-semibold text-farm-green-800">#<?= htmlspecialchars($cow['tag_number']) ?></span>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 font-medium text-farm-green-900">
                                    <?= $cow['name'] ? htmlspecialchars($cow['name']) : '<span class="text-farm-green-400 italic">Unnamed</span>' ?>
                                </td>
                                <td class="px-5 py-3.5 text-farm-green-700"><?= htmlspecialchars($cow['breed']) ?></td>
                                <td class="px-5 py-3.5 text-farm-green-700">
                                    <span class="inline-flex items-center gap-1">
                                        <i class="fas <?= $cow['gender'] === 'Female' ? 'fa-venus text-pink-400' : 'fa-mars text-blue-400' ?> text-xs"></i>
                                        <?= htmlspecialchars($cow['gender']) ?>
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-farm-green-700"><?= $dob_display ?></td>
                                <td class="px-5 py-3.5 text-farm-green-700"><?= $weight_display ?></td>
                                <td class="px-5 py-3.5">
                                    <span class="<?= $status_badge ?> text-xs font-semibold px-2.5 py-1 rounded-full">
                                        <?= htmlspecialchars($cow['status']) ?>
                                    </span>
                                </td>
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-2">
                                        <!-- Edit -->
                                        <button onclick='openEditModal(<?= json_encode($cow) ?>)'
                                                class="p-1.5 rounded-lg text-farm-green-600 hover:bg-farm-green-100 transition"
                                                title="Edit">
                                            <i class="fas fa-pen text-xs"></i>
                                        </button>
                                        <!-- Delete -->
                                        <button onclick="confirmDelete(<?= $cow['id'] ?>, '<?= htmlspecialchars($cow['tag_number'], ENT_QUOTES) ?>')"
                                                class="p-1.5 rounded-lg text-red-400 hover:bg-red-50 transition"
                                                title="Delete">
                                            <i class="fas fa-trash text-xs"></i>
                                        </button>
                                        <!-- View Profile -->
                                                     <a href="<?= UrlHelper::url('cow_profile', ['id' => $cow['id']]) ?>"
                                                class="p-1.5 rounded-lg text-blue-600 hover:bg-blue-50 transition"
                                                title="View Profile">
                                            <i class="fas fa-user text-xs"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>

                    </tbody>
                </table>
            </div>

            <!-- Table footer -->
            <?php if (!empty($cows)): ?>
            <div class="px-5 py-3 border-t border-farm-green-100 text-xs text-farm-green-500">
                Showing <span id="visibleCount"><?= $total_cows ?></span> of <?= $total_cows ?> cows
            </div>
            <?php endif; ?>
        </div>
    </main>
</div>

<div id="addCowModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <!-- Backdrop -->
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeModal('addCowModal')"></div>

    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h2 class="text-lg font-bold text-farm-green-900">Add New Cow</h2>
            <button onclick="closeModal('addCowModal')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form method="POST" action="<?= UrlHelper::url('cows') ?>" class="p-6 space-y-4" enctype="multipart/form-data">
            <input type="hidden" name="action" value="add_cow">

            <div class="grid grid-cols-2 gap-4">
                <?php renderField('tag_number', 'Tag Number *', 'text', 'e.g. T001') ?>
                <?php renderField('name', 'Name', 'text', 'e.g. Bessie') ?>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <?php renderSelect('breed', 'Breed *', ALLOWED_BREEDS) ?>
                <?php renderSelect('gender', 'Gender *', ALLOWED_GENDERS) ?>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <?php renderField('birth_date', 'Birth Date', 'date') ?>
                <?php renderField('weight_kg', 'Weight (kg)', 'number', '0.0') ?>
            </div>
            <?php renderSelect('status', 'Status *', ALLOWED_STATUSES, 'Active') ?>
            <div>
                <label class="block text-xs font-semibold text-farm-green-700 mb-1.5 uppercase tracking-wide">Notes</label>
                <textarea name="notes" rows="2" placeholder="Optional notes..."
                          class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-farm-green-400 resize-none"></textarea>
            </div>

            
            <div>
                <label class="block text-xs font-semibold text-farm-green-700 mb-1.5 uppercase tracking-wide">Cow Image</label>
                <input type="file" name="cow_image" accept="image/*"
                       class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-farm-green-400">
            </div>

            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeModal('addCowModal')"
                        class="flex-1 px-4 py-2.5 rounded-xl border border-gray-200 text-gray-600 text-sm font-medium hover:bg-gray-50 transition">
                    Cancel
                </button>
                <button type="submit"
                        class="flex-1 px-4 py-2.5 rounded-xl bg-farm-green-700 hover:bg-farm-green-800 text-white text-sm font-semibold transition">
                    Add Cow
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ══════════════════════════════════════════════════════════════════════════
     EDIT COW MODAL
════════════════════════════════════════════════════════════════════════════ -->
<div id="editCowModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeModal('editCowModal')"></div>

    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h2 class="text-lg font-bold text-farm-green-900">Edit Cow</h2>
            <button onclick="closeModal('editCowModal')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form method="POST" action="<?= UrlHelper::url('cows') ?>" class="p-6 space-y-4" enctype="multipart/form-data">
            <input type="hidden" name="action"  value="update_cow">
            <input type="hidden" name="cow_id"  id="edit_cow_id">

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-farm-green-700 mb-1.5 uppercase tracking-wide">Tag Number *</label>
                    <input type="text" name="tag_number" id="edit_tag_number" required
                           class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-farm-green-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-farm-green-700 mb-1.5 uppercase tracking-wide">Name</label>
                    <input type="text" name="name" id="edit_name"
                           class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-farm-green-400">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-farm-green-700 mb-1.5 uppercase tracking-wide">Breed *</label>
                    <select name="breed" id="edit_breed"
                            class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-farm-green-400">
                        <?php foreach (ALLOWED_BREEDS as $b): ?>
                            <option value="<?= $b ?>"><?= $b ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-farm-green-700 mb-1.5 uppercase tracking-wide">Gender *</label>
                    <select name="gender" id="edit_gender"
                            class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-farm-green-400">
                        <?php foreach (ALLOWED_GENDERS as $g): ?>
                            <option value="<?= $g ?>"><?= $g ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-farm-green-700 mb-1.5 uppercase tracking-wide">Birth Date</label>
                    <input type="date" name="birth_date" id="edit_birth_date"
                           class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-farm-green-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-farm-green-700 mb-1.5 uppercase tracking-wide">Weight (kg)</label>
                    <input type="number" name="weight_kg" id="edit_weight_kg" step="0.1" min="0"
                           class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-farm-green-400">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-farm-green-700 mb-1.5 uppercase tracking-wide">Status *</label>
                <select name="status" id="edit_status"
                        class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-farm-green-400">
                    <?php foreach (ALLOWED_STATUSES as $s): ?>
                        <option value="<?= $s ?>"><?= $s ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-farm-green-700 mb-1.5 uppercase tracking-wide">Notes</label>
                <textarea name="notes" id="edit_notes" rows="2"
                          class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-farm-green-400 resize-none"></textarea>
            </div>

            
            <div>
                <label class="block text-xs font-semibold text-farm-green-700 mb-1.5 uppercase tracking-wide">Cow Image</label>
                <input type="file" name="cow_image" accept="image/*"
                       class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-farm-green-400">
            </div>

            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeModal('editCowModal')"
                        class="flex-1 px-4 py-2.5 rounded-xl border border-gray-200 text-gray-600 text-sm font-medium hover:bg-gray-50 transition">
                    Cancel
                </button>
                <button type="submit"
                        class="flex-1 px-4 py-2.5 rounded-xl bg-farm-green-700 hover:bg-farm-green-800 text-white text-sm font-semibold transition">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ══════════════════════════════════════════════════════════════════════════
     DELETE CONFIRM MODAL
════════════════════════════════════════════════════════════════════════════ -->
<div id="deleteModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeModal('deleteModal')"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 text-center">
        <div class="w-14 h-14 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-trash text-red-500 text-xl"></i>
        </div>
        <h3 class="text-lg font-bold text-gray-900 mb-1">Remove Cow?</h3>
        <p class="text-sm text-gray-500 mb-6">You're about to remove <strong id="deleteTagLabel"></strong>. This action cannot be undone.</p>
        <form method="POST" action="<?= UrlHelper::url('cows') ?>">
            <input type="hidden" name="action" value="delete_cow">
            <input type="hidden" name="cow_id" id="deleteCowId">
            <div class="flex gap-3">
                <button type="button" onclick="closeModal('deleteModal')"
                        class="flex-1 px-4 py-2.5 rounded-xl border border-gray-200 text-gray-600 text-sm font-medium hover:bg-gray-50 transition">
                    Cancel
                </button>
                <button type="submit"
                        class="flex-1 px-4 py-2.5 rounded-xl bg-red-500 hover:bg-red-600 text-white text-sm font-semibold transition">
                    Remove
                </button>
            </div>
        </form>
    </div>
</div>

<?php
// ── Reusable form field helpers (local, not global) ──────────────────────────
function renderField(string $name, string $label, string $type = 'text', string $placeholder = '', string $default = ''): void {
    echo <<<HTML
    <div>
        <label class="block text-xs font-semibold text-farm-green-700 mb-1.5 uppercase tracking-wide">{$label}</label>
        <input type="{$type}" name="{$name}" value="{$default}" placeholder="{$placeholder}" step="0.1" min="0"
               class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-farm-green-400">
    </div>
    HTML;
}
function renderSelect(string $name, string $label, array $options, string $selected = ''): void {
    $opts = '';
    foreach ($options as $o) {
        $sel  = ($o === $selected) ? ' selected' : '';
        $opts .= "<option value=\"{$o}\"{$sel}>{$o}</option>";
    }
    echo <<<HTML
    <div>
        <label class="block text-xs font-semibold text-farm-green-700 mb-1.5 uppercase tracking-wide">{$label}</label>
        <select name="{$name}" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-farm-green-400">
            {$opts}
        </select>
    </div>
    HTML;
}
?>

<script>
// ── Modal helpers ────────────────────────────────────────────────────────────
function openModal(id) {
    const m = document.getElementById(id);
    m.classList.remove('hidden');
    m.classList.add('flex');
    document.body.style.overflow = 'hidden';
}
function closeModal(id) {
    const m = document.getElementById(id);
    m.classList.add('hidden');
    m.classList.remove('flex');
    document.body.style.overflow = '';
}

// ── Populate edit modal ──────────────────────────────────────────────────────
function openEditModal(cow) {
    document.getElementById('edit_cow_id').value    = cow.id;
    document.getElementById('edit_tag_number').value = cow.tag_number;
    document.getElementById('edit_name').value      = cow.name        || '';
    document.getElementById('edit_breed').value     = cow.breed;
    document.getElementById('edit_gender').value    = cow.gender;
    document.getElementById('edit_birth_date').value = cow.birth_date || '';
    document.getElementById('edit_weight_kg').value = cow.weight_kg   || '';
    document.getElementById('edit_status').value    = cow.status;
    document.getElementById('edit_notes').value     = cow.notes       || '';
    openModal('editCowModal');
}

// ── Confirm delete ───────────────────────────────────────────────────────────
function confirmDelete(id, tag) {
    document.getElementById('deleteCowId').value      = id;
    document.getElementById('deleteTagLabel').textContent = '#' + tag;
    openModal('deleteModal');
}

// ── Live search + status filter ──────────────────────────────────────────────
function filterTable() {
    const q      = document.getElementById('cowSearch').value.toLowerCase();
    const status = document.getElementById('statusFilter').value;
    const rows   = document.querySelectorAll('.cow-row');
    let visible  = 0;

    rows.forEach(row => {
        const matchQ = !q ||
            row.dataset.name.includes(q)  ||
            row.dataset.tag.includes(q)   ||
            row.dataset.breed.includes(q);
        const matchS = !status || row.dataset.status === status;

        if (matchQ && matchS) {
            row.style.display = '';
            visible++;
        } else {
            row.style.display = 'none';
        }
    });

    const countEl = document.getElementById('visibleCount');
    if (countEl) countEl.textContent = visible;
}

document.getElementById('cowSearch')?.addEventListener('input', filterTable);
document.getElementById('statusFilter')?.addEventListener('change', filterTable);

// ── Close modals on Escape ───────────────────────────────────────────────────
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        ['addCowModal','editCowModal','deleteModal'].forEach(closeModal);
    }
});
</script>
</body>
</html>
