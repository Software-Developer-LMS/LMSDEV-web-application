<?php
include '../../includes/db_connection.php';

// BASE URL
$base_url = "../index.php?page=submissions";

// Handle Grading Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_grade'])) {
    $sub_id = intval($_POST['submission_id']);
    $marks = intval($_POST['marks']);
    $feedback = $_POST['feedback'];
    $graded_at = date('Y-m-d H:i:s');
    $assignment_id = intval($_POST['assignment_id']); // Need to capture this for redirect

    $grade_sql = "UPDATE submissions SET marks = ?, feedback = ?, graded_at = ? WHERE id = ?";
    $stmt = $conn->prepare($grade_sql);
    $stmt->bind_param("issi", $marks, $feedback, $graded_at, $sub_id);

    if ($stmt->execute()) {
        header("Location: $base_url&assignment_id=$assignment_id&msg=graded");
        exit();
    } else {
        $error = "Error saving grade: " . $stmt->error;
        header("Location: $base_url&assignment_id=$assignment_id&error=" . urlencode($error));
        exit();
    }
}
?>