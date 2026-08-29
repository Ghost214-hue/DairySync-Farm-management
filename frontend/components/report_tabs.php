<?php
// /frontend/components/report_tabs.php
require_once dirname(__DIR__, 2) . '/router/urlHelper.php';
$active_tab = $active_tab ?? 'milk'; // 'milk' or 'income'
?>

<div class="mb-6 border-b border-slate-200 overflow-x-auto">
    <nav class="flex gap-1 min-w-max" aria-label="Report tabs">
        <a href="<?= UrlHelper::url('milk_report') ?>"
           class="px-4 md:px-5 py-3 text-sm md:text-base font-medium rounded-t-2xl transition-all whitespace-nowrap <?= $active_tab === 'milk' ? 'bg-white text-emerald-700 shadow-sm border-x border-t border-slate-200' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-50' ?>">
            <i class="fas fa-tachometer-alt mr-2"></i> Milk Production Report
        </a>
        <a href="<?= UrlHelper::url('income_report') ?>"
           class="px-4 md:px-5 py-3 text-sm md:text-base font-medium rounded-t-2xl transition-all whitespace-nowrap <?= $active_tab === 'income' ? 'bg-white text-emerald-700 shadow-sm border-x border-t border-slate-200' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-50' ?>">
            <i class="fas fa-chart-line mr-2"></i> Income Report
        </a>
    </nav>
</div>
