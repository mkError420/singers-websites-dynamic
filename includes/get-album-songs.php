<?php
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/functions.php';

header('Content-Type: application/json');

// Get album name from POST request
$album = sanitize_input($_POST['album'] ?? '');

if (empty($album)) {
    echo json_encode(['success' => false, 'message' => 'Album name is required']);
    exit();
}

try {
    // Get all songs from the specified album
    $songs_query = "SELECT * FROM songs WHERE album = ? AND is_active = TRUE ORDER BY created_at ASC";
    $songs = fetchAll($songs_query, [$album]);
    
    if (!empty($songs)) {
        echo json_encode([
            'success' => true,
            'album' => $album,
            'songs' => $songs,
            'count' => count($songs)
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'No songs found in this album'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
