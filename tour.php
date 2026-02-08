<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/functions.php';

// Get all tour dates
$allTourDates = get_tour_dates(false);
$pastTourDates = get_tour_dates(false, false);
$upcomingTourDates = get_tour_dates(true);
?>

<!-- Tour Section -->
<section class="section">
    <div class="container">
        <div class="section-title">
            <h2>Tour Dates</h2>
            <p>Join us live at a city near you</p>
        </div>
        
        <!-- Tour Stats -->
        <div class="tour-stats">
            <div class="stat-card">
                <div class="stat-number"><?php echo count($upcomingTourDates); ?></div>
                <div class="stat-label">Upcoming Shows</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo count($pastTourDates); ?></div>
                <div class="stat-label">Past Shows</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">15</div>
                <div class="stat-label">Countries</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">50K+</div>
                <div class="stat-label">Fans</div>
            </div>
        </div>
        
        <!-- Tour Filters -->
        <div class="tour-filters">
            <div class="filter-buttons">
                <button class="filter-btn active" data-filter="upcoming">Upcoming</button>
                <button class="filter-btn" data-filter="past">Past Shows</button>
                <button class="filter-btn" data-filter="all">All Dates</button>
            </div>
        </div>
        
        <!-- Upcoming Tour Dates -->
        <div id="upcoming-tours" class="tour-section">
            <h3 class="tour-section-title">Upcoming Tour Dates</h3>
            <div class="tour-dates">
                <?php if (!empty($upcomingTourDates)): ?>
                    <?php foreach ($upcomingTourDates as $tour): ?>
                        <div class="tour-item upcoming" data-date-type="upcoming">
                            <div class="tour-date-badge">
                                <div class="tour-month"><?php echo format_date($tour['event_date'], 'M'); ?></div>
                                <div class="tour-day"><?php echo format_date($tour['event_date'], 'j'); ?></div>
                            </div>
                            <div class="tour-info">
                                <h3><?php echo xss_clean($tour['event_name']); ?></h3>
                                <div class="tour-venue"><?php echo xss_clean($tour['venue']); ?></div>
                                <div class="tour-location">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <?php echo xss_clean($tour['city'] . ', ' . $tour['country']); ?>
                                </div>
                                <?php if ($tour['description']): ?>
                                    <div class="tour-description"><?php echo truncate_text(xss_clean($tour['description']), 100); ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="tour-details">
                                <div class="tour-datetime">
                                    <?php if ($tour['event_time']): ?>
                                        <i class="fas fa-clock"></i>
                                        <?php echo format_time($tour['event_time']); ?>
                                    <?php endif; ?>
                                </div>
                                <div class="tour-actions">
                                    <?php if ($tour['ticket_url']): ?>
                                        <a href="<?php echo xss_clean($tour['ticket_url']); ?>" 
                                           target="_blank" 
                                           class="btn btn-primary">Get Tickets</a>
                                    <?php endif; ?>
                                    <button class="btn-icon" onclick="addToCalendar(<?php echo $tour['id']; ?>)" title="Add to Calendar">
                                        <i class="fas fa-calendar-plus"></i>
                                    </button>
                                    <button class="btn-icon" onclick="shareTour(<?php echo $tour['id']; ?>)" title="Share">
                                        <i class="fas fa-share"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-content">
                        <p>No upcoming tour dates scheduled at the moment. Check back soon!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Past Tour Dates -->
        <div id="past-tours" class="tour-section" style="display: none;">
            <h3 class="tour-section-title">Past Tour Dates</h3>
            <div class="tour-dates">
                <?php if (!empty($pastTourDates)): ?>
                    <?php foreach ($pastTourDates as $tour): ?>
                        <div class="tour-item past" data-date-type="past">
                            <div class="tour-date-badge">
                                <div class="tour-month"><?php echo format_date($tour['event_date'], 'M'); ?></div>
                                <div class="tour-day"><?php echo format_date($tour['event_date'], 'j'); ?></div>
                            </div>
                            <div class="tour-info">
                                <h3><?php echo xss_clean($tour['event_name']); ?></h3>
                                <div class="tour-venue"><?php echo xss_clean($tour['venue']); ?></div>
                                <div class="tour-location">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <?php echo xss_clean($tour['city'] . ', ' . $tour['country']); ?>
                                </div>
                                <div class="tour-status">
                                    <span class="status-badge past-show">Past Show</span>
                                </div>
                            </div>
                            <div class="tour-details">
                                <div class="tour-datetime">
                                    <?php if ($tour['event_time']): ?>
                                        <i class="fas fa-clock"></i>
                                        <?php echo format_time($tour['event_time']); ?>
                                    <?php endif; ?>
                                </div>
                                <div class="tour-actions">
                                    <button class="btn secondary" onclick="viewPhotos(<?php echo $tour['id']; ?>)">View Photos</button>
                                    <button class="btn-icon" onclick="shareTour(<?php echo $tour['id']; ?>)" title="Share">
                                        <i class="fas fa-share"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-content">
                        <p>No past tour dates available.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Tour Map -->
        <div class="tour-map-section">
            <h3 class="tour-section-title">Tour Map</h3>
            <div class="tour-map-container">
                <div class="map-placeholder">
                    <i class="fas fa-map-marked-alt"></i>
                    <p>Interactive tour map coming soon</p>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
/* Tour Page Specific Styles */
.tour-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 2rem;
    margin-bottom: 3rem;
}

.stat-card {
    background: var(--dark-secondary);
    padding: 2rem;
    border-radius: 15px;
    text-align: center;
    box-shadow: var(--shadow-md);
    transition: transform 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
}

.stat-number {
    font-size: 2.5rem;
    font-weight: 900;
    color: var(--primary-color);
    margin-bottom: 0.5rem;
}

.stat-label {
    color: var(--text-secondary);
    font-weight: 500;
}

.tour-filters {
    text-align: center;
    margin-bottom: 2rem;
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

.tour-section {
    margin-bottom: 3rem;
}

.tour-section-title {
    font-size: 1.8rem;
    margin-bottom: 2rem;
    color: var(--text-primary);
    text-align: center;
}

.tour-dates {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.tour-item {
    background: var(--dark-secondary);
    border-radius: 15px;
    padding: 2rem;
    display: flex;
    align-items: center;
    gap: 2rem;
    box-shadow: var(--shadow-lg);
    transition: transform 0.3s ease;
    position: relative;
}

.tour-item:hover {
    transform: translateX(5px);
}

.tour-item.upcoming {
    border-left: 4px solid var(--primary-color);
}

.tour-item.past {
    border-left: 4px solid var(--text-muted);
    opacity: 0.8;
}

.tour-date-badge {
    background: var(--dark-tertiary);
    border-radius: 10px;
    padding: 1rem;
    text-align: center;
    min-width: 80px;
}

.tour-month {
    font-size: 0.9rem;
    color: var(--text-secondary);
    text-transform: uppercase;
    margin-bottom: 0.25rem;
}

.tour-day {
    font-size: 1.8rem;
    font-weight: 700;
    color: var(--primary-color);
}

.tour-info {
    flex: 1;
}

.tour-info h3 {
    color: var(--text-primary);
    margin-bottom: 0.5rem;
    font-size: 1.3rem;
}

.tour-venue {
    color: var(--text-secondary);
    margin-bottom: 0.25rem;
    font-weight: 500;
}

.tour-location {
    color: var(--text-muted);
    font-size: 0.9rem;
    margin-bottom: 0.5rem;
}

.tour-description {
    color: var(--text-secondary);
    font-size: 0.9rem;
    margin-top: 0.5rem;
}

.tour-details {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 1rem;
}

.tour-datetime {
    color: var(--text-muted);
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.tour-actions {
    display: flex;
    gap: 0.5rem;
    align-items: center;
}

.tour-status {
    margin-top: 0.5rem;
}

.status-badge {
    background: var(--dark-tertiary);
    color: var(--text-muted);
    padding: 0.25rem 0.75rem;
    border-radius: 15px;
    font-size: 0.8rem;
    font-weight: 500;
}

.status-badge.past-show {
    background: var(--text-muted);
    color: var(--text-primary);
}

.tour-map-section {
    margin-top: 4rem;
}

.tour-map-container {
    background: var(--dark-secondary);
    border-radius: 15px;
    height: 400px;
    overflow: hidden;
    position: relative;
}

.map-placeholder {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100%;
    color: var(--text-muted);
}

.map-placeholder i {
    font-size: 3rem;
    margin-bottom: 1rem;
}

@media (max-width: 768px) {
    .tour-stats {
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }
    
    .stat-card {
        padding: 1.5rem;
    }
    
    .stat-number {
        font-size: 2rem;
    }
    
    .tour-item {
        flex-direction: column;
        text-align: center;
        gap: 1rem;
    }
    
    .tour-date-badge {
        align-self: center;
    }
    
    .tour-details {
        align-items: center;
        width: 100%;
    }
    
    .tour-actions {
        justify-content: center;
        flex-wrap: wrap;
    }
    
    .filter-buttons {
        flex-direction: column;
        align-items: center;
    }
    
    .filter-btn {
        width: 200px;
    }
}

@media (max-width: 480px) {
    .tour-stats {
        grid-template-columns: 1fr;
    }
    
    .tour-item {
        padding: 1.5rem;
    }
    
    .tour-actions {
        flex-direction: column;
        width: 100%;
    }
    
    .tour-actions .btn {
        width: 100%;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    initTourFilters();
});

function initTourFilters() {
    const filterButtons = document.querySelectorAll('.filter-btn');
    const upcomingTours = document.getElementById('upcoming-tours');
    const pastTours = document.getElementById('past-tours');
    
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Update active state
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            const filter = this.dataset.filter;
            
            // Show/hide sections
            if (filter === 'upcoming') {
                upcomingTours.style.display = 'block';
                pastTours.style.display = 'none';
            } else if (filter === 'past') {
                upcomingTours.style.display = 'none';
                pastTours.style.display = 'block';
            } else if (filter === 'all') {
                upcomingTours.style.display = 'block';
                pastTours.style.display = 'block';
            }
        });
    });
}

function addToCalendar(tourId) {
    // This would typically fetch tour details and create a calendar event
    showToast('Calendar event created!', 'success');
}

function shareTour(tourId) {
    if (navigator.share) {
        navigator.share({
            title: 'Tour Date',
            text: `Check out this tour date by ${APP_NAME}`,
            url: window.location.href
        });
    } else {
        navigator.clipboard.writeText(window.location.href);
        showToast('Link copied to clipboard!', 'success');
    }
}

function viewPhotos(tourId) {
    // This would open a photo gallery for the past event
    showToast('Photo gallery coming soon!', 'info');
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
