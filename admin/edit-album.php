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

// Get album name from URL
$album_name = sanitize_input($_GET['album'] ?? '');

if (empty($album_name)) {
    header('Location: dashboard.php');
    exit();
}

// Handle form submission for editing album BEFORE any HTML output
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_album_name = sanitize_input($_POST['album_name'] ?? '');
    $new_artist = sanitize_input($_POST['artist'] ?? '');
    
    $error_message = '';
    
    if (empty($new_album_name) || empty($new_artist)) {
        $error_message = 'Album name and artist are required.';
    } else {
        try {
            require_once __DIR__ . '/../includes/database.php';
            // Update all songs in this album
            $update_query = "UPDATE songs SET album = ?, artist = ? WHERE album = ?";
            $update_stmt = executeQuery($update_query, [$new_album_name, $new_artist, $album_name]);
            
            if ($update_stmt) {
                $affected_rows = $update_stmt->rowCount();
                header('Location: albums.php?album_updated=' . urlencode($new_album_name) . '&rows=' . $affected_rows);
                exit();
            } else {
                $error_message = 'Failed to update album. Please try again.';
            }
        } catch (Exception $e) {
            $error_message = 'Database error: ' . $e->getMessage();
        }
    }
}

$page_title = 'Edit Album';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/database.php';

// Get all songs from this album (only needed for displaying the form)
$songs_query = "SELECT * FROM songs WHERE album = ? AND is_active = TRUE ORDER BY created_at ASC";
$songs = fetchAll($songs_query, [$album_name]);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Album - Admin</title>
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
        
        .album-form {
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
        
        .songs-list {
            margin-top: 2rem;
        }
        
        .song-item {
            background: var(--dark-tertiary);
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 0.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .song-info {
            flex: 1;
        }
        
        .song-title {
            font-weight: 600;
            color: var(--text-primary);
        }
        
        .song-artist {
            color: var(--text-secondary);
            font-size: 0.9rem;
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
        
        .alert-success {
            background: var(--success-color);
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
                <h1>Edit Album: <?php echo xss_clean($album_name); ?></h1>
                <div class="admin-user">
                    <span>Welcome, <?php echo $_SESSION['username']; ?></span>
                    <i class="fas fa-user-circle"></i>
                </div>
            </div>
            
            <?php if (isset($error_message) && !empty($error_message)): ?>
                <div class="alert-error" style="background: var(--error-color); color: white; padding: 1rem; border-radius: 8px; margin-bottom: 2rem;">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" class="album-form">
                <div class="form-group">
                    <label for="album_name">Album Name *</label>
                    <input type="text" id="album_name" name="album_name" class="form-control" required
                           value="<?php echo xss_clean($_POST['album_name'] ?? $album_name); ?>">
                </div>
                
                <div class="form-group">
                    <label for="artist">Artist *</label>
                    <input type="text" id="artist" name="artist" class="form-control" required
                           value="<?php echo xss_clean($_POST['artist'] ?? ($songs[0]['artist'] ?? '')); ?>">
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn-submit">
                        <i class="fas fa-save"></i> Update Album
                    </button>
                    <a href="albums.php" class="btn-cancel">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
            
            <?php if (!empty($songs)): ?>
                <div class="songs-list">
                    <h3>Songs in this Album</h3>
                    <?php foreach ($songs as $song): ?>
                        <div class="song-item">
                            <div class="song-info">
                                <div class="song-title"><?php echo xss_clean($song['title']); ?></div>
                                <div class="song-artist"><?php echo xss_clean($song['artist']); ?></div>
                            </div>
                            <div>
                                <a href="edit-song.php?id=<?php echo $song['id']; ?>" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p>No songs found in this album.</p>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>
