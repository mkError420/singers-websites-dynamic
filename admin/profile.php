<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/functions.php';

// Start session and check login
session_start();

// Simple authentication check
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'update_profile':
                $profile_image = $_POST['profile_image'] ?? '';
                $artist_name = $_POST['artist_name'] ?? '';
                $tagline = $_POST['tagline'] ?? '';
                $bio = $_POST['bio'] ?? '';
                $years_experience = $_POST['years_experience'] ?? '';
                $songs_count = $_POST['songs_count'] ?? '';
                $views_count = $_POST['views_count'] ?? '';
                $status = $_POST['status'] ?? 'active';
                
                // Handle image upload
                if (isset($_FILES['profile_image_file']) && $_FILES['profile_image_file']['error'] === UPLOAD_ERR_OK) {
                    $upload_result = upload_file($_FILES['profile_image_file'], 'uploads/profile/', ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                    if ($upload_result['success']) {
                        $profile_image = $upload_result['filepath'];
                    } else {
                        $error = $upload_result['message'];
                    }
                }
                
                if (!isset($error)) {
                    $profile_data = [
                        'profile_image' => $profile_image,
                        'artist_name' => $artist_name,
                        'tagline' => $tagline,
                        'bio' => $bio,
                        'years_experience' => $years_experience,
                        'songs_count' => $songs_count,
                        'views_count' => $views_count,
                        'status' => $status
                    ];
                    
                    // Check if profile exists
                    $existing = fetchOne("SELECT id FROM profile LIMIT 1");
                    if ($existing) {
                        $updated = updateData('profile', $profile_data, 'id = ?', [$existing['id']]);
                    } else {
                        $profile_data['created_at'] = date('Y-m-d H:i:s');
                        $updated = insertData('profile', $profile_data);
                    }
                    
                    if ($updated) {
                        $success = 'Profile updated successfully!';
                    } else {
                        $error = 'Failed to update profile.';
                    }
                }
                break;
        }
    }
}

// Get current profile data
$profile = fetchOne("SELECT * FROM profile ORDER BY id DESC LIMIT 1");
if (!$profile) {
    $profile = [
        'profile_image' => 'assets/images/artist-photo.jpg',
        'artist_name' => '',
        'tagline' => '',
        'bio' => '',
        'years_experience' => '10+',
        'songs_count' => '50+',
        'views_count' => '1M+',
        'status' => 'active'
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Management - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .visit-website-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            background: var(--primary-color);
            color: var(--text-primary);
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 25px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            z-index: 1000;
            box-shadow: var(--shadow-lg);
        }
        
        .visit-website-btn:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: var(--shadow-xl);
        }
        
        body {
            background: var(--dark-bg);
            margin: 0;
            padding: 0;
        }
        
        .admin-layout {
            display: flex;
            min-height: 100vh;
        }
        
        .admin-sidebar {
            width: 250px;
            background: var(--dark-secondary);
            padding: 2rem 0;
            border-right: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .admin-content {
            flex: 1;
            padding: 2rem;
            overflow-y: auto;
        }
        
        .content-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .header-title h1 {
            margin: 0;
            color: var(--text-primary);
            font-size: 2rem;
        }
        
        .header-title p {
            margin: 0.5rem 0 0 0;
            color: var(--text-secondary);
        }
        
        .card {
            background: linear-gradient(145deg, var(--dark-secondary) 0%, var(--dark-tertiary) 100%);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4), 0 5px 15px rgba(255, 107, 107, 0.1);
            margin-bottom: 2rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5), 0 10px 25px rgba(255, 107, 107, 0.15);
        }
        
        .card-header {
            padding: 2rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            background: linear-gradient(90deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            position: relative;
            overflow: hidden;
        }
        
        .card-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            animation: shimmerHeader 3s infinite;
        }
        
        @keyframes shimmerHeader {
            0% { left: -100%; }
            100% { left: 100%; }
        }
        
        .card-header h3 {
            margin: 0 0 0.5rem 0;
            color: #ffffff;
            font-size: 1.5rem;
            font-weight: 700;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .card-subtitle {
            margin: 0;
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.95rem;
            font-weight: 400;
            position: relative;
            z-index: 1;
            font-style: italic;
        }
        
        .card-body {
            padding: 2.5rem;
            background: var(--dark-secondary);
        }
        
        .profile-form {
            max-width: 900px;
            margin: 0 auto;
        }
        
        .form-section {
            margin-bottom: 3rem;
            padding: 2rem;
            background: linear-gradient(145deg, var(--dark-tertiary) 0%, var(--dark-secondary) 100%);
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.05);
            position: relative;
            overflow: hidden;
        }
        
        .form-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color), var(--primary-color));
            background-size: 200% 100%;
            animation: shimmerGradient 4s linear infinite;
        }
        
        @keyframes shimmerGradient {
            0% { background-position: 0% 50%; }
            100% { background-position: 200% 50%; }
        }
        
        .section-title {
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .section-title h4 {
            margin: 0 0 0.5rem 0;
            color: var(--text-primary);
            font-size: 1.3rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .section-title h4 i {
            color: var(--primary-color);
            font-size: 1.1rem;
        }
        
        .section-title p {
            margin: 0;
            color: var(--text-secondary);
            font-size: 0.9rem;
            font-style: italic;
            margin-left: 2rem;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }
        
        .form-group {
            margin-bottom: 2rem;
            position: relative;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.75rem;
            font-weight: 700;
            color: var(--text-primary);
            font-size: 0.95rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            position: relative;
            padding-left: 2.5rem;
        }
        
        .form-group label::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 20px;
            height: 20px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            color: white;
        }
        
        /* Basic Information Section Specific Styles */
        .basic-info-section {
            background: linear-gradient(145deg, rgba(76, 175, 80, 0.08) 0%, rgba(76, 175, 80, 0.02) 100%);
            border: 1px solid rgba(76, 175, 80, 0.2);
        }
        
        .basic-info-container {
            position: relative;
        }
        
        .artist-name-group,
        .tagline-group {
            position: relative;
        }
        
        .input-wrapper {
            position: relative;
        }
        
        .artist-name-group input,
        .tagline-group input {
            width: 100%;
            padding: 1rem 3.5rem 1rem 3.5rem;
            border: 2px solid rgba(76, 175, 80, 0.3);
            border-radius: 15px;
            font-size: 1rem;
            background: linear-gradient(145deg, rgba(255, 255, 255, 0.08) 0%, rgba(255, 255, 255, 0.02) 100%);
            color: var(--text-primary);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: inset 0 2px 8px rgba(0, 0, 0, 0.2), 0 4px 15px rgba(76, 175, 80, 0.1);
        }
        
        .artist-name-group input:focus,
        .tagline-group input:focus {
            outline: none;
            border-color: #4caf50;
            background: linear-gradient(145deg, rgba(255, 255, 255, 0.12) 0%, rgba(76, 175, 80, 0.05) 100%);
            box-shadow: 0 0 0 4px rgba(76, 175, 80, 0.15), inset 0 2px 8px rgba(0, 0, 0, 0.1), 0 8px 25px rgba(76, 175, 80, 0.2);
            transform: translateY(-3px) scale(1.01);
        }
        
        .input-decoration {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            padding: 0 1rem;
            pointer-events: none;
        }
        
        .decoration-icon {
            font-size: 1.2rem;
            color: #4caf50;
            animation: float 3s ease-in-out infinite;
        }
        
        .tagline-group .decoration-icon {
            color: #2196f3;
            animation-delay: 1.5s;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-5px); }
        }
        
        .char-counter {
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--text-secondary);
            background: rgba(76, 175, 80, 0.1);
            padding: 0.25rem 0.5rem;
            border-radius: 12px;
            border: 1px solid rgba(76, 175, 80, 0.2);
        }
        
        .tagline-group .char-counter {
            background: rgba(33, 150, 243, 0.1);
            border-color: rgba(33, 150, 243, 0.2);
        }
        
        .field-tips {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-top: 0.75rem;
            padding: 0.75rem 1rem;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.05) 0%, rgba(255, 255, 255, 0.02) 100%);
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            font-size: 0.85rem;
            color: var(--text-secondary);
            transition: all 0.3s ease;
        }
        
        .field-tips:hover {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.08) 0%, rgba(255, 255, 255, 0.04) 100%);
            transform: translateY(-1px);
        }
        
        .field-tips i {
            color: #4caf50;
            font-size: 0.9rem;
        }
        
        .tagline-group .field-tips i {
            color: #2196f3;
        }
        
        .artist-name-group label::before { content: '🎤'; }
        .upload-wrapper-enhanced {
            position: relative;
        }
        
        .upload-area-enhanced {
            border: 3px dashed rgba(156, 39, 176, 0.6);
            border-radius: 20px;
            padding: 3rem 2rem;
            text-align: center;
            background: linear-gradient(145deg, rgba(156, 39, 176, 0.08) 0%, rgba(156, 39, 176, 0.02) 100%);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            position: relative;
            overflow: hidden;
            min-height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .upload-area-enhanced:hover {
            border-color: #9c27b0;
            background: linear-gradient(145deg, rgba(156, 39, 176, 0.15) 0%, rgba(156, 39, 176, 0.05) 100%);
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(156, 39, 176, 0.2);
        }
        
        .upload-area-enhanced.dragover {
            border-color: #9c27b0;
            background: linear-gradient(145deg, rgba(156, 39, 176, 0.2) 0%, rgba(156, 39, 176, 0.1) 100%);
            transform: scale(1.02);
        }
        
        .upload-content {
            position: relative;
            z-index: 1;
        }
        
        .upload-icon-large {
            font-size: 4rem;
            margin-bottom: 1.5rem;
            animation: float 3s ease-in-out infinite;
            display: block;
        }
        
        .upload-text-enhanced h5 {
            margin: 0 0 0.5rem 0;
            color: var(--text-primary);
            font-size: 1.3rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .upload-text-enhanced p {
            margin: 0 0 1.5rem 0;
            color: var(--text-secondary);
            font-size: 1rem;
            font-weight: 500;
        }
        
        .upload-specs-enhanced {
            display: flex;
            justify-content: center;
            gap: 1rem;
            flex-wrap: wrap;
        }
        
        .spec-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: rgba(156, 39, 176, 0.1);
            border-radius: 25px;
            border: 1px solid rgba(156, 39, 176, 0.2);
            font-size: 0.85rem;
            color: var(--text-secondary);
            transition: all 0.3s ease;
        }
        
        .spec-item:hover {
            background: rgba(156, 39, 176, 0.15);
            transform: translateY(-1px);
        }
        
        .spec-item i {
            color: #9c27b0;
            font-size: 0.9rem;
        }
        
        .upload-progress {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.8);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 2;
        }
        
        .progress-bar {
            width: 80%;
            height: 6px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 3px;
            overflow: hidden;
            margin-bottom: 1rem;
        }
        
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #9c27b0, #e91e63);
            border-radius: 3px;
            width: 0%;
            transition: width 0.3s ease;
            animation: shimmer 2s infinite;
        }
        
        .progress-text {
            color: white;
            font-size: 1.1rem;
            font-weight: 600;
        }
        
        .upload-preview-enhanced {
            margin-top: 1.5rem;
            text-align: center;
        }
        
        .prev.image-action-btn {
            background: rgba(255, 255, 255, 0.15);
            border: 2px solid rgba(255, 255, 255, 0.25);
            color: white;
            padding: 0.6rem 1.2rem;
            border-radius: 25px;
            cursor: pointer;
            font-size: 0.85rem;
            font-weight: 600;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            gap: 0.6rem;
            backdrop-filter: blur(10px);
            text-transform: uppercase;
            letter-spacing: 0.8px;
            position: relative;
            overflow: hidden;
        }
        
        .image-action-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.6s ease;
        }
        
        .image-action-btn:hover::before {
            left: 100%;
        }
        
        .image-action-btn:hover {
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
        }
        
        .edit-btn {
            background: linear-gradient(135deg, rgba(76, 175, 80, 0.8), rgba(139, 195, 74, 0.8));
            border-color: rgba(76, 175, 80, 0.6);
            color: white;
        }
        
        .edit-btn:hover {
            background: linear-gradient(135deg, rgba(76, 175, 80, 1), rgba(139, 195, 74, 1));
            border-color: #4caf50;
            box-shadow: 0 8px 25px rgba(76, 175, 80, 0.4);
        }
        
        .remove-btn {
            background: linear-gradient(135deg, rgba(244, 67, 54, 0.8), rgba(233, 30, 99, 0.8));
            border-color: rgba(244, 67, 54, 0.6);
            color: white;
        }
        
        .remove-btn:hover {
            background: linear-gradient(135deg, rgba(244, 67, 54, 1), rgba(233, 30, 99, 1));
            border-color: #f44336;
            box-shadow: 0 8px 25px rgba(244, 67, 54, 0.4);
        }
        
        .image-action-btn i {
            font-size: 0.9rem;
            transition: transform 0.3s ease;
        }
        
        .edit-btn:hover i {
            transform: rotate(15deg) scale(1.1);
        }
        
        .remove-btn:hover i {
            transform: rotate(-15deg) scale(1.1);
        }
        
        .preview-placeholder {
            padding: 2rem;
            border: 2px dashed rgba(156, 39, 176, 0.3);
            border-radius: 15px;
            background: rgba(156, 39, 176, 0.05);
            color: var(--text-secondary);
            transition: all 0.3s ease;
        }
        
        .preview-placeholder i {
        }
        
        .edit-btn:hover {
            background: rgba(76, 175, 80, 0.3);
            border-color: #4caf50;
        }
        
        .remove-btn:hover {
            background: rgba(244, 67, 54, 0.3);
            border-color: #f44336;
        }
        
        .image-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        
        .detail-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            font-size: 0.85rem;
        }
        
        .detail-item i {
            color: #9c27b0;
            width: 16px;
        }
        
        .image-actions {
            display: flex;
            gap: 0.75rem;
            margin-top: 1rem;
        }
        
        .action-btn {
            background: linear-gradient(135deg, #9c27b0, #e91e63);
            border: none;
            color: white;
            padding: 0.75rem 1.25rem;
            border-radius: 25px;
            cursor: pointer;
            font-size: 0.85rem;
            font-weight: 600;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(156, 39, 176, 0.3);
        }
        
        .download-btn {
            background: linear-gradient(135deg, #4caf50, #8bc34a);
        }
        
        .view-btn {
            background: linear-gradient(135deg, #2196f3, #03a9f4);
        }
        
        .current-image-group label::before { content: '�️'; }
        .form-group:has([id="years_experience"]) label::before { content: '📅'; }
        .form-group:has([id="songs_count"]) label::before { content: '🎵'; }
        .form-group:has([id="views_count"]) label::before { content: '👁️'; }
        .form-group:has([id="status"]) label::before { content: '⚡'; }
        
        .form-group label::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 2.5rem;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
            border-radius: 1px;
            transition: width 0.3s ease;
        }
        
        .form-group:hover label::after {
            width: 30px;
        }
        
        .form-control {
            width: 100%;
            padding: 1rem 1.25rem 1rem 3.5rem;
            border: 2px solid rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            font-size: 1rem;
            background: linear-gradient(145deg, var(--dark-tertiary) 0%, rgba(255, 255, 255, 0.02) 100%);
            color: var(--text-primary);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: inset 0 2px 8px rgba(0, 0, 0, 0.2), 0 4px 15px rgba(0, 0, 0, 0.1);
            position: relative;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            background: linear-gradient(145deg, var(--dark-secondary) 0%, rgba(255, 107, 107, 0.05) 100%);
            box-shadow: 0 0 0 4px rgba(255, 107, 107, 0.15), inset 0 2px 8px rgba(0, 0, 0, 0.1), 0 8px 25px rgba(255, 107, 107, 0.2);
            transform: translateY(-3px) scale(1.02);
        }
        
        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.4);
            font-style: italic;
            transition: opacity 0.3s ease;
        }
        
        .form-control:focus::placeholder {
            opacity: 0.7;
        }
        
        /* Enhanced Years Experience Field */
        .years-input-wrapper {
            position: relative;
        }
        
        .years-group input {
            width: 100%;
            padding: 1rem 3.5rem 1rem 3.5rem;
            border: 2px solid rgba(33, 150, 243, 0.3);
            border-radius: 15px;
            font-size: 1rem;
            background: linear-gradient(145deg, rgba(255, 255, 255, 0.08) 0%, rgba(255, 255, 255, 0.02) 100%);
            color: var(--text-primary);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: inset 0 2px 8px rgba(0, 0, 0, 0.2), 0 4px 15px rgba(33, 150, 243, 0.1);
        }
        
        .years-group input:focus {
            outline: none;
            border-color: #2196f3;
            background: linear-gradient(145deg, rgba(255, 255, 255, 0.12) 0%, rgba(33, 150, 243, 0.05) 100%);
            box-shadow: 0 0 0 4px rgba(33, 150, 243, 0.15), inset 0 2px 8px rgba(0, 0, 0, 0.1), 0 8px 25px rgba(33, 150, 243, 0.2);
            transform: translateY(-3px) scale(1.01);
        }
        
        .years-slider {
            position: absolute;
            top: 50%;
            right: 1rem;
            transform: translateY(-50%);
            width: 120px;
            opacity: 0.7;
            transition: opacity 0.3s ease;
        }
        
        .years-slider:hover {
            opacity: 1;
        }
        
        .years-slider input[type="range"] {
            width: 100%;
            height: 6px;
            background: transparent;
            outline: none;
            cursor: pointer;
            -webkit-appearance: none;
        }
        
        .years-slider input[type="range"]::-webkit-slider-track {
            width: 100%;
            height: 6px;
            background: linear-gradient(90deg, #2196f3 0%, #4caf50 50%, #2196f3 100%);
            border-radius: 3px;
            border: none;
        }
        
        .years-slider input[type="range"]::-webkit-slider-thumb {
            -webkit-appearance: none;
            width: 20px;
            height: 20px;
            background: linear-gradient(135deg, #4caf50, #8bc34a);
            border-radius: 50%;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(76, 175, 80, 0.3);
            transition: all 0.3s ease;
        }
        
        .years-slider input[type="range"]::-moz-range-track {
            width: 100%;
            height: 6px;
            background: linear-gradient(90deg, #2196f3 0%, #4caf50 50%, #2196f3 100%);
            border-radius: 3px;
            border: none;
        }
        
        .years-slider input[type="range"]::-moz-range-thumb {
            width: 20px;
            height: 20px;
            background: linear-gradient(135deg, #4caf50, #8bc34a);
            border-radius: 50%;
            cursor: pointer;
            border: none;
            box-shadow: 0 2px 8px rgba(76, 175, 80, 0.3);
            transition: all 0.3s ease;
        }
        
        .slider-track {
            position: absolute;
            top: 50%;
            left: 0;
            transform: translateY(-50%);
            width: 100%;
            height: 6px;
            background: rgba(33, 150, 243, 0.2);
            border-radius: 3px;
            pointer-events: none;
        }
        
        .slider-thumb {
            position: absolute;
            top: 50%;
            left: 0;
            transform: translateY(-50%);
            width: 20px;
            height: 20px;
            background: linear-gradient(135deg, #4caf50, #8bc34a);
            border-radius: 50%;
            box-shadow: 0 2px 8px rgba(76, 175, 80, 0.3);
            pointer-events: none;
            transition: all 0.3s ease;
        }
        
        /* Enhanced Songs Count Field */
        .songs-input-wrapper {
            position: relative;
        }
        
        .songs-group input {
            width: 100%;
            padding: 1rem 3.5rem 1rem 3.5rem;
            border: 2px solid rgba(33, 150, 243, 0.3);
            border-radius: 15px;
            font-size: 1rem;
            background: linear-gradient(145deg, rgba(255, 255, 255, 0.08) 0%, rgba(255, 255, 255, 0.02) 100%);
            color: var(--text-primary);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: inset 0 2px 8px rgba(0, 0, 0, 0.2), 0 4px 15px rgba(33, 150, 243, 0.1);
        }
        
        .songs-group input:focus {
            outline: none;
            border-color: #2196f3;
            background: linear-gradient(145deg, rgba(255, 255, 255, 0.12) 0%, rgba(33, 150, 243, 0.05) 100%);
            box-shadow: 0 0 0 4px rgba(33, 150, 243, 0.15), inset 0 2px 8px rgba(0, 0, 0, 0.1), 0 8px 25px rgba(33, 150, 243, 0.2);
            transform: translateY(-3px) scale(1.01);
        }
        
        .songs-slider {
            position: absolute;
            top: 50%;
            right: 1rem;
            transform: translateY(-50%);
            width: 120px;
            opacity: 0.7;
            transition: opacity 0.3s ease;
        }
        
        .songs-slider:hover {
            opacity: 1;
        }
        
        .songs-slider input[type="range"] {
            width: 100%;
            height: 6px;
            background: transparent;
            outline: none;
            cursor: pointer;
            -webkit-appearance: none;
        }
        
        .songs-slider input[type="range"]::-webkit-slider-track {
            width: 100%;
            height: 6px;
            background: linear-gradient(90deg, #2196f3 0%, #4caf50 50%, #2196f3 100%);
            border-radius: 3px;
            border: none;
        }
        
        .songs-slider input[type="range"]::-webkit-slider-thumb {
            -webkit-appearance: none;
            width: 20px;
            height: 20px;
            background: linear-gradient(135deg, #4caf50, #8bc34a);
            border-radius: 50%;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(76, 175, 80, 0.3);
            transition: all 0.3s ease;
        }
        
        .songs-slider input[type="range"]::-moz-range-track {
            width: 100%;
            height: 6px;
            background: linear-gradient(90deg, #2196f3 0%, #4caf50 50%, #2196f3 100%);
            border-radius: 3px;
            border: none;
        }
        
        .songs-slider input[type="range"]::-moz-range-thumb {
            width: 20px;
            height: 20px;
            background: linear-gradient(135deg, #4caf50, #8bc34a);
            border-radius: 50%;
            cursor: pointer;
            border: none;
            box-shadow: 0 2px 8px rgba(76, 175, 80, 0.3);
            transition: all 0.3s ease;
        }
        
        /* Enhanced Views Count Field */
        .views-input-wrapper {
            position: relative;
        }
        
        .views-group input {
            width: 100%;
            padding: 1rem 3.5rem 1rem 3.5rem;
            border: 2px solid rgba(33, 150, 243, 0.3);
            border-radius: 15px;
            font-size: 1rem;
            background: linear-gradient(145deg, rgba(255, 255, 255, 0.08) 0%, rgba(255, 255, 255, 0.02) 100%);
            color: var(--text-primary);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: inset 0 2px 8px rgba(0, 0, 0, 0.2), 0 4px 15px rgba(33, 150, 243, 0.1);
        }
        
        .views-group input:focus {
            outline: none;
            border-color: #2196f3;
            background: linear-gradient(145deg, rgba(255, 255, 255, 0.12) 0%, rgba(33, 150, 243, 0.05) 100%);
            box-shadow: 0 0 0 4px rgba(33, 150, 243, 0.15), inset 0 2px 8px rgba(0, 0, 0, 0.1), 0 8px 25px rgba(33, 150, 243, 0.2);
            transform: translateY(-3px) scale(1.01);
        }
        
        .views-slider {
            position: absolute;
            top: 50%;
            right: 1rem;
            transform: translateY(-50%);
            width: 120px;
            opacity: 0.7;
            transition: opacity 0.3s ease;
        }
        
        .views-slider:hover {
            opacity: 1;
        }
        
        .views-slider input[type="range"] {
            width: 100%;
            height: 6px;
            background: transparent;
            outline: none;
            cursor: pointer;
            -webkit-appearance: none;
        }
        
        .views-slider input[type="range"]::-webkit-slider-track {
            width: 100%;
            height: 6px;
            background: linear-gradient(90deg, #2196f3 0%, #4caf50 50%, #2196f3 100%);
            border-radius: 3px;
            border: none;
        }
        
        .views-slider input[type="range"]::-webkit-slider-thumb {
            -webkit-appearance: none;
            width: 20px;
            height: 20px;
            background: linear-gradient(135deg, #4caf50, #8bc34a);
            border-radius: 50%;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(76, 175, 80, 0.3);
            transition: all 0.3s ease;
        }
        
        .views-slider input[type="range"]::-moz-range-track {
            width: 100%;
            height: 6px;
            background: linear-gradient(90deg, #2196f3 0%, #4caf50 50%, #2196f3 100%);
            border-radius: 3px;
            border: none;
        }
        
        .views-slider input[type="range"]::-moz-range-thumb {
            width: 20px;
            height: 20px;
            background: linear-gradient(135deg, #4caf50, #8bc34a);
            border-radius: 50%;
            cursor: pointer;
            border: none;
            box-shadow: 0 2px 8px rgba(76, 175, 80, 0.3);
            transition: all 0.3s ease;
        }
        
        .views-group label::before { content: '👁️'; }
        
        textarea.form-control {
            resize: vertical;
            min-height: 140px;
            font-family: inherit;
            line-height: 1.6;
            padding-top: 1.25rem;
        }
        
        .file-input {
            width: 100%;
            padding: 1.25rem 1.5rem 1.25rem 3.5rem;
            border: 2px dashed rgba(255, 107, 107, 0.4);
            border-radius: 15px;
            background: linear-gradient(145deg, rgba(255, 107, 107, 0.05) 0%, rgba(255, 107, 107, 0.02) 100%);
            color: var(--text-primary);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }
        
        .file-input:hover {
            border-color: var(--primary-color);
            background: linear-gradient(145deg, rgba(255, 107, 107, 0.1) 0%, rgba(255, 107, 107, 0.05) 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 107, 107, 0.15);
        }
        
        .file-input::before {
            content: '📁';
            position: absolute;
            left: 1.25rem;
            top: 50%;
            transform: translateY(-50%);
            font-size: 1.2rem;
            color: var(--primary-color);
        }
        
        .file-input::after {
            content: 'Choose Profile Image';
            position: absolute;
            left: 3.5rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-secondary);
            font-weight: 600;
            pointer-events: none;
        }
        
        .form-text {
            display: block;
            margin-top: 0.75rem;
            font-size: 0.85rem;
            color: var(--text-secondary);
            font-style: italic;
            padding-left: 2.5rem;
            opacity: 0.8;
        }
        
        .current-image {
            margin-top: 1.5rem;
            padding: 2.5rem;
            border: 2px solid rgba(255, 107, 107, 0.3);
            border-radius: 20px;
            background: linear-gradient(145deg, rgba(255, 107, 107, 0.08) 0%, rgba(255, 107, 107, 0.02) 100%);
            text-align: center;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .current-image:hover {
            border-color: var(--primary-color);
            transform: scale(1.02);
            box-shadow: 0 15px 40px rgba(255, 107, 107, 0.2);
        }
        
        .current-image::before {
            content: '📸 Current Profile';
            position: absolute;
            top: 15px;
            left: 25px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 15px rgba(255, 107, 107, 0.3);
        }
        
        .current-image img {
            border: 4px solid var(--primary-color);
            border-radius: 20px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.6);
            max-width: 280px;
            max-height: 280px;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .current-image:hover img {
            transform: scale(1.05) rotate(2deg);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.7);
        }
        
        /* Enhanced select styling */
        select.form-control {
            appearance: none;
            background-image: linear-gradient(45deg, transparent 50%, var(--primary-color) 50%),
                              linear-gradient(135deg, var(--primary-color) 50%, transparent 50%);
            background-position: calc(100% - 20px) calc(1em + 2px),
                             calc(100% - 15px) calc(1em + 2px);
            background-size: 5px 5px, 5px 5px;
            background-repeat: no-repeat;
            padding-right: 3rem;
        }
        
        select.form-control:hover {
            background-image: linear-gradient(45deg, transparent 50%, var(--secondary-color) 50%),
                              linear-gradient(135deg, var(--secondary-color) 50%, transparent 50%);
        }
        
        /* Biography Section Specific Styles */
        .biography-section {
            background: linear-gradient(145deg, rgba(255, 107, 107, 0.08) 0%, rgba(255, 107, 107, 0.02) 100%);
            border: 1px solid rgba(255, 107, 107, 0.2);
        }
        
        .biography-container {
            position: relative;
        }
        
        .bio-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding: 1rem;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.05) 0%, rgba(255, 255, 255, 0.02) 100%);
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .bio-stats {
            display: flex;
            gap: 1.5rem;
        }
        
        .stat-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 0.5rem 1rem;
            background: rgba(255, 107, 107, 0.1);
            border-radius: 8px;
            border: 1px solid rgba(255, 107, 107, 0.2);
            transition: all 0.3s ease;
        }
        
        .stat-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(255, 107, 107, 0.2);
            background: rgba(255, 107, 107, 0.15);
        }
        
        .stat-icon {
            font-size: 1.2rem;
            margin-bottom: 0.25rem;
        }
        
        .stat-label {
            font-size: 0.7rem;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.25rem;
        }
        
        .stat-count {
            font-size: 0.9rem;
            font-weight: 700;
            color: var(--primary-color);
        }
        
        .bio-tools {
            display: flex;
            gap: 0.5rem;
        }
        
        .tool-btn {
            padding: 0.5rem 0.75rem;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border: none;
            border-radius: 8px;
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.8rem;
        }
        
        .tool-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(255, 107, 107, 0.3);
        }
        
        .bio-group {
            position: relative;
        }
        
        .bio-wrapper {
            position: relative;
        }
        
        .bio-wrapper textarea {
            width: 100%;
            padding: 1.5rem 1.25rem 1.5rem 3.5rem;
            border: 2px solid rgba(255, 107, 107, 0.3);
            border-radius: 15px;
            font-size: 1rem;
            background: linear-gradient(145deg, rgba(255, 255, 255, 0.08) 0%, rgba(255, 255, 255, 0.02) 100%);
            color: var(--text-primary);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: inset 0 3px 10px rgba(0, 0, 0, 0.2), 0 5px 20px rgba(255, 107, 107, 0.1);
            resize: vertical;
            min-height: 180px;
            font-family: inherit;
            line-height: 1.7;
        }
        
        .bio-wrapper textarea:focus {
            outline: none;
            border-color: var(--primary-color);
            background: linear-gradient(145deg, rgba(255, 255, 255, 0.12) 0%, rgba(255, 107, 107, 0.05) 100%);
            box-shadow: 0 0 0 4px rgba(255, 107, 107, 0.15), inset 0 3px 10px rgba(0, 0, 0, 0.1), 0 10px 30px rgba(255, 107, 107, 0.2);
            transform: translateY(-3px) scale(1.01);
        }
        
        .bio-decoration {
            position: absolute;
            top: 1rem;
            right: 1rem;
            pointer-events: none;
        }
        
        .decoration-line {
            width: 30px;
            height: 2px;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
            margin-bottom: 8px;
            border-radius: 1px;
        }
        
        .decoration-dot {
            width: 8px;
            height: 8px;
            background: var(--primary-color);
            border-radius: 50%;
            margin-left: 11px;
            animation: pulse 2s infinite;
        }
        
        .bio-tips {
            margin-top: 1.5rem;
            padding: 1.5rem;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.05) 0%, rgba(255, 255, 255, 0.02) 100%);
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-left: 3px solid var(--primary-color);
        }
        
        .bio-tips h5 {
            margin: 0 0 1rem 0;
            color: var(--text-primary);
            font-size: 1rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .bio-tips h5 i {
            color: var(--primary-color);
        }
        
        .bio-tips ul {
            margin: 0;
            padding-left: 1.5rem;
            color: var(--text-secondary);
            font-size: 0.9rem;
            line-height: 1.6;
        }
        
        .bio-tips li {
            margin-bottom: 0.5rem;
            position: relative;
        }
        
        .bio-tips li::marker {
            color: var(--primary-color);
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.6; transform: scale(1.1); }
        }
        
        .form-actions {
            display: flex;
            gap: 1.5rem;
            margin-top: 3rem;
            padding-top: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            justify-content: center;
        }
        
        .btn {
            padding: 1rem 2rem;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 1rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s ease;
        }
        
        .btn:hover::before {
            left: 100%;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            box-shadow: 0 8px 20px rgba(255, 107, 107, 0.3);
        }
        
        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(255, 107, 107, 0.4);
        }
        
        .btn-secondary {
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
            color: white;
            box-shadow: 0 8px 20px rgba(108, 117, 125, 0.3);
        }
        
        .btn-secondary:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(108, 117, 125, 0.4);
        }
        
        .alert {
            padding: 1.25rem 1.5rem;
            margin-bottom: 2rem;
            border-radius: 12px;
            display: flex;
            align-items: center;
            gap: 1rem;
            border-left: 4px solid;
            animation: slideInAlert 0.5s ease;
        }
        
        @keyframes slideInAlert {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        .alert-success {
            background: linear-gradient(135deg, rgba(76, 175, 80, 0.1) 0%, rgba(76, 175, 80, 0.05) 100%);
            color: #4caf50;
            border-left-color: #4caf50;
            box-shadow: 0 4px 15px rgba(76, 175, 80, 0.1);
        }
        
        .alert-danger {
            background: linear-gradient(135deg, rgba(244, 67, 54, 0.1) 0%, rgba(244, 67, 54, 0.05) 100%);
            color: #f44336;
            border-left-color: #f44336;
            box-shadow: 0 4px 15px rgba(244, 67, 54, 0.1);
        }
        
        .alert i {
            font-size: 1.25rem;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.6; }
        }
        
        /* Special styling for required fields */
        .form-group:has([required]) label::after {
            content: ' *';
            color: var(--primary-color);
            font-weight: 700;
        }
        
        /* Profile section header */
        .profile-header {
            text-align: center;
            margin-bottom: 3rem;
            padding: 2rem;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            border-radius: 20px;
            color: white;
            position: relative;
            overflow: hidden;
        }
        
        .profile-header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            animation: rotate 20s linear infinite;
        }
        
        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        
        .profile-header h2 {
            margin: 0;
            font-size: 2.5rem;
            font-weight: 700;
            position: relative;
            z-index: 1;
            text-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        }
        
        .profile-header p {
            margin: 1rem 0 0 0;
            font-size: 1.1rem;
            opacity: 0.9;
            position: relative;
            z-index: 1;
        }
    </style>
</head>
<body>
    <!-- Visit Website Button -->
    <a href="<?php echo APP_URL; ?>/" target="_blank" class="visit-website-btn">
        <i class="fas fa-external-link-alt"></i>
        Visit Website
    </a>
    
    <div class="admin-layout">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <div class="sidebar-header">
                <h2 style="color: var(--text-primary); text-align: center; margin-bottom: 2rem;">
                    <i class="fas fa-music"></i> <?php echo APP_NAME; ?>
                </h2>
                <small style="color: var(--text-secondary); display: block; text-align: center;">Admin Panel</small>
            </div>
            
            <?php require_once __DIR__ . '/../includes/admin-nav.php'; ?>
        </aside>
        
        <!-- Main Content -->
        <main class="admin-content">
            <!-- Profile Header -->
            <div class="profile-header">
                <h2><i class="fas fa-user-circle"></i> Profile Management</h2>
                <p>Manage your artist profile information and appearance</p>
            </div>

            <?php if (isset($success)): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-edit"></i> Profile Information</h3>
            <p class="card-subtitle">Update your personal details and artist information</p>
        </div>
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data" class="profile-form">
                <input type="hidden" name="action" value="update_profile">
                
                <!-- Basic Information Section -->
                <div class="form-section basic-info-section">
                    <div class="section-title">
                        <h4><i class="fas fa-user"></i> Basic Information</h4>
                        <p>Your core artist details</p>
                    </div>
                    
                    <div class="basic-info-container">
                        <div class="form-row">
                            <div class="form-group artist-name-group">
                                <label for="artist_name">Artist Name *</label>
                                <div class="input-wrapper">
                                    <input type="text" id="artist_name" name="artist_name" 
                                           value="<?php echo htmlspecialchars($profile['artist_name']); ?>" 
                                           placeholder="Enter your stage name or artist name"
                                           maxlength="100"
                                           required>
                                    <div class="input-decoration">
                                        <div class="decoration-icon">🎤</div>
                                        <div class="char-counter">
                                            <span id="artistNameCount"><?php echo strlen($profile['artist_name']); ?></span>/100
                                        </div>
                                    </div>
                                </div>
                                <div class="field-tips">
                                    <i class="fas fa-info-circle"></i>
                                    <span>This is how fans will find and recognize you</span>
                                </div>
                            </div>
                            
                            <div class="form-group tagline-group">
                                <label for="tagline">Tagline</label>
                                <div class="input-wrapper">
                                    <input type="text" id="tagline" name="tagline" 
                                           value="<?php echo htmlspecialchars($profile['tagline']); ?>" 
                                           placeholder="e.g., Musician, Songwriter, Storyteller"
                                           maxlength="150">
                                    <div class="input-decoration">
                                        <div class="decoration-icon">💫</div>
                                        <div class="char-counter">
                                            <span id="taglineCount"><?php echo strlen($profile['tagline']); ?></span>/150
                                        </div>
                                    </div>
                                </div>
                                <div class="field-tips">
                                    <i class="fas fa-info-circle"></i>
                                    <span>A short, memorable phrase that describes your style</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Biography Section -->
                <div class="form-section biography-section">
                    <div class="section-title">
                        <h4><i class="fas fa-pen-fancy"></i> Biography</h4>
                        <p>Tell your story to your fans</p>
                    </div>
                    
                    <div class="biography-container">
                        <div class="bio-header">
                            <div class="bio-stats">
                                <div class="stat-item">
                                    <span class="stat-icon">📝</span>
                                    <span class="stat-label">Characters</span>
                                    <span class="stat-count" id="charCount">0</span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-icon">📄</span>
                                    <span class="stat-label">Words</span>
                                    <span class="stat-count" id="wordCount">0</span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-icon">⏱️</span>
                                    <span class="stat-label">Read Time</span>
                                    <span class="stat-count" id="readTime">0 min</span>
                                </div>
                            </div>
                            <div class="bio-tools">
                                <button type="button" class="tool-btn" onclick="formatBio('bold')" title="Bold">
                                    <i class="fas fa-bold"></i>
                                </button>
                                <button type="button" class="tool-btn" onclick="formatBio('italic')" title="Italic">
                                    <i class="fas fa-italic"></i>
                                </button>
                                <button type="button" class="tool-btn" onclick="clearBio()" title="Clear">
                                    <i class="fas fa-eraser"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="form-group bio-group">
                            <label for="bio">Your Story</label>
                            <div class="bio-wrapper">
                                <textarea id="bio" name="bio" rows="8" 
                                          placeholder="Share your journey, inspirations, and what drives your music...&#10;&#10;• Where did your musical journey begin?&#10;• What inspires your creativity?&#10;• What message do you want to share with your fans?&#10;• How has music shaped your life?&#10;&#10;Let your personality shine through your words..."><?php echo htmlspecialchars($profile['bio']); ?></textarea>
                                <div class="bio-decoration">
                                    <div class="decoration-line"></div>
                                    <div class="decoration-dot"></div>
                                </div>
                            </div>
                            <div class="bio-tips">
                                <h5><i class="fas fa-lightbulb"></i> Writing Tips</h5>
                                <ul>
                                    <li>Be authentic and share your genuine story</li>
                                    <li>Include personal anecdotes and experiences</li>
                                    <li>Connect your music to your life journey</li>
                                    <li>Keep it engaging and conversational</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistics Section -->
                <div class="form-section statistics-section">
                    <div class="section-title">
                        <h4><i class="fas fa-chart-bar"></i> Statistics</h4>
                        <p>Your achievements and numbers</p>
                    </div>
                    
                    <div class="statistics-container">
                        <div class="form-row">
                            <div class="form-group years-group">
                                <label for="years_experience">Years Experience</label>
                                <div class="years-input-wrapper">
                                    <input type="text" id="years_experience" name="years_experience" 
                                           value="<?php echo htmlspecialchars($profile['years_experience']); ?>" 
                                           placeholder="e.g., 10+"
                                           maxlength="20"
                                           pattern="[0-9+]+">
                                    <div class="input-decoration">
                                        <div class="decoration-icon">📅</div>
                                        <div class="years-slider">
                                            <input type="range" id="yearsSlider" min="0" max="50" value="10" step="1">
                                            <div class="slider-track"></div>
                                            <div class="slider-thumb"></div>
                                        </div>
                                        <div class="validation-indicator" id="yearsValid"></div>
                                    </div>
                                </div>
                                <div class="field-tips">
                                    <i class="fas fa-info-circle"></i>
                                    <span>How long have you been making music professionally?</span>
                                </div>
                            </div>
                            
                            <div class="form-group songs-group">
                                <label for="songs_count">Songs Count</label>
                                <div class="songs-input-wrapper">
                                    <input type="text" id="songs_count" name="songs_count" 
                                           value="<?php echo htmlspecialchars($profile['songs_count']); ?>" 
                                           placeholder="e.g., 50+"
                                           maxlength="20"
                                           pattern="[0-9+]+">
                                    <div class="input-decoration">
                                        <div class="decoration-icon">🎵</div>
                                        <div class="songs-slider">
                                            <input type="range" id="songsSlider" min="0" max="500" value="50" step="5">
                                            <div class="slider-track"></div>
                                            <div class="slider-thumb"></div>
                                        </div>
                                        <div class="validation-indicator" id="songsValid"></div>
                                    </div>
                                </div>
                                <div class="field-tips">
                                    <i class="fas fa-info-circle"></i>
                                    <span>Total songs released or in your catalog</span>
                                </div>
                            </div>
                            
                            <div class="form-group views-group">
                                <label for="views_count">Views Count</label>
                                <div class="views-input-wrapper">
                                    <input type="text" id="views_count" name="views_count" 
                                           value="<?php echo htmlspecialchars($profile['views_count']); ?>" 
                                           placeholder="e.g., 1M+"
                                           maxlength="20"
                                           pattern="[0-9KMB.]+">
                                    <div class="input-decoration">
                                        <div class="decoration-icon">👁️</div>
                                        <div class="views-slider">
                                            <input type="range" id="viewsSlider" min="0" max="10000000" value="100000" step="1000">
                                            <div class="slider-track"></div>
                                            <div class="slider-thumb"></div>
                                        </div>
                                        <div class="validation-indicator" id="viewsValid"></div>
                                    </div>
                                </div>
                                <div class="field-tips">
                                    <i class="fas fa-info-circle"></i>
                                    <span>Total views across all platforms</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Media Section -->
                <div class="form-section media-section">
                    <div class="section-title">
                        <h4><i class="fas fa-image"></i> Profile Image</h4>
                        <p>Your visual identity</p>
                    </div>
                    
                    <div class="media-container">
                        <div class="upload-section">
                            <div class="form-group upload-group">
                                <label for="profile_image_file">Upload New Image</label>
                                <div class="upload-wrapper-enhanced">
                                    <input type="file" id="profile_image_file" name="profile_image_file" 
                                           accept="image/*" class="file-input-enhanced">
                                    <div class="upload-area-enhanced" id="uploadArea">
                                        <div class="upload-content">
                                            <div class="upload-icon-large">📸</div>
                                            <div class="upload-text-enhanced">
                                                <h5>Drop your image here</h5>
                                                <p>or click to browse files</p>
                                            </div>
                                            <div class="upload-specs-enhanced">
                                                <div class="spec-item">
                                                    <i class="fas fa-file-image"></i>
                                                    <span>JPG, PNG, GIF, WebP</span>
                                                </div>
                                                <div class="spec-item">
                                                    <i class="fas fa-weight"></i>
                                                    <span>Max 5MB</span>
                                                </div>
                                                <div class="spec-item">
                                                    <i class="fas fa-expand"></i>
                                                    <span>400x400px recommended</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="upload-progress" id="uploadProgress" style="display: none;">
                                            <div class="progress-bar">
                                                <div class="progress-fill" id="progressFill"></div>
                                            </div>
                                            <div class="progress-text" id="progressText">Uploading...</div>
                                        </div>
                                    </div>
                                    <div class="upload-preview-enhanced" id="uploadPreview">
                                        <div class="preview-placeholder" id="previewPlaceholder">
                                            <i class="fas fa-image"></i>
                                            <span>No image selected</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="field-tips">
                                    <i class="fas fa-info-circle"></i>
                                    <span>Choose a high-quality image that represents your brand</span>
                                </div>
                            </div>
                        </div>

                        <div class="current-image-section">
                            <div class="form-group current-image-group">
                                <label>Current Profile Image</label>
                                <div class="current-image-enhanced" id="currentImageContainer">
                                    <div class="image-wrapper">
                                        <img src="<?php echo APP_URL . '/' . $profile['profile_image']; ?>" 
                                             alt="Profile Image" id="currentProfileImage">
                                        <div class="image-overlay-enhanced">
                                            <div class="image-info-enhanced">
                                                <div class="image-header">
                                                    <h5><i class="fas fa-user-circle"></i> Current Profile</h5>
                                                    <button type="button" class="image-action-btn edit-btn" onclick="editCurrentImage()">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </button>
                                                    <button type="button" class="image-action-btn remove-btn" onclick="removeCurrentImage()">
                                                        <i class="fas fa-trash"></i> Remove
                                                    </button>
                                                </div>
                                                <div class="image-details">
                                                    <div class="detail-item">
                                                        <i class="fas fa-ruler-combined"></i>
                                                        <span id="imageDimensions">Loading...</span>
                                                    </div>
                                                    <div class="detail-item">
                                                        <i class="fas fa-file"></i>
                                                        <span id="imageSize">Loading...</span>
                                                    </div>
                                                    <div class="detail-item">
                                                        <i class="fas fa-calendar"></i>
                                                        <span id="imageDate">Loading...</span>
                                                    </div>
                                                </div>
                                                <div class="image-actions">
                                                    <button type="button" class="action-btn download-btn" onclick="downloadCurrentImage()">
                                                        <i class="fas fa-download"></i> Download
                                                    </button>
                                                    <button type="button" class="action-btn view-btn" onclick="viewCurrentImage()">
                                                        <i class="fas fa-eye"></i> Full View
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="field-tips">
                                    <i class="fas fa-info-circle"></i>
                                    <span>This is your current profile image displayed on the website</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Status Section -->
                <div class="form-section status-section">
                    <div class="section-title">
                        <h4><i class="fas fa-toggle-on"></i> Status</h4>
                        <p>Control profile visibility</p>
                    </div>
                    
                    <div class="status-container">
                        <div class="form-group status-group">
                            <label for="status">Profile Status</label>
                            <div class="status-wrapper">
                                <div class="status-options">
                                    <label class="status-option" for="statusActive">
                                        <input type="radio" name="status_radio" value="active" id="statusActive" 
                                               <?php echo $profile['status'] === 'active' ? 'checked' : ''; ?>>
                                        <div class="status-card active">
                                            <div class="status-icon">👁️</div>
                                            <div class="status-details">
                                                <h5>Active</h5>
                                                <p>Visible on website</p>
                                            </div>
                                            <div class="status-indicator"></div>
                                        </div>
                                    </label>
                                    
                                    <label class="status-option" for="statusInactive">
                                        <input type="radio" name="status_radio" value="inactive" id="statusInactive" 
                                               <?php echo $profile['status'] === 'inactive' ? 'checked' : ''; ?>>
                                        <div class="status-card inactive">
                                            <div class="status-icon">👁️‍🗨️</div>
                                            <div class="status-details">
                                                <h5>Inactive</h5>
                                                <p>Hidden from website</p>
                                            </div>
                                            <div class="status-indicator"></div>
                                        </div>
                                    </label>
                                </div>
                                <input type="hidden" id="status" name="status" value="<?php echo htmlspecialchars($profile['status']); ?>">
                            </div>
                            <div class="field-tips">
                                <i class="fas fa-info-circle"></i>
                                <span>Active profiles are displayed on your public website</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Profile
                    </button>
                    <a href="dashboard.php" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.profile-form {
    max-width: 800px;
}

.form-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 1.5rem;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 600;
    color: #e0d8d8ff;
}

.form-control {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 1rem;
}

.form-control:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
}

textarea.form-control {
    resize: vertical;
}

.file-input {
    padding: 0.5rem;
}

.form-text {
    display: block;
    margin-top: 0.25rem;
    font-size: 0.875rem;
    color: #6c757d;
}

.current-image {
    margin-top: 0.5rem;
    padding: 1rem;
    border: 1px solid #ddd;
    border-radius: 5px;
    background: #f8f9fa;
    text-align: center;
}

.current-image img {
    border: 2px solid #007bff;
}

.form-actions {
    display: flex;
    gap: 1rem;
    margin-top: 2rem;
}

.btn {
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 1rem;
}

.btn-primary {
    background: #007bff;
    color: white;
}

.btn-primary:hover {
    background: #0056b3;
}

.btn-secondary {
    background: #6c757d;
    color: white;
}

.btn-secondary:hover {
    background: #545b62;
}

.alert {
    padding: 1rem;
    margin-bottom: 1.5rem;
    border-radius: 5px;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.alert-success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert-danger {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}
</style>

</main>
    </div>
    
    <script>
        // Biography functionality
        document.addEventListener('DOMContentLoaded', function() {
            const bioTextarea = document.getElementById('bio');
            const charCount = document.getElementById('charCount');
            const wordCount = document.getElementById('wordCount');
            const readTime = document.getElementById('readTime');
            
            // Basic info fields
            const artistNameInput = document.getElementById('artist_name');
            const taglineInput = document.getElementById('tagline');
            const artistNameCount = document.getElementById('artistNameCount');
            const taglineCount = document.getElementById('taglineCount');
            
            if (bioTextarea) {
                // Update stats on input
                bioTextarea.addEventListener('input', updateBioStats);
                bioTextarea.addEventListener('paste', function() {
                    setTimeout(updateBioStats, 10);
                });
                
                // Initial stats update
                updateBioStats();
            }
            
            // Basic info character counters
            if (artistNameInput) {
                artistNameInput.addEventListener('input', function() {
                    updateCharCount(artistNameInput, artistNameCount, 100);
                });
            }
            
            if (taglineInput) {
                taglineInput.addEventListener('input', function() {
                    updateCharCount(taglineInput, taglineCount, 150);
                });
            }
            
            function updateBioStats() {
                const text = bioTextarea.value;
                
                // Character count
                const chars = text.length;
                charCount.textContent = chars;
                
                // Word count
                const words = text.trim() === '' ? 0 : text.trim().split(/\s+/).length;
                wordCount.textContent = words;
                
                // Read time (average 200 words per minute)
                const minutes = Math.ceil(words / 200);
                readTime.textContent = minutes + ' min';
                
                // Update colors based on length
                if (chars > 500) {
                    charCount.style.color = '#ff6b6b';
                } else if (chars > 300) {
                    charCount.style.color = '#ffa726';
                } else {
                    charCount.style.color = 'var(--primary-color)';
                }
            }
            
            // Statistics fields validation
            const yearsInput = document.getElementById('years_experience');
            const songsInput = document.getElementById('songs_count');
            const viewsInput = document.getElementById('views_count');
            const yearsValid = document.getElementById('yearsValid');
            const songsValid = document.getElementById('songsValid');
            const viewsValid = document.getElementById('viewsValid');
            const yearsSlider = document.getElementById('yearsSlider');
            const songsSlider = document.getElementById('songsSlider');
            const viewsSlider = document.getElementById('viewsSlider');
            
            if (yearsInput) {
                yearsInput.addEventListener('input', () => validateInput(yearsInput, yearsValid, 'number'));
            }
            if (songsInput) {
                songsInput.addEventListener('input', () => validateInput(songsInput, songsValid, 'number'));
            }
            if (viewsInput) {
                viewsInput.addEventListener('input', () => validateInput(viewsInput, viewsValid, 'mixed'));
            }
            
            // Years slider functionality
            if (yearsSlider && yearsInput) {
                yearsSlider.addEventListener('input', function() {
                    yearsInput.value = this.value;
                    validateInput(yearsInput, yearsValid, 'number');
                });
                
                // Sync slider with input field
                yearsInput.addEventListener('input', function() {
                    if (this.value) {
                        yearsSlider.value = parseInt(this.value) || 0;
                    }
                });
            }
            
            // Songs slider functionality
            if (songsSlider && songsInput) {
                songsSlider.addEventListener('input', function() {
                    songsInput.value = this.value;
                    validateInput(songsInput, songsValid, 'number');
                });
                
                // Sync slider with input field
                songsInput.addEventListener('input', function() {
                    if (this.value) {
                        songsSlider.value = parseInt(this.value) || 0;
                    }
                });
            }
            
            // Views slider functionality
            if (viewsSlider && viewsInput) {
                viewsSlider.addEventListener('input', function() {
                    formatViewsValue(this.value);
                    validateInput(viewsInput, viewsValid, 'mixed');
                });
                
                // Sync slider with input field
                viewsInput.addEventListener('input', function() {
                    if (this.value) {
                        const numericValue = parseViewsValue(this.value);
                        viewsSlider.value = numericValue || 0;
                    }
                });
            }
            
            function parseViewsValue(value) {
                // Extract numeric value from formats like "1M", "500K", "1000000"
                const match = value.match(/^(\d+(?:\.\d+)?)([KMB]?)$/);
                if (match) {
                    const number = parseFloat(match[1]);
                    const suffix = match[2];
                    switch (suffix) {
                        case 'K': return number * 1000;
                        case 'M': return number * 1000000;
                        case 'B': return number * 1000000000;
                        default: return number;
                    }
                }
                return parseInt(value) || 0;
            }
            
            function formatViewsValue(value) {
                // Format numeric value to K/M/B format
                const num = parseInt(value);
                if (num >= 1000000) {
                    const millions = (num / 1000000).toFixed(1);
                    viewsInput.value = millions + 'M';
                } else if (num >= 1000) {
                    const thousands = (num / 1000).toFixed(1);
                    viewsInput.value = thousands + 'K';
                } else {
                    viewsInput.value = num.toString();
                }
            }
            
            // Status radio buttons
            const statusRadios = document.querySelectorAll('input[name="status_radio"]');
            const statusHidden = document.getElementById('status');
            
            statusRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    statusHidden.value = this.value;
                });
            });
            
            // File upload
            const fileInput = document.getElementById('profile_image_file');
            const uploadArea = document.getElementById('uploadArea');
            const uploadPreview = document.getElementById('uploadPreview');
            const uploadProgress = document.getElementById('uploadProgress');
            const progressFill = document.getElementById('progressFill');
            const progressText = document.getElementById('progressText');
            const previewPlaceholder = document.getElementById('previewPlaceholder');
            
            if (fileInput && uploadArea) {
                // Drag and drop
                uploadArea.addEventListener('dragover', (e) => {
                    e.preventDefault();
                    uploadArea.classList.add('dragover');
                    uploadArea.style.borderColor = '#9c27b0';
                    uploadArea.style.background = 'linear-gradient(145deg, rgba(156, 39, 176, 0.2) 0%, rgba(156, 39, 176, 0.1) 100%)';
                });
                
                uploadArea.addEventListener('dragleave', () => {
                    uploadArea.classList.remove('dragover');
                    uploadArea.style.borderColor = 'rgba(156, 39, 176, 0.6)';
                    uploadArea.style.background = 'linear-gradient(145deg, rgba(156, 39, 176, 0.08) 0%, rgba(156, 39, 176, 0.02) 100%)';
                });
                
                uploadArea.addEventListener('drop', (e) => {
                    e.preventDefault();
                    uploadArea.classList.remove('dragover');
                    handleFileSelect(e.dataTransfer.files[0]);
                });
                
                // Click to browse
                uploadArea.addEventListener('click', () => fileInput.click());
                
                // File selection
                fileInput.addEventListener('change', (e) => handleFileSelect(e.target.files[0]));
            }
            
            function handleFileSelect(file) {
                if (file && file.type.startsWith('image/')) {
                    // Show progress
                    if (uploadProgress) {
                        uploadProgress.style.display = 'flex';
                    }
                    
                    // Simulate upload progress
                    let progress = 0;
                    const progressInterval = setInterval(() => {
                        progress += Math.random() * 30;
                        if (progress >= 100) {
                            progress = 100;
                            clearInterval(progressInterval);
                            setTimeout(() => {
                                if (uploadProgress) {
                                    uploadProgress.style.display = 'none';
                                }
                            }, 500);
                        }
                        
                        if (progressFill) {
                            progressFill.style.width = progress + '%';
                        }
                        if (progressText) {
                            progressText.textContent = `Uploading... ${Math.round(progress)}%`;
                        }
                    }, 200);
                    
                    // Read and display file
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        // Hide placeholder
                        if (previewPlaceholder) {
                            previewPlaceholder.style.display = 'none';
                        }
                        
                        // Show preview
                        if (uploadPreview) {
                            uploadPreview.innerHTML = `
                                <div class="preview-image-container">
                                    <img src="${e.target.result}" alt="Preview" style="max-width: 300px; max-height: 200px; border-radius: 15px; box-shadow: 0 8px 25px rgba(156, 39, 176, 0.3);">
                                    <div class="preview-info">
                                        <h6>${file.name}</h6>
                                        <p>${formatFileSize(file.size)}</p>
                                        <button type="button" class="remove-preview-btn" onclick="clearPreview()">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            `;
                        }
                    };
                    reader.readAsDataURL(file);
                } else {
                    alert('Please select a valid image file (JPG, PNG, GIF, WebP)');
                }
            }
            
            function formatFileSize(bytes) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
            }
            
            function clearPreview() {
                if (uploadPreview) {
                    uploadPreview.innerHTML = `
                        <div class="preview-placeholder" id="previewPlaceholder">
                            <i class="fas fa-image"></i>
                            <span>No image selected</span>
                        </div>
                    `;
                }
                if (fileInput) {
                    fileInput.value = '';
                }
            }
            
            function validateInput(input, indicator, type) {
                const value = input.value.trim();
                let isValid = false;
                
                if (type === 'number') {
                    isValid = /^\d+$/.test(value);
                } else if (type === 'mixed') {
                    isValid = /^[\d.]+[KMB]?$/.test(value);
                }
                
                if (isValid) {
                    indicator.classList.add('valid');
                    indicator.classList.remove('invalid');
                } else {
                    indicator.classList.add('invalid');
                    indicator.classList.remove('valid');
                }
            }
            
            function handleFileSelect(file) {
                if (file && file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        uploadPreview.innerHTML = `<img src="${e.target.result}" style="max-width: 200px; max-height: 150px; border-radius: 8px;">`;
                    };
                    reader.readAsDataURL(file);
                }
            }
            
            function updateImageDimensions() {
                const img = document.getElementById('currentProfileImage');
                if (img) {
                    img.onload = function() {
                        document.getElementById('imageDimensions').textContent = `${this.naturalWidth} × ${this.naturalHeight}px`;
                        document.getElementById('imageSize').textContent = formatFileSize(this.naturalWidth * this.naturalHeight * 3);
                        document.getElementById('imageDate').textContent = new Date().toLocaleDateString();
                    };
                }
            }
            
            function editCurrentImage() {
                // Scroll to upload section
                const uploadSection = document.getElementById('uploadArea');
                if (uploadSection) {
                    uploadSection.scrollIntoView({ behavior: 'smooth' });
                    uploadSection.style.borderColor = '#4caf50';
                    setTimeout(() => {
                        uploadSection.style.borderColor = 'rgba(156, 39, 176, 0.6)';
                    }, 2000);
                }
            }
            
            function downloadCurrentImage() {
                const img = document.getElementById('currentProfileImage');
                if (img) {
                    const link = document.createElement('a');
                    link.href = img.src;
                    link.download = 'profile-image.jpg';
                    link.click();
                }
            }
            
            function viewCurrentImage() {
                const img = document.getElementById('currentProfileImage');
                if (img) {
                    window.open(img.src, '_blank');
                }
            }
            
            function removeCurrentImage() {
                if (confirm('Remove current profile image?')) {
                    const img = document.getElementById('currentProfileImage');
                    if (img) {
                        img.src = 'data:image/svg+xml;base64,PHN2ZyB4bWxuczcmxmFDQ==';
                    }
                    document.getElementById('imageDimensions').textContent = 'No image';
                }
            }
        });
        
        // Formatting functions
        function formatBio(format) {
            const bioTextarea = document.getElementById('bio');
            const start = bioTextarea.selectionStart;
            const end = bioTextarea.selectionEnd;
            const selectedText = bioTextarea.value.substring(start, end);
            
            let formattedText = '';
            if (format === 'bold') {
                formattedText = `**${selectedText}**`;
            } else if (format === 'italic') {
                formattedText = `*${selectedText}*`;
            }
            
            bioTextarea.value = bioTextarea.value.substring(0, start) + formattedText + bioTextarea.value.substring(end);
            bioTextarea.focus();
            bioTextarea.setSelectionRange(start + formattedText.length, start + formattedText.length);
            
            // Update stats
            const event = new Event('input');
            bioTextarea.dispatchEvent(event);
        }
        
        function clearBio() {
            if (confirm('Are you sure you want to clear your biography? This action cannot be undone.')) {
                const bioTextarea = document.getElementById('bio');
                bioTextarea.value = '';
                
                // Update stats
                const event = new Event('input');
                bioTextarea.dispatchEvent(event);
                
                // Focus back to textarea
                bioTextarea.focus();
            }
        }
    </script>
</body>
</html>
