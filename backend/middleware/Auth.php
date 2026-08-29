<?php
/**
 * Authentication Middleware
 * Handles both session and JWT authentication
 */

require_once __DIR__ . '/../config/jwt.php';

class Auth {
    /**
     * Authenticate request via session or JWT
     */
    public static function authenticate(): ?array {
        // Check session first (traditional web auth)
        if (self::checkSession()) {
            return $_SESSION;
        }
        
        // Check JWT token (API/SPA auth)
        $token = self::getBearerToken();
        if ($token) {
            $payload = JWT::verifyAccessToken($token);
            if ($payload) {
                return $payload;
            }
        }
        
        return null;
    }
    
    /**
     * Check if user is authenticated via session
     */
    public static function checkSession(): bool {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        return isset($_SESSION['user_id']) 
            && isset($_SESSION['username']) 
            && isset($_SESSION['logged_in']) 
            && $_SESSION['logged_in'] === true
            && (time() - ($_SESSION['login_time'] ?? 0) < 7200); // 2 hour timeout
    }
    
    /**
     * Require authentication (redirect or 401)
     */
    public static function requireAuth(): array {
        $user = self::authenticate();
        
        if (!$user) {
            if (self::isApiRequest()) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                exit();
            } else {
                header('Location: /h3j5n8q1e81ea2b3a2d2bcf5ce5f54dc81c6d327031');
                exit();
            }
        }
        
        return $user;
    }
    
    /**
     * Get JWT token from Authorization header
     */
    private static function getBearerToken(): ?string {
        $headers = getallheaders();
        
        // Check Authorization header
        if (isset($headers['Authorization'])) {
            if (preg_match('/Bearer\s+(.+)/i', $headers['Authorization'], $matches)) {
                return trim($matches[1]);
            }
        }
        
        // Check query parameter (for initial page load)
        if (isset($_GET['token'])) {
            return $_GET['token'];
        }
        
        return null;
    }
    
    /**
     * Check if request is an API request
     */
    private static function isApiRequest(): bool {
        $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
        $content_type = $_SERVER['CONTENT_TYPE'] ?? '';
        
        return strpos($accept, 'application/json') !== false
            || strpos($content_type, 'application/json') !== false
            || isset($_GET['api']);
    }
    
    /**
     * Generate JWT tokens on login
     */
    public static function generateTokens(int $user_id, string $username): array {
        return JWT::generateTokenPair($user_id, $username);
    }
    
    /**
     * Refresh access token using refresh token
     */
    public static function refreshToken(string $refresh_token): ?array {
        $payload = JWT::verifyRefreshToken($refresh_token);
        
        if (!$payload) {
            return null;
        }
        
        // Clear old attempts on successful refresh
        RateLimiter::clearAttempts('refresh_token');
        
        return JWT::generateTokenPair($payload['user_id'], $payload['username']);
    }
    
    /**
     * Regenerate session ID (prevent session fixation)
     */
    public static function regenerateSession(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_regenerate_id(true);
    }
}