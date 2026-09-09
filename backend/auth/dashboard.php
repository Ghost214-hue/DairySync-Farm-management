<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    header('Location: /h3j5n8q1e81ea2b3a2d2bcf5ce5f54dc81c6d327031');
    exit();
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/FarmContext.php';
$conn = getDatabase();
$user_id = (int)$_SESSION['user_id'];
$farm_id = FarmContext::currentFarmId();

$user = [];
$user_query = "SELECT id, username, email, phone, created_at FROM users WHERE id = ?";
$stmt = $conn->prepare($user_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc() ?? [];
$stmt->close();

$farm = $farm_id ? FarmContext::currentFarm() : [];


$total_cows = 0;
$cows_query = "SELECT COUNT(*) as total FROM cows WHERE farm_id = ?";
$stmt = $conn->prepare($cows_query);
$stmt->bind_param("i", $farm_id);
$stmt->execute();
$total_cows = (int)($stmt->get_result()->fetch_assoc()['total'] ?? 0);
$stmt->close();

$healthy_cows = 0;
$healthy_query = "SELECT COUNT(*) as healthy FROM cows WHERE farm_id = ? AND status = 'Active'";
$stmt = $conn->prepare($healthy_query);
$stmt->bind_param("i", $farm_id);
$stmt->execute();
$healthy_cows = (int)($stmt->get_result()->fetch_assoc()['healthy'] ?? 0);
$stmt->close();

$today_milk = 0;
$milk_query = "SELECT SUM(quantity) as total FROM milk_production WHERE farm_id = ? AND production_date = CURDATE()";
$stmt = $conn->prepare($milk_query);
$stmt->bind_param("i", $farm_id);
$stmt->execute();
$today_milk = (float)($stmt->get_result()->fetch_assoc()['total'] ?? 0);
$stmt->close();

$avg_milk_per_cow = 0;
$avg_query = "SELECT AVG(quantity) as avg_milk FROM milk_production WHERE farm_id = ? AND production_date = CURDATE()";
$stmt = $conn->prepare($avg_query);
$stmt->bind_param("i", $farm_id);
$stmt->execute();
$avg_milk_per_cow = round((float)($stmt->get_result()->fetch_assoc()['avg_milk'] ?? 0), 1);
$stmt->close();

$total_income = 0;
$income_query = "SELECT SUM(total_amount) as income FROM income WHERE farm_id = ?";
$stmt = $conn->prepare($income_query);
$stmt->bind_param("i", $farm_id);
$stmt->execute();
$total_income = (float)($stmt->get_result()->fetch_assoc()['income'] ?? 0);
$stmt->close();

$total_expenses = 0;
$expense_query = "SELECT SUM(amount) as expenses FROM expenses WHERE farm_id = ?";
$stmt = $conn->prepare($expense_query);
$stmt->bind_param("i", $farm_id);
$stmt->execute();
$total_expenses = (float)($stmt->get_result()->fetch_assoc()['expenses'] ?? 0);
$stmt->close();

$net_profit = $total_income - $total_expenses;


$feed_cost_month = 0;
$feed_query = "SELECT SUM(cost) as feed_cost FROM feed_management WHERE farm_id = ? AND purchase_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
$stmt = $conn->prepare($feed_query);
$stmt->bind_param("i", $farm_id);
$stmt->execute();
$feed_cost_month = (float)($stmt->get_result()->fetch_assoc()['feed_cost'] ?? 0);
$stmt->close();

$health_alerts = [];
$alerts_query = "
    SELECT c.cow_name, h.condition_name, h.status
    FROM health_records h
    JOIN cows c ON h.cow_id = c.id
    WHERE h.farm_id = ? AND h.status IN ('Under Treatment', 'Critical')
    ORDER BY h.created_at DESC LIMIT 5
";
$stmt = $conn->prepare($alerts_query);
$stmt->bind_param("i", $farm_id);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) {
    $health_alerts[] = $row['cow_name'] . ' - ' . $row['condition_name'] . ' (' . $row['status'] . ')';
}
$stmt->close();

$upcoming_alerts = [];
$reminder_query = "
    SELECT cow_name, status, date_of_birth, 
           CASE 
               WHEN status = 'Pregnant' AND DATE_ADD(IFNULL(date_of_birth, CURDATE()), INTERVAL 270 DAY) BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 14 DAY) 
               THEN CONCAT(cow_name, ' is due to calve soon')
               WHEN status = 'Dry' AND DATEDIFF(CURDATE(), acquisition_date) > 60 
               THEN CONCAT(cow_name, ' has been dry for >60 days')
               ELSE NULL
           END as reminder
    FROM cows
    WHERE farm_id = ? AND status IN ('Pregnant', 'Dry')
    HAVING reminder IS NOT NULL
    LIMIT 5
";
$stmt = $conn->prepare($reminder_query);
$stmt->bind_param("i", $farm_id);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) {
    $upcoming_alerts[] = $row['reminder'];
}
$stmt->close();


$recent_milks = [];
$recent_query = "
    SELECT m.quantity, m.session, m.production_date, c.cow_name
    FROM milk_production m
    JOIN cows c ON m.cow_id = c.id
    WHERE m.farm_id = ?
    ORDER BY m.production_date DESC, m.created_at DESC
    LIMIT 5
";
$stmt = $conn->prepare($recent_query);
$stmt->bind_param("i", $farm_id);
$stmt->execute();
$recent_milks = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();


$trends = [];
$yesterday_milk = 0;
$yest_query = "SELECT SUM(quantity) as total FROM milk_production WHERE farm_id = ? AND production_date = DATE_SUB(CURDATE(), INTERVAL 1 DAY)";
$stmt = $conn->prepare($yest_query);
$stmt->bind_param("i", $farm_id);
$stmt->execute();
$yesterday_milk = (float)($stmt->get_result()->fetch_assoc()['total'] ?? 0);
$stmt->close();
$trends['milk_change'] = $yesterday_milk > 0 ? round(($today_milk - $yesterday_milk) / $yesterday_milk * 100, 1) : ($today_milk > 0 ? 100 : 0);


$last_month_cows = 0;
$last_month_query = "SELECT COUNT(*) as total FROM cows WHERE farm_id = ? AND created_at < DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
$stmt = $conn->prepare($last_month_query);
$stmt->bind_param("i", $farm_id);
$stmt->execute();
$last_month_cows = (int)($stmt->get_result()->fetch_assoc()['total'] ?? 0);
$stmt->close();
$trends['cows_change'] = $total_cows - $last_month_cows;


$milk_chart_labels = [];
$milk_chart_data = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $milk_chart_labels[] = date('D', strtotime($date));
    $chart_query = "SELECT SUM(quantity) as total FROM milk_production WHERE farm_id = ? AND production_date = ?";
    $stmt = $conn->prepare($chart_query);
    $stmt->bind_param("is", $farm_id, $date);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $milk_chart_data[] = (float)($row['total'] ?? 0);
    $stmt->close();
}

$conn->close();
?>