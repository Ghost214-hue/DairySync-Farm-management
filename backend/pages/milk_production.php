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
// Handle add/delete milk records
// ------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add_record') {
        $cow_id          = (int)($_POST['cow_id'] ?? 0);
        $quantity        = (float)($_POST['quantity'] ?? 0);
        $session_type    = trim($_POST['session_type'] ?? 'Morning');
        $quality         = trim($_POST['quality'] ?? 'Good');
        $production_date = $_POST['production_date'] ?? date('Y-m-d');
        $notes           = trim($_POST['notes'] ?? '');

        $allowed_sessions = ['Morning', 'Afternoon', 'Evening'];
        $allowed_quality  = ['Excellent', 'Good', 'Average', 'Poor'];
        if (!in_array($session_type, $allowed_sessions)) $session_type = 'Morning';
        if (!in_array($quality, $allowed_quality)) $quality = 'Good';

        $insert = "INSERT INTO milk_production (user_id, farm_id, cow_id, quantity, session, quality, production_date, notes)
                   VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insert);
        $stmt->bind_param("iiidssss", $user_id, $farm_id, $cow_id, $quantity, $session_type, $quality, $production_date, $notes);
        
        if ($stmt->execute()) {
            // Automatically create income record with current milk price
            $total_amount = $quantity * $milk_price;
            $source = 'Milk Sales';
            $insert_income = "INSERT INTO income (user_id, farm_id, source, litres, rate_per_litre, total_amount, income_date)
                              VALUES (?, ?, ?, ?, ?, ?, ?)";
            $inc_stmt = $conn->prepare($insert_income);
            $inc_stmt->bind_param("iisddds", $user_id, $farm_id, $source, $quantity, $milk_price, $total_amount, $production_date);
            $inc_stmt->execute();
            $inc_stmt->close();
            $_SESSION['milk_success'] = "Milk production recorded and income added automatically.";
        } else {
            $_SESSION['milk_error'] = "Failed to record milk production.";
        }
        $stmt->close();
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }

    if ($action === 'delete_record') {
        $record_id = (int)($_POST['record_id'] ?? 0);
        // Get milk details to also delete matching income
        $select = "SELECT quantity, production_date FROM milk_production WHERE id = ? AND user_id = ?";
        $sel_stmt = $conn->prepare($select);
        $sel_stmt->bind_param("ii", $record_id, $user_id);
        $sel_stmt->execute();
        $milk_data = $sel_stmt->get_result()->fetch_assoc();
        $sel_stmt->close();

        if ($milk_data) {
            $delete = "DELETE FROM milk_production WHERE id = ? AND user_id = ?";
            $stmt = $conn->prepare($delete);
            $stmt->bind_param("ii", $record_id, $user_id);
            if ($stmt->execute()) {
                // Delete corresponding income entry (same date, litres, source)
                $delete_income = "DELETE FROM income WHERE user_id = ? AND source = 'Milk Sales' AND litres = ? AND income_date = ?";
                $inc_stmt = $conn->prepare($delete_income);
                $inc_stmt->bind_param("ids", $user_id, $milk_data['quantity'], $milk_data['production_date']);
                $inc_stmt->execute();
                $inc_stmt->close();
                $_SESSION['milk_success'] = "Record and linked income deleted.";
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
$month_query = "SELECT SUM(quantity) as total FROM milk_production 
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
    $milk_records[] = $row;
}
$stmt->close();

// ------------------------------------------------------------------
// Today's total milk
// ------------------------------------------------------------------
$today_milk = 0;
$today_query = "SELECT SUM(quantity) as total FROM milk_production WHERE user_id = ? AND production_date = CURDATE()";
$stmt = $conn->prepare($today_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$today_res = $stmt->get_result()->fetch_assoc();
$today_milk = (float)($today_res['total'] ?? 0);
$stmt->close();

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