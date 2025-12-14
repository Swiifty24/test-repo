<?php 
    
    require_once __DIR__ . "/../Controller.php";
    require_once __DIR__ . "/../models/Plan.php";
    require_once __DIR__ . "/../config/Database.php";
    require_once __DIR__ . "/../models/User.php";
    require_once __DIR__ . "/../models/Plan.php";
    require_once __DIR__ . "/../models/Subscription.php";
    require_once __DIR__ . "/../models/Session.php";
    require_once __DIR__ . "/../models/Trainer.php";

    class DashboardController extends Controller {
        protected $db;
        public function __construct() {
            $database = new Database();
            $this->db = $database->connect();
        }

        public function member() {
            $this->requireLogin();
            
            $user_id = $_SESSION['user_id'];

            $userModel = new User($this->db);
            $planModel = new Plan();
            $sessionModel = new Session();
            $trainerModel = new Trainer();

            $user = $userModel->getMember($user_id);
            $userPlan = $planModel->getUserPlan($user_id);
            $mySessions = $sessionModel->getUserSessions($user_id) ?? []; // Ensure array if null

            // --- NEW LOGIC: Calculate Dashboard Stats ---
            
            // 1. Total Classes Attended
            $totalSessions = count($mySessions);

            // 2. Workouts This Month
            $monthSessions = 0;
            $currentMonth = date('Y-m');
            foreach($mySessions as $sess) {
                // Assuming session_date exists in your session table
                if (isset($sess['session_date']) && strpos($sess['session_date'], $currentMonth) === 0) {
                    $monthSessions++;
                }
            }

            // 3. Days Remaining
            $daysRemaining = 0;
            if(isset($userPlan['end_date'])) {
                $end = new DateTime($userPlan['end_date']);
                $now = new DateTime();
                if ($end > $now) {
                    $daysRemaining = $now->diff($end)->days;
                }
            }

            // 4. Plan Status (Replacing "Personal Trainer" which isn't explicitly in your schema)
            $planStatus = isset($userPlan['status']) ? ucfirst($userPlan['status']) : "Inactive";

            // Bundle stats to pass to view
            $dashboardStats = [
                'total' => $totalSessions,
                'month' => $monthSessions,
                'days' => $daysRemaining,
                'status' => $planStatus
            ];

            // --- END NEW LOGIC ---

            //expire user plan logic (Existing)
            if(isset($userPlan) && $userPlan['end_date'] <= date("Y-m-d")) {
                // Removed echo to prevent header errors
                header("location: index.php?controller=Subscribe&action=expirePlan");
                exit;
            }

            // Consolidated View Rendering
            $viewData = [
                'userInfo' => $user,
                'userPlan' => $userPlan,
                'mySessions' => $mySessions,
                'dashboardStats' => $dashboardStats // Passing the new stats
            ];

            // Handle status logic for view (Existing logic preserved but cleaned up)
            if(isset($userPlan['status'])) {
                if($userPlan['status'] == "cancelled") {
                    $viewData['userPlan']['status'] = "cancelled";
                } else if($userPlan['status'] == "expire") {
                    $viewData['userPlan']['status'] = "expired";
                }
            }

            $this->view('dashboard', $viewData);
        }

        public function cancelSubscription() {
            $this->requireLogin();
            $this->view('cancelSubscription',[]);
        } 

        public function getTrainerMembers() {
            $this->requireLogin();
            header('Content-Type: application/json');
            
            $userModel = new User();
            $db = $userModel->connect();
            
            $query = "SELECT user_id, CONCAT(first_name, ' ', last_name) as name, email 
                    FROM members 
                    WHERE role = 'trainer' 
                    ORDER BY first_name, last_name";
            
            $stmt = $db->prepare($query);
            $stmt->execute();
            $members = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode(['success' => true, 'data' => $members]);
            exit;
        }

        public function listTrainers() {
            $this->requireLogin();
            header('Content-Type: application/json');
            $trainerModel = new Trainer();
            $trainers = $trainerModel->getAllTrainers() ?? [];
            echo json_encode(['success' => true, 'trainers' => $trainers]);
            exit;
        }

        public function requestTrainer() {
            $this->requireLogin();
            header('Content-Type: application/json');

            if($_SERVER['REQUEST_METHOD'] !== 'POST') {
                http_response_code(405);
                echo json_encode(['success' => false, 'message' => 'Method not allowed']);
                exit;
            }

            $user_id = $_SESSION['user_id'] ?? null;
            $trainer_id = intval($_POST['trainer_id'] ?? 0);
            $note = trim($_POST['notes'] ?? '');

            if(empty($user_id) || empty($trainer_id)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Trainer is required.']);
                exit;
            }

            $trainerModel = new Trainer();
            $trainer = $trainerModel->getTrainerById($trainer_id);
            if(!$trainer) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Trainer not found.']);
                exit;
            }

            if($trainerModel->hasPendingOrActiveRequest($user_id, $trainer_id)) {
                http_response_code(409);
                echo json_encode(['success' => false, 'message' => 'You already have a pending/active request with this trainer.']);
                exit;
            }

            try {
                if($trainerModel->requestTrainer($user_id, $trainer_id, $note)) {
                    echo json_encode(['success' => true, 'message' => 'Trainer request submitted.']);
                } else {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'message' => 'Unable to submit request at this time.']);
                }
            } catch(Exception $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Server error while creating request.']);
            }
            exit;
        }
        
    }
?>