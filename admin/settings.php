<?php
// Start session and check login
session_start();

// Simple authentication check
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Include database
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/functions.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'update_settings') {
        // Update configuration (in a real app, you'd update a settings table)
        $success_message = 'Settings updated successfully!';
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Admin</title>
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
            margin-left: 0;
            padding: 2rem;
            background: transparent;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
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
        
        .settings-form {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.05) 0%, rgba(255, 255, 255, 0.02) 100%);
            padding: 2.5rem;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.4), 0 5px 15px rgba(255, 107, 107, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            max-width: 800px;
            margin: 0 auto;
            position: relative;
            overflow: hidden;
        }
        
        .settings-form::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color), var(--primary-color));
            background-size: 200% 100%;
            animation: shimmerGradient 3s linear infinite;
        }
        
        .form-group {
            margin-bottom: 2rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--text-primary);
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.9rem;
        }
        
        .form-control {
            width: 100%;
            padding: 1rem;
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.1);
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
        
        .textarea.form-control {
            resize: vertical;
            min-height: 100px;
        }
        
        .btn-save {
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            color: var(--text-primary);
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .btn-save:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(255, 107, 107, 0.2);
        }
        
        .alert-success {
            background: var(--success-color);
            color: white;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 2rem;
        }
        
        .settings-section {
            margin-bottom: 3rem;
        }
        
        .settings-section h3 {
            color: var(--text-primary);
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid var(--border-color);
        }
        
        .info-box {
            background: var(--dark-tertiary);
            padding: 1.5rem;
            border-radius: 10px;
            margin-top: 1rem;
        }
        
        .info-box h4 {
            color: var(--primary-color);
            margin-bottom: 1rem;
        }
        
        .info-box p {
            color: var(--text-secondary);
            line-height: 1.6;
            margin-bottom: 0.5rem;
        }
        
        .current-settings {
            background: var(--dark-tertiary);
            padding: 1rem;
            border-radius: 8px;
            margin-top: 0.5rem;
        }
        
        .current-settings strong {
            color: var(--text-primary);
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
                    <li><a href="settings.php" class="active"><i class="fas fa-cog"></i> Settings</a></li>
                    <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </nav>
        </aside>
        
        <!-- Main Content -->
        <main class="admin-content">
            <div class="admin-header">
                <h1>Settings</h1>
                <div class="admin-user">
                    <span>Welcome, <?php echo $_SESSION['username']; ?></span>
                    <i class="fas fa-user-circle"></i>
                </div>
            </div>
            
            <?php if (isset($success_message)): ?>
                <div class="alert-success">
                    <?php echo $success_message; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" class="settings-form">
                <input type="hidden" name="action" value="update_settings">
                
                <div class="settings-section">
                    <h3><i class="fas fa-globe"></i> General Settings</h3>
                    
                    <div class="form-group">
                        <label for="app_name">Application Name</label>
                        <input type="text" id="app_name" name="app_name" class="form-control" 
                               value="<?php echo APP_NAME; ?>" readonly>
                        <small style="color: var(--text-muted);">Edit in config/config.php</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="app_url">Application URL</label>
                        <input type="text" id="app_url" name="app_url" class="form-control" 
                               value="<?php echo APP_URL; ?>" readonly>
                        <small style="color: var(--text-muted);">Edit in config/config.php</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="from_email">From Email</label>
                        <input type="email" id="from_email" name="from_email" class="form-control" 
                               value="<?php echo FROM_EMAIL; ?>" readonly>
                        <small style="color: var(--text-muted);">Edit in config/config.php</small>
                    </div>
                </div>
                
                <div class="settings-section">
                    <h3><i class="fas fa-database"></i> Database Configuration</h3>
                    
                    <div class="current-settings">
                        <p><strong>Database Host:</strong> <?php echo DB_HOST; ?></p>
                        <p><strong>Database Name:</strong> <?php echo DB_NAME; ?></p>
                        <p><strong>Database User:</strong> <?php echo DB_USER; ?></p>
                        <p><strong>Connection Status:</strong> 
                            <?php 
                            try {
                                $db = new Database();
                                $conn = $db->getConnection();
                                if ($conn) {
                                    echo '<span style="color: var(--success-color);">Connected</span>';
                                } else {
                                    echo '<span style="color: var(--error-color);">Not Connected</span>';
                                }
                            } catch (Exception $e) {
                                echo '<span style="color: var(--error-color);">Error: ' . $e->getMessage() . '</span>';
                            }
                            ?>
                        </p>
                    </div>
                </div>
                
                <div class="settings-section">
                    <h3><i class="fas fa-server"></i> Server Information</h3>
                    
                    <div class="info-box">
                        <h4>PHP Version</h4>
                        <p><?php echo PHP_VERSION; ?></p>
                        
                        <h4>Server Software</h4>
                        <p><?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'; ?></p>
                        
                        <h4>Document Root</h4>
                        <p><?php echo $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown'; ?></p>
                        
                        <h4>Current Time</h4>
                        <p><?php echo date('Y-m-d H:i:s'); ?></p>
                    </div>
                </div>
                
                <button type="submit" class="btn-save">
                    <i class="fas fa-save"></i> Save Settings
                </button>
            </form>
        </main>
    </div>
</body>
</html>
