<?php
include '../includes/db_connection.php';

echo "Checking 'legacy_xp' column...\n";
$check = $conn->query("SHOW COLUMNS FROM users LIKE 'legacy_xp'");
if ($check->num_rows > 0) {
    echo "Column 'legacy_xp' EXISTS.\n";
} else {
    echo "Column 'legacy_xp' DOES NOT EXIST.\n";
}

echo "\nTesting SQL Query...\n";
$sql = "SELECT u.id, u.student_id, u.name, u.email, u.profile_photo, u.status, u.created_at, 
               (COALESCE(SUM(s.marks), 0) + u.legacy_xp) as total_xp 
        FROM users u 
        LEFT JOIN submissions s ON u.id = s.user_id 
        WHERE u.role='student' 
        GROUP BY u.id, u.student_id, u.name, u.email, u.profile_photo, u.status, u.created_at, u.legacy_xp 
        ORDER BY u.created_at DESC";

$result = $conn->query($sql);
if ($result) {
    echo "Query SUCCESS. Rows: " . $result->num_rows . "\n";
} else {
    echo "Query FAILED: " . $conn->error . "\n";
}
?>