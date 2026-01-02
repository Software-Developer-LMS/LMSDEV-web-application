<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function check_login()
{
    if (!isset($_SESSION['user_id'])) {
        header("Location: /Software developer LMS/auth/login.php");
        exit();
    }
}

function check_admin()
{
    check_login();
    if ($_SESSION['user_role'] !== 'admin') {
        header("Location: /Software developer LMS/student/dashboard.php");
        exit();
    }
}

function check_student()
{
    check_login();
    if ($_SESSION['user_role'] !== 'student') {
        header("Location: /Software developer LMS/admin/dashboard.php");
        exit();
    }
}
?>