<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../../router/urlHelper.php';

$conn = getDatabase();

$user_id = null;

// Check for share token first (allows public access)
if (isset($_GET['share']) && preg_match('/^[A-Za-z0-9\/=]+$/', $_GET['share'])) {
    $share_parts = explode(':', base64_decode($_GET['share']));
    if (count($share_parts) === 3) {
        $cow_id = (int)$share_parts[0];
        $share_user_id = (int)$share_parts[1];
        // Token valid for 30 days
        if ($share_parts[2] > time() - (30 * 24 * 60 * 60)) {
            $user_id = $share_user_id;
        }
    }
}

// If no valid share token, require authentication
if ($user_id === null) {
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
        header('Location: /h3j5n8q1e81ea2b3a2d2bcf5ce5f54dc81c6d327031');
        exit();
    }
    $user_id = (int)$_SESSION['user_id'];
}

// Get cow ID from URL
$cow_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($cow_id <= 0) {
    header('Location: ' . UrlHelper::url('cows'));
    exit();
}

// Get cow details
$cow_query = "SELECT * FROM cows WHERE id = ? AND user_id = ? LIMIT 1";
$stmt = $conn->prepare($cow_query);
$stmt->bind_param("ii", $cow_id, $user_id);
$stmt->execute();
$cow = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$cow) {
    if (isset($_GET['share'])) {
        http_response_code(404);
        die("Cow not found or share link expired.");
    }
    header('Location: ' . UrlHelper::url('cows'));
    exit();
}

// Get farm info
$farm_query = "SELECT farm_name, location FROM farms WHERE user_id = ? LIMIT 1";
$stmt = $conn->prepare($farm_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$farm = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Get milk production summary
$milk_summary = [];
$milk_query = "SELECT 
                production_date,
                morning_litres,
                evening_litres,
                (morning_litres + evening_litres) as total_litres
              FROM milk_production 
              WHERE cow_id = ? AND user_id = ?
              ORDER BY production_date DESC
              LIMIT 30";
$stmt = $conn->prepare($milk_query);
$stmt->bind_param("ii", $cow_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $milk_summary[] = $row;
}
$stmt->close();

// Get recent milk records (last 60 days) for the Recent Milk Records table
$recent_milk = [];
$recent_query = "SELECT 
                    production_date,
                    morning_litres,
                    evening_litres,
                    (morning_litres + evening_litres) AS total_litres,
                    notes
                FROM milk_production 
                WHERE cow_id = ? AND user_id = ?
                  AND production_date >= DATE_SUB(CURDATE(), INTERVAL 60 DAY)
                ORDER BY production_date DESC
                LIMIT 60";
$stmt = $conn->prepare($recent_query);
$stmt->bind_param("ii", $cow_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $recent_milk[] = $row;
}
$stmt->close();

$total_milk = array_sum(array_column($milk_summary, 'total_litres'));
$avg_daily = count($milk_summary) > 0 ? $total_milk / count($milk_summary) : 0;

// Get health records
$health_records = [];
$health_query = "SELECT * FROM health_records 
                 WHERE cow_id = ? AND user_id = ?
                 ORDER BY record_date DESC
                 LIMIT 10";
$stmt = $conn->prepare($health_query);
$stmt->bind_param("ii", $cow_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $health_records[] = $row;
}
$stmt->close();

// Generate shareable link (only for authenticated users viewing their own cows)
if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $user_id) {
    $share_token = base64_encode($cow_id . ':' . $user_id . ':' . time());
    $share_link = UrlHelper::url('cow_profile') . '?id=' . $cow_id . '&share=' . $share_token;
} else {
    $share_link = '';
}

$conn->close();
?>