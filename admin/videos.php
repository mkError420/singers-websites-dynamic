<?php
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/functions.php';

// Start secure session and require login
start_secure_session();
require_login();

// Get all videos
$all_videos = fetchAll("SELECT * FROM videos ORDER BY created_at DESC");

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'delete') {
        $video_id = $_POST['video_id'] ?? 0;
        if ($video_id) {
            deleteData('videos', 'id = ?', [$video_id]);
            header('Location: videos.php?deleted=1');
            exit();
        }
    }
}

// Handle success messages
$success_message = '';
if (isset($_GET['added']) && $_GET['added'] == 1) {
    $success_message = 'Video added successfully!';
}
if (isset($_GET['updated']) && $_GET['updated'] == 1) {
    $success_message = 'Video updated successfully!';
}
if (isset($_GET['deleted']) && $_GET['deleted'] == 1) {
    $success_message = 'Video deleted successfully!';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Videos - Admin</title>
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
        
        .videos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
        }
        
        .video-card {
            background: var(--dark-secondary);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: var(--shadow-md);
            transition: transform 0.3s ease;
        }
        
        .video-card:hover {
            transform: translateY(-5px);
        }
        
        .video-thumbnail {
            width: 100%;
            height: 180px;
            object-fit: cover;
            background: var(--dark-tertiary);
        }
        
        .video-info {
            padding: 1.5rem;
        }
        
        .video-title {
            color: var(--text-primary);
            font-weight: 600;
            margin-bottom: 0.5rem;
            font-size: 1.1rem;
        }
        
        .video-description {
            color: var(--text-secondary);
            font-size: 0.9rem;
            margin-bottom: 1rem;
            line-height: 1.5;
        }
        
        .video-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            font-size: 0.8rem;
            color: var(--text-muted);
        }
        
        .video-actions {
            display: flex;
            gap: 0.5rem;
        }
        
        .btn-sm {
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-edit {
            background: var(--primary-color);
            color: var(--text-primary);
        }
        
        .btn-delete {
            background: var(--error-color);
            color: white;
        }
        
        .add-video-btn {
            background: var(--primary-color);
            color: var(--text-primary);
            padding: 1rem 2rem;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            display: inline-block;
            margin-bottom: 2rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .add-video-btn:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
        }
        
        .alert-success {
            background: var(--success-color);
            color: white;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 2rem;
        }
        
        .no-videos {
            text-align: center;
            padding: 3rem;
            color: var(--text-muted);
        }
        
        .video-type-badge {
            background: var(--dark-tertiary);
            color: var(--text-secondary);
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 500;
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
                    <li><a href="videos.php" class="active"><i class="fas fa-video"></i> Videos</a></li>
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
                <h1>Manage Videos</h1>
                <div class="admin-user">
                    <span>Welcome, <?php echo $_SESSION['username']; ?></span>
                    <i class="fas fa-user-circle"></i>
                </div>
            </div>
            
            <?php if ($success_message): ?>
                <div class="alert-success">
                    <?php echo $success_message; ?>
                </div>
            <?php endif; ?>
            
            <a href="add-video.php" class="add-video-btn">
                <i class="fas fa-plus"></i> Add New Video
            </a>
            
            <?php if (!empty($all_videos)): ?>
                <div class="videos-grid">
                    <?php foreach ($all_videos as $video): ?>
                        <div class="video-card">
                            <?php if ($video['thumbnail']): ?>
                                <img src="<?php echo APP_URL . '/' . $video['thumbnail']; ?>" 
                                     alt="<?php echo xss_clean($video['title']); ?>" 
                                     class="video-thumbnail">
                            <?php else: ?>
                                <div class="video-thumbnail">
                                    <i class="fas fa-video" style="font-size: 3rem; color: var(--text-muted);"></i>
                                </div>
                            <?php endif; ?>
                            
                            <div class="video-info">
                                <h3 class="video-title">
                                    <?php echo xss_clean($video['title']); ?>
                                    <?php if ($video['is_active']): ?>
                                        <span style="color: var(--success-color);">●</span>
                                    <?php else: ?>
                                        <span style="color: var(--text-muted);">○</span>
                                    <?php endif; ?>
                                </h3>
                                
                                <div class="video-meta">
                                    <span class="video-type-badge"><?php echo ucfirst($video['video_type']); ?></span>
                                    <span><?php echo format_date($video['created_at'], 'M j, Y'); ?></span>
                                </div>
                                
                                <p class="video-description">
                                    <?php echo truncate_text(xss_clean($video['description']), 100); ?>
                                </p>
                                
                                <div class="video-actions">
                                    <a href="edit-video.php?id=<?php echo $video['id']; ?>" class="btn-sm btn-edit">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="video_id" value="<?php echo $video['id']; ?>">
                                        <button type="submit" class="btn-sm btn-delete" onclick="return confirm('Are you sure you want to delete this video?');">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="no-videos">
                    <h3>No videos found</h3>
                    <p>Start by adding your first video!</p>
                    <a href="add-video.php" class="add-video-btn">
                        <i class="fas fa-plus"></i> Add Your First Video
                    </a>
                </div>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>
