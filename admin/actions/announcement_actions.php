<?php
include '../../includes/db_connection.php';

// BASE URL
$base_url = "../index.php?page=announcements";

// Handle Add/Update Announcement
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_announcement'])) {
        $title = $_POST['title'];
        $type = $_POST['type'];
        $msg = $_POST['message'];

        $sql = "INSERT INTO announcements (title, type, message) VALUES ('$title', '$type', '$msg')";

        if ($conn->query($sql) === TRUE) {
            header("Location: $base_url&msg=" . urlencode("Broadcast sent."));
            exit();
        } else {
            $error = "Error: " . $conn->error;
            header("Location: $base_url&error=" . urlencode($error));
            exit();
        }
    } elseif (isset($_POST['update_announcement'])) {
        $id = intval($_POST['id']);
        $title = $_POST['title'];
        $type = $_POST['type'];
        $msg = $_POST['message'];

        $sql = "UPDATE announcements SET title='$title', type='$type', message='$msg' WHERE id=$id";

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

// Handle Delete Announcement
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    $conn->query("DELETE FROM announcements WHERE id=$id");
    header("Location: $base_url&msg=deleted");
    exit();
}
?>