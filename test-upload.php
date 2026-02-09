<?php
require_once 'includes/database.php';
require_once 'includes/functions.php';

echo "<h3>Gallery Upload Test</h3>";

// Check if gallery table exists and has data
echo "<h4>1. Checking Gallery Table:</h4>";
$gallery_check = fetchAll("SHOW TABLES LIKE 'gallery'");
if (empty($gallery_check)) {
    echo "❌ Gallery table does not exist<br>";
} else {
    echo "✅ Gallery table exists<br>";
    
    // Count records
    $count = fetchOne("SELECT COUNT(*) as count FROM gallery");
    echo "📊 Total records: " . $count['count'] . "<br>";
    
    // Show recent records
    $recent = fetchAll("SELECT id, title, image_url, thumbnail_url, category, is_active FROM gallery ORDER BY created_at DESC LIMIT 5");
    if (!empty($recent)) {
        echo "<h4>2. Recent Gallery Records:</h4>";
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
        echo "<tr><th>ID</th><th>Title</th><th>Image URL</th><th>Thumbnail URL</th><th>Category</th><th>Active</th></tr>";
        foreach ($recent as $record) {
            echo "<tr>";
            echo "<td>{$record['id']}</td>";
            echo "<td>{$record['title']}</td>";
            echo "<td>{$record['image_url']}</td>";
            echo "<td>{$record['thumbnail_url']}</td>";
            echo "<td>{$record['category']}</td>";
            echo "<td>" . ($record['is_active'] ? 'Yes' : 'No') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Check if files actually exist
        echo "<h4>3. File Existence Check:</h4>";
        foreach ($recent as $record) {
            $image_path = $record['image_url'];
            $thumb_path = $record['thumbnail_url'];
            
            echo "<strong>{$record['title']}:</strong><br>";
            echo "Image URL: $image_path - ";
            if (file_exists($image_path)) {
                echo "✅ File exists<br>";
            } else {
                echo "❌ File NOT found at: $image_path<br>";
                // Try alternative paths
                $alt_paths = [
                    "../$image_path",
                    "admin/$image_path",
                    "admin/../$image_path"
                ];
                foreach ($alt_paths as $alt) {
                    if (file_exists($alt)) {
                        echo "✅ Found at alternative path: $alt<br>";
                        break;
                    }
                }
            }
            
            echo "Thumbnail URL: $thumb_path - ";
            if (file_exists($thumb_path)) {
                echo "✅ File exists<br>";
            } else {
                echo "❌ File NOT found at: $thumb_path<br>";
            }
            echo "<br>";
        }
    } else {
        echo "❌ No records found in gallery table<br>";
    }
}

// Check upload directories
echo "<h4>4. Upload Directory Check:</h4>";
$upload_dirs = [
    'uploads/gallery/',
    '../uploads/gallery/',
    'admin/uploads/gallery/',
    'admin/../uploads/gallery/'
];

foreach ($upload_dirs as $dir) {
    echo "Checking: $dir - ";
    if (file_exists($dir)) {
        echo "✅ Exists<br>";
        if (is_dir($dir)) {
            $files = scandir($dir);
            $image_files = array_filter($files, function($file) use ($dir) {
                return !in_array($file, ['.', '..']) && 
                       (pathinfo($file, PATHINFO_EXTENSION) === 'jpg' || 
                        pathinfo($file, PATHINFO_EXTENSION) === 'jpeg' || 
                        pathinfo($file, PATHINFO_EXTENSION) === 'png' || 
                        pathinfo($file, PATHINFO_EXTENSION) === 'gif');
            });
            echo "   📁 Contains " . count($image_files) . " image files<br>";
        }
    } else {
        echo "❌ Not found<br>";
    }
}

// Test get_gallery_images function
echo "<h4>5. Testing get_gallery_images Function:</h4>";
$images = get_gallery_images(5, 0, 'all', false);
if (!empty($images)) {
    echo "✅ Function returned " . count($images) . " images<br>";
    echo "<pre>" . print_r($images[0], true) . "</pre>";
} else {
    echo "❌ Function returned no images<br>";
}
?>
