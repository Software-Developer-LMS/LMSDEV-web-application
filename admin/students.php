<?php
require_once '../includes/auth_check.php';
require_once '../config/db.php';
require_once '../includes/header.php';

check_admin();

$message = '';
$error = '';

// Handle Add Student
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_student'])) {
    $student_id = htmlspecialchars($_POST['student_id']);
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $password = $_POST['password'];

    // Check duplication
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ? OR student_id = ?");
    $stmt->execute([$email, $student_id]);
    if ($stmt->fetchColumn() > 0) {
        $error = "Student ID or Email already exists.";
    } else {
        $hashed_pwd = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (student_id, name, email, password, role) VALUES (?, ?, ?, ?, 'student')");
        if ($stmt->execute([$student_id, $name, $email, $hashed_pwd])) {
            $message = "Student added successfully!";
        } else {
            $error = "Database error.";
        }
    }
}

// Handle Status Toggle
if (isset($_GET['toggle'])) {
    $id = (int) $_GET['toggle'];
    $stmt = $pdo->prepare("SELECT status FROM users WHERE id = ?");
    $stmt->execute([$id]);
    $current = $stmt->fetchColumn();
    $new_status = ($current === 'active') ? 'inactive' : 'active';

    $pdo->prepare("UPDATE users SET status = ? WHERE id = ?")->execute([$new_status, $id]);
    header("Location: students.php");
    exit();
}

$students = $pdo->query("SELECT * FROM users WHERE role = 'student' ORDER BY created_at DESC")->fetchAll();
?>

<div class="max-w-screen-xl mx-auto">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Manage Students</h1>
        <p class="text-gray-500 mt-1">Register new students and manage access.</p>
    </div>

    <?php if ($message): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Add Student Form -->
        <div class="lg:col-span-1">
            <div class="glass p-6 rounded-2xl sticky top-24">
                <h3 class="text-xl font-bold text-gray-800 mb-4">Register Student</h3>
                <form method="POST">
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Student ID</label>
                        <input type="text" name="student_id" placeholder="SD001" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Full Name</label>
                        <input type="text" name="name" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                        <input type="email" name="email" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Initial Password</label>
                        <input type="text" name="password" value="student123" required
                            class="w-full px-3 py-2 border border-gray-300 bg-gray-50 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all font-mono text-sm">
                        <p class="text-xs text-gray-400 mt-1">Share this with the student.</p>
                    </div>
                    <button type="submit" name="add_student"
                        class="w-full bg-brand-600 hover:bg-brand-700 text-white font-bold py-2 px-4 rounded-lg transition-all shadow-lg shadow-brand-500/30">
                        Register
                    </button>
                    <a href="dashboard.php"
                        class="block text-center mt-4 text-gray-500 hover:text-gray-700 text-sm">Back to Dashboard</a>
                </form>
            </div>
        </div>

        <!-- Student List -->
        <div class="lg:col-span-2">
            <div class="glass overflow-hidden rounded-2xl">
                <table class="min-w-full text-left text-sm text-gray-600">
                    <thead class="bg-gray-50 text-gray-900 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 font-semibold">ID</th>
                            <th class="px-6 py-4 font-semibold">Name</th>
                            <th class="px-6 py-4 font-semibold">Email</th>
                            <th class="px-6 py-4 font-semibold">Status</th>
                            <th class="px-6 py-4 font-semibold">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php if (empty($students)): ?>
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500">No students registered yet.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($students as $stu): ?>
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-6 py-4 font-medium text-gray-900">
                                        <?php echo htmlspecialchars($stu['student_id']); ?>
                                    </td>
                                    <td class="px-6 py-4">
                                        <?php echo htmlspecialchars($stu['name']); ?>
                                    </td>
                                    <td class="px-6 py-4">
                                        <?php echo htmlspecialchars($stu['email']); ?>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="px-2 py-1 rounded-full text-xs font-semibold <?php echo $stu['status'] === 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'; ?>">
                                            <?php echo ucfirst($stu['status']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <a href="?toggle=<?php echo $stu['id']; ?>"
                                            class="text-brand-600 hover:text-brand-800 font-medium text-xs">
                                            <?php echo $stu['status'] === 'active' ? 'Disable' : 'Enable'; ?>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>