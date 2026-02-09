<?php
// Common functions for the singer website
require_once __DIR__ . '/../config/config.php';

// Redirect function
function redirect($url) {
    header("Location: $url");
    exit();
}

// Check if user is logged in
function is_logged_in() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

// Require login
function require_login() {
    if (!is_logged_in()) {
        $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
        redirect('admin/login.php');
    }
}

// Get current page URL
function current_url() {
    return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . 
           "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
}

// Format date
function format_date($date, $format = 'F j, Y') {
    return date($format, strtotime($date));
}

// Format time
function format_time($time) {
    return date('g:i A', strtotime($time));
}

// Truncate text
function truncate_text($text, $length = 100, $suffix = '...') {
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length) . $suffix;
}

// Generate slug from string
function generate_slug($string) {
    $slug = strtolower($string);
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
    $slug = trim($slug, '-');
    return $slug;
}

// Upload file
function upload_file($file, $destination, $allowed_types = []) {
    if (empty($allowed_types)) {
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'mp3', 'mp4', 'pdf'];
    }
    
    // Check if file was uploaded
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $error_messages = [
            UPLOAD_ERR_INI_SIZE => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
            UPLOAD_ERR_FORM_SIZE => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
            UPLOAD_ERR_PARTIAL => 'The uploaded file was only partially uploaded',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
            UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload',
        ];
        
        $error_message = $error_messages[$file['error']] ?? 'Unknown upload error';
        return ['success' => false, 'message' => $error_message];
    }
    
    $file_info = pathinfo($file['name']);
    $extension = strtolower($file_info['extension']);
    
    if (!in_array($extension, $allowed_types)) {
        return ['success' => false, 'message' => 'File type not allowed'];
    }
    
    if ($file['size'] > MAX_FILE_SIZE) {
        return ['success' => false, 'message' => 'File size too large'];
    }
    
    // Ensure destination directory exists
    if (!is_dir($destination)) {
        if (!mkdir($destination, 0755, true)) {
            return ['success' => false, 'message' => 'Failed to create upload directory'];
        }
    }
    
    // Generate unique filename
    $filename = uniqid() . '.' . $extension;
    $filepath = $destination . $filename;
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return ['success' => true, 'filename' => $filename, 'filepath' => $filepath];
    }
    
    return ['success' => false, 'message' => 'Failed to upload file: ' . error_get_last()['message']];
}

// Send email (basic implementation)
function send_email($to, $subject, $message, $from = FROM_EMAIL) {
    $headers = "From: " . FROM_NAME . " <" . $from . ">\r\n";
    $headers .= "Reply-To: " . $from . "\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    
    return mail($to, $subject, $message, $headers);
}

// Get songs from database
function get_songs($limit = null, $active_only = true) {
    $sql = "SELECT * FROM songs";
    $params = [];
    
    if ($active_only) {
        $sql .= " WHERE is_active = 1";
    }
    
    $sql .= " ORDER BY created_at DESC";
    
    if ($limit) {
        $sql .= " LIMIT ?";
        $params[] = $limit;
    }
    
    return fetchAll($sql, $params);
}

// Get videos from database
function get_videos($limit = null, $offset = 0, $active_only = true) {
    $sql = "SELECT * FROM videos";
    $params = [];
    
    if ($active_only) {
        $sql .= " WHERE is_active = 1";
    }
    
    $sql .= " ORDER BY created_at DESC";
    
    if ($limit) {
        $sql .= " LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
    }
    
    return fetchAll($sql, $params);
}

// Get tour dates from database
function get_tour_dates($future_only = true, $limit = null) {
    $sql = "SELECT * FROM tour_dates";
    $params = [];
    
    if ($future_only) {
        $sql .= " WHERE event_date >= CURDATE()";
    }
    
    $sql .= " AND is_active = 1 ORDER BY event_date ASC";
    
    if ($limit) {
        $sql .= " LIMIT ?";
        $params[] = $limit;
    }
    
    return fetchAll($sql, $params);
}

// Get page title based on current file
function get_page_title() {
    $current_file = basename($_SERVER['PHP_SELF'], '.php');
    $titles = [
        'index' => 'Home - ' . APP_NAME,
        'music' => 'Music - ' . APP_NAME,
        'videos' => 'Videos - ' . APP_NAME,
        'tour' => 'Tour Dates - ' . APP_NAME,
        'about' => 'About - ' . APP_NAME,
        'contact' => 'Contact - ' . APP_NAME,
        'admin' => 'Admin Dashboard - ' . APP_NAME
    ];
    
    return isset($titles[$current_file]) ? $titles[$current_file] : APP_NAME;
}

// Pagination helper
function get_pagination_data($total_items, $items_per_page = 10, $current_page = 1) {
    $total_pages = ceil($total_items / $items_per_page);
    $offset = ($current_page - 1) * $items_per_page;
    
    return [
        'total_items' => $total_items,
        'items_per_page' => $items_per_page,
        'total_pages' => $total_pages,
        'current_page' => $current_page,
        'offset' => $offset,
        'has_prev' => $current_page > 1,
        'has_next' => $current_page < $total_pages
    ];
}

// Get video categories from database (text-based)
function get_video_categories_from_videos($limit = null) {
    $sql = "SELECT DISTINCT category_name FROM videos WHERE category_name IS NOT NULL AND category_name != '' AND is_active = 1 ORDER BY category_name ASC";
    $params = [];
    
    if ($limit) {
        $sql .= " LIMIT ?";
        $params[] = $limit;
    }
    
    $result = fetchAll($sql, $params);
    
    // Convert to category objects with default colors
    $categories = [];
    $default_colors = ['#ff6b6b', '#4ecdc4', '#45b7d1', '#96ceb4', '#ffeaa7', '#dfe6e9', '#fab1a0', '#a29bfe'];
    $color_index = 0;
    
    foreach ($result as $row) {
        $categories[] = [
            'name' => $row['category_name'],
            'color' => $default_colors[$color_index % count($default_colors)],
            'count' => 0 // Will be updated below
        ];
        $color_index++;
    }
    
    // Get video counts for each category
    foreach ($categories as &$category) {
        $count_sql = "SELECT COUNT(*) as count FROM videos WHERE category_name = ? AND is_active = 1";
        $count_result = fetchOne($count_sql, [$category['name']]);
        $category['count'] = $count_result['count'] ?? 0;
    }
    
    return $categories;
}

// Get videos by category name
function get_videos_by_category_name($category_name, $limit = null, $offset = 0) {
    $sql = "SELECT * FROM videos WHERE is_active = 1";
    $params = [];
    
    if ($category_name) {
        $sql .= " AND category_name = ?";
        $params[] = $category_name;
    }
    
    $sql .= " ORDER BY created_at DESC";
    
    if ($limit) {
        $sql .= " LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
    }
    
    return fetchAll($sql, $params);
}

// Get videos with search term
function get_videos_with_search($search_term, $limit = null, $offset = 0) {
    $sql = "SELECT * FROM videos WHERE is_active = 1 AND (title LIKE ? OR description LIKE ? OR category_name LIKE ?) ORDER BY created_at DESC";
    $params = ['%' . $search_term . '%', '%' . $search_term . '%', '%' . $search_term . '%'];
    
    if ($limit) {
        $sql .= " LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
    }
    
    return fetchAll($sql, $params);
}

// Get videos with search term and category
function get_videos_with_search_and_category($search_term, $category_name, $limit = null, $offset = 0) {
    $sql = "SELECT * FROM videos WHERE is_active = 1 AND (title LIKE ? OR description LIKE ? OR category_name LIKE ?) ORDER BY created_at DESC";
    $params = ['%' . $search_term . '%', '%' . $search_term . '%', '%' . $search_term . '%', $category_name];
    
    if ($limit) {
        $sql .= " LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
    }
    
    return fetchAll($sql, $params);
}

// Gallery Functions
function get_gallery_images($limit = null, $offset = 0, $category = null, $active_only = true) {
    $sql = "SELECT * FROM gallery";
    $params = [];
    
    if ($active_only) {
        $sql .= " WHERE is_active = 1";
    }
    
    if ($category && $category !== 'all') {
        $sql .= ($active_only ? " AND" : " WHERE") . " category = ?";
        $params[] = $category;
    }
    
    $sql .= " ORDER BY created_at DESC";
    
    if ($limit) {
        $sql .= " LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
    }
    
    return fetchAll($sql, $params);
}

function get_gallery_categories() {
    $sql = "SELECT DISTINCT category FROM gallery WHERE is_active = 1 ORDER BY category";
    $result = fetchAll($sql);
    $categories = [];
    foreach ($result as $row) {
        $categories[] = $row['category'];
    }
    return $categories;
}

function get_gallery_image_count($category = null, $active_only = true) {
    $sql = "SELECT COUNT(*) as count FROM gallery";
    $params = [];
    
    if ($active_only) {
        $sql .= " WHERE is_active = 1";
    }
    
    if ($category && $category !== 'all') {
        $sql .= ($active_only ? " AND" : " WHERE") . " category = ?";
        $params[] = $category;
    }
    
    return fetchOne($sql, $params)['count'];
}

function add_gallery_image($title, $description, $image_url, $thumbnail_url, $category, $tags) {
    $sql = "INSERT INTO gallery (title, description, image_url, thumbnail_url, category, tags) VALUES (?, ?, ?, ?, ?, ?)";
    $params = [$title, $description, $image_url, $thumbnail_url, $category, $tags];
    return executeQuery($sql, $params);
}

function update_gallery_image($id, $title, $description, $category, $tags, $is_active) {
    $sql = "UPDATE gallery SET title = ?, description = ?, category = ?, tags = ?, is_active = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
    $params = [$title, $description, $category, $tags, $is_active, $id];
    return executeQuery($sql, $params);
}

function delete_gallery_image($id) {
    $sql = "DELETE FROM gallery WHERE id = ?";
    return executeQuery($sql, [$id]);
}

function get_gallery_image_by_id($id) {
    $sql = "SELECT * FROM gallery WHERE id = ?";
    return fetchOne($sql, [$id]);
}

function search_gallery_images($search_term, $limit = null, $offset = 0) {
    $sql = "SELECT * FROM gallery WHERE is_active = 1 AND (title LIKE ? OR description LIKE ? OR tags LIKE ? OR category LIKE ?) ORDER BY created_at DESC";
    $params = ['%' . $search_term . '%', '%' . $search_term . '%', '%' . $search_term . '%', '%' . $search_term . '%'];
    
    if ($limit) {
        $sql .= " LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
    }
    
    return fetchAll($sql, $params);
}

function get_search_gallery_count($search_term) {
    $sql = "SELECT COUNT(*) as count FROM gallery WHERE is_active = 1 AND (title LIKE ? OR description LIKE ? OR tags LIKE ? OR category LIKE ?)";
    $params = ['%' . $search_term . '%', '%' . $search_term . '%', '%' . $search_term . '%', '%' . $search_term . '%'];
    return fetchOne($sql, $params)['count'];
}
?>
