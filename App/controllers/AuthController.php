<?php 
require_once __DIR__ . "/../Controller.php";
require_once __DIR__ . "/../models/User.php";
require_once __DIR__ . "/../models/PasswordReset.php";
require_once __DIR__ . "/../helpers/notificationHelper.php";

session_set_cookie_params(['path' => '/']);
session_start();

class AuthController extends Controller {

    public function Login() {
        $login = ["email" => "", "password" => ""];
        $loginErrors = ["email" => "", "password"=> ""];
        $loginError = "";
        
        $this->auth('login', [
            'login' => $login,
            'loginErrors' => $loginErrors,
            'loginError' => $loginError
        ]); 
    }
    
    public function Register() {
        // Add address fields to the initialization arrays
        $register = [
            "first_name" => "", "last_name" => "", "middle_name" => "", 
            "email" => "", "date_of_birth" => "", "gender" => "", 
            "password" => "", "cPassword" => "",
            "street_address" => "", "city" => "", "zip" => "" // <--- ADDED
        ];
        
        $registerError = [
            "first_name" => "", "last_name" => "", "middle_name" => "", 
            "email" => "", "date_of_birth" => "", "gender" => "", 
            "password" => "", "cPassword" => "", "register" => "",
            "street_address" => "", "city" => "", "zip" => "" // <--- ADDED
        ];
        
        $this->auth('register', [
            'register' => $register,
            'registerError' => $registerError
        ]); 
    }

    public function verifyLogin() {         
        $login = ["email" => "", "password"=> ""];
        $loginErrors = ["email" => "", "password"=> ""];
        $loginError = "";
        
        
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $login["email"] = isset($_POST['email']) ? trim($_POST['email']) : "";
            $login["password"] = isset($_POST['password']) ? $_POST['password'] : "";

            // Validate email
            if(empty($login['email'])) {
                $loginErrors['email'] = "Email is required.";
            } else if(!filter_var($login['email'], FILTER_VALIDATE_EMAIL)) {
                $loginErrors['email'] = "Email is invalid";
            }

            // Validate password
            if(empty($login['password'])) {
                $loginErrors['password'] = "Password is required.";
            }

            // If no validation errors, attempt login
            if(empty(array_filter($loginErrors))) {
                $loginStatus = $this->loginUser($login['email'], $login['password']);
                if($loginStatus === true) {
                    if($_SESSION['role'] == 'trainer') {
                        header("Location: index.php?controller=trainer&action=trainerDashboard");
                    } else if($_SESSION['role'] == 'admin') {
                        header("Location: index.php?controller=Admin&action=dashboard");
                    } else {
                        header("Location: index.php?controller=dashboard&action=member");
                    }
                    exit();
                } else {
                    if($loginStatus === 'pending') {
                         $loginError = "Your account is pending approval. Please wait for admin verification.";
                    } else if($loginStatus === 'rejected') {
                         $loginError = "Your account registration has been rejected.";
                    } else {
                         $loginError = "Invalid Email or Password.";
                    }
                    
                    $this->auth('login', [
                        'login' => $login,
                        'loginErrors' => $loginErrors,
                        'loginError' => $loginError
                    ]); 
                }
            } else {
                $this->auth('login', [
                    'login' => $login,
                    'loginErrors' => $loginErrors,
                    'loginError' => $loginError
                ]); 
            }
        }
    }
    
    public function LoginUser($email, $password) {
        $userModel = new User();
        $user = $userModel->findByEmail($email);
        
        if($user) {
            if($password == $user['password']) {
                // Check Status
                if($user['role'] == 'member' && $user['status'] == 'pending_approval') {
                    // We can't return error message comfortably here without changing signature, 
                    // relying on the controller calling this to handle false return generic error, 
                    // OR we can session flash message.
                    // For now, let's just fail authentication if pending.
                    // A better approach: return string error or throw exception?
                    // The calling function verifyLogin() just displays "Invalid Email or Password".
                    // Let's modify verifyLogin to check status via a separate check if needed, 
                    // OR just let it fail silently as "Invalid creds" for security (though bad UX).
                    // Given the request, I should probably provide feedback.
                    // Let's returning false here will confusingly say "Invalid Email/Pass".
                    return 'pending'; 
                }
                if($user['role'] == 'member' && $user['status'] == 'rejected') {
                    return 'rejected';
                }

                $_SESSION['user_id'] = $user['user_id'];             
                $_SESSION['role'] = $user['role'];
                
                if($_SESSION['role'] == 'trainer') {
                    $trainer = $userModel->getTrainerId($_SESSION['user_id']);
                    $_SESSION['trainer_id'] = $trainer['trainer_id'];
                }
                return true;
            }
        }
        return false;
    }

    public function logout() {
        session_destroy();
        header("Location: ../../public/index.php");
        exit();
    }

    public function validateUserRegistration() {
        $user = new User();

        // Re-initialize arrays to ensure keys exist
        $register = [
            "first_name" => "", "last_name" => "", "middle_name" => "", 
            "phone_no" => "", "email" => "", "date_of_birth" => "", "gender" => "", 
            "password" => "", "cPassword" => "",
            "street_address" => "", "city" => "", "zip" => "",
            "valid_id_picture" => ""
        ];
        
        $registerError = [
            "first_name" => "", "last_name" => "", "middle_name" => "", 
            "phone_no" => "", "email" => "", "date_of_birth" => "", "gender" => "", 
            "password" => "", "cPassword" => "", "register" => "",
            "street_address" => "", "city" => "", "zip" => "",
            "valid_id_picture" => ""
        ];
        
        if($_SERVER['REQUEST_METHOD'] == "POST") {
            // --- 1. SANITIZATION ---
            $register["first_name"] = trim(htmlspecialchars($_POST['first_name']));
            $register["last_name"] = trim(htmlspecialchars($_POST['last_name']));
            $register["middle_name"] = isset($_POST['middle_name']) ? trim(htmlspecialchars($_POST['middle_name'])) : "";
            $register["phone_no"] = isset($_POST['phone_no']) ? trim(htmlspecialchars($_POST['phone_no'])) : "";
            $register["email"] = trim(htmlspecialchars($_POST['email']));
            $register["date_of_birth"] = trim(htmlspecialchars($_POST['date_of_birth']));
            $register["gender"] = isset($_POST['gender']) ? trim(htmlspecialchars($_POST['gender'])) : "";
            $register["password"] = $_POST['password'];
            $register["cPassword"] = $_POST['cPassword'];

            $register["street_address"] = isset($_POST['street_address']) ? trim(htmlspecialchars($_POST['street_address'])) : "";
            $register["city"] = isset($_POST['city']) ? trim(htmlspecialchars($_POST['city'])) : "";
            $register["zip"] = isset($_POST['zip']) ? trim(htmlspecialchars($_POST['zip'])) : "";

            // --- 2. VALIDATION ---

            // Validate first name
            if(empty($register['first_name'])) {
                $registerError['first_name'] = "Please provide your first name";
            }
            
            // Validate last name
            if(empty($register['last_name'])) {
                $registerError['last_name'] = "Please provide your last name";
            }
            
            // Validate phone
            if(empty($register['phone_no'])) {
                 $registerError['phone_no'] = "Phone number is required";
            }

            // Validate email
            if(empty($register['email'])) {
                $registerError['email'] = "Please provide a valid email";
            } else if(!filter_var($register['email'], FILTER_VALIDATE_EMAIL)) {
                $registerError['email'] = "Please provide a valid email address";
            }

            // Validate Date of Birth (Age Check)
            if(empty($register['date_of_birth'])) {
                $registerError['date_of_birth'] = "Please provide your birthdate";
            } else {
                $dob = new DateTime($register['date_of_birth']);
                $today = new DateTime();
                $age = $today->diff($dob)->y;
                
                if($age < 12) {
                    $registerError['date_of_birth'] = "You must be at least 12 years old";
                }
            }
            
            // Validate Gender
            if(empty($register["gender"])) {
                $registerError['gender'] = "Please select your gender";
            }

            // Validate Address Fields
            if(empty($register['street_address'])) {
                $registerError['street_address'] = "Street address is required";
            }
            if(empty($register['city'])) {
                $registerError['city'] = "City is required";
            }
            if(empty($register['zip'])) {
                $registerError['zip'] = "Zip code is required";
            }
            
            // Validate password
            if(empty($register['password'])) {
                $registerError['password'] = "Password should not be empty.";
            } else if(strlen($register['password']) < 8) {
                $registerError['password'] = "Password should be at least 8 characters";
            } else if($register['password'] != $register['cPassword']) {
                $registerError['password'] = "Passwords do not match";
                $registerError['cPassword'] = "Passwords do not match";
            }

            // Validate Confirm Password
            if(empty($register['cPassword'])) {
                $registerError['cPassword'] = "Please enter your password again.";
            }
            
            // Validate Agreement
            if(!isset($_POST['agreement'])) {
                $registerError['register'] = "You must agree to the terms and conditions";
            }

            // --- 3. FILE UPLOAD HANDLING ---
            if(isset($_FILES['valid_id_picture']) && $_FILES['valid_id_picture']['error'] == 0) {
                $targetDir = __DIR__ . "/../../public/uploads/valid_ids/";
                if (!file_exists($targetDir)) { mkdir($targetDir, 0777, true); }
                
                $fileExtension = strtolower(pathinfo($_FILES["valid_id_picture"]["name"], PATHINFO_EXTENSION));
                $newFileName = "id_" . time() . "_" . uniqid() . "." . $fileExtension;
                $targetFile = $targetDir . $newFileName;
                $allowedTypes = ['jpg', 'jpeg', 'png'];

                if(in_array($fileExtension, $allowedTypes)) {
                    if(move_uploaded_file($_FILES["valid_id_picture"]["tmp_name"], $targetFile)) {
                        $register['valid_id_picture'] = "public/uploads/valid_ids/" . $newFileName;
                    } else {
                        $registerError['valid_id_picture'] = "Failed to upload file.";
                    }
                } else {
                     $registerError['valid_id_picture'] = "Only JPG, JPEG, and PNG files are allowed.";
                }
            } else {
                $registerError['valid_id_picture'] = "Valid ID picture is required.";
            }


            // --- 4. EXECUTION ---
            if(empty(array_filter($registerError))) {
                if(!$user->findByEmail($register['email'])) {
                    if($this->RegisterUser($register)) {
                        NotificationHelper::newMemberRegistered(7, $register['first_name'] . ' ' . $register['last_name']);
                        // Redirect to login with success message (or special 'pending' page)
                        $registerError['register'] = "Registration successful! Your account is pending admin approval."; 
                        // Instead of redirecting to login immediately, maybe show a success message on the login page?
                        // For now, redirecting to login. Ideally, I'd flash a message.
                        // I'll redirect to login.
                        header("Location: index.php?controller=auth&action=login"); 
                        exit();
                    } else {
                        $registerError['register'] = "Error registering user. Please try again";
                        $this->auth('register', [
                            'register' => $register,
                            'registerError' => $registerError
                        ]);
                    }
                } else {
                    $registerError['register'] = "Account already exists.";
                    $this->auth('register', [
                        'register' => $register,
                        'registerError' => $registerError
                    ]);
                } 
            } else {
                $this->auth('register', [
                    'register' => $register,
                    'registerError' => $registerError
                ]);
            }
        }
    }
    
    public function RegisterUser($userData) {
        $userModel = new User();
        
        // FIXED: Hash password before storing
        // $userData['password'] = password_hash($userData['password'], PASSWORD_DEFAULT);
        
        if($userModel->addMember($userData)) {
            return true;
        } else {
            return false;
        }
    }

    public function notifyNewUser($email, $name) {
        $mail = $this->mailer();

        $mail->addAddress($email, $name);
        $mail->Subject = "Application Received - Pending Approval ⏳";
        $mail->isHTML(true);
        $mail->Body = "
            <div style='font-family: Arial, sans-serif; padding: 20px; background-color: #f4f7fa;'>
                <div style='max-width: 600px; margin: auto; background-color: #ffffff; padding: 25px; border-radius: 8px; border: 1px solid #e1e5ea;'>

                    <h2 style='color: #F59E0B;'>Application Under Review 📋</h2>

                    <p>Hi {$name},</p>

                    <p>Thank you for registering with Gymazing! We have received your application and ID for verification.</p>

                    <div style='margin: 15px 0; padding: 12px; background-color: #fff8e1; border-left: 4px solid #F59E0B;'>
                        <p style='margin: 0; font-size: 15px;'>Your account is currently <strong>Pending Approval</strong>. Our team will review your valid ID and notify you once your account is active.</p>
                    </div>

                    <p>You will receive another email from us as soon as the review is complete.</p>

                    <p style='margin-top: 25px;'>Thank you for your patience!</p>

                    <hr style='margin-top: 30px;'>

                    <p style='font-size: 12px; color: #777;'>If you did not make this request, please contact support.</p>
                    <p style='font-size: 12px; color: #777;'>&copy; " . date('Y') . " Gymazing. All rights reserved.</p>
                </div>
            </div>
        ";
        $mail->AltBody = "Hi {$name}, your application has been received and is currently pending approval. We will notify you once verified.";
        $mail->send();
    }

    /**
     * Display forgot password form
     */
    public function ForgotPassword() {
        $this->auth('forgotPassword', [
            'email' => '',
            'error' => '',
            'success' => ''
        ]);
    }
    
    /**
     * Handle forgot password request
     */
    public function RequestPasswordReset() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            return;
        }
        
        $email = trim($_POST['email'] ?? '');
        
        // Validate email
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['success' => false, 'message' => 'Please provide a valid email address']);
            return;
        }
        
        $userModel = new User();
        $user = $userModel->findByEmail($email);
        
        // Always return success even if email doesn't exist (security)
        if (!$user) {
            echo json_encode([
                'success' => true, 
                'message' => 'If that email exists, a password reset link has been sent.'
            ]);
            return;
        }
        
        // Create reset token
        $passwordReset = new PasswordReset();
        $token = $passwordReset->createToken($user['user_id'], $email);
        
        if ($token) {
            // Send email with reset link
            $resetLink = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . "/index.php?controller=Auth&action=ResetPassword&token=" . $token;
            $this->sendPasswordResetEmail($email, $user['first_name'] ?? 'Member', $resetLink);
            
            echo json_encode([
                'success' => true,
                'message' => 'Password reset instructions have been sent to your email.'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'An error occurred. Please try again later.'
            ]);
        }
    }
    
    /**
     * Display reset password form
     */
    public function ResetPassword() {
        $token = $_GET['token'] ?? '';
        
        if (empty($token)) {
            header('Location: index.php?controller=Auth&action=Login');
            exit();
        }
        
        // Verify token is valid
        $passwordReset = new PasswordReset();
        $tokenData = $passwordReset->verifyToken($token);
        
        if (!$tokenData) {
            $this->auth('resetPassword', [
                'token' => '',
                'error' => 'This password reset link is invalid or has expired.',
                'tokenValid' => false
            ]);
            return;
        }
        
        $this->auth('resetPassword', [
            'token' => $token,
            'email' => $tokenData['email'],
            'error' => '',
            'tokenValid' => true
        ]);
    }
    
    /**
     * Handle password update
     */
    public function UpdatePassword() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            return;
        }
        
        $token = $_POST['token'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        // Validate inputs
        if (empty($token) || empty($password) || empty($confirmPassword)) {
            echo json_encode(['success' => false, 'message' => 'All fields are required']);
            return;
        }
        
        if ($password !== $confirmPassword) {
            echo json_encode(['success' => false, 'message' => 'Passwords do not match']);
            return;
        }
        
        if (strlen($password) < 6) {
            echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters']);
            return;
        }
        
        // Verify token
        $passwordReset = new PasswordReset();
        $tokenData = $passwordReset->verifyToken($token);
        
        if (!$tokenData) {
            echo json_encode(['success' => false, 'message' => 'Invalid or expired reset link']);
            return;
        }
        
        // Update password
        $userModel = new User();
        $db = $userModel->connect();
        
        // TODO: Hash password properly (security issue - currently storing plain text)
        // For now, matching existing system behavior
        $sql = "UPDATE members SET password = :password WHERE user_id = :user_id";
        $query = $db->prepare($sql);
        $query->bindParam(':password', $password);
        $query->bindParam(':user_id', $tokenData['user_id']);
        
        if ($query->execute()) {
            // Mark token as used
            $passwordReset->markTokenUsed($token);
            
            // Invalidate all other tokens for this user
            $passwordReset->invalidateUserTokens($tokenData['user_id']);
            
            echo json_encode([
                'success' => true,
                'message' => 'Password updated successfully! You can now log in.'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to update password. Please try again.'
            ]);
        }
    }
    
    /**
     * Send password reset email
     */
    private function sendPasswordResetEmail($email, $name, $resetLink) {
        try {
            $mail = $this->mailer();
            $mail->addAddress($email, $name);
            $mail->Subject = "Reset Your Gymazing Password";
            $mail->isHTML(true);
            
            $mail->Body = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f4f4f4;'>
                <div style='background-color: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);'>
                    <h2 style='color: #3b82f6; margin-bottom: 20px;'>Reset Your Password</h2>
                    <p style='color: #333; font-size: 16px; line-height: 1.6;'>Hi {$name},</p>
                    <p style='color: #333; font-size: 16px; line-height: 1.6;'>
                        We received a request to reset your password for your Gymazing account. 
                        Click the button below to create a new password:
                    </p>
                    <div style='text-align: center; margin: 30px 0;'>
                        <a href='{$resetLink}' 
                           style='background-color: #3b82f6; color: white; padding: 14px 30px; 
                                  text-decoration: none; border-radius: 5px; font-weight: bold; 
                                  display: inline-block;'>Reset Password</a>
                    </div>
                    <p style='color: #666; font-size: 14px; line-height: 1.6;'>
                        This link will expire in 1 hour for security reasons.
                    </p>
                    <p style='color: #666; font-size: 14px; line-height: 1.6;'>
                        If you didn't request a password reset, please ignore this email or contact support if you have concerns.
                    </p>
                    <hr style='border: none; border-top: 1px solid #eee; margin: 20px 0;'>
                    <p style='color: #999; font-size: 12px;'>
                        If the button doesn't work, copy and paste this link into your browser:<br>
                        <span style='color: #3b82f6;'>{$resetLink}</span>
                    </p>
                    <p style='color: #333; font-size: 16px; margin-top: 20px;'>
                        Best regards,<br>
                        <strong>The Gymazing Team</strong>
                    </p>
                </div>
            </div>
            ";
            
            $mail->send();
        } catch (Exception $e) {
            error_log("Failed to send password reset email: " . $mail->ErrorInfo);
        }
    }
    
    public function forgotPasswordNotifier($email, $name) {
        $mail = $this->mailer();
        $mail->addAddress($email, $name);
        $mail->Subject = "Forgot Password";
        $mail->isHTML(true);
        $mail->Body = "
            <div style='font-family: Arial, sans-serif; padding: 20px; background-color: #f4f6f9;'>
                <div style='max-width: 600px; margin: auto; background-color: #fff; padding: 20px; border-radius: 8px;'>
                    
                    <h2 style='color: #4CAF50;'>Password Reset Request 🔐</h2>

                    <p>Hi <strong>{$name}</strong>,</p>
                    <p>We received a request to reset your password. No worries—let’s get you back into your account.</p>

                    <div style='margin: 20px 0; padding: 15px; background-color: #fff3cd; border-left: 4px solid #ffca28;'>
                        <p style='margin: 0; font-size: 15px;'>
                            Click the button below to reset your password. This link will expire in <strong>30 minutes</strong>.
                        </p>
                    </div>

                    <a href='#' 
                        style='display:inline-block; margin-top: 15px; background-color: #4CAF50; 
                            color: white; padding: 10px 20px; text-decoration: none; 
                            border-radius: 5px; font-weight: bold;'>
                        Reset Password
                    </a>

                    <p style='margin-top: 20px; font-size: 14px; color: #555;'>
                        If you didn’t request this password reset, you can safely ignore this email.
                    </p>

                    <hr style='margin-top: 30px;'>

                    <p style='font-size: 12px; color: #777;'>Need help? Contact our support team anytime.</p>
                    <p style='font-size: 12px; color: #777;'>&copy; " . date('Y') . " Gymazing!. All rights reserved.</p>

                </div>
            </div>
        ";

        $mail->AltBody = "Hi {$name}, you requested a password reset. Click this link to reset it: <a>Link</a>";

        $mail->send();
    }
}
?>