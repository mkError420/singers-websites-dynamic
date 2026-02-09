<?php
require_once 'includes/database.php';
require_once 'includes/functions.php';

// Test get_gallery_image_by_id function
echo "<h3>Testing get_gallery_image_by_id function:</h3>";

// First, check if gallery table has any records
$all_images = fetchAll("SELECT id, title FROM gallery LIMIT 5");
if (empty($all_images)) {
    echo "No images found in gallery table. Adding test image...<br>";
    
    // Add a test image
    $result = add_gallery_image(
        "Test Image for Edit", 
        "Test Description", 
        "uploads/gallery/test.jpg", 
        "uploads/gallery/thumbnails/thumb_test.jpg", 
        "general", 
        ""
    );
    
    if ($result) {
        echo "Test image added successfully!<br>";
        $all_images = fetchAll("SELECT id, title FROM gallery LIMIT 5");
    } else {
        echo "Failed to add test image!<br>";
    }
}

if (!empty($all_images)) {
    echo "Found " . count($all_images) . " images:<br>";
    foreach ($all_images as $img) {
        echo "- ID: {$img['id']}, Title: {$img['title']}<br>";
        
        // Test the get_gallery_image_by_id function
        $image_data = get_gallery_image_by_id($img['id']);
        if ($image_data) {
            echo "  ✓ Successfully fetched image data<br>";
            echo "  Data: " . json_encode($image_data) . "<br>";
        } else {
            echo "  ✗ Failed to fetch image data<br>";
        }
    }
} else {
    echo "Still no images found. There might be an issue with the database or table.<br>";
}

// Test the JSON response format
echo "<h3>Testing JSON response format:</h3>";
if (!empty($all_images)) {
    $test_id = $all_images[0]['id'];
    $image = get_gallery_image_by_id($test_id);
    
    if ($image) {
        header('Content-Type: application/json');
        echo "JSON response that would be sent to frontend:<br>";
        echo json_encode($image, JSON_PRETTY_PRINT);
    }
}
?>
