<?php
// Test database connection
try {
    require_once 'includes/database.php';
    echo "Database connection: SUCCESS<br>";
    
    // Test if gallery table exists
    $tables = fetchAll("SHOW TABLES");
    echo "Tables in database:<br>";
    foreach ($tables as $table) {
        echo "- " . implode(", ", $table) . "<br>";
    }
    
    // Check specifically for gallery table
    $gallery_exists = fetchAll("SHOW TABLES LIKE 'gallery'");
    if (!empty($gallery_exists)) {
        echo "<br>Gallery table: EXISTS<br>";
        
        // Count records
        $count = fetchOne("SELECT COUNT(*) as count FROM gallery");
        echo "Gallery records: " . $count['count'] . "<br>";
        
        // Show some records
        $records = fetchAll("SELECT id, title, category FROM gallery LIMIT 5");
        if (!empty($records)) {
            echo "Sample records:<br>";
            foreach ($records as $record) {
                echo "- ID: {$record['id']}, Title: {$record['title']}, Category: {$record['category']}<br>";
            }
        }
    } else {
        echo "<br>Gallery table: NOT EXISTS<br>";
        echo "Creating gallery table...<br>";
        
        $sql = "CREATE TABLE IF NOT EXISTS gallery (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            description TEXT,
            image_url VARCHAR(500) NOT NULL,
            thumbnail_url VARCHAR(500),
            category VARCHAR(100) DEFAULT 'general',
            tags VARCHAR(500),
            is_active BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_category (category),
            INDEX idx_active (is_active),
            INDEX idx_created_at (created_at)
        )";
        
        if (executeQuery($sql)) {
            echo "Gallery table created successfully!<br>";
        } else {
            echo "Failed to create gallery table!<br>";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "<br>";
}
?>
