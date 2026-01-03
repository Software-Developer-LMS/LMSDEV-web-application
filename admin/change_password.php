<?php
session_start();
include '../includes/db_connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
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

        if ($new_password !== $confirm_password) {
            $error = "Passwords do not match.";
        } elseif (strlen($new_password) < 6) {
            $error = "Password must be at least 6 characters long.";
        } else {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $sql = "UPDATE users SET password = '$hashed_password', change_password = 0 WHERE id = $user_id";

            if ($conn->query($sql) === TRUE) {
                header("Location: index.php?msg=pw_updated");
                exit();
            } else {
                $error = "Error updating password: " . $conn->error;
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
    <title>Admin Protocol | Update Credentials</title>
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
            background: linear-gradient(to right, transparent, #ff3366, transparent);
            /* Red/Pink for Admin vibe */
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

    <div class="glass-panel p-8 rounded-2xl w-full max-w-md relative z-10 shadow-2xl border-t border-nexus-red/50">
        <div class="text-center mb-8">
            <i class="fa-solid fa-user-shield text-5xl text-red-500 mb-4 animate-pulse"></i>
            <h1 class="text-2xl font-bold uppercase tracking-widest text-white">Admin Protocol</h1>
            <p class="text-slate-400 font-mono text-xs mt-2">SECURE CREDENTIAL UPDATE</p>
        </div>

        <?php if ($error): ?>
            <div
                class="bg-red-500/10 border border-red-500/50 text-red-500 p-3 rounded mb-6 text-sm flex items-center gap-2 font-mono">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-6">
            <div>
                <label class="block text-slate-400 text-xs font-mono uppercase mb-1">New Passcode</label>
                <div class="relative group">
                    <span
                        class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-500 group-focus-within:text-red-500 transition-colors">
                        <i class="fa-solid fa-lock"></i>
                    </span>
                    <input type="password" name="new_password" required
                        class="w-full bg-slate-800 border border-slate-700 rounded-lg py-3 pl-10 text-white focus:border-red-500 focus:outline-none focus:ring-1 focus:ring-red-500 transition-all font-mono placeholder-slate-600"
                        placeholder="Enter secure admin password">
                </div>
            </div>

            <div>
                <label class="block text-slate-400 text-xs font-mono uppercase mb-1">Confirm Passcode</label>
                <div class="relative group">
                    <span
                        class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-500 group-focus-within:text-red-500 transition-colors">
                        <i class="fa-solid fa-lock"></i>
                    </span>
                    <input type="password" name="confirm_password" required
                        class="w-full bg-slate-800 border border-slate-700 rounded-lg py-3 pl-10 text-white focus:border-red-500 focus:outline-none focus:ring-1 focus:ring-red-500 transition-all font-mono placeholder-slate-600"
                        placeholder="Re-enter password">
                </div>
            </div>

            <button type="submit"
                class="w-full bg-red-600 text-white font-bold py-3 rounded-lg hover:bg-red-500 transition-all uppercase tracking-widest shadow-[0_0_20px_rgba(255,51,102,0.3)] hover:shadow-[0_0_30px_rgba(255,51,102,0.5)] flex items-center justify-center gap-2 group">
                <i class="fa-solid fa-key group-hover:rotate-12 transition-transform"></i>
                Update Admin Key
            </button>

            <div class="relative flex py-2 items-center">
                <div class="flex-grow border-t border-slate-700"></div>
                <span class="flex-shrink-0 mx-4 text-slate-500 text-xs font-mono">OR</span>
                <div class="flex-grow border-t border-slate-700"></div>
            </div>

            <button type="submit" name="keep_current" formnovalidate
                class="w-full bg-slate-800 border border-slate-700 text-slate-400 font-bold py-3 rounded-lg hover:bg-slate-700 hover:text-white transition-all uppercase tracking-widest flex items-center justify-center gap-2 text-xs">
                <i class="fa-solid fa-arrow-left"></i>
                Skip & Return to Dashboard
            </button>
        </form>
    </div>

</body>

</html>