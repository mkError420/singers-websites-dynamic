<?php
require_once 'includes/database.php';
require_once 'includes/functions.php';

echo "<h3>Database Gallery Check</h3>";

// Get all gallery records
$images = fetchAll("SELECT * FROM gallery ORDER BY created_at DESC");

if (empty($images)) {
    echo "❌ No records found in gallery table<br>";
} else {
    echo "✅ Found " . count($images) . " records in gallery table<br><br>";
    
    foreach ($images as $image) {
        echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 10px 0;'>";
        echo "<strong>ID: {$image['id']}</strong><br>";
        echo "Title: {$image['title']}<br>";
        echo "Category: {$image['category']}<br>";
        echo "Active: " . ($image['is_active'] ? 'Yes' : 'No') . "<br>";
        echo "Image URL: {$image['image_url']}<br>";
        echo "Thumbnail URL: {$image['thumbnail_url']}<br>";
        
        // Check if files exist
        $image_exists = file_exists($image['image_url']);
        $thumb_exists = file_exists($image['thumbnail_url']);
        
        echo "Image File: " . ($image_exists ? '✅ Exists' : '❌ Missing') . "<br>";
        echo "Thumbnail File: " . ($thumb_exists ? '✅ Exists' : '❌ Missing') . "<br>";
        
        // If files don't exist, try alternative paths
        if (!$image_exists) {
            $alt_path = "../" . $image['image_url'];
            if (file_exists($alt_path)) {
                echo "✅ Image found at: $alt_path<br>";
            }
        }
        
        if (!$thumb_exists) {
            $alt_path = "../" . $image['thumbnail_url'];
            if (file_exists($alt_path)) {
                echo "✅ Thumbnail found at: $alt_path<br>";
            }
        }
        
        echo "</div>";
    }
}

// Test the get_gallery_images function
echo "<h3>Testing get_gallery_images Function:</h3>";
$gallery_images = get_gallery_images(10, 0, 'all', false);
echo "Function returned: " . count($gallery_images) . " images<br>";

if (!empty($gallery_images)) {
    echo "<pre>";
    print_r($gallery_images[0]);
    echo "</pre>";
}
?>
