<?php
include __DIR__ . '/../includes/db_connection.php';

// Check if column exists
$check_col = $conn->query("SHOW COLUMNS FROM users LIKE 'mobile_number'");

if ($check_col->num_rows == 0) {
    // Add column
    $sql = "ALTER TABLE users ADD COLUMN mobile_number VARCHAR(20) DEFAULT NULL AFTER email";
    if ($conn->query($sql) === TRUE) {
        echo "Successfully added 'mobile_number' column to 'users' table.";
    } else {
        echo "Error creating column: " . $conn->error;
    }
} else {
    echo "'mobile_number' column already exists.";
}
?>