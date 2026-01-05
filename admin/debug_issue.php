<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../includes/db_connection.php';

echo "<h1>Diagnostic Report</h1>";

// Check Database Connection
if ($conn->connect_error) {
    die("<p style='color:red'>Connection failed: " . $conn->connect_error . "</p>");
}
echo "<p style='color:green'>Database connected successfully.</p>";

// Check 'users' table structure
$result = $conn->query("SHOW COLUMNS FROM users");
if ($result) {
    echo "<h2>Users Table Columns:</h2><ul>";
    $columns = [];
    while ($row = $result->fetch_assoc()) {
        echo "<li>" . $row['Field'] . " (" . $row['Type'] . ")</li>";
        $columns[] = $row['Field'];
    }
    echo "</ul>";

    if (in_array('mobile_number', $columns)) {
        echo "<p style='color:green'>'mobile_number' column EXISTS.</p>";
    } else {
        echo "<p style='color:red'>'mobile_number' column MISSING.</p>";
    }
} else {
    echo "<p style='color:red'>Error fetching columns: " . $conn->error . "</p>";
}

// Check SQL Query used in students.php
$sql = "SELECT u.id, u.student_id, u.name, u.email, u.mobile_number, u.profile_photo, u.status, u.created_at, 
               (COALESCE(SUM(s.marks), 0) + u.legacy_xp) as total_xp 
        FROM users u 
        LEFT JOIN submissions s ON u.id = s.user_id 
        WHERE u.role='student' 
        GROUP BY u.id, u.student_id, u.name, u.email, u.mobile_number, u.profile_photo, u.status, u.created_at, u.legacy_xp 
        ORDER BY u.created_at DESC";

echo "<h2>Testing Main Student Query:</h2>";
$query_result = $conn->query($sql);
if ($query_result) {
    echo "<p style='color:green'>Query executed successfully. Found " . $query_result->num_rows . " students.</p>";
} else {
    echo "<p style='color:red'>Query Failed: " . $conn->error . "</p>";
}
?>