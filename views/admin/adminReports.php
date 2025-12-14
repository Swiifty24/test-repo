<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports | Gymazing</title>
    <script src="../public/assets/js/tailwindcss/tailwindcss.js"></script>
    <script src="../public/assets/js/jquery/jquery-3.7.1.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* Base Theme */
        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background-color: #0f172a; /* Slate 900 */
            color: #e2e8f0;
            background-image: radial-gradient(at 0% 0%, hsla(253,16%,7%,1) 0, transparent 50%), 
                              radial-gradient(at 50% 0%, hsla(225,39%,30%,1) 0, transparent 50%), 
                              radial-gradient(at 100% 0%, hsla(339,49%,30%,1) 0, transparent 50%);
            background-attachment: fixed;
        }

        /* Glassmorphism Components */
        .glass-panel {
            background: rgba(30, 41, 59, 0.7); /* Slate 800 with opacity */
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .glass-panel:hover {
            border-color: rgba(255, 255, 255, 0.15);
        }

        /* Form Elements */
        .glass-input {
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
            transition: all 0.2s;
        }
        
        .glass-input:focus {
            outline: none;
            border-color: #3b82f6;
            background: rgba(15, 23, 42, 0.9);
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
        }

        /* KPI Cards */
        .stat-card {
            position: relative;
            overflow: hidden;
            background: rgba(30, 41, 59, 0.6);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.05);
            transition: all 0.3s ease;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            opacity: 0;
            transition: opacity 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3);
            background: rgba(30, 41, 59, 0.8);
        }
        
        .stat-card:hover::before {
            opacity: 1;
        }

        /* Icon Backgrounds */
        .icon-bg {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 48px;
            height: 48px;
            border-radius: 12px;
            font-size: 1.25rem;
        }

        /* Custom Scrollbar */
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

        /* Table Styling */
        .glass-table th {
            background-color: rgba(15, 23, 42, 0.8);
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            color: #94a3b8;
        }
        .glass-table tr {
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            transition: background 0.2s;
        }
        .glass-table tr:last-child {
            border-bottom: none;
        }
        .glass-table tr:hover {
            background-color: rgba(255, 255, 255, 0.03);
        }

        /* Loading Spinner */
        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid rgba(59, 130, 246, 0.1);
            border-left-color: #3b82f6;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin { 100% { transform: rotate(360deg); } }

        /* Print Styles - Preserved & Optimized */
        @media print {
            .no-print, button, select, input, nav, .modal-backdrop, #alertContainer { display: none !important; }
            body { background: white !important; color: black !important; background-image: none !important; }
            .glass-panel, .stat-card { background: white !important; border: 1px solid #ccc !important; box-shadow: none !important; color: black !important; break-inside: avoid; }
            .text-white, h2, h3, p { color: black !important; }
            canvas { max-width: 100% !important; }
            .grid { display: grid !important; grid-template-columns: 1fr 1fr !important; gap: 20px; }
            .print-header { display: block !important; text-align: center; margin-bottom: 20px; border-bottom: 2px solid black; }
            .text-green-400 { color: #16a34a !important; }
            .text-red-400 { color: #dc2626 !important; }
        }
        .print-header { display: none; }
    </style>
</head>
<body class="min-h-screen">
    
    <?php include __DIR__ . "/layouts/adminnavbar.php" ?> 
    
    <div id="alertContainer" class="fixed top-6 right-4 z-50 space-y-4 max-w-sm"></div>

    <main class="pb-12 pt-6 md:ml-64 transition-all duration-300">
        <div class="p-6 lg:p-8">
            
            <div class="print-header">
                <h1>Gymazing Report</h1>
                <p>Generated on <?= date('Y-m-d H:i') ?></p>
            </div>

            <div class="mb-8 flex flex-col xl:flex-row justify-between items-start xl:items-center gap-6">
                <div>
                    <h2 class="text-3xl font-bold text-white tracking-tight">Reports & Analytics</h2>
                    <p class="text-slate-400 text-sm mt-1">Deep dive into your gym's financial and operational performance.</p>
                </div>

                <div class="glass-panel p-2 rounded-xl flex flex-wrap gap-2 items-center no-print w-full xl:w-auto">
                    
                    <div class="flex items-center gap-2 px-2">
                        <span class="text-slate-400 text-xs font-semibold uppercase tracking-wider"><i class="far fa-calendar-alt mr-1"></i> Period</span>
                        <select id="dateRangeFilter" class="glass-input px-3 py-2 rounded-lg text-sm min-w-[140px]">
                            <option value="7">Last 7 Days</option>
                            <option value="30" selected>Last 30 Days</option>
                            <option value="90">Last 3 Months</option>
                            <option value="180">Last 6 Months</option>
                            <option value="365">Last 12 Months</option>
                            <option value="custom">Custom Range</option>
                        </select>
                    </div>

                    <div id="customDateRange" class="hidden flex items-center gap-2 border-l border-white/10 pl-2">
                        <input type="date" id="startDate" class="glass-input px-3 py-2 rounded-lg text-sm">
                        <span class="text-slate-500">-</span>
                        <input type="date" id="endDate" class="glass-input px-3 py-2 rounded-lg text-sm">
                    </div>

                    <div class="border-l border-white/10 pl-2 pr-2">
                         <select id="chartTypeFilter" class="glass-input px-3 py-2 rounded-lg text-sm">
                            <option value="all">All Charts</option>
                            <option value="revenue">Revenue</option>
                            <option value="members">Members</option>
                            <option value="payments">Payments</option>
                        </select>
                    </div>

                    <div class="flex gap-2 ml-auto xl:ml-0">
                        <button id="btnApplyFilter" class="px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white text-sm font-semibold rounded-lg transition-colors shadow-lg shadow-blue-900/20">
                            Apply
                        </button>
                        <button id="btnResetFilter" class="px-3 py-2 bg-slate-700 hover:bg-slate-600 text-slate-300 rounded-lg transition-colors">
                            <i class="fas fa-redo"></i>
                        </button>
                        <button id="btnExportCSV" class="px-3 py-2 bg-purple-600 hover:bg-purple-500 text-white rounded-lg transition-colors shadow-lg shadow-purple-900/20" title="Export CSV">
                            <i class="fas fa-file-csv"></i>
                        </button>
                        <button onclick="window.print()" class="px-3 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg transition-colors shadow-lg shadow-emerald-900/20" title="Print Report">
                            <i class="fas fa-print"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div id="loadingIndicator" class="hidden flex flex-col items-center justify-center py-12">
                <div class="spinner mb-4"></div>
                <p class="text-slate-400 text-sm animate-pulse">Crunching numbers...</p>
            </div>

            <div id="filterSummary" class="mb-8 hidden">
                <div class="bg-blue-500/10 border border-blue-500/20 rounded-lg p-4 flex items-center justify-between">
                    <div class="flex items-center text-blue-300">
                        <i class="fas fa-filter mr-3"></i>
                        <span class="text-sm">Viewing data for: <strong id="filterSummaryText" class="text-white">Last 30 Days</strong></span>
                    </div>
                    <button onclick="$('#btnResetFilter').click()" class="text-xs text-blue-400 hover:text-white transition-colors">Clear</button>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                
                <div class="stat-card rounded-2xl p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div class="icon-bg bg-emerald-500/10 text-emerald-400">
                            <i class="fa-regular fa-money-bill-1"></i>
                        </div>
                    </div>
                    <h3 id="kpiTotalRevenue" class="text-3xl font-bold text-white mb-1">₱<?= number_format($paymentStats['total_paid'] ?? 0, 2) ?></h3>
                    <p class="text-slate-400 text-sm mb-1">Total Revenue</p>
                    <p id="kpiTransactionCount" class="text-slate-500 text-xs"><?= $paymentStats['paid_count'] ?? 0 ?> successful transactions</p>
                </div>

                <div class="stat-card rounded-2xl p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div class="icon-bg bg-yellow-500/10 text-yellow-400">
                            <i class="fa-regular fa-hourglass"></i>
                        </div>
                        <span class="px-2 py-1 rounded bg-yellow-500/10 text-yellow-400 text-xs font-bold border border-yellow-500/20">
                            <span id="kpiPendingCount"><?= $pendingPayments['pending_count'] ?></span> pending
                        </span>
                    </div>
                    <h3 id="kpiPendingAmount" class="text-3xl font-bold text-white mb-1">₱<?= number_format($pendingPayments['pending_amount'] ?? 0, 2) ?></h3>
                    <p class="text-slate-400 text-sm mb-1">Pending Revenue</p>
                    <p class="text-slate-500 text-xs">Expected receivable</p>
                </div>

                <div class="stat-card rounded-2xl p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div class="icon-bg bg-blue-500/10 text-blue-400">
                            <i class="fa-solid fa-user"></i>
                        </div>
                    </div>
                    <h3 id="kpiActiveMembers" class="text-3xl font-bold text-white mb-1"><?= $activeInactiveCount['active_count'] ?? 0 ?></h3>
                    <p class="text-slate-400 text-sm mb-1">Active Members</p>
                    <p class="text-slate-500 text-xs"><span id="kpiInactiveMembers"><?= $activeInactiveCount['inactive_count'] ?? 0 ?></span> currently inactive</p>
                </div>

                <div class="stat-card rounded-2xl p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div class="icon-bg bg-purple-500/10 text-purple-400">
                            <i class="fa-regular fa-chart-bar"></i>
                        </div>
                    </div>
                    <h3 id="kpiRetentionRate" class="text-3xl font-bold text-white mb-1"><?= $retentionRate['rate'] ?>%</h3>
                    <p class="text-slate-400 text-sm mb-1">Retention Rate</p>
                    <p class="text-slate-500 text-xs">Based on active vs total</p>
                </div>

                <div class="stat-card rounded-2xl p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div class="icon-bg bg-orange-500/10 text-orange-400">
                            <i class="fa-regular fa-clock"></i>
                        </div>
                        <span class="px-2 py-1 rounded bg-orange-500/10 text-orange-400 text-xs font-bold border border-orange-500/20">Urgent</span>
                    </div>
                    <h3 id="kpiExpiringCount" class="text-3xl font-bold text-white mb-1"><?= $expiringSubscriptions['expiring_count'] ?? 0 ?></h3>
                    <p class="text-slate-400 text-sm mb-1">Expiring Soon</p>
                    <p class="text-slate-500 text-xs">Within next 7 days</p>
                </div>

                <div class="stat-card rounded-2xl p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div class="icon-bg bg-cyan-500/10 text-cyan-400">
                            <i class="fa-regular fa-credit-card"></i>
                        </div>
                    </div>
                    <h3 id="kpiAvgTransaction" class="text-3xl font-bold text-white mb-1">₱<?= number_format(($paymentStats['total_paid'] ?? 0) / max(($paymentStats['paid_count'] ?? 1), 1), 2) ?></h3>
                    <p class="text-slate-400 text-sm mb-1">Avg Transaction</p>
                    <p class="text-slate-500 text-xs">Per successful payment</p>
                </div>

                 <div class="stat-card rounded-2xl p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div class="icon-bg bg-pink-500/10 text-pink-400">
                            <i class="fa-regular fa-clipboard"></i>
                        </div>
                        <span class="px-2 py-1 rounded bg-pink-500/10 text-pink-400 text-xs font-bold border border-pink-500/20">
                            <?= count($activePlans) ?> active
                        </span>
                    </div>
                    <h3 class="text-3xl font-bold text-white mb-1"><?= count($plans) ?></h3>
                    <p class="text-slate-400 text-sm mb-1">Total Plans</p>
                    <p class="text-slate-500 text-xs">Available for purchase</p>
                </div>

                <div class="stat-card rounded-2xl p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div class="icon-bg bg-teal-500/10 text-teal-400">
                            <i class="fa-regular fa-circle-check"></i>
                        </div>
                    </div>
                    <h3 id="kpiSuccessRate" class="text-3xl font-bold text-white mb-1"><?= round((($paymentStats['paid_count'] ?? 0) / max(($paymentStats['total_transactions'] ?? 1), 1)) * 100, 1) ?>%</h3>
                    <p class="text-slate-400 text-sm mb-1">Success Rate</p>
                    <p class="text-slate-500 text-xs"><span id="kpiFailedCount"><?= $paymentStats['failed_count'] ?? 0 ?></span> failed attempts</p>
                </div>
            </div>

            <div class="space-y-6">
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="glass-panel rounded-2xl p-6">
                        <h3 class="text-lg font-bold text-white mb-6 pl-2 border-l-4 border-blue-500">Revenue Trend (12 Months)</h3>
                        <div style="position: relative; height: 300px;">
                            <canvas id="revenueTrendChart"></canvas>
                        </div>
                    </div>
                    <div class="glass-panel rounded-2xl p-6">
                        <h3 class="text-lg font-bold text-white mb-6 pl-2 border-l-4 border-emerald-500">Daily Revenue (30 Days)</h3>
                        <div style="position: relative; height: 300px;">
                            <canvas id="dailyRevenueChart"></canvas>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="glass-panel rounded-2xl p-6">
                        <h3 class="text-lg font-bold text-white mb-6 pl-2 border-l-4 border-purple-500">Revenue by Plan</h3>
                        <div style="position: relative; height: 300px;">
                            <canvas id="revenueByPlanChart"></canvas>
                        </div>
                    </div>
                    <div class="glass-panel rounded-2xl p-6">
                        <h3 class="text-lg font-bold text-white mb-6 pl-2 border-l-4 border-pink-500">Members by Plan</h3>
                        <div style="position: relative; height: 300px;">
                            <canvas id="membersByPlanChart"></canvas>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="glass-panel rounded-2xl p-6">
                        <h3 class="text-lg font-bold text-white mb-6 pl-2 border-l-4 border-green-500">Member Growth</h3>
                        <div style="position: relative; height: 300px;">
                            <canvas id="memberGrowthChart"></canvas>
                        </div>
                    </div>
                    <div class="glass-panel rounded-2xl p-6">
                        <h3 class="text-lg font-bold text-white mb-6 pl-2 border-l-4 border-cyan-500">Payment Methods</h3>
                        <div style="position: relative; height: 300px;">
                            <canvas id="paymentMethodChart"></canvas>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="glass-panel rounded-2xl p-6">
                        <h3 class="text-lg font-bold text-white mb-6 pl-2 border-l-4 border-red-500">Member Status</h3>
                        <div style="position: relative; height: 300px;">
                            <canvas id="memberStatusChart"></canvas>
                        </div>
                    </div>
                    <div class="glass-panel rounded-2xl p-6">
                        <h3 class="text-lg font-bold text-white mb-6 pl-2 border-l-4 border-yellow-500">Subscription Status</h3>
                        <div style="position: relative; height: 300px;">
                            <canvas id="subscriptionStatusChart"></canvas>
                        </div>
                    </div>
                </div>

                <div class="glass-panel rounded-2xl p-6 overflow-hidden">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-bold text-white pl-2 border-l-4 border-indigo-500">Top Performing Plans</h3>
                        <button class="text-xs text-blue-400 hover:text-white transition-colors">View All Details</button>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left glass-table rounded-lg overflow-hidden">
                            <thead>
                                <tr>
                                    <th class="px-6 py-4">Plan Name</th>
                                    <th class="px-6 py-4 text-right">Total Revenue</th>
                                    <th class="px-6 py-4 text-right">Transactions</th>
                                    <th class="px-6 py-4 text-right">Avg / Transaction</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm text-slate-300">
                                <?php foreach($revenueByPlan as $plan): ?>
                                <tr class="group">
                                    <td class="px-6 py-4 font-medium text-white group-hover:text-blue-300 transition-colors">
                                        <?= htmlspecialchars($plan['plan_name']) ?>
                                    </td>
                                    <td class="px-6 py-4 text-right font-bold text-emerald-400">
                                        ₱<?= number_format($plan['total_revenue'], 2) ?>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <span class="px-2 py-1 bg-slate-700 rounded text-xs"><?= $plan['payment_count'] ?></span>
                                    </td>
                                    <td class="px-6 py-4 text-right text-slate-400">
                                        ₱<?= number_format($plan['total_revenue'] / $plan['payment_count'], 2) ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </main>
    
    <script src="../public/assets/js/admin/admin.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>

    <script>
    // Chart.js Configuration
    document.addEventListener('DOMContentLoaded', function() {
        // Common chart options with FIXED settings
        const commonOptions = {
            responsive: true,
            maintainAspectRatio: true,
            aspectRatio: 2,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            plugins: {
                legend: {
                    labels: {
                        font: {
                            color: '#9ca3af',
                            size: 12
                        }
                    }
                },
                tooltip: {
                    enabled: true,
                    backgroundColor: 'rgba(15, 23, 42, 0.9)',
                    padding: 12,
                    bodySpacing: 4,
                    mode: 'index',
                    intersect: false,
                    borderColor: 'rgba(255,255,255,0.1)',
                    borderWidth: 1
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { 
                        color: '#9ca3af',
                        font: { size: 11 }
                    },
                    grid: { 
                        color: 'rgba(255, 255, 255, 0.05)',
                        drawBorder: false
                    }
                },
                x: {
                    ticks: { 
                        color: '#9ca3af',
                        font: { size: 11 },
                        maxRotation: 45,
                        minRotation: 0
                    },
                    grid: { 
                        color: 'rgba(255, 255, 255, 0.05)',
                        drawBorder: false
                    }
                }
            },
            animation: {
                duration: 750
            }
        };

        // Doughnut/Pie chart options
        const pieOptions = {
            responsive: true,
            maintainAspectRatio: true,
            aspectRatio: 1.5,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { 
                        color: '#9ca3af',
                        padding: 15,
                        font: { size: 11 }
                    }
                },
                tooltip: {
                    enabled: true,
                    backgroundColor: 'rgba(15, 23, 42, 0.9)',
                    padding: 12,
                    borderColor: 'rgba(255,255,255,0.1)',
                    borderWidth: 1
                }
            },
            animation: {
                duration: 750
            }
        };

        // Revenue Trend Chart (Last 12 Months)
        const revenueTrendData = <?= json_encode($last12MonthsRevenue) ?>;
        const ctx1 = document.getElementById('revenueTrendChart');
        if(ctx1) {
            new Chart(ctx1, {
                type: 'line',
                data: {
                    labels: revenueTrendData.map(d => d.month_label),
                    datasets: [{
                        label: 'Monthly Revenue (₱)',
                        data: revenueTrendData.map(d => d.revenue),
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        fill: true,
                        tension: 0.4,
                        borderWidth: 2,
                        pointRadius: 3,
                        pointHoverRadius: 5
                    }]
                },
                options: commonOptions
            });
        }

        // Daily Revenue Chart (Last 30 Days)
        const dailyRevenueData = <?= json_encode($dailyRevenue30Days) ?>;
        const ctx2 = document.getElementById('dailyRevenueChart');
        if(ctx2) {
            new Chart(ctx2, {
                type: 'bar',
                data: {
                    labels: dailyRevenueData.map(d => d.date_label),
                    datasets: [{
                        label: 'Daily Revenue (₱)',
                        data: dailyRevenueData.map(d => d.revenue),
                        backgroundColor: 'rgba(34, 197, 94, 0.6)',
                        borderColor: '#22c55e',
                        borderWidth: 1,
                        borderRadius: 4
                    }]
                },
                options: commonOptions
            });
        }

        // Revenue by Plan Chart
        const revenueByPlanData = <?= json_encode($revenueByPlan) ?>;
        const ctx3 = document.getElementById('revenueByPlanChart');
        if(ctx3) {
            new Chart(ctx3, {
                type: 'doughnut',
                data: {
                    labels: revenueByPlanData.map(d => d.plan_name),
                    datasets: [{
                        data: revenueByPlanData.map(d => d.total_revenue),
                        backgroundColor: [
                            'rgba(59, 130, 246, 0.8)',
                            'rgba(34, 197, 94, 0.8)',
                            'rgba(251, 146, 60, 0.8)',
                            'rgba(168, 85, 247, 0.8)',
                            'rgba(236, 72, 153, 0.8)'
                        ],
                        borderWidth: 2,
                        borderColor: '#1e293b',
                        hoverOffset: 4
                    }]
                },
                options: pieOptions
            });
        }

        // Members by Plan Chart
        const membersByPlanData = <?= json_encode($membersByPlan) ?>;
        const ctx4 = document.getElementById('membersByPlanChart');
        if(ctx4) {
            new Chart(ctx4, {
                type: 'bar',
                data: {
                    labels: membersByPlanData.map(d => d.plan_name),
                    datasets: [{
                        label: 'Members',
                        data: membersByPlanData.map(d => d.member_count),
                        backgroundColor: 'rgba(168, 85, 247, 0.6)',
                        borderColor: '#a855f7',
                        borderWidth: 1,
                        borderRadius: 4
                    }]
                },
                options: commonOptions  
                
            });
        }

        // Member Growth Chart
        const memberGrowthData = <?= json_encode($memberGrowth) ?>;
        const ctx5 = document.getElementById('memberGrowthChart');
        if(ctx5) {
            new Chart(ctx5, {
                type: 'line',
                data: {
                    labels: memberGrowthData.map(d => d.month_label),
                    datasets: [{
                        label: 'New Members',
                        data: memberGrowthData.map(d => d.new_members),
                        borderColor: '#22c55e',
                        backgroundColor: 'rgba(34, 197, 94, 0.1)',
                        fill: true,
                        tension: 0.4,
                        borderWidth: 2,
                        pointRadius: 3,
                        pointHoverRadius: 5
                    }]
                },
                options: commonOptions
            });
        }

        // Payment Method Chart
        const paymentMethodData = <?= json_encode($paymentMethodStats) ?>;
        const ctx6 = document.getElementById('paymentMethodChart');
        if(ctx6) {
            new Chart(ctx6, {
                type: 'pie',
                data: {
                    labels: paymentMethodData.map(d => d.payment_method || 'Unknown'),
                    datasets: [{
                        data: paymentMethodData.map(d => d.total_amount),
                        backgroundColor: [
                            'rgba(59, 130, 246, 0.8)',
                            'rgba(34, 197, 94, 0.8)',
                            'rgba(251, 146, 60, 0.8)',
                            'rgba(236, 72, 153, 0.8)'
                        ],
                        borderWidth: 2,
                        borderColor: '#1e293b',
                        hoverOffset: 4
                    }]
                },
                options: pieOptions
            });
        }

        // Member Status Chart
        const activeInactiveData = <?= json_encode($activeInactiveCount) ?>;
        const ctx7 = document.getElementById('memberStatusChart');
        if(ctx7) {
            new Chart(ctx7, {
                type: 'doughnut',
                data: {
                    labels: ['Active Members', 'Inactive Members'],
                    datasets: [{
                        data: [activeInactiveData.active_count, activeInactiveData.inactive_count],
                        backgroundColor: [
                            'rgba(34, 197, 94, 0.8)',
                            'rgba(239, 68, 68, 0.8)'
                        ],
                        borderWidth: 2,
                        borderColor: '#1e293b',
                        hoverOffset: 4
                    }]
                },
                options: pieOptions
            });
        }

        // Subscription Status Chart
        const subscriptionStatusData = <?= json_encode($subscriptionStatusBreakdown) ?>;
        const ctx8 = document.getElementById('subscriptionStatusChart');
        if(ctx8) {
            new Chart(ctx8, {
                type: 'bar',
                data: {
                    labels: subscriptionStatusData.map(d => d.status.charAt(0).toUpperCase() + d.status.slice(1)),
                    datasets: [{
                        label: 'Subscriptions',
                        data: subscriptionStatusData.map(d => d.count),
                        backgroundColor: [
                            'rgba(34, 197, 94, 0.6)',
                            'rgba(251, 146, 60, 0.6)',
                            'rgba(239, 68, 68, 0.6)',
                            'rgba(59, 130, 246, 0.6)'
                        ],
                        borderColor: [
                            '#22c55e',
                            '#fb923c',
                            '#ef4444',
                            '#3b82f6'
                        ],
                        borderWidth: 1,
                        borderRadius: 4
                    }]
                },
                options: {
                    ...commonOptions,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            enabled: true,
                            backgroundColor: 'rgba(15, 23, 42, 0.9)',
                            padding: 12,
                            borderColor: 'rgba(255,255,255,0.1)',
                            borderWidth: 1
                        }
                    }
                }
            });
        }
    });
    </script>
    <script src="../public/assets/js/admin/reports.js"></script>
</body>
</html>