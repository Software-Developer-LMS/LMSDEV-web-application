<?php
include '../includes/db_connection.php';

// Add legacy_xp column if it doesn't exist
$check = $conn->query("SHOW COLUMNS FROM users LIKE 'legacy_xp'");
if ($check->num_rows == 0) {
    $sql = "ALTER TABLE users ADD COLUMN legacy_xp INT DEFAULT 0";
    if ($conn->query($sql) === TRUE) {
        echo "SUCCESS: 'legacy_xp' column added to users table.";
    } else {
        echo "ERROR: " . $conn->error;
    }
} else {
    echo "NOTICE: 'legacy_xp' column already exists.";
}
?>