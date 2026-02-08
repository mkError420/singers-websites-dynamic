<?php
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/functions.php';

echo "Updating video category system to use text categories...\n";

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    // Add category_name column if it doesn't exist
    $checkColumn = $conn->query("SHOW COLUMNS FROM videos LIKE 'category_name'");
    if ($checkColumn->num_rows == 0) {
        $sql = "ALTER TABLE videos ADD COLUMN category_name VARCHAR(100) DEFAULT NULL AFTER video_type";
        if ($conn->query($sql)) {
            echo "✓ category_name column added to videos table\n";
        } else {
            echo "✗ Error adding category_name column: " . $conn->error . "\n";
        }
    } else {
        echo "✓ category_name column already exists\n";
    }
    
    // Drop foreign key constraint if it exists
    $sql = "ALTER TABLE videos DROP FOREIGN KEY IF EXISTS fk_video_category";
    $conn->query($sql);
    
    // Drop category_id column if it exists
    $checkColumn = $conn->query("SHOW COLUMNS FROM videos LIKE 'category_id'");
    if ($checkColumn->num_rows > 0) {
        $sql = "ALTER TABLE videos DROP COLUMN category_id";
        if ($conn->query($sql)) {
            echo "✓ category_id column removed from videos table\n";
        } else {
            echo "✗ Error removing category_id column: " . $conn->error . "\n";
        }
    }
    
    echo "\n✅ Video category system updated to use text categories!\n";
    echo "Admins can now write custom category names for videos.\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
