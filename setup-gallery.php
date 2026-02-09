<?php
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/functions.php';

try {
    echo "Setting up Gallery System...\n\n";
    
    // Create gallery table
    $sql = "CREATE TABLE IF NOT EXISTS gallery (
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
    )";
    
    if (executeQuery($sql)) {
        echo "✓ Gallery table created successfully\n";
    } else {
        echo "❌ Failed to create gallery table\n";
    }
    
    // Create uploads directories
    $upload_dir = __DIR__ . '/uploads/gallery';
    $thumbnail_dir = __DIR__ . '/uploads/gallery/thumbnails';
    
    if (!file_exists($upload_dir)) {
        if (mkdir($upload_dir, 0755, true)) {
            echo "✓ Gallery upload directory created\n";
        } else {
            echo "❌ Failed to create gallery upload directory\n";
        }
    }
    
    if (!file_exists($thumbnail_dir)) {
        if (mkdir($thumbnail_dir, 0755, true)) {
            echo "✓ Gallery thumbnail directory created\n";
        } else {
            echo "❌ Failed to create gallery thumbnail directory\n";
        }
    }
    
    // Insert sample gallery data
    $sample_images = [
        [
            'title' => 'Concert Performance',
            'description' => 'Amazing performance at Madison Square Garden with thousands of fans singing along to every song.',
            'image_url' => 'https://picsum.photos/seed/concert1/800/600.jpg',
            'thumbnail_url' => 'https://picsum.photos/seed/concert1/300/200.jpg',
            'category' => 'performances',
            'tags' => 'concert, live, madison square garden, performance'
        ],
        [
            'title' => 'Studio Session',
            'description' => 'Behind the scenes recording session for the new album. Working with amazing producers.',
            'image_url' => 'https://picsum.photos/seed/studio1/800/600.jpg',
            'thumbnail_url' => 'https://picsum.photos/seed/studio1/300/200.jpg',
            'category' => 'behind-scenes',
            'tags' => 'studio, recording, behind scenes, album'
        ],
        [
            'title' => 'Photo Shoot',
            'description' => 'Professional photo shoot for the new album cover. Amazing team and great vibes.',
            'image_url' => 'https://picsum.photos/seed/photoshoot1/800/600.jpg',
            'thumbnail_url' => 'https://picsum.photos/seed/photoshoot1/300/200.jpg',
            'category' => 'photoshoot',
            'tags' => 'photo shoot, album cover, professional'
        ],
        [
            'title' => 'Fan Meeting',
            'description' => 'Meeting amazing fans at the concert. Your energy and support means everything!',
            'image_url' => 'https://picsum.photos/seed/fans1/800/600.jpg',
            'thumbnail_url' => 'https://picsum.photos/seed/fans1/300/200.jpg',
            'category' => 'fans',
            'tags' => 'fans, meeting, concert, interaction'
        ],
        [
            'title' => 'Music Video',
            'description' => 'Behind the scenes of our latest music video shoot. So much fun creating this!',
            'image_url' => 'https://picsum.photos/seed/video1/800/600.jpg',
            'thumbnail_url' => 'https://picsum.photos/seed/video1/300/200.jpg',
            'category' => 'music-videos',
            'tags' => 'music video, behind scenes, shooting'
        ]
    ];
    
    foreach ($sample_images as $image) {
        $sql = "INSERT IGNORE INTO gallery (title, description, image_url, thumbnail_url, category, tags) VALUES (?, ?, ?, ?, ?, ?)";
        $params = [
            $image['title'],
            $image['description'],
            $image['image_url'],
            $image['thumbnail_url'],
            $image['category'],
            $image['tags']
        ];
        
        if (executeQuery($sql, $params)) {
            echo "✓ Sample image '{$image['title']}' added\n";
        }
    }
    
    echo "\n✅ Gallery system setup completed successfully!\n";
    echo "\n📁 Upload directories created: uploads/gallery/ and uploads/gallery/thumbnails/\n";
    echo "🖼️ Sample images added for testing\n";
    echo "🎯 Gallery page available at: /gallery.php\n";
    echo "⚙️ Admin gallery management at: /admin/gallery.php\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
