<div class="fixed inset-y-0 left-0 w-64 bg-neutral-900 border-r border-gray-800 hidden md:flex flex-col z-40">
    <div class="p-6 border-b border-gray-800">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-700 rounded-lg flex items-center justify-center shadow-lg">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm text-gray-400">Admin Panel</p>
                <h1 class="text-xl font-bold text-white">GYM<span class="text-blue-400">AZING</span></h1>
            </div>
        </div>
    </div>
    <nav class="flex-1 p-4 space-y-1">
        <a href="index.php?controller=Admin&action=dashboard" class="block px-4 py-3 rounded-lg text-white font-medium hover:bg-gray-800 hover:text-blue-400 transition">Dashboard</a>
        <a href="index.php?controller=Admin&action=reports" class="block px-4 py-3 rounded-lg text-white font-medium hover:bg-gray-800 hover:text-blue-400 transition">Reports</a>
        <a href="settings.php" class="block px-4 py-3 rounded-lg text-white font-medium hover:bg-gray-800 hover:text-blue-400 transition">Settings</a>
        <a href="../views/auth/logout.php" class="block px-4 py-3 rounded-lg text-red-400 font-medium hover:bg-gray-800 transition">Logout</a>
    </nav>
    <div class="p-4 border-t border-gray-800 text-sm text-gray-400">
        Logged in as <span class="text-white font-semibold">Admin</span>
    </div>
</div>