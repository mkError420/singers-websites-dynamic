<?php
require_once __DIR__ . '/includes/database.php';

try {
    // Create profile table
    $sql = "CREATE TABLE IF NOT EXISTS profile (
        id INT AUTO_INCREMENT PRIMARY KEY,
        profile_image VARCHAR(255) NOT NULL DEFAULT 'assets/images/artist-photo.jpg',
        artist_name VARCHAR(255) NOT NULL,
        tagline VARCHAR(500) DEFAULT NULL,
        bio TEXT DEFAULT NULL,
        years_experience VARCHAR(50) DEFAULT NULL,
        songs_count VARCHAR(50) DEFAULT NULL,
        views_count VARCHAR(50) DEFAULT NULL,
        status ENUM('active', 'inactive') DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $pdo->exec($sql);
    echo "✅ Profile table created successfully!<br>";
    
    // Insert default profile if not exists
    $checkSql = "SELECT COUNT(*) FROM profile";
    $stmt = $pdo->query($checkSql);
    $count = $stmt->fetchColumn();
    
    if ($count == 0) {
        $insertSql = "INSERT INTO profile (artist_name, tagline, bio, years_experience, songs_count, views_count) 
                     VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($insertSql);
        $stmt->execute([
            'Artist Name',
            'Musician, Songwriter, Storyteller',
            'From humble beginnings to international stages, my journey has been driven by a passion for creating music that speaks to the soul. Every song tells a story, every melody carries emotion, and every performance is an opportunity to connect with amazing people like you.',
            '10+',
            '50+',
            '1M+'
        ]);
        echo "✅ Default profile data inserted successfully!<br>";
    } else {
        echo "ℹ️ Profile data already exists.<br>";
    }
    
    echo "<br><a href='admin/profile.php'>Go to Profile Management</a>";
    
} catch (PDOException $e) {
    echo "❌ Error creating profile table: " . $e->getMessage();
}
?>
