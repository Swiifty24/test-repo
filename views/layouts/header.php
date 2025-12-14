<?php
    $nav = [
        ["navItem" => "Home", "navLink" => "index.php"],
        ["navItem" => "About", "navLink" => "#about"],
        ["navItem" => "Plans", "navLink" => "#plans"],
        ["navItem" => "Contact", "navLink" => "#contact"]
    ];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gymazing!</title>
    <script src="../public/assets/js/tailwindcss/tailwindcss.js"></script>
    <script src="../public/assets/js/jquery/jquery-3.7.1.min.js"></script>
    <style>
        /* Smooth scroll behavior */
        html {
            scroll-behavior: smooth;
        }

        /* Header backdrop blur effect */
        .header-blur {
            backdrop-filter: blur(10px);
            background: rgba(23, 23, 23, 0.8);
        }

        /* Mobile menu animation */
        .mobile-menu {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-in-out;
        }

        .mobile-menu.active {
            max-height: 500px;
        }

        /* Hamburger animation */
        .hamburger span {
            transition: all 0.3s ease-in-out;
        }

        .hamburger.active span:nth-child(1) {
            transform: rotate(45deg) translate(5px, 5px);
        }

        .hamburger.active span:nth-child(2) {
            opacity: 0;
        }

        .hamburger.active span:nth-child(3) {
            transform: rotate(-45deg) translate(7px, -6px);
        }

        /* Nav link hover effect */
        .nav-link {
            position: relative;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -4px;
            left: 0;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, #2563eb, #3b82f6);
            transition: width 0.3s ease;
        }

        .nav-link:hover::after {
            width: 100%;
        }

        /* Sticky header shadow */
        .header-shadow {
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }
    </style>
</head>
<body>
    <header class="header-blur header-shadow fixed top-0 left-0 right-0 z-50 transition-all duration-300">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between py-4 lg:py-5">
                <!-- Logo -->
                <div class="flex items-center space-x-2">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-700 rounded-lg flex items-center justify-center shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <h1 class="font-bold text-white text-xl lg:text-2xl tracking-wider">
                        GYM<span class="text-blue-400">AZING</span>
                    </h1>
                </div>

                <!-- Desktop Navigation -->
                <nav class="hidden lg:flex items-center space-x-1">
                    <ul class="flex items-center space-x-1">
                        <?php foreach ($nav as $item) { ?>
                            <li>
                                <a href="<?= $item['navLink'] ?>" 
                                   class="nav-link px-4 py-2 text-white font-medium hover:text-blue-400 transition-colors duration-200">
                                    <?= $item['navItem'] ?>
                                </a>
                            </li>
                        <?php } ?>
                    </ul>
                </nav>

                <!-- Desktop Auth Buttons -->
                <div class="hidden lg:flex items-center space-x-3">
                    <?php if(isset($_SESSION['user_id'])) { ?>
                        <!-- Logged in user menu -->
                        <div class="relative group">
                            <button id="account" class="flex items-center space-x-2 px-4 py-2 text-white font-medium hover:text-blue-400 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                <span>Account</span>
                            </button>
                            <div id="account_menu" class="hidden absolute right-0 mt-2 w-48 bg-gray-800 rounded-lg shadow-xl  transition-all duration-200">
                                <?php $_SESSION['role'] == ""?>
                                <?php if($_SESSION['role'] == 'member') { ?>
                                    <a href="index.php?controller=user&action=profile" class="block px-4 py-3 text-white hover:bg-gray-700 transition-colors">Profile</a>
                                    <a href="index.php?controller=Dashboard&action=member" class="block px-4 py-3 text-white hover:bg-gray-700 transition-colors">Dashboard</a>
                                    <a href="../views/auth/logout.php" class="block px-4 py-3 text-white hover:bg-gray-700 rounded-b-lg transition-colors">Logout</a>    
                                <?php } else if($_SESSION['role'] == 'admin') { ?>
                                    <a href="index.php?controller=user&action=profile" class="block px-4 py-3 text-white hover:bg-gray-700 transition-colors">Profile</a>
                                    <a href="index.php?controller=Admin&action=dashboard" class="block px-4 py-3 text-white hover:bg-gray-700 transition-colors">Dashboard</a>
                                    <a href="../views/auth/logout.php" class="block px-4 py-3 text-white hover:bg-gray-700 rounded-b-lg transition-colors">Logout</a>
                                <?php } else if($_SESSION['role'] == 'trainer') { ?>
                                    <a href="index.php?controller=user&action=profile" class="block px-4 py-3 text-white hover:bg-gray-700 transition-colors">Profile</a>
                                    <a href="index.php?controller=Trainer&action=trainerDashboard" class="block px-4 py-3 text-white hover:bg-gray-700 transition-colors">Dashboard</a>
                                    <a href="../views/auth/logout.php" class="block px-4 py-3 text-white hover:bg-gray-700 rounded-b-lg transition-colors">Logout</a>
                                <?php }?>
                            </div>
                        </div>
                    <?php } else { ?>
                        <!-- Guest buttons -->
                        <a href="index.php?controller=auth&action=login" 
                           class="px-5 py-2.5 text-white font-semibold hover:text-blue-400 transition-colors duration-200">
                            Login
                        </a>
                        <a href="index.php?controller=auth&action=register" 
                           class="px-5 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105">
                            Get Started
                        </a>
                    <?php } ?>
                </div>

                <!-- Mobile Menu Button -->
                <button class="lg:hidden hamburger flex flex-col space-y-1.5 p-2" id="mobileMenuBtn">
                    <span class="w-6 h-0.5 bg-white block"></span>
                    <span class="w-6 h-0.5 bg-white block"></span>
                    <span class="w-6 h-0.5 bg-white block"></span>
                </button>
            </div>

            <!-- Mobile Navigation -->
            <div class="mobile-menu lg:hidden" id="mobileMenu">
                <nav class="py-4 border-t border-gray-700">
                    <ul class="space-y-1">
                        <?php foreach ($nav as $item) { ?>
                            <li>
                                <a href="<?= $item['navLink'] ?>" 
                                   class="block px-4 py-3 text-white font-medium hover:bg-gray-800 hover:text-blue-400 rounded-lg transition-all duration-200">
                                    <?= $item['navItem'] ?>
                                </a>
                            </li>
                        <?php } ?>
                    </ul>
                    
                    <!-- Mobile Auth Buttons -->
                    <div class="mt-4 pt-4 border-t border-gray-700 space-y-2 px-4">
                        <?php if(isset($_SESSION['user_id'])) { ?>
                            <a href="dashboard.php" 
                               class="block w-full px-4 py-3 text-center text-white font-semibold hover:bg-gray-800 rounded-lg transition-all duration-200">
                                Dashboard
                            </a>
                            <a href="index.php?controller=user&action=profile" 
                               class="block w-full px-4 py-3 text-center text-white font-semibold hover:bg-gray-800 rounded-lg transition-all duration-200">
                                Profile
                            </a>
                            <a href="logout.php" 
                               class="block w-full px-4 py-3 text-center text-white font-semibold hover:bg-gray-800 rounded-lg transition-all duration-200">
                                Logout
                            </a>
                        <?php } else { ?>
                            <a href="login.php" 
                               class="block w-full px-4 py-3 text-center text-white font-semibold border-2 border-blue-500 hover:bg-blue-500 rounded-lg transition-all duration-200">
                                Login
                            </a>
                            <a href="register.php" 
                               class="block w-full px-4 py-3 text-center bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold rounded-lg shadow-lg transition-all duration-200">
                                Get Started
                            </a>
                        <?php } ?>
                    </div>
                </nav>
            </div>
        </div>
    </header>

    <!-- Spacer to prevent content from hiding under fixed header -->
    <div class="h-20 lg:h-24"></div>

    <script>
        $(document).ready(function() {
            $('#account').click((e) => {
                e.stopPropagation();
                $('#account_menu').toggleClass('hidden');
            });
            // Mobile menu toggle
            $('#mobileMenuBtn').click(() => {
                $(this).toggleClass('active');
                $('#mobileMenu').toggleClass('active');
            });

            // Close mobile menu when clicking a link
            $('#mobileMenu a').click(function() {
                $('#mobileMenuBtn').removeClass('active');
                $('#mobileMenu').removeClass('active');
            });

            // Add shadow on scroll
            $(window).scroll(function() {
                if ($(this).scrollTop() > 10) {
                    $('header').addClass('header-shadow');
                } else {
                    $('header').removeClass('header-shadow');
                }
            });

            // Smooth scroll for anchor links
            $('a[href^="#"]').click(function(e) {
                var target = $(this.hash);
                if (target.length) {
                    e.preventDefault();
                    $('html, body').animate({
                        scrollTop: target.offset().top - 100
                    }, 800);
                }
            });
        });
    </script>
</body>
</html>