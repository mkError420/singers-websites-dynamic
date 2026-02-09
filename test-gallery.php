<?php
require_once 'includes/database.php';
require_once 'includes/functions.php';

// Test if gallery table exists
try {
    $result = fetchAll("SHOW TABLES LIKE 'gallery'");
    if (empty($result)) {
        echo "Gallery table does not exist. Creating it...<br>";
        
        // Create gallery table
        $sql = "CREATE TABLE gallery (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            description TEXT,
            image_url VARCHAR(500) NOT NULL,
            thumbnail_url VARCHAR(500),
            category VARCHAR(100) NOT NULL,
            tags TEXT,
            is_active BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";
        
        if (executeQuery($sql)) {
            echo "Gallery table created successfully!<br>";
        } else {
            echo "Failed to create gallery table!<br>";
        }
    } else {
        echo "Gallery table exists.<br>";
        
        // Show table structure
        $structure = fetchAll("DESCRIBE gallery");
        echo "<h3>Gallery Table Structure:</h3>";
        echo "<table border='1'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th></tr>";
        foreach ($structure as $row) {
            echo "<tr><td>{$row['Field']}</td><td>{$row['Type']}</td><td>{$row['Null']}</td><td>{$row['Key']}</td></tr>";
        }
        echo "</table>";
        
        // Show current records
        $records = fetchAll("SELECT * FROM gallery");
        echo "<h3>Current Gallery Records (" . count($records) . "):</h3>";
        if (empty($records)) {
            echo "No records found.<br>";
        } else {
            foreach ($records as $record) {
                echo "ID: {$record['id']}, Title: {$record['title']}, Category: {$record['category']}<br>";
            }
        }
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "<br>";
}

// Test add_gallery_image function
echo "<h3>Testing add_gallery_image function:</h3>";
$test_result = add_gallery_image(
    "Test Image", 
    "Test Description", 
    "uploads/gallery/test.jpg", 
    "uploads/gallery/thumbnails/thumb_test.jpg", 
    "general", 
    "test,demo"
);

if ($test_result) {
    echo "add_gallery_image function works!<br>";
} else {
    echo "add_gallery_image function failed!<br>";
}
?>
