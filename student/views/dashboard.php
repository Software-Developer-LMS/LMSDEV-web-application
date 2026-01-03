<?php
// Fetch User Stats
$user_id = $_SESSION['user_id'];

// Assignments Progress
$total_assign = $conn->query("SELECT COUNT(*) as count FROM assignments")->fetch_assoc()['count'];
$my_submissions = $conn->query("SELECT COUNT(*) as count FROM submissions WHERE user_id=$user_id")->fetch_assoc()['count'];
$progress = ($total_assign > 0) ? round(($my_submissions / $total_assign) * 100) : 0;

// Upcoming Classes
$upcoming_classes = $conn->query("SELECT l.*, m.module_title FROM lessons l JOIN modules m ON l.module_id = m.id WHERE l.class_date >= CURDATE() ORDER BY l.class_date ASC LIMIT 3");

// Announcements
$announcements = $conn->query("SELECT * FROM announcements ORDER BY created_at DESC LIMIT 3");

// XP & Level Logic (Gamification)
$xp_result = $conn->query("SELECT (COALESCE(SUM(marks), 0) + (SELECT legacy_xp FROM users WHERE id=$user_id)) as total_xp FROM submissions WHERE user_id=$user_id")->fetch_assoc();
$current_xp = $xp_result['total_xp'] ? intval($xp_result['total_xp']) : 0;
$current_level = floor($current_xp / 100) + 1;
$level_progress = $current_xp % 100;
?>

<div class="animate-slide-in">
    <!-- Welcome Section -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-nexus-blue mb-2">Welcome back,
            <?php echo explode(' ', $_SESSION['user_name'])[0]; ?>! <span class="ml-2 text-2xl">👨‍💻</span>
        </h1>
        <div class="text-gray-400 font-mono text-sm">
            <span class="text-nexus-green">●</span> System Online <span class="text-nexus-blue">|</span>
            Pending Missions: <span class="text-nexus-purple font-bold">
                <?php echo $total_assign - $my_submissions; ?>
            </span>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- XP Card (Dynamic) -->
        <div
            class="bg-gradient-to-br from-nexus-card to-nexus-black rounded-xl p-5 glow-border hover:animate-pulse-glow transition group">
            <div class="flex justify-between items-start">
                <div>
                    <div class="text-gray-400 text-sm font-mono mb-1">Developer XP</div>
                    <div class="text-3xl font-bold text-nexus-green"><?php echo number_format($current_xp); ?></div>
                    <div class="text-xs text-gray-400 mt-2">Level <?php echo $current_level; ?> Developer</div>
                </div>
                <i class="fas fa-bolt text-nexus-green text-3xl opacity-80 group-hover:opacity-100 transition"></i>
            </div>
            <div class="mt-4">
                <div class="flex justify-between text-[10px] text-gray-500 mb-1 font-mono">
                    <span>Progress to Lvl <?php echo $current_level + 1; ?></span>
                    <span><?php echo $level_progress; ?>/100 XP</span>
                </div>
                <div class="h-1 bg-gray-800 rounded-full overflow-hidden">
                    <div class="h-full bg-gradient-to-r from-nexus-green to-nexus-blue"
                        style="width: <?php echo $level_progress; ?>%"></div>
                </div>
            </div>
        </div>

        <!-- Assignment Progress -->
        <div
            class="bg-gradient-to-br from-nexus-card to-nexus-black rounded-xl p-5 glow-border hover:animate-pulse-glow transition group">
            <div class="flex justify-between items-start">
                <div>
                    <div class="text-gray-400 text-sm font-mono mb-1">Global Progress</div>
                    <div class="text-3xl font-bold text-nexus-blue">
                        <?php echo $progress; ?>%
                    </div>
                    <div class="text-xs text-gray-400 mt-2">
                        <?php echo $my_submissions; ?>/
                        <?php echo $total_assign; ?> Completed
                    </div>
                </div>
                <i class="fas fa-tasks text-nexus-blue text-3xl opacity-80 group-hover:opacity-100 transition"></i>
            </div>
            <div class="mt-4">
                <div class="h-1 bg-gray-800 rounded-full overflow-hidden">
                    <div class="h-full bg-gradient-to-r from-nexus-blue to-nexus-purple"
                        style="width: <?php echo $progress; ?>%"></div>
                </div>
            </div>
        </div>

        <!-- Next Class -->
        <div
            class="bg-gradient-to-br from-nexus-card to-nexus-black rounded-xl p-5 glow-border hover:animate-pulse-glow transition group col-span-1 md:col-span-2">
            <h3 class="text-gray-400 text-sm font-mono mb-3">Next Protocol Session</h3>
            <?php
            $next_class = $upcoming_classes->fetch_assoc();
            if ($next_class):
                ?>
                <div class="flex items-center gap-4">
                    <div class="bg-nexus-blue/10 p-3 rounded-lg text-nexus-blue border border-nexus-blue/30">
                        <i class="fas fa-calendar-day text-2xl"></i>
                    </div>
                    <div>
                        <div class="text-xl font-bold text-white">
                            <?php echo $next_class['module_title']; ?>
                        </div>
                        <div class="text-sm text-nexus-green font-mono">
                            <?php echo date('D, M d', strtotime($next_class['class_date'])); ?> @
                            <?php echo substr($next_class['start_time'], 0, 5); ?>
                        </div>
                        <div class="text-xs text-gray-500"><i class="fas fa-map-marker-alt mr-1"></i>
                            <?php echo $next_class['location']; ?>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="text-gray-500 italic">No upcoming sessions detected.</div>
            <?php endif; ?>
            <?php
            // Reset pointer for second loop if needed, or just re-query. We'll use re-query later or fetch all first.
            $upcoming_classes->data_seek(0);
            ?>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Upcoming Classes Feed -->
        <div class="lg:col-span-2 space-y-6">
            <h2 class="text-xl font-bold text-nexus-blue flex items-center">
                <i class="fas fa-clock mr-2"></i> Incoming Sessions
            </h2>
            <?php while ($class = $upcoming_classes->fetch_assoc()): ?>
                <div
                    class="bg-nexus-card rounded-xl p-4 glow-border border-l-4 border-nexus-purple flex justify-between items-center group hover:bg-nexus-code transition">
                    <div>
                        <div class="font-bold text-lg text-white group-hover:text-nexus-blue transition">
                            <?php echo $class['module_title']; ?>
                        </div>
                        <div class="text-sm text-gray-400">
                            <?php echo $class['lesson_title']; ?>
                        </div>
                        <div class="text-xs text-nexus-green font-mono mt-1">
                            <i class="far fa-calendar mr-1"></i>
                            <?php echo date('M d', strtotime($class['class_date'])); ?> •
                            <i class="far fa-clock mr-1"></i>
                            <?php echo substr($class['start_time'], 0, 5); ?>
                        </div>
                    </div>
                    <div class="text-right hidden md:block">
                        <div class="text-xs text-gray-500 uppercase tracking-widest mb-1">Location</div>
                        <div class="text-sm font-bold text-white">
                            <?php echo $class['location']; ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

        <!-- Announcements Feed -->
        <div class="space-y-6">
            <h2 class="text-xl font-bold text-nexus-green flex items-center">
                <i class="fas fa-bullhorn mr-2"></i> Broadcasts
            </h2>
            <?php while ($announce = $announcements->fetch_assoc()):
                $color = 'nexus-green';
                if ($announce['type'] == 'exam')
                    $color = 'nexus-red';
                if ($announce['type'] == 'deadline')
                    $color = 'nexus-purple';
                ?>
                <div class="bg-nexus-card rounded-xl p-4 glow-border-green relative overflow-hidden">
                    <div class="flex justify-between items-start mb-2">
                        <span
                            class="text-xs font-mono px-2 py-0.5 rounded bg-<?php echo $color; ?>/20 text-<?php echo $color; ?> border border-<?php echo $color; ?>/30 uppercase">
                            <?php echo $announce['type']; ?>
                        </span>
                        <span class="text-[10px] text-gray-500">
                            <?php echo date('M d', strtotime($announce['created_at'])); ?>
                        </span>
                    </div>
                    <h3 class="font-bold text-white mb-1">
                        <?php echo $announce['title']; ?>
                    </h3>
                    <p class="text-sm text-gray-400 leading-relaxed max-h-20 overflow-hidden text-ellipsis">
                        <?php echo $announce['message']; ?>
                    </p>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</div>