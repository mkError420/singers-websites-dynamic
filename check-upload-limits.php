<?php
// Check current PHP upload limits
echo "<h2>Current PHP Upload Limits</h2>";

echo "<h3>Current Settings:</h3>";
echo "upload_max_filesize: " . ini_get('upload_max_filesize') . "<br>";
echo "post_max_size: " . ini_get('post_max_size') . "<br>";
echo "memory_limit: " . ini_get('memory_limit') . "<br>";
echo "max_execution_time: " . ini_get('max_execution_time') . " seconds<br>";
echo "max_input_time: " . ini_get('max_input_time') . " seconds<br>";

echo "<h3>Required for 100MB uploads:</h3>";
echo "upload_max_filesize: 100M<br>";
echo "post_max_size: 100M<br>";
echo "memory_limit: 128M (recommended)<br>";
echo "max_execution_time: 300 (recommended)<br>";
echo "max_input_time: 300 (recommended)<br>";

echo "<h3>How to Fix:</h3>";
echo "<ol>";
echo "<li>Open XAMPP Control Panel</li>";
echo "<li>Click on 'Apache' → 'Config' → 'php.ini'</li>";
echo "<li>Find and update these settings:</li>";
echo "<pre>
upload_max_filesize = 100M
post_max_size = 100M
memory_limit = 128M
max_execution_time = 300
max_input_time = 300
</pre>";
echo "<li>Save the file</li>";
echo "<li>Restart Apache from XAMPP Control Panel</li>";
echo "</ol>";

echo "<h3>Alternative - .htaccess method:</h3>";
echo "<p>Create or update .htaccess file in your project root with:</p>";
echo "<pre>
php_value upload_max_filesize 100M
php_value post_max_size 100M
php_value memory_limit 128M
php_value max_execution_time 300
php_value max_input_time 300
</pre>";

echo "<h3>Current Upload Test:</h3>";
echo "Your current limit: " . (ini_get('upload_max_filesize') ? ini_get('upload_max_filesize') : 'Not set') . "<br>";
echo "Your current POST limit: " . (ini_get('post_max_size') ? ini_get('post_max_size') : 'Not set') . "<br>";

// Calculate if current settings support 100MB
$current_upload = ini_get('upload_max_filesize');
$current_post = ini_get('post_max_size');

function php_ini_to_bytes($val) {
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

if ($current_upload && $current_post) {
    $upload_bytes = php_ini_to_bytes($current_upload);
    $post_bytes = php_ini_to_bytes($current_post);
    $required_bytes = 100 * 1024 * 1024; // 100MB
    
    echo "<h3>Can you upload 100MB?</h3>";
    if ($upload_bytes >= $required_bytes && $post_bytes >= $required_bytes) {
        echo "<span style='color: green;'>✅ Yes, your current settings support 100MB uploads</span>";
    } else {
        echo "<span style='color: red;'>❌ No, your current settings do NOT support 100MB uploads</span>";
        echo "<br>Current upload limit: " . round($upload_bytes / 1024 / 1024, 2) . "MB";
        echo "<br>Current POST limit: " . round($post_bytes / 1024 / 1024, 2) . "MB";
        echo "<br>Required: 100MB";
    }
}
?>
