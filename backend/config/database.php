<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Security: Disable error display in production
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Try to set error log to a valid location
$log_dir = __DIR__ . '/../../logs';
if (!is_dir($log_dir)) {
    @mkdir($log_dir, 0777, true);
}
if (is_dir($log_dir) && is_writable($log_dir)) {
    ini_set('error_log', $log_dir . '/php_errors.log');
} else {
    // Fallback to system temp directory
    ini_set('error_log', sys_get_temp_dir() . '/php_errors.log');
}

class Database {
    public $host;
    public $db_name;
    public $username;
    public $password;
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            // Create connection using MySQLi
            $this->conn = new mysqli($this->host, $this->username, $this->password, $this->db_name);
            
            // Check connection
            if ($this->conn->connect_error) {
                throw new Exception("Database connection failed");
            }
            
            // Set charset to UTF-8
            $this->conn->set_charset("utf8");
            
        } catch(Exception $exception) {
            // Log error but don't expose details
            error_log("Database connection error: " . $exception->getMessage());
            http_response_code(500);
            die("System temporarily unavailable. Please try again later.");
        }
        return $this->conn;
    }
}

// Function to get database connection
function getDatabase() {
    $database = new Database();
    $database->host = getenv('DB_HOST') ?: 'localhost';
    $database->db_name = getenv('DB_NAME') ?: 'farm_management';
    $database->username = getenv('DB_USER') ?: 'root';
    $database->password = getenv('DB_PASS') ?: '';
    return $database->getConnection();
}

// Function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['username']);
}

// Function to redirect if not logged in
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: /frontend/authentication/signin.php');
        exit();
    }
}

// Function to sanitize input
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

// Initialize database config from environment
function initDatabase() {
    $database = new Database();
    $database->host = getenv('DB_HOST') ?: 'localhost';
    $database->db_name = getenv('DB_NAME') ?: 'farm_management';
    $database->username = getenv('DB_USER') ?: 'root';
    $database->password = getenv('DB_PASS') ?: '';
    return $database;
}
?>