-- Create gallery table
CREATE TABLE IF NOT EXISTS gallery (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    image_url VARCHAR(500) NOT NULL,
    thumbnail_url VARCHAR(500),
    category VARCHAR(100) DEFAULT 'general',
    tags VARCHAR(500),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_category (category),
    INDEX idx_active (is_active),
    INDEX idx_created_at (created_at)
);

-- Insert sample gallery data
INSERT INTO gallery (title, description, image_url, thumbnail_url, category, tags, is_active) VALUES
('Concert Performance', 'Amazing performance at Madison Square Garden', 'uploads/gallery/concert1.jpg', 'uploads/gallery/thumbnails/concert1_thumb.jpg', 'performances', 'concert, live, madison square garden', TRUE),
('Studio Session', 'Behind the scenes recording session', 'uploads/gallery/studio1.jpg', 'uploads/gallery/thumbnails/studio1_thumb.jpg', 'behind-scenes', 'studio, recording, behind scenes', TRUE),
('Photo Shoot', 'Professional photo shoot for new album', 'uploads/gallery/photoshoot1.jpg', 'uploads/gallery/thumbnails/photoshoot1_thumb.jpg', 'photoshoot', 'photo shoot, album cover, professional', TRUE),
('Fan Meeting', 'Meeting amazing fans at the concert', 'uploads/gallery/fans1.jpg', 'uploads/gallery/thumbnails/fans1_thumb.jpg', 'fans', 'fans, meeting, concert, interaction', TRUE),
('Music Video', 'Behind the scenes of music video shoot', 'uploads/gallery/video1.jpg', 'uploads/gallery/thumbnails/video1_thumb.jpg', 'music-videos', 'music video, behind scenes, shooting', TRUE);
