<?php 
    require_once __DIR__ . "/../Controller.php";
    require_once __DIR__ . "/../models/User.php";
    require_once __DIR__ . "/../models/Trainer.php";
    require_once __DIR__ . "/../models/Session.php";
    class UserController extends Controller {

        public function getUserDetails($user_id) {
            $this->requireLogin();
            $userModel = new User();
            $sql = "SELECT CONCAT(m.first_name, ' ', m.last_name) as name, m.first_name, m.email, m.role, m.created_at, p.plan_name, s.end_date, s.status FROM members m 
            JOIN subscriptions s ON s.user_id = m.user_id
            LEFT JOIN membership_plans p ON p.plan_id = s.plan_id 
            WHERE m.user_id = :user_id";
            $query = $userModel->connect()->prepare($sql);
            $query->bindParam(":user_id", $user_id);
            if($query->execute()) {
                return $query->fetch();
            } else {
                return null;
            }
        }

        public function getMemberData() {
            if (!isset($_GET['user_id'])) {
                echo json_encode(['success' => false, 'message' => 'User ID is required']);
                return;
            }
            $userModel = new User();
            $userId = $_GET['user_id'];

            $memberData = $userModel->getMemberDetailsById($userId);

            if ($memberData) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'data' => $memberData
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Member not found']);
            }
            exit;
        }

        public function validateWalkin() {
            $userModel = new User();
            header('Content-Type: application/json');
            if($_SERVER['REQUEST_METHOD'] == 'POST') {
                $walkinDetails = [
                    "first_name" => "",
                    "last_name" => "",
                    "middle_name" => "",
                    "email" => "",
                    "contact_no" => "",
                    "session_type" => "",
                    "payment_method" => "",
                    "payment_amount" => "",
                    "visit_time" => "",
                    "end_date" => "",
                ];
                $walkinDetails['first_name'] = trim(htmlspecialchars($_POST['first_name']));
                $walkinDetails['last_name'] = trim(htmlspecialchars($_POST['last_name']));
                $walkinDetails['middle_name'] = trim(htmlspecialchars($_POST['middle_name']) ?? "");
                $walkinDetails['email'] = trim(htmlspecialchars($_POST['email']) ?? "");
                $walkinDetails['contact_no'] = trim(htmlspecialchars($_POST['contact_no']));
                $walkinDetails['session_type'] = trim(htmlspecialchars($_POST['session_type']));
                $walkinDetails['payment_method'] = trim(htmlspecialchars($_POST['payment_method']));
                $walkinDetails['payment_amount'] = trim(htmlspecialchars($_POST['payment_amount']));
                $walkinDetails['visit_time'] = date("Y-m-d h:i:s");
                $walkinDetails['end_date'] = date("Y-m-d h:i:s", strtotime('+1 days'));
                if(!$walkinDetails) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Missing required payment data from the form.']);
                    exit;
                }
                $result = $userModel->addWalkinMember($walkinDetails);
                if($result) {
                    http_response_code(200);
                    echo json_encode([
                        'success' => true,
                        'message' => 'Added Successfully',
                    ]);
                } else {
                    http_response_code(400);
                    echo json_encode([
                        'success' => false,
                        'message' => 'Error user has not been added successfully, please try again.',
                    ]);
                }
            } else {
                http_response_code(405);
                echo json_encode([
                    'success' => false,
                    'error' => 'invalid request method',
                ]);
            }
        }

        public function getWalkinData() {
            $this->requireLogin();
            
            if (!isset($_GET['walkin_id'])) {
                echo json_encode(['success' => false, 'message' => 'Walk-in ID is required']);
                return;
            }

            $userModel = new User();
            $walkinData = $userModel->getWalkinById($_GET['walkin_id']);

            if ($walkinData) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'data' => $walkinData
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Walk-in record not found']);
            }
            exit;
        }

        public function updateWalkin() {
            $this->requireLogin();
            header('Content-Type: application/json');

            if($_SERVER['REQUEST_METHOD'] == 'POST') {
                $walkin_id = $_POST['walkin_id'];
                $userModel = new User();
                
                $walkinDetails = [
                    'first_name' => trim(htmlspecialchars($_POST['first_name'])),
                    'last_name' => trim(htmlspecialchars($_POST['last_name'])),
                    'middle_name' => trim(htmlspecialchars($_POST['middle_name'] ?? "")),
                    'email' => trim(htmlspecialchars($_POST['email'] ?? "")),
                    'contact_no' => trim(htmlspecialchars($_POST['contact_no'])),
                    'session_type' => trim(htmlspecialchars($_POST['session_type'])),
                    'payment_method' => trim(htmlspecialchars($_POST['payment_method'])),
                    'payment_amount' => trim(htmlspecialchars($_POST['payment_amount'])),
                    'visit_time' => $_POST['visit_time'],
                    'end_date' => $_POST['end_date']
                ];

                if($userModel->updateWalkinMember($walkinDetails, $walkin_id)) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Walk-in details updated successfully',
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Failed to update walk-in details',
                    ]);
                }
            }
        }
        public function updateMember() {
            $this->requireLogin();
            $user_id = $_GET['user_id'];
            $user = new User();
            $userData = [
                'first_name' => "",
                'last_name' => "",
                'middle_name' => "",
                'email' => "",
                'password' => "",
                'role' => "",
                'status' => ""
            ];
            $userData['first_name'] = trim(htmlspecialchars($_POST['first_name']));
            $userData['last_name'] = trim(htmlspecialchars($_POST['last_name']));
            $userData['middle_name'] = trim(htmlspecialchars($_POST['middle_name']));
            $userData['email'] = trim(htmlspecialchars($_POST['email']));
            $userData['password'] = trim(htmlspecialchars($_POST['password']));
            $userData['role'] = trim(htmlspecialchars($_POST['role']));
            $userData['status'] = trim(htmlspecialchars($_POST['status']));
            if($user->updateMemberViaUserId($userData, $user_id)) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Update Complete!',
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Error failed to update user details, Please try again.',
                ]);
            }
        }
        public function deleteMember() {
            $user_id = $_POST['user_id'];
            $user = new User();
            if($user->deleteMemberViaId($user_id)) {
                echo json_encode([
                    'success' => true,
                    'message' => 'member set to inactive',
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'An error occured, Please try again.',
                ]);
            }
        }
        
        public function deleteWalkin() {
            $walkin_id = $_POST['walkin_id'];
            $user = new User();
            if($user->deleteWalkinViaId($walkin_id)) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Walk-in record deleted successfully',
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'An error occurred, Please try again.',
                ]);
            }
        }
        public function profile() {
            $this->requireLogin();
            $user_id = $_SESSION['user_id'];
            $userModel = new User();
            
            $userInfo = $userModel->getMember($user_id);
            
            // --- NEW: Fetch Address ---
            $addressInfo = $userModel->getMemberAddress($user_id);
            
            $profileData = [
                'userInfo' => $userInfo,
                'addressInfo' => $addressInfo, // <--- Add this line
                'role' => $userInfo['role'] ?? '',
                'assignedMembers' => [],
                'sessions' => []
            ];

            if ($userInfo['role'] === 'trainer') {
                $trainerModel = new Trainer();
                $assignedMembers = $trainerModel->getAssignedMembers($user_id);
                $sessionModel = new Session();
                $sessions = $sessionModel->getUpcomingByTrainer($user_id);
                
                $profileData['assignedMembers'] = $assignedMembers;
                $profileData['sessions'] = $sessions;
            } else if ($userInfo['role'] === 'member') {
                $sessionModel = new Session();
                $sessions = $sessionModel->getByUser($user_id);
                $profileData['sessions'] = $sessions;
            }
            
            $this->view('profile', $profileData);
        }
        public function editProfile() {
            $this->requireLogin();
            $user_id = $_SESSION['user_id'];
            $userModel = new User();
            
            // Fetch existing data to pre-fill the form
            $userInfo = $userModel->getMember($user_id);
            
            // Re-fetching specific details just to be safe if getMember is limited
            $fullDetails = $userModel->getMemberDetailsById($user_id);
            
            $addressInfo = $userModel->getMemberAddress($user_id);
            
            $this->view('edit_profile', [
                'user' => $userInfo,
                'address' => $addressInfo
            ]);
        }

        public function saveProfile() {
            $this->requireLogin();
            $user_id = $_SESSION['user_id'];
            
            if($_SERVER['REQUEST_METHOD'] == 'POST') {
                $userModel = new User();
                
                // 1. Prepare Profile Data
                $profileData = [
                    'first_name' => trim(htmlspecialchars($_POST['first_name'])),
                    'last_name'  => trim(htmlspecialchars($_POST['last_name'])),
                    'middle_name'=> trim(htmlspecialchars($_POST['middle_name'])),
                    'email'      => trim(htmlspecialchars($_POST['email'])),
                    'phone_no'   => trim(htmlspecialchars($_POST['phone_no']))
                ];

                // 2. Prepare Address Data
                $street = trim(htmlspecialchars($_POST['street_address']));
                $city   = trim(htmlspecialchars($_POST['city']));
                $zip    = trim(htmlspecialchars($_POST['zip']));

                // 3. Handle File Upload
                $profilePicturePath = null;
                if(isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
                    $targetDir = __DIR__ . "/../../public/uploads/profile_images/";
                    
                    // Create directory if not exists
                    if (!file_exists($targetDir)) {
                        mkdir($targetDir, 0777, true);
                    }

                    $fileExtension = strtolower(pathinfo($_FILES["profile_picture"]["name"], PATHINFO_EXTENSION));
                    $newFileName = "user_" . $user_id . "_" . time() . "." . $fileExtension;
                    $targetFile = $targetDir . $newFileName;
                    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

                    if(in_array($fileExtension, $allowedTypes)) {
                        if(move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $targetFile)) {
                            $profilePicturePath = "public/uploads/profile_images/" . $newFileName;
                        } else {
                           // Handle upload error (optional)
                        }
                    }
                }

                // Append profile picture to data if uploaded, otherwise keep existing
                if($profilePicturePath) {
                    $profileData['profile_picture'] = $profilePicturePath;
                } else {
                     // Get existing picture if not updating
                    $existingUser = $userModel->getMember($user_id);
                    $profileData['profile_picture'] = $existingUser['profile_picture'];
                }

                // 4. Update Database
                $profileUpdated = $userModel->updateMemberProfile($user_id, $profileData);
                $addressUpdated = $userModel->updateUserAddress($user_id, $zip, $street, $city);

                if($profileUpdated && $addressUpdated) {
                    // Success
                    header("Location: index.php?controller=User&action=profile");
                    exit;
                } else {
                    // Error handling
                    echo "Failed to update profile.";
                }
            }
        }
    }
?>