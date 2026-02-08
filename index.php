<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/functions.php';

// Get latest songs and tour dates for home page
$latestSongs = get_songs(3);
$upcomingTourDates = get_tour_dates(true, 3);
$latestVideos = get_videos(2); // Get last 2 videos of any type
?>

<!-- Hero Section -->
<section class="hero" id="home">
    <div class="hero-content">
        <h1><?php echo APP_NAME; ?></h1>
        <p class="tagline">Where Music Meets Soul</p>
        <div class="hero-buttons">
            <a href="#music" class="cta-button">Listen Now</a>
            <a href="#tour" class="cta-button secondary">View Tour</a>
        </div>
    </div>
</section>

<!-- Latest Music Section -->
<section id="music" class="section">
    <div class="container">
        <div class="section-title">
            <h2>Latest Music</h2>
            <p>Experience the newest sounds and melodies</p>
        </div>
        
        <div class="enhanced-music-player">
            <div class="player-header">
                <div class="now-playing">
                    <h4>Now Playing</h4>
                    <div class="track-info">
                        <span id="currentSongTitle">Select a song</span>
                        <span id="currentSongArtist"></span>
                    </div>
                </div>
                <div class="player-controls">
                    <button id="prevBtn" class="control-btn" title="Previous"><i class="fas fa-step-backward"></i></button>
                    <button id="playPauseBtn" class="control-btn play-pause" title="Play/Pause"><i class="fas fa-play"></i></button>
                    <button id="nextBtn" class="control-btn" title="Next"><i class="fas fa-step-forward"></i></button>
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
                    <button id="shuffleBtn" class="control-btn" title="Shuffle"><i class="fas fa-random"></i></button>
                    <button id="repeatBtn" class="control-btn" title="Repeat"><i class="fas fa-redo"></i></button>
                </div>
            </div>
        </div>
        
        <div class="playlist">
            <?php if (!empty($latestSongs)): ?>
                <?php foreach ($latestSongs as $song): ?>
                    <div class="song-item" data-audio="<?php echo APP_URL . '/' . $song['file_path']; ?>">
                        <img src="<?php echo APP_URL . '/' . ($song['cover_image'] ?: 'assets/images/default-album.jpg'); ?>" 
                             alt="<?php echo xss_clean($song['title']); ?>" class="song-cover">
                        <div class="song-info">
                            <div class="song-title"><?php echo xss_clean($song['title']); ?></div>
                            <div class="song-artist"><?php echo xss_clean($song['artist']); ?></div>
                        </div>
                        <div class="song-duration"><?php echo $song['duration']; ?></div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-content">
                    <p>No songs available yet. Check back soon!</p>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="text-center" style="margin-top: 3rem;">
            <a href="<?php echo APP_URL; ?>/music.php" class="modern-btn primary-btn">
                <span class="btn-content">
                    <i class="fas fa-music"></i>
                    <span class="btn-text">View All Music</span>
                    <span class="btn-arrow">
                        <i class="fas fa-arrow-right"></i>
                    </span>
                </span>
            </a>
        </div>
    </div>
</section>

<style>
/* Enhanced Music Player Styles - Simple & Small */
.enhanced-music-player {
    background: var(--dark-secondary);
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 2rem;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.player-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.now-playing h4 {
    color: var(--text-secondary);
    font-size: 0.8rem;
    margin: 0 0 0.5rem 0;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.track-info {
    display: flex;
    flex-direction: column;
}

.track-info span {
    color: var(--text-primary);
    font-size: 1rem;
    font-weight: 600;
    margin: 0;
}

.track-info span:first-child {
    font-size: 1.2rem;
    color: var(--text-primary);
    font-weight: 700;
}

.player-controls {
    display: flex;
    gap: 0.75rem;
    align-items: center;
}

.control-btn {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: var(--text-primary);
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 1rem;
}

.control-btn:hover {
    background: var(--primary-color);
    border-color: var(--primary-color);
    transform: scale(1.05);
    box-shadow: 0 3px 10px rgba(255, 107, 107, 0.3);
    filter: brightness(1.1);
}

.control-btn.play-pause {
    width: 50px;
    height: 50px;
    background: var(--primary-color);
    border-color: var(--primary-color);
    font-size: 1.2rem;
    box-shadow: 0 3px 15px rgba(255, 107, 107, 0.3);
}

.control-btn.play-pause:hover {
    background: var(--secondary-color);
    transform: scale(1.1);
    box-shadow: 0 5px 20px rgba(255, 107, 107, 0.4);
    filter: brightness(1.1);
}

.control-btn.active {
    background: var(--secondary-color);
    border-color: var(--secondary-color);
    color: var(--text-primary);
}

.control-btn.active:hover {
    background: var(--primary-color);
    border-color: var(--primary-color);
    transform: scale(1.1);
    box-shadow: 0 5px 20px rgba(255, 107, 107, 0.4);
}

.progress-section {
    margin-bottom: 1.5rem;
}

.progress-container {
    background: rgba(255, 255, 255, 0.1);
    height: 6px;
    border-radius: 8px;
    overflow: hidden;
    cursor: pointer;
    transition: all 0.3s ease;
}

.progress-container:hover {
    background: rgba(255, 255, 255, 0.15);
    transform: scaleY(1.2);
}

.progress-bar {
    height: 4px;
    background: var(--dark-tertiary);
    border-radius: 2px;
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
    background: var(--primary-color);
    border-radius: 2px;
    width: 0%;
    transition: width 0.1s linear;
    box-shadow: 0 0 5px rgba(255, 107, 107, 0.3);
}

.progress-container:hover .progress-bar::before {
    box-shadow: 0 0 10px rgba(255, 107, 107, 0.5);
    filter: brightness(1.2);
}

.time-display {
    display: flex;
    justify-content: space-between;
    margin-top: 0.5rem;
    color: var(--text-muted);
    font-size: 0.85rem;
    font-weight: 500;
}

.player-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1.5rem;
}

.volume-control {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.volume-control i {
    color: var(--text-secondary);
    font-size: 1rem;
}

.volume-slider {
    width: 80px;
    height: 4px;
    background: rgba(255, 255, 255, 0.1);
    outline: none;
    border-radius: 8px;
    -webkit-appearance: none;
    appearance: none;
    transition: all 0.3s ease;
}

.volume-slider:hover {
    background: rgba(255, 255, 255, 0.15);
    transform: scaleY(1.2);
}

.volume-slider::-webkit-slider-thumb {
    -webkit-appearance: none;
    appearance: none;
    width: 12px;
    height: 12px;
    background: var(--primary-color);
    border-radius: 50%;
    cursor: pointer;
    box-shadow: 0 2px 6px rgba(255, 107, 107, 0.3);
    transition: all 0.3s ease;
}

.volume-slider::-webkit-slider-thumb:hover {
    transform: scale(1.2);
    box-shadow: 0 3px 10px rgba(255, 107, 107, 0.5);
    filter: brightness(1.2);
}

.volume-slider::-moz-range-thumb {
    width: 12px;
    height: 12px;
    background: var(--primary-color);
    border-radius: 50%;
    cursor: pointer;
    border: none;
    box-shadow: 0 2px 6px rgba(255, 107, 107, 0.3);
    transition: all 0.3s ease;
}

.volume-slider::-moz-range-thumb:hover {
    transform: scale(1.2);
    box-shadow: 0 3px 10px rgba(255, 107, 107, 0.5);
    filter: brightness(1.2);
}

.player-actions {
    display: flex;
    gap: 0.75rem;
}

.player-actions .control-btn {
    width: 35px;
    height: 35px;
    font-size: 0.9rem;
}

/* Simple Playlist Styles */
.playlist {
    background: rgba(0, 0, 0, 0.2);
    border-radius: 12px;
    padding: 1rem;
    max-height: 350px;
    overflow-y: auto;
}

.song-item {
    display: flex;
    align-items: center;
    padding: 0.75rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    transition: all 0.3s ease;
    cursor: pointer;
    border-radius: 8px;
    position: relative;
    overflow: hidden;
}

.song-item::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 0;
    background: linear-gradient(90deg, var(--primary-color), transparent);
    transition: width 0.3s ease;
    opacity: 0.3;
}

.song-item:hover {
    background: rgba(255, 255, 255, 0.05);
    transform: translateX(5px);
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
}

.song-item:hover::before {
    width: 3px;
}

.song-item:hover .song-cover {
    transform: scale(1.05);
    filter: brightness(1.1);
}

.song-item:hover .song-title {
    color: var(--primary-color);
    transform: translateX(2px);
}

.song-item:hover .song-artist {
    color: var(--text-primary);
    transform: translateX(2px);
}

.song-item:last-child {
    border-bottom: none;
}

.song-cover {
    width: 50px;
    height: 50px;
    border-radius: 6px;
    object-fit: cover;
    margin-right: 0.75rem;
    flex-shrink: 0;
    transition: all 0.3s ease;
}

.song-info {
    flex: 1;
}

.song-title {
    color: var(--text-primary);
    font-size: 0.95rem;
    font-weight: 600;
    margin-bottom: 0.25rem;
    transition: all 0.3s ease;
}
    
.song-artist {
    color: var(--text-secondary);
    font-size: 0.85rem;
    margin-bottom: 0.25rem;
    transition: all 0.3s ease;
}

.song-duration {
    color: var(--text-muted);
    font-size: 0.8rem;
    background: rgba(255, 255, 255, 0.1);
    padding: 0.2rem 0.4rem;
    border-radius: 10px;
    margin-left: auto;
    transition: all 0.3s ease;
}

.song-item:hover .song-duration {
    background: var(--primary-color);
    color: var(--text-primary);
    transform: scale(1.05);
}

.no-content {
    text-align: center;
    padding: 4rem 2rem;
    background: linear-gradient(135deg, rgba(255, 107, 107, 0.1), rgba(0, 0, 0, 0.2));
    border-radius: 20px;
    border: 1px solid rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    position: relative;
    overflow: hidden;
}

.no-content h3 {
    color: var(--text-primary);
    font-size: 1.8rem;
    margin: 0 0 0.5rem 0;
    font-weight: 700;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
}

.no-content p {
    color: var(--text-secondary);
    font-size: 1.1rem;
    margin: 0;
    opacity: 0.9;
}

.no-content .icon {
    font-size: 4rem;
    margin-bottom: 1rem;
    opacity: 0.6;
    animation: float 3s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
}

/* Responsive Design */
@media (max-width: 768px) {
    .enhanced-music-player {
        padding: 1rem;
    }
    
    .player-header {
        flex-direction: column;
        gap: 0.75rem;
        text-align: center;
    }
    
    .player-controls {
        justify-content: center;
    }
    
    .player-footer {
        flex-direction: column;
        gap: 0.75rem;
    }
    
    .song-item {
        padding: 0.5rem;
    }
    
    .song-cover {
        width: 45px;
        height: 45px;
    }
}

@media (max-width: 480px) {
    .enhanced-music-player {
        padding: 0.75rem;
    }
    
    .control-btn {
        width: 35px;
        height: 35px;
        font-size: 0.9rem;
    }
    
    .control-btn.play-pause {
        width: 45px;
        height: 45px;
        font-size: 1.1rem;
    }
    
    .player-actions .control-btn {
        width: 30px;
        height: 30px;
        font-size: 0.8rem;
    }
}

/* Modern Button Styles */
.modern-btn {
    display: inline-block;
    position: relative;
    padding: 0;
    background: transparent;
    border: none;
    cursor: pointer;
    text-decoration: none;
    overflow: hidden;
    transition: all 0.3s ease;
}

.btn-content {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem 2rem;
    background: linear-gradient(135deg, var(--primary-color), #ff6b6b);
    color: var(--text-primary);
    font-size: 1rem;
    font-weight: 600;
    border-radius: 50px;
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease;
    box-shadow: 0 5px 20px rgba(255, 107, 107, 0.3);
}

.btn-content::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    transition: left 0.5s ease;
}

.modern-btn:hover .btn-content {
    transform: translateY(-2px);
    box-shadow: 0 8px 30px rgba(255, 107, 107, 0.4);
    background: linear-gradient(135deg, #ff6b6b, var(--primary-color));
}

.modern-btn:hover .btn-content::before {
    left: 100%;
}

.btn-content i:first-child {
    font-size: 1.1rem;
    transition: transform 0.3s ease;
}

.modern-btn:hover .btn-content i:first-child {
    transform: rotate(15deg) scale(1.1);
}

.btn-text {
    position: relative;
    z-index: 1;
    transition: transform 0.3s ease;
}

.modern-btn:hover .btn-text {
    transform: translateX(2px);
}

.btn-arrow {
    position: relative;
    z-index: 1;
    transition: transform 0.3s ease;
}

.modern-btn:hover .btn-arrow {
    transform: translateX(3px);
}

.btn-arrow i {
    font-size: 0.9rem;
    transition: transform 0.3s ease;
}

.modern-btn:hover .btn-arrow i {
    transform: translateX(2px);
}

/* Secondary Button Style */
.modern-btn.secondary-btn .btn-content {
    background: linear-gradient(135deg, var(--dark-secondary), var(--dark-tertiary));
    color: var(--text-primary);
    border: 1px solid rgba(255, 255, 255, 0.2);
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
}

.modern-btn.secondary-btn:hover .btn-content {
    background: linear-gradient(135deg, var(--dark-tertiary), var(--dark-secondary));
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.3);
    border-color: var(--primary-color);
}

/* Button Ripple Effect */
.modern-btn::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.5);
    transform: translate(-50%, -50%);
    transition: width 0.6s, height 0.6s;
    opacity: 0;
}

.modern-btn:active::after {
    width: 300px;
    height: 300px;
    opacity: 0;
    transition: 0s;
}

/* Responsive Button */
@media (max-width: 768px) {
    .btn-content {
        padding: 0.875rem 1.5rem;
        font-size: 0.95rem;
    }
    
    .btn-content i:first-child {
        font-size: 1rem;
    }
    
    .btn-arrow i {
        font-size: 0.8rem;
    }
}

@media (max-width: 480px) {
    .btn-content {
        padding: 0.75rem 1.25rem;
        font-size: 0.9rem;
        gap: 0.5rem;
    }
    
    .btn-content i:first-child {
        font-size: 0.9rem;
    }
    
    .btn-arrow i {
        font-size: 0.75rem;
    }
}
</style>
<section id="videos" class="section" style="background: var(--dark-secondary);">
    <div class="container">
        <div class="section-title">
            <h2>Latest Videos</h2>
            <p>Watch the most recent videos from our collection</p>
        </div>
        
        <div class="video-grid">
            <?php if (!empty($latestVideos)): ?>
                <?php foreach ($latestVideos as $video): ?>
                    <div class="video-item">
                        <img src="<?php echo APP_URL . '/' . ($video['thumbnail'] ?: 'assets/images/default-video.jpg'); ?>" 
                             alt="<?php echo xss_clean($video['title']); ?>" 
                             class="video-thumbnail"
                             data-video-url="<?php echo $video['video_url']; ?>"
                             onclick="openVideoModal('<?php echo $video['video_url']; ?>', '<?php echo xss_clean($video['title']); ?>', '<?php echo xss_clean($video['description']); ?>')">
                        <div class="video-info">
                            <h3 class="video-title"><?php echo xss_clean($video['title']); ?></h3>
                            <p class="video-description"><?php echo truncate_text(xss_clean($video['description']), 100); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-content">
                    <div class="icon">
                        <i class="fas fa-video"></i>
                    </div>
                    <h3>No videos found</h3>
                    <p>No videos available yet. Check back soon!</p>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="text-center" style="margin-top: 3rem;">
            <a href="<?php echo APP_URL; ?>/videos.php" class="modern-btn secondary-btn">
                <span class="btn-content">
                    <i class="fas fa-play"></i>
                    <span class="btn-text">View All Videos</span>
                    <span class="btn-arrow">
                        <i class="fas fa-arrow-right"></i>
                    </span>
                </span>
            </a>
        </div>
    </div>
</section>

<!-- Video Modal -->
<div id="videoModal" class="video-modal">
    <div class="modal-content">
        <span class="close-modal">&times;</span>
        <div class="video-container">
            <iframe id="modalVideo" src="" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
        </div>
        <div class="modal-info">
            <h3 id="modalVideoTitle"></h3>
            <p id="modalVideoDescription"></p>
            <div class="modal-actions">
                <button class="btn btn-primary" onclick="closeVideoModal()">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
// Video Modal Function
function openVideoModal(videoUrl, title, description) {
    const videoModal = document.getElementById('videoModal');
    const modalVideo = document.getElementById('modalVideo');
    
    // Convert YouTube URL to embed format
    let embedUrl = videoUrl;
    
    try {
        if (videoUrl.includes('m.youtube.com/watch?v=')) {
            // Mobile YouTube URL
            const videoId = videoUrl.split('v=')[1]?.split('&')[0];
            if (videoId) {
                embedUrl = `https://www.youtube-nocookie.com/embed/${videoId}?autoplay=1`;
            }
        } else if (videoUrl.includes('youtube.com/watch?v=')) {
            // Standard YouTube URL
            const videoId = videoUrl.split('v=')[1]?.split('&')[0];
            if (videoId) {
                embedUrl = `https://www.youtube-nocookie.com/embed/${videoId}?autoplay=1`;
            }
        } else if (videoUrl.includes('youtu.be/')) {
            // Short YouTube URL
            const videoId = videoUrl.split('youtu.be/')[1]?.split('?')[0];
            if (videoId) {
                embedUrl = `https://www.youtube-nocookie.com/embed/${videoId}?autoplay=1`;
            }
        } else if (videoUrl.includes('youtube.com/embed/')) {
            // Already embed format
            const baseUrl = videoUrl.split('?')[0];
            embedUrl = `${baseUrl}?autoplay=1`;
        } else if (videoUrl.includes('vimeo.com/')) {
            // Vimeo URL
            const videoId = videoUrl.split('vimeo.com/')[1]?.split('?')[0];
            if (videoId) {
                embedUrl = `https://player.vimeo.com/video/${videoId}?autoplay=1`;
            }
        } else if (videoUrl.includes('uploads/videos/')) {
            // Uploaded video file
            embedUrl = videoUrl;
        }
        
        // Set video info and open modal
        document.getElementById('modalVideoTitle').textContent = title;
        document.getElementById('modalVideoDescription').textContent = description;
        
        if (embedUrl.includes('uploads/videos/')) {
            // HTML5 video for uploaded files
            const videoContainer = document.querySelector('.video-container');
            videoContainer.innerHTML = `
                <video controls autoplay style="width: 100%; height: 100%;">
                    <source src="${embedUrl}" type="video/mp4">
                    <source src="${embedUrl}" type="video/webm">
                    <source src="${embedUrl}" type="video/ogg">
                    Your browser does not support the video tag.
                </video>
            `;
        } else {
            // iframe for YouTube/Vimeo
            modalVideo.src = embedUrl;
        }
        
        videoModal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        
    } catch (error) {
        console.error('Error loading video:', error);
    }
}

function closeVideoModal() {
    const videoModal = document.getElementById('videoModal');
    const modalVideo = document.getElementById('modalVideo');
    
    if (modalVideo) {
        modalVideo.src = '';
    }
    
    // Clear video container
    const videoContainer = document.querySelector('.video-container');
    videoContainer.innerHTML = '<iframe id="modalVideo" src="" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
    
    videoModal.style.display = 'none';
    document.body.style.overflow = 'auto';
}

// Close modal events
document.addEventListener('DOMContentLoaded', function() {
    const closeModal = document.querySelector('.close-modal');
    const videoModal = document.getElementById('videoModal');
    
    if (closeModal) {
        closeModal.addEventListener('click', closeVideoModal);
    }
    
    videoModal.addEventListener('click', function(e) {
        if (e.target === videoModal) {
            closeVideoModal();
        }
    });
    
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && videoModal.style.display === 'flex') {
            closeVideoModal();
        }
    });
});
</script>

<style>
/* Video Section Styles */
.video-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 2rem;
    margin-bottom: 2rem;
}

.video-item {
    background: linear-gradient(135deg, var(--dark-secondary) 0%, var(--dark-tertiary) 100%);
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3), inset 0 1px 0 rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.1);
    position: relative;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    transform-style: preserve-3d;
}

.video-item::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, transparent, var(--primary-color), transparent);
    background-size: 200% 100%;
    animation: shimmerGradient 3s linear infinite;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.video-item:hover::before {
    opacity: 1;
}

@keyframes shimmerGradient {
    0% { background-position: -200% 0; }
    100% { background-position: 200% 0; }
}

.video-item:hover {
    transform: translateY(-10px) scale(1.02);
    box-shadow: 0 20px 40px rgba(255, 107, 107, 0.2);
    border-color: rgba(255, 107, 107, 0.3);
}

.video-thumbnail-container {
    position: relative;
    overflow: hidden;
}

.video-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
    pointer-events: none;
}

.video-item:hover .video-overlay {
    opacity: 1;
}

.video-item.playing .video-overlay {
    display: none;
}

.play-button {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 70px;
    height: 70px;
    background: linear-gradient(135deg, var(--primary-color), #ff6b6b);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--text-primary);
    font-size: 1.8rem;
    cursor: pointer;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 8px 25px rgba(255, 107, 107, 0.4);
    backdrop-filter: blur(10px);
    z-index: 10;
}

.play-button:hover {
    transform: translate(-50%, -50%) scale(1.15);
    background: linear-gradient(135deg, #ff6b6b, var(--primary-color));
    box-shadow: 0 12px 35px rgba(255, 107, 107, 0.5);
}

.play-button::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    border-radius: 50%;
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.2), transparent);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.play-button:hover::before {
    opacity: 1;
}

.video-item.playing .play-button {
    background: linear-gradient(135deg, #ff6b6b, #ff4757);
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { transform: translate(-50%, -50%) scale(1); }
    50% { transform: translate(-50%, -50%) scale(1.1); }
    100% { transform: translate(-50%, -50%) scale(1); }
}

.video-duration {
    position: absolute;
    bottom: 10px;
    right: 10px;
    background: rgba(0, 0, 0, 0.8);
    color: white;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.8rem;
}

.video-info {
    padding: 2rem;
    background: rgba(0, 0, 0, 0.2);
    backdrop-filter: blur(10px);
    border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.video-title {
    font-size: 1.3rem;
    margin-bottom: 0.75rem;
    color: var(--text-primary);
    font-weight: 700;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    line-height: 1.3;
}

.video-description {
    color: var(--text-secondary);
    font-size: 0.95rem;
    margin-bottom: 1.25rem;
    line-height: 1.6;
    opacity: 0.9;
}

.video-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    color: var(--text-muted);
    font-size: 0.85rem;
    margin-bottom: 0;
    padding-top: 0.5rem;
    border-top: 1px solid rgba(255, 255, 255, 0.05);
}

.video-views {
    color: var(--text-muted);
    font-size: 0.85rem;
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.video-views::before {
    content: '👁';
    font-size: 1rem;
}

.video-date {
    color: var(--text-muted);
    font-size: 0.85rem;
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.video-date::before {
    content: '📅';
    font-size: 1rem;
}

.video-modal {
    display: none;
    position: fixed;
    z-index: 2000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.9);
    align-items: center;
    justify-content: center;
}

.modal-content {
    background: var(--dark-secondary);
    border-radius: 15px;
    max-width: 900px;
    width: 90%;
    max-height: 90vh;
    overflow-y: auto;
    position: relative;
}

.close-modal {
    position: absolute;
    top: 1rem;
    right: 1rem;
    font-size: 2rem;
    color: var(--text-primary);
    cursor: pointer;
    z-index: 1;
    background: rgba(0, 0, 0, 0.5);
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.video-container {
    position: relative;
    padding-bottom: 56.25%;
    height: 0;
    overflow: hidden;
}

.video-container iframe {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
}

.modal-info {
    padding: 2rem;
}

.modal-info h3 {
    margin-bottom: 1rem;
    color: var(--text-primary);
}

.modal-info p {
    color: var(--text-secondary);
    margin-bottom: 1.5rem;
}

.modal-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
}

.modal-actions .btn {
    padding: 0.75rem 1.5rem;
    font-size: 0.9rem;
    border-radius: 8px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.modal-actions .btn i {
    font-size: 1rem;
}

@media (max-width: 768px) {
    .video-grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
    
    .modal-content {
        width: 95%;
        margin: 1rem;
    }
    
    .modal-actions {
        flex-direction: column;
    }
}
</style>

<section id="tour" class="section">
    <div class="container">
        <div class="section-title">
            <h2>Upcoming Tour Dates</h2>
            <p>Join us live at a city near you</p>
        </div>
        
        <div class="tour-dates">
            <?php if (!empty($upcomingTourDates)): ?>
                <?php foreach ($upcomingTourDates as $tour): ?>
                    <div class="tour-item">
                        <div class="tour-date">
                            <div class="tour-info">
                                <h3><?php echo xss_clean($tour['event_name']); ?></h3>
                                <div class="tour-venue"><?php echo xss_clean($tour['venue']); ?></div>
                                <div class="tour-location">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <?php echo xss_clean($tour['city'] . ', ' . $tour['country']); ?>
                                </div>
                                <div class="tour-datetime">
                                    <i class="fas fa-calendar"></i>
                                    <?php echo format_date($tour['event_date'], 'F j, Y'); ?>
                                    <?php if ($tour['event_time']): ?>
                                        <i class="fas fa-clock" style="margin-left: 1rem;"></i>
                                        <?php echo format_time($tour['event_time']); ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="tour-actions">
                                <?php if ($tour['ticket_url']): ?>
                                    <a href="<?php echo xss_clean($tour['ticket_url']); ?>" 
                                       target="_blank" 
                                       class="btn btn-primary">Get Tickets</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-content">
                    <p>No tour dates scheduled at the moment. Check back soon!</p>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="text-center" style="margin-top: 2rem;">
            <a href="<?php echo APP_URL; ?>/tour.php" class="btn btn-primary">View All Tour Dates</a>
        </div>
    </div>
</section>

<!-- Newsletter Section -->
<section class="newsletter">
    <div class="container">
        <div class="section-title">
            <h2>Stay Connected</h2>
            <p>Get the latest updates, exclusive content, and tour announcements</p>
        </div>
        
        <form id="newsletterForm" class="newsletter-form">
            <input type="email" name="email" placeholder="Enter your email address" required class="form-control">
            <button type="submit" class="btn btn-primary">Subscribe</button>
        </form>
        
        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
    </div>
</section>

<!-- Hidden audio element -->
<audio id="audioPlayer" preload="metadata"></audio>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
