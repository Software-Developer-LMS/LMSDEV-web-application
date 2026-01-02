<?php
require_once '../includes/auth_check.php';
require_once '../config/db.php';
require_once '../includes/header.php';

check_admin();

$message = '';

// Handle Add Module
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_module'])) {
    $title = htmlspecialchars($_POST['module_title']);
    $desc = htmlspecialchars($_POST['description']);
    $order = (int) $_POST['order_no'];

    $stmt = $pdo->prepare("INSERT INTO modules (module_title, description, order_no) VALUES (?, ?, ?)");
    if ($stmt->execute([$title, $desc, $order])) {
        $message = "Module added successfully!";
    } else {
        $message = "Error adding module.";
    }
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $pdo->prepare("DELETE FROM modules WHERE id = ?")->execute([$id]);
    header("Location: modules.php");
    exit();
}

// Fetch Modules
$modules = $pdo->query("SELECT * FROM modules ORDER BY order_no ASC")->fetchAll();
?>

<div class="max-w-screen-xl mx-auto">
    <div class="flex flex-col md:flex-row justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Manage Modules</h1>
            <p class="text-gray-500 mt-1">Organize your course syllabus.</p>
        </div>
    </div>

    <?php if ($message): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Add Module Form -->
        <div class="md:col-span-1">
            <div class="glass p-6 rounded-2xl sticky top-24">
                <h3 class="text-xl font-bold text-gray-800 mb-4">Add New Module</h3>
                <form method="POST">
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Module Title</label>
                        <input type="text" name="module_title" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Description</label>
                        <textarea name="description" rows="3"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all"></textarea>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Order No</label>
                        <input type="number" name="order_no" value="1"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
                    </div>
                    <button type="submit" name="add_module"
                        class="w-full bg-brand-600 hover:bg-brand-700 text-white font-bold py-2 px-4 rounded-lg transition-all shadow-lg shadow-brand-500/30">
                        Add Module
                    </button>
                    <a href="dashboard.php"
                        class="block text-center mt-4 text-gray-500 hover:text-gray-700 text-sm">Back to Dashboard</a>
                </form>
            </div>
        </div>

        <!-- Module List -->
        <div class="md:col-span-2 space-y-4">
            <?php if (empty($modules)): ?>
                <div class="text-center py-10 text-gray-500">No modules found. Start by adding one!</div>
            <?php else: ?>
                <?php foreach ($modules as $mod): ?>
                    <div class="glass p-6 rounded-2xl flex justify-between items-start group">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="bg-brand-100 text-brand-800 text-xs font-semibold px-2.5 py-0.5 rounded">Order:
                                    <?php echo $mod['order_no']; ?>
                                </span>
                                <h3 class="text-xl font-bold text-gray-900">
                                    <?php echo htmlspecialchars($mod['module_title']); ?>
                                </h3>
                            </div>
                            <p class="text-gray-600 custom-mt-1">
                                <?php echo htmlspecialchars($mod['description']); ?>
                            </p>
                        </div>
                        <div class="flex gap-2 opacity-100 md:opacity-0 group-hover:opacity-100 transition-opacity">
                            <a href="?delete=<?php echo $mod['id']; ?>"
                                onclick="return confirm('Are you sure? This will delete all lessons in this module.');"
                                class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition-colors" title="Delete">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                    </path>
                                </svg>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>