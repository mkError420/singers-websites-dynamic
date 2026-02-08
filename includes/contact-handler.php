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
$name = sanitize_input($_POST['name'] ?? '');
$email = sanitize_input($_POST['email'] ?? '');
$subject = sanitize_input($_POST['subject'] ?? '');
$message = sanitize_input($_POST['message'] ?? '');
$csrf_token = $_POST['csrf_token'] ?? '';

// Validate CSRF token
if (!verify_csrf_token($csrf_token)) {
    echo json_encode(['success' => false, 'message' => 'Security token expired. Please try again.']);
    exit;
}

// Validate required fields
if (empty($name) || empty($email) || empty($subject) || empty($message)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required.']);
    exit;
}

// Validate email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
    exit;
}

// Rate limiting (optional - check if same IP sent message recently)
$ip_address = $_SERVER['REMOTE_ADDR'];
$recent_message = fetchOne(
    "SELECT id FROM contact_messages WHERE ip_address = ? AND created_at > DATE_SUB(NOW(), INTERVAL 5 MINUTE)",
    [$ip_address]
);

if ($recent_message) {
    echo json_encode(['success' => false, 'message' => 'Please wait a few minutes before sending another message.']);
    exit;
}

// Insert into database
$contact_data = [
    'name' => $name,
    'email' => $email,
    'subject' => $subject,
    'message' => $message,
    'ip_address' => $ip_address
];

$result = insertData('contact_messages', $contact_data);

if ($result) {
    // Send email notification to admin
    $admin_email = FROM_EMAIL;
    $email_subject = "New Contact Message: $subject";
    $email_body = "You have received a new contact message:\n\n";
    $email_body .= "Name: $name\n";
    $email_body .= "Email: $email\n";
    $email_body .= "Subject: $subject\n\n";
    $email_body .= "Message:\n$message\n\n";
    $email_body .= "IP Address: $ip_address\n";
    $email_body .= "Date: " . date('Y-m-d H:i:s');
    
    // Send email (optional - depends on your server configuration)
    @send_email($admin_email, $email_subject, $email_body);
    
    // Send auto-reply to sender (optional)
    $auto_reply_subject = "Thank you for contacting " . APP_NAME;
    $auto_reply_body = "Dear $name,\n\n";
    $auto_reply_body .= "Thank you for reaching out to us. We have received your message and will get back to you within 24-48 hours.\n\n";
    $auto_reply_body .= "Your message:\n$message\n\n";
    $auto_reply_body .= "Best regards,\n";
    $auto_reply_body .= "The " . APP_NAME . " Team";
    
    @send_email($email, $auto_reply_subject, $auto_reply_body);
    
    echo json_encode(['success' => true, 'message' => 'Message sent successfully!']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to send message. Please try again.']);
}
?>
