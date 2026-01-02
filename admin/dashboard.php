<?php
require_once '../includes/auth_check.php';
require_once '../config/db.php';
require_once '../includes/header.php';

check_admin();

// Fetch stats
$student_count = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'student'")->fetchColumn();
$module_count = $pdo->query("SELECT COUNT(*) FROM modules")->fetchColumn();
$assignment_count = $pdo->query("SELECT COUNT(*) FROM assignments")->fetchColumn();
$submission_count = $pdo->query("SELECT COUNT(*) FROM submissions")->fetchColumn();
?>

<div class="max-w-screen-xl mx-auto">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Admin Dashboard</h1>
        <p class="text-gray-500 mt-1">Manage your course, students, and content.</p>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="glass p-6 rounded-2xl shadow-sm border border-white hover:shadow-md transition-shadow">
            <div class="text-gray-500 text-sm font-medium uppercase tracking-wider">Total Students</div>
            <div class="text-3xl font-bold text-brand-600 mt-2">
                <?php echo $student_count; ?>
            </div>
        </div>
        <div class="glass p-6 rounded-2xl shadow-sm border border-white hover:shadow-md transition-shadow">
            <div class="text-gray-500 text-sm font-medium uppercase tracking-wider">Modules</div>
            <div class="text-3xl font-bold text-purple-600 mt-2">
                <?php echo $module_count; ?>
            </div>
        </div>
        <div class="glass p-6 rounded-2xl shadow-sm border border-white hover:shadow-md transition-shadow">
            <div class="text-gray-500 text-sm font-medium uppercase tracking-wider">Active Assignments</div>
            <div class="text-3xl font-bold text-orange-600 mt-2">
                <?php echo $assignment_count; ?>
            </div>
        </div>
        <div class="glass p-6 rounded-2xl shadow-sm border border-white hover:shadow-md transition-shadow">
            <div class="text-gray-500 text-sm font-medium uppercase tracking-wider">Submissions</div>
            <div class="text-3xl font-bold text-emerald-600 mt-2">
                <?php echo $submission_count; ?>
            </div>
        </div>
    </div>

    <!-- Action Cards -->
    <h2 class="text-xl font-bold text-gray-800 mb-4">Quick Actions</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <a href="modules.php"
            class="glass p-6 rounded-2xl group hover:bg-white transition-all border border-transparent hover:border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 group-hover:text-brand-600">Modules & Lessons</h3>
            <p class="text-gray-500 text-sm mt-2">Manage course syllabus, schedule classes, and upload notes.</p>
        </a>
        <a href="students.php"
            class="glass p-6 rounded-2xl group hover:bg-white transition-all border border-transparent hover:border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 group-hover:text-brand-600">Students</h3>
            <p class="text-gray-500 text-sm mt-2">Add new students, reset passwords, and manage access.</p>
        </a>
        <a href="assignments.php"
            class="glass p-6 rounded-2xl group hover:bg-white transition-all border border-transparent hover:border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 group-hover:text-brand-600">Assignments</h3>
            <p class="text-gray-500 text-sm mt-2">Create tasks, set deadlines, and review submissions.</p>
        </a>
        <a href="announcements.php"
            class="glass p-6 rounded-2xl group hover:bg-white transition-all border border-transparent hover:border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 group-hover:text-brand-600">Announcements</h3>
            <p class="text-gray-500 text-sm mt-2">Post updates about class schedules or exams.</p>
        </a>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>