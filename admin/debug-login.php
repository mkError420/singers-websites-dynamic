<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Debug: Starting login script...<br>";

try {
    // Start session
    echo "Debug: Starting session...<br>";
    session_start();
    echo "Debug: Session started successfully<br>";

    // Check if already logged in
    if (isset($_SESSION['user_id'])) {
        echo "Debug: User already logged in, redirecting...<br>";
        header('Location: dashboard.php');
        exit();
    }

    // Process login
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        echo "Debug: Processing POST request...<br>";
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        echo "Debug: Username: " . htmlspecialchars($username) . "<br>";
        
        if (empty($username) || empty($password)) {
            $error = 'Username and password are required.';
        } else {
            try {
                echo "Debug: Including database...<br>";
                require_once __DIR__ . '/../includes/database.php';
                echo "Debug: Database included successfully<br>";
                
                // Check user credentials
                $sql = "SELECT id, username, password FROM admin_users WHERE username = ?";
                echo "Debug: Executing query...<br>";
                $user = fetchOne($sql, [$username]);
                
                if ($user) {
                    echo "Debug: User found<br>";
                    if (password_verify($password, $user['password'])) {
                        echo "Debug: Password verified<br>";
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['username'] = $user['username'];
                        $_SESSION['login_time'] = time();
                        header('Location: dashboard.php');
                        exit();
                    } else {
                        echo "Debug: Password verification failed<br>";
                        $error = 'Invalid username or password.';
                    }
                } else {
                    echo "Debug: User not found<br>";
                    $error = 'Invalid username or password.';
                }
            } catch (Exception $e) {
                echo "Debug: Database error: " . $e->getMessage() . "<br>";
                $error = 'Database error: ' . $e->getMessage();
            }
        }
    } else {
        echo "Debug: Showing login form (GET request)<br>";
    }
} catch (Exception $e) {
    echo "Debug: Fatal error: " . $e->getMessage() . "<br>";
    echo "Debug: Stack trace: " . $e->getTraceAsString() . "<br>";
}

echo "Debug: Rendering HTML...<br>";
?>
<!DOCTYPE html>
<html>
<head>
    <title>Debug Login</title>
    <style>
        body { font-family: Arial, sans-serif; background: #1a1a2e; color: white; padding: 20px; }
        .container { max-width: 500px; margin: 0 auto; background: #2a2a3e; padding: 20px; border-radius: 10px; }
        input { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #444; background: #333; color: white; border-radius: 5px; }
        button { width: 100%; padding: 10px; background: #ff6b6b; color: white; border: none; border-radius: 5px; cursor: pointer; }
        .error { color: #ff6b6b; margin: 10px 0; }
        .debug { background: #333; padding: 10px; margin: 10px 0; border-radius: 5px; font-family: monospace; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Debug Login</h2>
        
        <?php if (isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        
        <div class="debug">
            <h3>Debug Info:</h3>
            <p>Session ID: <?php echo session_id(); ?></p>
            <p>Current Time: <?php echo date('Y-m-d H:i:s'); ?></p>
            <p>Request Method: <?php echo $_SERVER['REQUEST_METHOD']; ?></p>
            <p>Script Name: <?php echo $_SERVER['SCRIPT_NAME']; ?></p>
        </div>
    </div>
</body>
</html>
