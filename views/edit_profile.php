<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - Gymazing</title>
    <script src="../public/assets/js/tailwindcss/tailwindcss.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #0f172a; color: #e2e8f0; }
        .glass-panel {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body class="min-h-screen">
    
    <?php include __DIR__ . '/layouts/navbar.php'; ?>

    <main class="pt-24 pb-12 md:ml-64 transition-all duration-300">
        <div class="container mx-auto max-w-3xl px-4">
            
            <div class="mb-6 flex items-center justify-between">
                <h1 class="text-2xl font-bold text-white">Edit Profile</h1>
                <a href="index.php?controller=User&action=profile" class="text-slate-400 hover:text-white transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Profile
                </a>
            </div>

            <form action="index.php?controller=User&action=saveProfile" method="POST" enctype="multipart/form-data">
                
                <div class="glass-panel rounded-2xl p-8 mb-6">
                    <h2 class="text-xl font-bold text-white mb-6 flex items-center">
                        <i class="fas fa-user-edit text-blue-500 mr-3"></i> Personal Information
                    </h2>
                    
                    <div class="mb-6 flex items-center gap-6">
                        <div class="relative group">
                            <div class="w-24 h-24 rounded-full overflow-hidden border-2 border-slate-700 bg-slate-800">
                                <?php if(!empty($user['profile_picture'])): ?>
                                    <img src="<?= htmlspecialchars($user['profile_picture']) ?>" alt="Profile" class="w-full h-full object-cover">
                                <?php else: ?>
                                    <div class="w-full h-full flex items-center justify-center text-slate-500 text-3xl">
                                        <i class="fas fa-user"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="flex-1">
                            <label class="block text-xs font-medium text-slate-400 mb-2">Profile Picture</label>
                            <input type="file" name="profile_picture" accept="image/*" 
                                   class="block w-full text-sm text-slate-400
                                   file:mr-4 file:py-2 file:px-4
                                   file:rounded-full file:border-0
                                   file:text-xs file:font-semibold
                                   file:bg-blue-600 file:text-white
                                   hover:file:bg-blue-700
                                   cursor-pointer">
                            <p class="text-xs text-slate-500 mt-1">Allowed formats: JPG, PNG. Max size: 2MB.</p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-medium text-slate-400 mb-1">First Name</label>
                            <input type="text" name="first_name" value="<?= htmlspecialchars($user['first_name'] ?? '') ?>" required 
                                   class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-lg text-white focus:border-blue-500 focus:outline-none transition-colors">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-400 mb-1">Last Name</label>
                            <input type="text" name="last_name" value="<?= htmlspecialchars($user['last_name'] ?? '') ?>" required 
                                   class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-lg text-white focus:border-blue-500 focus:outline-none transition-colors">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-400 mb-1">Middle Name</label>
                            <input type="text" name="middle_name" value="<?= htmlspecialchars($user['middle_name'] ?? '') ?>" 
                                   class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-lg text-white focus:border-blue-500 focus:outline-none transition-colors">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-400 mb-1">Phone Number</label>
                            <input type="tel" name="phone_no" value="<?= htmlspecialchars($user['phone_no'] ?? '') ?>" 
                                   class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-lg text-white focus:border-blue-500 focus:outline-none transition-colors">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-medium text-slate-400 mb-1">Email Address</label>
                            <input type="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required 
                                   class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-lg text-white focus:border-blue-500 focus:outline-none transition-colors">
                        </div>
                    </div>
                </div>

                <div class="glass-panel rounded-2xl p-8 mb-6">
                    <h2 class="text-xl font-bold text-white mb-6 flex items-center">
                        <i class="fas fa-map-marker-alt text-red-500 mr-3"></i> Address Details
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-medium text-slate-400 mb-1">Street Address / Barangay</label>
                            <input type="text" name="street_address" value="<?= htmlspecialchars($address['street_address'] ?? '') ?>" required 
                                   class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-lg text-white focus:border-blue-500 focus:outline-none transition-colors">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-400 mb-1">City</label>
                            <input type="text" name="city" value="<?= htmlspecialchars($address['city'] ?? '') ?>" required 
                                   class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-lg text-white focus:border-blue-500 focus:outline-none transition-colors">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-400 mb-1">Zip Code</label>
                            <input type="number" name="zip" value="<?= htmlspecialchars($address['zip'] ?? '') ?>" required 
                                   class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-lg text-white focus:border-blue-500 focus:outline-none transition-colors">
                        </div>
                    </div>
                </div>

                <div class="flex gap-4">
                    <a href="index.php?controller=User&action=profile" class="flex-1 py-3 px-4 bg-slate-700 hover:bg-slate-600 text-white font-medium rounded-lg text-center transition-colors">
                        Cancel
                    </a>
                    <button type="submit" class="flex-1 py-3 px-4 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg shadow-lg shadow-blue-900/20 transition-colors">
                        Save Changes
                    </button>
                </div>

            </form>
        </div>
    </main>
</body>
</html>