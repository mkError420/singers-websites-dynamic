<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo get_page_title(); ?> - Official website of <?php echo APP_NAME; ?>">
    <meta name="keywords" content="music, artist, singer, songs, videos, tour, concerts">
    <meta name="author" content="<?php echo APP_NAME; ?>">
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="<?php echo get_page_title(); ?>">
    <meta property="og:description" content="Official website of <?php echo APP_NAME; ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo current_url(); ?>">
    <meta property="og:image" content="<?php echo APP_URL; ?>/assets/images/og-image.jpg">
    
    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo get_page_title(); ?>">
    <meta name="twitter:description" content="Official website of <?php echo APP_NAME; ?>">
    <meta name="twitter:image" content="<?php echo APP_URL; ?>/assets/images/og-image.jpg">
    
    <title><?php echo get_page_title(); ?></title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo APP_URL; ?>/assets/images/favicon.ico">
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo APP_URL; ?>/assets/images/apple-touch-icon.png">
    
    <!-- CSS -->
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/assets/css/style.css">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Canonical URL -->
    <link rel="canonical" href="<?php echo current_url(); ?>">
</head>
<body>
    <header>
        <nav class="container">
            <div class="logo">
                <?php echo APP_NAME; ?>
            </div>
            
            <ul class="nav-links">
                <li><a href="<?php echo APP_URL; ?>/index.php">Home</a></li>
                <li><a href="<?php echo APP_URL; ?>/music.php">Music</a></li>
                <li><a href="<?php echo APP_URL; ?>/videos.php">Videos</a></li>
                <li><a href="<?php echo APP_URL; ?>/tour.php">Tour</a></li>
                <li><a href="<?php echo APP_URL; ?>/about.php">About</a></li>
                <li><a href="<?php echo APP_URL; ?>/contact.php">Contact</a></li>
            </ul>
            
            <div class="mobile-menu-toggle">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </nav>
    </header>
