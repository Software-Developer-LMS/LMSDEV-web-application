<?php
require_once '../includes/auth_check.php';
require_once '../config/db.php';
require_once '../includes/header.php';

check_admin();

$message = '';

// Handle Add Lesson
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_lesson'])) {
    $module_id = (int) $_POST['module_id'];
    $title = htmlspecialchars($_POST['lesson_title']);
    $date = $_POST['class_date'];
    $start = $_POST['start_time'];
    $end = $_POST['end_time'];
    $loc = htmlspecialchars($_POST['location']);
    $desc = htmlspecialchars($_POST['description']);

    // File Upload
    $notes_file = null;
    if (isset($_FILES['notes_file']) && $_FILES['notes_file']['error'] === 0) {
        $allowed = ['pdf', 'zip', 'doc', 'docx', 'ppt', 'pptx'];
        $ext = strtolower(pathinfo($_FILES['notes_file']['name'], PATHINFO_EXTENSION));

        if (in_array($ext, $allowed)) {
            $filename = uniqid() . '.' . $ext;
            if (move_uploaded_file($_FILES['notes_file']['tmp_name'], '../uploads/notes/' . $filename)) {
                $notes_file = $filename;
            } else {
                $message = "Error uploading file.";
            }
        } else {
            $message = "Invalid file type. Allowed: PDF, ZIP, DOC, PPT";
        }
    }

    if (!$message) { // Only proceed if no upload error
        $stmt = $pdo->prepare("INSERT INTO lessons (module_id, lesson_title, class_date, start_time, end_time, location, notes_file, description) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$module_id, $title, $date, $start, $end, $loc, $notes_file, $desc])) {
            $message = "Lesson scheduled successfully!";
        } else {
            $message = "Database error.";
        }
    }
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    // Get file to delete
    $stmt = $pdo->prepare("SELECT notes_file FROM lessons WHERE id = ?");
    $stmt->execute([$id]);
    $file = $stmt->fetchColumn();
    if ($file && file_exists("../uploads/notes/$file")) {
        unlink("../uploads/notes/$file");
    }

    $pdo->prepare("DELETE FROM lessons WHERE id = ?")->execute([$id]);
    header("Location: lessons.php");
    exit();
}

// Fetch Data
$modules = $pdo->query("SELECT * FROM modules ORDER BY order_no ASC")->fetchAll();
$lessons = $pdo->query("SELECT l.*, m.module_title FROM lessons l JOIN modules m ON l.module_id = m.id ORDER BY l.class_date DESC")->fetchAll();
?>

<div class="max-w-screen-xl mx-auto">
    <div class="flex flex-col md:flex-row justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Manage Lessons</h1>
            <p class="text-gray-500 mt-1">Schedule physical classes and upload notes.</p>
        </div>
    </div>

    <?php if ($message): ?>
        <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative mb-4">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Add Lesson Form -->
        <div class="lg:col-span-1">
            <div class="glass p-6 rounded-2xl sticky top-24">
                <h3 class="text-xl font-bold text-gray-800 mb-4">Schedule Lesson</h3>
                <form method="POST" enctype="multipart/form-data">
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
                        <label class="block text-gray-700 text-sm font-bold mb-2">Lesson Title</label>
                        <input type="text" name="lesson_title" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-500">
                    </div>
                    <div class="grid grid-cols-2 gap-2 mb-4">
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Date</label>
                            <input type="date" name="class_date" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-500">
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Location</label>
                            <input type="text" name="location" value="Main Hall"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-500">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-2 mb-4">
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Start Time</label>
                            <input type="time" name="start_time" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-500">
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">End Time</label>
                            <input type="time" name="end_time" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-500">
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Notes (Optional)</label>
                        <input type="file" name="notes_file"
                            class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100">
                        <p class="text-xs text-gray-400 mt-1">PDF, ZIP, DOC</p>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Description</label>
                        <textarea name="description" rows="2"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-500"></textarea>
                    </div>
                    <button type="submit" name="add_lesson"
                        class="w-full bg-brand-600 hover:bg-brand-700 text-white font-bold py-2 px-4 rounded-lg transition-all shadow-lg shadow-brand-500/30">
                        Save Lesson
                    </button>
                    <a href="dashboard.php"
                        class="block text-center mt-4 text-gray-500 hover:text-gray-700 text-sm">Back to Dashboard</a>
                </form>
            </div>
        </div>

        <!-- Lessons List -->
        <div class="lg:col-span-2 space-y-4">
            <?php if (empty($lessons)): ?>
                <div class="text-center py-10 text-gray-500">No lessons scheduled.</div>
            <?php else: ?>
                <?php foreach ($lessons as $lesson): ?>
                    <div
                        class="glass p-6 rounded-2xl group hover:bg-white transition-all border border-transparent hover:border-gray-100">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="text-xs font-semibold px-2 py-0.5 rounded bg-gray-100 text-gray-600">
                                        <?php echo htmlspecialchars($lesson['module_title']); ?>
                                    </span>
                                    <span class="text-xs font-semibold px-2 py-0.5 rounded bg-blue-50 text-blue-600">
                                        <?php echo htmlspecialchars($lesson['class_date']); ?>
                                    </span>
                                </div>
                                <h3 class="text-xl font-bold text-gray-900">
                                    <?php echo htmlspecialchars($lesson['lesson_title']); ?>
                                </h3>
                                <div class="text-sm text-gray-500 mt-1 flex items-center gap-4">
                                    <span class="flex items-center gap-1">🕒
                                        <?php echo substr($lesson['start_time'], 0, 5) . ' - ' . substr($lesson['end_time'], 0, 5); ?>
                                    </span>
                                    <span class="flex items-center gap-1">📍
                                        <?php echo htmlspecialchars($lesson['location']); ?>
                                    </span>
                                </div>
                                <?php if ($lesson['notes_file']): ?>
                                    <div class="mt-3">
                                        <a href="../uploads/notes/<?php echo $lesson['notes_file']; ?>" target="_blank"
                                            class="text-brand-600 hover:text-brand-800 text-sm font-medium flex items-center gap-1">
                                            📄 View/Download Notes
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div>
                                <a href="?delete=<?php echo $lesson['id']; ?>" onclick="return confirm('Delete this lesson?');"
                                    class="text-red-400 hover:text-red-600 p-2">
                                    🗑️
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>