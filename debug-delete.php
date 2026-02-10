<?php
// Debug deletion issue
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/functions.php';

echo "<h2>Delete Hero Video Debug</h2>";

// Test database connection
echo "<h3>Database Connection Test:</h3>";
try {
    $db = new Database();
    $conn = $db->getConnection();
    if ($conn) {
        echo "✅ Database connection successful<br>";
    } else {
        echo "❌ Database connection failed<br>";
    }
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "<br>";
}

// Test table existence
echo "<h3>Table Test:</h3>";
try {
    $table_check = $conn->query("SHOW TABLES LIKE 'hero_videos'");
    if ($table_check->rowCount() > 0) {
        echo "✅ hero_videos table exists<br>";
        
        // Show table structure
        $structure = $conn->query("DESCRIBE hero_videos");
        echo "<h4>Table Structure:</h4>";
        echo "<table border='1'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th></tr>";
        while ($row = $structure->fetch()) {
            echo "<tr><td>" . $row['Field'] . "</td><td>" . $row['Type'] . "</td><td>" . $row['Null'] . "</td><td>" . $row['Key'] . "</td></tr>";
        }
        echo "</table>";
    } else {
        echo "❌ hero_videos table does not exist<br>";
    }
} catch (Exception $e) {
    echo "❌ Table check error: " . $e->getMessage() . "<br>";
}

// Show current hero videos
echo "<h3>Current Hero Videos:</h3>";
try {
    $hero_videos = fetchAll("SELECT * FROM hero_videos ORDER BY id DESC");
    if ($hero_videos) {
        foreach ($hero_videos as $video) {
            echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 10px 0;'>";
            echo "<strong>ID:</strong> " . $video['id'] . "<br>";
            echo "<strong>Title:</strong> " . $video['title'] . "<br>";
            echo "<strong>Type:</strong> " . $video['video_type'] . "<br>";
            echo "<strong>Active:</strong> " . ($video['is_active'] ? 'Yes' : 'No') . "<br>";
            echo "<strong>Created:</strong> " . $video['created_at'] . "<br>";
            
            // Test deletion for this specific video
            $test_id = $video['id'];
            echo "<br><strong>Testing deletion for ID $test_id:</strong><br>";
            
            // Test the exact delete query
            $test_sql = "DELETE FROM hero_videos WHERE id = ?";
            $test_stmt = $conn->prepare($test_sql);
            $test_stmt->execute([$test_id]);
            $affected = $test_stmt->rowCount();
            
            echo "SQL: $test_sql<br>";
            echo "Params: [$test_id]<br>";
            echo "Rows affected: $affected<br>";
            
            if ($affected > 0) {
                echo "<span style='color: green;'>✅ Deletion test successful</span><br>";
            } else {
                echo "<span style='color: red;'>❌ Deletion test failed</span><br>";
                
                // Check if record still exists
                $check_stmt = $conn->prepare("SELECT * FROM hero_videos WHERE id = ?");
                $check_stmt->execute([$test_id]);
                $still_exists = $check_stmt->rowCount();
                
                if ($still_exists > 0) {
                    echo "Record still exists after deletion attempt<br>";
                } else {
                    echo "Record was already deleted<br>";
                }
            }
            
            echo "</div>";
            break; // Only test the first video
        }
    } else {
        echo "No hero videos found in database<br>";
    }
} catch (Exception $e) {
    echo "❌ Error fetching videos: " . $e->getMessage() . "<br>";
}

echo "<br><a href='admin/hero-videos.php'>Back to Hero Videos Admin</a>";
?>
