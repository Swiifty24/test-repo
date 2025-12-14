<?php 

    class App {
        public function run() {
            $controllerName = $_GET['controller'] ?? "Home";
            $actionName = $_GET['action'] ?? 'index';

            $controllerClass = ucfirst($controllerName) . 'Controller';
            $controllerFile = __DIR__ . "/controllers/{$controllerClass}.php";

            if(file_exists($controllerFile)) {
                require_once $controllerFile;

                $controller = new $controllerClass();

                if(method_exists($controller, $actionName)) {
                    $controller->$actionName();
                } else {
                    echo "Method '$actionName' not Found in controller '$controllerClass'.";
                } 
            } else {
                echo "Controller '$controllerClass' not Found.";
            }
        }
        
    }

?>