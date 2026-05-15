<?php
// Only process if this file is included in a form submission context

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
$redirect = false;

// Only process POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Include database configuration
    require_once __DIR__ . '/../config/database.php';
    
    // Get database connection
    $conn = getDatabase();
    
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
    // Set session variables
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['login_time'] = time();
    $_SESSION['logged_in'] = true;

    // Clear POST data
    $_POST = array();

    // Redirect to dashboard
    require_once __DIR__ . '/../../router/urlHelper.php';
    UrlHelper::redirect('dashboard');
                
            } else {
                $message = 'Invalid username/email or password';
                $message_type = 'error';
            }
        }
        $stmt->close();
    }
    
    $conn->close();
}
?>