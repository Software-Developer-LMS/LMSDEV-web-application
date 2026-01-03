<?php
include '../includes/db_connection.php';

echo "<h1>System Diagnostic</h1>";

// 1. Check/Add profile_photo to users
$check = $conn->query("SHOW COLUMNS FROM users LIKE 'profile_photo'");
if ($check->num_rows == 0) {
    echo "<li>'profile_photo' column MISSING in 'users'. Attempting to add...</li>";
    if ($conn->query("ALTER TABLE users ADD COLUMN profile_photo VARCHAR(255) DEFAULT NULL")) {
        echo "<li>SUCCESS: Added 'profile_photo'.</li>";
    } else {
        echo "<li style='color:red'>FAILED: " . $conn->error . "</li>";
    }
} else {
    echo "<li>'profile_photo' column exists in 'users'.</li>";
}

// 2. Check/Add marks to submissions
$check = $conn->query("SHOW COLUMNS FROM submissions LIKE 'marks'");
if ($check->num_rows == 0) {
    echo "<li>'marks' column MISSING in 'submissions'. Attempting to add...</li>";
    if ($conn->query("ALTER TABLE submissions ADD COLUMN marks INT DEFAULT NULL")) {
        echo "<li>SUCCESS: Added 'marks'.</li>";
    } else {
        echo "<li style='color:red'>FAILED: " . $conn->error . "</li>";
    }
} else {
    echo "<li>'marks' column exists in 'submissions'.</li>";
}

// 3. Check/Add graded_at to submissions
$check = $conn->query("SHOW COLUMNS FROM submissions LIKE 'graded_at'");
if ($check->num_rows == 0) {
    echo "<li>'graded_at' column MISSING in 'submissions'. Attempting to add...</li>";
    if ($conn->query("ALTER TABLE submissions ADD COLUMN graded_at DATETIME DEFAULT NULL")) {
        echo "<li>SUCCESS: Added 'graded_at'.</li>";
    } else {
        echo "<li style='color:red'>FAILED: " . $conn->error . "</li>";
    }
} else {
    echo "<li>'graded_at' column exists in 'submissions'.</li>";
}

// 4. Check/Add legacy_xp to users
$check = $conn->query("SHOW COLUMNS FROM users LIKE 'legacy_xp'");
if ($check->num_rows == 0) {
    echo "<li>'legacy_xp' column MISSING in 'users'. Attempting to add...</li>";
    if ($conn->query("ALTER TABLE users ADD COLUMN legacy_xp INT DEFAULT 0")) {
        echo "<li>SUCCESS: Added 'legacy_xp'.</li>";
    } else {
        echo "<li style='color:red'>FAILED: " . $conn->error . "</li>";
    }
} else {
    echo "<li>'legacy_xp' column exists in 'users'.</li>";
}

// 4. Test Student Query
echo "<h3>Testing Student Query...</h3>";
$sql = "SELECT u.id, u.student_id, u.name, u.email, u.profile_photo, u.status, u.created_at, SUM(s.marks) as total_xp 
        FROM users u 
        LEFT JOIN submissions s ON u.id = s.user_id 
        WHERE u.role='student' 
        GROUP BY u.id, u.student_id, u.name, u.email, u.profile_photo, u.status, u.created_at 
        ORDER BY u.created_at DESC";

$result = $conn->query($sql);

if ($result) {
    echo "<h2 style='color:green'>STUDENT QUERY SUCCESS! Found " . $result->num_rows . " records.</h2>";
} else {
    echo "<h2 style='color:red'>STUDENT QUERY FAILED: " . $conn->error . "</h2>";
}

// 4. Test Submissions Query
echo "<h3>Testing Submissions Query (Assigment ID: 1)...</h3>";
$assign_id = 1;
$sql_sub = "SELECT u.id, u.name, u.email, u.student_id as student_code, 
               s.id as submission_id, s.file_path, s.github_link, s.submitted_at, s.marks, s.feedback 
        FROM users u 
        LEFT JOIN submissions s ON u.id = s.user_id AND s.assignment_id = $assign_id
        WHERE u.role = 'student' 
        ORDER BY u.name ASC";

$res_sub = $conn->query($sql_sub);

if ($res_sub) {
    echo "<h2 style='color:green'>SUBMISSIONS QUERY SUCCESS! Found " . $res_sub->num_rows . " records.</h2>";
} else {
    echo "<h2 style='color:red'>SUBMISSIONS QUERY FAILED: " . $conn->error . "</h2>";
}

echo "<br><a href='index.php?page=students'>Return to Students Page</a>";
?>