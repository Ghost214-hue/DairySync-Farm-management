<?php
// /farm-management/frontend/components/sidebar.php
require_once __DIR__ . '/../../router/urlHelper.php';

function renderSidebar() {
    $current_path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
    $dashboard_url = ltrim(UrlHelper::url('dashboard'), '/');
    $cows_url      = ltrim(UrlHelper::url('cows'), '/');
    $health_url    = ltrim(UrlHelper::url('health'), '/');
    $profile_url   = ltrim(UrlHelper::url('profile'), '/');
    $logout_url    = ltrim(UrlHelper::url('logout'), '/');
    ?>
    <aside class="w-64 flex-shrink-0 hidden md:block" style="background: rgba(5, 46, 22, 0.75); backdrop-filter: blur(20px); border-right: 1px solid rgba(34, 197, 94, 0.25);">
        <div class="p-6">
            <div class="flex items-center gap-3 mb-8">
                <div class="w-10 h-10 bg-gradient-to-br from-emerald-600 to-green-700 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-tractor text-white text-lg"></i>
                </div>
                <span class="font-bold text-white text-xl tracking-tight">DairySync</span>
            </div>
            <nav class="space-y-1.5">
                <a href="<?= UrlHelper::url('dashboard') ?>" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all duration-200 <?= str_contains($current_path, $dashboard_url) ? 'bg-emerald-700/70 text-white shadow-md' : 'text-emerald-100/80 hover:bg-white/10 hover:text-white' ?>">
                    <i class="fas fa-tachometer-alt w-5"></i>
                    <span>Dashboard</span>
                </a>
                <a href="<?= UrlHelper::url('cows') ?>" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all duration-200 <?= str_contains($current_path, $cows_url) ? 'bg-emerald-700/70 text-white shadow-md' : 'text-emerald-100/80 hover:bg-white/10 hover:text-white' ?>">
                    <i class="fas fa-paw w-5"></i>
                    <span>Cows</span>
                </a>
                <a href="<?= UrlHelper::url('health') ?>" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all duration-200 <?= str_contains($current_path, $health_url) ? 'bg-emerald-700/70 text-white shadow-md' : 'text-emerald-100/80 hover:bg-white/10 hover:text-white' ?>">
                    <i class="fas fa-notes-medical w-5"></i>
                    <span>Health Records</span>
                </a>
                <a href="#" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all duration-200 text-emerald-100/80 hover:bg-white/10 hover:text-white">
                    <i class="fas fa-chart-line w-5"></i>
                    <span>Milk Production</span>
                </a>
                <a href="#" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all duration-200 text-emerald-100/80 hover:bg-white/10 hover:text-white">
                    <i class="fas fa-dollar-sign w-5"></i>
                    <span>Income</span>
                </a>
                <a href="#" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all duration-200 text-emerald-100/80 hover:bg-white/10 hover:text-white">
                    <i class="fas fa-chart-pie w-5"></i>
                    <span>Reports</span>
                </a>
                <div class="pt-6 mt-6 border-t border-emerald-700/30">
                    <a href="<?= UrlHelper::url('profile') ?>" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all duration-200 text-emerald-100/80 hover:bg-white/10 hover:text-white">
                        <i class="fas fa-user-circle w-5"></i>
                        <span>Profile</span>
                    </a>
                    <a href="<?= UrlHelper::url('logout') ?>" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all duration-200 text-red-300 hover:bg-red-500/20 hover:text-white">
                        <i class="fas fa-sign-out-alt w-5"></i>
                        <span>Logout</span>
                    </a>
                </div>
            </nav>
        </div>
    </aside>
    <?php
}
?>