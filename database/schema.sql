-- Singer/Artist Website Database Schema
-- Created for modern music artist website

-- Create database
CREATE DATABASE IF NOT EXISTS singer_website CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE singer_website;

-- Admin users table
CREATE TABLE admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Songs table
CREATE TABLE songs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    artist VARCHAR(100) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    cover_image VARCHAR(255),
    duration VARCHAR(10),
    genre VARCHAR(50),
    release_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE
);

-- Videos table
CREATE TABLE videos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    video_url VARCHAR(500) NOT NULL,
    thumbnail VARCHAR(255),
    video_type ENUM('youtube', 'vimeo', 'uploaded') DEFAULT 'youtube',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE
);

-- Tour dates table
CREATE TABLE tour_dates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_name VARCHAR(200) NOT NULL,
    venue VARCHAR(200) NOT NULL,
    city VARCHAR(100) NOT NULL,
    country VARCHAR(100) NOT NULL,
    event_date DATE NOT NULL,
    event_time TIME,
    ticket_url VARCHAR(500),
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE
);

-- Newsletter subscribers table
CREATE TABLE newsletter_subscribers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) UNIQUE NOT NULL,
    name VARCHAR(100),
    subscribe_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE
);

-- Contact messages table
CREATE TABLE contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subject VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_read BOOLEAN DEFAULT FALSE
);

-- Insert default admin user (password: admin123)
INSERT INTO admin_users (username, password, email) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@singerwebsite.com');

-- Insert sample data
INSERT INTO songs (title, artist, file_path, cover_image, duration, genre, release_date) VALUES 
('Midnight Dreams', 'Artist Name', 'uploads/songs/midnight-dreams.mp3', 'uploads/covers/midnight-dreams.jpg', '3:45', 'Pop', '2024-01-15'),
('Electric Hearts', 'Artist Name', 'uploads/songs/electric-hearts.mp3', 'uploads/covers/electric-hearts.jpg', '4:12', 'Electronic', '2024-02-20'),
('Golden Hour', 'Artist Name', 'uploads/songs/golden-hour.mp3', 'uploads/covers/golden-hour.jpg', '3:28', 'Acoustic', '2024-03-10');

INSERT INTO videos (title, description, video_url, thumbnail, video_type) VALUES 
('Midnight Dreams - Official Video', 'Official music video for Midnight Dreams', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'uploads/thumbnails/midnight-dreams-video.jpg', 'youtube'),
('Live Performance - Electric Hearts', 'Live performance from the summer tour', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'uploads/thumbnails/electric-hearts-live.jpg', 'youtube');

INSERT INTO tour_dates (event_name, venue, city, country, event_date, event_time, ticket_url, description) VALUES 
('Summer Tour 2024', 'Madison Square Garden', 'New York', 'USA', '2024-07-15', '20:00', 'https://tickets.example.com', 'Kick off the summer tour with an unforgettable night'),
('Acoustic Sessions', 'Royal Albert Hall', 'London', 'UK', '2024-08-22', '19:30', 'https://tickets.example.com', 'Intimate acoustic performance'),
('Festival Headliner', 'Coachella Valley', 'California', 'USA', '2024-09-15', '18:00', 'https://tickets.example.com', 'Main stage performance at the desert festival');
