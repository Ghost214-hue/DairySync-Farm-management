<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    // Redirect to login if not authenticated
    header('Location: /farm-management/router.php/h3j5n8q1');
    exit();
}

// Include database configuration
require_once __DIR__ . '/../config/database.php';

// Get database connection
$conn = getDatabase();

// Fetch user details
$user_id = $_SESSION['user_id'];
$user_query = "SELECT id, username, email, phone, created_at FROM users WHERE id = ?";
$user_stmt = $conn->prepare($user_query);
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user_result = $user_stmt->get_result();
$user = $user_result->fetch_assoc();
$user_stmt->close();

// Fetch farm details
$farm_query = "SELECT id, farm_name, location, registration_number, created_at FROM farms WHERE user_id = ?";
$farm_stmt = $conn->prepare($farm_query);
$farm_stmt->bind_param("i", $user_id);
$farm_stmt->execute();
$farm_result = $farm_stmt->get_result();
$farm = $farm_result->fetch_assoc();
$farm_stmt->close();

// Fetch cow statistics
$total_cows = 0;
try {
    $cows_query = "SELECT COUNT(*) as total_cows FROM cows WHERE user_id = ?";
    $cows_stmt = $conn->prepare($cows_query);
    if ($cows_stmt) {
        $cows_stmt->bind_param("i", $user_id);
        $cows_stmt->execute();
        $cows_result = $cows_stmt->get_result();
        $cows_data = $cows_result->fetch_assoc();
        $total_cows = $cows_data['total_cows'] ?? 0;
        $cows_stmt->close();
    }
} catch (Exception $e) {
    // Table might not exist yet, set default value
    $total_cows = 0;
}

$conn->close();
?>
