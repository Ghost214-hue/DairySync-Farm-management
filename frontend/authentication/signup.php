<?php 
// Include backend logic - handles POST request and sets $message, $message_type
require_once __DIR__ . '/../../backend/auth/signup.php';
session_start();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up | MooManager</title>
    <link href="/farm-management/frontend/css/output.css" rel="stylesheet">
</head>
<body class="bg-gradient-to-br from-farm-green-50 via-farm-olive-50 to-farm-green-100 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-5xl w-full">
        <!-- Glassmorphic Card -->
        <div class="bg-white/25 backdrop-blur-lg rounded-2xl shadow-2xl border border-white/30 overflow-hidden">
            <div class="text-center py-6 border-b border-white/30">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-farm-green-700 to-farm-green-900 rounded-2xl shadow-lg mb-2">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7.5l-8-4.5-8 4.5v9l8 4.5 8-4.5v-9z"></path>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-farm-green-900">DairySync</h1>
                <p class="text-farm-green-700">Register your account and farm to get started</p>
            </div>

            <!-- Display messages -->
            <?php if (isset($message)): ?>
                <div class="mx-6 mt-4 p-3 rounded-xl <?php echo $message_type === 'success' ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-red-100 text-red-800 border border-red-200'; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <!-- Registration Form - submits to same page -->
            <form method="POST" action="" class="p-6 md:p-8">
                <div class="grid md:grid-cols-2 gap-6">
                    <!-- Left: User Information -->
                    <div class="space-y-4">
                        <h2 class="text-xl font-bold text-farm-green-900 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                            Account Information
                        </h2>
                        <div>
                            <label class="block text-farm-green-800 font-medium mb-1">Username *</label>
                            <input type="text" name="username" required 
                                   value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
                                   class="w-full px-4 py-2.5 bg-white/50 border border-white/40 rounded-xl focus:ring-2 focus:ring-farm-green-500 outline-none transition">
                        </div>
                        <div>
                            <label class="block text-farm-green-800 font-medium mb-1">Email Address *</label>
                            <input type="email" name="email" required 
                                   value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                                   class="w-full px-4 py-2.5 bg-white/50 border border-white/40 rounded-xl focus:ring-2 focus:ring-farm-green-500 outline-none transition">
                        </div>
                        <div>
                            <label class="block text-farm-green-800 font-medium mb-1">Password *</label>
                            <input type="password" name="password" required 
                                   class="w-full px-4 py-2.5 bg-white/50 border border-white/40 rounded-xl focus:ring-2 focus:ring-farm-green-500 outline-none transition">
                            <p class="text-xs text-farm-green-600 mt-1">Minimum 8 characters</p>
                        </div>
                        <div>
                            <label class="block text-farm-green-800 font-medium mb-1">Phone Number</label>
                            <input type="tel" name="phone" 
                                   value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>"
                                   class="w-full px-4 py-2.5 bg-white/50 border border-white/40 rounded-xl focus:ring-2 focus:ring-farm-green-500 outline-none transition">
                        </div>
                    </div>

                    <!-- Right: Farm Information -->
                    <div class="space-y-4">
                        <h2 class="text-xl font-bold text-farm-green-900 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21h18M3 10h18M5 6l7-3 7 3M4 10v11h16V10M8 14h.01M12 14h.01M16 14h.01M9 16h6" /></svg>
                            Farm Information
                        </h2>
                        <div>
                            <label class="block text-farm-green-800 font-medium mb-1">Farm Name *</label>
                            <input type="text" name="farm_name" required 
                                   value="<?php echo htmlspecialchars($_POST['farm_name'] ?? ''); ?>"
                                   class="w-full px-4 py-2.5 bg-white/50 border border-white/40 rounded-xl focus:ring-2 focus:ring-farm-green-500 outline-none transition">
                        </div>
                        <div>
                            <label class="block text-farm-green-800 font-medium mb-1">Location *</label>
                            <input type="text" name="location" required 
                                   value="<?php echo htmlspecialchars($_POST['location'] ?? ''); ?>"
                                   class="w-full px-4 py-2.5 bg-white/50 border border-white/40 rounded-xl focus:ring-2 focus:ring-farm-green-500 outline-none transition">
                        </div>
                        <div>
                            <label class="block text-farm-green-800 font-medium mb-1">Registration Number *</label>
                            <input type="text" name="registration_number" required 
                                   value="<?php echo htmlspecialchars($_POST['registration_number'] ?? ''); ?>"
                                   class="w-full px-4 py-2.5 bg-white/50 border border-white/40 rounded-xl focus:ring-2 focus:ring-farm-green-500 outline-none transition">
                            <p class="text-xs text-farm-green-600 mt-1">This will be your unique farm identifier</p>
                        </div>
                    </div>
                </div>

                <div class="mt-8 pt-4 border-t border-white/30">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                    <button type="submit" 
                            class="w-full bg-gradient-to-r from-farm-green-700 to-farm-green-800 hover:from-farm-green-800 hover:to-farm-green-900 text-white font-bold py-3 rounded-xl shadow-lg transition-all flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Create Account & Register Farm
                    </button>
                    <p class="text-center mt-4 text-farm-green-800">
                        Already have an account? 
                        <a href="signin.php" class="font-semibold text-farm-green-700 hover:text-farm-green-900 underline decoration-2 underline-offset-2">Sign In Here</a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</body>
</html>