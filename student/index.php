<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'student') {
    header("Location: ../pages/index.php");
    exit();
}
include '../includes/db_connection.php';

// Fetch Navbar XP
$navbar_uid = $_SESSION['user_id'];
$navbar_xp_res = $conn->query("SELECT (COALESCE(SUM(marks), 0) + (SELECT legacy_xp FROM users WHERE id=$navbar_uid)) as total_xp FROM submissions WHERE user_id=$navbar_uid")->fetch_assoc();
$navbar_xp = $navbar_xp_res['total_xp'] ? intval($navbar_xp_res['total_xp']) : 0;
?>
<!DOCTYPE html>
<html lang="en" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SDLMS - Student Dashboard</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        'nexus-black': '#0A0A0F',
                        'nexus-blue': '#00D4FF',
                        'nexus-green': '#00FF9D',
                        'nexus-purple': '#9D4EDD',
                        'nexus-card': '#111827',
                        'nexus-code': '#1E1E2E',
                    },
                    fontFamily: {
                        'mono': ['JetBrains Mono', 'monospace'],
                        'sans': ['Inter', 'system-ui']
                    },
                    animation: {
                        'pulse-glow': 'pulse-glow 2s infinite',
                        'slide-in': 'slide-in 0.5s ease-out',
                        'spin-slow': 'spin 3s linear infinite',
                        'loading-bar': 'loading-bar 2s ease-in-out infinite',
                    },
                    keyframes: {
                        'pulse-glow': {
                            '0%, 100%': { 'box-shadow': '0 0 5px #00D4FF' },
                            '50%': { 'box-shadow': '0 0 20px #00D4FF' }
                        },
                        'slide-in': {
                            'from': { opacity: '0', transform: 'translateX(-20px)' },
                            'to': { opacity: '1', transform: 'translateX(0)' }
                        },
                        'loading-bar': {
                            '0%': { width: '0%', marginLeft: '0%' },
                            '50%': { width: '100%', marginLeft: '0%' },
                            '100%': { width: '0%', marginLeft: '100%' }
                        }
                    }
                }
            }
        }
    </script>

    <style>
        body {
            background-color: #0A0A0F;
            overflow-x: hidden;
        }

        /* Hexagonal Grid Background */
        .hex-grid-bg {
            background-image:
                radial-gradient(circle at 25% 25%, rgba(0, 212, 255, 0.03) 0%, transparent 50%),
                linear-gradient(0deg, transparent 24%, rgba(0, 255, 157, 0.02) 25%, rgba(0, 255, 157, 0.02) 26%, transparent 27%, transparent 74%, rgba(157, 78, 221, 0.02) 75%, rgba(157, 78, 221, 0.02) 76%, transparent 77%, transparent),
                linear-gradient(90deg, transparent 24%, rgba(0, 255, 157, 0.02) 25%, rgba(0, 255, 157, 0.02) 26%, transparent 27%, transparent 74%, rgba(157, 78, 221, 0.02) 75%, rgba(157, 78, 221, 0.02) 76%, transparent 77%, transparent);
            background-size: 100px 100px;
        }

        .glow-border {
            border: 1px solid rgba(0, 212, 255, 0.2);
            box-shadow: 0 0 15px rgba(0, 212, 255, 0.1);
        }

        .glow-border-green {
            border: 1px solid rgba(0, 255, 157, 0.2);
            box-shadow: 0 0 15px rgba(0, 255, 157, 0.1);
        }

        .code-editor {
            background: #1E1E2E;
            font-family: 'JetBrains Mono', monospace;
        }

        /* Syntax Highlighting */
        .code-keyword {
            color: #F472B6;
        }

        .code-function {
            color: #00D4FF;
        }

        .code-string {
            color: #00FF9D;
        }

        .code-comment {
            color: #6B7280;
        }

        .code-variable {
            color: #FBBF24;
        }

        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #0A0A0F;
        }

        ::-webkit-scrollbar-thumb {
            background: #00D4FF;
            border-radius: 4px;
        }
    </style>

    <link
        href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@300;400;500;700&family=Inter:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="text-gray-200 font-sans hex-grid-bg">

    <!-- Loading Screen -->
    <div id="loading-screen"
        class="fixed inset-0 z-50 bg-nexus-black flex flex-col items-center justify-center transition-opacity duration-700">
        <div class="relative w-24 h-24 mb-6">
            <!-- Outer Ring -->
            <div class="absolute inset-0 border-4 border-nexus-blue/20 rounded-full animate-spin-slow"></div>
            <!-- Inner Spinning Ring -->
            <div
                class="absolute inset-2 border-4 border-t-nexus-green border-r-transparent border-b-nexus-purple border-l-transparent rounded-full animate-spin">
            </div>
            <!-- Center Icon -->
            <div class="absolute inset-0 flex items-center justify-center">
                <i class="fas fa-code text-nexus-blue text-3xl animate-pulse"></i>
            </div>
        </div>

        <!-- Loading Text -->
        <div class="font-mono text-nexus-blue text-lg tracking-wider animate-pulse mb-4">INITIALIZING SDLMS CORE...
        </div>

        <!-- Progress Bar -->
        <div class="w-64 h-1 bg-gray-800 rounded-full overflow-hidden relative">
            <div
                class="h-full bg-gradient-to-r from-nexus-blue to-nexus-green animate-loading-bar absolute top-0 left-0 w-full">
            </div>
        </div>

        <div class="mt-2 text-xs text-gray-500 font-mono">v2.4.0 Stable Build</div>
    </div>

    <!-- Navigation -->
    <nav class="bg-nexus-card/80 backdrop-blur-lg border-b border-nexus-blue/20 p-4 sticky top-0 z-40">
        <div class="container mx-auto flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <a href="?page=dashboard" class="text-nexus-blue text-2xl font-mono font-bold">
                    <i class="fas fa-code mr-2"></i>SDL<span class="text-nexus-green">MS</span>
                </a>
                <div class="hidden md:flex space-x-6 pl-6">
                    <a href="?page=dashboard"
                        class="<?php echo (!isset($_GET['page']) || $_GET['page'] == 'dashboard') ? 'text-nexus-blue font-bold' : 'text-gray-300 hover:text-nexus-green'; ?> transition">Dashboard</a>
                    <a href="?page=lessons"
                        class="<?php echo (isset($_GET['page']) && $_GET['page'] == 'lessons') ? 'text-nexus-blue font-bold' : 'text-gray-300 hover:text-nexus-green'; ?> transition">Lessons</a>
                    <a href="?page=assignments"
                        class="<?php echo (isset($_GET['page']) && $_GET['page'] == 'assignments') ? 'text-nexus-blue font-bold' : 'text-gray-300 hover:text-nexus-green'; ?> transition">Assignments</a>
                </div>
            </div>

            <div class="flex items-center space-x-6">
                <!-- Notifications -->
                <div class="relative group cursor-pointer">
                    <i class="fas fa-bell text-nexus-blue hover:text-nexus-green text-xl transition"></i>
                    <span class="absolute -top-1 -right-1 h-3 w-3 bg-nexus-purple rounded-full animate-pulse"></span>
                </div>

                <!-- XP Badge -->
                <div
                    class="hidden md:flex items-center space-x-2 px-3 py-1 bg-gradient-to-r from-nexus-blue/20 to-nexus-purple/20 rounded-lg border border-nexus-blue/30">
                    <i class="fas fa-bolt text-nexus-green"></i>
                    <div class="font-mono text-sm">
                        <span class="text-nexus-blue font-bold"><?php echo number_format($navbar_xp); ?></span> XP
                    </div>
                </div>

                <!-- Profile -->
                <a href="?page=profile" class="flex items-center space-x-3 group">
                    <div
                        class="h-10 w-10 rounded-full border-2 border-transparent group-hover:border-nexus-blue transition-all overflow-hidden relative">
                        <?php
                        // Fetch photo
                        $uid = $_SESSION['user_id'];
                        $u_res = $conn->query("SELECT profile_photo FROM users WHERE id=$uid");
                        $u_dat = $u_res->fetch_assoc();
                        if (!empty($u_dat['profile_photo'])): ?>
                            <img src="../uploads/profile_photos/<?php echo $u_dat['profile_photo']; ?>"
                                class="w-full h-full object-cover">
                        <?php else: ?>
                            <div
                                class="w-full h-full bg-gradient-to-r from-nexus-blue to-nexus-green flex items-center justify-center text-black font-bold font-mono text-sm">
                                <?php echo substr($_SESSION['user_name'], 0, 2); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="hidden md:block text-left">
                        <div class="font-mono text-sm font-bold group-hover:text-nexus-blue transition">
                            <?php echo $_SESSION['user_name']; ?>
                        </div>
                        <span class="text-xs text-nexus-green">OPERATIVE</span>
                    </div>
                </a>
                <a href="../pages/logout.php" class="ml-4 text-xs text-nexus-red hover:text-white transition">
                    <i class="fas fa-power-off text-lg"></i>
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mx-auto p-6 relative z-10 min-h-screen">
        <?php
        $page = $_GET['page'] ?? 'dashboard';
        $view_file = __DIR__ . "/views/{$page}.php";

        if (file_exists($view_file)) {
            include $view_file;
        } else {
            echo "<div class='text-center text-nexus-red mt-20'>404 - MODULE NOT FOUND</div>";
        }
        ?>
    </div>

    <script>
        // Loading Screen Logic
        window.addEventListener('load', () => {
            setTimeout(() => {
                const loader = document.getElementById('loading-screen');
                loader.classList.add('opacity-0', 'pointer-events-none');
                setTimeout(() => loader.remove(), 700);
            }, 1000);
        });
    </script>
</body>

</html>