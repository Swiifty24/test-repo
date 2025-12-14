<?php 

    require_once __DIR__ . "/../Controller.php";
    require_once __DIR__ . "/../models/Plan.php";
    require_once __DIR__ . "/../config/Database.php";
    require_once __DIR__ . "/../models/User.php";
    require_once __DIR__ . "/../models/Plan.php";
    require_once __DIR__ . "/../models/Subscription.php";
    require_once __DIR__ . "/../models/Payment.php";
    require_once __DIR__ . "/../models/Trainer.php";
    require_once __DIR__ . "/../models/notification.php";

    class AdminController extends Controller {

        public function dashboard() {
            $this->requireLogin();
            $user_id = $_SESSION['user_id'];

            $user = new User();
            $plan = new Plan();
            $payment = new Payment();
            $subscription = new Subscription();
            $trainerModel = new Trainer();

            $members = $user->displayAllUsers();
            $memberCount = $user->countActiveMembers();
            $totalEarned = $payment->totalEarned();
            $trainers = $trainerModel->getAllTrainers();
            $paymentDetails = $subscription->getUserPayments();
            $totalPayments = $subscription->countTotalPayments();
            $walk_ins = $user->displayAllWalkInMembers();
            $plans = $plan->getAllPlans();
            $activePlans = $plan->getAllActivePlans();
            
            // Get pending freeze requests
            $freezeRequests = $subscription->getPendingFreezeRequests();

            $this->adminView('dashboard', [
                'memberCount' => $memberCount,
                'totalEarned' => $totalEarned,
                'paymentDetails' => $paymentDetails,
                'totalPayments' =>  $totalPayments,
                'trainers' => $trainers,
                'walk_ins' => $walk_ins,
                'members' => $members,
                'plans' => $plans,
                'activePlans' => $activePlans,
                'freezeRequests' => $freezeRequests
            ]);
        } 
        public function reports() {
            $this->requireLogin();
            $user_id = $_SESSION['user_id'];

            $user = new User();
            $plan = new Plan();
            $payment = new Payment();
            $subscription = new Subscription();
            $trainerModel = new Trainer();

            $members = $user->displayAllUsers();
            $memberCount = $user->countActiveMembers();
            $totalEarned = $payment->totalEarned();
            $trainers = $trainerModel->getAllTrainers();
            $paymentDetails = $subscription->getUserPayments();
            $totalPayments = $subscription->countTotalPayments();
            $walk_ins = $user->displayAllWalkInMembers();
            $plans = $plan->getAllPlans();
            $activePlans = $plan->getAllActivePlans();

            $last12MonthsRevenue = $payment->getLast12MonthsRevenue();
            $dailyRevenue30Days = $payment->getDailyRevenueLast30Days();
            $revenueByPlan = $payment->getRevenueByPlan();
            $paymentStats = $payment->getPaymentStats();
            $pendingPayments = $payment->getPendingPayments();
            $paymentMethodStats = $payment->getPaymentMethodStats();

            $memberGrowth = $user->getMemberGrowthLast12Months();
            $activeInactiveCount = $user->getActiveInactiveCount();
            $membersByPlan = $user->getMembersByPlan();
            $retentionRate = $user->getRetentionRate();

            $expiringSubscriptions = $subscription->getExpiringSubscriptions(7);
            $subscriptionStatusBreakdown = $subscription->getSubscriptionStatusBreakdown();

            $this->adminView('reports', [
                'memberCount' => $memberCount,
                'totalEarned' => $totalEarned,
                'paymentDetails' => $paymentDetails,
                'totalPayments' =>  $totalPayments,
                'trainers' => $trainers,
                'walk_ins' => $walk_ins,
                'members' => $members,
                'plans' => $plans,
                'activePlans' => $activePlans,

               'last12MonthsRevenue' => $last12MonthsRevenue,
                'dailyRevenue30Days' => $dailyRevenue30Days,
                'revenueByPlan' => $revenueByPlan,
                'paymentStats' => $paymentStats,
                'pendingPayments' => $pendingPayments,
                'paymentMethodStats' => $paymentMethodStats,
                'memberGrowth' => $memberGrowth,
                'activeInactiveCount' => $activeInactiveCount,
                'membersByPlan' => $membersByPlan,
                'retentionRate' => $retentionRate,
                'expiringSubscriptions' => $expiringSubscriptions,
                'subscriptionStatusBreakdown' => $subscriptionStatusBreakdown,
            ]);
        }
        public function getReportData() {
            header('Content-Type: application/json');
            
            if($_SERVER['REQUEST_METHOD'] == 'GET') {
                $period = $_GET['period'] ?? 30;
                
                $payment = new Payment();
                $user = new User();
                $subscription = new Subscription();
                
                // Calculate date range
                $end_date = date('Y-m-d');
                $start_date = date('Y-m-d', strtotime("-{$period} days"));
                
                // Get data for the period
                $data = [
                    'revenue_trend' => $payment->getRevenueByDateRange($start_date, $end_date),
                    'total_revenue' => $payment->totalEarned()['total_earned'],
                    'pending_revenue' => $payment->getPendingPayments()['pending_amount'],
                    'active_members' => $user->countActiveMembers()['active_member_count'],
                    'retention_rate' => $user->getRetentionRate()['rate'],
                    'member_growth' => $user->getMemberGrowthLast12Months(),
                    'revenue_by_plan' => $payment->getRevenueByPlan(),
                ];
                
                echo json_encode([
                    'success' => true,
                    'data' => $data
                ]);
            } else {
                http_response_code(405);
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid request method'
                ]);
            }
        }
        public function getFilteredReportData() {
            header('Content-Type: application/json');
            
            if($_SERVER['REQUEST_METHOD'] == 'GET') {
                $payment = new Payment();
                $user = new User();
                $subscription = new Subscription();
                
                // Get filter parameters
                $dateRange = $_GET['date_range'] ?? '30';
                $startDate = $_GET['start_date'] ?? '';
                $endDate = $_GET['end_date'] ?? '';
                
                // Calculate dates
                if ($dateRange == 'custom' && !empty($startDate) && !empty($endDate)) {
                    $start = $startDate;
                    $end = $endDate;
                } else {
                    $end = date('Y-m-d');
                    $start = date('Y-m-d', strtotime("-{$dateRange} days"));
                }
                
                // Get filtered data
                $data = [
                    // Revenue data
                    'revenue_trend' => $payment->getRevenueTrend($start, $end),
                    'daily_revenue' => $payment->getDailyRevenue($start, $end),
                    'revenue_by_plan' => $payment->getRevenueByPlan($start, $end),
                    'payment_method_stats' => $payment->getPaymentMethodStats($start, $end),
                    
                    // Member data
                    'member_growth' => $user->getMemberGrowth($start, $end),
                    'members_by_plan' => $user->getMembersByPlan(),
                    'active_inactive_count' => $user->getActiveInactiveCount(),
                    'retention_rate' => $user->getRetentionRate(),
                    
                    // Payment stats
                    'payment_stats' => $payment->getPaymentStatsFiltered($start, $end),
                    'pending_payments' => $payment->getPendingPayments(),
                    
                    // Subscription data
                    'expiring_subscriptions' => $subscription->getExpiringSubscriptions(7),
                    'subscription_status_breakdown' => $subscription->getSubscriptionStatusBreakdown(),
                    
                    // Date info
                    'filter_info' => [
                        'start_date' => $start,
                        'end_date' => $end,
                        'days' => (strtotime($end) - strtotime($start)) / 86400,
                        'formatted_start' => date('M d, Y', strtotime($start)),
                        'formatted_end' => date('M d, Y', strtotime($end))
                    ]
                ];
                
                echo json_encode([
                    'success' => true,
                    'data' => $data
                ]);
            } else {
                http_response_code(405);
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid request method'
                ]);
            }
        }

        public function registerMember() {
            $user = new User();

            header('Content-Type: application/json');

            if($_SERVER['REQUEST_METHOD'] == 'POST') {

                $register = ["first_name" => "", "last_name" => "", "middle_name" => "", "email" => "", "date_of_birth" => "", "gender"=>"" , "password" => "", "cPassword" => ""];
                $registerError = ["first_name" => "", "last_name" => "", "middle_name" => "", "email" => "", "date_of_birth" => "", "gender"=>"" , "password" => "", "cPassword" => "", "register"=>""];
                
                $register["first_name"] =  trim(htmlspecialchars($_POST['first_name']));
                $register["last_name"] = trim(htmlspecialchars($_POST['last_name']));
                $register["middle_name"] = isset($_POST['middle_name']) ? trim(htmlspecialchars($_POST['middle_name'])) : "";
                $register["email"] =  trim(htmlspecialchars($_POST['email']));
                $register["date_of_birth"] = trim(htmlspecialchars($_POST['date_of_birth']));
                $register["gender"] =  trim(htmlspecialchars($_POST['gender']));
                $register["password"] = trim(htmlspecialchars($_POST['password']));
                $register["cPassword"] = trim(htmlspecialchars($_POST['cPassword']));

                if(empty($register['first_name'])) {
                    $registerError['first_name'] = "Please provide your first name";
                }
                if(empty($register['last_name'])) {
                    $registerError['last_name'] = "Please provide your last name";
                }
                if(empty($register['email'])) {
                    $registerError['email'] = "Please  provide a valid email";
                } else if(!filter_var($register['email'], FILTER_VALIDATE_EMAIL)) {
                    $registerError['email'] = "Please provide a valid email address";
                }

                if(empty($register['date_of_birth'])) {
                    $registerError['date_of_birth'] = "Please provide you birthdate";
                } else if($register['date_of_birth'] < 12) {
                    $registerError['date_of_birth'] = "Children are not allowed in the gym";
                }
                if(!isset($register["gender"] )) {
                    $registerError['gender'] = "Please set your preferred gender";
                }
                if(empty($register['password'])) {
                    $registerError['password'] = "Password should not be empty.";
                } else if(strlen($register['password']) < 8) {
                    $registerError['password'] = "Password should not be less than 8 characters";
                } else if($register['password'] != $register['cPassword']) {
                    $registerError['password'] = "Passwords do not match";
                    $registerError['cPassword'] = "Passwords do not match";
                }

                if(empty($register['cPassword'])) {
                    $registerError['cPassword'] = "Please enter you password again.";
                }

                if(empty(array_filter($registerError))) {
                    if(!$user->findByEmail($register['email'])) {
                        $user_id = $user->addMember($register);
                        if($user_id) {
                            $subscriptionDetails = [
                                "subscription_id" => "",
                                "user_id" => "",
                                "plan_id" => "",
                                "start_date" => "",
                                "end_date" => "",
                            ];
                            $subscriptionDetails['user_id'] = $user_id['user_id'];
                            $subscriptionDetails['plan_id'] = trim(htmlspecialchars($_POST['plan_id']) ?? "");
                            $subscriptionDetails['start_date'] = date("Y-m-d");
                            $subscriptionDetails['end_date'] =  date('y-m-d', strtotime('+30 days'));

                            $subscribe = new Subscription();
                            $paymentModel = new Payment();
                            $planModel = new Plan();

                            if($subscribe->subscripePlan($subscriptionDetails)) {
                                $userCurrentPlan = $subscribe->checkUserCurrentPlan($subscriptionDetails['user_id']);
                                $userPlan = $planModel->getUserPlan($subscriptionDetails['user_id']);
                                
                                //fill up payment details
                                $paymentDetails['subscription_id'] = $userCurrentPlan['subscription_id'];
                                $paymentDetails['amount'] = $userPlan['price'];
                                $paymentDetails['payment_date'] = $userPlan['end_date'];
                                $paymentDetails['status'] = "pending";
                                if($paymentModel->openPayment($paymentDetails)) {
                                    echo json_encode([
                                        'success' => true,
                                        'message' => 'User added Successfully',
                                    ]);
                                    
                                } else {
                                    echo json_encode([
                                        'success' => false,
                                        'message' => 'Error setting up user payment',
                                    ]);
                                }

                            }   
                                                
                        } else {
                            echo json_encode([
                                'success' => false,
                                'message' => 'Error registering user, please try again',
                            ]);
                            
                        }
                    } else {
                        http_response_code(401);
                        echo json_encode([
                            'success' => false,
                            'message' => 'Error registering user: Account already exist.',
                        ]);
                    }             
                }
            } else {
                http_response_code(403);
                echo json_encode([
                    'success' => false,
                    'message' => 'invalid request method',
                ]);
            }
        }

        public function getTrainerData() {
            header('Content-Type: application/json');
            $trainerModel = new Trainer();
            $trainer_id = $_GET['trainer_id'];
            $trainerData = $trainerModel->getTrainerById($trainer_id);
            
            if($trainerData) {
                echo json_encode([
                    'success' => true,
                    'data' => $trainerData,
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' =>"an error occured.",
                ]);
            }  
        }

        public function addMemberAsTrainer() {
            header('Content-Type: application/json');
            
            if($_SERVER['REQUEST_METHOD'] !== 'POST') {
                http_response_code(405);
                echo json_encode(['success' => false, 'message' => 'Method not allowed']);
                exit;
            }
            
            $userId = trim($_POST['user_id'] ?? '');
            $specialization = trim($_POST['specialization'] ?? '');
            $experienceYears = trim($_POST['experience_years'] ?? '0');
            $contactNo = trim($_POST['contact_no'] ?? '');

            // Validation
            if(empty($userId) || empty($specialization)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Missing required fields']);
                exit;
            }

            $db = null;
            
            try {
                $userModel = new User();
                $trainerModel = new Trainer();
                
                // Check if user exists
                $user = $userModel->getMember($userId);
                if(!$user) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'User not found']);
                    exit;
                }

                // Check if already a trainer
                if(isset($user['role']) && $user['role'] === 'trainer') {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'User is already a trainer']);
                    exit;
                }

                // Get database connection
                $db = $trainerModel->connect();
                $db->beginTransaction();

                // Update user role to trainer
                $updateRoleQuery = "UPDATE members SET role = 'trainer' WHERE user_id = ?";
                $stmt = $db->prepare($updateRoleQuery);
                
                if(!$stmt->execute([$userId])) {
                    throw new Exception('Failed to update user role');
                }

                // Insert into trainers table
                $trainerQuery = "INSERT INTO trainers (user_id, specialization, experience_years, contact_no, status, join_date) 
                                VALUES (?, ?, ?, ?, 'active', NOW())";
                $stmt = $db->prepare($trainerQuery);
                
                if(!$stmt->execute([$userId, $specialization, $experienceYears, $contactNo])) {
                    throw new Exception('Failed to create trainer record');
                }

                $db->commit();
                
                // Get member name for notifications
                $memberName = ($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '');
                $email = $user['email'];

                $this->sendTrainerNotifications($email, $userId, $memberName, $specialization);

                // Success response
                http_response_code(200);
                echo json_encode([
                    'success' => true, 
                    'message' => 'Member promoted to trainer successfully'
                ]);

            } catch(Exception $e) {
                // Only rollback if transaction is still active
                if($db && $db->inTransaction()) {
                    $db->rollBack();
                }
                
                error_log("Error promoting member to trainer: " . $e->getMessage());
                
                http_response_code(500);
                echo json_encode([
                    'success' => false, 
                    'message' => 'Failed to promote member to trainer'
                ]);
            }
            exit;
        }
        private function sendTrainerNotifications($email, $userId, $memberName, $specialization) {
            try {
                // Check if NotificationHelper file exists
                $helperPath = __DIR__ . '/../helpers/NotificationHelper.php';
                if(!file_exists($helperPath)) {
                    error_log("NotificationHelper not found at: $helperPath");
                    return false;
                }
                
                require_once $helperPath;
                
                // Check if the class exists
                if(!class_exists('NotificationHelper')) {
                    error_log("NotificationHelper class not found");
                    return false;
                }
                
                // Try to send notification to the promoted trainer
                try {
                    NotificationHelper::trainerPromoted($userId, $specialization);
                } catch(Exception $e) {
                    error_log("Failed to send trainer promotion notification: " . $e->getMessage());
                }
                
                // Try to notify admins
                try {
                    NotificationHelper::notifyAllAdmins(
                        'New Trainer Added',
                        "$memberName has been promoted to trainer with specialization in $specialization.",
                        'index.php?controller=Admin&action=dashboard'
                    );
                } catch(Exception $e) {
                    error_log("Failed to send admin notification: " . $e->getMessage());
                }             
                $this->sendTrainerWelcomeEmail($email, $memberName, $specialization);
                
                
                return true;
                
            } catch(Exception $e) {
                error_log("Notification system error: " . $e->getMessage());
                return false;
            }
        }
       private function sendTrainerWelcomeEmail($email, $memberName, $specialization) {
            $mail = $this->mailer();
            $mail->addAddress($email, $memberName);
            $mail->Subject = "Hello, New Trainer!";
            $mail->isHTML(true);
            $mail->Body = "
                <html>
                <head>
                    <style>
                        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                        .header { background: linear-gradient(135deg, #1e40af, #3b82f6); color: white; padding: 30px; text-align: center; border-radius: 8px 8px 0 0; }
                        .content { background: #f9fafb; padding: 30px; border-radius: 0 0 8px 8px; }
                    </style>
                </head>
                <body>
                    <div class='container'>
                        <div class='header'>
                            <h1>🎉 Welcome to the Trainer Team!</h1>
                        </div>
                        <div class='content'>
                            <h2>Hi $memberName,</h2>
                            <p>Congratulations! You've been promoted to a trainer at Gymazing!</p>
                            
                            <p><strong>Your Specialization:</strong> $specialization</p>
                            
                            <p>As a trainer, you now have access to:</p>
                            <ul>
                                <li>Trainer Dashboard</li>
                                <li>Member Management</li>
                                <li>Session Scheduling</li>
                                <li>Progress Tracking Tools</li>
                            </ul>
                            
                            <p>Best regards,<br>The Gymazing Team</p>
                        </div>
                    </div>
                </body>
                </html>
            ";
            $mail->send();
        }

        public function getNonTrainerMembers() {
            header('Content-Type: application/json');
            
            $userModel = new User();
            $db = $userModel->connect();
            
            $query = "SELECT user_id, CONCAT(first_name, ' ', last_name) as name, email 
                    FROM members 
                    WHERE role = 'member' 
                    ORDER BY first_name, last_name";
            
            $stmt = $db->prepare($query);
            $stmt->execute();
            $members = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode(['success' => true, 'data' => $members]);
            exit;
        }

        // ... existing getNonTrainerMembers method ...
        
        public function getPendingRegistrations() {
            $this->requireLogin();
            header('Content-Type: application/json');
            $userModel = new User();
            $pending = $userModel->getPendingMembers();
            
            echo json_encode([
                'success' => true,
                'data' => $pending
            ]);
        }
        
        public function approveUserId() {
            $this->requireLogin();
            header('Content-Type: application/json');
            if($_SERVER['REQUEST_METHOD'] !== 'POST') {
                http_response_code(405);
                echo json_encode(['success' => false, 'message' => 'Method not allowed']);
                exit;
            }
            
            $user_id = $_POST['user_id'] ?? '';
            if(empty($user_id)) {
                 echo json_encode(['success' => false, 'message' => 'User ID is required']);
                 exit;
            }
            
            $userModel = new User();
            if($userModel->approveMemberStatus($user_id)) {
                
                // Fetch user data for notification
                $user = $userModel->getMemberDetailsById($user_id);
                if($user) {
                     $this->notifyUserApproval($user['email'], $user['first_name']);
                }
                
                echo json_encode(['success' => true, 'message' => 'User approved successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to approve user']);
            }
        }
        
        public function rejectUserId() {
            $this->requireLogin();
            header('Content-Type: application/json');
            if($_SERVER['REQUEST_METHOD'] !== 'POST') {
                http_response_code(405);
                echo json_encode(['success' => false, 'message' => 'Method not allowed']);
                exit;
            }
            
            $user_id = $_POST['user_id'] ?? '';
            if(empty($user_id)) {
                 echo json_encode(['success' => false, 'message' => 'User ID is required']);
                 exit;
            }
             
            $userModel = new User();
            if($userModel->rejectMemberStatus($user_id)) {
                
                // Fetch user data for notification
                $user = $userModel->getMemberDetailsById($user_id);
                if($user) {
                     $this->notifyUserRejection($user['email'], $user['first_name']);
                }

                echo json_encode(['success' => true, 'message' => 'User rejected successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to reject user']);
            }
        }

        private function notifyUserApproval($email, $name) {
            $mail = $this->mailer();
            $mail->addAddress($email, $name);
            $mail->Subject = "Account Approved! Welcome to Gymazing! 🎉";
            $mail->isHTML(true);
            $mail->Body = "
                <div style='font-family: Arial, sans-serif; padding: 20px; background-color: #f4f7fa;'>
                    <div style='max-width: 600px; margin: auto; background-color: #ffffff; padding: 25px; border-radius: 8px; border: 1px solid #e1e5ea;'>

                        <h2 style='color: #10B981;'>Approval Successful! ✅</h2>

                        <p>Hi {$name},</p>

                        <p>Great news! Your account verification is complete and has been <strong>APPROVED</strong>.</p>

                        <div style='margin: 15px 0; padding: 12px; background-color: #ecfdf5; border-left: 4px solid #10B981;'>
                            <p style='margin: 0; font-size: 15px;'>You can now log in to your dashboard, manage your profile, and subscribe to membership plans.</p>
                        </div>

                        <p>We are excited to be part of your fitness journey!</p>

                        <a href='http://localhost/GymMembershipApplicationSystem/index.php?controller=auth&action=login' 
                           style='display:inline-block; margin: 15px 0; background-color: #10B981; 
                           color: white; padding: 12px 20px; text-decoration: none; border-radius: 6px; font-weight: bold;'>
                           Log In Now
                        </a>

                        <hr style='margin-top: 30px;'>

                        <p style='font-size: 12px; color: #777;'>&copy; " . date('Y') . " Gymazing. All rights reserved.</p>
                    </div>
                </div>
            ";
            $mail->AltBody = "Hi {$name}, your account has been approved! You can now log in.";
            $mail->send();
        }
        
        private function notifyUserRejection($email, $name) {
            $mail = $this->mailer();
            $mail->addAddress($email, $name);
            $mail->Subject = "Update on Your Account Registration";
            $mail->isHTML(true);
            $mail->Body = "
                <div style='font-family: Arial, sans-serif; padding: 20px; background-color: #f4f7fa;'>
                    <div style='max-width: 600px; margin: auto; background-color: #ffffff; padding: 25px; border-radius: 8px; border: 1px solid #e1e5ea;'>

                        <h2 style='color: #EF4444;'>Application Update ⚠️</h2>

                        <p>Hi {$name},</p>

                        <p>Thank you for your interest in Gymazing.</p>

                        <div style='margin: 15px 0; padding: 12px; background-color: #fef2f2; border-left: 4px solid #EF4444;'>
                            <p style='margin: 0; font-size: 15px;'>We regret to inform you that your registration could not be approved at this time.</p>
                        </div>

                        <p>This decision may be due to incomplete information or issues with the uploaded ID verification document.</p>

                        <p>If you believe this is a mistake or would like to try again, please contact our support team or register with corrected details.</p>

                        <a href='mailto:support@gymazing.com' 
                           style='display:inline-block; margin: 15px 0; background-color: #64748B; 
                           color: white; padding: 12px 20px; text-decoration: none; border-radius: 6px;'>
                           Contact Support
                        </a>

                        <hr style='margin-top: 30px;'>

                        <p style='font-size: 12px; color: #777;'>&copy; " . date('Y') . " Gymazing. All rights reserved.</p>
                    </div>
                </div>
            ";
            $mail->AltBody = "Hi {$name}, your registration was rejected. Please contact support context for more details.";
            $mail->send();
        }

    }


?>