<?php
include '../includes/db_connection.php';

// Logic moved to actions/lesson_actions.php

// Fetch Lessons joined with Modules
$sql = "SELECT l.*, m.module_title FROM lessons l JOIN modules m ON l.module_id = m.id ORDER BY l.class_date DESC";
$result = $conn->query($sql);

// Fetch Modules for Dropdown
$modules = $conn->query("SELECT * FROM modules");

// Handle Edit Fetch
$edit_lesson = null;
if (isset($_GET['edit_id'])) {
    $id = intval($_GET['edit_id']);
    $edit_lesson = $conn->query("SELECT * FROM lessons WHERE id=$id")->fetch_assoc();
}
?>

<div class="flex flex-col gap-6">
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-header font-bold text-white uppercase tracking-widest"><span
                class="text-nexus-red">Lesson</span>_Protocols</h2>
        <a href="?page=lessons&add_new=1"
            class="bg-nexus-red/10 border border-nexus-red text-nexus-red px-4 py-2 rounded hover:bg-nexus-red hover:text-white transition-colors font-bold uppercase text-xs tracking-wider">
            + Schedule New Class
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

    <div class="holo-card rounded-xl overflow-hidden">
        <table class="w-full text-left text-xs font-mono">
            <thead class="bg-nexus-dark border-b border-gray-800 text-gray-500 uppercase">
                <tr>
                    <th class="px-6 py-4">Title</th>
                    <th class="px-6 py-4">Module</th>
                    <th class="px-6 py-4">Date & Time</th>
                    <th class="px-6 py-4">Location</th>
                    <th class="px-6 py-4">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-800 text-gray-400">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr class="hover:bg-white/5 transition-colors">
                        <td class="px-6 py-4 font-bold text-white">
                            <?php echo $row['lesson_title']; ?>
                            <?php if ($row['notes_file']): ?>
                                <div class="mt-1 text-[10px] text-nexus-green"><i class="fa-solid fa-file-pdf"></i> PDF Linked
                                </div>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 text-nexus-purple"><?php echo $row['module_title']; ?></td>
                        <td class="px-6 py-4">
                            <div class="text-nexus-blue"><?php echo $row['class_date']; ?></div>
                            <div class="text-[10px] text-gray-500">
                                <?php echo substr($row['start_time'], 0, 5) . ' - ' . substr($row['end_time'], 0, 5); ?>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-500"><i class="fa-solid fa-map-pin mr-1"></i>
                            <?php echo $row['location']; ?></td>
                        <td class="px-6 py-4">
                            <a href="?page=lessons&edit_id=<?php echo $row['id']; ?>"
                                class="text-nexus-red hover:text-white mr-2"><i class="fa-solid fa-pen-to-square"></i></a>
                            <a href="actions/lesson_actions.php?delete_id=<?php echo $row['id']; ?>"
                                onclick="return confirm('Cancel this protocol?')" class="text-nexus-red hover:text-white"><i
                                    class="fa-solid fa-trash"></i></a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add/Edit Lesson Modal -->
<div id="addLessonModal"
    class="fixed inset-0 z-50 <?php echo (isset($_GET['add_new']) || isset($_GET['edit_id'])) ? '' : 'hidden'; ?> bg-black/80 backdrop-blur-sm flex items-center justify-center">
    <div class="holo-card w-full max-w-2xl p-6 rounded-xl relative border-nexus-red/30">
        <h3 class="text-xl font-header font-bold text-white mb-6 uppercase border-b border-gray-800 pb-2">
            <?php echo $edit_lesson ? 'Modify Session Protocol' : 'Initialize New Session'; ?>
        </h3>

        <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4" action="actions/lesson_actions.php"
            enctype="multipart/form-data">
            <?php if ($edit_lesson): ?>
                <input type="hidden" name="id" value="<?php echo $edit_lesson['id']; ?>">
            <?php endif; ?>

            <div class="col-span-2">
                <label class="block text-xs text-gray-500 mb-1 uppercase">Lesson Title</label>
                <input type="text" name="lesson_title" required
                    value="<?php echo $edit_lesson ? $edit_lesson['lesson_title'] : ''; ?>"
                    class="w-full bg-nexus-dark border border-gray-700 rounded p-2 text-white focus:border-nexus-red focus:outline-none font-mono text-xs">
            </div>

            <div>
                <label class="block text-xs text-gray-500 mb-1 uppercase">Module</label>
                <select name="module_id"
                    class="w-full bg-nexus-dark border border-gray-700 rounded p-2 text-white focus:border-nexus-red focus:outline-none font-mono text-xs">
                    <?php while ($m = $modules->fetch_assoc()): ?>
                        <option value="<?php echo $m['id']; ?>" <?php echo ($edit_lesson && $edit_lesson['module_id'] == $m['id']) ? 'selected' : ''; ?>>
                            <?php echo $m['module_title']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div>
                <label class="block text-xs text-gray-500 mb-1 uppercase">Date</label>
                <input type="date" name="class_date" required
                    value="<?php echo $edit_lesson ? $edit_lesson['class_date'] : ''; ?>"
                    class="w-full bg-nexus-dark border border-gray-700 rounded p-2 text-white focus:border-nexus-red focus:outline-none font-mono text-xs">
            </div>

            <div>
                <label class="block text-xs text-gray-500 mb-1 uppercase">Start Time</label>
                <input type="time" name="start_time" required
                    value="<?php echo $edit_lesson ? $edit_lesson['start_time'] : ''; ?>"
                    class="w-full bg-nexus-dark border border-gray-700 rounded p-2 text-white focus:border-nexus-red focus:outline-none font-mono text-xs">
            </div>

            <div>
                <label class="block text-xs text-gray-500 mb-1 uppercase">End Time</label>
                <input type="time" name="end_time" required
                    value="<?php echo $edit_lesson ? $edit_lesson['end_time'] : ''; ?>"
                    class="w-full bg-nexus-dark border border-gray-700 rounded p-2 text-white focus:border-nexus-red focus:outline-none font-mono text-xs">
            </div>

            <div class="col-span-2">
                <label class="block text-xs text-gray-500 mb-1 uppercase">Location</label>
                <input type="text" name="location"
                    value="<?php echo $edit_lesson ? $edit_lesson['location'] : 'Main Hall'; ?>"
                    class="w-full bg-nexus-dark border border-gray-700 rounded p-2 text-white focus:border-nexus-red focus:outline-none font-mono text-xs">
            </div>

            <div class="col-span-2">
                <label class="block text-xs text-gray-500 mb-1 uppercase">Lesson Notes (PDF)</label>
                <input type="file" name="notes_file" accept=".pdf"
                    class="w-full bg-nexus-dark border border-gray-700 rounded p-2 text-gray-400 file:mr-4 file:py-1 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-nexus-red file:text-white hover:file:bg-nexus-red/80">
                <?php if ($edit_lesson && $edit_lesson['notes_file']): ?>
                    <div class="mt-1 text-[10px] text-nexus-green">Current: <?php echo $edit_lesson['notes_file']; ?></div>
                <?php endif; ?>
            </div>

            <div class="col-span-2">
                <label class="block text-xs text-gray-500 mb-1 uppercase">Description</label>
                <textarea name="description"
                    class="w-full bg-nexus-dark border border-gray-700 rounded p-2 text-white focus:border-nexus-red focus:outline-none font-mono text-xs h-20"><?php echo $edit_lesson ? $edit_lesson['description'] : ''; ?></textarea>
            </div>

            <div class="col-span-2 flex gap-4 mt-2">
                <button type="submit" name="<?php echo $edit_lesson ? 'update_lesson' : 'add_lesson'; ?>"
                    class="flex-1 bg-nexus-red text-white font-bold py-2 rounded hover:bg-nexus-red/80 transition-colors uppercase text-xs tracking-wider">
                    <?php echo $edit_lesson ? 'Update Schedule' : 'Confirm Schedule'; ?>
                </button>
                <a href="?page=lessons"
                    class="flex-1 border border-gray-700 text-gray-500 font-bold py-2 rounded hover:text-white transition-colors uppercase text-xs tracking-wider text-center">Cancel</a>
            </div>
        </form>
        <!-- Last line of the modal -->
    </div>
</div>