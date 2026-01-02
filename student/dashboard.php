<?php
require_once '../includes/auth_check.php';
require_once '../config/db.php';
require_once '../includes/header.php';

check_student();
$user_id = $_SESSION['user_id'];

// Get upcoming class (Next lesson after today)
$stmt = $pdo->prepare("SELECT l.*, m.module_title FROM lessons l JOIN modules m ON l.module_id = m.id WHERE l.class_date >= CURDATE() ORDER BY l.class_date ASC LIMIT 1");
$stmt->execute();
$next_class = $stmt->fetch();

// Get latest announcements
$stmt = $pdo->query("SELECT * FROM announcements ORDER BY created_at DESC LIMIT 3");
$announcements = $stmt->fetchAll();

// Get assignment progress
$total_assignments = $pdo->query("SELECT COUNT(*) FROM assignments")->fetchColumn();
if ($total_assignments > 0) {
    $submitted = $pdo->prepare("SELECT COUNT(*) FROM submissions WHERE user_id = ?");
    $submitted->execute([$user_id]);
    $submitted_count = $submitted->fetchColumn();
    $progress = round(($submitted_count / $total_assignments) * 100);
} else {
    $progress = 0;
    $submitted_count = 0;
}
?>

<div class="max-w-screen-xl mx-auto">
    <!-- Welcome Section -->
    <div class="flex flex-col md:flex-row justify-between items-center mb-8 glass p-8 rounded-3xl">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Hello,
                <?php echo htmlspecialchars($_SESSION['user_name']); ?>! 👋
            </h1>
            <p class="text-gray-500 mt-2">Ready to learn? Here's what's happening today.</p>
        </div>
        <div class="mt-4 md:mt-0 w-full md:w-1/3">
            <div class="flex justify-between mb-1">
                <span class="text-sm font-medium text-brand-700">Course Progress</span>
                <span class="text-sm font-medium text-brand-700">
                    <?php echo $progress; ?>%
                </span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-300">
                <div class="bg-brand-600 h-2.5 rounded-full transition-all duration-1000"
                    style="width: <?php echo $progress; ?>%"></div>
            </div>
            <div class="text-xs text-gray-500 mt-1">
                <?php echo $submitted_count; ?> of
                <?php echo $total_assignments; ?> assignments submitted
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Next Class -->
            <section>
                <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <span class="p-2 bg-blue-100 text-blue-600 rounded-lg">📅</span> Upcoming Class
                </h2>
                <?php if ($next_class): ?>
                    <div class="glass p-6 rounded-2xl border-l-4 border-l-brand-500">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-xl font-bold text-gray-900">
                                    <?php echo htmlspecialchars($next_class['lesson_title']); ?>
                                </h3>
                                <div class="text-sm text-brand-600 font-semibold mb-2">
                                    <?php echo htmlspecialchars($next_class['module_title']); ?>
                                </div>
                                <div class="space-y-1 text-gray-600">
                                    <p class="flex items-center gap-2">
                                        🗓️
                                        <?php echo date('F j, Y', strtotime($next_class['class_date'])); ?>
                                    </p>
                                    <p class="flex items-center gap-2">
                                        ⏰
                                        <?php echo date('g:i A', strtotime($next_class['start_time'])) . ' - ' . date('g:i A', strtotime($next_class['end_time'])); ?>
                                    </p>
                                    <p class="flex items-center gap-2">
                                        📍
                                        <?php echo htmlspecialchars($next_class['location']); ?>
                                    </p>
                                </div>
                            </div>
                            <?php if ($next_class['notes_file']): ?>
                                <a href="../uploads/notes/<?php echo $next_class['notes_file']; ?>" download
                                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium transition-colors">
                                    Download Notes
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="glass p-6 rounded-2xl text-center text-gray-500 py-12">
                        No upcoming classes scheduled. Enjoy your break! ☕
                    </div>
                <?php endif; ?>
            </section>

            <!-- Quick Links -->
            <section>
                <div class="grid grid-cols-2 gap-4">
                    <a href="lessons.php"
                        class="p-6 bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow flex flex-col items-center justify-center text-center group border border-gray-100">
                        <span class="text-4xl mb-3 group-hover:scale-110 transition-transform">📚</span>
                        <span class="font-semibold text-gray-800">All Lessons</span>
                    </a>
                    <a href="assignment_submit.php"
                        class="p-6 bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow flex flex-col items-center justify-center text-center group border border-gray-100">
                        <span class="text-4xl mb-3 group-hover:scale-110 transition-transform">📝</span>
                        <span class="font-semibold text-gray-800">Assignments</span>
                    </a>
                </div>
            </section>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                <span class="p-2 bg-yellow-100 text-yellow-600 rounded-lg">📢</span> Announcements
            </h2>
            <div class="space-y-4">
                <?php if ($announcements): ?>
                    <?php foreach ($announcements as $announce): ?>
                        <div class="glass p-5 rounded-xl">
                            <h3 class="font-semibold text-gray-900">
                                <?php echo htmlspecialchars($announce['title']); ?>
                            </h3>
                            <p class="text-gray-600 text-sm mt-1 line-clamp-3">
                                <?php echo nl2br(htmlspecialchars($announce['message'])); ?>
                            </p>
                            <div class="text-xs text-gray-400 mt-3">
                                <?php echo date('M j, Y', strtotime($announce['created_at'])); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center text-gray-400 py-4">No new announcements.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>