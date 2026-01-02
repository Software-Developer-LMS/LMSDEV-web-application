<?php
session_start();
require_once '../config/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login_id = trim($_POST['login_id']); // Can be email or student_id
    $password = $_POST['password'];

    if (empty($login_id) || empty($password)) {
        $error = "Please fill in all fields.";
    } else {
        try {
            // Check if input looks like an email or student ID
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :id OR student_id = :id");
            $stmt->execute(['id' => $login_id]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                if ($user['status'] === 'inactive') {
                    $error = "Your account is inactive. Contact admin.";
                } else {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['name'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['user_role'] = $user['role'];

                    if ($user['role'] === 'admin') {
                        header("Location: ../admin/dashboard.php");
                    } else {
                        header("Location: ../student/dashboard.php");
                    }
                    exit();
                }
            } else {
                $error = "Invalid credentials.";
            }
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - DevLMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background-image: linear-gradient(to bottom right, #f0f9ff, #e0f2fe);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .glass {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.18);
        }
    </style>
</head>

<body>
    <div class="w-full max-w-md bg-white rounded-2xl shadow-xl overflow-hidden glass p-8">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800 tracking-tight">Welcome Back</h1>
            <p class="text-gray-500 mt-2">Sign in to access your course.</p>
        </div>

        <?php if ($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">
                    <?php echo htmlspecialchars($error); ?>
                </span>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2 ml-1" for="login_id">
                    Email or Student ID
                </label>
                <input
                    class="shadow-sm appearance-none border border-gray-200 rounded-xl w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                    id="login_id" name="login_id" type="text" placeholder="SD001 or name@example.com" required>
            </div>
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2 ml-1" for="password">
                    Password
                </label>
                <input
                    class="shadow-sm appearance-none border border-gray-200 rounded-xl w-full py-3 px-4 text-gray-700 mb-3 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                    id="password" name="password" type="password" placeholder="******************" required>
            </div>
            <div class="flex items-center justify-between mb-6">
                <a class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800" href="#">
                    Forgot Password?
                </a>
            </div>
            <button
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-xl focus:outline-none focus:shadow-outline transition-all shadow-lg shadow-blue-500/30 transform hover:-translate-y-0.5"
                type="submit">
                Sign In
            </button>
        </form>
        <p class="text-center text-gray-400 text-xs mt-8">
            &copy;2026 DevLMS. Physical Classes Only.
        </p>
    </div>
</body>

</html>