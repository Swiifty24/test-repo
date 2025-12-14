<?php 
   // PHP logic remains untouched
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment & Billing - Gymazing</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    
    <script src="../public/assets/js/tailwindcss/tailwindcss.js"></script>
    <script src="../public/assets/js/jquery/jquery-3.7.1.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

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

        /* Glassmorphism Panel */
        .glass-panel {
            background: rgba(30, 41, 59, 0.7);
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

        /* Payment Method Option Card (Radio replacement) */
        .payment-option-card {
            border: 1px solid rgba(255, 255, 255, 0.1);
            background: rgba(30, 41, 59, 0.4);
            transition: all 0.2s ease;
            cursor: pointer;
        }
        
        .payment-option-card:hover {
            border-color: rgba(59, 130, 246, 0.5);
            background: rgba(30, 41, 59, 0.6);
        }

        /* Hide actual radio but keep logic accessible */
        .payment-method-option input[type="radio"]:checked + span {
            /* Styling handled via JS usually, but here we can target parent with :has or just style the span */
            color: #60a5fa; 
            font-weight: bold;
        }
        
        /* Modern Table */
        .glass-table th {
            background-color: rgba(30, 41, 59, 0.8);
            color: #94a3b8;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .glass-table tr {
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            transition: background-color 0.2s;
        }
        .glass-table tr:hover {
            background-color: rgba(255, 255, 255, 0.03);
        }

        /* Status Badges */
        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .status-paid { background: rgba(34, 197, 94, 0.1); color: #4ade80; border: 1px solid rgba(34, 197, 94, 0.2); }
        .status-pending { background: rgba(251, 146, 60, 0.1); color: #fb923c; border: 1px solid rgba(251, 146, 60, 0.2); }
        .status-failed { background: rgba(239, 68, 68, 0.1); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.2); }

        /* Animation Classes */
        .pulse-animation { animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite; }
        @keyframes pulse { 50% { opacity: .7; } }
        
        .fadeIn { animation: fadeIn 0.5s ease-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

        /* Form Inputs */
        .glass-input {
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
            transition: all 0.3s;
        }
        .glass-input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
        }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #0f172a; }
        ::-webkit-scrollbar-thumb { background: #3b82f6; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #2563eb; }
        
        /* Modal Adjustments */
        .modal-backdrop { opacity: 0; visibility: hidden; transition: all 0.3s; backdrop-filter: blur(5px); }
        .modal-backdrop.show { opacity: 1; visibility: visible; }
        .modal-content { transform: scale(0.95); transition: all 0.3s; }
        .modal-backdrop.show .modal-content { transform: scale(1); }
    </style>
</head>
<body class="gradient-bg min-h-screen">
    
    <?php include_once '../views/layouts/navbar.php'?>

    <div id="alertContainer" class="fixed top-24 md:top-6 right-4 z-50 space-y-4 max-w-sm"></div>

    <main class="main-content min-h-screen pt-24 pb-12 md:ml-64 transition-all duration-300">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="mb-10 fadeIn">
                <h1 class="text-3xl font-bold text-white mb-2 tracking-tight">Payment & Billing</h1>
                <p class="text-slate-400">Manage your subscription, view history, and update methods.</p>
            </div>

            <section id="current_payment" class="mb-12 fadeIn" style="animation-delay: 0.1s;">
                <?php
                    $currentPlan = array_filter($paymentDetails, function($value) {
                        return $value['status'] == 'pending';
                    });
                    $currentPlan = reset($currentPlan); 
                ?>
                
                <?php if ($currentPlan): ?>
                    <div class="relative overflow-hidden rounded-2xl p-8 lg:p-10 border border-blue-500/30 shadow-2xl bg-gradient-to-br from-blue-900/80 to-slate-900">
                        <div class="absolute top-0 right-0 -mr-20 -mt-20 w-64 h-64 bg-blue-500/20 rounded-full blur-3xl"></div>
                        
                        <div class="relative z-10 flex flex-col lg:flex-row items-start justify-between gap-8 mb-8">
                            <div>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-orange-500/10 text-orange-400 border border-orange-500/20 pulse-animation mb-4">
                                    <i class="fa-solid fa-circle-exclamation mr-2"></i> Payment Due
                                </span>
                                <h2 class="text-3xl font-bold text-white mb-1">Current Plan: <?= $currentPlan['plan_name'] ?></h2>
                                <p class="text-slate-400 text-sm font-mono opacity-80">SUB-ID: <?= $currentPlan['subscription_id'] ?></p>
                            </div>
                            <div class="text-left lg:text-right bg-slate-900/50 p-4 rounded-xl border border-slate-700/50">
                                <p class="text-slate-400 text-xs uppercase tracking-wider mb-1">Total Amount Due</p>
                                <p class="text-4xl font-bold text-white tracking-tight">₱<?= number_format($currentPlan['amount']) ?></p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                            <div class="bg-slate-900/40 p-4 rounded-xl border border-white/5">
                                <p class="text-slate-400 text-xs uppercase mb-1">Start Date</p>
                                <p class="text-white font-semibold flex items-center"><i class="fa-regular fa-calendar-check mr-2 text-blue-400"></i><?= date('M d, Y', strtotime($currentPlan['start_date'])) ?></p>
                            </div>
                            <div class="bg-slate-900/40 p-4 rounded-xl border border-white/5">
                                <p class="text-slate-400 text-xs uppercase mb-1">End Date</p>
                                <p class="text-white font-semibold flex items-center"><i class="fa-regular fa-calendar-xmark mr-2 text-blue-400"></i><?= date('M d, Y', strtotime($currentPlan['end_date'])) ?></p>
                            </div>
                            <div class="bg-slate-900/40 p-4 rounded-xl border border-white/5">
                                <p class="text-slate-400 text-xs uppercase mb-1">Due Date</p>
                                <p class="text-white font-semibold flex items-center"><i class="fa-regular fa-clock mr-2 text-orange-400"></i><?= date('M d, Y', strtotime($currentPlan['payment_date'])) ?></p>
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row gap-4">
                            <button id="btnPayNow" data-subscription-id="<?= $currentPlan['subscription_id'] ?>" data-amount="<?= $currentPlan['amount'] ?>" data-plan="<?= $currentPlan['plan_name'] ?>"
                                    class="flex-1 px-6 py-4 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-xl shadow-lg shadow-blue-900/30 transition-all transform hover:-translate-y-1">
                                <i class="fa-regular fa-credit-card mr-2"></i> Pay Now
                            </button>
                            <button onclick="window.open('index.php?controller=Invoice&action=downloadInvoice&subscription_id=<?= $currentPlan['subscription_id'] ?>', '_blank')" 
                                    class="flex-1 px-6 py-4 bg-slate-800 hover:bg-slate-700 text-white font-bold rounded-xl border border-slate-600 transition-all hover:border-slate-500">
                                <i class="fa-solid fa-file-invoice mr-2"></i> Download Invoice
                            </button>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="glass-panel rounded-2xl p-10 text-center border border-emerald-500/20 relative overflow-hidden">
                        <div class="absolute inset-0 bg-emerald-500/5"></div>
                        <div class="relative z-10">
                            <div class="w-16 h-16 bg-emerald-500/20 rounded-full flex items-center justify-center mx-auto mb-4 text-emerald-400 text-3xl">
                                <i class="fa-solid fa-check"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-white mb-2">All Caught Up!</h3>
                            <p class="text-slate-400 max-w-md mx-auto">You have no pending payments. Your next billing cycle will be processed automatically.</p>
                        </div>
                    </div>
                <?php endif; ?>
            </section>

            <!-- <section class="mb-12 fadeIn" style="animation-delay: 0.2s;">
                <div class="flex flex-col sm:flex-row items-center justify-between mb-6 gap-4">
                    <h2 class="text-xl font-bold text-white flex items-center">
                        <i class="fa-solid fa-wallet mr-3 text-blue-500"></i> Payment Methods
                    </h2>
                    <button id="btnAddPaymentMethod" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white text-sm font-medium rounded-lg border border-slate-600 transition-colors flex items-center">
                        <i class="fa-solid fa-plus mr-2"></i> Add New
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="glass-panel p-6 rounded-xl hover:border-blue-500/50 cursor-pointer group relative overflow-hidden">
                        <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                            <i class="fa-brands fa-cc-visa text-6xl text-white"></i>
                        </div>
                        <div class="flex justify-between items-start mb-8">
                            <div class="w-10 h-10 bg-slate-800 rounded-lg flex items-center justify-center text-white text-xl">
                                <i class="fa-brands fa-cc-visa"></i>
                            </div>
                            <span class="px-2 py-1 bg-emerald-500/20 text-emerald-400 text-[10px] font-bold uppercase rounded tracking-wider border border-emerald-500/30">Default</span>
                        </div>
                        <p class="text-white font-mono text-lg mb-1">•••• •••• •••• 4242</p>
                        <div class="flex justify-between text-xs text-slate-400 uppercase tracking-wider">
                            <span>Visa Credit</span>
                            <span>Exp 12/25</span>
                        </div>
                    </div>

                    <div class="glass-panel p-6 rounded-xl hover:border-blue-500/50 cursor-pointer group">
                        <div class="flex justify-between items-start mb-8">
                            <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center text-white font-bold">G</div>
                        </div>
                        <p class="text-white font-medium text-lg mb-1">GCash e-Wallet</p>
                        <p class="text-slate-400 text-sm font-mono">0917 ••• 1234</p>
                    </div>

                    <button class="border-2 border-dashed border-slate-700 rounded-xl p-6 flex flex-col items-center justify-center text-slate-500 hover:text-white hover:border-slate-500 transition-all h-full min-h-[160px]">
                        <i class="fa-solid fa-plus text-2xl mb-2"></i>
                        <span class="text-sm font-medium">Add Payment Method</span>
                    </button>
                </div>
            </section> -->

            <section id="transaction_history" class="fadeIn" style="animation-delay: 0.3s;">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-white flex items-center">
                        <i class="fa-solid fa-clock-rotate-left mr-3 text-purple-500"></i> Transaction History
                    </h2>
                    <button class="text-slate-400 hover:text-white text-sm"><i class="fa-solid fa-download mr-1"></i> Export CSV</button>
                </div>

                <div class="glass-panel rounded-xl overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full glass-table text-left border-collapse">
                            <thead>
                                <tr>
                                    <th class="px-6 py-4">ID</th>
                                    <th class="px-6 py-4">Plan</th>
                                    <th class="px-6 py-4">Amount</th>
                                    <th class="px-6 py-4">Date</th>
                                    <th class="px-6 py-4">Status</th>
                                    <th class="px-6 py-4 text-center">Receipt</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm text-slate-300">
                                <?php foreach ($paymentDetails as $payment) { ?>
                                    <tr class="hover:bg-slate-800/50 transition-colors border-b border-slate-700/50 last:border-0">
                                        <td class="px-6 py-4 font-mono text-slate-400">#<?= $payment['subscription_id'] ?></td>
                                        <td class="px-6 py-4 font-medium text-white"><?= $payment['plan_name'] ?></td>
                                        <td class="px-6 py-4 font-bold text-white">₱<?= number_format($payment['amount']) ?></td>
                                        <td class="px-6 py-4 text-slate-400"><?= date('M d, Y', strtotime($payment['payment_date'])) ?></td>
                                        <td class="px-6 py-4">
                                            <span class="status-badge status-<?= $payment['status'] ?>">
                                                <?= ucfirst($payment['status']) ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <button class="text-slate-400 hover:text-blue-400 transition-colors" 
                                                    onclick="window.open('index.php?controller=Invoice&action=downloadReceipt&subscription_id=<?= $payment['subscription_id'] ?>', '_blank')"
                                                    title="Download Receipt">
                                                <i class="fa-solid fa-file-arrow-down text-lg"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

        </div>
    </main>

    <div id="paymentModal" class="modal-backdrop fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="modal-content bg-slate-900 rounded-2xl max-w-md w-full border border-slate-700 shadow-2xl relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-blue-500 to-purple-500"></div>
            
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-white">Select Payment Method</h3>
                    <button class="modal-close text-slate-500 hover:text-white"><i class="fa-solid fa-xmark text-xl"></i></button>
                </div>

                <div class="bg-slate-800/50 rounded-xl p-4 mb-6 border border-slate-700/50 flex justify-between items-center">
                    <div>
                        <p class="text-xs text-slate-400 uppercase tracking-wider">Total to Pay</p>
                        <p class="text-2xl font-bold text-white" id="modalAmount"></p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-slate-400 uppercase tracking-wider">Plan</p>
                        <p class="text-sm font-semibold text-blue-400" id="modalPlanName"></p>
                        <p class="text-xs text-slate-500 font-mono" id="modalSubscriptionId"></p>
                    </div>
                </div>

                <div class="space-y-3 mb-6">
                    <label class="payment-method-option flex items-center p-4 rounded-xl border border-slate-700 bg-slate-800/30 hover:bg-slate-800 hover:border-blue-500 cursor-pointer transition-all">
                        <input type="radio" name="payment_method" value="card" class="mr-3 w-4 h-4 accent-blue-500" checked>
                        <div class="flex-1 flex items-center justify-between">
                            <span class="text-white font-medium">Credit / Debit Card</span>
                            <div class="flex gap-2 text-slate-400 text-xl"><i class="fa-brands fa-cc-visa"></i> <i class="fa-brands fa-cc-mastercard"></i></div>
                        </div>
                    </label>
                    
                    <label class="payment-method-option flex items-center p-4 rounded-xl border border-slate-700 bg-slate-800/30 hover:bg-slate-800 hover:border-blue-500 cursor-pointer transition-all">
                        <input type="radio" name="payment_method" value="gcash" class="mr-3 w-4 h-4 accent-blue-500">
                        <span class="text-white font-medium">GCash</span>
                    </label>
                    
                    <label class="payment-method-option flex items-center p-4 rounded-xl border border-slate-700 bg-slate-800/30 hover:bg-slate-800 hover:border-blue-500 cursor-pointer transition-all">
                        <input type="radio" name="payment_method" value="paymaya" class="mr-3 w-4 h-4 accent-blue-500">
                        <span class="text-white font-medium">PayMaya</span>
                    </label>
                    
                    <label class="payment-method-option flex items-center p-4 rounded-xl border border-slate-700 bg-slate-800/30 hover:bg-slate-800 hover:border-blue-500 cursor-pointer transition-all">
                        <input type="radio" name="payment_method" value="bank" class="mr-3 w-4 h-4 accent-blue-500">
                        <span class="text-white font-medium">Bank Transfer</span>
                    </label>
                </div>

                <div id="paymentMessage" class="hidden text-center text-sm text-red-400 mb-4 bg-red-500/10 p-2 rounded-lg"></div>

                <div class="flex gap-3">
                    <button class="modal-cancel flex-1 py-3 bg-transparent border border-slate-600 text-slate-300 hover:text-white rounded-xl transition-colors">Cancel</button>
                    <button id="btnProceedPayment" class="flex-1 py-3 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-xl shadow-lg transition-all">Next Step &rarr;</button>
                </div>
            </div>
        </div>
    </div>

    <div id="paymentDetailsModal" class="modal-backdrop fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="modal-content bg-slate-900 rounded-2xl max-w-lg w-full border border-slate-700 shadow-2xl relative max-h-[90vh] overflow-y-auto custom-scrollbar">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6 border-b border-slate-700 pb-4">
                    <h3 class="text-xl font-bold text-white">Enter Details</h3>
                    <button class="payment-details-close text-slate-500 hover:text-white"><i class="fa-solid fa-xmark text-xl"></i></button>
                </div>

                <div class="bg-blue-900/20 border border-blue-500/20 rounded-lg p-4 mb-6 flex justify-between items-center">
                    <span class="text-sm text-blue-300"><i class="fa-solid fa-wallet mr-2"></i>Paying via <strong id="detailsMethod" class="uppercase"></strong></span>
                    <span class="text-lg font-bold text-white" id="detailsAmount"></span>
                </div>

                <form id="paymentDetailsForm" method="GET" class="space-y-5">
                    <input type="hidden" id="form_subscription_id" name="subscription_id">
                    <input type="hidden" id="form_amount" name="amount">
                    <input type="hidden" id="form_payment_method" name="payment_method">

                    <div id="cardForm" class="payment-form-section space-y-4">
                        <div>
                            <label class="block text-xs uppercase tracking-wider text-slate-400 font-semibold mb-2">Cardholder Name</label>
                            <input type="text" name="cardholder_name" class="glass-input w-full px-4 py-3 rounded-xl" placeholder="Name on card">
                        </div>
                        <div>
                            <label class="block text-xs uppercase tracking-wider text-slate-400 font-semibold mb-2">Card Number</label>
                            <div class="relative">
                                <input type="text" name="card_number" maxlength="19" class="glass-input w-full px-4 py-3 rounded-xl pl-12" placeholder="0000 0000 0000 0000">
                                <i class="fa-regular fa-credit-card absolute left-4 top-3.5 text-slate-500"></i>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs uppercase tracking-wider text-slate-400 font-semibold mb-2">Expiry</label>
                                <input type="text" name="expiry_date" maxlength="5" class="glass-input w-full px-4 py-3 rounded-xl" placeholder="MM/YY">
                            </div>
                            <div>
                                <label class="block text-xs uppercase tracking-wider text-slate-400 font-semibold mb-2">CVV</label>
                                <input type="text" name="cvv" maxlength="4" class="glass-input w-full px-4 py-3 rounded-xl" placeholder="123">
                            </div>
                        </div>
                    </div>

                    <div id="gcashForm" class="payment-form-section hidden space-y-4">
                        <div>
                            <label class="block text-xs uppercase tracking-wider text-slate-400 font-semibold mb-2">Mobile Number</label>
                            <input type="tel" name="gcash_number" class="glass-input w-full px-4 py-3 rounded-xl" placeholder="09XX XXX XXXX">
                        </div>
                        <div>
                            <label class="block text-xs uppercase tracking-wider text-slate-400 font-semibold mb-2">Account Name</label>
                            <input type="text" name="gcash_name" class="glass-input w-full px-4 py-3 rounded-xl" placeholder="Full Name">
                        </div>
                        <div class="p-4 bg-blue-500/10 border border-blue-500/20 rounded-xl text-sm text-blue-300 flex items-start gap-3">
                            <i class="fa-solid fa-circle-info mt-0.5"></i>
                            <p>You will be redirected to the secure GCash payment gateway to complete this transaction.</p>
                        </div>
                    </div>

                    <div id="paymayaForm" class="payment-form-section hidden space-y-4">
                        <div>
                            <label class="block text-xs uppercase tracking-wider text-slate-400 font-semibold mb-2">Mobile Number</label>
                            <input type="tel" name="paymaya_number" class="glass-input w-full px-4 py-3 rounded-xl" placeholder="09XX XXX XXXX">
                        </div>
                        <div class="p-4 bg-green-500/10 border border-green-500/20 rounded-xl text-sm text-green-300 flex items-start gap-3">
                            <i class="fa-solid fa-circle-info mt-0.5"></i>
                            <p>You will be redirected to the PayMaya portal to authorize the payment.</p>
                        </div>
                    </div>

                    <div id="bankForm" class="payment-form-section hidden space-y-4">
                        <div class="p-4 bg-slate-800 border border-slate-700 rounded-xl space-y-2 text-sm text-slate-300">
                            <p class="font-bold text-white border-b border-slate-700 pb-2 mb-2">Transfer Instructions</p>
                            <div class="flex justify-between"><span>Bank:</span> <span class="text-white">BDO / BPI</span></div>
                            <div class="flex justify-between"><span>Account Name:</span> <span class="text-white">Gymazing Inc.</span></div>
                            <div class="flex justify-between"><span>Account No:</span> <span class="font-mono text-white">1234-5678-90</span></div>
                        </div>
                        <div>
                            <label class="block text-xs uppercase tracking-wider text-slate-400 font-semibold mb-2">Your Account Name</label>
                            <input type="text" name="bank_account_name" class="glass-input w-full px-4 py-3 rounded-xl" placeholder="Sender Name">
                        </div>
                        <div>
                            <label class="block text-xs uppercase tracking-wider text-slate-400 font-semibold mb-2">Reference No.</label>
                            <input type="text" name="bank_reference" class="glass-input w-full px-4 py-3 rounded-xl" placeholder="Ref ID from Receipt">
                        </div>
                        <div>
                            <label class="block text-xs uppercase tracking-wider text-slate-400 font-semibold mb-2">Upload Proof</label>
                            <input type="file" name="payment_proof" accept="image/*" class="glass-input w-full px-4 py-3 rounded-xl file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700">
                        </div>
                    </div>

                    <div id="detailsMessage" class="hidden text-center text-sm text-red-400"></div>

                    <div class="flex gap-3 mt-6 pt-4 border-t border-slate-700">
                        <button type="button" class="payment-details-back flex-1 py-3 bg-transparent border border-slate-600 text-slate-300 hover:text-white rounded-xl transition-colors">Back</button>
                        <button type="submit" id="btnConfirmPayment" class="flex-1 py-3 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl shadow-lg transition-all">
                            <i class="fa-solid fa-lock mr-2"></i> Pay Securely
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="../public/assets/js/payments.js"></script>
</body>
</html>