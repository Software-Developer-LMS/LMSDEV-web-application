<?php
include '../includes/db_connection.php';

// Logic moved to actions/assignment_actions.php

// Fetch Assignments
$sql = "SELECT a.*, m.module_title FROM assignments a JOIN modules m ON a.module_id = m.id ORDER BY a.deadline ASC";
$result = $conn->query($sql);

// Fetch Modules
$modules = $conn->query("SELECT * FROM modules");

// Handle Edit Fetch
$edit_assign = null;
if (isset($_GET['edit_id'])) {
    $id = intval($_GET['edit_id']);
    $edit_assign = $conn->query("SELECT * FROM assignments WHERE id=$id")->fetch_assoc();
}
?>

<div class="flex flex-col gap-6">
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-header font-bold text-white uppercase tracking-widest"><span
                class="text-nexus-blue">Mission</span>_Directives</h2>
        <a href="?page=assignments&add_new=1"
            class="bg-nexus-blue/10 border border-nexus-blue text-nexus-blue px-4 py-2 rounded hover:bg-nexus-blue hover:text-nexus-black transition-colors font-bold uppercase text-xs tracking-wider">
            + New Assessment
        </a>
    </div>

    <?php if (isset($_GET['msg']) || isset($_GET['error'])): ?>
        <?php if (isset($_GET['msg'])): ?>
            <div class="p-4 border border-nexus-green/50 bg-nexus-green/10 text-nexus-green text-xs font-mono">
                > SUCCESS: <?php echo htmlspecialchars($_GET['msg']); ?>
            </div>
        <?php endif; ?>
        <?php if (isset($_GET['error'])): ?>
            <div class="p-4 border border-red-500/50 bg-red-500/10 text-red-500 text-xs font-mono">
                > ERROR: <?php echo htmlspecialchars($_GET['error']); ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php while ($row = $result->fetch_assoc()): ?>
            <?php
            $deadline = new DateTime($row['deadline']);
            $now = new DateTime();
            $interval = $now->diff($deadline);
            $is_overdue = $now > $deadline;
            ?>
            <div class="holo-card p-6 rounded-xl flex flex-col group relative overflow-hidden">
                <!-- Glitch decoration -->
                <div
                    class="glitch-hover h-full w-full absolute inset-0 pointer-events-none opacity-0 group-hover:opacity-10 transition-opacity bg-[url('https://media.giphy.com/media/oEI9uBYSzLpBK/giphy.gif')] bg-cover mix-blend-overlay">
                </div>

                <div class="flex justify-between items-start mb-4 relative z-10">
                    <span
                        class="bg-nexus-purple/20 text-nexus-purple text-[10px] px-2 py-1 rounded uppercase font-bold tracking-wider">
                        <?php echo $row['module_title']; ?>
                    </span>
                    <?php if ($is_overdue): ?>
                        <span class="text-red-500 text-xs font-bold"><i class="fa-solid fa-triangle-exclamation"></i>
                            EXPIRED</span>
                    <?php else: ?>
                        <span class="text-nexus-blue text-xs font-bold">
                            <?php echo $interval->days; ?>d remaining
                        </span>
                    <?php endif; ?>
                </div>

                <h3 class="text-lg font-bold text-white mb-2 relative z-10">
                    <?php echo $row['title']; ?>
                </h3>
                <p class="text-gray-400 text-xs mb-4 flex-1 relative z-10">
                    <?php echo substr($row['description'], 0, 80) . '...'; ?>
                </p>

                <div class="border-t border-gray-800 pt-3 mt-auto relative z-10">
                    <div class="flex justify-between items-center text-xs text-gray-500 mb-2">
                        <span><i class="fa-regular fa-clock mr-1"></i> Deadline</span>
                        <span class="<?php echo $is_overdue ? 'text-red-500' : 'text-nexus-green'; ?>">
                            <?php echo date('M d, H:i', strtotime($row['deadline'])); ?>
                        </span>
                    </div>
                    <div class="flex gap-2 w-full mt-2">
                        <a href="?page=submissions&assignment_id=<?php echo $row['id']; ?>"
                            class="flex-1 bg-nexus-green/10 hover:bg-nexus-green text-nexus-green hover:text-black border border-nexus-green/30 py-1 rounded transition-colors uppercase text-[10px] font-bold text-center flex items-center justify-center gap-1">
                            <i class="fa-solid fa-eye"></i> View Subs
                        </a>
                        <a href="?page=assignments&edit_id=<?php echo $row['id']; ?>"
                            class="flex-1 bg-gray-800 hover:bg-white text-gray-400 hover:text-black py-1 rounded transition-colors uppercase text-[10px] font-bold text-center">Modify</a>
                        <a href="actions/assignment_actions.php?delete_id=<?php echo $row['id']; ?>"
                            onclick="return confirm('Abort mission directive?')"
                            class="px-3 bg-red-500/10 text-red-500 hover:bg-red-500 hover:text-white rounded transition-colors flex items-center justify-center"><i
                                class="fa-solid fa-trash"></i></a>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<!-- Add/Edit Assignment Modal -->
<div id="addAssignModal"
    class="fixed inset-0 z-50 <?php echo (isset($_GET['add_new']) || isset($_GET['edit_id'])) ? '' : 'hidden'; ?> bg-black/80 backdrop-blur-sm flex items-center justify-center">
    <div class="holo-card w-full max-w-md p-6 rounded-xl relative border-nexus-blue/30">
        <h3 class="text-xl font-header font-bold text-white mb-6 uppercase border-b border-gray-800 pb-2">
            <?php echo $edit_assign ? 'Modify Assessment' : 'New Assessment'; ?>
        </h3>

        <form method="POST" class="space-y-4" action="actions/assignment_actions.php" enctype="multipart/form-data">
            <?php if ($edit_assign): ?>
                <input type="hidden" name="id" value="<?php echo $edit_assign['id']; ?>">
            <?php endif; ?>

            <div>
                <label class="block text-xs text-gray-500 mb-1 uppercase">Directive Title</label>
                <input type="text" name="title" required
                    value="<?php echo $edit_assign ? $edit_assign['title'] : ''; ?>"
                    class="w-full bg-nexus-dark border border-gray-700 rounded p-2 text-white focus:border-nexus-blue focus:outline-none font-mono text-xs">
            </div>

            <div>
                <label class="block text-xs text-gray-500 mb-1 uppercase">Target Module</label>
                <select name="module_id"
                    class="w-full bg-nexus-dark border border-gray-700 rounded p-2 text-white focus:border-nexus-blue focus:outline-none font-mono text-xs">
                    <?php while ($m = $modules->fetch_assoc()): ?>
                        <option value="<?php echo $m['id']; ?>" <?php echo ($edit_assign && $edit_assign['module_id'] == $m['id']) ? 'selected' : ''; ?>>
                            <?php echo $m['module_title']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div>
                <label class="block text-xs text-gray-500 mb-1 uppercase">Submission Deadline</label>
                <input type="datetime-local" name="deadline" required
                    value="<?php echo $edit_assign ? $edit_assign['deadline'] : ''; ?>"
                    class="w-full bg-nexus-dark border border-gray-700 rounded p-2 text-white focus:border-nexus-blue focus:outline-none font-mono text-xs">
            </div>

            <div>
                <label class="block text-xs text-gray-500 mb-1 uppercase">Detailed Instructions</label>
                <textarea name="description"
                    class="w-full bg-nexus-dark border border-gray-700 rounded p-2 text-white focus:border-nexus-blue focus:outline-none font-mono text-xs h-24"><?php echo $edit_assign ? $edit_assign['description'] : ''; ?></textarea>
            </div>

            <div>
                <label class="block text-xs text-gray-500 mb-1 uppercase">Reference Material (PDF - Optional)</label>
                <input type="file" name="assignment_file" accept=".pdf"
                    class="w-full bg-nexus-dark border border-gray-700 rounded p-2 text-white focus:border-nexus-blue focus:outline-none font-mono text-xs file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-nexus-blue/10 file:text-nexus-blue hover:file:bg-nexus-blue/20">
                <?php if ($edit_assign && $edit_assign['assignment_file']): ?>
                    <div class="mt-1 text-xs text-nexus-green">
                        Current file: <a href="../uploads/assignments/<?php echo $edit_assign['assignment_file']; ?>"
                            target="_blank"
                            class="underline hover:text-white"><?php echo $edit_assign['assignment_file']; ?></a>
                    </div>
                <?php endif; ?>
            </div>

            <div class="flex gap-4 mt-6">
                <button type="submit" name="<?php echo $edit_assign ? 'update_assignment' : 'add_assignment'; ?>"
                    class="flex-1 bg-nexus-blue text-nexus-black font-bold py-2 rounded hover:bg-white transition-colors uppercase text-xs tracking-wider">
                    <?php echo $edit_assign ? 'Update Directive' : 'Initialize'; ?>
                </button>
                <a href="?page=assignments"
                    class="flex-1 border border-gray-700 text-gray-500 font-bold py-2 rounded hover:text-white transition-colors uppercase text-xs tracking-wider text-center">Abort</a>
            </div>
        </form>
        <!-- Last line of the modal -->
    </div>
</div>