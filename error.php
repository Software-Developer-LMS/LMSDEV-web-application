<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SDLMS - Service Error</title>

    <!-- FONTS: JetBrains Mono (Code) & Rajdhani (Headers) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@100..800&family=Rajdhani:wght@500;600;700&display=swap"
        rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

    <!-- TAILWIND CSS (CDN) with NEXUS Config -->
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
        body {
            background-color: #0A0A0F;
            color: #e2e8f0;
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

        /* Scanline Overlay */
        .scanline-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to bottom, rgba(18, 16, 16, 0) 50%, rgba(0, 0, 0, 0.25) 50%);
            background-size: 100% 4px;
            z-index: 50;
            pointer-events: none;
            opacity: 0.3;
        }

        /* Error Digit Animation */
        .error-code-digit {
            display: inline-block;
            text-shadow: 0 0 20px rgba(255, 0, 85, 0.5);
            animation: digitIn 0.8s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
            opacity: 0;
            transform: translateY(-20px);
        }

        @keyframes digitIn {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Glitch Animation */
        .glitch-text {
            animation: glitch 3s infinite;
        }

        @keyframes glitch {

            0%,
            100% {
                transform: translate(0);
            }

            2% {
                transform: translate(-2px, 2px);
            }

            4% {
                transform: translate(2px, -2px);
            }

            6% {
                transform: translate(-2px, -2px);
            }

            8% {
                transform: translate(2px, 2px);
            }

            10% {
                transform: translate(0);
            }
        }
    </style>
</head>

<body class="min-h-screen flex flex-col items-center justify-center p-4 relative overflow-hidden bg-hex-grid font-mono">

    <!-- OVERLAYS -->
    <div class="scanline-overlay"></div>

    <!-- Floating Particles (Tailwindified) -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-1/4 left-1/4 w-3 h-3 bg-nexus-blue rounded-full opacity-20 animate-float"></div>
        <div class="absolute top-1/3 right-1/4 w-2 h-2 bg-nexus-purple rounded-full opacity-20 animate-float"
            style="animation-delay: -1s;"></div>
        <div class="absolute bottom-1/4 left-1/3 w-4 h-4 bg-nexus-green rounded-full opacity-20 animate-float"
            style="animation-delay: -2s;"></div>
        <div class="absolute top-2/3 right-1/3 w-2 h-2 bg-nexus-red rounded-full opacity-20 animate-float"
            style="animation-delay: -3s;"></div>
    </div>

    <!-- Main Container -->
    <div class="relative z-10 w-full max-w-2xl">
        <!-- Main Card -->
        <div class="holo-card rounded-xl overflow-hidden relative">

            <!-- Header -->
            <div class="bg-nexus-black/50 border-b border-gray-800 p-6 relative">
                <div class="flex justify-between items-center">
                    <div class="flex items-center space-x-3">
                        <div class="relative">
                            <div
                                class="w-10 h-10 bg-nexus-blue/10 border border-nexus-blue rounded-lg flex items-center justify-center text-nexus-blue">
                                <i class="fas fa-graduation-cap text-xl"></i>
                            </div>
                            <div class="absolute -top-1 -right-1 w-2 h-2 bg-nexus-red rounded-full animate-pulse"></div>
                        </div>
                        <div>
                            <h1 class="text-xl font-header font-bold tracking-widest text-white uppercase">SDLMS</h1>
                            <p class="text-xs text-gray-500 font-mono">Secure Digital Learning Management</p>
                        </div>
                    </div>
                    <div class="animate-pulse bg-nexus-red/10 border border-nexus-red/30 px-3 py-1 rounded">
                        <span class="text-nexus-red font-mono text-xs flex items-center gap-2 font-bold tracking-wider">
                            <i class="fas fa-exclamation-circle"></i>
                            SYSTEM_FAILURE
                        </span>
                    </div>
                </div>
            </div>

            <!-- Error Content -->
            <div class="p-8 md:p-10 relative bg-gradient-to-b from-transparent to-nexus-black/30">
                <!-- Animated Server Icon -->
                <div class="flex justify-center mb-8">
                    <div class="relative group">
                        <div
                            class="w-24 h-24 rounded-full bg-nexus-dark border border-gray-700 flex items-center justify-center relative z-10 group-hover:border-nexus-red transition-colors duration-500">
                            <i
                                class="fas fa-server text-4xl text-gray-600 group-hover:text-nexus-red transition-colors duration-500"></i>
                        </div>
                        <!-- Orbital Rings -->
                        <div
                            class="absolute inset-0 border border-nexus-red/30 rounded-full animate-[spin_4s_linear_infinite] scale-125">
                        </div>
                        <div
                            class="absolute inset-0 border border-dashed border-nexus-red/20 rounded-full animate-[spin_6s_linear_infinite_reverse] scale-150">
                        </div>

                        <div
                            class="absolute -bottom-2 -right-2 w-10 h-10 bg-nexus-red rounded-full flex items-center justify-center shadow-[0_0_15px_rgba(255,0,85,0.5)] z-20">
                            <i class="fas fa-times text-white text-lg"></i>
                        </div>
                    </div>
                </div>

                <!-- Error Code with Animation -->
                <div class="text-center mb-8">
                    <h2 id="errorCode"
                        class="text-8xl font-header font-bold mb-2 text-transparent bg-clip-text bg-gradient-to-r from-nexus-red to-orange-500 glitch-text leading-none">
                        <span class="error-code-digit" style="animation-delay: 0s">5</span>
                        <span class="error-code-digit" style="animation-delay: 0.1s">0</span>
                        <span class="error-code-digit" style="animation-delay: 0.2s">0</span>
                    </h2>
                    <h3 id="errorTitle" class="text-2xl font-header text-white mb-2 tracking-wide uppercase">
                        Internal Server Error
                    </h3>
                    <p class="text-gray-400 text-sm leading-relaxed max-w-md mx-auto font-mono">
                        [CRITICAL_ERROR] The system encountered an unrecoverable exception. Engineering protocols have
                        been engaged.
                    </p>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center mb-6">
                    <button onclick="window.location.reload()"
                        class="group relative px-6 py-3 bg-nexus-blue/10 border border-nexus-blue text-nexus-blue font-bold rounded hover:bg-nexus-blue hover:text-nexus-black transition-all duration-300 uppercase text-xs tracking-wider flex items-center justify-center gap-2">
                        <i class="fas fa-redo-alt group-hover:animate-spin"></i>
                        <span>Re-Initialize</span>
                    </button>

                    <button onclick="copyErrorID()" id="reportBtn"
                        class="group relative px-6 py-3 bg-gray-800 border border-gray-700 text-gray-400 font-bold rounded hover:border-nexus-green hover:text-nexus-green hover:bg-nexus-green/10 transition-all duration-300 uppercase text-xs tracking-wider flex items-center justify-center gap-2">
                        <i class="fas fa-bug"></i>
                        <span>Log Incident</span>
                    </button>
                </div>

                <!-- Technical Details -->
                <div class="mt-8 pt-6 border-t border-gray-800/50">
                    <div class="bg-nexus-black/50 border border-gray-800 rounded p-4 font-mono text-xs">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-gray-500 uppercase">Diagnostics</span>
                            <span class="text-gray-600">TS: <span id="timestamp" class="text-nexus-blue"></span></span>
                        </div>
                        <div class="space-y-1">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-500">Ref_ID:</span>
                                <span id="refId" class="text-nexus-green select-all">SDLMS-ERR-GEN</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-500">Status:</span>
                                <span class="text-nexus-red uppercase font-bold animate-pulse">Critical_Stop</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Status Bar -->
            <div
                class="bg-nexus-black/80 p-3 flex justify-between items-center text-[10px] text-gray-500 font-mono border-t border-gray-800">
                <div class="flex gap-4">
                    <span class="flex items-center gap-2">
                        <span class="w-1.5 h-1.5 bg-nexus-green rounded-full animate-pulse"></span>
                        SYS: ONLINE
                    </span>
                    <span class="flex items-center gap-2">
                        <span class="w-1.5 h-1.5 bg-nexus-red rounded-full animate-pulse"></span>
                        DB: UNREACHABLE
                    </span>
                </div>
                <div id="liveClock">00:00:00</div>
            </div>

        </div>

    </div>

    <!-- Toast Notification -->
    <div id="toast"
        class="fixed bottom-6 right-6 bg-nexus-panel border border-nexus-green/30 text-nexus-green px-6 py-4 rounded shadow-[0_0_20px_rgba(0,255,157,0.1)] transform translate-y-24 opacity-0 transition-all duration-500 flex items-center gap-4 z-50">
        <div
            class="w-8 h-8 bg-nexus-green/10 rounded-full flex items-center justify-center border border-nexus-green/50">
            <i class="fas fa-check text-xs"></i>
        </div>
        <div>
            <p class="font-bold text-xs uppercase tracking-wider">Report Logged</p>
            <p class="text-[10px] text-gray-400 font-mono">ID copied to clipboard</p>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Generate dynamic error ID
            const generateId = () => {
                const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
                let id = 'ERR-';
                for (let i = 0; i < 6; i++) {
                    id += chars.charAt(Math.floor(Math.random() * chars.length));
                }
                return id + '-' + new Date().getFullYear().toString().substr(-2);
            };

            // Set error ID and timestamp
            document.getElementById('refId').innerText = generateId();
            document.getElementById('timestamp').innerText =
                new Date().toISOString().replace('T', ' ').substring(11, 19);

            // Handle URL error codes
            const urlParams = new URLSearchParams(window.location.search);
            const code = urlParams.get('code');
            const errorCodes = {
                '400': 'Bad Request',
                '401': 'Unauthorized Access',
                '403': 'Access Forbidden',
                '404': 'Node Not Found',
                '500': 'System Malfunction',
                '503': 'Service Offline'
            };

            if (code && errorCodes[code]) {
                const digits = code.split('');
                document.getElementById('errorCode').innerHTML =
                    digits.map((d, i) => `<span class="error-code-digit" style="animation-delay: ${i * 0.15}s">${d}</span>`).join('');
                document.getElementById('errorTitle').innerText = errorCodes[code];

                // Update reason text based on code
                if (code === '404') {
                    document.querySelector('p.text-gray-400').innerText = "[NAV_ERROR] The requested data node could not be located in the current sector.";
                } else if (code === '403') {
                    document.querySelector('p.text-gray-400').innerText = "[SEC_ALERT] You do not have the required clearance level to access this node.";
                }
            }

            // Live clock
            function updateClock() {
                const now = new Date();
                const timeStr = now.toLocaleTimeString('en-US', {
                    hour12: false,
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit'
                });
                document.getElementById('liveClock').innerText = `[${timeStr}]`;
            }
            setInterval(updateClock, 1000);
            updateClock();
        });

        function copyErrorID() {
            const idText = document.getElementById('refId').innerText;
            const btn = document.getElementById('reportBtn');
            const originalHtml = btn.innerHTML;

            // Visual feedback
            btn.innerHTML = '<i class="fas fa-check"></i><span>Copied</span>';
            btn.classList.add('border-nexus-green', 'text-nexus-green');

            navigator.clipboard.writeText(idText).then(() => {
                const toast = document.getElementById('toast');
                toast.classList.remove('translate-y-24', 'opacity-0');

                setTimeout(() => {
                    toast.classList.add('translate-y-24', 'opacity-0');
                }, 3000);

                setTimeout(() => {
                    btn.innerHTML = originalHtml;
                    btn.classList.remove('border-nexus-green', 'text-nexus-green');
                }, 2000);
            });
        }
    </script>
</body>

</html>