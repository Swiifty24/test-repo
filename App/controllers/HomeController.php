<?php 
    require_once __DIR__ . "/../Controller.php";
    require_once __DIR__ . "/../models/Plan.php";
    require_once __DIR__ . "/../config/Database.php";


    class HomeController extends Controller {


        public function index() {
            session_start();
            $planModel = new Plan();
            $plans = $planModel->getAllPlans();
            $this->view('landingpage', [
                'plans' => $plans,
            ]);
        }
    }
?>