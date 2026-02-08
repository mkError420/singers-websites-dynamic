<?php
// Complete admin reset
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/database.php';

echo "<h1>Complete Admin Reset</h1>";

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    // Delete existing admin users
    $delete_sql = "DELETE FROM admin_users WHERE username = 'admin'";
    $conn->exec($delete_sql);
    echo "<p style='color: orange;'>⚠️ Deleted existing admin user</p>";
    
    // Create new admin user with fresh hash
    $username = 'admin';
    $password = 'admin123';
    $email = 'admin@singerwebsite.com';
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    $insert_sql = "INSERT INTO admin_users (username, password, email) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($insert_sql);
    $result = $stmt->execute([$username, $hashed_password, $email]);
    
    if ($result) {
        echo "<p style='color: green;'>✅ New admin user created successfully!</p>";
        echo "<h3>Login Credentials:</h3>";
        echo "<p><strong>Username:</strong> admin</p>";
        echo "<p><strong>Password:</strong> admin123</p>";
        
        // Test the new credentials
        $test_sql = "SELECT * FROM admin_users WHERE username = ?";
        $stmt = $conn->prepare($test_sql);
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            echo "<p style='color: green;'>✅ Credentials verified and working!</p>";
        } else {
            echo "<p style='color: red;'>❌ Something went wrong with verification</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ Failed to create admin user</p>";
        echo "<p>Error: " . implode(", ", $stmt->errorInfo()) . "</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<h3>Next Steps:</h3>";
echo "<ol>";
echo "<li><strong>Immediately:</strong> <a href='admin/login.php'>Try logging in</a></li>";
echo "<li>Use: Username = <code>admin</code>, Password = <code>admin123</code></li>";
echo "<li>If successful, <strong>delete this file</strong> for security</li>";
echo "</ol>";

echo "<div style='background: #ffe6e6; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
echo "<strong>⚠️ IMPORTANT SECURITY WARNING:</strong><br>";
echo "This file creates admin access to your website. Delete it immediately after use!";
echo "</div>";
?>
