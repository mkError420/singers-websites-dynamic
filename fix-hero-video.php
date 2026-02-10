<?php
// Fix hero video type
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/functions.php';

// Update the hero video type from 'uploaded' to 'youtube'
$update_sql = "UPDATE hero_videos SET video_type = 'youtube' WHERE video_url LIKE '%youtube%' OR video_url LIKE '%youtu.be%'";

try {
    $result = executeQuery($update_sql);
    if ($result) {
        echo "✅ Hero video type fixed! Updated " . $result->rowCount() . " record(s).";
        
        // Show updated hero video
        $heroVideo = fetchOne("SELECT * FROM hero_videos WHERE is_active = 1 ORDER BY display_order ASC LIMIT 1");
        if ($heroVideo) {
            echo "<br><br><strong>Updated Hero Video:</strong><br>";
            echo "Title: " . $heroVideo['title'] . "<br>";
            echo "Type: " . $heroVideo['video_type'] . "<br>";
            echo "URL: " . $heroVideo['video_url'] . "<br>";
            echo "Active: " . ($heroVideo['is_active'] ? 'Yes' : 'No');
        }
    } else {
        echo "❌ No records needed updating or update failed.";
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>
