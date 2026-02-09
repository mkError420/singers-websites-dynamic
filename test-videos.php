<?php
require_once 'includes/database.php';
require_once 'includes/functions.php';

$db = new Database();
$conn = $db->getConnection();

echo "<h3>Video Data Check</h3>";

$result = $conn->query('SELECT id, title, video_url, thumbnail, video_type, category_name FROM videos WHERE is_active = 1 LIMIT 5');
if ($result) {
    echo "<table border='1' style='border-collapse: collapse; margin: 20px 0;'>";
    echo "<tr><th>ID</th><th>Title</th><th>Video URL</th><th>Thumbnail</th><th>Type</th><th>Category</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . htmlspecialchars($row['title']) . "</td>";
        echo "<td>" . htmlspecialchars($row['video_url']) . "</td>";
        echo "<td>" . htmlspecialchars($row['thumbnail']) . "</td>";
        echo "<td>" . htmlspecialchars($row['video_type']) . "</td>";
        echo "<td>" . htmlspecialchars($row['category_name'] ?? 'NULL') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "Error: " . $conn->error;
}

// Test get_videos function
echo "<h3>Test get_videos function</h3>";
$videos = get_videos(5);
if ($videos) {
    foreach ($videos as $video) {
        echo "<div style='border: 1px solid #ccc; margin: 10px; padding: 10px;'>";
        echo "<h4>" . htmlspecialchars($video['title']) . "</h4>";
        echo "<p><strong>URL:</strong> " . htmlspecialchars($video['video_url']) . "</p>";
        echo "<p><strong>Thumbnail:</strong> " . htmlspecialchars($video['thumbnail']) . "</p>";
        echo "<p><strong>Type:</strong> " . htmlspecialchars($video['video_type']) . "</p>";
        echo "<p><strong>Category:</strong> " . htmlspecialchars($video['category_name'] ?? 'NULL') . "</p>";
        echo "</div>";
    }
} else {
    echo "No videos found";
}
?>
