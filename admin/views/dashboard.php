<div class="grid grid-cols-1 md:grid-cols-12 gap-6 pb-20">

    <!-- COLUMN 1: Main Visuals (8 cols) -->
    <div class="col-span-1 md:col-span-8 flex flex-col gap-6">

        <!-- HERO: Global Map Hologram -->
        <div class="holo-card h-80 rounded-xl relative overflow-hidden flex items-center justify-center group">
            <div
                class="absolute top-4 left-4 text-xs font-header text-gray-500 tracking-[0.2em] group-hover:text-nexus-blue transition-colors">
                [GLOBAL_NODE_VISUALIZATION]
            </div>

            <!-- HOLOGRAPHIC SPHERE (CSS) -->
            <div
                class="relative w-48 h-48 md:w-64 md:h-64 opacity-90 transform group-hover:scale-105 transition-transform duration-700">
                <div class="absolute inset-0 border border-nexus-blue/40 rounded-full animate-spin-slow"></div>
                <div class="absolute inset-4 border border-dashed border-nexus-green/30 rounded-full animate-spin-slow"
                    style="animation-direction: reverse;"></div>
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="text-center z-10">
                        <div class="text-4xl font-bold text-white mb-1">128</div>
                        <div
                            class="text-[10px] uppercase text-nexus-blue tracking-widest bg-nexus-black/80 px-2 rounded">
                            Active Nodes</div>
                    </div>
                </div>
                <!-- Decorative Orbital Rings -->
                <div
                    class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[120%] h-[10px] border border-nexus-purple/30 rounded-[50%] rotate-45">
                </div>
                <div
                    class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[120%] h-[10px] border border-nexus-purple/30 rounded-[50%] -rotate-45">
                </div>
            </div>

            <!-- Map Data Overlay -->
            <div class="absolute bottom-4 right-4 flex flex-col gap-2">
                <!-- Static Data Row 1 -->
                <div class="flex items-center justify-end gap-2 text-xs font-mono">
                    <span class="text-gray-400">Full Stack Alpha</span>
                    <div class="w-20 h-1 bg-gray-800 rounded-full overflow-hidden">
                        <div class="h-full bg-nexus-green" style="width: 88%"></div>
                    </div>
                </div>
                <!-- Static Data Row 2 -->
                <div class="flex items-center justify-end gap-2 text-xs font-mono">
                    <span class="text-gray-400">CyberSec Delta</span>
                    <div class="w-20 h-1 bg-gray-800 rounded-full overflow-hidden">
                        <div class="h-full bg-nexus-purple" style="width: 45%"></div>
                    </div>
                </div>
                <!-- Static Data Row 3 -->
                <div class="flex items-center justify-end gap-2 text-xs font-mono">
                    <span class="text-gray-400">AI Neural Net</span>
                    <div class="w-20 h-1 bg-gray-800 rounded-full overflow-hidden">
                        <div class="h-full bg-nexus-green" style="width: 92%"></div>
                    </div>
                </div>
                <!-- Static Data Row 4 -->
                <div class="flex items-center justify-end gap-2 text-xs font-mono">
                    <span class="text-gray-400">Blockchain Zeta</span>
                    <div class="w-20 h-1 bg-gray-800 rounded-full overflow-hidden">
                        <div class="h-full bg-red-500" style="width: 12%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- LIVE COMMITS & INFRASTRUCTURE -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <!-- Commit Feed -->
            <div class="holo-card p-5 rounded-xl flex flex-col h-64">
                <h3 class="text-xs font-bold text-nexus-blue mb-4 uppercase flex justify-between items-center">
                    <span>Code_Stream</span>
                    <span class="w-2 h-2 rounded-full bg-nexus-green animate-pulse"></span>
                </h3>
                <div class="flex-1 overflow-y-auto space-y-3 pr-2 custom-scroll">
                    <!-- Static Commit 1 -->
                    <div class="group cursor-pointer">
                        <div class="flex justify-between text-xs mb-1">
                            <span class="text-nexus-purple font-bold">Dev_Sarah</span>
                            <span class="text-gray-600 font-mono">8f3a21</span>
                        </div>
                        <div class="text-gray-400 text-xs mb-1 group-hover:text-white transition-colors">
                            Fixed memory leak in core loop
                        </div>
                        <div class="flex justify-between items-center text-[10px]">
                            <span class="text-nexus-green bg-nexus-green/10 px-1 rounded">+12 -4</span>
                            <span class="text-gray-600">2m ago</span>
                        </div>
                    </div>
                    <!-- Static Commit 2 -->
                    <div class="group cursor-pointer">
                        <div class="flex justify-between text-xs mb-1">
                            <span class="text-nexus-purple font-bold">Ghost_Coder</span>
                            <span class="text-gray-600 font-mono">7b2c99</span>
                        </div>
                        <div class="text-gray-400 text-xs mb-1 group-hover:text-white transition-colors">
                            Refactored auth middleware
                        </div>
                        <div class="flex justify-between items-center text-[10px]">
                            <span class="text-nexus-green bg-nexus-green/10 px-1 rounded">+45 -120</span>
                            <span class="text-gray-600">5m ago</span>
                        </div>
                    </div>
                    <!-- Static Commit 3 -->
                    <div class="group cursor-pointer">
                        <div class="flex justify-between text-xs mb-1">
                            <span class="text-nexus-purple font-bold">Neo_Jr</span>
                            <span class="text-gray-600 font-mono">1a9f44</span>
                        </div>
                        <div class="text-gray-400 text-xs mb-1 group-hover:text-white transition-colors">
                            Updated encryption keys
                        </div>
                        <div class="flex justify-between items-center text-[10px]">
                            <span class="text-nexus-green bg-nexus-green/10 px-1 rounded">+2 -2</span>
                            <span class="text-gray-600">12m ago</span>
                        </div>
                    </div>
                    <!-- Static Commit 4 -->
                    <div class="group cursor-pointer">
                        <div class="flex justify-between text-xs mb-1">
                            <span class="text-nexus-purple font-bold">Trinity_X</span>
                            <span class="text-gray-600 font-mono">9c8d11</span>
                        </div>
                        <div class="text-gray-400 text-xs mb-1 group-hover:text-white transition-colors">
                            Deployed hotfix for Sector 7
                        </div>
                        <div class="flex justify-between items-center text-[10px]">
                            <span class="text-nexus-green bg-nexus-green/10 px-1 rounded">+1 -0</span>
                            <span class="text-gray-600">15m ago</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Server Infrastructure -->
            <div class="holo-card p-5 rounded-xl h-64 relative">
                <h3 class="text-xs font-bold text-nexus-purple mb-4 uppercase">Infrastructure_Load</h3>

                <div class="flex items-end justify-between h-32 px-4 gap-4 mt-8">
                    <!-- Bars -->
                    <div class="w-full bg-nexus-blue/10 rounded-t-lg relative group h-full overflow-hidden">
                        <div class="absolute bottom-0 left-0 right-0 bg-nexus-blue/50 transition-all duration-1000 group-hover:bg-nexus-blue"
                            style="height: 45%"></div>
                        <div class="absolute bottom-2 left-0 right-0 text-center text-[10px] text-white">CPU</div>
                    </div>
                    <div class="w-full bg-nexus-green/10 rounded-t-lg relative group h-full overflow-hidden">
                        <div class="absolute bottom-0 left-0 right-0 bg-nexus-green/50 transition-all duration-1000 group-hover:bg-nexus-green"
                            style="height: 62%"></div>
                        <div class="absolute bottom-2 left-0 right-0 text-center text-[10px] text-white">RAM</div>
                    </div>
                    <div class="w-full bg-nexus-purple/10 rounded-t-lg relative group h-full overflow-hidden">
                        <div class="absolute bottom-0 left-0 right-0 bg-nexus-purple/50 transition-all duration-1000 group-hover:bg-nexus-purple"
                            style="height: 42%"></div>
                        <div class="absolute bottom-2 left-0 right-0 text-center text-[10px] text-white">NET</div>
                    </div>
                </div>
                <div class="mt-4 text-center text-xs text-gray-500">
                    Total Load: 42% <span class="text-nexus-green ml-2">[OPTIMAL]</span>
                </div>
            </div>

        </div>
    </div>

    <!-- COLUMN 2: Sidebar Widgets (4 cols) -->
    <div class="col-span-1 md:col-span-4 flex flex-col gap-6">

        <!-- DNA HELIX (Student Progress) -->
        <div class="holo-card p-5 rounded-xl h-64 relative overflow-hidden">
            <h3 class="text-xs font-bold text-white mb-2 uppercase">Student_Evolution</h3>
            <div class="absolute inset-0 flex items-center justify-center opacity-30 pointer-events-none">
                <!-- SVG BG DNA -->
                <svg width="200" height="200" viewBox="0 0 100 100">
                    <path d="M30 0 Q70 25 30 50 T30 100" stroke="#00D4FF" fill="none" stroke-width="2" />
                    <path d="M70 0 Q30 25 70 50 T70 100" stroke="#00FF9D" fill="none" stroke-width="2" />
                    <!-- Connection Lines -->
                    <line x1="30" y1="10" x2="70" y2="10" stroke="#444" stroke-width="1" />
                    <line x1="45" y1="25" x2="55" y2="25" stroke="#444" stroke-width="1" />
                    <line x1="30" y1="40" x2="70" y2="40" stroke="#444" stroke-width="1" />
                </svg>
            </div>
            <div class="relative z-10 mt-10 space-y-4">
                <div class="bg-black/50 p-3 rounded border border-gray-800">
                    <div class="flex justify-between text-xs mb-1">
                        <span class="text-nexus-blue">Theory</span>
                        <span class="text-white">84%</span>
                    </div>
                    <div class="w-full h-1 bg-gray-800 rounded">
                        <div class="h-full bg-nexus-blue w-[84%]"></div>
                    </div>
                </div>
                <div class="bg-black/50 p-3 rounded border border-gray-800">
                    <div class="flex justify-between text-xs mb-1">
                        <span class="text-nexus-green">Practical</span>
                        <span class="text-white">91%</span>
                    </div>
                    <div class="w-full h-1 bg-gray-800 rounded">
                        <div class="h-full bg-nexus-green w-[91%]"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SYSTEM TERMINAL -->
        <div class="holo-card p-0 rounded-xl flex-1 flex flex-col min-h-[300px] bg-black">
            <div class="bg-gray-900 px-4 py-2 text-[10px] text-gray-500 border-b border-gray-800 flex justify-between">
                <span>TERMINAL_OUTPUT</span>
                <span>bash - 80x24</span>
            </div>
            <div class="p-4 font-mono text-xs space-y-2 overflow-y-auto flex-1 text-gray-400">
                <!-- Static Log 1 -->
                <div>
                    <span class="text-gray-600">[14:02:01]</span>
                    <span class="text-nexus-blue">INFO:</span>
                    <span>Connection established on port 8080</span>
                </div>
                <!-- Static Log 2 -->
                <div>
                    <span class="text-gray-600">[14:02:05]</span>
                    <span class="text-nexus-green">SUCCESS:</span>
                    <span>Neural API handshake complete</span>
                </div>
                <!-- Static Log 3 -->
                <div>
                    <span class="text-gray-600">[14:03:12]</span>
                    <span class="text-nexus-purple">WARN:</span>
                    <span>High latency detected in Sector 7</span>
                </div>
                <!-- Static Log 4 -->
                <div>
                    <span class="text-gray-600">[14:04:00]</span>
                    <span class="text-nexus-blue">INFO:</span>
                    <span>Garbage collection cycle started</span>
                </div>
                <div class="flex mt-2 animate-pulse">
                    <span class="text-nexus-green mr-2">admin@nexus:~$</span>
                    <span class="w-2 h-4 bg-nexus-green block"></span>
                </div>
            </div>
        </div>

    </div>
</div>