<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/database.php';

$conn = getDatabase();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    header('Location: /h3j5n8q1e81ea2b3a2d2bcf5ce5');
    exit();
}

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
// Handle add/update/delete customer
// ------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add_customer') {
        $customer_name    = trim($_POST['customer_name'] ?? '');
        $price_per_litre  = (float)($_POST['price_per_litre'] ?? 0);
        $contact_info     = trim($_POST['contact_info'] ?? '');

        // Validate input
        if (empty($customer_name) || $price_per_litre <= 0) {
            $_SESSION['customer_error'] = "Please provide customer name and valid price.";
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }

        // Check if customer already exists
        $check_query = "SELECT id FROM customers WHERE user_id = ? AND customer_name = ? LIMIT 1";
        $stmt = $conn->prepare($check_query);
        $stmt->bind_param("is", $user_id, $customer_name);
        $stmt->execute();
        if ($stmt->get_result()->fetch_assoc()) {
            $_SESSION['customer_error'] = "Customer '$customer_name' already exists.";
            $stmt->close();
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }
        $stmt->close();

        // Insert customer
        $insert = "INSERT INTO customers (user_id, farm_id, customer_name, price_per_litre, contact_info, status, created_at)
                   VALUES (?, ?, ?, ?, ?, 'Active', NOW())";
        $stmt = $conn->prepare($insert);
        $stmt->bind_param("iisds", $user_id, $farm_id, $customer_name, $price_per_litre, $contact_info);
        
        if ($stmt->execute()) {
            $_SESSION['customer_success'] = "Customer '$customer_name' added successfully (KSh $price_per_litre/L).";
        } else {
            $_SESSION['customer_error'] = "Failed to add customer.";
        }
        $stmt->close();

        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    if ($action === 'update_customer') {
        $customer_id      = (int)($_POST['customer_id'] ?? 0);
        $price_per_litre  = (float)($_POST['price_per_litre'] ?? 0);
        $contact_info     = trim($_POST['contact_info'] ?? '');

        if ($customer_id <= 0 || $price_per_litre <= 0) {
            $_SESSION['customer_error'] = "Invalid customer data.";
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }

        $update = "UPDATE customers SET price_per_litre = ?, contact_info = ?, updated_at = NOW() 
                   WHERE id = ? AND user_id = ?";
        $stmt = $conn->prepare($update);
        $stmt->bind_param("dsii", $price_per_litre, $contact_info, $customer_id, $user_id);
        
        if ($stmt->execute()) {
            $_SESSION['customer_success'] = "Customer updated successfully.";
        } else {
            $_SESSION['customer_error'] = "Failed to update customer.";
        }
        $stmt->close();

        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    if ($action === 'toggle_customer_status') {
        $customer_id = (int)($_POST['customer_id'] ?? 0);
        $new_status  = $_POST['new_status'] ?? 'Active';

        if ($customer_id <= 0 || !in_array($new_status, ['Active', 'Inactive'])) {
            $_SESSION['customer_error'] = "Invalid request.";
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }

        $update = "UPDATE customers SET status = ?, updated_at = NOW() WHERE id = ? AND user_id = ?";
        $stmt = $conn->prepare($update);
        $stmt->bind_param("sii", $new_status, $customer_id, $user_id);
        
        if ($stmt->execute()) {
            $status_text = $new_status === 'Active' ? 'activated' : 'deactivated';
            $_SESSION['customer_success'] = "Customer $status_text successfully.";
        } else {
            $_SESSION['customer_error'] = "Failed to update customer status.";
        }
        $stmt->close();

        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    if ($action === 'delete_customer') {
        $customer_id = (int)($_POST['customer_id'] ?? 0);

        if ($customer_id <= 0) {
            $_SESSION['customer_error'] = "Invalid customer.";
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }

        // Check if customer has sales records
        $sales_check = "SELECT COUNT(*) as count FROM income WHERE customer_id = ? LIMIT 1";
        $stmt = $conn->prepare($sales_check);
        $stmt->bind_param("i", $customer_id);
        $stmt->execute();
        $sales_count = (int)($stmt->get_result()->fetch_assoc()['count'] ?? 0);
        $stmt->close();

        if ($sales_count > 0) {
            $_SESSION['customer_error'] = "Cannot delete customer with existing sales records ($sales_count sales). Deactivate instead.";
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }

        $delete = "DELETE FROM customers WHERE id = ? AND user_id = ?";
        $stmt = $conn->prepare($delete);
        $stmt->bind_param("ii", $customer_id, $user_id);
        
        if ($stmt->execute()) {
            $_SESSION['customer_success'] = "Customer deleted successfully.";
        } else {
            $_SESSION['customer_error'] = "Failed to delete customer.";
        }
        $stmt->close();

        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

// ------------------------------------------------------------------
// Get all customers (active + inactive)
// ------------------------------------------------------------------
$customers = [];
$customer_query = "SELECT id, customer_name, price_per_litre, contact_info, status, created_at FROM customers 
                   WHERE user_id = ?
                   ORDER BY status DESC, customer_name ASC";
$stmt = $conn->prepare($customer_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $customers[] = $row;
}
$stmt->close();

// Get total active customers
$active_count = count(array_filter($customers, fn($c) => $c['status'] === 'Active'));

$conn->close();
?>