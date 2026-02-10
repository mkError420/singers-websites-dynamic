<?php
// Start session and check login
session_start();

// Simple authentication check
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/functions.php';

try {
    // Get all subscribers
    $subscribers = fetchAll("SELECT * FROM newsletter_subscribers ORDER BY subscribe_date DESC");
    
    if (empty($subscribers)) {
        // If no subscribers, create empty CSV with headers only
        $subscribers = [];
    }
    
    // Set headers for CSV download
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="newsletter_subscribers_' . date('Y-m-d_H-i-s') . '.csv"');
    header('Cache-Control: no-cache, must-revalidate');
    header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
    
    // Create CSV output
    $output = fopen('php://output', 'w');
    
    // Add BOM for UTF-8 support
    fwrite($output, "\xEF\xBB\xBF");
    
    // CSV header
    fputcsv($output, ['Email', 'Name', 'Subscribe Date', 'Status', 'IP Address', 'Referrer']);
    
    // CSV data
    foreach ($subscribers as $subscriber) {
        fputcsv($output, [
            $subscriber['email'],
            $subscriber['name'] ?? '',
            $subscriber['subscribe_date'],
            $subscriber['is_active'] ? 'Active' : 'Inactive',
            $subscriber['ip_address'] ?? 'Unknown',
            $subscriber['referrer'] ?? 'Direct'
        ]);
    }
    
    fclose($output);
    
} catch (Exception $e) {
    // Handle errors gracefully
    header('Content-Type: text/plain');
    echo 'Error exporting subscribers: ' . $e->getMessage();
    exit;
}

exit();
?>
