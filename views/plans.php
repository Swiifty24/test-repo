<?php 
    $plan_features = [
        1 => [
            "Unlimited gym access",
            "Basic equipment access",
            "Community support",
            "Mobile app access"
        ],
        2 => [
            "Unlimited gym access",
            "All equipment access",
            "Priority support",
            "Mobile app access",
            "1 free personal training session",
            "Group classes included"
        ],
        3 => [
            "24/7 gym access",
            "All equipment access",
            "Priority support",
            "Mobile app access",
            "Unlimited personal training",
            "Unlimited group classes",
            "Nutritionist consultation",
            "Performance tracking"
        ]
    ];

    $current_plan = isset($_SESSION['current_plan']) ? $_SESSION['current_plan'] : null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plans - Gymazing</title>
    
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
            overflow-x: hidden;
        }

        .gradient-bg {
            background-color: #0f172a;
            background-image: 
                radial-gradient(at 0% 0%, hsla(253,16%,7%,1) 0, transparent 50%), 
                radial-gradient(at 50% 0%, hsla(225,39%,30%,1) 0, transparent 50%), 
                radial-gradient(at 100% 0%, hsla(339,49%,30%,1) 0, transparent 50%);
            background-attachment: fixed;
        }

        /* Glass Cards */
        .glass-card {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease;
        }

        .glass-card:hover {
            transform: translateY(-8px);
            border-color: rgba(59, 130, 246, 0.4);
            box-shadow: 0 20px 40px -5px rgba(0, 0, 0, 0.4);
        }

        /* Featured Plan */
        .glass-card.featured {
            background: rgba(30, 41, 59, 0.85);
            border: 2px solid #3b82f6;
            box-shadow: 0 0 20px rgba(59, 130, 246, 0.2);
            transform: scale(1.05);
            z-index: 10;
        }
        .glass-card.featured:hover {
            transform: scale(1.05) translateY(-8px);
        }

        /* Toggle Button Logic */
        #billingToggle {
            transition: background-color 0.3s ease;
        }
        #billingToggle.active {
            background-color: #3b82f6; /* Blue when active */
        }
        .toggle-slider {
            transition: transform 0.3s cubic-bezier(0.4, 0.0, 0.2, 1);
        }
        /* JS handles the translate transform */

        /* FAQ Animation */
        .faq-item {
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 0.75rem;
            background: rgba(30, 41, 59, 0.4);
            overflow: hidden;
            transition: all 0.3s ease;
        }
        .faq-item.active {
            background: rgba(30, 41, 59, 0.7);
            border-color: rgba(59, 130, 246, 0.5);
        }
        .faq-answer {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.4s ease-out;
        }
        .faq-icon {
            transition: transform 0.3s ease;
        }

        /* Comparison Table */
        .glass-table th, .glass-table td {
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        .glass-table tr:hover { background-color: rgba(255, 255, 255, 0.03); }

        /* Modal */
        .modal-backdrop { opacity: 0; visibility: hidden; transition: all 0.3s; backdrop-filter: blur(5px); }
        .modal-backdrop.show { opacity: 1; visibility: visible; }
        .modal-content { transform: scale(0.95); transition: all 0.3s; }
        .modal-backdrop.show .modal-content { transform: scale(1); }
        
        .badge-featured {
            position: absolute; top: 0; left: 50%; transform: translate(-50%, -50%);
            padding: 0.5rem 1.5rem; border-radius: 9999px;
            background: linear-gradient(to right, #3b82f6, #2563eb);
            color: white; font-weight: 700; font-size: 0.75rem;
            text-transform: uppercase; box-shadow: 0 4px 10px rgba(37, 99, 235, 0.4);
        }
    </style>
</head>
<body class="gradient-bg min-h-screen">
    
    <?php include_once __DIR__ . "/layouts/header.php" ?>  

    <div id="alertContainer" class="fixed top-24 right-4 z-50 space-y-4 max-w-sm w-full"></div>

    <main class="pt-24 pb-20">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            
            <section class="text-center max-w-4xl mx-auto mb-16">
                <span class="text-blue-400 font-bold tracking-wider text-sm uppercase mb-2 block">Membership Plans</span>
                <h1 class="text-4xl md:text-6xl font-extrabold text-white mb-6">
                    Invest in your <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-emerald-400">Health</span>
                </h1>
                
                <div class="flex items-center justify-center gap-4 bg-slate-800/50 w-fit mx-auto p-1.5 rounded-full border border-slate-700 mt-8">
                    <span class="text-sm font-medium text-slate-300 pl-4 monthly-label transition-colors">Monthly</span>
                    
                    <button id="billingToggle" class="relative w-14 h-7 bg-slate-600 rounded-full focus:outline-none">
                        <div class="toggle-slider absolute left-1 top-1 w-5 h-5 bg-white rounded-full shadow-sm"></div>
                    </button>
                    
                    <span class="text-sm font-medium text-white pr-4 flex items-center gap-2 yearly-label">
                        Yearly 
                        <span class="text-[10px] bg-emerald-500/20 text-emerald-400 px-2 py-0.5 rounded-full font-bold border border-emerald-500/30">-20%</span>
                    </span>
                </div>
            </section>

            <section class="mb-24 relative">
                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full h-full max-w-4xl max-h-[500px] bg-blue-600/10 blur-[100px] -z-10 rounded-full"></div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 items-start plans-container">
                    <?php if(!empty($plans)) {
                        foreach($plans as $index => $plan) {
                            $is_featured = isset($plan['is_featured']) && $plan['is_featured'] == 1;
                            $features = $plan_features[$index + 1] ?? [];
                    ?>
                        <div class="glass-card <?= $is_featured ? 'featured' : 'bg-slate-800/40' ?> rounded-2xl p-8 flex flex-col h-full relative plan-card" 
                             data-plan-id="<?= $plan['plan_id'] ?>" 
                             data-plan-name="<?= $plan['plan_name'] ?>" 
                             data-plan-price="<?= $plan['price'] ?>">
                            
                            <?php if($is_featured) { ?>
                                <div class="badge-featured">Most Popular</div>
                            <?php } ?>

                            <div class="mb-6">
                                <h3 class="text-xl font-bold text-white mb-2"><?= $plan['plan_name'] ?></h3>
                                <p class="text-slate-400 text-sm"><?= isset($plan['tagline']) ? $plan['tagline'] : 'Everything you need' ?></p>
                            </div>

                            <div class="mb-8 pb-8 border-b border-slate-700/50">
                                <div class="flex items-baseline gap-1">
                                    <span class="text-4xl font-bold text-white">₱<span class="price-value"><?= number_format($plan['price']) ?></span></span>
                                    <span class="text-slate-400 period-text">/mo</span>
                                </div>
                                <p class="text-xs text-emerald-400 mt-2 font-medium save-text hidden">You save 20%</p>
                            </div>

                            <ul class="mb-8 flex-1 space-y-4">
                                <?php foreach($features as $feature) { ?>
                                    <li class="flex items-start gap-3 text-slate-300">
                                        <i class="fas fa-check-circle text-blue-500 mt-1"></i>
                                        <span class="text-sm"><?= $feature ?></span>
                                    </li>
                                <?php } ?>
                            </ul>

                            <button class="btn-subscribe-plan w-full py-3.5 px-6 rounded-xl font-bold text-sm transition-all
                                <?= $is_featured ? 'bg-blue-600 hover:bg-blue-500 text-white shadow-lg shadow-blue-900/20' : 'bg-slate-700 hover:bg-slate-600 text-white' ?>" 
                                data-plan-id="<?= $plan['plan_id'] ?>">
                                <?= $current_plan == $plan['plan_id'] ? 'Current Plan' : 'Choose Plan' ?>
                            </button>
                        </div>
                    <?php }
                    } else { ?>
                        <div class="col-span-full text-center py-12 glass-card rounded-2xl">
                            <p class="text-slate-400">No plans available at the moment.</p>
                        </div>
                    <?php } ?>
                </div>
            </section>

            <section class="mb-24 max-w-5xl mx-auto">
                <h2 class="text-3xl font-bold text-white mb-10 text-center">Compare Features</h2>
                <div class="glass-card rounded-2xl overflow-hidden overflow-x-auto">
                    <table class="glass-table w-full text-left">
                        <thead>
                            <tr>
                                <th class="p-4 text-slate-400 font-medium uppercase text-xs">Features</th>
                                <?php foreach($plans as $plan) { ?>
                                    <th class="p-4 text-center text-white font-bold"><?= $plan['plan_name'] ?></th>
                                <?php } ?>
                            </tr>
                        </thead>
                        <tbody class="text-slate-300 text-sm">
                            <tr>
                                <td class="p-4 font-medium text-white">Gym Access</td>
                                <td class="p-4 text-center">Restricted</td>
                                <td class="p-4 text-center">Unlimited</td>
                                <td class="p-4 text-center text-blue-400 font-bold">24/7 Access</td>
                            </tr>
                            <tr>
                                <td class="p-4 font-medium text-white">Group Classes</td>
                                <td class="p-4 text-center text-slate-500">—</td>
                                <td class="p-4 text-center"><i class="fas fa-check text-emerald-400"></i></td>
                                <td class="p-4 text-center"><i class="fas fa-check text-emerald-400"></i></td>
                            </tr>
                            <tr>
                                <td class="p-4 font-medium text-white">Personal Training</td>
                                <td class="p-4 text-center text-slate-500">—</td>
                                <td class="p-4 text-center">1 Session</td>
                                <td class="p-4 text-center text-blue-400 font-bold">Unlimited</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="max-w-3xl mx-auto mb-16">
                <h2 class="text-3xl font-bold text-white mb-8 text-center">Frequently Asked Questions</h2>
                
                <div class="space-y-4">
                    <div class="faq-item">
                        <button class="faq-toggle w-full px-6 py-4 flex items-center justify-between text-left focus:outline-none">
                            <span class="font-semibold text-white">Can I cancel anytime?</span>
                            <i class="fas fa-chevron-down text-slate-400 faq-icon"></i>
                        </button>
                        <div class="faq-answer">
                            <div class="px-6 pb-4">
                                <p class="text-slate-400 text-sm">Yes, you can cancel your subscription from your dashboard settings. There are no cancellation fees.</p>
                            </div>
                        </div>
                    </div>

                    <div class="faq-item">
                        <button class="faq-toggle w-full px-6 py-4 flex items-center justify-between text-left focus:outline-none">
                            <span class="font-semibold text-white">How does the 3-day trial work?</span>
                            <i class="fas fa-chevron-down text-slate-400 faq-icon"></i>
                        </button>
                        <div class="faq-answer">
                            <div class="px-6 pb-4">
                                <p class="text-slate-400 text-sm">You get full access to all features of your chosen plan for 3 days. You won't be charged until the trial ends.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="faq-item">
                        <button class="faq-toggle w-full px-6 py-4 flex items-center justify-between text-left focus:outline-none">
                            <span class="font-semibold text-white">Can I freeze my membership?</span>
                            <i class="fas fa-chevron-down text-slate-400 faq-icon"></i>
                        </button>
                        <div class="faq-answer">
                            <div class="px-6 pb-4">
                                <p class="text-slate-400 text-sm">Yes, Pro and Elite members can freeze their membership for up to 3 months per year for valid reasons (travel/medical).</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <div id="subscriptionModal" class="<?= isset($openModal) && $openModal ? 'show' : ''?> modal-backdrop fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="modal-content bg-slate-900 rounded-2xl max-w-md w-full border border-slate-700 shadow-2xl relative">
            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-blue-500 to-emerald-500"></div>
            
            <div class="p-8">
                <div class="flex justify-between items-start mb-6">
                    <h3 class="text-2xl font-bold text-white">Confirm Plan</h3>
                    <button class="modal-close text-slate-500 hover:text-white"><i class="fas fa-times text-xl"></i></button>
                </div>
                
                <div class="bg-slate-800/50 rounded-xl p-6 mb-6 border border-slate-700/50 text-center">
                    <p class="text-slate-400 text-xs uppercase tracking-wider mb-1">You are selecting</p>
                    <p class="text-xl font-bold text-blue-400 mb-2" id="modalPlanName"></p>
                    <p class="text-3xl font-bold text-white" id="modalPlanPrice"></p>
                </div>

                <form id="subscriptionForm" action="index.php?controller=Subscribe&action=Subscribe" method="POST">
                    <input type="hidden" id="modal_plan_id" name="plan_id">
                    <label class="flex items-start gap-3 p-4 rounded-lg hover:bg-slate-800/50 transition-colors cursor-pointer mb-6">
                        <input type="checkbox" id="modal_terms" name="terms" required class="mt-1 w-4 h-4 bg-slate-700 border-slate-600 rounded">
                        <span class="text-slate-300 text-sm">I agree to the Terms of Service.</span>
                    </label>
                    <div id="formMessage" class="hidden mb-4 text-red-400 text-sm text-center"></div>
                    <div class="flex gap-3">
                        <button type="button" class="modal-cancel flex-1 px-4 py-3 bg-transparent border border-slate-600 text-slate-300 hover:text-white rounded-xl">Cancel</button>
                        <input type="submit" value="Confirm" id="submitBtn" class="flex-1 px-4 py-3 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-xl cursor-pointer">
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="../public/assets/js/plans.js"></script>

    <script>
        $(document).ready(function() {
            
            // --- 1. FIXED FAQ ACCORDION ---
            $('.faq-toggle').off('click').on('click', function() { // use .off() to prevent double binding
                const $item = $(this).closest('.faq-item');
                const $answer = $item.find('.faq-answer');
                const $icon = $(this).find('.faq-icon');

                // If currently active, close it
                if ($item.hasClass('active')) {
                    $item.removeClass('active');
                    $answer.css('max-height', '0');
                    $icon.css('transform', 'rotate(0deg)');
                } else {
                    // Close others (optional accordion behavior)
                    $('.faq-item').removeClass('active').find('.faq-answer').css('max-height', '0');
                    $('.faq-item').find('.faq-icon').css('transform', 'rotate(0deg)');

                    // Open this one
                    $item.addClass('active');
                    // Use scrollHeight to animate correctly to content height
                    $answer.css('max-height', $answer[0].scrollHeight + 'px');
                    $icon.css('transform', 'rotate(180deg)');
                }
            });

            // --- 2. FIXED YEARLY TOGGLE LOGIC ---
            $('#billingToggle').off('click').on('click', function() {
                const $btn = $(this);
                const $slider = $btn.find('.toggle-slider');
                
                // Toggle active state
                $btn.toggleClass('active');
                const isYearly = $btn.hasClass('active');

                // Animate Slider
                if (isYearly) {
                    $slider.css('transform', 'translateX(28px)'); // Move slider right
                    $('.monthly-label').removeClass('text-white').addClass('text-slate-400');
                    $('.yearly-label').addClass('text-blue-400');
                } else {
                    $slider.css('transform', 'translateX(0)'); // Move slider back
                    $('.monthly-label').addClass('text-white').removeClass('text-slate-400');
                    $('.yearly-label').removeClass('text-blue-400');
                }

                // Update Prices in Real-time
                $('.plan-card').each(function() {
                    const $card = $(this);
                    const originalPrice = parseFloat($card.data('plan-price')); // Get base price from PHP data attribute
                    
                    if (isYearly) {
                        // Calculate Yearly Price (Monthly * 12 * 0.8 discount)
                        const yearlyTotal = (originalPrice * 12 * 0.8).toFixed(0); 
                        // Or show monthly breakdown: (originalPrice * 0.8)
                        const monthlyBreakdown = (originalPrice * 0.8).toFixed(0);

                        // Update Text
                        $card.find('.price-value').text(Number(monthlyBreakdown).toLocaleString());
                        $card.find('.period-text').text('/mo (billed yearly)');
                        $card.find('.save-text').removeClass('hidden'); // Show "Save 20%" badge
                    } else {
                        // Revert to Monthly
                        $card.find('.price-value').text(Number(originalPrice).toLocaleString());
                        $card.find('.period-text').text('/mo');
                        $card.find('.save-text').addClass('hidden');
                    }
                });
            });

            // --- 3. ENSURE MODAL STILL WORKS WITH NEW CLASSES ---
            $('.btn-subscribe-plan').on('click', function() {
                const planId = $(this).data('plan-id');
                const planCard = $(this).closest('.plan-card');
                const planName = planCard.find('.plan-name').text() || planCard.data('plan-name');
                const planPrice = planCard.find('.price-value').text(); // Grab currently displayed price

                $('#modal_plan_id').val(planId);
                $('#modalPlanName').text(planName);
                $('#modalPlanPrice').text('₱' + planPrice); // Show currently toggled price in modal
                
                $('#subscriptionModal').addClass('show');
                $('#subscriptionModal').css('opacity', '1').css('visibility', 'visible');
            });

            $('.modal-close, .modal-cancel').on('click', function() {
                $('#subscriptionModal').removeClass('show');
                setTimeout(() => {
                    $('#subscriptionModal').css('opacity', '0').css('visibility', 'hidden');
                }, 300);
            });
        });
    </script>
</body>
</html>