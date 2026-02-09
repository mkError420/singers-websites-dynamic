<?php
$page_title = 'Manage Songs';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/functions.php';

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

<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <div class="admin-logo">
                <h2><?php echo APP_NAME; ?></h2>
                <small>Admin Panel</small>
            </div>
            
            <?php require_once __DIR__ . '/../includes/admin-nav.php'; ?>
        </aside>
        
        <!-- Main Content -->
        <main class="admin-content">
            <div class="admin-header">
                <h1>Manage Songs</h1>
                <div class="admin-user">
                    <i class="fas fa-user-circle"></i>
                    <span><?php echo $_SESSION['username']; ?></span>
                </div>
            </div>
            
            <a href="add-song.php" class="btn">
                <i class="fas fa-plus"></i> Add New Song
            </a>
            
            <?php if (isset($_GET['deleted'])): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> Song deleted successfully!
                </div>
            <?php endif; ?>
            
            <div class="admin-card">
                <h3>All Songs</h3>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Album</th>
                            <th>Duration</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($songs)): ?>
                            <?php foreach ($songs as $song): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($song['title']); ?></td>
                                    <td><?php echo htmlspecialchars($song['album'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($song['duration'] ?? 'N/A'); ?></td>
                                    <td><?php echo date('M j, Y', strtotime($song['created_at'])); ?></td>
                                    <td>
                                        <div class="song-actions">
                                            <a href="edit-song.php?id=<?php echo $song['id']; ?>" class="btn btn-sm">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="song_id" value="<?php echo $song['id']; ?>">
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this song?')">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 2rem;">
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
