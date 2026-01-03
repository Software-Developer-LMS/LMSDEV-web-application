<?php
$user_id = $_SESSION['user_id'];

// Fetch Assignments with Submission Status
$sql = "SELECT a.*, m.module_title, s.submitted_at, s.file_path, s.github_link, s.marks, s.feedback 
        FROM assignments a 
        JOIN modules m ON a.module_id = m.id 
        LEFT JOIN submissions s ON a.id = s.assignment_id AND s.user_id = $user_id
        ORDER BY a.deadline ASC";
$result = $conn->query($sql);
?>

<div class="animate-slide-in">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-nexus-blue">Mission Directives</h1>
        <div class="text-sm font-mono flex items-center gap-4">
            <span class="flex items-center text-nexus-green"><span
                    class="w-2 h-2 bg-nexus-green rounded-full mr-2"></span> SUBMITTED</span>
            <span class="flex items-center text-yellow-500"><span
                    class="w-2 h-2 bg-yellow-500 rounded-full mr-2"></span> LATE</span>
            <span class="flex items-center text-nexus-purple"><span
                    class="w-2 h-2 bg-nexus-purple rounded-full mr-2"></span> PENDING</span>
            <span class="flex items-center text-nexus-red"><span class="w-2 h-2 bg-nexus-red rounded-full mr-2"></span>
                OVERDUE</span>
        </div>
    </div>

    <!-- Success Message -->
    <?php if (isset($_GET['success'])): ?>
        <div
            class="bg-nexus-green/10 border border-nexus-green text-nexus-green p-4 rounded-xl mb-6 flex items-center animate-pulse">
            <i class="fas fa-check-circle text-xl mr-3"></i>
            <span class="font-mono font-bold">MISSION ACCOMPLISHED: Submission received successfully.</span>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <?php while ($row = $result->fetch_assoc()):
            $deadline = new DateTime($row['deadline']);
            $now = new DateTime();
            $is_overdue = $now > $deadline && !$row['submitted_at'];
            $is_submitted = !empty($row['submitted_at']);

            $status_color = 'nexus-purple'; // Pending
            $status_text = 'PENDING';

            if ($is_submitted) {
                // Check if late
                $submitted_at = new DateTime($row['submitted_at']);
                if ($submitted_at > $deadline) {
                    $status_color = 'yellow-500';
                    $status_text = 'LATE SUBMISSION';
                } else {
                    $status_color = 'nexus-green';
                    $status_text = 'COMPLETED';
                }
            } else if ($is_overdue) {
                $status_color = 'nexus-red';
                $status_text = 'FAILED/OVERDUE';
            }
            ?>
            <div
                class="bg-nexus-card rounded-xl border border-gray-800 hover:border-<?php echo $status_color; ?> transition group overflow-hidden flex flex-col">
                <div class="p-6 flex-1">
                    <div class="flex justify-between items-start mb-4">
                        <span
                            class="text-xs font-mono font-bold bg-<?php echo $status_color == 'yellow-500' ? 'yellow-500/20 text-yellow-500' : $status_color . '/20 text-' . $status_color; ?> px-2 py-1 rounded">
                            <?php echo $status_text; ?>
                        </span>
                        <span class="text-xs text-gray-500 font-mono">
                            DEADLINE:
                            <?php echo date('Y-m-d H:i', strtotime($row['deadline'])); ?>
                        </span>
                    </div>

                    <h3 class="text-xl font-bold text-white mb-2">
                        <?php echo $row['title']; ?>
                    </h3>
                    <div class="text-sm text-nexus-blue mb-4 font-mono">
                        <?php echo $row['module_title']; ?>
                    </div>
                    <p class="text-gray-400 text-sm mb-6 line-clamp-3">
                        <?php echo $row['description']; ?>
                    </p>

                    <?php if ($is_submitted): ?>
                        <div class="bg-gray-900/50 p-3 rounded border border-gray-800 text-xs font-mono mb-4 text-gray-300">
                            <div class="flex items-center gap-2 mb-1">
                                <i class="fab fa-github"></i>
                                <?php echo $row['github_link'] ? "<a href='{$row['github_link']}' target='_blank' class='text-nexus-blue hover:underline'>repo_link</a>" : "N/A"; ?>
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="fas fa-file-archive"></i>
                                <?php echo $row['file_path'] ? "{$row['file_path']}" : "N/A"; ?>
                            </div>
                            <div class="mt-2 text-nexus-green">Submitted:
                                <?php echo $row['submitted_at']; ?>
                            </div>
                        </div>

                        <?php if (isset($row['marks'])): ?>
                            <div class="mt-4 p-4 border border-nexus-blue/30 bg-nexus-blue/5 rounded-lg">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-xs text-nexus-blue uppercase font-bold tracking-widest">Mission
                                        Evaluation</span>
                                    <span class="text-2xl font-bold text-white"><?php echo $row['marks']; ?><span
                                            class="text-xs text-nexus-blue ml-1">XP</span></span>
                                </div>
                                <?php if ($row['feedback']): ?>
                                    <div class="text-xs text-gray-400 font-mono border-t border-gray-700 pt-2 mt-2">
                                        <span class="text-nexus-purple font-bold">DEBRIEF:</span>
                                        <?php echo nl2br(htmlspecialchars($row['feedback'])); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

                <div class="p-4 bg-nexus-black/50 border-t border-gray-800 flex justify-between items-center">
                    <div class="text-xs text-gray-500">
                        <?php if (!$is_submitted && !$is_overdue): ?>
                            <i class="fas fa-clock text-nexus-purple animate-pulse"></i> Time remaining:
                            <?php echo $now->diff($deadline)->days; ?> days
                        <?php endif; ?>
                    </div>

                    <button
                        onclick="openSubmissionModal(<?php echo $row['id']; ?>, '<?php echo addslashes($row['title']); ?>')"
                        class="px-4 py-2 rounded font-bold text-sm transition uppercase flex items-center gap-2
                    <?php echo $is_submitted ? 'bg-gray-800 text-gray-400 hover:text-white' : 'bg-nexus-blue text-black hover:bg-white'; ?>">
                        <?php echo $is_submitted ? '<i class="fas fa-edit"></i> Update' : '<i class="fas fa-upload"></i> Initialize'; ?>
                    </button>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<!-- Submission Modal -->
<div id="submissionModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
    <div class="absolute inset-0 bg-black/80 backdrop-blur-sm" onclick="closeSubmissionModal()"></div>
    <div
        class="bg-nexus-card rounded-xl w-full max-w-lg border border-nexus-blue/30 relative z-10 shadow-[0_0_30px_rgba(0,212,255,0.1)]">
        <div class="p-6 border-b border-gray-800 flex justify-between items-center bg-nexus-code">
            <h3 class="text-xl font-bold text-nexus-blue" id="modalTitle">Submit Assignment</h3>
            <button onclick="closeSubmissionModal()" class="text-gray-400 hover:text-white">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <form action="process_submission.php" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
            <input type="hidden" name="assignment_id" id="modalAssignId">

            <div>
                <label class="block text-xs font-mono text-gray-400 mb-2 uppercase">GitHub Repository Link</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fab fa-github text-gray-600"></i>
                    </div>
                    <input type="url" name="github_link" placeholder="https://github.com/username/repo"
                        class="w-full bg-nexus-black border border-gray-700 rounded p-3 pl-10 text-white focus:border-nexus-blue focus:outline-none font-mono text-sm">
                </div>
            </div>

            <div>
                <label class="block text-xs font-mono text-gray-400 mb-2 uppercase">Project Files (ZIP/PDF)</label>
                <div
                    class="relative border-2 border-dashed border-gray-700 rounded-lg p-6 text-center hover:border-nexus-green transition cursor-pointer group">
                    <input type="file" name="submission_file" id="submissionFile"
                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" onchange="updateFileName(this)">
                    <i
                        class="fas fa-cloud-upload-alt text-3xl text-gray-600 group-hover:text-nexus-green mb-2 transition"></i>
                    <p class="text-xs text-gray-400 group-hover:text-white transition" id="fileName">Drag & drop or
                        click to upload</p>
                    <p class="text-[10px] text-gray-600 mt-1">Max size: 100MB</p>
                </div>
            </div>

            <div class="pt-4 flex gap-4">
                <button type="submit"
                    class="flex-1 bg-gradient-to-r from-nexus-blue to-nexus-green text-black font-bold py-3 rounded-lg hover:opacity-90 transition shadow-[0_0_15px_rgba(0,212,255,0.3)] uppercase text-sm tracking-wider">
                    Confirm Transmission
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openSubmissionModal(id, title) {
        document.getElementById('modalAssignId').value = id;
        document.getElementById('modalTitle').innerText = 'Submit: ' + title;
        document.getElementById('submissionModal').classList.remove('hidden');
    }

    function closeSubmissionModal() {
        document.getElementById('submissionModal').classList.add('hidden');
    }

    function updateFileName(input) {
        if (input.files && input.files[0]) {
            document.getElementById('fileName').innerText = input.files[0].name;
            document.getElementById('fileName').classList.add('text-nexus-green');
        }
    }

    // Client-side validation: Ensure at least one input is provided
    document.querySelector('form[action="process_submission.php"]').addEventListener('submit', function (e) {
        const link = document.querySelector('input[name="github_link"]').value.trim();
        const file = document.getElementById('submissionFile').files.length;

        if (!link && file === 0) {
            e.preventDefault();
            alert("PROTOCOL ERROR: You must provide either a GitHub Link OR a Project File to transmit.");
        }
    });

