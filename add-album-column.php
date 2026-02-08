<?php
require_once 'config/config.php';

try {
    $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASS);
    
    // Add album column to songs table
    $sql = "ALTER TABLE songs ADD COLUMN album VARCHAR(100) AFTER artist";
    $pdo->exec($sql);
    
    echo "Album column added successfully to songs table!\n";
    
    // Update existing songs with default album if needed
    $update_sql = "UPDATE songs SET album = 'Unknown Album' WHERE album IS NULL";
    $pdo->exec($update_sql);
    
    echo "Existing records updated with default album values.\n";
    
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . "\n";
}
?>
