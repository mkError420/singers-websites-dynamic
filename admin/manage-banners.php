<?php
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/functions.php';

// Check if user is logged in
require_login();

// Handle banner operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add_banner') {
        $page_name = sanitize_input($_POST['page_name']);
        $title = sanitize_input($_POST['title']);
        $subtitle = sanitize_input($_POST['subtitle']);
        $type = sanitize_input($_POST['type']);
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        
        // Handle file upload
        $image_url = null;
        $video_url = null;
        
        if ($type === 'image' && isset($_FILES['image_file'])) {
            $upload_result = upload_file($_FILES['image_file'], 'assets/images/banners/', ['jpg', 'jpeg', 'png', 'gif']);
            if ($upload_result) {
                $image_url = $upload_result;
            }
        } elseif ($type === 'video' && isset($_FILES['video_file'])) {
            $upload_result = upload_file($_FILES['video_file'], 'assets/videos/banners/', ['mp4', 'webm']);
            if ($upload_result) {
                $video_url = $upload_result;
            }
        }
        
        $banner_data = [
            'page_name' => $page_name,
            'title' => $title,
            'subtitle' => $subtitle,
            'image_url' => $image_url,
            'video_url' => $video_url,
            'type' => $type,
            'is_active' => $is_active
        ];
        
        if (insertData('page_banners', $banner_data)) {
            $_SESSION['success'] = 'Banner added successfully!';
        } else {
            $_SESSION['error'] = 'Failed to add banner.';
        }
        
        redirect('admin/manage-banners.php');
    }
    
    elseif ($action === 'toggle_banner') {
        $banner_id = (int)$_POST['banner_id'];
        $is_active = (int)$_POST['is_active'];
        
        global $pdo;
        $stmt = $pdo->prepare("UPDATE page_banners SET is_active = ? WHERE id = ?");
        if ($stmt->execute([$is_active, $banner_id])) {
            $_SESSION['success'] = 'Banner updated successfully!';
        } else {
            $_SESSION['error'] = 'Failed to update banner.';
        }
        
        redirect('admin/manage-banners.php');
    }
    
    elseif ($action === 'delete_banner') {
        $banner_id = (int)$_POST['banner_id'];
        
        global $pdo;
        $stmt = $pdo->prepare("DELETE FROM page_banners WHERE id = ?");
        if ($stmt->execute([$banner_id])) {
            $_SESSION['success'] = 'Banner deleted successfully!';
        } else {
            $_SESSION['error'] = 'Failed to delete banner.';
        }
        
        redirect('admin/manage-banners.php');
    }
}

// Get all banners
global $pdo;
$stmt = $pdo->prepare("
    SELECT * FROM page_banners 
    ORDER BY page_name, created_at DESC
");
$stmt->execute();
$banners = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Group banners by page
$grouped_banners = [];
foreach ($banners as $banner) {
    $grouped_banners[$banner['page_name']][] = $banner;
}

$pages = ['music', 'tour', 'contact', 'videos', 'about'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Banners - Admin</title>
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/assets/css/style.css">
    <style>
        .admin-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        .admin-header {
            margin-bottom: 2rem;
        }
        
        .admin-header h1 {
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }
        
        .admin-header p {
            color: var(--text-secondary);
        }
        
        .banner-form {
            background: var(--dark-secondary);
            padding: 2rem;
            border-radius: 15px;
            margin-bottom: 3rem;
            box-shadow: var(--shadow-lg);
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text-primary);
            font-weight: 500;
        }
        
        .form-control {
            width: 100%;
            padding: 0.75rem;
            background: var(--dark-tertiary);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            color: var(--text-primary);
            font-size: 1rem;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(255, 107, 107, 0.1);
        }
        
        .banner-list {
            display: grid;
            gap: 2rem;
        }
        
        .page-section {
            background: var(--dark-secondary);
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: var(--shadow-md);
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border-color);
        }
        
        .page-title {
            color: var(--text-primary);
            font-size: 1.3rem;
            font-weight: 600;
            text-transform: capitalize;
        }
        
        .banner-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            background: var(--dark-tertiary);
            border-radius: 10px;
            margin-bottom: 1rem;
            transition: transform 0.3s ease;
        }
        
        .banner-item:hover {
            transform: translateX(5px);
        }
        
        .banner-preview {
            width: 80px;
            height: 60px;
            border-radius: 8px;
            object-fit: cover;
            background: var(--dark-secondary);
        }
        
        .banner-info {
            flex: 1;
        }
        
        .banner-info h4 {
            color: var(--text-primary);
            margin-bottom: 0.25rem;
        }
        
        .banner-info p {
            color: var(--text-secondary);
            font-size: 0.9rem;
            margin-bottom: 0.25rem;
        }
        
        .banner-meta {
            color: var(--text-muted);
            font-size: 0.8rem;
        }
        
        .banner-actions {
            display: flex;
            gap: 0.5rem;
        }
        
        .btn-sm {
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
        }
        
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .status-active {
            background: var(--success-color);
            color: white;
        }
        
        .status-inactive {
            background: var(--dark-tertiary);
            color: var(--text-muted);
        }
        
        .file-input-wrapper {
            position: relative;
            overflow: hidden;
            display: inline-block;
            width: 100%;
        }
        
        .file-input-wrapper input[type=file] {
            position: absolute;
            left: -9999px;
        }
        
        .file-input-label {
            display: block;
            padding: 0.75rem;
            background: var(--dark-tertiary);
            border: 2px dashed var(--border-color);
            border-radius: 8px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .file-input-label:hover {
            border-color: var(--primary-color);
            background: rgba(255, 107, 107, 0.05);
        }
        
        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .banner-item {
                flex-direction: column;
                text-align: center;
            }
            
            .banner-actions {
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <h1>Manage Page Banners</h1>
            <p>Add and manage banners for different pages</p>
            <a href="<?php echo APP_URL; ?>/admin/" class="btn btn-secondary" style="margin-top: 1rem;">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>
        
        <!-- Add Banner Form -->
        <div class="banner-form">
            <h2>Add New Banner</h2>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add_banner">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="page_name">Page *</label>
                        <select name="page_name" id="page_name" class="form-control" required>
                            <option value="">Select a page</option>
                            <?php foreach ($pages as $page): ?>
                                <option value="<?php echo $page; ?>"><?php echo ucfirst($page); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="type">Banner Type *</label>
                        <select name="type" id="type" class="form-control" required onchange="toggleFileInput()">
                            <option value="image">Image</option>
                            <option value="video">Video</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="title">Title *</label>
                    <input type="text" name="title" id="title" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="subtitle">Subtitle</label>
                    <textarea name="subtitle" id="subtitle" class="form-control" rows="3"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="image_file">Image File</label>
                    <div class="file-input-wrapper">
                        <input type="file" name="image_file" id="image_file" accept="image/*">
                        <label for="image_file" class="file-input-label">
                            <i class="fas fa-cloud-upload-alt"></i> Choose Image File
                        </label>
                    </div>
                </div>
                
                <div class="form-group" id="video_file_group" style="display: none;">
                    <label for="video_file">Video File</label>
                    <div class="file-input-wrapper">
                        <input type="file" name="video_file" id="video_file" accept="video/*">
                        <label for="video_file" class="file-input-label">
                            <i class="fas fa-cloud-upload-alt"></i> Choose Video File
                        </label>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>
                        <input type="checkbox" name="is_active" checked> Active
                    </label>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add Banner
                </button>
            </form>
        </div>
        
        <!-- Existing Banners -->
        <div class="banner-list">
            <?php foreach ($pages as $page): ?>
                <?php if (isset($grouped_banners[$page]) && !empty($grouped_banners[$page])): ?>
                    <div class="page-section">
                        <div class="page-header">
                            <h3 class="page-title"><?php echo ucfirst($page); ?> Page Banners</h3>
                            <span class="status-badge <?php echo $grouped_banners[$page][0]['is_active'] ? 'status-active' : 'status-inactive'; ?>">
                                <?php echo $grouped_banners[$page][0]['is_active'] ? 'Active' : 'Inactive'; ?>
                            </span>
                        </div>
                        
                        <?php foreach ($grouped_banners[$page] as $banner): ?>
                            <div class="banner-item">
                                <?php if ($banner['type'] === 'image' && $banner['image_url']): ?>
                                    <img src="<?php echo APP_URL . '/' . $banner['image_url']; ?>" alt="Banner" class="banner-preview">
                                <?php else: ?>
                                    <div class="banner-preview" style="display: flex; align-items: center; justify-content: center; background: var(--dark-tertiary);">
                                        <i class="fas fa-video" style="color: var(--text-muted);"></i>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="banner-info">
                                    <h4><?php echo xss_clean($banner['title']); ?></h4>
                                    <p><?php echo xss_clean($banner['subtitle']); ?></p>
                                    <div class="banner-meta">
                                        Type: <?php echo ucfirst($banner['type']); ?> | 
                                        Created: <?php echo format_date($banner['created_at'], 'M j, Y'); ?>
                                    </div>
                                </div>
                                
                                <div class="banner-actions">
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="action" value="toggle_banner">
                                        <input type="hidden" name="banner_id" value="<?php echo $banner['id']; ?>">
                                        <input type="hidden" name="is_active" value="<?php echo $banner['is_active'] ? 0 : 1; ?>">
                                        <button type="submit" class="btn btn-sm <?php echo $banner['is_active'] ? 'secondary' : 'primary'; ?>">
                                            <?php echo $banner['is_active'] ? 'Deactivate' : 'Activate'; ?>
                                        </button>
                                    </form>
                                    
                                    <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this banner?');">
                                        <input type="hidden" name="action" value="delete_banner">
                                        <input type="hidden" name="banner_id" value="<?php echo $banner['id']; ?>">
                                        <button type="submit" class="btn btn-sm secondary">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
    
    <script>
        function toggleFileInput() {
            const type = document.getElementById('type').value;
            const imageGroup = document.getElementById('image_file').closest('.form-group');
            const videoGroup = document.getElementById('video_file_group');
            
            if (type === 'video') {
                imageGroup.style.display = 'none';
                videoGroup.style.display = 'block';
            } else {
                imageGroup.style.display = 'block';
                videoGroup.style.display = 'none';
            }
        }
        
        // Update file input labels
        document.getElementById('image_file').addEventListener('change', function(e) {
            const label = this.nextElementSibling;
            if (e.target.files.length > 0) {
                label.innerHTML = '<i class="fas fa-check"></i> ' + e.target.files[0].name;
            }
        });
        
        document.getElementById('video_file').addEventListener('change', function(e) {
            const label = this.nextElementSibling;
            if (e.target.files.length > 0) {
                label.innerHTML = '<i class="fas fa-check"></i> ' + e.target.files[0].name;
            }
        });
    </script>
</body>
</html>
