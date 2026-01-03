<?php
// admin/views/submissions.php
include '../includes/db_connection.php';

if (!isset($_GET['assignment_id'])) {
    echo "<div class='text-nexus-red'>Error: No assignment specified.</div>";
    exit;
}

$assignment_id = intval($_GET['assignment_id']);

// --- HANDLE GRADING SUBMISSION ---
// --- HANDLE GRADING SUBMISSION ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_grade'])) {
    $sub_id = intval($_POST['submission_id']);
    $marks = intval($_POST['marks']);
    $feedback = $_POST['feedback'];
    $graded_at = date('Y-m-d H:i:s');

    $grade_sql = "UPDATE submissions SET marks = ?, feedback = ?, graded_at = ? WHERE id = ?";
    $stmt = $conn->prepare($grade_sql);
    $stmt->bind_param("issi", $marks, $feedback, $graded_at, $sub_id);

    if ($stmt->execute()) {
        echo "<script>window.location.href='?page=submissions&assignment_id=$assignment_id&msg=graded';</script>";
        exit;
    } else {
        $error = "Error saving grade: " . $stmt->error;
    }
}

// Fetch Assignment Details
$assign_sql = "SELECT * FROM assignments WHERE id = $assignment_id";
$assign = $conn->query($assign_sql)->fetch_assoc();

if (!$assign) {
    echo "<div class='text-nexus-red'>Error: Assignment not found.</div>";
    exit;
}

// Fetch All Students and their submission status
// Fetch All Students and their submission status
// Using explicit columns to avoid ambiguous column issues in join
$sql = "SELECT u.id, u.name, u.email, u.student_id as student_code, 
               s.id as submission_id, s.file_path, s.github_link, s.submitted_at, s.marks, s.feedback 
        FROM users u 
        LEFT JOIN submissions s ON u.id = s.user_id AND s.assignment_id = $assignment_id
        WHERE u.role = 'student' 
        ORDER BY u.name ASC";

$result = $conn->query($sql);

if (!$result) {
    echo "<div class='text-red-500 font-mono p-4 border border-red-500'>SQL Error: " . $conn->error . "</div>";
    die();
}
?>

<div class="flex flex-col gap-6">
    <div class="flex justify-between items-center mb-4">
        <div>
            <a href="?page=assignments" class="text-xs text-gray-500 hover:text-white mb-2 block"><i
                    class="fa-solid fa-arrow-left"></i> Back to Directives</a>
            <h2 class="text-2xl font-header font-bold text-white uppercase tracking-widest">
                <span class="text-nexus-blue">Submission</span>_Log
            </h2>
            <div class="text-sm font-mono text-gray-400 mt-1">
                Directive: <span class="text-white font-bold">
                    <?php echo htmlspecialchars($assign['title']); ?>
                </span>
            </div>
        </div>

        <div class="text-right">
            <div class="text-xs text-gray-500 uppercase">Deadline</div>
            <div class="text-nexus-red font-bold font-mono">
                <?php echo date('Y-m-d H:i', strtotime($assign['deadline'])); ?>
            </div>
        </div>
    </div>

    <div class="holo-card rounded-xl overflow-hidden">
        <table class="w-full text-left text-xs font-mono">
            <thead class="bg-nexus-dark border-b border-gray-800 text-gray-500 uppercase">
                <tr>
                    <th class="px-6 py-4">Operative (Student)</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4">Timestamp</th>
                    <th class="px-6 py-4">Assessment (Grade)</th>
                    <th class="px-6 py-4">Payload/Uplink</th>
                    <th class="px-6 py-4 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-800 text-gray-400">
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()):
                        $is_submitted = !empty($row['submission_id']);

                        $status_class = 'text-nexus-red';
                        $status_text = 'PENDING';

                        if ($is_submitted) {
                            $deadline_ts = strtotime($assign['deadline']);
                            $submitted_ts = strtotime($row['submitted_at']);

                            if ($submitted_ts > $deadline_ts) {
                                $status_class = 'text-yellow-500';
                                $status_text = 'LATE';
                            } else {
                                $status_class = 'text-nexus-green';
                                $status_text = 'RECEIVED';
                            }
                        }
                        ?>
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-bold text-white">
                                    <?php echo htmlspecialchars($row['name']); ?>
                                </div>
                                <div class="text-[10px] text-gray-500">
                                    <?php echo htmlspecialchars($row['student_code']); ?>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="<?php echo $status_class; ?> font-bold tracking-wider flex items-center gap-2">
                                    <?php if ($status_text == 'LATE'): ?>
                                        <i class="fa-solid fa-triangle-exclamation"></i>
                                    <?php endif; ?>
                                    <?php echo $status_text; ?>
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <?php echo $is_submitted ? $row['submitted_at'] : '<span class="text-gray-600">--</span>'; ?>
                            </td>
                            <td class="px-6 py-4">
                                <?php if ($is_submitted): ?>
                                    <?php if (isset($row['marks'])): ?>
                                        <span class="text-nexus-blue font-bold text-sm"><?php echo $row['marks']; ?>XP</span>
                                    <?php else: ?>
                                        <span class="text-gray-500 text-[10px]">UNGRADED</span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="text-gray-700">--</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-2">
                                    <?php if ($is_submitted && $row['file_path']):
                                        // Simple file link
                                        $file_ext = pathinfo($row['file_path'], PATHINFO_EXTENSION);
                                        $st_code = preg_replace('/[^a-zA-Z0-9_-]/', '', $row['student_code']);
                                        $st_name = preg_replace('/[^a-zA-Z0-9_-]/', '_', $row['name']);
                                        $download_name = $st_code . '_' . $st_name . '_Submission.' . $file_ext;
                                        ?>
                                        <a href="../uploads/<?php echo $row['file_path']; ?>"
                                            download="<?php echo $download_name; ?>"
                                            class="text-nexus-blue hover:text-white transition-colors flex items-center gap-2">
                                            <i class="fa-solid fa-download"></i> File
                                        </a>
                                    <?php endif; ?>

                                    <?php if ($is_submitted && $row['github_link']): ?>
                                        <a href="<?php echo $row['github_link']; ?>" target="_blank"
                                            class="text-nexus-purple hover:text-white transition-colors flex items-center gap-2">
                                            <i class="fa-brands fa-github"></i> GitHub
                                        </a>
                                    <?php endif; ?>

                                    <?php if ($is_submitted && !$row['file_path'] && !$row['github_link']): ?>
                                        <span class="text-gray-600">Empty Payload</span>
                                    <?php endif; ?>

                                    <?php if (!$is_submitted): ?>
                                        <span class="text-gray-700">--</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <?php if ($is_submitted): ?>
                                    <button
                                        onclick="openGradeModal('<?php echo $row['submission_id']; ?>', '<?php echo htmlspecialchars($row['name']); ?>', '<?php echo $row['marks'] ?? ''; ?>', '<?php echo htmlspecialchars($row['feedback'] ?? '', ENT_QUOTES); ?>')"
                                        class="bg-nexus-blue/10 border border-nexus-blue text-nexus-blue hover:bg-nexus-blue hover:text-black px-3 py-1 rounded transition-colors uppercase text-[10px] font-bold">
                                        <?php echo isset($row['marks']) ? 'Edit XP' : 'Assign XP'; ?>
                                    </button>
                                <?php else: ?>
                                    <span class="text-gray-700 text-[10px] uppercase">N/A</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                            No operatives found in the system.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- GRADE MODAL -->
<div id="gradeModal" class="fixed inset-0 z-50 hidden bg-black/80 backdrop-blur-sm flex items-center justify-center">
    <div class="holo-card w-full max-w-sm p-6 rounded-xl relative border-nexus-green/30">
        <h3 class="text-xl font-header font-bold text-white mb-4 uppercase border-b border-gray-800 pb-2">
            Assessment Protocol
        </h3>
        <p class="text-xs text-gray-400 mb-4">Operative: <span id="gradeStudentName"
                class="text-white font-bold"></span>
        </p>

        <form method="POST">
            <input type="hidden" name="submission_id" id="gradeSubmissionId">

            <div class="mb-4">
                <label class="block text-xs text-gray-500 mb-1 uppercase">XP Allocation (0-100)</label>
                <input type="number" name="marks" id="gradeMarks" min="0" max="100" required
                    class="w-full bg-nexus-dark border border-gray-700 rounded p-2 text-white focus:border-nexus-green focus:outline-none font-mono text-sm">
            </div>

            <div class="mb-6">
                <label class="block text-xs text-gray-500 mb-1 uppercase">Debrief / Feedback</label>
                <textarea name="feedback" id="gradeFeedback"
                    class="w-full bg-nexus-dark border border-gray-700 rounded p-2 text-white focus:border-nexus-green focus:outline-none font-mono text-xs h-24 placeholder-gray-600"
                    placeholder="Enter mission feedback..."></textarea>
            </div>

            <div class="flex gap-4">
                <button type="submit" name="submit_grade"
                    class="flex-1 bg-nexus-green text-nexus-black font-bold py-2 rounded hover:bg-white transition-colors uppercase text-xs tracking-wider">
                    Confirm XP
                </button>
                <button type="button" onclick="closeGradeModal()"
                    class="flex-1 border border-gray-700 text-gray-500 font-bold py-2 rounded hover:text-white transition-colors uppercase text-xs tracking-wider text-center">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openGradeModal(subId, studentName, marks, feedback) {
        document.getElementById('gradeModal').classList.remove('hidden');
        document.getElementById('gradeSubmissionId').value = subId;
        document.getElementById('gradeStudentName').textContent = studentName;
        document.getElementById('gradeMarks').value = marks;
        document.getElementById('gradeFeedback').value = feedback;
    }

    function closeGradeModal() {
        document.getElementById('gradeModal').classList.add('hidden');
    }
</script>