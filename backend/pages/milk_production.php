<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/SettingsHelper.php';

$conn = getDatabase();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    header('Location: /farm-management/h3j5n8q1');
    exit();
}

$user_id = (int)$_SESSION['user_id'];
$settings = new SettingsHelper($user_id);
$milk_price = $settings->getMilkPrice();

// ------------------------------------------------------------------
// Handle milk price update
// ------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_price') {
    $new_price = (float)($_POST['milk_price'] ?? 0);
    if ($new_price > 0) {
        $settings->setMilkPrice($new_price);
        $_SESSION['milk_success'] = "Milk price updated to KSh " . number_format($new_price, 2);
        $milk_price = $new_price;
    } else {
        $_SESSION['milk_error'] = "Invalid price value.";
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Get user's first farm
$farm_id = 0;
$farm_query = "SELECT id FROM farms WHERE user_id = ? LIMIT 1";
$farm_stmt = $conn->prepare($farm_query);
$farm_stmt->bind_param("i", $user_id);
$farm_stmt->execute();
$farm_result = $farm_stmt->get_result();
if ($farm = $farm_result->fetch_assoc()) {
    $farm_id = (int)$farm['id'];
}
$farm_stmt->close();

// ------------------------------------------------------------------
// Handle add/update milk record
// ------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add_record') {
        $cow_id          = (int)($_POST['cow_id'] ?? 0);
        $production_date = $_POST['production_date'] ?? date('Y-m-d');
        $morning         = (float)($_POST['morning_litres'] ?? 0);
        $evening         = (float)($_POST['evening_litres'] ?? 0);
        $litres_sold     = (float)($_POST['litres_sold'] ?? 0);
        $notes           = trim($_POST['notes'] ?? '');

        // Check if a record already exists for this cow & date
        $check = "SELECT id FROM milk_production WHERE cow_id = ? AND production_date = ? AND user_id = ?";
        $check_stmt = $conn->prepare($check);
        $check_stmt->bind_param("isi", $cow_id, $production_date, $user_id);
        $check_stmt->execute();
        $existing = $check_stmt->get_result()->fetch_assoc();
        $check_stmt->close();

        if ($existing) {
            // Update existing record
            $update = "UPDATE milk_production 
                       SET morning_litres = ?, evening_litres = ?, litres_sold = ?, notes = ?
                       WHERE id = ?";
            $upd_stmt = $conn->prepare($update);
            $upd_stmt->bind_param("dddsi", $morning, $evening, $litres_sold, $notes, $existing['id']);
            $success = $upd_stmt->execute();
            $upd_stmt->close();
            if ($success) {
                $_SESSION['milk_success'] = "Milk record updated successfully.";
            } else {
                $_SESSION['milk_error'] = "Failed to update record.";
            }
        } else {
            // Insert new record
            $insert = "INSERT INTO milk_production (user_id, farm_id, cow_id, production_date, morning_litres, evening_litres, litres_sold, notes)
                       VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($insert);
            $stmt->bind_param("iiisddds", $user_id, $farm_id, $cow_id, $production_date, $morning, $evening, $litres_sold, $notes);
            $success = $stmt->execute();
            $stmt->close();
            if ($success) {
                $_SESSION['milk_success'] = "Milk record added successfully.";
            } else {
                $_SESSION['milk_error'] = "Failed to add record.";
            }
        }

        // Update income for the sold milk (delete old, insert new with NRM)
        if ($success) {
            // Delete previous income for this cow & date (source Milk Sales)
            $del_inc = "DELETE FROM income WHERE user_id = ? AND source = 'Milk Sales' AND cow_id = ? AND income_date = ?";
            $del_stmt = $conn->prepare($del_inc);
            $del_stmt->bind_param("iis", $user_id, $cow_id, $production_date);
            $del_stmt->execute();
            $del_stmt->close();

            // Insert new income record with NRM value
            $total_produced = $morning + $evening;
            $nrm = $total_produced - $litres_sold;
            $nrm_value = $nrm * $milk_price;
            $total_amount = $litres_sold * $milk_price;

            $insert_income = "INSERT INTO income (user_id, farm_id, source, litres, rate_per_litre, total_amount, nrm_value, income_date, cow_id)
                              VALUES (?, ?, 'Milk Sales', ?, ?, ?, ?, ?, ?)";
            $inc_stmt = $conn->prepare($insert_income);
            $inc_stmt->bind_param("iiddddsi", $user_id, $farm_id, $litres_sold, $milk_price, $total_amount, $nrm_value, $production_date, $cow_id);
            $inc_stmt->execute();
            $inc_stmt->close();
        }

        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }

    if ($action === 'delete_record') {
        $record_id = (int)($_POST['record_id'] ?? 0);
        // Get cow_id and date to delete associated income
        $fetch = "SELECT cow_id, production_date FROM milk_production WHERE id = ? AND user_id = ?";
        $fetch_stmt = $conn->prepare($fetch);
        $fetch_stmt->bind_param("ii", $record_id, $user_id);
        $fetch_stmt->execute();
        $row = $fetch_stmt->get_result()->fetch_assoc();
        $fetch_stmt->close();

        if ($row) {
            $delete = "DELETE FROM milk_production WHERE id = ? AND user_id = ?";
            $stmt = $conn->prepare($delete);
            $stmt->bind_param("ii", $record_id, $user_id);
            if ($stmt->execute()) {
                // Delete corresponding income entry
                $del_inc = "DELETE FROM income WHERE user_id = ? AND source = 'Milk Sales' AND cow_id = ? AND income_date = ?";
                $inc_stmt = $conn->prepare($del_inc);
                $inc_stmt->bind_param("iis", $user_id, $row['cow_id'], $row['production_date']);
                $inc_stmt->execute();
                $inc_stmt->close();
                $_SESSION['milk_success'] = "Record deleted successfully.";
            } else {
                $_SESSION['milk_error'] = "Failed to delete record.";
            }
            $stmt->close();
        } else {
            $_SESSION['milk_error'] = "Record not found.";
        }
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}

// ------------------------------------------------------------------
// Monthly total milk
// ------------------------------------------------------------------
$current_month_total = 0;
$month_query = "SELECT SUM(morning_litres + evening_litres) as total FROM milk_production 
                WHERE user_id = ? AND YEAR(production_date) = YEAR(CURDATE()) AND MONTH(production_date) = MONTH(CURDATE())";
$stmt = $conn->prepare($month_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$month_res = $stmt->get_result()->fetch_assoc();
$current_month_total = (float)($month_res['total'] ?? 0);
$stmt->close();

// ------------------------------------------------------------------
// Pagination (50 per page)
// ------------------------------------------------------------------
$limit = 50;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $limit;

$total_query = "SELECT COUNT(*) as count FROM milk_production WHERE user_id = ?";
$stmt = $conn->prepare($total_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$total_records = (int)($stmt->get_result()->fetch_assoc()['count'] ?? 0);
$total_pages = ceil($total_records / $limit);
$stmt->close();

$milk_records = [];
$query = "SELECT mp.*, c.cow_name, c.ear_tag
          FROM milk_production mp
          LEFT JOIN cows c ON c.id = mp.cow_id
          WHERE mp.user_id = ?
          ORDER BY mp.production_date DESC, mp.id DESC
          LIMIT ? OFFSET ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("iii", $user_id, $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $row['total_litres'] = $row['morning_litres'] + $row['evening_litres'];
    $row['nrm'] = $row['total_litres'] - $row['litres_sold'];
    $milk_records[] = $row;
}
$stmt->close();

// ------------------------------------------------------------------
// Today's total milk, sold, and NRM value
// ------------------------------------------------------------------
$today_milk = 0;
$today_query = "SELECT SUM(morning_litres + evening_litres) as total FROM milk_production 
                WHERE user_id = ? AND production_date = CURDATE()";
$stmt = $conn->prepare($today_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$today_res = $stmt->get_result()->fetch_assoc();
$today_milk = (float)($today_res['total'] ?? 0);
$stmt->close();

$today_sold = 0;
$sold_query = "SELECT SUM(litres_sold) as sold FROM milk_production WHERE user_id = ? AND production_date = CURDATE()";
$stmt = $conn->prepare($sold_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$sold_res = $stmt->get_result()->fetch_assoc();
$today_sold = (float)($sold_res['sold'] ?? 0);
$stmt->close();

$today_nrm = $today_milk - $today_sold;
$today_nrm_value = $today_nrm * $milk_price;

// ------------------------------------------------------------------
// List of cows for dropdown
// ------------------------------------------------------------------
$cows = [];
$cow_query = "SELECT id, cow_name, ear_tag FROM cows WHERE user_id = ? ORDER BY cow_name ASC";
$stmt = $conn->prepare($cow_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$cow_result = $stmt->get_result();
while ($cow = $cow_result->fetch_assoc()) {
    $cows[] = $cow;
}
$stmt->close();

$conn->close();
?>