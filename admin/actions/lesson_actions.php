<?php
include '../../includes/db_connection.php';

// BASE URL
$base_url = "../index.php?page=lessons";

// Handle Add/Update Lesson
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $notes_file = null;

    // File Upload Handler
    if (isset($_FILES['notes_file']) && $_FILES['notes_file']['error'] == 0) {
        $allowed = ['pdf'];
        $ext = strtolower(pathinfo($_FILES['notes_file']['name'], PATHINFO_EXTENSION));

        if (in_array($ext, $allowed)) {
            $new_name = uniqid('note_') . '.pdf';
            // Adjusted path for actions folder
            if (move_uploaded_file($_FILES['notes_file']['tmp_name'], '../../uploads/' . $new_name)) {
                $notes_file = $new_name;
            }
        }
    }

    if (isset($_POST['add_lesson'])) {
        $module_id = $_POST['module_id'];
        $title = $_POST['lesson_title'];
        $date = $_POST['class_date'];
        $start = $_POST['start_time'];
        $end = $_POST['end_time'];
        $loc = $_POST['location'];
        $desc = $_POST['description'];

        $sql = "INSERT INTO lessons (module_id, lesson_title, class_date, start_time, end_time, location, description, notes_file) 
                VALUES ('$module_id', '$title', '$date', '$start', '$end', '$loc', '$desc', '$notes_file')";

        if ($conn->query($sql) === TRUE) {
            header("Location: $base_url&msg=" . urlencode("Lesson scheduled."));
            exit();
        } else {
            $error = "Error: " . $conn->error;
            header("Location: $base_url&error=" . urlencode($error));
            exit();
        }
    } elseif (isset($_POST['update_lesson'])) {
        $id = intval($_POST['id']);
        $module_id = $_POST['module_id'];
        $title = $_POST['lesson_title'];
        $date = $_POST['class_date'];
        $start = $_POST['start_time'];
        $end = $_POST['end_time'];
        $loc = $_POST['location'];
        $desc = $_POST['description'];

        $sql = "UPDATE lessons SET module_id='$module_id', lesson_title='$title', class_date='$date', 
                start_time='$start', end_time='$end', location='$loc', description='$desc'";

        if ($notes_file) {
            // Delete old file if exists
            $old_sql = "SELECT notes_file FROM lessons WHERE id=$id";
            $old_res = $conn->query($old_sql);
            if ($old_res && $old_row = $old_res->fetch_assoc()) {
                if (!empty($old_row['notes_file'])) {
                    $old_path = '../../uploads/' . $old_row['notes_file']; // Adjusted path
                    if (file_exists($old_path)) {
                        unlink($old_path);
                    }
                }
            }
            $sql .= ", notes_file='$notes_file'";
        }

        $sql .= " WHERE id=$id";

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

// Handle Delete Lesson
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);

    // Delete associated notes file
    $check_sql = "SELECT notes_file FROM lessons WHERE id=$id";
    $check_res = $conn->query($check_sql);
    if ($check_res && $lesson = $check_res->fetch_assoc()) {
        if (!empty($lesson['notes_file'])) {
            $file_path = '../../uploads/' . $lesson['notes_file']; // Adjusted path
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }
    }

    $conn->query("DELETE FROM lessons WHERE id=$id");
    header("Location: $base_url&msg=deleted");
    exit();
}
?>