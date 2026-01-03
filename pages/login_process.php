<?php
session_start();
include '../includes/db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    $sql = "SELECT id, name, password, role, change_password, status FROM users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();

        // Status Check First
        if ($row['status'] == 'inactive') {
            header("Location: index.php?error=deactivated");
            exit();
        }

        if (password_verify($password, $row['password'])) {
            // Password correct, start session
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['user_name'] = $row['name'];
            $_SESSION['user_role'] = $row['role'];

            // Force Password Change Check
            if ($row['change_password'] == 1) {
                if ($row['role'] == 'student') {
                    header("Location: ../student/change_password.php");
                    exit();
                } elseif ($row['role'] == 'admin') {
                    header("Location: ../admin/change_password.php");
                    exit();
                }
            }

            if ($row['role'] == 'admin') {
                header("Location: ../admin/index.php");
            } else {
                header("Location: ../student/index.php");
            }
            exit();
        } else {
            // Invalid password
            header("Location: index.php?error=invalid_credentials");
            exit();
        }
    } else {
        // User not found
        header("Location: index.php?error=invalid_credentials");
        exit();
    }
} else {
    header("Location: index.php");
    exit();
}
?>