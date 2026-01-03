<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DevAccess | System Entry</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;700&family=Inter:wght@400;600;700;800&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #020617;
            /* Slate 950 - Thawa dark kala */
        }

        .font-mono {
            font-family: 'JetBrains Mono', monospace;
        }

        /* Custom Scrollbar (hidden for this full immersive view) */
        ::-webkit-scrollbar {
            width: 0px;
        }
    </style>
</head>

<body class="bg-slate-950 h-screen w-full overflow-hidden relative flex flex-col items-center justify-center">

    <!-- Canvas Background (Full Screen) -->
    <canvas id="homeCanvas" class="absolute inset-0 z-0 w-full h-full"></canvas>

    <!-- Center Content Overlay -->
    <div class="relative z-10 flex flex-col items-center gap-10 animate-fade-in p-6 text-center">

        <!-- Logo Section -->
        <div class="relative group">
            <!-- Glow Effect behind logo -->
            <div
                class="absolute -inset-4 bg-sky-500/20 rounded-full blur-xl opacity-50 group-hover:opacity-75 transition duration-500">
            </div>

            <img src="assets/home/sd1.png"
                onerror="this.style.display='none'; document.getElementById('fallback-icon').style.display='block';"
                alt="Developer Logo"
                class="relative w-48 md:w-64 object-contain drop-shadow-[0_0_15px_rgba(56,189,248,0.3)] transition-transform duration-500 hover:scale-105 rounded-lg">

            <!-- Fallback Icon if image missing -->
            <i id="fallback-icon"
                class="hidden fa-solid fa-code-branch text-9xl text-sky-500 drop-shadow-[0_0_30px_rgba(14,165,233,0.6)]"></i>



        </div>

            <p class="mt-6 mb-2 text-3xl md:text-5xl font-extrabold font-mono tracking-tight text-transparent bg-clip-text bg-gradient-to-r from-white via-blue-500 to-sky-600 drop-shadow-[0_0_15px_rgba(14,165,233,0.4)] animate-pulse">
                SOFTWARE_DEVELOPER <span class="text-white">|</span> LMS
            </p>

        <!-- Login Button -->
        <div class="flex flex-col items-center gap-4">
            <a href="pages/index.php"
                class="group relative px-10 py-4 bg-slate-900/80 backdrop-blur-md text-sky-400 font-mono font-bold text-xl rounded-full border border-sky-500/30 hover:border-sky-400 hover:text-white hover:shadow-[0_0_30px_rgba(56,189,248,0.4)] transition-all duration-300 overflow-hidden ring-1 ring-sky-900/50">
                <span class="relative z-10 flex items-center gap-3">
                    <i class="fa-solid fa-power-off text-sm group-hover:animate-pulse"></i>
                    LOGIN_SYSTEM
                </span>
                <!-- Button Fill Effect -->
                <div
                    class="absolute inset-0 h-full w-full bg-gradient-to-r from-sky-600 to-blue-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                </div>
            </a>

            <p class="text-slate-500 text-xs font-mono tracking-widest uppercase opacity-70">Secure Connection Required
            </p>
        </div>

    </div>

    <!-- Script for Full Screen Animation -->
    <script>
        const canvas = document.getElementById('homeCanvas');
        const ctx = canvas.getContext('2d');
        let width, height;
        let particles = [];

        // Configuration
        const particleCount = 250; // Dots gana 600 idan 250 ta adu kala
        const connectionDistance = 140; // Connection dura poddak adjust kala
        const mouseDistance = 300;

        let mouse = { x: null, y: null };

        // Resize handler - Full Window
        function resize() {
            width = canvas.width = window.innerWidth;
            height = canvas.height = window.innerHeight;
        }

        window.addEventListener('resize', resize);
        resize();

        // Mouse listeners
        window.addEventListener('mousemove', (e) => {
            mouse.x = e.clientX;
            mouse.y = e.clientY;
        });

        window.addEventListener('mouseleave', () => {
            mouse.x = null;
            mouse.y = null;
        });

        // Particle Class
        class Particle {
            constructor() {
                this.x = Math.random() * width;
                this.y = Math.random() * height;
                this.vx = (Math.random() - 0.5) * 0.5; // Speed adu kala (1.5 idan 0.5 ta)
                this.vy = (Math.random() - 0.5) * 0.5;
                this.size = Math.random() * 2 + 0.5;
                const colors = ['#38bdf8', '#0ea5e9', '#6366f1', '#94a3b8'];
                this.color = colors[Math.floor(Math.random() * colors.length)];
            }

            update() {
                this.x += this.vx;
                this.y += this.vy;

                // Wrap around edges (Screen eke anith paththen enna haduwa)
                if (this.x < 0) this.x = width;
                if (this.x > width) this.x = 0;
                if (this.y < 0) this.y = height;
                if (this.y > height) this.y = 0;

                // Mouse Interaction
                if (mouse.x != null) {
                    let dx = mouse.x - this.x;
                    let dy = mouse.y - this.y;
                    let distance = Math.sqrt(dx * dx + dy * dy);

                    if (distance < mouseDistance) {
                        const forceDirectionX = dx / distance;
                        const forceDirectionY = dy / distance;
                        const force = (mouseDistance - distance) / mouseDistance;
                        const directionX = forceDirectionX * force * 1.5;
                        const directionY = forceDirectionY * force * 1.5;

                        // Gentle repulsion
                        this.x -= directionX;
                        this.y -= directionY;
                    }
                }
            }

            draw() {
                ctx.beginPath();
                ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
                ctx.fillStyle = this.color;

                // --- GLOW EFFECT ADDED HERE ---
                ctx.shadowBlur = 15;       // Glow eke pramanaya
                ctx.shadowColor = this.color; // Dot eke patinma glow eka

                ctx.fill();

                // Reset shadow (Lines walata effect eka nathi wenna)
                ctx.shadowBlur = 0;
            }
        }

        function initParticles() {
            particles = [];
            for (let i = 0; i < particleCount; i++) {
                particles.push(new Particle());
            }
        }

        function animate() {
            ctx.clearRect(0, 0, width, height);

            for (let i = 0; i < particles.length; i++) {
                particles[i].update();
                particles[i].draw();

                for (let j = i; j < particles.length; j++) {
                    let dx = particles[i].x - particles[j].x;
                    let dy = particles[i].y - particles[j].y;
                    let distance = Math.sqrt(dx * dx + dy * dy);

                    if (distance < connectionDistance) {
                        ctx.beginPath();
                        // Opacity based on distance - Subtle lines
                        let opacity = 1 - (distance / connectionDistance);
                        ctx.strokeStyle = `rgba(56, 189, 248, ${opacity * 0.15})`;
                        ctx.lineWidth = 1;
                        ctx.moveTo(particles[i].x, particles[i].y);
                        ctx.lineTo(particles[j].x, particles[j].y);
                        ctx.stroke();
                    }
                }
            }
            requestAnimationFrame(animate);
        }

        initParticles();
        animate();

        window.addEventListener('resize', initParticles);
    </script>
</body>

</html>