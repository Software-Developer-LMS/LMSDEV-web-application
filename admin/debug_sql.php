<?php
include '../includes/db_connection.php';

echo "<h2>Table: submissions</h2>";
$res = $conn->query("DESCRIBE submissions");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        echo $row['Field'] . " - " . $row['Type'] . "<br>";
    }
} else {
    echo "Error describing table: " . $conn->error;
}

echo "<h2>Testing Manual Query</h2>";
$sql = "SELECT u.*, SUM(s.marks) as total_xp 
        FROM users u 
        LEFT JOIN submissions s ON u.id = s.user_id 
        WHERE u.role='student' 
        GROUP BY u.id 
        ORDER BY u.created_at DESC";

$result = $conn->query($sql);

if ($result) {
    echo "Query Successful! Rows: " . $result->num_rows;
} else {
    echo "Query Failed! Error: " . $conn->error;
}
?>