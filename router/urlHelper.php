<?php
require_once __DIR__ . '/UrlEncoder.php';

class UrlHelper {
    private static $route_map = [
    // Auth
    'signup'          => 'a7k9m2x4b8498447a8d9b490bd20e599d74c2a402563ed',
    'signin'          => 'h3j5n8q1e81ea2b3a2d2bcf5ce5f54dc81c6d327031',
    'logout'          => 'q1a4z7w34d368ebdcd1356bb63c3fa9bb2794a6e787d',

    // Onboarding
    'register_farm'   => 'f3d8r1k5',

    // Main app
    'dashboard'       => 'r2t6y9u3531ae7c877d967f298ee2d9278ceb68dd73a31',
    'cows'            => 'v4b7n1m88c9e3970c5d8e3f6fa7f7dd9ed3160b37b',
    'health'          => 'w5c8p2k9f6aefe6e8cb4493b3edacf08050a6b55158',
    'milk_production' => 'e6r9t2y5682da84a5c4c0178359fe6e6dcc2d77cc623f',
    'milk_sales'      => 'x3c6m1k984dcb73b7d7f0fb8c3263e9defe3cf3e89a',
    'customers'       => 's2v5b8c48ea7cb668e70da41ef431968625a053e844a',
    'feeds'           => 'k4f7d2m977e1dc1de7264579e3c1313a79facc2b596',
    'income'          => 'b1m5q8c356a9491a5ad6154926336fdf967db8d5bb38',
    'reports'         => 'l7s0x2p4',

    // Report pages (new)
    'milk_report'     => 'd4k9m2x8dfb603d36bddeaac9b494f548b702f8899d6',
    'income_report'   => 'p7l3n6w17d8ef039d3f1d6c743709260f268562d92d4',
    'collections'     => 'm3k8n2b5a9c1d4e6f8h0j2l4p6r8t0w2y4',

    // Account
    'profile'         => 'e6r9t2y5682da84a5c4c0178359fe',
    'settings'        => 'u8i1o4p7c0ba72c64318c9b863fb4e2f41d3aad4fd4',
    'cow_profile'     => 'c6o9w2p5r8o1f3i5l7e9',
];

   public static function isActive(string $route): bool {
    $current = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
    $slug    = basename(self::url($route)); 
    return str_ends_with($current, $slug);
}
   
    public static function url(string $route, array $params = []): string {
        $encoded = self::$route_map[$route]
            ?? UrlEncoder::simpleEncode($route);

        $url = '/' . $encoded;

        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }

        return $url;
    }

    public static function asset(string $path): string {
        return '/frontend/' . ltrim($path, '/');
    }

    public static function redirect(string $route, array $params = []): void {
        header('Location: ' . self::url($route, $params));
        exit();
    }
}