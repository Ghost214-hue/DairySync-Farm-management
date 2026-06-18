<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/database.php';

$conn = getDatabase();

$user_id = $_SESSION['user_id'] ?? 0;

if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    header('Location: /farm-management/h3j5n8q1');
    exit();
}

// Get farm ID
$farm_id = 0;
$farm_stmt = $conn->prepare("SELECT id FROM farms WHERE user_id = ? LIMIT 1");
$farm_stmt->bind_param("i", $user_id);
$farm_stmt->execute();
$farm_result = $farm_stmt->get_result();
if ($farm = $farm_result->fetch_assoc()) {
    $farm_id = (int)$farm['id'];
}
$farm_stmt->close();

// ------------------------------------------------------------------
// Handle POST with token validation
// ------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Token validation – prevent duplicate submission
    if (!isset($_POST['form_token']) || $_POST['form_token'] !== ($_SESSION['feed_form_token'] ?? null)) {
        $_SESSION['feed_error'] = "Invalid or duplicate submission. Please try again.";
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit();
    }
    // Clear token immediately to prevent reuse
    unset($_SESSION['feed_form_token']);

    $action = $_POST['action'] ?? '';

    if ($action === 'add_feed') {
        $feed_name     = trim($_POST['feed_name'] ?? '');
        $feed_type     = trim($_POST['feed_type'] ?? '');
        $quantity_kg   = (float)($_POST['quantity_kg'] ?? 0);
        $cost          = (float)($_POST['cost'] ?? 0);
        $supplier      = trim($_POST['supplier'] ?? '');
        $purchase_date = $_POST['purchase_date'] ?? date('Y-m-d');
        $notes         = trim($_POST['notes'] ?? '');

        if (empty($feed_name)) {
            $_SESSION['feed_error'] = "Feed name is required.";
        } else {
            // Insert feed
            $stmt = $conn->prepare("
                INSERT INTO feed_management (
                    user_id, farm_id, feed_name, feed_type,
                    quantity_kg, cost, supplier, purchase_date, notes
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->bind_param(
                "iissddsss",
                $user_id,
                $farm_id,
                $feed_name,
                $feed_type,
                $quantity_kg,
                $cost,
                $supplier,
                $purchase_date,
                $notes
            );

            if ($stmt->execute()) {
                $feed_id = $conn->insert_id;

                // --- automatically create expense ---
                $expense_stmt = $conn->prepare("
                    INSERT INTO expenses (
                        user_id, farm_id, category, description,
                        amount, expense_date, feed_id, source_type
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                ");
                $category    = 'Feed';
                $description = $feed_name;
                $amount      = $cost;
                $expense_date = $purchase_date;
                $source_type = 'feed';

                $expense_stmt->bind_param(
                    "iissdsis",
                    $user_id,
                    $farm_id,
                    $category,
                    $description,
                    $amount,
                    $expense_date,
                    $feed_id,
                    $source_type
                );

                if ($expense_stmt->execute()) {
                    $_SESSION['feed_success'] = "Feed and related expense recorded successfully.";
                } else {
                    $_SESSION['feed_error'] = "Feed saved, but expense creation failed: " . $expense_stmt->error;
                }
                $expense_stmt->close();
            } else {
                $_SESSION['feed_error'] = "Failed to save feed.";
            }
            $stmt->close();
        }

        header("Location: " . $_SERVER['REQUEST_URI']);
        exit;
    }

    if ($action === 'delete_feed') {
        $feed_id = (int)($_POST['feed_id'] ?? 0);

        // Delete linked expense
        $delete_exp = $conn->prepare("
            DELETE FROM expenses
            WHERE feed_id = ? AND source_type = 'feed' AND user_id = ?
        ");
        $delete_exp->bind_param("ii", $feed_id, $user_id);
        $delete_exp->execute();
        $delete_exp->close();

        // Delete feed
        $stmt = $conn->prepare("
            DELETE FROM feed_management
            WHERE id = ? AND user_id = ?
        ");
        $stmt->bind_param("ii", $feed_id, $user_id);

        if ($stmt->execute()) {
            $_SESSION['feed_success'] = "Feed and its linked expense deleted successfully.";
        } else {
            $_SESSION['feed_error'] = "Unable to delete feed.";
        }
        $stmt->close();

        header("Location: " . $_SERVER['REQUEST_URI']);
        exit;
    }
}

// ------------------------------------------------------------------
// Generate a fresh token for the form (stored in session)
// ------------------------------------------------------------------
if (empty($_SESSION['feed_form_token'])) {
    $_SESSION['feed_form_token'] = bin2hex(random_bytes(32));
}
$form_token = $_SESSION['feed_form_token'];

// ------------------------------------------------------------------
// Fetch all feeds
// ------------------------------------------------------------------
$feeds = [];
$stmt = $conn->prepare("
    SELECT *
    FROM feed_management
    WHERE user_id = ?
    ORDER BY purchase_date DESC, id DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $feeds[] = $row;
}
$stmt->close();

$total_feed_records = count($feeds);
$total_feed_cost = 0;
$total_feed_quantity = 0;

foreach ($feeds as $feed) {
    $total_feed_cost += (float)$feed['cost'];
    $total_feed_quantity += (float)$feed['quantity_kg'];
}

$conn->close();
?>