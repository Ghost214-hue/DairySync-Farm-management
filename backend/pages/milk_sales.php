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
// Helpers for milk sales NRM calculation
// ------------------------------------------------------------------
function getDailyProduction($conn, int $user_id, string $date): float {
    $query = "SELECT SUM(morning_litres + evening_litres) AS total FROM milk_production WHERE user_id = ? AND production_date = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("is", $user_id, $date);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return (float)($result['total'] ?? 0);
}

function getDailyMilkSalesTotal($conn, int $user_id, string $date): float {
    $query = "SELECT SUM(litres) AS total FROM income WHERE user_id = ? AND source = 'Milk Sales' AND income_date = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("is", $user_id, $date);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return (float)($result['total'] ?? 0);
}

function recalculateDailyMilkSalesNrm($conn, int $user_id, string $date): void {
    $daily_total = getDailyProduction($conn, $user_id, $date);
    $query = "SELECT id, litres FROM income WHERE user_id = ? AND source = 'Milk Sales' AND income_date = ? ORDER BY created_at ASC, id ASC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("is", $user_id, $date);
    $stmt->execute();
    $result = $stmt->get_result();
    $sales = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    $update = $conn->prepare("UPDATE income SET nrm_value = ? WHERE id = ?");
    $cumulative = 0.0;
    foreach ($sales as $sale) {
        $cumulative += (float)$sale['litres'];
        $nrm_value = max(0, $daily_total - $cumulative);
        $update->bind_param("di", $nrm_value, $sale['id']);
        $update->execute();
    }
    $update->close();
}

// ------------------------------------------------------------------
// Handle add/update/delete milk sale record
// ------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add_sale') {
        $sale_date       = $_POST['sale_date'] ?? date('Y-m-d');
        $litres_sold     = (float)($_POST['litres_sold'] ?? 0);
        $customer_name   = trim($_POST['customer_name'] ?? '');

        $daily_total = getDailyProduction($conn, $user_id, $sale_date);
        $today_sales = getDailyMilkSalesTotal($conn, $user_id, $sale_date);
        $available_before_sale = max(0, $daily_total - $today_sales);

        // Validate input
        if ($litres_sold <= 0 || empty($customer_name)) {
            $_SESSION['milk_error'] = "Please fill in all required fields.";
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }

        if ($litres_sold > $available_before_sale) {
            $_SESSION['milk_error'] = "Cannot sell more milk than is available for today.";
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }

        // Calculate values
        $total_amount = $litres_sold * $milk_price;
        $source = 'Milk Sales';
        $nrm_value = max(0, $available_before_sale - $litres_sold);

        // Insert income record
        $insert = "INSERT INTO income (user_id, farm_id, source, customer_name, litres, rate_per_litre, total_amount, nrm_value, income_date, created_at)
                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($insert);
        $stmt->bind_param("iissdddds", $user_id, $farm_id, $source, $customer_name, $litres_sold, $milk_price, $total_amount, $nrm_value, $sale_date);
        
        if ($stmt->execute()) {
            recalculateDailyMilkSalesNrm($conn, $user_id, $sale_date);
            $_SESSION['milk_success'] = "Milk sale recorded successfully (KSh " . number_format($total_amount, 2) . ")";
        } else {
            $_SESSION['milk_error'] = "Failed to record milk sale.";
        }
        $stmt->close();

        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    if ($action === 'delete_sale') {
        $sale_id = (int)($_POST['sale_id'] ?? 0);

        if ($sale_id > 0) {
            $date_query = "SELECT income_date FROM income WHERE id = ? AND user_id = ? AND source = 'Milk Sales' LIMIT 1";
            $stmt = $conn->prepare($date_query);
            $stmt->bind_param("ii", $sale_id, $user_id);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            if ($result) {
                $sale_date = $result['income_date'];
                $delete = "DELETE FROM income WHERE id = ? AND user_id = ? AND source = 'Milk Sales'";
                $stmt = $conn->prepare($delete);
                $stmt->bind_param("ii", $sale_id, $user_id);
                if ($stmt->execute()) {
                    recalculateDailyMilkSalesNrm($conn, $user_id, $sale_date);
                    $_SESSION['milk_success'] = "Milk sale deleted successfully.";
                } else {
                    $_SESSION['milk_error'] = "Failed to delete sale.";
                }
                $stmt->close();
            } else {
                $_SESSION['milk_error'] = "Sale not found.";
            }
        } else {
            $_SESSION['milk_error'] = "Sale not found.";
        }

        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

// ------------------------------------------------------------------
// Get TODAY's milk production
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

// Get today's sales (sold)
$today_sales = 0;
$today_sales_amount = 0;
$today_sales_query = "SELECT SUM(litres) as sold, SUM(total_amount) as amount FROM income 
                      WHERE user_id = ? AND source = 'Milk Sales' AND income_date = CURDATE()";
$stmt = $conn->prepare($today_sales_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$today_sales_res = $stmt->get_result()->fetch_assoc();
$today_sales = (float)($today_sales_res['sold'] ?? 0);
$today_sales_amount = (float)($today_sales_res['amount'] ?? 0);
$stmt->close();

// Calculate remaining milk for today
$remaining_milk = $today_milk - $today_sales;

// ------------------------------------------------------------------
// Pagination (50 per page)
// ------------------------------------------------------------------
$limit = 50;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $limit;

$total_query = "SELECT COUNT(*) as count FROM income WHERE user_id = ? AND source = 'Milk Sales'";
$stmt = $conn->prepare($total_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$total_records = (int)($stmt->get_result()->fetch_assoc()['count'] ?? 0);
$total_pages = ceil($total_records / $limit);
$stmt->close();

// Get milk sales records
$milk_sales = [];
$query = "SELECT i.*
          FROM income i
          WHERE i.user_id = ? AND i.source = 'Milk Sales'
          ORDER BY i.income_date DESC, i.created_at DESC
          LIMIT ? OFFSET ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("iii", $user_id, $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $milk_sales[] = $row;
}
$stmt->close();

$conn->close();
?>
