<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gymazing | Profile</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <script src="../public/assets/js/tailwindcss/tailwindcss.js"></script>
    <script src="../public/assets/js/jquery/jquery-3.7.1.min.js"></script>
    <link rel="stylesheet" href="../public/assets/icons/fontawesome/css/all.min.css">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #0f172a;
            color: #e2e8f0;
        }

        .gradient-bg {
            background-color: #0f172a;
            background-image: 
                radial-gradient(at 0% 0%, hsla(253,16%,7%,1) 0, transparent 50%), 
                radial-gradient(at 50% 0%, hsla(225,39%,30%,1) 0, transparent 50%), 
                radial-gradient(at 100% 0%, hsla(339,49%,30%,1) 0, transparent 50%);
            background-attachment: fixed;
        }

        .glass-panel {
            background: rgba(30, 41, 59, 0.6);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            transition: all 0.3s ease;
        }

        .glass-panel:hover {
            border-color: rgba(59, 130, 246, 0.3);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        .glass-table-row {
            transition: background-color 0.2s ease;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        .glass-table-row:last-child { border-bottom: none; }
        .glass-table-row:hover { background-color: rgba(59, 130, 246, 0.1); }

        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #0f172a; }
        ::-webkit-scrollbar-thumb { background: #3b82f6; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #2563eb; }
    </style>
</head>
<body class="gradient-bg min-h-screen">
    
    <?php include __DIR__ . '/layouts/navbar.php'; ?>

    <main class="pt-24 pb-12 md:ml-64 transition-all duration-300">
        <div class="container mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            
            <div class="glass-panel rounded-2xl p-8 mb-10 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-64 h-64 bg-blue-600/10 rounded-full blur-3xl -mr-16 -mt-16 pointer-events-none"></div>

                <div class="flex flex-col md:flex-row items-center relative z-10">
                    <div class="flex-shrink-0 mb-6 md:mb-0 md:mr-8 relative group">
                        <div class="absolute inset-0 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full blur opacity-75 group-hover:opacity-100 transition duration-300"></div>
                        <?php if(!empty($userInfo['profile_picture'])): ?>
                            <div class="w-28 h-28 rounded-full overflow-hidden border-2 border-slate-700 bg-slate-900">
                                <img src="<?= htmlspecialchars($userInfo['profile_picture']) ?>" alt="Profile" class="w-full h-full object-cover">
                            </div>
                        <?php else: ?>
                            <div class="relative w-28 h-28 rounded-full bg-slate-900 flex items-center justify-center text-5xl text-blue-400 border-2 border-slate-700">
                                <i class="fa-solid fa-user"></i>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="text-center md:text-left flex-1">
                        <h2 class="text-3xl md:text-4xl font-bold text-white mb-2 tracking-tight">
                            <?= htmlspecialchars($userInfo['first_name'] . ' ' . $userInfo['last_name']) ?>
                        </h2>
                        
                        <div class="flex flex-wrap items-center justify-center md:justify-start gap-3 mb-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider 
                                <?= $role === 'trainer' ? 'bg-purple-500/10 text-purple-400 border border-purple-500/20' : 'bg-blue-500/10 text-blue-400 border border-blue-500/20' ?>">
                                <i class="fa-solid fa-user-tag mr-2"></i> <?= htmlspecialchars(ucfirst($role)) ?>
                            </span>
                            
                            <span class="inline-flex items-center px-3 py-1 rounded-full bg-slate-700/50 text-slate-300 text-xs border border-slate-600">
                                <i class="fa-regular fa-clock mr-2"></i> Member since <?= date('F Y', strtotime($userInfo['created_at'] ?? '')) ?>
                            </span>
                        </div>

                        <div class="flex flex-col md:flex-row items-center justify-center md:justify-start text-slate-400 text-sm gap-3">
                            
                            <div class="flex items-center bg-slate-800/50 px-4 py-2 rounded-lg border border-slate-700/50 w-full md:w-auto justify-center md:justify-start">
                                <i class="fa-solid fa-envelope mr-3 text-blue-500"></i>
                                <?= htmlspecialchars($userInfo['email']) ?>
                            </div>

                            <?php if (!empty($addressInfo)): ?>
                            <div class="flex items-center bg-slate-800/50 px-4 py-2 rounded-lg border border-slate-700/50 w-full md:w-auto justify-center md:justify-start">
                                <i class="fa-solid fa-location-dot mr-3 text-red-500"></i>
                                <span>
                                    <?= htmlspecialchars($addressInfo['street_address']) ?>, 
                                    <?= htmlspecialchars($addressInfo['city']) ?> 
                                    <span class="text-slate-500 ml-1">(<?= htmlspecialchars($addressInfo['zip']) ?>)</span>
                                </span>
                            </div>
                            <?php endif; ?>
                            
                        </div>
                    </div>
                </div>
            </div>

            <?php if ($role === 'trainer'): ?>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                
                <div class="glass-panel rounded-2xl p-6 flex flex-col h-full">
                    <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-700/50">
                        <h4 class="text-xl font-bold text-white flex items-center">
                            <i class="fa-solid fa-users text-blue-500 mr-3"></i>Assigned Members
                        </h4>
                        <span class="text-xs font-semibold bg-blue-500/20 text-blue-300 px-2 py-1 rounded-md">
                            Total: <?= count($assignedMembers) ?>
                        </span>
                    </div>

                    <?php if (!empty($assignedMembers)): ?>
                    <div class="flex-1 overflow-y-auto max-h-[500px] pr-2 custom-scrollbar space-y-3">
                        <?php foreach ($assignedMembers as $member): ?>
                            <div class="p-4 rounded-xl bg-slate-800/40 border border-slate-700/50 hover:bg-slate-700/40 transition-colors group">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h5 class="font-semibold text-white group-hover:text-blue-400 transition-colors">
                                            <?= htmlspecialchars($member['name']) ?>
                                        </h5>
                                        <p class="text-slate-400 text-sm mt-1">
                                            <?= htmlspecialchars($member['email']) ?>
                                        </p>
                                    </div>
                                    <div class="w-10 h-10 rounded-full bg-slate-700 flex items-center justify-center text-slate-400 group-hover:bg-blue-600 group-hover:text-white transition-all">
                                        <i class="fa-solid fa-chevron-right text-xs"></i>
                                    </div>
                                </div>
                                <div class="mt-3 pt-3 border-t border-slate-700/50 flex items-center text-xs text-slate-500">
                                    <i class="fa-regular fa-calendar-check mr-2"></i>
                                    Assigned: <?= date('M d, Y', strtotime($member['assigned_date'])) ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <div class="flex-1 flex flex-col items-center justify-center text-slate-500 py-10">
                        <i class="fa-solid fa-user-slash text-4xl mb-3 opacity-50"></i>
                        <p>No members assigned yet.</p>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="glass-panel rounded-2xl p-6 flex flex-col h-full">
                    <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-700/50">
                        <h4 class="text-xl font-bold text-white flex items-center">
                            <i class="fa-solid fa-chalkboard-user text-purple-500 mr-3"></i>Upcoming Sessions
                        </h4>
                    </div>

                    <?php if (!empty($sessions)): ?>
                    <div class="overflow-hidden rounded-xl border border-slate-700/50">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-800/80 text-slate-400 text-xs uppercase tracking-wider">
                                    <th class="p-4 font-semibold">Date & Time</th>
                                    <th class="p-4 font-semibold">Member</th>
                                    <th class="p-4 font-semibold text-right">Status</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm text-slate-300">
                                <?php foreach ($sessions as $session): ?>
                                <tr class="glass-table-row">
                                    <td class="p-4">
                                        <div class="font-medium text-white">
                                            <?= date('M d', strtotime($session['session_date'])) ?>
                                        </div>
                                        <div class="text-slate-500 text-xs mt-0.5">
                                            <?= date('H:i', strtotime($session['session_date'])) ?>
                                        </div>
                                    </td>
                                    <td class="p-4 font-medium">
                                        <?= htmlspecialchars($session['member_name']) ?>
                                    </td>
                                    <td class="p-4 text-right">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            <?php
                                                if ($session['status']==='scheduled') echo 'bg-yellow-500/10 text-yellow-400 border border-yellow-500/20';
                                                elseif ($session['status']==='completed') echo 'bg-green-500/10 text-green-400 border border-green-500/20';
                                                else echo 'bg-slate-700 text-slate-400';
                                            ?>">
                                            <?= ucfirst($session['status']) ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div class="flex-1 flex flex-col items-center justify-center text-slate-500 py-10">
                        <i class="fa-regular fa-calendar-xmark text-4xl mb-3 opacity-50"></i>
                        <p>No upcoming sessions.</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($role === 'member'): ?>
            <div class="glass-panel rounded-2xl p-8">
                <div class="flex items-center justify-between mb-8">
                    <h4 class="text-2xl font-bold text-white flex items-center">
                        <i class="fa-solid fa-dumbbell text-blue-500 mr-3"></i>My Sessions
                    </h4>
                    <button class="text-sm text-blue-400 hover:text-blue-300 transition-colors font-medium">View History <i class="fa-solid fa-arrow-right ml-1"></i></button>
                </div>

                <?php if (!empty($sessions)): ?>
                <div class="overflow-x-auto rounded-xl border border-slate-700/50">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-800/80 text-slate-400 text-xs uppercase tracking-wider">
                                <th class="p-4 font-semibold">Session Date</th>
                                <th class="p-4 font-semibold">Trainer</th>
                                <th class="p-4 font-semibold text-center">Status</th>
                                <th class="p-4 font-semibold text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm text-slate-300">
                            <?php foreach ($sessions as $session): ?>
                            <tr class="glass-table-row group">
                                <td class="p-4">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 rounded-lg bg-blue-900/30 text-blue-400 flex items-center justify-center mr-3 border border-blue-500/20">
                                            <i class="fa-regular fa-calendar"></i>
                                        </div>
                                        <div>
                                            <div class="font-bold text-white"><?= date('M d, Y', strtotime($session['session_date'])) ?></div>
                                            <div class="text-xs text-slate-500"><?= date('H:i A', strtotime($session['session_date'])) ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-4">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 rounded-full bg-slate-700 flex items-center justify-center text-xs mr-2">
                                            <?= substr($session['trainer_name'], 0, 1) ?>
                                        </div>
                                        <?= htmlspecialchars($session['trainer_name']) ?>
                                    </div>
                                </td>
                                <td class="p-4 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        <?php
                                            if ($session['status']==='scheduled') echo 'bg-yellow-500/10 text-yellow-400 border border-yellow-500/20';
                                            elseif ($session['status']==='completed') echo 'bg-green-500/10 text-green-400 border border-green-500/20';
                                            else echo 'bg-slate-700 text-slate-400';
                                        ?>">
                                        <?= ucfirst($session['status']) ?>
                                    </span>
                                </td>
                                <td class="p-4 text-right">
                                    <button class="text-slate-400 hover:text-white transition-colors">
                                        <i class="fa-solid fa-ellipsis-vertical"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="text-center py-16 bg-slate-800/30 rounded-xl border border-dashed border-slate-700">
                    <div class="inline-block p-4 rounded-full bg-slate-800 mb-4 text-slate-600">
                        <i class="fa-solid fa-calendar-plus text-3xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-white mb-1">No sessions found</h3>
                    <p class="text-slate-500 mb-6">You haven't scheduled any training sessions yet.</p>
                    <button class="px-6 py-2 bg-blue-600 hover:bg-blue-500 text-white rounded-lg font-medium transition-colors">
                        Book a Session
                    </button>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

        </div>
        <?php include_once __DIR__ . '/layouts/footer.php'; ?>
    </main>

</body>
</html>