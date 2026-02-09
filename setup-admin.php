<?php
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/functions.php';

try {
    echo "Setting up Admin User...\n\n";
    
    // Create admin_users table if it doesn't exist
    $sql = "CREATE TABLE IF NOT EXISTS admin_users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        role VARCHAR(20) DEFAULT 'admin',
        is_active BOOLEAN DEFAULT TRUE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    
    if (executeQuery($sql)) {
        echo "✓ Admin users table created successfully\n";
    }
    
    // Check if admin user already exists
    $existing_admin = fetchOne("SELECT id FROM admin_users WHERE username = 'admin'");
    
    if (!$existing_admin) {
        // Create default admin user
        $password = password_hash('admin123', PASSWORD_DEFAULT);
        $sql = "INSERT INTO admin_users (username, password, email, role) VALUES (?, ?, ?, ?)";
        $params = ['admin', $password, 'admin@example.com', 'admin'];
        
        if (executeQuery($sql, $params)) {
            echo "✓ Default admin user created\n";
            echo "  Username: admin\n";
            echo "  Password: admin123\n";
            echo "  Email: admin@example.com\n";
        } else {
            echo "❌ Failed to create admin user\n";
        }
    } else {
        echo "ℹ Admin user already exists\n";
    }
    
    echo "\n✅ Admin setup completed!\n";
    echo "\n🔐 Login Credentials:\n";
    echo "URL: http://localhost/website-singers/admin/login.php\n";
    echo "Username: admin\n";
    echo "Password: admin123\n";
    echo "\n⚠️ Please change the default password after first login!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
