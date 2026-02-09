<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/functions.php';

// Get pagination and filter parameters
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 12;
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

$categories = get_gallery_categories();
$total_pages = ceil($total_images / $per_page);
?>

<!-- Gallery Section -->
<section class="gallery-section" id="gallery">
    <div class="container">
        <div class="section-header">
            <h2>Gallery</h2>
            <p>Explore our collection of memorable moments and behind-the-scenes content</p>
        </div>

        <!-- Gallery Filters -->
        <div class="gallery-filters">
            <div class="filter-buttons">
                <a href="gallery.php" class="filter-btn <?php echo $category === 'all' && !$search ? 'active' : ''; ?>">
                    All
                </a>
                <?php foreach ($categories as $cat): ?>
                    <a href="gallery.php?category=<?php echo urlencode($cat); ?>" 
                       class="filter-btn <?php echo $category === $cat && !$search ? 'active' : ''; ?>">
                        <?php echo ucfirst($cat); ?>
                    </a>
                <?php endforeach; ?>
            </div>
            
            <div class="search-section">
                <form method="GET" action="gallery.php" class="search-form">
                    <input type="text" 
                           name="search" 
                           placeholder="Search gallery..." 
                           value="<?php echo htmlspecialchars($search); ?>"
                           class="search-input">
                    <button type="submit" class="search-btn">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>
        </div>

        <!-- Gallery Grid -->
        <div class="gallery-grid">
            <?php if (!empty($gallery_images)): ?>
                <?php foreach ($gallery_images as $image): ?>
                    <div class="gallery-item" data-category="<?php echo htmlspecialchars($image['category']); ?>">
                        <div class="gallery-image-container">
                            <img src="<?php echo htmlspecialchars($image['thumbnail_url'] ?: $image['image_url']); ?>" 
                                 alt="<?php echo htmlspecialchars($image['title']); ?>"
                                 loading="lazy"
                                 class="gallery-image">
                            <div class="gallery-overlay">
                                <div class="gallery-content">
                                    <h3><?php echo htmlspecialchars($image['title']); ?></h3>
                                    <p><?php echo htmlspecialchars(substr($image['description'], 0, 100)); ?>...</p>
                                    <div class="gallery-tags">
                                        <?php 
                                        $tags = explode(',', $image['tags']);
                                        foreach (array_slice($tags, 0, 3) as $tag): ?>
                                            <span class="gallery-tag"><?php echo htmlspecialchars(trim($tag)); ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                    <button class="view-image-btn" 
                                            onclick="openGalleryModal('<?php echo htmlspecialchars($image['image_url']); ?>', '<?php echo htmlspecialchars($image['title']); ?>', '<?php echo htmlspecialchars($image['description']); ?>')">
                                        <i class="fas fa-expand"></i> View
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="gallery-info">
                            <h4><?php echo htmlspecialchars($image['title']); ?></h4>
                            <span class="gallery-category"><?php echo ucfirst(htmlspecialchars($image['category'])); ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-gallery">
                    <i class="fas fa-images"></i>
                    <h3>No gallery images found</h3>
                    <p><?php echo $search ? 'Try adjusting your search terms' : 'No images available in this category'; ?></p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>" 
                       class="pagination-btn">
                        <i class="fas fa-chevron-left"></i> Previous
                    </a>
                <?php endif; ?>

                <div class="pagination-info">
                    Page <?php echo $page; ?> of <?php echo $total_pages; ?>
                </div>

                <?php if ($page < $total_pages): ?>
                    <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>" 
                       class="pagination-btn">
                        Next <i class="fas fa-chevron-right"></i>
                    </a>
                <?php endif; ?>
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
}

.filter-btn {
    padding: 0.75rem 1.5rem;
    background: var(--dark-secondary);
    color: var(--text-primary);
    text-decoration: none;
    border-radius: 25px;
    border: 1px solid var(--border-color);
    transition: all 0.3s ease;
    font-weight: 500;
}

.filter-btn:hover,
.filter-btn.active {
    background: var(--primary-color);
    color: var(--text-primary);
    transform: translateY(-2px);
}

.search-section {
    display: flex;
    align-items: center;
}

.search-form {
    display: flex;
    background: var(--dark-secondary);
    border-radius: 25px;
    border: 1px solid var(--border-color);
    overflow: hidden;
}

.search-input {
    background: transparent;
    border: none;
    padding: 0.75rem 1rem;
    color: var(--text-primary);
    outline: none;
    width: 250px;
}

.search-input::placeholder {
    color: var(--text-muted);
}

.search-btn {
    background: var(--primary-color);
    border: none;
    padding: 0.75rem 1rem;
    color: var(--text-primary);
    cursor: pointer;
    transition: background 0.3s ease;
}

.search-btn:hover {
    background: var(--secondary-color);
}

/* Gallery Grid */
.gallery-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 2rem;
    margin-bottom: 3rem;
}

.gallery-item {
    background: var(--dark-secondary);
    border-radius: 15px;
    overflow: hidden;
    box-shadow: var(--shadow-lg);
    transition: all 0.3s ease;
    border: 1px solid var(--border-color);
}

.gallery-item:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 40px rgba(255, 107, 107, 0.2);
}

.gallery-image-container {
    position: relative;
    aspect-ratio: 16/9;
    overflow: hidden;
}

.gallery-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.gallery-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(to bottom, transparent 0%, rgba(0, 0, 0, 0.8) 100%);
    display: flex;
    align-items: flex-end;
    opacity: 0;
    transition: opacity 0.3s ease;
    padding: 1.5rem;
}

.gallery-item:hover .gallery-overlay {
    opacity: 1;
}

.gallery-item:hover .gallery-image {
    transform: scale(1.1);
}

.gallery-content h3 {
    color: var(--text-primary);
    margin-bottom: 0.5rem;
    font-size: 1.1rem;
}

.gallery-content p {
    color: var(--text-secondary);
    font-size: 0.9rem;
    margin-bottom: 1rem;
}

.gallery-tags {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
    margin-bottom: 1rem;
}

.gallery-tag {
    background: var(--primary-color);
    color: var(--text-primary);
    padding: 0.25rem 0.75rem;
    border-radius: 15px;
    font-size: 0.8rem;
}

.view-image-btn {
    background: var(--primary-color);
    color: var(--text-primary);
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 0.9rem;
}

.view-image-btn:hover {
    background: var(--secondary-color);
    transform: translateY(-2px);
}

.gallery-info {
    padding: 1.5rem;
}

.gallery-info h4 {
    color: var(--text-primary);
    margin-bottom: 0.5rem;
    font-size: 1.1rem;
}

.gallery-category {
    color: var(--text-muted);
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.no-gallery {
    grid-column: 1 / -1;
    text-align: center;
    padding: 4rem 2rem;
    color: var(--text-secondary);
}

.no-gallery i {
    font-size: 4rem;
    color: var(--text-muted);
    margin-bottom: 1rem;
}

.no-gallery h3 {
    color: var(--text-primary);
    margin-bottom: 1rem;
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
    background-color: rgba(0, 0, 0, 0.9);
    backdrop-filter: blur(10px);
}

.modal-content {
    position: relative;
    margin: 5% auto;
    padding: 2rem;
    width: 90%;
    max-width: 800px;
    background: var(--dark-secondary);
    border-radius: 20px;
    box-shadow: var(--shadow-xl);
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
}

.modal-image {
    width: 100%;
    height: auto;
    border-radius: 10px;
    margin-bottom: 1.5rem;
}

.modal-info h3 {
    color: var(--text-primary);
    margin-bottom: 1rem;
    font-size: 1.5rem;
}

.modal-info p {
    color: var(--text-secondary);
    line-height: 1.6;
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
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 1rem;
    }

    .modal-content {
        margin: 10% auto;
        width: 95%;
        padding: 1rem;
    }
}

@media (max-width: 480px) {
    .gallery-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
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
        modal.style.display = 'none';
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
