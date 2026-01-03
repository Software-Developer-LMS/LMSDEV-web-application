<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DevAccess | Developer Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;700&family=Inter:wght@400;600;700&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #0f172a;
            /* Slate 900 */
        }

        .font-mono {
            font-family: 'JetBrains Mono', monospace;
        }

        .glass-panel {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* Custom Scrollbar for code aesthetic */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #0f172a;
        }

        ::-webkit-scrollbar-thumb {
            background: #334155;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #475569;
        }

        .input-group:focus-within label {
            color: #38bdf8;
            /* Sky 400 */
        }

        .input-group:focus-within i {
            color: #38bdf8;
        }
    </style>
</head>

<body class="text-white h-screen w-full overflow-hidden flex">

    <!-- Left Side: Login Form -->
    <div
        class="w-full md:w-1/2 h-full flex flex-col justify-center items-center p-8 relative z-10 bg-slate-900 border-r border-slate-800">

        <div class="w-full max-w-md space-y-8">
            <!-- Header -->
            <div class="text-center md:text-left">
                <div class="flex justify-center md:justify-start items-center gap-2 mb-2">
                    <i class="fa-solid fa-terminal text-sky-400 text-2xl"></i>
                    <span class="font-mono text-xl font-bold tracking-tighter text-white">DEV_ACCESS_LOG</span>
                </div>
                <h2 class="text-3xl font-bold tracking-tight text-white">Welcome back</h2>
                <p class="mt-2 text-sm text-slate-400">
                    Enter your credentials to access the repo.
                </p>
            </div>

            <!-- Form -->
            <form class="mt-8 space-y-6">
                <div class="space-y-4">
                    <!-- Error Message Display -->
                    <?php if (isset($_GET['error'])): ?>
                        <?php if ($_GET['error'] == 'deactivated'): ?>
                            <div class="p-3 bg-red-500/10 border border-red-500/50 rounded text-red-400 text-xs font-mono mb-4">
                                > ERROR: Profile deactivated. Contact Admin.
                            </div>
                        <?php else: ?>
                            <div class="p-3 bg-red-500/10 border border-red-500/50 rounded text-red-400 text-xs font-mono">
                                > ERROR: Invalid credentials provided. Access denied.
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                    <?php if (isset($_GET['logout'])): ?>
                        <div
                            class="p-3 bg-green-500/10 border border-green-500/50 rounded text-green-400 text-xs font-mono">
                            > SYSTEM: Session terminated successfully.
                        </div>
                    <?php endif; ?>

                    <!-- Email Field -->
                    <div class="input-group">
                        <label for="email-address"
                            class="block text-xs font-mono font-medium text-slate-400 mb-1 transition-colors">EMAIL_ADDRESS</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fa-regular fa-envelope text-slate-500 transition-colors"></i>
                            </div>
                            <input id="email-address" name="email" type="email" autocomplete="email" required
                                class="appearance-none block w-full pl-10 pr-3 py-3 border border-slate-700 rounded-md leading-5 bg-slate-800 text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500 focus:bg-slate-800 sm:text-sm transition-all duration-200"
                                placeholder="dev@example.com">
                        </div>
                    </div>

                    <!-- Password Field -->
                    <div class="input-group">
                        <div class="flex justify-between items-center mb-1">
                            <label for="password"
                                class="block text-xs font-mono font-medium text-slate-400 transition-colors">PASSWORD_KEY</label>
                            <a href="#" class="text-xs font-medium text-sky-400 hover:text-sky-300">Forgot key?</a>
                        </div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fa-solid fa-lock text-slate-500 transition-colors"></i>
                            </div>
                            <input id="password" name="password" type="password" autocomplete="current-password"
                                required
                                class="appearance-none block w-full pl-10 pr-3 py-3 border border-slate-700 rounded-md leading-5 bg-slate-800 text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500 focus:bg-slate-800 sm:text-sm transition-all duration-200"
                                placeholder="••••••••">
                        </div>
                    </div>
                </div>

                <div class="flex items-center">
                    <input id="remember-me" name="remember-me" type="checkbox"
                        class="h-4 w-4 text-sky-500 focus:ring-sky-400 border-gray-600 rounded bg-slate-800">
                    <label for="remember-me" class="ml-2 block text-sm text-slate-400">
                        Keep session active
                    </label>
                </div>

                <div>
                    <button type="submit"
                        class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-sky-600 hover:bg-sky-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-slate-900 focus:ring-sky-500 transition-all duration-200 shadow-[0_0_15px_rgba(14,165,233,0.3)] hover:shadow-[0_0_25px_rgba(14,165,233,0.5)]">
                        <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <i class="fa-solid fa-code text-sky-200 group-hover:text-white transition-colors"></i>
                        </span>
                        INITIALIZE_SESSION
                    </button>
                </div>
            </form>

            <!-- Terminal Output Style Footer -->
            <div class="mt-8 p-3 bg-black rounded border border-slate-800 font-mono text-xs text-green-400 opacity-70">
                <p>> System ready...</p>
                <p>> Waiting for user input<span class="animate-pulse">_</span></p>
            </div>
        </div>

        <!-- Bottom copyright -->
        <div class="absolute bottom-4 text-xs text-slate-600">
            &copy; 2026 Lumnix Solutions. All systems functional.
        </div>
    </div>

    <!-- Right Side: Animation Canvas (Replaces Image) -->
    <div class="hidden md:block md:w-1/2 h-full relative bg-slate-950 overflow-hidden">
        <canvas id="homeCanvas" class="absolute inset-0 w-full h-full z-0"></canvas>

        <!-- Floating Code Card (Decoration) -->
        <div
            class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-96 glass-panel rounded-lg p-0 shadow-2xl overflow-hidden border border-slate-700/50 opacity-90 transform transition-transform duration-500 hover:scale-105">
            <!-- Terminal Header -->
            <div class="bg-slate-900/90 px-4 py-2 flex items-center justify-between border-b border-slate-700/50">
                <div class="flex gap-2">
                    <div class="w-3 h-3 rounded-full bg-red-500"></div>
                    <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
                    <div class="w-3 h-3 rounded-full bg-green-500"></div>
                </div>
                <span class="text-xs text-slate-500 font-mono">server.js</span>
            </div>
            <!-- Code Content -->
            <div id="code-container" class="p-4 font-mono text-xs leading-relaxed text-slate-300 min-h-[200px]">
                <!-- Typewriter content will appear here -->
            </div>
        </div>



        <!-- Overlay Content on Animation -->
        <div
            class="absolute inset-0 z-10 flex flex-col justify-end p-12 pointer-events-none bg-gradient-to-t from-slate-950 via-transparent to-transparent">
            <h1 class="text-4xl font-bold text-white mb-2 tracking-tight">Build the Future.</h1>
            <img src="../assets/logo/software_developer_logo.png" alt="" class="w-32 mt-2">
        </div>
    </div>

    <script>
        // --- Typewriter Effect Logic ---
        const codeContainer = document.getElementById('code-container');

        // Data structure: Array of lines. Each line is an array of segments.
        // null represents a line break / empty line spacing
        const codeLines = [
            [   // Line 1
                { text: "const ", class: "text-purple-400" },
                { text: "DevAccess", class: "text-blue-400" },
                { text: " = ", class: "text-white" },
                { text: "require", class: "text-yellow-300" },
                { text: "('", class: "text-white" },
                { text: "@dev/core", class: "text-green-400" },
                { text: "');", class: "text-white" }
            ],
            null, // Spacer
            [   // Line 3
                { text: "// Initialize secure connection", class: "text-slate-500" }
            ],
            [   // Line 4
                { text: "await ", class: "text-purple-400" },
                { text: "DevAccess.", class: "text-white" },
                { text: "connect", class: "text-blue-400" },
                { text: "({", class: "text-white" }
            ],
            [   // Line 5
                { text: "  mode: ", class: "text-white pl-4" },
                { text: "'turbo'", class: "text-green-400" },
                { text: ",", class: "text-white" }
            ],
            [   // Line 6
                { text: "  sync: ", class: "text-white pl-4" },
                { text: "true", class: "text-purple-400" },
                { text: ",", class: "text-white" }
            ],
            [   // Line 7
                { text: "  encryption: ", class: "text-white pl-4" },
                { text: "'AES-256'", class: "text-green-400" }
            ],
            [   // Line 8
                { text: "});", class: "text-white" }
            ],
            null, // Spacer
            [   // Line 10
                { text: "console", class: "text-blue-400" },
                { text: ".", class: "text-white" },
                { text: "log", class: "text-yellow-300" },
                { text: "(", class: "text-white" },
                { text: "\"System Online 🚀\"", class: "text-green-400" },
                { text: ");", class: "text-white" }
            ]
        ];

        let cursorElement = document.createElement('span');
        cursorElement.className = "text-sky-400 animate-pulse ml-1";
        cursorElement.textContent = "_";

        async function typeCode() {
            if (!codeContainer) return;
            codeContainer.innerHTML = '';

            // Initial cursor
            const initialLine = document.createElement('div');
            initialLine.className = 'flex items-center';
            initialLine.appendChild(cursorElement);
            codeContainer.appendChild(initialLine);

            for (let line of codeLines) {
                // Remove cursor from previous position
                if (cursorElement.parentNode) {
                    cursorElement.parentNode.removeChild(cursorElement);
                }

                // Handle Spacer
                if (line === null) {
                    const br = document.createElement('br');
                    codeContainer.appendChild(br);
                    continue;
                }

                // Create new line container (using div for flex layout to align cursor)
                const p = document.createElement('div');
                p.className = 'min-h-[1.5em] flex flex-wrap items-center'; // Flex to keep cursor aligned
                codeContainer.appendChild(p);

                // Add cursor to current line
                p.appendChild(cursorElement);

                // Type out each segment in the line
                for (let segment of line) {
                    const span = document.createElement('span');
                    span.className = segment.class;
                    // Insert segment before cursor
                    p.insertBefore(span, cursorElement);

                    for (let char of segment.text) {
                        span.textContent += char;
                        // Typing delay
                        await new Promise(r => setTimeout(r, Math.random() * 20 + 30));
                    }
                }

                // Small pause at end of line
                await new Promise(r => setTimeout(r, 200));
            }
        }

        // Start typewriter
        setTimeout(typeCode, 800);

        // --- Particle Network Animation Logic ---
        const canvas = document.getElementById('homeCanvas');
        const ctx = canvas.getContext('2d');
        let width, height;
        let particles = [];

        // Configuration
        const particleCount = 100; // Number of dots
        const connectionDistance = 150; // Max distance to draw line
        const mouseDistance = 200; // Interaction radius

        // Mouse state
        let mouse = { x: null, y: null };

        // Resize handler
        function resize() {
            width = canvas.width = canvas.parentElement.offsetWidth;
            height = canvas.height = canvas.parentElement.offsetHeight;
        }

        window.addEventListener('resize', resize);

        // Initial sizing
        resize();

        // Mouse listeners
        canvas.addEventListener('mousemove', (e) => {
            const rect = canvas.getBoundingClientRect();
            mouse.x = e.clientX - rect.left;
            mouse.y = e.clientY - rect.top;
        });

        canvas.addEventListener('mouseleave', () => {
            mouse.x = null;
            mouse.y = null;
        });

        // Particle Class
        class Particle {
            constructor() {
                this.x = Math.random() * width;
                this.y = Math.random() * height;
                this.vx = (Math.random() - 0.5) * 1.5; // Velocity X
                this.vy = (Math.random() - 0.5) * 1.5; // Velocity Y
                this.size = Math.random() * 2 + 1;
                // Random tech colors: mainly cyan/blue, some purple
                const colors = ['#38bdf8', '#818cf8', '#22d3ee', '#64748b'];
                this.color = colors[Math.floor(Math.random() * colors.length)];
            }

            update() {
                this.x += this.vx;
                this.y += this.vy;

                // Bounce off edges
                if (this.x < 0 || this.x > width) this.vx *= -1;
                if (this.y < 0 || this.y > height) this.vy *= -1;

                // Mouse Interaction (Attraction/Repulsion)
                // Let's make them slightly flee from mouse to create a clearing, or attract
                if (mouse.x != null) {
                    let dx = mouse.x - this.x;
                    let dy = mouse.y - this.y;
                    let distance = Math.sqrt(dx * dx + dy * dy);

                    if (distance < mouseDistance) {
                        const forceDirectionX = dx / distance;
                        const forceDirectionY = dy / distance;
                        const force = (mouseDistance - distance) / mouseDistance;
                        const directionX = forceDirectionX * force * 2; // Strength
                        const directionY = forceDirectionY * force * 2;

                        // To attract: +=, To repel: -=
                        // Let's create a gentle "net" movement
                        this.x -= directionX;
                        this.y -= directionY;
                    }
                }
            }

            draw() {
                ctx.beginPath();
                ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
                ctx.fillStyle = this.color;
                ctx.fill();
            }
        }

        // Initialize particles
        function initParticles() {
            particles = [];
            for (let i = 0; i < particleCount; i++) {
                particles.push(new Particle());
            }
        }

        // Animation Loop
        function animate() {
            ctx.clearRect(0, 0, width, height);

            // Update and draw particles
            for (let i = 0; i < particles.length; i++) {
                particles[i].update();
                particles[i].draw();

                // Draw connections
                for (let j = i; j < particles.length; j++) {
                    let dx = particles[i].x - particles[j].x;
                    let dy = particles[i].y - particles[j].y;
                    let distance = Math.sqrt(dx * dx + dy * dy);

                    if (distance < connectionDistance) {
                        ctx.beginPath();
                        // Opacity based on distance
                        let opacity = 1 - (distance / connectionDistance);
                        ctx.strokeStyle = `rgba(56, 189, 248, ${opacity * 0.4})`; // Sky blue lines
                        ctx.lineWidth = 1;
                        ctx.moveTo(particles[i].x, particles[i].y);
                        ctx.lineTo(particles[j].x, particles[j].y);
                        ctx.stroke();
                    }
                }
            }
            requestAnimationFrame(animate);
        }

        // Start
        initParticles();
        animate();

        // Handle Resize regeneration to keep density consistent
        window.addEventListener('resize', () => {
            initParticles();
        });

        // Simple login feedback
        function handleLogin() {
            const btn = document.querySelector('button[type="submit"]');
            const form = document.querySelector('form');

            // Basic validation
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            // Prevent double submission
            if (btn.disabled) return;

            btn.innerHTML = `<i class="fa-solid fa-circle-notch fa-spin mr-2"></i> ACCESSING...`;
            btn.classList.add('opacity-75', 'cursor-not-allowed');
            btn.disabled = true;

            setTimeout(() => {
                btn.innerHTML = `<i class="fa-solid fa-check mr-2"></i> ACCESS GRANTED`;
                btn.classList.replace('bg-sky-600', 'bg-emerald-600');
                btn.classList.remove('shadow-[0_0_15px_rgba(14,165,233,0.3)]');
                btn.classList.add('shadow-[0_0_15px_rgba(16,185,129,0.3)]');

                // Add success message to "terminal"
                const term = document.querySelector('.bg-black');
                const p = document.createElement('p');
                p.className = "text-emerald-400";
                p.innerHTML = `> Auth token received. Redirecting...`;
                term.insertBefore(p, term.lastElementChild);

                // Submit the form to backend
                form.action = "login_process.php";
                form.method = "POST";
                form.submit();

            }, 1500);
        }

        // Prevent default form submission to handle it via button click
        document.querySelector('form').addEventListener('submit', function (e) {
            e.preventDefault();
            handleLogin();
        });
    </script>
</body>

</html>