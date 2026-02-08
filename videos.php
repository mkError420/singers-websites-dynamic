<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/functions.php';

// Get all videos
$all_videos = get_videos();

// Get video categories for filtering
$categories = [];
foreach ($all_videos as $video) {
    $category = $video['video_type'] ?? 'youtube';
    if (!in_array($category, $categories)) {
        $categories[] = $category;
    }
}
?>

<!-- Hero Section -->
<section class="hero">
    <div class="hero-content">
        <h1>Music Videos</h1>
        <p>Watch our latest music videos, live performances, and behind-the-scenes content.</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-title">
            <h2>Our Videos</h2>
            <p>Explore our collection of music videos and performances</p>
        </div>
        
        <!-- Filter Buttons -->
        <div class="video-filters">
            <div class="filter-buttons">
                <button class="filter-btn active" data-filter="all">All</button>
                <button class="filter-btn" data-filter="youtube">YouTube</button>
                <button class="filter-btn" data-filter="vimeo">Vimeo</button>
                <button class="filter-btn" data-filter="uploaded">Uploaded</button>
            </div>
        </div>
        
        <!-- Video Grid -->
        <div class="video-grid">
            <?php if (!empty($all_videos)): ?>
                <?php foreach ($all_videos as $video): ?>
                    <div class="video-item" data-category="<?php echo $video['video_type']; ?>">
                        <div class="video-thumbnail-container">
                            <img src="<?php echo APP_URL . '/' . ($video['thumbnail'] ?: 'assets/images/default-video.jpg'); ?>" 
                                 alt="<?php echo xss_clean($video['title']); ?>" 
                                 class="video-thumbnail"
                                 data-video-url="<?php echo $video['video_url']; ?>">
                            <div class="video-overlay">
                                <div class="play-button">
                                    <i class="fas fa-play"></i>
                                </div>
                                <div class="video-duration"><?php echo $video['duration'] ?? '3:45'; ?></div>
                            </div>
                        </div>
                        
                        <div class="video-info">
                            <h3 class="video-title"><?php echo xss_clean($video['title']); ?></h3>
                            <p class="video-description"><?php echo truncate_text(xss_clean($video['description']), 120); ?></p>
                            <div class="video-meta">
                                <span class="video-views">1.2M views</span>
                                <span class="video-date"><?php echo format_date($video['created_at'], 'M j, Y'); ?></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-videos">
                    <h3>No videos found</h3>
                    <p>Check back soon for new content!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Video Scripts -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    initVideoFilters();
    initSimpleVideoClick();
});

function showToast(message, type = 'success') {
    // Remove existing toast
    const existingToast = document.querySelector('.toast');
    if (existingToast) {
        existingToast.remove();
    }
    
    // Create new toast
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.textContent = message;
    
    document.body.appendChild(toast);
    
    // Show toast
    setTimeout(() => {
        toast.classList.add('show');
    }, 100);
    
    // Hide toast after 3 seconds
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => {
            toast.remove();
        }, 300);
    }, 3000);
}

function shareVideo(title, url) {
    if (navigator.share) {
        navigator.share({
            title: title,
            text: `Check out ${title} by ${APP_NAME}`,
            url: url
        });
    } else {
        navigator.clipboard.writeText(url);
        showToast('Link copied to clipboard!', 'success');
    }
}

function likeVideo(videoId) {
    fetch('includes/video-likes-handler.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ video_id: videoId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Video liked!', 'success');
            event.target.classList.remove('far');
            event.target.classList.add('fas');
        }
    })
    .catch(error => {
        showToast('Failed to like video', 'error');
    });
}

function addToWatchLater(videoId) {
    fetch('includes/watchlater-handler.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ video_id: videoId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Added to Watch Later!', 'success');
        }
    })
    .catch(error => {
        showToast('Failed to add to Watch Later', 'error');
    });
}

function initVideoFilters() {
    const filterButtons = document.querySelectorAll('.filter-btn');
    const videoItems = document.querySelectorAll('.video-item');
    
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Update active state
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            const filter = this.dataset.filter;
            
            // Filter videos
            videoItems.forEach(item => {
                if (filter === 'all' || item.dataset.category === filter) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    });
}

function initSimpleVideoClick() {
    const videoThumbnails = document.querySelectorAll('.video-thumbnail');
    const videoModal = document.getElementById('videoModal');
    const modalVideo = document.getElementById('modalVideo');
    const closeModal = document.querySelector('.close-modal');
    
    console.log('Found video thumbnails:', videoThumbnails.length);
    
    videoThumbnails.forEach(thumbnail => {
        thumbnail.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const videoUrl = this.dataset.videoUrl;
            const videoItem = this.closest('.video-item');
            const videoTitle = videoItem.querySelector('.video-title').textContent;
            const videoDescription = videoItem.querySelector('.video-description').textContent;
            
            console.log('Opening video in modal:', videoUrl);
            
            // Convert YouTube URL to embed format
            let embedUrl = videoUrl;
            console.log('Original URL:', videoUrl);
            
            try {
                console.log('Converting video URL:', videoUrl);
                
                // Handle different video URL formats
                let embedUrl = '';
                let videoId = '';
                
                if (videoUrl.includes('m.youtube.com/watch?v=')) {
                    // Mobile YouTube URL: https://m.youtube.com/watch?v=VIDEO_ID
                    videoId = videoUrl.split('v=')[1]?.split('&')[0];
                    if (videoId) {
                        embedUrl = `https://www.youtube-nocookie.com/embed/${videoId}?autoplay=1&rel=0`;
                    }
                } else if (videoUrl.includes('youtube.com/watch?v=')) {
                    // Standard YouTube URL: https://www.youtube.com/watch?v=VIDEO_ID
                    videoId = videoUrl.split('v=')[1]?.split('&')[0];
                    if (videoId) {
                        embedUrl = `https://www.youtube-nocookie.com/embed/${videoId}?autoplay=1&rel=0`;
                    }
                } else if (videoUrl.includes('youtu.be/')) {
                    // Short YouTube URL: https://youtu.be/VIDEO_ID
                    videoId = videoUrl.split('youtu.be/')[1]?.split('?')[0];
                    if (videoId) {
                        embedUrl = `https://www.youtube-nocookie.com/embed/${videoId}?autoplay=1&rel=0`;
                    }
                } else if (videoUrl.includes('youtube.com/embed/')) {
                    // Already embed format, just add parameters
                    const baseUrl = videoUrl.split('?')[0];
                    embedUrl = `${baseUrl}?autoplay=1&rel=0`;
                } else if (videoUrl.includes('vimeo.com/')) {
                    // Vimeo URL: https://vimeo.com/VIDEO_ID
                    videoId = videoUrl.split('vimeo.com/')[1]?.split('?')[0];
                    if (videoId) {
                        embedUrl = `https://player.vimeo.com/video/${videoId}?autoplay=1`;
                    }
                } else if (videoUrl.includes('uploads/videos/')) {
                    // Uploaded video file
                    embedUrl = videoUrl;
                    console.log('Using uploaded video file:', embedUrl);
                } else {
                    // Non-YouTube/Vimeo URL, try to detect other video platforms
                    console.log('Non-YouTube/Vimeo URL, attempting to use as-is:', videoUrl);
                    embedUrl = videoUrl;
                }
                
                if (!embedUrl) {
                    throw new Error('Invalid video URL format');
                }
                
                console.log('Final embed URL:', embedUrl);
                
                // Set video info and open modal
                document.getElementById('modalVideoTitle').textContent = videoTitle;
                document.getElementById('modalVideoDescription').textContent = videoDescription;
                
                // Check if it's an uploaded video file
                if (embedUrl.includes('uploads/videos/')) {
                    // Create HTML5 video element for uploaded files
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
                    // Use iframe for YouTube/Vimeo videos
                    const videoContainer = document.querySelector('.video-container');
                    videoContainer.innerHTML = `
                        <iframe id="modalVideo" src="" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                    `;
                    
                    // Load video with proper event listeners
                    const modalVideo = document.getElementById('modalVideo');
                    modalVideo.addEventListener('load', function() {
                        console.log('Video loaded successfully');
                    });
                    
                    modalVideo.addEventListener('error', function() {
                        console.error('Video failed to load:', embedUrl);
                        showToast('Failed to load video. Please try again.', 'error');
                        closeVideoModal();
                    });
                    
                    // Set the src
                    modalVideo.src = embedUrl;
                }
                
                videoModal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
                
            } catch (error) {
                console.error('Error converting video URL:', error);
                showToast('Invalid video URL format', 'error');
                closeVideoModal();
            }
        });
    });
    
    // Close modal events
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
}

function closeVideoModal() {
    const videoModal = document.getElementById('videoModal');
    const modalVideo = document.getElementById('modalVideo');
    
    if (modalVideo) {
        modalVideo.src = '';
    }
    videoModal.style.display = 'none';
    document.body.style.overflow = 'auto';
}

</script>

<style>
/* Videos Page Specific Styles */
.video-filters {
    margin-bottom: 2rem;
    text-align: center;
}

.filter-buttons {
    display: flex;
    justify-content: center;
    gap: 1rem;
    flex-wrap: wrap;
}

.filter-btn {
    background: var(--dark-tertiary);
    border: 1px solid var(--border-color);
    color: var(--text-primary);
    padding: 0.75rem 1.5rem;
    border-radius: 25px;
    cursor: pointer;
    transition: all 0.3s ease;
    font-weight: 500;
}

.filter-btn:hover,
.filter-btn.active {
    background: var(--primary-color);
    border-color: var(--primary-color);
}

.video-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 2rem;
    margin-bottom: 2rem;
}

.video-item {
    background: var(--dark-secondary);
    border-radius: 15px;
    overflow: hidden;
    box-shadow: var(--shadow-lg);
    transition: transform 0.3s ease;
}

.video-item:hover {
    transform: translateY(-5px);
}

.video-thumbnail-container {
    position: relative;
    overflow: hidden;
}

.video-thumbnail {
    width: 100%;
    height: 200px;
    object-fit: cover;
    cursor: pointer;
    transition: transform 0.3s ease;
}

.video-item:hover .video-thumbnail {
    transform: scale(1.05);
}

.play-button:hover {
    transform: scale(1.1);
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
    padding: 1.5rem;
}

.video-title {
    font-size: 1.2rem;
    margin-bottom: 0.5rem;
    color: var(--text-primary);
}

.video-description {
    color: var(--text-secondary);
    font-size: 0.9rem;
    margin-bottom: 1rem;
    line-height: 1.5;
}

.video-meta {
    display: flex;
    justify-content: space-between;
    color: var(--text-muted);
    font-size: 0.8rem;
    margin-bottom: 1rem;
}

.video-actions {
    display: flex;
    gap: 0.5rem;
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
    
    .filter-buttons {
        flex-direction: column;
        align-items: center;
    }
    
    .filter-btn {
        width: 200px;
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
document.addEventListener('DOMContentLoaded', function() {
    initVideoFilters();
    initSimpleVideoClick();
});

function initVideoFilters() {
    const filterButtons = document.querySelectorAll('.filter-btn');
    const videoItems = document.querySelectorAll('.video-item');
    
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Update active state
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            const filter = this.dataset.filter;
            
            // Filter videos
            videoItems.forEach(item => {
                if (filter === 'all' || item.dataset.category === filter) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    });
}

function initSimpleVideoClick() {
    const videoThumbnails = document.querySelectorAll('.video-thumbnail');
    const videoModal = document.getElementById('videoModal');
    const modalVideo = document.getElementById('modalVideo');
    const closeModal = document.querySelector('.close-modal');
    
    console.log('Found video thumbnails:', videoThumbnails.length);
    
    videoThumbnails.forEach(thumbnail => {
        thumbnail.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const videoUrl = this.dataset.videoUrl;
            const videoItem = this.closest('.video-item');
            const videoTitle = videoItem.querySelector('.video-title').textContent;
            const videoDescription = videoItem.querySelector('.video-description').textContent;
            
            console.log('Opening video in modal:', videoUrl);
            
            // Convert YouTube URL to embed format
            let embedUrl = videoUrl;
            console.log('Original URL:', videoUrl);
            
            try {
                console.log('Converting video URL:', videoUrl);
                
                // Handle different video URL formats
                let embedUrl = '';
                let videoId = '';
                
                if (videoUrl.includes('m.youtube.com/watch?v=')) {
                    // Mobile YouTube URL: https://m.youtube.com/watch?v=VIDEO_ID
                    videoId = videoUrl.split('v=')[1]?.split('&')[0];
                    if (videoId) {
                        embedUrl = `https://www.youtube-nocookie.com/embed/${videoId}?autoplay=1&rel=0`;
                    }
                } else if (videoUrl.includes('youtube.com/watch?v=')) {
                    // Standard YouTube URL: https://www.youtube.com/watch?v=VIDEO_ID
                    videoId = videoUrl.split('v=')[1]?.split('&')[0];
                    if (videoId) {
                        embedUrl = `https://www.youtube-nocookie.com/embed/${videoId}?autoplay=1&rel=0`;
                    }
                } else if (videoUrl.includes('youtu.be/')) {
                    // Short YouTube URL: https://youtu.be/VIDEO_ID
                    videoId = videoUrl.split('youtu.be/')[1]?.split('?')[0];
                    if (videoId) {
                        embedUrl = `https://www.youtube-nocookie.com/embed/${videoId}?autoplay=1&rel=0`;
                    }
                } else if (videoUrl.includes('youtube.com/embed/')) {
                    // Already embed format, just add parameters
                    const baseUrl = videoUrl.split('?')[0];
                    embedUrl = `${baseUrl}?autoplay=1&rel=0`;
                } else if (videoUrl.includes('vimeo.com/')) {
                    // Vimeo URL: https://vimeo.com/VIDEO_ID
                    videoId = videoUrl.split('vimeo.com/')[1]?.split('?')[0];
                    if (videoId) {
                        embedUrl = `https://player.vimeo.com/video/${videoId}?autoplay=1`;
                    }
                } else if (videoUrl.includes('uploads/videos/')) {
                    // Uploaded video file
                    embedUrl = videoUrl;
                    console.log('Using uploaded video file:', embedUrl);
                } else {
                    // Non-YouTube/Vimeo URL, try to detect other video platforms
                    console.log('Non-YouTube/Vimeo URL, attempting to use as-is:', videoUrl);
                    embedUrl = videoUrl;
                }
                
                if (!embedUrl) {
                    throw new Error('Invalid video URL format');
                }
                
                console.log('Final embed URL:', embedUrl);
                
                // Set video info and open modal
                document.getElementById('modalVideoTitle').textContent = videoTitle;
                document.getElementById('modalVideoDescription').textContent = videoDescription;
                
                // Check if it's an uploaded video file
                if (embedUrl.includes('uploads/videos/')) {
                    // Create HTML5 video element for uploaded files
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
                    // Use iframe for YouTube/Vimeo videos
                    const videoContainer = document.querySelector('.video-container');
                    videoContainer.innerHTML = `
                        <iframe id="modalVideo" src="" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                    `;
                    
                    // Load video with proper event listeners
                    const modalVideo = document.getElementById('modalVideo');
                    modalVideo.addEventListener('load', function() {
                        console.log('Video loaded successfully');
                    });
                    
                    modalVideo.addEventListener('error', function() {
                        console.error('Video failed to load:', embedUrl);
                        showToast('Failed to load video. Please try again.', 'error');
                        closeVideoModal();
                    });
                    
                    // Set the src
                    modalVideo.src = embedUrl;
                }
                
                videoModal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
                
            } catch (error) {
                console.error('Error converting video URL:', error);
                showToast('Invalid video URL format', 'error');
                closeVideoModal();
            }
        });
    });
    
    // Close modal events
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
}

function closeVideoModal() {
    const videoModal = document.getElementById('videoModal');
    const modalVideo = document.getElementById('modalVideo');
    
    if (modalVideo) {
        modalVideo.src = '';
    }
    videoModal.style.display = 'none';
    document.body.style.overflow = 'auto';
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
