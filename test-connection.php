<?php
// Test database connection
require_once __DIR__ . '/includes/database.php';

echo "<h1>Database Connection Test</h1>";

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    if ($conn) {
        echo "<p style='color: green;'>✓ Database connection successful!</p>";
        
        // Test basic query
        $result = $conn->query("SELECT VERSION() as version");
        $version = $result->fetch(PDO::FETCH_ASSOC);
        echo "<p>MySQL Version: " . $version['version'] . "</p>";
        
        // Check if tables exist
        $tables = ['admin_users', 'songs', 'videos', 'tour_dates', 'newsletter_subscribers', 'contact_messages'];
        echo "<h3>Checking Tables:</h3>";
        
        foreach ($tables as $table) {
            $result = $conn->query("SHOW TABLES LIKE '$table'");
            if ($result->rowCount() > 0) {
                echo "<p style='color: green;'>✓ Table '$table' exists</p>";
                
                // Count records
                $count = $conn->query("SELECT COUNT(*) as count FROM $table")->fetch(PDO::FETCH_ASSOC)['count'];
                echo "<p style='margin-left: 20px;'>Records: $count</p>";
            } else {
                echo "<p style='color: red;'>✗ Table '$table' missing</p>";
            }
        }
        
    } else {
        echo "<p style='color: red;'>✗ Database connection failed!</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<h3>Configuration Check:</h3>";
echo "<p>DB_HOST: " . DB_HOST . "</p>";
echo "<p>DB_NAME: " . DB_NAME . "</p>";
echo "<p>DB_USER: " . DB_USER . "</p>";
echo "<p>APP_URL: " . APP_URL . "</p>";

echo "<hr>";
echo "<p><a href='index.php'>← Back to Website</a></p>";
echo "<p><a href='admin/login.php'>→ Admin Login</a></p>";
?>
