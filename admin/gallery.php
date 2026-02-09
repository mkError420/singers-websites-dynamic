<?php
session_start();
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/functions.php';

// Simple authentication check
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Check if gallery table exists, create if not
try {
    $gallery_table_check = fetchAll("SHOW TABLES LIKE 'gallery'");
    if (empty($gallery_table_check)) {
        // Create gallery table
        $create_table_sql = "CREATE TABLE gallery (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            description TEXT,
            image_url VARCHAR(500) NOT NULL,
            thumbnail_url VARCHAR(500),
            category VARCHAR(100) DEFAULT 'general',
            tags VARCHAR(500),
            is_active BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_category (category),
            INDEX idx_active (is_active),
            INDEX idx_created_at (created_at)
        )";
        executeQuery($create_table_sql);
    }
} catch (Exception $e) {
    // Continue even if table creation fails
    error_log("Gallery table check failed: " . $e->getMessage());
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $title = $_POST['title'] ?? '';
                $description = $_POST['description'] ?? '';
                $category = $_POST['category'] ?? '';
                $tags = ''; // Tags field removed from form
                
                // Validate required fields
                if (empty($title) || empty($category)) {
                    $error_message = "Title and Category are required fields!";
                    break;
                }
                
                // Handle image upload
                $image_url = '';
                $thumbnail_url = '';
                
                if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    $upload_dir = '../uploads/gallery/';
                    $thumbnail_dir = '../uploads/gallery/thumbnails/';
                    
                    // Create directories if they don't exist
                    if (!file_exists($upload_dir)) {
                        mkdir($upload_dir, 0755, true);
                    }
                    if (!file_exists($thumbnail_dir)) {
                        mkdir($thumbnail_dir, 0755, true);
                    }
                    
                    $file_name = time() . '_' . basename($_FILES['image']['name']);
                    $target_file = $upload_dir . $file_name;
                    $thumbnail_file = $thumbnail_dir . 'thumb_' . $file_name;
                    
                    error_log("Upload target: " . $target_file);
                    error_log("Upload directory exists: " . (file_exists($upload_dir) ? 'Yes' : 'No'));
                    
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                        $image_url = 'uploads/gallery/' . $file_name;
                        
                        error_log("File uploaded successfully. Image URL: " . $image_url);
                        error_log("File exists at: " . (file_exists($target_file) ? 'Yes' : 'No'));
                        
                        // Create thumbnail (simple resize)
                        createThumbnail($target_file, $thumbnail_file, 300, 200);
                        if (file_exists($thumbnail_file)) {
                            $thumbnail_url = 'uploads/gallery/thumbnails/thumb_' . $file_name;
                            error_log("Thumbnail created: " . $thumbnail_url);
                        } else {
                            $thumbnail_url = $image_url; // Use original if thumbnail fails
                            error_log("Thumbnail failed, using original: " . $thumbnail_url);
                        }
                    } else {
                        $error_message = "Failed to upload image file!";
                        error_log("File upload failed!");
                        break;
                    }
                } else {
                    $error_message = "Please select an image file!";
                    break;
                }
                
                // Add to database
                error_log("Adding to database: " . print_r([
                    'title' => $title,
                    'description' => $description,
                    'image_url' => $image_url,
                    'thumbnail_url' => $thumbnail_url,
                    'category' => $category,
                    'tags' => $tags
                ], true));
                
                $result = add_gallery_image($title, $description, $image_url, $thumbnail_url, $category, $tags);
                
                error_log("Database insert result: " . ($result ? 'Success' : 'Failed'));
                
                if ($result) {
                    header('Location: gallery.php?success=added');
                    exit;
                } else {
                    $error_message = "Failed to save image to database!";
                }
                break;
                
            case 'edit':
                $id = $_POST['id'];
                $title = $_POST['title'];
                $description = $_POST['description'];
                $category = $_POST['category'];
                $tags = ''; // Tags field removed from form
                $is_active = isset($_POST['is_active']);
                
                if (update_gallery_image($id, $title, $description, $category, $tags, $is_active)) {
                    header('Location: gallery.php?success=updated');
                    exit;
                }
                break;
                
            case 'delete':
                $id = $_POST['id'];
                if (delete_gallery_image($id)) {
                    header('Location: gallery.php?success=deleted');
                    exit;
                }
                break;
        }
    }
}

// Handle GET request for edit data
if (isset($_GET['action']) && $_GET['action'] === 'get' && isset($_GET['id'])) {
    $image_id = $_GET['id'];
    error_log("Fetching image data for ID: " . $image_id);
    
    $image = get_gallery_image_by_id($image_id);
    
    if ($image) {
        error_log("Image data found: " . print_r($image, true));
        header('Content-Type: application/json');
        echo json_encode($image);
    } else {
        error_log("No image found for ID: " . $image_id);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Image not found']);
    }
    exit;
}

// Get gallery data
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 12;
$offset = ($page - 1) * $per_page;
$category = isset($_GET['category']) ? $_GET['category'] : 'all';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

if ($search) {
    $gallery_images = search_gallery_images($search, $per_page, $offset);
    $total_images = get_search_gallery_count($search);
} else {
    $gallery_images = get_gallery_images($per_page, $offset, $category, false); // Show all including inactive
    $total_images = get_gallery_image_count($category, false);
}

$categories = get_gallery_categories();
$total_pages = ceil($total_images / $per_page);

// Debug: Show gallery data information
error_log("Gallery Images Count: " . count($gallery_images));
error_log("Total Images: " . $total_images);
error_log("Category: " . $category);
error_log("Search: " . $search);
if (!empty($gallery_images)) {
    error_log("First image data: " . print_r($gallery_images[0], true));
}

// Helper function to create thumbnails
function createThumbnail($source, $destination, $width, $height) {
    // Check if GD library is available
    if (!extension_loaded('gd') || !function_exists('imagecreatetruecolor')) {
        // If GD is not available, just copy the original file
        if (file_exists($source)) {
            return copy($source, $destination);
        }
        return false;
    }
    
    $info = getimagesize($source);
    if (!$info) return false;
    
    $type = $info[2];
    switch ($type) {
        case IMAGETYPE_JPEG:
            $source_image = imagecreatefromjpeg($source);
            break;
        case IMAGETYPE_PNG:
            $source_image = imagecreatefrompng($source);
            break;
        case IMAGETYPE_GIF:
            $source_image = imagecreatefromgif($source);
            break;
        default:
            // If image type is not supported, just copy the file
            return copy($source, $destination);
    }
    
    if (!$source_image) {
        // If image creation fails, just copy the file
        return copy($source, $destination);
    }
    
    $thumb = imagecreatetruecolor($width, $height);
    if (!$thumb) {
        imagedestroy($source_image);
        return copy($source, $destination);
    }
    
    // Handle transparency for PNG and GIF
    if ($type == IMAGETYPE_PNG || $type == IMAGETYPE_GIF) {
        imagealphablending($thumb, false);
        imagesavealpha($thumb, true);
        $transparent = imagecolorallocatealpha($thumb, 255, 255, 255, 127);
        imagefilledrectangle($thumb, 0, 0, $width, $height, $transparent);
    }
    
    imagecopyresampled($thumb, $source_image, 0, 0, 0, 0, $width, $height, $info[0], $info[1]);
    
    $result = false;
    switch ($type) {
        case IMAGETYPE_JPEG:
            $result = imagejpeg($thumb, $destination, 85);
            break;
        case IMAGETYPE_PNG:
            $result = imagepng($thumb, $destination, 8);
            break;
        case IMAGETYPE_GIF:
            $result = imagegif($thumb, $destination);
            break;
        default:
            $result = false;
    }
    
    imagedestroy($source_image);
    imagedestroy($thumb);
    
    // If thumbnail creation failed, copy the original file
    if (!$result && file_exists($source)) {
        return copy($source, $destination);
    }
    
    return $result;
}

require_once __DIR__ . '/../includes/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery Management - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .admin-container {
            display: flex;
            min-height: 100vh;
            background: var(--dark-bg);
        }
        
        .admin-sidebar {
            width: 250px;
            background: var(--dark-secondary);
            padding: 2rem 0;
            border-right: 1px solid var(--border-color);
            height: 100vh;
            overflow-y: auto;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            transition: transform 0.3s ease;
            display: block;
            visibility: visible;
        }
        
        .admin-logo {
            text-align: center;
            margin-bottom: 2rem;
            padding: 0 1rem;
        }
        
        .admin-logo h2 {
            color: var(--primary-color);
            font-size: 1.5rem;
            font-weight: 700;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }
        
        .admin-logo small {
            color: var(--text-muted);
            font-size: 0.9rem;
        }
        
        .admin-nav {
            list-style: none;
            padding: 0 1rem;
        }
        
        .admin-nav li {
            margin-bottom: 0.5rem;
        }
        
        .admin-nav a {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem 1.5rem;
            color: var(--text-primary);
            text-decoration: none;
            background: transparent;
            border-radius: 10px;
            transition: all 0.3s ease;
            font-weight: 500;
        }
        
        .admin-nav a:hover,
        .admin-nav a.active {
            background: var(--dark-tertiary);
            color: var(--primary-color);
            transform: translateX(5px);
        }
        
        .admin-nav i {
            width: 20px;
            text-align: center;
        }
        
        .admin-content {
            flex: 1;
            margin-left: 250px;
            padding: 2rem;
            padding-top: 5rem;
            background: var(--dark-bg);
            min-height: 100vh;
            display: block;
            visibility: visible;
        }
        
        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border-color);
        }
        
        .admin-header h1 {
            color: var(--text-primary);
            font-size: 2rem;
            font-weight: 700;
        }
        
        .admin-user {
            display: flex;
            align-items: center;
            gap: 1rem;
            color: var(--text-secondary);
            background: var(--dark-secondary);
            padding: 0.75rem 1.5rem;
            border-radius: 25px;
            border: 1px solid var(--border-color);
        }
        
        .gallery-controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .gallery-filters {
            display: flex;
            gap: 1rem;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .filter-select {
            background: var(--dark-secondary);
            color: var(--text-primary);
            border: 1px solid var(--border-color);
            padding: 0.75rem 1rem;
            border-radius: 10px;
            outline: none;
        }
        
        .search-form {
            display: flex;
            background: var(--dark-secondary);
            border-radius: 10px;
            border: 1px solid var(--border-color);
            overflow: hidden;
        }
        
        .search-input {
            background: transparent;
            border: none;
            padding: 0.75rem 1rem;
            color: var(--text-primary);
            outline: none;
            width: 200px;
        }
        
        .search-btn {
            background: var(--primary-color);
            border: none;
            padding: 0.75rem 1rem;
            color: var(--text-primary);
            cursor: pointer;
        }
        
        .btn {
            background: var(--primary-color);
            color: var(--text-primary);
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }
        
        .btn:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
        }
        
        .btn-danger {
            background: var(--error-color);
        }
        
        .btn-danger:hover {
            background: #c62828;
        }
        
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }
        
        .gallery-item {
            background: var(--dark-secondary);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: var(--shadow-lg);
            transition: all 0.3s ease;
            border: 1px solid var(--border-color);
        }
        
        .gallery-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(255, 107, 107, 0.2);
        }
        
        .gallery-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        
        .gallery-info {
            padding: 1.5rem;
        }
        
        .gallery-title {
            color: var(--text-primary);
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }
        
        .gallery-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .gallery-category {
            background: var(--primary-color);
            color: var(--text-primary);
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.8rem;
        }
        
        .gallery-status {
            color: var(--text-muted);
            font-size: 0.8rem;
        }
        
        .gallery-status.active {
            color: var(--success-color);
        }
        
        .gallery-status.inactive {
            color: var(--error-color);
        }
        
        .gallery-actions {
            display: flex;
            gap: 0.5rem;
        }
        
        .btn-sm {
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 1rem;
            margin-top: 2rem;
        }
        
        .pagination-btn {
            background: var(--dark-secondary);
            color: var(--text-primary);
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            text-decoration: none;
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
        }
        
        .pagination-btn:hover {
            background: var(--primary-color);
        }
        
        .alert {
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 2rem;
        }
        
        .alert-success {
            background: var(--success-color);
            color: white;
        }
        
        .modal {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.8);
        }
        
        .modal-content {
            position: relative;
            background: var(--dark-secondary);
            margin: 5% auto;
            padding: 2rem;
            width: 90%;
            max-width: 500px;
            border-radius: 15px;
            box-shadow: var(--shadow-xl);
        }
        
        .close-modal {
            position: absolute;
            right: 1rem;
            top: 1rem;
            color: var(--text-primary);
            font-size: 1.5rem;
            cursor: pointer;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
            font-weight: 500;
        }
        
        .form-control {
            width: 100%;
            background: var(--dark-tertiary);
            color: var(--text-primary);
            border: 1px solid var(--border-color);
            padding: 0.75rem 1rem;
            border-radius: 10px;
            outline: none;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
        }
        
        textarea.form-control {
            resize: vertical;
            min-height: 100px;
        }
        
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .checkbox-group input[type="checkbox"] {
            width: auto;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <div class="admin-logo">
                <h2><?php echo APP_NAME; ?></h2>
                <small>Admin Panel</small>
            </div>
            
            <?php require_once __DIR__ . '/../includes/admin-nav.php'; ?>
        </aside>
        
        <!-- Main Content -->
        <main class="admin-content">
            <div class="admin-header">
                <h1>Gallery Management</h1>
                <div class="admin-user">
                    <span>Welcome, <?php echo $_SESSION['username']; ?></span>
                    <i class="fas fa-user-circle"></i>
                </div>
            </div>
            
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success" id="success-message">
                    <?php
                    switch ($_GET['success']) {
                        case 'added':
                            echo 'Gallery image added successfully!';
                            break;
                        case 'updated':
                            echo 'Gallery image updated successfully!';
                            break;
                        case 'deleted':
                            echo 'Gallery image deleted successfully!';
                            break;
                    }
                    ?>
                </div>
                <script>
                    // Auto-hide success message after 4 seconds
                    setTimeout(function() {
                        const successMsg = document.getElementById('success-message');
                        if (successMsg) {
                            successMsg.style.transition = 'opacity 0.5s';
                            successMsg.style.opacity = '0';
                            setTimeout(function() {
                                successMsg.style.display = 'none';
                            }, 500);
                        }
                    }, 4000);
                </script>
            <?php endif; ?>
            
            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger" style="background: var(--error-color); color: white; padding: 1rem; border-radius: 10px; margin-bottom: 2rem;">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>
            
            <!-- Gallery Controls -->
            <div class="gallery-controls">
                <div class="gallery-filters">
                    <form method="GET" action="gallery.php" class="search-form">
                        <input type="text" name="search" placeholder="Search gallery..." value="<?php echo htmlspecialchars($search); ?>" class="search-input">
                        <button type="submit" class="search-btn">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                    
                    <form method="GET" action="gallery.php">
                        <select name="category" class="filter-select" onchange="this.form.submit()">
                            <option value="all" <?php echo $category === 'all' ? 'selected' : ''; ?>>All Categories</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat; ?>" <?php echo $category === $cat ? 'selected' : ''; ?>>
                                    <?php echo ucfirst($cat); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                </div>
                
                <button class="btn" onclick="openAddModal()">
                    <i class="fas fa-plus"></i> Add Image
                </button>
            </div>
            
            <!-- Gallery Grid -->
            <div class="gallery-grid">
                <?php 
                // Debug: Show if images are found
                error_log("Displaying gallery grid. Images count: " . count($gallery_images));
                
                if (!empty($gallery_images)): ?>
                    <?php foreach ($gallery_images as $image): ?>
                        <?php 
                        // Debug: Show individual image data
                        error_log("Displaying image: " . $image['title'] . " - URL: " . ($image['thumbnail_url'] ?: $image['image_url']));
                        ?>
                        <div class="gallery-item">
                            <?php 
                            $image_path = $image['thumbnail_url'] ?: $image['image_url'];
                            // Try different path variations
                            $possible_paths = [
                                $image_path,
                                '../' . $image_path,
                                APP_URL . '/' . $image_path
                            ];
                            
                            $display_path = $image_path; // default
                            foreach ($possible_paths as $path) {
                                if (file_exists($path)) {
                                    $display_path = $path;
                                    break;
                                }
                            }
                            
                            error_log("Image display paths - Original: $image_path, Display: $display_path");
                            ?>
                            <img src="<?php echo htmlspecialchars($display_path); ?>" 
                                 alt="<?php echo htmlspecialchars($image['title']); ?>"
                                 class="gallery-image"
                                 onerror="console.error('Image failed to load: <?php echo htmlspecialchars($display_path); ?>'); this.style.display='none';">
                            <div class="gallery-info">
                                <h3 class="gallery-title"><?php echo htmlspecialchars($image['title']); ?></h3>
                                <div class="gallery-meta">
                                    <span class="gallery-category"><?php echo ucfirst(htmlspecialchars($image['category'])); ?></span>
                                    <span class="gallery-status <?php echo $image['is_active'] ? 'active' : 'inactive'; ?>">
                                        <?php echo $image['is_active'] ? 'Active' : 'Inactive'; ?>
                                    </span>
                                </div>
                                <div class="gallery-actions">
                                    <button class="btn btn-sm" onclick="console.log('Edit button clicked for ID: <?php echo $image['id']; ?>'); openEditModal(<?php echo $image['id']; ?>)">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <button class="btn btn-danger btn-sm" onclick="deleteImage(<?php echo $image['id']; ?>)">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div style="grid-column: 1 / -1; text-align: center; padding: 3rem; color: var(--text-secondary);">
                        <i class="fas fa-images" style="font-size: 3rem; margin-bottom: 1rem;"></i>
                        <h3>No gallery images found</h3>
                        <p><?php echo $search ? 'Try adjusting your search terms' : 'Upload your first image to get started'; ?></p>
                        <p><small>Debug: Total images in database: <?php echo $total_images; ?></small></p>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>" class="pagination-btn">
                            <i class="fas fa-chevron-left"></i> Previous
                        </a>
                    <?php endif; ?>

                    <span>Page <?php echo $page; ?> of <?php echo $total_pages; ?></span>

                    <?php if ($page < $total_pages): ?>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>" class="pagination-btn">
                            Next <i class="fas fa-chevron-right"></i>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </main>
    </div>
    
    <!-- Add Image Modal -->
    <div id="addModal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal('addModal')">&times;</span>
            <h2>Add Gallery Image</h2>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add">
                
                <div class="form-group">
                    <label for="title">Title *</label>
                    <input type="text" id="title" name="title" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" class="form-control"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="category">Category *</label>
                    <select id="category" name="category" class="form-control" required>
                        <option value="">Select Category</option>
                        <option value="performances">Performances</option>
                        <option value="behind-scenes">Behind Scenes</option>
                        <option value="photoshoot">Photoshoot</option>
                        <option value="fans">Fans</option>
                        <option value="music-videos">Music Videos</option>
                        <option value="general">General</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="image">Image *</label>
                    <input type="file" id="image" name="image" class="form-control" accept="image/*" required>
                </div>
                
                <button type="submit" class="btn">Add Image</button>
            </form>
        </div>
    </div>
    
    <!-- Edit Image Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal('editModal')">&times;</span>
            <h2>Edit Gallery Image</h2>
            <form method="POST" id="editForm">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" id="editId">
                
                <div class="form-group">
                    <label for="editTitle">Title *</label>
                    <input type="text" id="editTitle" name="title" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="editDescription">Description</label>
                    <textarea id="editDescription" name="description" class="form-control"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="editCategory">Category *</label>
                    <select id="editCategory" name="category" class="form-control" required>
                        <option value="">Select Category</option>
                        <option value="performances">Performances</option>
                        <option value="behind-scenes">Behind Scenes</option>
                        <option value="photoshoot">Photoshoot</option>
                        <option value="fans">Fans</option>
                        <option value="music-videos">Music Videos</option>
                        <option value="general">General</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <div class="checkbox-group">
                        <input type="checkbox" id="editActive" name="is_active">
                        <label for="editActive">Active</label>
                    </div>
                </div>
                
                <button type="submit" class="btn">Update Image</button>
            </form>
        </div>
    </div>
    
    <script>
        function openAddModal() {
            document.getElementById('addModal').style.display = 'block';
        }
        
        function openEditModal(id) {
            console.log('Opening edit modal for ID:', id);
            fetch(`gallery.php?action=get&id=${id}`)
                .then(response => {
                    console.log('Response status:', response.status);
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Received data:', data);
                    if (data && data.id) {
                        document.getElementById('editId').value = data.id;
                        document.getElementById('editTitle').value = data.title || '';
                        document.getElementById('editDescription').value = data.description || '';
                        document.getElementById('editCategory').value = data.category || '';
                        document.getElementById('editActive').checked = data.is_active || false;
                        document.getElementById('editModal').style.display = 'block';
                    } else {
                        alert('Error: No data received for this image');
                    }
                })
                .catch(error => {
                    console.error('Error fetching image data:', error);
                    alert('Error loading image data. Please try again.');
                });
        }
        
        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }
        
        function deleteImage(id) {
            if (confirm('Are you sure you want to delete this image?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="${id}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }
        
        // Close modals when clicking outside
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
            }
        }
    </script>
</body>
</html>
