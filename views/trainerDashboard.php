<?php

// if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'trainer') {
//     header("location: login.php");
//     exit;
// }


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trainer Dashboard - Gymazing</title>
    <script src="../public/assets/js/tailwindcss/tailwindcss.js"></script>
    <script src="../public/assets/js/jquery/jquery-3.7.1.min.js"></script>
    <link rel="stylesheet" href="../public/assets/icons/fontawesome/css/all.min.css"></link>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #0f172a; /* slate-900 */
            color: #e2e8f0;
        }
        
        .gradient-bg {
            background: radial-gradient(circle at top left, #1e293b, #0f172a);
            background-attachment: fixed;
        }

        /* Glassmorphism Utilities */
        .glass-panel {
            background: rgba(30, 41, 59, 0.4);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.05);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
        }

        /* Logic-Essential Classes (Preserved) */
        .tab-content { display: none; }
        .tab-content.active { display: block; animation: fadeIn 0.4s ease-out; }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .tab-button {
            transition: all 0.3s ease;
            border-bottom: 2px solid transparent;
            opacity: 0.7;
        }
        
        .tab-button:hover { opacity: 1; background: rgba(255,255,255,0.05); }
        
        .tab-button.active {
            opacity: 1;
            color: #60a5fa; /* blue-400 */
            border-bottom-color: #3b82f6;
            background: linear-gradient(to top, rgba(59, 130, 246, 0.1), transparent);
        }

        /* Modal Transitions */
        .modal-backdrop {
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            backdrop-filter: blur(5px);
        }
        .modal-backdrop.show { opacity: 1; visibility: visible; }
        .modal-content {
            transform: scale(0.95) translateY(20px);
            opacity: 0;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .modal-backdrop.show .modal-content { transform: scale(1) translateY(0); opacity: 1; }

        /* Status Badges */
        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .status-scheduled { background: rgba(59, 130, 246, 0.15); color: #60a5fa; border: 1px solid rgba(59, 130, 246, 0.2); }
        .status-completed { background: rgba(34, 197, 94, 0.15); color: #4ade80; border: 1px solid rgba(34, 197, 94, 0.2); }
        .status-cancelled { background: rgba(239, 68, 68, 0.15); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.2); }
        .status-pending { background: rgba(251, 146, 60, 0.15); color: #fb923c; border: 1px solid rgba(251, 146, 60, 0.2); }
        .status-active { background: rgba(16, 185, 129, 0.15); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.2); }

        .loading {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #0f172a; }
        ::-webkit-scrollbar-thumb { background: #334155; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #475569; }
    </style>
</head>
<body class="gradient-bg min-h-screen selection:bg-blue-500 selection:text-white">

    <?php include __DIR__ . "/layouts/navbar.php" ?>
    
    <div class="md:ml-64 transition-all duration-300">
        
        <main class="container mx-auto pt-24 px-4 sm:px-6 lg:px-8 py-8 max-w-7xl">
            
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-white tracking-tight">Trainer Dashboard</h1>
                <p class="text-slate-400 mt-1">Manage your schedule, members, and requests.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
                <div class="glass-panel rounded-2xl p-6 relative overflow-hidden group hover:-translate-y-1 transition-transform duration-300">
                    <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                        <i class="fa-regular fa-user text-6xl text-blue-500"></i>
                    </div>
                    <div class="relative z-10">
                        <div class="w-12 h-12 rounded-xl bg-blue-500/20 flex items-center justify-center mb-4 text-blue-400 text-xl">
                            <i class="fa-regular fa-user"></i>
                        </div>
                        <p class="text-slate-400 text-xs font-medium uppercase tracking-wider">Assigned Members</p>
                        <p class="text-3xl font-bold text-white mt-1"><?= $stats['total_members'] ?></p>
                    </div>
                </div>

                <div class="glass-panel rounded-2xl p-6 relative overflow-hidden group hover:-translate-y-1 transition-transform duration-300">
                    <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                        <i class="fa-regular fa-calendar text-6xl text-rose-500"></i>
                    </div>
                    <div class="relative z-10">
                        <div class="w-12 h-12 rounded-xl bg-rose-500/20 flex items-center justify-center mb-4 text-rose-400 text-xl">
                            <i class="fa-regular fa-calendar"></i>
                        </div>
                        <p class="text-slate-400 text-xs font-medium uppercase tracking-wider">Upcoming Sessions</p>
                        <p class="text-3xl font-bold text-white mt-1"><?= $stats['upcoming_sessions'] ?></p>
                    </div>
                </div>

                <div class="glass-panel rounded-2xl p-6 relative overflow-hidden group hover:-translate-y-1 transition-transform duration-300">
                    <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                        <i class="fa-regular fa-calendar-check text-6xl text-emerald-500"></i>
                    </div>
                    <div class="relative z-10">
                        <div class="w-12 h-12 rounded-xl bg-emerald-500/20 flex items-center justify-center mb-4 text-emerald-400 text-xl">
                            <i class="fa-regular fa-calendar-check"></i>
                        </div>
                        <p class="text-slate-400 text-xs font-medium uppercase tracking-wider">Completed (Month)</p>
                        <p class="text-3xl font-bold text-white mt-1"><?= $stats['completed_sessions'] ?></p>
                    </div>
                </div>

                <div class="glass-panel rounded-2xl p-6 relative overflow-hidden group hover:-translate-y-1 transition-transform duration-300">
                    <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                        <i class="fa-regular fa-hourglass text-6xl text-orange-500"></i>
                    </div>
                    <div class="relative z-10">
                        <div class="w-12 h-12 rounded-xl bg-orange-500/20 flex items-center justify-center mb-4 text-orange-400 text-xl">
                            <i class="fa-regular fa-hourglass"></i>
                        </div>
                        <p class="text-slate-400 text-xs font-medium uppercase tracking-wider">Pending Requests</p>
                        <p class="text-3xl font-bold text-white mt-1"><?= $stats['pending_requests'] ?></p>
                    </div>
                </div>
            </div>

            <div class="mb-8">
                <div class="glass-panel rounded-t-2xl flex items-center px-2 pt-2 border-b-0">
                    <button class="tab-button active px-6 py-4 text-sm font-semibold text-slate-300 hover:text-white transition-colors focus:outline-none" data-tab="members">
                        <i class="fa-regular fa-user mr-2"></i> My Members
                    </button>
                    <button class="tab-button px-6 py-4 text-sm font-semibold text-slate-300 hover:text-white transition-colors focus:outline-none" data-tab="sessions">
                        <i class="fa-regular fa-calendar mr-2"></i> Sessions
                    </button>
                    <button class="tab-button px-6 py-4 text-sm font-semibold text-slate-300 hover:text-white transition-colors focus:outline-none" data-tab="requests">
                        <i class="fa-regular fa-bell mr-2"></i> Requests
                        <?php if($stats['pending_requests'] > 0): ?>
                            <span class="ml-2 bg-orange-500 text-white text-[10px] px-1.5 py-0.5 rounded-full"><?= $stats['pending_requests'] ?></span>
                        <?php endif; ?>
                    </button>
                </div>

                <div id="members" class="tab-content active glass-panel rounded-b-2xl p-6 min-h-[400px]">
                    
                    <div class="overflow-hidden rounded-xl border border-slate-700/50">
                        <table class="w-full text-left text-sm text-slate-300">
                            <thead class="bg-slate-800/80 text-xs uppercase font-semibold text-slate-400">
                                <tr>
                                    <th class="px-6 py-4">Member</th>
                                    <th class="px-6 py-4">Contact</th>
                                    <th class="px-6 py-4">Assigned Date</th>
                                    <th class="px-6 py-4">Status</th>
                                    <th class="px-6 py-4 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-700/50 bg-slate-900/20">
                                <?php foreach($assignedMembers as $member): ?>
                                <tr class="hover:bg-slate-800/50 transition-colors group">
                                    <td class="px-6 py-4 font-medium text-white">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-slate-700 flex items-center justify-center text-xs">
                                                <?= substr($member['name'], 0, 1) ?>
                                            </div>
                                            <?= htmlspecialchars($member['name']) ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4"><?= htmlspecialchars($member['email']) ?></td>
                                    <td class="px-6 py-4"><?= date('M d, Y', strtotime($member['assigned_date'])) ?></td>
                                    <td class="px-6 py-4">
                                        <span class="status-badge status-<?= $member['status'] ?>"><?= ucfirst($member['status']) ?></span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <button class="btn-schedule-session text-sm text-blue-400 hover:text-white bg-blue-500/10 hover:bg-blue-600 px-3 py-1.5 rounded-lg transition-all border border-blue-500/20 hover:border-transparent" 
                                                data-user-id="<?= $member['user_id'] ?>" 
                                                data-name="<?= htmlspecialchars($member['name']) ?>">
                                            <i class="fa-solid fa-plus mr-1"></i> Schedule
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if(empty($assignedMembers)): ?>
                                    <tr><td colspan="5" class="px-6 py-8 text-center text-slate-500">No members assigned yet.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div id="sessions" class="tab-content glass-panel rounded-b-2xl p-6 min-h-[400px]">
                    <div class="space-y-4">
                        <?php if(empty($upcomingSessions)): ?>
                            <div class="text-center py-12">
                                <div class="bg-slate-800/50 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fa-regular fa-calendar-xmark text-2xl text-slate-500"></i>
                                </div>
                                <p class="text-slate-400">No upcoming sessions scheduled.</p>
                            </div>
                        <?php endif; ?>

                        <?php foreach($upcomingSessions as $session): ?>
                        <div class="bg-slate-800/40 rounded-xl p-5 border border-slate-700/50 hover:border-slate-600 transition-all flex flex-col md:flex-row items-start md:items-center justify-between gap-4 group">
                            
                            <div class="flex items-start gap-4">
                                <div class="bg-slate-900/80 p-3 rounded-lg text-center min-w-[70px] border border-slate-700">
                                    <span class="block text-xs text-rose-400 font-bold uppercase"><?= date('M', strtotime($session['session_date'])) ?></span>
                                    <span class="block text-2xl text-white font-bold leading-none mt-1"><?= date('d', strtotime($session['session_date'])) ?></span>
                                </div>
                                
                                <div>
                                    <h3 class="text-lg font-bold text-white group-hover:text-blue-400 transition-colors"><?= htmlspecialchars($session['member_name']) ?></h3>
                                    <div class="flex items-center gap-4 text-sm text-slate-400 mt-1">
                                        <span><i class="fa-regular fa-clock mr-1.5 text-slate-500"></i> <?= date('h:i A', strtotime($session['session_date'])) ?></span>
                                        <span><i class="fa-solid fa-hashtag mr-1.5 text-slate-500"></i> ID: <?= $session['session_id'] ?></span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-4 w-full md:w-auto justify-between md:justify-end">
                                <span class="status-badge status-<?= $session['status'] ?>"><?= ucfirst($session['status']) ?></span>
                                
                                <?php if($session['status'] === 'scheduled'): ?>
                                <div class="flex gap-2">
                                    <button class="btn-complete-session w-8 h-8 rounded-lg bg-emerald-500/10 hover:bg-emerald-500 text-emerald-500 hover:text-white border border-emerald-500/20 transition-all flex items-center justify-center" 
                                            title="Complete" 
                                            data-session-id="<?= $session['session_id'] ?>">
                                        <i class="fa-solid fa-check"></i>
                                    </button>
                                    <button class="btn-cancel-session w-8 h-8 rounded-lg bg-rose-500/10 hover:bg-rose-500 text-rose-500 hover:text-white border border-rose-500/20 transition-all flex items-center justify-center" 
                                            title="Cancel" 
                                            data-session-id="<?= $session['session_id'] ?>">
                                        <i class="fa-solid fa-xmark"></i>
                                    </button>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div id="requests" class="tab-content glass-panel rounded-b-2xl p-6 min-h-[400px]">
                    <div class="space-y-4" id="requestsList">
                        </div>
                </div>
            </div>
        </main>
    </div> <div id="scheduleSessionModal" class="modal-backdrop fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60">
        <div class="modal-content bg-slate-800 rounded-2xl shadow-2xl max-w-md w-full border border-slate-700 overflow-hidden">
            
            <div class="p-6 border-b border-slate-700/50 flex justify-between items-center bg-slate-900/30">
                <h3 class="text-xl font-bold text-white">Schedule Session</h3>
                <button class="session-modal-close text-slate-400 hover:text-white transition-colors w-8 h-8 flex items-center justify-center rounded-lg hover:bg-slate-700">
                    <i class="fa-solid fa-times"></i>
                </button>
            </div>
            
            <form id="scheduleSessionForm" class="p-6">
                <input type="hidden" id="schedule_user_id" name="user_id">
                <input type="hidden" name="trainer_id" value="<?= $trainerId ?>">
                
                <div class="space-y-5">
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Member</label>
                        <input type="text" id="schedule_member_name" readonly class="w-full px-4 py-2.5 bg-slate-900/50 border border-slate-700 rounded-xl text-slate-300 focus:outline-none cursor-not-allowed">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Date & Time <span class="text-rose-500">*</span></label>
                        <input type="datetime-local" name="session_date" required class="w-full px-4 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all placeholder-slate-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Notes (Optional)</label>
                        <textarea name="notes" rows="3" class="w-full px-4 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all resize-none placeholder-slate-600" placeholder="Focus areas, goals, etc..."></textarea>
                    </div>

                    <div id="scheduleMessage" class="hidden text-sm text-center py-2 rounded-lg"></div>

                    <div class="flex gap-3 pt-2">
                        <button type="button" class="session-modal-close flex-1 px-4 py-2.5 bg-slate-700 hover:bg-slate-600 text-white font-semibold rounded-xl transition-colors">
                            Cancel
                        </button>
                        <button type="submit" id="btnScheduleSession" class="flex-1 px-4 py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-semibold rounded-xl shadow-lg shadow-blue-500/20 transition-all">
                            Confirm Schedule
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            
            // Tab switching
            $('.tab-button').on('click', function() {
                const tabName = $(this).data('tab');
                
                // Remove active from all tab buttons
                $('.tab-button').removeClass('active');
                
                // Add active to clicked button
                $(this).addClass('active');
                
                // Hide all tab content
                $('.tab-content').removeClass('active');
                
                // Show selected tab content
                $('#' + tabName).addClass('active');
                if(tabName === 'requests') {
                    loadRequests();
                }
                
            });

            // Schedule session
            $('.btn-schedule-session').on('click', function() {
                const userId = $(this).data('user-id');
                const userName = $(this).data('name');
                
                $('#schedule_user_id').val(userId);
                $('#schedule_member_name').val(userName);
                $('#scheduleSessionModal').addClass('show');
                $('body').css('overflow', 'hidden');
            });

            $('.session-modal-close').on('click', function() {
                $('#scheduleSessionModal').removeClass('show');
                $('body').css('overflow', 'auto');
            });

            $('#scheduleSessionForm').on('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                const submitBtn = $('#btnScheduleSession');
                const originalText = submitBtn.text();
                
                submitBtn.html('<span class="loading"></span>').prop('disabled', true);

                $.ajax({
                    type: 'POST',
                    url: 'index.php?controller=Trainer&action=createSession',
                    data: formData,
                    processData: false,
                    contentType: false,
                    // Remove dataType: 'json' temporarily
                    success: function(response) {
                        console.log('Raw response:', response);
                        console.log('Response type:', typeof response);
                        
                        const data = typeof response === 'string' ? JSON.parse(response) : response;
                        
                        if(data.success) {
                            showMessage('scheduleMessage', '✓ ' + data.message, 'success');
                            setTimeout(() => location.reload(), 2000);
                        } else {
                            showMessage('scheduleMessage', data.message, 'error');
                            submitBtn.html(originalText).prop('disabled', false);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log('XHR:', xhr);
                        console.log('Status:', status);
                        console.log('Error:', error);
                        console.log('Response Text:', xhr.responseText);
                        showMessage('scheduleMessage', 'An error occurred', 'error');
                        submitBtn.html(originalText).prop('disabled', false);
                    }
                });
            });

            // Complete session
            $('.btn-complete-session').on('click', function() {
                const sessionId = $(this).data('session-id');
                if(confirm('Mark this session as completed?')) {
                    updateSessionStatus(sessionId, 'completed');
                }
            });

            // Cancel session
            $('.btn-cancel-session').on('click', function() {
                const sessionId = $(this).data('session-id');
                if(confirm('Cancel this session?')) {
                    updateSessionStatus(sessionId, 'cancelled');
                }
            });

            function updateSessionStatus(sessionId, status) {
                $.ajax({
                    type: 'POST',
                    url: 'index.php?controller=Trainer&action=updateSessionStatus',
                    data: { session_id: sessionId, status: status },
                    dataType: 'json',
                    success: function(response) {
                        if(response.success) {
                            location.reload();
                        } else {
                            alert(response.message);
                        }
                    }
                });
            }

            // Load requests
            function loadRequests() {
                $.ajax({
                    type: 'GET',
                    url: 'index.php?controller=Trainer&action=getPendingRequests',
                    dataType: 'json',
                    success: function(response) {
                        if(response.success && response.data) {
                            let html = '';
                            response.data.forEach(req => {
                                // Updated to match redesign
                                html += `
                                    <div class="bg-slate-800/40 rounded-xl p-5 border border-slate-700/50 hover:border-slate-600 transition-all flex flex-col sm:flex-row items-center justify-between gap-4">
                                        <div class="flex items-center gap-4 w-full sm:w-auto">
                                            <div class="w-10 h-10 rounded-full bg-blue-500/20 flex items-center justify-center text-blue-400 font-bold">
                                                ${req.member_name.charAt(0).toUpperCase()}
                                            </div>
                                            <div>
                                                <h3 class="text-lg font-bold text-white leading-tight">${req.member_name}</h3>
                                                <p class="text-slate-400 text-sm">${req.email}</p>
                                                <div class="flex items-center gap-2 mt-1">
                                                    <span class="text-xs text-slate-500"><i class="fa-regular fa-clock mr-1"></i> ${req.request_date}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex gap-3 w-full sm:w-auto">
                                            <button class="btn-accept-request flex-1 sm:flex-none px-4 py-2 bg-emerald-500/10 hover:bg-emerald-500 text-emerald-500 hover:text-white border border-emerald-500/20 rounded-lg transition-all text-sm font-semibold" data-request-id="${req.request_id}">
                                                Accept
                                            </button>
                                            <button class="btn-reject-request flex-1 sm:flex-none px-4 py-2 bg-rose-500/10 hover:bg-rose-500 text-rose-500 hover:text-white border border-rose-500/20 rounded-lg transition-all text-sm font-semibold" data-request-id="${req.request_id}">
                                                Reject
                                            </button>
                                        </div>
                                    </div>
                                `;
                            });
                            $('#requestsList').html(html || `
                                <div class="text-center py-12">
                                    <div class="bg-slate-800/50 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <i class="fa-regular fa-bell-slash text-2xl text-slate-500"></i>
                                    </div>
                                    <p class="text-slate-400">No pending requests at the moment.</p>
                                </div>
                            `);
                        }
                    }
                });
            }

            // Accept/Reject requests
            $(document).on('click', '.btn-accept-request', function() {
                handleRequest($(this).data('request-id'), 'accepted');
            });

            $(document).on('click', '.btn-reject-request', function() {
                handleRequest($(this).data('request-id'), 'rejected');
            });

            function handleRequest(requestId, action) {
                $.ajax({
                    type: 'POST',
                    url: 'index.php?controller=Trainer&action=handleRequest',
                    data: { request_id: requestId, action: action },
                    dataType: 'json',
                    success: function(response) {
                        if(response.success) {
                            loadRequests();
                            location.reload();
                        } else {
                            alert(response.message);
                        }
                    }
                });
            }

            function showMessage(elementId, message, type) {
                const messageDiv = $('#' + elementId);
                const bgColor = type === 'error' 
                    ? 'bg-rose-500/20 text-rose-400 border border-rose-500/30' 
                    : 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30';
                
                messageDiv.html(`<div class="p-3 rounded-lg ${bgColor} text-sm font-medium">${message}</div>`).removeClass('hidden');
                if(type !== 'error') setTimeout(() => messageDiv.addClass('hidden'), 3000);
            }
        });
    </script>
</body>
</html>