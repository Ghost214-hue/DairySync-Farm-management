<?php
require_once __DIR__ . '/../config/database.php';

$conn = getDatabase();
if (!$conn) die("DB connection failed");

// Get all feeds
$feeds = $conn->query("SELECT id, user_id, farm_id, feed_name, cost, purchase_date FROM feed_management");

while ($feed = $feeds->fetch_assoc()) {
    // Check if expense already exists for this feed
    $check = $conn->prepare("SELECT id FROM expenses WHERE feed_id = ? AND source_type = 'feed' LIMIT 1");
    $check->bind_param("i", $feed['id']);
    $check->execute();
    $result = $check->get_result();
    if ($result->num_rows === 0) {
        // Insert expense
        $insert = $conn->prepare("
            INSERT INTO expenses (user_id, farm_id, category, description, amount, expense_date, feed_id, source_type)
            VALUES (?, ?, 'Feed', ?, ?, ?, ?, 'feed')
        ");
        $insert->bind_param("iisdsi", $feed['user_id'], $feed['farm_id'], $feed['feed_name'], $feed['cost'], $feed['purchase_date'], $feed['id']);
        $insert->execute();
        $insert->close();
        echo "Added expense for feed ID {$feed['id']} ({$feed['feed_name']})<br>";
    } else {
        echo "Expense already exists for feed ID {$feed['id']}<br>";
    }
    $check->close();
}

echo "Done.";
$conn->close();
?>