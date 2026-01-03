<?php
session_start();
include '../includes/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {

    // Check for POST max size limit violation
    if (empty($_POST) && empty($_FILES) && $_SERVER['CONTENT_LENGTH'] > 0) {
        $max_size = ini_get('post_max_size');
        die("Error: File exceeds server upload limit ($max_size). Please contact admin or reduce file size.");
    }

    $user_id = $_SESSION['user_id'];
    $assignment_id = intval($_POST['assignment_id'] ?? 0); // Use null coalescing to avoid warning
    $github_link = $_POST['github_link'] ?? '';            // Use null coalescing

    $file_path = null;

    // Validation: Ensure at least one method is used
    if (empty($github_link) && (!isset($_FILES['submission_file']) || $_FILES['submission_file']['error'] != 0)) {
        die("Error: You must provide either a GitHub Link or a Project File.");
    }

    // Handle File Upload
    if (isset($_FILES['submission_file']) && $_FILES['submission_file']['error'] == 0) {
        $upload_dir = '../uploads/';
        $file_name = $_FILES['submission_file']['name'];
        $file_tmp = $_FILES['submission_file']['tmp_name'];
        $file_size = $_FILES['submission_file']['size'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        $allowed = array('pdf', 'zip');

        if (in_array($file_ext, $allowed)) {
            if ($file_size < 100000000) { // 100MB limit
                // Randomize filename
                $new_file_name = uniqid('sub_') . '.' . $file_ext;
                $destination = $upload_dir . $new_file_name;

                if (move_uploaded_file($file_tmp, $destination)) {
                    $file_path = $new_file_name;
                } else {
                    die("Error uploading file.");
                }
            } else {
                die("File too large (Max 100MB).");
            }
        } else {
            die("Invalid file type. Only PDF and ZIP allowed.");
        }
    }

    // Check if updating or inserting
    $check_sql = "SELECT id, file_path FROM submissions WHERE user_id=$user_id AND assignment_id=$assignment_id";
    $result = $conn->query($check_sql);

    if ($result->num_rows > 0) {
        // Update
        $row = $result->fetch_assoc();

        $sql = "UPDATE submissions SET github_link='$github_link', submitted_at=NOW()";

        // If a new file is uploaded, delete the old one
        if ($file_path) {
            if (!empty($row['file_path'])) {
                $old_file = '../uploads/' . $row['file_path'];
                if (file_exists($old_file)) {
                    unlink($old_file);
                }
            }
            $sql .= ", file_path='$file_path'";
        }
        $sql .= " WHERE user_id=$user_id AND assignment_id=$assignment_id";
    } else {
        // Insert
        // Assuming file_path might be null if they only submitted a link, or vice versa. 
        // But usually at least one is required. For now trusting input.
        $sql = "INSERT INTO submissions (user_id, assignment_id, file_path, github_link) VALUES ('$user_id', '$assignment_id', '$file_path', '$github_link')";
    }

    if ($conn->query($sql) === TRUE) {
        header("Location: index.php?page=assignments&success=1");
    } else {
        echo "Error: " . $conn->error;
    }

} else {
    header("Location: index.php");
}
?>