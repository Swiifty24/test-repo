<?php
    $testimonies = [
        ["name"=>"John Doe","rating"=>"4","comment"=>"Amazing place to shape your body!"],
        ["name"=>"Jane Smith","rating"=>"5","comment"=>"Gymazing is the best place to go if you're looking for high quality gym!"],
        ["name"=>"John McCaine","rating"=>"4","comment"=>"Great Ambience and Knowledgable Trainers"]
    ];
    $services = [
        ["service"=>"CrossFit Group Classes", "image"=>""],
        ["service"=>"Strength Training", "image"=>""],
        ["service"=>"Personal Training", "image"=>""],
        ["service"=>"Member Only Events", "image"=>""]
    ];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gymazing - Transform Your Body & Mind</title>
    <script src="../public/assets/js/tailwindcss/tailwindcss.js"></script>
    <style href="../public/assets/css/style.css">
        /* Custom gradient background */
        .gradient-bg {
            background: linear-gradient(135deg, #1a1a1a 0%, #2d3748 50%, #1a1a1a 100%);
        }

        /* Animated gradient for hero */
        .hero-gradient {
            background: linear-gradient(135deg, rgba(29, 78, 216, 0.1) 0%, rgba(30, 58, 138, 0.2) 100%);
        }

        /* Smooth transitions */
        * {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 10px;
        }

        ::-webkit-scrollbar-track {
            background: #1a1a1a;
        }

        ::-webkit-scrollbar-thumb {
            background: #1e3a8a;
            border-radius: 5px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #1e40af;
        }

        /* Card hover effects */
        .service-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(29, 78, 216, 0.3);
        }

        .plan-card:hover {
            transform: translateY(-12px);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
        }

        /* Button pulse animation */
        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.8;
            }
        }

        .btn-primary:hover {
            animation: pulse 2s infinite;
        }
    </style>
</head>
<body class="gradient-bg min-h-screen">
    
    <!-- header -->
    <?php include_once '../views/layouts/header.php'?>

    <!-- main content -->
    <main class="min-h-screen">
        <!-- Hero Section -->
        <section class="hero-section relative overflow-hidden">
            <div class="absolute inset-0 hero-gradient"></div>
            <div class="relative container mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-20">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12 items-center">
                    <!-- Left Content -->
                    <div class="text-white space-y-6 lg:space-y-8 order-2 lg:order-1">
                        <div class="space-y-4">
                            <h1 class="font-bold text-4xl sm:text-5xl lg:text-6xl xl:text-7xl leading-tight">
                                Build Perfect Body<br>
                                <span class="text-blue-400">With Clean Mind</span>
                            </h1>
                        </div>
                        <div>
                            <p class="text-base sm:text-lg lg:text-xl text-gray-300 leading-relaxed max-w-xl">
                                Transform your life with our state-of-the-art facilities, expert trainers, and supportive community. Start your fitness journey today and become the best version of yourself.
                            </p>
                        </div>
                        <div class="flex flex-col sm:flex-row gap-4">
                            <button class="btn-primary px-8 py-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transition-all duration-300">
                                Get Started Now
                            </button>
                            <button class="px-8 py-4 border-2 border-blue-500 text-blue-400 hover:bg-blue-500 hover:text-white font-semibold rounded-lg transition-all duration-300">
                                View Plans
                            </button>
                        </div>
                        <div class="flex items-center gap-6 pt-6">
                            <a href="#" class="text-gray-400 hover:text-blue-400 transition-colors duration-300">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                            </a>
                            <a href="#" class="text-gray-400 hover:text-blue-400 transition-colors duration-300">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                            </a>
                            <a href="#" class="text-gray-400 hover:text-blue-400 transition-colors duration-300">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0C8.74 0 8.333.015 7.053.072 5.775.132 4.905.333 4.14.63c-.789.306-1.459.717-2.126 1.384S.935 3.35.63 4.14C.333 4.905.131 5.775.072 7.053.012 8.333 0 8.74 0 12s.015 3.667.072 4.947c.06 1.277.261 2.148.558 2.913.306.788.717 1.459 1.384 2.126.667.666 1.336 1.079 2.126 1.384.766.296 1.636.499 2.913.558C8.333 23.988 8.74 24 12 24s3.667-.015 4.947-.072c1.277-.06 2.148-.262 2.913-.558.788-.306 1.459-.718 2.126-1.384.666-.667 1.079-1.335 1.384-2.126.296-.765.499-1.636.558-2.913.06-1.28.072-1.687.072-4.947s-.015-3.667-.072-4.947c-.06-1.277-.262-2.149-.558-2.913-.306-.789-.718-1.459-1.384-2.126C21.319 1.347 20.651.935 19.86.63c-.765-.297-1.636-.499-2.913-.558C15.667.012 15.26 0 12 0zm0 2.16c3.203 0 3.585.016 4.85.071 1.17.055 1.805.249 2.227.415.562.217.96.477 1.382.896.419.42.679.819.896 1.381.164.422.36 1.057.413 2.227.057 1.266.07 1.646.07 4.85s-.015 3.585-.074 4.85c-.061 1.17-.256 1.805-.421 2.227-.224.562-.479.96-.899 1.382-.419.419-.824.679-1.38.896-.42.164-1.065.36-2.235.413-1.274.057-1.649.07-4.859.07-3.211 0-3.586-.015-4.859-.074-1.171-.061-1.816-.256-2.236-.421-.569-.224-.96-.479-1.379-.899-.421-.419-.69-.824-.9-1.38-.165-.42-.359-1.065-.42-2.235-.045-1.26-.061-1.649-.061-4.844 0-3.196.016-3.586.061-4.861.061-1.17.255-1.814.42-2.234.21-.57.479-.96.9-1.381.419-.419.81-.689 1.379-.898.42-.166 1.051-.361 2.221-.421 1.275-.045 1.65-.06 4.859-.06l.045.03zm0 3.678c-3.405 0-6.162 2.76-6.162 6.162 0 3.405 2.76 6.162 6.162 6.162 3.405 0 6.162-2.76 6.162-6.162 0-3.405-2.76-6.162-6.162-6.162zM12 16c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4zm7.846-10.405c0 .795-.646 1.44-1.44 1.44-.795 0-1.44-.646-1.44-1.44 0-.794.646-1.439 1.44-1.439.793-.001 1.44.645 1.44 1.439z"/></svg>
                            </a>
                        </div>
                    </div>
                    
                    <!-- Right Image -->
                    <div class="order-1 lg:order-2 flex items-center justify-center">
                        <div class="relative w-full max-w-lg">
                            <div class="absolute inset-0 bg-blue-600 rounded-2xl blur-3xl opacity-20"></div>
                            <img src="assets/images/image.png" alt="Fitness Training" class="relative rounded-2xl shadow-2xl w-full h-auto object-cover">
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Services Section -->
        <section class="py-16 lg:py-24 bg-neutral-900" id="about">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12 lg:mb-16">
                    <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-white mb-4">
                        We Offer Something For <span class="text-blue-400">Everybody</span>
                    </h2>
                    <p class="text-gray-400 text-lg max-w-2xl mx-auto">
                        Discover our comprehensive range of fitness services designed to help you achieve your goals
                    </p>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 lg:gap-8">
                    <?php foreach ($services as $index => $service) {?>
                        <div class="service-card bg-gradient-to-br from-gray-800 to-gray-900 rounded-xl p-8 shadow-xl border border-gray-700 hover:border-blue-500 cursor-pointer">
                            <div class="flex flex-col items-center text-center space-y-4">
                                <div class="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center">
                                    <span class="text-2xl font-bold text-white"><?= $index + 1 ?></span>
                                </div>
                                <h3 class="text-xl font-bold text-white"><?= $service['service'] ?></h3>
                                <p class="text-gray-400 text-sm">Expert guidance and support for your fitness journey</p>
                            </div>
                        </div>
                    <?php }?>
                </div>
            </div>
        </section>

        <!-- Membership Plans Section -->
        <section class="py-16 lg:py-24 bg-zinc-800" id="plans">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12 lg:mb-16">
                    <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-white mb-4">
                        Your Fitness Journey <span class="text-blue-400">Starts Here</span>
                    </h2>
                    <p class="text-gray-400 text-lg">Choose a plan that suits you best</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 lg:gap-10 max-w-7xl mx-auto">
                    <?php foreach($plans as $plan) { if($plan['status'] != 'inactive' && $plan['status'] != "removed") { ?>
                        <div class="plan-card bg-white rounded-2xl shadow-2xl overflow-hidden flex flex-col transition-all duration-250 ease-in-out">
                            <div class="bg-gradient-to-r from-blue-600 to-blue-800 p-8 text-center text-white">
                                <h3 class="text-3xl font-bold mb-2"><?= $plan['plan_name'] ?></h3>
                                <div class="text-5xl font-bold">
                                    <?= "₱" . number_format($plan['price']) ?>
                                    <span class="text-lg font-normal">/mo</span>
                                </div>
                            </div>
                            
                            <div class="flex-1 p-8 flex flex-col">
                                <p class="text-gray-700 text-center mb-8 leading-relaxed flex-1">
                                    <?= $plan['description'] ?>
                                </p>
                                
                                <div class="space-y-4">
                                    <p class="text-sm text-gray-500 text-center">
                                        Charges every month unless you cancel
                                    </p>
                                    <button class="w-full px-6 py-4 bg-gradient-to-r from-blue-600 to-blue-800 hover:from-blue-700 hover:to-blue-900 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                                        Start 3 Day Free Trial Now
                                    </button>
                                </div>
                            </div>
                        </div>    
                    <?php } }?>
                </div>
            </div>
        </section>

        <!-- Testimonials Section -->
        <section class="py-16 lg:py-24 bg-neutral-900">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12 lg:mb-16">
                    <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-white mb-4">
                        What Our <span class="text-blue-400">Members Say</span>
                    </h2>
                    <p class="text-gray-400 text-lg">Real stories from real people</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 max-w-6xl mx-auto">
                    <?php foreach($testimonies as $testimony) { ?>
                        <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-xl p-8 shadow-xl border border-gray-700 hover:border-blue-500 transition-all duration-300">
                            <div class="flex items-center mb-4">
                                <?php for($i = 0; $i < 5; $i++) { ?>
                                    <svg class="w-5 h-5 <?= $i < $testimony['rating'] ? 'text-yellow-400' : 'text-gray-600' ?>" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                <?php } ?>
                            </div>
                            <p class="text-gray-300 mb-6 leading-relaxed">"<?= $testimony['comment'] ?>"</p>
                            <p class="text-white font-semibold">- <?= $testimony['name'] ?></p>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </section>

        <!-- Contact Section -->
        <section id="contact" class="py-16 lg:py-24 bg-zinc-800">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12 lg:mb-16">
                    <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-white mb-4">
                        Get In <span class="text-blue-400">Touch</span>
                    </h2>
                    <p class="text-gray-400 text-lg">Have questions? We'd love to hear from you</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 max-w-6xl mx-auto">
                    <!-- Contact Info -->
                    <div class="space-y-8">
                        <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-xl p-8 border border-gray-700">
                            <h3 class="text-2xl font-bold text-white mb-6">Contact Information</h3>
                            
                            <div class="space-y-6">
                                <!-- Location -->
                                <div class="flex items-start space-x-4">
                                    <div class="flex-shrink-0 w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="text-white font-semibold mb-1">Location</h4>
                                        <p class="text-gray-400">Zamboanga City, Zamboanga Del Sur<br>Philippines</p>
                                    </div>
                                </div>

                                <!-- Phone -->
                                <div class="flex items-start space-x-4">
                                    <div class="flex-shrink-0 w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="text-white font-semibold mb-1">Phone</h4>
                                        <p class="text-gray-400">+63 123 456 7890</p>
                                    </div>
                                </div>

                                <!-- Email -->
                                <div class="flex items-start space-x-4">
                                    <div class="flex-shrink-0 w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="text-white font-semibold mb-1">Email</h4>
                                        <p class="text-gray-400">info@gymazing.com</p>
                                    </div>
                                </div>

                                <!-- Hours -->
                                <div class="flex items-start space-x-4">
                                    <div class="flex-shrink-0 w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="text-white font-semibold mb-1">Opening Hours</h4>
                                        <p class="text-gray-400">Mon - Fri: 5:00 AM - 11:00 PM<br>Sat - Sun: 6:00 AM - 10:00 PM</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Social Media -->
                            <div class="mt-8 pt-6 border-t border-gray-700">
                                <h4 class="text-white font-semibold mb-4">Follow Us</h4>
                                <div class="flex space-x-4">
                                    <a href="#" class="w-10 h-10 bg-gray-700 hover:bg-blue-600 rounded-lg flex items-center justify-center transition-all duration-300">
                                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                        </svg>
                                    </a>
                                    <a href="#" class="w-10 h-10 bg-gray-700 hover:bg-blue-600 rounded-lg flex items-center justify-center transition-all duration-300">
                                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                                        </svg>
                                    </a>
                                    <a href="#" class="w-10 h-10 bg-gray-700 hover:bg-blue-600 rounded-lg flex items-center justify-center transition-all duration-300">
                                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 0C8.74 0 8.333.015 7.053.072 5.775.132 4.905.333 4.14.63c-.789.306-1.459.717-2.126 1.384S.935 3.35.63 4.14C.333 4.905.131 5.775.072 7.053.012 8.333 0 8.74 0 12s.015 3.667.072 4.947c.06 1.277.261 2.148.558 2.913.306.788.717 1.459 1.384 2.126.667.666 1.336 1.079 2.126 1.384.766.296 1.636.499 2.913.558C8.333 23.988 8.74 24 12 24s3.667-.015 4.947-.072c1.277-.06 2.148-.262 2.913-.558.788-.306 1.459-.718 2.126-1.384.666-.667 1.079-1.335 1.384-2.126.296-.765.499-1.636.558-2.913.06-1.28.072-1.687.072-4.947s-.015-3.667-.072-4.947c-.06-1.277-.262-2.149-.558-2.913-.306-.789-.718-1.459-1.384-2.126C21.319 1.347 20.651.935 19.86.63c-.765-.297-1.636-.499-2.913-.558C15.667.012 15.26 0 12 0zm0 2.16c3.203 0 3.585.016 4.85.071 1.17.055 1.805.249 2.227.415.562.217.96.477 1.382.896.419.42.679.819.896 1.381.164.422.36 1.057.413 2.227.057 1.266.07 1.646.07 4.85s-.015 3.585-.074 4.85c-.061 1.17-.256 1.805-.421 2.227-.224.562-.479.96-.899 1.382-.419.419-.824.679-1.38.896-.42.164-1.065.36-2.235.413-1.274.057-1.649.07-4.859.07-3.211 0-3.586-.015-4.859-.074-1.171-.061-1.816-.256-2.236-.421-.569-.224-.96-.479-1.379-.899-.421-.419-.69-.824-.9-1.38-.165-.42-.359-1.065-.42-2.235-.045-1.26-.061-1.649-.061-4.844 0-3.196.016-3.586.061-4.861.061-1.17.255-1.814.42-2.234.21-.57.479-.96.9-1.381.419-.419.81-.689 1.379-.898.42-.166 1.051-.361 2.221-.421 1.275-.045 1.65-.06 4.859-.06l.045.03zm0 3.678c-3.405 0-6.162 2.76-6.162 6.162 0 3.405 2.76 6.162 6.162 6.162 3.405 0 6.162-2.76 6.162-6.162 0-3.405-2.76-6.162-6.162-6.162zM12 16c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4zm7.846-10.405c0 .795-.646 1.44-1.44 1.44-.795 0-1.44-.646-1.44-1.44 0-.794.646-1.439 1.44-1.439.793-.001 1.44.645 1.44 1.439z"/>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Form -->
                    <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-xl p-8 border border-gray-700">
                        <h3 class="text-2xl font-bold text-white mb-6">Send Us a Message</h3>
                        
                        <form action="contact_handler.php" method="POST" class="space-y-6">
                            <!-- Name -->
                            <div>
                                <label for="name" class="block text-white font-semibold mb-2">Full Name</label>
                                <input type="text" id="name" name="name" required
                                       class="w-full px-4 py-3 bg-gray-700 text-white border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300"
                                       placeholder="John Doe">
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-white font-semibold mb-2">Email Address</label>
                                <input type="email" id="email" name="email" required
                                       class="w-full px-4 py-3 bg-gray-700 text-white border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300"
                                       placeholder="john@example.com">
                            </div>

                            <!-- Phone -->
                            <div>
                                <label for="phone" class="block text-white font-semibold mb-2">Phone Number</label>
                                <input type="tel" id="phone" name="phone"
                                       class="w-full px-4 py-3 bg-gray-700 text-white border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300"
                                       placeholder="+63 123 456 7890">
                            </div>

                            <!-- Subject -->
                            <div>
                                <label for="subject" class="block text-white font-semibold mb-2">Subject</label>
                                <select id="subject" name="subject" required
                                        class="w-full px-4 py-3 bg-gray-700 text-white border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300">
                                    <option value="">Select a subject</option>
                                    <option value="membership">Membership Inquiry</option>
                                    <option value="training">Personal Training</option>
                                    <option value="facilities">Facilities</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>

                            <!-- Message -->
                            <div>
                                <label for="message" class="block text-white font-semibold mb-2">Message</label>
                                <textarea id="message" name="message" rows="4" required
                                          class="w-full px-4 py-3 bg-gray-700 text-white border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 resize-none"
                                          placeholder="Tell us how we can help you..."></textarea>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit"
                                    class="w-full px-6 py-4 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                                Send Message
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- footer -->
    <?php include '../views/layouts/footer.php' ?>
    
    <script src="../assets/public/js/jquery/juery-3.7.1.min.js"></script>
</body>
</html>