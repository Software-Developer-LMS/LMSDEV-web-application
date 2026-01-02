<?php
require_once '../includes/auth_check.php';
require_once '../config/db.php';
require_once '../includes/header.php';

check_student();

// Fetch all modules and lessons grouped
$modules = $pdo->query("SELECT * FROM modules ORDER BY order_no ASC")->fetchAll();
?>

<div class="max-w-screen-xl mx-auto">
    <div class="mb-8 text-center text-left">
        <h1 class="text-3xl font-bold text-gray-900">My Classes & Notes</h1>
        <p class="text-gray-500 mt-1">Access all your course materials in one place.</p>
    </div>

    <div class="space-y-8">
        <?php foreach ($modules as $mod): ?>
            <div class="glass rounded-2xl overflow-hidden border border-white">
                <!-- Module Header -->
                <div class="bg-gray-50/80 p-6 border-b border-gray-100">
                    <h2 class="text-2xl font-bold text-gray-800"><?php echo htmlspecialchars($mod['module_title']); ?></h2>
                    <p class="text-gray-500 mt-1"><?php echo htmlspecialchars($mod['description']); ?></p>
                </div>

                <!-- Lessons -->
                <div class="divide-y divide-gray-100">
                    <?php 
                        $stmt = $pdo->prepare("SELECT * FROM lessons WHERE module_id = ? ORDER BY class_date ASC");
                        $stmt->execute([$mod['id']]);
                        $lessons = $stmt->fetchAll();
                    ?>
                    
                    <?php if (empty($lessons)): ?>
                        <div class="p-6 text-center text-gray-400 text-sm">No lessons in this module yet.</div>
                    <?php else: ?>
                        <?php foreach ($lessons as $lesson): ?>
                            <div class="p-6 hover:bg-white/50 transition-colors">
                                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="text-xs font-semibold px-2 py-0.5 rounded bg-blue-100 text-blue-700">
                                                <?php echo date('M j, Y', strtotime($lesson['class_date'])); ?>
                                            </span>
                                            <span class="text-xs font-semibold px-2 py-0.5 rounded bg-gray-100 text-gray-600">
                                                <?php echo substr($lesson['start_time'], 0, 5) . ' - ' . substr($lesson['end_time'], 0, 5); ?>
                                            </span>
                                        </div>
                                        <h3 class="text-lg font-bold text-gray-900"><?php echo htmlspecialchars($lesson['lesson_title']); ?></h3>
                                        <div class="text-sm text-gray-600 mt-1 flex items-center gap-2">
                                            📍 <?php echo htmlspecialchars($lesson['location']); ?>
                                        </div>
                                        <?php if ($lesson['description']): ?>
                                            <p class="text-sm text-gray-500 mt-2"><?php echo nl2br(htmlspecialchars($lesson['description'])); ?></p>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div>
                                        <?php if ($lesson['notes_file']): ?>
                                            <a href="../uploads/notes/<?php echo $lesson['notes_file']; ?>" download class="inline-flex items-center justify-center px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white text-sm font-medium rounded-lg transition-all shadow-lg shadow-brand-500/30">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 01-2-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                                Download Notes
                                            </a>
                                        <?php else: ?>
                                            <span class="text-gray-400 text-sm italic">No notes available</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
