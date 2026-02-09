<?php
require_once 'includes/database.php';
require_once 'includes/functions.php';

$db = new Database();
$conn = $db->getConnection();

echo "<h1>Complete Video System Debug</h1>";

// 1. Check database connection
echo "<h2>1. Database Connection</h2>";
if ($conn) {
    echo "<p style='color: green;'>✅ Database connection successful</p>";
} else {
    echo "<p style='color: red;'>❌ Database connection failed</p>";
    exit;
}

// 2. Check video table structure
echo "<h2>2. Video Table Structure</h2>";
$result = $conn->query("DESCRIBE videos");
echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th></tr>";
while ($row = $result->fetch_assoc()) {
    echo "<tr><td>{$row['Field']}</td><td>{$row['Type']}</td><td>{$row['Null']}</td><td>{$row['Key']}</td></tr>";
}
echo "</table>";

// 3. Check total videos
echo "<h2>3. Video Count</h2>";
$result = $conn->query("SELECT COUNT(*) as count FROM videos");
$row = $result->fetch_assoc();
echo "<p>Total videos in database: <strong>{$row['count']}</strong></p>";

// 4. Check active videos
$result = $conn->query("SELECT COUNT(*) as count FROM videos WHERE is_active = 1");
$row = $result->fetch_assoc();
echo "<p>Active videos: <strong>{$row['count']}</strong></p>";

// 5. Show actual video data
echo "<h2>4. Actual Video Data</h2>";
$result = $conn->query("SELECT * FROM videos WHERE is_active = 1 LIMIT 5");
if ($result->num_rows > 0) {
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr><th>ID</th><th>Title</th><th>Video URL</th><th>Thumbnail</th><th>Type</th><th>Category</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['id']}</td>";
        echo "<td>" . htmlspecialchars($row['title']) . "</td>";
        echo "<td>" . htmlspecialchars($row['video_url']) . "</td>";
        echo "<td>" . htmlspecialchars($row['thumbnail']) . "</td>";
        echo "<td>" . htmlspecialchars($row['video_type']) . "</td>";
        echo "<td>" . htmlspecialchars($row['category_name'] ?? 'NULL') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'>❌ No active videos found in database!</p>";
}

// 6. Test get_videos function
echo "<h2>5. Test get_videos() Function</h2>";
$videos = get_videos(5);
if ($videos) {
    echo "<p style='color: green;'>✅ get_videos() function returned " . count($videos) . " videos</p>";
    foreach ($videos as $video) {
        echo "<div style='border: 1px solid #ccc; margin: 10px; padding: 10px;'>";
        echo "<strong>Title:</strong> " . htmlspecialchars($video['title']) . "<br>";
        echo "<strong>Video URL:</strong> " . htmlspecialchars($video['video_url']) . "<br>";
        echo "<strong>Thumbnail:</strong> " . htmlspecialchars($video['thumbnail']) . "<br>";
        echo "<strong>Type:</strong> " . htmlspecialchars($video['video_type']) . "<br>";
        echo "<strong>Category:</strong> " . htmlspecialchars($video['category_name'] ?? 'NULL') . "<br>";
        echo "</div>";
    }
} else {
    echo "<p style='color: red;'>❌ get_videos() function returned false or empty</p>";
}

// 7. Check if thumbnail files exist
echo "<h2>6. Check Thumbnail Files</h2>";
$result = $conn->query("SELECT id, title, thumbnail FROM videos WHERE is_active = 1 AND thumbnail IS NOT NULL LIMIT 3");
while ($row = $result->fetch_assoc()) {
    $thumbnail_path = __DIR__ . '/' . $row['thumbnail'];
    $exists = file_exists($thumbnail_path);
    $status = $exists ? "✅ Exists" : "❌ Missing";
    $color = $exists ? "green" : "red";
    echo "<p style='color: $color;'>{$status}: " . htmlspecialchars($row['thumbnail']) . " (ID: {$row['id']})</p>";
}

// 8. Generate sample video HTML for testing
echo "<h2>7. Sample Video HTML Output</h2>";
$videos = get_videos(2);
if ($videos) {
    foreach ($videos as $video) {
        echo "<div style='border: 2px solid #007bff; margin: 10px; padding: 15px;'>";
        echo "<h4>Generated HTML for: " . htmlspecialchars($video['title']) . "</h4>";
        echo "<pre style='background: #f8f9fa; padding: 10px; overflow-x: auto;'>";
        echo htmlspecialchars('<div class="video-item" data-category="' . $video['video_type'] . '">');
        echo "\n    <div class=\"video-thumbnail-container\">";
        echo "\n        <img src=\"" . APP_URL . '/' . ($video['thumbnail'] ?: 'assets/images/default-video.jpg') . "\" ";
        echo "\n             alt=\"" . xss_clean($video['title']) . "\" ";
        echo "\n             class=\"video-thumbnail\" ";
        echo "\n             data-video-url=\"" . $video['video_url'] . "\">";
        echo "\n        <div class=\"video-overlay\">";
        echo "\n            <div class=\"play-button\">";
        echo "\n                <i class=\"fas fa-play\"></i>";
        echo "\n            </div>";
        echo "\n        </div>";
        echo "\n    </div>";
        echo "\n</div>";
        echo "</pre>";
        echo "</div>";
    }
}
?>
