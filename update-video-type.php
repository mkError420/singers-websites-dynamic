<?php
require_once 'includes/database.php';
require_once 'includes/functions.php';

// Create database connection
$db = new Database();
$conn = $db->getConnection();

try {
    // Update the video_type column to include 'uploaded'
    $sql = "ALTER TABLE videos MODIFY COLUMN video_type ENUM('youtube', 'vimeo', 'uploaded') DEFAULT 'youtube'";
    
    if ($conn->query($sql)) {
        echo "Database schema updated successfully! Video type now includes 'uploaded'.\n";
    } else {
        echo "Error updating database schema: " . $conn->error . "\n";
    }
} catch (Exception $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}

$conn = null;
?>
