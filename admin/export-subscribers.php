<?php
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/functions.php';

// Start secure session and require login
start_secure_session();
require_login();

// Get all subscribers
$subscribers = fetchAll("SELECT * FROM newsletter_subscribers ORDER BY subscribe_date DESC");

// Set headers for CSV download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="newsletter_subscribers_' . date('Y-m-d') . '.csv"');

// Create CSV output
$output = fopen('php://output', 'w');

// CSV header
fputcsv($output, ['Email', 'Name', 'Subscribe Date', 'Status']);

// CSV data
foreach ($subscribers as $subscriber) {
    fputcsv($output, [
        $subscriber['email'],
        $subscriber['name'] ?? '',
        $subscriber['subscribe_date'],
        $subscriber['is_active'] ? 'Active' : 'Inactive'
    ]);
}

fclose($output);
exit();
?>
