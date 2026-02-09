<?php
require_once 'includes/database.php';
require_once 'includes/functions.php';

$db = new Database();
$conn = $db->getConnection();

echo "<h3>Video Database Check</h3>";

$result = $conn->query('SELECT COUNT(*) as count FROM videos WHERE is_active = 1');
$row = $result->fetch_assoc();
echo "<p><strong>Total active videos:</strong> " . $row['count'] . "</p>";

if ($row['count'] > 0) {
    $result = $conn->query('SELECT id, title, video_url, thumbnail, video_type, category_name FROM videos WHERE is_active = 1 LIMIT 3');
    echo "<h4>Sample Videos:</h4>";
    while ($video = $result->fetch_assoc()) {
        echo "<div style='border: 1px solid #ccc; margin: 10px; padding: 10px;'>";
        echo "<strong>ID:</strong> " . $video['id'] . "<br>";
        echo "<strong>Title:</strong> " . htmlspecialchars($video['title']) . "<br>";
        echo "<strong>Video URL:</strong> " . htmlspecialchars($video['video_url']) . "<br>";
        echo "<strong>Thumbnail:</strong> " . htmlspecialchars($video['thumbnail']) . "<br>";
        echo "<strong>Type:</strong> " . htmlspecialchars($video['video_type']) . "<br>";
        echo "<strong>Category:</strong> " . htmlspecialchars($video['category_name'] ?? 'NULL') . "<br>";
        echo "</div>";
    }
} else {
    echo "<p style='color: red;'>No active videos found in database!</p>";
    echo "<p>You need to add videos through the admin panel first.</p>";
}
?>
