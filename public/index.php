<?php 
    define('BASE_PATH', dirname(__DIR__));

    require_once BASE_PATH . '/App/App.php';

    $app = new App();
    $app->run();
    
?>
