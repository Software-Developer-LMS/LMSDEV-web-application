<?php
session_start();
include '../includes/db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];

    // Handle "Keep Current" action
    if (isset($_POST['keep_current'])) {
        $sql = "UPDATE users SET change_password = 0 WHERE id = $user_id";
        if ($conn->query($sql) === TRUE) {
            header("Location: index.php");
            exit();
        } else {
            $error = "System Error: " . $conn->error;
        }
    } else {
        // Handle "Update Password" action
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        // Photo validations
        $photo_path = null;
        if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $filename = $_FILES['profile_photo']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

            if (!in_array($ext, $allowed)) {
                $error = "Invalid image format. Allowed: JPG, PNG, GIF.";
            } else {
                $new_name = "profile_" . $user_id . "_" . time() . "." . $ext;
                $destination = "../uploads/profile_photos/" . $new_name;
                // Create dir if missing
                if (!is_dir('../uploads/profile_photos'))
                    mkdir('../uploads/profile_photos', 0777, true);

                if (move_uploaded_file($_FILES['profile_photo']['tmp_name'], $destination)) {
                    $photo_path = $new_name;
                }
            }
        }

        if ($new_password !== $confirm_password) {
            $error = "Passwords do not match.";
        } elseif (strlen($new_password) < 6) {
            $error = "Password must be at least 6 characters long.";
        } elseif (!$error) { // Only proceed if no upload error
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            $sql = "UPDATE users SET password = '$hashed_password', change_password = 0";
            if ($photo_path) {
                $sql .= ", profile_photo = '$photo_path'";
            }
            $sql .= " WHERE id = $user_id";

            if ($conn->query($sql) === TRUE) {
                header("Location: index.php?msg=pw_updated");
                exit();
            } else {
                $error = "Error updating database: " . $conn->error;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Login | Update Credentials</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;700&family=Inter:wght@400;600;700&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #0f172a;
        }

        .font-mono {
            font-family: 'JetBrains Mono', monospace;
        }

        .glass-panel {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .scan-line {
            width: 100%;
            height: 2px;
            background: linear-gradient(to right, transparent, #38bdf8, transparent);
            animation: scan 3s linear infinite;
        }

        @keyframes scan {
            0% {
                transform: translateY(-100%);
                opacity: 0;
            }

            50% {
                opacity: 1;
            }

            100% {
                transform: translateY(100vh);
                opacity: 0;
            }
        }
    </style>
</head>

<body class="h-screen flex items-center justify-center p-4 relative overflow-hidden bg-slate-900">

    <div class="scan-line absolute inset-0 pointer-events-none"></div>

    <div class="glass-panel p-8 rounded-2xl w-full max-w-md relative z-10 shadow-2xl">
        <div class="text-center mb-8">
            <i class="fa-solid fa-shield-halved text-5xl text-sky-400 mb-4 animate-pulse"></i>
            <h1 class="text-2xl font-bold uppercase tracking-widest text-white">Security Protocol</h1>
            <p class="text-slate-400 font-mono text-xs mt-2">MANDATORY CREDENTIAL CHECKPOINT</p>
        </div>

        <?php if ($error): ?>
            <div
                class="bg-red-500/10 border border-red-500/50 text-red-500 p-3 rounded mb-6 text-sm flex items-center gap-2 font-mono">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-6" enctype="multipart/form-data">
            <!-- Profile Photo Upload -->
            <div>
                <label class="block text-slate-400 text-xs font-mono uppercase mb-1">Profile Photo (Optional)</label>
                <div class="relative group">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-500 group-focus-within:text-sky-400 transition-colors">
                        <i class="fa-solid fa-camera"></i>
                    </span>
                    <input type="file" name="profile_photo" accept="image/*"
                        class="w-full bg-slate-800 border border-slate-700 rounded-lg py-2.5 pl-10 text-white focus:border-sky-500 focus:outline-none file:mr-4 file:py-1 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-sky-600 file:text-white hover:file:bg-sky-500 font-mono text-sm cursor-pointer">
                </div>
            </div>

            <div>
                <label class="block text-slate-400 text-xs font-mono uppercase mb-1">New Passcode</label>
                <div class="relative group">
                    <span
                        class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-500 group-focus-within:text-sky-400 transition-colors">
                        <i class="fa-solid fa-lock"></i>
                    </span>
                    <input type="password" name="new_password"
                        class="w-full bg-slate-800 border border-slate-700 rounded-lg py-3 pl-10 text-white focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500 transition-all font-mono placeholder-slate-600"
                        placeholder="Enter new 6+ char password">
                </div>
            </div>

            <div>
                <label class="block text-slate-400 text-xs font-mono uppercase mb-1">Confirm Passcode</label>
                <div class="relative group">
                    <span
                        class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-500 group-focus-within:text-sky-400 transition-colors">
                        <i class="fa-solid fa-lock"></i>
                    </span>
                    <input type="password" name="confirm_password"
                        class="w-full bg-slate-800 border border-slate-700 rounded-lg py-3 pl-10 text-white focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500 transition-all font-mono placeholder-slate-600"
                        placeholder="Re-enter password">
                </div>
            </div>

            <button type="submit"
                class="w-full bg-sky-600 text-white font-bold py-3 rounded-lg hover:bg-sky-500 transition-all uppercase tracking-widest shadow-[0_0_20px_rgba(14,165,233,0.3)] hover:shadow-[0_0_30px_rgba(14,165,233,0.5)] flex items-center justify-center gap-2 group">
                <i class="fa-solid fa-key group-hover:rotate-12 transition-transform"></i>
                Update Credentials
            </button>

            <div class="relative flex py-2 items-center">
                <div class="flex-grow border-t border-slate-700"></div>
                <span class="flex-shrink-0 mx-4 text-slate-500 text-xs font-mono">OR</span>
                <div class="flex-grow border-t border-slate-700"></div>
            </div>

            <button type="submit" name="keep_current" formnovalidate
                class="w-full bg-slate-800 border border-slate-700 text-slate-400 font-bold py-3 rounded-lg hover:bg-slate-700 hover:text-white transition-all uppercase tracking-widest flex items-center justify-center gap-2 text-xs">
                <i class="fa-solid fa-arrow-right"></i>
                Keep Current Password
            </button>
        </form>
    </div>

</body>

</html>