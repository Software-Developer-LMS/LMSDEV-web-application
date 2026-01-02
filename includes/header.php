<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DevCourse LMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            900: '#0c4a6e',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        .glass {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.18);
        }

        .glass-dark {
            background: rgba(17, 24, 39, 0.7);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        body {
            background-image: linear-gradient(to bottom right, #f0f9ff, #e0f2fe);
            min-height: 100vh;
        }
    </style>
</head>

<body class="text-gray-800 antialiased">
    <?php if (isset($_SESSION['user_id'])): ?>
        <nav class="glass fixed w-full z-50 top-0 start-0 border-b border-gray-200">
            <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-4">
                <a href="#" class="flex items-center space-x-3 rtl:space-x-reverse">
                    <span
                        class="self-center text-2xl font-semibold whitespace-nowrap text-brand-900 tracking-tight">DevLMS</span>
                </a>
                <div class="flex md:order-2 space-x-3 md:space-x-0 rtl:space-x-reverse">
                    <div class="flex items-center gap-4">
                        <span class="text-sm font-medium text-gray-600 hidden md:block">
                            <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?>
                            (<?php echo ucfirst($_SESSION['user_role'] ?? ''); ?>)
                        </span>
                        <a href="/Software developer LMS/auth/logout.php"
                            class="text-white bg-brand-600 hover:bg-brand-700 focus:ring-4 focus:outline-none focus:ring-brand-300 font-medium rounded-lg text-sm px-4 py-2 text-center transition-all shadow-lg shadow-brand-500/30">Logout</a>
                    </div>
                </div>
            </div>
        </nav>
        <div class="h-16"></div> <!-- Spacer for fixed header -->
    <?php endif; ?>
    <main class="p-4">