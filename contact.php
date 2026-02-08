<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/functions.php';

// Process contact form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize_input($_POST['name']);
    $email = sanitize_input($_POST['email']);
    $subject = sanitize_input($_POST['subject']);
    $message = sanitize_input($_POST['message']);
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    // Verify CSRF token
    if (!verify_csrf_token($csrf_token)) {
        $error = 'Security token expired. Please try again.';
    } elseif (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        // Insert into database
        $contact_data = [
            'name' => $name,
            'email' => $email,
            'subject' => $subject,
            'message' => $message,
            'ip_address' => $_SERVER['REMOTE_ADDR']
        ];
        
        if (insertData('contact_messages', $contact_data)) {
            // Send email notification (optional)
            $email_subject = "New Contact Message: $subject";
            $email_body = "Name: $name\nEmail: $email\n\nMessage:\n$message";
            send_email(FROM_EMAIL, $email_subject, $email_body);
            
            $success = 'Thank you for your message! We\'ll get back to you soon.';
        } else {
            $error = 'Failed to send message. Please try again.';
        }
    }
}
?>

<!-- Contact Section -->
<section class="section">
    <div class="container">
        <div class="section-title">
            <h2>Contact</h2>
            <p>Get in touch - we'd love to hear from you</p>
        </div>
        
        <div class="contact-container">
            <!-- Contact Form -->
            <div class="contact-form-section">
                <h3>Send a Message</h3>
                <?php if (isset($error)): ?>
                    <div class="alert alert-error">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($success)): ?>
                    <div class="alert alert-success">
                        <?php echo $success; ?>
                    </div>
                <?php endif; ?>
                
                <form id="contactForm" class="contact-form" method="POST" action="">
                    <div class="form-group">
                        <label for="name">Name *</label>
                        <input type="text" id="name" name="name" class="form-control" required 
                               value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email *</label>
                        <input type="email" id="email" name="email" class="form-control" required
                               value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="subject">Subject *</label>
                        <select id="subject" name="subject" class="form-control" required>
                            <option value="">Select a subject</option>
                            <option value="General Inquiry" <?php echo (isset($_POST['subject']) && $_POST['subject'] === 'General Inquiry') ? 'selected' : ''; ?>>General Inquiry</option>
                            <option value="Booking" <?php echo (isset($_POST['subject']) && $_POST['subject'] === 'Booking') ? 'selected' : ''; ?>>Booking</option>
                            <option value="Press" <?php echo (isset($_POST['subject']) && $_POST['subject'] === 'Press') ? 'selected' : ''; ?>>Press/Media</option>
                            <option value="Collaboration" <?php echo (isset($_POST['subject']) && $_POST['subject'] === 'Collaboration') ? 'selected' : ''; ?>>Collaboration</option>
                            <option value="Technical Support" <?php echo (isset($_POST['subject']) && $_POST['subject'] === 'Technical Support') ? 'selected' : ''; ?>>Technical Support</option>
                            <option value="Other" <?php echo (isset($_POST['subject']) && $_POST['subject'] === 'Other') ? 'selected' : ''; ?>>Other</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="message">Message *</label>
                        <textarea id="message" name="message" class="form-control" rows="6" required><?php echo isset($_POST['message']) ? htmlspecialchars($_POST['message']) : ''; ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <div class="recaptcha-container">
                            <div class="g-recaptcha" data-sitekey="your-recaptcha-site-key"></div>
                        </div>
                    </div>
                    
                    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                    
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-paper-plane"></i> Send Message
                    </button>
                </form>
            </div>
            
            <!-- Contact Information -->
            <div class="contact-info-section">
                <h3>Get in Touch</h3>
                
                <div class="contact-methods">
                    <div class="contact-method">
                        <div class="contact-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="contact-details">
                            <h4>Email</h4>
                            <p>contact@singerwebsite.com</p>
                            <small>We'll respond within 24-48 hours</small>
                        </div>
                    </div>
                    
                    <div class="contact-method">
                        <div class="contact-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div class="contact-details">
                            <h4>Phone</h4>
                            <p>+1 (555) 123-4567</p>
                            <small>Mon-Fri, 9am-5pm EST</small>
                        </div>
                    </div>
                    
                    <div class="contact-method">
                        <div class="contact-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="contact-details">
                            <h4>Address</h4>
                            <p>123 Music Street<br>Los Angeles, CA 90028</p>
                            <small>By appointment only</small>
                        </div>
                    </div>
                </div>
                
                <!-- Social Media -->
                <div class="social-section">
                    <h4>Follow on Social Media</h4>
                    <div class="social-links">
                        <a href="https://facebook.com/<?php echo strtolower(APP_NAME); ?>" target="_blank" class="social-link">
                            <i class="fab fa-facebook"></i>
                        </a>
                        <a href="https://twitter.com/<?php echo strtolower(APP_NAME); ?>" target="_blank" class="social-link">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="https://instagram.com/<?php echo strtolower(APP_NAME); ?>" target="_blank" class="social-link">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="https://youtube.com/<?php echo strtolower(APP_NAME); ?>" target="_blank" class="social-link">
                            <i class="fab fa-youtube"></i>
                        </a>
                        <a href="https://spotify.com/artist/<?php echo strtolower(APP_NAME); ?>" target="_blank" class="social-link">
                            <i class="fab fa-spotify"></i>
                        </a>
                    </div>
                </div>
                
                <!-- FAQ Link -->
                <div class="faq-section">
                    <h4>Have a Question?</h4>
                    <p>Check out our <a href="#faq">Frequently Asked Questions</a> for quick answers to common inquiries.</p>
                </div>
            </div>
        </div>
        
        <!-- FAQ Section -->
        <div class="faq-section" id="faq">
            <h3 class="section-subtitle">Frequently Asked Questions</h3>
            <div class="faq-container">
                <div class="faq-item">
                    <div class="faq-question">
                        <h4>How can I book you for an event?</h4>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>For booking inquiries, please use the contact form above and select "Booking" as the subject. Include details about your event, date, venue, and budget. Our booking team will get back to you within 48 hours.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">
                        <h4>Do you offer private lessons or workshops?</h4>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Yes! I occasionally offer songwriting workshops and vocal coaching sessions. These are announced through the newsletter and social media. You can also express interest through the contact form.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">
                        <h4>How can I get permission to use your music?</h4>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>For licensing requests, please contact us with details about your project. We offer different licensing options for commercial, educational, and non-profit use. Response time varies based on the complexity of your request.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">
                        <h4>Where can I buy merchandise?</h4>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Official merchandise is available at concerts and through our online store. We announce new merchandise drops through the newsletter and social media channels.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">
                        <h4>How do I join your street team?</h4>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Our street team helps promote shows and share music in local communities. Subscribe to the newsletter and look for announcements about street team recruitment, typically before major tours.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
/* Contact Page Specific Styles */
.contact-container {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 3rem;
    margin-bottom: 4rem;
}

.contact-form-section,
.contact-info-section {
    background: var(--dark-secondary);
    padding: 2rem;
    border-radius: 15px;
    box-shadow: var(--shadow-lg);
}

.contact-form-section h3,
.contact-info-section h3 {
    color: var(--text-primary);
    margin-bottom: 2rem;
    font-size: 1.5rem;
}

.alert {
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 1.5rem;
    font-weight: 500;
}

.alert-success {
    background: var(--success-color);
    color: white;
}

.alert-error {
    background: var(--error-color);
    color: white;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
    color: var(--text-primary);
}

.form-control {
    width: 100%;
    padding: 1rem;
    background: var(--dark-tertiary);
    border: 1px solid var(--border-color);
    border-radius: 8px;
    color: var(--text-primary);
    font-size: 1rem;
    transition: border-color 0.3s ease;
}

.form-control:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(255, 107, 107, 0.1);
}

textarea.form-control {
    resize: vertical;
    min-height: 120px;
}

.recaptcha-container {
    margin: 1rem 0;
}

.btn-lg {
    padding: 1rem 2rem;
    font-size: 1.1rem;
}

.contact-methods {
    margin-bottom: 2rem;
}

.contact-method {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1.5rem;
    padding-bottom: 1.5rem;
    border-bottom: 1px solid var(--border-color);
}

.contact-method:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}

.contact-icon {
    width: 50px;
    height: 50px;
    background: var(--primary-color);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.2rem;
    flex-shrink: 0;
}

.contact-details h4 {
    color: var(--text-primary);
    margin-bottom: 0.25rem;
}

.contact-details p {
    color: var(--text-secondary);
    margin-bottom: 0.25rem;
}

.contact-details small {
    color: var(--text-muted);
    font-size: 0.8rem;
}

.social-section {
    margin-bottom: 2rem;
}

.social-section h4 {
    color: var(--text-primary);
    margin-bottom: 1rem;
}

.social-links {
    display: flex;
    gap: 1rem;
}

.social-link {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    background: var(--dark-tertiary);
    border-radius: 50%;
    color: var(--text-primary);
    transition: all 0.3s ease;
}

.social-link:hover {
    background: var(--primary-color);
    transform: translateY(-3px);
}

.faq-section h4 {
    color: var(--text-primary);
    margin-bottom: 0.5rem;
}

.faq-section p {
    color: var(--text-secondary);
}

.faq-section a {
    color: var(--primary-color);
}

.faq-container {
    max-width: 800px;
    margin: 0 auto;
}

.faq-item {
    background: var(--dark-secondary);
    border-radius: 10px;
    margin-bottom: 1rem;
    overflow: hidden;
    box-shadow: var(--shadow-md);
}

.faq-question {
    padding: 1.5rem;
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
    transition: background 0.3s ease;
}

.faq-question:hover {
    background: var(--dark-tertiary);
}

.faq-question h4 {
    color: var(--text-primary);
    margin: 0;
}

.faq-question i {
    color: var(--text-muted);
    transition: transform 0.3s ease;
}

.faq-item.active .faq-question i {
    transform: rotate(180deg);
}

.faq-answer {
    padding: 0 1.5rem;
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease, padding 0.3s ease;
}

.faq-item.active .faq-answer {
    padding: 0 1.5rem 1.5rem;
    max-height: 200px;
}

.faq-answer p {
    color: var(--text-secondary);
    line-height: 1.6;
    margin: 0;
}

@media (max-width: 768px) {
    .contact-container {
        grid-template-columns: 1fr;
        gap: 2rem;
    }
    
    .contact-form-section,
    .contact-info-section {
        padding: 1.5rem;
    }
    
    .contact-method {
        flex-direction: column;
        text-align: center;
        gap: 0.5rem;
    }
    
    .social-links {
        justify-content: center;
    }
    
    .faq-question {
        padding: 1rem;
    }
    
    .faq-question h4 {
        font-size: 0.9rem;
    }
}

@media (max-width: 480px) {
    .contact-form-section,
    .contact-info-section {
        padding: 1rem;
    }
    
    .btn-lg {
        width: 100%;
    }
    
    .social-links {
        flex-wrap: wrap;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    initFAQ();
});

function initFAQ() {
    const faqItems = document.querySelectorAll('.faq-item');
    
    faqItems.forEach(item => {
        const question = item.querySelector('.faq-question');
        
        question.addEventListener('click', function() {
            // Close other items
            faqItems.forEach(otherItem => {
                if (otherItem !== item) {
                    otherItem.classList.remove('active');
                }
            });
            
            // Toggle current item
            item.classList.toggle('active');
        });
    });
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
