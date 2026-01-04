<?php
// Include DB Connection
include '../includes/db_connection.php';

// Logic moved to actions/student_actions.php


$sql = "SELECT u.id, u.student_id, u.name, u.email, u.mobile_number, u.profile_photo, u.status, u.created_at, 
               (COALESCE(SUM(s.marks), 0) + u.legacy_xp) as total_xp 
        FROM users u 
        LEFT JOIN submissions s ON u.id = s.user_id 
        WHERE u.role='student' 
        GROUP BY u.id, u.student_id, u.name, u.email, u.mobile_number, u.profile_photo, u.status, u.created_at, u.legacy_xp 
        ORDER BY u.created_at DESC";

$result = $conn->query($sql);

if (!$result) {
    $sql_fallback = "SELECT u.id, u.student_id, u.name, u.email, '' as mobile_number, u.profile_photo, u.status, u.created_at, 
               (COALESCE(SUM(s.marks), 0) + u.legacy_xp) as total_xp 
        FROM users u 
        LEFT JOIN submissions s ON u.id = s.user_id 
        WHERE u.role='student' 
        GROUP BY u.id, u.student_id, u.name, u.email, u.profile_photo, u.status, u.created_at, u.legacy_xp 
        ORDER BY u.created_at DESC";
    $result = $conn->query($sql_fallback);

    if ($result) {
        $db_update_needed = true;
    } else {
        echo "<div class='text-red-500 font-mono p-4 border border-red-500'>SQL Error: " . $conn->error . "</div>";
        // Do not die here to preserve layout if possible, but return early
        return;
    }
}

// Logic moved to actions/student_actions.php

// Logic moved to actions/student_actions.php

// Handle Edit Fetch
$edit_student = null;
if (isset($_GET['edit_id'])) {
    $id = intval($_GET['edit_id']);
    $edit_student = $conn->query("SELECT * FROM users WHERE id=$id")->fetch_assoc();
}
?>

<div class="flex flex-col gap-6">

    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-header font-bold text-white uppercase tracking-widest"><span
                class="text-nexus-blue">Student</span>_Management</h2>
        <a href="?page=students&add_new=1"
            class="bg-nexus-blue/10 border border-nexus-blue text-nexus-blue px-4 py-2 rounded hover:bg-nexus-blue hover:text-nexus-black transition-colors font-bold uppercase text-xs tracking-wider">
            + Add New Operative
        </a>
    </div>

    <?php if (isset($db_update_needed) && $db_update_needed): ?>
            <div
                class="p-4 border border-yellow-500/50 bg-yellow-500/10 text-yellow-500 text-xs font-mono mb-4 flex justify-between items-center">
                <span>> WARNING: Database schema update required for 'Mobile Number'. Feature disabled.</span>
                <a href="update_db_schema.php" target="_blank"
                    class="bg-yellow-500 text-black px-3 py-1 rounded font-bold hover:bg-white hover:text-black transition-colors">RUN
                    UPDATE NOW</a>
            </div>
    <?php endif; ?>

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

    <!-- Student List Table -->
    <div class="holo-card rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs font-mono">
                <thead class="bg-nexus-dark border-b border-gray-800 text-gray-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Profile</th>
                        <th class="px-6 py-4">ID_Tag</th>
                        <th class="px-6 py-4">Operative_Name</th>
                        <th class="px-6 py-4">Comms_Link (Email)</th>
                        <th class="px-6 py-4">Mobile_Signal</th>
                        <th class="px-6 py-4">XP_Level</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800 text-gray-400">
                    <?php if ($result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr class="hover:bg-white/5 transition-colors group">
                                        <td class="px-6 py-4">
                                            <div class="h-8 w-8 rounded-full border border-gray-600 overflow-hidden bg-gray-800">
                                                <?php if (!empty($row['profile_photo'])): ?>
                                                        <img src="../uploads/profile_photos/<?php echo $row['profile_photo']; ?>"
                                                            class="w-full h-full object-cover">
                                                <?php else: ?>
                                                        <div
                                                            class="w-full h-full flex items-center justify-center text-[10px] text-gray-500 font-bold">
                                                            <?php echo substr($row['name'], 0, 2); ?>
                                                        </div>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-nexus-blue font-bold">
                                            <?php echo $row['student_id']; ?>
                                        </td>
                                        <td class="px-6 py-4 group-hover:text-white">
                                            <?php echo $row['name']; ?>
                                        </td>
                                        <td class="px-6 py-4">
                                            <?php echo $row['email']; ?>
                                        </td>
                                        <td class="px-6 py-4 font-mono text-gray-500">
                                            <?php echo !empty($row['mobile_number']) ? $row['mobile_number'] : '<span class="text-gray-700">-</span>'; ?>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="text-nexus-blue font-bold font-mono">
                                                <?php echo $row['total_xp'] ? intval($row['total_xp']) : 0; ?> XP
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span
                                                class="px-2 py-1 rounded text-[10px] uppercase <?php echo $row['status'] == 'active' ? 'bg-nexus-green/10 text-nexus-green' : 'bg-red-500/10 text-red-500'; ?>">
                                                <?php echo $row['status']; ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 flex items-center gap-3">
                                            <!-- Edit -->
                                            <a href="?page=students&edit_id=<?php echo $row['id']; ?>"
                                                class="text-nexus-blue hover:text-white" title="Edit"><i
                                                    class="fa-solid fa-pen-to-square"></i></a>

                                            <!-- Toggle Status -->
                                            <a href="actions/student_actions.php?toggle_id=<?php echo $row['id']; ?>&status=<?php echo $row['status']; ?>"
                                                class="<?php echo $row['status'] == 'active' ? 'text-nexus-green hover:text-red-500' : 'text-gray-600 hover:text-nexus-green'; ?>"
                                                title="<?php echo $row['status'] == 'active' ? 'Deactivate' : 'Activate'; ?>">
                                                <i class="fa-solid <?php echo $row['status'] == 'active' ? 'fa-toggle-on' : 'fa-toggle-off'; ?> text-lg"></i>
                                            </a>

                                            <!-- Delete -->
                                            <a href="actions/student_actions.php?delete_id=<?php echo $row['id']; ?>"
                                                onclick="return confirm('Terminate this operative? This action cannot be undone.')"
                                                class="text-nexus-red hover:text-white" title="Delete"><i
                                                    class="fa-solid fa-trash"></i></a>
                                        </td>
                                    </tr>
                            <?php endwhile; ?>
                    <?php else: ?>
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-600">[NULL] No operatives found in
                                    database.</td>
                            </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Modal -->
<div id="studentModal"
    class="fixed inset-0 z-50 <?php echo (isset($_GET['add_new']) || isset($_GET['edit_id'])) ? '' : 'hidden'; ?> bg-black/80 backdrop-blur-sm flex items-center justify-center">
    <div class="holo-card w-full max-w-md p-6 rounded-xl relative">
        <h3 class="text-xl font-header font-bold text-white mb-6 uppercase border-b border-gray-800 pb-2">
            <?php echo $edit_student ? 'Update Operative Protocol' : 'Initialize New Operative'; ?>
        </h3>

        <form method="POST" class="space-y-4" action="actions/student_actions.php">
            <?php if ($edit_student): ?>
                    <input type="hidden" name="id" value="<?php echo $edit_student['id']; ?>">
            <?php endif; ?>

            <div>
                <label class="block text-xs text-gray-500 mb-1 uppercase">Student ID</label>
                <input type="text" name="student_id" required
                    value="<?php echo $edit_student ? $edit_student['student_id'] : ''; ?>"
                    class="w-full bg-nexus-dark border border-gray-700 rounded p-2 text-white focus:border-nexus-blue focus:outline-none font-mono text-xs"
                    placeholder="SD-00X">
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1 uppercase">Full Name</label>
                <input type="text" name="name" required
                    value="<?php echo $edit_student ? $edit_student['name'] : ''; ?>"
                    class="w-full bg-nexus-dark border border-gray-700 rounded p-2 text-white focus:border-nexus-blue focus:outline-none font-mono text-xs"
                    placeholder="John Doe">
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1 uppercase">Email Address</label>
                <input type="email" name="email" required
                    value="<?php echo $edit_student ? $edit_student['email'] : ''; ?>"
                    class="w-full bg-nexus-dark border border-gray-700 rounded p-2 text-white focus:border-nexus-blue focus:outline-none font-mono text-xs"
                    placeholder="operative@nexus.com">
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1 uppercase">Mobile Frequency (Number)</label>
                <input type="text" name="mobile_number"
                    value="<?php echo $edit_student ? ($edit_student['mobile_number'] ?? '') : ''; ?>"
                    class="w-full bg-nexus-dark border border-gray-700 rounded p-2 text-white focus:border-nexus-blue focus:outline-none font-mono text-xs"
                    placeholder="07X XXX XXXX">
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1 uppercase">Password
                    <?php echo $edit_student ? '(Leave blank to keep current)' : ''; ?></label>
                <input type="password" name="password" <?php echo $edit_student ? '' : 'required'; ?>
                    class="w-full bg-nexus-dark border border-gray-700 rounded p-2 text-white focus:border-nexus-blue focus:outline-none font-mono text-xs"
                    placeholder="******">
            </div>

            <div class="flex gap-4 mt-6">
                <button type="submit" name="<?php echo $edit_student ? 'update_student' : 'add_student'; ?>"
                    class="flex-1 bg-nexus-blue text-nexus-black font-bold py-2 rounded hover:bg-white transition-colors uppercase text-xs tracking-wider">
                    <?php echo $edit_student ? 'Update Entry' : 'Confirm Entry'; ?>
                </button>
                <a href="?page=students"
                    class="flex-1 border border-gray-700 text-gray-500 font-bold py-2 rounded hover:text-white transition-colors uppercase text-xs tracking-wider text-center">Abort</a>
            </div>
        </form>
        <!-- Last line of the modal -->
    </div>
</div>