<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cancel Subscription</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-4 font-sans text-slate-800">

    <div class="max-w-md w-full bg-white rounded-xl shadow-xl overflow-hidden border border-slate-100">
        <div class="bg-amber-50 p-6 text-center border-b border-amber-100">
            <div class="w-16 h-16 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fa-solid fa-triangle-exclamation text-3xl text-amber-500"></i>
            </div>
            <h1 class="text-xl font-bold text-slate-900">Cancel Subscription?</h1>
        </div>

        <div class="p-6">
            <p class="text-slate-600 text-center mb-6">
                Are you sure you want to cancel your current plan? You will lose access to premium features immediately.
            </p>

            <div class="bg-slate-50 rounded-lg p-4 mb-8 border border-slate-200">
                <div class="flex justify-between text-sm mb-2">
                    <span class="text-slate-500">Current Plan:</span>
                    <span class="font-bold text-slate-700"><?= htmlspecialchars($plan_name ?? 'Membership') ?></span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-slate-500">Subscription ID:</span>
                    <span class="font-mono text-slate-700">#<?= htmlspecialchars($subscription_id ?? 'N/A') ?></span>
                </div>
            </div>

            <form method="POST" action="index.php?controller=Subscribe&action=CancelSubscription" class="flex gap-3">
                <input type="hidden" name="confirm_cancel" value="true">
                
                <a href="index.php?controller=Dashboard&action=member" 
                   class="flex-1 py-2.5 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-lg text-center transition-colors">
                    Keep Plan
                </a>
                
                <button type="submit" 
                        class="flex-1 py-2.5 px-4 bg-red-600 hover:bg-red-700 text-white font-bold rounded-lg shadow-sm transition-colors">
                    Yes, Cancel
                </button>
            </form>
        </div>
    </div>

</body>
</html>