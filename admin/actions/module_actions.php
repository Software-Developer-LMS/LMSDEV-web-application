<?php
include '../../includes/db_connection.php';

// BASE URL
$base_url = "../index.php?page=modules";

// Handle Add/Update Module
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_module'])) {
        $title = $_POST['module_title'];
        $desc = $_POST['description'];
        $order = $_POST['order_no'];

        $sql = "INSERT INTO modules (module_title, description, order_no) VALUES ('$title', '$desc', '$order')";
        if ($conn->query($sql) === TRUE) {
            header("Location: $base_url&msg=" . urlencode("Module initialized."));
            exit();
        } else {
            $error = "Error: " . $conn->error;
            header("Location: $base_url&error=" . urlencode($error));
            exit();
        }
    } elseif (isset($_POST['update_module'])) {
        $id = intval($_POST['id']);
        $title = $_POST['module_title'];
        $desc = $_POST['description'];
        $order = $_POST['order_no'];

        $sql = "UPDATE modules SET module_title='$title', description='$desc', order_no='$order' WHERE id=$id";
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

// Handle Delete Module
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    $conn->query("DELETE FROM modules WHERE id=$id");
    header("Location: $base_url&msg=deleted");
    exit();
}
?>