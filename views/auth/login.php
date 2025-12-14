<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gymazing! - Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        input { transition: all 0.2s; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased">

   <main class="min-h-screen flex items-center justify-center p-4">
        <div class="w-full max-w-md bg-white rounded-xl shadow-xl border border-slate-100 overflow-hidden">
            
            <div class="p-8 text-center border-b border-slate-100 bg-slate-50/50">
                <div class="flex items-center justify-center space-x-3 mb-2">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-600 to-blue-700 rounded-lg flex items-center justify-center shadow-md">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <h1 class="font-bold text-2xl tracking-tight text-slate-900">
                        GYM<span class="text-blue-600">AZING</span>
                    </h1>
                </div>
                <p class="text-slate-500 text-sm">Welcome back! Please login to your account.</p>
            </div>

            <div class="p-8">
                <form method="POST" action="index.php?controller=auth&action=verifyLogin" class="space-y-6">
                    
                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Email Address</label>
                        <input type="email" name="email" id="email" placeholder="example@email.com" 
                               value="<?= htmlspecialchars($login['email'] ?? "") ?>" 
                               class="w-full rounded-lg border-gray-300 border px-3 py-2.5 outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-slate-50 focus:bg-white transition-colors" required>
                        
                        <?php if(!empty($loginErrors['email'])): ?>
                            <p class="text-red-500 text-xs mt-1 font-medium flex items-center">⚠ <?= htmlspecialchars($loginErrors['email']) ?></p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label for="password" class="block text-sm font-medium text-slate-700">Password</label>
                            <a href="index.php?controller=Auth&action=ForgotPassword" class="text-xs font-medium text-blue-600 hover:text-blue-800 hover:underline">Forgot Password?</a>
                        </div>
                        <input type="password" name="password" id="password" placeholder="••••••••" 
                               value="<?= htmlspecialchars($login['password'] ?? "") ?>" 
                               class="w-full rounded-lg border-gray-300 border px-3 py-2.5 outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-slate-50 focus:bg-white transition-colors" required>
                        
                        <?php if(!empty($loginErrors['password'])): ?>
                            <p class="text-red-500 text-xs mt-1 font-medium flex items-center">⚠ <?= htmlspecialchars($loginErrors['password']) ?></p>
                        <?php endif; ?>
                    </div>

                    <?php if(!empty($loginError)): ?>
                        <div class="bg-red-50 border border-red-200 rounded-md p-3 flex items-start">
                            <svg class="w-5 h-5 text-red-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <p class="text-sm text-red-600 font-medium"><?= htmlspecialchars($loginError) ?></p>
                        </div>
                    <?php endif; ?>

                    <div>
                        <input type="submit" value="Sign In" class="w-full bg-blue-600 text-white font-bold py-3 px-4 rounded-lg shadow-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition cursor-pointer">
                    </div>

                    <div class="text-center mt-6">
                        <p class="text-sm text-slate-600">
                            Don't have an account? 
                            <a href="index.php?controller=auth&action=register" class="text-blue-600 font-bold hover:underline">Sign up</a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
   </main>
</body>
</html>