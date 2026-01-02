<?php
include '../includes/db_connection.php';

// Handle Add Announcement
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_announcement'])) {
    $title = $_POST['title'];
    $type = $_POST['type'];
    $msg = $_POST['message'];

    $sql = "INSERT INTO announcements (title, type, message) VALUES ('$title', '$type', '$msg')";

    if ($conn->query($sql) === TRUE) {
        $success = "Broadcast sent.";
    }
}

// Fetch Announcements
$result = $conn->query("SELECT * FROM announcements ORDER BY created_at DESC");
?>

<div class="flex flex-col gap-6">
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-header font-bold text-white uppercase tracking-widest"><span
                class="text-nexus-green">System</span>_Broadcasts</h2>
        <button onclick="document.getElementById('addAnnounceModal').classList.remove('hidden')"
            class="bg-nexus-green/10 border border-nexus-green text-nexus-green px-4 py-2 rounded hover:bg-nexus-green hover:text-nexus-black transition-colors font-bold uppercase text-xs tracking-wider">
            + New Transmission
        </button>
    </div>

    <!-- Feed -->
    <div class="space-y-4">
        <?php while ($row = $result->fetch_assoc()): ?>
            <?php
            // Color coding based on type
            $color = 'nexus-green'; // notice
            $icon = 'fa-circle-info';

            if ($row['type'] == 'exam') {
                $color = 'nexus-red';
                $icon = 'fa-file-signature';
            }
            if ($row['type'] == 'deadline') {
                $color = 'nexus-orange';
                $icon = 'fa-hourglass';
            } // Assuming default orange if styled, else purple
            if ($row['type'] == 'class_change') {
                $color = 'nexus-blue';
                $icon = 'fa-shuffle';
            }
            ?>

            <div class="holo-card p-5 rounded-xl group relative overflow-hidden flex gap-4">
                <!-- Type Indicator Strip -->
                <div class="w-1 bg-<?php echo $color; ?> shadow-[0_0_10px_currentcolor]"></div>

                <div class="flex-1">
                    <div class="flex justify-between items-start mb-2">
                        <div class="flex items-center gap-2">
                            <span class="text-<?php echo $color; ?> text-lg"><i
                                    class="fa-solid <?php echo $icon; ?>"></i></span>
                            <h3 class="text-lg font-bold text-white">
                                <?php echo $row['title']; ?>
                            </h3>
                        </div>
                        <span class="text-xs text-gray-600 font-mono">
                            <?php echo date('Y-m-d H:i', strtotime($row['created_at'])); ?>
                        </span>
                    </div>

                    <p class="text-gray-400 text-sm font-mono leading-relaxed pl-8">
                        <?php echo nl2br($row['message']); ?>
                    </p>
                </div>

                <button class="text-gray-600 hover:text-nexus-red transition-colors self-start"><i
                        class="fa-solid fa-xmark"></i></button>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<!-- Add Announcement Modal -->
<div id="addAnnounceModal"
    class="fixed inset-0 z-50 hidden bg-black/80 backdrop-blur-sm flex items-center justify-center">
    <div class="holo-card w-full max-w-lg p-6 rounded-xl relative border-nexus-green/30">
        <h3 class="text-xl font-header font-bold text-white mb-6 uppercase border-b border-gray-800 pb-2">Inject System
            Message</h3>

        <form method="POST" class="space-y-4">
            <div class="flex gap-4">
                <div class="flex-1">
                    <label class="block text-xs text-gray-500 mb-1 uppercase">Header</label>
                    <input type="text" name="title" required
                        class="w-full bg-nexus-dark border border-gray-700 rounded p-2 text-white focus:border-nexus-green focus:outline-none font-mono text-xs">
                </div>
                <div class="w-1/3">
                    <label class="block text-xs text-gray-500 mb-1 uppercase">Category</label>
                    <select name="type"
                        class="w-full bg-nexus-dark border border-gray-700 rounded p-2 text-white focus:border-nexus-green focus:outline-none font-mono text-xs">
                        <option value="notice">General Notice</option>
                        <option value="class_change">Class Update</option>
                        <option value="exam">Examination</option>
                        <option value="deadline">Critical Deadline</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs text-gray-500 mb-1 uppercase">Message Content</label>
                <textarea name="message" required
                    class="w-full bg-nexus-dark border border-gray-700 rounded p-2 text-white focus:border-nexus-green focus:outline-none font-mono text-xs h-32"></textarea>
            </div>

            <div class="flex gap-4 mt-6">
                <button type="submit" name="add_announcement"
                    class="flex-1 bg-nexus-green text-nexus-black font-bold py-2 rounded hover:bg-white transition-colors uppercase text-xs tracking-wider">Broadcast</button>
                <button type="button" onclick="document.getElementById('addAnnounceModal').classList.add('hidden')"
                    class="flex-1 border border-gray-700 text-gray-500 font-bold py-2 rounded hover:text-white transition-colors uppercase text-xs tracking-wider">Cancel</button>
            </div>
        </form>
    </div>
</div>