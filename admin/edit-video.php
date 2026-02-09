<?php
// Start session and check login first
session_start();

// Include functions for sanitize_input
require_once __DIR__ . '/../includes/functions.php';

// Simple authentication check
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Get video ID from URL
$video_id = $_GET['id'] ?? 0;

if (!$video_id) {
    header('Location: videos.php');
    exit();
}

// Handle form submission BEFORE any HTML output
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitize_input($_POST['title'] ?? '');
    $description = sanitize_input($_POST['description'] ?? '');
    $video_url = sanitize_input($_POST['video_url'] ?? '');
    $video_type = sanitize_input($_POST['video_type'] ?? 'youtube');
    $category_name = sanitize_input($_POST['category_name'] ?? '');
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    $error = '';
    
    // Handle thumbnail upload
    $thumbnail = $video['thumbnail']; // Always show existing thumbnail by default
    $thumbnail_changed = false; // Track if thumbnail was changed
    
    if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
        $upload_result = upload_file($_FILES['thumbnail'], __DIR__ . '/../uploads/thumbnails/', ['jpg', 'jpeg', 'png', 'gif']);
        if ($upload_result['success']) {
            $thumbnail = 'uploads/thumbnails/' . $upload_result['filename']; // Use NEW thumbnail if uploaded successfully
            $thumbnail_changed = true; // Mark that thumbnail was changed
        } else {
            $error = $upload_result['message'];
        }
    }
    
    if (empty($error) && !empty($title) && !empty($video_url)) {
        require_once __DIR__ . '/../includes/database.php';
        
        $video_data = [
            'title' => $title,
            'description' => $description,
            'video_url' => $video_url,
            'video_type' => $video_type,
            'category_name' => $category_name,
            'thumbnail' => $thumbnail,
            'is_active' => $is_active
        ];
        
        if (updateData('videos', $video_data, 'id = ?', [$video_id])) {
            header('Location: videos.php?updated=1');
            exit();
        } else {
            $error = 'Failed to update video. Please try again.';
        }
    }
}

$page_title = 'Edit Video';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/database.php';

// Get existing video data
$video = fetchOne("SELECT * FROM videos WHERE id = ?", [$video_id]);

if (!$video) {
    header('Location: videos.php');
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Video - Admin</title>
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
            color: var(--primary-color);
        }
        
        .admin-content {
            flex: 1;
            padding: 2rem;
            background: var(--dark-bg);
        }
        
        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        
        .admin-header h1 {
            color: var(--text-primary);
        }
        
        .admin-user {
            display: flex;
            align-items: center;
            gap: 1rem;
            color: var(--text-secondary);
        }
        
        .video-form {
            background: var(--dark-secondary);
            padding: 2rem;
            border-radius: 15px;
            box-shadow: var(--shadow-md);
            max-width: 800px;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--text-primary);
        }
        
        .form-control {
            width: 100%;
            padding: 1rem;
            background: var(--dark-tertiary);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            color: var(--text-primary);
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(255, 107, 107, 0.1);
        }
        
        textarea.form-control {
            resize: vertical;
            min-height: 120px;
        }
        
        .file-upload {
            margin-bottom: 1rem;
        }
        
        .file-upload input[type="file"] {
            display: none;
        }
        
        .file-upload-label {
            display: block;
            padding: 1rem;
            background: var(--dark-tertiary);
            border: 2px dashed var(--border-color);
            border-radius: 8px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .file-upload-label:hover {
            border-color: var(--primary-color);
            background: rgba(255, 107, 107, 0.1);
        }
        
        .current-file {
            background: var(--dark-tertiary);
            padding: 1rem;
            border-radius: 8px;
            margin-top: 0.5rem;
            font-size: 0.9rem;
            color: var(--text-secondary);
        }
        
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .checkbox-group input[type="checkbox"] {
            width: 20px;
            height: 20px;
        }
        
        .radio-group {
            display: flex;
            gap: 2rem;
            margin-top: 0.5rem;
        }
        
        .radio-group label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
        }
        
        .btn-submit {
            background: var(--primary-color);
            color: var(--text-primary);
            padding: 1rem 2rem;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-right: 1rem;
        }
        
        .btn-submit:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
        }
        
        .btn-cancel {
            background: var(--text-muted);
            color: var(--text-primary);
            padding: 1rem 2rem;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-cancel:hover {
            background: var(--error-color);
            transform: translateY(-2px);
        }
        
        .alert-error {
            background: var(--error-color);
            color: white;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 2rem;
        }
        
        .form-actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }
        
        .help-text {
            font-size: 0.9rem;
            color: var(--text-muted);
            margin-top: 0.5rem;
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
                    <li><a href="settings.php"><i class="fas fa-cog"></i> Settings</a></li>
                    <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </nav>
        </aside>
        
        <!-- Main Content -->
        <main class="admin-content">
            <div class="admin-header">
                <h1>Edit Video</h1>
                <div class="admin-user">
                    <span>Welcome, <?php echo $_SESSION['username']; ?></span>
                    <i class="fas fa-user-circle"></i>
                </div>
            </div>
            
            <?php if (isset($error)): ?>
                <div class="alert-error">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" enctype="multipart/form-data" class="video-form">
                <div class="form-group">
                    <label for="title">Video Title *</label>
                    <input type="text" id="title" name="title" class="form-control" required
                           value="<?php echo xss_clean($video['title']); ?>">
                </div>
                
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" class="form-control" 
                              placeholder="Enter video description..."><?php echo xss_clean($video['description']); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="video_url">Video URL *</label>
                    <input type="url" id="video_url" name="video_url" class="form-control" 
                           value="<?php echo xss_clean($video['video_url']); ?>" required>
                    <div class="help-text">
                        <strong>YouTube:</strong> https://www.youtube.com/watch?v=VIDEO_ID<br>
                        <strong>Vimeo:</strong> https://vimeo.com/VIDEO_ID
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Video Type</label>
                    <div class="radio-group">
                        <label>
                            <input type="radio" name="video_type" value="youtube" <?php echo $video['video_type'] === 'youtube' ? 'checked' : ''; ?>>
                            YouTube
                        </label>
                        <label>
                            <input type="radio" name="video_type" value="vimeo" <?php echo $video['video_type'] === 'vimeo' ? 'checked' : ''; ?>>
                            Vimeo
                        </label>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="category_name">Category</label>
                    <input type="text" id="category_name" name="category_name" class="form-control" 
                           placeholder="Enter category name (e.g., Music Videos, Live Performance, etc.)"
                           value="<?php echo isset($_POST['category_name']) ? htmlspecialchars($_POST['category_name']) : ($video['category_name'] ?? ''); ?>">
                    <small>Enter a custom category name for this video</small>
                </div>
                
                <div class="form-group file-upload">
                    <label for="thumbnail">Thumbnail Image</label>
                    <input type="file" id="thumbnail" name="thumbnail" accept="image/*">
                    <label for="thumbnail" class="file-upload-label">
                        <i class="fas fa-image"></i>
                        <span>Choose New Thumbnail (optional)</span>
                        <small>JPG, PNG, GIF (Max: <?php echo MAX_FILE_SIZE / 1024 / 1024; ?>MB)</small>
                    </label>
                    <?php if ($video['thumbnail']): ?>
                        <div class="current-file">
                            <strong>Current:</strong> <?php echo xss_clean($video['thumbnail']); ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <div class="checkbox-group">
                        <input type="checkbox" id="is_active" name="is_active" <?php echo $video['is_active'] ? 'checked' : ''; ?>>
                        <label for="is_active">Active (show on website)</label>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn-submit">
                        <i class="fas fa-save"></i> Update Video
                    </button>
                    <a href="videos.php" class="btn-cancel">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </main>
    </div>
</body>
</html>
