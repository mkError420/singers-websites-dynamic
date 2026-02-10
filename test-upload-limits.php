<?php
// Test upload limits after configuration changes
echo "<h2>Upload Limits Test</h2>";

echo "<h3>Current PHP Settings:</h3>";
echo "upload_max_filesize: " . ini_get('upload_max_filesize') . "<br>";
echo "post_max_size: " . ini_get('post_max_size') . "<br>";
echo "memory_limit: " . ini_get('memory_limit') . "<br>";
echo "max_execution_time: " . ini_get('max_execution_time') . " seconds<br>";
echo "max_input_time: " . ini_get('max_input_time') . " seconds<br>";

// Convert to bytes for comparison
function to_bytes($val) {
    $val = trim($val);
    $last = strtolower($val[strlen($val)-1]);
    $val = (int)$val;
    switch($last) {
        case 'g':
            $val *= 1024;
        case 'm':
            $val *= 1024;
        case 'k':
            $val *= 1024;
    }
    return $val;
}

$current_upload = to_bytes(ini_get('upload_max_filesize'));
$current_post = to_bytes(ini_get('post_max_size'));
$required_bytes = 100 * 1024 * 1024; // 100MB

echo "<h3>Upload Capacity Check:</h3>";
echo "Current upload limit: " . round($current_upload / 1024 / 1024, 2) . "MB<br>";
echo "Current POST limit: " . round($current_post / 1024 / 1024, 2) . "MB<br>";
echo "Required for 100MB: 100MB<br>";

if ($current_upload >= $required_bytes && $current_post >= $required_bytes) {
    echo "<span style='color: green; font-size: 18px;'>✅ SUCCESS: You can now upload 100MB files!</span>";
} else {
    echo "<span style='color: red; font-size: 18px;'>❌ FAILED: Settings not updated yet</span>";
    echo "<br><br><strong>You need to restart Apache or edit XAMPP php.ini</strong>";
}

echo "<h3>Next Steps:</h3>";
echo "<ol>";
echo "<li><strong>Restart Apache</strong> from XAMPP Control Panel</li>";
echo "<li><strong>Refresh this page</strong> to check if settings are applied</li>";
echo "<li>If still not working, <strong>edit XAMPP php.ini</strong>:</li>";
echo "<ul>";
echo "<li>Open XAMPP Control Panel</li>";
echo "<li>Click Apache → Config → php.ini</li>";
echo "<li>Find and update: upload_max_filesize = 100M</li>";
echo "<li>Find and update: post_max_size = 100M</li>";
echo "<li>Save and restart Apache</li>";
echo "</ul>";
echo "</ol>";

echo "<h3>Your file size:</h3>";
echo "75.9 MB = " . round(75.9 * 1024 * 1024) . " bytes<br>";
echo "Current limit: " . $current_post . " bytes<br>";

if ($current_post < (75.9 * 1024 * 1024)) {
    echo "<span style='color: red;'>❌ Your file is too large for current settings</span>";
} else {
    echo "<span style='color: green;'>✅ Your file should upload successfully now</span>";
}
?>
