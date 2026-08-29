<?php
// Global authentication protection
require_once __DIR__ . '/../middleware/Protector.php';

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../../router/urlHelper.php';

$conn = getDatabase();

$user_id = (int)$_SESSION['user_id'];

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
// Generate a fresh token for forms
// ------------------------------------------------------------------
if (empty($_SESSION['milk_form_token'])) {
    $_SESSION['milk_form_token'] = bin2hex(random_bytes(32));
}
$form_token = $_SESSION['milk_form_token'];

// ------------------------------------------------------------------
// Helpers
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

function getCustomerPrice($conn, int $customer_id): float {
    $query = "SELECT price_per_litre FROM customers WHERE id = ? LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $customer_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return (float)($result['price_per_litre'] ?? 0);
}

function getCustomerName($conn, int $customer_id): string {
    $query = "SELECT customer_name FROM customers WHERE id = ? LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $customer_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $result['customer_name'] ?? 'Unknown';
}

// ------------------------------------------------------------------
// Handle POST with token validation
// ------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate token
    if (!isset($_POST['form_token']) || $_POST['form_token'] !== ($_SESSION['milk_form_token'] ?? null)) {
        $_SESSION['milk_error'] = "Invalid or duplicate submission. Please try again.";
        header("Location: " . UrlHelper::url('milk_sales'));
        exit();
    }
    // Clear token immediately
    unset($_SESSION['milk_form_token']);

    $action = $_POST['action'] ?? '';

    if ($action === 'add_sale') {
        $sale_date       = $_POST['sale_date'] ?? date('Y-m-d');
        $litres_sold     = (float)($_POST['litres_sold'] ?? 0);
        $customer_id     = (int)($_POST['customer_id'] ?? 0);

        $daily_total = getDailyProduction($conn, $user_id, $sale_date);
        $today_sales = getDailyMilkSalesTotal($conn, $user_id, $sale_date);
        $available_before_sale = max(0, $daily_total - $today_sales);

        if ($litres_sold <= 0 || $customer_id <= 0) {
            $_SESSION['milk_error'] = "Please fill in all required fields.";
            header("Location: " . UrlHelper::url('milk_sales'));
            exit();
        }

        if ($litres_sold > $available_before_sale) {
            $_SESSION['milk_error'] = "Cannot sell more than " . number_format($available_before_sale, 1) . "L available on " . date('M j, Y', strtotime($sale_date)) . ".";
            header("Location: " . UrlHelper::url('milk_sales'));
            exit();
        }

        $customer_name = getCustomerName($conn, $customer_id);
        $milk_price = getCustomerPrice($conn, $customer_id);

        $total_amount = $litres_sold * $milk_price;
        $source = 'Milk Sales';
        $nrm_value = max(0, $available_before_sale - $litres_sold);

        $insert = "INSERT INTO income (user_id, farm_id, customer_id, source, customer_name, litres, rate_per_litre, total_amount, nrm_value, income_date, created_at)
                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($insert);
        $stmt->bind_param("iiissdddds", $user_id, $farm_id, $customer_id, $source, $customer_name, $litres_sold, $milk_price, $total_amount, $nrm_value, $sale_date);
        
        if ($stmt->execute()) {
            recalculateDailyMilkSalesNrm($conn, $user_id, $sale_date);
            $_SESSION['milk_success'] = "Milk sale to $customer_name recorded successfully (KSh " . number_format($total_amount, 2) . ")";
        } else {
            $_SESSION['milk_error'] = "Failed to record milk sale.";
        }
        $stmt->close();

        header("Location: " . UrlHelper::url('milk_sales'));
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

        header("Location: " . UrlHelper::url('milk_sales'));
        exit();
    }
}

// ------------------------------------------------------------------
// Get TODAY's data
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

$remaining_milk = $today_milk - $today_sales;

// ------------------------------------------------------------------
// Get active customers
// ------------------------------------------------------------------
$customers = [];
$customer_query = "SELECT id, customer_name, price_per_litre FROM customers 
                   WHERE user_id = ? AND status = 'Active' 
                   ORDER BY customer_name ASC";
$stmt = $conn->prepare($customer_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $customers[] = $row;
}
$stmt->close();

// ------------------------------------------------------------------
// Pagination
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