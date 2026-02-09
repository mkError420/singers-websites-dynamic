<?php
session_start();
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/database.php';

// Test with a sample video ID
$video_id = 1;
$video = fetchOne("SELECT * FROM videos WHERE id = ?", [$video_id]);

echo "<h2>Debug Video Data:</h2>";
if ($video) {
    echo "<pre>";
    print_r($video);
    echo "</pre>";
} else {
    echo "Video not found";
}
?>
