<?php
// Include DB Connection
include '../includes/db_connection.php';

// Handle Add Module
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_module'])) {
    $title = $_POST['module_title'];
    $desc = $_POST['description'];
    $order = $_POST['order_no'];

    $sql = "INSERT INTO modules (module_title, description, order_no) VALUES ('$title', '$desc', '$order')";
    if ($conn->query($sql) === TRUE) {
        $msg = "Module initialized.";
    } else {
        $error = "Error: " . $conn->error;
    }
}

// Fetch Modules
$result = $conn->query("SELECT * FROM modules ORDER BY order_no ASC");
?>

<div class="flex flex-col gap-6">
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-header font-bold text-white uppercase tracking-widest"><span
                class="text-nexus-purple">Module</span>_Structure</h2>
        <button onclick="document.getElementById('addModuleModal').classList.remove('hidden')"
            class="bg-nexus-purple/10 border border-nexus-purple text-nexus-purple px-4 py-2 rounded hover:bg-nexus-purple hover:text-white transition-colors font-bold uppercase text-xs tracking-wider">
            + Compile New Module
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="holo-card p-6 rounded-xl relative group hover:border-nexus-purple/50 transition-all">
                <div class="absolute top-4 right-4 text-xs font-mono text-gray-600">ID:
                    <?php echo str_pad($row['id'], 3, '0', STR_PAD_LEFT); ?>
                </div>
                <h3 class="text-lg font-bold text-white mb-2 group-hover:text-nexus-purple transition-colors">
                    <?php echo $row['module_title']; ?>
                </h3>
                <p class="text-gray-400 text-xs mb-4 min-h-[3em]">
                    <?php echo substr($row['description'], 0, 100) . '...'; ?>
                </p>

                <div class="flex justify-between items-center border-t border-gray-800 pt-4 mt-2">
                    <span class="text-[10px] uppercase text-gray-500 tracking-wider">Order:
                        <?php echo $row['order_no']; ?>
                    </span>
                    <div class="flex gap-2">
                        <button class="text-nexus-purple hover:text-white"><i class="fa-solid fa-pen"></i></button>
                        <button class="text-nexus-red hover:text-white"><i class="fa-solid fa-trash"></i></button>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<!-- Add Module Modal -->
<div id="addModuleModal"
    class="fixed inset-0 z-50 hidden bg-black/80 backdrop-blur-sm flex items-center justify-center">
    <div class="holo-card w-full max-w-md p-6 rounded-xl relative border-nexus-purple/30">
        <h3 class="text-xl font-header font-bold text-white mb-6 uppercase border-b border-gray-800 pb-2">Compile New
            Module</h3>

        <form method="POST" class="space-y-4">
            <div>
                <label class="block text-xs text-gray-500 mb-1 uppercase">Module Title</label>
                <input type="text" name="module_title" required
                    class="w-full bg-nexus-dark border border-gray-700 rounded p-2 text-white focus:border-nexus-purple focus:outline-none font-mono text-xs">
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1 uppercase">Description</label>
                <textarea name="description"
                    class="w-full bg-nexus-dark border border-gray-700 rounded p-2 text-white focus:border-nexus-purple focus:outline-none font-mono text-xs h-24"></textarea>
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1 uppercase">Order Sequence</label>
                <input type="number" name="order_no" value="0"
                    class="w-full bg-nexus-dark border border-gray-700 rounded p-2 text-white focus:border-nexus-purple focus:outline-none font-mono text-xs">
            </div>

            <div class="flex gap-4 mt-6">
                <button type="submit" name="add_module"
                    class="flex-1 bg-nexus-purple text-white font-bold py-2 rounded hover:bg-nexus-purple/80 transition-colors uppercase text-xs tracking-wider">Execute</button>
                <button type="button" onclick="document.getElementById('addModuleModal').classList.add('hidden')"
                    class="flex-1 border border-gray-700 text-gray-500 font-bold py-2 rounded hover:text-white transition-colors uppercase text-xs tracking-wider">Cancel</button>
            </div>
        </form>
    </div>
</div>