<?php 
    require_once __DIR__ . "/../Controller.php";
    require_once __DIR__ . "/../models/Plan.php";
    require_once __DIR__ . "/../config/Database.php";

    class PlanController extends Controller {

        public function viewPlans() {
            session_start();
            $planModel = new Plan();
            $plans = $planModel->getAllActivePlans();
            
            $this->view('plans', [
                'plans' => $plans,
            ]);
        }

        public function getPlanData() {
            // Check login if needed: $this->requireLogin();
            
            if (!isset($_GET['plan_id'])) {
                echo json_encode(['success' => false, 'message' => 'Plan ID is required']);
                return;
            }

            $planModel = new Plan();
            $planData = $planModel->getPlanById($_GET['plan_id']);

            if ($planData) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'data' => $planData
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Plan not found']);
            }
            exit;
        }

        public function addPlan() {
            // Check login if needed: $this->requireLogin();
            header('Content-Type: application/json');

            if($_SERVER['REQUEST_METHOD'] == 'POST') {
                $planModel = new Plan();
                
                $planDetails = [
                    'plan_name' => trim(htmlspecialchars($_POST['plan_name'])),
                    'description' => trim(htmlspecialchars($_POST['description'])),
                    'duration_months' => trim(htmlspecialchars($_POST['duration_months'])),
                    'price' => trim(htmlspecialchars($_POST['price']))
                ];

                if($planModel->addNewPlan($planDetails)) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'New plan added successfully',
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Failed to add plan',
                    ]);
                }
            }
        }
        public function updatePlan() {
            // Check login if needed: $this->requireLogin();
            header('Content-Type: application/json');

            if($_SERVER['REQUEST_METHOD'] == 'POST') {
                $plan_id = $_POST['plan_id'];
                $planModel = new Plan();
                
                $planDetails = [
                    'plan_name' => trim(htmlspecialchars($_POST['plan_name'])),
                    'description' => trim(htmlspecialchars($_POST['description'])),
                    'duration_months' => trim(htmlspecialchars($_POST['duration_months'])),
                    'price' => trim(htmlspecialchars($_POST['price'])),
                    'status' => trim(htmlspecialchars($_POST['status']))
                ];

                if($planModel->updatePlan($plan_id, $planDetails)) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Plan updated successfully',
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Failed to update plan',
                    ]);
                }
            }
        }
    }

    public function deletePlan() {
        // Check login if needed: $this->requireLogin();
        header('Content-Type: application/json');

        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $plan_id = $_POST['plan_id'];
            $planModel = new Plan();
            
            if($planModel->deletePlanViaId($plan_id)) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Plan removed successfully',
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to remove plan',
                ]);
            }
        }
    }


?>