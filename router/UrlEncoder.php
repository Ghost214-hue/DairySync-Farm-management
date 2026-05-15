<?php
class UrlEncoder {
    private static $secret_key = null;
    private static $cipher_method = 'AES-256-CBC';
    private static $config_loaded = false;
    
    // Load secret key from .enc file
    private static function loadSecretKey() {
        if (self::$config_loaded) {
            return;
        }
        
        // Try multiple possible locations for the .enc file
        $possible_paths = [
            __DIR__ . '/../config/secret.enc',
            __DIR__ . '/secret.enc',
            $_SERVER['DOCUMENT_ROOT'] . '/farm-management/config/secret.enc',
            '/opt/lampp/htdocs/farm-management/config/secret.enc'
        ];
        
        $enc_file_path = null;
        foreach ($possible_paths as $path) {
            if (file_exists($path)) {
                $enc_file_path = $path;
                break;
            }
        }
        
        if (!$enc_file_path) {
            // For development only - show helpful error
            if ($_SERVER['SERVER_NAME'] === 'localhost') {
                die('Secret key file not found. Please run setup to create config/secret.enc');
            } else {
                die('System configuration error');
            }
        }
        
        // Read and decrypt the secret key
        $encrypted_data = file_get_contents($enc_file_path);
        if (!$encrypted_data) {
            die('Unable to read secret key file');
        }
        
        // Decrypt the secret key (simple XOR for basic obfuscation)
        // For production, you'd want to use a more secure method
        $decoded = base64_decode($encrypted_data);
        if (!$decoded) {
            die('Invalid secret key format');
        }
        
        // Simple XOR decryption with a master key (stored in environment variable or excluded from git)
        $master_key = getenv('APP_MASTER_KEY') ?: 'FarmMasterKey2024!';
        $decrypted = '';
        for ($i = 0; $i < strlen($decoded); $i++) {
            $decrypted .= chr(ord($decoded[$i]) ^ ord($master_key[$i % strlen($master_key)]));
        }
        
        self::$secret_key = $decrypted;
        self::$config_loaded = true;
    }
    
    // Encode a path to a seemingly random string
    public static function encode($path) {
        self::loadSecretKey();
        
        $iv_length = openssl_cipher_iv_length(self::$cipher_method);
        $iv = openssl_random_pseudo_bytes($iv_length);
        
        $encrypted = openssl_encrypt(
            $path, 
            self::$cipher_method, 
            self::$secret_key, 
            0, 
            $iv
        );
        
        // Combine IV and encrypted data, then base64 encode
        $combined = base64_encode($iv . $encrypted);
        
        // Convert to URL-safe string
        $url_safe = str_replace(['+', '/', '='], ['-', '_', ''], $combined);
        
        // Add random prefix and suffix
        $random_prefix = substr(md5(rand()), 0, 8);
        $random_suffix = substr(md5(rand()), 0, 8);
        
        return $random_prefix . $url_safe . $random_suffix;
    }
    
    // Decode the encoded string back to the original path
    public static function decode($encoded_string) {
        self::loadSecretKey();
        
        // Remove random prefix and suffix
        if (strlen($encoded_string) < 16) return null;
        $clean_string = substr($encoded_string, 8, -8);
        
        // Convert back from URL-safe to base64
        $base64 = str_replace(['-', '_'], ['+', '/'], $clean_string);
        
        // Decode base64
        $combined = base64_decode($base64);
        if (!$combined) return null;
        
        $iv_length = openssl_cipher_iv_length(self::$cipher_method);
        $iv = substr($combined, 0, $iv_length);
        $encrypted = substr($combined, $iv_length);
        
        $decrypted = openssl_decrypt(
            $encrypted, 
            self::$cipher_method, 
            self::$secret_key, 
            0, 
            $iv
        );
        
        return $decrypted;
    }
    
    // Simple encode using mapping (doesn't require secret key)
    public static function simpleEncode($path) {
        $map = [
            'signup' => 'a7k9m2x4',
            'signin' => 'h3j5n8q1',
            'dashboard' => 'r2t6y9u3',
            'cows' => 'v4b7n1m8',
            'health' => 'w5c8p2k9',
            'logout' => 'q1a4z7w3',
            'profile' => 'e6r9t2y5',
            'settings' => 'u8i1o4p7',
            'api_signup' => 'x9z3m7k1',
            'api_signin' => 'y4w8n2p6',
            'api_logout' => 'q1a4z7w3'
        ];
        
        return isset($map[$path]) ? $map[$path] : substr(md5($path), 0, 12);
    }
    
    public static function simpleDecode($encoded) {
        $reverse_map = [
            'a7k9m2x4' => 'signup',
            'h3j5n8q1' => 'signin',
            'r2t6y9u3' => 'dashboard',
            'v4b7n1m8' => 'cows',
            'w5c8p2k9' => 'health',
            'q1a4z7w3' => 'logout',
            'e6r9t2y5' => 'profile',
            'u8i1o4p7' => 'settings',
            'x9z3m7k1' => 'api_signup',
            'y4w8n2p6' => 'api_signin'
        ];
        
        return isset($reverse_map[$encoded]) ? $reverse_map[$encoded] : null;
    }
}
?>