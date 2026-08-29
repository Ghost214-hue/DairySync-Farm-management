<?php
/**
 * Rate Limiter Middleware
 * Prevents brute force attacks on authentication endpoints
 */

class RateLimiter {
    private static $redis = null;
    private static $file_based = true; // Fallback to file-based if Redis unavailable
    
    /**
     * Check if IP is rate limited
     */
    public static function check(string $action, int $max_attempts = 5, int $window_seconds = 900): bool {
        $ip = self::getClientIP();
        $key = "rate_limit_{$action}_{$ip}";
        
        // Try Redis first
        if (extension_loaded('redis') && self::$redis !== null) {
            return self::checkRedis($key, $max_attempts, $window_seconds);
        }
        
        // Fallback to file-based
        return self::checkFile($key, $max_attempts, $window_seconds);
    }
    
    /**
     * Record failed attempt
     */
    public static function recordAttempt(string $action): void {
        $ip = self::getClientIP();
        $key = "rate_limit_{$action}_{$ip}";
        
        if (extension_loaded('redis') && self::$redis !== null) {
            self::recordRedisAttempt($key);
        } else {
            self::recordFileAttempt($key);
        }
    }
    
    /**
     * Clear attempts (on successful login)
     */
    public static function clearAttempts(string $action): void {
        $ip = self::getClientIP();
        $key = "rate_limit_{$action}_{$ip}";
        
        if (extension_loaded('redis') && self::$redis !== null) {
            self::$redis->delete($key);
        } else {
            $file = sys_get_temp_dir() . '/' . md5($key) . '.cache';
            if (file_exists($file)) {
                unlink($file);
            }
        }
    }
    
    /**
     * Get client IP address
     */
    private static function getClientIP(): string {
        $headers = [
            'HTTP_CF_CONNECTING_IP', // Cloudflare
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_REAL_IP',
            'REMOTE_ADDR'
        ];
        
        foreach ($headers as $header) {
            if (!empty($_SERVER[$header])) {
                $ip = $_SERVER[$header];
                // Handle X-Forwarded-For (can be comma-separated)
                if (strpos($ip, ',') !== false) {
                    $ips = explode(',', $ip);
                    $ip = trim($ips[0]);
                }
                return preg_replace('/[^0-9a-fA-F:.]/', '', $ip);
            }
        }
        
        return 'unknown';
    }
    
    /**
     * Redis-based rate limiting
     */
    private static function checkRedis(string $key, int $max_attempts, int $window_seconds): bool {
        $attempts = self::$redis->get($key);
        
        if ($attempts === false) {
            self::$redis->setex($key, $window_seconds, 0);
            return true;
        }
        
        if ((int)$attempts >= $max_attempts) {
            return false;
        }
        
        return true;
    }
    
    private static function recordRedisAttempt(string $key): void {
        self::$redis->incr($key);
    }
    
    /**
     * File-based rate limiting
     */
    private static function checkFile(string $key, int $max_attempts, int $window_seconds): bool {
        $file = sys_get_temp_dir() . '/' . md5($key) . '.cache';
        $now = time();
        
        if (!file_exists($file)) {
            file_put_contents($file, json_encode(['count' => 1, 'first_attempt' => $now]), LOCK_EX);
            return true;
        }
        
        $data = json_decode(file_get_contents($file), true);
        
        if ($data['first_attempt'] < ($now - $window_seconds)) {
            // Window expired, reset
            file_put_contents($file, json_encode(['count' => 1, 'first_attempt' => $now]), LOCK_EX);
            return true;
        }
        
        if ($data['count'] >= $max_attempts) {
            return false;
        }
        
        // Increment count
        $data['count']++;
        file_put_contents($file, json_encode($data), LOCK_EX);
        
        return true;
    }
    
    private static function recordFileAttempt(string $key): void {
        $file = sys_get_temp_dir() . '/' . md5($key) . '.cache';
        $now = time();
        
        if (!file_exists($file)) {
            file_put_contents($file, json_encode(['count' => 1, 'first_attempt' => $now]), LOCK_EX);
            return;
        }
        
        $data = json_decode(file_get_contents($file), true);
        $data['count']++;
        file_put_contents($file, json_encode($data), LOCK_EX);
    }
    
    /**
     * Initialize Redis connection (optional)
     */
    public static function initRedis(string $host = '127.0.0.1', int $port = 6379, string $password = ''): void {
        if (!extension_loaded('redis')) {
            return;
        }
        
        try {
            self::$redis = new Redis();
            self::$redis->connect($host, $port);
            if ($password) {
                self::$redis->auth($password);
            }
            self::$file_based = false;
        } catch (Exception $e) {
            self::$redis = null;
            self::$file_based = true;
        }
    }
}