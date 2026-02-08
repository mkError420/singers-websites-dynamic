<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/functions.php';

// Get all songs
$allSongs = get_songs();
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
                        <span id="currentSongArtist"></span>
                    </div>
                </div>
                <div class="player-controls">
                    <button id="prevBtn" class="control-btn"><i class="fas fa-step-backward"></i></button>
                    <button id="playPauseBtn" class="control-btn play-pause"><i class="fas fa-play"></i></button>
                    <button id="nextBtn" class="control-btn"><i class="fas fa-step-forward"></i></button>
                    <button id="shuffleBtn" class="control-btn"><i class="fas fa-random"></i></button>
                    <button id="repeatBtn" class="control-btn"><i class="fas fa-redo"></i></button>
                </div>
            </div>
            
            <div class="progress-container">
                <div class="progress-bar" id="progressBar"></div>
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
                <div class="playlist-toggle">
                    <button id="playlistToggle"><i class="fas fa-list"></i> Playlist</button>
                </div>
            </div>
        </div>
        
        <div class="playlist-container" id="playlistContainer">
            <div class="playlist-header">
                <h3>Playlist</h3>
                <div class="playlist-controls">
                    <button id="playAllBtn" class="btn btn-sm">Play All</button>
                    <button id="shuffleAllBtn" class="btn btn-sm secondary">Shuffle</button>
                </div>
            </div>
            
            <div class="playlist">
                <?php if (!empty($allSongs)): ?>
                    <?php foreach ($allSongs as $index => $song): ?>
                        <div class="song-item" data-audio="<?php echo APP_URL . '/' . $song['file_path']; ?>" data-index="<?php echo $index; ?>">
                            <div class="song-number"><?php echo $index + 1; ?></div>
                            <img src="<?php echo APP_URL . '/' . ($song['cover_image'] ?: 'assets/images/default-album.jpg'); ?>" 
                                 alt="<?php echo xss_clean($song['title']); ?>" class="song-cover">
                            <div class="song-info">
                                <div class="song-title"><?php echo xss_clean($song['title']); ?></div>
                                <div class="song-artist"><?php echo xss_clean($song['artist']); ?></div>
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
        </div>
        
        <!-- Albums Section -->
        <div class="albums-section" style="margin-top: 4rem;">
            <div class="section-title">
                <h3>Albums</h3>
                <p>Complete album collection</p>
            </div>
            
            <div class="albums-grid">
                <?php
                // Get unique albums from database
                $albums_query = "SELECT DISTINCT album, artist, cover_image FROM songs WHERE album IS NOT NULL AND album != '' ORDER BY album ASC";
                $albums = fetchAll($albums_query);
                
                if (!empty($albums)):
                ?>
                    <?php foreach ($albums as $album): ?>
                        <div class="album-card">
                            <img src="<?php echo APP_URL . '/' . ($album['cover_image'] ?: 'assets/images/default-album.jpg'); ?>" 
                                 alt="<?php echo xss_clean($album['album']); ?>" class="album-cover">
                            <div class="album-info">
                                <h4><?php echo xss_clean($album['album']); ?></h4>
                                <p><?php echo xss_clean($album['artist']); ?></p>
                                <button class="btn btn-primary" onclick="playAlbum('<?php echo xss_clean($album['album']); ?>')">Play Album</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
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
.player-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid var(--border-color);
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
    background: none;
    border: none;
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
}

.control-btn:hover {
    background: var(--dark-tertiary);
    color: var(--primary-color);
}

.control-btn.play-pause {
    background: var(--primary-color);
    color: var(--text-primary);
    font-size: 1.5rem;
    width: 50px;
    height: 50px;
}

.control-btn.play-pause:hover {
    background: var(--secondary-color);
}

.progress-container {
    position: relative;
    margin-bottom: 1.5rem;
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
    background: var(--primary-color);
    border-radius: 2px;
    width: 0%;
    transition: width 0.1s linear;
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
}

.volume-control {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.volume-control input[type="range"] {
    width: 100px;
    height: 4px;
    background: var(--dark-tertiary);
    outline: none;
    border-radius: 2px;
    -webkit-appearance: none;
}

.volume-control input[type="range"]::-webkit-slider-thumb {
    -webkit-appearance: none;
    width: 12px;
    height: 12px;
    background: var(--primary-color);
    border-radius: 50%;
    cursor: pointer;
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
    gap: 0.5rem;
}

.btn-sm {
    padding: 0.5rem 1rem;
    font-size: 0.9rem;
}

.btn.secondary {
    background: transparent;
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
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 2rem;
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
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
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
    
    let currentSongIndex = 0;
    let isPlaying = false;
    let isShuffled = false;
    let isRepeating = false;
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
        if (isPlaying) {
            audioPlayer.pause();
            playPauseBtn.innerHTML = '<i class="fas fa-play"></i>';
        } else {
            audioPlayer.play();
            playPauseBtn.innerHTML = '<i class="fas fa-pause"></i>';
        }
        isPlaying = !isPlaying;
    }
    
    // Shuffle playlist
    function shufflePlaylist() {
        if (!isShuffled) {
            shuffledPlaylist = [...originalPlaylist].sort(() => Math.random() - 0.5);
            isShuffled = true;
            shuffleBtn.style.color = 'var(--primary-color)';
        } else {
            shuffledPlaylist = [...originalPlaylist];
            isShuffled = false;
            shuffleBtn.style.color = 'var(--text-primary)';
        }
    }
    
    // Toggle repeat
    function toggleRepeat() {
        isRepeating = !isRepeating;
        repeatBtn.style.color = isRepeating ? 'var(--primary-color)' : 'var(--text-primary)';
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
    
    // Previous/Next buttons
    if (prevBtn) {
        prevBtn.addEventListener('click', function() {
            const songs = isShuffled ? shuffledPlaylist : originalPlaylist;
            currentSongIndex = (currentSongIndex - 1 + songs.length) % songs.length;
            loadSong(currentSongIndex);
            if (isPlaying) audioPlayer.play();
        });
    }
    
    if (nextBtn) {
        nextBtn.addEventListener('click', function() {
            const songs = isShuffled ? shuffledPlaylist : originalPlaylist;
            currentSongIndex = (currentSongIndex + 1) % songs.length;
            loadSong(currentSongIndex);
            if (isPlaying) audioPlayer.play();
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
        if (isRepeating) {
            audioPlayer.currentTime = 0;
            audioPlayer.play();
        } else {
            const songs = isShuffled ? shuffledPlaylist : originalPlaylist;
            currentSongIndex = (currentSongIndex + 1) % songs.length;
            loadSong(currentSongIndex);
            audioPlayer.play();
        }
    });
    
    // Load first song
    if (songItems.length > 0) {
        loadSong(0);
    }
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
    // Get all songs from the selected album
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
            // Replace current playlist with album songs
            const playlistContainer = document.getElementById('playlistContainer');
            const playlistDiv = playlistContainer.querySelector('.playlist');
            
            // Clear existing playlist
            playlistDiv.innerHTML = '';
            
            // Add album songs to playlist
            data.songs.forEach((song, index) => {
                const songItem = document.createElement('div');
                songItem.className = 'song-item';
                songItem.dataset.audio = song.file_path;
                songItem.dataset.index = index;
                
                songItem.innerHTML = `
                    <div class="song-number">${index + 1}</div>
                    <img src="${song.cover_image || 'assets/images/default-album.jpg'}" 
                         alt="${song.title}" class="song-cover">
                    <div class="song-info">
                        <div class="song-title">${song.title}</div>
                        <div class="song-artist">${song.artist}</div>
                        ${song.genre ? `<div class="song-genre">${song.genre}</div>` : ''}
                    </div>
                    <div class="song-meta">
                        <div class="song-duration">${song.duration}</div>
                        ${song.release_date ? `<div class="song-release">${new Date(song.release_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}</div>` : ''}
                    </div>
                    <div class="song-actions">
                        <button class="btn-icon" onclick="downloadSong('${song.file_path}')" title="Download">
                            <i class="fas fa-download"></i>
                        </button>
                        <button class="btn-icon" onclick="shareSong('${song.title}')" title="Share">
                            <i class="fas fa-share"></i>
                        </button>
                        <button class="btn-icon" onclick="addToFavorites(${song.id})" title="Add to Favorites">
                            <i class="far fa-heart"></i>
                        </button>
                    </div>
                `;
                
                playlistDiv.appendChild(songItem);
                
                // Add click event listener
                songItem.addEventListener('click', function(e) {
                    if (e.target.closest('.song-actions')) return;
                    
                    // Update current song index
                    currentSongIndex = index;
                    originalPlaylist = Array.from(playlistDiv.querySelectorAll('.song-item'));
                    
                    // Load and play first song
                    loadSong(index);
                    togglePlayPause();
                });
            });
            
            // Load first song from album
            currentSongIndex = 0;
            loadSong(0);
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
</script>

<!-- Hidden audio element -->
<audio id="audioPlayer" preload="metadata"></audio>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
