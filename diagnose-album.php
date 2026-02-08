<?php
require_once 'config/config.php';

echo "=== Album Field Diagnostic ===\n\n";

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
        echo "❌ Album column missing - attempting to add...\n";
        try {
            $pdo->exec("ALTER TABLE songs ADD COLUMN album VARCHAR(100) AFTER artist");
            echo "✅ Album column added successfully\n";
        } catch (Exception $e) {
            echo "❌ Failed to add column: " . $e->getMessage() . "\n";
        }
    }
    
    // 2. Test adding a song with album
    echo "\n2. Testing album insertion...\n";
    $test_title = "Test Song " . date('His');
    $test_artist = "Test Artist";
    $test_album = "Test Album";
    
    $insert_sql = "INSERT INTO songs (title, artist, album, file_path) VALUES (?, ?, ?, ?)";
    $insert_stmt = $pdo->prepare($insert_sql);
    
    try {
        $result = $insert_stmt->execute([$test_title, $test_artist, $test_album, 'test.mp3']);
        if ($result) {
            $last_id = $pdo->lastInsertId();
            echo "✅ Test song added with album (ID: $last_id)\n";
            
            // 3. Test retrieving the song
            echo "\n3. Testing album retrieval...\n";
            $select_sql = "SELECT * FROM songs WHERE id = ?";
            $select_stmt = $pdo->prepare($select_sql);
            $select_stmt->execute([$last_id]);
            $song = $select_stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($song) {
                echo "✅ Retrieved song:\n";
                echo "   Title: " . $song['title'] . "\n";
                echo "   Artist: " . $song['artist'] . "\n";
                echo "   Album: " . ($song['album'] ?? 'NULL') . "\n";
                echo "   Album is null: " . (is_null($song['album']) ? 'YES' : 'NO') . "\n";
            } else {
                echo "❌ Failed to retrieve test song\n";
            }
            
            // 4. Clean up test record
            $delete_sql = "DELETE FROM songs WHERE id = ?";
            $delete_stmt = $pdo->prepare($delete_sql);
            $delete_stmt->execute([$last_id]);
            echo "✅ Test record cleaned up\n";
        }
    } catch (Exception $e) {
        echo "❌ Insert failed: " . $e->getMessage() . "\n";
    }
    
    // 5. Check existing songs
    echo "\n4. Checking existing songs...\n";
    $existing_sql = "SELECT id, title, artist, album FROM songs LIMIT 5";
    $existing_stmt = $pdo->query($existing_sql);
    
    while ($song = $existing_stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "Song ID " . $song['id'] . ": ";
        echo "Album = " . ($song['album'] ?? 'NULL/Not Set') . "\n";
    }
    
} catch (Exception $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}

echo "\n=== Diagnostic Complete ===\n";
?>
