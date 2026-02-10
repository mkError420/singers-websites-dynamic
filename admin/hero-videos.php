<?php
// Start session and check login
session_start();

// Simple authentication check
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Include database
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/functions.php';

// Debug: Check if table exists
try {
    $table_check = fetchOne("SHOW TABLES LIKE 'hero_videos'");
    if (!$table_check) {
        // Create table if it doesn't exist
        $create_table_sql = "
        CREATE TABLE IF NOT EXISTS hero_videos (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(200) NOT NULL,
            video_url VARCHAR(500) NOT NULL,
            video_type ENUM('youtube', 'vimeo', 'uploaded') DEFAULT 'youtube',
            thumbnail VARCHAR(255),
            is_active BOOLEAN DEFAULT TRUE,
            display_order INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";
        
        if (executeQuery($create_table_sql)) {
            $success = 'Hero videos table created successfully!';
        } else {
            $error = 'Failed to create hero videos table.';
        }
    }
} catch (Exception $e) {
    $error = 'Database error: ' . $e->getMessage();
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add_hero_video') {
        // Debug: Show what was submitted
        error_log("Form submission received: " . print_r($_POST, true));
        error_log("Files submitted: " . print_r($_FILES, true));
        
        $title = sanitize_input($_POST['title']);
        $video_url = sanitize_input($_POST['video_url']);
        $video_type = sanitize_input($_POST['video_type']);
        $thumbnail = sanitize_input($_POST['thumbnail'] ?? '');
        $display_order = intval($_POST['display_order'] ?? 0);
        
        if (empty($title)) {
            $error = 'Title is required.';
        } elseif ($video_type !== 'uploaded' && empty($video_url)) {
            $error = 'Video URL is required for YouTube/Vimeo videos.';
        } else {
            // Handle video file upload
            if ($video_type === 'uploaded' && isset($_FILES['video_file']) && $_FILES['video_file']['error'] === UPLOAD_ERR_OK) {
                // Create uploads/videos directory if it doesn't exist
                $upload_dir = __DIR__ . '/../uploads/videos/';
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                
                $upload_result = upload_file($_FILES['video_file'], 'uploads/videos/', ['mp4', 'webm', 'ogg', 'mov']);
                if ($upload_result['success']) {
                    $video_url = $upload_result['file_path'];
                    error_log("Video uploaded successfully: " . $video_url);
                } else {
                    $error = 'Failed to upload video: ' . $upload_result['message'];
                }
            }
            
            // Handle thumbnail upload
            $thumbnail_path = '';
            if (isset($_FILES['thumbnail_file']) && $_FILES['thumbnail_file']['error'] === UPLOAD_ERR_OK) {
                // Create uploads/hero directory if it doesn't exist
                $upload_dir = __DIR__ . '/../uploads/hero/';
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                
                $upload_result = upload_file($_FILES['thumbnail_file'], 'uploads/hero/', ['jpg', 'jpeg', 'png', 'gif']);
                if ($upload_result['success']) {
                    $thumbnail_path = $upload_result['filepath'];
                } else {
                    $error = 'Failed to upload thumbnail: ' . $upload_result['message'];
                }
            }
            
            if (empty($error)) {
                $video_data = [
                    'title' => $title,
                    'video_url' => $video_url,
                    'video_type' => $video_type,
                    'thumbnail' => $thumbnail_path ?: $thumbnail,
                    'display_order' => $display_order
                ];
                
                error_log("Attempting to insert: " . print_r($video_data, true));
                
                if (insertData('hero_videos', $video_data)) {
                    $success = 'Hero video added successfully!';
                    error_log("Insert successful");
                } else {
                    $error = 'Failed to add hero video. Please check database connection.';
                    error_log("Insert failed");
                }
            }
        }
    }
    
    if ($action === 'update_hero_video') {
        $video_id = intval($_POST['video_id']);
        $title = sanitize_input($_POST['title']);
        $video_url = sanitize_input($_POST['video_url']);
        $video_type = sanitize_input($_POST['video_type']);
        $thumbnail = sanitize_input($_POST['thumbnail'] ?? '');
        $display_order = intval($_POST['display_order'] ?? 0);
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        
        error_log("Updating hero video ID: " . $video_id);
        error_log("Update data: " . print_r($_POST, true));
        
        if (empty($title)) {
            $error = 'Title is required.';
        } elseif ($video_type !== 'uploaded' && empty($video_url)) {
            $error = 'Video URL is required for YouTube/Vimeo videos.';
        } else {
            // Handle video file upload for type 'uploaded'
            if ($video_type === 'uploaded' && isset($_FILES['video_file']) && $_FILES['video_file']['error'] === UPLOAD_ERR_OK) {
                // Create uploads/videos directory if it doesn't exist
                $upload_dir = __DIR__ . '/../uploads/videos/';
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                
                $upload_result = upload_file($_FILES['video_file'], 'uploads/videos/', ['mp4', 'webm', 'ogg', 'mov']);
                if ($upload_result['success']) {
                    $video_url = $upload_result['filepath'];
                    error_log("Video uploaded successfully: " . $video_url);
                } else {
                    $error = 'Failed to upload video: ' . $upload_result['message'];
                }
            }
            
            // Handle thumbnail upload
            $thumbnail_path = $thumbnail;
            if (isset($_FILES['thumbnail_file']) && $_FILES['thumbnail_file']['error'] === UPLOAD_ERR_OK) {
                // Create uploads/hero directory if it doesn't exist
                $upload_dir = __DIR__ . '/../uploads/hero/';
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                
                $upload_result = upload_file($_FILES['thumbnail_file'], 'uploads/hero/', ['jpg', 'jpeg', 'png', 'gif']);
                if ($upload_result['success']) {
                    $thumbnail_path = $upload_result['filepath'];
                    error_log("Thumbnail uploaded successfully: " . $thumbnail_path);
                } else {
                    $error = 'Failed to upload thumbnail: ' . $upload_result['message'];
                }
            }
            
            if (empty($error)) {
                $video_data = [
                    'title' => $title,
                    'video_url' => $video_url,
                    'video_type' => $video_type,
                    'thumbnail' => $thumbnail_path,
                    'display_order' => $display_order,
                    'is_active' => $is_active
                ];
                
                error_log("Attempting to update with data: " . print_r($video_data, true));
                
                // Try multiple update methods
                $updated = false;
                $error_msg = '';
                
                // Method 1: Using updateData function
                if (updateData('hero_videos', $video_data, 'id = ?', [$video_id])) {
                    $updated = true;
                    error_log("Hero video updated successfully using updateData function");
                } else {
                    $error_msg = "updateData function failed";
                    error_log("updateData function failed");
                    
                    // Method 2: Direct SQL execution
                    try {
                        $db = new Database();
                        $conn = $db->getConnection();
                        
                        $setClauses = [];
                        $values = [];
                        
                        foreach ($video_data as $column => $value) {
                            $setClauses[] = "$column = ?";
                            $values[] = $value;
                        }
                        $values[] = $video_id;
                        
                        $setClause = implode(', ', $setClauses);
                        $sql = "UPDATE hero_videos SET $setClause WHERE id = ?";
                        
                        $stmt = $conn->prepare($sql);
                        $result = $stmt->execute($values);
                        $affected = $stmt->rowCount();
                        
                        if ($affected > 0) {
                            $updated = true;
                            error_log("Hero video updated successfully using direct SQL");
                        } else {
                            $error_msg = "Direct SQL failed - no rows affected";
                            error_log("Direct SQL failed - no rows affected");
                        }
                    } catch (Exception $e) {
                        $error_msg = "Direct SQL exception: " . $e->getMessage();
                        error_log("Direct SQL exception: " . $e->getMessage());
                    }
                }
                
                if ($updated) {
                    $success = 'Hero video updated successfully!';
                    error_log("Hero video updated successfully from database");
                    
                    // Redirect to refresh the page and show success message
                    header('Location: hero-videos.php?updated=1');
                    exit();
                } else {
                    $error = 'Failed to update hero video from database. ' . $error_msg;
                    error_log("Failed to update hero video from database: " . $error_msg);
                }
            }
        }
    }
    
    if ($action === 'delete_hero_video') {
        $video_id = intval($_POST['video_id']);
        
        error_log("Attempting to delete hero video ID: " . $video_id);
        
        // Get video info to delete thumbnail file
        $video = fetchOne("SELECT thumbnail, video_url FROM hero_videos WHERE id = ?", [$video_id]);
        
        if ($video) {
            error_log("Video found: " . print_r($video, true));
            
            // Delete thumbnail file if exists
            if ($video['thumbnail'] && file_exists(__DIR__ . '/../' . $video['thumbnail'])) {
                unlink(__DIR__ . '/../' . $video['thumbnail']);
                error_log("Thumbnail deleted: " . $video['thumbnail']);
            }
            
            // Delete video file if it's an uploaded video
            if ($video['video_url'] && strpos($video['video_url'], 'uploads/') === 0) {
                $video_path = __DIR__ . '/../' . $video['video_url'];
                if (file_exists($video_path)) {
                    unlink($video_path);
                    error_log("Video file deleted: " . $video['video_url']);
                }
            }
        }
        
        // Try multiple deletion methods
        $deleted = false;
        $error_msg = '';
        
        // Method 1: Using deleteData function
        if (deleteData('hero_videos', 'id = ?', [$video_id])) {
            $deleted = true;
            error_log("Hero video deleted successfully using deleteData function");
        } else {
            $error_msg = "deleteData function failed";
            error_log("deleteData function failed");
            
            // Method 2: Direct SQL execution
            try {
                $db = new Database();
                $conn = $db->getConnection();
                $stmt = $conn->prepare("DELETE FROM hero_videos WHERE id = ?");
                $result = $stmt->execute([$video_id]);
                $affected = $stmt->rowCount();
                
                if ($affected > 0) {
                    $deleted = true;
                    error_log("Hero video deleted successfully using direct SQL");
                } else {
                    $error_msg = "Direct SQL failed - no rows affected";
                    error_log("Direct SQL failed - no rows affected");
                }
            } catch (Exception $e) {
                $error_msg = "Direct SQL exception: " . $e->getMessage();
                error_log("Direct SQL exception: " . $e->getMessage());
            }
        }
        
        if ($deleted) {
            $success = 'Hero video deleted successfully!';
            error_log("Hero video deleted successfully from database");
            
            // Redirect to refresh the page and show success message
            header('Location: hero-videos.php?deleted=1');
            exit();
        } else {
            $error = 'Failed to delete hero video from database. ' . $error_msg;
            error_log("Failed to delete hero video from database: " . $error_msg);
        }
    }
    
    if ($action === 'toggle_hero_video') {
        $video_id = intval($_POST['video_id']);
        $current_status = fetchOne("SELECT is_active FROM hero_videos WHERE id = ?", [$video_id]);
        
        if ($current_status) {
            $new_status = $current_status['is_active'] ? 0 : 1;
            if (updateData('hero_videos', ['is_active' => $new_status], 'id = ?', [$video_id])) {
                $success = 'Hero video status updated successfully!';
            } else {
                $error = 'Failed to update hero video status.';
            }
        }
    }
}

// Handle AJAX request for getting video data
if (isset($_GET['action']) && $_GET['action'] === 'get_video' && isset($_GET['id'])) {
    $video_id = intval($_GET['id']);
    $video = fetchOne("SELECT * FROM hero_videos WHERE id = ?", [$video_id]);
    
    if ($video) {
        header('Content-Type: application/json');
        echo json_encode($video);
        exit();
    } else {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Video not found']);
        exit();
    }
}

// Handle success messages from URL parameters
if (isset($_GET['deleted']) && $_GET['deleted'] == 1) {
    $success = 'Hero video deleted successfully!';
}
if (isset($_GET['toggled']) && $_GET['toggled'] == 1) {
    $success = 'Hero video status updated successfully!';
}
if (isset($_GET['updated']) && $_GET['updated'] == 1) {
    $success = 'Hero video updated successfully!';
}

// Get all hero videos
try {
    $hero_videos = fetchAll("SELECT * FROM hero_videos ORDER BY display_order ASC, created_at DESC");
} catch (Exception $e) {
    $error = 'Error fetching hero videos: ' . $e->getMessage();
    $hero_videos = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hero Videos - Admin</title>
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .admin-container {
            display: flex;
            min-height: 100vh;
        }
        
        .admin-sidebar {
            width: 250px;
            background: var(--dark-secondary);
            padding: 2rem 0;
            border-right: 1px solid var(--border-color);
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            z-index: 1000;
        }
        
        .admin-logo {
            text-align: center;
            margin-bottom: 2rem;
            padding: 0 1rem;
        }
        
        .admin-logo h2 {
            color: var(--primary-color);
            font-size: 1.5rem;
        }
        
        .admin-nav {
            list-style: none;
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
            transition: background 0.3s ease;
        }
        
        .admin-nav a:hover,
        .admin-nav a.active {
            background: var(--dark-tertiary);
            color: #000000;
        }
        
        .admin-content {
            flex: 1;
            margin-left: 250px;
            padding: 2rem;
            background: transparent;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        
        .admin-header h1 {
            color: var(--text-primary);
            font-size: 1.8rem;
            font-weight: 700;
        }
        
        .admin-user {
            display: flex;
            align-items: center;
            gap: 1rem;
            color: var(--text-secondary);
            font-size: 1rem;
            padding: 0.5rem 1rem;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .hero-videos-container {
            background: var(--dark-secondary);
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            max-width: 1200px;
            margin: 0 auto;
            flex: 1;
        }
        
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .section-title {
            color: var(--text-primary);
            font-size: 1.5rem;
            font-weight: 600;
        }
        
        .btn-add {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            white-space: nowrap;
        }
        
        .btn-add:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
        }
        
        .hero-videos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 1.5rem;
        }
        
        .hero-video-item {
            background: var(--dark-tertiary);
            border-radius: 12px;
            padding: 1.5rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }
        
        .hero-video-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.4);
        }
        
        .video-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 1rem;
        }
        
        .video-info h3 {
            color: var(--text-primary);
            margin-bottom: 0.5rem;
            font-size: 1.1rem;
        }
        
        .video-meta {
            color: var(--text-secondary);
            font-size: 0.9rem;
            line-height: 1.4;
        }
        
        .video-preview {
            background: rgba(0, 0, 0, 0.3);
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
            min-height: 120px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        
        .video-preview img {
            max-width: 100%;
            max-height: 150px;
            border-radius: 8px;
            object-fit: cover;
        }
        
        .video-actions {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        
        .btn-sm {
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
            cursor: pointer;
            border: none;
            transition: all 0.3s ease;
        }
        
        .btn-edit {
            background: var(--warning-color);
            color: white;
        }
        
        .btn-delete {
            background: var(--error-color);
            color: white;
        }
        
        .btn-toggle {
            background: var(--text-muted);
            color: white;
        }
        
        .btn-toggle.active {
            background: var(--success-color);
        }
        
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(5px);
        }
        
        .modal-content {
            background: var(--dark-secondary);
            margin: 5% auto;
            padding: 2rem;
            border-radius: 15px;
            width: 90%;
            max-width: 600px;
            position: relative;
            max-height: 90vh;
            overflow-y: auto;
        }
        
        .close-modal {
            position: absolute;
            top: 1rem;
            right: 1rem;
            color: var(--text-muted);
            font-size: 1.5rem;
            cursor: pointer;
            z-index: 1001;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text-primary);
            font-weight: 600;
        }
        
        .form-control {
            width: 100%;
            padding: 0.75rem;
            background: var(--dark-tertiary);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            color: var(--text-primary);
            font-size: 1rem;
            box-sizing: border-box;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(255, 107, 107, 0.2);
        }
        
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }
        
        .alert-success {
            background: var(--success-color);
            color: white;
        }
        
        .alert-error {
            background: var(--error-color);
            color: white;
        }
        
        .no-videos {
            text-align: center;
            padding: 3rem;
            color: var(--text-muted);
        }
        
        .no-videos h3 {
            margin-bottom: 1rem;
            color: var(--text-primary);
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .admin-sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            
            .admin-sidebar.show {
                transform: translateX(0);
            }
            
            .admin-content {
                margin-left: 0;
                padding: 1rem;
            }
            
            .hero-videos-grid {
                grid-template-columns: 1fr;
            }
            
            .section-header {
                flex-direction: column;
                align-items: stretch;
                text-align: center;
            }
            
            .btn-add {
                width: 100%;
            }
        }
        
        @media (max-width: 480px) {
            .hero-videos-container {
                padding: 1rem;
            }
            
            .video-actions {
                justify-content: center;
            }
            
            .btn-sm {
                flex: 1;
                text-align: center;
            }
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
            
            <nav>
                <ul class="admin-nav">
                    <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="songs.php"><i class="fas fa-music"></i> Songs</a></li>
                    <li><a href="videos.php"><i class="fas fa-video"></i> Videos</a></li>
                    <li><a href="tour.php"><i class="fas fa-calendar-alt"></i> Tour Dates</a></li>
                    <li><a href="messages.php"><i class="fas fa-envelope"></i> Messages</a></li>
                    <li><a href="subscribers.php"><i class="fas fa-users"></i> Subscribers</a></li>
                    <li><a href="hero-videos.php" class="active"><i class="fas fa-film"></i> Hero Videos</a></li>
                    <li><a href="settings.php"><i class="fas fa-cog"></i> Settings</a></li>
                    <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </nav>
        </aside>
        
        <!-- Main Content -->
        <main class="admin-content">
            <div class="admin-header">
                <h1>Hero Videos</h1>
                <div class="admin-user">
                    <span>Welcome, <?php echo $_SESSION['username']; ?></span>
                    <i class="fas fa-user-circle"></i>
                </div>
            </div>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-error">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($success)): ?>
                <div class="alert alert-success" id="success-message">
                    <?php echo $success; ?>
                </div>
                <script>
                    // Auto-hide success message after 4 seconds
                    setTimeout(function() {
                        const successMessage = document.getElementById('success-message');
                        if (successMessage) {
                            successMessage.style.transition = 'opacity 0.5s ease';
                            successMessage.style.opacity = '0';
                            setTimeout(function() {
                                successMessage.remove();
                            }, 500);
                        }
                    }, 4000);
                </script>
            <?php endif; ?>
            
            <div class="hero-videos-container">
                <div class="section-header">
                    <h2 class="section-title">Manage Hero Background Videos</h2>
                    <button class="btn-add" onclick="openAddModal()">
                        <i class="fas fa-plus"></i> Add Hero Video
                    </button>
                </div>
                
                <?php if (!empty($hero_videos)): ?>
                    <div class="hero-videos-grid">
                        <?php foreach ($hero_videos as $video): ?>
                            <div class="hero-video-item">
                                <div class="video-header">
                                    <div class="video-info">
                                        <h3><?php echo xss_clean($video['title']); ?></h3>
                                        <div class="video-meta">
                                            Type: <?php echo ucfirst($video['video_type']); ?><br>
                                            Order: <?php echo $video['display_order']; ?><br>
                                            Status: <?php echo $video['is_active'] ? 'Active' : 'Inactive'; ?>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="video-preview">
                                    <?php if ($video['thumbnail']): ?>
                                        <img src="<?php echo APP_URL . '/' . $video['thumbnail']; ?>" 
                                             alt="<?php echo xss_clean($video['title']); ?>"
                                             style="max-width: 100%; max-height: 150px; border-radius: 8px;">
                                    <?php else: ?>
                                        <div style="color: var(--text-muted);">
                                            <i class="fas fa-video fa-3x"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="video-actions">
                                    <button class="btn-sm btn-edit" onclick="openEditModal(<?php echo $video['id']; ?>)">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <button class="btn-sm btn-toggle <?php echo $video['is_active'] ? 'active' : ''; ?>" 
                                            onclick="toggleStatus(<?php echo $video['id']; ?>)">
                                        <i class="fas fa-power-off"></i> <?php echo $video['is_active'] ? 'Active' : 'Inactive'; ?>
                                    </button>
                                    <button class="btn-sm btn-delete" onclick="deleteVideo(<?php echo $video['id']; ?>)">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="no-videos">
                        <h3>No Hero Videos Found</h3>
                        <p>Add your first hero background video to get started.</p>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
    
    <!-- Add/Edit Modal -->
    <div id="videoModal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal()">&times;</span>
            <h2 id="modalTitle">Add Hero Video</h2>
            
            <form id="videoForm" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" id="formAction" value="add_hero_video">
                <input type="hidden" name="video_id" id="videoId">
                
                <div class="form-group">
                    <label for="title">Title *</label>
                    <input type="text" id="title" name="title" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="video_type">Video Type</label>
                    <select id="video_type" name="video_type" class="form-control" onchange="toggleVideoFields()">
                        <option value="youtube">YouTube</option>
                        <option value="vimeo">Vimeo</option>
                        <option value="uploaded">Uploaded File</option>
                    </select>
                </div>
                
                <div class="form-group" id="video_url_group">
                    <label for="video_url">Video URL *</label>
                    <input type="text" id="video_url" name="video_url" class="form-control" 
                           placeholder="YouTube URL or Vimeo URL" required>
                </div>
                
                <div class="form-group" id="video_file_group" style="display: none;">
                    <label for="video_file">Upload Video File *</label>
                    <input type="file" id="video_file" name="video_file" class="form-control" 
                           accept="video/*">
                    <small style="color: var(--text-muted);">Supported formats: MP4, WebM, OGG (Max 100MB)</small>
                </div>
                
                <div class="form-group">
                    <label for="thumbnail_file">Thumbnail Image</label>
                    <input type="file" id="thumbnail_file" name="thumbnail_file" class="form-control" 
                           accept="image/*">
                    <small style="color: var(--text-muted);">Optional: Upload thumbnail image</small>
                </div>
                
                <div class="form-group">
                    <label for="display_order">Display Order</label>
                    <input type="number" id="display_order" name="display_order" class="form-control" 
                           value="0" min="0">
                    <small style="color: var(--text-muted);">Lower numbers appear first</small>
                </div>
                
                <div class="form-group">
                    <div class="checkbox-group">
                        <input type="checkbox" id="is_active" name="is_active" checked>
                        <label for="is_active">Active (show on homepage)</label>
                    </div>
                </div>
                
                <button type="submit" class="btn-add" style="width: 100%;">
                    <i class="fas fa-save"></i> Save Hero Video
                </button>
            </form>
        </div>
    </div>
    
    <script>
        function toggleVideoFields() {
            const videoType = document.getElementById('video_type').value;
            const urlGroup = document.getElementById('video_url_group');
            const fileGroup = document.getElementById('video_file_group');
            const urlInput = document.getElementById('video_url');
            
            if (videoType === 'uploaded') {
                urlGroup.style.display = 'none';
                fileGroup.style.display = 'block';
                urlInput.removeAttribute('required');
            } else {
                urlGroup.style.display = 'block';
                fileGroup.style.display = 'none';
                urlInput.setAttribute('required', 'required');
            }
        }
        
        function openAddModal() {
            document.getElementById('modalTitle').textContent = 'Add Hero Video';
            document.getElementById('formAction').value = 'add_hero_video';
            document.getElementById('videoId').value = '';
            document.getElementById('videoForm').reset();
            toggleVideoFields(); // Reset field visibility
            document.getElementById('videoModal').style.display = 'block';
        }
        
        function openEditModal(videoId) {
            console.log('Opening edit modal for video ID:', videoId);
            
            // Fetch video data and populate form
            fetch('hero-videos.php?action=get_video&id=' + videoId)
                .then(response => {
                    console.log('Response status:', response.status);
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Video data received:', data);
                    
                    if (data.error) {
                        alert('Error: ' + data.error);
                        return;
                    }
                    
                    // Populate form fields
                    document.getElementById('modalTitle').textContent = 'Edit Hero Video';
                    document.getElementById('formAction').value = 'update_hero_video';
                    document.getElementById('videoId').value = data.id;
                    document.getElementById('title').value = data.title || '';
                    document.getElementById('video_url').value = data.video_url || '';
                    document.getElementById('video_type').value = data.video_type || 'youtube';
                    document.getElementById('display_order').value = data.display_order || 0;
                    document.getElementById('is_active').checked = data.is_active == 1;
                    
                    // Update field visibility based on video type
                    toggleVideoFields();
                    
                    // Show modal
                    document.getElementById('videoModal').style.display = 'block';
                })
                .catch(error => {
                    console.error('Error fetching video data:', error);
                    alert('Failed to load video data. Please try again.');
                });
        }
        
        function closeModal() {
            document.getElementById('videoModal').style.display = 'none';
        }
        
        function toggleStatus(videoId) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.innerHTML = `
                <input type="hidden" name="action" value="toggle_hero_video">
                <input type="hidden" name="video_id" value="${videoId}">
            `;
            document.body.appendChild(form);
            form.submit();
        }
        
        function deleteVideo(videoId) {
            if (confirm('Are you sure you want to delete this hero video?')) {
                // Show loading state
                const deleteBtn = event.target;
                const originalHTML = deleteBtn.innerHTML;
                deleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Deleting...';
                deleteBtn.disabled = true;
                
                // Create and submit form
                const form = document.createElement('form');
                form.method = 'POST';
                form.style.display = 'none';
                form.innerHTML = `
                    <input type="hidden" name="action" value="delete_hero_video">
                    <input type="hidden" name="video_id" value="${videoId}">
                `;
                document.body.appendChild(form);
                
                // Submit form
                form.submit();
            }
        }
        
        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('videoModal');
            if (event.target == modal) {
                closeModal();
            }
        }
        
        // Initialize field visibility on page load
        document.addEventListener('DOMContentLoaded', function() {
            toggleVideoFields();
        });
    </script>
</body>
</html>
