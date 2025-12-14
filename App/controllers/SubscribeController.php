<?php 
    require_once __DIR__ . "/../Controller.php";
    //models
    require_once __DIR__ . "/../models/Subscription.php";
    require_once __DIR__ . "/../models/User.php";
    require_once __DIR__ . "/../models/Plan.php";
    require_once __DIR__ . "/../models/Payment.php";
    require_once __DIR__ . "/../models/notification.php";
    //helper
    require_once __DIR__ . "/../helpers/notificationHelper.php";
    
    class SubscribeController extends Controller {
        
        public function Subscribe() {
            $this->requireLogin();
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            
            $subscribe = new Subscription();
            $planModel = new Plan();
            $paymentModel = new Payment();
            $userModel = new User();
            
            $user_id = $_SESSION['user_id'];
            
            // Initialize details arrays
            $subscriptionDetails = [
                "subscription_id" => "", "user_id" => "", "plan_id" => "",
                "start_date" => "", "end_date" => ""
            ];
            $subscriptionError = []; 
            
            $paymentDetails = [
                "subscription_id" => "", "amount" => "", 
                "payment_date" => "", "status" => ""
            ];

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $subscriptionDetails['user_id'] = $user_id;
                $subscriptionDetails['plan_id'] = trim(htmlspecialchars($_POST['plan_id']) ?? "");
                $subscriptionDetails['start_date'] = date("Y-m-d");
                $subscriptionDetails['end_date'] = date('Y-m-d', strtotime('+30 days'));

                // 1. Basic Validation
                if (empty($subscriptionDetails['user_id'])) $subscriptionError[] = "User not logged in.";
                if (empty($subscriptionDetails['plan_id'])) $subscriptionError[] = "Please Select a plan.";

                // If basic validation fails, stop here
                if (!empty($subscriptionError)) {
                    $this->view('subscription_failed', ['error_message' => implode(', ', $subscriptionError)]);
                    return;
                }

                // ============================================================
                // 2. NEW: Check for Pending Payments
                // ============================================================
                $pendingResult = $paymentModel->getUserPendingPayments($user_id);
                
                // Since your function uses fetch(), it returns an array (e.g., [0 => '3'])
                // We need to extract the actual number from the first column
                $pendingCount = 0;
                if (is_array($pendingResult)) {
                    $pendingCount = $pendingResult[0] ?? 0;
                }

                if ($pendingCount > 0) {
                    // Stop execution and show failure page
                    $this->feedback('subscription_failed', [
                        'error_message' => "You have pending payments. Please settle your outstanding balance before subscribing to a new plan."
                    ]);
                    return;
                }
                // ============================================================

                // 3. Proceed with Subscription Logic
                $userCurrentPlan = $subscribe->checkUserCurrentPlan($subscriptionDetails['user_id']);
                $currentUser = $userModel->getMember($subscriptionDetails['user_id']); // Fetch user details

                if ($userCurrentPlan) {
                    // --- SCENARIO A: Upgrade/Change Plan (Overwrite old plan) ---
                    if ($subscribe->subscripePlan($subscriptionDetails)) {
                        
                        if ($subscribe->cancelPlan($userCurrentPlan['subscription_id'])) { 
                            $newUserPlan = $subscribe->checkUserCurrentPlan($subscriptionDetails['user_id']); 
                            $planInfo = $planModel->getPlanById($subscriptionDetails['plan_id']); 

                            // Setup Payment
                            $paymentDetails['subscription_id'] = $newUserPlan['subscription_id'];
                            $paymentDetails['amount'] = $planInfo['price']; 
                            $paymentDetails['payment_date'] = date('Y-m-d'); 
                            $paymentDetails['status'] = "pending";

                            if ($paymentModel->openPayment($paymentDetails)) {
                                NotificationHelper::membershipRenewed($user_id, $subscriptionDetails['end_date']);
                                // Notify Upgrade
                                $this->notifyUpgradedPlan($currentUser['email'], $currentUser['first_name'], $planInfo['plan_name']);
                                $this->feedback('subscription_success');
                            } else {
                                $this->feedback('subscription_failed', ['error_message' => "Error setting up payment."]);
                            }
                        } else {
                            $this->feedback('subscription_failed', ['error_message' => "Could not cancel previous plan."]);
                        }
                    } else {
                        $this->feedback('subscription_failed', ['error_message' => "Database error while subscribing."]);
                    }

                } else {
                    // --- SCENARIO B: New Subscription ---
                    if ($subscribe->subscripePlan($subscriptionDetails)) {
                        $newUserPlan = $subscribe->checkUserCurrentPlan($subscriptionDetails['user_id']);
                        $planInfo = $planModel->getPlanById($subscriptionDetails['plan_id']);

                        $paymentDetails['subscription_id'] = $newUserPlan['subscription_id'];
                        $paymentDetails['amount'] = $planInfo['price'];
                        $paymentDetails['payment_date'] = date('Y-m-d');
                        $paymentDetails['status'] = "pending";

                        if ($paymentModel->openPayment($paymentDetails)) {
                            // Notify New Subscription
                            $this->notifySubscription($currentUser['email'], $currentUser['first_name']); 
                            $this->feedback('subscription_success');
                        } else {
                            $this->feedback('subscription_failed', ['error_message' => "Error setting up payment."]);
                        }
                    } else {
                        $this->feedback('subscription_failed', ['error_message' => "Database error while subscribing."]);
                    }
                }
            }
        }

        public function CancelSubscription() {
            $this->requireLogin();
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }

            $subscriptionModel = new Subscription();
            $notificationModel = new Notification();

            $user_id = $_SESSION['user_id'];
            
            // Check if user actually has a plan
            $userPlan = $subscriptionModel->checkUserCurrentPlan($user_id);
            
            if (!$userPlan) {
                $this->feedback('cancel_failed', ['error_message' => "Cancel failed, No active plan."]);
                header("location: index.php?controller=Dashboard&action=member");
                exit();
            }

            // --- LOGIC SPLIT: GET vs POST ---

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // 1. Process the Cancellation
                if (isset($_POST['confirm_cancel']) && $_POST['confirm_cancel'] === 'true') {
                    
                    $subscription_id = $userPlan['subscription_id'];

                    if ($subscriptionModel->cancelPlan($subscription_id)) {
                        
                        // Create Notification
                        $notificationModel->create(
                            $user_id, 
                            "Plan Cancellation", 
                            "Your Current Plan has been Cancelled.", 
                            "warning", 
                            "membership"
                        );

                        // Show Success View
                        $this->feedback('cancel_success');

                    } else {
                        // Show Failure View
                        $this->feedback('cancel_failed', ['error_message' => "Database error occurred while cancelling."]);
                    }
                } else {
                    // Invalid POST request (missing confirmation token)
                    header("location: index.php?controller=Dashboard&action=member");
                }

            } else {
                // 2. Show the "Are you sure?" Page (GET Request)
                
                // Pass plan details to the feedback so user knows what they are cancelling
                $this->feedback('cancel_confirmation', [
                    'plan_name' => $userPlan['plan_name'] ?? 'Membership', // Adjust key based on your DB
                    'subscription_id' => $userPlan['subscription_id']
                ]);
            }
        }

        public function expirePlan() {
            $this->requireLogin();
            $user_id = $_SESSION['user_id'];

            $userModel = new User();
            $planModel = new Plan();
            $notificationModel = new Notification();

            $user = $userModel->getMember($user_id);
            $userPlan = $planModel->getUserPlan($user_id);
            $subscriptionModel = new Subscription();
            $userCurrentPlan = $subscriptionModel->checkUserCurrentPlan($_SESSION['user_id']);
            $subscription_id = $userCurrentPlan['subscription_id'];
            if($subscriptionModel->expirePlan($subscription_id)) {
                $userModel->deleteMemberViaId($user_id);
                $userPlan['status'] = 'expired';
                //email user of expired subscription
                $this->notifyExpired($user['email'], $user['name']);
                NotificationHelper::membershipExpired($user_id);
                $notificationModel->create($user_id, "Plan Expiration", "Your Current Plan has Expired", "warning", "membership");

                $this->view('dashboard', [
                    'userInfo' => $user,
                    'userPlan' => $userPlan,
                ]);
            } else {
                $this->view('dashboard', [
                    'userInfo' => $user,
                    'userPlan' => $userPlan,
                ]);
            }
        }

        
        public function notifyExpired($email, $name) {
            $mail = $this->mailer(); 
            $mail->addAddress($email, $name);
            $mail->Subject = "Subscription Expired";
            $mail->isHTML(true);
            $mail->Body = "
                <h3>Hello, $name</h3>
                <p>Your subscription has expired.</p>
                <p><a href='https://gymazing.com/renew'>Renew now</a> to continue enjoying our services!</p>
                <br>
                <p>Thank you!</p>
            ";
            $mail->AltBody = "Hi $name, your subscription has expired. Please renew your plan.";
            $mail->send();
        }

        public function notifySubscription($email, $name) {
            $mail = $this->mailer();
            $mail->addAddress($email, $name);
            $mail->Subject = "Subscription Activated Successfully!";
            $mail->isHTML(true);
            $mail->Body = "
                <div style='font-family: Arial, sans-serif; padding: 20px; background-color: #f4f6f9;'>
                    <div style='max-width: 600px; margin: auto; background-color: #fff; padding: 20px; border-radius: 8px;'>
                        <h2 style='color: #4CAF50;'>Welcome to Our Service! 🌟</h2>

                        <p>Hi <strong>{$name}</strong>,</p>
                        <p>We're excited to let you know that your subscription has been <strong>successfully activated</strong>!</p>

                        <div style='margin: 20px 0; padding: 15px; background-color: #e8f5e9; border-left: 4px solid #4CAF50;'>
                            <p style='margin: 0; font-size: 15px;'>You now have full access to all premium features.</p>
                        </div>

                        <p>Start exploring now and enjoy the experience! </p>

                        <a href='https://your-website.com/dashboard' style='display:inline-block; margin-top: 15px; background-color: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Go to Dashboard</a>

                        <hr style='margin-top: 30px;'>

                        <p style='font-size: 12px; color: #777;'>If you have any questions, feel free to reach out to our support team.</p>
                        <p style='font-size: 12px; color: #777;'>&copy; " . date('Y') . " Gymazing!. All rights reserved.</p>
                    </div>
                </div>
            ";
            $mail->AltBody = "Hi {$name}, your subscription was successfully activated! Enjoy full access now.";
            $mail->send();
        }

        public function notifyUpgradedPlan($email, $name, $newPlanName) {
            $mail = $this->mailer();
            $mail->addAddress($email, $name);
            $mail->Subject = "Membership Plan Upgraded! 🚀";
            $mail->isHTML(true);
            $mail->Body = "
                <div style='font-family: Arial, sans-serif; padding: 20px; background-color: #fce7f3;'>
                    <div style='max-width: 600px; margin: auto; background-color: #ffffff; padding: 20px; border-radius: 8px;'>
                        <h2 style='color: #db2777;'>Plan Upgrade Successful! 💎</h2>

                        <p>Hi <strong>{$name}</strong>,</p>
                        <p>You have successfully upgraded your membership!</p>

                        <div style='margin: 20px 0; padding: 15px; background-color: #fdf2f8; border-left: 4px solid #db2777;'>
                            <p style='margin: 0; font-size: 16px;'>You are now on the <strong>{$newPlanName}</strong> plan.</p>
                        </div>

                        <p>Enjoy the new perks and features unlocked with this upgrade.</p>

                        <a href='https://gymazing.com/dashboard' style='display:inline-block; margin-top: 15px; background-color: #db2777; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>View My Plan</a>
                        
                        <p style='margin-top:20px; color:#666;'>Keep pushing your limits!</p>
                        <p style='font-size: 12px; color: #777;'>&copy; " . date('Y') . " Gymazing.</p>
                    </div>
                </div>
            ";
            $mail->AltBody = "Hi {$name}, your plan has been upgraded to {$newPlanName}.";
            $mail->send();
        }
        
        /**
         * Member requests a freeze
         */
        public function RequestFreeze() {
            session_start(); // Ensure session is started
            header('Content-Type: application/json');
            
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                echo json_encode(['success' => false, 'message' => 'Invalid request method']);
                return;
            }
            
            $userId = $_SESSION['user_id'] ?? null;
            if (!$userId) {
                echo json_encode(['success' => false, 'message' => 'Not logged in. Please refresh and try again.']);
                return;
            }
            
            $freezeStart = $_POST['freeze_start'] ?? '';
            $freezeEnd = $_POST['freeze_end'] ?? '';
            $reason = $_POST['reason'] ?? '';
            
            // Validate inputs
            if (empty($freezeStart) || empty($freezeEnd)) {
                echo json_encode(['success' => false, 'message' => 'Please provide freeze dates']);
                return;
            }
            
            $subscriptionModel = new Subscription();
            
            // Check eligibility first
            $eligibility = $subscriptionModel->canRequestFreeze($userId);
            if (!$eligibility['can_request']) {
                echo json_encode(['success' => false, 'message' => $eligibility['message']]);
                return;
            }
            
            // Get active subscription
            $activeSubscription = $subscriptionModel->checkUserCurrentPlan($userId);
            
            // Debug: Check if subscription exists
            if (!$activeSubscription) {
                echo json_encode([
                    'success' => false, 
                    'message' => 'No active subscription found. Please ensure you have an active membership plan.',
                    'debug' => 'User ID: ' . $userId
                ]);
                return;
            }
            
            // Request freeze
            $result = $subscriptionModel->requestFreeze(
                $activeSubscription['subscription_id'],
                $userId,
                $freezeStart,
                $freezeEnd,
                $reason
            );
            
            echo json_encode($result);
        }
        
        /**
         * Admin approves freeze request
         */
        public function ApproveFreezeRequest() {
            session_start();
            header('Content-Type: application/json');
            
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                echo json_encode(['success' => false, 'message' => 'Invalid request method']);
                return;
            }
            
            $adminId = $_SESSION['user_id'] ?? null;
            // if (!$adminId) {
            //     echo json_encode(['success' => false, 'message' => 'Not logged in']);
            //     return;
            // }
            
            $freezeId = $_POST['freeze_id'] ?? null;
            
            if (!$freezeId) {
                echo json_encode(['success' => false, 'message' => 'Invalid freeze ID']);
                return;
            }
            
            $subscriptionModel = new Subscription();
            
            // Get freeze request details before approving
            $sql = "SELECT mfh.*, CONCAT(m.first_name, ' ', m.last_name) as member_name, m.email
                    FROM membership_freeze_history mfh
                    JOIN members m ON mfh.user_id = m.user_id
                    WHERE mfh.freeze_id = :freeze_id";
            $query = $subscriptionModel->connect()->prepare($sql);
            $query->bindParam(':freeze_id', $freezeId);
            $query->execute();
            $freezeDetails = $query->fetch(PDO::FETCH_ASSOC);
            
            if ($subscriptionModel->approveFreeze($freezeId, $adminId)) {
                // Send approval email
                if ($freezeDetails) {
                    $this->notifyFreezeApproved(
                        $freezeDetails['email'],
                        $freezeDetails['member_name'],
                        $freezeDetails['freeze_start'],
                        $freezeDetails['freeze_end']
                    );
                }
                echo json_encode(['success' => true, 'message' => 'Freeze request approved']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to approve freeze request']);
            }
        }
        
        /**
         * Admin rejects freeze request
         */
        public function RejectFreezeRequest() {
            session_start();
            header('Content-Type: application/json');
            
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                echo json_encode(['success' => false, 'message' => 'Invalid request method']);
                return;
            }
            
            $adminId = $_SESSION['user_id'] ?? null;
            // if (!$adminId) {
            //     echo json_encode(['success' => false, 'message' => 'Not logged in']);
            //     return;
            // }
            
            $freezeId = $_POST['freeze_id'] ?? null;
            $notes = $_POST['admin_notes'] ?? '';
            
            if (!$freezeId) {
                echo json_encode(['success' => false, 'message' => 'Invalid freeze ID']);
                return;
            }
            
            $subscriptionModel = new Subscription();
            
            // Get freeze request details before rejecting
            $sql = "SELECT mfh.*, CONCAT(m.first_name, ' ', m.last_name) as member_name, m.email
                    FROM membership_freeze_history mfh
                    JOIN members m ON mfh.user_id = m.user_id
                    WHERE mfh.freeze_id = :freeze_id";
            $query = $subscriptionModel->connect()->prepare($sql);
            $query->bindParam(':freeze_id', $freezeId);
            $query->execute();
            $freezeDetails = $query->fetch(PDO::FETCH_ASSOC);
            
            if ($subscriptionModel->rejectFreeze($freezeId, $adminId, $notes)) {
                // Send rejection email
                if ($freezeDetails) {
                    $this->notifyFreezeRejected(
                        $freezeDetails['email'],
                        $freezeDetails['member_name'],
                        $notes
                    );
                }
                echo json_encode(['success' => true, 'message' => 'Freeze request rejected']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to reject freeze request']);
            }
        }
        
        /**
         * Notify user of freeze approval
         */
        private function notifyFreezeApproved($email, $name, $freezeStart, $freezeEnd) {
            $mail = $this->mailer();
            $mail->addAddress($email, $name);
            $mail->Subject = 'Membership Freeze Request Approved - Gymazing';
            $mail->isHTML(true);
            
            $startDate = date('F d, Y', strtotime($freezeStart));
            $endDate = date('F d, Y', strtotime($freezeEnd));
            
            $mail->Body = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; background-color: #f4f4f4; padding: 20px;'>
                <div style='background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 30px; border-radius: 10px 10px 0 0; text-align: center;'>
                    <h1 style='color: white; margin: 0; font-size: 28px;'>✅ Freeze Request Approved</h1>
                </div>
                <div style='background-color: white; padding: 30px; border-radius: 0 0 10px 10px;'>
                    <p style='font-size: 16px; color: #333; margin-bottom: 20px;'>Hi <strong>{$name}</strong>,</p>
                    <p style='font-size: 16px; color: #333; line-height: 1.6;'>
                        Great news! Your membership freeze request has been <strong style='color: #10b981;'>approved</strong>.
                    </p>
                    <div style='background-color: #f0fdf4; border-left: 4px solid #10b981; padding: 15px; margin: 20px 0;'>
                        <p style='margin: 5px 0; color: #166534;'><strong>Freeze Period:</strong></p>
                        <p style='margin: 5px 0; color: #166534;'>📅 <strong>Start:</strong> {$startDate}</p>
                        <p style='margin: 5px 0; color: #166534;'>📅 <strong>End:</strong> {$endDate}</p>
                    </div>
                    <p style='font-size: 14px; color: #666; line-height: 1.6;'>
                        During this period, your membership will be paused and you will not be charged. Your membership will automatically resume after the freeze period ends.
                    </p>
                    <hr style='border: none; border-top: 1px solid #e5e7eb; margin: 20px 0;'>
                    <p style='font-size: 14px; color: #666;'>If you have any questions, please contact us.</p>
                    <p style='font-size: 14px; color: #666; margin-top: 20px;'>Best regards,<br><strong>Gymazing Team</strong></p>
                </div>
            </div>";
            
            $mail->AltBody = "Hi {$name}, your membership freeze request has been approved from {$startDate} to {$endDate}.";
            $mail->send();
        }
        
        /**
         * Notify user of freeze rejection
         */
        private function notifyFreezeRejected($email, $name, $reason = '') {
            $mail = $this->mailer();
            $mail->addAddress($email, $name);
            $mail->Subject = 'Membership Freeze Request Update - Gymazing';
            $mail->isHTML(true);
            
            $reasonText = $reason ? "<div style='background-color: #fef2f2; border-left: 4px solid #ef4444; padding: 15px; margin: 20px 0;'>
                        <p style='margin: 0; color: #991b1b;'><strong>Reason:</strong> {$reason}</p>
                    </div>" : '';
            
            $mail->Body = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; background-color: #f4f4f4; padding: 20px;'>
                <div style='background: linear-gradient(135deg, #f59e0b 0%, #ef4444 100%); padding: 30px; border-radius: 10px 10px 0 0; text-align: center;'>
                    <h1 style='color: white; margin: 0; font-size: 28px;'>Freeze Request Update</h1>
                </div>
                <div style='background-color: white; padding: 30px; border-radius: 0 0 10px 10px;'>
                    <p style='font-size: 16px; color: #333; margin-bottom: 20px;'>Hi <strong>{$name}</strong>,</p>
                    <p style='font-size: 16px; color: #333; line-height: 1.6;'>
                        We've reviewed your membership freeze request, and unfortunately, we're unable to approve it at this time.
                    </p>
                    {$reasonText}
                    <p style='font-size: 14px; color: #666; line-height: 1.6;'>
                        If you have any questions or would like to discuss alternative options, please don't hesitate to contact us. We're here to help!
                    </p>
                    <hr style='border: none; border-top: 1px solid #e5e7eb; margin: 20px 0;'>
                    <p style='font-size: 14px; color: #666;'>Thank you for your understanding.</p>
                    <p style='font-size: 14px; color: #666; margin-top: 20px;'>Best regards,<br><strong>Gymazing Team</strong></p>
                </div>
            </div>";
            
            $mail->AltBody = "Hi {$name}, unfortunately your membership freeze request could not be approved. " . ($reason ? "Reason: {$reason}" : '');
            $mail->send();
        }
    }

?>