<?php

    require_once("vendor/autoload.php");

    use \Slim\Slim;

    $app = new Slim();

    require_once('./routes/index.php');

    require_once('./routes/championship.php');

    require_once('./routes/cup.php');

    $app->run();

?>