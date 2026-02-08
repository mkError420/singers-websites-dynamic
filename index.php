<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/functions.php';

// Get latest songs and tour dates for home page
$latestSongs = get_songs(3);
$upcomingTourDates = get_tour_dates(true, 3);
$latestVideos = get_videos(2);
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
                </div>
            </div>
            
            <div class="progress-container">
                <div class="progress-bar" id="progressBar"></div>
                <div class="time-display">
                    <span id="currentTime">0:00</span>
                    <span id="duration">0:00</span>
                </div>
            </div>
            
            <div class="volume-control">
                <i class="fas fa-volume-up"></i>
                <input type="range" id="volumeSlider" min="0" max="100" value="70">
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
        
        <div class="text-center" style="margin-top: 2rem;">
            <a href="<?php echo APP_URL; ?>/music.php" class="btn btn-primary">View All Music</a>
        </div>
    </div>
</section>

<!-- Latest Videos Section -->
<section id="videos" class="section" style="background: var(--dark-secondary);">
    <div class="container">
        <div class="section-title">
            <h2>Latest Videos</h2>
            <p>Watch the latest music videos and performances</p>
        </div>
        
        <div class="video-grid">
            <?php if (!empty($latestVideos)): ?>
                <?php foreach ($latestVideos as $video): ?>
                    <div class="video-item">
                        <img src="<?php echo APP_URL . '/' . ($video['thumbnail'] ?: 'assets/images/default-video.jpg'); ?>" 
                             alt="<?php echo xss_clean($video['title']); ?>" 
                             class="video-thumbnail"
                             data-video-url="<?php echo $video['video_url']; ?>">
                        <div class="video-info">
                            <h3 class="video-title"><?php echo xss_clean($video['title']); ?></h3>
                            <p class="video-description"><?php echo truncate_text(xss_clean($video['description']), 100); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-content">
                    <p>No videos available yet. Check back soon!</p>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="text-center" style="margin-top: 2rem;">
            <a href="<?php echo APP_URL; ?>/videos.php" class="btn btn-primary">View All Videos</a>
        </div>
    </div>
</section>

<!-- Upcoming Tour Dates Section -->
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
