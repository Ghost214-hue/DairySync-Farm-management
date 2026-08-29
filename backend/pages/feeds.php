<?php
// Global authentication protection
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../middleware/Protector.php';

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/FarmContext.php';
require_once __DIR__ . '/../../router/urlHelper.php';

$conn = getDatabase();

$user_id = (int)$_SESSION['user_id'];

// Get user's first farm
$farm_id = FarmContext::currentFarmId() ?? 0;

// ------------------------------------------------------------------
// Generate a fresh token for forms
// ------------------------------------------------------------------
if (empty($_SESSION['feed_form_token'])) {
    $_SESSION['feed_form_token'] = bin2hex(random_bytes(32));
}
$form_token = $_SESSION['feed_form_token'];

// ------------------------------------------------------------------
// Validate form token on POST (prevents duplicate submissions)
// ------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' &&
    (!isset($_POST['form_token']) || !hash_equals($form_token, (string)$_POST['form_token'])) &&
    ($_POST['action'] ?? '') !== 'delete_feed') {
    $_SESSION['feed_error'] = "Invalid or duplicate submission. Please try again.";
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit();
}

// ------------------------------------------------------------------
// Keep expenses table in sync with feed_management records so that
// feed purchases also show up on the Income & Expenses page.
// Idempotent: only feeds without a matching expense are inserted.
// ------------------------------------------------------------------
$sync_stmt = $conn->prepare("
    INSERT INTO expenses (user_id, farm_id, category, description, amount, expense_date, feed_id, source_type)
    SELECT f.user_id, f.farm_id, 'Feed', f.feed_name, f.cost, COALESCE(f.purchase_date, CURDATE()), f.id, 'feed'
    FROM feed_management f
    LEFT JOIN expenses e ON e.feed_id = f.id AND e.source_type = 'feed'
    WHERE f.user_id = ? AND e.id IS NULL
");
$sync_stmt->bind_param("i", $user_id);
$sync_stmt->execute();
$sync_stmt->close();

// ------------------------------------------------------------------
// Handle Add Feed
// ------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add_feed') {

    $feed_name     = trim($_POST['feed_name'] ?? '');
    $feed_type     = trim($_POST['feed_type'] ?? '');
    $quantity_kg   = (float)($_POST['quantity_kg'] ?? 0);
    $cost          = (float)($_POST['cost'] ?? 0);
    $supplier      = trim($_POST['supplier'] ?? '');
    $purchase_date = $_POST['purchase_date'] ?? date('Y-m-d');
    $notes         = trim($_POST['notes'] ?? '');

    if ($feed_name === '') {
        $_SESSION['feed_error'] = "Feed name is required.";
    } else {
        $stmt = $conn->prepare("
            INSERT INTO feed_management
                (user_id, farm_id, feed_name, feed_type, quantity_kg, cost, supplier, purchase_date, notes)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("iissddsss", $user_id, $farm_id, $feed_name, $feed_type, $quantity_kg, $cost, $supplier, $purchase_date, $notes);

        if ($stmt->execute()) {
            $feed_id = $stmt->insert_id;
            $stmt->close();

            // Mirror the purchase into expenses so it appears in the finance overview
            $exp = $conn->prepare("
                INSERT INTO expenses (user_id, farm_id, category, description, amount, expense_date, feed_id, source_type)
                VALUES (?, ?, 'Feed', ?, ?, ?, ?, 'feed')
            ");
            $exp->bind_param("iisdsi", $user_id, $farm_id, $feed_name, $cost, $purchase_date, $feed_id);
            $exp->execute();
            $exp->close();

            $_SESSION['feed_success'] = "Feed recorded successfully.";
        } else {
            $_SESSION['feed_error'] = "Failed to save feed.";
        }
    }

    header("Location: " . $_SERVER['REQUEST_URI']);
    exit();
}

// ------------------------------------------------------------------
// Handle Delete Feed (also removes the mirrored expense)
// ------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete_feed') {

    $feed_id = (int)($_POST['feed_id'] ?? 0);

    if ($feed_id > 0) {
        $stmt = $conn->prepare("DELETE FROM feed_management WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $feed_id, $user_id);
        $stmt->execute();
        $stmt->close();

        $exp = $conn->prepare("DELETE FROM expenses WHERE feed_id = ? AND source_type = 'feed' AND user_id = ?");
        $exp->bind_param("ii", $feed_id, $user_id);
        $exp->execute();
        $exp->close();

        $_SESSION['feed_success'] = "Feed record deleted.";
    }

    header("Location: " . $_SERVER['REQUEST_URI']);
    exit();
}

// ------------------------------------------------------------------
// Fetch feed records
// ------------------------------------------------------------------
$feeds = [];
$feed_query = "SELECT * FROM feed_management WHERE user_id = ? ORDER BY purchase_date DESC, id DESC";
$stmt = $conn->prepare($feed_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$feed_result = $stmt->get_result();
while ($row = $feed_result->fetch_assoc()) {
    $feeds[] = $row;
}
$stmt->close();

$total_feed_records = count($feeds);

$total_feed_quantity = 0;
$total_feed_cost     = 0;
foreach ($feeds as $f) {
    $total_feed_quantity += (float)$f['quantity_kg'];
    $total_feed_cost     += (float)$f['cost'];
}

$conn->close();
?>
