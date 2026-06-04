<?php
require_once __DIR__ . '/UrlEncoder.php';

class UrlHelper {
    private static $route_map = [
    // Auth
    'signup'          => 'a7k9m2x4',
    'signin'          => 'h3j5n8q1',
    'logout'          => 'q1a4z7w3',

    // Onboarding
    'register_farm'   => 'f3d8r1k5',

    // Main app
    'dashboard'       => 'r2t6y9u3',
    'cows'            => 'v4b7n1m8',
    'health'          => 'w5c8p2k9',
    'milk_production' => 'e6r9t2y5',
    'milk_sales'      => 'x3c6m1k9',
    'feeds'           => 'k4f7d2m9',
    'income'          => 'b1m5q8c3',
    'reports'         => 'l7s0x2p4',

    // Report pages (new)
    'milk_report'     => 'd4k9m2x8',
    'income_report'   => 'p7l3n6w1',

    // Account
    'profile'         => 'e6r9t2y5',
    'settings'        => 'u8i1o4p7',
];

   public static function isActive(string $route): bool {
    $current = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
    $slug    = basename(self::url($route)); 
    return str_ends_with($current, $slug);
}
   
    public static function url(string $route, array $params = []): string {
        $encoded = self::$route_map[$route]
            ?? UrlEncoder::simpleEncode($route);

        $url = '/farm-management/' . $encoded;

        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }

        return $url;
    }

    public static function asset(string $path): string {
        return '/farm-management/frontend/' . ltrim($path, '/');
    }

    public static function redirect(string $route, array $params = []): void {
        header('Location: ' . self::url($route, $params));
        exit();
    }
}