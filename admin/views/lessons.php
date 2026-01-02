<?php
include '../includes/db_connection.php';

// Handle Add Lesson
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_lesson'])) {
    $module_id = $_POST['module_id'];
    $title = $_POST['lesson_title'];
    $date = $_POST['class_date'];
    $start = $_POST['start_time'];
    $end = $_POST['end_time'];
    $loc = $_POST['location'];
    $desc = $_POST['description'];

    // File upload logic would go here

    $sql = "INSERT INTO lessons (module_id, lesson_title, class_date, start_time, end_time, location, description) 
            VALUES ('$module_id', '$title', '$date', '$start', '$end', '$loc', '$desc')";

    if ($conn->query($sql) === TRUE) {
        $msg = "Lesson scheduled.";
    }
}

// Fetch Lessons joined with Modules
$sql = "SELECT l.*, m.module_title FROM lessons l JOIN modules m ON l.module_id = m.id ORDER BY l.class_date DESC";
$result = $conn->query($sql);

// Fetch Modules for Dropdown
$modules = $conn->query("SELECT * FROM modules");
?>

<div class="flex flex-col gap-6">
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-header font-bold text-white uppercase tracking-widest"><span
                class="text-nexus-red">Lesson</span>_Protocols</h2>
        <button onclick="document.getElementById('addLessonModal').classList.remove('hidden')"
            class="bg-nexus-red/10 border border-nexus-red text-nexus-red px-4 py-2 rounded hover:bg-nexus-red hover:text-white transition-colors font-bold uppercase text-xs tracking-wider">
            + Schedule New Class
        </button>
    </div>

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
                        </td>
                        <td class="px-6 py-4 text-nexus-purple">
                            <?php echo $row['module_title']; ?>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-nexus-blue">
                                <?php echo $row['class_date']; ?>
                            </div>
                            <div class="text-[10px] text-gray-500">
                                <?php echo substr($row['start_time'], 0, 5) . ' - ' . substr($row['end_time'], 0, 5); ?>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-500"><i class="fa-solid fa-map-pin mr-1"></i>
                            <?php echo $row['location']; ?>
                        </td>
                        <td class="px-6 py-4">
                            <button class="text-nexus-red hover:text-white"><i class="fa-solid fa-trash"></i></button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Lesson Modal -->
<div id="addLessonModal"
    class="fixed inset-0 z-50 hidden bg-black/80 backdrop-blur-sm flex items-center justify-center">
    <div class="holo-card w-full max-w-2xl p-6 rounded-xl relative border-nexus-red/30">
        <h3 class="text-xl font-header font-bold text-white mb-6 uppercase border-b border-gray-800 pb-2">Initialize New
            Session</h3>

        <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="col-span-2">
                <label class="block text-xs text-gray-500 mb-1 uppercase">Lesson Title</label>
                <input type="text" name="lesson_title" required
                    class="w-full bg-nexus-dark border border-gray-700 rounded p-2 text-white focus:border-nexus-red focus:outline-none font-mono text-xs">
            </div>

            <div>
                <label class="block text-xs text-gray-500 mb-1 uppercase">Module</label>
                <select name="module_id"
                    class="w-full bg-nexus-dark border border-gray-700 rounded p-2 text-white focus:border-nexus-red focus:outline-none font-mono text-xs">
                    <?php while ($m = $modules->fetch_assoc()): ?>
                        <option value="<?php echo $m['id']; ?>">
                            <?php echo $m['module_title']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div>
                <label class="block text-xs text-gray-500 mb-1 uppercase">Date</label>
                <input type="date" name="class_date" required
                    class="w-full bg-nexus-dark border border-gray-700 rounded p-2 text-white focus:border-nexus-red focus:outline-none font-mono text-xs">
            </div>

            <div>
                <label class="block text-xs text-gray-500 mb-1 uppercase">Start Time</label>
                <input type="time" name="start_time" required
                    class="w-full bg-nexus-dark border border-gray-700 rounded p-2 text-white focus:border-nexus-red focus:outline-none font-mono text-xs">
            </div>

            <div>
                <label class="block text-xs text-gray-500 mb-1 uppercase">End Time</label>
                <input type="time" name="end_time" required
                    class="w-full bg-nexus-dark border border-gray-700 rounded p-2 text-white focus:border-nexus-red focus:outline-none font-mono text-xs">
            </div>

            <div class="col-span-2">
                <label class="block text-xs text-gray-500 mb-1 uppercase">Location</label>
                <input type="text" name="location" value="Main Hall"
                    class="w-full bg-nexus-dark border border-gray-700 rounded p-2 text-white focus:border-nexus-red focus:outline-none font-mono text-xs">
            </div>

            <div class="col-span-2">
                <label class="block text-xs text-gray-500 mb-1 uppercase">Description</label>
                <textarea name="description"
                    class="w-full bg-nexus-dark border border-gray-700 rounded p-2 text-white focus:border-nexus-red focus:outline-none font-mono text-xs h-20"></textarea>
            </div>

            <div class="col-span-2 flex gap-4 mt-2">
                <button type="submit" name="add_lesson"
                    class="flex-1 bg-nexus-red text-white font-bold py-2 rounded hover:bg-nexus-red/80 transition-colors uppercase text-xs tracking-wider">Confirm
                    Schedule</button>
                <button type="button" onclick="document.getElementById('addLessonModal').classList.add('hidden')"
                    class="flex-1 border border-gray-700 text-gray-500 font-bold py-2 rounded hover:text-white transition-colors uppercase text-xs tracking-wider">Cancel</button>
            </div>
        </form>
    </div>
</div>