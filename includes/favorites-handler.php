<?php
require_once 'database.php';
require_once 'functions.php';

header('Content-Type: application/json');

try {
    // Get POST data
    $input = json_decode(file_get_contents('php://input'), true);
    $songId = $input['song_id'] ?? null;
    
    if (!$songId) {
        throw new Exception('Song ID is required');
    }
    
    // Check if user is logged in (you might need to implement session management)
    session_start();
    $userId = $_SESSION['user_id'] ?? null;
    
    if (!$userId) {
        // For demo purposes, we'll use a default user ID
        // In production, you should require user login
        $userId = 1;
    }
    
    // Get database connection
    $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if already in favorites
    $checkQuery = "SELECT id FROM favorites WHERE user_id = ? AND song_id = ?";
    $stmt = $pdo->prepare($checkQuery);
    $stmt->execute([$userId, $songId]);
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($existing) {
        // Remove from favorites
        $deleteQuery = "DELETE FROM favorites WHERE user_id = ? AND song_id = ?";
        $stmt = $pdo->prepare($deleteQuery);
        $stmt->execute([$userId, $songId]);
        
        echo json_encode([
            'success' => true,
            'action' => 'removed',
            'message' => 'Removed from favorites'
        ]);
    } else {
        // Add to favorites
        $insertQuery = "INSERT INTO favorites (user_id, song_id, created_at) VALUES (?, ?, NOW())";
        $stmt = $pdo->prepare($insertQuery);
        $stmt->execute([$userId, $songId]);
        
        echo json_encode([
            'success' => true,
            'action' => 'added',
            'message' => 'Added to favorites'
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
