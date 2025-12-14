<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gymazing! - Register</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Optional: Smooth transition for inputs */
        input, select { transition: all 0.2s; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased">

   <main class="min-h-screen py-12 px-4 sm:px-6 flex items-center justify-center">
        <div class="max-w-3xl w-full bg-white rounded-xl shadow-xl border border-slate-100 overflow-hidden">
            
            <div class="bg-blue-600 px-8 py-6 text-center">
                <h1 class="text-white text-3xl font-bold tracking-tight">Create Account</h1>
                <p class="text-blue-100 text-sm mt-2">Join Gymazing today and start your journey.</p>
            </div>

            <div class="p-8 md:p-10">
                <form method="POST" action="index.php?controller=auth&action=validateUserRegistration" class="space-y-8" enctype="multipart/form-data">
                    
                    <div>
                        <h2 class="text-sm uppercase tracking-wide text-slate-500 font-semibold mb-4 border-b pb-2">Personal Details</h2>
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                            
                            <div class="md:col-span-5">
                                <label for="first_name" class="block text-sm font-medium text-slate-700 mb-1">First Name</label>
                                <input type="text" name="first_name" id="first_name" placeholder="John" 
                                       value="<?= htmlspecialchars($register['first_name'] ?? '') ?>" 
                                       class="w-full rounded-lg border-gray-300 border px-3 py-2.5 outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-slate-50 focus:bg-white" required>
                                <?php if(!empty($registerError['first_name'])): ?>
                                    <p class="text-red-500 text-xs mt-1 font-medium flex items-center">⚠ <?= htmlspecialchars($registerError['first_name']) ?></p>
                                <?php endif; ?>
                            </div>
                            
                            <div class="md:col-span-5">
                                <label for="last_name" class="block text-sm font-medium text-slate-700 mb-1">Last Name</label>
                                <input type="text" name="last_name" id="last_name" placeholder="Doe" 
                                       value="<?= htmlspecialchars($register['last_name'] ?? '') ?>" 
                                       class="w-full rounded-lg border-gray-300 border px-3 py-2.5 outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-slate-50 focus:bg-white" required>
                                <?php if(!empty($registerError['last_name'])): ?>
                                    <p class="text-red-500 text-xs mt-1 font-medium flex items-center">⚠ <?= htmlspecialchars($registerError['last_name']) ?></p>
                                <?php endif; ?>
                            </div>

                            <div class="md:col-span-12">
                                <label for="phone_no" class="block text-sm font-medium text-slate-700 mb-1">Phone Number</label>
                                <input type="tel" name="phone_no" id="phone_no" placeholder="09123456789" 
                                       value="<?= htmlspecialchars($register['phone_no'] ?? '') ?>" 
                                       class="w-full rounded-lg border-gray-300 border px-3 py-2.5 outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-slate-50 focus:bg-white" required>
                                <?php if(!empty($registerError['phone_no'])): ?>
                                    <p class="text-red-500 text-xs mt-1 font-medium flex items-center">⚠ <?= htmlspecialchars($registerError['phone_no']) ?></p>
                                <?php endif; ?>
                            </div>
                            
                            <div class="md:col-span-2">
                                <label for="middle_name" class="block text-sm font-medium text-slate-700 mb-1">M.I.</label>
                                <input type="text" name="middle_name" id="middle_name" placeholder="S." 
                                       value="<?= htmlspecialchars($register['middle_name'] ?? '') ?>" 
                                       class="w-full rounded-lg border-gray-300 border px-3 py-2.5 outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-slate-50 focus:bg-white">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                            <div>
                                <label for="date_of_birth" class="block text-sm font-medium text-slate-700 mb-1">Date Of Birth</label>
                                <input type="date" name="date_of_birth" id="date_of_birth" 
                                       value="<?= htmlspecialchars($register['date_of_birth'] ?? '') ?>" 
                                       class="w-full rounded-lg border-gray-300 border px-3 py-2.5 outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-slate-50 focus:bg-white text-slate-600" required>
                                <?php if(!empty($registerError['date_of_birth'])): ?>
                                    <p class="text-red-500 text-xs mt-1 font-medium">⚠ <?= htmlspecialchars($registerError['date_of_birth']) ?></p>
                                <?php endif; ?>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Gender</label>
                                <div class="flex items-center gap-4 h-[46px] px-3 border border-gray-200 rounded-lg bg-slate-50">
                                    <div class="flex items-center">
                                        <input type="radio" name="gender" id="male" value="male" class="w-4 h-4 text-blue-600 focus:ring-blue-500 border-gray-300" <?= (isset($register['gender']) && $register['gender'] == "male") ? "checked" : "" ?> required>
                                        <label for="male" class="ml-2 text-sm text-slate-700 cursor-pointer">Male</label>
                                    </div>
                                    <div class="flex items-center">
                                        <input type="radio" name="gender" id="female" value="female" class="w-4 h-4 text-blue-600 focus:ring-blue-500 border-gray-300" <?= (isset($register['gender']) && $register['gender'] == "female") ? "checked" : "" ?>>
                                        <label for="female" class="ml-2 text-sm text-slate-700 cursor-pointer">Female</label>
                                    </div>
                                    <div class="flex items-center">
                                        <input type="radio" name="gender" id="other" value="Other" class="w-4 h-4 text-blue-600 focus:ring-blue-500 border-gray-300" <?= (isset($register['gender']) && $register['gender'] == "Other") ? "checked" : "" ?>>
                                        <label for="other" class="ml-2 text-sm text-slate-700 cursor-pointer">Others</label>
                                    </div>
                                </div>
                                <?php if(!empty($registerError['gender'])): ?>
                                    <p class="text-red-500 text-xs mt-1 font-medium">⚠ <?= htmlspecialchars($registerError['gender']) ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h2 class="text-sm uppercase tracking-wide text-slate-500 font-semibold mb-4 border-b pb-2">Address Information</h2>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="md:col-span-1">
                                <label for="street_address" class="block text-sm font-medium text-slate-700 mb-1">Street / Brgy</label>
                                <input type="text" name="street_address" id="street_address" placeholder="123 Street" 
                                       value="<?= htmlspecialchars($register['street_address'] ?? '') ?>" 
                                       class="w-full rounded-lg border-gray-300 border px-3 py-2.5 outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-slate-50 focus:bg-white" required>
                                <?php if(!empty($registerError['street_address'])): ?>
                                    <p class="text-red-500 text-xs mt-1 font-medium">⚠ <?= htmlspecialchars($registerError['street_address']) ?></p>
                                <?php endif; ?>
                            </div>

                            <div class="md:col-span-1">
                                <label for="city" class="block text-sm font-medium text-slate-700 mb-1">City</label>
                                <input type="text" name="city" id="city" placeholder="Cagayan de Oro" 
                                       value="<?= htmlspecialchars($register['city'] ?? '') ?>" 
                                       class="w-full rounded-lg border-gray-300 border px-3 py-2.5 outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-slate-50 focus:bg-white" required>
                                <?php if(!empty($registerError['city'])): ?> <p class="text-red-500 text-xs mt-1 font-medium">⚠ <?= htmlspecialchars($registerError['city'] ?? '') ?></p>
                                <?php endif; ?>
                            </div>

                            <div class="md:col-span-1">
                                <label for="zip" class="block text-sm font-medium text-slate-700 mb-1">Zip Code</label>
                                <input type="number" name="zip" id="zip" placeholder="9000" 
                                       value="<?= htmlspecialchars($register['zip'] ?? '') ?>" 
                                       class="w-full rounded-lg border-gray-300 border px-3 py-2.5 outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-slate-50 focus:bg-white" required>
                                <?php if(!empty($registerError['zip'])): ?>
                                    <p class="text-red-500 text-xs mt-1 font-medium">⚠ <?= htmlspecialchars($registerError['zip']) ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h2 class="text-sm uppercase tracking-wide text-slate-500 font-semibold mb-4 border-b pb-2">Verification</h2>
                        <div class="mb-4">
                            <label for="valid_id_picture" class="block text-sm font-medium text-slate-700 mb-1">Upload Valid ID</label>
                            <input type="file" name="valid_id_picture" id="valid_id_picture" accept="image/*"
                                   class="block w-full text-sm text-slate-500
                                   file:mr-4 file:py-2 file:px-4
                                   file:rounded-full file:border-0
                                   file:text-sm file:font-semibold
                                   file:bg-blue-50 file:text-blue-700
                                   hover:file:bg-blue-100" required>
                            <p class="text-xs text-slate-500 mt-1">Please upload a clear picture of a valid government ID for account verification.</p>
                            <?php if(!empty($registerError['valid_id_picture'])): ?>
                                <p class="text-red-500 text-xs mt-1 font-medium">⚠ <?= htmlspecialchars($registerError['valid_id_picture']) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div>
                        <h2 class="text-sm uppercase tracking-wide text-slate-500 font-semibold mb-4 border-b pb-2">Account Security</h2>
                        
                        <div class="mb-4">
                            <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Email Address</label>
                            <input type="email" name="email" id="email" placeholder="example@email.com" 
                                   value="<?= htmlspecialchars($register['email'] ?? '') ?>" 
                                   class="w-full rounded-lg border-gray-300 border px-3 py-2.5 outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-slate-50 focus:bg-white" required>
                            <?php if(!empty($registerError['email'])): ?>
                                <p class="text-red-500 text-xs mt-1 font-medium">⚠ <?= htmlspecialchars($registerError['email']) ?></p>
                            <?php endif; ?>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="password" class="block text-sm font-medium text-slate-700 mb-1">Password</label>
                                <input type="password" name="password" id="password" placeholder="Min. 8 characters" 
                                       class="w-full rounded-lg border-gray-300 border px-3 py-2.5 outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-slate-50 focus:bg-white" required minlength="8">
                                <?php if(!empty($registerError['password'])): ?>
                                    <p class="text-red-500 text-xs mt-1 font-medium">⚠ <?= htmlspecialchars($registerError['password']) ?></p>
                                <?php endif; ?>
                            </div>

                            <div>
                                <label for="cPassword" class="block text-sm font-medium text-slate-700 mb-1">Confirm Password</label>
                                <input type="password" name="cPassword" id="cPassword" placeholder="Retype password" 
                                       class="w-full rounded-lg border-gray-300 border px-3 py-2.5 outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-slate-50 focus:bg-white" required minlength="8">
                                <?php if(!empty($registerError['cPassword'])): ?>
                                    <p class="text-red-500 text-xs mt-1 font-medium">⚠ <?= htmlspecialchars($registerError['cPassword']) ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="pt-2">
                        <div class="flex items-start mb-6">
                            <div class="flex items-center h-5">
                                <input type="checkbox" name="agreement" id="agreement" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" required>
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="agreement" class="text-slate-600">I agree to the <a href="#" class="text-blue-600 hover:text-blue-800 font-medium underline">User Agreement</a> and <a href="#" class="text-blue-600 hover:text-blue-800 font-medium underline">Privacy Terms</a></label>
                            </div>
                        </div>

                        <?php if(!empty($registerError['register'])): ?>
                            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-4">
                                <div class="flex">
                                    <div class="ml-3">
                                        <p class="text-sm text-red-700"><?= htmlspecialchars($registerError['register']) ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <input type="submit" value="Create Account" class="w-full bg-blue-600 text-white font-bold py-3 px-4 rounded-lg shadow-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition cursor-pointer">
                    </div>

                    <div class="text-center mt-4">
                        <p class="text-sm text-slate-600">Already have an account? <a href="index.php?controller=auth&action=login" class="text-blue-600 font-bold hover:underline">Sign in</a></p>
                    </div>
                </form>
            </div>
        </div>
   </main>
</body>
</html>