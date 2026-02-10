<?php
$page_title = 'Manage Songs';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/functions.php';

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

// Handle search
$search_query = '';
if (isset($_GET['search'])) {
    $search_query = trim($_GET['search']);
}

// Get songs with search filter
if ($search_query) {
    $songs = fetchAll("SELECT * FROM songs WHERE title LIKE ? ORDER BY created_at DESC", ['%' . $search_query . '%']);
} else {
    $songs = fetchAll("SELECT * FROM songs ORDER BY created_at DESC");
}
?>
<style>
    /* Navigation Styles */
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
        color: #000000;
    }
    
    .admin-content {
        flex: 1;
        padding: 2rem;
        padding-top: 4rem;
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
    
    /* Search Styles */
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
</style>

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
            
            <div class="search-container">
                <form method="GET" class="search-form">
                    <input type="text" 
                           name="search" 
                           class="search-input" 
                           placeholder="Search songs by name..." 
                           value="<?php echo htmlspecialchars($search_query); ?>">
                    <button type="submit" class="search-btn">
                        <i class="fas fa-search"></i> Search
                    </button>
                </form>
                
                <?php if ($search_query): ?>
                    <a href="songs.php" class="clear-search">
                        <i class="fas fa-times"></i> Clear
                    </a>
                <?php endif; ?>
                
                <a href="add-song.php" class="btn">
                    <i class="fas fa-plus"></i> Add New Song
                </a>
            </div>
            
            <?php if ($search_query): ?>
                <div style="margin-bottom: 1rem; color: var(--text-secondary);">
                    <i class="fas fa-info-circle"></i> 
                    Showing results for: <strong><?php echo htmlspecialchars($search_query); ?></strong>
                    (<?php echo count($songs); ?> songs found)
                </div>
            <?php endif; ?>
            
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
