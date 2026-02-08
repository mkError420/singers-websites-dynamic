<?php
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/functions.php';

// Start secure session
start_secure_session();

echo "<h1>Login Debug - Step by Step</h1>";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize_input($_POST['username']);
    $password = $_POST['password'];
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    echo "<h2>Step 1: Form Data Received</h2>";
    echo "<p><strong>Username:</strong> " . $username . "</p>";
    echo "<p><strong>Password:</strong> " . str_repeat('*', strlen($password)) . "</p>";
    echo "<p><strong>CSRF Token:</strong> " . ($csrf_token ? 'Present' : 'Missing') . "</p>";
    
    // Verify CSRF token
    echo "<h2>Step 2: CSRF Verification</h2>";
    if (verify_csrf_token($csrf_token)) {
        echo "<p style='color: green;'>✅ CSRF token valid</p>";
    } else {
        echo "<p style='color: red;'>❌ CSRF token invalid</p>";
        $error = 'Security token expired. Please try again.';
    }
    
    if (!isset($error)) {
        echo "<h2>Step 3: Database Query</h2>";
        $sql = "SELECT id, username, password FROM admin_users WHERE username = ?";
        echo "<p><strong>SQL:</strong> " . $sql . "</p>";
        echo "<p><strong>Parameter:</strong> " . $username . "</p>";
        
        $user = fetchOne($sql, [$username]);
        
        if ($user) {
            echo "<p style='color: green;'>✅ User found in database</p>";
            echo "<p><strong>User ID:</strong> " . $user['id'] . "</p>";
            echo "<p><strong>Username:</strong> " . $user['username'] . "</p>";
            
            echo "<h2>Step 4: Password Verification</h2>";
            $verify_result = password_verify($password, $user['password']);
            
            if ($verify_result) {
                echo "<p style='color: green;'>✅ Password verification successful</p>";
                
                echo "<h2>Step 5: Creating Session</h2>";
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['login_time'] = time();
                
                echo "<p style='color: green;'>✅ Session data created</p>";
                echo "<p><strong>Session ID:</strong> " . session_id() . "</p>";
                echo "<p><strong>User ID in session:</strong> " . $_SESSION['user_id'] . "</p>";
                echo "<p><strong>Username in session:</strong> " . $_SESSION['username'] . "</p>";
                
                echo "<h2>Step 6: Preparing Redirect</h2>";
                $redirect_url = APP_URL . '/admin/dashboard.php';
                echo "<p><strong>Redirect URL:</strong> " . $redirect_url . "</p>";
                echo "<p><strong>APP_URL:</strong> " . APP_URL . "</p>";
                
                echo "<h2>Step 7: Executing Redirect</h2>";
                echo "<p>Click here to manually redirect: <a href='" . $redirect_url . "'>" . $redirect_url . "</a></p>";
                
                // Actual redirect
                header("Location: " . $redirect_url);
                exit();
                
            } else {
                echo "<p style='color: red;'>❌ Password verification failed</p>";
                $error = 'Invalid username or password.';
            }
        } else {
            echo "<p style='color: red;'>❌ User not found in database</p>";
            $error = 'Invalid username or password.';
        }
    }
}

// Show login form
if (!isset($error) && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "<h2>Login Form</h2>";
}
?>

<?php if (isset($error)): ?>
    <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 20px 0;">
        <strong>Error:</strong> <?php echo $error; ?>
    </div>
<?php endif; ?>

<form method="POST" style="max-width: 400px; margin: 20px 0;">
    <div style="margin-bottom: 15px;">
        <label for="username" style="display: block; margin-bottom: 5px; font-weight: bold;">Username:</label>
        <input type="text" id="username" name="username" value="admin" required 
               style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
    </div>
    
    <div style="margin-bottom: 15px;">
        <label for="password" style="display: block; margin-bottom: 5px; font-weight: bold;">Password:</label>
        <input type="password" id="password" name="password" value="admin123" required 
               style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
    </div>
    
    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
    
    <button type="submit" style="background: #007cba; color: white; padding: 12px 24px; border: none; border-radius: 5px; cursor: pointer;">
        Login (Debug Mode)
    </button>
</form>

<hr>
<p><strong>Normal Login:</strong> <a href="login.php">Go to regular login page</a></p>
<p><strong>Test Dashboard:</strong> <a href="dashboard.php">Go directly to dashboard</a></p>
