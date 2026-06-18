<?php
session_start();
require_once __DIR__ . '/UrlEncoder.php';

define('BASE_PATH', dirname(__DIR__));

// Clean the request URI
$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);
// Remove base folder prefix and any router.php trail
$path = preg_replace('#^/farm-management(/router/router\.php)?/?#', '', $path);
$encoded_route = trim($path, '/');

// Static route map (fast lookup, no crypto)
$static_map = [
    'a7k9m2x4' => 'frontend/authentication/signup.php',
    'h3j5n8q1' => 'frontend/authentication/signin.php',
    'r2t6y9u3' => 'frontend/dashboard.php',
    'v4b7n1m8' => 'frontend/pages/cows.php',
    'w5c8p2k9' => 'frontend/pages/health.php',
    'q1a4z7w3' => 'backend/auth/logout.php',
    'e6r9t2y5' => 'frontend/pages/milk_production.php',
    'x3c6m1k9' => 'frontend/pages/milk_sales.php',
    'k4f7d2m9' => 'frontend/pages/feeds.php',
    'b1m5q8c3' => 'frontend/pages/income.php',
    'u8i1o4p7' => 'frontend/pages/settings.php',
 
    'd4k9m2x8' => 'frontend/pages/milk_report.php',
    'p7l3n6w1' => 'frontend/pages/income_report.php',
    's2v5b8c4' => 'frontend/pages/customers.php',
];

// 1. Try static map first
if (isset($static_map[$encoded_route])) {
    $target = BASE_PATH . '/' . $static_map[$encoded_route];
    if (file_exists($target)) {
        require $target;
        exit;
    }
}

// 2. Fallback: decode dynamic AES routes (for future extension)
$decoded = UrlEncoder::decode($encoded_route);
if ($decoded && file_exists(BASE_PATH . '/' . $decoded)) {
    require BASE_PATH . '/' . $decoded;
    exit;
}

// 3. If we have a decoded path but file is missing, treat as 404
if ($decoded && !file_exists(BASE_PATH . '/' . $decoded)) {
    http_response_code(404);
    die("404 - Page not found (missing file: " . htmlspecialchars($decoded) . ")");
}

// 4. Final 404 - unknown route
http_response_code(404);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found</title>
    <link href="/farm-management/frontend/css/output.css" rel="stylesheet">
    <style>
        body { font-family: system-ui, -apple-system, sans-serif; background: #f4f7f2; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; }
        .error-card { background: white; border-radius: 2rem; padding: 3rem; text-align: center; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05); max-width: 500px; }
        h1 { font-size: 6rem; font-weight: 800; color: #166534; margin: 0; }
        p { color: #475569; margin: 1rem 0; }
        a { background: #166534; color: white; padding: 0.75rem 1.5rem; border-radius: 2rem; text-decoration: none; display: inline-block; margin-top: 1rem; }
    </style>
</head>
<body>
<div class="error-card">
    <h1>404</h1>
    <p>Oops! The page you're looking for doesn't exist.</p>
    <a href="/farm-management/r2t6y9u3">Go to Dashboard</a>
</div>
</body>
</html>
<?php
exit;