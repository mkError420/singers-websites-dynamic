<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/functions.php';

// Start secure session and require login
start_secure_session();
require_login();

// Get all songs
$songs = fetchAll("SELECT * FROM songs ORDER BY created_at DESC");

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'delete') {
        $song_id = $_POST['song_id'] ?? 0;
        if ($song_id) {
            deleteData('songs', 'id = ?', [$song_id]);
            header('Location: songs.php?deleted=1');
            exit();
        }
    }
}
?>

<head>
    <title>Manage Songs - Admin</title>
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
        
        .songs-table {
            width: 100%;
            background: var(--dark-secondary);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: var(--shadow-lg);
        }
        
        .songs-table table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .songs-table th {
            background: var(--dark-tertiary);
            color: var(--text-primary);
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            border-bottom: 2px solid var(--border-color);
        }
        
        .songs-table td {
            padding: 1rem;
            border-bottom: 1px solid var(--border-color);
            color: var(--text-secondary);
        }
        
        .songs-table tr:hover td {
            background: var(--dark-tertiary);
        }
        
        .song-actions {
            display: flex;
            gap: 0.5rem;
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
        
        .add-song-btn {
            background: var(--primary-color);
            color: var(--text-primary);
            border: none;
            padding: 1rem 2rem;
            border-radius: 25px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            margin-bottom: 2rem;
        }
        
        .add-song-btn:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
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
        
            .songs-table {
                font-size: 0.9rem;
            }
        
            .songs-table th,
            .songs-table td {
                padding: 0.75rem 0.5rem;
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
                    <li><a href="songs.php" class="active"><i class="fas fa-music"></i> Songs</a></li>
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
                <h1>Manage Songs</h1>
                <div class="admin-user">
                    <span>Welcome, <?php echo $_SESSION['username']; ?></span>
                    <i class="fas fa-user-circle"></i>
                </div>
            </div>
            
            <a href="add-song.php" class="add-song-btn">
                <i class="fas fa-plus"></i> Add New Song
            </a>
            
            <?php if (isset($_GET['deleted'])): ?>
                <div class="alert-success">
                    Song deleted successfully!
                </div>
            <?php endif; ?>
            
            <div class="songs-table">
                <table>
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Artist</th>
                            <th>Album</th>
                            <th>Genre</th>
                            <th>Duration</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($songs)): ?>
                            <?php foreach ($songs as $song): ?>
                                <tr>
                                    <td><?php echo xss_clean($song['title']); ?></td>
                                    <td><?php echo xss_clean($song['artist']); ?></td>
                                    <td><?php echo xss_clean($song['album'] ?? 'N/A'); ?></td>
                                    <td><?php echo xss_clean($song['genre']); ?></td>
                                    <td><?php echo $song['duration']; ?></td>
                                    <td><?php echo format_date($song['created_at'], 'M j, Y'); ?></td>
                                    <td>
                                        <div class="song-actions">
                                            <a href="edit-song.php?id=<?php echo $song['id']; ?>" class="btn-action">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="song_id" value="<?php echo $song['id']; ?>">
                                                <button type="submit" class="btn-action btn-danger" onclick="return confirm('Are you sure you want to delete this song?')">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 2rem;">
                                    No songs found. <a href="add-song.php">Add your first song</a>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>
