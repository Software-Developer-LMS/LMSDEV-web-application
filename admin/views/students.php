<?php
// Include DB Connection
include '../includes/db_connection.php';

// Handle Add Student
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_student'])) {
    $student_id = $_POST['student_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (student_id, name, email, password, role) VALUES ('$student_id', '$name', '$email', '$password', 'student')";
    if ($conn->query($sql) === TRUE) {
        $success_msg = "Student added successfully.";
    } else {
        $error_msg = "Error: " . $conn->error;
    }
}

// Fetch Students
$sql = "SELECT * FROM users WHERE role='student' ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<div class="flex flex-col gap-6">

    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-header font-bold text-white uppercase tracking-widest"><span
                class="text-nexus-blue">Student</span>_Management</h2>
        <button onclick="document.getElementById('addStudentModal').classList.remove('hidden')"
            class="bg-nexus-blue/10 border border-nexus-blue text-nexus-blue px-4 py-2 rounded hover:bg-nexus-blue hover:text-nexus-black transition-colors font-bold uppercase text-xs tracking-wider">
            + Add New Operative
        </button>
    </div>

    <?php if (isset($success_msg)): ?>
        <div class="p-4 border border-nexus-green/50 bg-nexus-green/10 text-nexus-green text-xs font-mono">
            > SUCCESS:
            <?php echo $success_msg; ?>
        </div>
    <?php endif; ?>

    <!-- Student List Table -->
    <div class="holo-card rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs font-mono">
                <thead class="bg-nexus-dark border-b border-gray-800 text-gray-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-4">ID_Tag</th>
                        <th class="px-6 py-4">Operative_Name</th>
                        <th class="px-6 py-4">Comms_Link (Email)</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800 text-gray-400">
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr class="hover:bg-white/5 transition-colors group">
                                <td class="px-6 py-4 text-nexus-blue font-bold">
                                    <?php echo $row['student_id']; ?>
                                </td>
                                <td class="px-6 py-4 group-hover:text-white">
                                    <?php echo $row['name']; ?>
                                </td>
                                <td class="px-6 py-4">
                                    <?php echo $row['email']; ?>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="px-2 py-1 rounded text-[10px] uppercase <?php echo $row['status'] == 'active' ? 'bg-nexus-green/10 text-nexus-green' : 'bg-red-500/10 text-red-500'; ?>">
                                        <?php echo $row['status']; ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <button class="text-nexus-blue hover:text-white mr-2"><i
                                            class="fa-solid fa-pen-to-square"></i></button>
                                    <button class="text-nexus-red hover:text-white"><i class="fa-solid fa-trash"></i></button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-600">[NULL] No operatives found in
                                database.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Modal -->
<div id="addStudentModal"
    class="fixed inset-0 z-50 hidden bg-black/80 backdrop-blur-sm flex items-center justify-center">
    <div class="holo-card w-full max-w-md p-6 rounded-xl relative">
        <h3 class="text-xl font-header font-bold text-white mb-6 uppercase border-b border-gray-800 pb-2">Initialize New
            Operative</h3>

        <form method="POST" class="space-y-4">
            <div>
                <label class="block text-xs text-gray-500 mb-1 uppercase">Student ID</label>
                <input type="text" name="student_id" required
                    class="w-full bg-nexus-dark border border-gray-700 rounded p-2 text-white focus:border-nexus-blue focus:outline-none font-mono text-xs"
                    placeholder="SD-00X">
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1 uppercase">Full Name</label>
                <input type="text" name="name" required
                    class="w-full bg-nexus-dark border border-gray-700 rounded p-2 text-white focus:border-nexus-blue focus:outline-none font-mono text-xs"
                    placeholder="John Doe">
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1 uppercase">Email Address</label>
                <input type="email" name="email" required
                    class="w-full bg-nexus-dark border border-gray-700 rounded p-2 text-white focus:border-nexus-blue focus:outline-none font-mono text-xs"
                    placeholder="operative@nexus.com">
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1 uppercase">Temp Password</label>
                <input type="password" name="password" required
                    class="w-full bg-nexus-dark border border-gray-700 rounded p-2 text-white focus:border-nexus-blue focus:outline-none font-mono text-xs"
                    placeholder="******">
            </div>

            <div class="flex gap-4 mt-6">
                <button type="submit" name="add_student"
                    class="flex-1 bg-nexus-blue text-nexus-black font-bold py-2 rounded hover:bg-white transition-colors uppercase text-xs tracking-wider">Confirm
                    Entry</button>
                <button type="button" onclick="document.getElementById('addStudentModal').classList.add('hidden')"
                    class="flex-1 border border-gray-700 text-gray-500 font-bold py-2 rounded hover:text-white transition-colors uppercase text-xs tracking-wider">Abort</button>
            </div>
        </form>
    </div>
</div>