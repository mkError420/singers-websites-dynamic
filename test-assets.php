<?php
// Simple test to check file paths and APP_URL
require_once __DIR__ . '/config/config.php';

echo "<h2>Asset Path Test</h2>";
echo "<p>APP_URL: " . APP_URL . "</p>";
echo "<p>Server Name: " . $_SERVER['SERVER_NAME'] . "</p>";
echo "<p>Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "</p>";

// Check if files exist
$files_to_check = [
    'assets/css/style.css',
    'assets/js/main.js',
    'assets/images/artist-photo.jpg'
];

echo "<h3>File Existence Check:</h3>";
foreach ($files_to_check as $file) {
    $full_path = __DIR__ . '/' . $file;
    $exists = file_exists($full_path);
    $url = APP_URL . '/' . $file;
    
    echo "<p>";
    echo "<strong>$file</strong>: ";
    echo $exists ? "✅ EXISTS" : "❌ MISSING";
    echo "<br>";
    echo "Path: $full_path<br>";
    echo "URL: <a href='$url' target='_blank'>$url</a>";
    echo "</p>";
}

// Check hero videos
echo "<h3>Hero Videos Check:</h3>";
try {
    require_once __DIR__ . '/includes/database.php';
    require_once __DIR__ . '/includes/functions.php';
    
    $heroVideo = fetchOne("SELECT * FROM hero_videos WHERE is_active = 1 ORDER BY display_order ASC LIMIT 1");
    
    if ($heroVideo) {
        echo "<p>✅ Active hero video found: " . $heroVideo['title'] . "</p>";
        echo "<p>Type: " . $heroVideo['video_type'] . "</p>";
        echo "<p>URL: " . $heroVideo['video_url'] . "</p>";
        
        if ($heroVideo['video_type'] === 'uploaded') {
            $video_path = __DIR__ . '/' . $heroVideo['video_url'];
            echo "<p>File exists: " . (file_exists($video_path) ? "✅ YES" : "❌ NO") . "</p>";
        }
    } else {
        echo "<p>❌ No active hero video found</p>";
    }
} catch (Exception $e) {
    echo "<p>❌ Database error: " . $e->getMessage() . "</p>";
}
?>
