<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/functions.php';

// Get all songs
$all_songs_query = "SELECT * FROM songs WHERE is_active = TRUE ORDER BY created_at DESC";
$all_songs = fetchAll($all_songs_query);

// Pagination settings
$songs_per_page = 8;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $songs_per_page;

// Get total songs count
$total_songs_query = "SELECT COUNT(*) as total FROM songs WHERE is_active = TRUE";
$total_result = fetchOne($total_songs_query);
$total_songs = $total_result['total'];
$total_pages = ceil($total_songs / $songs_per_page);

// Get songs for current page
$songs_query = "SELECT * FROM songs WHERE is_active = TRUE ORDER BY created_at DESC LIMIT ? OFFSET ?";
$allSongs = fetchAll($songs_query, [$songs_per_page, $offset]);
?>

<!-- Music Section -->
<section class="section">
    <div class="container">
        <div class="section-title">
            <h2>Music</h2>
            <p>Complete discography and latest releases</p>
        </div>
        
        <div class="music-player">
            <div class="player-header">
                <div class="now-playing">
                    <h4>Now Playing</h4>
                    <div class="track-info">
                        <span id="currentSongTitle">Select a song</span>
                        <span id="currentSongArtist">-</span>
                    </div>
                </div>
                <div class="player-controls">
                    <button id="shuffleBtn" class="control-btn" title="Shuffle"><i class="fas fa-random"></i></button>
                    <button id="prevBtn" class="control-btn" title="Previous"><i class="fas fa-step-backward"></i></button>
                    <button id="playPauseBtn" class="control-btn play-pause" title="Play/Pause"><i class="fas fa-play"></i></button>
                    <button id="nextBtn" class="control-btn" title="Next"><i class="fas fa-step-forward"></i></button>
                    <button id="repeatBtn" class="control-btn" title="Repeat"><i class="fas fa-redo"></i></button>
                </div>
            </div>
            
            <div class="progress-section">
                <div class="progress-container">
                    <div class="progress-bar" id="progressBar"></div>
                </div>
                <div class="time-display">
                    <span id="currentTime">0:00</span>
                    <span id="duration">0:00</span>
                </div>
            </div>
            
            <div class="player-footer">
                <div class="volume-control">
                    <i class="fas fa-volume-up"></i>
                    <input type="range" id="volumeSlider" min="0" max="100" value="70">
                </div>
                <div class="player-actions">
                    <button id="playAllBtn" class="control-btn" title="Play All"><i class="fas fa-play-circle"></i></button>
                    <button id="shuffleAllBtn" class="control-btn" title="Shuffle All"><i class="fas fa-random"></i></button>
                </div>
            </div>
        </div>
        
        <div class="playlist-container" id="playlistContainer">
            <div class="playlist-header">
                <h3>Playlist</h3>
                <div class="playlist-controls">
                    <div class="search-container">
                        <input type="text" id="playlistSearch" placeholder="Search songs..." class="search-input">
                        <button id="clearSearch" class="btn btn-sm secondary">Clear</button>
                    </div>
                    <div class="playlist-actions">
                        <button id="playAllBtn" class="btn btn-sm">Play All</button>
                        <button id="shuffleAllBtn" class="btn btn-sm secondary">Shuffle</button>
                    </div>
                </div>
            </div>
            
            <div class="playlist">
                <?php if (!empty($allSongs)): ?>
                    <?php foreach ($allSongs as $index => $song): ?>
                        <div class="song-item" data-audio="<?php echo APP_URL . '/' . $song['file_path']; ?>" data-index="<?php echo $index; ?>">
                            <div class="song-number"><?php echo ($page - 1) * $songs_per_page + $index + 1; ?></div>
                            <img src="<?php echo APP_URL . '/' . ($song['cover_image'] ?: 'assets/images/default-album.jpg'); ?>" 
                                 alt="<?php echo xss_clean($song['title']); ?>" class="song-cover">
                            <div class="song-info">
                                <div class="song-title"><?php echo xss_clean($song['title']); ?></div>
                                <div class="song-artist"><?php echo xss_clean($song['artist']); ?></div>
                                <?php if ($song['album']): ?>
                                    <div class="song-album"><?php echo xss_clean($song['album']); ?></div>
                                <?php endif; ?>
                                <?php if ($song['genre']): ?>
                                    <div class="song-genre"><?php echo xss_clean($song['genre']); ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="song-meta">
                                <div class="song-duration"><?php echo $song['duration']; ?></div>
                                <?php if ($song['release_date']): ?>
                                    <div class="song-release"><?php echo format_date($song['release_date'], 'M j, Y'); ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="song-actions">
                                <button class="btn-icon" onclick="downloadSong('<?php echo $song['file_path']; ?>')" title="Download">
                                    <i class="fas fa-download"></i>
                                </button>
                                <button class="btn-icon" onclick="shareSong('<?php echo xss_clean($song['title']); ?>')" title="Share">
                                    <i class="fas fa-share"></i>
                                </button>
                                <button class="btn-icon" onclick="addToFavorites(<?php echo $song['id']; ?>)" title="Add to Favorites">
                                    <i class="far fa-heart"></i>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-content">
                        <p>No songs available yet. Check back soon!</p>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Playlist Pagination -->
            <?php if ($total_pages > 1): ?>
                <div class="playlist-pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?php echo $page - 1; ?>#playlist" class="pagination-btn prev">
                            <i class="fas fa-chevron-left"></i> Previous
                        </a>
                    <?php endif; ?>
                    
                    <div class="pagination-pages">
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <?php if ($i == $page): ?>
                                <span class="pagination-current"><?php echo $i; ?></span>
                            <?php else: ?>
                                <a href="?page=<?php echo $i; ?>#playlist" class="pagination-page"><?php echo $i; ?></a>
                            <?php endif; ?>
                        <?php endfor; ?>
                    </div>
                    
                    <?php if ($page < $total_pages): ?>
                        <a href="?page=<?php echo $page + 1; ?>#playlist" class="pagination-btn next">
                            Next <i class="fas fa-chevron-right"></i>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Albums Section -->
        <div class="albums-section" style="margin-top: 4rem;">
            <div class="section-title">
                <h3>Albums</h3>
                <p>Complete album collection</p>
            </div>
            
            <div class="albums-container">
                <?php
                // Pagination settings
                $albums_per_page = 6; // 3 items per row × 2 rows = 6 per page
                $page = isset($_GET['album_page']) ? (int)$_GET['album_page'] : 1;
                $offset = ($page - 1) * $albums_per_page;
                
                // Get total albums count
                $total_albums_query = "SELECT COUNT(DISTINCT album) as total FROM songs WHERE album IS NOT NULL AND album != ''";
                $total_result = fetchOne($total_albums_query);
                $total_albums = $total_result['total'];
                $total_pages = ceil($total_albums / $albums_per_page);
                
                // Get albums for current page
                $albums_query = "SELECT DISTINCT album, artist, cover_image FROM songs WHERE album IS NOT NULL AND album != '' GROUP BY album ORDER BY album ASC LIMIT ? OFFSET ?";
                $albums = fetchAll($albums_query, [$albums_per_page, $offset]);
                
                if (!empty($albums)):
                ?>
                    <div class="albums-grid">
                        <?php foreach ($albums as $album): ?>
                            <?php
                            // Get all songs from this album
                            $album_songs_query = "SELECT * FROM songs WHERE album = ? AND is_active = TRUE ORDER BY created_at ASC";
                            $album_songs = fetchAll($album_songs_query, [$album['album']]);
                            ?>
                            
                            <div class="album-item">
                                <div class="album-header">
                                    <img src="<?php echo APP_URL . '/' . ($album['cover_image'] ?: 'assets/images/default-album.jpg'); ?>" 
                                         alt="<?php echo xss_clean($album['album']); ?>" class="album-cover">
                                    <div class="album-info">
                                        <h4><?php echo xss_clean($album['album']); ?></h4>
                                        <p><?php echo xss_clean($album['artist']); ?> • <?php echo count($album_songs); ?> songs</p>
                                    </div>
                                </div>
                                
                                <div class="album-songs">
                                    <h5>Tracks</h5>
                                    <div class="songs-list">
                                        <?php foreach ($album_songs as $index => $song): ?>
                                            <div class="album-song-item" 
                                                 data-audio="<?php echo APP_URL . '/' . $song['file_path']; ?>" 
                                                 data-index="<?php echo $index; ?>"
                                                 data-title="<?php echo xss_clean($song['title']); ?>"
                                                 data-artist="<?php echo xss_clean($song['artist']); ?>"
                                                 data-album="<?php echo xss_clean($album['album']); ?>">
                                                <div class="song-number"><?php echo $index + 1; ?></div>
                                                <div class="song-details">
                                                    <div class="song-title"><?php echo xss_clean($song['title']); ?></div>
                                                    <div class="song-meta">
                                                        <span class="song-duration"><?php echo $song['duration']; ?></span>
                                                        <?php if ($song['genre']): ?>
                                                            <span class="song-genre"><?php echo xss_clean($song['genre']); ?></span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                                <div class="song-actions">
                                                    <button class="btn-icon" onclick="downloadSong('<?php echo $song['file_path']; ?>')" title="Download">
                                                        <i class="fas fa-download"></i>
                                                    </button>
                                                    <button class="btn-icon" onclick="shareSong('<?php echo xss_clean($song['title']); ?>')" title="Share">
                                                        <i class="fas fa-share"></i>
                                                    </button>
                                                    <button class="btn-icon" onclick="addToFavorites(<?php echo $song['id']; ?>)" title="Add to Favorites">
                                                        <i class="far fa-heart"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Pagination -->
                    <?php if ($total_pages > 1): ?>
                        <div class="albums-pagination">
                            <?php if ($page > 1): ?>
                                <a href="?album_page=<?php echo $page - 1; ?>#albums" class="pagination-btn prev">
                                    <i class="fas fa-chevron-left"></i> Previous
                                </a>
                            <?php endif; ?>
                            
                            <div class="pagination-pages">
                                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                    <?php if ($i == $page): ?>
                                        <span class="pagination-current"><?php echo $i; ?></span>
                                    <?php else: ?>
                                        <a href="?album_page=<?php echo $i; ?>#albums" class="pagination-page"><?php echo $i; ?></a>
                                    <?php endif; ?>
                                <?php endfor; ?>
                            </div>
                            
                            <?php if ($page < $total_pages): ?>
                                <a href="?album_page=<?php echo $page + 1; ?>#albums" class="pagination-btn next">
                                    Next <i class="fas fa-chevron-right"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="no-content">
                        <p>No albums available yet. Add songs with album information to see them here!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<style>
/* Music Page Specific Styles */
.music-player {
    background: linear-gradient(135deg, var(--dark-secondary) 0%, var(--dark-tertiary) 100%);
    border-radius: 20px;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3), inset 0 1px 0 rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.1);
    position: relative;
    overflow: hidden;
}

.music-player::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 1px;
    background: linear-gradient(90deg, transparent, var(--primary-color), transparent);
    opacity: 0.5;
}

.player-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    padding-bottom: 1.5rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.now-playing h4 {
    margin: 0 0 0.5rem 0;
    color: var(--text-secondary);
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.track-info {
    display: flex;
    flex-direction: column;
}

.track-info span:first-child {
    font-weight: 600;
    font-size: 1.1rem;
    color: var(--text-primary);
}

.track-info span:last-child {
    color: var(--text-secondary);
    font-size: 0.9rem;
}

.player-controls {
    display: flex;
    gap: 1rem;
    align-items: center;
}

.control-btn {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: var(--text-primary);
    font-size: 1.2rem;
    cursor: pointer;
    transition: all 0.3s ease;
    padding: 0.5rem;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    backdrop-filter: blur(10px);
}

.control-btn:hover {
    background: var(--primary-color);
    border-color: var(--primary-color);
    transform: scale(1.1);
    box-shadow: 0 5px 15px rgba(255, 107, 107, 0.3);
}

.control-btn.shuffle-active {
    color: #FF6B6B !important;
    background: rgba(255, 107, 107, 0.2) !important;
    border-color: #FF6B6B !important;
}

.control-btn.repeat-active {
    color: #FF6B6B !important;
    background: rgba(255, 107, 107, 0.2) !important;
    border-color: #FF6B6B !important;
}

.control-btn.play-pause {
    background: var(--primary-color);
    color: var(--text-primary);
    font-size: 1.5rem;
    width: 50px;
    height: 50px;
    box-shadow: 0 5px 20px rgba(255, 107, 107, 0.4);
}

.control-btn.play-pause:hover {
    background: var(--secondary-color);
    transform: scale(1.15);
    box-shadow: 0 8px 25px rgba(255, 107, 107, 0.5);
}

.progress-section {
    margin-bottom: 2rem;
}

.progress-container {
    position: relative;
    margin-bottom: 1.5rem;
    background: rgba(255, 255, 255, 0.1);
    height: 8px;
    border-radius: 10px;
    overflow: hidden;
    cursor: pointer;
    backdrop-filter: blur(5px);
}

.progress-container::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
    animation: shimmer 2s infinite;
}

@keyframes shimmer {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
}

.progress-bar {
    height: 4px;
    background: var(--dark-tertiary);
    border-radius: 2px;
    position: relative;
    cursor: pointer;
}

.progress-bar::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    height: 100%;
    background: linear-gradient(90deg, var(--primary-color), #ff6b6b);
    border-radius: 2px;
    width: 0%;
    transition: width 0.1s linear;
    box-shadow: 0 0 10px rgba(255, 107, 107, 0.5);
}

.progress-bar::after {
    content: '';
    position: absolute;
    right: 0;
    top: 50%;
    transform: translateY(-50%);
    width: 16px;
    height: 16px;
    background: var(--text-primary);
    border-radius: 50%;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.progress-container:hover .progress-bar::after {
    opacity: 1;
}

.time-display {
    display: flex;
    justify-content: space-between;
    margin-top: 0.5rem;
    font-size: 0.9rem;
    color: var(--text-muted);
}

.player-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 2rem;
}

.volume-control {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.volume-control i {
    color: var(--text-secondary);
    font-size: 1.1rem;
}

.volume-control input[type="range"] {
    width: 100px;
    height: 6px;
    background: rgba(255, 255, 255, 0.1);
    outline: none;
    border-radius: 10px;
    -webkit-appearance: none;
    appearance: none;
    backdrop-filter: blur(5px);
}

.volume-control input[type="range"]::-webkit-slider-thumb {
    -webkit-appearance: none;
    appearance: none;
    width: 16px;
    height: 16px;
    background: var(--primary-color);
    border-radius: 50%;
    cursor: pointer;
    box-shadow: 0 2px 8px rgba(255, 107, 107, 0.3);
}

.volume-control input[type="range"]::-moz-range-thumb {
    width: 16px;
    height: 16px;
    background: var(--primary-color);
    border-radius: 50%;
    cursor: pointer;
    border: none;
    box-shadow: 0 2px 8px rgba(255, 107, 107, 0.3);
}

.playlist-toggle button {
    background: none;
    border: 1px solid var(--border-color);
    color: var(--text-primary);
    padding: 0.5rem 1rem;
    border-radius: 20px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.playlist-toggle button:hover {
    background: var(--primary-color);
    border-color: var(--primary-color);
}

.playlist-container {
    margin-top: 2rem;
}

.playlist {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    max-height: 600px;
    overflow-y: auto;
}

.playlist-pagination {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 2rem;
    padding: 1rem;
    background: var(--dark-secondary);
    border-radius: 10px;
}

.playlist-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid var(--border-color);
}

.playlist-controls {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom:1px solid var(--border-color);
    gap: 1rem;
    flex-wrap: wrap;
}

.search-container {
    display: flex;
    gap: 0.5rem;
    align-items: center;
    flex: 1;
    max-width: 300px;
}

.search-input {
    flex: 1;
    padding: 0.5rem 1rem;
    border: 1px solid var(--border-color);
    border-radius: 20px;
    background: var(--dark-tertiary);
    color: var(--text-primary);
    font-size: 0.9rem;
    transition: all 0.3s ease;
}

.search-input:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 2px rgba(255, 107, 107, 0.2);
}

.search-input::placeholder {
    color: var(--text-muted);
}

.playlist-actions {
    display: flex;
    gap: 0.5rem;
    align-items: center;
}

.no-search-results {
    text-align: center;
    padding: 2rem;
    color: var(--text-muted);
    font-style: italic;
}

.no-search-results p {
    margin: 0;
    font-size: 1.1rem;
}

.btn-sm {
    padding: 0.5rem 1rem;
    font-size: 0.9rem;
}

.btn.secondary {
    background: transparent;
    color: var(--text-primary);
    border: 1px solid var(--border-color);
}

.btn.secondary:hover {
    background: var(--dark-tertiary);
}

.song-item {
    display: grid;
    grid-template-columns: 40px 60px 1fr auto 120px;
    align-items: center;
    padding: 1rem;
    border-bottom: 1px solid var(--border-color);
    cursor: pointer;
    transition: background 0.3s ease;
    gap: 1rem;
}

.song-item:hover {
    background: rgba(255, 255, 255, 0.05);
}

.song-item.active {
    background: rgba(255, 107, 107, 0.1);
    border-left: 3px solid var(--primary-color);
}

.song-number {
    color: var(--text-muted);
    font-size: 0.9rem;
    text-align: center;
}

.song-cover {
    width: 50px;
    height: 50px;
    border-radius: 8px;
    object-fit: cover;
}

.song-info {
    flex: 1;
}

.song-info .song-title {
    color: var(--text-primary);
    font-weight: 500;
    margin-bottom: 0.25rem;
}

.song-info .song-artist {
    color: var(--text-secondary);
    font-size: 0.9rem;
    margin-bottom: 0.25rem;
}

.song-info .song-album {
    color: var(--text-muted);
    font-size: 0.8rem;
    margin-bottom: 0.25rem;
    font-style: italic;
}

.song-info .song-genre {
    background: var(--primary-color);
    color: var(--text-primary);
    padding: 0.15rem 0.5rem;
    border-radius: 10px;
    font-size: 0.75rem;
    display: inline-block;
    margin-right: 0.5rem;
}

.song-genre {
    color: var(--text-muted);
    font-size: 0.8rem;
    margin-top: 0.25rem;
}

.song-meta {
    text-align: right;
    color: var(--text-muted);
    font-size: 0.9rem;
}

.song-actions {
    display: flex;
    gap: 0.5rem;
    justify-content: center;
}

.btn-icon {
    background: none;
    border: none;
    color: var(--text-muted);
    cursor: pointer;
    padding: 0.5rem;
    border-radius: 50%;
    transition: all 0.3s ease;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.btn-icon:hover {
    background: var(--dark-tertiary);
    color: var(--primary-color);
}

.albums-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 2rem;
}

.albums-container {
    display: flex;
    flex-direction: column;
    gap: 3rem;
}

.album-item {
    background: var(--dark-secondary);
    border-radius: 15px;
    overflow: hidden;
    box-shadow: var(--shadow-md);
    transition: transform 0.3s ease;
}

.album-item:hover {
    transform: translateY(-5px);
}

.album-item .album-header {
    display: flex;
    align-items: center;
    padding: 1.5rem;
    border-bottom: 1px solid var(--border-color);
    gap: 1.5rem;
}

.album-item .album-cover {
    width: 80px;
    height: 80px;
    border-radius: 8px;
    object-fit: cover;
    flex-shrink: 0;
}

.album-item .album-info {
    flex: 1;
    text-align: left;
}

.album-item .album-info h4 {
    margin-bottom: 0.5rem;
    color: var(--text-primary);
    font-size: 1.1rem;
}

.album-item .album-info p {
    color: var(--text-secondary);
    margin-bottom: 1rem;
    font-size: 0.9rem;
}

.album-item .album-songs {
    padding: 1rem 1.5rem;
    max-height: 300px;
    overflow-y: auto;
}

.album-item .album-songs h5 {
    color: var(--text-primary);
    margin-bottom: 1rem;
    font-size: 1rem;
    font-weight: 600;
}

.album-item .songs-list {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.album-item .album-song-item {
    display: flex;
    align-items: center;
    padding: 0.75rem;
    background: var(--dark-tertiary);
    border-radius: 6px;
    cursor: pointer;
    transition: background 0.3s ease;
    gap: 0.75rem;
}

.album-item .album-song-item:hover {
    background: rgba(255, 255, 255, 0.05);
}

.album-item .album-song-item.active {
    background: rgba(255, 107, 107, 0.2);
    border-left: 4px solid var(--primary-color);
    box-shadow: 0 2px 8px rgba(255, 107, 107, 0.3);
    transform: translateX(2px);
}

.album-item .album-song-item.active .song-title {
    color: var(--primary-color);
    font-weight: 600;
}

.album-item .album-song-item.active .song-number {
    color: var(--primary-color);
    font-weight: 600;
}

.album-item .album-song-item.active .song-genre {
    background: var(--secondary-color);
    color: var(--text-primary);
}

.album-item .album-song-item.playing {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% {
        background: rgba(255, 107, 107, 0.1);
    }
    50% {
        background: rgba(255, 107, 107, 0.3);
    }
    100% {
        background: rgba(255, 107, 107, 0.1);
    }
}

.album-item .album-song-item .song-number {
    width: 25px;
    color: var(--text-muted);
    font-size: 0.8rem;
    text-align: center;
    flex-shrink: 0;
}

.album-item .album-song-item .song-details {
    flex: 1;
}

.album-item .album-song-item .song-title {
    color: var(--text-primary);
    font-weight: 500;
    margin-bottom: 0.25rem;
    font-size: 0.9rem;
}

.album-item .album-song-item .song-meta {
    display: flex;
    gap: 0.75rem;
    color: var(--text-muted);
    font-size: 0.8rem;
}

.album-item .album-song-item .song-genre {
    background: var(--primary-color);
    color: var(--text-primary);
    padding: 0.1rem 0.4rem;
    border-radius: 8px;
    font-size: 0.7rem;
}

.album-item .album-song-item .song-actions {
    display: flex;
    gap: 0.5rem;
    flex-shrink: 0;
}

.album-item .album-song-item .song-actions .btn-icon {
    width: 28px;
    height: 28px;
    font-size: 0.8rem;
}

.albums-pagination {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 2rem;
    padding: 1rem;
    background: var(--dark-secondary);
    border-radius: 10px;
}

.pagination-pages {
    display: flex;
    gap: 0.5rem;
}

.pagination-btn {
    background: var(--primary-color);
    color: var(--text-primary);
    padding: 0.5rem 1rem;
    border-radius: 20px;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.pagination-btn:hover {
    background: var(--secondary-color);
    transform: translateY(-2px);
}

.pagination-page {
    background: var(--dark-tertiary);
    color: var(--text-primary);
    padding: 0.5rem 0.75rem;
    border-radius: 15px;
    text-decoration: none;
    transition: all 0.3s ease;
}

.pagination-page:hover {
    background: var(--primary-color);
    color: var(--text-primary);
}

.pagination-current {
    background: var(--primary-color);
    color: var(--text-primary);
    padding: 0.5rem 0.75rem;
    border-radius: 15px;
    font-weight: 600;
}

.album-card {
    background: var(--dark-secondary);
    border-radius: 15px;
    overflow: hidden;
    transition: transform 0.3s ease;
    box-shadow: var(--shadow-md);
}

.album-card:hover {
    transform: translateY(-5px);
}

.album-cover {
    width: 100%;
    height: 250px;
    object-fit: cover;
}

.album-info {
    padding: 1.5rem;
    text-align: center;
}

.album-info h4 {
    margin-bottom: 0.5rem;
    color: var(--text-primary);
}

.album-info p {
    color: var(--text-secondary);
    margin-bottom: 1rem;
}

@media (max-width: 768px) {
    .song-item {
        grid-template-columns: 30px 50px 1fr;
        gap: 0.5rem;
    }
    
    .song-meta,
    .song-actions {
        display: none;
    }
    
    .player-header {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }
    
    .player-footer {
        flex-direction: column;
        gap: 1rem;
    }
    
    .playlist-header {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }
    
    .albums-grid {
        grid-template-columns: repeat(2, 1fr);  
        gap: 1rem;
    }
    
    .album-item .album-header {
        flex-direction: column;
        text-align: center;
        gap: 1rem;
        padding: 1rem;
    }
    
    .album-item .album-cover {
        width: 60px;
        height: 60px;
    }
    
    .album-item .album-songs {
        max-height: 200px;
        padding: 0.75rem;
    }
    
    .album-item .album-song-item .song-number {
        width: 20px;
        font-size: 0.7rem;
    }
    
    .album-item .album-song-item .song-title {
        font-size: 0.8rem;
    }
    
    .album-item .album-song-item .song-meta {
        font-size: 0.7rem;
    }
    
    .albums-pagination {
        flex-direction: column;
        gap: 1rem;
    }
    
    .pagination-pages {
        justify-content: center;
        flex-wrap: wrap;
    }
}

</style>

<script>
// Enhanced Music Player Functionality
document.addEventListener('DOMContentLoaded', function() {
    initEnhancedAudioPlayer();
});

function initEnhancedAudioPlayer() {
    const audioPlayer = document.getElementById('audioPlayer');
    const playPauseBtn = document.getElementById('playPauseBtn');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const shuffleBtn = document.getElementById('shuffleBtn');
    const repeatBtn = document.getElementById('repeatBtn');
    const progressBar = document.getElementById('progressBar');
    const currentTimeEl = document.getElementById('currentTime');
    const durationEl = document.getElementById('duration');
    const volumeSlider = document.getElementById('volumeSlider');
    const songItems = document.querySelectorAll('.song-item');
    const playAllBtn = document.getElementById('playAllBtn');
    const shuffleAllBtn = document.getElementById('shuffleAllBtn');
    const playlistToggle = document.getElementById('playlistToggle');
    const playlistContainer = document.getElementById('playlistContainer');
    
    if (!audioPlayer) return;
    
    // Global variables for album playback
    window.currentAlbumSongs = null;
    window.currentAlbumName = null;
    window.currentSongIndex = 0;
    window.isPlaying = false;
    window.isShuffled = false;
    window.isRepeating = false;
    
    let originalPlaylist = Array.from(songItems);
    let shuffledPlaylist = [...originalPlaylist];
    
    // Load song
    function loadSong(index) {
        const songs = isShuffled ? shuffledPlaylist : originalPlaylist;
        const song = songs[index];
        const audioSrc = song.dataset.audio;
        const title = song.querySelector('.song-title').textContent;
        const artist = song.querySelector('.song-artist').textContent;
        
        audioPlayer.src = audioSrc;
        document.getElementById('currentSongTitle').textContent = title;
        document.getElementById('currentSongArtist').textContent = artist;
        
        // Update active state
        songItems.forEach(s => s.classList.remove('active'));
        song.classList.add('active');
        
        // Scroll to current song
        song.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
    
    // Play/Pause functionality
    function togglePlayPause() {
        if (window.isPlaying) {
            audioPlayer.pause();
            playPauseBtn.innerHTML = '<i class="fas fa-play"></i>';
        } else {
            audioPlayer.play();
            playPauseBtn.innerHTML = '<i class="fas fa-pause"></i>';
        }
        window.isPlaying = !window.isPlaying;
        
        // Update playing state in album songs
        updateAlbumSongStates();
    }
    
    // Shuffle playlist
    function shufflePlaylist() {
        if (!window.isShuffled) {
            if (window.currentAlbumSongs) {
                // Shuffle album songs
                window.currentAlbumSongs = [...window.currentAlbumSongs].sort(() => Math.random() - 0.5);
            } else {
                // Shuffle regular playlist
                shuffledPlaylist = [...originalPlaylist].sort(() => Math.random() - 0.5);
            }
            window.isShuffled = true;
            shuffleBtn.classList.add('shuffle-active');
        } else {
            if (window.currentAlbumSongs) {
                // Restore original album order
                const albumName = window.currentAlbumName;
                const album_songs_query = "SELECT * FROM songs WHERE album = ? AND is_active = TRUE ORDER BY created_at ASC";
                // This would need a fetch call, for now just sort by title
                window.currentAlbumSongs.sort((a, b) => a.title.localeCompare(b.title));
            } else {
                // Restore original playlist
                shuffledPlaylist = [...originalPlaylist];
            }
            window.isShuffled = false;
            shuffleBtn.classList.remove('shuffle-active');
        }
    }
    
    // Toggle repeat
    function toggleRepeat() {
        window.isRepeating = !window.isRepeating;
        if (window.isRepeating) {
            repeatBtn.classList.add('repeat-active');
        } else {
            repeatBtn.classList.remove('repeat-active');
        }
    }
    
    // Event listeners
    if (playPauseBtn) {
        playPauseBtn.addEventListener('click', togglePlayPause);
    }
    
    if (shuffleBtn) {
        shuffleBtn.addEventListener('click', shufflePlaylist);
    }
    
    if (repeatBtn) {
        repeatBtn.addEventListener('click', toggleRepeat);
    }
    
    // Song selection
    songItems.forEach((item, index) => {
        item.addEventListener('click', function(e) {
            if (e.target.closest('.song-actions')) return;
            
            currentSongIndex = isShuffled ? 
                shuffledPlaylist.indexOf(item) : 
                originalPlaylist.indexOf(item);
            loadSong(currentSongIndex);
            togglePlayPause();
        });
    });
    
    // Album song selection
    const albumSongItems = document.querySelectorAll('.album-song-item');
    albumSongItems.forEach((item, index) => {
        item.addEventListener('click', function(e) {
            if (e.target.closest('.song-actions')) return;
            
            // Get song data from attributes
            const audioSrc = item.dataset.audio;
            const title = item.dataset.title;
            const artist = item.dataset.artist;
            const album = item.dataset.album;
            const songIndex = parseInt(item.dataset.index);
            
            // Create song object for album context
            const songData = {
                file_path: audioSrc,
                title: title,
                artist: artist,
                album: album
            };
            
            // Set current album context
            window.currentAlbumSongs = Array.from(albumSongItems).map(songItem => ({
                file_path: songItem.dataset.audio,
                title: songItem.dataset.title,
                artist: songItem.dataset.artist,
                album: songItem.dataset.album
            }));
            window.currentAlbumName = album;
            window.currentSongIndex = songIndex;
            
            // Load and play selected song
            loadAlbumSongFromData(songData);
            togglePlayPause();
            
            // Update active state immediately
            updateAlbumSongStates();
        });
    });
    
    // Function to update album song states
    function updateAlbumSongStates() {
        const allSongItems = document.querySelectorAll('.album-song-item');
        allSongItems.forEach((item, i) => {
            const isActive = i === window.currentSongIndex;
            const isPlaying = isActive && window.isPlaying;
            
            item.classList.toggle('active', isActive);
            item.classList.toggle('playing', isPlaying);
        });
        
        // Debug: Log current state
        console.log('Album song states updated:', {
            currentIndex: window.currentSongIndex,
            isPlaying: window.isPlaying,
            totalSongs: allSongItems.length
        });
    }
    
    // Load album song from data
    function loadAlbumSongFromData(song) {
        const audioPlayer = document.getElementById('audioPlayer');
        
        // Handle both API data and DOM data formats
        const audioSrc = song.file_path || song.file_path;
        const title = song.title || song.title;
        const artist = song.artist || song.artist;
        
        audioPlayer.src = audioSrc;
        
        // Update now playing display
        document.getElementById('currentSongTitle').textContent = title;
        document.getElementById('currentSongArtist').textContent = artist;
        
        // Update active state in album songs
        updateAlbumSongStates();
    }
    
    // Previous/Next buttons
    if (prevBtn) {
        prevBtn.addEventListener('click', function() {
            if (window.currentAlbumSongs) {
                // Navigate within album (API-loaded songs)
                window.currentSongIndex = (window.currentSongIndex - 1 + window.currentAlbumSongs.length) % window.currentAlbumSongs.length;
                const songData = window.currentAlbumSongs[window.currentSongIndex];
                const formattedSong = {
                    file_path: songData.file_path,
                    title: songData.title,
                    artist: songData.artist,
                    album: window.currentAlbumName
                };
                loadAlbumSongFromData(formattedSong);
                if (window.isPlaying) audioPlayer.play();
            } else {
                // Navigate within playlist
                const songs = window.isShuffled ? shuffledPlaylist : originalPlaylist;
                window.currentSongIndex = (window.currentSongIndex - 1 + songs.length) % songs.length;
                loadSong(window.currentSongIndex);
                if (window.isPlaying) audioPlayer.play();
            }
        });
    }
    
    if (nextBtn) {
        nextBtn.addEventListener('click', function() {
            if (window.currentAlbumSongs) {
                // Navigate within album (API-loaded songs)
                window.currentSongIndex = (window.currentSongIndex + 1) % window.currentAlbumSongs.length;
                const songData = window.currentAlbumSongs[window.currentSongIndex];
                const formattedSong = {
                    file_path: songData.file_path,
                    title: songData.title,
                    artist: songData.artist,
                    album: window.currentAlbumName
                };
                loadAlbumSongFromData(formattedSong);
                if (window.isPlaying) audioPlayer.play();
            } else {
                // Navigate within playlist
                const songs = window.isShuffled ? shuffledPlaylist : originalPlaylist;
                window.currentSongIndex = (window.currentSongIndex + 1) % songs.length;
                loadSong(window.currentSongIndex);
                if (window.isPlaying) audioPlayer.play();
            }
        });
    }
    
    // Play all button
    if (playAllBtn) {
        playAllBtn.addEventListener('click', function() {
            currentSongIndex = 0;
            loadSong(currentSongIndex);
            togglePlayPause();
        });
    }
    
    // Shuffle all button
    if (shuffleAllBtn) {
        shuffleAllBtn.addEventListener('click', function() {
            shufflePlaylist();
            currentSongIndex = 0;
            loadSong(currentSongIndex);
            togglePlayPause();
        });
    }
    
    // Playlist toggle
    if (playlistToggle && playlistContainer) {
        playlistToggle.addEventListener('click', function() {
            playlistContainer.style.display = 
                playlistContainer.style.display === 'none' ? 'block' : 'none';
        });
    }
    
    // Progress bar
    audioPlayer.addEventListener('timeupdate', function() {
        if (progressBar) {
            const progress = (audioPlayer.currentTime / audioPlayer.duration) * 100;
            progressBar.style.setProperty('--progress', progress + '%');
        }
        
        if (currentTimeEl) {
            currentTimeEl.textContent = formatTime(audioPlayer.currentTime);
        }
        
        if (durationEl && audioPlayer.duration) {
            durationEl.textContent = formatTime(audioPlayer.duration);
        }
    });
    
    // Progress bar click
    const progressContainer = document.querySelector('.progress-container');
    if (progressContainer) {
        progressContainer.addEventListener('click', function(e) {
            const width = this.clientWidth;
            const clickX = e.offsetX;
            const duration = audioPlayer.duration;
            audioPlayer.currentTime = (clickX / width) * duration;
        });
    }
    
    // Volume control
    if (volumeSlider) {
        volumeSlider.addEventListener('input', function() {
            audioPlayer.volume = this.value / 100;
        });
    }
    
    // Auto play next song
    audioPlayer.addEventListener('ended', function() {
        if (window.isRepeating) {
            audioPlayer.currentTime = 0;
            audioPlayer.play();
        } else {
            if (window.currentAlbumSongs) {
                // Navigate within album (API-loaded songs)
                window.currentSongIndex = (window.currentSongIndex + 1) % window.currentAlbumSongs.length;
                const songData = window.currentAlbumSongs[window.currentSongIndex];
                const formattedSong = {
                    file_path: songData.file_path,
                    title: songData.title,
                    artist: songData.artist,
                    album: window.currentAlbumName
                };
                loadAlbumSongFromData(formattedSong);
                audioPlayer.play();
            } else {
                // Navigate within playlist
                const songs = window.isShuffled ? shuffledPlaylist : originalPlaylist;
                window.currentSongIndex = (window.currentSongIndex + 1) % songs.length;
                loadSong(window.currentSongIndex);
                audioPlayer.play();
            }
        }
    });
    
    // Search functionality - Initialize after DOM is ready
    function initializeSearch() {
        const searchInput = document.getElementById('playlistSearch');
        const clearSearchBtn = document.getElementById('clearSearch');
        const songItems = document.querySelectorAll('.song-item');
        
        console.log('Initializing search...'); // Debug
        console.log('Search input found:', searchInput); // Debug
        console.log('Clear button found:', clearSearchBtn); // Debug
        console.log('Song items found:', songItems.length); // Debug
        
        if (searchInput && clearSearchBtn) {
            // Remove existing listeners to prevent duplicates
            searchInput.removeEventListener('input', handleSearchInput);
            clearSearchBtn.removeEventListener('click', handleClearSearch);
            
            // Add new listeners
            searchInput.addEventListener('input', handleSearchInput);
            clearSearchBtn.addEventListener('click', handleClearSearch);
            
            console.log('Search event listeners attached'); // Debug
        } else {
            console.log('Search elements not found!'); // Debug
        }
    }
    
    // Handle search input
    function handleSearchInput(e) {
        const searchTerm = e.target.value.toLowerCase().trim();
        console.log('Search term:', searchTerm); // Debug log
        filterSongs(searchTerm);
    }
    
    // Handle clear search
    function handleClearSearch() {
        const searchInput = document.getElementById('playlistSearch');
        searchInput.value = '';
        console.log('Search cleared'); // Debug log
        filterSongs('');
    }
    
    function filterSongs(searchTerm) {
        const songItems = document.querySelectorAll('.song-item');
        let visibleCount = 0;
        
        console.log('Starting filter with term:', searchTerm); // Debug
        console.log('Total song items found:', songItems.length); // Debug
        
        if (searchTerm === '') {
            // Show all songs if search is empty
            console.log('Empty search - showing all songs'); // Debug
            songItems.forEach(item => {
                item.style.display = 'flex';
            });
            // Remove no results message
            const noResultsMsg = document.querySelector('.no-search-results');
            if (noResultsMsg) {
                noResultsMsg.remove();
            }
            return;
        }
        
        // Handle both comma and 'or' separators
        let searchTerms;
        if (searchTerm.toLowerCase().includes(' or ')) {
            // Split by 'or' for OR logic
            searchTerms = searchTerm.toLowerCase().split('or').map(term => term.trim()).filter(term => term !== '');
            console.log('Using OR logic with terms:', searchTerms); // Debug
        } else {
            // Split by comma for AND logic
            searchTerms = searchTerm.toLowerCase().split(',').map(term => term.trim()).filter(term => term !== '');
            console.log('Using AND logic with terms:', searchTerms); // Debug
        }
        
        console.log('Processing', searchTerms.length, 'search terms'); // Debug
        
        songItems.forEach((item, index) => {
            const title = item.querySelector('.song-title')?.textContent.toLowerCase() || '';
            const artist = item.querySelector('.song-artist')?.textContent.toLowerCase() || '';
            const genre = item.querySelector('.song-genre')?.textContent.toLowerCase() || '';
            const album = item.querySelector('.song-album')?.textContent.toLowerCase() || '';
            
            console.log(`Song ${index}:`, { title, artist, genre, album }); // Debug
            
            // Check if ANY search term matches ANY field
            const matches = searchTerms.some(term => 
                title.includes(term) || 
                artist.includes(term) || 
                genre.includes(term) || 
                album.includes(term)
            );
            
            console.log(`Song ${index} matches:`, matches); // Debug
            
            if (matches) {
                item.style.display = 'flex';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });
        
        console.log('Visible songs count:', visibleCount); // Debug
        
        // Show no results message if needed
        const playlist = document.querySelector('.playlist');
        let noResultsMsg = playlist.querySelector('.no-search-results');
        
        if (visibleCount === 0) {
            console.log('No matches found - showing no results message'); // Debug
            if (!noResultsMsg) {
                noResultsMsg = document.createElement('div');
                noResultsMsg.className = 'no-search-results';
                noResultsMsg.innerHTML = `<p>No songs found for "${searchTerm}"</p>`;
                playlist.appendChild(noResultsMsg);
            }
        } else if (noResultsMsg) {
            console.log('Removing no results message'); // Debug
            noResultsMsg.remove();
        }
    }
    
    // Initialize search functionality
    initializeSearch();
}

// Utility functions
function downloadSong(filePath) {
    window.open(filePath, '_blank');
}

function shareSong(songTitle) {
    if (navigator.share) {
        navigator.share({
            title: songTitle,
            text: `Check out ${songTitle} by ${APP_NAME}`,
            url: window.location.href
        });
    } else {
        // Fallback: copy to clipboard
        navigator.clipboard.writeText(window.location.href);
        showToast('Link copied to clipboard!', 'success');
    }
}

function addToFavorites(songId) {
    // Implement favorites functionality
    fetch('includes/favorites-handler.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ song_id: songId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Added to favorites!', 'success');
            // Update heart icon
            event.target.classList.remove('far');
            event.target.classList.add('fas');
        }
    })
    .catch(error => {
        showToast('Failed to add to favorites', 'error');
    });
}

// Play album functionality
function playAlbum(albumName) {
    // Get all songs from selected album
    fetch('includes/get-album-songs.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ album: albumName })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.songs.length > 0) {
            // Set current album songs
            window.currentAlbumSongs = data.songs;
            window.currentAlbumName = albumName;
            
            // Create song objects for the new loading function
            const songData = data.songs[0];
            const formattedSong = {
                file_path: songData.file_path,
                title: songData.title,
                artist: songData.artist,
                album: albumName
            };
            
            // Load first song from album
            window.currentSongIndex = 0;
            loadAlbumSongFromData(formattedSong);
            togglePlayPause();
            
            showToast(`Playing album: ${albumName}`, 'success');
        } else {
            showToast('No songs found in this album', 'error');
        }
    })
    .catch(error => {
        showToast('Failed to load album', 'error');
    });
}

// Load specific song from current album
function loadAlbumSong(index) {
    if (currentAlbumSongs && currentAlbumSongs[index]) {
        const song = currentAlbumSongs[index];
        
        // Update audio player
        const audioPlayer = document.getElementById('audioPlayer');
        audioPlayer.src = song.file_path;
        
        // Update now playing display
        document.getElementById('currentSongTitle').textContent = song.title;
        document.getElementById('currentSongArtist').textContent = song.artist;
        
        // Update active state in album songs
        document.querySelectorAll('.album-song-item').forEach((item, i) => {
            item.classList.toggle('active', i === index);
        });
    }
}

</script>

<!-- Hidden audio element -->
<audio id="audioPlayer" preload="metadata"></audio>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
