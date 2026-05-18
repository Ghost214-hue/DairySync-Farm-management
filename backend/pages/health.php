<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    header('Location: /farm-management/h3j5n8q1');
    exit();
}

require_once __DIR__ . '/../config/database.php';

$conn = getDatabase();

$user_id = (int) $_SESSION['user_id'];
$farm_id = (int) ($_SESSION['farm_id'] ?? 1);

const HEALTH_STATUSES = [
    'Healthy',
    'Under Treatment',
    'Recovered',
    'Critical'
];

function clean($value) {
    return htmlspecialchars(trim($value));
}

function healthRedirect($type, $message) {
    $_SESSION['health_' . $type] = $message;

    header('Location: /farm-management/w5c8p2k9');
    exit();
}

# FETCH COWS
$cows = [];

$cow_stmt = $conn->prepare("
    SELECT id, cow_name, ear_tag
    FROM cows
    WHERE user_id = ?
    ORDER BY cow_name ASC
");

$cow_stmt->bind_param("i", $user_id);
$cow_stmt->execute();

$cow_result = $cow_stmt->get_result();

while ($row = $cow_result->fetch_assoc()) {
    $cows[] = $row;
}

$cow_stmt->close();

# FETCH HEALTH RECORDS
$health_records = [];

$health_stmt = $conn->prepare("
    SELECT
        hr.*,
        c.cow_name,
        c.ear_tag
    FROM health_records hr
    JOIN cows c ON c.id = hr.cow_id
    WHERE hr.user_id = ?
    ORDER BY hr.record_date DESC
");

$health_stmt->bind_param("i", $user_id);
$health_stmt->execute();

$health_result = $health_stmt->get_result();

while ($row = $health_result->fetch_assoc()) {
    $health_records[] = $row;
}

$health_stmt->close();

# ADD RECORD
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $action = clean($_POST['action'] ?? '');

    if ($action === 'add_record') {

        $cow_id     = (int) ($_POST['cow_id'] ?? 0);
        $condition  = clean($_POST['condition_name'] ?? '');
        $status     = clean($_POST['status'] ?? 'Healthy');
        $treatment  = clean($_POST['treatment'] ?? '');
        $notes      = clean($_POST['notes'] ?? '');
        $recordDate = clean($_POST['record_date'] ?? '');

        if (!$cow_id) {
            healthRedirect('error', 'Please select a cow.');
        }

        if (!$condition) {
            healthRedirect('error', 'Condition is required.');
        }

        if (!in_array($status, HEALTH_STATUSES)) {
            healthRedirect('error', 'Invalid status.');
        }

        $insert = $conn->prepare("
            INSERT INTO health_records (
                user_id,
                farm_id,
                cow_id,
                condition_name,
                treatment,
                status,
                notes,
                record_date
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $insert->bind_param(
            "iiisssss",
            $user_id,
            $farm_id,
            $cow_id,
            $condition,
            $treatment,
            $status,
            $notes,
            $recordDate
        );

        $insert->execute();
        $insert->close();

        healthRedirect('success', 'Health record added successfully.');
    }

    # DELETE RECORD
    if ($action === 'delete_record') {

        $record_id = (int) ($_POST['record_id'] ?? 0);

        $delete = $conn->prepare("
            DELETE FROM health_records
            WHERE id = ? AND user_id = ?
        ");

        $delete->bind_param("ii", $record_id, $user_id);
        $delete->execute();
        $delete->close();

        healthRedirect('success', 'Health record deleted.');
    }
}
?>