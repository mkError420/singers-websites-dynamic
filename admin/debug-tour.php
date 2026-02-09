<?php
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/functions.php';

echo "<h2>Tour Dates Debug</h2>";

// Test database connection
echo "<h3>Database Connection Test:</h3>";
try {
    $test = fetchOne("SELECT COUNT(*) as count FROM tour_dates");
    echo "<p>Database connection: SUCCESS - Found " . $test['count'] . " tour dates</p>";
} catch (Exception $e) {
    echo "<p>Database connection: FAILED - " . $e->getMessage() . "</p>";
}

echo "<h3>Direct Query Test:</h3>";
try {
    $tours = fetchAll("SELECT * FROM tour_dates ORDER BY event_date DESC LIMIT 3");
    echo "<p>Query executed: Found " . count($tours) . " tour dates</p>";
    
    if (!empty($tours)) {
        echo "<table border='1'>";
        echo "<tr><th>ID</th><th>Event Name</th><th>Venue</th><th>Date</th></tr>";
        foreach ($tours as $tour) {
            echo "<tr>";
            echo "<td>" . $tour['id'] . "</td>";
            echo "<td>" . htmlspecialchars($tour['event_name']) . "</td>";
            echo "<td>" . htmlspecialchars($tour['venue']) . "</td>";
            echo "<td>" . $tour['event_date'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No tour dates found in database</p>";
    }
} catch (Exception $e) {
    echo "<p>Query failed: " . $e->getMessage() . "</p>";
}
?>
