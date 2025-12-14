<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Gymazing</title>
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
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="inline-block p-4 rounded-full bg-blue-500/10 mb-4">
                <i class="fas fa-lock text-blue-400 text-3xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-white mb-2">Forgot Password?</h1>
            <p class="text-slate-400 text-sm">No worries, we'll send you reset instructions</p>
        </div>

        <!-- Alert Container -->
        <div id="alertContainer" class="mb-6"></div>

        <!-- Form -->
        <form id="forgotPasswordForm" class="space-y-6">
            <div>
                <label for="email" class="block text-sm font-medium text-slate-300 mb-2">
                    <i class="fas fa-envelope mr-2"></i>Email Address
                </label>
                <input 
                    type="email" 
                    id="email" 
                    name="email"
                    required
                    placeholder="Enter your email"
                    class="w-full px-4 py-3 bg-slate-900/50 border border-slate-700 rounded-lg 
                           text-white placeholder-slate-500 focus:outline-none focus:ring-2 
                           focus:ring-blue-500 focus:border-transparent transition"
                >
            </div>

            <button 
                type="submit" 
                id="submitBtn"
                class="w-full bg-blue-600 hover:bg-blue-500 text-white font-semibold py-3 px-4 
                       rounded-lg transition duration-200 shadow-lg shadow-blue-900/20 
                       flex items-center justify-center space-x-2"
            >
                <span id="btnText">Send Reset Link</span>
                <i id="btnIcon" class="fas fa-paper-plane"></i>
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
    </div>

    <script>
        const form = document.getElementById('forgotPasswordForm');
        const submitBtn = document.getElementById('submitBtn');
        const btnText = document.getElementById('btnText');
        const btnIcon = document.getElementById('btnIcon');
        const btnSpinner = document.getElementById('btnSpinner');
        const alertContainer = document.getElementById('alertContainer');

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const email = document.getElementById('email').value;
            
            // Disable button
            submitBtn.disabled = true;
            btnText.textContent = 'Sending...';
            btnIcon.classList.add('hidden');
            btnSpinner.classList.remove('hidden');
            
            try {
                const formData = new FormData();
                formData.append('email', email);
                
                const response = await fetch('index.php?controller=Auth&action=RequestPasswordReset', {
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
                    }, 3000);
                }
            } catch (error) {
                showAlert('An error occurred. Please try again.', 'error');
            } finally {
                // Re-enable button
                submitBtn.disabled = false;
                btnText.textContent = 'Send Reset Link';
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
    </script>
</body>
</html>
