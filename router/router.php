<?php
// /farm-management/router/router.php

session_start();
require_once __DIR__ . '/UrlEncoder.php';

define('BASE_PATH', dirname(__DIR__)); // /farm-management/

// Extract encoded segment from URL
// Works whether accessed via router.php/code OR clean /farm-management/code
$request_uri = $_SERVER['REQUEST_URI'];

// Strip base path and router.php references
$path = parse_url($request_uri, PHP_URL_PATH);
$path = preg_replace('#^/farm-management(/router/router\.php)?/?#', '', $path);
$encoded_route = trim($path, '/');

// ── Static route map ─────────────────────────────────────────────────────────
// Maps encoded token → relative path from BASE_PATH
$route_map = [
    'a7k9m2x4' => 'frontend/authentication/signup.php',
    'h3j5n8q1' => 'frontend/authentication/signin.php',
    'r2t6y9u3' => 'frontend/dashboard.php',
    'v4b7n1m8' => 'frontend/cows/index.php',
    'w5c8p2k9' => 'frontend/health/index.php',
    'q1a4z7w3' => 'backend/auth/logout.php',
    'e6r9t2y5' => 'frontend/profile.php',
    'u8i1o4p7' => 'frontend/settings.php',
    'x9z3m7k1' => 'backend/auth/signup.php',
    'y4w8n2p6' => 'backend/auth/signin.php',
];

// ── Auto-discovery fallback ───────────────────────────────────────────────────
// If encoded_route not in static map, try AES decode (UrlEncoder::encode())
if (!isset($route_map[$encoded_route])) {
    $decoded = UrlEncoder::decode($encoded_route);
    if ($decoded && file_exists(BASE_PATH . '/' . $decoded)) {
        $route_map[$encoded_route] = $decoded; // cache it for this request
    }
}

// ── Dispatch ─────────────────────────────────────────────────────────────────
if (isset($route_map[$encoded_route])) {
    $target = BASE_PATH . '/' . $route_map[$encoded_route];
    if (file_exists($target)) {
        require_once $target;
        exit();
    }
}

// 404
http_response_code(404);
echo "<h1>404 - Page Not Found</h1>";