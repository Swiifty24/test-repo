<?php
include_once "./../App/models/User.php";
if(session_status() == PHP_SESSION_NONE) {
    session_set_cookie_params(['path' => "/"]);
    session_start();
}
$userobj = new User();

$me = $userobj->getMember($_SESSION['user_id']);

$user_name = $me['first_name'];
$user_initial = substr($user_name, 0, 1);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="../../public/assets/js/tailwindcss/tailwindcss.js"></script>
    <script src="../../public/assets/js/jquery/jquery-3.7.1.min.js"></script>
    <style>
        /* Notification styles */
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        
        .notification-item {
            animation: slideIn 0.3s ease-out;
        }
        
        .notification-badge {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }

        #notificationDropdown {
            max-height: 500px;
        }

        .nav-link {
            position: relative;
            transition: all 0.3s ease;
        }

        .nav-link.active {
            background-color: rgba(59, 130, 246, 0.1);
            color: #3b82f6;
            border-left: 4px solid #3b82f6;
        }

        /* Mobile overlay */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 39;
        }

        .sidebar-overlay.active {
            display: block;
        }

        /* Mobile sidebar */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease-in-out;
            }

            .sidebar.active {
                transform: translateX(0);
            }
        }

        .submenu {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-in-out;
        }

        .submenu.active {
            max-height: 300px;
        }

        .dropdown-toggle {
            transition: transform 0.3s ease;
        }

        .dropdown-toggle.active {
            transform: rotate(180deg);
        }
    </style>
</head>
<body>
    <header class="md:hidden fixed top-0 left-0 right-0 z-50 bg-neutral-900 border-b border-gray-800 shadow-lg">
        <div class="flex items-center justify-between p-4">
            <div class="flex items-center space-x-2">
                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-700 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <h1 class="font-bold text-white text-xl">
                    GYM<span class="text-blue-400">AZING</span>
                </h1>
            </div>

            <button id="mobileMenuBtn" class="text-white p-2 hover:bg-gray-800 rounded-lg">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
        </div>
    </header>

    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <div class="sidebar fixed inset-y-0 left-0 w-64 bg-neutral-900 border-r border-gray-800 flex flex-col z-40">
        <div class="p-6 border-b border-gray-800">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-700 rounded-lg flex items-center justify-center shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-white">GYM<span class="text-blue-400">AZING</span></h1>
                        <p class="text-xs text-gray-400">Member Portal</p>
                    </div>
                </div>
                <button id="closeSidebarBtn" class="md:hidden text-gray-400 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        <div class="p-4 border-b border-gray-800 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-700 rounded-full flex items-center justify-center text-white font-bold shadow-lg text-lg">
                    <?= $user_initial ?>
                </div>
                <div class="flex-1">
                    <p class="text-white font-semibold"><?= $user_name ?></p>
                    <p class="text-xs text-gray-400">Active Member</p>
                </div>
            </div>

            <div class="relative">
                <button onclick="toggleNotifications(event)" class="relative p-2 text-gray-400 hover:text-white hover:bg-gray-800 rounded-full transition focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <span id="sidebarNotificationBadge" class="hidden absolute top-1 right-1 h-2.5 w-2.5 bg-red-500 rounded-full border-2 border-neutral-900"></span>
                </button>

                <div id="notificationDropdown" class="hidden fixed z-50 w-80 bg-gray-800 rounded-lg shadow-xl border border-gray-700 max-h-[500px]">
                    <div class="flex items-center justify-between p-4 border-b border-gray-700">
                        <h3 class="font-semibold text-lg text-white">Notifications</h3>
                        <button onclick="markAllAsRead()" class="text-sm text-blue-400 hover:text-blue-300 transition">
                            Mark all as read
                        </button>
                    </div>
                    
                    <div id="notificationList" class="max-h-96 overflow-y-auto">
                        <div class="p-8 text-center text-gray-400">
                            <svg class="w-12 h-12 mx-auto mb-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                            </svg>
                            <p>Loading...</p>
                        </div>
                    </div>
                    
                    <div class="p-3 border-t border-gray-700 text-center">
                        <a href="notifications.php" class="text-sm text-blue-400 hover:text-blue-300 transition">
                            View all notifications
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
            <a href="index.php?controller=Home&action=index" class="nav-link block px-4 py-3 rounded-lg text-white font-medium hover:bg-gray-800 hover:text-blue-400 transition">
                <svg class="w-5 h-5 inline-block mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Home
            </a>
            
            <a href="index.php?controller=Dashboard&action=member" class="nav-link block px-4 py-3 rounded-lg text-white font-medium hover:bg-gray-800 hover:text-blue-400 transition">
                <svg class="w-5 h-5 inline-block mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Dashboard
            </a>
            <a href="index.php?controller=user&action=profile" class="nav-link block px-4 py-3 rounded-lg text-white font-medium hover:bg-gray-800 hover:text-blue-400 transition">
                <svg class="w-5 h-5 inline-block mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                Profile
            </a>
            <!-- <a href="classes.php" class="nav-link block px-4 py-3 rounded-lg text-white font-medium hover:bg-gray-800 hover:text-blue-400 transition">
                <svg class="w-5 h-5 inline-block mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                Classes
            </a> -->

            <a href="index.php?controller=Payment&action=planPayment" class="nav-link block px-4 py-3 rounded-lg text-white font-medium hover:bg-gray-800 hover:text-blue-400 transition">
                <svg class="w-5 h-5 inline-block mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
                My Payments
            </a>

            <div class="pt-4 border-t border-gray-800 mt-4">
                <button id="moreOptionsBtn" class="w-full flex items-center justify-between px-4 py-3 rounded-lg text-white font-medium hover:bg-gray-800 hover:text-blue-400 transition">
                    <span class="flex items-center">
                        <svg class="w-5 h-5 inline-block mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                        </svg>
                        More Options
                    </span>
                    <svg class="w-4 h-4 dropdown-toggle" id="moreOptionsToggle" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div class="submenu" id="moreOptionsMenu">
                    <a href="index.php?controller=User&action=editProfile" class="block px-4 py-2 ml-8 text-gray-400 hover:text-blue-400 rounded transition-colors text-sm">
                        Edit Profile
                    </a>
                    <a href="settings.php" class="block px-4 py-2 ml-8 text-gray-400 hover:text-blue-400 rounded transition-colors text-sm">
                        Settings
                    </a>
                    <a href="billing.php" class="block px-4 py-2 ml-8 text-gray-400 hover:text-blue-400 rounded transition-colors text-sm">
                        Billing
                    </a>
                </div>
            </div>
        </nav>

        <div class="p-4 border-t border-gray-800">
            <a href="../views/auth/logout.php" class="block px-4 py-3 rounded-lg text-red-400 font-medium hover:bg-gray-800 transition text-center">
                <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                Logout
            </a>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Mobile menu toggle
            $('#mobileMenuBtn').click(function() {
                $('.sidebar').addClass('active');
                $('#sidebarOverlay').addClass('active');
                $('body').css('overflow', 'hidden');
            });

            // Close sidebar
            function closeSidebar() {
                $('.sidebar').removeClass('active');
                $('#sidebarOverlay').removeClass('active');
                $('body').css('overflow', '');
            }

            $('#closeSidebarBtn, #sidebarOverlay').click(closeSidebar);

            // Close sidebar when clicking a navigation link (mobile)
            $('.nav-link').click(function() {
                if (window.innerWidth < 768) {
                    closeSidebar();
                }
            });

            // More options dropdown
            $('#moreOptionsBtn').click(function() {
                $('#moreOptionsToggle').toggleClass('active');
                $('#moreOptionsMenu').toggleClass('active');
            });

            // Set active nav link based on current page
            var currentPage = window.location.pathname.split('/').pop();
            $('.nav-link').each(function() {
                var href = $(this).attr('href');
                if (href === currentPage || (currentPage === '' && href === 'dashboard.php')) {
                    $(this).addClass('active');
                }
            });

            // Initialize notification system
            updateUnreadCount();
        });

        // ============================================
        // NOTIFICATION FUNCTIONS
        // ============================================
        let notificationDropdownOpen = false;

        function toggleNotifications(event) {
            const dropdown = document.getElementById('notificationDropdown');
            notificationDropdownOpen = !notificationDropdownOpen;
            
            if(notificationDropdownOpen) {
                // Calculate position based on the button clicked
                const button = event.currentTarget;
                const rect = button.getBoundingClientRect();
                
                // Set styles for fixed position
                // Align top of dropdown with top of bell (or slightly below)
                dropdown.style.top = rect.top + 'px'; 
                // Position to the right of the bell
                dropdown.style.left = (rect.right + 15) + 'px'; 
                
                dropdown.classList.remove('hidden');
                loadNotifications();
            } else {
                dropdown.classList.add('hidden');
            }
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const notificationButton = event.target.closest('button[onclick^="toggleNotifications"]');
            const dropdown = document.getElementById('notificationDropdown');
            
            // Check if click is outside both button and dropdown
            if(!notificationButton && dropdown && !dropdown.contains(event.target)) {
                dropdown.classList.add('hidden');
                notificationDropdownOpen = false;
            }
        });

        function loadNotifications() {
            fetch('index.php?controller=notification&action=getNotifications')
                .then(response => response.json())
                .then(notifications => {
                    displayNotifications(notifications);
                })
                .catch(error => {
                    console.error('Error loading notifications:', error);
                    document.getElementById('notificationList').innerHTML = 
                        '<div class="p-8 text-center text-gray-400"><p>Error loading notifications</p></div>';
                });
        }

        function displayNotifications(notifications) {
            const list = document.getElementById('notificationList');
            
            if(notifications.length === 0) {
                list.innerHTML = `
                    <div class="p-8 text-center text-gray-400">
                        <svg class="w-12 h-12 mx-auto mb-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                        </svg>
                        <p>No notifications</p>
                    </div>
                `;
                return;
            }
            
            list.innerHTML = notifications.map(notif => `
                <div class="notification-item p-4 border-b border-gray-700 hover:bg-gray-700 cursor-pointer ${notif.is_read == 0 ? 'bg-gray-750' : ''}" 
                     onclick="markAsRead(${notif.notification_id}, '${notif.link || '#'}')">
                    <div class="flex items-start">
                        <div class="mr-3 mt-1">
                            ${getNotificationIcon(notif.type)}
                        </div>
                        
                        <div class="flex-1">
                            <div class="flex items-center justify-between">
                                <h4 class="font-semibold text-sm ${notif.is_read == 0 ? 'text-white' : 'text-gray-300'}">${notif.title}</h4>
                                ${notif.is_read == 0 ? '<span class="w-2 h-2 bg-blue-500 rounded-full"></span>' : ''}
                            </div>
                            <p class="text-sm text-gray-400 mt-1">${notif.message}</p>
                            <p class="text-xs text-gray-500 mt-1">${timeAgo(notif.created_at)}</p>
                        </div>
                    </div>
                </div>
            `).join('');
        }

        function getNotificationIcon(type) {
            const icons = {
                success: '<svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>',
                error: '<svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>',
                warning: '<svg class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>',
                info: '<svg class="w-5 h-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>'
            };
            return icons[type] || icons.info;
        }

        function markAsRead(notificationId, link) {
            fetch('index.php?controller=notification&action=markAsRead', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'notification_id=' + notificationId
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    updateUnreadCount();
                    if(link && link !== '#' && link !== 'null') {
                        window.location.href = link;
                    } else {
                        loadNotifications(); // Refresh the list
                    }
                }
            })
            .catch(error => console.error('Error:', error));
        }

        function markAllAsRead() {
            fetch('index.php?controller=notification&action=markAllAsRead', {
                method: 'POST'
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    loadNotifications();
                    updateUnreadCount();
                }
            })
            .catch(error => console.error('Error:', error));
        }

        function updateUnreadCount() {
            fetch('index.php?controller=notification&action=getUnreadCount')
                .then(response => response.json())
                .then(data => {
                    const badge = document.getElementById('sidebarNotificationBadge');
                    if(data.count > 0) {
                        // Badge logic for red dot (simple toggle)
                        badge.classList.remove('hidden');
                    } else {
                        badge.classList.add('hidden');
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        function timeAgo(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const seconds = Math.floor((now - date) / 1000);
            
            const intervals = {
                year: 31536000,
                month: 2592000,
                week: 604800,
                day: 86400,
                hour: 3600,
                minute: 60
            };
            
            for(let interval in intervals) {
                const count = Math.floor(seconds / intervals[interval]);
                if(count >= 1) {
                    return count + ' ' + interval + (count > 1 ? 's' : '') + ' ago';
                }
            }
            
            return 'Just now';
        }

        // Auto-refresh every 30 seconds
        setInterval(updateUnreadCount, 30000);
    </script>
</body>
</html>