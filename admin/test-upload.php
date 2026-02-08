<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

echo "<h1>Upload Path Debug</h1>";

echo "<h2>Configuration Check:</h2>";
echo "<p><strong>UPLOAD_PATH:</strong> " . UPLOAD_PATH . "</p>";
echo "<p><strong>UPLOAD_PATH exists:</strong> " . (is_dir(UPLOAD_PATH) ? 'Yes' : 'No') . "</p>";
echo "<p><strong>UPLOAD_PATH is writable:</strong> " . (is_writable(UPLOAD_PATH) ? 'Yes' : 'No') . "</p>";
echo "<p><strong>MAX_FILE_SIZE:</strong> " . MAX_FILE_SIZE . " bytes (" . (MAX_FILE_SIZE / 1024 / 1024) . "MB)</p>";

echo "<h2>Directory Contents:</h2>";
if (is_dir(UPLOAD_PATH)) {
    $files = scandir(UPLOAD_PATH);
    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..') {
            $filepath = UPLOAD_PATH . $file;
            $type = is_dir($filepath) ? 'Directory' : 'File';
            $writable = is_writable($filepath) ? 'Writable' : 'Not Writable';
            echo "<p><strong>$file:</strong> $type - $writable</p>";
        }
    }
}

echo "<h2>Test Upload:</h2>";
echo '<form method="POST" enctype="multipart/form-data">';
echo '<input type="file" name="test_file" accept="image/*">';
echo '<input type="submit" value="Test Upload">';
echo '</form>';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['test_file'])) {
    echo "<h3>Upload Result:</h3>";
    $result = upload_file($_FILES['test_file'], UPLOAD_PATH . 'test/', ['jpg', 'jpeg', 'png', 'gif']);
    
    echo "<pre>";
    print_r($result);
    echo "</pre>";
    
    if ($result['success']) {
        echo "<p style='color: green;'>✅ Upload successful!</p>";
    } else {
        echo "<p style='color: red;'>❌ Upload failed: " . $result['message'] . "</p>";
    }
}

echo "<hr>";
echo "<p><a href='add-song.php'>← Back to Add Song</a></p>";
?>
