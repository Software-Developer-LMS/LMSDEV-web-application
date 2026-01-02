<?php
require_once '../includes/auth_check.php';
require_once '../config/db.php';
require_once '../includes/header.php';

check_student();
$user_id = $_SESSION['user_id'];
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_assignment'])) {
    $asn_id = (int) $_POST['assignment_id'];
    $github = htmlspecialchars($_POST['github_link']);

    $file_path = null;
    if (isset($_FILES['file']) && $_FILES['file']['error'] === 0) {
        $allowed = ['pdf', 'zip', 'rar'];
        $ext = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $allowed)) {
            $filename = uniqid() . "_u" . $user_id . "." . $ext;
            if (move_uploaded_file($_FILES['file']['tmp_name'], '../uploads/assignments/' . $filename)) {
                $file_path = $filename;
            }
        }
    }

    // Check if already submitted
    $check = $pdo->prepare("SELECT id FROM submissions WHERE assignment_id = ? AND user_id = ?");
    $check->execute([$asn_id, $user_id]);

    if ($check->fetch()) {
        // Update
        $sql = "UPDATE submissions SET github_link = ?, submitted_at = NOW()";
        $params = [$github];
        if ($file_path) {
            $sql .= ", file_path = ?";
            $params[] = $file_path;
        }
        $sql .= " WHERE assignment_id = ? AND user_id = ?";
        $params[] = $asn_id;
        $params[] = $user_id;

        $pdo->prepare($sql)->execute($params);
        $message = "Assignment updated successfully!";
    } else {
        // Insert
        // Only insert if at least one submission method is present
        if ($github || $file_path) {
            $stmt = $pdo->prepare("INSERT INTO submissions (assignment_id, user_id, file_path, github_link) VALUES (?, ?, ?, ?)");
            $stmt->execute([$asn_id, $user_id, $file_path, $github]);
            $message = "Assignment submitted successfully!";
        } else {
            $message = "Please upload a file or provide a GitHub link.";
        }
    }
}

// Fetch Assignments with Submission Status
$sql = "SELECT a.*, m.module_title, s.file_path, s.github_link, s.submitted_at 
        FROM assignments a 
        JOIN modules m ON a.module_id = m.id 
        LEFT JOIN submissions s ON a.id = s.assignment_id AND s.user_id = ? 
        ORDER BY a.deadline DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$assignments = $stmt->fetchAll();
?>

<div class="max-w-screen-xl mx-auto">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900"> Assignments</h1>
        <p class="text-gray-500 mt-1">Submit your work before the deadline.</p>
    </div>

    <?php if ($message): ?>
        <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative mb-4">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <div class="space-y-6">
        <?php foreach ($assignments as $asn): ?>
            <?php
            $is_late = time() > strtotime($asn['deadline']);
            $is_submitted = !empty($asn['submitted_at']);
            $status_color = $is_submitted ? 'border-green-500' : ($is_late ? 'border-red-500' : 'border-white');
            ?>
            <div class="glass p-6 rounded-2xl border-l-4 <?php echo $status_color; ?> hover:shadow-md transition-shadow">
                <div class="flex flex-col md:flex-row gap-6">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-xs font-semibold px-2 py-0.5 rounded bg-gray-100">
                                <?php echo htmlspecialchars($asn['module_title']); ?>
                            </span>
                            <?php if ($is_submitted): ?>
                                <span
                                    class="text-xs font-semibold px-2 py-0.5 rounded bg-green-100 text-green-700">Submitted</span>
                            <?php elseif ($is_late): ?>
                                <span class="text-xs font-semibold px-2 py-0.5 rounded bg-red-100 text-red-700">Overdue</span>
                            <?php else: ?>
                                <span class="text-xs font-semibold px-2 py-0.5 rounded bg-blue-100 text-blue-700">Pending</span>
                            <?php endif; ?>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900">
                            <?php echo htmlspecialchars($asn['title']); ?>
                        </h3>
                        <p class="text-gray-600 mt-2 text-sm">
                            <?php echo nl2br(htmlspecialchars($asn['description'])); ?>
                        </p>
                        <p class="text-sm font-medium mt-3 <?php echo $is_late ? 'text-red-500' : 'text-gray-500'; ?>">
                            Deadline:
                            <?php echo date('M j, Y g:i A', strtotime($asn['deadline'])); ?>
                        </p>
                    </div>

                    <div class="w-full md:w-1/3 bg-white/50 p-4 rounded-xl">
                        <form method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="assignment_id" value="<?php echo $asn['id']; ?>">
                            <div class="mb-3">
                                <label class="block text-xs font-bold text-gray-700 mb-1">GitHub Repo Link</label>
                                <input type="url" name="github_link"
                                    value="<?php echo htmlspecialchars($asn['github_link'] ?? ''); ?>"
                                    placeholder="https://github.com/..."
                                    class="w-full text-sm px-3 py-2 border border-gray-300 rounded-lg">
                            </div>
                            <div class="mb-3">
                                <label class="block text-xs font-bold text-gray-700 mb-1">Upload File (PDF/ZIP)</label>
                                <input type="file" name="file"
                                    class="w-full text-xs text-gray-500 file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:bg-brand-50 file:text-brand-700">
                                <?php if ($asn['file_path']): ?>
                                    <p class="text-xs text-green-600 mt-1">✓ File uploaded</p>
                                <?php endif; ?>
                            </div>
                            <button type="submit" name="submit_assignment"
                                class="w-full bg-brand-600 hover:bg-brand-700 text-white font-bold py-2 px-4 rounded-lg text-sm transition-all shadow-md shadow-brand-500/20">
                                <?php echo $is_submitted ? 'Update Submission' : 'Submit Assignment'; ?>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>