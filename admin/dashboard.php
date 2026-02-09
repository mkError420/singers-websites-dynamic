<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/functions.php';

// Start session and check login
session_start();

// Simple authentication check
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Get statistics
$total_songs = fetchOne("SELECT COUNT(*) as count FROM songs")['count'];
$total_videos = fetchOne("SELECT COUNT(*) as count FROM videos")['count'];
$total_tour_dates = fetchOne("SELECT COUNT(*) as count FROM tour_dates")['count'];
$total_messages = fetchOne("SELECT COUNT(*) as count FROM contact_messages WHERE is_read = FALSE")['count'];
$total_subscribers = fetchOne("SELECT COUNT(*) as count FROM newsletter_subscribers WHERE is_active = TRUE")['count'];

// Get recent activity
$recent_songs = fetchAll("SELECT * FROM songs ORDER BY created_at DESC LIMIT 5");
$recent_messages = fetchAll("SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT 5");

// Handle success messages
if (isset($_GET['album_updated'])) {
    $success_message = "Album '" . xss_clean($_GET['album_updated']) . "' updated successfully! " . $_GET['rows'] . " songs affected.";
} elseif (isset($_GET['album_deleted'])) {
    $success_message = "Album '" . xss_clean($_GET['album_deleted']) . "' deleted successfully! " . $_GET['rows'] . " songs removed.";
} else {
    $success_message = '';
}

$upcoming_tours = fetchAll("SELECT * FROM tour_dates WHERE event_date >= CURDATE() ORDER BY event_date ASC LIMIT 5");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .admin-container {
            display: flex;
            min-height: 100vh;
            background: linear-gradient(135deg, var(--dark-bg) 0%, #1a1a2e 100%);
        }
        
        .admin-sidebar {
            width: 280px;
            background: linear-gradient(180deg, rgba(255, 107, 107, 0.1) 0%, var(--dark-secondary) 100%);
            padding: 2rem 0;
            border-right: 2px solid transparent;
            border-image: linear-gradient(180deg, var(--primary-color), transparent) 1;
            height: 100vh;
            overflow-y: auto;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            display: block;
            visibility: visible;
            backdrop-filter: blur(10px);
            box-shadow: 4px 0 20px rgba(0, 0, 0, 0.3);
        }
        
        .admin-logo {
            text-align: center;
            margin-bottom: 2rem;
            padding: 0 1rem;
            position: relative;
        }
        
        .admin-logo::before {
            content: '';
            position: absolute;
            top: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 3px;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
            border-radius: 2px;
        }
        
        .admin-logo h2 {
            color: var(--primary-color);
            font-size: 1.6rem;
            font-weight: 700;
            text-shadow: 0 4px 8px rgba(255, 107, 107, 0.3);
            margin-bottom: 0.5rem;
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .admin-logo small {
            color: var(--text-muted);
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            opacity: 0.8;
        }
        
        .admin-nav {
            list-style: none;
            padding: 0 1rem;
        }
        
        .admin-nav li {
            margin-bottom: 0.5rem;
            position: relative;
        }
        
        .admin-nav a {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem 1.5rem;
            color: var(--text-primary);
            text-decoration: none;
            background: transparent;
            border-radius: 15px;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            font-weight: 500;
            position: relative;
            overflow: hidden;
            border: 1px solid transparent;
        }
        
        .admin-nav a::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
            transition: left 0.4s ease;
            z-index: -1;
        }
        
        .admin-nav a:hover,
        .admin-nav a.active {
            background: rgba(255, 107, 107, 0.1);
            color: var(--primary-color);
            transform: translateX(8px);
            border-color: rgba(255, 107, 107, 0.3);
            box-shadow: 0 4px 12px rgba(255, 107, 107, 0.2);
        }
        
        .admin-nav a:hover::before,
        .admin-nav a.active::before {
            left: 0;
        }
        
        .admin-nav i {
            width: 20px;
            text-align: center;
        }
        
        .admin-content {
            flex: 1;
            margin-left: 280px;
            padding: 2rem;
            background: transparent;
            min-height: 100vh;
            display: block;
            visibility: visible;
            position: relative;
        }
        
        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid transparent;
            border-image: linear-gradient(90deg, var(--primary-color), transparent) 1;
            position: relative;
        }
        
        .admin-header::before {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 100px;
            height: 2px;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
        }
        
        .admin-header h1 {
            color: var(--text-primary);
            font-size: 2.2rem;
            font-weight: 700;
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
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
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
            position: relative;
        }
        
        .stats-grid::before {
            content: '';
            position: absolute;
            top: -20px;
            left: -20px;
            right: -20px;
            bottom: -20px;
            background: radial-gradient(circle at 50% 50%, rgba(255, 107, 107, 0.1) 0%, transparent 70%);
            border-radius: 20px;
            z-index: -1;
        }
        
        .stat-card {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.05) 0%, rgba(255, 255, 255, 0.02) 100%);
            padding: 2rem;
            border-radius: 25px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.4), 0 5px 15px rgba(255, 107, 107, 0.1);
            text-align: center;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid rgba(255, 255, 255, 0.1);
            position: relative;
            overflow: hidden;
            backdrop-filter: blur(10px);
        }
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color), var(--primary-color));
            background-size: 200% 100%;
            animation: shimmerGradient 3s linear infinite;
        }
        
        .stat-card::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 107, 107, 0.1) 0%, transparent 70%);
            opacity: 0;
            transition: opacity 0.4s ease;
        }
        
        @keyframes shimmerGradient {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }
        
        .stat-card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 20px 40px rgba(255, 107, 107, 0.2);
            border-color: rgba(255, 107, 107, 0.3);
        }
        
        .stat-card:hover::after {
            opacity: 1;
        }
        
        .stat-icon {
            font-size: 3.5rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
            text-shadow: 0 4px 8px rgba(255, 107, 107, 0.3);
            position: relative;
            z-index: 2;
        }
        
        .stat-number {
            font-size: 2.8rem;
            font-weight: 800;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
            position: relative;
            z-index: 2;
        }
        
        .stat-label {
            color: var(--text-secondary);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-size: 0.85rem;
            opacity: 0.9;
        }
        
        .dashboard-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-bottom: 2rem;
        }
        
        .dashboard-card {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.03) 0%, rgba(255, 255, 255, 0.01) 100%);
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3), 0 2px 8px rgba(255, 107, 107, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.1);
            position: relative;
            overflow: hidden;
            backdrop-filter: blur(5px);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .dashboard-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--primary-color), transparent);
            background-size: 200% 100%;
            animation: shimmerGradient 4s linear infinite;
        }
        
        .dashboard-card h3 {
            color: var(--text-primary);
            margin-bottom: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 1.3rem;
            font-weight: 700;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }
        
        .view-all {
            font-size: 0.9rem;
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .view-all:hover {
            color: var(--secondary-color);
            transform: translateX(3px);
        }
        
        .activity-list {
            list-style: none;
        }
        
        .activity-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 0;
            border-bottom: 1px solid var(--border-color);
            transition: all 0.3s ease;
        }
        
        .activity-item:hover {
            background: rgba(255, 255, 255, 0.05);
            padding-left: 1rem;
        }
        
        .activity-item:last-child {
            border-bottom: none;
        }
        
        .activity-info {
            flex: 1;
        }
        
        .activity-title {
            color: var(--text-primary);
            font-weight: 500;
            margin-bottom: 0.25rem;
        }
        
        .activity-meta {
            color: var(--text-muted);
            font-size: 0.85rem;
        }
        
        .activity-date {
            color: var(--text-muted);
            font-size: 0.85rem;
            white-space: nowrap;
        }
        
        /* Responsive Design */
        @media (max-width: 1400px) {
            .admin-sidebar {
                width: 260px;
            }
            
            .admin-content {
                margin-left: 260px;
            }
        }
        
        @media (max-width: 1200px) {
            .stats-grid {
                grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
                gap: 1.2rem;
            }
            
            .dashboard-grid {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }
        }
        
        @media (max-width: 1024px) {
            .admin-sidebar {
                width: 240px;
            }
            
            .admin-content {
                margin-left: 240px;
            }
            
            .stats-grid {
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 1rem;
            }
            
            .stat-card {
                padding: 1.5rem;
            }
            
            .stat-icon {
                font-size: 3rem;
            }
            
            .stat-number {
                font-size: 2.4rem;
            }
        }
        
        @media (max-width: 768px) {
            .admin-sidebar {
                transform: translateX(0);
                display: block;
                visibility: visible;
                width: 100%;
                padding: 1rem 0;
            }
            
            .admin-sidebar.active {
                transform: translateX(0);
            }
            
            .admin-content {
                margin-left: 0;
                padding: 1rem;
                display: block;
                visibility: visible;
            }
            
            .admin-header h1 {
                font-size: 1.8rem;
            }
            
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 1rem;
            }
            
            .stat-card {
                padding: 1.2rem;
            }
            
            .stat-icon {
                font-size: 2.5rem;
            }
            
            .stat-number {
                font-size: 2rem;
            }
            
            .dashboard-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
        }
        
        @media (max-width: 480px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .admin-header {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }
            
            .admin-header h1 {
                font-size: 1.5rem;
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
                    <li><a href="dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="songs.php"><i class="fas fa-music"></i> Songs</a></li>
                    <li><a href="videos.php"><i class="fas fa-video"></i> Videos</a></li>
                    <li><a href="tour.php"><i class="fas fa-calendar-alt"></i> Tour Dates</a></li>
                    <li><a href="albums.php"><i class="fas fa-compact-disc"></i> Albums</a></li>
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
                <h1>Admin Dashboard</h1>
                <div class="admin-user">
                    <span>Welcome, <?php echo $_SESSION['username']; ?></span>
                    <i class="fas fa-user-circle"></i>
                </div>
            </div>
            
            <?php if (isset($success_message)): ?>
                <div class="alert-success">
                    <?php echo $success_message; ?>
                </div>
            <?php endif; ?>
            
            <!-- Statistics Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-music"></i>
                    </div>
                    <div class="stat-number"><?php echo $total_songs; ?></div>
                    <div class="stat-label">Total Songs</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-video"></i>
                    </div>
                    <div class="stat-number"><?php echo $total_videos; ?></div>
                    <div class="stat-label">Total Videos</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-calendar"></i>
                    </div>
                    <div class="stat-number"><?php echo $total_tour_dates; ?></div>
                    <div class="stat-label">Tour Dates</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="stat-number"><?php echo $total_messages; ?></div>
                    <div class="stat-label">New Messages</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-number"><?php echo $total_subscribers; ?></div>
                    <div class="stat-label">Subscribers</div>
                </div>
            </div>
            
            <!-- Dashboard Grid -->
            <div class="dashboard-grid">
                <!-- Recent Songs -->
                <div class="dashboard-card">
                    <h3>
                        Recent Songs
                        <a href="songs.php" class="view-all">View All</a>
                    </h3>
                    <ul class="activity-list">
                        <?php if (!empty($recent_songs)): ?>
                            <?php foreach ($recent_songs as $song): ?>
                                <li class="activity-item">
                                    <div class="activity-info">
                                        <div class="activity-title"><?php echo xss_clean($song['title']); ?></div>
                                        <div class="activity-meta">by <?php echo xss_clean($song['artist']); ?></div>
                                    </div>
                                    <div class="activity-date"><?php echo format_date($song['created_at'], 'M j, Y'); ?></div>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li class="activity-item">
                                <div class="activity-info">
                                    <div class="activity-title">No songs yet</div>
                                    <div class="activity-meta">Add your first song</div>
                                </div>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
                
                <!-- Recent Messages -->
                <div class="dashboard-card">
                    <h3>
                        Recent Messages
                        <a href="messages.php" class="view-all">View All</a>
                    </h3>
                    <ul class="activity-list">
                        <?php if (!empty($recent_messages)): ?>
                            <?php foreach ($recent_messages as $message): ?>
                                <li class="activity-item">
                                    <div class="activity-info">
                                        <div class="activity-title"><?php echo xss_clean($message['subject']); ?></div>
                                        <div class="activity-meta"><?php echo xss_clean($message['name']); ?></div>
                                    </div>
                                    <div class="activity-date"><?php echo format_date($message['created_at'], 'M j, Y'); ?></div>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li class="activity-item">
                                <div class="activity-info">
                                    <div class="activity-title">No messages yet</div>
                                    <div class="activity-meta">No contact messages</div>
                                </div>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
            
            <!-- Upcoming Tour Dates -->
            <div class="dashboard-card">
                <h3>
                    Upcoming Tour Dates
                    <a href="tour.php" class="view-all">View All</a>
                </h3>
                <ul class="activity-list">
                    <?php if (!empty($upcoming_tours)): ?>
                        <?php foreach ($upcoming_tours as $tour): ?>
                            <li class="activity-item">
                                <div class="activity-info">
                                    <div class="activity-title"><?php echo xss_clean($tour['event_name']); ?></div>
                                    <div class="activity-meta"><?php echo xss_clean($tour['venue']) ?> - <?php echo xss_clean($tour['city']); ?></div>
                                </div>
                                <div class="activity-date"><?php echo format_date($tour['event_date'], 'M j, Y'); ?></div>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li class="activity-item">
                            <div class="activity-info">
                                <div class="activity-title">No tour dates</div>
                                <div class="activity-meta">No upcoming events</div>
                            </div>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </main>
    </div>
</body>
</html>
            color: var(--text-secondary);
            margin-bottom: 1rem;
        }
        
        .album-actions {
            display: flex;
            gap: 0.5rem;
            justify-content: center;
        }
        
        .album-actions .btn-sm {
            padding: 0.4rem 0.8rem;
            font-size: 0.85rem;
            text-decoration: none;
        }
        
        .album-actions .btn-primary {
            background: var(--primary-color);
            color: var(--text-primary);
            border: none;
            border-radius: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .album-actions .btn-primary:hover {
            background: var(--secondary-color);
        }
        
        .album-actions .btn-danger {
            background: var(--error-color);
            color: var(--text-primary);
            border: none;
            border-radius: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .album-actions .btn-danger:hover {
            background: #c62828;
        }
        
        @media (max-width: 768px) {
            .admin-container {
                flex-direction: column;
            }
            
            .admin-sidebar {
                width: 100%;
                padding: 1rem 0;
            }
            
            .admin-nav {
                display: flex;
                overflow-x: auto;
                padding: 0 1rem;
            }
            
            .admin-nav li {
                margin: 0;
                margin-right: 0.5rem;
            }
            
            .admin-nav a {
                white-space: nowrap;
            }
            
            .admin-content {
                padding: 1rem;
            }
            
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
            
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 1rem;
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
                    <li><a href="dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="songs.php"><i class="fas fa-music"></i> Songs</a></li>
                    <li><a href="albums.php"><i class="fas fa-compact-disc"></i> Albums</a></li>
                    <li><a href="videos.php"><i class="fas fa-video"></i> Videos</a></li>
                    <li><a href="tour.php"><i class="fas fa-calendar-alt"></i> Tour Dates</a></li>
                    <li><a href="messages.php"><i class="fas fa-envelope"></i> Messages <span style="background: var(--error-color); color: white; padding: 2px 6px; border-radius: 10px; font-size: 0.8rem;"><?php echo $total_messages; ?></span></a></li>
                    <li><a href="subscribers.php"><i class="fas fa-users"></i> Subscribers</a></li>
                    <li><a href="settings.php"><i class="fas fa-cog"></i> Settings</a></li>
                    <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </nav>
        </aside>
        
        <!-- Main Content -->
        <main class="admin-content">
            <div class="admin-header">
                <h1>Dashboard</h1>
                <div class="admin-user">
                    <span>Welcome, <?php echo $_SESSION['username']; ?></span>
                    <i class="fas fa-user-circle"></i>
                </div>
            </div>
            
            <!-- Statistics -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-music"></i></div>
                    <div class="stat-number"><?php echo $total_songs; ?></div>
                    <div class="stat-label">Total Songs</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-video"></i></div>
                    <div class="stat-number"><?php echo $total_videos; ?></div>
                    <div class="stat-label">Total Videos</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-calendar-alt"></i></div>
                    <div class="stat-number"><?php echo $total_tour_dates; ?></div>
                    <div class="stat-label">Tour Dates</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-envelope"></i></div>
                    <div class="stat-number"><?php echo $total_messages; ?></div>
                    <div class="stat-label">Unread Messages</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-users"></i></div>
                    <div class="stat-number"><?php echo $total_subscribers; ?></div>
                    <div class="stat-label">Subscribers</div>
                </div>
            </div>
            
            <!-- Recent Activity -->
            <div class="dashboard-grid">
                <div class="dashboard-card">
                    <h3>
                        Recent Songs
                        <a href="songs.php" class="view-all">View All</a>
                    </h3>
                    <ul class="activity-list">
                        <?php if (!empty($recent_songs)): ?>
                            <?php foreach ($recent_songs as $song): ?>
                                <li class="activity-item">
                                    <div class="activity-info">
                                        <div class="activity-title"><?php echo xss_clean($song['title']); ?></div>
                                        <div class="activity-meta"><?php echo format_date($song['created_at'], 'M j, Y'); ?></div>
                                    </div>
                                    <div class="activity-action">
                                        <a href="edit-song.php?id=<?php echo $song['id']; ?>" class="btn-quick-action">Edit</a>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li class="activity-item">
                                <div class="activity-info">
                                    <div class="activity-title">No songs yet</div>
                                </div>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
                
                <div class="dashboard-card">
                    <h3>
                        Recent Messages
                        <a href="messages.php" class="view-all">View All</a>
                    </h3>
                    <ul class="activity-list">
                        <?php if (!empty($recent_messages)): ?>
                            <?php foreach ($recent_messages as $message): ?>
                                <li class="activity-item">
                                    <div class="activity-info">
                                        <div class="activity-title"><?php echo xss_clean($message['subject']); ?></div>
                                        <div class="activity-meta"><?php echo xss_clean($message['name']); ?> • <?php echo format_date($message['created_at'], 'M j, Y'); ?></div>
                                    </div>
                                    <div class="activity-action">
                                        <?php if (!$message['is_read']): ?>
                                            <span style="color: var(--primary-color);">•</span>
                                        <?php endif; ?>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li class="activity-item">
                                <div class="activity-info">
                                    <div class="activity-title">No messages yet</div>
                                </div>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
            
            <!-- Upcoming Tours -->
            <div class="dashboard-card">
                <h3>
                    Upcoming Tour Dates
                    <a href="tour.php" class="view-all">View All</a>
                </h3>
                <ul class="activity-list">
                    <?php if (!empty($upcoming_tours)): ?>
                        <?php foreach ($upcoming_tours as $tour): ?>
                            <li class="activity-item">
                                <div class="activity-info">
                                    <div class="activity-title"><?php echo xss_clean($tour['event_name']); ?></div>
                                    <div class="activity-meta"><?php echo xss_clean($tour['venue']); ?> • <?php echo format_date($tour['event_date'], 'M j, Y'); ?></div>
                                </div>
                                <div class="activity-action">
                                    <a href="edit-tour.php?id=<?php echo $tour['id']; ?>" class="btn-quick-action">Edit</a>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li class="activity-item">
                            <div class="activity-info">
                                <div class="activity-title">No upcoming tours</div>
                            </div>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
            
            <?php if (isset($error)): ?>
                <div class="alert-error">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success_message): ?>
                <div class="alert-success">
                    <?php echo $success_message; ?>
                </div>
            <?php endif; ?>
            
            <!-- Albums Section -->
            <div class="albums-section">
                <div class="section-title">
                    <h3>Albums</h3>
                    <p>Manage your album collection</p>
                </div>
                
                <div class="albums-grid">
                    <?php
                    // Get unique albums from database
                    $albums_query = "SELECT DISTINCT album, artist, cover_image, COUNT(*) as song_count FROM songs WHERE album IS NOT NULL AND album != '' GROUP BY album ORDER BY album ASC";
                    $albums = fetchAll($albums_query);
                    
                    if (!empty($albums)):
                    ?>
                        <?php foreach ($albums as $album): ?>
                            <div class="album-card">
                                <img src="<?php echo APP_URL . '/' . ($album['cover_image'] ?: 'assets/images/default-album.jpg'); ?>" 
                                     alt="<?php echo xss_clean($album['album']); ?>" class="album-cover">
                                <div class="album-info">
                                    <h4><?php echo xss_clean($album['album']); ?></h4>
                                    <p><?php echo xss_clean($album['artist']); ?> • <?php echo $album['song_count']; ?> songs</p>
                                    <div class="album-actions">
                                        <a href="edit-album.php?album=<?php echo urlencode($album['album']); ?>" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <a href="delete-album.php?album=<?php echo urlencode($album['album']); ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this album and all its songs?')">
                                            <i class="fas fa-trash"></i> Delete
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="no-content">
                            <p>No albums available yet. <a href="add-song.php">Add songs with album information</a> to create albums.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
