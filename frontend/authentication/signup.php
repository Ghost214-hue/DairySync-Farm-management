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
    <title>Sign Up | DairySync</title>
    <link href="/frontend/css/output.css" rel="stylesheet">
</head>
<body class="bg-gradient-to-br from-farm-green-50 via-farm-olive-50 to-farm-green-100 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-5xl w-full">
        <!-- Glassmorphic Card -->
        <div class="bg-white/25 backdrop-blur-lg rounded-2xl shadow-2xl border border-white/30 overflow-hidden">
            <div class="text-center py-6 border-b border-white/30">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl shadow-lg mb-2"
                     style="background: linear-gradient(135deg, #2d6a4f, #1b4332);">
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

            <!-- Registration Form -->
            <form method="POST" action="" class="p-6 md:p-8">
                <div class="grid md:grid-cols-2 gap-6">
                    <!-- Left: User Information -->
                    <div class="space-y-4">
                        <h2 class="text-xl font-bold text-farm-green-900 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
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
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21h18M3 10h18M5 6l7-3 7 3M4 10v11h16V10M8 14h.01M12 14h.01M16 14h.01M9 16h6" />
                            </svg>
                            Farm Information
                        </h2>
                        <div>
                            <label class="block text-farm-green-800 font-medium mb-1">Farm Name *</label>
                            <input type="text" name="farm_name" id="farm_name" required
                                   value="<?php echo htmlspecialchars($_POST['farm_name'] ?? ''); ?>"
                                   class="w-full px-4 py-2.5 bg-white/50 border border-white/40 rounded-xl focus:ring-2 focus:ring-farm-green-500 outline-none transition">
                        </div>
                        <div>
                            <label class="block text-farm-green-800 font-medium mb-1">Location *</label>
                            <input type="text" name="location" required
                                   value="<?php echo htmlspecialchars($_POST['location'] ?? ''); ?>"
                                   class="w-full px-4 py-2.5 bg-white/50 border border-white/40 rounded-xl focus:ring-2 focus:ring-farm-green-500 outline-none transition">
                        </div>

                        <!-- Auto-generated Farm Registration ID -->
                        <div>
                            <label class="block text-farm-green-800 font-medium mb-1">Farm Registration ID *</label>
                            <div class="flex gap-2">
                                <input type="text" name="registration_number" id="registration_number" readonly
                                       value="<?php echo htmlspecialchars($_POST['registration_number'] ?? ''); ?>"
                                       placeholder="e.g. FM-10042"
                                       class="w-full px-4 py-2.5 bg-white/30 border border-white/40 rounded-xl outline-none font-mono tracking-wider text-sm cursor-not-allowed"
                                       style="color: #1b4332;"
                                       required>
                                <!-- Inline styles used so button is always visible regardless of Tailwind JIT -->
                                <button type="button" id="generate-btn" onclick="generateFarmId()"
                                        style="flex-shrink:0; padding: 0.6rem 1rem; background: linear-gradient(135deg, #2d6a4f, #1b4332); color: #fff; font-weight: 600; font-size: 0.875rem; border-radius: 0.75rem; border: none; cursor: pointer; display: flex; align-items: center; gap: 0.4rem; white-space: nowrap; box-shadow: 0 2px 8px rgba(27,67,50,0.3); transition: opacity 0.2s;"
                                        onmouseover="this.style.opacity='0.85'"
                                        onmouseout="this.style.opacity='1'">
                                    <svg id="gen-icon" style="width:16px;height:16px;transition:transform 0.5s;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                    Generate
                                </button>
                            </div>
                            <p class="text-xs text-farm-green-600 mt-1">
                                Click Generate — a simple unique ID will be created for your farm
                            </p>
                            <!-- Success badge, shown after generation -->
                            <div id="reg-badge" style="display:none; margin-top:0.5rem; align-items:center; gap:0.4rem; padding:0.25rem 0.75rem; background:#dcfce7; border:1px solid #86efac; border-radius:999px; font-size:0.75rem; color:#166534; font-weight:500; width: fit-content;">
                                <svg style="width:12px;height:12px;color:#16a34a;" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                ID ready — click Generate again to change it
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-8 pt-4 border-t border-white/30">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                    <button type="submit"
                            style="width:100%; background: linear-gradient(135deg, #2d6a4f, #1b4332); color:#fff; font-weight:700; padding:0.85rem; border-radius:0.75rem; border:none; cursor:pointer; font-size:1rem; display:flex; align-items:center; justify-content:center; gap:0.5rem; box-shadow:0 4px 14px rgba(27,67,50,0.35); transition: opacity 0.2s;"
                            onmouseover="this.style.opacity='0.88'"
                            onmouseout="this.style.opacity='1'">
                        <svg style="width:20px;height:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Create Account &amp; Register Farm
                    </button>
                    <p class="text-center mt-4 text-farm-green-800">
                        Already have an account?
                        <a href="/h3j5n8q1e81ea2b3a2d2bcf5ce5f54dc81c6d327031" class="font-semibold text-farm-green-700 hover:text-farm-green-900 underline decoration-2 underline-offset-2">Sign In Here</a>
                    </p>
                </div>
            </form>
        </div>
    </div>

    <script>
        function generateFarmId() {
            // Simple, predictable format: FM-XXXXX (FM prefix + 5-digit random number)
            const num = Math.floor(10000 + Math.random() * 90000); // always 5 digits
            const id  = 'FM-' + num;

            document.getElementById('registration_number').value = id;

            // Show success badge
            const badge = document.getElementById('reg-badge');
            badge.style.display = 'flex';

            // Spin the refresh icon
            const icon = document.getElementById('gen-icon');
            icon.style.transform = 'rotate(360deg)';
            setTimeout(() => { icon.style.transform = 'rotate(0deg)'; }, 500);
        }

        // Block submission if no ID generated yet
        document.querySelector('form').addEventListener('submit', function(e) {
            const regVal = document.getElementById('registration_number').value.trim();
            if (!regVal) {
                e.preventDefault();
                const btn = document.getElementById('generate-btn');
                const origStyle = btn.style.background;
                btn.style.background = '#dc2626';
                btn.textContent = '⚠ Generate First!';
                setTimeout(() => {
                    btn.style.background = 'linear-gradient(135deg, #2d6a4f, #1b4332)';
                    btn.innerHTML = `<svg id="gen-icon" style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg> Generate`;
                }, 2500);
            }
        });
    </script>
</body>
</html>