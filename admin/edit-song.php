<?php
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/functions.php';

// Start secure session and require login
start_secure_session();
require_login();

// Get song ID from URL
$song_id = $_GET['id'] ?? 0;

if (!$song_id) {
    header('Location: songs.php');
    exit();
}

// Get existing song data
$song = fetchOne("SELECT * FROM songs WHERE id = ?", [$song_id]);

if (!$song) {
    header('Location: songs.php');
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitize_input($_POST['title'] ?? '');
    $artist = sanitize_input($_POST['artist'] ?? '');
    $album = sanitize_input($_POST['album'] ?? '');
    $genre = sanitize_input($_POST['genre'] ?? '');
    $duration = sanitize_input($_POST['duration'] ?? '');
    $release_date = $_POST['release_date'] ?? '';
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    // Handle file uploads (only if new files provided)
    $file_path = $song['file_path']; // Keep existing if no new file
    $cover_image = $song['cover_image']; // Keep existing if no new file
    
    if (isset($_FILES['audio_file']) && $_FILES['audio_file']['error'] === UPLOAD_ERR_OK) {
        $upload_result = upload_file($_FILES['audio_file'], UPLOAD_PATH . 'songs/', ['mp3', 'wav', 'm4a']);
        if ($upload_result['success']) {
            $file_path = 'uploads/songs/' . $upload_result['filename'];
        }
    }
    
    if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
        $upload_result = upload_file($_FILES['cover_image'], UPLOAD_PATH . 'covers/', ['jpg', 'jpeg', 'png', 'gif']);
        if ($upload_result['success']) {
            $cover_image = 'uploads/covers/' . $upload_result['filename'];
        }
    }
    
    // Validate required fields
    if (empty($title) || empty($artist) || empty($file_path)) {
        $error = 'Title, artist, and audio file are required.';
    } else {
        // Update database
        $song_data = [
            'title' => $title,
            'artist' => $artist,
            'album' => $album,
            'genre' => $genre,
            'duration' => $duration,
            'release_date' => $release_date,
            'file_path' => $file_path,
            'cover_image' => $cover_image,
            'is_active' => $is_active
        ];
        
        if (updateData('songs', $song_data, 'id = ?', [$song_id])) {
            header('Location: songs.php?updated=1');
            exit();
        } else {
            $error = 'Failed to update song. Please try again.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Song - Admin</title>
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
        
        .song-form {
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
                <h1>Edit Song</h1>
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
            
            <form method="POST" enctype="multipart/form-data" class="song-form">
                <div class="form-group">
                    <label for="title">Song Title *</label>
                    <input type="text" id="title" name="title" class="form-control" required
                           value="<?php echo xss_clean($song['title']); ?>">
                </div>
                
                <div class="form-group">
                    <label for="artist">Artist *</label>
                    <input type="text" id="artist" name="artist" class="form-control" required
                           value="<?php echo xss_clean($song['artist']); ?>">
                </div>
                
                <div class="form-group">
                    <label for="album">Album</label>
                    <input type="text" id="album" name="album" class="form-control" 
                           value="<?php echo xss_clean($song['album'] ?? ''); ?>" placeholder="e.g., Greatest Hits">
                </div>
                
                <div class="form-group">
                    <label for="genre">Genre</label>
                    <input type="text" id="genre" name="genre" class="form-control" 
                           value="<?php echo xss_clean($song['genre'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="duration">Duration</label>
                    <input type="text" id="duration" name="duration" class="form-control" 
                           value="<?php echo xss_clean($song['duration'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="release_date">Release Date</label>
                    <input type="date" id="release_date" name="release_date" class="form-control"
                           value="<?php echo $song['release_date']; ?>">
                </div>
                
                <div class="form-group file-upload">
                    <label for="audio_file">Audio File</label>
                    <input type="file" id="audio_file" name="audio_file" accept="audio/*">
                    <label for="audio_file" class="file-upload-label">
                        <i class="fas fa-music"></i>
                        <span>Choose New Audio File (optional)</span>
                        <small>MP3, WAV, M4A (Max: <?php echo MAX_FILE_SIZE / 1024 / 1024; ?>MB)</small>
                    </label>
                    <?php if ($song['file_path']): ?>
                        <div class="current-file">
                            <strong>Current:</strong> <?php echo xss_clean($song['file_path']); ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="form-group file-upload">
                    <label for="cover_image">Cover Image</label>
                    <input type="file" id="cover_image" name="cover_image" accept="image/*">
                    <label for="cover_image" class="file-upload-label">
                        <i class="fas fa-image"></i>
                        <span>Choose New Cover Image (optional)</span>
                        <small>JPG, PNG, GIF (Max: <?php echo MAX_FILE_SIZE / 1024 / 1024; ?>MB)</small>
                    </label>
                    <?php if ($song['cover_image']): ?>
                        <div class="current-file">
                            <strong>Current:</strong> <?php echo xss_clean($song['cover_image']); ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <div class="checkbox-group">
                        <input type="checkbox" id="is_active" name="is_active" <?php echo $song['is_active'] ? 'checked' : ''; ?>>
                        <label for="is_active">Active (show on website)</label>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn-submit">
                        <i class="fas fa-save"></i> Update Song
                    </button>
                    <a href="songs.php" class="btn-cancel">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </main>
    </div>
</body>
</html>
