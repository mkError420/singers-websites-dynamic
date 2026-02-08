<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/functions.php';

// Start secure session and require login
start_secure_session();
require_login();

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
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }
        
        .stat-card {
            background: var(--dark-secondary);
            padding: 2rem;
            border-radius: 15px;
            box-shadow: var(--shadow-md);
            text-align: center;
            transition: transform 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-icon {
            font-size: 2.5rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            color: var(--text-secondary);
            font-weight: 500;
        }
        
        .dashboard-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-bottom: 2rem;
        }
        
        .dashboard-card {
            background: var(--dark-secondary);
            border-radius: 15px;
            padding: 2rem;
            box-shadow: var(--shadow-md);
        }
        
        .dashboard-card h3 {
            color: var(--text-primary);
            margin-bottom: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .view-all {
            font-size: 0.9rem;
            color: var(--primary-color);
            text-decoration: none;
        }
        
        .view-all:hover {
            color: var(--secondary-color);
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
            font-size: 0.9rem;
        }
        
        .activity-action {
            color: var(--text-muted);
        }
        
        .btn-quick-action {
            background: var(--primary-color);
            color: var(--text-primary);
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-quick-action:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
        }
        
        .quick-actions {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            margin-top: 2rem;
        }
        
        .albums-section {
            margin-top: 3rem;
        }
        
        .albums-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
        }
        
        .album-card {
            background: var(--dark-secondary);
            border-radius: 15px;
            overflow: hidden;
            transition: transform 0.3s ease;
            box-shadow: var(--shadow-md);
        }
        
        .album-card:hover {
            transform: translateY(-5px);
        }
        
        .album-cover {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        
        .album-info {
            padding: 1.5rem;
            text-align: center;
        }
        
        .album-info h4 {
            margin-bottom: 0.5rem;
            color: var(--text-primary);
            font-size: 1.2rem;
        }
        
        .album-info p {
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
                    <li><a href="songs.php" class="active"><i class="fas fa-music"></i> Manage Songs</a></li>
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
            
            <!-- Quick Actions -->
            <div class="quick-actions">
                <a href="add-song.php" class="btn-quick-action"><i class="fas fa-plus"></i> Add Song</a>
                <a href="add-video.php" class="btn-quick-action"><i class="fas fa-plus"></i> Add Video</a>
                <a href="add-tour.php" class="btn-quick-action"><i class="fas fa-plus"></i> Add Tour Date</a>
            </div>
        </main>
    </div>
</body>
</html>
