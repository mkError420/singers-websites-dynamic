<?php
// Direct test without header includes
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/functions.php';

// Start session
start_secure_session();

// Check if logged in
if (is_logged_in()) {
    echo "<h1>✅ You ARE logged in!</h1>";
    echo "<p><strong>User ID:</strong> " . $_SESSION['user_id'] . "</p>";
    echo "<p><strong>Username:</strong> " . $_SESSION['username'] . "</p>";
    echo "<p><strong>Login Time:</strong> " . date('Y-m-d H:i:s', $_SESSION['login_time']) . "</p>";
    echo "<p><a href='dashboard.php'>Go to Dashboard</a></p>";
} else {
    echo "<h1>❌ You are NOT logged in</h1>";
    
    // Try to log in directly
    if ($_POST['username'] && $_POST['password']) {
        $username = $_POST['username'];
        $password = $_POST['password'];
        
        $sql = "SELECT id, username, password FROM admin_users WHERE username = ?";
        $user = fetchOne($sql, [$username]);
        
        if ($user && password_verify($password, $user['password'])) {
            echo "<p style='color: green;'>✅ Login successful! Creating session...</p>";
            
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['login_time'] = time();
            
            echo "<p style='color: green;'>✅ Session created!</p>";
            echo "<p><a href='test-direct.php'>Refresh to see login status</a></p>";
        } else {
            echo "<p style='color: red;'>❌ Login failed</p>";
            if ($user) {
                echo "<p>User found but password doesn't match</p>";
            } else {
                echo "<p>User not found</p>";
            }
        }
    }
    
    // Show login form
    echo "<form method='post'>";
    echo "<p>Username: <input type='text' name='username' value='admin'></p>";
    echo "<p>Password: <input type='password' name='password' value='admin123'></p>";
    echo "<input type='submit' value='Login'>";
    echo "</form>";
}

// Show all admin users
echo "<h2>All Admin Users:</h2>";
$db = new Database();
$conn = $db->getConnection();
$users = $conn->query("SELECT id, username, email FROM admin_users")->fetchAll();

foreach ($users as $user) {
    echo "<p>ID: " . $user['id'] . ", Username: " . $user['username'] . ", Email: " . $user['email'] . "</p>";
}
?>
