<?php
// Debug login issue
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/database.php';

echo "<h1>Login Debug Tool</h1>";

// Test 1: Database Connection
echo "<h2>1. Testing Database Connection</h2>";
try {
    $db = new Database();
    $conn = $db->getConnection();
    if ($conn) {
        echo "<p style='color: green;'>✅ Database connection successful</p>";
    } else {
        echo "<p style='color: red;'>❌ Database connection failed</p>";
        exit;
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Database error: " . $e->getMessage() . "</p>";
    exit;
}

// Test 2: Check Admin User Exists
echo "<h2>2. Checking Admin User</h2>";
$sql = "SELECT * FROM admin_users WHERE username = 'admin'";
$stmt = $conn->prepare($sql);
$stmt->execute();
$admin_user = $stmt->fetch();

if ($admin_user) {
    echo "<p style='color: green;'>✅ Admin user found in database</p>";
    echo "<p><strong>ID:</strong> " . $admin_user['id'] . "</p>";
    echo "<p><strong>Username:</strong> " . $admin_user['username'] . "</p>";
    echo "<p><strong>Email:</strong> " . $admin_user['email'] . "</p>";
    echo "<p><strong>Stored Hash:</strong> " . $admin_user['password'] . "</p>";
} else {
    echo "<p style='color: red;'>❌ No admin user found</p>";
    echo "<p>Let's create one...</p>";
    
    // Create admin user
    $password = 'admin123';
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    $insert_sql = "INSERT INTO admin_users (username, password, email) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($insert_sql);
    $result = $stmt->execute(['admin', $hashed_password, 'admin@singerwebsite.com']);
    
    if ($result) {
        echo "<p style='color: green;'>✅ Admin user created</p>";
    } else {
        echo "<p style='color: red;'>❌ Failed to create admin user</p>";
    }
}

// Test 3: Password Verification
echo "<h2>3. Testing Password Verification</h2>";
$test_password = 'admin123';
$test_hash = password_hash($test_password, PASSWORD_DEFAULT);

echo "<p><strong>Test Password:</strong> " . $test_password . "</p>";
echo "<p><strong>New Hash:</strong> " . $test_hash . "</p>";

if (isset($admin_user)) {
    $verify_result = password_verify($test_password, $admin_user['password']);
    
    if ($verify_result) {
        echo "<p style='color: green;'>✅ Password verification successful</p>";
    } else {
        echo "<p style='color: red;'>❌ Password verification failed</p>";
        echo "<p>Let's update the password...</p>";
        
        // Update password
        $new_hash = password_hash($test_password, PASSWORD_DEFAULT);
        $update_sql = "UPDATE admin_users SET password = ? WHERE username = 'admin'";
        $stmt = $conn->prepare($update_sql);
        $result = $stmt->execute([$new_hash]);
        
        if ($result) {
            echo "<p style='color: green;'>✅ Password updated in database</p>";
            
            // Test again
            $updated_user = $conn->query("SELECT * FROM admin_users WHERE username = 'admin'")->fetch();
            $verify_again = password_verify($test_password, $updated_user['password']);
            
            if ($verify_again) {
                echo "<p style='color: green;'>✅ Updated password verification successful</p>";
            } else {
                echo "<p style='color: red;'>❌ Still failing after update</p>";
            }
        } else {
            echo "<p style='color: red;'>❌ Failed to update password</p>";
        }
    }
}

// Test 4: Simulate Login Process
echo "<h2>4. Simulating Login Process</h2>";
$login_sql = "SELECT id, username, password FROM admin_users WHERE username = ?";
$stmt = $conn->prepare($login_sql);
$stmt->execute(['admin']);
$login_user = $stmt->fetch();

if ($login_user && password_verify('admin123', $login_user['password'])) {
    echo "<p style='color: green;'>✅ Login simulation successful</p>";
    echo "<p><strong>User ID:</strong> " . $login_user['id'] . "</p>";
    echo "<p><strong>Username:</strong> " . $login_user['username'] . "</p>";
} else {
    echo "<p style='color: red;'>❌ Login simulation failed</p>";
}

// Test 5: Check Configuration
echo "<h2>5. Configuration Check</h2>";
echo "<p><strong>DB_HOST:</strong> " . DB_HOST . "</p>";
echo "<p><strong>DB_NAME:</strong> " . DB_NAME . "</p>";
echo "<p><strong>DB_USER:</strong> " . DB_USER . "</p>";
echo "<p><strong>DB_PASS:</strong> " . (empty(DB_PASS) ? '(empty)' : '***') . "</p>";

// Test 6: Current Admin Users
echo "<h2>6. All Admin Users in Database</h2>";
$all_admins = $conn->query("SELECT id, username, email, created_at FROM admin_users")->fetchAll();

if (count($all_admins) > 0) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ID</th><th>Username</th><th>Email</th><th>Created</th></tr>";
    foreach ($all_admins as $admin) {
        echo "<tr>";
        echo "<td>" . $admin['id'] . "</td>";
        echo "<td>" . $admin['username'] . "</td>";
        echo "<td>" . $admin['email'] . "</td>";
        echo "<td>" . $admin['created_at'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: orange;'>⚠️ No admin users found</p>";
}

echo "<hr>";
echo "<h3>Recommended Actions:</h3>";
echo "<ol>";
echo "<li><a href='admin/login.php'>Try logging in again</a></li>";
echo "<li>If still failing, <a href='reset-admin.php'>completely reset admin user</a></li>";
echo "<li><a href='test-connection.php'>Test database connection</a></li>";
echo "</ol>";

echo "<hr>";
echo "<p><strong>⚠️ Security:</strong> Delete this debug file after fixing the issue!</p>";
?>
