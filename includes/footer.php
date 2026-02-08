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
                    <li><a href="#">Latest Release</a></li>
                    <li><a href="#">Discography</a></li>
                    <li><a href="#">Lyrics</a></li>
                    <li><a href="#">Behind the Scenes</a></li>
                    <li><a href="#">Music Videos</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h3>Connect</h3>
                <ul>
                    <li><a href="#">Newsletter</a></li>
                    <li><a href="#">Fan Club</a></li>
                    <li><a href="#">Press Kit</a></li>
                    <li><a href="#">Booking</a></li>
                    <li><a href="#">Management</a></li>
                </ul>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; <span class="current-year"></span> <?php echo APP_NAME; ?>. All rights reserved. | 
            <a href="privacy-policy.php">Privacy Policy</a> | 
            <a href="terms-of-service.php">Terms of Service</a></p>
        </div>
    </div>
</footer>

<!-- Video Modal -->
<div id="videoModal" class="video-modal" style="display: none;">
    <div class="modal-content">
        <span class="close-modal">&times;</span>
        <iframe id="modalVideo" width="100%" height="500" frameborder="0" allowfullscreen></iframe>
    </div>
</div>

<!-- JavaScript -->
<script src="<?php echo APP_URL; ?>/assets/js/main.js"></script>

</body>
</html>
