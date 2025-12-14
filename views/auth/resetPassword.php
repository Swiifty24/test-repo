<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Gymazing</title>
    <script src="../public/assets/js/tailwindcss/tailwindcss.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            font-family: 'Inter', system-ui, sans-serif;
        }
        .glass-card {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <div class="glass-card rounded-2xl p-8 w-full max-w-md shadow-2xl">
        
        <?php if (!$tokenValid): ?>
            <!-- Invalid Token -->
            <div class="text-center">
                <div class="inline-block p-4 rounded-full bg-red-500/10 mb-4">
                    <i class="fas fa-exclamation-triangle text-red-400 text-3xl"></i>
                </div>
                <h1 class="text-2xl font-bold text-white mb-2">Invalid Reset Link</h1>
                <p class="text-slate-400 text-sm mb-6"><?= htmlspecialchars($error) ?></p>
                <a href="index.php?controller=Auth&action=Login" 
                   class="inline-block bg-blue-600 hover:bg-blue-500 text-white font-semibold py-3 px-6 rounded-lg transition">
                    Back to Login
                </a>
            </div>
        <?php else: ?>
            <!-- Reset Form -->
            <div class="text-center mb-8">
                <div class="inline-block p-4 rounded-full bg-green-500/10 mb-4">
                    <i class="fas fa-key text-green-400 text-3xl"></i>
                </div>
                <h1 class="text-3xl font-bold text-white mb-2">Reset Password</h1>
                <p class="text-slate-400 text-sm">Enter your new password</p>
            </div>

            <!-- Alert Container -->
            <div id="alertContainer" class="mb-6"></div>

            <!-- Form -->
            <form id="resetPasswordForm" class="space-y-6">
                <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
                
                <div>
                    <label for="password" class="block text-sm font-medium text-slate-300 mb-2">
                        <i class="fas fa-lock mr-2"></i>New Password
                    </label>
                    <div class="relative">
                        <input 
                            type="password" 
                            id="password" 
                            name="password"
                            required
                            minlength="6"
                            placeholder="Enter new password"
                            class="w-full px-4 py-3 bg-slate-900/50 border border-slate-700 rounded-lg 
                                   text-white placeholder-slate-500 focus:outline-none focus:ring-2 
                                   focus:ring-blue-500 focus:border-transparent transition"
                        >
                        <button 
                            type="button" 
                            id="togglePassword"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-white transition">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <p class="text-xs text-slate-500 mt-1">Must be at least 6 characters</p>
                </div>

                <div>
                    <label for="confirm_password" class="block text-sm font-medium text-slate-300 mb-2">
                        <i class="fas fa-lock mr-2"></i>Confirm Password
                    </label>
                    <div class="relative">
                        <input 
                            type="password" 
                            id="confirm_password" 
                            name="confirm_password"
                            required
                            minlength="6"
                            placeholder="Confirm new password"
                            class="w-full px-4 py-3 bg-slate-900/50 border border-slate-700 rounded-lg 
                                   text-white placeholder-slate-500 focus:outline-none focus:ring-2 
                                   focus:ring-blue-500 focus:border-transparent transition"
                        >
                        <button 
                            type="button" 
                            id="toggleConfirmPassword"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-white transition">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <button 
                    type="submit" 
                    id="submitBtn"
                    class="w-full bg-green-600 hover:bg-green-500 text-white font-semibold py-3 px-4 
                           rounded-lg transition duration-200 shadow-lg shadow-green-900/20 
                           flex items-center justify-center space-x-2"
                >
                    <span id="btnText">Reset Password</span>
                    <i id="btnIcon" class="fas fa-check"></i>
                    <div id="btnSpinner" class="hidden">
                        <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-white"></div>
                    </div>
                </button>
            </form>

            <!-- Back to Login -->
            <div class="mt-6 text-center">
                <a href="index.php?controller=Auth&action=Login" 
                   class="text-slate-400 hover:text-white text-sm transition inline-flex items-center space-x-2">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back to Login</span>
                </a>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Toggle password visibility
        const togglePassword = document.getElementById('togglePassword');
        const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
        const passwordInput = document.getElementById('password');
        const confirmPasswordInput = document.getElementById('confirm_password');

        if (togglePassword) {
            togglePassword.addEventListener('click', () => {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                togglePassword.querySelector('i').classList.toggle('fa-eye');
                togglePassword.querySelector('i').classList.toggle('fa-eye-slash');
            });
        }

        if (toggleConfirmPassword) {
            toggleConfirmPassword.addEventListener('click', () => {
                const type = confirmPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                confirmPasswordInput.setAttribute('type', type);
                toggleConfirmPassword.querySelector('i').classList.toggle('fa-eye');
                toggleConfirmPassword.querySelector('i').classList.toggle('fa-eye-slash');
            });
        }

        // Form submission
        const form = document.getElementById('resetPasswordForm');
        if (form) {
            const submitBtn = document.getElementById('submitBtn');
            const btnText = document.getElementById('btnText');
            const btnIcon = document.getElementById('btnIcon');
            const btnSpinner = document.getElementById('btnSpinner');
            const alertContainer = document.getElementById('alertContainer');

            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                
                const password = passwordInput.value;
                const confirmPassword = confirmPasswordInput.value;
                const token = document.querySelector('input[name="token"]').value;
                
                // Client-side validation
                if (password !== confirmPassword) {
                    showAlert('Passwords do not match', 'error');
                    return;
                }
                
                if (password.length < 6) {
                    showAlert('Password must be at least 6 characters', 'error');
                    return;
                }
                
                // Disable button
                submitBtn.disabled = true;
                btnText.textContent = 'Resetting...';
                btnIcon.classList.add('hidden');
                btnSpinner.classList.remove('hidden');
                
                try {
                    const formData = new FormData();
                    formData.append('token', token);
                    formData.append('password', password);
                    formData.append('confirm_password', confirmPassword);
                    
                    const response = await fetch('index.php?controller=Auth&action=UpdatePassword', {
                        method: 'POST',
                        body: formData
                    });
                    
                    const result = await response.json();
                    
                    // Show alert
                    showAlert(result.message, result.success ? 'success' : 'error');
                    
                    if (result.success) {
                        form.reset();
                        setTimeout(() => {
                            window.location.href = 'index.php?controller=Auth&action=Login';
                        }, 2000);
                    }
                } catch (error) {
                    showAlert('An error occurred. Please try again.', 'error');
                } finally {
                    // Re-enable button
                    submitBtn.disabled = false;
                    btnText.textContent = 'Reset Password';
                    btnIcon.classList.remove('hidden');
                    btnSpinner.classList.add('hidden');
                }
            });

            function showAlert(message, type) {
                const alertClass = type === 'success' ? 'bg-green-500/10 border-green-500 text-green-400' : 'bg-red-500/10 border-red-500 text-red-400';
                const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
                
                const alert = document.createElement('div');
                alert.className = `border ${alertClass} rounded-lg p-4 flex items-center space-x-3 animate-fade-in`;
                alert.innerHTML = `
                    <i class="fas ${icon}"></i>
                    <span class="flex-1">${message}</span>
                `;
                
                alertContainer.innerHTML = '';
                alertContainer.appendChild(alert);
                
                setTimeout(() => {
                    alert.remove();
                }, 5000);
            }
        }
    </script>
</body>
</html>
