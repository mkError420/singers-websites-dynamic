<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/functions.php';

// Start secure session
start_secure_session();

// Set content type
header('Content-Type: application/json');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Get and validate input
$email = sanitize_input($_POST['email'] ?? '');
$name = sanitize_input($_POST['name'] ?? '');
$csrf_token = $_POST['csrf_token'] ?? '';

// Validate CSRF token
if (!verify_csrf_token($csrf_token)) {
    echo json_encode(['success' => false, 'message' => 'Security token expired. Please try again.']);
    exit;
}

// Validate email
if (empty($email)) {
    echo json_encode(['success' => false, 'message' => 'Email address is required.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
    exit;
}

// Check if email already exists
$existing_subscriber = fetchOne(
    "SELECT id, is_active FROM newsletter_subscribers WHERE email = ?",
    [$email]
);

if ($existing_subscriber) {
    if ($existing_subscriber['is_active']) {
        echo json_encode(['success' => false, 'message' => 'You are already subscribed to our newsletter.']);
        exit;
    } else {
        // Reactivate existing subscription
        $result = updateData(
            'newsletter_subscribers',
            ['is_active' => 1, 'name' => $name],
            'id = ?',
            [$existing_subscriber['id']]
        );
        
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Welcome back! You have been re-subscribed to our newsletter.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to reactivate subscription. Please try again.']);
        }
        exit;
    }
}

// Rate limiting (optional - check if same IP subscribed recently)
$ip_address = $_SERVER['REMOTE_ADDR'];
$recent_subscription = fetchOne(
    "SELECT id FROM newsletter_subscribers WHERE ip_address = ? AND subscribe_date > DATE_SUB(NOW(), INTERVAL 1 HOUR)",
    [$ip_address]
);

if ($recent_subscription) {
    echo json_encode(['success' => false, 'message' => 'Please wait before subscribing again.']);
    exit;
}

// Insert new subscriber
$subscriber_data = [
    'email' => $email,
    'name' => $name,
    'ip_address' => $ip_address
];

$result = insertData('newsletter_subscribers', $subscriber_data);

if ($result) {
    // Send welcome email
    $welcome_subject = "Welcome to " . APP_NAME . " Newsletter!";
    $welcome_body = "Dear " . ($name ?: 'Subscriber') . ",\n\n";
    $welcome_body .= "Thank you for subscribing to the " . APP_NAME . " newsletter! You'll now receive updates about:\n\n";
    $welcome_body .= "• New music releases\n";
    $welcome_body .= "• Upcoming tour dates\n";
    $welcome_body .= "• Exclusive content and behind-the-scenes\n";
    $welcome_body .= "• Special offers and announcements\n\n";
    $welcome_body .= "We're excited to share our musical journey with you!\n\n";
    $welcome_body .= "Best regards,\n";
    $welcome_body .= "The " . APP_NAME . " Team\n\n";
    $welcome_body .= "If you didn't subscribe to this newsletter, please ignore this email or contact us.";
    
    @send_email($email, $welcome_subject, $welcome_body);
    
    // Notify admin about new subscriber
    $admin_subject = "New Newsletter Subscriber";
    $admin_body = "A new user has subscribed to the newsletter:\n\n";
    $admin_body .= "Email: $email\n";
    $admin_body .= "Name: " . ($name ?: 'Not provided') . "\n";
    $admin_body .= "IP Address: $ip_address\n";
    $admin_body .= "Date: " . date('Y-m-d H:i:s');
    
    @send_email(FROM_EMAIL, $admin_subject, $admin_body);
    
    echo json_encode(['success' => true, 'message' => 'Successfully subscribed to newsletter!']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to subscribe. Please try again.']);
}
?>
