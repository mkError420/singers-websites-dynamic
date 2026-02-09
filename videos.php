<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/functions.php';

// Helper function to get category color
function getCategoryColor($categoryName) {
    $default_colors = ['#ff6b6b', '#4ecdc4', '#45b7d1', '#96ceb4', '#ffeaa7', '#dfe6e9', '#fab1a0', '#a29bfe'];
    $hash = md5($categoryName);
    $index = hexdec(substr($hash, 0, 8)) % count($default_colors);
    return $default_colors[$index];
}

// Get all videos with category information
$all_videos = get_videos();

// Get video categories from actual videos (text-based)
$categories = get_video_categories_from_videos();

// Get current category filter and search term
$current_category_name = isset($_GET['category']) ? sanitize_input($_GET['category']) : null;
$search_term = isset($_GET['search']) ? sanitize_input($_GET['search']) : null;

// Pagination settings
$videos_per_page = 9;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $videos_per_page;

// Filter videos by category and/or search
if ($search_term && $current_category_name) {
    // Both search and category
    $all_videos = get_videos_with_search_and_category($search_term, $current_category_name, $videos_per_page, $offset);
    $total_sql = "SELECT COUNT(*) as total FROM videos WHERE is_active = 1 AND (title LIKE ? OR description LIKE ?) AND category_name = ?";
    $total_result = fetchOne($total_sql, ['%' . $search_term . '%', '%' . $search_term . '%', $current_category_name]);
    $total_videos = $total_result['total'] ?? 0;
} elseif ($search_term) {
    // Search only
    $all_videos = get_videos_with_search($search_term, $videos_per_page, $offset);
    $total_sql = "SELECT COUNT(*) as total FROM videos WHERE is_active = 1 AND (title LIKE ? OR description LIKE ?)";
    $total_result = fetchOne($total_sql, ['%' . $search_term . '%', '%' . $search_term . '%']);
    $total_videos = $total_result['total'] ?? 0;
} elseif ($current_category_name) {
    // Category only
    $all_videos = get_videos_by_category_name($current_category_name, $videos_per_page, $offset);
    // Get total count for pagination
    $total_sql = "SELECT COUNT(*) as total FROM videos WHERE category_name = ? AND is_active = 1";
    $total_result = fetchOne($total_sql, [$current_category_name]);
    $total_videos = $total_result['total'] ?? 0;
} else {
    // All videos
    $all_videos = get_videos($videos_per_page, $offset);
    // Get total count for pagination
    $total_sql = "SELECT COUNT(*) as total FROM videos WHERE is_active = 1";
    $total_result = fetchOne($total_sql);
    $total_videos = $total_result['total'] ?? 0;
}

// Calculate pagination
$total_pages = ceil($total_videos / $videos_per_page);
?>

<!-- Our Videos Section -->
<section class="videos-section" id="videos">
    <div class="container">
        <div class="section-header">
            <div class="header-content">
                <h2 class="videos-title">
                    <span class="title-gradient">Our Videos</span>
                    <div class="title-underline"></div>
                </h2>
                <p class="videos-subtitle">
                    <span class="subtitle-icon">🎵</span>
                    Explore our collection of music videos and performances
                    <span class="subtitle-icon">🎬</span>
                </p>
            </div>
            <div class="header-decoration">
                <div class="decoration-circle decoration-1"></div>
                <div class="decoration-circle decoration-2"></div>
                <div class="decoration-circle decoration-3"></div>
            </div>
        </div>
        
        <!-- Filter Buttons -->
        <div class="video-filters">
            <div class="filter-buttons">
                <a href="videos.php" class="filter-btn <?php echo !$current_category_name && !isset($_GET['search']) ? 'active' : ''; ?>">
                    <span>All</span>
                </a>
                <?php foreach ($categories as $category): ?>
                    <a href="videos.php?category=<?php echo urlencode($category['name']); ?>" 
                       class="filter-btn <?php echo $current_category_name == $category['name'] && !isset($_GET['search']) ? 'active' : ''; ?>"
                       style="background-color: <?php echo $category['color']; ?>20; border-color: <?php echo $category['color']; ?>;">
                        <span><?php echo htmlspecialchars($category['name']); ?> (<?php echo $category['count']; ?>)</span>
                    </a>
                <?php endforeach; ?>
            </div>
            
            <!-- Search Bar -->
            <div class="search-bar">
                <form method="GET" action="videos.php" class="search-form-inline">
                    <input type="text" name="search" class="search-input-inline" 
                           placeholder="Search videos..." 
                           value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                    <button type="submit" class="search-btn-inline">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
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
                                <?php if (!empty($video['category_name'])): ?>
                                    <span class="video-category-badge" style="background-color: <?php echo getCategoryColor($video['category_name']); ?>;">
                                        <?php echo htmlspecialchars($video['category_name']); ?>
                                    </span>
                                <?php endif; ?>
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
        
        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <?php if ($current_page > 1): ?>
                    <a href="?page=<?php echo $current_page - 1; ?><?php echo $current_category_name ? '&category=' . urlencode($current_category_name) : ''; ?>" class="pagination-btn">
                        <i class="fas fa-chevron-left"></i>
                        Previous
                    </a>
                <?php endif; ?>
                
                <div class="pagination-info">
                    Page <?php echo $current_page; ?> of <?php echo $total_pages; ?>
                </div>
                
                <?php if ($current_page < $total_pages): ?>
                    <a href="?page=<?php echo $current_page + 1; ?><?php echo $current_category_name ? '&category=' . urlencode($current_category_name) : ''; ?>" class="pagination-btn">
                        Next
                        <i class="fas fa-chevron-right"></i>
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Video Scripts -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    initVideoFilters();
    initSimpleVideoClick();
});

function showToast(message, type = 'success') {
    const existingToast = document.querySelector('.toast');
    if (existingToast) {
        existingToast.remove();
    }
    
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.textContent = message;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.classList.add('show');
    }, 100);
    
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => {
            toast.remove();
        }, 300);
    }, 3000);
}

function initVideoFilters() {
    const filterButtons = document.querySelectorAll('.filter-btn');
    const videoItems = document.querySelectorAll('.video-item');
    
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            const filter = this.dataset.filter;
            
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
    const videoOverlays = document.querySelectorAll('.video-overlay');
    const videoContainers = document.querySelectorAll('.video-thumbnail-container');
    const videoModal = document.getElementById('videoModal');
    const closeModal = document.querySelector('.close-modal');
    
    console.log('=== VIDEO CLICK DEBUG ===');
    console.log('Found video thumbnails:', videoThumbnails.length);
    console.log('Found video overlays:', videoOverlays.length);
    console.log('Found video containers:', videoContainers.length);
    console.log('Found video modal:', videoModal);
    console.log('Found close modal:', closeModal);
    
    if (videoThumbnails.length === 0) {
        console.error('❌ No video thumbnails found!');
        return;
    }
    
    if (!videoModal) {
        console.error('❌ Video modal not found!');
        return;
    }
    
    // Function to handle video click
    function handleVideoClick(thumbnail, index) {
        return function(e) {
            console.log('🎯 THUMBNAIL CLICKED!', index);
            e.preventDefault();
            e.stopPropagation();
            
            const videoUrl = thumbnail.dataset.videoUrl;
            console.log('📹 Video URL from dataset:', videoUrl);
            
            if (!videoUrl) {
                console.error('❌ No video URL found!');
                return;
            }
            
            const videoItem = thumbnail.closest('.video-item');
            const videoTitle = videoItem ? videoItem.querySelector('.video-title').textContent : 'Unknown Video';
            const videoDescription = videoItem ? videoItem.querySelector('.video-description').textContent : '';
            
            console.log('📝 Video title:', videoTitle);
            console.log('📝 Video description:', videoDescription);
            
            try {
                let embedUrl = '';
                let videoId = '';
                
                console.log('🔄 Converting URL:', videoUrl);
                
                if (videoUrl.includes('m.youtube.com/watch?v=')) {
                    videoId = videoUrl.split('v=')[1]?.split('&')[0];
                    if (videoId) {
                        embedUrl = `https://www.youtube-nocookie.com/embed/${videoId}?autoplay=1&rel=0`;
                    }
                } else if (videoUrl.includes('youtube.com/watch?v=')) {
                    videoId = videoUrl.split('v=')[1]?.split('&')[0];
                    if (videoId) {
                        embedUrl = `https://www.youtube-nocookie.com/embed/${videoId}?autoplay=1&rel=0`;
                    }
                } else if (videoUrl.includes('youtu.be/')) {
                    videoId = videoUrl.split('youtu.be/')[1]?.split('?')[0];
                    if (videoId) {
                        embedUrl = `https://www.youtube-nocookie.com/embed/${videoId}?autoplay=1&rel=0`;
                    }
                } else if (videoUrl.includes('youtube.com/embed/')) {
                    const baseUrl = videoUrl.split('?')[0];
                    embedUrl = `${baseUrl}?autoplay=1&rel=0`;
                } else if (videoUrl.includes('vimeo.com/')) {
                    videoId = videoUrl.split('vimeo.com/')[1]?.split('?')[0];
                    if (videoId) {
                        embedUrl = `https://player.vimeo.com/video/${videoId}?autoplay=1`;
                    }
                } else if (videoUrl.includes('uploads/videos/')) {
                    embedUrl = videoUrl;
                    console.log('📁 Using uploaded video file:', embedUrl);
                } else {
                    console.log('🌐 Non-YouTube/Vimeo URL, using as-is:', videoUrl);
                    embedUrl = videoUrl;
                }
                
                if (!embedUrl) {
                    throw new Error('Invalid video URL format');
                }
                
                console.log('✅ Final embed URL:', embedUrl);
                
                // Set video info
                document.getElementById('modalVideoTitle').textContent = videoTitle;
                document.getElementById('modalVideoDescription').textContent = videoDescription;
                
                // Setup video container
                const videoContainer = document.querySelector('.video-container');
                console.log('📦 Video container:', videoContainer);
                
                if (embedUrl.includes('uploads/videos/')) {
                    videoContainer.innerHTML = `
                        <video controls autoplay style="width: 100%; height: 100%;">
                            <source src="${embedUrl}" type="video/mp4">
                            Your browser does not support video tag.
                        </video>
                    `;
                } else {
                    videoContainer.innerHTML = `
                        <iframe id="modalVideo" src="${embedUrl}" frameborder="0" 
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                allowfullscreen></iframe>
                    `;
                }
                
                console.log('🎬 Showing modal...');
                videoModal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
                console.log('✅ Modal should be visible now');
                
            } catch (error) {
                console.error('❌ Error converting video URL:', error);
                showToast('Invalid video URL format', 'error');
            }
        };
    }
    
    // Add click listeners to thumbnails
    videoThumbnails.forEach((thumbnail, index) => {
        console.log(`Thumbnail ${index}:`, thumbnail);
        console.log(`Data video URL: ${thumbnail.dataset.videoUrl}`);
        
        thumbnail.addEventListener('click', handleVideoClick(thumbnail, index));
        thumbnail.style.cursor = 'pointer';
        thumbnail.style.zIndex = '10';
    });
    
    // Add click listeners to overlays (they sit on top of thumbnails)
    videoOverlays.forEach((overlay, index) => {
        const thumbnail = overlay.parentElement.querySelector('.video-thumbnail');
        if (thumbnail) {
            overlay.addEventListener('click', handleVideoClick(thumbnail, index));
            overlay.style.cursor = 'pointer';
            overlay.style.zIndex = '15';
            console.log(`Added click listener to overlay ${index}`);
        }
    });
    
    // Add click listeners to entire video containers (fallback)
    videoContainers.forEach((container, index) => {
        const thumbnail = container.querySelector('.video-thumbnail');
        if (thumbnail) {
            container.addEventListener('click', handleVideoClick(thumbnail, index));
            container.style.cursor = 'pointer';
            container.style.position = 'relative';
            container.style.zIndex = '5';
            console.log(`Added click listener to container ${index}`);
        }
    });
    
    // Close modal events
    if (closeModal) {
        closeModal.addEventListener('click', function() {
            console.log('🔴 Close button clicked');
            closeVideoModal();
        });
    }
    
    videoModal.addEventListener('click', function(e) {
        if (e.target === videoModal) {
            console.log('🔴 Modal background clicked');
            closeVideoModal();
        }
    });
    
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && videoModal.style.display === 'flex') {
            console.log('🔴 Escape key pressed');
            closeVideoModal();
        }
    });
}

function closeVideoModal() {
    const videoModal = document.getElementById('videoModal');
    const videoContainer = document.querySelector('.video-container');
    
    if (videoContainer) {
        videoContainer.innerHTML = '';
    }
    videoModal.style.display = 'none';
    document.body.style.overflow = 'auto';
}
</script>

<style>
/* Videos Page Specific Styles */
.videos-section {
    padding: 5rem 0;
    background: var(--dark-bg);
    position: relative;
    overflow: hidden;
}

.videos-section::before {
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

.videos-section::after {
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

.videos-title {
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

.videos-subtitle {
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
.video-filters {
    margin-bottom: 3rem;
    text-align: center;
    position: relative;
    z-index: 1;
}

.filter-buttons {
    display: flex;
    justify-content: center;
    gap: 1rem;
    flex-wrap: wrap;
    position: relative;
    padding: 1rem;
    background: linear-gradient(145deg, rgba(255, 255, 255, 0.02) 0%, rgba(255, 255, 255, 0.01) 100%);
    border-radius: 20px;
    border: 1px solid rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(10px);
}

.filter-btn {
    padding: 1rem 2rem;
    background: linear-gradient(145deg, var(--dark-secondary) 0%, var(--dark-tertiary) 100%);
    color: var(--text-secondary);
    text-decoration: none;
    border-radius: 30px;
    border: 2px solid transparent;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    font-weight: 600;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 1px;
    position: relative;
    overflow: hidden;
    box-shadow: 
        0 4px 15px rgba(0, 0, 0, 0.2),
        inset 0 1px 0 rgba(255, 255, 255, 0.1);
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.filter-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
    transition: left 0.6s ease;
    z-index: 1;
}

.filter-btn::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    border-radius: 30px;
    opacity: 0;
    transition: opacity 0.4s ease;
    z-index: 0;
}

.filter-btn span {
    position: relative;
    z-index: 2;
}

.filter-btn:hover {
    transform: translateY(-3px) scale(1.05);
    border-color: rgba(255, 107, 107, 0.3);
    box-shadow: 
        0 8px 25px rgba(255, 107, 107, 0.2),
        0 4px 15px rgba(0, 0, 0, 0.3);
    color: var(--text-primary);
}

.filter-btn:hover::before {
    left: 100%;
}

.filter-btn.active,
.filter-btn:hover.active {
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    color: var(--text-primary);
    border-color: var(--primary-color);
    transform: translateY(-3px) scale(1.05);
    box-shadow: 
        0 10px 30px rgba(255, 107, 107, 0.4),
        0 5px 20px rgba(78, 205, 196, 0.3);
}

.filter-btn.active::after,
.filter-btn:hover.active::after {
    opacity: 1;
}

.filter-btn:active {
    transform: translateY(-1px) scale(1.02);
}

.video-category-badge {
    background: var(--primary-color);
    color: var(--text-primary);
    padding: 0.2rem 0.5rem;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    margin-right: 0.5rem;
    display: inline-block;
}

.video-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(380px, 1fr));
    gap: 2.5rem;
    margin-bottom: 3rem;
    position: relative;
    z-index: 1;
}

.video-item {
    background: linear-gradient(145deg, var(--dark-secondary) 0%, var(--dark-tertiary) 100%);
    border-radius: 25px;
    overflow: hidden;
    box-shadow: 
        0 20px 40px rgba(0, 0, 0, 0.4),
        0 5px 15px rgba(255, 107, 107, 0.1),
        inset 0 1px 0 rgba(255, 255, 255, 0.1);
    transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    border: 1px solid rgba(255, 255, 255, 0.1);
    position: relative;
    transform-style: preserve-3d;
}

.video-item::before {
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

.video-item::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, 
        transparent 0%, 
        rgba(255, 107, 107, 0.05) 50%, 
        transparent 100%);
    opacity: 0;
    transition: opacity 0.5s ease;
    border-radius: 25px;
    z-index: 1;
}

@keyframes shimmerGradient {
    0% { background-position: -300% 0; }
    100% { background-position: 300% 0; }
}

.video-item:hover {
    transform: translateY(-20px) rotateX(5deg) scale(1.03);
    box-shadow: 
        0 40px 80px rgba(255, 107, 107, 0.3),
        0 10px 30px rgba(78, 205, 196, 0.2),
        0 5px 15px rgba(69, 183, 209, 0.1);
    border-color: rgba(255, 107, 107, 0.4);
}

.video-item:hover::after {
    opacity: 1;
}

.video-thumbnail-container {
    position: relative;
    overflow: hidden;
    aspect-ratio: 16/9;
    background: linear-gradient(45deg, #1a1a1a, #2a2a2a);
}

.video-thumbnail {
    width: 100%;
    height: 100%;
    object-fit: cover;
    cursor: pointer;
    transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
    filter: brightness(0.9);
}

.video-item:hover .video-thumbnail {
    transform: scale(1.1) rotate(2deg);
    filter: brightness(1.1);
}

.video-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(180deg, 
        rgba(0, 0, 0, 0.7) 0%, 
        transparent 30%, 
        transparent 70%, 
        rgba(0, 0, 0, 0.7) 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.4s ease;
}

.video-item:hover .video-overlay {
    opacity: 1;
}

.play-button {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 2rem;
    cursor: pointer;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 10px 30px rgba(255, 107, 107, 0.4);
    opacity: 0;
    transform: translate(-50%, -50%) scale(0.8);
}

.video-item:hover .play-button {
    opacity: 1;
    transform: translate(-50%, -50%) scale(1);
}

.play-button:hover {
    transform: translate(-50%, -50%) scale(1.1);
    box-shadow: 0 15px 40px rgba(255, 107, 107, 0.6);
}

.play-button i {
    margin-left: 5px;
    transition: transform 0.3s ease;
}

.play-button:hover i {
    transform: translateX(3px);
}

.video-duration {
    position: absolute;
    bottom: 15px;
    right: 15px;
    background: linear-gradient(135deg, rgba(0, 0, 0, 0.9), rgba(0, 0, 0, 0.7));
    color: white;
    padding: 0.4rem 0.8rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    z-index: 3;
}

.video-info {
    padding: 2.5rem;
    background: linear-gradient(180deg, rgba(0, 0, 0, 0.3) 0%, transparent 100%);
    position: relative;
    z-index: 2;
}

.video-title {
    font-size: 1.4rem;
    margin-bottom: 1rem;
    color: var(--text-primary);
    font-weight: 700;
    line-height: 1.3;
    background: linear-gradient(135deg, #ffffff, #e0e0e0);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.video-description {
    color: var(--text-secondary);
    font-size: 0.95rem;
    margin-bottom: 1.5rem;
    line-height: 1.5;
    opacity: 0.9;
}

.video-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
    flex-wrap: wrap;
}

.video-category-badge {
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    color: var(--text-primary);
    padding: 0.4rem 1rem;
    border-radius: 25px;
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    box-shadow: 0 4px 15px rgba(255, 107, 107, 0.4);
    transition: all 0.3s ease;
    margin-right: 0;
}

.video-category-badge:hover {
    transform: translateY(-2px) scale(1.05);
    box-shadow: 0 6px 20px rgba(255, 107, 107, 0.6);
}

.video-views {
    color: var(--text-muted);
    font-size: 0.85rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 500;
}

.video-date {
    color: var(--text-muted);
    font-size: 0.85rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 500;
}

.video-views i,
.video-date i {
    color: var(--primary-color);
    font-size: 0.8rem;
    transition: color 0.3s ease;
}

.video-item:hover .video-views i,
.video-item:hover .video-date i {
    color: var(--secondary-color);
}

.video-actions {
    display: flex;
    gap: 0.5rem;
}

.video-modal {
    display: none;
    position: fixed;
    z-index: 9999;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background: 
        rgba(0, 0, 0, 0.9),
        radial-gradient(circle at 50% 50%, rgba(255, 107, 107, 0.1) 0%, transparent 50%);
    backdrop-filter: blur(15px);
    animation: modalFadeIn 0.3s ease-out;
    align-items: center;
    justify-content: center;
}

@keyframes modalFadeIn {
    from { 
        opacity: 0;
        backdrop-filter: blur(0px);
    }
    to { 
        opacity: 1;
        backdrop-filter: blur(15px);
    }
}

.modal-content {
    background: 
        linear-gradient(145deg, var(--dark-secondary) 0%, var(--dark-tertiary) 100%),
        radial-gradient(circle at 30% 30%, rgba(255, 107, 107, 0.05) 0%, transparent 50%);
    border-radius: 25px;
    max-width: 900px;
    width: 90%;
    max-height: 90vh;
    overflow-y: auto;
    position: relative;
    box-shadow: 
        0 30px 60px rgba(0, 0, 0, 0.5),
        0 10px 30px rgba(255, 107, 107, 0.2),
        inset 0 1px 0 rgba(255, 255, 255, 0.1);
    border: 2px solid rgba(255, 255, 255, 0.1);
    animation: modalSlideIn 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

@keyframes modalSlideIn {
    from { 
        opacity: 0;
        transform: translateY(-50px) scale(0.9);
    }
    to { 
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

.close-modal {
    position: absolute;
    top: 1.5rem;
    right: 1.5rem;
    color: var(--text-primary);
    font-size: 2rem;
    font-weight: bold;
    cursor: pointer;
    z-index: 1;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.1);
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.close-modal:hover {
    background: var(--error-color);
    transform: rotate(90deg) scale(1.1);
    box-shadow: 0 10px 20px rgba(255, 107, 107, 0.3);
}

.video-container {
    position: relative;
    padding-bottom: 56.25%;
    height: 0;
    overflow: hidden;
    border-radius: 15px;
    margin-bottom: 1.5rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
}

.video-container iframe {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    border-radius: 15px;
}

.modal-info {
    padding: 2.5rem;
    position: relative;
    z-index: 2;
}

.modal-info h3 {
    margin-bottom: 1rem;
    color: var(--text-primary);
    font-size: 1.5rem;
    font-weight: 700;
    background: linear-gradient(135deg, #ffffff, #e0e0e0);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.modal-info p {
    color: var(--text-secondary);
    margin-bottom: 1.5rem;
    line-height: 1.6;
    font-size: 1.1rem;
}

.modal-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
}

.modal-actions .btn {
    padding: 1rem 2rem;
    font-size: 0.9rem;
    border-radius: 25px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.modal-actions .btn-primary {
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    color: var(--text-primary);
    box-shadow: 0 6px 20px rgba(255, 107, 107, 0.4);
}

.modal-actions .btn-primary::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    transition: left 0.6s ease;
}

.modal-actions .btn-primary:hover {
    transform: translateY(-3px) scale(1.05);
    box-shadow: 0 10px 30px rgba(255, 107, 107, 0.6);
}

.modal-actions .btn-primary:hover::before {
    left: 100%;
}

.modal-actions .btn i {
    font-size: 1rem;
    transition: transform 0.3s ease;
}

.modal-actions .btn:hover i {
    transform: translateX(3px);
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

/* Pagination Styles */
.pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 1rem;
    margin-top: 3rem;
    margin-bottom: 2rem;
}

.pagination-btn {
    background: var(--dark-tertiary);
    border: 1px solid var(--border-color);
    color: var(--text-primary);
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
    font-weight: 500;
}

.pagination-btn:hover {
    background: var(--primary-color);
    border-color: var(--primary-color);
    transform: translateY(-2px);
}

.pagination-info {
    color: var(--text-secondary);
    font-size: 0.9rem;
    padding: 0.5rem 1rem;
    background: var(--dark-tertiary);
    border-radius: 8px;
    border: 1px solid var(--border-color);
}

@media (max-width: 768px) {
    .pagination {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .pagination-btn {
        width: 100%;
        justify-content: center;
    }
}

/* Search Bar Styles */
.search-bar {
    display: flex;
    flex-direction: column;
    align-items: stretch;
    gap: 1rem;
    margin: 2rem 0 3rem 0;
    flex-wrap: wrap;
    position: relative;
    z-index: 1;
}

.search-bar:not(.has-search) {
    flex-direction: row;
    justify-content: space-between;
    align-items: center;
}

.search-form-inline {
    display: flex;
    gap: 0.5rem;
    align-items: center;
    flex: 1;
    position: relative;
}

.search-input-inline {
    flex: 1;
    padding: 1.5rem 2.5rem;
    background: linear-gradient(145deg, #1a1a1a 0%, #2d2d2d 100%);
    border: 2px solid #444444;
    border-radius: 35px;
    color: var(--text-primary);
    font-size: 1.1rem;
    transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    outline: none;
    min-width: 300px;
    font-weight: 500;
    font-family: 'Inter', sans-serif;
    box-shadow: 
        0 10px 40px rgba(0, 0, 0, 0.4),
        0 4px 20px rgba(100, 100, 100, 0.2),
        inset 0 1px 0 rgba(255, 255, 255, 0.1);
    position: relative;
}

.search-input-inline::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    border-radius: 35px;
    padding: 2px;
    background: linear-gradient(90deg, #ff6b6b, #4ecdc4, #45b7d1, #ff6b6b);
    background-size: 300% 100%;
    animation: shimmerGradient 4s linear infinite;
    -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
    mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
    -webkit-mask-composite: xor;
    mask-composite: exclude;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.search-input-inline:focus {
    border-color: #666666;
    transform: translateY(-3px);
    box-shadow: 
        0 15px 50px rgba(100, 100, 100, 0.4),
        0 8px 25px rgba(255, 107, 107, 0.3),
        inset 0 1px 0 rgba(255, 255, 255, 0.2);
}

.search-input-inline:focus::before {
    opacity: 1;
}

.search-input-inline::placeholder {
    color: var(--text-muted);
    font-weight: 400;
    font-style: italic;
}

.search-btn-inline {
    background: linear-gradient(135deg, #4ecdc4, #45b7d1);
    color: #ffffff;
    border: none;
    padding: 1.5rem 2.5rem;
    border-radius: 0 30px 30px 0;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-size: 1.1rem;
    position: relative;
    z-index: 1;
    box-shadow: 0 6px 20px rgba(78, 205, 196, 0.4);
}

.search-btn-inline::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    transition: left 0.6s ease;
}

.search-btn-inline:hover {
    transform: translateY(-2px) scale(1.02);
    box-shadow: 0 8px 25px rgba(78, 205, 196, 0.5);
    background: linear-gradient(135deg, #45b7d1, #4ecdc4);
}

.search-btn-inline:hover::before {
    left: 100%;
}

.search-btn-inline:active {
    transform: translateY(0);
}
</style>

<style>
/* Toast notification styles */
.toast {
    position: fixed;
    top: 20px;
    right: 20px;
    padding: 15px 20px;
    border-radius: 8px;
    color: white;
    font-weight: 500;
    z-index: 10000;
    opacity: 0;
    transform: translateX(100%);
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
}

.toast.show {
    opacity: 1;
    transform: translateX(0);
}

.toast.success {
    background: linear-gradient(135deg, #28a745, #20c997);
}

.toast.error {
    background: linear-gradient(135deg, #dc3545, #c82333);
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

<?php require_once __DIR__ . '/includes/footer.php'; ?>
