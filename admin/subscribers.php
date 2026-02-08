<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/functions.php';

// Start secure session and require login
start_secure_session();
require_login();

// Get all subscribers
$all_subscribers = fetchAll("SELECT * FROM newsletter_subscribers ORDER BY subscribe_date DESC");

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'delete') {
        $subscriber_id = $_POST['subscriber_id'] ?? 0;
        if ($subscriber_id) {
            deleteData('newsletter_subscribers', 'id = ?', [$subscriber_id]);
            header('Location: subscribers.php?deleted=1');
            exit();
        }
    }
    
    if ($action === 'toggle_status') {
        $subscriber_id = $_POST['subscriber_id'] ?? 0;
        $current_status = $_POST['current_status'] ?? 0;
        $new_status = $current_status ? 0 : 1;
        
        if ($subscriber_id) {
            updateData('newsletter_subscribers', ['is_active' => $new_status], 'id = ?', [$subscriber_id]);
            header('Location: subscribers.php?toggled=1');
            exit();
        }
    }
}

// Handle success messages
$success_message = '';
if (isset($_GET['deleted']) && $_GET['deleted'] == 1) {
    $success_message = 'Subscriber deleted successfully!';
}
if (isset($_GET['toggled']) && $_GET['toggled'] == 1) {
    $success_message = 'Subscriber status updated successfully!';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Newsletter Subscribers - Admin</title>
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
        
        .subscribers-table {
            width: 100%;
            background: var(--dark-secondary);
            border-radius: 10px;
            overflow: hidden;
            box-shadow: var(--shadow-md);
        }
        
        .subscribers-table table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .subscribers-table th {
            background: var(--dark-tertiary);
            color: var(--text-primary);
            padding: 1rem;
            text-align: left;
            font-weight: 600;
        }
        
        .subscribers-table td {
            padding: 1rem;
            border-bottom: 1px solid var(--border-color);
            color: var(--text-secondary);
        }
        
        .subscribers-table tr:hover {
            background: var(--dark-tertiary);
        }
        
        .subscriber-actions {
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
        
        .btn-toggle {
            background: var(--warning-color);
            color: white;
        }
        
        .btn-delete {
            background: var(--error-color);
            color: white;
        }
        
        .alert-success {
            background: var(--success-color);
            color: white;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 2rem;
        }
        
        .no-subscribers {
            text-align: center;
            padding: 3rem;
            color: var(--text-muted);
        }
        
        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .status-badge.active {
            background: var(--success-color);
            color: white;
        }
        
        .status-badge.inactive {
            background: var(--text-muted);
            color: var(--text-primary);
        }
        
        .export-btn {
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
        
        .export-btn:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
        }
        
        .stats-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: var(--dark-secondary);
            padding: 1.5rem;
            border-radius: 10px;
            text-align: center;
            box-shadow: var(--shadow-md);
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            color: var(--text-secondary);
            font-size: 0.9rem;
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
                    <li><a href="subscribers.php" class="active"><i class="fas fa-users"></i> Subscribers</a></li>
                    <li><a href="settings.php"><i class="fas fa-cog"></i> Settings</a></li>
                    <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </nav>
        </aside>
        
        <!-- Main Content -->
        <main class="admin-content">
            <div class="admin-header">
                <h1>Newsletter Subscribers</h1>
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
            
            <!-- Statistics -->
            <div class="stats-cards">
                <?php 
                $total_subscribers = count($all_subscribers);
                $active_subscribers = count(array_filter($all_subscribers, function($s) { return $s['is_active']; }));
                $inactive_subscribers = $total_subscribers - $active_subscribers;
                ?>
                
                <div class="stat-card">
                    <div class="stat-number"><?php echo $total_subscribers; ?></div>
                    <div class="stat-label">Total Subscribers</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-number"><?php echo $active_subscribers; ?></div>
                    <div class="stat-label">Active</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-number"><?php echo $inactive_subscribers; ?></div>
                    <div class="stat-label">Inactive</div>
                </div>
            </div>
            
            <a href="export-subscribers.php" class="export-btn">
                <i class="fas fa-download"></i> Export Subscribers
            </a>
            
            <?php if (!empty($all_subscribers)): ?>
                <div class="subscribers-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Email</th>
                                <th>Name</th>
                                <th>Status</th>
                                <th>Subscribe Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($all_subscribers as $subscriber): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo xss_clean($subscriber['email']); ?></strong>
                                    </td>
                                    <td><?php echo xss_clean($subscriber['name'] ?? 'N/A'); ?></td>
                                    <td>
                                        <span class="status-badge <?php echo $subscriber['is_active'] ? 'active' : 'inactive'; ?>">
                                            <?php echo $subscriber['is_active'] ? 'Active' : 'Inactive'; ?>
                                        </span>
                                    </td>
                                    <td><?php echo format_date($subscriber['subscribe_date'], 'M j, Y'); ?></td>
                                    <td>
                                        <div class="subscriber-actions">
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="action" value="toggle_status">
                                                <input type="hidden" name="subscriber_id" value="<?php echo $subscriber['id']; ?>">
                                                <input type="hidden" name="current_status" value="<?php echo $subscriber['is_active']; ?>">
                                                <button type="submit" class="btn-sm btn-toggle" title="Toggle status">
                                                    <i class="fas fa-power-off"></i>
                                                </button>
                                            </form>
                                            
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="subscriber_id" value="<?php echo $subscriber['id']; ?>">
                                                <button type="submit" class="btn-sm btn-delete" onclick="return confirm('Are you sure you want to delete this subscriber?');">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="no-subscribers">
                    <h3>No subscribers found</h3>
                    <p>When people subscribe to your newsletter, they will appear here.</p>
                </div>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>
