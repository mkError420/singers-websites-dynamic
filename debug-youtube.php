<?php
// Debug YouTube video playback issues
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/functions.php';

echo "<h2>YouTube Video Debug Tool</h2>";

// Get active hero video
$heroVideo = fetchOne("SELECT * FROM hero_videos WHERE is_active = 1 ORDER BY display_order ASC LIMIT 1");

if ($heroVideo) {
    echo "<h3>Current Hero Video:</h3>";
    echo "<p><strong>Title:</strong> " . $heroVideo['title'] . "</p>";
    echo "<p><strong>Type:</strong> " . $heroVideo['video_type'] . "</p>";
    echo "<p><strong>Original URL:</strong> " . $heroVideo['video_url'] . "</p>";
    
    if ($heroVideo['video_type'] === 'youtube') {
        $embedUrl = convertToEmbedUrl($heroVideo['video_url']);
        $videoId = getYoutubeVideoId($heroVideo['video_url']);
        
        echo "<p><strong>Embed URL:</strong> " . $embedUrl . "</p>";
        echo "<p><strong>Video ID:</strong> " . $videoId . "</p>";
        
        // Test different URL formats
        echo "<h3>URL Format Tests:</h3>";
        
        $testUrls = [
            'Standard Embed' => "https://www.youtube.com/embed/$videoId",
            'No-Cookie Embed' => "https://www.youtube-nocookie.com/embed/$videoId",
            'With Parameters' => "https://www.youtube.com/embed/$videoId?autoplay=1&mute=1&loop=1&playlist=$videoId&controls=0",
        ];
        
        foreach ($testUrls as $name => $url) {
            echo "<p><strong>$name:</strong> <a href='$url' target='_blank'>$url</a></p>";
        }
        
        echo "<h3>Test Iframe:</h3>";
        echo "<iframe src='https://www.youtube.com/embed/$videoId?autoplay=1&mute=1&loop=1&playlist=$videoId&controls=0' width='560' height='315' frameborder='0' allowfullscreen></iframe>";
        
        echo "<h3>Troubleshooting Steps:</h3>";
        echo "<ol>";
        echo "<li>Check if the video ID is correct: <code>$videoId</code></li>";
        echo "<li>Test the iframe above - does it play?</li>";
        echo "<li>Check browser console for errors</li>";
        echo "<li>Try a different YouTube URL</li>";
        echo "<li>Make sure the video is not age-restricted or private</li>";
        echo "<li>Check if the video allows embedding</li>";
        echo "</ol>";
        
        echo "<h3>Common Issues:</h3>";
        echo "<ul>";
        echo "<li><strong>Video unavailable:</strong> Video might be private, deleted, or region-restricted</li>";
        echo "<li><strong>Embedding disabled:</strong> Video owner disabled embedding</li>";
        echo "<li><strong>Age-restricted:</strong> Some age-restricted videos don't allow embedding</li>";
        echo "<li><strong>Copyright issues:</strong> Video might have copyright restrictions</li>";
        echo "</ul>";
        
    } else {
        echo "<p>Not a YouTube video type</p>";
    }
} else {
    echo "<p>No active hero video found</p>";
}

echo "<h3>YouTube URL Formats That Work:</h3>";
echo "<ul>";
echo "<li><code>https://www.youtube.com/watch?v=VIDEO_ID</code></li>";
echo "<li><code>https://youtu.be/VIDEO_ID</code></li>";
echo "<li><code>https://www.youtube.com/embed/VIDEO_ID</code></li>";
echo "</ul>";

echo "<p><a href='admin/hero-videos.php'>Edit Hero Video</a> | <a href='/'>Back to Home</a></p>";
?>
