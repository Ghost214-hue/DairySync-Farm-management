<?php
session_start();
require_once __DIR__ . '/UrlEncoder.php';

define('BASE_PATH', dirname(__DIR__));

// Clean the request URI
$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);
// Remove base folder prefix and any router.php trail
$path = preg_replace('#^/?(?:router/router\.php)?/?#', '', $path);
$encoded_route = trim($path, '/');

// Static route map (fast lookup, no crypto)
$static_map = [
    'a7k9m2x4b8498447a8d9b490bd20e599d74c2a402563ed' => 'frontend/authentication/signup.php',
    'h3j5n8q1e81ea2b3a2d2bcf5ce5f54dc81c6d327031' => 'frontend/authentication/signin.php',
    'r2t6y9u3531ae7c877d967f298ee2d9278ceb68dd73a31' => 'frontend/dashboard.php',
    'v4b7n1m88c9e3970c5d8e3f6fa7f7dd9ed3160b37b' => 'frontend/pages/cows.php',
    'w5c8p2k9f6aefe6e8cb4493b3edacf08050a6b55158' => 'frontend/pages/health.php',
    'q1a4z7w34d368ebdcd1356bb63c3fa9bb2794a6e787d' => 'backend/auth/logout.php',
    'e6r9t2y5682da84a5c4c0178359fe6e6dcc2d77cc623f' => 'frontend/pages/milk_production.php',
    'x3c6m1k984dcb73b7d7f0fb8c3263e9defe3cf3e89a' => 'frontend/pages/milk_sales.php',
    'k4f7d2m977e1dc1de7264579e3c1313a79facc2b596' => 'frontend/pages/feeds.php',
    'b1m5q8c356a9491a5ad6154926336fdf967db8d5bb38' => 'frontend/pages/income.php',
    'u8i1o4p7c0ba72c64318c9b863fb4e2f41d3aad4fd4' => 'frontend/pages/settings.php',
    'c6o9w2p5r8o1f3i5l7e9' => 'frontend/pages/cow_profile.php',

    'd4k9m2x8dfb603d36bddeaac9b494f548b702f8899d6' => 'frontend/pages/milk_report.php',
    'p7l3n6w17d8ef039d3f1d6c743709260f268562d92d4' => 'frontend/pages/income_report.php',
    's2v5b8c48ea7cb668e70da41ef431968625a053e844a' => 'frontend/pages/customers.php',
    'm3k8n2b5a9c1d4e6f8h0j2l4p6r8t0w2y4' => 'frontend/pages/collections.php',
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
    <link href="/frontend/css/output.css" rel="stylesheet">
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
    <a href="/r2t6y9u3531ae7c877d967f298ee2d9278ceb68dd73a31">Go to Dashboard</a>
</div>
</body>
</html>
<?php
exit;