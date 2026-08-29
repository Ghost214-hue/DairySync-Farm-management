<?php
session_start();
require_once __DIR__ . '/router/UrlEncoder.php';
require_once __DIR__ . '/router/urlHelper.php';

// Define base paths
define('BASE_PATH', __DIR__);
define('FRONTEND_PATH', BASE_PATH . '/frontend');
define('BACKEND_PATH', BASE_PATH . '/backend');

// Get the encoded route from URL
$request_uri = $_SERVER['REQUEST_URI'];
$encoded_route = str_replace('/router.php/', '', $request_uri);
$encoded_route = strtok($encoded_route, '?'); // Remove query parameters

// Define route mapping (encoded strings to actual paths)
$route_map = [
    // Frontend pages
    'a7k9m2x4b8498447a8d9b490bd20e599d74c2a402563ed' => 'frontend/authentication/signup.php',
    'h3j5n8q1e81ea2b3a2d2bcf5ce5f54dc81c6d327031' => 'frontend/authentication/signin.php',
    'r2t6y9u3531ae7c877d967f298ee2d9278ceb68dd73a31' => 'frontend/dashboard.php',
    'v4b7n1m88c9e3970c5d8e3f6fa7f7dd9ed3160b37b' => 'frontend/pages/cows.php',
    'w5c8p2k9f6aefe6e8cb4493b3edacf08050a6b55158' => 'frontend/pages/health.php',
    'q1a4z7w34d368ebdcd1356bb63c3fa9bb2794a6e787d' => 'backend/auth/logout.php',
    'm3k8n2b5a9c1d4e6f8h0j2l4p6r8t0w2y4' => 'frontend/pages/collections.php',
    'x3c6m1k984dcb73b7d7f0fb8c3263e9defe3cf3e89a' => 'frontend/pages/milk_sales.php',
    
    // API endpoints
    'x9z3m7k1' => 'backend/auth/signup.php',
    'y4w8n2p6' => 'backend/auth/signin.php',
    'c7v5b3n9' => 'backend/cows/create.php',
    'r8t6y4u2' => 'backend/cows/read.php',
    'p9l7k5j3' => 'backend/cows/update.php',
    'd1f2g3h4' => 'backend/cows/delete.php',
];

// Check if route exists
if (isset($route_map[$encoded_route])) {
    $target = $route_map[$encoded_route];
    $full_path = BASE_PATH . '/' . $target;
    
    if (file_exists($full_path)) {
        require_once $full_path;
    } else {
        http_response_code(404);
        echo "<h1>404 - Page Not Found</h1>";
        echo "<p>File not found: " . htmlspecialchars($target) . "</p>";
    }
} else {
    // Try to decode using UrlEncoder
    $decoded = UrlEncoder::decode($encoded_route);
    if ($decoded && file_exists(BASE_PATH . '/' . $decoded)) {
        require_once BASE_PATH . '/' . $decoded;
    } else {
        // Try simple decode
        $simple_decoded = UrlEncoder::simpleDecode($encoded_route);
        if ($simple_decoded && isset($route_map[$simple_decoded])) {
            require_once BASE_PATH . '/' . $route_map[$simple_decoded];
        } else {
            // Route not found
            http_response_code(404);
            echo "<h1>404 - Page Not Found</h1>";
            echo "<p>Invalid route: " . htmlspecialchars($encoded_route) . "</p>";
            echo "<p><a href='/h3j5n8q1e81ea2b3a2d2bcf5ce5f54dc81c6d327031'>Go to Sign In</a></p>";
        }
    }
}
?>
