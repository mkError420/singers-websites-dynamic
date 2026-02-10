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
<section class="contact-section" id="contact">
    <div class="container">
        <div class="section-header">
            <div class="header-content">
                <h2 class="contact-title">
                    <span class="title-gradient">Contact</span>
                    <div class="title-underline"></div>
                </h2>
                <p class="contact-subtitle">
                    <span class="subtitle-icon">📧</span>
                    Get in touch - we'd love to hear from you
                    <span class="subtitle-icon">💬</span>
                </p>
            </div>
            <div class="header-decoration">
                <div class="decoration-circle decoration-1"></div>
                <div class="decoration-circle decoration-2"></div>
                <div class="decoration-circle decoration-3"></div>
            </div>
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
                            <p>+88 0185-718767</p>
                            <small>Sun-Thu, 9am-5pm </small>
                        </div>
                    </div>
                    
                    <div class="contact-method">
                        <div class="contact-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="contact-details">
                            <h4>Address</h4>
                            <p>123 Medicle Street<br>Rangpur City, Rangpur</p>
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
.contact-section {
    padding: 5rem 0;
    background: var(--dark-bg);
    position: relative;
    overflow: hidden;
}

.contact-section::before {
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

.contact-section::after {
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

.contact-title {
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

.contact-subtitle {
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
.contact-container {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 3rem;
    margin-bottom: 4rem;
    position: relative;
    z-index: 1;
}

.contact-form-section {
    background: 
        linear-gradient(145deg, var(--dark-secondary) 0%, var(--dark-tertiary) 100%),
        radial-gradient(circle at 30% 30%, rgba(255, 107, 107, 0.05) 0%, transparent 50%);
    border-radius: 25px;
    padding: 2.5rem;
    border: 2px solid rgba(255, 255, 255, 0.1);
    box-shadow: 
        0 20px 40px rgba(0, 0, 0, 0.4),
        0 8px 20px rgba(255, 107, 107, 0.1),
        inset 0 1px 0 rgba(255, 255, 255, 0.1);
    position: relative;
    overflow: hidden;
}

.contact-form-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--primary-color), var(--secondary-color), var(--accent-color), var(--primary-color));
    background-size: 300% 100%;
    animation: formShimmer 4s linear infinite;
    z-index: 2;
}

@keyframes formShimmer {
    0% { background-position: -300% 0; }
    100% { background-position: 300% 0; }
}

.contact-form-section::after {
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

.contact-form-section:hover::after {
    opacity: 1;
}

.contact-form-section h3 {
    color: var(--text-primary);
    margin-bottom: 2rem;
    font-size: 1.8rem;
    font-weight: 700;
    background: linear-gradient(135deg, #ffffff, #e0e0e0);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    background-size: 200% 200%;
    animation: formTitleGradient 3s ease-in-out infinite;
    text-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
    position: relative;
    z-index: 3;
}

@keyframes formTitleGradient {
    0%, 100% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
}

.alert {
    padding: 1rem;
    border-radius: 15px;
    margin-bottom: 1.5rem;
    font-weight: 500;
    position: relative;
    overflow: hidden;
    backdrop-filter: blur(10px);
}

.alert-success {
    background: 
        linear-gradient(135deg, rgba(76, 175, 80, 0.9) 0%, rgba(76, 175, 80, 0.8) 100%),
        radial-gradient(circle at 30% 30%, rgba(76, 175, 80, 0.1) 0%, transparent 50%);
    color: white;
    border: 2px solid rgba(255, 255, 255, 0.2);
    box-shadow: 
        0 10px 25px rgba(76, 175, 80, 0.3),
        0 4px 12px rgba(255, 255, 255, 0.1),
        inset 0 1px 0 rgba(255, 255, 255, 0.1);
}

.alert-success::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    background-size: 200% 100%;
    animation: successShimmer 3s linear infinite;
}

@keyframes successShimmer {
    0% { background-position: -200% 0; }
    100% { background-position: 200% 0; }
}

.alert-error {
    background: 
        linear-gradient(135deg, rgba(244, 67, 54, 0.9) 0%, rgba(244, 67, 54, 0.8) 100%),
        radial-gradient(circle at 30% 30%, rgba(244, 67, 54, 0.1) 0%, transparent 50%);
    color: white;
    border: 2px solid rgba(255, 255, 255, 0.2);
    box-shadow: 
        0 10px 25px rgba(244, 67, 54, 0.3),
        0 4px 12px rgba(255, 255, 255, 0.1),
        inset 0 1px 0 rgba(255, 255, 255, 0.1);
}

.alert-error::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    background-size: 200% 100%;
    animation: errorShimmer 3s linear infinite;
}

@keyframes errorShimmer {
    0% { background-position: -200% 0; }
    100% { background-position: 200% 0; }
}

.contact-form {
    position: relative;
    z-index: 2;
}

.form-group {
    margin-bottom: 1.5rem;
    position: relative;
}

.form-group label {
    display: block;
    margin-bottom: 0.75rem;
    font-weight: 600;
    color: var(--text-primary);
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    background-size: 200% 200%;
    animation: labelGradient 4s ease-in-out infinite;
    position: relative;
    z-index: 2;
}

@keyframes labelGradient {
    0%, 100% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
}

.form-control {
    width: 100%;
    padding: 1rem 1.25rem;
    background: 
        linear-gradient(145deg, rgba(255, 255, 255, 0.05) 0%, rgba(255, 255, 255, 0.02) 100%),
        radial-gradient(circle at 30% 30%, rgba(255, 255, 255, 0.03) 0%, transparent 50%);
    border: 2px solid rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    color: var(--text-primary);
    font-size: 1rem;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    backdrop-filter: blur(5px);
}

.form-control::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
    background-size: 200% 100%;
    animation: inputShimmer 3s linear infinite;
    border-radius: 12px;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.form-control:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 
        0 0 0 3px rgba(255, 107, 107, 0.1),
        0 0 10px rgba(255, 107, 107, 0.2);
    background: 
        linear-gradient(145deg, rgba(255, 255, 255, 0.08) 0%, rgba(255, 255, 255, 0.04) 100%),
        radial-gradient(circle at 30% 30%, rgba(255, 255, 255, 0.03) 0%, transparent 50%);
    transform: translateY(-2px);
}

.form-control:focus::before {
    opacity: 1;
}

.form-control option {
    background: var(--dark-tertiary);
    color: var(--text-primary);
    padding: 0.5rem 1rem;
    border: 1px solid var(--border-color);
}

.form-control option:hover {
    background: var(--primary-color);
    color: var(--text-primary);
}

@keyframes inputShimmer {
    0% { background-position: -200% 0; }
    100% { background-position: 200% 0; }
}

textarea.form-control {
    resize: vertical;
    min-height: 120px;
}

.recaptcha-container {
    margin: 1rem 0;
    position: relative;
}

.btn-lg {
    padding: 1rem 2rem;
    font-size: 1.1rem;
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    color: var(--text-primary);
    border: none;
    border-radius: 25px;
    cursor: pointer;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
    box-shadow: 
        0 8px 25px rgba(255, 107, 107, 0.4),
        0 4px 15px rgba(78, 205, 196, 0.2);
}

.btn-lg::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    background-size: 200% 100%;
    animation: buttonShimmer 3s linear infinite;
    border-radius: 25px;
}

.btn-lg:hover {
    transform: translateY(-3px) scale(1.05);
    box-shadow: 
        0 12px 35px rgba(255, 107, 107, 0.6),
        0 6px 20px rgba(78, 205, 196, 0.3);
}

.btn-lg:hover::before {
    animation: buttonShimmer 0.5s linear infinite;
}

@keyframes buttonShimmer {
    0% { background-position: -200% 0; }
    100% { background-position: 200% 0; }
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
