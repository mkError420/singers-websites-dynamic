<?php
// Quick fix for hero video type detection
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/functions.php';

echo "<h2>Hero Video Fix</h2>";

// Get all hero videos
$hero_videos = fetchAll("SELECT * FROM hero_videos ORDER BY id DESC");

echo "<h3>Current Hero Videos:</h3>";
foreach ($hero_videos as $video) {
    echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 10px 0;'>";
    echo "<strong>ID:</strong> " . $video['id'] . "<br>";
    echo "<strong>Title:</strong> " . $video['title'] . "<br>";
    echo "<strong>Type:</strong> " . $video['video_type'] . "<br>";
    echo "<strong>URL:</strong> " . $video['video_url'] . "<br>";
    echo "<strong>Active:</strong> " . ($video['is_active'] ? 'Yes' : 'No') . "<br>";
    
    // Check if it's actually a YouTube URL but marked as uploaded
    if ($video['video_type'] === 'uploaded' && (strpos($video['video_url'], 'youtube.com') !== false || strpos($video['video_url'], 'youtu.be') !== false)) {
        echo "<strong style='color: red;'>⚠️ ISSUE: YouTube URL marked as uploaded type</strong><br>";
        
        // Fix it
        $update_sql = "UPDATE hero_videos SET video_type = 'youtube' WHERE id = ?";
        $result = executeQuery($update_sql, [$video['id']]);
        
        if ($result) {
            echo "<strong style='color: green;'>✅ FIXED: Changed type to 'youtube'</strong><br>";
        }
    }
    
    echo "</div>";
}

echo "<br><a href='admin/hero-videos.php'>Go to Hero Videos Admin</a>";
?>
