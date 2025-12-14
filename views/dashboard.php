<?php 
    $user = [
        "name" => "",
        "created_at" => "",
        "plan_name" => "",
        "end_date" => "",
        "status" => "",
    ];
    
    $user['name'] = $userInfo['name'];
    $user['created_at'] = $userInfo['created_at'];
    $user['plan_name'] = isset($userPlan['plan_name']) ? $userPlan['plan_name'] : "No Active Plan";
    $user['end_date'] = isset($userPlan['end_date']) ? $userPlan['end_date'] : "";
    $user['status'] = isset($userPlan['status']) ? $userPlan['status'] : "Inactive";

    // Use the dynamic data passed from the controller ($dashboardStats)
    // Fallback to 0 or 'N/A' if variable isn't set to prevent errors
    $dStats = isset($dashboardStats) ? $dashboardStats : ['total'=>0, 'month'=>0, 'days'=>0, 'status'=>'Inactive'];

    $stats = [
        [
            "label" => "Total Classes", 
            "value" => $dStats['total'], 
            "icon" => '<i class="fa-regular fa-rectangle-list text-yellow-400"></i>'
        ],
        [
            "label" => "Workouts This Month", 
            "value" => $dStats['month'], 
            "icon" => '<i class="fa-regular fa-hand text-orange-400"></i>'
        ],
        [
            "label" => "Days Remaining", 
            "value" => $dStats['days'], 
            "icon" => '<i class="fa-regular fa-calendar text-red-400"></i>'
        ],
        [
            "label" => "Plan Status", 
            "value" => $dStats['status'], 
            "icon" => '<i class="fa-regular fa-id-card text-blue-400"></i>'
        ]
    ];

    // Upcoming classes logic preserved...
    $upcoming_classes = [
        ["name" => "CrossFit Training", "time" => "10:00 AM - 11:00 AM", "trainer" => "Coach Mike", "capacity" => "15/20"],
        ["name" => "Strength Building", "time" => "2:00 PM - 3:30 PM", "trainer" => "Coach Sarah", "capacity" => "12/15"],
        ["name" => "Cardio Blast", "time" => "5:00 PM - 6:00 PM", "trainer" => "Coach Alex", "capacity" => "18/25"],
    ];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gymazing! | Dashboard</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <script src="../public/assets/js/tailwindcss/tailwindcss.js"></script>
    <script src="../public/assets/js/jquery/jquery-3.7.1.min.js"></script>
    <link rel="stylesheet" href="../public/assets/icons/fontawesome/css/all.min.css"></link>
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #0f172a; /* Slate 900 */
            background-image: 
                radial-gradient(at 0% 0%, hsla(253,16%,7%,1) 0, transparent 50%), 
                radial-gradient(at 50% 0%, hsla(225,39%,30%,1) 0, transparent 50%), 
                radial-gradient(at 100% 0%, hsla(339,49%,30%,1) 0, transparent 50%);
            background-attachment: fixed;
            color: #e2e8f0;
        }

        /* Glassmorphism Panel */
        .glass-panel {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease;
        }

        .glass-panel:hover {
            border-color: rgba(255, 255, 255, 0.2);
        }

        /* Animations */
        * { transition: transform 0.2s ease, box-shadow 0.2s ease; }
        
        .hover-lift:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3);
        }

        .fade-in { animation: fadeIn 0.6s ease-out forwards; opacity: 0; }
        @keyframes fadeIn { to { opacity: 1; transform: translateY(0); } }

        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #0f172a; }
        ::-webkit-scrollbar-thumb { background: #3b82f6; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #2563eb; }

        /* Button Glows */
        .btn-glow {
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .btn-glow:hover {
            box-shadow: 0 0 15px rgba(59, 130, 246, 0.5);
            transform: translateY(-1px);
        }

        /* Adjust content for sidebar */
        .main-content { margin-left: 0; }
        @media (min-width: 768px) { .main-content { margin-left: 16rem; } }
    </style>
</head>
<body class="min-h-screen">
    
    <?php include __DIR__ . "/layouts/navbar.php" ?>

    <main class="main-content pt-20 md:pt-8 pb-12 fade-in" style="animation-delay: 0.1s;">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            
            <div id="dashboard" class="mb-10 relative overflow-hidden rounded-3xl bg-gradient-to-r from-blue-700 to-indigo-900 shadow-2xl">
                <div class="absolute top-0 right-0 -mr-20 -mt-20 w-64 h-64 rounded-full bg-white opacity-5"></div>
                
                <div class="relative p-8 lg:p-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <h1 class="text-3xl lg:text-5xl font-extrabold text-white mb-2 tracking-tight">
                            Welcome, <?= explode(' ', $user['name'])[0] ?>! 👋
                        </h1>
                        <p class="text-blue-200 text-lg">Ready to crush your goals today?</p>
                    </div>
                    
                    <div class="flex flex-col items-end">
                        <div class="glass-panel px-4 py-2 rounded-xl flex items-center gap-3 bg-white/10 border-0">
                            <span class="text-blue-100 text-sm font-medium uppercase tracking-wider">Status</span>
                            <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider shadow-sm
                                <?= $userPlan['status'] == 'active' ? 'bg-emerald-500 text-white' : 'bg-rose-500 text-white' ?>">
                                <?= isset($userPlan) ? $user['status'] : "Inactive"; ?>
                            </span>
                        </div>
                        <?php if(isset($userPlan)) {?>
                            <p class="text-blue-200 text-sm mt-3 flex items-center gap-2">
                                <i class="far fa-clock"></i> <?= $user['plan_name'] ?> expires in <span class="font-bold text-white">30 days</span>
                            </p>
                        <?php }?>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10 fade-in" style="animation-delay: 0.2s;">
                <?php foreach ($stats as $stat) { ?>
                    <div class="glass-panel rounded-2xl p-6 hover-lift cursor-pointer group">
                        <div class="flex justify-between items-start mb-4">
                            <div class="p-3 rounded-xl bg-slate-800/50 group-hover:bg-slate-700/50 transition-colors text-2xl">
                                <?= $stat['icon'] ?>
                            </div>
                        </div>
                        <p class="text-3xl font-bold text-white mb-1"><?= $stat['value'] ?></p>
                        <p class="text-slate-400 text-sm font-medium"><?= $stat['label'] ?></p>
                    </div>
                <?php } ?>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 fade-in" style="animation-delay: 0.3s;">
                
                <div class="lg:col-span-2 space-y-8">
                    
                    <div class="glass-panel rounded-2xl p-8 relative overflow-hidden">
                        <div class="absolute top-0 right-0 p-4 opacity-5">
                            <i class="fas fa-id-card text-9xl"></i>
                        </div>
                        
                        <h2 class="text-xl font-bold text-white mb-6 flex items-center gap-3">
                            <span class="w-1 h-6 bg-blue-500 rounded-full"></span>
                            Your Membership
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 relative z-10">
                            <div class="bg-slate-800/50 rounded-xl p-5 border border-slate-700/50">
                                <?php if(isset($userPlan)) { ?>
                                    <p class="text-slate-400 text-xs uppercase tracking-wider mb-2">Current Plan</p>
                                    <p class="text-2xl font-bold text-white mb-1"><?= $user['plan_name'] ?></p>
                                    <div class="flex items-center gap-2">
                                        <div class="w-2 h-2 rounded-full <?= $userPlan['status'] == 'active' ? 'bg-emerald-500' : 'bg-rose-500' ?>"></div>
                                        <span class="text-sm text-slate-300 capitalize"><?= $userPlan['status'] ?></span>
                                    </div>
                                <?php } else { ?>
                                    <p class="text-slate-400 text-sm">No Active Plan</p> 
                                <?php } ?>
                            </div>

                            <div class="bg-slate-800/50 rounded-xl p-5 border border-slate-700/50">
                                <?php if(isset($userPlan)) { ?>
                                    <p class="text-slate-400 text-xs uppercase tracking-wider mb-2">Renewal Date</p>
                                    <p class="text-2xl font-bold text-white mb-1"><?= date('M d, Y', strtotime($user['end_date'])) ?></p>
                                    <p class="text-blue-400 text-xs mt-1">Auto-renew enabled</p>
                                <?php } else { ?>
                                    <p class="text-slate-400 text-sm">-- / -- / ----</p> 
                                <?php } ?>
                            </div>

                            <div class="bg-slate-800/50 rounded-xl p-5 border border-slate-700/50">
                                <p class="text-slate-400 text-xs uppercase tracking-wider mb-2">Member Since</p>
                                <p class="text-xl font-bold text-white"><?= date('M d, Y', strtotime($user['created_at'])) ?></p>
                            </div>

                            <div class="bg-slate-800/50 rounded-xl p-5 border border-slate-700/50">
                                <?php if(isset($userPlan)) { ?>
                                    <p class="text-slate-400 text-xs uppercase tracking-wider mb-2">Next Billing</p>
                                    <p class="text-xl font-bold text-white"><?= date('M d, Y', strtotime('+30 days')) ?></p>
                                    <a href="#" class="text-blue-400 text-xs hover:text-blue-300 mt-1 inline-block">Update Method</a>
                                <?php } else { ?>
                                    <p class="text-slate-400 text-sm">--</p> 
                                <?php } ?>
                            </div>
                        </div>

                        <div class="mt-8 flex flex-col sm:flex-row gap-4 relative z-10">
                            <a href="index.php?controller=Plan&action=viewPlans" 
                               class="flex-1 text-center px-6 py-3 bg-blue-600 hover:bg-blue-500 text-white font-semibold rounded-xl btn-glow">
                                <?= isset($userPlan['status']) ? "Upgrade Plan" : "Subscribe Now" ?>
                            </a>
                            
                            <?php if(isset($userPlan) && $userPlan['status'] === 'active'): ?>
                            <button id="btnFreezeMembership"
                               class="flex-1 text-center px-6 py-3 bg-transparent border border-amber-500/50 text-amber-400 hover:bg-amber-500/10 hover:text-amber-300 font-semibold rounded-xl transition-all">
                                <i class="fas fa-snowflake mr-2"></i>Freeze Membership
                            </button>
                            <?php endif; ?>
                            
                            <a href="index.php?controller=Subscribe&action=CancelSubscription" 
                               class="flex-1 text-center px-6 py-3 bg-transparent border border-rose-500/50 text-rose-400 hover:bg-rose-500/10 hover:text-rose-300 font-semibold rounded-xl transition-all">
                                Cancel Plan
                            </a>
                        </div>
                    </div>

                    <div class="glass-panel rounded-2xl p-8">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-xl font-bold text-white flex items-center gap-3">
                                <span class="w-1 h-6 bg-purple-500 rounded-full"></span>
                                Upcoming Classes
                            </h2>
                            <?php if(isset($userPlan)) { ?>
                                <a href="#" class="text-xs text-purple-400 hover:text-purple-300 font-semibold uppercase tracking-wide">View All</a>
                            <?php } ?>
                        </div>

                        <div class="space-y-4">
                            <?php if(isset($userPlan)) { foreach ($mySessions as $session) { ?>
                                <div class="bg-slate-800/40 hover:bg-slate-800/80 border border-slate-700/50 rounded-xl p-5 transition-all flex items-center gap-4 group">
                                    <div class="bg-slate-900 rounded-lg p-3 text-center min-w-[70px] border border-slate-700">
                                        <p class="text-xs text-slate-400 uppercase"><?= date('M', strtotime($session['session_date'])) ?></p>
                                        <p class="text-xl font-bold text-white"><?= date('d', strtotime($session['session_date'])) ?></p>
                                    </div>
                                    
                                    <div class="flex-1">
                                        <h3 class="text-lg font-bold text-white group-hover:text-blue-400 transition-colors"><?= $user['name'] ?></h3>
                                        <div class="flex items-center gap-4 mt-1 text-sm text-slate-400">
                                            <span><i class="far fa-clock text-blue-500 mr-1"></i> <?= date('h:i A', strtotime($session['session_date'])) ?></span>
                                            <span><i class="far fa-user text-purple-500 mr-1"></i> <?= $session['trainer_name'] ?></span>
                                        </div>
                                    </div>
                                    
                                    <div class="hidden sm:block">
                                        <span class="px-3 py-1 rounded-full bg-blue-500/10 text-blue-400 text-xs font-bold border border-blue-500/20">Confirmed</span>
                                    </div>
                                </div>
                            <?php } } else { ?>
                                <div class="text-center py-10 bg-slate-800/30 rounded-xl border border-dashed border-slate-700">
                                    <div class="inline-block p-4 rounded-full bg-slate-800 mb-3 text-slate-500">
                                        <i class="fas fa-calendar-times text-2xl"></i>
                                    </div>
                                    <h2 class="text-lg text-white font-medium">No Upcoming Classes</h2>
                                    <p class="text-slate-400 text-sm mb-4">You need an active plan to book classes.</p>
                                    <a href="#" class="text-blue-400 hover:text-blue-300 text-sm font-semibold">Browse Plans &rarr;</a>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>

                <div class="space-y-8">
                    
                    <div class="glass-panel rounded-2xl p-6">
                        <h2 class="text-lg font-bold text-white mb-5 flex items-center gap-3">
                            <span class="w-1 h-6 bg-emerald-500 rounded-full"></span>
                            Quick Actions
                        </h2>
                        <div class="grid grid-cols-1 gap-3">
                            <a href="index.php?controller=User&action=editProfile" class="p-4 rounded-xl bg-slate-800/50 hover:bg-blue-600 hover:shadow-lg hover:shadow-blue-900/40 border border-slate-700/50 hover:border-blue-500 transition-all group flex items-center gap-4">
                                <div class="w-10 h-10 rounded-full bg-blue-500/20 text-blue-400 flex items-center justify-center group-hover:bg-white group-hover:text-blue-600 transition-colors">
                                    <i class="fas fa-user-edit"></i>
                                </div>
                                <span class="font-semibold text-slate-200 group-hover:text-white">Edit Profile</span>
                            </a>

                            <button id="openRequestTrainer" class="p-4 rounded-xl bg-slate-800/50 hover:bg-purple-600 hover:shadow-lg hover:shadow-purple-900/40 border border-slate-700/50 hover:border-purple-500 transition-all group flex items-center gap-4 w-full text-left">
                                <div class="w-10 h-10 rounded-full bg-purple-500/20 text-purple-400 flex items-center justify-center group-hover:bg-white group-hover:text-purple-600 transition-colors">
                                    <i class="fas fa-dumbbell"></i>
                                </div>
                                <span class="font-semibold text-slate-200 group-hover:text-white">Request Trainer</span>
                            </button>

                            <a href="#" class="p-4 rounded-xl bg-slate-800/50 hover:bg-emerald-600 hover:shadow-lg hover:shadow-emerald-900/40 border border-slate-700/50 hover:border-emerald-500 transition-all group flex items-center gap-4">
                                <div class="w-10 h-10 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center group-hover:bg-white group-hover:text-emerald-600 transition-colors">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                                <span class="font-semibold text-slate-200 group-hover:text-white">View Progress</span>
                            </a>
                        </div>
                    </div>

                    <div class="rounded-2xl p-6 bg-gradient-to-br from-indigo-900 to-slate-900 border border-indigo-500/30 relative overflow-hidden">
                        <div class="absolute -right-6 -bottom-6 text-indigo-800/20 text-9xl transform rotate-12">
                            <i class="fas fa-headset"></i>
                        </div>
                        
                        <h3 class="text-lg font-bold text-white mb-2 relative z-10">Need Assistance?</h3>
                        <p class="text-indigo-200 text-sm mb-4 relative z-10">Our support team is available 24/7 to help you.</p>
                        
                        <div class="space-y-3 relative z-10">
                            <div class="flex items-center gap-3 text-sm text-indigo-100">
                                <div class="w-8 h-8 rounded-full bg-indigo-500/20 flex items-center justify-center text-indigo-400">
                                    <i class="fas fa-phone-alt"></i>
                                </div>
                                +63 123 456 7890
                            </div>
                            <div class="flex items-center gap-3 text-sm text-indigo-100">
                                <div class="w-8 h-8 rounded-full bg-indigo-500/20 flex items-center justify-center text-indigo-400">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                support@gymazing.com
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </main>

    <div id="bookTrainerModal" class="fixed inset-0 bg-black/80 backdrop-blur-sm hidden items-center justify-center z-50 transition-opacity opacity-0" style="transition: opacity 0.3s ease;">
        <div class="bg-slate-900 border border-slate-700 rounded-2xl shadow-2xl w-full max-w-lg mx-4 p-0 relative transform scale-95 transition-transform" style="transition: transform 0.3s ease;">
            
            <div class="p-6 border-b border-slate-700 flex justify-between items-center bg-slate-800/50 rounded-t-2xl">
                <h3 class="text-xl font-bold text-white">Request a Trainer</h3>
                <button id="closeBookTrainer" class="text-slate-400 hover:text-white transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <div class="p-6 space-y-5">
                <div>
                    <label class="block text-xs uppercase tracking-wider text-slate-400 font-semibold mb-2">Select Trainer</label>
                    <div class="relative">
                        <select id="trainerSelect" class="w-full bg-slate-800 text-white border border-slate-600 rounded-xl p-3 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 appearance-none outline-none transition-all">
                            <option value="">Loading...</option>
                        </select>
                        <div class="absolute right-4 top-4 text-slate-400 pointer-events-none">
                            <i class="fas fa-chevron-down"></i>
                        </div>
                    </div>
                </div>
                
                <div>
                    <label class="block text-xs uppercase tracking-wider text-slate-400 font-semibold mb-2">Session Notes</label>
                    <textarea id="sessionNotes" rows="3" class="w-full bg-slate-800 text-white border border-slate-600 rounded-xl p-3 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all resize-none" placeholder="E.g., Focus on cardio, leg day, injury recovery..."></textarea>
                </div>
                
                <div id="bookTrainerMessage" class="text-sm font-medium text-center min-h-[20px]"></div>
                
                <button id="submitBookTrainer" class="w-full bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-xl py-3.5 shadow-lg shadow-blue-900/20 btn-glow transition-all">
                    Send Request
                </button>
            </div>
        </div>
    </div>

    <script src="../public/assets/js/dashboard.js"></script>
    <script>
        $(function() {
            const $modal = $('#bookTrainerModal');
            // Added animation classes for smoother open/close
            const $modalContent = $modal.find('> div'); 

            const $trainerSelect = $('#trainerSelect');
            const $sessionNotes = $('#sessionNotes');
            const $submitBtn = $('#submitBookTrainer');
            const $messageBox = $('#bookTrainerMessage');

            function showModal() {
                $modal.removeClass('hidden').addClass('flex');
                // Small delay to allow display:flex to apply before opacity transition
                setTimeout(() => {
                    $modal.removeClass('opacity-0');
                    $modalContent.removeClass('scale-95').addClass('scale-100');
                }, 10);
                
                $messageBox.text('').removeClass('text-green-400 text-red-400');
                loadTrainers();
            }

            function hideModal() {
                $modal.addClass('opacity-0');
                $modalContent.removeClass('scale-100').addClass('scale-95');
                setTimeout(() => {
                    $modal.addClass('hidden').removeClass('flex');
                }, 300);
            }

            function loadTrainers() {
                $trainerSelect.html('<option value="">Loading...</option>');
                $.ajax({
                    url: 'index.php?controller=Dashboard&action=listTrainers',
                    method: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        if (!data.success || !data.trainers) {
                            $trainerSelect.html('<option value="">No trainers available</option>');
                            return;
                        }
                        let opts = '<option value="">Select a trainer</option>';
                        data.trainers.forEach(t => {
                            opts += `<option value="${t.trainer_id}">${t.name} (${t.specialization ?? 'Trainer'})</option>`;
                        });
                        $trainerSelect.html(opts);
                    },
                    error: function() {
                        $trainerSelect.html('<option value="">Error loading trainers</option>');
                    }
                });
            }

            function setMessage(text, success = false) {
                $messageBox
                    .text(text)
                    .removeClass('text-green-400 text-red-400')
                    .addClass(success ? 'text-green-400' : 'text-red-400');
            }

            function submitRequest() {
                const trainerId = $trainerSelect.val();
                const notesVal = $sessionNotes.val().trim();
                if (!trainerId) {
                    setMessage('Please select a trainer.');
                    return;
                }
                $submitBtn.prop('disabled', true).addClass('opacity-75 cursor-not-allowed');
                setMessage('');

                const formData = new FormData();
                formData.append('trainer_id', trainerId);
                formData.append('notes', notesVal);

                $.ajax({
                    url: 'index.php?controller=Dashboard&action=requestTrainer',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function(data, textStatus, jqXHR) {
                        if (jqXHR.status !== 200 || !data.success) {
                            setMessage(data.message || 'Unable to send request.');
                            return;
                        }
                        setMessage('Request sent successfully!', true);
                        setTimeout(hideModal, 1500);
                    },
                    error: function(xhr) {
                        let msg = 'Network error, please try again.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        setMessage(msg);
                    },
                    complete: function() {
                        $submitBtn.prop('disabled', false).removeClass('opacity-75 cursor-not-allowed');
                    }
                });
            }

            $('#openRequestTrainer').on('click', showModal);
            $('#closeBookTrainer').on('click', hideModal);
            $modal.on('click', function(e) {
                if (e.target === this) hideModal();
            });
            $submitBtn.on('click', submitRequest);
        });
    </script>
    
    <!-- Freeze Membership Modal -->
    <div id="freezeModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4">
        <div class="bg-slate-800 rounded-2xl max-w-md w-full shadow-2xl border border-slate-700">
            <div class="p-6 border-b border-slate-700">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-bold text-white flex items-center gap-2">
                        <i class="fas fa-snowflake text-amber-400"></i>
                        Freeze Membership
                    </h3>
                    <button id="closeFreezeModal" class="text-slate-400 hover:text-white transition">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>
            
            <form id="freezeRequestForm" class="p-6 space-y-4">
                <div id="freezeAlert" class="hidden"></div>
                
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">
                        <i class="fas fa-calendar-alt mr-2"></i>Freeze Start Date
                    </label>
                    <input type="date" id="freezeStart" name="freeze_start" required
                           min="<?= date('Y-m-d') ?>"
                           class="w-full px-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">
                        <i class="fas fa-calendar-check mr-2"></i>Freeze End Date
                    </label>
                    <input type="date" id="freezeEnd" name="freeze_end" required
                           min="<?= date('Y-m-d', strtotime('+1 day')) ?>"
                           class="w-full px-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                    <p class="text-xs text-slate-500 mt-1">Maximum 90 days</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">
                        <i class="fas fa-comment mr-2"></i>Reason (Optional)
                    </label>
                    <textarea id="freezeReason" name="reason" rows="3"
                              placeholder="E.g., Traveling, Medical reasons, etc."
                              class="w-full px-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white placeholder-slate-500 focus:ring-2 focus:ring-amber-500 focus:border-transparent"></textarea>
                </div>
                
                <div class="bg-blue-900/20 border border-blue-500/30 rounded-lg p-4">
                    <p class="text-xs text-blue-300">
                        <i class="fas fa-info-circle mr-2"></i>
                        Your freeze request will be submitted for admin approval. Once approved, you won't be charged during the freeze period.
                    </p>
                </div>
                
                <button type="submit" id="submitFreeze"
                        class="w-full px-6 py-3 bg-amber-600 hover:bg-amber-500 text-white font-semibold rounded-lg transition">
                    <i class="fas fa-paper-plane mr-2"></i>Submit Request
                </button>
            </form>
        </div>
    </div>
    
    <script>
        $(document).ready(function() {
            const $freezeModal = $('#freezeModal');
            const $freezeForm = $('#freezeRequestForm');
            const $freezeAlert = $('#freezeAlert');
            const $submitBtn = $('#submitFreeze');
            
            $('#btnFreezeMembership').on('click', function() {
                $freezeModal.removeClass('hidden');
            });
            
            $('#closeFreezeModal').on('click', function() {
                $freezeModal.addClass('hidden');
                $freezeForm[0].reset();
                $freezeAlert.addClass('hidden');
            });
            
            $freezeModal.on('click', function(e) {
                if ($(e.target).is($freezeModal)) {
                    $('#closeFreezeModal').click();
                }
            });
            
            $freezeForm.on('submit', function(e) {
                e.preventDefault();
                
                const freezeStart = $('#freezeStart').val();
                const freezeEnd = $('#freezeEnd').val();
                const reason = $('#freezeReason').val();
                
                const start = new Date(freezeStart);
                const end = new Date(freezeEnd);
                const daysDiff = Math.ceil((end - start) / (1000 * 60 * 60 * 24));
                
                if (daysDiff <= 0) {
                    showFreezeAlert('End date must be after start date', 'error');
                    return;
                }
                
                if (daysDiff > 90) {
                    showFreezeAlert('Freeze period cannot exceed 90 days', 'error');
                    return;
                }
                
                $submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>Submitting...');
                
                $.ajax({
                    url: 'index.php?controller=Subscribe&action=RequestFreeze',
                    method: 'POST',
                    data: { freeze_start: freezeStart, freeze_end: freezeEnd, reason: reason },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            showFreezeAlert(response.message, 'success');
                            setTimeout(() => location.reload(), 2000);
                        } else {
                            showFreezeAlert(response.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        let msg = 'An error occurred. Please try again.';
                        if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                        showFreezeAlert(msg, 'error');
                    },
                    complete: function() {
                        $submitBtn.prop('disabled', false).html('<i class="fas fa-paper-plane mr-2"></i>Submit Request');
                    }
                });
            });
            
            function showFreezeAlert(message, type) {
                const bgColor = type === 'success' ? 'bg-green-500/10 border-green-500 text-green-400' : 'bg-red-500/10 border-red-500 text-red-400';
                const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
                
                $freezeAlert
                    .removeClass('hidden')
                    .attr('class', `border ${bgColor} rounded-lg p-3 flex items-center gap-2 text-sm`)
                    .html(`<i class="fas ${icon}"></i><span>${message}</span>`);
                
                if (type === 'success') {
                    setTimeout(() => $freezeAlert.addClass('hidden'), 3000);
                }
            }
        });
    </script>
</body>
</html>