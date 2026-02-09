<?php
$page_title = 'Manage Videos';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/functions.php';

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

// Handle search
$search_query = '';
if (isset($_GET['search'])) {
    $search_query = trim($_GET['search']);
}

// Get videos with search filter
if ($search_query) {
    $all_videos = fetchAll("SELECT * FROM videos WHERE title LIKE ? ORDER BY created_at DESC", ['%' . $search_query . '%']);
} else {
    $all_videos = fetchAll("SELECT * FROM videos ORDER BY created_at DESC");
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
            position: fixed;
            height: 100vh;
            z-index: 1000;
            overflow-y: auto;
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
        
        /* Mobile menu toggle */
        .menu-toggle {
            display: block;
            position: fixed;
            top: 5rem;
            left: 1rem;
            z-index: 1001;
            background: var(--primary-color);
            color: var(--text-primary);
            border: none;
            border-radius: 8px;
            padding: 0.75rem;
            cursor: pointer;
            font-size: 1.2rem;
            transition: all 0.3s ease;
        }
        
        .menu-toggle:hover {
            background: var(--secondary-color);
            transform: scale(1.05);
        }
        
        @media (min-width: 769px) {
            .menu-toggle {
                display: none;
            }
        }
        
        @media (max-width: 768px) {
            .admin-sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            
            .admin-sidebar.active {
                transform: translateX(0);
            }
            
                        
            .admin-content {
                width: 100%;
                margin-left: 0;
                padding: 4rem 1rem 1rem 1rem;
            }
        }
        
        /* Mobile menu toggle */
        .menu-toggle {
            display: none;
            position: fixed;
            top: 1rem;
            left: 1rem;
            z-index: 1001;
            background: var(--primary-color);
            color: var(--text-primary);
            border: none;
            border-radius: 8px;
            padding: 0.75rem;
            cursor: pointer;
            font-size: 1.2rem;
        }
        
        @media (max-width: 768px) {
            .admin-sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            
            .admin-sidebar.active {
                transform: translateX(0);
            }
            
                        
            .admin-content {
                width: 100%;
                margin-left: 0;
                padding: 4rem 1rem 1rem 1rem;
            }
        }
        
        .admin-content {
            flex: 1;
            margin-left: 0;
            padding: 2rem;
            padding-top: 4rem;
            background: transparent;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            width: calc(100% - 250px);
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
            grid-template-columns: repeat(6, 1fr);
            gap: 0.5rem;
            margin: 0 auto;
            max-width: 1200px;
            width: 100%;
            justify-items: center;
        }
        
        .video-card {
            background: linear-gradient(145deg, #2a2a2a, #1a1a1a);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3), 0 1px 3px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.05);
            position: relative;
        }
        
        .video-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, #ff6b6b, #4ecdc4, #45b7d1);
            background-size: 200% 100%;
            animation: shimmerGradient 3s linear infinite;
        }
        
        .video-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.4), 0 2px 4px rgba(78, 205, 196, 0.2);
            border-color: rgba(78, 205, 196, 0.3);
        }
        
        .video-thumbnail {
            width: 100%;
            height: 90px;
            object-fit: cover;
            background: var(--dark-tertiary);
        }
        
        @keyframes shimmerGradient {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }
        
        .video-info {
            padding: 0.75rem;
            background: linear-gradient(180deg, rgba(0, 0, 0, 0.7) 0%, rgba(0, 0, 0, 0.9) 100%);
            backdrop-filter: blur(10px);
        }
        
        .video-info h3 {
            color: var(--text-primary);
            margin-bottom: 0.25rem;
            font-size: 0.85rem;
            font-weight: 600;
            line-height: 1.2;
        }
        
        .video-info p {
            color: var(--text-secondary);
            margin-bottom: 0.25rem;
            line-height: 1.3;
            font-size: 0.75rem;
        }
        
        .video-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 0.25rem;
            padding-top: 0.25rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .video-meta span {
            color: var(--text-muted);
            font-size: 0.65rem;
        }
        
        .video-actions {
            display: flex;
            justify-content: space-between;
            gap: 0.25rem;
            margin-top: 0.5rem;
        }
        
        .btn-sm {
            padding: 0.3rem 0.6rem;
            font-size: 0.7rem;
            border-radius: 4px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.3rem;
            flex: 1;
            text-align: center;
        }
        
        .btn-sm:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.4);
        }
        
        .btn-edit {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            transition: all 0.3s ease;
            padding: 0.4rem 0.8rem;
            font-size: 0.8rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            box-shadow: 0 2px 4px rgba(102, 126, 234, 0.3);
        }
        
        .btn-delete {
            background: linear-gradient(135deg, #f093fb, #f5576c);
            color: white;
            border: none;
            transition: all 0.3s ease;
            padding: 0.4rem 0.8rem;
            font-size: 0.8rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            box-shadow: 0 2px 4px rgba(245, 87, 108, 0.3);
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
        
        .search-container {
            margin-bottom: 2rem;
            display: flex;
            gap: 1rem;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .search-form {
            display: flex;
            gap: 0.5rem;
            flex: 1;
            max-width: 400px;
        }
        
        .search-input {
            flex: 1;
            padding: 0.75rem 1rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.05);
            color: var(--text-primary);
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }
        
        .search-input:focus {
            outline: none;
            border-color: var(--primary-color);
            background: rgba(255, 255, 255, 0.08);
        }
        
        .search-input::placeholder {
            color: var(--text-muted);
        }
        
        .search-btn {
            padding: 0.75rem 1.5rem;
            background: var(--primary-color);
            color: var(--text-primary);
            border: none;
            border-radius: 8px;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.3s ease;
            white-space: nowrap;
        }
        
        .search-btn:hover {
            background: var(--secondary-color);
            transform: translateY(-1px);
        }
        
        .clear-search {
            padding: 0.75rem 1rem;
            background: transparent;
            color: var(--text-secondary);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
        }
        
        .clear-search:hover {
            background: rgba(255, 255, 255, 0.05);
            color: var(--text-primary);
        }
        
        /* Responsive Design */
        @media (max-width: 1440px) {
            .videos-grid {
                grid-template-columns: repeat(3, 1fr);
                gap: 1rem;
                max-width: 900px;
            }
            
            .video-thumbnail {
                height: 120px;
            }
            
            .video-info h3 {
                font-size: 0.95rem;
            }
            
            .video-info p {
                font-size: 0.8rem;
            }
            
            .admin-content {
                width: calc(100% - 250px);
            }
        }
        
        @media (max-width: 1024px) {
            .videos-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 1.25rem;
                max-width: 600px;
            }
            
            .video-thumbnail {
                height: 140px;
            }
            
            .video-info h3 {
                font-size: 1rem;
            }
            
            .video-info p {
                font-size: 0.85rem;
            }
            
            .admin-content {
                width: calc(100% - 250px);
                margin-left: 4.5rem;
            }
        }
        
        @media (max-width: 768px) {
            .videos-grid {
                grid-template-columns: repeat(3, 1fr);
                gap: 1rem;
            }
            
            .search-container {
                flex-direction: column;
                align-items: stretch;
            }
            
            .search-form {
                max-width: 100%;
            }
        }
        
        @media (max-width: 480px) {
            .videos-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .video-thumbnail {
                height: 120px;
            }
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
            padding: 0.15rem 0.4rem;
            border-radius: 8px;
            font-size: 0.6rem;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <button class="menu-toggle" onclick="toggleMenu()">
        <i class="fas fa-bars"></i>
    </button>
    
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
                <div class="alert-success" id="success-message">
                    <?php echo $success_message; ?>
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
            
            <div class="search-container">
                <form method="GET" class="search-form">
                    <input type="text" 
                           name="search" 
                           class="search-input" 
                           placeholder="Search videos by name..." 
                           value="<?php echo htmlspecialchars($search_query); ?>">
                    <button type="submit" class="search-btn">
                        <i class="fas fa-search"></i> Search
                    </button>
                </form>
                
                <?php if ($search_query): ?>
                    <a href="videos.php" class="clear-search">
                        <i class="fas fa-times"></i> Clear
                    </a>
                <?php endif; ?>
                
                <a href="add-video.php" class="add-video-btn">
                    <i class="fas fa-plus"></i> Add New Video
                </a>
            </div>
            
            <?php if ($search_query): ?>
                <div style="margin-bottom: 1rem; color: var(--text-secondary);">
                    <i class="fas fa-info-circle"></i> 
                    Showing results for: <strong><?php echo htmlspecialchars($search_query); ?></strong>
                    (<?php echo count($all_videos); ?> videos found)
                </div>
            <?php endif; ?>
            
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
    
    <script>
        function toggleMenu() {
            const sidebar = document.querySelector('.admin-sidebar');
            sidebar.classList.toggle('active');
        }
        
        // Close menu when clicking outside on mobile
        document.addEventListener('click', function(event) {
            const sidebar = document.querySelector('.admin-sidebar');
            const menuToggle = document.querySelector('.menu-toggle');
            
            if (window.innerWidth <= 768 && 
                !sidebar.contains(event.target) && 
                !menuToggle.contains(event.target) &&
                sidebar.classList.contains('active')) {
                sidebar.classList.remove('active');
            }
        });
    </script>
</body>
</html>
