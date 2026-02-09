<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/functions.php';

// Get pagination and filter parameters
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 9;
$offset = ($page - 1) * $per_page;
$category = isset($_GET['category']) ? $_GET['category'] : 'all';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Get gallery data
if ($search) {
    $gallery_images = search_gallery_images($search, $per_page, $offset);
    $total_images = get_search_gallery_count($search);
} else {
    $gallery_images = get_gallery_images($per_page, $offset, $category);
    $total_images = get_gallery_image_count($category);
}

// Handle sorting
$sort = $_GET['sort'] ?? 'newest';
if (!empty($gallery_images)) {
    switch ($sort) {
        case 'oldest':
            usort($gallery_images, function($a, $b) {
                return strtotime($a['created_at']) - strtotime($b['created_at']);
            });
            break;
        case 'title':
            usort($gallery_images, function($a, $b) {
                return strcasecmp($a['title'], $b['title']);
            });
            break;
        case 'category':
            usort($gallery_images, function($a, $b) {
                $category_cmp = strcasecmp($a['category'], $b['category']);
                return $category_cmp !== 0 ? $category_cmp : strcasecmp($a['title'], $b['title']);
            });
            break;
        case 'newest':
        default:
            usort($gallery_images, function($a, $b) {
                return strtotime($b['created_at']) - strtotime($a['created_at']);
            });
            break;
    }
}

$categories = get_gallery_categories();
$total_pages = ceil($total_images / $per_page);
?>

<!-- Gallery Section -->
<section class="gallery-section" id="gallery">
    <div class="container">
        <div class="section-header">
            <div class="header-content">
                <h2 class="gallery-title">
                    <span class="title-gradient">Gallery</span>
                    <div class="title-underline"></div>
                </h2>
                <p class="gallery-subtitle">
                    <span class="subtitle-icon">✨</span>
                    Explore our collection of memorable moments and behind-the-scenes content
                    <span class="subtitle-icon">📸</span>
                </p>
            </div>
            <div class="header-decoration">
                <div class="decoration-circle decoration-1"></div>
                <div class="decoration-circle decoration-2"></div>
                <div class="decoration-circle decoration-3"></div>
            </div>
        </div>

        <!-- Gallery Filters -->
        <div class="gallery-filters">
            <div class="filter-buttons">
                <a href="gallery.php" class="filter-btn <?php echo $category === 'all' && !$search ? 'active' : ''; ?>">
                    <span>All</span>
                </a>
                <?php foreach ($categories as $cat): ?>
                    <a href="gallery.php?category=<?php echo urlencode($cat); ?>" 
                       class="filter-btn <?php echo $category === $cat && !$search ? 'active' : ''; ?>">
                        <span><?php echo ucfirst($cat); ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
            
            <div class="search-section">
                <div class="search-container">
                    <div class="search-wrapper">
                        <form method="GET" action="gallery.php" class="search-form" id="searchForm">
                            <div class="search-input-group">
                                <input type="text" 
                                       name="search" 
                                       id="searchInput"
                                       placeholder="Search by title, description, or category..." 
                                       value="<?php echo htmlspecialchars($search); ?>"
                                       class="search-input"
                                       autocomplete="off">
                                <button type="submit" class="search-btn" title="Search">
                                    <i class="fas fa-search"></i>
                                </button>
                                <button type="button" class="clear-search-btn" id="clearSearch" title="Clear search" style="<?php echo $search ? '' : 'display: none;'; ?>">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </form>
                        
                        <!-- Search Suggestions Dropdown -->
                        <div class="search-suggestions" id="searchSuggestions" style="display: none;">
                            <div class="suggestions-header">
                                <i class="fas fa-lightbulb"></i>
                                <span>Suggestions</span>
                            </div>
                            <div class="suggestions-list" id="suggestionsList">
                                <!-- Suggestions will be populated here -->
                            </div>
                        </div>
                    </div>
                    
                    <!-- Search Filters -->
                    <div class="search-filters-toggle" id="searchFiltersToggle">
                        <i class="fas fa-filter"></i>
                        <span>Filters</span>
                    </div>
                </div>
                
                <!-- Advanced Search Filters -->
                <div class="advanced-search-filters" id="advancedFilters" style="display: none;">
                    <div class="filters-content">
                        <div class="filter-group">
                            <label>Category</label>
                            <select name="category" id="categoryFilter" class="filter-select">
                                <option value="">All Categories</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo urlencode($cat); ?>" <?php echo $category === $cat ? 'selected' : ''; ?>>
                                        <?php echo ucfirst($cat); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="filter-group">
                            <label>Sort By</label>
                            <select name="sort" id="sortFilter" class="filter-select">
                                <option value="newest" <?php echo (($_GET['sort'] ?? 'newest') === 'newest') ? 'selected' : ''; ?>>Newest First</option>
                                <option value="oldest" <?php echo (($_GET['sort'] ?? 'newest') === 'oldest') ? 'selected' : ''; ?>>Oldest First</option>
                                <option value="title" <?php echo (($_GET['sort'] ?? 'newest') === 'title') ? 'selected' : ''; ?>>Title A-Z</option>
                                <option value="category" <?php echo (($_GET['sort'] ?? 'newest') === 'category') ? 'selected' : ''; ?>>By Category</option>
                            </select>
                        </div>
                        
                        <div class="filter-actions">
                            <button type="submit" class="apply-filters-btn">
                                <i class="fas fa-check"></i> Apply Filters
                            </button>
                            <a href="gallery.php" class="reset-filters-btn">
                                <i class="fas fa-redo"></i> Reset
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Search Results Info -->
                <?php if ($search || $category !== 'all'): ?>
                    <div class="search-results-info">
                        <div class="results-count">
                            <i class="fas fa-info-circle"></i>
                            <span>
                                <?php 
                                if ($search) {
                                    echo "Found <strong>" . $total_images . "</strong> results for <strong>'" . htmlspecialchars($search) . "'</strong>";
                                    if ($category !== 'all') {
                                        echo " in <strong>" . ucfirst($category) . "</strong>";
                                    }
                                } else {
                                    echo "Showing <strong>" . $total_images . "</strong> images in <strong>" . ucfirst($category) . "</strong>";
                                }
                                ?>
                            </span>
                        </div>
                        <a href="gallery.php" class="clear-all-btn">
                            <i class="fas fa-times"></i> Clear All
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Gallery Grid -->
        <div class="gallery-grid">
            <?php if (!empty($gallery_images)): ?>
                <?php foreach ($gallery_images as $image): ?>
                    <div class="gallery-item" data-category="<?php echo htmlspecialchars($image['category']); ?>">
                        <div class="gallery-card" onclick="openGalleryModal('<?php echo htmlspecialchars($image['image_url']); ?>', '<?php echo htmlspecialchars($image['title']); ?>', '<?php echo htmlspecialchars($image['description']); ?>')" style="cursor: pointer;">
                            <div class="gallery-image-container">
                                <img src="<?php echo htmlspecialchars($image['thumbnail_url'] ?: $image['image_url']); ?>" 
                                     alt="<?php echo htmlspecialchars($image['title']); ?>"
                                     loading="lazy"
                                     class="gallery-image">
                            </div>
                            <div class="gallery-info">
                                <h4><?php echo htmlspecialchars($image['title']); ?></h4>
                                <div class="gallery-meta">
                                    <span class="gallery-category">
                                        <i class="fas fa-folder"></i>
                                        <?php echo ucfirst(htmlspecialchars($image['category'])); ?>
                                    </span>
                                    <span class="gallery-date">
                                        <i class="fas fa-calendar"></i>
                                        <?php echo format_date($image['created_at'], 'M j, Y'); ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-gallery">
                    <div class="no-gallery-content">
                        <i class="fas fa-images"></i>
                        <h3>No gallery images found</h3>
                        <p><?php echo $search ? 'Try adjusting your search terms' : 'No images available in this category'; ?></p>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <div class="pagination-container">
                <div class="pagination-wrapper">
                    <?php if ($page > 1): ?>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>" 
                           class="pagination-btn pagination-prev">
                            <i class="fas fa-chevron-left"></i>
                            <span>Previous</span>
                        </a>
                    <?php endif; ?>

                    <div class="pagination-numbers">
                        <?php
                        $start_page = max(1, $page - 2);
                        $end_page = min($total_pages, $page + 2);
                        
                        if ($start_page > 1) {
                            echo '<a href="?'.http_build_query(array_merge($_GET, ['page' => 1])).'" class="pagination-number">1</a>';
                            if ($start_page > 2) {
                                echo '<span class="pagination-dots">...</span>';
                            }
                        }
                        
                        for ($i = $start_page; $i <= $end_page; $i++) {
                            $active_class = $i == $page ? 'active' : '';
                            echo '<a href="?'.http_build_query(array_merge($_GET, ['page' => $i])).'" class="pagination-number '.$active_class.'">'.$i.'</a>';
                        }
                        
                        if ($end_page < $total_pages) {
                            if ($end_page < $total_pages - 1) {
                                echo '<span class="pagination-dots">...</span>';
                            }
                            echo '<a href="?'.http_build_query(array_merge($_GET, ['page' => $total_pages])).'" class="pagination-number">'.$total_pages.'</a>';
                        }
                        ?>
                    </div>

                    <?php if ($page < $total_pages): ?>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>" 
                           class="pagination-btn pagination-next">
                            <span>Next</span>
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    <?php endif; ?>
                </div>
                
                <div class="pagination-info">
                    <span class="pagination-text">
                        Showing <strong><?php echo min(($page - 1) * $per_page + 1, $total_images); ?></strong> to 
                        <strong><?php echo min($page * $per_page, $total_images); ?></strong> of 
                        <strong><?php echo $total_images; ?></strong> images
                    </span>
                    <span class="pagination-pages">
                        Page <strong><?php echo $page; ?></strong> of <strong><?php echo $total_pages; ?></strong>
                    </span>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Gallery Modal -->
<div id="galleryModal" class="gallery-modal">
    <div class="modal-content">
        <span class="close-modal" onclick="closeGalleryModal()">&times;</span>
        <img id="modalImage" src="" alt="" class="modal-image">
        <div class="modal-info">
            <h3 id="modalTitle"></h3>
            <p id="modalDescription"></p>
        </div>
    </div>
</div>

<style>
/* Gallery Section */
.gallery-section {
    padding: 5rem 0;
    background: var(--dark-bg);
    position: relative;
    overflow: hidden;
}

.gallery-section::before {
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

.gallery-section::after {
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

.gallery-title {
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

.gallery-subtitle {
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

.gallery-filters {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 3rem;
    flex-wrap: wrap;
    gap: 2rem;
}

.filter-buttons {
    display: flex;
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

.search-section {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 3rem;
    flex-wrap: wrap;
    gap: 1rem;
    padding: 2.5rem;
    background: 
        linear-gradient(135deg, rgba(255, 255, 255, 0.05) 0%, rgba(255, 255, 255, 0.02) 100%),
        radial-gradient(circle at 30% 20%, rgba(255, 107, 107, 0.1) 0%, transparent 50%),
        radial-gradient(circle at 70% 80%, rgba(78, 205, 196, 0.1) 0%, transparent 50%);
    border-radius: 25px;
    border: 2px solid rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(15px);
    position: relative;
    overflow: hidden;
    box-shadow: 
        0 15px 35px rgba(0, 0, 0, 0.3),
        0 5px 15px rgba(255, 107, 107, 0.1),
        inset 0 1px 0 rgba(255, 255, 255, 0.1);
}

.search-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(90deg, 
        transparent 0%, 
        rgba(255, 107, 107, 0.05) 25%, 
        rgba(78, 205, 196, 0.05) 50%, 
        rgba(69, 183, 209, 0.05) 75%, 
        transparent 100%);
    background-size: 200% 100%;
    animation: searchShimmer 8s linear infinite;
    pointer-events: none;
}

@keyframes searchShimmer {
    0% { background-position: -200% 0; }
    100% { background-position: 200% 0; }
}

.search-container {
    flex: 1;
    max-width: 700px;
    position: relative;
    z-index: 2;
}

.search-wrapper {
    position: relative;
}

.search-form {
    display: flex;
    background: linear-gradient(145deg, #1a1a1a 0%, #2d2d2d 100%);
    border-radius: 35px;
    border: 2px solid #444444;
    background-clip: padding-box;
    overflow: hidden;
    box-shadow: 
        0 10px 40px rgba(0, 0, 0, 0.4),
        0 4px 20px rgba(100, 100, 100, 0.2),
        inset 0 1px 0 rgba(255, 255, 255, 0.1);
    transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
}

.search-form::before {
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
}

.search-form:focus-within {
    transform: translateY(-3px);
    box-shadow: 
        0 15px 50px rgba(100, 100, 100, 0.4),
        0 8px 25px rgba(255, 107, 107, 0.3),
        inset 0 1px 0 rgba(255, 255, 255, 0.2);
    border-color: #666666;
}

.search-input-group {
    display: flex;
    align-items: center;
    flex: 1;
    position: relative;
    z-index: 1;
}

.search-input {
    flex: 1;
    background: transparent;
    border: none;
    padding: 1.5rem 2.5rem;
    color: var(--text-primary);
    outline: none;
    font-size: 1.1rem;
    min-width: 300px;
    font-weight: 500;
    font-family: 'Inter', sans-serif;
}

.search-input::placeholder {
    color: var(--text-muted);
    font-weight: 400;
    font-style: italic;
}

.search-btn {
    background: linear-gradient(135deg, #4ecdc4, #45b7d1);
    border: none;
    padding: 1.5rem 2.5rem;
    color: #ffffff;
    cursor: pointer;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    font-size: 1.1rem;
    border-radius: 0 30px 30px 0;
    position: relative;
    z-index: 1;
    box-shadow: 0 6px 20px rgba(78, 205, 196, 0.4);
    font-weight: 600;
}

.search-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    transition: left 0.6s ease;
}

.search-btn:hover {
    transform: translateY(-2px) scale(1.02);
    box-shadow: 0 8px 25px rgba(78, 205, 196, 0.5);
    background: linear-gradient(135deg, #45b7d1, #4ecdc4);
}

.search-btn:hover::before {
    left: 100%;
}

.search-btn:active {
    transform: translateY(0);
}

.clear-search-btn {
    background: transparent;
    border: none;
    padding: 1.5rem;
    color: var(--text-muted);
    cursor: pointer;
    transition: all 0.3s ease;
    border-radius: 0;
    position: relative;
    z-index: 1;
    font-size: 1rem;
}

.clear-search-btn:hover {
    color: var(--error-color);
    background: rgba(255, 107, 107, 0.1);
    transform: scale(1.1);
}

.clear-search-btn:active {
    transform: scale(0.95);
}

/* Search Suggestions */
.search-suggestions {
    position: absolute;
    top: calc(100% + 10px);
    left: 0;
    right: 0;
    background: linear-gradient(145deg, #1a1a1a 0%, #2d2d2d 100%);
    border: 2px solid #444444;
    border-radius: 20px;
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.3), 0 4px 12px rgba(100, 100, 100, 0.1);
    z-index: 1000;
    max-height: 350px;
    overflow-y: auto;
    backdrop-filter: blur(10px);
}

.search-suggestions::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    border-radius: 20px;
    padding: 2px;
    background: linear-gradient(90deg, #ff6b6b, #4ecdc4, #45b7d1, #ff6b6b);
    background-size: 300% 100%;
    animation: shimmerGradient 4s linear infinite;
    -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
    mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
    -webkit-mask-composite: xor;
    mask-composite: exclude;
}

.suggestions-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1.25rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    color: var(--text-muted);
    font-size: 0.9rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.suggestions-list {
    padding: 0.75rem;
}

.suggestion-item {
    padding: 1rem 1.5rem;
    color: #ffffff;
    cursor: pointer;
    border-radius: 15px;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 1rem;
    margin: 0.25rem;
    position: relative;
}

.suggestion-item::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 3px;
    background: linear-gradient(180deg, #4ecdc4, #45b7d1);
    border-radius: 15px 0 0 15px;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.suggestion-item:hover {
    background: linear-gradient(135deg, rgba(78, 205, 196, 0.2) 0%, rgba(69, 183, 209, 0.1) 100%);
    color: #4ecdc4;
    transform: translateX(5px);
}

.suggestion-item:hover::before {
    opacity: 1;
}

.suggestion-item i {
    color: #4ecdc4;
    font-size: 1rem;
    width: 20px;
    text-align: center;
}

/* Search Filters Toggle */
.search-filters-toggle {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    margin-top: 1.5rem;
    background: #2d2d2d;
    border: 1px solid #444444;
    border-radius: 20px;
    cursor: pointer;
    transition: all 0.3s ease;
    color: #cccccc;
    font-weight: 500;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
    position: relative;
    overflow: hidden;
}

.search-filters-toggle:hover {
    background: #3a3a3a;
    border-color: #666666;
    color: #ffffff;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
}

.search-filters-toggle:active {
    transform: translateY(0);
}

/* Advanced Search Filters */
.advanced-search-filters {
    width: 100%;
    background: linear-gradient(145deg, #1a1a1a 0%, #2d2d2d 100%);
    border: 2px solid #444444;
    border-radius: 25px;
    padding: 2.5rem;
    margin-top: 2rem;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3), 0 4px 12px rgba(100, 100, 100, 0.1);
    backdrop-filter: blur(10px);
    position: relative;
    overflow: hidden;
}

.advanced-search-filters::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    border-radius: 25px;
    padding: 2px;
    background: linear-gradient(90deg, #ff6b6b, #4ecdc4, #45b7d1, #ff6b6b);
    background-size: 300% 100%;
    animation: shimmerGradient 4s linear infinite;
    -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
    mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
    -webkit-mask-composite: xor;
    mask-composite: exclude;
}

.filters-content {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 2.5rem;
    align-items: end;
    position: relative;
    z-index: 1;
}

.filter-group {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.filter-group label {
    color: #ffffff;
    font-weight: 600;
    font-size: 0.95rem;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.filter-select {
    background: #000000;
    color: #ffffff;
    border: 2px solid #444444;
    padding: 1rem 1.5rem;
    border-radius: 15px;
    outline: none;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 1rem;
    font-weight: 500;
    position: relative;
}

.filter-select::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    border-radius: 15px;
    padding: 2px;
    background: linear-gradient(90deg, #4ecdc4, #45b7d1);
    background-size: 200% 100%;
    animation: shimmerGradient 3s linear infinite;
    -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
    mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
    -webkit-mask-composite: xor;
    mask-composite: exclude;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.filter-select:focus {
    border-color: #4ecdc4;
}

.filter-select:focus::before {
    opacity: 1;
}

.filter-actions {
    display: flex;
    gap: 1.5rem;
    align-items: center;
    justify-content: center;
}

.apply-filters-btn,
.reset-filters-btn {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem 2rem;
    border-radius: 15px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    cursor: pointer;
    border: none;
    font-size: 1rem;
    text-transform: uppercase;
    letter-spacing: 1px;
    position: relative;
    overflow: hidden;
}

.apply-filters-btn {
    background: linear-gradient(135deg, #4ecdc4, #45b7d1);
    color: #ffffff;
    box-shadow: 0 6px 20px rgba(78, 205, 196, 0.3);
}

.apply-filters-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    transition: left 0.4s ease;
}

.apply-filters-btn:hover {
    transform: translateY(-3px) scale(1.05);
    box-shadow: 0 10px 30px rgba(78, 205, 196, 0.4);
}

.apply-filters-btn:hover::before {
    left: 100%;
}

.reset-filters-btn {
    background: transparent;
    color: #888888;
    border: 2px solid #444444;
}

.reset-filters-btn:hover {
    background: #ff6b6b;
    color: #ffffff;
    border-color: #ff6b6b;
    transform: translateY(-3px) scale(1.05);
    box-shadow: 0 10px 30px rgba(255, 107, 107, 0.2);
}

/* Search Results Info */
.search-results-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem 2rem;
    background: linear-gradient(135deg, rgba(255, 107, 107, 0.15) 0%, rgba(255, 107, 107, 0.05) 100%);
    border: 2px solid rgba(255, 107, 107, 0.3);
    border-radius: 20px;
    margin-top: 2rem;
    backdrop-filter: blur(10px);
    position: relative;
    overflow: hidden;
}

.search-results-info::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    border-radius: 20px;
    padding: 2px;
    background: linear-gradient(90deg, var(--primary-color), var(--secondary-color), var(--primary-color));
    background-size: 200% 100%;
    animation: shimmerGradient 4s linear infinite;
    -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
    mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
    -webkit-mask-composite: xor;
    mask-composite: exclude;
}

.results-count {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    color: var(--text-primary);
    font-size: 1rem;
    font-weight: 600;
    position: relative;
    z-index: 1;
}

.results-count i {
    color: var(--primary-color);
    font-size: 1.2rem;
}

.clear-all-btn {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: var(--error-color);
    color: var(--text-primary);
    text-decoration: none;
    border-radius: 8px;
    font-size: 0.8rem;
    font-weight: 600;
    transition: all 0.3s ease;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    box-shadow: 0 2px 8px rgba(255, 107, 107, 0.2);
    position: relative;
    z-index: 1;
}

.clear-all-btn:hover {
    background: #c62828;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(255, 107, 107, 0.3);
}

/* Modern Pagination */
.pagination-container {
    margin-top: 3rem;
    padding: 2rem;
    background: linear-gradient(145deg, rgba(255, 255, 255, 0.03) 0%, rgba(255, 255, 255, 0.01) 100%);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 20px;
    backdrop-filter: blur(10px);
}

.pagination-wrapper {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
    gap: 1rem;
}

.pagination-numbers {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.pagination-btn {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    background: linear-gradient(135deg, #2d2d2d 0%, #1a1a1a 100%);
    color: #cccccc;
    text-decoration: none;
    border: 1px solid #444444;
    border-radius: 12px;
    font-size: 0.9rem;
    font-weight: 500;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.pagination-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(78, 205, 196, 0.2), transparent);
    transition: left 0.4s ease;
}

.pagination-btn:hover {
    background: linear-gradient(135deg, #4ecdc4 0%, #45b7d1 100%);
    color: #ffffff;
    border-color: #4ecdc4;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(78, 205, 196, 0.3);
}

.pagination-btn:hover::before {
    left: 100%;
}

.pagination-btn:active {
    transform: translateY(0);
}

.pagination-number {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    background: linear-gradient(145deg, #2d2d2d 0%, #1a1a1a 100%);
    color: #cccccc;
    text-decoration: none;
    border: 1px solid #444444;
    border-radius: 10px;
    font-size: 0.9rem;
    font-weight: 600;
    transition: all 0.3s ease;
    position: relative;
}

.pagination-number:hover {
    background: linear-gradient(135deg, #4ecdc4 0%, #45b7d1 100%);
    color: #ffffff;
    border-color: #4ecdc4;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(78, 205, 196, 0.3);
}

.pagination-number.active {
    background: linear-gradient(135deg, #ff6b6b 0%, #f06292 100%);
    color: #ffffff;
    border-color: #ff6b6b;
    box-shadow: 0 4px 12px rgba(255, 107, 107, 0.3);
    transform: scale(1.1);
}

.pagination-dots {
    color: #888888;
    font-size: 1.2rem;
    font-weight: bold;
    padding: 0 0.5rem;
}

.pagination-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 0;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    flex-wrap: wrap;
    gap: 1rem;
}

.pagination-text,
.pagination-pages {
    color: #cccccc;
    font-size: 0.9rem;
}

.pagination-text strong,
.pagination-pages strong {
    color: #4ecdc4;
    font-weight: 600;
}

/* Responsive Pagination */
@media (max-width: 768px) {
    .pagination-wrapper {
        flex-direction: column;
        gap: 1.5rem;
    }
    
    .pagination-numbers {
        order: -1;
        flex-wrap: wrap;
        justify-content: center;
    }
    
    .pagination-btn {
        width: 100%;
        justify-content: center;
    }
    
    .pagination-info {
        flex-direction: column;
        text-align: center;
        gap: 0.5rem;
    }
}

@media (max-width: 480px) {
    .pagination-container {
        padding: 1.5rem;
        margin-top: 2rem;
    }
    
    .pagination-number {
        width: 35px;
        height: 35px;
        font-size: 0.8rem;
    }
    
    .pagination-btn {
        padding: 0.6rem 1rem;
        font-size: 0.8rem;
    }
}

/* Gallery Grid */
.gallery-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 2.5rem;
    margin-bottom: 3rem;
    position: relative;
    z-index: 1;
}

.gallery-item {
    transform: translateZ(0);
    perspective: 1000px;
}

.gallery-card {
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

.gallery-card::before {
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

.gallery-card::after {
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

.gallery-card:hover {
    transform: translateY(-20px) rotateX(5deg) scale(1.03);
    box-shadow: 
        0 40px 80px rgba(255, 107, 107, 0.3),
        0 10px 30px rgba(78, 205, 196, 0.2),
        0 5px 15px rgba(69, 183, 209, 0.1);
    border-color: rgba(255, 107, 107, 0.4);
}

.gallery-card:hover::after {
    opacity: 1;
}

.gallery-card:hover .gallery-overlay {
    opacity: 0;
}

.gallery-image-container {
    position: relative;
    aspect-ratio: 16/10;
    overflow: hidden;
    background: linear-gradient(45deg, #1a1a1a, #2a2a2a);
}

.gallery-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
    filter: brightness(0.9);
}

.gallery-card:hover .gallery-image {
    transform: scale(1.1) rotate(2deg);
    filter: brightness(1.1);
}

.gallery-content {
    width: 100%;
    transform: translateY(20px);
    transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
}

.gallery-card:hover .gallery-content {
    transform: translateY(0);
}

.gallery-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1rem;
}

.gallery-header h3 {
    color: var(--text-primary);
    margin: 0;
    font-size: 1.4rem;
    font-weight: 800;
    text-shadow: 0 2px 8px rgba(0, 0, 0, 0.5);
    line-height: 1.2;
    background: linear-gradient(135deg, #ffffff, #e0e0e0);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.gallery-category-badge {
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
}

.gallery-category-badge:hover {
    transform: translateY(-2px) scale(1.05);
    box-shadow: 0 6px 20px rgba(255, 107, 107, 0.6);
}

.gallery-content p {
    color: var(--text-secondary);
    font-size: 0.95rem;
    margin-bottom: 1.5rem;
    line-height: 1.5;
    opacity: 0.9;
}

.gallery-actions {
    display: flex;
    justify-content: center;
}

.view-btn {
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    color: var(--text-primary);
    border: none;
    padding: 1rem 2rem;
    border-radius: 30px;
    cursor: pointer;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    font-size: 0.9rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    box-shadow: 0 6px 20px rgba(255, 107, 107, 0.4);
    text-transform: uppercase;
    letter-spacing: 1.5px;
    position: relative;
    overflow: hidden;
}

.view-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    transition: left 0.6s ease;
}

.view-btn:hover {
    transform: translateY(-5px) scale(1.05);
    box-shadow: 0 12px 30px rgba(255, 107, 107, 0.6);
}

.view-btn:hover::before {
    left: 100%;
}

.view-btn i {
    font-size: 0.9rem;
    transition: transform 0.3s ease;
}

.view-btn:hover i {
    transform: translateX(3px);
}

.gallery-info {
    padding: 2.5rem;
    background: linear-gradient(180deg, rgba(0, 0, 0, 0.3) 0%, transparent 100%);
    position: relative;
    z-index: 2;
}

.gallery-info h4 {
    color: var(--text-primary);
    margin: 0 0 1.5rem 0;
    font-size: 1.3rem;
    font-weight: 700;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
    background: linear-gradient(135deg, #ffffff, #f0f0f0);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.gallery-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
}

.gallery-category,
.gallery-date {
    color: var(--text-muted);
    font-size: 0.85rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-weight: 500;
}

.gallery-category i,
.gallery-date i {
    font-size: 0.8rem;
    color: var(--primary-color);
    transition: color 0.3s ease;
}

.gallery-card:hover .gallery-category i,
.gallery-card:hover .gallery-date i {
    color: var(--secondary-color);
}

@media (max-width: 1200px) {
    .gallery-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
    }
}

@media (max-width: 768px) {
    .gallery-grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
}

.gallery-item {
    transform: translateZ(0);
}

.gallery-card {
    background: linear-gradient(145deg, var(--dark-secondary) 0%, var(--dark-tertiary) 100%);
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3), 0 2px 8px rgba(255, 107, 107, 0.1);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    border: 1px solid rgba(255, 255, 255, 0.1);
    position: relative;
}

.gallery-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 2px;
    background: linear-gradient(90deg, var(--primary-color), var(--secondary-color), var(--primary-color));
    background-size: 200% 100%;
    animation: shimmerGradient 3s linear infinite;
}

@keyframes shimmerGradient {
    0% { background-position: -200% 0; }
    100% { background-position: 200% 0; }
}

.gallery-card:hover {
    transform: translateY(-15px) scale(1.02);
    box-shadow: 0 25px 50px rgba(255, 107, 107, 0.3), 0 5px 15px rgba(255, 107, 107, 0.2);
    border-color: rgba(255, 107, 107, 0.3);
}

.gallery-card:hover .gallery-overlay {
    opacity: 0;
}

.gallery-image-container {
    position: relative;
    aspect-ratio: 16/10;
    overflow: hidden;
}

.gallery-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.gallery-card:hover .gallery-image {
    transform: scale(1.05);
}

.gallery-content {
    width: 100%;
    transform: translateY(20px);
    transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.gallery-card:hover .gallery-content {
    transform: translateY(0);
}

.gallery-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1rem;
}

.gallery-header h3 {
    color: var(--text-primary);
    margin: 0;
    font-size: 1.3rem;
    font-weight: 700;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
    line-height: 1.2;
}

.gallery-category-badge {
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    color: var(--text-primary);
    padding: 0.3rem 0.8rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    box-shadow: 0 2px 8px rgba(255, 107, 107, 0.3);
}

.gallery-content p {
    color: var(--text-secondary);
    font-size: 0.9rem;
    margin-bottom: 1.5rem;
    line-height: 1.4;
    opacity: 0.9;
}

.gallery-actions {
    display: flex;
    justify-content: center;
}

.view-btn {
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    color: var(--text-primary);
    border: none;
    padding: 0.8rem 1.5rem;
    border-radius: 25px;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 0.9rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    box-shadow: 0 4px 12px rgba(255, 107, 107, 0.3);
    text-transform: uppercase;
    letter-spacing: 1px;
}

.view-btn:hover {
    transform: translateY(-3px) scale(1.05);
    box-shadow: 0 8px 20px rgba(255, 107, 107, 0.4);
}

.view-btn i {
    font-size: 0.8rem;
}

.gallery-info {
    padding: 2rem;
    background: linear-gradient(180deg, rgba(0, 0, 0, 0.2) 0%, transparent 100%);
}

.gallery-info h4 {
    color: var(--text-primary);
    margin: 0 0 1rem 0;
    font-size: 1.2rem;
    font-weight: 600;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
}

.gallery-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
}

.gallery-category,
.gallery-date {
    color: var(--text-muted);
    font-size: 0.85rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.gallery-category i,
.gallery-date i {
    font-size: 0.75rem;
    color: var(--primary-color);
}

.no-gallery {
    grid-column: 1 / -1;
    text-align: center;
    padding: 6rem 2rem;
    background: 
        linear-gradient(145deg, var(--dark-secondary) 0%, var(--dark-tertiary) 100%),
        radial-gradient(circle at 50% 50%, rgba(255, 107, 107, 0.05) 0%, transparent 50%);
    border-radius: 25px;
    border: 2px solid rgba(255, 255, 255, 0.1);
    position: relative;
    overflow: hidden;
    box-shadow: 
        0 20px 40px rgba(0, 0, 0, 0.3),
        inset 0 1px 0 rgba(255, 255, 255, 0.1);
}

.no-gallery::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(90deg, 
        transparent 0%, 
        rgba(255, 107, 107, 0.05) 25%, 
        rgba(78, 205, 196, 0.05) 50%, 
        rgba(69, 183, 209, 0.05) 75%, 
        transparent 100%);
    background-size: 200% 100%;
    animation: shimmerGradient 4s linear infinite;
    pointer-events: none;
}

.no-gallery-content {
    max-width: 400px;
    margin: 0 auto;
    position: relative;
    z-index: 2;
}

.no-gallery i {
    font-size: 5rem;
    color: var(--text-muted);
    margin-bottom: 2rem;
    opacity: 0.6;
    animation: iconPulse 3s ease-in-out infinite;
    display: block;
}

@keyframes iconPulse {
    0%, 100% { 
        transform: scale(1);
        opacity: 0.6;
    }
    50% { 
        transform: scale(1.1);
        opacity: 0.8;
    }
}

.no-gallery h3 {
    color: var(--text-primary);
    margin-bottom: 1rem;
    font-size: 1.5rem;
    font-weight: 700;
    background: linear-gradient(135deg, #ffffff, #e0e0e0);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.no-gallery p {
    color: var(--text-secondary);
    line-height: 1.6;
    font-size: 1.1rem;
}

/* Gallery Modal */
.gallery-modal {
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
    position: relative;
    margin: 5% auto;
    padding: 2.5rem;
    width: 90%;
    max-width: 800px;
    background: 
        linear-gradient(145deg, var(--dark-secondary) 0%, var(--dark-tertiary) 100%),
        radial-gradient(circle at 30% 30%, rgba(255, 107, 107, 0.05) 0%, transparent 50%);
    border-radius: 25px;
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
    right: 1.5rem;
    top: 1.5rem;
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
}

.close-modal:hover {
    background: var(--error-color);
    transform: rotate(90deg) scale(1.1);
}

.modal-image {
    width: 100%;
    height: auto;
    border-radius: 15px;
    margin-bottom: 1.5rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
}

.modal-info h3 {
    color: var(--text-primary);
    margin-bottom: 1rem;
    font-size: 1.5rem;
    font-weight: 700;
    background: linear-gradient(135deg, #ffffff, #e0e0e0);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.modal-info p {
    color: var(--text-secondary);
    line-height: 1.6;
    font-size: 1.1rem;
}

/* Responsive Design */
@media (max-width: 768px) {
    .gallery-filters {
        flex-direction: column;
        align-items: stretch;
        gap: 1rem;
    }

    .filter-buttons {
        justify-content: center;
    }

    .search-form {
        max-width: 100%;
    }

    .search-input {
        width: 100%;
        flex: 1;
    }

    .gallery-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }

    .modal-content {
        margin: 10% auto;
        width: 95%;
        padding: 1rem;
    }
    
    .search-section {
        padding: 1.5rem;
    }
    
    .filters-content {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
    
    .filter-actions {
        flex-direction: column;
        gap: 1rem;
        align-items: stretch;
    }
    
    .apply-filters-btn,
    .reset-filters-btn {
        width: 100%;
        justify-content: center;
        padding: 1rem 1.5rem;
        font-size: 0.9rem;
    }
}

@media (max-width: 480px) {
    .gallery-grid {
        grid-template-columns: 1fr;
    }
    
    .search-container {
        max-width: 100%;
    }
    
    .search-input {
        min-width: 200px;
    }
    
    .filter-actions {
        flex-direction: column;
        gap: 0.75rem;
    }
    
    .apply-filters-btn,
    .reset-filters-btn {
        padding: 0.8rem 1rem;
        font-size: 0.85rem;
        min-height: 44px; /* Touch-friendly size */
    }
}
</style>

<script>
// Enhanced Search Functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const clearSearchBtn = document.getElementById('clearSearch');
    const searchSuggestions = document.getElementById('searchSuggestions');
    const suggestionsList = document.getElementById('suggestionsList');
    const searchFiltersToggle = document.getElementById('searchFiltersToggle');
    const advancedFilters = document.getElementById('advancedFilters');
    
    // Sample suggestions (you can make this dynamic from database)
    const suggestions = [
        { text: 'performances', icon: 'fa-music', type: 'category' },
        { text: 'behind-scenes', icon: 'fa-video', type: 'category' },
        { text: 'photoshoot', icon: 'fa-camera', type: 'category' },
        { text: 'concert', icon: 'fa-microphone', type: 'keyword' },
        { text: 'live', icon: 'fa-broadcast-tower', type: 'keyword' },
        { text: 'studio', icon: 'fa-headphones', type: 'keyword' }
    ];
    
    // Clear search functionality
    if (clearSearchBtn) {
        clearSearchBtn.addEventListener('click', function() {
            searchInput.value = '';
            clearSearchBtn.style.display = 'none';
            hideSuggestions();
            // Submit form to clear search
            window.location.href = 'gallery.php';
        });
    }
    
    // Show/hide clear button based on input
    searchInput.addEventListener('input', function() {
        if (this.value.trim()) {
            clearSearchBtn.style.display = 'block';
            showSuggestions(this.value);
        } else {
            clearSearchBtn.style.display = 'none';
            hideSuggestions();
        }
    });
    
    // Search suggestions
    function showSuggestions(query) {
        if (query.length < 2) {
            hideSuggestions();
            return;
        }
        
        const filteredSuggestions = suggestions.filter(s => 
            s.text.toLowerCase().includes(query.toLowerCase())
        );
        
        if (filteredSuggestions.length > 0) {
            suggestionsList.innerHTML = '';
            filteredSuggestions.forEach(suggestion => {
                const item = document.createElement('div');
                item.className = 'suggestion-item';
                item.innerHTML = `
                    <i class="fas ${suggestion.icon}"></i>
                    <span>${suggestion.text}</span>
                `;
                item.addEventListener('click', function() {
                    searchInput.value = suggestion.text;
                    document.getElementById('searchForm').submit();
                });
                suggestionsList.appendChild(item);
            });
            searchSuggestions.style.display = 'block';
        } else {
            hideSuggestions();
        }
    }
    
    function hideSuggestions() {
        searchSuggestions.style.display = 'none';
    }
    
    // Toggle advanced filters
    searchFiltersToggle.addEventListener('click', function() {
        const isVisible = advancedFilters.style.display !== 'none';
        advancedFilters.style.display = isVisible ? 'none' : 'block';
        
        // Update toggle button text
        this.querySelector('span').textContent = isVisible ? 'Filters' : 'Hide Filters';
    });
    
    // Apply filters functionality
    const applyFiltersBtn = document.querySelector('.apply-filters-btn');
    if (applyFiltersBtn) {
        applyFiltersBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Get current search term
            const searchValue = searchInput.value;
            
            // Get filter values
            const categoryValue = document.getElementById('categoryFilter').value;
            const sortValue = document.getElementById('sortFilter').value;
            
            // Build URL with parameters
            const params = new URLSearchParams();
            
            if (searchValue) {
                params.set('search', searchValue);
            }
            
            if (categoryValue) {
                params.set('category', categoryValue);
            }
            
            if (sortValue) {
                params.set('sort', sortValue);
            }
            
            // Redirect to new URL
            const newUrl = 'gallery.php' + (params.toString() ? '?' + params.toString() : '');
            window.location.href = newUrl;
        });
    }
    
    // Hide suggestions when clicking outside
    document.addEventListener('click', function(event) {
        if (!searchInput.contains(event.target) && 
            !searchSuggestions.contains(event.target)) {
            hideSuggestions();
        }
    });
    
    // Keyboard navigation for suggestions
    searchInput.addEventListener('keydown', function(event) {
        const items = suggestionsList.querySelectorAll('.suggestion-item');
        let currentIndex = -1;
        
        // Find currently selected item
        items.forEach((item, index) => {
            if (item.classList.contains('selected')) {
                currentIndex = index;
            }
        });
        
        switch (event.key) {
            case 'ArrowDown':
                event.preventDefault();
                if (currentIndex < items.length - 1) {
                    if (currentIndex >= 0) items[currentIndex].classList.remove('selected');
                    items[currentIndex + 1].classList.add('selected');
                }
                break;
            case 'ArrowUp':
                event.preventDefault();
                if (currentIndex > 0) {
                    items[currentIndex].classList.remove('selected');
                    items[currentIndex - 1].classList.add('selected');
                }
                break;
            case 'Enter':
                event.preventDefault();
                if (currentIndex >= 0) {
                    items[currentIndex].click();
                }
                break;
            case 'Escape':
                hideSuggestions();
                searchInput.blur();
                break;
        }
    });
    
    // Add selected class styling
    const style = document.createElement('style');
    style.textContent = `
        .suggestion-item.selected {
            background: rgba(255, 107, 107, 0.2) !important;
            color: var(--primary-color) !important;
        }
    `;
    document.head.appendChild(style);
});

function openGalleryModal(imageUrl, title, description) {
    const modal = document.getElementById('galleryModal');
    const modalImage = document.getElementById('modalImage');
    const modalTitle = document.getElementById('modalTitle');
    const modalDescription = document.getElementById('modalDescription');

    modalImage.src = imageUrl;
    modalTitle.textContent = title;
    modalDescription.textContent = description;
    modal.style.display = 'block';
}

function closeGalleryModal() {
    document.getElementById('galleryModal').style.display = 'none';
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('galleryModal');
    if (event.target == modal) {
        closeGalleryModal();
    }
}

// Close modal with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeGalleryModal();
    }
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
