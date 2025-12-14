
<?php
require_once __DIR__ . '/../models/Trainer.php';
require_once __DIR__ . '/../models/Session.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . "/../Controller.php";

require_once __DIR__ . "/../helpers/notificationHelper.php";
class TrainerController extends Controller {
    private $trainerModel;
    private $sessionModel;

    public function __construct() {
        $this->trainerModel = new Trainer();
        $this->sessionModel = new Session();
    }

    public function trainerDashboard() {
        $this->requireLogin();
        $trainerId = $_SESSION['trainer_id'] ?? 1;

        // Fetch trainer data
        $trainerData = $this->getTrainerById($trainerId);
        $assignedMembers = $this->getAssignedMembers($trainerId);
        $upcomingSessions = $this->getUpcomingSessions($trainerId);
        $stats = $this->getTrainerStats($trainerId);

        $this->view('trainerDashboard', [
            'trainerData' => $trainerData,
            'assignedMembers' => $assignedMembers,
            'upcomingSessions' => $upcomingSessions,
            'stats' => $stats,
 
        ]);
    }
    public function getPendingRequests() {
        $this->requireLogin();
        header('Content-Type: application/json');

        $trainerId = $_SESSION['trainer_id'] ?? null;
        if(!$trainerId) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }

        $requests = $this->trainerModel->getPendingRequests($trainerId);
        echo json_encode(['success' => true, 'data' => $requests]);
        return;
    }
     public function getTrainerById($trainerId) {
        return $this->trainerModel->findById($trainerId);
    }

    public function getAssignedMembers($trainerId) {
        return $this->trainerModel->getAssignedMembers($trainerId);
    }

    public function getUpcomingSessions($trainerId) {
        return $this->sessionModel->getUpcomingByTrainer($trainerId);
    }

    public function getTrainerStats($trainerId) {
        return $this->trainerModel->getStats($trainerId);
    }

    public function addTrainer() {
    header('Content-Type: application/json');
    
    if($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        exit;
    }

    require_once __DIR__ . '/../helpers/NotificationHelper.php';

    // Validate required fields
    $firstName = trim($_POST['first_name'] ?? '');
    $lastName = trim($_POST['last_name'] ?? '');
    $middleName = trim($_POST['middle_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $contactNo = trim($_POST['contact_no'] ?? '');
    $specialization = trim($_POST['specialization'] ?? '');
    $experienceYears = trim($_POST['experience_years'] ?? '0');

    // Validation
    $errors = [];
    
    if(empty($firstName)) $errors[] = 'First name is required';
    if(empty($lastName)) $errors[] = 'Last name is required';
    if(empty($email)) $errors[] = 'Email is required';
    if(empty($password)) $errors[] = 'Password is required';
    if(empty($specialization)) $errors[] = 'Specialization is required';
    
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email format';
    }
    
    if(strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters';
    }
    
    if($password !== $confirmPassword) {
        $errors[] = 'Passwords do not match';
    }

    if(!empty($errors)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
        exit;
    }

    try {
        $db = $this->trainerModel->connect();
        $db->beginTransaction();

        // Check if email already exists
        $checkQuery = "SELECT user_id FROM members WHERE email = :email";
        $stmt = $db->prepare($checkQuery);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        if($stmt->fetch()) {
            $db->rollBack();
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Email already exists']);
            exit;
        }

        // Insert into members table
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $memberQuery = "INSERT INTO members (first_name, last_name, middle_name, email, password, role, is_active, created_at) 
                    VALUES (:first_name, :last_name, :middle_name, :email, :password, 'trainer', 1, NOW())";
        $stmt = $db->prepare($memberQuery);
        $stmt->bindParam(':first_name', $firstName);
        $stmt->bindParam(':last_name', $lastName);
        $stmt->bindParam(':middle_name', $middleName);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->execute();

        $userId = $db->lastInsertId();

        // Insert into trainers table
        $trainerQuery = "INSERT INTO trainers (user_id, specialization, experience_years, contact_no, status, join_date) 
                        VALUES (:user_id, :specialization, :experience_years, :contact_no, 'active', NOW())";
        $stmt = $db->prepare($trainerQuery);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':specialization', $specialization);
        $stmt->bindParam(':experience_years', $experienceYears);
        $stmt->bindParam(':contact_no', $contactNo);
        $stmt->execute();

        $db->commit();

        // 🔔 SEND NOTIFICATIONS
        $trainerName = "$firstName $lastName";
        
        // Notify the new trainer
        NotificationHelper::welcome($userId, $firstName);
        NotificationHelper::trainerAccountCreated($userId, $specialization);
        
        // Notify all admins
        NotificationHelper::notifyAllAdmins(
            'New Trainer Added',
            "$trainerName has been added as a trainer specializing in $specialization.",
            'index.php?controller=Admin&action=dashboard'
        );

        // 📧 SEND WELCOME EMAIL
        $this->sendNewTrainerEmail($email, $firstName, $specialization, $password);

        echo json_encode(['success' => true, 'message' => 'Trainer added successfully']);
        
    } catch(Exception $e) {
        if(isset($db) && $db->inTransaction()) {
            $db->rollBack();
        }
        error_log("Error adding trainer: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to add trainer']);
    }
    exit;
}

/**
 * Send welcome email to new trainer with credentials
 */
private function sendNewTrainerEmail($email, $firstName, $specialization, $tempPassword) {
    $mail = $this->mailer();
    $mail->addAddress($email, $firstName);
    $mail->Subject = "Welcome to Gymazing - Your Trainer Account";
    $mail->isHTML(true);
    $mail->Body = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: linear-gradient(135deg, #7c3aed, #a855f7); color: white; padding: 30px; text-align: center; }
            .content { background: #f9fafb; padding: 30px; border-radius: 8px; margin: 20px 0; }
            .credentials { background: #fff; border: 2px solid #7c3aed; padding: 20px; border-radius: 8px; margin: 20px 0; }
            .button { background: #7c3aed; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 20px 0; }
            .warning { background: #fef3c7; border-left: 4px solid #f59e0b; padding: 12px; margin: 20px 0; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>🎉 Welcome to Gymazing!</h1>
                <p>You're now part of our trainer team</p>
            </div>
            <div class='content'>
                <h2>Hi $firstName,</h2>
                <p>Your trainer account has been created successfully!</p>
                
                <div class='credentials'>
                    <h3>Your Login Credentials:</h3>
                    <p><strong>Email:</strong> $email</p>
                    <p><strong>Temporary Password:</strong> $tempPassword</p>
                    <p><strong>Specialization:</strong> $specialization</p>
                </div>
                
                <div class='warning'>
                    <strong>⚠️ Important:</strong> Please change your password after your first login for security.
                </div>
                
                <p>As a trainer at Gymazing, you'll have access to:</p>
                <ul>
                    <li>📊 Trainer Dashboard</li>
                    <li>👥 Member Management</li>
                    <li>📅 Session Scheduling</li>
                    <li>📈 Progress Tracking</li>
                    <li>💬 Direct Communication with Members</li>
                </ul>
                
                <a href='https://yourwebsite.com/login' class='button'>Login to Your Dashboard</a>
                
                <p>If you have any questions, please don't hesitate to contact us.</p>
                
                <p>Best regards,<br>The Gymazing Team</p>
            </div>
        </div>
    </body>
    </html>
    ";
    $mail->send();
}

public function updateTrainer() {
    header('Content-Type: application/json');
    
    if($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        exit;
    }

    require_once __DIR__ . '/../helpers/NotificationHelper.php';

    $trainerId = trim($_POST['trainer_id'] ?? '');
    $userId = trim($_POST['user_trainer_id'] ?? '');
    $firstName = trim($_POST['trainer_first_name'] ?? '');
    $lastName = trim($_POST['trainer_last_name'] ?? '');
    $middleName = trim($_POST['trainer_middle_name'] ?? '');
    $email = trim($_POST['trainer_email'] ?? '');
    $contactNo = trim($_POST['trainer_contact_no'] ?? '');
    $specialization = trim($_POST['specialization'] ?? '');
    $experienceYears = trim($_POST['experience_years'] ?? '');
    $status = trim($_POST['trainer_status'] ?? 'active');
    $password = $_POST['trainer_password'] ?? '';

    // Validation
    if(empty($trainerId) || empty($userId) || empty($firstName) || empty($lastName) || empty($email)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        exit;
    }

    if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid email format']);
        exit;
    }

    if(!empty($password) && strlen($password) < 8) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Password must be at least 8 characters']);
        exit;
    }

    try {
        $db = $this->trainerModel->connect();
        $db->beginTransaction();

        // Get old trainer data for comparison
        $oldTrainerData = $this->trainerModel->getTrainerById($trainerId);

        // Check if email exists for another user
        $checkQuery = "SELECT user_id FROM members WHERE email = :email AND user_id != :user_id";
        $stmt = $db->prepare($checkQuery);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        
        if($stmt->fetch()) {
            $db->rollBack();
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Email already exists']);
            exit;
        }

        // Update members table
        if(!empty($password)) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $memberQuery = "UPDATE members SET 
                        first_name = :first_name, 
                        last_name = :last_name, 
                        middle_name = :middle_name, 
                        email = :email,
                        password = :password
                        WHERE user_id = :user_id";
            $stmt = $db->prepare($memberQuery);
            $stmt->bindParam(':password', $hashedPassword);
        } else {
            $memberQuery = "UPDATE members SET 
                        first_name = :first_name, 
                        last_name = :last_name, 
                        middle_name = :middle_name, 
                        email = :email
                        WHERE user_id = :user_id";
            $stmt = $db->prepare($memberQuery);
        }
        
        $stmt->bindParam(':first_name', $firstName);
        $stmt->bindParam(':last_name', $lastName);
        $stmt->bindParam(':middle_name', $middleName);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();

        // Update trainers table
        $trainerQuery = "UPDATE trainers SET 
                        specialization = :specialization, 
                        experience_years = :experience_years, 
                        contact_no = :contact_no,
                        status = :status
                        WHERE trainer_id = :trainer_id";
        $stmt = $db->prepare($trainerQuery);
        $stmt->bindParam(':specialization', $specialization);
        $stmt->bindParam(':experience_years', $experienceYears);
        $stmt->bindParam(':contact_no', $contactNo);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':trainer_id', $trainerId);
        $stmt->execute();

        $db->commit();

        // 🔔 SEND NOTIFICATIONS
        $trainerName = "$firstName $lastName";
        
        // Notify trainer about profile update
        NotificationHelper::profileUpdated($userId);
        
        // If status changed to inactive
        if($oldTrainerData['status'] === 'active' && $status === 'inactive') {
            NotificationHelper::trainerDeactivated($userId);
        }
        
        // If status changed to active
        if($oldTrainerData['status'] === 'inactive' && $status === 'active') {
            NotificationHelper::trainerReactivated($userId);
        }

        // 📧 SEND EMAIL if status changed
        if($oldTrainerData['status'] !== $status) {
            $this->sendTrainerStatusChangeEmail($email, $firstName, $status);
        }

        echo json_encode(['success' => true, 'message' => 'Trainer updated successfully']);
        
    } catch(Exception $e) {
        if(isset($db) && $db->inTransaction()) {
            $db->rollBack();
        }
        error_log("Error updating trainer: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to update trainer']);
    }
    exit;
}

/**
 * Send email when trainer status changes
 */
private function sendTrainerStatusChangeEmail($email, $firstName, $status) {
    $mail = $this->mailer();
    $mail->addAddress($email, $firstName);
    $mail->Subject = $status === 'active' ? "Your Trainer Account is Now Active" : "Trainer Account Status Update";
    $mail->isHTML(true);
    $statusMessage = $status === 'active' 
        ? "Your trainer account has been reactivated! You can now access all trainer features." 
        : "Your trainer account has been temporarily deactivated. Please contact administration for more information.";
    
    $mail->Body = "
    <html>
    <body style='font-family: Arial, sans-serif;'>
        <h2>Hi $firstName,</h2>
        <p>$statusMessage</p>
        <p>If you have any questions, please contact our support team.</p>
        <p>Best regards,<br>The Gymazing Team</p>
    </body>
    </html>
    ";
    
    $mail->send();
}

public function deleteTrainer() {
    header('Content-Type: application/json');
    
    if($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        exit;
    }

    require_once __DIR__ . '/../helpers/NotificationHelper.php';
    
    $trainerId = $_POST['trainer_id'] ?? '';

    if(empty($trainerId)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Trainer ID is required']);
        exit;
    }

    try {
        // Get trainer data before deletion
        $trainerData = $this->trainerModel->getTrainerById($trainerId);
        
        if(!$trainerData) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Trainer not found']);
            exit;
        }

        // Deactivate trainer
        if($this->trainerModel->deleteTrainerViaId($trainerId)) {
            
            // 🔔 SEND NOTIFICATIONS
            $userId = $trainerData['user_id'];
            $trainerName = $trainerData['name'];
            
            // Notify the trainer
            NotificationHelper::trainerDeactivated($userId);
            
            // Notify all admins
            NotificationHelper::notifyAllAdmins(
                'Trainer Deactivated',
                "$trainerName's trainer account has been deactivated.",
                'index.php?controller=Admin&action=dashboard'
            );

            // 📧 SEND EMAIL
            $this->sendTrainerDeactivationEmail(
                $trainerData['email'], 
                $trainerData['first_name']
            );

            echo json_encode([
                'success' => true,
                'message' => 'Trainer deactivated successfully',
            ]);
        } else {
            throw new Exception('Failed to deactivate trainer');
        }
        
    } catch(Exception $e) {
        error_log("Error deactivating trainer: " . $e->getMessage());
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'An error occurred. Please try again.',
        ]);
    }
    exit;
}

/**
 * Send email when trainer is deactivated
 */
    private function sendTrainerDeactivationEmail($email, $firstName) {
        $mail = $this->mailer();
        $mail->addAddress($email, $firstName);
        $mail->Subject = "Trainer Account Deactivated";
        $mail->isHTML(true);
        $mail->Body = "
        <html>
        <body style='font-family: Arial, sans-serif;'>
            <h2>Hi $firstName,</h2>
            <p>We're writing to inform you that your trainer account at Gymazing has been deactivated.</p>
            <p>If you believe this is an error or would like to discuss reactivation, please contact our administration team.</p>
            <p>Thank you for your service to our gym community.</p>
            <p>Best regards,<br>The Gymazing Team</p>
        </body>
        </html>
        ";
        
        $mail->send();
    }
    public function createSession() {
        $this->requireLogin();
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }

        // Get POST data
        $userId = $_POST['user_id'] ?? null;
        $trainerId = $_SESSION['trainer_id'] ?? 1;
        $sessionDate = $_POST['session_date'] ?? null;
        $notes = $_POST['notes'] ?? '';

        // Validation
        if (!$userId || !$trainerId || !$sessionDate) {
            echo json_encode(['success' => false, 'message' => 'Please fill in all required fields.']);
            exit;
        }

        // Security: Ensure the logged-in trainer is the one creating the session
        // (Assuming session stores trainer_id, otherwise check against user_id)

        // $loggedInTrainerId = $_SESSION['trainer_id'] ?? null;
        // echo $loggedInTrainerId;
        // if ($loggedInTrainerId && $loggedInTrainerId != $trainerId) {
        //      echo json_encode(['success' => false, 'message' => 'Unauthorized action.']);
        //      exit;
        // }

        $data = [
            'user_id' => $userId,
            'trainer_id' => $trainerId,
            'session_date' => $sessionDate,
            'notes' => $notes,
            'status' => 'scheduled'
        ];

        if ($this->sessionModel->create($data)) {
            echo json_encode(['success' => true, 'message' => 'Session scheduled successfully!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to schedule session.']);
        }
        exit;
    }

    public function updateSessionStatus() {
        $this->requireLogin();
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }

        $sessionId = $_POST['session_id'] ?? null;
        $status = $_POST['status'] ?? null;

        if (!$sessionId || !$status) {
            echo json_encode(['success' => false, 'message' => 'Missing required parameters.']);
            exit;
        }

        // Validate allowed statuses
        $allowedStatuses = ['scheduled', 'completed', 'cancelled'];
        if (!in_array($status, $allowedStatuses)) {
            echo json_encode(['success' => false, 'message' => 'Invalid status provided.']);
            exit;
        }

        if ($this->sessionModel->updateStatus($sessionId, $status)) {
            echo json_encode(['success' => true, 'message' => 'Session updated successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update session.']);
        }
        exit;
    }

    public function handleRequest() {
        $this->requireLogin();
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }

        $requestId = $_POST['request_id'] ?? null;
        $action = $_POST['action'] ?? null; // 'accepted' or 'rejected'

        if (!$requestId || !$action) {
            echo json_encode(['success' => false, 'message' => 'Missing ID or Action.']);
            exit;
        }

        if ($this->trainerModel->handleRequest($requestId, $action)) {
            echo json_encode(['success' => true, 'message' => 'Request ' . $action . ' successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Database error occurred.']);
        }
        exit;
    }
}   
?>
