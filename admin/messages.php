<?php
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/functions.php';

// Start secure session and require login
start_secure_session();
require_login();

// Get all messages
$all_messages = fetchAll("SELECT * FROM contact_messages ORDER BY created_at DESC");

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'mark_read') {
        $message_id = $_POST['message_id'] ?? 0;
        if ($message_id) {
            updateData('contact_messages', ['is_read' => 1], 'id = ?', [$message_id]);
            header('Location: messages.php?marked=1');
            exit();
        }
    }
    
    if ($action === 'delete') {
        $message_id = $_POST['message_id'] ?? 0;
        if ($message_id) {
            deleteData('contact_messages', 'id = ?', [$message_id]);
            header('Location: messages.php?deleted=1');
            exit();
        }
    }
}

// Handle success messages
$success_message = '';
if (isset($_GET['marked']) && $_GET['marked'] == 1) {
    $success_message = 'Message marked as read!';
}
if (isset($_GET['deleted']) && $_GET['deleted'] == 1) {
    $success_message = 'Message deleted successfully!';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - Admin</title>
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
        
        .messages-table {
            width: 100%;
            background: var(--dark-secondary);
            border-radius: 10px;
            overflow: hidden;
            box-shadow: var(--shadow-md);
        }
        
        .messages-table table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .messages-table th {
            background: var(--dark-tertiary);
            color: var(--text-primary);
            padding: 1rem;
            text-align: left;
            font-weight: 600;
        }
        
        .messages-table td {
            padding: 1rem;
            border-bottom: 1px solid var(--border-color);
            color: var(--text-secondary);
        }
        
        .messages-table tr:hover {
            background: var(--dark-tertiary);
        }
        
        .messages-table tr.unread {
            background: rgba(255, 107, 107, 0.1);
            border-left: 3px solid var(--primary-color);
        }
        
        .message-actions {
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
        
        .btn-mark-read {
            background: var(--primary-color);
            color: var(--text-primary);
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
        
        .no-messages {
            text-align: center;
            padding: 3rem;
            color: var(--text-muted);
        }
        
        .message-subject {
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.25rem;
        }
        
        .message-meta {
            font-size: 0.8rem;
            color: var(--text-muted);
            margin-bottom: 0.5rem;
        }
        
        .message-preview {
            line-height: 1.4;
            max-height: 3em;
            overflow: hidden;
        }
        
        .unread-indicator {
            display: inline-block;
            width: 8px;
            height: 8px;
            background: var(--primary-color);
            border-radius: 50%;
            margin-left: 0.5rem;
        }
        
        .message-full {
            line-height: 1.4;
            margin-top: 0.5rem;
            padding: 1rem;
            background: var(--dark-tertiary);
            border-radius: 8px;
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
                    <li><a href="messages.php" class="active">
                        <i class="fas fa-envelope"></i> Messages 
                        <?php 
                        $unread_count = fetchOne("SELECT COUNT(*) as count FROM contact_messages WHERE is_read = 0")['count'];
                        if ($unread_count > 0): ?>
                            <span style="background: var(--error-color); color: white; padding: 2px 6px; border-radius: 10px; font-size: 0.8rem;"><?php echo $unread_count; ?></span>
                        <?php endif; ?>
                    </a></li>
                    <li><a href="subscribers.php"><i class="fas fa-users"></i> Subscribers</a></li>
                    <li><a href="settings.php"><i class="fas fa-cog"></i> Settings</a></li>
                    <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </nav>
        </aside>
        
        <!-- Main Content -->
        <main class="admin-content">
            <div class="admin-header">
                <h1>Contact Messages</h1>
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
            
            <?php if (!empty($all_messages)): ?>
                <div class="messages-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Status</th>
                                <th>From</th>
                                <th>Subject</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($all_messages as $message): ?>
                                <tr class="<?php echo $message['is_read'] ? 'read' : 'unread'; ?>">
                                    <td>
                                        <?php if ($message['is_read']): ?>
                                            <i class="fas fa-envelope-open" style="color: var(--text-muted);"></i>
                                        <?php else: ?>
                                            <i class="fas fa-envelope" style="color: var(--primary-color);"></i>
                                            <span class="unread-indicator"></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div><?php echo xss_clean($message['name']); ?></div>
                                        <small style="color: var(--text-muted);"><?php echo xss_clean($message['email']); ?></small>
                                    </td>
                                    <td>
                                        <div class="message-subject"><?php echo xss_clean($message['subject']); ?></div>
                                        <div class="message-preview"><?php echo truncate_text(xss_clean($message['message']), 100); ?></div>
                                    </td>
                                    <td><?php echo format_date($message['created_at'], 'M j, Y H:i'); ?></td>
                                    <td>
                                        <div class="message-actions">
                                            <?php if (!$message['is_read']): ?>
                                                <form method="POST" style="display: inline;">
                                                    <input type="hidden" name="action" value="mark_read">
                                                    <input type="hidden" name="message_id" value="<?php echo $message['id']; ?>">
                                                    <button type="submit" class="btn-sm btn-mark-read" title="Mark as read">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                            
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="message_id" value="<?php echo $message['id']; ?>">
                                                <button type="submit" class="btn-sm btn-delete" onclick="return confirm('Are you sure you want to delete this message?');">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="5" style="padding: 0;">
                                        <div class="message-full">
                                            <strong>Full Message:</strong><br>
                                            <?php echo nl2br(xss_clean($message['message'])); ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="no-messages">
                    <h3>No messages found</h3>
                    <p>When people contact you through the website, their messages will appear here.</p>
                </div>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>
