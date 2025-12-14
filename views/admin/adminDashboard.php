<?php
// $user_name = $members['first_name'];
// $user_initial = substr($user_name, 0, 1);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Gymazing</title>
    <script src="../public/assets/js/tailwindcss/tailwindcss.js"></script>
    <script src="../public/assets/js/jquery/jquery-3.7.1.min.js"></script>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* Modern Dark Theme Base */
        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background-color: #0f172a; /* Slate 900 */
            color: #e2e8f0;
        }

        /* Glassmorphism Utilities */
        .glass-panel {
            background: rgba(30, 41, 59, 0.7); /* Slate 800 with opacity */
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .glass-header {
            background: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        /* Scrollbar Styling */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #0f172a;
        }
        ::-webkit-scrollbar-thumb {
            background: #334155;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #475569;
        }

        /* Interactive Elements */
        .transition-all-300 {
            transition: all 0.3s ease;
        }

        .hover-lift:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3);
        }

        /* Tab Animations */
        .tab-button {
            position: relative;
            transition: all 0.3s ease;
            border-bottom: 2px solid transparent;
            opacity: 0.6;
        }
        .tab-button:hover {
            opacity: 1;
            background-color: rgba(255,255,255,0.05);
        }
        .tab-button.active {
            opacity: 1;
            border-bottom-color: #3b82f6; /* Blue 500 */
            color: #60a5fa; /* Blue 400 */
            background: linear-gradient(to top, rgba(59, 130, 246, 0.1), transparent);
        }

        .tab-content {
            display: none;
            animation: fadeIn 0.4s ease-out;
        }
        .tab-content.active {
            display: block;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(5px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Modal Transitions */
        .modal-backdrop {
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            background-color: rgba(0, 0, 0, 0.75);
            backdrop-filter: blur(4px);
        }
        .modal-backdrop.show {
            opacity: 1;
            visibility: visible;
        }
        .modal-content {
            transform: scale(0.95) translateY(10px);
            opacity: 0;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: #1e293b; /* Slate 800 */
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .modal-backdrop.show .modal-content {
            transform: scale(1) translateY(0);
            opacity: 1;
        }

        /* Table Styling */
        .custom-table th {
            background-color: rgba(30, 41, 59, 0.95);
            color: #94a3b8;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
        }
        .custom-table tr {
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            transition: background-color 0.2s;
        }
        .custom-table tr:hover {
            background-color: rgba(255, 255, 255, 0.02);
        }
        .custom-table td {
            color: #cbd5e1;
        }

        /* Status Badges */
        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            border: 1px solid transparent;
        }
        .status-active, .status-paid {
            background-color: rgba(34, 197, 94, 0.1);
            color: #4ade80;
            border-color: rgba(34, 197, 94, 0.2);
        }
        .status-inactive, .status-failed, .status-removed {
            background-color: rgba(239, 68, 68, 0.1);
            color: #f87171;
            border-color: rgba(239, 68, 68, 0.2);
        }
        .status-pending, .status-trial {
            background-color: rgba(251, 146, 60, 0.1);
            color: #fb923c;
            border-color: rgba(251, 146, 60, 0.2);
        }
    </style>
</head>
<body class="min-h-screen">
    
    <?php include __DIR__ . "/layouts/adminnavbar.php" ?>

    <div class="min-h-screen md:ml-64 transition-all duration-300">
        
        <div id="alertContainer" class="fixed top-6 right-4 z-50 space-y-4 max-w-sm w-full"></div>

        <main class="p-6 lg:p-10">
            
            <div class="mb-10 flex flex-col md:flex-row justify-between items-start md:items-center">
                <div>
                    <h1 class="text-3xl font-bold text-white mb-2 tracking-tight">Admin Dashboard</h1>
                    <p class="text-slate-400">Overview of gym operations and performance</p>
                </div>
                <div class="mt-4 md:mt-0 flex gap-3">
                    <span class="px-4 py-2 glass-panel rounded-lg text-sm text-slate-300 flex items-center">
                        <span class="w-2 h-2 rounded-full bg-green-500 mr-2"></span> System Online
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-10">
                <div class="glass-panel rounded-xl p-6 hover-lift cursor-pointer relative overflow-hidden group">
                    <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                        <i class="fas fa-users text-6xl text-blue-500"></i>
                    </div>
                    <p class="text-slate-400 text-sm font-medium uppercase tracking-wider mb-1">Total Members</p>
                    <p class="text-3xl font-bold text-white"><?= $memberCount['active_member_count'] ?></p>
                    <div class="mt-4 flex items-center text-xs">
                        <span class="text-emerald-400 bg-emerald-400/10 px-2 py-0.5 rounded mr-2">↑ 12%</span>
                        <span class="text-slate-500">vs last month</span>
                    </div>
                </div>

                <div class="glass-panel rounded-xl p-6 hover-lift cursor-pointer relative overflow-hidden group">
                    <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                        <i class="fas fa-wallet text-6xl text-emerald-500"></i>
                    </div>
                    <p class="text-slate-400 text-sm font-medium uppercase tracking-wider mb-1">Total Revenue</p>
                    <p class="text-3xl font-bold text-white">₱ <?= number_format($totalEarned['total_earned'], 2) ?></p>
                    <div class="mt-4 flex items-center text-xs">
                        <span class="text-emerald-400 bg-emerald-400/10 px-2 py-0.5 rounded mr-2">↑ 8%</span>
                        <span class="text-slate-500">vs last month</span>
                    </div>
                </div>

                <div class="glass-panel rounded-xl p-6 hover-lift cursor-pointer relative overflow-hidden group">
                    <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                        <i class="fas fa-clipboard-list text-6xl text-purple-500"></i>
                    </div>
                    <p class="text-slate-400 text-sm font-medium uppercase tracking-wider mb-1">Active Plans</p>
                    <p class="text-3xl font-bold text-white">3</p>
                    <div class="mt-4 flex items-center text-xs">
                        <span class="text-blue-400 hover:text-blue-300">Create new plan →</span>
                    </div>
                </div>

                <div class="glass-panel rounded-xl p-6 hover-lift cursor-pointer relative overflow-hidden group">
                    <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                        <i class="fas fa-stopwatch text-6xl text-orange-500"></i>
                    </div>
                    <p class="text-slate-400 text-sm font-medium uppercase tracking-wider mb-1">Active Trials</p>
                    <p class="text-3xl font-bold text-white">89</p>
                    <div class="mt-4 flex items-center text-xs">
                        <span class="text-orange-400">5 expiring soon</span>
                    </div>
                </div>
            </div>

            <!-- Freeze Requests Section -->
            <?php if(count($freezeRequests) > 0): ?>
            <div class="glass-panel rounded-xl p-6 border border-amber-500/30 bg-amber-500/5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2">
                        <i class="fas fa-snowflake text-amber-400"></i>
                        Membership Freeze Requests
                    </h3>
                    <span class="bg-amber-500/20 text-amber-400 px-3 py-1 rounded-full text-sm font-semibold">
                        <?= count($freezeRequests) ?> Pending
                    </span>
                </div>
                
                <div class="space-y-3 max-h-96 overflow-y-auto">
                    <?php foreach($freezeRequests as $request): ?>
                    <div class="bg-slate-900/50 border border-slate-700 rounded-lg p-4 hover:border-amber-500/50 transition">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-3 mb-2">
                                    <h4 class="font-semibold text-white truncate"><?= htmlspecialchars($request['member_name']) ?></h4>
                                    <span class="text-xs bg-blue-500/20 text-blue-400 px-2 py-1 rounded whitespace-nowrap">
                                        <?= htmlspecialchars($request['plan_name']) ?>
                                    </span>
                                </div>
                                <p class="text-sm text-slate-400 mb-2 truncate"><?= htmlspecialchars($request['email']) ?></p>
                                
                                <div class="grid grid-cols-2 gap-3 mb-2">
                                    <div class="text-sm">
                                        <i class="fas fa-calendar-alt text-slate-500 mr-2"></i>
                                        <span class="text-slate-400">Start:</span>
                                        <span class="text-white font-medium"><?= date('M d', strtotime($request['freeze_start'])) ?></span>
                                    </div>
                                    <div class="text-sm">
                                        <i class="fas fa-calendar-check text-slate-500 mr-2"></i>
                                        <span class="text-slate-400">End:</span>
                                        <span class="text-white font-medium"><?= date('M d', strtotime($request['freeze_end'])) ?></span>
                                    </div>
                                </div>
                                
                                <?php if (!empty($request['reason'])): ?>
                                <div class="text-sm bg-slate-800 border border-slate-700 rounded p-2">
                                    <i class="fas fa-comment text-slate-500 mr-2"></i>
                                    <span class="text-slate-300"><?= htmlspecialchars($request['reason']) ?></span>
                                </div>
                                <?php endif; ?>
                                
                                <p class="text-xs text-slate-500 mt-2">
                                    Requested: <?= date('M d, Y g:i A', strtotime($request['requested_at'])) ?>
                                </p>
                            </div>
                            
                            <div class="flex flex-col gap-2">
                                <button onclick="approveFreeze(<?= $request['freeze_id'] ?>)"
                                        class="px-4 py-2 bg-green-600 hover:bg-green-500 text-white text-sm font-semibold rounded-lg transition whitespace-nowrap">
                                    <i class="fas fa-check mr-1"></i> Approve
                                </button>
                                <button onclick="rejectFreeze(<?= $request['freeze_id'] ?>)"
                                        class="px-4 py-2 bg-red-600 hover:bg-red-500 text-white text-sm font-semibold rounded-lg transition whitespace-nowrap">
                                    <i class="fas fa-times mr-1"></i> Reject
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <div class="glass-panel rounded-xl border-0 overflow-hidden">
                <div class="glass-header px-4 pt-2 flex overflow-x-auto space-x-1 no-scrollbar">
                    <button class="tab-button active px-6 py-4 text-sm font-medium text-slate-300 whitespace-nowrap focus:outline-none" data-tab="members">
                        <i class="fas fa-users mr-2"></i> Members
                    </button>
                    <button class="tab-button px-6 py-4 text-sm font-medium text-slate-300 whitespace-nowrap focus:outline-none" data-tab="trainers">
                        <i class="fas fa-dumbbell mr-2"></i> Trainers
                    </button>
                    <button class="tab-button px-6 py-4 text-sm font-medium text-slate-300 whitespace-nowrap focus:outline-none" data-tab="walkins">
                        <i class="fas fa-walking mr-2"></i> Walk Ins
                    </button>
                    <button class="tab-button px-6 py-4 text-sm font-medium text-slate-300 whitespace-nowrap focus:outline-none" data-tab="plans">
                        <i class="fas fa-tags mr-2"></i> Plans
                    </button>
                    <button class="tab-button px-6 py-4 text-sm font-medium text-slate-300 whitespace-nowrap focus:outline-none" data-tab="payments">
                        <i class="fas fa-credit-card mr-2"></i> Payments
                    </button>
                    <button class="tab-button px-6 py-4 text-sm font-medium text-slate-300 whitespace-nowrap focus:outline-none" data-tab="pending">
                        <i class="fas fa-user-clock mr-2"></i> Pending
                    </button>
                </div>

                <div id="members" class="tab-content active bg-[#162032]"> <div class="p-6">
                        <div class="flex flex-col xl:flex-row justify-between gap-4 mb-6">
                            <button id="btnAddMember" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-lg shadow-blue-900/20 flex items-center justify-center">
                                <i class="fas fa-plus mr-2"></i> Add Member
                            </button>
                            
                            <div class="flex flex-col sm:flex-row gap-3 flex-1 xl:justify-end">
                                <div class="relative flex-1 max-w-md">
                                    <i class="fas fa-search absolute left-3 top-3 text-slate-500"></i>
                                    <input type="text" id="searchMembers" placeholder="Search members..." class="w-full pl-10 pr-4 py-2.5 bg-slate-800 text-slate-200 border border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                                </div>
                                <select id="filterStatus" class="px-4 py-2.5 bg-slate-800 text-slate-200 border border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm cursor-pointer">
                                    <option value="">All Status</option>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                    <option value="trial">Trial</option>
                                </select>
                                <select id="filterPlan" class="px-4 py-2.5 bg-slate-800 text-slate-200 border border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm cursor-pointer">
                                    <option value="">All Plans</option>
                                    <option value="basic">Basic</option>
                                    <option value="standard">Premium</option>
                                    <option value="premium">Elite</option>
                                </select>
                            </div>
                        </div>

                        <div class="overflow-x-auto rounded-lg border border-slate-700">
                            <table class="w-full text-left custom-table">
                                <thead>
                                    <tr>
                                        <th class="p-4 rounded-tl-lg">Name</th>
                                        <th class="p-4">Email</th>
                                        <th class="p-4">Plan</th>
                                        <th class="p-4">Join Date</th>
                                        <th class="p-4">Status</th>
                                        <th class="p-4 text-center rounded-tr-lg">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($members as $member) { 
                                        $user_name = $member['name'];
                                        $user_initial = substr($user_name, 0, 1); 
                                    ?>
                                        <tr class="table-row" data-user-id="<?= $member['user_id'] ?>">
                                            <td class="p-4">
                                                <div class="flex items-center">
                                                    <div class="w-9 h-9 bg-gradient-to-br from-blue-500 to-blue-700 rounded-full flex items-center justify-center text-white font-bold text-sm shadow-md mr-3">
                                                        <?= $user_initial ?>
                                                    </div>
                                                    <span class="font-medium text-white"><?= $member['name'] ?></span>
                                                </div>
                                            </td>
                                            <td class="p-4 text-sm text-slate-400"><?= $member['email'] ?></td>
                                            <td class="p-4 text-sm font-medium text-slate-300"><?= isset($member['plan_name']) ? $member['plan_name'] : '<span class="text-slate-500 italic">No Plan</span>' ?></td>
                                            <td class="p-4 text-sm text-slate-400"><?= date('M d, Y', strtotime($member['created_at'])) ?></td>
                                            <td class="p-4">
                                                <span class="status-badge <?= $member['status'] == 'active' ? 'status-active' : 'status-inactive'?>">
                                                    <?= isset($member['status']) ? ucfirst($member['status']) : 'Inactive' ?>
                                                </span>
                                            </td>
                                            <td class="p-4 text-center">
                                                <div class="flex items-center justify-center gap-2">
                                                    <button id="viewMemberDetailsBtn" class="btn-view-member p-1.5 text-blue-400 hover:text-blue-300 transition-colors" title="View"><i class="fas fa-eye"></i></button>
                                                    <button class="btn-edit-member p-1.5 text-emerald-400 hover:text-emerald-300 transition-colors" title="Edit"><i class="fas fa-pen"></i></button>
                                                    <button class="btn-delete-member p-1.5 text-red-400 hover:text-red-300 transition-colors" title="Deactivate"><i class="fas fa-trash-alt"></i></button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-4 text-sm text-slate-400">
                            <p>Showing 1-4 of <?= $memberCount['active_member_count'] ?> members</p>
                            <div class="flex space-x-1">
                                <button class="px-3 py-1 bg-slate-800 hover:bg-slate-700 rounded border border-slate-700 transition-colors">Prev</button>
                                <button class="px-3 py-1 bg-blue-600 text-white rounded shadow-sm">1</button>
                                <button class="px-3 py-1 bg-slate-800 hover:bg-slate-700 rounded border border-slate-700 transition-colors">2</button>
                                <button class="px-3 py-1 bg-slate-800 hover:bg-slate-700 rounded border border-slate-700 transition-colors">Next</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="trainers" class="tab-content bg-[#162032]">
                    <div class="p-6">
                        <div class="flex flex-col xl:flex-row justify-between gap-4 mb-6">
                            <div class="flex gap-3">
                                <button id="btnAddNewTrainer" class="px-5 py-2.5 bg-purple-600 hover:bg-purple-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-lg shadow-purple-900/20 flex items-center">
                                    <i class="fas fa-dumbbell mr-2"></i> Add Trainer
                                </button>
                                <button id="btnAddMemberNewTrainer" class="px-5 py-2.5 bg-slate-700 hover:bg-slate-600 text-white text-sm font-semibold rounded-lg transition-colors border border-slate-600">
                                    Promote Member
                                </button>
                            </div>
                            
                            <div class="flex flex-col sm:flex-row gap-3 flex-1 xl:justify-end">
                                <div class="relative flex-1 max-w-md">
                                    <i class="fas fa-search absolute left-3 top-3 text-slate-500"></i>
                                    <input type="text" placeholder="Search trainers..." class="w-full pl-10 pr-4 py-2.5 bg-slate-800 text-slate-200 border border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 text-sm">
                                </div>
                                <select class="px-4 py-2.5 bg-slate-800 text-slate-200 border border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 text-sm">
                                    <option value="">Specialization</option>
                                    <option value="Cardio">Cardio</option>
                                    <option value="Strength">Strength</option>
                                    <option value="Yoga">Yoga</option>
                                </select>
                            </div>
                        </div>

                        <div class="overflow-x-auto rounded-lg border border-slate-700">
                            <table class="w-full text-left custom-table">
                                <thead>
                                    <tr>
                                        <th class="p-4 rounded-tl-lg">Name</th>
                                        <th class="p-4">Email</th>
                                        <th class="p-4">Contact</th>
                                        <th class="p-4">Specialization</th>
                                        <th class="p-4">Status</th>
                                        <th class="p-4 text-center rounded-tr-lg">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($trainers as $trainer) { 
                                        $user_name = $trainer['name'];
                                        $user_initial = substr($user_name, 0, 1); 
                                    ?>
                                        <tr class="table-row" data-trainer-id="<?= $trainer['trainer_id'] ?>">
                                            <td class="p-4">
                                                <div class="flex items-center">
                                                    <div class="w-9 h-9 bg-purple-600 rounded-full flex items-center justify-center text-white font-bold text-sm shadow-md mr-3">
                                                        <?= $user_initial ?>
                                                    </div>
                                                    <span class="font-medium text-white"><?= $trainer['name'] ?></span>
                                                </div>
                                            </td>
                                            <td class="p-4 text-sm text-slate-400"><?= $trainer['email'] ?></td>
                                            <td class="p-4 text-sm text-slate-400"><?= $trainer['contact_no'] ?></td>
                                            <td class="p-4 text-sm">
                                                <span class="px-2 py-1 bg-purple-500/10 text-purple-400 rounded text-xs border border-purple-500/20">
                                                    <?= isset($trainer['specialization']) ? $trainer['specialization'] : 'General' ?>
                                                </span>
                                            </td>
                                            <td class="p-4">
                                                <span class="status-badge <?= $trainer['status'] == 'active' ? 'status-active' : 'status-inactive'?>">
                                                    <?= isset($trainer['status']) ? ucfirst($trainer['status']) : 'Inactive' ?>
                                                </span>
                                            </td>
                                            <td class="p-4 text-center">
                                                <div class="flex items-center justify-center gap-2">
                                                    <button class="btn-view-trainer p-1.5 text-blue-400 hover:text-blue-300 transition-colors"><i class="fas fa-eye"></i></button>
                                                    <button class="btn-edit-trainer p-1.5 text-emerald-400 hover:text-emerald-300 transition-colors"><i class="fas fa-pen"></i></button>
                                                    <button class="btn-delete-trainer p-1.5 text-red-400 hover:text-red-300 transition-colors"><i class="fas fa-trash-alt"></i></button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div id="walkins" class="tab-content bg-[#162032]">
                    <div class="p-6">
                        <div class="flex flex-col xl:flex-row justify-between gap-4 mb-6">
                            <button id="btnAddWalkIn" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-lg shadow-emerald-900/20 flex items-center">
                                <i class="fas fa-walking mr-2"></i> Add Walk-in
                            </button>
                            <div class="relative flex-1 max-w-md xl:ml-auto">
                                <i class="fas fa-search absolute left-3 top-3 text-slate-500"></i>
                                <input type="text" id="searchWalkins" placeholder="Search walk-ins..." class="w-full pl-10 pr-4 py-2.5 bg-slate-800 text-slate-200 border border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm">
                            </div>
                        </div>

                        <div class="overflow-x-auto rounded-lg border border-slate-700">
                            <table class="w-full text-left custom-table">
                                <thead>
                                    <tr>
                                        <th class="p-4 rounded-tl-lg">Name</th>
                                        <th class="p-4">Contact</th>
                                        <th class="p-4">Session Type</th>
                                        <th class="p-4">Time In</th>
                                        <th class="p-4">Time Out</th>
                                        <th class="p-4 text-center rounded-tr-lg">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($walk_ins as $walkin) { 
                                        $user_name = $walkin['name'];
                                        $user_initial = substr($user_name, 0, 1); 
                                    ?>
                                        <tr class="table-row" data-walkin-id="<?= $walkin['walkin_id'] ?>">
                                            <td class="p-4">
                                                <div class="flex items-center">
                                                    <div class="w-9 h-9 bg-emerald-600 rounded-full flex items-center justify-center text-white font-bold text-sm shadow-md mr-3">
                                                        <?= $user_initial ?>
                                                    </div>
                                                    <span class="font-medium text-white"><?= $walkin['name'] ?></span>
                                                </div>
                                            </td>
                                            <td class="p-4 text-sm text-slate-400"><?= $walkin['contact_no'] ?></td>
                                            <td class="p-4 text-sm">
                                                <span class="text-white"><?= isset($walkin['session_type']) ? ucfirst($walkin['session_type']) : 'Standard' ?></span>
                                                <span class="ml-1 text-slate-500 text-xs">(₱<?= $walkin['payment_amount'] ?>)</span>
                                            </td>
                                            <td class="p-4 text-sm text-slate-400"><?= date('H:i', strtotime($walkin['visit_time'])) ?></td>
                                            <td class="p-4 text-sm text-slate-400"><?= date('H:i', strtotime($walkin['end_date'])) ?></td>
                                            <td class="p-4 text-center">
                                                <div class="flex items-center justify-center gap-2">
                                                    <button id="" class="btn-view-walkin p-1.5 text-blue-400 hover:text-blue-300"><i class="fas fa-eye"></i></button>
                                                    <button class="btn-edit-walkin p-1.5 text-emerald-400 hover:text-emerald-300"><i class="fas fa-pen"></i></button>
                                                    <button class="btn-delete-walkin p-1.5 text-red-400 hover:text-red-300"><i class="fas fa-trash-alt"></i></button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div id="plans" class="tab-content bg-[#162032]">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-xl font-bold text-white">Membership Plans</h2>
                            <button id="btnAddPlan" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-lg shadow-blue-900/20 flex items-center">
                                <i class="fas fa-plus mr-2"></i> New Plan
                            </button>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <?php foreach($plans as $plan) { ?>
                                <div class="glass-panel p-6 rounded-xl relative overflow-hidden group hover-lift transition-all-300 border border-slate-700/50">
                                    <div class="absolute top-0 right-0 p-4 opacity-5">
                                        <i class="fas fa-dumbbell text-6xl"></i>
                                    </div>
                                    <div class="flex justify-between items-start mb-4 relative z-10">
                                        <div>
                                            <h3 class="text-xl font-bold text-white tracking-tight"><?= $plan['plan_name'] ?></h3>
                                            <p class="text-slate-400 text-xs uppercase tracking-wider mt-1">Membership</p>
                                        </div>
                                        <span class="px-2.5 py-1 text-xs font-bold rounded-full 
                                            <?php if($plan['status'] == 'active') { echo 'bg-green-500/20 text-green-400 border border-green-500/30'; } 
                                            else if($plan['status'] == 'inactive') { echo 'bg-yellow-500/20 text-yellow-400 border border-yellow-500/30'; } 
                                            else { echo 'bg-red-500/20 text-red-400 border border-red-500/30'; } ?>">
                                            <?= ucfirst($plan['status']) ?>
                                        </span>
                                    </div>
                                    
                                    <div class="mb-6 relative z-10">
                                        <div class="flex items-baseline">
                                            <span class="text-3xl font-bold text-white">₱<?= number_format($plan['price'], 0) ?></span>
                                            <span class="text-sm text-slate-500 ml-1">/mo</span>
                                        </div>
                                        <p class="text-slate-400 text-sm mt-3 line-clamp-2 min-h-[40px]"><?= $plan['description'] ?></p>
                                    </div>

                                    <div class="flex gap-2 relative z-10 pt-4 border-t border-slate-700/50">
                                        <button class="btn-edit-plan flex-1 py-2 bg-slate-700 hover:bg-slate-600 text-white text-sm font-medium rounded transition-colors" data-plan-id="<?= $plan['plan_id'] ?>">Edit</button>
                                        <button class="btn-delete-plan flex-1 py-2 bg-red-600/20 hover:bg-red-600/30 text-red-400 border border-red-600/30 hover:border-red-500 text-sm font-medium rounded transition-colors" data-plan-id="<?= $plan['plan_id'] ?>">Delete</button>
                                    </div>
                                </div>    
                            <?php } ?>
                        </div>
                    </div>
                </div>

                <div id="payments" class="tab-content bg-[#162032]">
                    <div class="p-6">
                        <div class="flex flex-col xl:flex-row justify-between gap-4 mb-6">
                            <h2 class="text-xl font-bold text-white flex items-center"><i class="fas fa-history mr-2 text-slate-500"></i> Transaction History</h2>
                            
                            <div class="flex flex-col sm:flex-row gap-3 flex-1 xl:justify-end">
                                <div class="relative flex-1 max-w-md">
                                    <i class="fas fa-search absolute left-3 top-3 text-slate-500"></i>
                                    <input type="text" id="searchPayments" placeholder="Search transaction ID..." class="w-full pl-10 pr-4 py-2.5 bg-slate-800 text-slate-200 border border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                                </div>
                                <select class="px-4 py-2.5 bg-slate-800 text-slate-200 border border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                                    <option value="">Status</option>
                                    <option value="paid">Paid</option>
                                    <option value="pending">Pending</option>
                                </select>
                            </div>
                        </div>

                        <div class="overflow-x-auto rounded-lg border border-slate-700">
                            <table class="w-full text-left custom-table">
                                <thead>
                                    <tr>
                                        <th class="p-4 rounded-tl-lg">Member</th>
                                        <th class="p-4">Txn ID</th>
                                        <th class="p-4">Amount</th>
                                        <th class="p-4">Date</th>
                                        <th class="p-4">Status</th>
                                        <th class="p-4 text-center rounded-tr-lg">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($paymentDetails as $paymentDetail) { 
                                        $user_name = $paymentDetail['name'];
                                        $user_initial = substr($user_name, 0, 1); 
                                    ?>
                                        <tr class="table-row">
                                            <td class="p-4">
                                                <div class="flex items-center">
                                                    <div class="w-8 h-8 bg-slate-700 rounded-full flex items-center justify-center text-xs text-white mr-3">
                                                        <?= $user_initial ?>
                                                    </div>
                                                    <span class="text-white text-sm"><?= $paymentDetail['name'] ?></span>
                                                </div>
                                            </td>
                                            <td class="p-4 text-xs font-mono text-slate-400"><?= $paymentDetail['transaction_id'] ?? "—" ?></td>
                                            <td class="p-4 text-sm font-bold text-slate-200">₱<?= number_format($paymentDetail['amount'], 2) ?></td>
                                            <td class="p-4 text-sm text-slate-400"><?= date('M d, Y', strtotime($paymentDetail['payment_date'])) ?></td>
                                            <td class="p-4">
                                                <span class="status-badge <?= $paymentDetail['status'] == 'paid' ? 'status-paid' : 'status-pending' ?>">
                                                    <?= ucfirst($paymentDetail['status']) ?>
                                                </span>
                                            </td>
                                            <td class="p-4 text-center">
                                                <div class="flex items-center justify-center gap-2">
                                                    <button class="btn-view-payment text-slate-400 hover:text-white transition-colors" title="View" data-payment-id="<?= $paymentDetail['payment_id'] ?>"><i class="fas fa-file-invoice"></i></button>
                                                    <?php if($paymentDetail['status'] == 'pending') { ?>
                                                        <button class="btn-remind-payment text-orange-400 hover:text-orange-300 transition-colors" title="Send Reminder" data-payment-id="<?= $paymentDetail['payment_id'] ?>"><i class="fas fa-bell"></i></button>
                                                    <?php } else if($paymentDetail['status'] == 'paid') { ?>
                                                        <button class="btn-refund-payment text-red-400 hover:text-red-300 transition-colors" title="Refund" data-payment-id="<?= $paymentDetail['payment_id'] ?>"><i class="fas fa-undo"></i></button>
                                                    <?php } ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div id="pending" class="tab-content bg-[#162032]">
                    <div class="p-6">
                        <div class="flex flex-col xl:flex-row justify-between gap-4 mb-6">
                            <h2 class="text-xl font-bold text-white flex items-center"><i class="fas fa-user-clock mr-2 text-slate-500"></i> Pending Registrations</h2>
                            
                            <div class="flex flex-col sm:flex-row gap-3 flex-1 xl:justify-end">
                                <button id="btnRefreshPending" class="px-4 py-2.5 bg-slate-700 hover:bg-slate-600 text-white text-sm font-medium rounded-lg transition-colors border border-slate-600">
                                    <i class="fas fa-sync-alt mr-2"></i> Refresh
                                </button>
                            </div>
                        </div>

                        <div class="overflow-x-auto rounded-lg border border-slate-700">
                            <table class="w-full text-left custom-table" id="pendingUsersTable">
                                <thead>
                                    <tr>
                                        <th class="p-4 rounded-tl-lg">Name</th>
                                        <th class="p-4">Email</th>
                                        <th class="p-4">Registered Date</th>
                                        <th class="p-4">ID Proof</th>
                                        <th class="p-4 text-center rounded-tr-lg">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="pendingUsersTableBody">
                                    <!-- Populated via AJAX -->
                                    <tr class="text-slate-500 text-sm text-center">
                                        <td colspan="5" class="p-6">Loading pending registrations...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div> </main>
    </div>

    <div id="addMemberModal" class="modal-backdrop fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="modal-content w-full max-w-2xl rounded-2xl shadow-2xl overflow-hidden max-h-[90vh] flex flex-col">
            <div class="p-6 border-b border-slate-700 flex justify-between items-center bg-slate-800">
                <h3 class="text-xl font-bold text-white">Add New Member</h3>
                <button class="add-member-close text-slate-400 hover:text-white transition-colors text-xl">&times;</button>
            </div>
            
            <div class="p-6 overflow-y-auto custom-scrollbar">
                <form id="addMemberForm" method="POST" action="add_member.php" class="space-y-6">
                    <div class="space-y-4">
                        <h4 class="text-sm font-semibold text-blue-400 uppercase tracking-wider">Personal Information</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-slate-400 mb-1">First Name *</label>
                                <input type="text" name="first_name" required class="w-full px-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white focus:border-blue-500 focus:outline-none transition-colors" placeholder="John">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-400 mb-1">Last Name *</label>
                                <input type="text" name="last_name" required class="w-full px-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white focus:border-blue-500 focus:outline-none transition-colors" placeholder="Doe">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-400 mb-1">Email *</label>
                            <input type="email" name="email" required class="w-full px-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white focus:border-blue-500 focus:outline-none transition-colors" placeholder="john.doe@example.com">
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-slate-400 mb-1">Phone Number *</label>
                                <input type="tel" name="phone" required class="w-full px-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white focus:border-blue-500 focus:outline-none transition-colors" placeholder="+63 9XX XXX XXXX">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-400 mb-1">Date of Birth</label>
                                <input type="date" name="date_of_birth" class="w-full px-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white focus:border-blue-500 focus:outline-none transition-colors">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-400 mb-1">Address</label>
                            <textarea name="address" rows="2" class="w-full px-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white focus:border-blue-500 focus:outline-none transition-colors resize-none"></textarea>
                        </div>
                    </div>

                    <div class="space-y-4 pt-4 border-t border-slate-700">
                        <h4 class="text-sm font-semibold text-blue-400 uppercase tracking-wider">Membership Details</h4>
                        <div>
                            <label class="block text-xs font-medium text-slate-400 mb-1">Select Plan *</label>
                            <select name="plan_id" required class="w-full px-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white focus:border-blue-500 focus:outline-none transition-colors">
                                <option value="">Choose a plan</option>
                                </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-400 mb-1">Start Date *</label>
                            <input type="date" name="start_date" required class="w-full px-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white focus:border-blue-500 focus:outline-none transition-colors">
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" id="startTrial" name="start_trial" class="w-4 h-4 rounded border-slate-600 bg-slate-700 text-blue-600 focus:ring-blue-500">
                            <label for="startTrial" class="ml-2 text-sm text-slate-300">Start with 3-day free trial</label>
                        </div>
                    </div>

                    <div class="space-y-4 pt-4 border-t border-slate-700">
                        <h4 class="text-sm font-semibold text-blue-400 uppercase tracking-wider">Security</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-slate-400 mb-1">Password *</label>
                                <input type="password" name="password" required class="w-full px-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white focus:border-blue-500 focus:outline-none transition-colors">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-400 mb-1">Confirm Password *</label>
                                <input type="password" name="confirm_password" required class="w-full px-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white focus:border-blue-500 focus:outline-none transition-colors">
                            </div>
                        </div>
                    </div>

                    <div id="addMemberMessage" class="hidden text-sm text-center py-2 rounded bg-red-500/10 text-red-400 border border-red-500/20"></div>

                    <div class="flex gap-4 pt-2">
                        <button type="button" class="add-member-cancel flex-1 py-3 px-4 bg-slate-700 hover:bg-slate-600 text-white font-medium rounded-lg transition-colors">Cancel</button>
                        <button type="submit" id="btnSubmitMember" class="flex-1 py-3 px-4 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors shadow-lg shadow-blue-900/20">Add Member</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="addWalkInModal" class="modal-backdrop fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="modal-content w-full max-w-3xl rounded-2xl shadow-2xl overflow-hidden max-h-[90vh] flex flex-col">
            <div class="p-6 border-b border-slate-700 flex justify-between items-center bg-slate-800">
                <h3 class="text-xl font-bold text-white flex items-center"><i class="fas fa-walking mr-2 text-emerald-500"></i> Add Walk-in</h3>
                <button class="walkin-close text-slate-400 hover:text-white transition-colors text-xl">&times;</button>
            </div>
            
            <div class="p-6 overflow-y-auto custom-scrollbar">
                <div class="bg-emerald-500/10 border border-emerald-500/20 rounded-lg p-3 mb-6 flex items-start">
                    <i class="fas fa-info-circle text-emerald-400 mt-0.5 mr-3"></i>
                    <p class="text-emerald-200 text-sm">Walk-in members pay per session and do not require a subscription plan.</p>
                </div>

                <form id="addWalkInForm" method="POST" action="index.php?controller=Admin&action=validateWalkin" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-medium text-slate-400 mb-1">First Name *</label>
                                <input type="text" name="first_name" required class="w-full px-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white focus:border-emerald-500 focus:outline-none transition-colors">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-400 mb-1">Middle Name</label>
                                <input type="text" name="middle_name" class="w-full px-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white focus:border-emerald-500 focus:outline-none transition-colors">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-400 mb-1">Last Name *</label>
                                <input type="text" name="last_name" required class="w-full px-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white focus:border-emerald-500 focus:outline-none transition-colors">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-400 mb-1">Contact Number *</label>
                                <input type="tel" name="contact_no" required class="w-full px-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white focus:border-emerald-500 focus:outline-none transition-colors">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-400 mb-1">Email</label>
                                <input type="email" name="email" class="w-full px-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white focus:border-emerald-500 focus:outline-none transition-colors">
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-medium text-slate-400 mb-1">Session Type *</label>
                                <select name="session_type" id="sessionType" required class="w-full px-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white focus:border-emerald-500 focus:outline-none transition-colors">
                                    <option value="">Select session type</option>
                                    <option value="single" data-price="20">Session Day Pass - ₱20</option>
                                    <option value="day_pass" data-price="60">Basic Pass - ₱60</option>
                                    <option value="weekend" data-price="200">Premium Day Pass - ₱200</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-400 mb-1">Payment Amount</label>
                                <input type="number" name="payment_amount" id="paymentAmount" required readonly class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-lg text-emerald-400 font-bold focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-400 mb-1">Visit Date *</label>
                                <input type="datetime-local" name="visit_time" id="visitTime" required class="w-full px-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white focus:border-emerald-500 focus:outline-none transition-colors">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-400 mb-1">End Date/Time</label>
                                <input type="datetime-local" name="end_date" id="endDate" required readonly class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-lg text-slate-400 focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-400 mb-1">Payment Method *</label>
                                <select name="payment_method" required class="w-full px-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white focus:border-emerald-500 focus:outline-none transition-colors">
                                    <option value="">Select payment method</option>
                                    <option value="cash">Cash</option>
                                    <option value="card">Credit/Debit Card</option>
                                    <option value="gcash">GCash</option>
                                    <option value="paymaya">PayMaya</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div id="walkInMessage" class="hidden text-sm text-center py-2 rounded bg-red-500/10 text-red-400"></div>

                    <div class="flex gap-4 pt-2">
                        <button type="button" class="walkin-cancel flex-1 py-3 px-4 bg-slate-700 hover:bg-slate-600 text-white font-medium rounded-lg transition-colors">Cancel</button>
                        <button type="submit" id="btnSubmitWalkIn" class="flex-1 py-3 px-4 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg transition-colors shadow-lg shadow-emerald-900/20">Register Walk-in</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="viewWalkinModal" class="modal-backdrop fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/80 backdrop-blur-sm transition-opacity duration-300">
        <div class="modal-content w-full max-w-lg bg-slate-800 rounded-2xl shadow-2xl border border-slate-700/50 transform transition-all scale-100 overflow-hidden">
            <div class="flex justify-between items-center px-6 py-5 border-b border-slate-700/50 bg-slate-800/50">
                <h3 class="text-xl font-bold text-white flex items-center gap-3">
                    <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-400">
                        <i class="fa-solid fa-walking"></i>
                    </span>
                    Walk-in Details
                </h3>
                <button class="view-walkin-close w-8 h-8 flex items-center justify-center rounded-full text-slate-400 hover:text-white hover:bg-slate-700/50 transition-all duration-200">
                    <i class="fa-solid fa-times text-lg"></i>
                </button>
            </div>
            <div class="p-6">
                <div id="walkinDetails" class="space-y-4 text-slate-300 bg-slate-900/30 rounded-xl p-5 border border-slate-700/30">
                    </div>
                <div class="flex gap-4 mt-8 pt-2">
                    <button type="button" class="view-walkin-close flex-1 py-2.5 px-4 bg-slate-700/50 hover:bg-slate-700 border border-slate-600 rounded-lg text-slate-200 font-medium transition-all duration-200">
                        Close
                    </button>
                    <button type="button" id="btnEditWalkinFromView" class="flex-1 py-2.5 px-4 bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-500 hover:to-emerald-400 rounded-lg text-white font-medium shadow-lg shadow-emerald-900/20 transition-all duration-200 flex items-center justify-center gap-2">
                        <i class="fa-solid fa-pen text-sm"></i> Edit Details
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="editWalkinModal" class="modal-backdrop fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="modal-content w-full max-w-3xl rounded-2xl shadow-2xl overflow-hidden max-h-[90vh] flex flex-col">
            <div class="p-6 border-b border-slate-700 flex justify-between items-center bg-slate-800">
                <h3 class="text-xl font-bold text-white flex items-center"><i class="fas fa-edit mr-2 text-emerald-500"></i> Edit Walk-in</h3>
                <button class="edit-walkin-close text-slate-400 hover:text-white transition-colors text-xl">&times;</button>
            </div>
            
            <div class="p-6 overflow-y-auto custom-scrollbar">
                <form id="editWalkinForm" method="POST" action="index.php?controller=User&action=updateWalkin" class="space-y-6">
                    <input type="hidden" name="walkin_id" id="edit_walkin_id">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-medium text-slate-400 mb-1">First Name *</label>
                                <input type="text" name="first_name" id="edit_walkin_first_name" required class="w-full px-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white focus:border-emerald-500 focus:outline-none transition-colors">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-400 mb-1">Middle Name</label>
                                <input type="text" name="middle_name" id="edit_walkin_middle_name" class="w-full px-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white focus:border-emerald-500 focus:outline-none transition-colors">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-400 mb-1">Last Name *</label>
                                <input type="text" name="last_name" id="edit_walkin_last_name" required class="w-full px-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white focus:border-emerald-500 focus:outline-none transition-colors">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-400 mb-1">Contact Number *</label>
                                <input type="tel" name="contact_no" id="edit_walkin_contact_no" required class="w-full px-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white focus:border-emerald-500 focus:outline-none transition-colors">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-400 mb-1">Email</label>
                                <input type="email" name="email" id="edit_walkin_email" class="w-full px-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white focus:border-emerald-500 focus:outline-none transition-colors">
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-medium text-slate-400 mb-1">Session Type *</label>
                                <select name="session_type" id="edit_walkin_session_type" required class="w-full px-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white focus:border-emerald-500 focus:outline-none transition-colors">
                                    <option value="single">Session Day Pass</option>
                                    <option value="day_pass">Basic Pass</option>
                                    <option value="weekend">Premium Day Pass</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-400 mb-1">Payment Amount</label>
                                <input type="number" name="payment_amount" id="edit_walkin_payment_amount" required class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-lg text-emerald-400 font-bold focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-400 mb-1">Visit Date *</label>
                                <input type="datetime-local" name="visit_time" id="edit_walkin_visit_time" required class="w-full px-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white focus:border-emerald-500 focus:outline-none transition-colors">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-400 mb-1">End Date/Time</label>
                                <input type="datetime-local" name="end_date" id="edit_walkin_end_date" required class="w-full px-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white focus:border-emerald-500 focus:outline-none transition-colors">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-400 mb-1">Payment Method *</label>
                                <select name="payment_method" id="edit_walkin_payment_method" required class="w-full px-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white focus:border-emerald-500 focus:outline-none transition-colors">
                                    <option value="cash">Cash</option>
                                    <option value="card">Credit/Debit Card</option>
                                    <option value="gcash">GCash</option>
                                    <option value="paymaya">PayMaya</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div id="editWalkinMessage" class="hidden text-sm text-center py-2 rounded bg-red-500/10 text-red-400"></div>

                    <div class="flex gap-4 pt-2">
                        <button type="button" class="edit-walkin-cancel flex-1 py-3 px-4 bg-slate-700 hover:bg-slate-600 text-white font-medium rounded-lg transition-colors">Cancel</button>
                        <button type="submit" id="btnUpdateWalkin" class="flex-1 py-3 px-4 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg transition-colors shadow-lg shadow-emerald-900/20">Update Walk-in</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="memberModal" class="modal-backdrop fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/80 backdrop-blur-sm transition-opacity duration-300">
    
        <div class="modal-content w-full max-w-lg bg-slate-800 rounded-2xl shadow-2xl border border-slate-700/50 transform transition-all scale-100 overflow-hidden">
            
            <div class="flex justify-between items-center px-6 py-5 border-b border-slate-700/50 bg-slate-800/50">
                <h3 class="text-xl font-bold text-white flex items-center gap-3">
                    <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-blue-500/10 text-blue-400">
                        <i class="fa-solid fa-user"></i>
                    </span>
                    Member Details
                </h3>
                
                <button class="member-modal-close w-8 h-8 flex items-center justify-center rounded-full text-slate-400 hover:text-white hover:bg-slate-700/50 transition-all duration-200">
                    <i class="fa-solid fa-times text-lg"></i>
                </button>
            </div>

            <div class="p-6">
                <div id="memberDetails" class="space-y-4 text-slate-300 bg-slate-900/30 rounded-xl p-5 border border-slate-700/30">
                    </div>

                <div class="flex gap-4 mt-8 pt-2">
                    <button type="button" class="member-modal-close flex-1 py-2.5 px-4 bg-slate-700/50 hover:bg-slate-700 border border-slate-600 rounded-lg text-slate-200 font-medium transition-all duration-200 focus:ring-2 focus:ring-slate-500 focus:ring-offset-2 focus:ring-offset-slate-800">
                        Close
                    </button>
                    
                    <button type="button" id="btnEditMemberFromView" class="flex-1 py-2.5 px-4 bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-500 hover:to-blue-400 rounded-lg text-white font-medium shadow-lg shadow-blue-900/20 transition-all duration-200 flex items-center justify-center gap-2 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-slate-800">
                        <i class="fa-solid fa-user-pen text-sm"></i> Edit Member
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="editMemberModal" class="modal-backdrop fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="modal-content w-full max-w-2xl rounded-2xl shadow-2xl overflow-hidden max-h-[90vh] flex flex-col">
            <div class="p-6 border-b border-slate-700 flex justify-between items-center bg-slate-800">
                <h3 class="text-xl font-bold text-white">Edit Member</h3>
                <button class="edit-member-close text-slate-400 hover:text-white transition-colors text-xl">&times;</button>
            </div>
            <div class="p-6 overflow-y-auto custom-scrollbar">
                <form id="editMemberForm" method="POST" action="index.php?controller=Admin&action=updateMember" class="space-y-4">
                    <input type="hidden" name="user_id" id="edit_user_id">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-slate-400 mb-1">First Name</label>
                            <input type="text" name="first_name" id="edit_first_name" required class="w-full px-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-400 mb-1">Last Name</label>
                            <input type="text" name="last_name" id="edit_last_name" required class="w-full px-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white focus:border-blue-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1">Middle Name</label>
                        <input type="text" name="middle_name" id="edit_middle_name" class="w-full px-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1">Email</label>
                        <input type="email" name="email" id="edit_email" required class="w-full px-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white focus:border-blue-500">
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-slate-400 mb-1">Role</label>
                            <select name="role" id="edit_role" required class="w-full px-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white focus:border-blue-500">
                                <option value="member">Member</option>
                                <option value="admin">Admin</option>
                                <option value="trainer">Trainer</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-400 mb-1">Status</label>
                            <select name="status" id="edit_status" required class="w-full px-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white focus:border-blue-500">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="pt-4 border-t border-slate-700">
                        <p class="text-xs text-slate-500 mb-3">Change Password (leave blank to keep current)</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <input type="password" name="password" id="edit_password" placeholder="New Password" class="w-full px-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white focus:border-blue-500">
                            <input type="password" name="confirm_password" id="edit_confirm_password" placeholder="Confirm" class="w-full px-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white focus:border-blue-500">
                        </div>
                    </div>

                    <div id="editMemberMessage" class="hidden text-red-400 text-sm"></div>

                    <div class="flex gap-4 pt-2">
                        <button type="button" class="edit-member-cancel flex-1 py-2 bg-slate-700 hover:bg-slate-600 rounded-lg text-white">Cancel</button>
                        <button type="submit" id="btnUpdateMember" class="flex-1 py-2 bg-blue-600 hover:bg-blue-700 rounded-lg text-white">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="deleteMemberModal" class="modal-backdrop fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="modal-content w-full max-w-md rounded-2xl shadow-2xl p-6 bg-slate-800 border border-red-500/30">
            <button class="delete-modal-close float-right text-slate-400 hover:text-white text-xl">&times;</button>
            <div class="text-center pt-4">
                <i class="fas fa-exclamation-triangle text-4xl text-red-500 mb-4"></i>
                <h3 class="text-xl font-bold text-white mb-2">Delete Member?</h3>
                <p class="text-slate-400 mb-6">This action cannot be undone. Are you sure?</p>
                
                <form id="deleteForm" method="POST" action="index.php?controller=Admin&action=deleteMember">
                    <input type="hidden" name="user_id" id="delete_user_id">
                    <div id="deleteMemberMessage" class="hidden mb-4"></div>
                    <div class="flex gap-3">
                        <button type="button" class="delete-modal-close flex-1 py-2 bg-slate-700 hover:bg-slate-600 rounded-lg text-white">Cancel</button>
                        <button type="submit" id="deleteBtn" class="flex-1 py-2 bg-red-600 hover:bg-red-700 rounded-lg text-white font-medium">Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="addTrainerModal" class="modal-backdrop fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="modal-content w-full max-w-2xl rounded-2xl shadow-2xl overflow-hidden max-h-[90vh] flex flex-col">
            <div class="p-6 border-b border-slate-700 flex justify-between items-center bg-slate-800">
                <h3 class="text-xl font-bold text-white">Add New Trainer</h3>
                <button class="add-trainer-close text-slate-400 hover:text-white transition-colors text-xl">&times;</button>
            </div>
            
            <div class="p-6 overflow-y-auto custom-scrollbar">
                <form id="addTrainerForm" method="POST" action="index.php?controller=Admin&action=addTrainer" class="space-y-6">
                    <div class="space-y-4">
                        <h4 class="text-sm font-semibold text-purple-400 uppercase tracking-wider">Trainer Info</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-slate-400 mb-1">First Name *</label>
                                <input type="text" name="first_name" required class="w-full px-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white focus:border-purple-500 focus:outline-none transition-colors">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-400 mb-1">Last Name *</label>
                                <input type="text" name="last_name" required class="w-full px-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white focus:border-purple-500 focus:outline-none transition-colors">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-400 mb-1">Middle Name</label>
                            <input type="text" name="middle_name" class="w-full px-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white focus:border-purple-500 focus:outline-none transition-colors">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-400 mb-1">Email *</label>
                            <input type="email" name="email" required class="w-full px-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white focus:border-purple-500 focus:outline-none transition-colors">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-400 mb-1">Contact Number *</label>
                            <input type="tel" name="contact_no" required class="w-full px-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white focus:border-purple-500 focus:outline-none transition-colors">
                        </div>
                    </div>

                    <div class="space-y-4 pt-4 border-t border-slate-700">
                        <h4 class="text-sm font-semibold text-purple-400 uppercase tracking-wider">Expertise</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-slate-400 mb-1">Specialization *</label>
                                <select name="specialization" required class="w-full px-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white focus:border-purple-500 focus:outline-none transition-colors">
                                    <option value="">Select</option>
                                    <option value="Weight Training">Weight Training</option>
                                    <option value="Cardio">Cardio</option>
                                    <option value="CrossFit">CrossFit</option>
                                    <option value="Yoga">Yoga</option>
                                    <option value="Pilates">Pilates</option>
                                    <option value="Boxing">Boxing</option>
                                    <option value="Personal Training">Personal Training</option>
                                    <option value="Nutrition">Nutrition</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-400 mb-1">Experience (Years) *</label>
                                <input type="number" name="experience_years" required min="0" max="50" class="w-full px-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white focus:border-purple-500 focus:outline-none transition-colors">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-400 mb-1">Bio / Description</label>
                            <textarea name="bio" rows="3" class="w-full px-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white focus:border-purple-500 focus:outline-none transition-colors resize-none"></textarea>
                        </div>
                    </div>

                    <div class="space-y-4 pt-4 border-t border-slate-700">
                        <h4 class="text-sm font-semibold text-purple-400 uppercase tracking-wider">Security</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-slate-400 mb-1">Password *</label>
                                <input type="password" name="password" required class="w-full px-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white focus:border-purple-500 focus:outline-none transition-colors">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-400 mb-1">Confirm Password *</label>
                                <input type="password" name="confirm_password" required class="w-full px-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white focus:border-purple-500 focus:outline-none transition-colors">
                            </div>
                        </div>
                    </div>

                    <div id="addTrainerMessage" class="hidden text-red-400 text-sm"></div>

                    <div class="flex gap-4 pt-2">
                        <button type="button" class="add-trainer-cancel flex-1 py-3 px-4 bg-slate-700 hover:bg-slate-600 text-white font-medium rounded-lg transition-colors">Cancel</button>
                        <button type="submit" id="btnAddTrainer" class="flex-1 py-3 px-4 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-lg transition-colors shadow-lg shadow-purple-900/20">Add Trainer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="viewTrainerModal" class="modal-backdrop fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/80 backdrop-blur-sm transition-opacity duration-300">
    
        <div class="modal-content w-full max-w-lg bg-slate-800 rounded-2xl shadow-2xl border border-slate-700/50 transform transition-all scale-100 overflow-hidden">
            
            <div class="flex justify-between items-center px-6 py-5 border-b border-slate-700/50 bg-slate-800/50">
                <h3 class="text-xl font-bold text-white flex items-center gap-3">
                    <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-blue-500/10 text-blue-400">
                        <i class="fa-solid fa-id-card"></i>
                    </span>
                    Trainer Details
                </h3>
                
                <button class="view-trainer-close w-8 h-8 flex items-center justify-center rounded-full text-slate-400 hover:text-white hover:bg-slate-700/50 transition-all duration-200">
                    <i class="fa-solid fa-times text-lg"></i>
                </button>
            </div>

            <div class="p-6">
                <div id="trainerDetails" class="space-y-4 text-slate-300 bg-slate-900/30 rounded-xl p-5 border border-slate-700/30">
                    </div>

                <div class="flex gap-4 mt-8 pt-2">
                    <button type="button" class="view-trainer-close flex-1 py-2.5 px-4 bg-slate-700/50 hover:bg-slate-700 border border-slate-600 rounded-lg text-slate-200 font-medium transition-all duration-200 focus:ring-2 focus:ring-slate-500 focus:ring-offset-2 focus:ring-offset-slate-800">
                        Close
                    </button>
                    
                    <button type="button" id="btnEditTrainer" class="flex-1 py-2.5 px-4 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500 rounded-lg text-white font-medium shadow-lg shadow-purple-900/20 transition-all duration-200 flex items-center justify-center gap-2 focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 focus:ring-offset-slate-800">
                        <i class="fa-solid fa-pen-to-square text-sm"></i> Edit Trainer
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="editTrainerModal" class="modal-backdrop fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="modal-content w-full max-w-2xl rounded-2xl shadow-2xl overflow-hidden max-h-[90vh] flex flex-col">
            <div class="p-6 border-b border-slate-700 flex justify-between items-center bg-slate-800">
                <h3 class="text-xl font-bold text-white">Edit Trainer</h3>
                <button class="edit-trainer-close text-slate-400 hover:text-white transition-colors text-xl">&times;</button>
            </div>
            <div class="p-6 overflow-y-auto custom-scrollbar">
                <form id="editTrainerForm" method="POST" action="index.php?controller=Admin&action=updateTrainer" class="space-y-4">
                    <input type="hidden" name="trainer_id" id="edit_trainer_id">
                    <input type="hidden" name="user_trainer_id" id="edit_user_trainer_id">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-slate-400 mb-1">First Name</label>
                            <input type="text" name="trainer_first_name" id="edit_trainer_first_name" required class="w-full px-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white focus:border-purple-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-400 mb-1">Last Name</label>
                            <input type="text" name="trainer_last_name" id="edit_trainer_last_name" required class="w-full px-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white focus:border-purple-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1">Middle Name</label>
                        <input type="text" name="trainer_middle_name" id="edit_trainer_middle_name" class="w-full px-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white focus:border-purple-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1">Email</label>
                        <input type="email" name="trainer_email" id="edit_trainer_email" required class="w-full px-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white focus:border-purple-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1">Contact</label>
                        <input type="tel" name="trainer_contact_no" id="edit_trainer_contact_no" required class="w-full px-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white focus:border-purple-500">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-slate-400 mb-1">Specialization</label>
                            <select name="specialization" id="edit_specialization" required class="w-full px-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white focus:border-purple-500">
                                <option value="Weight Training">Weight Training</option>
                                <option value="Cardio">Cardio</option>
                                <option value="CrossFit">CrossFit</option>
                                <option value="Yoga">Yoga</option>
                                <option value="Pilates">Pilates</option>
                                <option value="Boxing">Boxing</option>
                                <option value="Personal Training">Personal Training</option>
                                <option value="Nutrition">Nutrition</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-400 mb-1">Experience</label>
                            <input type="number" name="experience_years" id="edit_experience_years" required min="0" max="50" class="w-full px-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white focus:border-purple-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1">Status</label>
                        <select name="trainer_status" id="edit_trainer_status" required class="w-full px-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white focus:border-purple-500">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>

                    <div class="pt-4 border-t border-slate-700">
                        <p class="text-xs text-slate-500 mb-3">Change Password (leave blank to keep current)</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <input type="password" name="trainer_password" id="edit_trainer_password" placeholder="New Password" class="w-full px-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white focus:border-purple-500">
                            <input type="password" name="confirm_trainer_password" id="edit_confirm_trainer_password" placeholder="Confirm" class="w-full px-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white focus:border-purple-500">
                        </div>
                    </div>

                    <div id="editTrainerMessage" class="hidden text-red-400 text-sm"></div>

                    <div class="flex gap-4 pt-2">
                        <button type="button" class="edit-trainer-cancel flex-1 py-2 bg-slate-700 hover:bg-slate-600 rounded-lg text-white">Cancel</button>
                        <button type="submit" id="btnUpdateTrainer" class="flex-1 py-2 bg-purple-600 hover:bg-purple-700 rounded-lg text-white">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="deleteTrainerModal" class="modal-backdrop fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="modal-content w-full max-w-md rounded-2xl shadow-2xl p-6 bg-slate-800 border border-red-500/30">
            <button class="delete-trainer-modal-close float-right text-slate-400 hover:text-white text-xl">&times;</button>
            <div class="text-center pt-4">
                <i class="fas fa-user-slash text-4xl text-red-500 mb-4"></i>
                <h3 class="text-xl font-bold text-white mb-2">Delete Trainer?</h3>
                <p class="text-slate-400 mb-6">This action cannot be undone.</p>
                <form id="deleteTrainerForm" method="POST" action="index.php?controller=Admin&action=deleteMember">
                    <input type="hidden" name="trainer_id" id="delete_trainer_id">
                    <div id="deleteTrainerMessage" class="hidden mb-4"></div>
                    <div class="flex gap-3">
                        <button type="button" class="delete-trainer-cancel flex-1 py-2 bg-slate-700 hover:bg-slate-600 rounded-lg text-white">Cancel</button>
                        <button type="submit" id="deleteTrainerBtn" class="flex-1 py-2 bg-red-600 hover:bg-red-700 rounded-lg text-white font-medium">Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div id="addPlanModal" class="modal-backdrop fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="modal-content w-full max-w-lg rounded-2xl shadow-2xl overflow-hidden flex flex-col">
            <div class="p-6 border-b border-slate-700 flex justify-between items-center bg-slate-800">
                <h3 class="text-xl font-bold text-white flex items-center"><i class="fas fa-plus-circle mr-2 text-blue-500"></i> Add New Plan</h3>
                <button class="add-plan-close text-slate-400 hover:text-white transition-colors text-xl">&times;</button>
            </div>
            
            <div class="p-6">
                <form id="addPlanForm" method="POST" action="index.php?controller=Plan&action=addPlan" class="space-y-5">
                    
                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1">Plan Name *</label>
                        <input type="text" name="plan_name" required placeholder="e.g. Gold Membership" class="w-full px-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white focus:border-blue-500 focus:outline-none transition-colors">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-slate-400 mb-1">Price (₱) *</label>
                            <input type="number" step="0.01" name="price" required placeholder="0.00" class="w-full px-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white focus:border-blue-500 focus:outline-none transition-colors">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-400 mb-1">Duration (Months) *</label>
                            <input type="number" name="duration_months" required placeholder="1" class="w-full px-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white focus:border-blue-500 focus:outline-none transition-colors">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1">Description</label>
                        <textarea name="description" rows="3" placeholder="Plan details..." class="w-full px-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white focus:border-blue-500 focus:outline-none transition-colors resize-none"></textarea>
                    </div>

                    <div id="addPlanMessage" class="hidden text-sm text-center py-2 rounded bg-red-500/10 text-red-400"></div>

                    <div class="flex gap-4 pt-2">
                        <button type="button" class="add-plan-cancel flex-1 py-3 px-4 bg-slate-700 hover:bg-slate-600 text-white font-medium rounded-lg transition-colors">Cancel</button>
                        <button type="submit" id="btnAddPlanSubmit" class="flex-1 py-3 px-4 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors shadow-lg shadow-blue-900/20">Create Plan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div id="editPlanModal" class="modal-backdrop fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="modal-content w-full max-w-lg rounded-2xl shadow-2xl overflow-hidden flex flex-col">
            <div class="p-6 border-b border-slate-700 flex justify-between items-center bg-slate-800">
                <h3 class="text-xl font-bold text-white flex items-center"><i class="fas fa-edit mr-2 text-blue-500"></i> Edit Plan</h3>
                <button class="edit-plan-close text-slate-400 hover:text-white transition-colors text-xl">&times;</button>
            </div>
            
            <div class="p-6">
                <form id="editPlanForm" method="POST" action="index.php?controller=Plan&action=updatePlan" class="space-y-5">
                    <input type="hidden" name="plan_id" id="edit_plan_id">
                    
                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1">Plan Name *</label>
                        <input type="text" name="plan_name" id="edit_plan_name" required class="w-full px-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white focus:border-blue-500 focus:outline-none transition-colors">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-slate-400 mb-1">Price (₱) *</label>
                            <input type="number" step="0.01" name="price" id="edit_plan_price" required class="w-full px-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white focus:border-blue-500 focus:outline-none transition-colors">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-400 mb-1">Duration (Months) *</label>
                            <input type="number" name="duration_months" id="edit_plan_duration" required class="w-full px-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white focus:border-blue-500 focus:outline-none transition-colors">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1">Description</label>
                        <textarea name="description" id="edit_plan_description" rows="3" class="w-full px-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white focus:border-blue-500 focus:outline-none transition-colors resize-none"></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1">Status</label>
                        <select name="status" id="edit_plan_status" required class="w-full px-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white focus:border-blue-500 focus:outline-none transition-colors">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="removed">Removed</option>
                        </select>
                    </div>

                    <div id="editPlanMessage" class="hidden text-sm text-center py-2 rounded bg-red-500/10 text-red-400"></div>

                    <div class="flex gap-4 pt-2">
                        <button type="button" class="edit-plan-cancel flex-1 py-3 px-4 bg-slate-700 hover:bg-slate-600 text-white font-medium rounded-lg transition-colors">Cancel</button>
                        <button type="submit" id="btnUpdatePlan" class="flex-1 py-3 px-4 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors shadow-lg shadow-blue-900/20">Update Plan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="../public/assets/js/admin/admin.js"></script>
    <div id="deleteWalkinModal" class="modal-backdrop fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="modal-content w-full max-w-md bg-slate-900 rounded-2xl shadow-2xl overflow-hidden border border-slate-700">
            <div class="p-6 text-center">
                <div class="w-16 h-16 bg-red-500/10 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-trash-alt text-2xl text-red-500"></i>
                </div>
                <h3 class="text-xl font-bold text-white mb-2">Delete Walk-in Record?</h3>
                <p class="text-slate-400 mb-6">This action cannot be undone.</p>
                <form id="deleteWalkinForm" method="POST">
                    <input type="hidden" name="walkin_id" id="delete_walkin_id">
                    <div id="deleteWalkinMessage" class="hidden mb-4"></div>
                    <div class="flex gap-3">
                        <button type="button" class="delete-walkin-cancel flex-1 py-2 bg-slate-700 hover:bg-slate-600 rounded-lg text-white">Cancel</button>
                        <button type="submit" id="deleteWalkinBtn" class="flex-1 py-2 bg-red-600 hover:bg-red-700 rounded-lg text-white font-medium">Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="deletePlanModal" class="modal-backdrop fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="modal-content w-full max-w-md bg-slate-900 rounded-2xl shadow-2xl overflow-hidden border border-slate-700">
            <div class="p-6 text-center">
                <div class="w-16 h-16 bg-red-500/10 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-trash-alt text-2xl text-red-500"></i>
                </div>
                <h3 class="text-xl font-bold text-white mb-2">Delete Plan?</h3>
                <p class="text-slate-400 mb-6">This action will mark the plan as removed.</p>
                <form id="deletePlanForm" method="POST">
                    <input type="hidden" name="plan_id" id="delete_plan_id">
                    <div id="deletePlanMessage" class="hidden mb-4"></div>
                    <div class="flex gap-3">
                        <button type="button" class="delete-plan-cancel flex-1 py-2 bg-slate-700 hover:bg-slate-600 rounded-lg text-white">Cancel</button>
                        <button type="submit" id="deletePlanBtn" class="flex-1 py-2 bg-red-600 hover:bg-red-700 rounded-lg text-white font-medium">Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div id="viewPaymentModal" class="modal-backdrop fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="modal-content w-full max-w-lg bg-slate-900 rounded-2xl shadow-2xl overflow-hidden border border-slate-700">
            <div class="p-6 border-b border-slate-800 flex justify-between items-center">
                <h3 class="text-xl font-bold text-white">Payment Details</h3>
                <button class="view-payment-close text-slate-400 hover:text-white">&times;</button>
            </div>
            <div class="p-6 space-y-4" id="viewPaymentContent">
                <!-- Content loaded via AJAX -->
                <div class="animate-pulse space-y-4">
                    <div class="h-4 bg-slate-800 rounded w-3/4"></div>
                    <div class="h-4 bg-slate-800 rounded w-1/2"></div>
                </div>
            </div>
            <div class="p-4 border-t border-slate-800 bg-slate-900/50 flex justify-end">
                <button class="view-payment-close px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white rounded-lg">Close</button>
            </div>
        </div>
    </div>

    <div id="refundPaymentModal" class="modal-backdrop fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="modal-content w-full max-w-md bg-slate-900 rounded-2xl shadow-2xl overflow-hidden border border-slate-700">
            <div class="p-6 text-center">
                <div class="w-16 h-16 bg-red-500/10 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-undo text-2xl text-red-500"></i>
                </div>
                <h3 class="text-xl font-bold text-white mb-2">Refund Payment?</h3>
                <p class="text-slate-400 mb-6">Are you sure you want to mark this transaction as refunded? This action cannot be undone.</p>
                <form id="refundPaymentForm" method="POST">
                    <input type="hidden" name="payment_id" id="refund_payment_id">
                    <div id="refundPaymentMessage" class="hidden mb-4"></div>
                    <div class="flex gap-3">
                        <button type="button" class="refund-payment-cancel flex-1 py-2 bg-slate-700 hover:bg-slate-600 rounded-lg text-white">Cancel</button>
                        <button type="submit" id="refundPaymentBtn" class="flex-1 py-2 bg-red-600 hover:bg-red-700 rounded-lg text-white font-medium">Refund</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div id="viewIdModal" class="modal-backdrop fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/80 backdrop-blur-sm transition-opacity duration-300">
        <div class="modal-content w-full max-w-lg bg-slate-800 rounded-2xl shadow-2xl border border-slate-700/50 transform transition-all scale-100 overflow-hidden">
            <div class="flex justify-between items-center px-6 py-5 border-b border-slate-700/50 bg-slate-800/50">
                <h3 class="text-xl font-bold text-white flex items-center gap-3">
                    <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-blue-500/10 text-blue-400">
                        <i class="fa-solid fa-id-card"></i>
                    </span>
                    ID Verification
                </h3>
                <button class="view-id-close w-8 h-8 flex items-center justify-center rounded-full text-slate-400 hover:text-white hover:bg-slate-700/50 transition-all duration-200">
                    <i class="fa-solid fa-times text-lg"></i>
                </button>
            </div>
            <div class="p-6">
                
                <div class="mb-6 flex justify-center bg-slate-900/50 rounded-lg p-2 border border-slate-700/30">
                    <img id="viewIdImage" src="" alt="Valid ID" class="max-h-[70vh] w-auto max-w-full object-contain rounded shadow-sm">
                </div>

                <div id="viewIdUserDetails" class="space-y-3 text-slate-300 bg-slate-900/30 rounded-xl p-5 border border-slate-700/30 text-sm">
                    <!-- Details here -->
                </div>

                <div class="flex gap-4 mt-6 pt-2">
                    <button type="button" class="view-id-close flex-1 py-2.5 px-4 bg-slate-700/50 hover:bg-slate-700 border border-slate-600 rounded-lg text-slate-200 font-medium transition-all duration-200">
                        Close
                    </button>
                    <button type="button" id="btnRejectUser" class="flex-1 py-2.5 px-4 bg-red-600/10 hover:bg-red-600/20 text-red-400 border border-red-600/30 hover:border-red-500 rounded-lg font-medium transition-all duration-200">
                        Reject
                    </button>
                    <button type="button" id="btnApproveUser" class="flex-1 py-2.5 px-4 bg-green-600 hover:bg-green-500 text-white rounded-lg font-medium shadow-lg shadow-green-900/20 transition-all duration-200">
                        Approve
                    </button>
                </div>
            </div>
        </div>
        </div>
    
    <!-- Freeze Action Modals -->
    <div id="freezeConfirmModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4">
        <div class="bg-slate-800 rounded-2xl max-w-md w-full shadow-2xl border border-slate-700">
            <div class="p-6 border-b border-slate-700">
                <h3 class="text-xl font-bold text-white flex items-center gap-2">
                    <i id="freezeConfirmIcon" class="fas fa-check-circle text-green-400"></i>
                    <span id="freezeConfirmTitle">Confirm Action</span>
                </h3>
            </div>
            <div class="p-6">
                <p id="freezeConfirmMessage" class="text-slate-300 mb-4"></p>
                <div id="freezeRejectReasonContainer" class="hidden mb-4">
                    <label class="block text-sm font-medium text-slate-300 mb-2">Rejection Reason (Optional)</label>
                    <textarea id="freezeRejectReason" rows="3" 
                              class="w-full px-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white placeholder-slate-500 focus:ring-2 focus:ring-amber-500 focus:border-transparent"
                              placeholder="Enter reason for rejection..."></textarea>
                </div>
                <div class="flex gap-3">
                    <button id="freezeConfirmCancel" class="flex-1 px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white rounded-lg transition">
                        Cancel
                    </button>
                    <button id="freezeConfirmOk" class="flex-1 px-4 py-2 bg-green-600 hover:bg-green-500 text-white rounded-lg transition">
                        Confirm
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="freezeResultModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4">
        <div class="bg-slate-800 rounded-2xl max-w-md w-full shadow-2xl border border-slate-700">
            <div class="p-6 border-b border-slate-700">
                <h3 class="text-xl font-bold text-white flex items-center gap-2">
                    <i id="freezeResultIcon" class="fas fa-info-circle text-blue-400"></i>
                    <span id="freezeResultTitle">Result</span>
                </h3>
            </div>
            <div class="p-6">
                <p id="freezeResultMessage" class="text-slate-300 mb-4"></p>
                <button id="freezeResultClose" class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white rounded-lg transition">
                    OK
                </button>
            </div>
        </div>
    </div>
    
    <script>
    // Modal Helper Functions
    function showFreezeConfirmModal(title, message, onConfirm, showReasonInput = false) {
        const modal = $('#freezeConfirmModal');
        const reasonContainer = $('#freezeRejectReasonContainer');
        const reasonInput = $('#freezeRejectReason');
        
        $('#freezeConfirmTitle').text(title);
        $('#freezeConfirmMessage').text(message);
        $('#freezeConfirmIcon').attr('class', showReasonInput ? 'fas fa-times-circle text-red-400' : 'fas fa-check-circle text-green-400');
        $('#freezeConfirmOk').attr('class', showReasonInput ? 
            'flex-1 px-4 py-2 bg-red-600 hover:bg-red-500 text-white rounded-lg transition' :
            'flex-1 px-4 py-2 bg-green-600 hover:bg-green-500 text-white rounded-lg transition'
        );
        
        if (showReasonInput) {
            reasonContainer.removeClass('hidden');
            reasonInput.val('');
        } else {
            reasonContainer.addClass('hidden');
        }
        
        modal.removeClass('hidden');
        
        $('#freezeConfirmOk').off('click').on('click', function() {
            modal.addClass('hidden');
            const reason = showReasonInput ? reasonInput.val() : null;
            onConfirm(reason);
        });
        
        $('#freezeConfirmCancel').off('click').on('click', function() {
            modal.addClass('hidden');
        });
        
        modal.off('click').on('click', function(e) {
            if ($(e.target).is(modal)) {
                modal.addClass('hidden');
            }
        });
    }

    function showFreezeResultModal(title, message, isSuccess = true) {
        const modal = $('#freezeResultModal');
        
        $('#freezeResultTitle').text(title);
        $('#freezeResultMessage').text(message);
        $('#freezeResultIcon').attr('class', isSuccess ? 
            'fas fa-check-circle text-green-400' : 
            'fas fa-exclamation-circle text-red-400'
        );
        
        modal.removeClass('hidden');
        
        $('#freezeResultClose').off('click').on('click', function() {
            modal.addClass('hidden');
            if (isSuccess) {
                location.reload();
            }
        });
        
        modal.off('click').on('click', function(e) {
            if ($(e.target).is(modal)) {
                modal.addClass('hidden');
                if (isSuccess) {
                    location.reload();
                }
            }
        });
    }

    // Freeze Request Approval Functions
    function approveFreeze(freezeId) {
        showFreezeConfirmModal(
            'Approve Freeze Request',
            'Are you sure you want to approve this freeze request?',
            function() {
                $.ajax({
                    url: 'index.php?controller=Subscribe&action=ApproveFreezeRequest',
                    method: 'POST',
                    data: { freeze_id: freezeId },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            showFreezeResultModal('Success', 'Freeze request approved successfully!', true);
                        } else {
                            showFreezeResultModal('Error', response.message, false);
                        }
                    },
                    error: function(xhr) {
                        showFreezeResultModal('Error', 'Failed to process request. Please try again.', false);
                        console.error(xhr.responseText);
                    }
                });
            }
        );
    }

    function rejectFreeze(freezeId) {
        showFreezeConfirmModal(
            'Reject Freeze Request',
            'Are you sure you want to reject this freeze request?',
            function(notes) {
                $.ajax({
                    url: 'index.php?controller=Subscribe&action=RejectFreezeRequest',
                    method: 'POST',
                    data: { freeze_id: freezeId, admin_notes: notes || '' },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            showFreezeResultModal('Success', 'Freeze request rejected', true);
                        } else {
                            showFreezeResultModal('Error', response.message, false);
                        }
                    },
                    error: function(xhr) {
                        showFreezeResultModal('Error', 'Failed to process request. Please try again.', false);
                        console.error(xhr.responseText);
                    }
                });
            },
            true // Show reason input
        );
    }
    </script>
</body>
</html>