-- Create video categories table
CREATE TABLE video_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    color VARCHAR(7) DEFAULT '#ff6b6b',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE
);

-- Add category_id to videos table
ALTER TABLE videos ADD COLUMN category_id INT DEFAULT NULL;

-- Add foreign key constraint
ALTER TABLE videos ADD CONSTRAINT fk_video_category 
    FOREIGN KEY (category_id) REFERENCES video_categories(id) ON DELETE SET NULL;

-- Insert default categories
INSERT INTO video_categories (name, description, color) VALUES 
('Music Videos', 'Official music videos and performances', '#ff6b6b'),
('Live Performances', 'Live concert footage and performances', '#4ecdc4'),
('Behind the Scenes', 'Behind the scenes content and making of', '#45b7d1'),
('Interviews', 'Interviews and press coverage', '#96ceb4'),
('Cover Songs', 'Cover songs and tributes', '#ffeaa7'),
('Dance Videos', 'Dance performances and choreography', '#dfe6e9'),
('Acoustic', 'Acoustic performances and sessions', '#fab1a0'),
('Remixes', 'Remix versions and edits', '#a29bfe');
