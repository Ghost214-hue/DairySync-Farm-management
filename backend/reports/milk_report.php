<?php
/**
 * Milk Report - data layer
 * Produces:
 *   - Overall period summary (produced / sold / NRM / sales value)
 *   - Daily farm performance with per-day cow breakdown (no N+1 queries)
 *   - Cow performance aggregates
 *   - Detailed records (existing functionality preserved)
 * All queries scoped to the logged-in user.
 * Milk sales come ONLY from income (source = 'Milk Sales').
 * Customer payments live in the dedicated `collections` table and are
 * NEVER counted as milk sold or revenue.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: /h3j5n8q1e81ea2b3a2d2bcf5ce5');
    exit();
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/SettingsHelper.php';

$conn = getDatabase();
$user_id = (int)$_SESSION['user_id'];
$settings = new SettingsHelper($user_id);
$milk_price = (float)$settings->getMilkPrice();

// ------------------------------------------------------------------
// Filters
// ------------------------------------------------------------------
$start_date = $_GET['start_date'] ?? date('Y-m-01');
$end_date   = $_GET['end_date']   ?? date('Y-m-t');
$cow_id     = isset($_GET['cow_id']) && $_GET['cow_id'] !== '' ? (int)$_GET['cow_id'] : null;

$start_date = date('Y-m-d', strtotime($start_date));
$end_date   = date('Y-m-d', strtotime($end_date));

$cow_filter_sql = $cow_id ? " AND mp.cow_id = ?" : "";

// ------------------------------------------------------------------
// 1. Production per date AND per cow per date - single grouped query
//    reused for both the daily breakdown and cow aggregates (no N+1)
// ------------------------------------------------------------------
$prod_sql = "
    SELECT mp.production_date, mp.cow_id, c.cow_name,
           SUM(mp.morning_litres) AS morning_total,
           SUM(mp.evening_litres) AS evening_total,
           SUM(mp.morning_litres + mp.evening_litres) AS day_total
    FROM milk_production mp
    LEFT JOIN cows c ON c.id = mp.cow_id
    WHERE mp.user_id = ? AND mp.production_date BETWEEN ? AND ? $cow_filter_sql
    GROUP BY mp.production_date, mp.cow_id, c.cow_name
    ORDER BY mp.production_date DESC
";
$params = [$user_id, $start_date, $end_date];
$types  = "iss";
if ($cow_id) { $params[] = $cow_id; $types .= "i"; }
$stmt = $conn->prepare($prod_sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$prod_rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Group by date for the daily section; aggregate by cow for Section D
$daily = [];   // date => summary + cows breakdown
$cow_agg = []; // cow_id => aggregate
foreach ($prod_rows as $r) {
    $d = $r['production_date'];
    if (!isset($daily[$d])) {
        $daily[$d] = ['date' => $d, 'produced' => 0.0, 'sold' => 0.0, 'sales_value' => 0.0, 'nrm' => 0.0, 'nrm_value' => 0.0, 'cows' => []];
    }
    $daily[$d]['produced'] += (float)$r['day_total'];
    $daily[$d]['cows'][] = [
        'cow_id'   => (int)$r['cow_id'],
        'cow_name' => $r['cow_name'] ?? '?',
        'morning'  => (float)$r['morning_total'],
        'evening'  => (float)$r['evening_total'],
        'total'    => (float)$r['day_total'],
    ];

    $cid = (int)$r['cow_id'];
    if (!isset($cow_agg[$cid])) {
        $cow_agg[$cid] = ['cow_id' => $cid, 'cow_name' => $r['cow_name'] ?? '?', 'morning' => 0.0, 'evening' => 0.0, 'total' => 0.0, 'days' => 0, 'best' => 0.0];
    }
    $cow_agg[$cid]['morning'] += (float)$r['morning_total'];
    $cow_agg[$cid]['evening'] += (float)$r['evening_total'];
    $cow_agg[$cid]['total']   += (float)$r['day_total'];
    $cow_agg[$cid]['days']    += 1;
    $cow_agg[$cid]['best']     = max($cow_agg[$cid]['best'], (float)$r['day_total']);
}

// ------------------------------------------------------------------
// 2. Daily milk sales (revenue events only - source = 'Milk Sales').
//    Customer payments (collections table) are excluded by design.
// ------------------------------------------------------------------
$sales_sql = "
    SELECT income_date,
           SUM(litres) AS sold_litres,
           SUM(total_amount) AS sales_value
    FROM income
    WHERE user_id = ? AND source = 'Milk Sales' AND income_date BETWEEN ? AND ?
    GROUP BY income_date
";
$stmt = $conn->prepare($sales_sql);
$stmt->bind_param("iss", $user_id, $start_date, $end_date);
$stmt->execute();
$sales_res = $stmt->get_result();
while ($row = $sales_res->fetch_assoc()) {
    $d = $row['income_date'];
    if (isset($daily[$d])) {
        $daily[$d]['sold']        = (float)$row['sold_litres'];
        $daily[$d]['sales_value'] = (float)$row['sales_value'];
    }
}
$stmt->close();

// NRM per day: produced - sold (floored at 0), valued at current milk price
$grand_produced = 0.0; $grand_sold = 0.0; $grand_sales_value = 0.0; $grand_nrm = 0.0;
foreach ($daily as $d => $info) {
    $nrm = max(0, $info['produced'] - $info['sold']);
    $daily[$d]['nrm']       = $nrm;
    $daily[$d]['nrm_value'] = $nrm * $milk_price;
    $grand_produced    += $info['produced'];
    $grand_sold        += $info['sold'];
    $grand_sales_value += $info['sales_value'];
    $grand_nrm         += $nrm;
}
krsort($daily); // newest day first

$days_in_range = max(1, (strtotime($end_date) - strtotime($start_date)) / 86400 + 1);

// Best / lowest production days
$best_day = null; $worst_day = null;
foreach ($daily as $d => $info) {
    if ($best_day === null || $info['produced'] > $best_day['produced']) $best_day = $info;
    if ($worst_day === null || $info['produced'] < $worst_day['produced']) $worst_day = $info;
}

// ------------------------------------------------------------------
// 3. Cow performance aggregates (avg/day, herd contribution)
// ------------------------------------------------------------------
$herd_total = array_sum(array_column($cow_agg, 'total'));
foreach ($cow_agg as &$c) {
    $c['avg_per_day']      = $c['days'] > 0 ? $c['total'] / $c['days'] : 0;
    $c['contribution_pct'] = $herd_total > 0 ? ($c['total'] / $herd_total) * 100 : 0;
}
unset($c);
usort($cow_agg, function($a, $b) { return $b['total'] <=> $a['total']; });

// ------------------------------------------------------------------
// 4. Detailed records (existing functionality preserved)
// ------------------------------------------------------------------
$detail_sql = "
    SELECT 
        mp.production_date,
        c.cow_name,
        mp.morning_litres,
        mp.evening_litres,
        mp.morning_litres + mp.evening_litres AS total_litres
    FROM milk_production mp
    LEFT JOIN cows c ON mp.cow_id = c.id
    WHERE mp.user_id = ? AND mp.production_date BETWEEN ? AND ? $cow_filter_sql
    ORDER BY mp.production_date DESC, mp.id DESC
";
$stmt = $conn->prepare($detail_sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$records = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// ------------------------------------------------------------------
// 5. Cow dropdown list
// ------------------------------------------------------------------
$cows = [];
$cow_query = "SELECT id, cow_name FROM cows WHERE user_id = ? ORDER BY cow_name";
$stmt = $conn->prepare($cow_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$cow_res = $stmt->get_result();
while ($c = $cow_res->fetch_assoc()) {
    $cows[] = $c;
}
$stmt->close();
$conn->close();

// Period summary (used by the view)
$summary = [
    'total_milk'  => $grand_produced,
    'total_sold'  => $grand_sold,
    'sales_value' => $grand_sales_value,
    'total_nrm'   => $grand_nrm,
    'nrm_value'   => $grand_nrm * $milk_price,
    'avg_daily'   => $grand_produced / $days_in_range,
    'avg_per_cow' => count($cow_agg) > 0 ? $herd_total / count($cow_agg) : 0,
];
