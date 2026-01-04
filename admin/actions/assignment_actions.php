<?php
include '../../includes/db_connection.php';

// BASE URL
$base_url = "../index.php?page=assignments";

// Handle Add/Update Assignment
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_assignment'])) {
        $module_id = $_POST['module_id'];
        $title = $_POST['title'];
        $desc = $_POST['description'];
        $deadline = $_POST['deadline'];

        $file_path = NULL;
        if (isset($_FILES['assignment_file']) && $_FILES['assignment_file']['error'] == 0) {
            $upload_dir = '../../uploads/assignments/'; // Adjusted path
            if (!is_dir($upload_dir))
                mkdir($upload_dir, 0777, true);

            $file_ext = pathinfo($_FILES['assignment_file']['name'], PATHINFO_EXTENSION);
            $file_name = time() . '_' . uniqid() . '.' . $file_ext;

            if (move_uploaded_file($_FILES['assignment_file']['tmp_name'], $upload_dir . $file_name)) {
                $file_path = $file_name;
            }
        }

        $sql = "INSERT INTO assignments (module_id, title, description, deadline, assignment_file) 
                VALUES ('$module_id', '$title', '$desc', '$deadline', " . ($file_path ? "'$file_path'" : "NULL") . ")";

        if ($conn->query($sql) === TRUE) {
            header("Location: $base_url&msg=" . urlencode("Objective set."));
            exit();
        } else {
            $error = "Error: " . $conn->error;
            header("Location: $base_url&error=" . urlencode($error));
            exit();
        }
    } elseif (isset($_POST['update_assignment'])) {
        $id = intval($_POST['id']);
        $module_id = $_POST['module_id'];
        $title = $_POST['title'];
        $desc = $_POST['description'];
        $deadline = $_POST['deadline'];

        $file_update_sql = "";
        if (isset($_FILES['assignment_file']) && $_FILES['assignment_file']['error'] == 0) {
            $upload_dir = '../../uploads/assignments/'; // Adjusted path
            if (!is_dir($upload_dir))
                mkdir($upload_dir, 0777, true);

            // Delete old file
            $old_file_res = $conn->query("SELECT assignment_file FROM assignments WHERE id=$id");
            if ($old_file_res->num_rows > 0) {
                $old_file = $old_file_res->fetch_assoc()['assignment_file'];
                if ($old_file && file_exists($upload_dir . $old_file)) {
                    unlink($upload_dir . $old_file);
                }
            }

            $file_ext = pathinfo($_FILES['assignment_file']['name'], PATHINFO_EXTENSION);
            $file_name = time() . '_' . uniqid() . '.' . $file_ext;

            if (move_uploaded_file($_FILES['assignment_file']['tmp_name'], $upload_dir . $file_name)) {
                $file_update_sql = ", assignment_file='$file_name'";
            }
        }

        $sql = "UPDATE assignments SET module_id='$module_id', title='$title', description='$desc', deadline='$deadline' $file_update_sql WHERE id=$id";

        if ($conn->query($sql) === TRUE) {
            header("Location: $base_url&msg=updated");
            exit();
        } else {
            $error = "Error: " . $conn->error;
            header("Location: $base_url&error=" . urlencode($error));
            exit();
        }
    }
}

// Handle Delete Assignment
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);

    // 1. Fetch all submission files associated with this assignment
    $sub_sql = "SELECT user_id, marks, file_path FROM submissions WHERE assignment_id = $id";
    $sub_res = $conn->query($sub_sql);

    if ($sub_res->num_rows > 0) {
        while ($sub_row = $sub_res->fetch_assoc()) {
            // A. Preserve XP (Add to legacy_xp)
            if (!empty($sub_row['marks']) && $sub_row['marks'] > 0) {
                $uid = $sub_row['user_id'];
                $marks = intval($sub_row['marks']);
                $conn->query("UPDATE users SET legacy_xp = legacy_xp + $marks WHERE id = $uid");
            }

            if (!empty($sub_row['file_path'])) {
                $file_full_path = '../../uploads/' . $sub_row['file_path']; // Adjusted path
                if (file_exists($file_full_path)) {
                    unlink($file_full_path); // Delete file from server
                }
            }
        }
    }

    // Delete Assignment File
    $assign_file_res = $conn->query("SELECT assignment_file FROM assignments WHERE id=$id");
    if ($assign_file_res->num_rows > 0) {
        $assign_file = $assign_file_res->fetch_assoc()['assignment_file'];
        if ($assign_file) {
            $assign_file_path = '../../uploads/assignments/' . $assign_file; // Adjusted path
            if (file_exists($assign_file_path)) {
                unlink($assign_file_path);
            }
        }
    }

    // 2. Delete the assignment (Submissions will cascade delete via FK or be orphaned if no cascade, but files are gone)
    $conn->query("DELETE FROM submissions WHERE assignment_id=$id");

    // 3. Delete Assignment
    $conn->query("DELETE FROM assignments WHERE id=$id");
    header("Location: $base_url&msg=deleted");
    exit();
}
?>