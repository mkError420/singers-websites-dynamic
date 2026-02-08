<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/functions.php';

// Start secure session and require login
start_secure_session();
require_login();

// Get all tour dates
$all_tours = fetchAll("SELECT * FROM tour_dates ORDER BY event_date DESC");

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'delete') {
        $tour_id = $_POST['tour_id'] ?? 0;
        if ($tour_id) {
            deleteData('tour_dates', 'id = ?', [$tour_id]);
            header('Location: tour.php?deleted=1');
            exit();
        }
    }
}

// Handle success messages
$success_message = '';
if (isset($_GET['added']) && $_GET['added'] == 1) {
    $success_message = 'Tour date added successfully!';
}
if (isset($_GET['updated']) && $_GET['updated'] == 1) {
    $success_message = 'Tour date updated successfully!';
}
if (isset($_GET['deleted']) && $_GET['deleted'] == 1) {
    $success_message = 'Tour date deleted successfully!';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Tour Dates - Admin</title>
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
        
        .tour-table {
            width: 100%;
            background: var(--dark-secondary);
            border-radius: 10px;
            overflow: hidden;
            box-shadow: var(--shadow-md);
        }
        
        .tour-table table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .tour-table th {
            background: var(--dark-tertiary);
            color: var(--text-primary);
            padding: 1rem;
            text-align: left;
            font-weight: 600;
        }
        
        .tour-table td {
            padding: 1rem;
            border-bottom: 1px solid var(--border-color);
            color: var(--text-secondary);
        }
        
        .tour-table tr:hover {
            background: var(--dark-tertiary);
        }
        
        .tour-actions {
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
        
        .btn-edit {
            background: var(--primary-color);
            color: var(--text-primary);
        }
        
        .btn-delete {
            background: var(--error-color);
            color: white;
        }
        
        .add-tour-btn {
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
        
        .add-tour-btn:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
        }
        
        .alert-success {
            background: var(--success-color);
            color: white;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 2rem;
        }
        
        .no-tours {
            text-align: center;
            padding: 3rem;
            color: var(--text-muted);
        }
        
        .tour-status {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .tour-status.upcoming {
            background: var(--success-color);
            color: white;
        }
        
        .tour-status.past {
            background: var(--text-muted);
            color: var(--text-primary);
        }
        
        .ticket-link {
            color: var(--primary-color);
            text-decoration: none;
        }
        
        .ticket-link:hover {
            color: var(--secondary-color);
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
                    <li><a href="tour.php" class="active"><i class="fas fa-calendar-alt"></i> Tour Dates</a></li>
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
                <h1>Manage Tour Dates</h1>
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
            
            <a href="add-tour.php" class="add-tour-btn">
                <i class="fas fa-plus"></i> Add New Tour Date
            </a>
            
            <?php if (!empty($all_tours)): ?>
                <div class="tour-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Event Name</th>
                                <th>Venue</th>
                                <th>Location</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($all_tours as $tour): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo xss_clean($tour['event_name']); ?></strong>
                                        <?php if ($tour['is_active']): ?>
                                            <span style="color: var(--success-color);">●</span>
                                        <?php else: ?>
                                            <span style="color: var(--text-muted);">○</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo xss_clean($tour['venue']); ?></td>
                                    <td><?php echo xss_clean($tour['city'] . ', ' . $tour['country']); ?></td>
                                    <td>
                                        <?php echo format_date($tour['event_date'], 'M j, Y'); ?>
                                        <?php if ($tour['event_time']): ?>
                                            <br><small><?php echo format_time($tour['event_time']); ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php 
                                        $tour_date = new DateTime($tour['event_date']);
                                        $today = new DateTime();
                                        if ($tour_date >= $today): ?>
                                            <span class="tour-status upcoming">Upcoming</span>
                                        <?php else: ?>
                                            <span class="tour-status past">Past</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="tour-actions">
                                            <a href="edit-tour.php?id=<?php echo $tour['id']; ?>" class="btn-sm btn-edit">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="tour_id" value="<?php echo $tour['id']; ?>">
                                                <button type="submit" class="btn-sm btn-delete" onclick="return confirm('Are you sure you want to delete this tour date?');">
                                                    <i class="fas fa-trash"></i> Delete
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
                <div class="no-tours">
                    <h3>No tour dates found</h3>
                    <p>Start by adding your first tour date!</p>
                    <a href="add-tour.php" class="add-tour-btn">
                        <i class="fas fa-plus"></i> Add Your First Tour Date
                    </a>
                </div>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>
