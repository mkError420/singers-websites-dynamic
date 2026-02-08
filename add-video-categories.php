<?php
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/functions.php';

echo "Adding video categories system...\n";

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();
    
    // Create video categories table
    $sql1 = "CREATE TABLE IF NOT EXISTS video_categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL UNIQUE,
        description TEXT,
        color VARCHAR(7) DEFAULT '#ff6b6b',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        is_active BOOLEAN DEFAULT TRUE
    )";
    
    if ($conn->query($sql1)) {
        echo "✓ video_categories table created\n";
    } else {
        echo "✗ Error creating video_categories table: " . $conn->error . "\n";
    }
    
    // Add category_id to videos table if it doesn't exist
    $checkColumn = $conn->query("SHOW COLUMNS FROM videos LIKE 'category_id'");
    if ($checkColumn->num_rows == 0) {
        $sql2 = "ALTER TABLE videos ADD COLUMN category_id INT DEFAULT NULL";
        if ($conn->query($sql2)) {
            echo "✓ category_id column added to videos table\n";
        } else {
            echo "✗ Error adding category_id column: " . $conn->error . "\n";
        }
    } else {
        echo "✓ category_id column already exists\n";
    }
    
    // Insert default categories
    $categories = [
        ['Music Videos', 'Official music videos and performances', '#ff6b6b'],
        ['Live Performances', 'Live concert footage and performances', '#4ecdc4'],
        ['Behind the Scenes', 'Behind the scenes content and making of', '#45b7d1'],
        ['Interviews', 'Interviews and press coverage', '#96ceb4'],
        ['Cover Songs', 'Cover songs and tributes', '#ffeaa7'],
        ['Dance Videos', 'Dance performances and choreography', '#dfe6e9'],
        ['Acoustic', 'Acoustic performances and sessions', '#fab1a0'],
        ['Remixes', 'Remix versions and edits', '#a29bfe']
    ];
    
    foreach ($categories as $category) {
        $sql = "INSERT IGNORE INTO video_categories (name, description, color) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $category[0], $category[1], $category[2]);
        if ($stmt->execute()) {
            echo "✓ Category '{$category[0]}' added\n";
        }
    }
    
    echo "\n✅ Video categories system added successfully!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
