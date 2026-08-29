<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Generate CSRF token if not exists
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Initialize variables
$message = '';
$message_type = '';

// Only process POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Include required files
    require_once __DIR__ . '/../config/database.php';
    require_once __DIR__ . '/../middleware/RateLimiter.php';
    require_once __DIR__ . '/../middleware/Auth.php';
    
    // Get database connection
    $conn = getDatabase();
    
    // Rate limiting check
    if (!RateLimiter::check('signin', 5, 900)) {
        $message = 'Too many login attempts. Please try again in 15 minutes.';
        $message_type = 'error';
    } else {
        // Get and sanitize input data
        $login = sanitizeInput($_POST['login'] ?? '');
        $password = $_POST['password'] ?? '';
        
        // Validate required fields
        if (empty($login) || empty($password)) {
            $message = 'Please fill in all fields';
            $message_type = 'error';
        } else {
            // Check if login is email or username
            $query = "SELECT id, username, email, password FROM users WHERE username = ? OR email = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ss", $login, $login);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 0) {
                $message = 'Invalid username/email or password';
                $message_type = 'error';
            } else {
                $user = $result->fetch_assoc();
                
                // Verify password
                if (password_verify($password, $user['password'])) {
                    // Regenerate session ID to prevent session fixation
                    Auth::regenerateSession();
                    
                    // Set session variables
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['login_time'] = time();
                    $_SESSION['logged_in'] = true;

                    // Clear POST data
                    $_POST = array();

                    // Generate JWT tokens
                    $tokens = Auth::generateTokens($user['id'], $user['username']);
                    $_SESSION['access_token'] = $tokens['access_token'];
                    $_SESSION['refresh_token'] = $tokens['refresh_token'];

                    // Clear rate limiting attempts
                    RateLimiter::clearAttempts('signin');

                    // Redirect to dashboard
                    require_once __DIR__ . '/../../router/urlHelper.php';
                    UrlHelper::redirect('dashboard');
                    exit();
                } else {
                    $message = 'Invalid username/email or password';
                    $message_type = 'error';
                    // Record failed attempt
                    RateLimiter::recordAttempt('signin');
                }
            }
            $stmt->close();
        }
    }
    
    $conn->close();
}
?>