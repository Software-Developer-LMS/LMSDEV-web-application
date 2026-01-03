<?php
// Fetch All Lessons
$sql = "SELECT l.*, m.module_title FROM lessons l JOIN modules m ON l.module_id = m.id ORDER BY l.class_date DESC";
$result = $conn->query($sql);
?>

<div class="animate-slide-in">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-nexus-blue">Knowledge Protocols</h1>
        <div class="text-sm text-gray-400 font-mono">
            ACCESSING ARCHIVES...
        </div>
    </div>

    <div class="space-y-4">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()):
                $is_past = strtotime($row['class_date']) < time();
                ?>
                <div
                    class="bg-nexus-card rounded-xl p-6 glow-border group hover:bg-nexus-code transition relative overflow-hidden">
                    <!-- Status Strip -->
                    <div
                        class="absolute left-0 top-0 bottom-0 w-1 <?php echo $is_past ? 'bg-gray-600' : 'bg-nexus-green shadow-[0_0_10px_#00FF9D]'; ?>">
                    </div>

                    <div class="flex flex-col md:flex-row justify-between md:items-center gap-4 pl-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-1">
                                <span
                                    class="text-xs font-mono text-nexus-purple bg-nexus-purple/10 px-2 py-0.5 rounded border border-nexus-purple/30">
                                    <?php echo $row['module_title']; ?>
                                </span>
                                <?php if (!$is_past): ?>
                                    <span
                                        class="text-[10px] text-black font-bold bg-nexus-green px-2 py-0.5 rounded animate-pulse">UPCOMING</span>
                                <?php endif; ?>
                            </div>
                            <h3 class="text-xl font-bold text-white mb-2 group-hover:text-nexus-blue transition">
                                <?php echo $row['lesson_title']; ?>
                            </h3>
                            <p class="text-gray-400 text-sm mb-4 max-w-2xl">
                                <?php echo $row['description']; ?>
                            </p>

                            <div class="flex flex-wrap gap-4 text-xs font-mono text-gray-500">
                                <span class="flex items-center"><i class="far fa-calendar text-nexus-blue mr-2"></i>
                                    <?php echo date('F d, Y', strtotime($row['class_date'])); ?>
                                </span>
                                <span class="flex items-center"><i class="far fa-clock text-nexus-blue mr-2"></i>
                                    <?php echo substr($row['start_time'], 0, 5); ?> -
                                    <?php echo substr($row['end_time'], 0, 5); ?>
                                </span>
                                <span class="flex items-center"><i class="fas fa-map-marker-alt text-nexus-blue mr-2"></i>
                                    <?php echo $row['location']; ?>
                                </span>
                            </div>
                        </div>

                        <div class="flex flex-col gap-2 shrink-0">
                            <?php if (!empty($row['notes_file'])): ?>
                                <a href="../uploads/<?php echo $row['notes_file']; ?>" download
                                    class="bg-nexus-blue/10 text-nexus-blue border border-nexus-blue hover:bg-nexus-blue hover:text-black px-4 py-2 rounded font-bold text-sm transition flex items-center justify-center">
                                    <i class="fas fa-download mr-2"></i> DOWNLOAD NOTES
                                </a>
                            <?php else: ?>
                                <button disabled
                                    class="bg-gray-800 text-gray-600 border border-gray-700 px-4 py-2 rounded font-bold text-sm cursor-not-allowed flex items-center justify-center">
                                    <i class="fas fa-ban mr-2"></i> NO DATA
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="text-center py-12 text-gray-500 font-mono">
                <i class="fas fa-folder-open text-4xl mb-4 opacity-50"></i><br>
                NO PROTOCOLS FOUND
            </div>
        <?php endif; ?>
    </div>
</div>