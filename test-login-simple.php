<?php
// Simple login test without any extra checks
require_once __DIR__ . '/includes/database.php';

echo "<h1>Simple Login Test</h1>";

// Start session manually
session_start();

// Direct database test
try {
    $db = new Database();
    $conn = $db->getConnection();
    
    echo "<h2>Step 1: Check Admin User</h2>";
    $sql = "SELECT * FROM admin_users WHERE username = 'admin'";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $user = $stmt->fetch();
    
    if ($user) {
        echo "<p style='color: green;'>✅ Found admin user</p>";
        echo "<p><strong>ID:</strong> " . $user['id'] . "</p>";
        echo "<p><strong>Username:</strong> " . $user['username'] . "</p>";
        echo "<p><strong>Password Hash:</strong> " . substr($user['password'], 0, 20) . "...</p>";
        
        echo "<h2>Step 2: Test Password</h2>";
        $test_password = 'admin123';
        $verify = password_verify($test_password, $user['password']);
        
        if ($verify) {
            echo "<p style='color: green;'>✅ Password 'admin123' matches hash</p>";
            
            echo "<h2>Step 3: Create Session</h2>";
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['login_time'] = time();
            
            echo "<p style='color: green;'>✅ Session created</p>";
            echo "<p><strong>Session ID:</strong> " . session_id() . "</p>";
            echo "<p><strong>User ID in session:</strong> " . $_SESSION['user_id'] . "</p>";
            echo "<p><strong>Username in session:</strong> " . $_SESSION['username'] . "</p>";
            
            echo "<h2>Step 4: Test Redirect</h2>";
            echo "<p><a href='admin/dashboard.php' style='background: #007cba; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Go to Dashboard</a></p>";
            
        } else {
            echo "<p style='color: red;'>❌ Password 'admin123' does NOT match hash</p>";
            
            // Let's create a new hash and update
            echo "<h2>Step 3: Fix Password</h2>";
            $new_hash = password_hash('admin123', PASSWORD_DEFAULT);
            echo "<p><strong>New hash for 'admin123':</strong> " . $new_hash . "</p>";
            
            $update_sql = "UPDATE admin_users SET password = ? WHERE username = 'admin'";
            $stmt = $conn->prepare($update_sql);
            $result = $stmt->execute([$new_hash]);
            
            if ($result) {
                echo "<p style='color: green;'>✅ Password updated in database</p>";
                echo "<p><strong>Now try:</strong> <a href='admin/login.php'>Admin Login</a></p>";
            } else {
                echo "<p style='color: red;'>❌ Failed to update password</p>";
            }
        }
        
    } else {
        echo "<p style='color: red;'>❌ No admin user found</p>";
        
        // Create admin user
        echo "<h2>Step 2: Create Admin User</h2>";
        $password = 'admin123';
        $hash = password_hash($password, PASSWORD_DEFAULT);
        
        $insert_sql = "INSERT INTO admin_users (username, password, email) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($insert_sql);
        $result = $stmt->execute(['admin', $hash, 'admin@singerwebsite.com']);
        
        if ($result) {
            echo "<p style='color: green;'>✅ Admin user created</p>";
            echo "<p><strong>Now try:</strong> <a href='admin/login.php'>Admin Login</a></p>";
        } else {
            echo "<p style='color: red;'>❌ Failed to create admin user</p>";
        }
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Database error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<h3>Manual Login Test</h3>";
echo "<form method='post' action='admin/login.php'>";
echo "<p>Username: <input type='text' name='username' value='admin' readonly></p>";
echo "<p>Password: <input type='password' name='password' value='admin123' readonly></p>";
echo "<input type='hidden' name='csrf_token' value='" . bin2hex(random_bytes(32)) . "'>";
echo "<input type='submit' value='Test Login'>";
echo "</form>";

echo "<hr>";
echo "<p><strong>⚠️ Delete this file after testing!</strong></p>";
?>
