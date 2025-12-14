<?php 
    require_once __DIR__ . "/../Controller.php";
    require_once __DIR__ . "/../models/User.php";
    require_once __DIR__ . "/../models/Payment.php";
    require_once __DIR__ . "/../models/subscription.php";
    require_once __DIR__ . "/../models/Plan.php";

    require_once __DIR__ . "/../helpers/notificationHelper.php";


    class PaymentController extends Controller {

        private $userPaymentDetails;

        public function planPayment() {
            $this->requireLogin();
            $paymentModel = new Payment();

            $user_id = $_SESSION['user_id'];
            $this ->userPaymentDetails = $paymentModel->getPaymentDetails($user_id);
            $this->view('payments', [
                "paymentDetails" => $this->userPaymentDetails,
            ]);
        }

    public function processPayment() {
        $this->requireLogin();
        $user_id = $_SESSION['user_id'];
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
            exit;
        }

        // Initialize model
        $paymentModel = new Payment();
        $payment_id = $paymentModel->getPaymentId($_POST['subscription_id']);
        // Validate and gather inputs
        $paymentDetails = [
            "subscription_id" => $_POST['subscription_id'] ?? null,
            "payment_id" => $payment_id['payment_id'] ?? $_POST['payment_id'],
            "payment_method" => $_POST['payment_method'] ?? null,
            "payment_status" => "completed",
            "transaction_type" => "new",
            "remarks" => $_POST['remarks'] ?? ""
        ];

        // Ensure required data
        if (empty($paymentDetails['subscription_id']) || empty($paymentDetails['payment_method'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Missing required payment fields']);
            exit;
        }

        // Process payment
        $result = $paymentModel->completePayment($paymentDetails);

        // Respond accordingly
        if ($result) {
            $paymentModel = new Payment();
            $userModel = new User();
            $planModel = new Plan();
            $subModel = new Subscription();

            // Fetch Data
            $payment_details = $paymentModel->getPaymentId($paymentDetails['subscription_id']);
            $userDetails = $userModel->getMember($user_id); // Now we have userDetails
            $subData = $subModel->getSubscriptionById($paymentDetails['subscription_id']);
            $planData = $planModel->getPlanById($subData['plan_id']);

            // Notify Email
            $this->notifyPaymentSuccess(
                $userDetails['email'], 
                $userDetails['name'], 
                $payment_details['amount'], 
                $planData['plan_name'] ?? 'Membership', 
                $paymentDetails['payment_id']
            );

            // DB Notifications
            NotificationHelper::paymentReceived($user_id, $payment_details['amount']);
            NotificationHelper::paymentReceived_Admin(7, $userDetails['name'], $payment_details['amount']);
            http_response_code(200);
            echo json_encode([
                'success' => true,
                'message' => 'Transaction completed successfully'
            ]);
        } else {
             http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Transaction failed'
            ]);
        }
    }

    public function notifyPaymentSuccess($email, $name, $amount, $plan_name, $transactionId) {
        $mail = $this->mailer();
        $mail->addAddress($email, $name);
        $mail->Subject = "Payment Successful — Thank You! 💳";
        $mail->isHTML(true);
        $mail->Body = "
            <div style='font-family: Arial, sans-serif; padding: 20px; background-color: #f7f9fc;'>
                <div style='max-width: 600px; margin: auto; background-color: #ffffff; padding: 20px; border-radius: 8px; border: 1px solid #e1e8ee;'>
                    
                    <h2 style='color: #2C7DFA; margin-bottom: 10px;'>Payment Confirmation</h2>

                    <p>Hi <strong>{$name}</strong>,</p>
                    <p>We're happy to let you know that your payment was processed successfully! 🎉</p>

                    <div style='margin: 20px 0; padding: 15px; background-color: #eaf3ff; border-left: 4px solid #2C7DFA;'>
                        <p style='margin: 0; font-size: 15px;'>
                            ⭐ Your subscription is now fully activated and ready to use.
                        </p>
                    </div>

                    <p>Details of your payment:</p>
                    <ul style='color: #333;'>
                        <li><strong>Amount:</strong> {$amount}</li>
                        <li><strong>Subscription Plan:</strong> {$plan_name}</li>
                        <li><strong>Transaction ID:</strong> {$transactionId}</li>
                        <li><strong>Date:</strong> " . date('F j, Y') . "</li>
                    </ul>

                    <p>To explore features and manage your subscription, click below:</p>

                    <a href='https://your-website.com/dashboard' 
                    style='display:inline-block; margin-top: 15px; background-color: #2C7DFA; 
                    color: white; padding: 10px 20px; text-decoration: none; border-radius: 6px;'>
                    Go to Dashboard
                    </a>

                    <hr style='margin-top: 30px;'>
                    <p style='font-size: 12px; color: #777;'>If you didn't make this transaction, please contact our support immediately.</p>
                    <p style='font-size: 12px; color: #777;'>&copy; " . date('Y') . " Your Company. All rights reserved.</p>
                </div>
            </div>
        ";
        $mail->AltBody = "Hi {$name}, your payment was successful! Thanks for your purchase.";

    }

    public function notifyRefundIssued($email, $name, $amount) {
        $mail = $this->mailer();
        $mail->addAddress($email, $name);
        $mail->Subject = "Refund Issued: {$amount} 💸";
        $mail->isHTML(true);
        $mail->Body = "
            <div style='font-family: Arial, sans-serif; padding: 20px; background-color: #f7f9fc;'>
                <div style='max-width: 600px; margin: auto; background-color: #ffffff; padding: 20px; border-radius: 8px;'>
                    <h2 style='color: #64748B;'>Refund Processed</h2>
                    <p>Hi {$name},</p>
                    <p>We have processed a refund of <strong>{$amount}</strong> for your transaction.</p>
                    <p>Please allow 5-10 business days for the amount to reflect in your account.</p>
                    <p>If you have questions, reply to this email.</p>
                    <p>Best,<br>Gymazing Team</p>
                </div>
            </div>
        ";
        $mail->send();
    }

    public function notifyPaymentReminder($email, $name, $amount, $dueDate) {
        $mail = $this->mailer();
        $mail->addAddress($email, $name);
        $mail->Subject = "Payment Reminder: {$amount} Pending ⏳";
        $mail->isHTML(true);
        $mail->Body = "
            <div style='font-family: Arial, sans-serif; padding: 20px; background-color: #fff7ed;'>
                <div style='max-width: 600px; margin: auto; background-color: #ffffff; padding: 20px; border-radius: 8px; border: 1px solid #ffedd5;'>
                    <h2 style='color: #ea580c;'>Action Required: Payment Pending</h2>
                    <p>Hi {$name},</p>
                    <p>This is a friendly reminder that a payment of <strong>{$amount}</strong> is pending for your subscription.</p>
                    <p><strong>Due Date:</strong> {$dueDate}</p>
                    <div style='margin: 20px 0;'>
                        <a href='https://gymazing.com/dashboard/payments' style='background-color: #ea580c; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Pay Now</a>
                    </div>
                    <p>To avoid service interruption, please settle this at your earliest convenience.</p>
                    <p>Best,<br>Gymazing Team</p>
                </div>
            </div>
        ";
        $mail->send();
    }


    public function getPaymentData() {
        // $this->requireLogin();
        header('Content-Type: application/json');

        if(isset($_GET['payment_id'])) {
            $paymentModel = new Payment();
            $data = $paymentModel->getPaymentById($_GET['payment_id']);
            
            if($data) {
                echo json_encode(['success' => true, 'data' => $data]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Payment not found']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Missing payment ID']);
        }
    }

    public function refundPayment() {
        // $this->requireLogin();
        header('Content-Type: application/json');

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $payment_id = $_POST['payment_id'];
            $paymentModel = new Payment();
            
            if($paymentModel->updatePaymentStatus($payment_id, 'refunded')) {
                // Fetch user and payment details for notification
                $paymentData = $paymentModel->getPaymentById($payment_id);
                // We need user_id from subscription...
                // Assuming payment link to subscription link to user? 
                // Let's use getPaymentDetails which usually joins?
                // Or simplified: Just success response for now, OR fetch logic.
                // Let's assume we can get email.
                // Helper:
                $userModel = new User();
                // We need user_id. Payment table usually has subscription_id. Subscription has user_id.
                // For simplicity, I'll rely on internal logic if I can, OR just accept I need to fetch.
                // Let's SKIP complex fetching for this snippet to avoid breaking if models differ.
                // But user asked to COMPLETE it.
                // I will add the method definition below and leave the complex fetch logic for now or try best effort.
                
                // Fetching for Refund Notification:
                 $subModel = new Subscription();
                 $subData = $subModel->getSubscriptionById($paymentData['subscription_id']);
                 $userModel = new User();
                 $userData = $userModel->getMemberDetailsById($subData['user_id']);
                 
                 $this->notifyRefundIssued($userData['email'], $userData['first_name'], $paymentData['amount']);

                echo json_encode(['success' => true, 'message' => 'Payment refunded']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Refund failed']);
            }
        }
    }

    public function sendReminder() {
        // $this->requireLogin();
        header('Content-Type: application/json');
        
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $payment_id = $_POST['payment_id'];
            $payment_id = $_POST['payment_id'];
            $paymentModel = new Payment();
            $paymentData = $paymentModel->getPaymentById($payment_id);
            
            if($paymentData) {
                 $subModel = new Subscription();
                 $subData = $subModel->getSubscriptionById($paymentData['subscription_id']);
                 $userModel = new User();
                 $userData = $userModel->getMemberDetailsById($subData['user_id']);
                 
                 $this->notifyPaymentReminder($userData['email'], $userData['first_name'], $paymentData['amount'], $paymentData['payment_date']);
                 echo json_encode(['success' => true, 'message' => 'Reminder sent']);
            } else {
                 echo json_encode(['success' => false, 'message' => 'Payment not found']);
            }
        }
    }

}