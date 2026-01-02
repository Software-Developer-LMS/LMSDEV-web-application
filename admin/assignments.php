<?php
require_once '../includes/auth_check.php';
require_once '../config/db.php';
require_once '../includes/header.php';

check_admin();

$message = '';

// Handle Add Assignment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_assignment'])) {
    $module_id = (int) $_POST['module_id'];
    $title = htmlspecialchars($_POST['title']);
    $desc = htmlspecialchars($_POST['description']);
    $deadline = $_POST['deadline'];

    $stmt = $pdo->prepare("INSERT INTO assignments (module_id, title, description, deadline) VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$module_id, $title, $desc, $deadline])) {
        $message = "Assignment created successfully!";
    } else {
        $message = "Error creating assignment.";
    }
}

// Fetch Modules for dropdown
$modules = $pdo->query("SELECT * FROM modules ORDER BY order_no ASC")->fetchAll();

// Fetch Assignments
$assignments = $pdo->query("SELECT a.*, m.module_title FROM assignments a JOIN modules m ON a.module_id = m.id ORDER BY a.deadline DESC")->fetchAll();

// Fetch Submissions if view selected
$submissions = [];
$selected_assignment = null;
if (isset($_GET['view'])) {
    $view_id = (int) $_GET['view'];
    $stmt = $pdo->prepare("SELECT s.*, u.name, u.student_id FROM submissions s JOIN users u ON s.user_id = u.id WHERE s.assignment_id = ? ORDER BY s.submitted_at DESC");
    $stmt->execute([$view_id]);
    $submissions = $stmt->fetchAll();

    $stmt = $pdo->prepare("SELECT title FROM assignments WHERE id = ?");
    $stmt->execute([$view_id]);
    $selected_assignment = $stmt->fetchColumn();
}
?>

<div class="max-w-screen-xl mx-auto">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Manage Assignments</h1>
        <p class="text-gray-500 mt-1">Create tasks and review student work.</p>
    </div>

    <?php if ($message): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Add Assignment -->
        <div class="lg:col-span-1">
            <div class="glass p-6 rounded-2xl sticky top-24">
                <h3 class="text-xl font-bold text-gray-800 mb-4">Create Assignment</h3>
                <form method="POST">
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Module</label>
                        <select name="module_id" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-500 bg-white">
                            <?php foreach ($modules as $mod): ?>
                                <option value="<?php echo $mod['id']; ?>">
                                    <?php echo htmlspecialchars($mod['module_title']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Title</label>
                        <input type="text" name="title" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-500">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Deadline</label>
                        <input type="datetime-local" name="deadline" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-500">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Description</label>
                        <textarea name="description" rows="3"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-500"></textarea>
                    </div>
                    <button type="submit" name="add_assignment"
                        class="w-full bg-brand-600 hover:bg-brand-700 text-white font-bold py-2 px-4 rounded-lg transition-all shadow-lg shadow-brand-500/30">
                        Create Task
                    </button>
                    <a href="dashboard.php"
                        class="block text-center mt-4 text-gray-500 hover:text-gray-700 text-sm">Back to Dashboard</a>
                </form>
            </div>
        </div>

        <!-- Assignments List & Submissions -->
        <div class="lg:col-span-2 space-y-8">
            <!-- List -->
            <div class="glass overflow-hidden rounded-2xl">
                <table class="min-w-full text-left text-sm text-gray-600">
                    <thead class="bg-gray-50 text-gray-900 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 font-semibold">Title</th>
                            <th class="px-6 py-4 font-semibold">Module</th>
                            <th class="px-6 py-4 font-semibold">Deadline</th>
                            <th class="px-6 py-4 font-semibold">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php if (empty($assignments)): ?>
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-500">No assignments created.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($assignments as $asn): ?>
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-6 py-4 font-medium text-gray-900">
                                        <?php echo htmlspecialchars($asn['title']); ?>
                                    </td>
                                    <td class="px-6 py-4">
                                        <?php echo htmlspecialchars($asn['module_title']); ?>
                                    </td>
                                    <td
                                        class="px-6 py-4 <?php echo (strtotime($asn['deadline']) < time()) ? 'text-red-500' : 'text-green-600'; ?>">
                                        <?php echo date('M j, g:i A', strtotime($asn['deadline'])); ?>
                                    </td>
                                    <td class="px-6 py-4">
                                        <a href="?view=<?php echo $asn['id']; ?>"
                                            class="text-brand-600 hover:text-brand-800 font-medium">View Submissions</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Submissions Viewer -->
            <?php if (isset($_GET['view'])): ?>
                <div id="submissions" class="glass p-6 rounded-2xl border-t-4 border-brand-500">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Submissions for:
                        <?php echo htmlspecialchars($selected_assignment); ?>
                    </h3>
                    <?php if (empty($submissions)): ?>
                        <p class="text-gray-500">No submissions yet.</p>
                    <?php else: ?>
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-left text-sm text-gray-600">
                                <thead class="bg-gray-50 border-b border-gray-200">
                                    <tr>
                                        <th class="px-4 py-2">Student</th>
                                        <th class="px-4 py-2">Submitted At</th>
                                        <th class="px-4 py-2">Github</th>
                                        <th class="px-4 py-2">File</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <?php foreach ($submissions as $sub): ?>
                                        <tr>
                                            <td class="px-4 py-2 font-medium text-gray-900">
                                                <?php echo htmlspecialchars($sub['name']); ?> <span class="text-gray-400 text-xs">(
                                                    <?php echo $sub['student_id']; ?>)
                                                </span>
                                            </td>
                                            <td class="px-4 py-2">
                                                <?php echo date('M j, H:i', strtotime($sub['submitted_at'])); ?>
                                            </td>
                                            <td class="px-4 py-2">
                                                <?php if ($sub['github_link']): ?>
                                                    <a href="<?php echo htmlspecialchars($sub['github_link']); ?>" target="_blank"
                                                        class="text-blue-600 hover:underline">Repo Link</a>
                                                <?php else: ?>
                                                    -
                                                <?php endif; ?>
                                            </td>
                                            <td class="px-4 py-2">
                                                <?php if ($sub['file_path']): ?>
                                                    <a href="../uploads/assignments/<?php echo $sub['file_path']; ?>" download
                                                        class="text-brand-600 hover:underline">Download</a>
                                                <?php else: ?>
                                                    -
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>