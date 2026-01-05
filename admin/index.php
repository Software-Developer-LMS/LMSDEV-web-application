<?php
session_start();
// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header("Location: ../pages/index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LumnixSolutions Admin Panel | v3.1.0</title>

    <!-- FONTS: JetBrains Mono (Code) & Rajdhani (Headers) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@100..800&family=Rajdhani:wght@500;600;700&display=swap"
        rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <!-- TAILWIND CSS (CDN) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        nexus: {
                            black: '#0A0A0F',
                            dark: '#0f0f16',
                            panel: '#13131f',
                            blue: '#00D4FF',
                            green: '#00FF9D',
                            purple: '#9D4EDD',
                            red: '#FF0055',
                            dim: 'rgba(0, 212, 255, 0.1)',
                        }
                    },
                    fontFamily: {
                        mono: ['"JetBrains Mono"', 'monospace'],
                        header: ['"Rajdhani"', 'sans-serif'],
                    },
                    backgroundImage: {
                        'hex-grid': "url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PHBhdGggZD0iTTAgMjBMMjAgMEw0MCAyMEwyMCA0MHoiIGZpbGw9Im5vbmUiIHN0cm9rZT0iIzAwRDRGRiIgb3BhY2l0eT0iMC4wMyIvPjwvc3ZnPg==')",
                        'scanline': "linear-gradient(to bottom, rgba(255,255,255,0), rgba(255,255,255,0) 50%, rgba(0,0,0,0.1) 50%, rgba(0,0,0,0.1))",
                    },
                    animation: {
                        'spin-slow': 'spin 15s linear infinite',
                        'pulse-fast': 'pulse 1.5s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                        'float': 'float 3s ease-in-out infinite',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-5px)' },
                        }
                    }
                }
            }
        }
    </script>

    <style>
        /* CORE AESTHETICS */
        body {
            background-color: #0A0A0F;
            color: #e2e8f0;
        }

        /* CRT SCANLINE EFFECT */
        .scanline::before {
            content: " ";
            display: block;
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            right: 0;
            background: linear-gradient(to bottom, rgba(18, 16, 16, 0) 50%, rgba(0, 0, 0, 0.25) 50%);
            background-size: 100% 4px;
            z-index: 50;
            pointer-events: none;
        }

        /* SCROLLBARS */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-track {
            background: #0A0A0F;
        }

        ::-webkit-scrollbar-thumb {
            background: #1f2937;
            border-radius: 3px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #00D4FF;
        }

        /* HOLOGRAPHIC CARDS */
        .holo-card {
            background: rgba(19, 19, 31, 0.6);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(0, 212, 255, 0.1);
            box-shadow: 0 0 0 transparent;
            transition: all 0.3s ease;
        }

        .holo-card:hover {
            border-color: rgba(0, 212, 255, 0.5);
            box-shadow: 0 0 15px rgba(0, 212, 255, 0.15);
            transform: translateY(-2px);
        }

        /* TYPING ANIMATION */
        .typewriter {
            overflow: hidden;
            white-space: nowrap;
            border-right: 2px solid #00FF9D;
            animation: typing 3s steps(40, end), blink-caret .75s step-end infinite;
        }

        @keyframes typing {
            from {
                width: 0
            }

            to {
                width: 100%
            }
        }

        @keyframes blink-caret {

            from,
            to {
                border-color: transparent
            }

            50% {
                border-color: #00FF9D
            }
        }

        /* GLITCH TEXT ON HOVER */
        .glitch-hover:hover {
            animation: glitch 0.3s cubic-bezier(.25, .46, .45, .94) both infinite;
            color: #00FF9D;
        }

        @keyframes glitch {
            0% {
                transform: translate(0)
            }

            20% {
                transform: translate(-2px, 2px)
            }

            40% {
                transform: translate(-2px, -2px)
            }

            60% {
                transform: translate(2px, 2px)
            }

            80% {
                transform: translate(2px, -2px)
            }

            100% {
                transform: translate(0)
            }
        }

        .custom-scroll::-webkit-scrollbar {
            width: 4px;
        }
    </style>
</head>

<body
    class="font-mono text-sm h-screen overflow-hidden bg-hex-grid selection:bg-nexus-green selection:text-nexus-black relative">

    <!-- OVERLAYS -->
    <div class="scanline pointer-events-none fixed inset-0 z-50 opacity-20"></div>

    <!-- BOOT SEQUENCE (JS Controlled) -->
    <div id="boot-sequence" class="fixed inset-0 z-[100] bg-nexus-black flex flex-col items-center justify-center">
        <div class="w-96 space-y-2">
            <div id="boot-1" class="text-nexus-blue hidden">[SYSTEM] LumnixSolutions Admin Panel v3.1.0 initializing...</div>
            <div id="boot-2" class="text-nexus-green hidden">> Loading modules... OK</div>
            <div id="boot-3" class="text-nexus-green hidden">> Establishing secure link... OK</div>
            <div id="boot-4" class="text-white font-bold mt-4 hidden tracking-widest">ACCESS GRANTED</div>
        </div>
        <div class="w-64 h-1 bg-gray-800 mt-6 relative overflow-hidden">
            <div id="boot-progress"
                class="absolute top-0 left-0 h-full bg-nexus-blue w-0 transition-all duration-[2000ms] ease-out"></div>
        </div>
    </div>

    <!-- COMMAND PALETTE (Ctrl + K) -->
    <div id="cmd-palette"
        class="fixed inset-0 z-[60] bg-black/80 backdrop-blur-sm hidden items-start justify-center pt-32 transition-opacity duration-200">
        <div
            class="w-[600px] bg-nexus-panel border border-nexus-blue/50 shadow-[0_0_50px_rgba(0,212,255,0.2)] transform scale-100 transition-transform">
            <div class="p-4 border-b border-gray-700 flex items-center">
                <span class="text-nexus-blue mr-2">></span>
                <input type="text" placeholder="Run command..."
                    class="bg-transparent w-full focus:outline-none text-nexus-green font-bold uppercase tracking-wider">
                <span class="text-xs text-gray-500 border border-gray-700 px-2 py-1 rounded">ESC</span>
            </div>
            <div class="p-2">
                <div class="p-2 hover:bg-white/5 cursor-pointer flex justify-between group">
                    <span class="text-gray-400 group-hover:text-white">deploy_production</span>
                    <span class="text-xs text-gray-600 group-hover:text-nexus-blue">CMD</span>
                </div>
                <!-- More commands... -->
            </div>
        </div>
    </div>

    <!-- MAIN INTERFACE -->
    <div id="app-container" class="flex h-full opacity-0 transition-opacity duration-1000">

        <!-- SIDEBAR -->
        <aside
            class="w-16 md:w-20 border-r border-gray-800 bg-nexus-dark flex flex-col items-center py-6 z-40 relative">
            <!-- Logo -->
            <a href="?page=dashboard"
                class="mb-10 text-nexus-blue animate-pulse-fast block hover:scale-110 transition-transform"
                title="Go to Dashboard">
                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                </svg>
            </a>

            <!-- Nav Icons -->
            <nav class="space-y-6 flex-1 w-full flex flex-col items-center">

                <!-- Dashboard -->
                <a href="?page=dashboard"
                    class="relative group cursor-pointer <?php echo (!isset($_GET['page']) || $_GET['page'] == 'dashboard') ? 'text-nexus-blue' : 'text-gray-600'; ?>">
                    <?php if (!isset($_GET['page']) || $_GET['page'] == 'dashboard'): ?>
                        <div class="absolute -left-[18px] top-0 bottom-0 w-1 bg-nexus-blue shadow-[0_0_10px_#00D4FF]"></div>
                    <?php endif; ?>
                    <i class="fa-solid fa-grid-2 text-xl group-hover:text-nexus-blue transition-colors"></i>
                </a>

                <!-- Students -->
                <a href="?page=students"
                    class="relative group cursor-pointer <?php echo (isset($_GET['page']) && $_GET['page'] == 'students') ? 'text-nexus-blue' : 'text-gray-600'; ?>">
                    <?php if (isset($_GET['page']) && $_GET['page'] == 'students'): ?>
                        <div class="absolute -left-[18px] top-0 bottom-0 w-1 bg-nexus-blue shadow-[0_0_10px_#00D4FF]"></div>
                    <?php endif; ?>
                    <i class="fa-solid fa-users text-xl group-hover:text-nexus-green transition-colors"></i>
                </a>

                <!-- Modules -->
                <a href="?page=modules"
                    class="relative group cursor-pointer <?php echo (isset($_GET['page']) && $_GET['page'] == 'modules') ? 'text-nexus-blue' : 'text-gray-600'; ?>">
                    <?php if (isset($_GET['page']) && $_GET['page'] == 'modules'): ?>
                        <div class="absolute -left-[18px] top-0 bottom-0 w-1 bg-nexus-blue shadow-[0_0_10px_#00D4FF]"></div>
                    <?php endif; ?>
                    <i class="fa-solid fa-layer-group text-xl group-hover:text-nexus-purple transition-colors"></i>
                </a>

                <!-- Lessons -->
                <a href="?page=lessons"
                    class="relative group cursor-pointer <?php echo (isset($_GET['page']) && $_GET['page'] == 'lessons') ? 'text-nexus-blue' : 'text-gray-600'; ?>">
                    <?php if (isset($_GET['page']) && $_GET['page'] == 'lessons'): ?>
                        <div class="absolute -left-[18px] top-0 bottom-0 w-1 bg-nexus-blue shadow-[0_0_10px_#00D4FF]"></div>
                    <?php endif; ?>
                    <i class="fa-solid fa-chalkboard-user text-xl group-hover:text-nexus-red transition-colors"></i>
                </a>

                <!-- Assignments -->
                <a href="?page=assignments"
                    class="relative group cursor-pointer <?php echo (isset($_GET['page']) && $_GET['page'] == 'assignments') ? 'text-nexus-blue' : 'text-gray-600'; ?>">
                    <?php if (isset($_GET['page']) && $_GET['page'] == 'assignments'): ?>
                        <div class="absolute -left-[18px] top-0 bottom-0 w-1 bg-nexus-blue shadow-[0_0_10px_#00D4FF]"></div>
                    <?php endif; ?>
                    <i class="fa-solid fa-list-check text-xl group-hover:text-nexus-blue transition-colors"></i>
                </a>

                <!-- Announcements -->
                <a href="?page=announcements"
                    class="relative group cursor-pointer <?php echo (isset($_GET['page']) && $_GET['page'] == 'announcements') ? 'text-nexus-blue' : 'text-gray-600'; ?>">
                    <?php if (isset($_GET['page']) && $_GET['page'] == 'announcements'): ?>
                        <div class="absolute -left-[18px] top-0 bottom-0 w-1 bg-nexus-blue shadow-[0_0_10px_#00D4FF]"></div>
                    <?php endif; ?>
                    <i class="fa-solid fa-bullhorn text-xl group-hover:text-nexus-green transition-colors"></i>
                </a>

            </nav>

            <!-- Bottom Action -->
            <a href="change_password.php" class="mb-4 cursor-pointer text-gray-600 hover:text-nexus-blue">
                <i class="fa-solid fa-key text-xl"></i>
            </a>
            <a href="../pages/logout.php" class="mb-4 cursor-pointer text-gray-600 hover:text-nexus-red">
                <i class="fa-solid fa-power-off text-xl"></i>
            </a>
        </aside>

        <!-- CONTENT AREA -->
        <main class="flex-1 flex flex-col relative z-10 overflow-hidden">

            <!-- HEADER -->
            <header
                class="h-16 border-b border-gray-800 bg-nexus-black/80 backdrop-blur-md flex items-center justify-between px-6 shrink-0">
                <div class="flex items-center gap-4">
                    <h1 class="text-xl font-header tracking-widest text-white uppercase glitch-hover cursor-default">
                        LumnixSolutions<span class="text-nexus-blue">Panel</span>
                    </h1>
                    <div class="h-4 w-px bg-gray-700"></div>
                    <button onclick="toggleCmd()"
                        class="flex items-center gap-2 text-xs text-nexus-green bg-nexus-green/10 border border-nexus-green/30 px-3 py-1 rounded hover:bg-nexus-green/20 transition-all">
                        <span class="font-bold">CTRL + K</span>
                        <span class="opacity-70">COMMAND_PALETTE</span>
                    </button>
                </div>

                <div class="flex items-center gap-6">
                    <!-- Stat Widget -->
                    <div class="text-right hidden md:block">
                        <div class="text-[10px] text-gray-500 uppercase tracking-wider">Net_Traffic</div>
                        <div class="text-xs text-nexus-purple font-bold">1.2 TB/s</div>
                    </div>
                    <!-- User -->
                    <div class="flex items-center gap-3 pl-6 border-l border-gray-800">
                        <div class="text-right">
                            <div class="text-xs text-white font-bold">Admin_01</div>
                            <div class="text-[10px] text-nexus-green">Level 9 Access</div>
                        </div>
                        <div
                            class="w-8 h-8 rounded bg-gray-800 border border-gray-700 flex items-center justify-center text-nexus-blue font-bold">
                            A
                        </div>
                    </div>
                </div>
            </header>

            <!-- DYNAMIC CONTENT -->
            <div class="flex-1 overflow-y-auto p-6 scroll-smooth">
                <?php
                $page = $_GET['page'] ?? 'dashboard';
                // Simple security check for filename
                $page = preg_replace('/[^a-zA-Z0-9_-]/', '', $page);
                $view_file = __DIR__ . "/views/{$page}.php";

                if (file_exists($view_file)) {
                    include $view_file;
                } else {
                    echo "<div class='holo-card p-6 text-nexus-red border border-nexus-red/50'>[ERROR] View node '{$page}' not found. Check system logs.</div>";
                }
                ?>
            </div>

        </main>
    </div>

    <!-- CLIENT-SIDE LOGIC -->
    <script>
        // --- BOOT SEQUENCE LOGIC ---
        // Optimize: Skip if session already initialized? For now, we keep the effect as requested but maybe speed it up.
        document.addEventListener('DOMContentLoaded', () => {
            // Check if already booted in this session to avoid annoyance
            const booted = sessionStorage.getItem('nexus_booted');

            if (booted) {
                document.getElementById('boot-sequence').style.display = 'none';
                document.getElementById('app-container').style.opacity = '1';
                return;
            }

            sessionStorage.setItem('nexus_booted', 'true');

            const sequence = [
                { id: 'boot-1', delay: 300 },
                { id: 'boot-2', delay: 800 },
                { id: 'boot-3', delay: 1400 },
                { id: 'boot-4', delay: 1900 },
            ];

            // 1. Start Progress Bar
            setTimeout(() => {
                document.getElementById('boot-progress').style.width = '100%';
            }, 100);

            // 2. Show Text Steps
            sequence.forEach(step => {
                setTimeout(() => {
                    const el = document.getElementById(step.id);
                    el.classList.remove('hidden');
                    el.classList.add('typewriter');
                }, step.delay);
            });

            // 3. Reveal App
            setTimeout(() => {
                const bootScreen = document.getElementById('boot-sequence');
                const app = document.getElementById('app-container');

                bootScreen.style.opacity = '0';
                bootScreen.style.pointerEvents = 'none';

                setTimeout(() => {
                    app.style.opacity = '1';
                }, 500);
            }, 2500); // Faster boot
        });

        // --- COMMAND PALETTE LOGIC ---
        const cmdPalette = document.getElementById('cmd-palette');

        function toggleCmd() {
            if (cmdPalette.classList.contains('hidden')) {
                cmdPalette.classList.remove('hidden');
                cmdPalette.classList.add('flex');
                cmdPalette.querySelector('input').focus();
            } else {
                cmdPalette.classList.add('hidden');
                cmdPalette.classList.remove('flex');
            }
        }

        document.addEventListener('keydown', (e) => {
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                toggleCmd();
            }
            if (e.key === 'Escape') {
                cmdPalette.classList.add('hidden');
                cmdPalette.classList.remove('flex');
            }
        });
    </script>
</body>

</html>