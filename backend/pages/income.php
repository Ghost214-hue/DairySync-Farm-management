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
$default_milk_price = $settings->getMilkPrice();

// ------------------------------------------------------------------
// Handle milk price update from this page
// ------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_milk_price') {
    $new_price = (float)($_POST['milk_price'] ?? 0);
    if ($new_price > 0) {
        $settings->setMilkPrice($new_price);
        $_SESSION['income_success'] = "Milk price updated to KSh " . number_format($new_price, 2);
        $default_milk_price = $new_price; // refresh for this request
    } else {
        $_SESSION['income_error'] = "Invalid price value.";
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Get user's first farm
$farm_id = 0;
$farm_query = "SELECT id FROM farms WHERE user_id = ? LIMIT 1";
$stmt = $conn->prepare($farm_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$farm_result = $stmt->get_result();
if ($farm = $farm_result->fetch_assoc()) {
    $farm_id = (int)$farm['id'];
}
$stmt->close();

// ------------------------------------------------------------------
// Handle POST requests (Add Income / Add Expense)
// ------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add_income') {
        $source       = trim($_POST['source'] ?? '');
        $income_date  = $_POST['income_date'] ?? date('Y-m-d');
        $litres       = (float)($_POST['litres'] ?? 0);
        $rate_per_litre = (float)($_POST['rate_per_litre'] ?? 0);
        $total_amount = (float)($_POST['total_amount'] ?? 0);

        if ($source === 'Milk Sales') {
            if ($litres <= 0 || $rate_per_litre <= 0) {
                $_SESSION['income_error'] = "Litres and rate per litre are required for Milk Sales.";
                header("Location: " . $_SERVER['PHP_SELF']);
                exit;
            }
            $total_amount = $litres * $rate_per_litre;
        } else {
            if ($total_amount <= 0) {
                $_SESSION['income_error'] = "Please enter a valid total amount.";
                header("Location: " . $_SERVER['PHP_SELF']);
                exit;
            }
            $litres = 0;
            $rate_per_litre = 0;
        }

        $insert_income = "INSERT INTO income (user_id, farm_id, source, litres, rate_per_litre, total_amount, income_date)
                          VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_income);
        $stmt->bind_param("iisddds", $user_id, $farm_id, $source, $litres, $rate_per_litre, $total_amount, $income_date);
        if ($stmt->execute()) {
            $_SESSION['income_success'] = "Income added successfully.";
        } else {
            $_SESSION['income_error'] = "Failed to add income.";
        }
        $stmt->close();
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }

    if ($action === 'add_expense') {
        $category     = trim($_POST['category'] ?? '');
        $description  = trim($_POST['description'] ?? '');
        $amount       = (float)($_POST['amount'] ?? 0);
        $expense_date = $_POST['expense_date'] ?? date('Y-m-d');

        if (empty($category) || empty($description) || $amount <= 0) {
            $_SESSION['expense_error'] = "Please fill all fields correctly.";
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        }

        $insert_expense = "INSERT INTO expenses (user_id, farm_id, category, description, amount, expense_date)
                           VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_expense);
        $stmt->bind_param("iissds", $user_id, $farm_id, $category, $description, $amount, $expense_date);
        if ($stmt->execute()) {
            $_SESSION['expense_success'] = "Expense added successfully.";
        } else {
            $_SESSION['expense_error'] = "Failed to add expense.";
        }
        $stmt->close();
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}

// ------------------------------------------------------------------
// Pagination (20 per page)
// ------------------------------------------------------------------
$limit = 20;

// Income pagination
$income_page = isset($_GET['income_page']) ? max(1, (int)$_GET['income_page']) : 1;
$income_offset = ($income_page - 1) * $limit;

$income_total_query = "SELECT COUNT(*) as count FROM income WHERE user_id = ?";
$stmt = $conn->prepare($income_total_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$income_total = (int)($stmt->get_result()->fetch_assoc()['count'] ?? 0);
$stmt->close();
$income_total_pages = ceil($income_total / $limit);

$income_records = [];
$income_query = "SELECT * FROM income WHERE user_id = ? ORDER BY income_date DESC, id DESC LIMIT ? OFFSET ?";
$stmt = $conn->prepare($income_query);
$stmt->bind_param("iii", $user_id, $limit, $income_offset);
$stmt->execute();
$income_res = $stmt->get_result();
while ($row = $income_res->fetch_assoc()) {
    $income_records[] = $row;
}
$stmt->close();

// Expense pagination
$expense_page = isset($_GET['expense_page']) ? max(1, (int)$_GET['expense_page']) : 1;
$expense_offset = ($expense_page - 1) * $limit;

$expense_total_query = "SELECT COUNT(*) as count FROM expenses WHERE user_id = ?";
$stmt = $conn->prepare($expense_total_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$expense_total = (int)($stmt->get_result()->fetch_assoc()['count'] ?? 0);
$stmt->close();
$expense_total_pages = ceil($expense_total / $limit);

$expenses = [];
$expense_query = "SELECT * FROM expenses WHERE user_id = ? ORDER BY expense_date DESC, id DESC LIMIT ? OFFSET ?";
$stmt = $conn->prepare($expense_query);
$stmt->bind_param("iii", $user_id, $limit, $expense_offset);
$stmt->execute();
$expense_res = $stmt->get_result();
while ($row = $expense_res->fetch_assoc()) {
    $expenses[] = $row;
}
$stmt->close();

// ------------------------------------------------------------------
// Totals
// ------------------------------------------------------------------
$total_income_query = "SELECT SUM(total_amount) as total FROM income WHERE user_id = ?";
$stmt = $conn->prepare($total_income_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$total_income = (float)($stmt->get_result()->fetch_assoc()['total'] ?? 0);
$stmt->close();

$total_expenses_query = "SELECT SUM(amount) as total FROM expenses WHERE user_id = ?";
$stmt = $conn->prepare($total_expenses_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$total_expenses = (float)($stmt->get_result()->fetch_assoc()['total'] ?? 0);
$stmt->close();

$net_profit = $total_income - $total_expenses;

$conn->close();
?>