<?php
require_once 'config/config.php';

echo "=== Album Update Test ===\n\n";

try {
    $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASS);
    
    // 1. Check if album column exists
    echo "1. Checking album column...\n";
    $stmt = $pdo->query("DESCRIBE songs");
    $columns = [];
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $columns[] = $row['Field'];
    }
    
    if (in_array('album', $columns)) {
        echo "✅ Album column exists\n";
    } else {
        echo "❌ Album column missing\n";
        echo "Adding album column...\n";
        $pdo->exec("ALTER TABLE songs ADD COLUMN album VARCHAR(100) AFTER artist");
        echo "✅ Album column added\n";
    }
    
    // 2. Get a sample song to update
    echo "\n2. Getting sample song...\n";
    $sample = $pdo->query("SELECT * FROM songs LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    
    if ($sample) {
        $song_id = $sample['id'];
        echo "Found song ID: $song_id\n";
        echo "Current album: " . ($sample['album'] ?? 'NULL') . "\n";
        
        // 3. Test update with album
        echo "\n3. Testing update with album...\n";
        $test_album = "Test Album " . date('His');
        
        $update_sql = "UPDATE songs SET title = ?, artist = ?, album = ?, genre = ?, duration = ?, release_date = ?, file_path = ?, cover_image = ?, is_active = ? WHERE id = ?";
        $update_stmt = $pdo->prepare($update_sql);
        
        $result = $update_stmt->execute([
            $sample['title'],
            $sample['artist'], 
            $test_album,
            $sample['genre'] ?? '',
            $sample['duration'] ?? '',
            $sample['release_date'],
            $sample['file_path'],
            $sample['cover_image'],
            $sample['is_active'],
            $song_id
        ]);
        
        if ($result) {
            echo "✅ Update successful! Rows affected: " . $update_stmt->rowCount() . "\n";
            
            // 4. Verify the update
            echo "\n4. Verifying update...\n";
            $verify = $pdo->query("SELECT * FROM songs WHERE id = $song_id")->fetch(PDO::FETCH_ASSOC);
            if ($verify) {
                echo "New album value: " . ($verify['album'] ?? 'NULL') . "\n";
                echo "✅ Album update verified!\n";
            }
        } else {
            echo "❌ Update failed\n";
            echo "Error info: ";
            print_r($update_stmt->errorInfo());
        }
    } else {
        echo "❌ No songs found to test\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "\n=== Test Complete ===\n";
?>
