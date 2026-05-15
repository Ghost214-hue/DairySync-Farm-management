<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

require_once __DIR__ . '/router/urlHelper.php';

// Check if user is logged in
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    UrlHelper::redirect('dashboard');
} else {
    UrlHelper::redirect('signin');
}