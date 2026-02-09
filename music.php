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
<section class="music-section" id="music">
    <div class="container">
        <div class="section-header">
            <div class="header-content">
                <h2 class="music-title">
                    <span class="title-gradient">Music</span>
                    <div class="title-underline"></div>
                </h2>
                <p class="music-subtitle">
                    <span class="subtitle-icon">🎵</span>
                    Complete discography and latest releases
                    <span class="subtitle-icon">🎧</span>
                </p>
            </div>
            <div class="header-decoration">
                <div class="decoration-circle decoration-1"></div>
                <div class="decoration-circle decoration-2"></div>
                <div class="decoration-circle decoration-3"></div>
            </div>
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
                                <button class="btn-icon" onclick="shareSong('<?php echo xss_clean($song['title']); ?>', event)" title="Share">
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
                                                    <button class="btn-icon" onclick="shareSong('<?php echo xss_clean($song['title']); ?>', event)" title="Share">
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
.music-section {
    padding: 5rem 0;
    background: var(--dark-bg);
    position: relative;
    overflow: hidden;
}

.music-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: 
        radial-gradient(circle at 20% 20%, rgba(255, 107, 107, 0.08) 0%, transparent 50%),
        radial-gradient(circle at 80% 80%, rgba(78, 205, 196, 0.08) 0%, transparent 50%),
        radial-gradient(circle at 50% 50%, rgba(69, 183, 209, 0.05) 0%, transparent 50%),
        repeating-linear-gradient(
            45deg,
            transparent,
            transparent 10px,
            rgba(255, 255, 255, 0.01) 10px,
            rgba(255, 255, 255, 0.01) 20px
        );
    pointer-events: none;
    z-index: 0;
    animation: backgroundShift 20s ease-in-out infinite;
}

.music-section::after {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: 
        radial-gradient(circle at 30% 70%, rgba(255, 107, 107, 0.03) 0%, transparent 40%),
        radial-gradient(circle at 70% 30%, rgba(78, 205, 196, 0.03) 0%, transparent 40%);
    animation: rotateBackground 60s linear infinite;
    pointer-events: none;
    z-index: 0;
}

@keyframes backgroundShift {
    0%, 100% { 
        background-position: 0% 0%, 100% 100%, 50% 50%, 0% 0%;
        opacity: 1;
    }
    50% { 
        background-position: 100% 100%, 0% 0%, 100% 0%, 20px 20px;
        opacity: 0.8;
    }
}

@keyframes rotateBackground {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.section-header {
    text-align: center;
    margin-bottom: 4rem;
    position: relative;
    z-index: 1;
}

.header-content {
    position: relative;
    z-index: 2;
}

.music-title {
    font-size: 3.5rem;
    font-weight: 900;
    margin-bottom: 1.5rem;
    position: relative;
    display: inline-block;
}

.title-gradient {
    background: linear-gradient(135deg, #ff6b6b, #4ecdc4, #45b7d1);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    background-size: 200% 200%;
    animation: gradientShift 4s ease-in-out infinite;
    position: relative;
}

@keyframes gradientShift {
    0%, 100% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
}

.title-underline {
    position: absolute;
    bottom: -10px;
    left: 50%;
    transform: translateX(-50%);
    width: 100px;
    height: 4px;
    background: linear-gradient(90deg, #ff6b6b, #4ecdc4, #45b7d1);
    border-radius: 2px;
    animation: underlineGlow 3s ease-in-out infinite;
}

@keyframes underlineGlow {
    0%, 100% { 
        box-shadow: 0 0 20px rgba(255, 107, 107, 0.5);
        width: 100px;
    }
    50% { 
        box-shadow: 0 0 30px rgba(78, 205, 196, 0.7);
        width: 150px;
    }
}

.music-subtitle {
    font-size: 1.2rem;
    color: var(--text-secondary);
    margin-bottom: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 1rem;
    font-weight: 400;
    line-height: 1.6;
}

.subtitle-icon {
    font-size: 1.5rem;
    animation: iconFloat 3s ease-in-out infinite;
}

.subtitle-icon:first-child {
    animation-delay: 0s;
}

.subtitle-icon:last-child {
    animation-delay: 1.5s;
}

@keyframes iconFloat {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-5px); }
}

.header-decoration {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 100%;
    height: 100%;
    pointer-events: none;
    z-index: 1;
}

.decoration-circle {
    position: absolute;
    border-radius: 50%;
    opacity: 0.1;
    animation: float 6s ease-in-out infinite;
}

.decoration-1 {
    width: 120px;
    height: 120px;
    background: linear-gradient(135deg, #ff6b6b, #4ecdc4);
    top: 20%;
    left: 10%;
    animation-delay: 0s;
}

.decoration-2 {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #4ecdc4, #45b7d1);
    top: 60%;
    right: 15%;
    animation-delay: 2s;
}

.decoration-3 {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #45b7d1, #ff6b6b);
    bottom: 20%;
    left: 20%;
    animation-delay: 4s;
}

@keyframes float {
    0%, 100% { 
        transform: translateY(0px) rotate(0deg);
        opacity: 0.1;
    }
    50% { 
        transform: translateY(-20px) rotate(180deg);
        opacity: 0.2;
    }
}
.music-player {
    background: 
        linear-gradient(145deg, var(--dark-secondary) 0%, var(--dark-tertiary) 100%),
        radial-gradient(circle at 30% 30%, rgba(255, 107, 107, 0.05) 0%, transparent 50%);
    border-radius: 25px;
    padding: 2.5rem;
    margin-bottom: 2rem;
    box-shadow: 
        0 20px 40px rgba(0, 0, 0, 0.4),
        0 5px 15px rgba(255, 107, 107, 0.1),
        inset 0 1px 0 rgba(255, 255, 255, 0.1);
    border: 2px solid rgba(255, 255, 255, 0.1);
    position: relative;
    overflow: hidden;
    backdrop-filter: blur(10px);
}

.music-player::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--primary-color), var(--secondary-color), var(--accent-color), var(--primary-color));
    background-size: 300% 100%;
    animation: shimmerGradient 4s linear infinite;
    z-index: 2;
}

@keyframes shimmerGradient {
    0% { background-position: -300% 0; }
    100% { background-position: 300% 0; }
}

.player-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    padding-bottom: 1.5rem;
    border-bottom: 2px solid rgba(255, 255, 255, 0.1);
    position: relative;
}

.player-header::after {
    content: '';
    position: absolute;
    bottom: -2px;
    left: 0;
    right: 0;
    height: 2px;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    background-size: 200% 100%;
    animation: playerHeaderShimmer 3s linear infinite;
}

@keyframes playerHeaderShimmer {
    0% { background-position: -200% 0; }
    100% { background-position: 200% 0; }
}

.now-playing h4 {
    margin: 0 0 0.5rem 0;
    color: var(--text-secondary);
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 1px;
    font-weight: 600;
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    background-size: 200% 200%;
    animation: textGradient 3s ease-in-out infinite;
}

@keyframes textGradient {
    0%, 100% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
}

.track-info {
    display: flex;
    flex-direction: column;
}

.track-info span:first-child {
    font-weight: 600;
    font-size: 1.2rem;
    color: var(--text-primary);
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
}

.track-info span:last-child {
    color: var(--text-secondary);
    font-size: 1rem;
    font-weight: 500;
}

.player-controls {
    display: flex;
    gap: 1rem;
    align-items: center;
}

.control-btn {
    background: linear-gradient(145deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0.05) 100%);
    border: 2px solid rgba(255, 255, 255, 0.2);
    color: var(--text-primary);
    font-size: 1.2rem;
    cursor: pointer;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    padding: 0.75rem;
    border-radius: 50%;
    width: 45px;
    height: 45px;
    display: flex;
    align-items: center;
    justify-content: center;
    backdrop-filter: blur(10px);
    position: relative;
    overflow: hidden;
}

.control-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    border-radius: 50%;
    opacity: 0;
    transition: opacity 0.3s ease;
    z-index: 0;
}

.control-btn:hover {
    transform: scale(1.1);
    border-color: var(--primary-color);
    box-shadow: 
        0 8px 25px rgba(255, 107, 107, 0.3),
        0 4px 15px rgba(78, 205, 196, 0.2);
}

.control-btn:hover::before {
    opacity: 1;
}

.control-btn.shuffle-active {
    color: #FF6B6B !important;
    background: rgba(255, 107, 107, 0.2) !important;
    border-color: #FF6B6B !important;
    box-shadow: 0 8px 25px rgba(255, 107, 107, 0.4);
}

.control-btn.repeat-active {
    color: #FF6B6B !important;
    background: rgba(255, 107, 107, 0.2) !important;
    border-color: #FF6B6B !important;
    box-shadow: 0 8px 25px rgba(255, 107, 107, 0.4);
}

.control-btn.play-pause {
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    color: var(--text-primary);
    font-size: 1.5rem;
    width: 55px;
    height: 55px;
    box-shadow: 
        0 8px 25px rgba(255, 107, 107, 0.4),
        0 4px 15px rgba(78, 205, 196, 0.3);
    position: relative;
    overflow: hidden;
}

.control-btn.play-pause::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    background-size: 200% 100%;
    animation: playPauseShimmer 2s linear infinite;
}

@keyframes playPauseShimmer {
    0% { background-position: -200% 0; }
    100% { background-position: 200% 0; }
}

.control-btn.play-pause:hover {
    transform: scale(1.15);
    box-shadow: 
        0 12px 35px rgba(255, 107, 107, 0.5),
        0 6px 20px rgba(78, 205, 196, 0.4);
}

.progress-section {
    margin-bottom: 2rem;
    position: relative;
}

.progress-container {
    position: relative;
    margin-bottom: 1.5rem;
    background: linear-gradient(145deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0.05) 100%);
    height: 10px;
    border-radius: 20px;
    overflow: hidden;
    cursor: pointer;
    backdrop-filter: blur(5px);
    border: 2px solid rgba(255, 255, 255, 0.2);
    box-shadow: 
        0 4px 15px rgba(0, 0, 0, 0.3),
        inset 0 1px 0 rgba(255, 255, 255, 0.1);
}

.progress-container::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(90deg, 
        transparent, 
        rgba(255, 107, 107, 0.2), 
        rgba(78, 205, 196, 0.2), 
        rgba(69, 183, 209, 0.2), 
        transparent);
    background-size: 200% 100%;
    animation: progressShimmer 3s linear infinite;
}

@keyframes progressShimmer {
    0% { background-position: -200% 0; }
    100% { background-position: 200% 0; }
}

.progress-bar {
    height: 6px;
    background: linear-gradient(145deg, var(--dark-tertiary) 0%, var(--dark-secondary) 100%);
    border-radius: 3px;
    position: relative;
    cursor: pointer;
    transition: all 0.3s ease;
}

.progress-bar::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    height: 100%;
    background: linear-gradient(90deg, var(--primary-color), #ff6b6b);
    border-radius: 3px;
    width: 0%;
    transition: width 0.1s linear;
    box-shadow: 0 0 10px rgba(255, 107, 107, 0.5);
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
    font-weight: 500;
    letter-spacing: 0.5px;
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
    gap: 0.75rem;
}

.volume-control i {
    color: var(--primary-color);
    font-size: 1.2rem;
    transition: all 0.3s ease;
}

.volume-control:hover i {
    color: var(--secondary-color);
    transform: scale(1.1);
}

.volume-control input[type="range"] {
    width: 120px;
    height: 8px;
    background: linear-gradient(145deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0.05) 100%);
    outline: none;
    border: 2px solid rgba(255, 255, 255, 0.2);
    border-radius: 15px;
    -webkit-appearance: none;
    appearance: none;
    backdrop-filter: blur(5px);
    box-shadow: 
        0 2px 8px rgba(0, 0, 0, 0.3),
        inset 0 1px 0 rgba(255, 255, 255, 0.1);
}

.volume-control input[type="range"]::-webkit-slider-thumb {
    -webkit-appearance: none;
    appearance: none;
    width: 20px;
    height: 20px;
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    border-radius: 50%;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(255, 107, 107, 0.4);
    transition: all 0.3s ease;
}

.volume-control input[type="range"]::-moz-range-thumb {
    width: 20px;
    height: 20px;
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    border-radius: 50%;
    cursor: pointer;
    border: none;
    box-shadow: 0 4px 12px rgba(255, 107, 107, 0.4);
}

.volume-control input[type="range"]:hover::-webkit-slider-thumb {
    transform: scale(1.2);
    box-shadow: 0 6px 20px rgba(255, 107, 107, 0.6);
}

.volume-control input[type="range"]:hover::-moz-range-thumb {
    transform: scale(1.2);
    box-shadow: 0 6px 20px rgba(255, 107, 107, 0.6);
}

.player-actions {
    display: flex;
    gap: 1rem;
}

.player-actions .control-btn {
    background: linear-gradient(145deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0.05) 100%);
    border: 2px solid rgba(255, 255, 255, 0.2);
    color: var(--text-primary);
    font-size: 1rem;
    padding: 0.75rem 1.5rem;
    border-radius: 25px;
    backdrop-filter: blur(10px);
    text-transform: uppercase;
    letter-spacing: 1px;
    font-weight: 600;
    position: relative;
    overflow: hidden;
}

.player-actions .control-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    border-radius: 25px;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.player-actions .control-btn:hover {
    transform: translateY(-2px) scale(1.05);
    border-color: var(--primary-color);
    box-shadow: 
        0 8px 25px rgba(255, 107, 107, 0.3),
        0 4px 15px rgba(78, 205, 196, 0.2);
}

.player-actions .control-btn:hover::before {
    opacity: 1;
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
    background: 
        linear-gradient(145deg, rgba(255, 255, 255, 0.02) 0%, rgba(255, 255, 255, 0.01) 100%),
        radial-gradient(circle at 50% 50%, rgba(255, 107, 107, 0.03) 0%, transparent 50%);
    border-radius: 20px;
    border: 1px solid rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(10px);
    padding: 1.5rem;
    box-shadow: 
        0 10px 30px rgba(0, 0, 0, 0.3),
        inset 0 1px 0 rgba(255, 255, 255, 0.1);
}

.playlist-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid rgba(255, 255, 255, 0.1);
    position: relative;
}

.playlist-header::after {
    content: '';
    position: absolute;
    bottom: -2px;
    left: 0;
    right: 0;
    height: 2px;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    background-size: 200% 100%;
    animation: playlistHeaderShimmer 3s linear infinite;
}

@keyframes playlistHeaderShimmer {
    0% { background-position: -200% 0; }
    100% { background-position: 200% 0; }
}

.playlist-controls {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid rgba(255, 255, 255, 0.1);
    gap: 1rem;
    flex-wrap: wrap;
}

.search-container {
    display: flex;
    gap: 0.75rem;
    align-items: center;
    flex: 1;
    max-width: 350px;
}

.search-input {
    flex: 1;
    padding: 0.75rem 1.5rem;
    background: linear-gradient(145deg, rgba(255, 255, 255, 0.05) 0%, rgba(255, 255, 255, 0.02) 100%);
    border: 2px solid rgba(255, 255, 255, 0.2);
    border-radius: 20px;
    color: var(--text-primary);
    font-size: 0.9rem;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    outline: none;
    backdrop-filter: blur(5px);
    position: relative;
}

.search-input::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    border-radius: 20px;
    padding: 2px;
    background: linear-gradient(90deg, var(--primary-color), var(--secondary-color), var(--accent-color), var(--primary-color));
    background-size: 300% 100%;
    animation: searchShimmer 4s linear infinite;
    -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
    mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
    -webkit-mask-composite: xor;
    mask-composite: exclude;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.search-input:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 
        0 8px 25px rgba(255, 107, 107, 0.3),
        0 4px 15px rgba(78, 205, 196, 0.2);
    backdrop-filter: blur(10px);
}

.search-input:focus::before {
    opacity: 1;
}

.search-input::placeholder {
    color: var(--text-muted);
    font-weight: 400;
    font-style: italic;
}

.playlist-actions {
    display: flex;
    gap: 0.75rem;
    align-items: center;
}

.btn-sm {
    padding: 0.5rem 1rem;
    font-size: 0.9rem;
    border-radius: 15px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 600;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.btn.secondary {
    background: linear-gradient(145deg, rgba(255, 255, 255, 0.05) 0%, rgba(255, 255, 255, 0.02) 100%);
    color: var(--text-primary);
    border: 2px solid rgba(255, 255, 255, 0.2);
}

.btn-sm::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    border-radius: 15px;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.btn-sm:hover {
    transform: translateY(-2px) scale(1.05);
    box-shadow: 0 8px 20px rgba(255, 107, 107, 0.3);
}

.btn-sm:hover::before {
    opacity: 1;
}

.song-item {
    display: grid;
    grid-template-columns: 40px 60px 1fr auto 120px;
    align-items: center;
    padding: 1rem;
    border-bottom: 2px solid rgba(255, 255, 255, 0.1);
    cursor: pointer;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    gap: 1rem;
    position: relative;
    background: linear-gradient(145deg, rgba(255, 255, 255, 0.02) 0%, rgba(255, 255, 255, 0.01) 100%);
}

.song-item::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
    background-size: 200% 100%;
    animation: songItemShimmer 6s linear infinite;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.song-item:hover::before {
    opacity: 1;
}

@keyframes songItemShimmer {
    0% { background-position: -200% 0; }
    100% { background-position: 200% 0; }
}

.song-item:hover {
    background: linear-gradient(145deg, rgba(255, 255, 255, 0.08) 0%, rgba(255, 255, 255, 0.04) 100%);
    transform: translateY(-3px) scale(1.02);
    box-shadow: 
        0 15px 35px rgba(255, 107, 107, 0.2),
        0 5px 15px rgba(78, 205, 196, 0.1);
}

.song-item.active {
    background: linear-gradient(145deg, rgba(255, 107, 107, 0.15) 0%, rgba(255, 107, 107, 0.08) 100%);
    border-left: 4px solid var(--primary-color);
    box-shadow: 
        0 10px 25px rgba(255, 107, 107, 0.3),
        0 5px 15px rgba(78, 205, 196, 0.2);
    transform: translateX(3px);
}

.song-number {
    color: var(--text-muted);
    font-size: 0.9rem;
    text-align: center;
    font-weight: 600;
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    background-size: 200% 200%;
    animation: numberGradient 3s ease-in-out infinite;
}

@keyframes numberGradient {
    0%, 100% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
}

.song-cover {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    object-fit: cover;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    transition: all 0.3s ease;
}

.song-item:hover .song-cover {
    transform: scale(1.1) rotate(5deg);
    box-shadow: 0 8px 20px rgba(255, 107, 107, 0.4);
}

.song-info {
    flex: 1;
}

.song-info .song-title {
    color: var(--text-primary);
    font-weight: 600;
    margin-bottom: 0.25rem;
    font-size: 1rem;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
}

.song-info .song-artist {
    color: var(--text-secondary);
    font-size: 0.9rem;
    margin-bottom: 0.25rem;
    font-style: italic;
}

.song-info .song-genre {
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    color: var(--text-primary);
    padding: 0.2rem 0.5rem;
    border-radius: 10px;
    font-size: 0.75rem;
    display: inline-block;
    margin-right: 0.5rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    box-shadow: 0 2px 8px rgba(255, 107, 107, 0.3);
}

.song-meta {
    text-align: right;
    color: var(--text-muted);
    font-size: 0.9rem;
    font-weight: 500;
}

.song-actions {
    display: flex;
    gap: 0.5rem;
    justify-content: center;
}

.btn-icon {
    background: linear-gradient(145deg, rgba(255, 255, 255, 0.05) 0%, rgba(255, 255, 255, 0.02) 100%);
    border: none;
    color: var(--text-muted);
    cursor: pointer;
    padding: 0.5rem;
    border-radius: 50%;
    transition: all 0.3s ease;
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    backdrop-filter: blur(5px);
    position: relative;
}

.btn-icon::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    border-radius: 50%;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.btn-icon:hover {
    background: linear-gradient(135deg, var(--dark-tertiary), var(--dark-secondary));
    color: var(--primary-color);
    transform: scale(1.1);
    box-shadow: 0 4px 12px rgba(255, 107, 107, 0.3);
}

.btn-icon:hover::before {
    opacity: 1;
}

.albums-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 2.5rem;
    position: relative;
    z-index: 1;
}

.albums-container {
    display: flex;
    flex-direction: column;
    gap: 3rem;
}

.album-item {
    background: 
        linear-gradient(145deg, var(--dark-secondary) 0%, var(--dark-tertiary) 100%),
        radial-gradient(circle at 30% 30%, rgba(255, 107, 107, 0.05) 0%, transparent 50%);
    border-radius: 25px;
    overflow: hidden;
    transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 
        0 15px 35px rgba(0, 0, 0, 0.4),
        0 5px 15px rgba(255, 107, 107, 0.1),
        inset 0 1px 0 rgba(255, 255, 255, 0.1);
    border: 2px solid rgba(255, 255, 255, 0.1);
    position: relative;
}

.album-item::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--primary-color), var(--secondary-color), var(--accent-color), var(--primary-color));
    background-size: 300% 100%;
    animation: albumShimmer 4s linear infinite;
    z-index: 2;
}

@keyframes albumShimmer {
    0% { background-position: -300% 0; }
    100% { background-position: 300% 0; }
}

.album-item:hover {
    transform: translateY(-10px) scale(1.02);
    box-shadow: 
        0 25px 50px rgba(255, 107, 107, 0.2),
        0 8px 25px rgba(78, 205, 196, 0.1),
        border-color: rgba(255, 107, 107, 0.3);
}

.album-item .album-header {
    display: flex;
    align-items: center;
    padding: 2rem;
    border-bottom: 2px solid rgba(255, 255, 255, 0.1);
    gap: 1.5rem;
    position: relative;
}

.album-item .album-header::after {
    content: '';
    position: absolute;
    bottom: -2px;
    left: 0;
    right: 0;
    height: 2px;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    background-size: 200% 100%;
    animation: albumHeaderShimmer 3s linear infinite;
}

@keyframes albumHeaderShimmer {
    0% { background-position: -200% 0; }
    100% { background-position: 200% 0; }
}

.album-item .album-cover {
    width: 100px;
    height: 100px;
    border-radius: 15px;
    object-fit: cover;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
    transition: all 0.3s ease;
    flex-shrink: 0;
}

.album-item:hover .album-cover {
    transform: scale(1.05) rotate(3deg);
    box-shadow: 0 12px 30px rgba(255, 107, 107, 0.4);
}

.album-item .album-info {
    flex: 1;
    text-align: left;
}

.album-item .album-info h4 {
    margin-bottom: 0.75rem;
    color: var(--text-primary);
    font-size: 1.3rem;
    font-weight: 700;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    background: linear-gradient(135deg, #ffffff, #e0e0e0);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    background-size: 200% 200%;
    animation: albumTitleGradient 3s ease-in-out infinite;
}

@keyframes albumTitleGradient {
    0%, 100% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
}

.album-item .album-info p {
    color: var(--text-secondary);
    margin-bottom: 1rem;
    font-size: 1rem;
    line-height: 1.5;
}

.album-item .album-songs {
    padding: 1.5rem 2rem;
    max-height: 300px;
    overflow-y: auto;
    background: 
        linear-gradient(145deg, rgba(255, 255, 255, 0.02) 0%, rgba(255, 255, 255, 0.01) 100%);
    border-radius: 15px;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.album-item .album-songs h5 {
    color: var(--text-primary);
    margin-bottom: 1rem;
    font-size: 1.1rem;
    font-weight: 600;
    background: linear-gradient(135deg, #ffffff, #e0e0e0);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    background-size: 200% 200%;
    animation: albumTitleGradient 3s ease-in-out infinite;
}

.album-item .songs-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.album-item .album-song-item {
    display: flex;
    align-items: center;
    padding: 0.75rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    cursor: pointer;
    transition: all 0.3s ease;
    gap: 0.75rem;
    position: relative;
}

.album-item .album-song-item::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
    background-size: 200% 100%;
    animation: albumSongShimmer 6s linear infinite;
    opacity: 0;
    transition: opacity 0.3s ease;
}

@keyframes albumSongShimmer {
    0% { background-position: -200% 0; }
    100% { background-position: 200% 0; }
}

.album-item .album-song-item:hover {
    background: linear-gradient(145deg, rgba(255, 255, 255, 0.08) 0%, rgba(255, 255, 255, 0.04) 100%);
    transform: translateY(-2px) scale(1.02);
}

.album-item .album-song-item.active {
    background: linear-gradient(145deg, rgba(255, 107, 107, 0.15) 0%, rgba(255, 107, 107, 0.08) 100%);
    border-left: 4px solid var(--primary-color);
    box-shadow: 
        0 10px 25px rgba(255, 107, 107, 0.3),
        0 5px 15px rgba(78, 205, 196, 0.1);
    transform: translateX(3px);
}

.album-item .album-song-item.active .song-title {
    color: var(--primary-color);
    font-weight: 600;
}

.album-item .album-song-item.active .song-number {
    color: var(--primary-color);
    font-weight: 600;
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
    .music-title {
        font-size: 2.5rem;
    }
    
    .music-subtitle {
        font-size: 1rem;
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .subtitle-icon {
        font-size: 1.2rem;
    }
    
    .music-player {
        padding: 1.5rem;
        margin-bottom: 1.5rem;
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
    
    .control-btn {
        width: 40px;
        height: 40px;
        font-size: 1rem;
    }
    
    .control-btn.play-pause {
        width: 45px;
        height: 45px;
        font-size: 1.2rem;
    }
    
    .volume-control {
        width: 100%;
        justify-content: center;
    }
    
    .volume-control input[type="range"] {
        width: 150px;
    }
    
    .playlist-header {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }
    
    .playlist-controls {
        flex-direction: column;
        gap: 1rem;
    }
    
    .search-container {
        max-width: 100%;
    }
    
    .search-input {
        font-size: 0.9rem;
    }
    
    .song-item {
        grid-template-columns: 30px 50px 1fr;
        gap: 0.5rem;
        padding: 0.75rem;
    }
    
    .song-meta,
    .song-actions {
        display: none;
    }
    
    .song-cover {
        width: 50px;
        height: 50px;
    }
    
    .albums-grid {
        grid-template-columns: repeat(2, 1fr);  
        gap: 1.5rem;
    }
    
    .album-item .album-header {
        flex-direction: column;
        text-align: center;
        gap: 1rem;
        padding: 1rem;
    }
    
    .album-item .album-cover {
        width: 80px;
        height: 80px;
    }
    
    .album-item .album-songs {
        max-height: 200px;
        padding: 1rem;
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

@media (max-width: 480px) {
    .music-title {
        font-size: 2rem;
    }
    
    .music-subtitle {
        font-size: 0.9rem;
    }
    
    .music-player {
        padding: 1rem;
    }
    
    .control-btn {
        width: 35px;
        height: 35px;
        font-size: 0.9rem;
    }
    
    .control-btn.play-pause {
        width: 40px;
        height: 40px;
        font-size: 1.1rem;
    }
    
    .volume-control input[type="range"] {
        width: 120px;
    }
    
    .song-item {
        grid-template-columns: 25px 40px 1fr;
        gap: 0.5rem;
        padding: 0.5rem;
    }
    
    .song-cover {
        width: 40px;
        height: 40px;
    }
    
    .albums-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .album-item .album-cover {
        width: 60px;
        height: 60px;
    }
    
    .album-item .album-songs {
        max-height: 150px;
        padding: 0.75rem;
    }
    
    .album-item .album-song-item {
        padding: 0.5rem;
        gap: 0.5rem;
    }
    
    .album-item .album-song-item .song-number {
        width: 18px;
        font-size: 0.6rem;
    }
    
    .album-item .album-song-item .song-title {
        font-size: 0.7rem;
    }
    
    .album-item .album-song-item .song-meta {
        font-size: 0.6rem;
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
    
    // Store songItems globally for use in other functions
    window.songItems = songItems;
    
    console.log('Found songItems:', songItems.length); // Debug
    console.log('Found playAllBtn:', playAllBtn); // Debug
    
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
        console.log('shufflePlaylist called, isShuffled:', window.isShuffled); // Debug
        
        if (!window.isShuffled) {
            console.log('Enabling shuffle...'); // Debug
            
            // Enable shuffle
            if (window.currentAlbumSongs) {
                console.log('Shuffling album songs...'); // Debug
                // Shuffle album songs
                const shuffledSongs = [...window.currentAlbumSongs];
                for (let i = shuffledSongs.length - 1; i > 0; i--) {
                    const j = Math.floor(Math.random() * (i + 1));
                    [shuffledSongs[i], shuffledSongs[j]] = [shuffledSongs[j], shuffledSongs[i]];
                }
                window.currentAlbumSongs = shuffledSongs;
                console.log('Album songs shuffled:', shuffledSongs.map(s => s.title)); // Debug
                
                // Visually shuffle album song items
                visuallyShuffleAlbumSongs(shuffledSongs);
            } else {
                console.log('Shuffling regular playlist...'); // Debug
                // Shuffle regular playlist
                const shuffledSongs = [...originalPlaylist];
                for (let i = shuffledSongs.length - 1; i > 0; i--) {
                    const j = Math.floor(Math.random() * (i + 1));
                    [shuffledSongs[i], shuffledSongs[j]] = [shuffledSongs[j], shuffledSongs[i]];
                }
                shuffledPlaylist = shuffledSongs;
                console.log('Playlist shuffled:', shuffledSongs.map(s => s.querySelector('.song-title').textContent)); // Debug
                
                // Visually shuffle playlist items
                visuallyShufflePlaylist(shuffledSongs);
            }
            window.isShuffled = true;
            shuffleBtn.classList.add('shuffle-active');
            showToast('Shuffle enabled', 'info');
            
            console.log('Shuffle enabled, isShuffled:', window.isShuffled); // Debug
            
            // If currently playing, update to shuffled position
            if (window.isPlaying) {
                const currentSongTitle = document.getElementById('currentSongTitle').textContent;
                if (window.currentAlbumSongs) {
                    const newIndex = window.currentAlbumSongs.findIndex(s => s.title === currentSongTitle);
                    if (newIndex !== -1) {
                        window.currentSongIndex = newIndex;
                    }
                } else {
                    const currentSong = Array.from(songItems).find(item => 
                        item.querySelector('.song-title').textContent === currentSongTitle
                    );
                    if (currentSong) {
                        window.currentSongIndex = shuffledPlaylist.indexOf(currentSong);
                    }
                }
            }
        } else {
            console.log('Disabling shuffle...'); // Debug
            
            // Disable shuffle
            if (window.currentAlbumSongs) {
                // Restore original album order
                window.currentAlbumSongs.sort((a, b) => a.title.localeCompare(b.title));
                console.log('Album songs restored to original order'); // Debug
                
                // Visually restore album song order
                visuallyRestoreAlbumSongs();
            } else {
                // Restore original playlist
                shuffledPlaylist = [...originalPlaylist];
                console.log('Playlist restored to original order'); // Debug
                
                // Visually restore playlist order
                visuallyRestorePlaylist();
            }
            window.isShuffled = false;
            shuffleBtn.classList.remove('shuffle-active');
            showToast('Shuffle disabled', 'info');
            
            console.log('Shuffle disabled, isShuffled:', window.isShuffled); // Debug
            
            // If currently playing, update to original position
            if (window.isPlaying) {
                const currentSongTitle = document.getElementById('currentSongTitle').textContent;
                if (window.currentAlbumSongs) {
                    const newIndex = window.currentAlbumSongs.findIndex(s => s.title === currentSongTitle);
                    if (newIndex !== -1) {
                        window.currentSongIndex = newIndex;
                    }
                } else {
                    const currentSong = Array.from(songItems).find(item => 
                        item.querySelector('.song-title').textContent === currentSongTitle
                    );
                    if (currentSong) {
                        window.currentSongIndex = originalPlaylist.indexOf(currentSong);
                    }
                }
            }
        }
    }
    
    // Visually shuffle playlist items
    function visuallyShufflePlaylist(shuffledSongs) {
        console.log('visuallyShufflePlaylist called with:', shuffledSongs.length, 'songs'); // Debug
        
        const playlistContainer = document.querySelector('.playlist');
        const playlist = playlistContainer.querySelector('.playlist');
        
        if (!playlist) {
            console.error('Playlist element not found'); // Debug
            return;
        }
        
        console.log('Current playlist children before shuffle:', playlist.children.length); // Debug
        
        // Create a document fragment to hold reordered items
        const fragment = document.createDocumentFragment();
        
        // Add shuffled items to fragment
        shuffledSongs.forEach((songItem, index) => {
            console.log(`Adding song ${index}:`, songItem.querySelector('.song-title')?.textContent); // Debug
            const clonedItem = songItem.cloneNode(true);
            fragment.appendChild(clonedItem);
        });
        
        console.log('Fragment children count:', fragment.children.length); // Debug
        
        // Clear and re-append
        playlist.innerHTML = '';
        playlist.appendChild(fragment);
        
        console.log('Playlist children after shuffle:', playlist.children.length); // Debug
        
        // Re-attach event listeners to new elements
        reattachPlaylistEventListeners();
        
        // Update songItems reference
        window.songItems = document.querySelectorAll('.song-item');
        console.log('Updated songItems count:', window.songItems.length); // Debug
    }
    
    // Visually restore playlist order
    function visuallyRestorePlaylist() {
        const playlistContainer = document.querySelector('.playlist');
        const playlist = playlistContainer.querySelector('.playlist');
        
        // Create a document fragment to hold reordered items
        const fragment = document.createDocumentFragment();
        
        // Add original items to fragment
        originalPlaylist.forEach(songItem => {
            const clonedItem = songItem.cloneNode(true);
            fragment.appendChild(clonedItem);
        });
        
        // Clear and re-append
        playlist.innerHTML = '';
        playlist.appendChild(fragment);
        
        // Re-attach event listeners to new elements
        reattachPlaylistEventListeners();
        
        // Update songItems reference
        window.songItems = document.querySelectorAll('.song-item');
    }
    
    // Visually shuffle album songs
    function visuallyShuffleAlbumSongs(shuffledSongs) {
        const allAlbumContainers = document.querySelectorAll('.album-item');
        
        allAlbumContainers.forEach(albumContainer => {
            const songsList = albumContainer.querySelector('.songs-list');
            const albumName = albumContainer.querySelector('h4').textContent.trim();
            
            if (window.currentAlbumName === albumName) {
                // Create a document fragment to hold reordered items
                const fragment = document.createDocumentFragment();
                
                // Add shuffled items to fragment
                shuffledSongs.forEach(songData => {
                    const songItem = songsList.querySelector(`[data-audio*="${songData.file_path}"]`);
                    if (songItem) {
                        const clonedItem = songItem.cloneNode(true);
                        fragment.appendChild(clonedItem);
                    }
                });
                
                // Clear and re-append
                songsList.innerHTML = '';
                songsList.appendChild(fragment);
                
                // Re-attach event listeners to new elements
                reattachAlbumSongEventListeners();
            }
        });
    }
    
    // Visually restore album songs
    function visuallyRestoreAlbumSongs() {
        const albumContainer = document.querySelector('.album-item');
        const songsList = albumContainer.querySelector('.songs-list');
        const albumName = window.currentAlbumName;
        
        if (albumName) {
            // Re-fetch album songs in original order
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
                    const fragment = document.createDocumentFragment();
                    
                    data.songs.forEach(songData => {
                        const songItem = createAlbumSongItem(songData);
                        fragment.appendChild(songItem);
                    });
                    
                    songsList.innerHTML = '';
                    songsList.appendChild(fragment);
                    
                    // Re-attach event listeners
                    reattachAlbumSongEventListeners();
                }
            })
            .catch(error => {
                console.error('Error restoring album songs:', error);
            });
        }
    }
    
    // Re-attach playlist event listeners
    function reattachPlaylistEventListeners() {
        const playlistSongItems = document.querySelectorAll('.song-item');
        
        playlistSongItems.forEach((item, index) => {
            item.addEventListener('click', function(e) {
                if (e.target.closest('.song-actions')) return;
                
                // Get song data from attributes
                const audioSrc = item.dataset.audio;
                const title = item.querySelector('.song-title').textContent;
                const artist = item.querySelector('.song-artist').textContent;
                const album = item.querySelector('.song-album')?.textContent || '';
                const songIndex = parseInt(item.dataset.index);
                
                // Create song object for album context
                const songData = {
                    file_path: audioSrc,
                    title: title,
                    artist: artist,
                    album: album
                };
                
                // Set current album context
                window.currentAlbumSongs = Array.from(playlistSongItems).map(songItem => ({
                    file_path: songItem.dataset.audio,
                    title: songItem.querySelector('.song-title').textContent,
                    artist: songItem.querySelector('.song-artist').textContent,
                    album: songItem.querySelector('.song-album')?.textContent || ''
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
    }
    
    // Re-attach album song event listeners
    function reattachAlbumSongEventListeners() {
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
                // Navigate within album (shuffled or original)
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
                // Navigate within playlist (shuffled or original)
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
                // Navigate within album (shuffled or original)
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
                // Navigate within playlist (shuffled or original)
                const songs = window.isShuffled ? shuffledPlaylist : originalPlaylist;
                window.currentSongIndex = (window.currentSongIndex + 1) % songs.length;
                loadSong(window.currentSongIndex);
                if (window.isPlaying) audioPlayer.play();
            }
        });
    }
    
    // Play all button
    if (playAllBtn) {
        console.log('Play All button found:', playAllBtn); // Debug
        playAllBtn.addEventListener('click', function() {
            console.log('Play All button clicked'); // Debug
            playAllSongs();
        });
    } else {
        console.log('Play All button not found!'); // Debug
    }
    
    // Play all songs sequentially
    function playAllSongs() {
        console.log('Play All button clicked'); // Debug
        
        // Check if songItems is available
        if (!window.songItems) {
            window.songItems = document.querySelectorAll('.song-item');
            console.log('songItems defined:', window.songItems.length, 'items'); // Debug
        }
        
        // Get all songs from current context
        let allSongs = [];
        let songContext = 'playlist';
        
        if (window.currentAlbumSongs && window.currentAlbumSongs.length > 0) {
            // Use album songs if available
            allSongs = window.currentAlbumSongs.map(songData => ({
                file_path: songData.file_path,
                title: songData.title,
                artist: songData.artist,
                album: window.currentAlbumName
            }));
            songContext = 'album';
            console.log('Using album songs:', allSongs.length); // Debug
        } else {
            // Use playlist songs
            allSongs = Array.from(window.songItems).map(songItem => ({
                file_path: songItem.dataset.audio,
                title: songItem.querySelector('.song-title').textContent,
                artist: songItem.querySelector('.song-artist').textContent,
                album: songItem.querySelector('.song-album')?.textContent || ''
            }));
            songContext = 'playlist';
            console.log('Using playlist songs:', allSongs.length); // Debug
        }
        
        if (allSongs.length === 0) {
            showToast('No songs available to play', 'error');
            return;
        }
        
        console.log('All songs collected:', allSongs.length); // Debug
        
        // Set current context
        if (songContext === 'album') {
            window.currentAlbumSongs = allSongs;
            window.currentAlbumName = allSongs[0].album;
        } else {
            window.currentAlbumSongs = null;
            window.currentAlbumName = null;
            shuffledPlaylist = [...allSongs];
            originalPlaylist = [...allSongs];
        }
        
        // Start playing from first song
        window.currentSongIndex = 0;
        window.isShuffled = false;
        shuffleBtn.classList.remove('shuffle-active');
        
        console.log('Starting playback from index 0'); // Debug
        
        // Load and play first song
        if (songContext === 'album') {
            const songData = allSongs[0];
            const formattedSong = {
                file_path: songData.file_path,
                title: songData.title,
                artist: songData.artist,
                album: songData.album
            };
            loadAlbumSongFromData(formattedSong);
        } else {
            loadSong(0);
        }
        
        // Start playing
        togglePlayPause();
        
        // Show notification
        showToast(`All ${allSongs.length} songs are playing`, 'success');
        
        console.log('Started playing all songs from index 0'); // Debug
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
                // Navigate within album (shuffled or original)
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
                // Navigate within playlist (shuffled or original)
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

function shareSong(songTitle, event) {
    console.log('Share button clicked for:', songTitle); // Debug
    
    // Prevent music playback when clicking share
    if (event) {
        event.stopPropagation();
        event.preventDefault();
    }
    
    // Show social media share modal
    showShareModal(songTitle);
}

function showShareModal(songTitle) {
    console.log('showShareModal called with:', songTitle); // Debug
    
    // Remove existing modal if any
    const existingModal = document.querySelector('.share-modal');
    if (existingModal) {
        existingModal.remove();
    }
    
    // Create modal overlay
    const modalOverlay = document.createElement('div');
    modalOverlay.className = 'share-modal-overlay';
    modalOverlay.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.7);
        z-index: 10000;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease;
    `;
    
    // Create modal content
    const modalContent = document.createElement('div');
    modalContent.className = 'share-modal-content';
    modalContent.style.cssText = `
        background: var(--dark-secondary);
        border-radius: 15px;
        padding: 2rem;
        max-width: 400px;
        width: 90%;
        text-align: center;
        transform: scale(0.9);
        transition: transform 0.3s ease;
        border: 1px solid rgba(255, 255, 255, 0.1);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
    `;
    
    // Modal header
    const modalHeader = document.createElement('div');
    modalHeader.innerHTML = `
        <h3 style="color: var(--text-primary); margin-bottom: 1rem;">Share Song</h3>
        <p style="color: var(--text-secondary); margin-bottom: 1.5rem;">Share "${songTitle}" on social media</p>
    `;
    
    // Social media buttons
    const socialButtons = document.createElement('div');
    socialButtons.style.cssText = `
        display: flex;
        justify-content: center;
        gap: 1rem;
        margin-bottom: 1.5rem;
    `;
    
    // Share URL
    const shareUrl = window.location.href;
    const shareText = `Check out ${songTitle} by ${typeof APP_NAME !== 'undefined' ? APP_NAME : 'Music Player'}`;
    
    // Facebook button
    const facebookBtn = document.createElement('button');
    facebookBtn.innerHTML = '<i class="fab fa-facebook-f"></i>';
    facebookBtn.style.cssText = `
        background: #1877F2;
        color: white;
        border: none;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        font-size: 1.2rem;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
    `;
    facebookBtn.onclick = () => {
        window.open(`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(shareUrl)}&quote=${encodeURIComponent(shareText)}`, '_blank');
        closeModal();
    };
    facebookBtn.onmouseover = () => facebookBtn.style.transform = 'scale(1.1)';
    facebookBtn.onmouseout = () => facebookBtn.style.transform = 'scale(1)';
    
    // WhatsApp button
    const whatsappBtn = document.createElement('button');
    whatsappBtn.innerHTML = '<i class="fab fa-whatsapp"></i>';
    whatsappBtn.style.cssText = `
        background: #25D366;
        color: white;
        border: none;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        font-size: 1.2rem;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
    `;
    whatsappBtn.onclick = () => {
        window.open(`https://wa.me/?text=${encodeURIComponent(shareText + ' ' + shareUrl)}`, '_blank');
        closeModal();
    };
    whatsappBtn.onmouseover = () => whatsappBtn.style.transform = 'scale(1.1)';
    whatsappBtn.onmouseout = () => whatsappBtn.style.transform = 'scale(1)';
    
    // Messenger button
    const messengerBtn = document.createElement('button');
    messengerBtn.innerHTML = '<i class="fab fa-facebook-messenger"></i>';
    messengerBtn.style.cssText = `
        background: #0084FF;
        color: white;
        border: none;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        font-size: 1.2rem;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
    `;
    messengerBtn.onclick = () => {
        window.open(`https://www.facebook.com/dialog/send?link=${encodeURIComponent(shareUrl)}&app_id=YOUR_APP_ID`, '_blank');
        closeModal();
    };
    messengerBtn.onmouseover = () => messengerBtn.style.transform = 'scale(1.1)';
    messengerBtn.onmouseout = () => messengerBtn.style.transform = 'scale(1)';
    
    // Copy link button
    const copyBtn = document.createElement('button');
    copyBtn.innerHTML = '<i class="fas fa-link"></i>';
    copyBtn.style.cssText = `
        background: var(--primary-color);
        color: white;
        border: none;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        font-size: 1.2rem;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
    `;
    copyBtn.onclick = () => {
        copyToClipboard(shareUrl);
        closeModal();
    };
    copyBtn.onmouseover = () => copyBtn.style.transform = 'scale(1.1)';
    copyBtn.onmouseout = () => copyBtn.style.transform = 'scale(1)';
    
    // Close button
    const closeBtn = document.createElement('button');
    closeBtn.innerHTML = 'Close';
    closeBtn.style.cssText = `
        background: transparent;
        color: var(--text-secondary);
        border: 1px solid var(--border-color);
        padding: 0.5rem 1.5rem;
        border-radius: 20px;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 0.9rem;
    `;
    closeBtn.onclick = closeModal;
    closeBtn.onmouseover = () => {
        closeBtn.style.background = 'var(--dark-tertiary)';
        closeBtn.style.color = 'var(--text-primary)';
    };
    closeBtn.onmouseout = () => {
        closeBtn.style.background = 'transparent';
        closeBtn.style.color = 'var(--text-secondary)';
    };
    
    // Assemble modal
    socialButtons.appendChild(facebookBtn);
    socialButtons.appendChild(whatsappBtn);
    socialButtons.appendChild(messengerBtn);
    socialButtons.appendChild(copyBtn);
    
    modalContent.appendChild(modalHeader);
    modalContent.appendChild(socialButtons);
    modalContent.appendChild(closeBtn);
    
    modalOverlay.appendChild(modalContent);
    document.body.appendChild(modalOverlay);
    
    // Close modal function
    function closeModal() {
        modalOverlay.style.opacity = '0';
        modalContent.style.transform = 'scale(0.9)';
        setTimeout(() => {
            if (modalOverlay.parentNode) {
                modalOverlay.parentNode.removeChild(modalOverlay);
            }
        }, 300);
    }
    
    // Close on overlay click
    modalOverlay.onclick = (e) => {
        if (e.target === modalOverlay) {
            closeModal();
        }
    };
    
    // Animate in
    setTimeout(() => {
        modalOverlay.style.opacity = '1';
        modalContent.style.transform = 'scale(1)';
    }, 100);
    
    // Close on Escape key
    const handleEscape = (e) => {
        if (e.key === 'Escape') {
            closeModal();
            document.removeEventListener('keydown', handleEscape);
        }
    };
    document.addEventListener('keydown', handleEscape);
}

function copyToClipboard(text) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(text)
            .then(() => {
                showToast('Link copied to clipboard!', 'success');
            })
            .catch((error) => {
                console.error('Clipboard copy failed:', error);
                fallbackCopyToClipboard(text);
            });
    } else {
        fallbackCopyToClipboard(text);
    }
}

function fallbackCopyToClipboard(text) {
    const textArea = document.createElement('textarea');
    textArea.value = text;
    textArea.style.position = 'fixed';
    textArea.style.left = '-999999px';
    textArea.style.top = '-999999px';
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();
    
    try {
        const successful = document.execCommand('copy');
        if (successful) {
            showToast('Link copied to clipboard!', 'success');
        } else {
            showToast('Failed to copy link', 'error');
        }
    } catch (err) {
        console.error('Fallback copy failed:', err);
        showToast('Failed to copy link', 'error');
    }
    
    document.body.removeChild(textArea);
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
            // Update heart icon based on action
            const heartIcon = event.target;
            if (data.action === 'added') {
                heartIcon.classList.remove('far');
                heartIcon.classList.add('fas');
                heartIcon.style.color = '#FF6B6B';
                showToast('Added to favorites!', 'success');
            } else {
                heartIcon.classList.remove('fas');
                heartIcon.classList.add('far');
                heartIcon.style.color = '';
                showToast('Removed from favorites', 'info');
            }
        } else {
            showToast(data.error || 'Failed to update favorites', 'error');
        }
    })
    .catch(error => {
        console.error('Favorites error:', error);
        showToast('Failed to update favorites', 'error');
    });
}

// Toast notification function
function showToast(message, type = 'info') {
    // Remove existing toast if any
    const existingToast = document.querySelector('.toast');
    if (existingToast) {
        existingToast.remove();
    }
    
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.textContent = message;
    
    // Add toast styles
    toast.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 12px 20px;
        border-radius: 8px;
        color: white;
        font-weight: 500;
        z-index: 10000;
        opacity: 0;
        transform: translateX(100%);
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    `;
    
    // Set background color based on type
    switch (type) {
        case 'success':
            toast.style.background = '#4CAF50';
            break;
        case 'error':
            toast.style.background = '#F44336';
            break;
        case 'info':
            toast.style.background = '#2196F3';
            break;
        default:
            toast.style.background = '#666';
    }
    
    // Add to body
    document.body.appendChild(toast);
    
    // Animate in
    setTimeout(() => {
        toast.style.opacity = '1';
        toast.style.transform = 'translateX(0)';
    }, 100);
    
    // Remove after 3 seconds
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(100%)';
        setTimeout(() => {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 300);
    }, 3000);
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
