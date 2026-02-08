-- Add 'uploaded' to video_type enum
ALTER TABLE videos MODIFY COLUMN video_type ENUM('youtube', 'vimeo', 'uploaded') DEFAULT 'youtube';
