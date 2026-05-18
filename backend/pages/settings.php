<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/SettingsHelper.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /farm-management/h3j5n8q1');
    exit();
}

$user_id = $_SESSION['user_id'];
$settings = new SettingsHelper($user_id);
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['milk_price'])) {
    $new_price = (float)$_POST['milk_price'];
    if ($new_price > 0) {
        if ($settings->setMilkPrice($new_price)) {
            $message = "Milk price updated successfully to KSh " . number_format($new_price, 2);
        } else {
            $error = "Failed to update milk price.";
        }
    } else {
        $error = "Please enter a valid price greater than zero.";
    }
}

$current_price = $settings->getMilkPrice();
?>