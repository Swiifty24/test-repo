<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscription Cancelled</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-4 font-sans text-slate-800">

    <div class="max-w-md w-full bg-white rounded-xl shadow-xl overflow-hidden text-center p-8 border border-slate-100">
        <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <i class="fa-solid fa-ban text-4xl text-slate-500"></i>
        </div>

        <h1 class="text-2xl font-bold text-slate-900 mb-2">Subscription Cancelled</h1>
        <p class="text-slate-600 mb-8">
            Your plan has been successfully cancelled. We're sorry to see you go!
        </p>

        <a href="index.php?controller=Dashboard&action=member" 
           class="inline-block w-full py-3 px-4 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg shadow-md transition-colors duration-200">
            Return to Dashboard
        </a>
    </div>

</body>
</html>