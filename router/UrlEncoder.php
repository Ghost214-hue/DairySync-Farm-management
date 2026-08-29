<?php
// /router/UrlEncoder.php

class UrlEncoder {
    private static $cipher_method = 'AES-256-CBC';
    private static $secret_key    = null;
    private static $config_loaded = false;

    private static $decode_map = [
    // Auth
    'a7k9m2x4b8498447a8d9b490bd20e599d74c2a402563ed' => 'signup',
    'h3j5n8q1e81ea2b3a2d2bcf5ce5f54dc81c6d327031' => 'signin',
    'q1a4z7w34d368ebdcd1356bb63c3fa9bb2794a6e787d' => 'logout',

    // Onboarding
    'f3d8r1k5' => 'register_farm',

    // Main app
    'r2t6y9u3531ae7c877d967f298ee2d9278ceb68dd73a31' => 'dashboard',
    'v4b7n1m88c9e3970c5d8e3f6fa7f7dd9ed3160b37b' => 'cows',
    'w5c8p2k9f6aefe6e8cb4493b3edacf08050a6b55158' => 'health',
    'e6r9t2y5682da84a5c4c0178359fe6e6dcc2d77cc623f' => 'milk_production',
    's2v5b8c48ea7cb668e70da41ef431968625a053e844a' => 'customers',
    'k4f7d2m977e1dc1de7264579e3c1313a79facc2b596' => 'feeds', 
    'b1m5q8c356a9491a5ad6154926336fdf967db8d5bb38' => 'income',
    'l7s0x2p4' => 'reports',

    // Report pages (new)
    'd4k9m2x8dfb603d36bddeaac9b494f548b702f8899d6' => 'milk_report',
    'p7l3n6w17d8ef039d3f1d6c743709260f268562d92d4' => 'income_report',

    // Account
    'u8i1o4p7c0ba72c64318c9b863fb4e2f41d3aad4fd4' => 'settings',
    'm3k8n2b5a9c1d4e6f8h0j2l4p6r8t0w2y4' => 'collections',

    // API (legacy)
    'x9z3m7k1' => 'api_signup',
    'y4w8n2p6' => 'api_signin',
];

    private static function loadSecretKey(): void {
        if (self::$config_loaded) return;

        $possible_paths = [
            __DIR__ . '/../config/secret.enc',
            __DIR__ . '/secret.enc',
            $_SERVER['DOCUMENT_ROOT'] . '/config/secret.enc',
            '/opt/lampp/htdocs/config/secret.enc',
        ];

        $enc_file_path = null;
        foreach ($possible_paths as $path) {
            if (file_exists($path)) { $enc_file_path = $path; break; }
        }

        if (!$enc_file_path) {
            die($_SERVER['SERVER_NAME'] === 'localhost'
                ? 'Secret key file not found. Please run setup to create config/secret.enc'
                : 'System configuration error');
        }

        $encrypted_data = file_get_contents($enc_file_path);
        if (!$encrypted_data) die('Unable to read secret key file');

        $decoded = base64_decode($encrypted_data);
        if (!$decoded) die('Invalid secret key format');

        $master_key = getenv('APP_MASTER_KEY') ?: 'FarmMasterKey2024!';
        $decrypted  = '';
        for ($i = 0; $i < strlen($decoded); $i++) {
            $decrypted .= chr(ord($decoded[$i]) ^ ord($master_key[$i % strlen($master_key)]));
        }

        self::$secret_key    = $decrypted;
        self::$config_loaded = true;
    }

    // ── AES encode (for dynamic routes not in the static map) ───────────────
    public static function encode(string $path): string {
        self::loadSecretKey();

        $iv_length = openssl_cipher_iv_length(self::$cipher_method);
        $iv        = openssl_random_pseudo_bytes($iv_length);
        $encrypted = openssl_encrypt($path, self::$cipher_method, self::$secret_key, 0, $iv);

        $combined  = base64_encode($iv . $encrypted);
        $url_safe  = str_replace(['+', '/', '='], ['-', '_', ''], $combined);

        return substr(md5(rand()), 0, 8) . $url_safe . substr(md5(rand()), 0, 8);
    }

    // ── AES decode ───────────────────────────────────────────────────────────
    public static function decode(string $encoded_string): ?string {
        self::loadSecretKey();

        if (strlen($encoded_string) < 16) return null;
        $clean_string = substr($encoded_string, 8, -8);
        $base64       = str_replace(['-', '_'], ['+', '/'], $clean_string);
        $combined     = base64_decode($base64);
        if (!$combined) return null;

        $iv_length = openssl_cipher_iv_length(self::$cipher_method);
        $iv        = substr($combined, 0, $iv_length);
        $encrypted = substr($combined, $iv_length);

        return openssl_decrypt($encrypted, self::$cipher_method, self::$secret_key, 0, $iv) ?: null;
    }

    // ── Static map encode (fast, no crypto) ─────────────────────────────────
    public static function simpleEncode(string $path): string {
        $forward = array_flip(self::$decode_map);
        return $forward[$path] ?? substr(md5($path), 0, 12);
    }

    // ── Static map decode ────────────────────────────────────────────────────
    public static function simpleDecode(string $encoded): ?string {
        return self::$decode_map[$encoded] ?? null;
    }

    // ── Unified decode: try static map first, fall back to AES ──────────────
    public static function resolveRoute(string $encoded): ?string {
        return self::simpleDecode($encoded) ?? self::decode($encoded);
    }
}