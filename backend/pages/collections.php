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
// Handle Record Payment
// ------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'record_payment') {
    $customer_id = (int)($_POST['customer_id'] ?? 0);
    $amount_paid = (float)($_POST['amount_paid'] ?? 0);
    $payment_date = $_POST['payment_date'] ?? date('Y-m-d');
    $payment_method = trim($_POST['payment_method'] ?? 'Cash');
    $reference_number = trim($_POST['reference_number'] ?? '');

    if ($customer_id > 0 && $amount_paid > 0) {
        // Get customer name directly
        $customer_name = 'Unknown';
        $stmt = $conn->prepare("SELECT customer_name FROM customers WHERE id = ? LIMIT 1");
        $stmt->bind_param("i", $customer_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        if ($result) $customer_name = $result['customer_name'];
        $stmt->close();
        
        // Record payment in the dedicated collections table.
        // A collection is a payment event, NOT new revenue — it settles
        // money already owed from a recorded milk sale.
        $insert = "INSERT INTO collections (user_id, farm_id, customer_id, customer_name, amount, payment_method, reference_number, payment_date)
                   VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insert);
        $stmt->bind_param("iiisdsss", $user_id, $farm_id, $customer_id, $customer_name, $amount_paid, $payment_method, $reference_number, $payment_date);
        
        if ($stmt->execute()) {
            $_SESSION['collection_success'] = "Payment of KSh " . number_format($amount_paid, 2) . " recorded for $customer_name.";
        } else {
            $_SESSION['collection_error'] = "Failed to record payment.";
        }
        $stmt->close();
    } else {
        $_SESSION['collection_error'] = "Invalid payment data.";
    }

    header("Location: " . UrlHelper::url('collections'));
    exit();
}

// ------------------------------------------------------------------
// Get all customers with their sales and payment aggregates
// ------------------------------------------------------------------
$customers_summary = [];

// First get all customers who have milk sales or collections
// CONVERT(... USING utf8mb4) makes both sides of the UNION collation-safe:
// production tables may use different collations (e.g. utf8mb3 vs utf8mb4_0900_ai_ci).
$sql = "SELECT DISTINCT customer_id, CONVERT(customer_name USING utf8mb4) AS customer_name FROM (
            SELECT customer_id, CONVERT(customer_name USING utf8mb4) AS customer_name FROM income
            WHERE user_id = ? AND source = 'Milk Sales'
            UNION
            SELECT customer_id, CONVERT(customer_name USING utf8mb4) AS customer_name FROM collections
            WHERE user_id = ?
        ) t WHERE customer_id > 0
        ORDER BY customer_name ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

// Get unique customers
$unique_customers = [];
while ($row = $result->fetch_assoc()) {
    $unique_customers[$row['customer_id']] = $row['customer_name'];
}

// Calculate totals per customer
foreach ($unique_customers as $customer_id => $customer_name) {
    $customer_id = (int)$customer_id;
    
    // Total milk sales (litres and amount)
    $sales_query = "SELECT SUM(litres) as total_litres, SUM(total_amount) as total_amount 
                    FROM income 
                    WHERE user_id = ? AND customer_id = ? AND source = 'Milk Sales'";
    $stmt = $conn->prepare($sales_query);
    $stmt->bind_param("ii", $user_id, $customer_id);
    $stmt->execute();
    $sales_result = $stmt->get_result()->fetch_assoc();
    $total_litres = (float)($sales_result['total_litres'] ?? 0);
    $total_sales = (float)($sales_result['total_amount'] ?? 0);
    $stmt->close();
    
    // Total collections (payments received)
    $collection_query = "SELECT SUM(amount) as total_paid 
                        FROM collections 
                        WHERE user_id = ? AND customer_id = ?";
    $stmt = $conn->prepare($collection_query);
    $stmt->bind_param("ii", $user_id, $customer_id);
    $stmt->execute();
    $collection_result = $stmt->get_result()->fetch_assoc();
    $total_paid = (float)($collection_result['total_paid'] ?? 0);
    $stmt->close();
    
    $balance = $total_sales - $total_paid;
    
    // Get customer price per litre
    $price_query = "SELECT price_per_litre FROM customers WHERE id = ? LIMIT 1";
    $stmt = $conn->prepare($price_query);
    $stmt->bind_param("i", $customer_id);
    $stmt->execute();
    $price_result = $stmt->get_result()->fetch_assoc();
    $price_per_litre = (float)($price_result['price_per_litre'] ?? 0);
    $stmt->close();
    
    $customers_summary[] = [
        'customer_id' => $customer_id,
        'customer_name' => $customer_name,
        'total_litres' => $total_litres,
        'total_sales' => $total_sales,
        'total_paid' => $total_paid,
        'balance' => $balance,
        'price_per_litre' => $price_per_litre,
        'status' => $balance <= 0 ? 'Paid' : (abs($balance) / max($total_sales, 0.01) > 0.5 ? 'Overdue' : 'Partial')
    ];
}

// Sort by balance descending (highest debt first)
usort($customers_summary, function($a, $b) {
    return $b['balance'] <=> $a['balance'];
});

// ------------------------------------------------------------------
// Get totals
// ------------------------------------------------------------------
$totals = [
    'total_litres' => 0,
    'total_sales' => 0,
    'total_paid' => 0,
    'total_balance' => 0
];

foreach ($customers_summary as $summary) {
    $totals['total_litres'] += $summary['total_litres'];
    $totals['total_sales'] += $summary['total_sales'];
    $totals['total_paid'] += $summary['total_paid'];
    $totals['total_balance'] += $summary['balance'];
}

// Get all active customers for the payment modal dropdown
$all_customers = [];
$stmt = $conn->prepare("SELECT id, customer_name, price_per_litre FROM customers WHERE user_id = ? AND status = 'Active' ORDER BY customer_name ASC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $all_customers[] = $row;
}
$stmt->close();

$conn->close();
?>

