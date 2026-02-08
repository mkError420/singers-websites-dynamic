<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/functions.php';
?>

<!-- About Section -->
<section class="section">
    <div class="container">
        <div class="section-title">
            <h2>About</h2>
            <p>The story behind the music</p>
        </div>
        
        <!-- Hero About -->
        <div class="about-hero">
            <div class="about-image">
                <img src="assets/images/artist-photo.jpg" alt="<?php echo APP_NAME; ?>" class="artist-photo">
            </div>
            <div class="about-content">
                <h3><?php echo APP_NAME; ?></h3>
                <p class="artist-tagline">Musician, Songwriter, Storyteller</p>
                <p class="artist-bio">
                    From humble beginnings to international stages, my journey has been driven by a passion for creating music that speaks to the soul. Every song tells a story, every melody carries emotion, and every performance is an opportunity to connect with amazing people like you.
                </p>
                <div class="artist-stats">
                    <div class="stat">
                        <span class="stat-number">10+</span>
                        <span class="stat-label">Years</span>
                    </div>
                    <div class="stat">
                        <span class="stat-number">50+</span>
                        <span class="stat-label">Songs</span>
                    </div>
                    <div class="stat">
                        <span class="stat-number">1M+</span>
                        <span class="stat-label">Fans</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Story Section -->
        <div class="story-section">
            <h3 class="section-subtitle">My Story</h3>
            <div class="story-timeline">
                <div class="timeline-item">
                    <div class="timeline-date">2013</div>
                    <div class="timeline-content">
                        <h4>The Beginning</h4>
                        <p>Started writing songs in my bedroom, inspired by the stories of everyday life and the emotions that connect us all.</p>
                    </div>
                </div>
                
                <div class="timeline-item">
                    <div class="timeline-date">2016</div>
                    <div class="timeline-content">
                        <h4>First Breakthrough</h4>
                        <p>Released my debut single that gained unexpected traction online, leading to opportunities to perform at local venues.</p>
                    </div>
                </div>
                
                <div class="timeline-item">
                    <div class="timeline-date">2019</div>
                    <div class="timeline-content">
                        <h4>Debut Album</h4>
                        <p>Launched my first full-length album, a collection of songs that represented years of growth, experimentation, and discovery.</p>
                    </div>
                </div>
                
                <div class="timeline-item">
                    <div class="timeline-date">2022</div>
                    <div class="timeline-content">
                        <h4>International Recognition</h4>
                        <p>Toured across multiple countries, sharing my music with diverse audiences and learning from different cultures along the way.</p>
                    </div>
                </div>
                
                <div class="timeline-item">
                    <div class="timeline-date">2024</div>
                    <div class="timeline-content">
                        <h4>New Chapter</h4>
                        <p>Currently working on new music that pushes creative boundaries while staying true to the authentic storytelling that defines my art.</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Musical Influences -->
        <div class="influences-section">
            <h3 class="section-subtitle">Musical Influences</h3>
            <div class="influences-grid">
                <div class="influence-card">
                    <div class="influence-icon">
                        <i class="fas fa-guitar"></i>
                    </div>
                    <h4>Classic Rock</h4>
                    <p>The storytelling and emotional depth of classic rock legends who paved the way for authentic music.</p>
                </div>
                
                <div class="influence-card">
                    <div class="influence-icon">
                        <i class="fas fa-microphone"></i>
                    </div>
                    <h4>Soul & R&B</h4>
                    <p>The raw emotion and vocal expression that comes from soul and R&B traditions.</p>
                </div>
                
                <div class="influence-card">
                    <div class="influence-icon">
                        <i class="fas fa-headphones"></i>
                    </div>
                    <h4>Electronic</h4>
                    <p>Modern production techniques and sonic landscapes that push creative boundaries.</p>
                </div>
                
                <div class="influence-card">
                    <div class="influence-icon">
                        <i class="fas fa-music"></i>
                    </div>
                    <h4>Folk Traditions</h4>
                    <p>The timeless storytelling traditions of folk music from around the world.</p>
                </div>
            </div>
        </div>
        
        <!-- Achievements -->
        <div class="achievements-section">
            <h3 class="section-subtitle">Achievements</h3>
            <div class="achievements-grid">
                <div class="achievement-item">
                    <div class="achievement-icon">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <div class="achievement-content">
                        <h4>Best New Artist</h4>
                        <p>International Music Awards 2020</p>
                    </div>
                </div>
                
                <div class="achievement-item">
                    <div class="achievement-icon">
                        <i class="fas fa-award"></i>
                    </div>
                    <div class="achievement-content">
                        <h4>Song of the Year</h4>
                        <p>"Midnight Dreams" - Global Music Charts 2021</p>
                    </div>
                </div>
                
                <div class="achievement-item">
                    <div class="achievement-icon">
                        <i class="fas fa-certificate"></i>
                    </div>
                    <div class="achievement-content">
                        <h4>Gold Certification</h4>
                        <p>Debut Album - 500,000+ copies sold</p>
                    </div>
                </div>
                
                <div class="achievement-item">
                    <div class="achievement-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="achievement-content">
                        <h4>Fan Choice Award</h4>
                        <p>Most Streamed Artist 2023</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Press Quotes -->
        <div class="press-section">
            <h3 class="section-subtitle">What They Say</h3>
            <div class="press-quotes">
                <blockquote class="press-quote">
                    <p>"A voice that carries the weight of experience and the lightness of hope, creating music that resonates deeply with listeners."</p>
                    <cite>Music Magazine International</cite>
                </blockquote>
                
                <blockquote class="press-quote">
                    <p>"Authentic storytelling combined with innovative production - this artist is redefining what modern music can be."</p>
                    <cite>The Sound Review</cite>
                </blockquote>
                
                <blockquote class="press-quote">
                    <p>"Live performances that transform venues into intimate spaces where every song feels personal and every moment matters."</p>
                    <cite>Concert Chronicles</cite>
                </blockquote>
            </div>
        </div>
        
        <!-- Call to Action -->
        <div class="about-cta">
            <h3>Join the Journey</h3>
            <p>Music is better when shared. Subscribe to get updates on new releases, tour dates, and exclusive content.</p>
            <div class="cta-buttons">
                <a href="#newsletter" class="btn btn-primary">Subscribe Now</a>
                <a href="music.php" class="btn secondary">Listen to Music</a>
            </div>
        </div>
    </div>
</section>

<style>
/* About Page Specific Styles */
.about-hero {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 3rem;
    align-items: center;
    margin-bottom: 4rem;
}

.about-image {
    position: relative;
}

.artist-photo {
    width: 100%;
    height: auto;
    border-radius: 15px;
    box-shadow: var(--shadow-xl);
}

.about-content h3 {
    font-size: 2.5rem;
    margin-bottom: 0.5rem;
    background: var(--gradient-primary);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.artist-tagline {
    font-size: 1.2rem;
    color: var(--text-secondary);
    margin-bottom: 1.5rem;
    font-style: italic;
}

.artist-bio {
    color: var(--text-secondary);
    line-height: 1.8;
    margin-bottom: 2rem;
    font-size: 1.1rem;
}

.artist-stats {
    display: flex;
    gap: 2rem;
}

.stat {
    text-align: center;
}

.stat-number {
    display: block;
    font-size: 2rem;
    font-weight: 700;
    color: var(--primary-color);
}

.stat-label {
    color: var(--text-muted);
    font-size: 0.9rem;
}

.section-subtitle {
    font-size: 2rem;
    text-align: center;
    margin-bottom: 3rem;
    color: var(--text-primary);
}

.story-timeline {
    position: relative;
    max-width: 800px;
    margin: 0 auto;
}

.timeline-item {
    display: flex;
    gap: 2rem;
    margin-bottom: 3rem;
    position: relative;
}

.timeline-item::before {
    content: '';
    position: absolute;
    left: 60px;
    top: 30px;
    bottom: -30px;
    width: 2px;
    background: var(--border-color);
}

.timeline-item:last-child::before {
    display: none;
}

.timeline-date {
    background: var(--primary-color);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-weight: 600;
    min-width: 80px;
    text-align: center;
    position: relative;
    z-index: 1;
}

.timeline-content h4 {
    color: var(--text-primary);
    margin-bottom: 0.5rem;
}

.timeline-content p {
    color: var(--text-secondary);
    line-height: 1.6;
}

.influences-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 2rem;
    margin-bottom: 4rem;
}

.influence-card {
    background: var(--dark-secondary);
    padding: 2rem;
    border-radius: 15px;
    text-align: center;
    transition: transform 0.3s ease;
    box-shadow: var(--shadow-md);
}

.influence-card:hover {
    transform: translateY(-5px);
}

.influence-icon {
    font-size: 2.5rem;
    color: var(--primary-color);
    margin-bottom: 1rem;
}

.influence-card h4 {
    color: var(--text-primary);
    margin-bottom: 1rem;
}

.influence-card p {
    color: var(--text-secondary);
    font-size: 0.9rem;
}

.achievements-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
    margin-bottom: 4rem;
}

.achievement-item {
    display: flex;
    align-items: center;
    gap: 1.5rem;
    background: var(--dark-secondary);
    padding: 1.5rem;
    border-radius: 15px;
    box-shadow: var(--shadow-md);
}

.achievement-icon {
    font-size: 2rem;
    color: var(--primary-color);
    min-width: 50px;
    text-align: center;
}

.achievement-content h4 {
    color: var(--text-primary);
    margin-bottom: 0.25rem;
}

.achievement-content p {
    color: var(--text-secondary);
    font-size: 0.9rem;
}

.press-quotes {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
    margin-bottom: 4rem;
}

.press-quote {
    background: var(--dark-secondary);
    padding: 2rem;
    border-radius: 15px;
    border-left: 4px solid var(--primary-color);
    font-style: italic;
    box-shadow: var(--shadow-md);
}

.press-quote p {
    color: var(--text-secondary);
    margin-bottom: 1rem;
    line-height: 1.6;
}

.press-quote cite {
    color: var(--text-muted);
    font-size: 0.9rem;
    font-style: normal;
    font-weight: 500;
}

.about-cta {
    text-align: center;
    background: var(--gradient-secondary);
    padding: 3rem;
    border-radius: 15px;
    margin-top: 4rem;
}

.about-cta h3 {
    color: var(--text-primary);
    margin-bottom: 1rem;
}

.about-cta p {
    color: var(--text-secondary);
    margin-bottom: 2rem;
}

.cta-buttons {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
}

@media (max-width: 768px) {
    .about-hero {
        grid-template-columns: 1fr;
        gap: 2rem;
        text-align: center;
    }
    
    .artist-stats {
        justify-content: center;
    }
    
    .timeline-item {
        flex-direction: column;
        text-align: center;
    }
    
    .timeline-item::before {
        display: none;
    }
    
    .timeline-date {
        align-self: center;
    }
    
    .achievement-item {
        flex-direction: column;
        text-align: center;
    }
    
    .cta-buttons {
        flex-direction: column;
        align-items: center;
    }
    
    .cta-buttons .btn {
        width: 200px;
    }
}

@media (max-width: 480px) {
    .about-content h3 {
        font-size: 2rem;
    }
    
    .artist-stats {
        flex-direction: column;
        gap: 1rem;
    }
    
    .influences-grid,
    .achievements-grid,
    .press-quotes {
        grid-template-columns: 1fr;
    }
    
    .about-cta {
        padding: 2rem;
    }
}
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
