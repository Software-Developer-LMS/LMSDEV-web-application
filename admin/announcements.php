<?php
require_once '../includes/auth_check.php';
require_once '../config/db.php';
require_once '../includes/header.php';

check_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['post_announcement'])) {
    $title = htmlspecialchars($_POST['title']);
    $msg = htmlspecialchars($_POST['message']);

    $pdo->prepare("INSERT INTO announcements (title, message) VALUES (?, ?)")->execute([$title, $msg]);
    header("Location: announcements.php");
    exit();
}

if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $pdo->prepare("DELETE FROM announcements WHERE id = ?")->execute([$id]);
    header("Location: announcements.php");
    exit();
}

$announcements = $pdo->query("SELECT * FROM announcements ORDER BY created_at DESC")->fetchAll();
?>

<div class="max-w-screen-xl mx-auto">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Manage Announcements</h1>
        <p class="text-gray-500 mt-1">Keep students updated.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="md:col-span-1">
            <div class="glass p-6 rounded-2xl sticky top-24">
                <h3 class="text-xl font-bold text-gray-800 mb-4">Post Update</h3>
                <form method="POST">
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Title</label>
                        <input type="text" name="title" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-500">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Message</label>
                        <textarea name="message" rows="4" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-500"></textarea>
                    </div>
                    <button type="submit" name="post_announcement"
                        class="w-full bg-brand-600 hover:bg-brand-700 text-white font-bold py-2 px-4 rounded-lg transition-all shadow-lg shadow-brand-500/30">
                        Post Announcement
                    </button>
                    <a href="dashboard.php"
                        class="block text-center mt-4 text-gray-500 hover:text-gray-700 text-sm">Back to Dashboard</a>
                </form>
            </div>
        </div>

        <div class="md:col-span-2 space-y-4">
            <?php foreach ($announcements as $ann): ?>
                <div class="glass p-6 rounded-2xl relative group">
                    <h3 class="text-lg font-bold text-gray-900">
                        <?php echo htmlspecialchars($ann['title']); ?>
                    </h3>
                    <p class="text-gray-600 mt-2 text-sm">
                        <?php echo nl2br(htmlspecialchars($ann['message'])); ?>
                    </p>
                    <div class="text-xs text-gray-400 mt-3">
                        <?php echo date('M j, Y g:i A', strtotime($ann['created_at'])); ?>
                    </div>
                    <a href="?delete=<?php echo $ann['id']; ?>"
                        class="absolute top-4 right-4 text-gray-300 hover:text-red-500 opacity-0 group-hover:opacity-100 transition-all"
                        onclick="return confirm('Delete?');">🗑️</a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>