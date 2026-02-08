<?php
// Script to create page_banners table
require_once __DIR__ . '/includes/database.php';

// SQL to create page_banners table
$sql = "
CREATE TABLE IF NOT EXISTS `page_banners` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `page_name` varchar(50) NOT NULL,
    `title` varchar(255) NOT NULL,
    `subtitle` text DEFAULT NULL,
    `image_url` varchar(255) DEFAULT NULL,
    `video_url` varchar(255) DEFAULT NULL,
    `type` enum('image','video') NOT NULL DEFAULT 'image',
    `is_active` tinyint(1) NOT NULL DEFAULT 1,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_page_name` (`page_name`),
    KEY `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
";

try {
    global $pdo;
    $pdo->exec($sql);
    echo "✅ page_banners table created successfully!\n";
    
    // Insert default banners
    $default_banners = [
        [
            'page_name' => 'music',
            'title' => 'Music Collection',
            'subtitle' => 'Discover our complete discography and latest releases',
            'image_url' => 'assets/images/banners/music-banner.jpg',
            'type' => 'image'
        ],
        [
            'page_name' => 'tour',
            'title' => 'Tour Dates',
            'subtitle' => 'Join us live at a city near you',
            'image_url' => 'assets/images/banners/tour-banner.jpg',
            'type' => 'image'
        ],
        [
            'page_name' => 'contact',
            'title' => 'Get in Touch',
            'subtitle' => 'We would love to hear from you',
            'image_url' => 'assets/images/banners/contact-banner.jpg',
            'type' => 'image'
        ],
        [
            'page_name' => 'videos',
            'title' => 'Video Gallery',
            'subtitle' => 'Watch our latest music videos and performances',
            'image_url' => 'assets/images/banners/videos-banner.jpg',
            'type' => 'image'
        ],
        [
            'page_name' => 'about',
            'title' => 'About Us',
            'subtitle' => 'Learn more about our journey and music',
            'image_url' => 'assets/images/banners/about-banner.jpg',
            'type' => 'image'
        ]
    ];
    
    foreach ($default_banners as $banner) {
        $stmt = $pdo->prepare("
            INSERT IGNORE INTO page_banners 
            (page_name, title, subtitle, image_url, type) 
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $banner['page_name'],
            $banner['title'],
            $banner['subtitle'],
            $banner['image_url'],
            $banner['type']
        ]);
    }
    
    echo "✅ Default banners inserted successfully!\n";
    echo "✅ Banner system is now ready to use!\n";
    
} catch(PDOException $e) {
    echo "❌ Error creating table: " . $e->getMessage() . "\n";
}
?>
