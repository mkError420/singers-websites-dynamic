<?php
// Get current page filename
$current_page = basename($_SERVER['PHP_SELF'], '.php');

// Navigation items with their corresponding files
$nav_items = [
    'dashboard' => ['url' => 'dashboard.php', 'icon' => 'fas fa-tachometer-alt', 'label' => 'Dashboard'],
    'songs' => ['url' => 'songs.php', 'icon' => 'fas fa-music', 'label' => 'Songs'],
    'videos' => ['url' => 'videos.php', 'icon' => 'fas fa-video', 'label' => 'Videos'],
    'tour' => ['url' => 'tour.php', 'icon' => 'fas fa-calendar-alt', 'label' => 'Tour Dates'],
    'albums' => ['url' => 'albums.php', 'icon' => 'fas fa-compact-disc', 'label' => 'Albums'],
    'gallery' => ['url' => 'gallery.php', 'icon' => 'fas fa-images', 'label' => 'Gallery'],
    'messages' => ['url' => 'messages.php', 'icon' => 'fas fa-envelope', 'label' => 'Messages'],
    'subscribers' => ['url' => 'subscribers.php', 'icon' => 'fas fa-users', 'label' => 'Subscribers'],
    'hero-videos' => ['url' => 'hero-videos.php', 'icon' => 'fas fa-film', 'label' => 'Hero Videos'],
    'settings' => ['url' => 'settings.php', 'icon' => 'fas fa-cog', 'label' => 'Settings']
];
?>
<nav>
    <ul class="admin-nav">
        <?php foreach ($nav_items as $key => $item): ?>
            <li>
                <a href="<?php echo $item['url']; ?>" <?php echo ($current_page === $key ? 'class="active"' : ''); ?>>
                    <i class="<?php echo $item['icon']; ?>"></i> 
                    <?php echo $item['label']; ?>
                </a>
            </li>
        <?php endforeach; ?>
        <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</nav>
