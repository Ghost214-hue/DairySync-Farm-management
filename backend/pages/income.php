<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/SettingsHelper.php';

$conn = getDatabase();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    header('Location: /h3j5n8q1e81ea2b3a2d2bcf5ce5');
    exit();
}

$user_id = (int)$_SESSION['user_id'];
$settings = new SettingsHelper($user_id);
$default_milk_price = $settings->getMilkPrice();

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
// Handle milk price update (from income page)
// ------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_milk_price') {
    $new_price = (float)($_POST['milk_price'] ?? 0);
    if ($new_price > 0) {
        $settings->setMilkPrice($new_price);
        $_SESSION['income_success'] = "Milk price updated to KSh " . number_format($new_price, 2);
    } else {
        $_SESSION['income_error'] = "Invalid price value.";
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// ------------------------------------------------------------------
// Handle Add Income (manual)
// ------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_income') {
    $source       = $_POST['source'] ?? '';
    $income_date  = $_POST['income_date'] ?? date('Y-m-d');
    $customer_name = trim($_POST['customer_name'] ?? '');

    if ($source === 'Milk Sales') {
        $litres       = (float)($_POST['litres'] ?? 0);
        $rate         = (float)($_POST['rate_per_litre'] ?? 0);
        $total_amount = $litres * $rate;
        $nrm_value    = 0; // Manual entries don't auto-calculate NRM

        $insert = "INSERT INTO income (user_id, farm_id, source, customer_name, litres, rate_per_litre, total_amount, nrm_value, income_date)
                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insert);
        $stmt->bind_param("iissdddds", $user_id, $farm_id, $source, $customer_name, $litres, $rate, $total_amount, $nrm_value, $income_date);
    } else {
        $total_amount = (float)($_POST['total_amount'] ?? 0);
        $insert = "INSERT INTO income (user_id, farm_id, source, customer_name, total_amount, income_date)
                   VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insert);
        $stmt->bind_param("iissds", $user_id, $farm_id, $source, $customer_name, $total_amount, $income_date);
    }

    if ($stmt->execute()) {
        $_SESSION['income_success'] = "Income added successfully.";
    } else {
        $_SESSION['income_error'] = "Failed to add income.";
    }
    $stmt->close();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// ------------------------------------------------------------------
// Handle Add Expense
// ------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_expense') {
    $category     = $_POST['category'] ?? '';
    $description  = $_POST['description'] ?? '';
    $amount       = (float)($_POST['amount'] ?? 0);
    $expense_date = $_POST['expense_date'] ?? date('Y-m-d');

    $insert = "INSERT INTO expenses (user_id, farm_id, category, description, amount, expense_date)
               VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insert);
    $stmt->bind_param("iissds", $user_id, $farm_id, $category, $description, $amount, $expense_date);
    if ($stmt->execute()) {
        $_SESSION['expense_success'] = "Expense added successfully.";
    } else {
        $_SESSION['expense_error'] = "Failed to add expense.";
    }
    $stmt->close();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// ------------------------------------------------------------------
// Pagination for Income (50 per page)
// ------------------------------------------------------------------
$income_limit = 50;
$income_page = isset($_GET['income_page']) ? max(1, (int)$_GET['income_page']) : 1;
$income_offset = ($income_page - 1) * $income_limit;

$total_income_query = "SELECT COUNT(*) as count FROM income WHERE user_id = ?";
$stmt = $conn->prepare($total_income_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$total_income_records = (int)($stmt->get_result()->fetch_assoc()['count'] ?? 0);
$income_total_pages = ceil($total_income_records / $income_limit);
$stmt->close();

$income_records = [];
$income_query = "SELECT * FROM income WHERE user_id = ? ORDER BY income_date DESC, id DESC LIMIT ? OFFSET ?";
$stmt = $conn->prepare($income_query);
$stmt->bind_param("iii", $user_id, $income_limit, $income_offset);
$stmt->execute();
$income_result = $stmt->get_result();
while ($row = $income_result->fetch_assoc()) {
    $income_records[] = $row;
}
$stmt->close();

// ------------------------------------------------------------------
// Expenses with pagination
// ------------------------------------------------------------------
$expense_limit = 50;
$expense_page = isset($_GET['expense_page']) ? max(1, (int)$_GET['expense_page']) : 1;
$expense_offset = ($expense_page - 1) * $expense_limit;

$total_expense_query = "SELECT COUNT(*) as count FROM expenses WHERE user_id = ?";
$stmt = $conn->prepare($total_expense_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$total_expense_records = (int)($stmt->get_result()->fetch_assoc()['count'] ?? 0);
$expense_total_pages = ceil($total_expense_records / $expense_limit);
$stmt->close();

$expenses = [];
$expense_query = "SELECT * FROM expenses WHERE user_id = ? ORDER BY expense_date DESC, id DESC LIMIT ? OFFSET ?";
$stmt = $conn->prepare($expense_query);
$stmt->bind_param("iii", $user_id, $expense_limit, $expense_offset);
$stmt->execute();
$expense_result = $stmt->get_result();
while ($row = $expense_result->fetch_assoc()) {
    $expenses[] = $row;
}
$stmt->close();

// ------------------------------------------------------------------
// Totals (for cards)
// ------------------------------------------------------------------
$total_income = 0;
$sum_income = "SELECT SUM(total_amount) as total FROM income WHERE user_id = ?";
$stmt = $conn->prepare($sum_income);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$total_income = (float)($stmt->get_result()->fetch_assoc()['total'] ?? 0);
$stmt->close();

$total_expenses = 0;
$sum_expense = "SELECT SUM(amount) as total FROM expenses WHERE user_id = ?";
$stmt = $conn->prepare($sum_expense);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$total_expenses = (float)($stmt->get_result()->fetch_assoc()['total'] ?? 0);
$stmt->close();

$net_profit = $total_income - $total_expenses;

// ------------------------------------------------------------------
// Customer collections (cash flow — stored in dedicated collections
// table; payments are settlement of existing sales, NOT new revenue)
// ------------------------------------------------------------------
$total_collected = 0;
$sum_collected = "SELECT SUM(amount) as total FROM collections WHERE user_id = ?";
$stmt = $conn->prepare($sum_collected);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$total_collected = (float)($stmt->get_result()->fetch_assoc()['total'] ?? 0);
$stmt->close();

$outstanding_receivable = $total_income - $total_collected;

// Total NRM value (calculated from production minus total milk sales per day)
$total_nrm = 0;
$nrm_query = "SELECT SUM(GREATEST(0, IFNULL(mp.total, 0) - IFNULL(ms.total, 0))) as nrm
              FROM (
                  SELECT production_date, SUM(morning_litres + evening_litres) AS total
                  FROM milk_production
                  WHERE user_id = ?
                  GROUP BY production_date
              ) mp
              LEFT JOIN (
                  SELECT income_date, SUM(litres) AS total
                  FROM income
                  WHERE user_id = ? AND source = 'Milk Sales'
                  GROUP BY income_date
              ) ms ON mp.production_date = ms.income_date";
$stmt = $conn->prepare($nrm_query);
$stmt->bind_param("ii", $user_id, $user_id);
$stmt->execute();
$total_nrm = (float)($stmt->get_result()->fetch_assoc()['nrm'] ?? 0);
$stmt->close();

$conn->close();
?>