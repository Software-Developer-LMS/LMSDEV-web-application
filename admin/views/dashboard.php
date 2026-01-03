<?php
include '../includes/db_connection.php';
// Dynamic Stats
$student_count = $conn->query("SELECT COUNT(*) as count FROM users WHERE role='student'")->fetch_assoc()['count'];
$module_count = $conn->query("SELECT COUNT(*) as count FROM modules")->fetch_assoc()['count'];
// 1. Recent Submissions (Code_Stream)
$rec_sub_sql = "SELECT s.*, u.name as student_name, a.title as assign_title 
                FROM submissions s 
                JOIN users u ON s.user_id = u.id 
                JOIN assignments a ON s.assignment_id = a.id 
                ORDER BY s.submitted_at DESC LIMIT 5";
$recent_subs = $conn->query($rec_sub_sql);

// 2. Grading Stats (Infrastructure_Load)
$total_subs = $conn->query("SELECT COUNT(*) as count FROM submissions")->fetch_assoc()['count'];
$graded_subs = $conn->query("SELECT COUNT(*) as count FROM submissions WHERE marks IS NOT NULL")->fetch_assoc()['count'];
$grading_percentage = ($total_subs > 0) ? round(($graded_subs / $total_subs) * 100) : 0;

// 3. Top Students (Student_Evolution)
$top_students_sql = "SELECT * FROM users WHERE role='student' ORDER BY legacy_xp DESC LIMIT 3";
$top_students = $conn->query($top_students_sql);

// 4. System Logs (Terminal)
$log_sql = "(SELECT 'New Recruit' as type, name as message, created_at FROM users WHERE role='student') 
            UNION 
            (SELECT 'New Directive' as type, title as message, created_at FROM assignments)
            UNION
            (SELECT 'Data Upload' as type, CONCAT(u.name, ' :: ', a.title) as message, s.submitted_at as created_at 
             FROM submissions s 
             JOIN users u ON s.user_id = u.id 
             JOIN assignments a ON s.assignment_id = a.id)
            ORDER BY created_at DESC LIMIT 20";
$system_logs_result = $conn->query($log_sql);
$system_logs_data = [];
while ($row = $system_logs_result->fetch_assoc()) {
    $system_logs_data[] = [
        'time' => date('H:i:s', strtotime($row['created_at'])),
        'type' => $row['type'],
        'message' => $row['message'],
        'color' => ($row['type'] == 'New Recruit') ? 'text-nexus-green' : (($row['type'] == 'Data Upload') ? 'text-yellow-500' : 'text-nexus-blue')
    ];
}
$system_logs_data = array_reverse($system_logs_data);

// Basic Stats
$active_assigns = $conn->query("SELECT COUNT(*) as count FROM assignments WHERE deadline > NOW()")->fetch_assoc()['count'];
?>

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
                        <div class="text-4xl font-bold text-white mb-1">
                            <?php echo $student_count; ?>
                        </div>
                        <div
                            class="text-[10px] uppercase text-nexus-blue tracking-widest bg-nexus-black/80 px-2 rounded">
                            Active Operatives</div>
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
                    <span class="text-gray-400">Modules Online</span>
                    <div class="w-20 h-1 bg-gray-800 rounded-full overflow-hidden">
                        <div class="h-full bg-nexus-green" style="width: <?php echo min(100, $module_count * 10); ?>%">
                        </div>
                    </div>
                    <span class="text-nexus-green">
                        <?php echo $module_count; ?>
                    </span>
                </div>
                <!-- Static Data Row 2 -->
                <div class="flex items-center justify-end gap-2 text-xs font-mono">
                    <span class="text-gray-400">Active Missions</span>
                    <div class="w-20 h-1 bg-gray-800 rounded-full overflow-hidden">
                        <div class="h-full bg-nexus-purple"
                            style="width: <?php echo min(100, $active_assigns * 10); ?>%"></div>
                    </div>
                    <span class="text-nexus-purple">
                        <?php echo $active_assigns; ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- LIVE COMMITS & INFRASTRUCTURE -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <!-- Commit Feed -->
            <div class="holo-card p-5 rounded-xl flex flex-col h-64">
                <h3 class="text-xs font-bold text-nexus-blue mb-4 uppercase flex justify-between items-center">
                    <span>Recent_Submissions</span>
                    <span class="w-2 h-2 rounded-full bg-nexus-green animate-pulse"></span>
                </h3>
                <div class="flex-1 overflow-y-auto space-y-3 pr-2 custom-scroll">
                    <?php if ($recent_subs->num_rows > 0): ?>
                        <?php while ($sub = $recent_subs->fetch_assoc()): ?>
                            <div class="group cursor-pointer hover:bg-white/5 p-2 rounded transition-colors">
                                <div class="flex justify-between text-xs mb-1">
                                    <span
                                        class="text-nexus-purple font-bold"><?php echo htmlspecialchars($sub['student_name']); ?></span>
                                    <span class="text-gray-600 font-mono">ID:<?php echo $sub['user_id']; ?></span>
                                </div>
                                <div class="text-gray-400 text-xs mb-1 group-hover:text-white transition-colors">
                                    Submitted: <?php echo htmlspecialchars($sub['assign_title']); ?>
                                </div>
                                <div class="flex justify-between items-center text-[10px]">
                                    <span
                                        class="<?php echo $sub['marks'] ? 'text-nexus-green bg-nexus-green/10' : 'text-yellow-500 bg-yellow-500/10'; ?> px-1 rounded">
                                        <?php echo $sub['marks'] ? 'GRADED: ' . $sub['marks'] : 'PENDING REVIEW'; ?>
                                    </span>
                                    <span
                                        class="text-gray-600"><?php echo date('M d H:i', strtotime($sub['submitted_at'])); ?></span>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="text-gray-500 text-xs text-center mt-4">No recent submissions detected.</div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Server Infrastructure -->
            <div class="holo-card p-5 rounded-xl h-64 relative">
                <h3 class="text-xs font-bold text-nexus-purple mb-4 uppercase">Grading_Status</h3>

                <div class="flex items-end justify-between h-32 px-4 gap-4 mt-8">
                    <!-- Bars -->
                    <div class="w-full bg-nexus-blue/10 rounded-t-lg relative group h-full overflow-hidden">
                        <div class="absolute bottom-0 left-0 right-0 bg-nexus-blue/50 transition-all duration-1000 group-hover:bg-nexus-blue"
                            style="height: 100%"></div>
                        <div class="absolute bottom-2 left-0 right-0 text-center text-[10px] text-white">TOTAL</div>
                        <div class="absolute top-2 left-0 right-0 text-center text-xs font-bold text-white">
                            <?php echo $total_subs; ?></div>
                    </div>
                    <div class="w-full bg-nexus-green/10 rounded-t-lg relative group h-full overflow-hidden">
                        <div class="absolute bottom-0 left-0 right-0 bg-nexus-green/50 transition-all duration-1000 group-hover:bg-nexus-green"
                            style="height: <?php echo $grading_percentage; ?>%"></div>
                        <div class="absolute bottom-2 left-0 right-0 text-center text-[10px] text-white">DONE</div>
                        <div class="absolute top-2 left-0 right-0 text-center text-xs font-bold text-white">
                            <?php echo $graded_subs; ?></div>
                    </div>
                    <div class="w-full bg-nexus-purple/10 rounded-t-lg relative group h-full overflow-hidden">
                        <div class="absolute bottom-0 left-0 right-0 bg-nexus-purple/50 transition-all duration-1000 group-hover:bg-nexus-purple"
                            style="height: <?php echo 100 - $grading_percentage; ?>%"></div>
                        <div class="absolute bottom-2 left-0 right-0 text-center text-[10px] text-white">PENDING</div>
                        <div class="absolute top-2 left-0 right-0 text-center text-xs font-bold text-white">
                            <?php echo $total_subs - $graded_subs; ?></div>
                    </div>
                </div>
                <div class="mt-4 text-center text-xs text-gray-500">
                    Completion: <?php echo $grading_percentage; ?>% <span
                        class="text-nexus-green ml-2">[<?php echo ($grading_percentage == 100) ? 'COMPLETE' : 'IN_PROGRESS'; ?>]</span>
                </div>
            </div>

        </div>
    </div>

    <!-- COLUMN 2: Sidebar Widgets (4 cols) -->
    <div class="col-span-1 md:col-span-4 flex flex-col gap-6">

        <!-- TOP STUDENTS (Leaderboard) -->
        <div class="holo-card p-5 rounded-xl h-64 relative overflow-hidden flex flex-col">
            <h3 class="text-xs font-bold text-white mb-4 uppercase">Top_Operatives</h3>
            <div class="flex-1 overflow-y-auto space-y-3 custom-scroll pr-1">
                <?php $rank = 1;
                while ($st = $top_students->fetch_assoc()): ?>
                    <div class="flex items-center gap-3 p-2 bg-white/5 rounded border border-white/5">
                        <div class="text-nexus-blue font-bold text-sm w-4">#<?php echo $rank++; ?></div>
                        <div class="flex-1">
                            <div class="text-white text-xs font-bold"><?php echo htmlspecialchars($st['name']); ?></div>
                            <div class="text-gray-500 text-[10px] uppercase">XP: <?php echo $st['legacy_xp']; ?></div>
                        </div>
                        <i class="fa-solid fa-trophy text-yellow-500 text-xs"></i>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>

        <!-- SYSTEM LOGS (Animated) -->
        <div class="holo-card p-0 rounded-xl flex-1 flex flex-col min-h-[300px] bg-black">
            <div class="bg-gray-900 px-4 py-2 text-[10px] text-gray-500 border-b border-gray-800 flex justify-between">
                <span>SYSTEM_LOGS</span>
                <span>tail -f /var/log/sys</span>
            </div>
            <div id="terminal-content"
                class="p-4 font-mono text-xs space-y-2 overflow-y-auto flex-1 text-gray-400 custom-scroll relative">
                <!-- JS will inject logs here -->
            </div>
            <!-- Pulse Cursor -->
            <div class="px-4 pb-2">
                <div class="flex animate-pulse">
                    <span class="text-nexus-green mr-2">admin@nexus:~$</span>
                    <span class="w-2 h-4 bg-nexus-green block"></span>
                </div>
            </div>
        </div>

        <script>
            // Data from PHP
            const logData = <?php echo json_encode($system_logs_data); ?>;
            const terminal = document.getElementById('terminal-content');

            function typeLog(log, index) {
                const line = document.createElement('div');
                line.className = "border-l-2 border-gray-800 pl-2 opacity-0 transition-opacity duration-300";

                const timeSpan = `<span class="text-gray-600">[${log.time}]</span>`;
                const typeSpan = `<span class="${log.color} uppercase">${log.type}:</span>`;
                const msgSpan = `<span class="text-gray-300 typing-effect">${log.message}</span>`;

                line.innerHTML = `${timeSpan} ${typeSpan} ${msgSpan}`;
                terminal.appendChild(line);

                // Reveal line
                setTimeout(() => {
                    line.classList.remove('opacity-0');
                    terminal.scrollTop = terminal.scrollHeight; // Auto scroll
                }, 100);
            }

            // Animate logs
            let delay = 0;
            logData.forEach((log, index) => {
                setTimeout(() => {
                    typeLog(log, index);
                }, delay);
                delay += 800; // 800ms delay between lines for "reading" speed
            });
        </script>

    </div>
</div>