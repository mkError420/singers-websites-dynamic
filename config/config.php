<?php
// Configuration file for Singer Website
// Database and application settings

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'singer_website');
define('DB_USER', 'root');
define('DB_PASS', '');

// Application settings
define('APP_NAME', 'NILU AHASAN');
define('APP_URL', 'http://localhost/website-singers');
define('UPLOAD_PATH', __DIR__ . '/../uploads/');
define('MAX_FILE_SIZE', 50 * 1024 * 1024); // 50MB

// Security settings
define('HASH_ALGO', PASSWORD_DEFAULT);
define('SESSION_LIFETIME', 3600); // 1 hour

// Email settings (configure with your SMTP details)
define('SMTP_HOST', 'localhost');
define('SMTP_PORT', 587);
define('SMTP_USER', '');
define('SMTP_PASS', '');
define('FROM_EMAIL', 'noreplay@artistwebsite.com');
define('FROM_NAME', APP_NAME);

// Timezone
date_default_timezone_set('UTC');

// Error reporting (only for development)
if ($_SERVER['SERVER_NAME'] === 'localhost') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Start secure session
function start_secure_session() {
    // Check if session is already started
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }
    
    $secure = true;
    $httponly = true;
    
    // Force session to use cookies (don't send header on failure)
    ini_set('session.use_only_cookies', 1);
    
    // Get current cookies params
    $cookieParams = session_get_cookie_params();
    session_set_cookie_params(
        SESSION_LIFETIME,
        $cookieParams["path"],
        $cookieParams["domain"],
        $secure,
        $httponly
    );
    
    session_name('singer_session');
    session_start();
    session_regenerate_id(true);
}

// Prevent XSS
function xss_clean($data) {
    if ($data === null) {
        return '';
    }
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

// Sanitize input
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Generate CSRF token
function generate_csrf_token() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Verify CSRF token
function verify_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
?>
