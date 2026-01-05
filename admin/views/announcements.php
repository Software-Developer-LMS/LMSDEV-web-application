<?php
include '../includes/db_connection.php';

// Logic moved to actions/announcement_actions.php

// Fetch Announcements
$result = $conn->query("SELECT * FROM announcements ORDER BY created_at DESC");

// Handle Edit Fetch
$edit_announce = null;
if (isset($_GET['edit_id'])) {
    $id = intval($_GET['edit_id']);
    $edit_announce = $conn->query("SELECT * FROM announcements WHERE id=$id")->fetch_assoc();
}
?>

<div class="flex flex-col gap-6">
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-header font-bold text-white uppercase tracking-widest"><span
                class="text-nexus-green">System</span>_Broadcasts</h2>
        <a href="?page=announcements&add_new=1"
            class="bg-nexus-green/10 border border-nexus-green text-nexus-green px-4 py-2 rounded hover:bg-nexus-green hover:text-nexus-black transition-colors font-bold uppercase text-xs tracking-wider">
            + New Transmission
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

                <div class="flex flex-col gap-2">
                    <a href="?page=announcements&edit_id=<?php echo $row['id']; ?>"
                        class="text-gray-600 hover:text-white self-center"><i class="fa-solid fa-pen"></i></a>
                    <a href="actions/announcement_actions.php?delete_id=<?php echo $row['id']; ?>"
                        onclick="return confirm('Cease broadcast?')"
                        class="text-gray-600 hover:text-nexus-red transition-colors self-center"><i
                            class="fa-solid fa-xmark"></i></a>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<!-- Add/Edit Announcement Modal -->
<div id="addAnnounceModal"
    class="fixed inset-0 z-50 <?php echo (isset($_GET['add_new']) || isset($_GET['edit_id'])) ? '' : 'hidden'; ?> bg-black/80 backdrop-blur-sm flex items-center justify-center">
    <div class="holo-card w-full max-w-lg p-6 rounded-xl relative border-nexus-green/30">
        <h3 class="text-xl font-header font-bold text-white mb-6 uppercase border-b border-gray-800 pb-2">
            <?php echo $edit_announce ? 'Modify Broadcast' : 'Inject System Message'; ?>
        </h3>

        <form method="POST" class="space-y-4" action="actions/announcement_actions.php">
            <?php if ($edit_announce): ?>
                <input type="hidden" name="id" value="<?php echo $edit_announce['id']; ?>">
            <?php endif; ?>

            <div class="flex gap-4">
                <div class="flex-1">
                    <label class="block text-xs text-gray-500 mb-1 uppercase">Header</label>
                    <input type="text" name="title" required
                        value="<?php echo $edit_announce ? $edit_announce['title'] : ''; ?>"
                        class="w-full bg-nexus-dark border border-gray-700 rounded p-2 text-white focus:border-nexus-green focus:outline-none font-mono text-xs">
                </div>
                <div class="w-1/3">
                    <label class="block text-xs text-gray-500 mb-1 uppercase">Category</label>
                    <select name="type"
                        class="w-full bg-nexus-dark border border-gray-700 rounded p-2 text-white focus:border-nexus-green focus:outline-none font-mono text-xs">
                        <?php
                        $types = ['notice' => 'General Notice', 'class_change' => 'Class Update', 'exam' => 'Examination', 'deadline' => 'Critical Deadline'];
                        foreach ($types as $val => $label):
                            ?>
                            <option value="<?php echo $val; ?>" <?php echo ($edit_announce && $edit_announce['type'] == $val) ? 'selected' : ''; ?>>
                                <?php echo $label; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs text-gray-500 mb-1 uppercase">Message Content</label>
                <textarea name="message" required
                    class="w-full bg-nexus-dark border border-gray-700 rounded p-2 text-white focus:border-nexus-green focus:outline-none font-mono text-xs h-32"><?php echo $edit_announce ? $edit_announce['message'] : ''; ?></textarea>
            </div>

            <div class="flex gap-4 mt-6">
                <button type="submit" name="<?php echo $edit_announce ? 'update_announcement' : 'add_announcement'; ?>"
                    class="flex-1 bg-nexus-green text-nexus-black font-bold py-2 rounded hover:bg-white transition-colors uppercase text-xs tracking-wider">
                    <?php echo $edit_announce ? 'Update Broadcast' : 'Broadcast'; ?>
                </button>
                <a href="?page=announcements"
                    class="flex-1 border border-gray-700 text-gray-500 font-bold py-2 rounded hover:text-white transition-colors uppercase text-xs tracking-wider text-center">Cancel</a>
            </div>
        </form>
        <!-- Last line of the modal -->
    </div>
</div>