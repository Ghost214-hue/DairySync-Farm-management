<?php

require_once dirname(__DIR__, 2) . '/router/urlHelper.php';

function renderSidebar() {

    $nav_class = function(string $route): string {
        return UrlHelper::isActive($route)
            ? 'flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all duration-200 bg-emerald-600/60 text-white shadow-md font-medium'
            : 'flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all duration-200 text-emerald-100/75 hover:bg-white/10 hover:text-white';
    };

?>

<!-- =============================================
     MOBILE HEADER (only visible on small screens)
     ============================================= -->
<div class="mobile-header md:hidden fixed top-0 left-0 right-0 z-50 flex items-center justify-between px-4 py-3"
     style="background: rgba(5,46,22,0.95); backdrop-filter: blur(22px); border-bottom: 1px solid rgba(34,197,94,0.20);">

    <!-- Brand -->
    <div class="flex items-center gap-2">
        <div class="w-8 h-8 rounded-lg flex items-center justify-center shadow-lg flex-shrink-0"
             style="background: linear-gradient(135deg, #15803d, #22c55e);">
            <svg viewBox="0 0 32 32" class="w-5 h-5" fill="none">
                <ellipse cx="16" cy="19" rx="9" ry="7" fill="white" fill-opacity="0.92"/>
                <ellipse cx="10" cy="14" rx="3.5" ry="4" fill="white" fill-opacity="0.92"/>
                <ellipse cx="22" cy="14" rx="3.5" ry="4" fill="white" fill-opacity="0.92"/>
                <circle cx="13" cy="20" r="1.4" fill="#15803d"/>
                <circle cx="19" cy="20" r="1.4" fill="#15803d"/>
                <ellipse cx="16" cy="23.5" rx="2.8" ry="1.8" fill="#bbf7d0"/>
                <rect x="9" y="25" width="2.8" height="4.5" rx="1.4" fill="white" fill-opacity="0.85"/>
                <rect x="20.2" y="25" width="2.8" height="4.5" rx="1.4" fill="white" fill-opacity="0.85"/>
                <circle cx="7" cy="13.5" r="1.5" fill="#4ade80"/>
                <circle cx="25" cy="13.5" r="1.5" fill="#4ade80"/>
            </svg>
        </div>
        <span class="font-bold text-white text-lg tracking-tight" style="font-family: 'Fraunces', serif;">
            DairySync
        </span>
    </div>

    <!-- Hamburger Button -->
    <button id="hamburgerBtn" onclick="toggleMobileMenu()"
            class="text-white hover:text-emerald-200 transition p-1.5 rounded-lg hover:bg-white/20"
            aria-label="Toggle navigation">
        <svg id="hamburgerIcon" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
        <svg id="closeIcon" class="w-6 h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
    </button>
</div>

<!-- Mobile overlay backdrop -->
<div id="mobileBackdrop"
     onclick="closeMobileMenu()"
     class="md:hidden fixed inset-0 bg-black/50 z-40 hidden transition-opacity duration-300">
</div>

<!-- Mobile Sidebar (drawer) -->
<div id="mobileSidebar"
     class="mobile-sidebar md:hidden fixed top-0 left-0 h-full w-72 z-50 flex flex-col transform -translate-x-full transition-transform duration-300 ease-in-out"
     style="background: rgba(5,46,22,0.97); backdrop-filter: blur(22px); border-right: 1px solid rgba(34,197,94,0.20);">

    <div class="p-6 flex flex-col flex-1 overflow-y-auto">

        <!-- Brand -->
        <div class="flex items-center gap-3 mb-8 mt-1">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center shadow-lg flex-shrink-0"
                 style="background: linear-gradient(135deg, #15803d, #22c55e);">
                <svg viewBox="0 0 32 32" class="w-6 h-6" fill="none">
                    <ellipse cx="16" cy="19" rx="9" ry="7" fill="white" fill-opacity="0.92"/>
                    <ellipse cx="10" cy="14" rx="3.5" ry="4" fill="white" fill-opacity="0.92"/>
                    <ellipse cx="22" cy="14" rx="3.5" ry="4" fill="white" fill-opacity="0.92"/>
                    <circle cx="13" cy="20" r="1.4" fill="#15803d"/>
                    <circle cx="19" cy="20" r="1.4" fill="#15803d"/>
                    <ellipse cx="16" cy="23.5" rx="2.8" ry="1.8" fill="#bbf7d0"/>
                    <rect x="9" y="25" width="2.8" height="4.5" rx="1.4" fill="white" fill-opacity="0.85"/>
                    <rect x="20.2" y="25" width="2.8" height="4.5" rx="1.4" fill="white" fill-opacity="0.85"/>
                    <circle cx="7" cy="13.5" r="1.5" fill="#4ade80"/>
                    <circle cx="25" cy="13.5" r="1.5" fill="#4ade80"/>
                </svg>
            </div>
        </div>

        <!-- Mobile Navigation -->
        <nav class="space-y-1 flex-1">
            <p class="text-xs font-semibold text-emerald-500/70 uppercase tracking-widest px-4 mb-2">Main</p>

            <a href="<?= UrlHelper::url('dashboard') ?>" class="<?= $nav_class('dashboard') ?>">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0h6"/>
                </svg>
                <span>Dashboard</span>
            </a>

            <a href="<?= UrlHelper::url('cows') ?>" class="<?= $nav_class('cows') ?>">
                <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none">
                    <ellipse cx="12" cy="14" rx="7" ry="5.5" fill="currentColor" fill-opacity="0.2" stroke="currentColor" stroke-width="1.5"/>
                    <ellipse cx="7.5" cy="10.5" rx="2.5" ry="3" stroke="currentColor" stroke-width="1.5"/>
                    <ellipse cx="16.5" cy="10.5" rx="2.5" ry="3" stroke="currentColor" stroke-width="1.5"/>
                    <line x1="9.5" y1="19" x2="9.5" y2="22" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    <line x1="14.5" y1="19" x2="14.5" y2="22" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    <circle cx="5.5" cy="10" r="1" fill="currentColor"/>
                    <circle cx="18.5" cy="10" r="1" fill="currentColor"/>
                </svg>
                <span>Cows</span>
            </a>

            <a href="<?= UrlHelper::url('health') ?>" class="<?= $nav_class('health') ?>">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                </svg>
                <span>Health Records</span>
            </a>

            <a href="<?= UrlHelper::url('milk_production') ?>" class="<?= $nav_class('milk_production') ?>">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                <span>Milk Production</span>
            </a>

            <a href="<?= UrlHelper::url('milk_sales') ?>" class="<?= $nav_class('milk_sales') ?>">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4l1-12z"/>
                </svg>
                <span>Milk Sales</span>
            </a>

            <a href="<?= UrlHelper::url('feeds') ?>" class="<?= $nav_class('feeds') ?>">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 13V7a2 2 0 00-2-2h-3V3H9v2H6a2 2 0 00-2 2v6m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0H4m5 4h6"/>
                </svg>
                <span>Feed Management</span>
            </a>

            <a href="<?= UrlHelper::url('income') ?>" class="<?= $nav_class('income') ?>">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>Income</span>
            </a>

            <a href="<?= UrlHelper::url('milk_report') ?>" class="<?= $nav_class('milk_report') ?>">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/>
                </svg>
                <span>Reports</span>
            </a>

            <a href="<?= UrlHelper::url('settings') ?>" class="<?= $nav_class('settings') ?>">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span>Settings</span>
            </a>

            <a href="<?= UrlHelper::url('logout') ?>" class="<?= $nav_class('logout') ?>">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                <span>Logout</span>
            </a>
        </nav>

        <!-- Footer -->
        <div class="pt-6 border-t border-emerald-500/10 mt-6">
            <div class="text-xs text-emerald-200/50 text-center">DairySync v1.0</div>
        </div>
    </div>
</div>

<!-- =============================================
     DESKTOP SIDEBAR (hidden on mobile, visible on desktop)
     ============================================= -->
<aside class="desktop-sidebar w-64 flex-shrink-0 hidden md:flex flex-col min-h-screen sticky top-0"
       style="background: rgba(5,46,22,0.80); backdrop-filter: blur(22px); border-right: 1px solid rgba(34,197,94,0.20);">

    <div class="p-6 flex flex-col flex-1">

        <!-- Brand -->
        <div class="flex items-center gap-3 mb-8">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center shadow-lg flex-shrink-0"
                 style="background: linear-gradient(135deg, #15803d, #22c55e);">
                <svg viewBox="0 0 32 32" class="w-6 h-6" fill="none">
                    <ellipse cx="16" cy="19" rx="9" ry="7" fill="white" fill-opacity="0.92"/>
                    <ellipse cx="10" cy="14" rx="3.5" ry="4" fill="white" fill-opacity="0.92"/>
                    <ellipse cx="22" cy="14" rx="3.5" ry="4" fill="white" fill-opacity="0.92"/>
                    <circle cx="13" cy="20" r="1.4" fill="#15803d"/>
                    <circle cx="19" cy="20" r="1.4" fill="#15803d"/>
                    <ellipse cx="16" cy="23.5" rx="2.8" ry="1.8" fill="#bbf7d0"/>
                    <rect x="9" y="25" width="2.8" height="4.5" rx="1.4" fill="white" fill-opacity="0.85"/>
                    <rect x="20.2" y="25" width="2.8" height="4.5" rx="1.4" fill="white" fill-opacity="0.85"/>
                    <circle cx="7" cy="13.5" r="1.5" fill="#4ade80"/>
                    <circle cx="25" cy="13.5" r="1.5" fill="#4ade80"/>
                </svg>
            </div>
            <span class="font-bold text-white text-xl tracking-tight" style="font-family: 'Fraunces', serif;">
                DairySync
            </span>
        </div>

        <!-- Navigation (same as mobile but without the extra close behavior) -->
        <nav class="space-y-1 flex-1">
            <p class="text-xs font-semibold text-emerald-500/70 uppercase tracking-widest px-4 mb-2">Main</p>

            <a href="<?= UrlHelper::url('dashboard') ?>" class="<?= $nav_class('dashboard') ?>">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0h6"/>
                </svg>
                <span>Dashboard</span>
            </a>

            <a href="<?= UrlHelper::url('cows') ?>" class="<?= $nav_class('cows') ?>">
                <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none">
                    <ellipse cx="12" cy="14" rx="7" ry="5.5" fill="currentColor" fill-opacity="0.2" stroke="currentColor" stroke-width="1.5"/>
                    <ellipse cx="7.5" cy="10.5" rx="2.5" ry="3" stroke="currentColor" stroke-width="1.5"/>
                    <ellipse cx="16.5" cy="10.5" rx="2.5" ry="3" stroke="currentColor" stroke-width="1.5"/>
                    <line x1="9.5" y1="19" x2="9.5" y2="22" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    <line x1="14.5" y1="19" x2="14.5" y2="22" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    <circle cx="5.5" cy="10" r="1" fill="currentColor"/>
                    <circle cx="18.5" cy="10" r="1" fill="currentColor"/>
                </svg>
                <span>Cows</span>
            </a>

            <a href="<?= UrlHelper::url('health') ?>" class="<?= $nav_class('health') ?>">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                </svg>
                <span>Health Records</span>
            </a>

            <a href="<?= UrlHelper::url('milk_production') ?>" class="<?= $nav_class('milk_production') ?>">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                <span>Milk Production</span>
            </a>

            <a href="<?= UrlHelper::url('milk_sales') ?>" class="<?= $nav_class('milk_sales') ?>">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4l1-12z"/>
                </svg>
                <span>Milk Sales</span>
            </a>

            <a href="<?= UrlHelper::url('feeds') ?>" class="<?= $nav_class('feeds') ?>">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 13V7a2 2 0 00-2-2h-3V3H9v2H6a2 2 0 00-2 2v6m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0H4m5 4h6"/>
                </svg>
                <span>Feed Management</span>
            </a>

            <a href="<?= UrlHelper::url('income') ?>" class="<?= $nav_class('income') ?>">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>Income</span>
            </a>

            <a href="<?= UrlHelper::url('milk_report') ?>" class="<?= $nav_class('milk_report') ?>">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/>
                </svg>
                <span>Reports</span>
            </a>

            <a href="<?= UrlHelper::url('settings') ?>" class="<?= $nav_class('settings') ?>">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span>Settings</span>
            </a>

            <a href="<?= UrlHelper::url('logout') ?>" class="<?= $nav_class('logout') ?>">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                <span>Logout</span>
            </a>
        </nav>

        <!-- Footer -->
        <div class="pt-6 border-t border-emerald-500/10 mt-6">
            <div class="text-xs text-emerald-200/50 text-center">DairySync v1.0</div>
        </div>
    </div>
</aside>

<!-- =============================================
     HAMBURGER JS (improved)
     ============================================= -->
<script>
    function toggleMobileMenu() {
        const sidebar  = document.getElementById('mobileSidebar');
        const backdrop = document.getElementById('mobileBackdrop');
        const hamIcon  = document.getElementById('hamburgerIcon');
        const closeIcon = document.getElementById('closeIcon');

        if (!sidebar) return;
        const isOpen = !sidebar.classList.contains('-translate-x-full');

        if (isOpen) {
            closeMobileMenu();
        } else {
            sidebar.classList.remove('-translate-x-full');
            sidebar.classList.add('mobile-sidebar-open');
            backdrop.classList.remove('hidden');
            hamIcon.classList.add('hidden');
            closeIcon.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeMobileMenu() {
        const sidebar  = document.getElementById('mobileSidebar');
        const backdrop = document.getElementById('mobileBackdrop');
        const hamIcon  = document.getElementById('hamburgerIcon');
        const closeIcon = document.getElementById('closeIcon');

        if (sidebar) {
            sidebar.classList.add('-translate-x-full');
            sidebar.classList.remove('mobile-sidebar-open');
        }
        if (backdrop) backdrop.classList.add('hidden');
        if (hamIcon) hamIcon.classList.remove('hidden');
        if (closeIcon) closeIcon.classList.add('hidden');
        document.body.style.overflow = '';
    }

    // Close when a navigation link is clicked (better UX)
    document.addEventListener('DOMContentLoaded', function() {
        const mobileLinks = document.querySelectorAll('#mobileSidebar a');
        mobileLinks.forEach(link => {
            link.addEventListener('click', closeMobileMenu);
        });

        // Close on ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeMobileMenu();
        });
    });
</script>

<!-- Sidebar responsive fallback in case Tailwind output is stale -->
<style>
    .mobile-header {
        display: flex !important;
    }

    .mobile-sidebar {
        display: flex !important;
        transform: translateX(-100%);
    }

    .mobile-sidebar.mobile-sidebar-open {
        transform: translateX(0) !important;
    }

    @media (max-width: 767px) {
        .desktop-sidebar {
            display: none !important;
        }
    }

    @media (min-width: 768px) {
        .mobile-header,
        #mobileBackdrop,
        .mobile-sidebar {
            display: none !important;
        }

        .desktop-sidebar {
            display: flex !important;
        }
    }
</style>

<?php
}
?>
