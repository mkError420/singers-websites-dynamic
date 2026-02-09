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

$page_title = 'Manage Tour Dates';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/database.php';

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
    <title><?php echo $page_title; ?> - Admin</title>
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
        
        .admin-card {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.03) 0%, rgba(255, 255, 255, 0.01) 100%);
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .admin-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(270deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0.05) 100%);
            border-radius: 20px;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            animation: shimmerGradient 4s linear infinite;
        }
        
        .admin-card h3 {
            color: var(--text-primary);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .admin-table {
            width: 100%;
            border-collapse: collapse;
            background: var(--dark-secondary);
            border-radius: 10px;
            overflow: hidden;
        }
        
        .admin-table th {
            background: var(--dark-tertiary);
            color: var(--text-primary);
            padding: 1rem;
            text-align: left;
            font-weight: 600;
        }
        
        .admin-table td {
            padding: 1rem;
            border-bottom: 1px solid var(--border-color);
        }
        
        .admin-table tr:last-child td {
            border-bottom: none;
        }
        
        .tour-status {
            padding: 0.25rem 0.5rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .tour-status.upcoming {
            background: var(--success-color);
            color: white;
        }
        
        .tour-status.past {
            background: var(--text-muted);
            color: var(--text-primary);
        }
        
        .tour-actions {
            display: flex;
            gap: 0.5rem;
            justify-content: center;
        }
        
        .tour-actions a {
            padding: 0.5rem 1rem;
            color: var(--text-primary);
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .tour-actions a:hover {
            background: var(--dark-tertiary);
            color: var(--primary-color);
            transform: translateY(-2px);
        }
        
        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-sm {
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
        }
        
        .btn-edit {
            background: var(--primary-color);
            color: var(--text-primary);
        }
        
        .btn-delete {
            background: var(--error-color);
            color: var(--text-primary);
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }
        
        .add-tour-btn {
            display: inline-block;
            background: var(--primary-color);
            color: var(--text-primary);
            padding: 1rem 2rem;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            margin-top: 2rem;
        }
        
        .add-tour-btn:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }
        
        .no-tours {
            text-align: center;
            padding: 3rem;
            color: var(--text-secondary);
        }
        
        .no-tours h3 {
            margin-bottom: 1rem;
        }
        
        .no-tours p {
            margin-bottom: 1rem;
        }
        
        .map-container {
            margin-top: 1rem;
        }
        
        .map-container iframe {
            border: 0;
            border-radius: 8px;
        }
        
        .map-info {
            margin-top: 1rem;
            text-align: center;
        }
        
        .map-info p {
            margin-bottom: 0.5rem;
        }
        
        .map-info strong {
            color: var(--primary-color);
        }
        
        .alert-success {
            background: var(--success-color);
            color: white;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 2rem;
        }
        
        .activity-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .activity-item {
            background: var(--dark-secondary);
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 10px;
            transition: all 0.3s ease;
        }
        
        .activity-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .activity-title {
            color: var(--text-primary);
            font-weight: 600;
        }
        
        .activity-meta {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }
        
        .activity-date {
            color: var(--primary-color);
            font-weight: 600;
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
                <div class="admin-card">
                    <h3>All Tour Dates</h3>
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Event Name</th>
                                <th>Venue</th>
                                <th>Location</th>
                                <th>Date</th>
                                <th>Time</th>
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
                                            <span class="tour-status upcoming">●</span>
                                        <?php else: ?>
                                            <span class="tour-status past">○</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo xss_clean($tour['venue']); ?></td>
                                    <td><?php echo xss_clean($tour['city']) . ', ' . xss_clean($tour['country']); ?></td>
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
            
            <!-- Bangladesh Tour Map -->
            <div class="admin-card">
                <h3>Tour Map - Bangladesh</h3>
                <div class="tour-map-container">
                    <iframe 
                            src="https://www.google.com/maps?q=<?php echo urlencode($tour['venue'] . ', ' . urlencode($tour['city']) . ', ' . urlencode($tour['country'])); ?>&z=12&zoom=6&language=en&t=<?php echo time(); ?>"
                            width="100%" 
                            height="400" 
                            style="border:0; border-radius:8px;"
                            allowfullscreen
                            sandbox="allow-scripts allow-same-origin allow-popups allow-forms"
                            frameborder="0" 
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen>
                    </iframe>
                    <div class="map-info">
                        <p><small>Interactive map showing tour locations across Bangladesh</small></p>
                        <p><strong>Major Cities:</strong> Dhaka, Chittagong, Khulna, Rajshahi, Sylhet, Barisal, Rangpur</p>
                        <p><strong>Tourist Areas:</strong> Cox's Bazar, Saint Martin, Bandarban, Rangamati</p>
                        <p><strong>Historical Sites:</strong> Lalbagh Fort, Paharpur, Mahasthangarh</p>
                        <p><strong>Current Tour:</strong> <span style="color: var(--success-color); font-weight: bold;"><?php echo xss_clean($tour['event_name']); ?> - <?php echo xss_clean($tour['venue']); ?></span></p>
                        <p><a href="https://www.google.com/maps/place/Bangladesh" target="_blank">🗺️ View Full Bangladesh Map</a></p>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
