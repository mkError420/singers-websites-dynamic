<?php
echo "PHP is working!<br>";
echo "Current directory: " . __DIR__ . "<br>";
echo "Server: " . $_SERVER['SERVER_NAME'] . "<br>";
echo "Request URI: " . $_SERVER['REQUEST_URI'] . "<br>";

// Test database connection
try {
    require_once __DIR__ . '/../includes/database.php';
    echo "Database connection: OK<br>";
    
    // Check if admin_users table exists
    $result = fetchAll("SHOW TABLES LIKE 'admin_users'");
    if (count($result) > 0) {
        echo "Admin users table: EXISTS<br>";
        
        // Count admin users
        $count = fetchOne("SELECT COUNT(*) as count FROM admin_users")['count'];
        echo "Admin users count: " . $count . "<br>";
    } else {
        echo "Admin users table: NOT FOUND<br>";
    }
} catch (Exception $e) {
    echo "Database error: " . $e->getMessage() . "<br>";
}

echo "<br><a href='login.php'>Go to Login</a>";
?>
