<?php
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/functions.php';

// Start secure session and require login
start_secure_session();
require_login();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'delete') {
        $album_name = sanitize_input($_POST['album_name'] ?? '');
        if ($album_name) {
            // Delete all songs from this album
            $delete_query = "DELETE FROM songs WHERE album = ?";
            $delete_stmt = executeQuery($delete_query, [$album_name]);
            
            if ($delete_stmt) {
                $affected_rows = $delete_stmt->rowCount();
                header('Location: albums.php?deleted=' . urlencode($album_name) . '&rows=' . $affected_rows);
                exit();
            }
        }
    }
}

// Get all unique albums
$albums_query = "SELECT DISTINCT album, artist, cover_image, COUNT(*) as song_count 
                FROM songs 
                WHERE album IS NOT NULL AND album != '' 
                GROUP BY album 
                ORDER BY album ASC";
$albums = fetchAll($albums_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Albums - Admin</title>
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
        
        .albums-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
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
        
        .album-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 1rem;
        }
        
        .song-count {
            background: var(--primary-color);
            color: var(--text-primary);
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
        }
        
        .album-actions {
            display: flex;
            gap: 0.5rem;
            justify-content: center;
            margin-top: 1rem;
        }
        
        .btn-action {
            background: var(--primary-color);
            color: var(--text-primary);
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            font-size: 0.9rem;
        }
        
        .btn-action:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
        }
        
        .btn-danger {
            background: var(--error-color);
        }
        
        .btn-danger:hover {
            background: #c62828;
        }
        
        .no-albums {
            text-align: center;
            padding: 3rem;
            color: var(--text-muted);
        }
        
        .alert-success {
            background: var(--success-color);
            color: white;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 2rem;
        }
        
        @media (max-width: 768px) {
            .admin-container {
                flex-direction: column;
            }
            
            .admin-sidebar {
                width: 100%;
                padding: 1rem 0;
            }
            
            .albums-grid {
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
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
                    <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="songs.php"><i class="fas fa-music"></i> Songs</a></li>
                    <li><a href="albums.php" class="active"><i class="fas fa-compact-disc"></i> Albums</a></li>
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
                <h1>Manage Albums</h1>
                <div class="admin-user">
                    <span>Welcome, <?php echo $_SESSION['username']; ?></span>
                    <i class="fas fa-compact-disc"></i>
                </div>
            </div>
            
            <?php if (isset($_GET['deleted'])): ?>
                <div class="alert-success">
                    Album "<?php echo xss_clean($_GET['deleted']); ?>" deleted successfully! 
                    <?php echo $_GET['rows']; ?> songs removed.
                </div>
            <?php endif; ?>
            
            <div class="albums-grid">
                <?php if (!empty($albums)): ?>
                    <?php foreach ($albums as $album): ?>
                        <div class="album-card">
                            <img src="<?php echo APP_URL . '/' . ($album['cover_image'] ?: 'assets/images/default-album.jpg'); ?>" 
                                 alt="<?php echo xss_clean($album['album']); ?>" class="album-cover">
                            <div class="album-info">
                                <h4><?php echo xss_clean($album['album']); ?></h4>
                                <p><?php echo xss_clean($album['artist']); ?></p>
                                <div class="album-meta">
                                    <div class="song-count">
                                        <?php echo $album['song_count']; ?> Songs
                                    </div>
                                    <div class="album-actions">
                                        <a href="edit-album.php?album=<?php echo urlencode($album['album']); ?>" class="btn-action">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="album_name" value="<?php echo xss_clean($album['album']); ?>">
                                            <button type="submit" class="btn-action btn-danger" onclick="return confirm('Are you sure you want to delete this album and all its songs?')">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-albums">
                        <i class="fas fa-compact-disc" style="font-size: 3rem; margin-bottom: 1rem;"></i>
                        <p>No albums found yet. <a href="add-song.php">Add songs with album information</a> to create albums.</p>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>
