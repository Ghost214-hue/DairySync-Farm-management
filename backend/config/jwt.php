<?php
/**
 * Lightweight JWT Implementation for DairySync
 * No external dependencies required
 */

class JWT {
    private static $secret_key;
    private static $algorithm = 'HS256';
    
    /**
     * Initialize JWT with secret key from environment
     */
    private static function init(): void {
        if (!defined('JWT_SECRET_KEY')) {
            $secret = getenv('JWT_SECRET') ?: '';
            if (!$secret && file_exists(__DIR__ . '/../config/secret.enc')) {
                $secret = @file_get_contents(__DIR__ . '/../config/secret.enc');
            }
            // Fallback to a default if nothing is available (not recommended for production)
            if (!$secret) {
                $secret = 'DairySync-Fallback-Secret-Change-Me';
            }
            define('JWT_SECRET_KEY', $secret);
        }
        self::$secret_key = JWT_SECRET_KEY;
    }
    
    /**
     * Base64 URL encode
     */
    private static function base64url_encode(string $data): string {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
    
    /**
     * Base64 URL decode
     */
    private static function base64url_decode(string $data): string {
        return base64_decode(strtr($data, '-_', '+/') . str_repeat('=', 3 - (strlen($data) % 4)));
    }
    
    /**
     * Create JWT token
     */
    public static function encode(array $payload, int $expiry = 86400): string {
        self::init();
        
        $header = [
            'typ' => 'JWT',
            'alg' => self::$algorithm
        ];
        
        $payload['iat'] = time();
        $payload['exp'] = time() + $expiry;
        
        $header_encoded = self::base64url_encode(json_encode($header));
        $payload_encoded = self::base64url_encode(json_encode($payload));
        
        $signature = hash_hmac('sha256', $header_encoded . '.' . $payload_encoded, self::$secret_key, true);
        $signature_encoded = self::base64url_encode($signature);
        
        return $header_encoded . '.' . $payload_encoded . '.' . $signature_encoded;
    }
    
    /**
     * Decode JWT token
     */
    public static function decode(string $token): ?array {
        self::init();
        
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return null;
        }
        
        list($header_encoded, $payload_encoded, $signature_encoded) = $parts;
        
        // Verify signature
        $expected_signature = hash_hmac('sha256', $header_encoded . '.' . $payload_encoded, self::$secret_key, true);
        $expected_signature_encoded = self::base64url_encode($expected_signature);
        
        if (!hash_equals($signature_encoded, $expected_signature_encoded)) {
            return null;
        }
        
        $payload = json_decode(self::base64url_decode($payload_encoded), true);
        
        // Check expiry
        if (isset($payload['exp']) && $payload['exp'] < time()) {
            return null;
        }
        
        return $payload;
    }
    
    /**
     * Generate token pair (access + refresh)
     */
    public static function generateTokenPair(int $user_id, string $username): array {
        return [
            'access_token' => self::encode([
                'user_id' => $user_id,
                'username' => $username,
                'type' => 'access'
            ], 3600), // 1 hour
            'refresh_token' => self::encode([
                'user_id' => $user_id,
                'username' => $username,
                'type' => 'refresh'
            ], 604800), // 7 days
            'expires_in' => 3600
        ];
    }
    
    /**
     * Verify access token
     */
    public static function verifyAccessToken(string $token): ?array {
        $payload = self::decode($token);
        if (!$payload || $payload['type'] !== 'access') {
            return null;
        }
        return $payload;
    }
    
    /**
     * Verify refresh token
     */
    public static function verifyRefreshToken(string $token): ?array {
        $payload = self::decode($token);
        if (!$payload || $payload['type'] !== 'refresh') {
            return null;
        }
        return $payload;
    }
}