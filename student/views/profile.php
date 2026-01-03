<?php
// Fetch User Details
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id = $user_id";
$result = $conn->query($sql);
$user = $result->fetch_assoc();

// Handle Profile Update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {

    // Photo Upload
    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['profile_photo']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (in_array($ext, $allowed)) {
            $new_name = "profile_" . $user_id . "_" . time() . "." . $ext;
            $destination = "../uploads/profile_photos/" . $new_name;

            if (move_uploaded_file($_FILES['profile_photo']['tmp_name'], $destination)) {

                // Delete old photo if exists AND upload was successful
                if (!empty($user['profile_photo'])) {
                    $old_photo_path = "../uploads/profile_photos/" . $user['profile_photo'];
                    if (file_exists($old_photo_path)) {
                        unlink($old_photo_path);
                    }
                }
                $conn->query("UPDATE users SET profile_photo = '$new_name' WHERE id = $user_id");
                // Refresh user data
                $user['profile_photo'] = $new_name;
                $success_msg = "Profile photo updated successfully.";
            } else {
                $error_msg = "Failed to upload photo.";
            }
        } else {
            $error_msg = "Invalid file format.";
        }
    }

    // Password Update
    if (!empty($_POST['new_password'])) {
        if ($_POST['new_password'] === $_POST['confirm_password']) {
            if (strlen($_POST['new_password']) >= 6) {
                $hash = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
                $conn->query("UPDATE users SET password = '$hash' WHERE id = $user_id");
                $success_msg = "Password updated successfully.";
            } else {
                $error_msg = "Password too short.";
            }
        } else {
            $error_msg = "Passwords do not match.";
        }
    }
}
?>

<div class="animate-slide-in">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-nexus-blue">Operative Profile</h1>
        <div class="text-sm text-gray-400 font-mono">ID:
            <?php echo $user['student_id']; ?>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Digital ID Card -->
        <div class="md:col-span-1">
            <div class="bg-nexus-card rounded-xl p-6 border border-nexus-blue/20 relative overflow-hidden group">
                <div class="absolute inset-0 bg-blue-500/5 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                <div class="relative z-10 flex flex-col items-center text-center">
                    <div
                        class="w-32 h-32 rounded-full border-2 border-nexus-blue p-1 mb-4 shadow-[0_0_20px_rgba(0,212,255,0.3)]">
                        <?php if (!empty($user['profile_photo'])): ?>
                            <img src="../uploads/profile_photos/<?php echo $user['profile_photo']; ?>"
                                class="w-full h-full rounded-full object-cover">
                        <?php else: ?>
                            <div
                                class="w-full h-full rounded-full bg-nexus-dark flex items-center justify-center text-nexus-blue text-4xl">
                                <i class="fas fa-user-astronaut"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    <h2 class="text-xl font-bold text-white">
                        <?php echo $user['name']; ?>
                    </h2>
                    <p class="text-nexus-blue font-mono text-sm mb-4">
                        <?php echo $user['email']; ?>
                    </p>
                    <div class="w-full bg-nexus-dark rounded-lg p-3 border border-gray-700">
                        <div class="flex justify-between text-xs mb-1">
                            <span class="text-gray-500">ROLE</span>
                            <span class="text-nexus-green font-bold uppercase">
                                <?php echo $user['role']; ?>
                            </span>
                        </div>
                        <div class="flex justify-between text-xs">
                            <span class="text-gray-500">STATUS</span>
                            <span class="text-nexus-blue font-bold uppercase">ACTIVE</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Form -->
        <div class="md:col-span-2">
            <div class="bg-nexus-card rounded-xl p-8 border border-gray-800">
                <h3 class="text-lg font-bold text-white mb-6 border-b border-gray-800 pb-2">Update Credentials</h3>

                <?php if (isset($success_msg)): ?>
                    <div
                        class="bg-nexus-green/10 text-nexus-green p-3 rounded mb-4 text-xs font-mono border border-nexus-green/30">
                        >
                        <?php echo $success_msg; ?>
                    </div>
                <?php endif; ?>
                <?php if (isset($error_msg)): ?>
                    <div class="bg-red-500/10 text-red-500 p-3 rounded mb-4 text-xs font-mono border border-red-500/30">
                        > ERROR:
                        <?php echo $error_msg; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data" class="space-y-6">
                    <div>
                        <label class="block text-xs text-gray-500 mb-2 uppercase font-bold">Update Profile Photo</label>
                        <div class="flex items-center gap-4">
                            <label
                                class="cursor-pointer bg-nexus-dark border border-gray-700 hover:border-nexus-blue text-gray-300 px-4 py-2 rounded transition-colors flex items-center gap-2 text-sm">
                                <i class="fas fa-upload"></i> Choose File
                                <input type="file" name="profile_photo" class="hidden" accept="image/*"
                                    onchange="document.getElementById('file-name').textContent = this.files[0].name">
                            </label>
                            <span id="file-name" class="text-xs text-gray-500 font-mono">No file chosen</span>
                        </div>
                    </div>

                    <div class="border-t border-gray-800 pt-6">
                        <label class="block text-xs text-gray-500 mb-4 uppercase font-bold">Change Password (Leave blank
                            to keep)</label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <input type="password" name="new_password" placeholder="New Password"
                                    class="w-full bg-nexus-dark border border-gray-700 rounded p-3 text-white focus:border-nexus-blue focus:outline-none transition-colors text-sm">
                            </div>
                            <div>
                                <input type="password" name="confirm_password" placeholder="Confirm Password"
                                    class="w-full bg-nexus-dark border border-gray-700 rounded p-3 text-white focus:border-nexus-blue focus:outline-none transition-colors text-sm">
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 flex justify-end">
                        <button type="submit" name="update_profile"
                            class="bg-nexus-blue text-black font-bold px-6 py-2 rounded hover:bg-white transition-colors shadow-[0_0_15px_rgba(0,212,255,0.2)]">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>