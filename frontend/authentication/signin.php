<?php
session_start();
require_once __DIR__ . '/../../backend/auth/signin.php'; // original backend logic

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In | DairySync</title>
    <link href="/frontend/css/output.css" rel="stylesheet">
</head>
<body class="bg-gradient-to-br from-farm-green-50 via-farm-olive-50 to-farm-green-100 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full">
        <div class="bg-white/25 backdrop-blur-lg rounded-2xl shadow-2xl border border-white/30 p-8">
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-farm-green-700 to-farm-green-900 rounded-2xl shadow-lg mb-4">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7.5l-8-4.5-8 4.5v9l8 4.5 8-4.5v-9z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 12l8-4.5M12 12v9M12 12L4 7.5"></path>
                    </svg>
                </div>
                <h1 class="text-4xl font-extrabold text-farm-green-900 tracking-tight">DairySync</h1>
                <p class="text-farm-green-700 mt-2">Smart Farm Management System</p>
            </div>

            <?php if (!empty($message)): ?>
                <div class="mb-4 p-3 rounded-xl <?php echo $message_type === 'success' ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-red-100 text-red-800 border border-red-200'; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="" class="space-y-5">
                <div>
                    <label class="block text-farm-green-800 font-medium mb-1">Email Address</label>
                    <input type="text" name="login" required 
                           class="w-full px-4 py-3 bg-white/50 border border-white/40 rounded-xl focus:ring-2 focus:ring-farm-green-500 outline-none transition"
                           placeholder="farmer@example.com"
                           value="<?php echo htmlspecialchars($_POST['login'] ?? ''); ?>">
                </div>
                <div>
                    <label class="block text-farm-green-800 font-medium mb-1">Password</label>
                    <input type="password" name="password" required 
                           class="w-full px-4 py-3 bg-white/50 border border-white/40 rounded-xl focus:ring-2 focus:ring-farm-green-500 outline-none transition"
                           placeholder="••••••••">
                </div>
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                <button type="submit" 
                        class="w-full bg-gradient-to-r from-farm-green-700 to-farm-green-800 hover:from-farm-green-800 hover:to-farm-green-900 text-white font-bold py-3 rounded-xl shadow-lg transition-all">
                    Sign In
                </button>
            </form>

            <div class="mt-6 text-center">
                <p class="text-farm-green-800">
                    Don't have an account? 
                    <a href="/a7k9m2x4b8498447a8d9b490bd20e599d74c2a402563ed" class="font-semibold text-farm-green-700 hover:text-farm-green-900 underline decoration-2 underline-offset-2">Create one</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>