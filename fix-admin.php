<?php
// Fix admin login password
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/database.php';

echo "<h1>Fix Admin Login</h1>";

try {
    // Create proper password hash for 'admin123'
    $password = 'admin123';
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    echo "<p>Creating password hash for 'admin123':</p>";
    echo "<code style='background: #f0f0f0; padding: 10px; display: block;'>" . $hashed_password . "</code>";
    
    // Update or insert admin user
    $db = new Database();
    $conn = $db->getConnection();
    
    // Check if admin user exists
    $check_sql = "SELECT id FROM admin_users WHERE username = 'admin'";
    $stmt = $conn->prepare($check_sql);
    $stmt->execute();
    $existing = $stmt->fetch();
    
    if ($existing) {
        // Update existing admin user
        $update_sql = "UPDATE admin_users SET password = ? WHERE username = 'admin'";
        $stmt = $conn->prepare($update_sql);
        $result = $stmt->execute([$hashed_password]);
        
        if ($result) {
            echo "<p style='color: green;'>✅ Admin password updated successfully!</p>";
        } else {
            echo "<p style='color: red;'>❌ Failed to update admin password</p>";
        }
    } else {
        // Insert new admin user
        $insert_sql = "INSERT INTO admin_users (username, password, email) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($insert_sql);
        $result = $stmt->execute(['admin', $hashed_password, 'admin@singerwebsite.com']);
        
        if ($result) {
            echo "<p style='color: green;'>✅ Admin user created successfully!</p>";
        } else {
            echo "<p style='color: red;'>❌ Failed to create admin user</p>";
        }
    }
    
    // Test login
    echo "<h3>Testing Login:</h3>";
    $test_sql = "SELECT * FROM admin_users WHERE username = 'admin'";
    $stmt = $conn->prepare($test_sql);
    $stmt->execute();
    $user = $stmt->fetch();
    
    if ($user && password_verify('admin123', $user['password'])) {
        echo "<p style='color: green;'>✅ Login test successful!</p>";
        echo "<p><strong>Username:</strong> admin</p>";
        echo "<p><strong>Password:</strong> admin123</p>";
    } else {
        echo "<p style='color: red;'>❌ Login test failed</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<h3>Next Steps:</h3>";
echo "<ol>";
echo "<li><a href='admin/login.php'>Try logging in to admin panel</a></li>";
echo "<li><a href='test-connection.php'>Test database connection</a></li>";
echo "<li><a href='index.php'>Go to homepage</a></li>";
echo "</ol>";

echo "<hr>";
echo "<p><strong>Important:</strong> Delete this file after fixing the login for security!</p>";
?>
