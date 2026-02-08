-- Create page_banners table for managing page banners
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

-- Insert default banners for pages
INSERT INTO `page_banners` (`page_name`, `title`, `subtitle`, `image_url`, `type`) VALUES
('music', 'Music Collection', 'Discover our complete discography and latest releases', 'assets/images/banners/music-banner.jpg', 'image'),
('tour', 'Tour Dates', 'Join us live at a city near you', 'assets/images/banners/tour-banner.jpg', 'image'),
('contact', 'Get in Touch', 'We would love to hear from you', 'assets/images/banners/contact-banner.jpg', 'image'),
('videos', 'Video Gallery', 'Watch our latest music videos and performances', 'assets/images/banners/videos-banner.jpg', 'image'),
('about', 'About Us', 'Learn more about our journey and music', 'assets/images/banners/about-banner.jpg', 'image')
ON DUPLICATE KEY UPDATE
    `title` = VALUES(`title`),
    `subtitle` = VALUES(`subtitle`),
    `image_url` = VALUES(`image_url`),
    `type` = VALUES(`type`),
    `updated_at` = CURRENT_TIMESTAMP;
