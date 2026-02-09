<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/functions.php';
?>

<!-- About Section -->
<section class="about-section" id="about">
    <div class="container">
        <div class="section-header">
            <div class="header-content">
                <h2 class="about-title">
                    <span class="title-gradient">About</span>
                    <div class="title-underline"></div>
                </h2>
                <p class="about-subtitle">
                    <span class="subtitle-icon">🎵</span>
                    The story behind the music
                    <span class="subtitle-icon">📖</span>
                </p>
            </div>
            <div class="header-decoration">
                <div class="decoration-circle decoration-1"></div>
                <div class="decoration-circle decoration-2"></div>
                <div class="decoration-circle decoration-3"></div>
            </div>
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
            <div class="story-header">
                <h3 class="story-title">
                    <span class="title-gradient">My Story</span>
                    <div class="title-underline"></div>
                </h3>
                <p class="story-subtitle">
                    <span class="subtitle-icon">🎭</span>
                    The journey that shaped the music
                    <span class="subtitle-icon">🌟</span>
                </p>
            </div>
            <div class="story-timeline">
                <div class="timeline-item">
                    <div class="timeline-date">
                        <span class="year">2013</span>
                        <div class="date-decoration"></div>
                    </div>
                    <div class="timeline-content">
                        <div class="content-header">
                            <h4>The Beginning</h4>
                            <div class="content-icon">🎵</div>
                        </div>
                        <p>Started writing songs in my bedroom, inspired by stories of everyday life and emotions that connect us all.</p>
                        <div class="content-decoration"></div>
                    </div>
                </div>
                
                <div class="timeline-item">
                    <div class="timeline-date">
                        <span class="year">2016</span>
                        <div class="date-decoration"></div>
                    </div>
                    <div class="timeline-content">
                        <div class="content-header">
                            <h4>First Breakthrough</h4>
                            <div class="content-icon">🚀</div>
                        </div>
                        <p>Released my debut single that gained unexpected traction online, leading to opportunities to perform at local venues.</p>
                        <div class="content-decoration"></div>
                    </div>
                </div>
                
                <div class="timeline-item">
                    <div class="timeline-date">
                        <span class="year">2019</span>
                        <div class="date-decoration"></div>
                    </div>
                    <div class="timeline-content">
                        <div class="content-header">
                            <h4>Debut Album</h4>
                            <div class="content-icon">💿</div>
                        </div>
                        <p>Launched my first full-length album, a collection of songs that represented years of growth, experimentation, and discovery.</p>
                        <div class="content-decoration"></div>
                    </div>
                </div>
                
                <div class="timeline-item">
                    <div class="timeline-date">
                        <span class="year">2022</span>
                        <div class="date-decoration"></div>
                    </div>
                    <div class="timeline-content">
                        <div class="content-header">
                            <h4>International Recognition</h4>
                            <div class="content-icon">🌍</div>
                        </div>
                        <p>Toured across multiple countries, sharing my music with diverse audiences and learning from different cultures along the way.</p>
                        <div class="content-decoration"></div>
                    </div>
                </div>
                
                <div class="timeline-item">
                    <div class="timeline-date">
                        <span class="year">2024</span>
                        <div class="date-decoration"></div>
                    </div>
                    <div class="timeline-content">
                        <div class="content-header">
                            <h4>New Chapter</h4>
                            <div class="content-icon">✨</div>
                        </div>
                        <p>Currently working on new music that pushes creative boundaries while staying true to the authentic storytelling that defines my art.</p>
                        <div class="content-decoration"></div>
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
.about-section {
    padding: 5rem 0;
    background: var(--dark-bg);
    position: relative;
    overflow: hidden;
}

.about-section::before {
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

.about-section::after {
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

.about-title {
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

.about-subtitle {
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
.about-hero {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 3rem;
    align-items: center;
    margin-bottom: 4rem;
    position: relative;
    z-index: 1;
}

.about-image {
    position: relative;
    overflow: hidden;
    border-radius: 25px;
}

.artist-photo {
    width: 100%;
    height: auto;
    border-radius: 25px;
    box-shadow: 
        0 20px 40px rgba(0, 0, 0, 0.4),
        0 8px 20px rgba(255, 107, 107, 0.2),
        inset 0 1px 0 rgba(255, 255, 255, 0.1);
    border: 2px solid rgba(255, 255, 255, 0.1);
    transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    z-index: 2;
}

.artist-photo::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, 
        transparent 0%, 
        rgba(255, 107, 107, 0.1) 50%, 
        transparent 100%);
    opacity: 0;
    transition: opacity 0.5s ease;
    border-radius: 25px;
}

.artist-photo::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    background-size: 200% 100%;
    animation: photoShimmer 3s linear infinite;
    border-radius: 25px;
}

@keyframes photoShimmer {
    0% { background-position: -200% 0; }
    100% { background-position: 200% 0; }
}

.about-image:hover .artist-photo {
    transform: scale(1.05) rotate(2deg);
    box-shadow: 
        0 25px 50px rgba(255, 107, 107, 0.3),
        0 12px 30px rgba(78, 205, 196, 0.2),
        border-color: rgba(255, 107, 107, 0.3);
}

.about-image:hover .artist-photo::before {
    opacity: 1;
}

.about-content {
    position: relative;
    z-index: 1;
}

.about-content h3 {
    font-size: 2.8rem;
    margin-bottom: 0.75rem;
    background: linear-gradient(135deg, #ffffff, #e0e0e0);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    background-size: 200% 200%;
    animation: aboutTitleGradient 3s ease-in-out infinite;
    text-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
    position: relative;
    z-index: 2;
}

@keyframes aboutTitleGradient {
    0%, 100% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
}

.artist-tagline {
    font-size: 1.3rem;
    color: var(--text-secondary);
    margin-bottom: 1.5rem;
    font-style: italic;
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    background-size: 200% 200%;
    animation: taglineGradient 4s ease-in-out infinite;
    position: relative;
    z-index: 2;
}

@keyframes taglineGradient {
    0%, 100% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
}

.artist-bio {
    color: var(--text-secondary);
    line-height: 1.8;
    margin-bottom: 2rem;
    font-size: 1.1rem;
    position: relative;
    z-index: 1;
}

.artist-bio::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: 
        radial-gradient(circle at 30% 30%, rgba(255, 107, 107, 0.05) 0%, transparent 50%),
        radial-gradient(circle at 70% 70%, rgba(78, 205, 196, 0.05) 0%, transparent 50%);
    opacity: 0.3;
    z-index: -1;
}

.artist-stats {
    display: flex;
    gap: 2rem;
    position: relative;
    z-index: 2;
}

.stat {
    text-align: center;
    background: 
        linear-gradient(145deg, var(--dark-secondary) 0%, var(--dark-tertiary) 100%),
        radial-gradient(circle at 30% 30%, rgba(255, 107, 107, 0.05) 0%, transparent 50%);
    padding: 1.5rem 2rem;
    border-radius: 20px;
    border: 2px solid rgba(255, 255, 255, 0.1);
    transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
    box-shadow: 
        0 10px 25px rgba(0, 0, 0, 0.3),
        0 4px 12px rgba(255, 107, 107, 0.1),
        inset 0 1px 0 rgba(255, 255, 255, 0.1);
}

.stat::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 2px;
    background: linear-gradient(90deg, var(--primary-color), var(--secondary-color), var(--accent-color), var(--primary-color));
    background-size: 300% 100%;
    animation: statShimmer 4s linear infinite;
    z-index: 2;
}

@keyframes statShimmer {
    0% { background-position: -300% 0; }
    100% { background-position: 300% 0; }
}

.stat:hover {
    transform: translateY(-8px) scale(1.05);
    box-shadow: 
        0 20px 40px rgba(255, 107, 107, 0.2),
        0 8px 20px rgba(78, 205, 196, 0.1),
        border-color: rgba(255, 107, 107, 0.3);
}

.stat-number {
    display: block;
    font-size: 2.2rem;
    font-weight: 700;
    color: var(--text-primary);
    background: linear-gradient(135deg, #ffffff, #e0e0e0);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    background-size: 200% 200%;
    animation: statNumberGradient 3s ease-in-out infinite;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    position: relative;
    z-index: 3;
}

@keyframes statNumberGradient {
    0%, 100% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
}

.stat-label {
    color: var(--text-muted);
    font-size: 0.9rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
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

.section-subtitle {
    font-size: 2rem;
    text-align: center;
    margin-bottom: 3rem;
    color: var(--text-primary);
}

.story-section {
    margin-bottom: 4rem;
    position: relative;
    z-index: 1;
}

.story-header {
    text-align: center;
    margin-bottom: 3rem;
    position: relative;
}

.story-title {
    font-size: 2.5rem;
    font-weight: 800;
    margin-bottom: 1rem;
    position: relative;
    display: inline-block;
}

.story-title .title-gradient {
    background: linear-gradient(135deg, #ff6b6b, #4ecdc4, #45b7d1);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    background-size: 200% 200%;
    animation: storyTitleGradient 4s ease-in-out infinite;
}

@keyframes storyTitleGradient {
    0%, 100% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
}

.story-title .title-underline {
    position: absolute;
    bottom: -8px;
    left: 50%;
    transform: translateX(-50%);
    width: 80px;
    height: 3px;
    background: linear-gradient(90deg, #ff6b6b, #4ecdc4, #45b7d1);
    border-radius: 2px;
    animation: storyUnderlineGlow 3s ease-in-out infinite;
}

@keyframes storyUnderlineGlow {
    0%, 100% { 
        box-shadow: 0 0 15px rgba(255, 107, 107, 0.5);
        width: 80px;
    }
    50% { 
        box-shadow: 0 0 25px rgba(78, 205, 196, 0.7);
        width: 120px;
    }
}

.story-subtitle {
    font-size: 1.1rem;
    color: var(--text-secondary);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 1rem;
    font-weight: 400;
}

.subtitle-icon {
    font-size: 1.3rem;
    animation: storyIconFloat 3s ease-in-out infinite;
}

.subtitle-icon:first-child {
    animation-delay: 0s;
}

.subtitle-icon:last-child {
    animation-delay: 1.5s;
}

@keyframes storyIconFloat {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-5px); }
}

.story-timeline {
    position: relative;
    max-width: 900px;
    margin: 0 auto;
    padding: 2rem 0;
}

.story-timeline::before {
    content: '';
    position: absolute;
    left: 50%;
    top: 0;
    bottom: 0;
    width: 4px;
    background: linear-gradient(180deg, 
        transparent 0%, 
        rgba(255, 107, 107, 0.3) 20%, 
        rgba(78, 205, 196, 0.3) 50%, 
        rgba(69, 183, 209, 0.3) 80%, 
        transparent 100%);
    transform: translateX(-50%);
    border-radius: 2px;
}

.timeline-item {
    display: flex;
    gap: 3rem;
    margin-bottom: 4rem;
    position: relative;
    align-items: center;
}

.timeline-item:nth-child(odd) {
    flex-direction: row;
}

.timeline-item:nth-child(even) {
    flex-direction: row-reverse;
}

.timeline-item:nth-child(odd) .timeline-date {
    margin-right: auto;
}

.timeline-item:nth-child(even) .timeline-date {
    margin-left: auto;
}

.timeline-date {
    position: relative;
    z-index: 2;
    min-width: 120px;
    text-align: center;
}

.timeline-date .year {
    display: block;
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    color: white;
    padding: 1rem 1.5rem;
    border-radius: 25px;
    font-weight: 700;
    font-size: 1.2rem;
    box-shadow: 
        0 10px 25px rgba(255, 107, 107, 0.3),
        0 4px 15px rgba(78, 205, 196, 0.2);
    position: relative;
    overflow: hidden;
}

.timeline-date .year::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    background-size: 200% 100%;
    animation: yearShimmer 3s linear infinite;
}

@keyframes yearShimmer {
    0% { background-position: -200% 0; }
    100% { background-position: 200% 0; }
}

.date-decoration {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 140px;
    height: 140px;
    border: 2px solid rgba(255, 107, 107, 0.2);
    border-radius: 50%;
    animation: datePulse 4s ease-in-out infinite;
}

@keyframes datePulse {
    0%, 100% { 
        transform: translate(-50%, -50%) scale(1);
        opacity: 0.3;
    }
    50% { 
        transform: translate(-50%, -50%) scale(1.1);
        opacity: 0.6;
    }
}

.timeline-content {
    flex: 1;
    background: 
        linear-gradient(145deg, var(--dark-secondary) 0%, var(--dark-tertiary) 100%),
        radial-gradient(circle at 30% 30%, rgba(255, 107, 107, 0.05) 0%, transparent 50%);
    padding: 2rem;
    border-radius: 20px;
    border: 2px solid rgba(255, 255, 255, 0.1);
    box-shadow: 
        0 15px 35px rgba(0, 0, 0, 0.3),
        0 6px 20px rgba(255, 107, 107, 0.1);
    position: relative;
    overflow: hidden;
    transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
}

.timeline-content:hover {
    transform: translateY(-5px) scale(1.02);
    box-shadow: 
        0 20px 45px rgba(255, 107, 107, 0.2),
        0 8px 25px rgba(78, 205, 196, 0.15);
    border-color: rgba(255, 107, 107, 0.3);
}

.timeline-content::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--primary-color), var(--secondary-color), var(--accent-color), var(--primary-color));
    background-size: 300% 100%;
    animation: contentShimmer 4s linear infinite;
}

@keyframes contentShimmer {
    0% { background-position: -300% 0; }
    100% { background-position: 300% 0; }
}

.content-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.content-header h4 {
    color: var(--text-primary);
    margin: 0;
    font-size: 1.4rem;
    font-weight: 700;
    background: linear-gradient(135deg, #ffffff, #e0e0e0);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    background-size: 200% 200%;
    animation: contentTitleGradient 3s ease-in-out infinite;
}

@keyframes contentTitleGradient {
    0%, 100% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
}

.content-icon {
    font-size: 1.5rem;
    animation: contentIconBounce 2s ease-in-out infinite;
}

@keyframes contentIconBounce {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-8px); }
}

.timeline-content p {
    color: var(--text-secondary);
    line-height: 1.7;
    margin: 0;
    position: relative;
    z-index: 2;
}

.content-decoration {
    position: absolute;
    bottom: 0;
    right: 0;
    width: 100px;
    height: 100px;
    background: linear-gradient(135deg, rgba(255, 107, 107, 0.1), rgba(78, 205, 196, 0.1));
    border-radius: 50%;
    transform: translate(30%, 30%);
    opacity: 0.5;
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
