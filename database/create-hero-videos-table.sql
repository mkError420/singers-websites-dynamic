-- Hero Videos Table for Homepage Background Videos
CREATE TABLE hero_videos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    video_url VARCHAR(500) NOT NULL,
    video_type ENUM('youtube', 'vimeo', 'uploaded') DEFAULT 'youtube',
    thumbnail VARCHAR(255),
    is_active BOOLEAN DEFAULT TRUE,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert sample hero video data
INSERT INTO hero_videos (title, video_url, video_type, thumbnail, is_active, display_order) VALUES 
('Hero Background Video 1', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'youtube', 'uploads/hero/thumbnail-1.jpg', TRUE, 1),
('Hero Background Video 2', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'youtube', 'uploads/hero/thumbnail-2.jpg', FALSE, 2),
('Hero Background Video 3', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'youtube', 'uploads/hero/thumbnail-3.jpg', FALSE, 3);

-- Create uploads/hero directory structure (manual step needed)
-- Ensure the uploads/hero folder exists and is writable
