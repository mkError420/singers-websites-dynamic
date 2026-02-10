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
<section class="tour-section" id="tour">
    <div class="container">
        <div class="section-header">
            <div class="header-content">
                <h2 class="tour-title">
                    <span class="title-gradient">Tour Dates</span>
                    <div class="title-underline"></div>
                </h2>
                <p class="tour-subtitle">
                    <span class="subtitle-icon">🎤</span>
                    Join us live at a city near you
                    <span class="subtitle-icon">🌍</span>
                </p>
            </div>
            <div class="header-decoration">
                <div class="decoration-circle decoration-1"></div>
                <div class="decoration-circle decoration-2"></div>
                <div class="decoration-circle decoration-3"></div>
            </div>
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
                <button class="filter-btn active" data-filter="upcoming">
                    <span>Upcoming</span>
                </button>
                <button class="filter-btn" data-filter="past">
                    <span>Past Shows</span>
                </button>
                <button class="filter-btn" data-filter="all">
                    <span>All Dates</span>
                </button>
            </div>
        </div>
        
        <!-- Upcoming Tour Dates -->
        <div id="upcoming-tours" class="tour-section">
            <h3 class="tour-section-title">Upcoming Tour Dates</h3>
            <div class="tour-dates">
                <?php if (!empty($upcomingTourDates)): ?>
                    <?php foreach ($upcomingTourDates as $tour): ?>
                        <div class="tour-item upcoming" data-date-type="upcoming" data-tour-id="<?php echo $tour['id']; ?>">
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
                                        <!-- Ticket URL available but button removed per request -->
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
                        <div class="tour-item past" data-date-type="past" data-tour-id="<?php echo $tour['id']; ?>">
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
.tour-section {
    padding: 5rem 0;
    background: var(--dark-bg);
    position: relative;
    overflow: hidden;
}

.tour-section::before {
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

.tour-section::after {
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

.tour-title {
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

.tour-subtitle {
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
.tour-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 2rem;
    margin-bottom: 3rem;
    position: relative;
    z-index: 1;
}

.stat-card {
    background: 
        linear-gradient(145deg, var(--dark-secondary) 0%, var(--dark-tertiary) 100%),
        radial-gradient(circle at 30% 30%, rgba(255, 107, 107, 0.05) 0%, transparent 50%);
    border-radius: 25px;
    padding: 2.5rem;
    text-align: center;
    box-shadow: 
        0 15px 35px rgba(0, 0, 0, 0.4),
        0 5px 15px rgba(255, 107, 107, 0.1),
        inset 0 1px 0 rgba(255, 255, 255, 0.1);
    border: 2px solid rgba(255, 255, 255, 0.1);
    transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--primary-color), var(--secondary-color), var(--accent-color), var(--primary-color));
    background-size: 300% 100%;
    animation: statCardShimmer 4s linear infinite;
    z-index: 2;
}

@keyframes statCardShimmer {
    0% { background-position: -300% 0; }
    100% { background-position: 300% 0; }
}

.stat-card::after {
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
}

.stat-card:hover {
    transform: translateY(-15px) scale(1.05);
    box-shadow: 
        0 25px 50px rgba(255, 107, 107, 0.2),
        0 8px 25px rgba(78, 205, 196, 0.1),
        border-color: rgba(255, 107, 107, 0.3);
}

.stat-card:hover::after {
    opacity: 1;
}

.stat-number {
    font-size: 2.8rem;
    font-weight: 900;
    color: var(--text-primary);
    margin-bottom: 0.75rem;
    background: linear-gradient(135deg, #ffffff, #e0e0e0);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    background-size: 200% 200%;
    animation: statNumberGradient 3s ease-in-out infinite;
    text-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
    position: relative;
    z-index: 3;
}

@keyframes statNumberGradient {
    0%, 100% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
}

.stat-label {
    color: var(--text-secondary);
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    font-size: 0.9rem;
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    background-size: 200% 200%;
    animation: statLabelGradient 4s ease-in-out infinite;
    position: relative;
    z-index: 3;
}

@keyframes statLabelGradient {
    0%, 100% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
}

.tour-filters {
    text-align: center;
    margin-bottom: 2rem;
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
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    border-radius: 30px;
    opacity: 0;
    transition: opacity 0.4s ease;
    z-index: 0;
}

.filter-btn::after {
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

.filter-btn span {
    position: relative;
    z-index: 2;
}

.filter-btn:hover {
    transform: translateY(-3px) scale(1.05);
    border-color: rgba(255, 107, 107, 0.3);
    box-shadow: 
        0 8px 25px rgba(255, 107, 107, 0.2),
        0 4px 15px rgba(78, 205, 196, 0.1);
    color: var(--text-primary);
}

.filter-btn:hover::before {
    opacity: 1;
}

.filter-btn:hover::after {
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

.filter-btn.active::before,
.filter-btn:hover.active::before {
    opacity: 1;
}

.filter-btn:active {
    transform: translateY(-1px) scale(1.02);
}

.tour-section {
    margin-bottom: 3rem;
    position: relative;
    z-index: 1;
}

.tour-section-title {
    font-size: 2rem;
    margin-bottom: 2rem;
    color: var(--text-primary);
    text-align: center;
    font-weight: 700;
    background: linear-gradient(135deg, #ffffff, #e0e0e0);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    background-size: 200% 200%;
    animation: sectionTitleGradient 3s ease-in-out infinite;
    text-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
}

@keyframes sectionTitleGradient {
    0%, 100% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
}

.tour-dates {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 1.5rem;
    position: relative;
    z-index: 1;
}

.tour-item {
    background: 
        linear-gradient(145deg, var(--dark-secondary) 0%, var(--dark-tertiary) 100%),
        radial-gradient(circle at 30% 30%, rgba(255, 107, 107, 0.05) 0%, transparent 50%);
    border-radius: 20px;
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1.5rem;
    box-shadow: 
        0 10px 25px rgba(0, 0, 0, 0.3),
        0 3px 10px rgba(255, 107, 107, 0.1),
        inset 0 1px 0 rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.1);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.tour-item::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--primary-color), var(--secondary-color), var(--accent-color), var(--primary-color));
    background-size: 300% 100%;
    animation: tourItemShimmer 4s linear infinite;
    z-index: 2;
}

@keyframes tourItemShimmer {
    0% { background-position: -300% 0; }
    100% { background-position: 300% 0; }
}

.tour-item::after {
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
}

.tour-item:hover {
    transform: translateY(-5px) scale(1.01);
    box-shadow: 
        0 15px 35px rgba(255, 107, 107, 0.15),
        0 5px 15px rgba(0, 0, 0, 0.3),
        inset 0 1px 0 rgba(255, 255, 255, 0.2);
    border-color: rgba(255, 107, 107, 0.3);
}

.tour-item:hover::after {
    opacity: 1;
}

.tour-item.upcoming {
    border-left: 4px solid var(--primary-color);
    background: 
        linear-gradient(145deg, rgba(255, 107, 107, 0.1) 0%, rgba(255, 107, 107, 0.05) 100%),
        radial-gradient(circle at 30% 30%, rgba(255, 107, 107, 0.08) 0%, transparent 50%);
}

.tour-item.past {
    border-left: 4px solid var(--text-muted);
    opacity: 0.8;
    background: 
        linear-gradient(145deg, rgba(255, 255, 255, 0.02) 0%, rgba(255, 255, 255, 0.01) 100%),
        radial-gradient(circle at 30% 30%, rgba(100, 100, 100, 0.02) 0%, transparent 50%);
}

.tour-date-badge {
    background: 
        linear-gradient(145deg, var(--dark-tertiary) 0%, var(--dark-secondary) 100%),
        radial-gradient(circle at 50% 50%, rgba(255, 107, 107, 0.1) 0%, transparent 50%);
    border-radius: 12px;
    padding: 0.75rem;
    text-align: center;
    min-width: 65px;
    border: 1px solid rgba(255, 255, 255, 0.1);
    position: relative;
    overflow: hidden;
}

.tour-date-badge::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    background-size: 200% 100%;
    animation: dateBadgeShimmer 3s linear infinite;
}

@keyframes dateBadgeShimmer {
    0% { background-position: -200% 0; }
    100% { background-position: 200% 0; }
}

.tour-month {
    font-size: 0.75rem;
    color: var(--text-secondary);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0.2rem;
    font-weight: 600;
}

.tour-day {
    font-size: 1.4rem;
    font-weight: 700;
    color: var(--text-primary);
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
}

.tour-info {
    flex: 1;
}

.tour-info h3 {
    color: var(--text-primary);
    margin-bottom: 0.5rem;
    font-size: 1.2rem;
    font-weight: 700;
    line-height: 1.3;
    background: linear-gradient(135deg, #ffffff, #e0e0e0);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    background-size: 200% 200%;
    animation: tourTitleGradient 3s ease-in-out infinite;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
}

.tour-venue {
    color: var(--text-secondary);
    margin-bottom: 0.4rem;
    font-size: 1rem;
    font-weight: 500;
}

.tour-location {
    color: var(--text-muted);
    font-size: 0.85rem;
    margin-bottom: 0.4rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.tour-location i {
    color: var(--primary-color);
    font-size: 1rem;
    transition: all 0.3s ease;
}

.tour-item:hover .tour-location i {
    color: var(--secondary-color);
    transform: scale(1.1);
}

.tour-description {
    color: var(--text-secondary);
    font-size: 0.9rem;
    margin-top: 0.5rem;
    line-height: 1.5;
    font-style: italic;
}

.tour-details {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 1rem;
}

.tour-datetime {
    color: var(--text-muted);
    font-size: 0.8rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.tour-datetime i {
    color: var(--primary-color);
    font-size: 0.8rem;
    transition: all 0.3s ease;
}

.tour-item:hover .tour-datetime i {
    color: var(--secondary-color);
    transform: scale(1.1);
}

.tour-actions {
    display: flex;
    gap: 0.75rem;
    align-items: center;
}

.tour-status {
    margin-top: 0.5rem;
}

.status-badge {
    background: 
        linear-gradient(145deg, var(--dark-tertiary) 0%, var(--dark-secondary) 100%),
        radial-gradient(circle at 50% 50%, rgba(255, 107, 107, 0.1) 0%, transparent 50%);
    color: var(--text-muted);
    padding: 0.25rem 0.75rem;
    border-radius: 15px;
    font-size: 0.8rem;
    font-weight: 500;
    border: 2px solid rgba(255, 255, 255, 0.1);
}

.status-badge.past-show {
    background: 
        linear-gradient(145deg, rgba(100, 100, 100, 0.05) 0%, rgba(100, 100, 100, 0.02) 100%),
        radial-gradient(circle at 50% 50%, rgba(100, 100, 100, 0.05) 0%, transparent 50%);
    color: var(--text-primary);
}

.btn {
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    color: var(--text-primary);
    padding: 0.75rem 1.5rem;
    border-radius: 20px;
    text-decoration: none;
    font-weight: 600;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 1px;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    box-shadow: 0 6px 20px rgba(255, 107, 107, 0.4);
}

.btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    transition: left 0.6s ease;
}

.btn:hover {
    transform: translateY(-2px) scale(1.05);
    box-shadow: 0 8px 25px rgba(255, 107, 107, 0.6);
}

.btn:hover::before {
    left: 100%;
}

.btn.secondary {
    background: 
        linear-gradient(145deg, var(--dark-tertiary) 0%, var(--dark-secondary) 100%),
        radial-gradient(circle at 30% 30%, rgba(255, 255, 255, 0.05) 0%, transparent 50%);
    color: var(--text-primary);
    border: 2px solid rgba(255, 255, 255, 0.1);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
}

.btn.secondary:hover {
    transform: translateY(-2px) scale(1.05);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.4);
}

.btn-icon {
    background: 
        linear-gradient(145deg, var(--dark-tertiary) 0%, var(--dark-secondary) 100%),
        radial-gradient(circle at 30% 30%, rgba(255, 255, 255, 0.05) 0%, transparent 50%);
    color: var(--text-muted);
    border: none;
    padding: 0.75rem;
    border-radius: 50%;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
}

.btn-icon:hover {
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    color: var(--text-primary);
    transform: scale(1.1);
    box-shadow: 0 6px 20px rgba(255, 107, 107, 0.4);
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

@media (max-width: 768px) {
    .tour-dates {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .tour-item {
        padding: 1rem;
        gap: 1rem;
    }
    
    .tour-date-badge {
        min-width: 55px;
        padding: 0.5rem;
    }
    
    .tour-month {
        font-size: 0.7rem;
    }
    
    .tour-day {
        font-size: 1.2rem;
    }
    
    .tour-info h3 {
        font-size: 1.1rem;
    }
    
    .tour-venue {
        font-size: 0.9rem;
    }
    
    .tour-location {
        font-size: 0.8rem;
    }
    
    .tour-datetime {
        font-size: 0.75rem;
    }
    
    .tour-stats {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 480px) {
    .tour-item {
        padding: 0.75rem;
        gap: 0.75rem;
    }
    
    .tour-date-badge {
        min-width: 50px;
        padding: 0.4rem;
    }
    
    .tour-actions .btn {
        width: 100%;
    }
}

/* Share Popup Styles */
.share-popup {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
}

.share-popup.show {
    opacity: 1;
    visibility: visible;
}

.share-popup-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.8);
    backdrop-filter: blur(5px);
}

.share-popup-content {
    position: relative;
    background: var(--dark-secondary);
    border-radius: 20px;
    padding: 0;
    max-width: 500px;
    width: 90%;
    max-height: 90vh;
    overflow: hidden;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
    border: 1px solid rgba(255, 255, 255, 0.1);
    transform: scale(0.8) translateY(20px);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.share-popup.show .share-popup-content {
    transform: scale(1) translateY(0);
}

.share-popup-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    background: linear-gradient(135deg, var(--dark-tertiary), var(--dark-secondary));
}

.share-popup-header h3 {
    color: var(--text-primary);
    font-size: 1.2rem;
    font-weight: 700;
    margin: 0;
}

.share-popup-close {
    background: rgba(255, 255, 255, 0.1);
    border: none;
    color: var(--text-secondary);
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 1.1rem;
}

.share-popup-close:hover {
    background: var(--primary-color);
    color: var(--text-primary);
    transform: scale(1.1);
}

.share-popup-body {
    padding: 2rem;
}

.share-social-icons {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
    margin-bottom: 2rem;
}

.social-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 60px;
    height: 60px;
    border-radius: 15px;
    text-decoration: none;
    font-size: 1.5rem;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.social-icon::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transform: translateX(-100%);
    transition: transform 0.6s ease;
}

.social-icon:hover::before {
    transform: translateX(100%);
}

.social-icon.facebook {
    background: linear-gradient(135deg, #1877f2, #0c5ed8);
    color: white;
}

.social-icon.twitter {
    background: linear-gradient(135deg, #1da1f2, #0c85d0);
    color: white;
}

.social-icon.whatsapp {
    background: linear-gradient(135deg, #25d366, #128c7e);
    color: white;
}

.social-icon.linkedin {
    background: linear-gradient(135deg, #0077b5, #005885);
    color: white;
}

.social-icon.instagram {
    background: linear-gradient(135deg, #833ab4, #fd1d1d, #fcb045);
    color: white;
}

.social-icon.email {
    background: linear-gradient(135deg, #ea4335, #c23321);
    color: white;
}

.social-icon:hover {
    transform: translateY(-5px) scale(1.05);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
}

.share-copy-link {
    display: flex;
    gap: 0.5rem;
    align-items: center;
}

.share-copy-link input {
    flex: 1;
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: var(--text-primary);
    padding: 0.75rem 1rem;
    border-radius: 10px;
    font-size: 0.9rem;
    outline: none;
    transition: all 0.3s ease;
}

.share-copy-link input:focus {
    border-color: var(--primary-color);
    background: rgba(255, 255, 255, 0.15);
}

.copy-btn {
    background: var(--primary-color);
    border: none;
    color: var(--text-primary);
    padding: 0.75rem 1.5rem;
    border-radius: 10px;
    font-size: 0.9rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    white-space: nowrap;
}

.copy-btn:hover {
    background: var(--secondary-color);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(255, 107, 107, 0.3);
}

/* Mobile Responsive for Share Popup */
@media (max-width: 768px) {
    .share-popup-content {
        width: 95%;
        margin: 1rem;
    }
    
    .share-popup-header {
        padding: 1rem;
    }
    
    .share-popup-header h3 {
        font-size: 1.1rem;
    }
    
    .share-popup-body {
        padding: 1.5rem;
    }
    
    .share-social-icons {
        grid-template-columns: repeat(3, 1fr);
        gap: 0.75rem;
    }
    
    .social-icon {
        width: 50px;
        height: 50px;
        font-size: 1.2rem;
    }
    
    .share-copy-link {
        flex-direction: column;
        gap: 1rem;
    }
    
    .copy-btn {
        justify-content: center;
    }
}

@media (max-width: 480px) {
    .share-social-icons {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .social-icon {
        width: 45px;
        height: 45px;
        font-size: 1.1rem;
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
    console.log('🎯 Share button clicked! Tour ID:', tourId);
    
    // Get tour details
    const tourItem = document.querySelector(`[data-tour-id="${tourId}"]`);
    console.log('🔍 Found tour item:', tourItem);
    
    if (!tourItem) {
        console.error('❌ Tour not found for ID:', tourId);
        showToast('Tour not found', 'error');
        return;
    }
    
    const tourTitle = tourItem.querySelector('.tour-info h3');
    const tourVenue = tourItem.querySelector('.tour-venue');
    const tourLocation = tourItem.querySelector('.tour-location');
    const tourDate = tourItem.querySelector('.tour-datetime');
    
    console.log('📝 Tour elements:', {
        title: tourTitle,
        venue: tourVenue,
        location: tourLocation,
        date: tourDate
    });
    
    if (!tourTitle || !tourVenue) {
        console.error('❌ Missing tour information elements');
        showToast('Tour information incomplete', 'error');
        return;
    }
    
    const titleText = tourTitle.textContent;
    const venueText = tourVenue.textContent;
    const locationText = tourLocation ? tourLocation.textContent.trim() : '';
    const dateText = tourDate ? tourDate.textContent.trim() : '';
    
    console.log('📋 Extracted tour data:', {
        title: titleText,
        venue: venueText,
        location: locationText,
        date: dateText
    });
    
    const shareText = `${titleText} - ${venueText}, ${locationText} on ${dateText}`;
    const shareUrl = window.location.href + '#tour-' + tourId;
    
    console.log('🔗 Share data:', {
        text: shareText,
        url: shareUrl
    });
    
    // Create share popup
    createSharePopup(titleText, shareText, shareUrl);
}

function createSharePopup(title, text, url) {
    console.log('🎬 Creating share popup:', { title, text, url });
    
    // Remove existing popup if any
    const existingPopup = document.querySelector('.share-popup');
    if (existingPopup) {
        existingPopup.remove();
    }
    
    // Create popup HTML
    const popup = document.createElement('div');
    popup.className = 'share-popup';
    popup.innerHTML = `
        <div class="share-popup-overlay" onclick="closeSharePopup()"></div>
        <div class="share-popup-content">
            <div class="share-popup-header">
                <h3>Share "${title}"</h3>
                <button class="share-popup-close" onclick="closeSharePopup()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="share-popup-body">
                <div class="share-social-icons">
                    <a href="https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}&quote=${encodeURIComponent(text)}" 
                       target="_blank" class="social-icon facebook" title="Share on Facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="https://twitter.com/intent/tweet?text=${encodeURIComponent(text)}&url=${encodeURIComponent(url)}" 
                       target="_blank" class="social-icon twitter" title="Share on Twitter">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="https://wa.me/?text=${encodeURIComponent(text + ' ' + url)}" 
                       target="_blank" class="social-icon whatsapp" title="Share on WhatsApp">
                        <i class="fab fa-whatsapp"></i>
                    </a>
                    <a href="https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(url)}" 
                       target="_blank" class="social-icon linkedin" title="Share on LinkedIn">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                    <a href="https://www.instagram.com/" 
                       target="_blank" class="social-icon instagram" title="Share on Instagram" 
                       onclick="showInstagramInfo(event)">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="mailto:?subject=${encodeURIComponent(title)}&body=${encodeURIComponent(text + ' ' + url)}" 
                       class="social-icon email" title="Share via Email">
                        <i class="fas fa-envelope"></i>
                    </a>
                </div>
                <div class="share-copy-link">
                    <input type="text" readonly value="${url}" id="shareUrlInput">
                    <button class="copy-btn" onclick="copyShareUrl()">
                        <i class="fas fa-copy"></i> Copy
                    </button>
                </div>
            </div>
        </div>
    `;
    
    console.log('📦 Popup HTML created');
    
    // Add popup to page
    document.body.appendChild(popup);
    console.log('✅ Popup added to body');
    
    // Show popup with animation
    setTimeout(() => {
        popup.classList.add('show');
        console.log('🎬 Popup should be visible now');
    }, 10);
}

function closeSharePopup() {
    console.log('🔴 Closing share popup');
    const popup = document.querySelector('.share-popup');
    if (popup) {
        popup.classList.remove('show');
        setTimeout(() => {
            popup.remove();
            console.log('✅ Popup removed');
        }, 300);
    }
}

function copyShareUrl() {
    console.log('📋 Copying share URL');
    const input = document.getElementById('shareUrlInput');
    if (input) {
        input.select();
        document.execCommand('copy');
        console.log('✅ URL copied to clipboard');
        showToast('Link copied to clipboard!', 'success');
        
        // Change button text temporarily
        const copyBtn = document.querySelector('.copy-btn');
        if (copyBtn) {
            const originalHTML = copyBtn.innerHTML;
            copyBtn.innerHTML = '<i class="fas fa-check"></i> Copied!';
            setTimeout(() => {
                copyBtn.innerHTML = originalHTML;
            }, 2000);
        }
    } else {
        console.error('❌ Share URL input not found');
    }
}

function showInstagramInfo(event) {
    event.preventDefault();
    showToast('Instagram doesn\'t support direct sharing. Copy the link and share it in your Instagram story!', 'info');
}

function viewPhotos(tourId) {
    // This would open a photo gallery for the past event
    showToast('Photo gallery coming soon!', 'info');
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
