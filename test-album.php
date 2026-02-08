<?php
require_once 'config/config.php';

try {
    $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASS);
    
    // Check if album column exists
    $stmt = $pdo->query("DESCRIBE songs");
    $columns = [];
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $columns[] = $row['Field'];
    }
    
    echo "Songs table columns:\n";
    echo "====================\n";
    foreach ($columns as $column) {
        echo "- " . $column . "\n";
    }
    
    if (in_array('album', $columns)) {
        echo "\n✅ Album column EXISTS!\n";
        
        // Test inserting a sample record
        $test_sql = "SELECT id, title, artist, album FROM songs LIMIT 3";
        $test_stmt = $pdo->query($test_sql);
        
        echo "\nSample records:\n";
        echo "===============\n";
        while ($row = $test_stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "ID: " . $row['id'] . "\n";
            echo "Title: " . $row['title'] . "\n";
            echo "Artist: " . $row['artist'] . "\n";
            echo "Album: " . ($row['album'] ?? 'NULL') . "\n";
            echo "---\n";
        }
    } else {
        echo "\n❌ Album column MISSING!\n";
        echo "Please run add-album-column.php script first.\n";
    }
    
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . "\n";
}
?>
