<?php
// /farm-management/router/UrlHelper.php
require_once __DIR__ . '/UrlEncoder.php';

class UrlHelper {

    private static $route_map = [
        'signup'    => 'a7k9m2x4',
        'signin'    => 'h3j5n8q1',
        'dashboard' => 'r2t6y9u3',
        'cows'      => 'v4b7n1m8',
        'health'    => 'w5c8p2k9',
        'logout'    => 'q1a4z7w3',
        'profile'   => 'e6r9t2y5',
        'settings'  => 'u8i1o4p7',
    ];

    /**
     * Generate a clean URL: /farm-management/<encoded>
     * No router.php visible in the URL.
     */
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