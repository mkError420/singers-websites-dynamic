<footer>
    <div class="container">
        <div class="footer-content">
            <div class="footer-section">
                <h3><?php echo APP_NAME; ?></h3>
                <p>Creating music that touches the soul and moves the heart.</p>
                <div class="social-links">
                    <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                    <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="#" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                    <a href="#" aria-label="Spotify"><i class="fab fa-spotify"></i></a>
                    <a href="#" aria-label="Apple Music"><i class="fab fa-apple"></i></a>
                </div>
            </div>
            
            <div class="footer-section">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="<?php echo APP_URL; ?>/index.php">Home</a></li>
                    <li><a href="<?php echo APP_URL; ?>/music.php">Music</a></li>
                    <li><a href="<?php echo APP_URL; ?>/videos.php">Videos</a></li>
                    <li><a href="<?php echo APP_URL; ?>/tour.php">Tour Dates</a></li>
                    <li><a href="<?php echo APP_URL; ?>/about.php">About</a></li>
                    <li><a href="<?php echo APP_URL; ?>/contact.php">Contact</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h3>Music</h3>
                <ul>
                    <li><a href="http://localhost/website-singers/music.php">Latest Release</a></li>
                    <li><a href="http://localhost/website-singers/music.php">Discography</a></li>
                    <li><a href="http://localhost/website-singers/music.php">Lyrics</a></li>
                    <li><a href="http://localhost/website-singers/gallery.php">Behind the Scenes</a></li>
                    <li><a href="http://localhost/website-singers/videos.php">Music Videos</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h3>Connect</h3>
                <form class="footer-contact-form" method="POST" action="<?php echo APP_URL; ?>/contact.php">
                    <div class="form-group">
                        <input type="email" name="email" class="footer-form-input" placeholder="Your Email" required>
                    </div>
                    <div class="form-group">
                        <input type="tel" name="phone" class="footer-form-input" placeholder="Your Phone" required>
                    </div>
                    <button type="submit" class="footer-form-submit">
                        <i class="fas fa-paper-plane"></i> Submit
                    </button>
                </form>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; <span class="current-year"></span> <?php echo APP_NAME; ?>. All rights reserved.</p>
        </div>
    </div>
</footer>

<style>
/* Footer Contact Form Styles */
.footer-contact-form {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    margin-top: 1rem;
}

.footer-form-input {
    width: 100%;
    padding: 0.75rem 1rem;
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 8px;
    color: var(--text-primary);
    font-size: 0.9rem;
    transition: all 0.3s ease;
    backdrop-filter: blur(5px);
}

.footer-form-input::placeholder {
    color: var(--text-muted);
    opacity: 0.7;
}

.footer-form-input:focus {
    outline: none;
    border-color: var(--primary-color);
    background: rgba(255, 255, 255, 0.15);
    box-shadow: 0 0 0 2px rgba(255, 107, 107, 0.2);
}

.footer-form-submit {
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    color: white;
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-size: 0.9rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    box-shadow: 0 4px 12px rgba(255, 107, 107, 0.3);
}

.footer-form-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(255, 107, 107, 0.4);
    background: linear-gradient(135deg, var(--secondary-color), var(--primary-color));
}

.footer-form-submit:active {
    transform: translateY(0);
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .footer-contact-form {
        max-width: 100%;
    }
    
    .footer-form-input,
    .footer-form-submit {
        font-size: 0.85rem;
    }
}
</style>

<!-- JavaScript -->
<script src="<?php echo APP_URL; ?>/assets/js/main.js"></script>

</body>
</html>
