<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cancellation Failed</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-4 font-sans text-slate-800">

    <div class="max-w-md w-full bg-white rounded-xl shadow-xl overflow-hidden text-center p-8 border border-slate-100">
        <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <i class="fa-solid fa-circle-exclamation text-4xl text-red-500"></i>
        </div>

        <h1 class="text-2xl font-bold text-slate-900 mb-2">Cancellation Failed</h1>
        <p class="text-slate-600 mb-2">
            We couldn't cancel your subscription at this moment.
        </p>
        
        <?php if (!empty($error_message)): ?>
            <p class="text-red-500 text-sm mb-6 bg-red-50 p-2 rounded border border-red-100">
                <?= htmlspecialchars($error_message) ?>
            </p>
        <?php endif; ?>

        <div class="flex gap-4">
            <a href="index.php?controller=Dashboard&action=member" 
               class="flex-1 py-2.5 px-4 bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold rounded-lg transition-colors">
                Back
            </a>
            <a href="index.php?controller=Subscription&action=CancelSubscription" 
               class="flex-1 py-2.5 px-4 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg transition-colors">
                Try Again
            </a>
        </div>
    </div>

</body>
</html>