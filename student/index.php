<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'student') {
    header("Location: ../pages/index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Portal | LMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-900 text-white flex items-center justify-center h-screen">
    <div class="text-center">
        <h1 class="text-4xl font-bold mb-4">Welcome, <?php echo $_SESSION['user_name']; ?></h1>
        <p class="text-slate-400 mb-8">Access Level: Student</p>
        <div class="space-y-4">
            <p class="text-sm text-gray-500">Student Dashboard UI Under Construction</p>
            <a href="../pages/index.php?logout=true"
                class="inline-block bg-red-500 hover:bg-red-600 px-6 py-2 rounded font-bold transition">Logout</a>
        </div>
    </div>
</body>

</html>