<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Starting login test...<br>";

// Start session
session_start();
echo "Session started<br>";

// Check if already logged in
if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
    echo "Already logged in, redirecting...<br>";
    header('Location: dashboard.php');
    exit();
} else {
    echo "Not logged in, showing form...<br>";
}

// Process login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "Processing POST request...<br>";
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    echo "Username: " . htmlspecialchars($username) . "<br>";
    
    if (empty($username) || empty($password)) {
        $error = 'Username and password are required.';
    } else {
        try {
            echo "Including database...<br>";
            require_once __DIR__ . '/../includes/database.php';
            echo "Database included<br>";
            
            // Check user credentials
            $sql = "SELECT id, username, password FROM admin_users WHERE username = ?";
            echo "Executing query...<br>";
            $user = fetchOne($sql, [$username]);
            
            if ($user) {
                echo "User found<br>";
                if (password_verify($password, $user['password'])) {
                    echo "Password verified<br>";
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['login_time'] = time();
                    header('Location: dashboard.php');
                    exit();
                } else {
                    echo "Password verification failed<br>";
                    $error = 'Invalid username or password.';
                }
            } else {
                echo "User not found<br>";
                $error = 'Invalid username or password.';
            }
        } catch (Exception $e) {
            echo "Database error: " . $e->getMessage() . "<br>";
            $error = 'Database error: ' . $e->getMessage();
        }
    }
}

echo "Rendering HTML...<br>";
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login Test</title>
    <style>
        body { font-family: Arial, sans-serif; background: #1a1a2e; color: white; padding: 20px; }
        .container { max-width: 500px; margin: 0 auto; background: #2a2a3e; padding: 20px; border-radius: 10px; }
        input { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #444; background: #333; color: white; border-radius: 5px; }
        button { width: 100%; padding: 10px; background: #ff6b6b; color: white; border: none; border-radius: 5px; cursor: pointer; }
        .error { color: #ff6b6b; margin: 10px 0; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Login Test Page</h2>
        
        <?php if (isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        
        <p><strong>Test Info:</strong></p>
        <ul>
            <li>Session ID: <?php echo session_id(); ?></li>
            <li>Current Time: <?php echo date('Y-m-d H:i:s'); ?></li>
            <li>Request Method: <?php echo $_SERVER['REQUEST_METHOD']; ?></li>
        </ul>
    </div>
</body>
</html>
